#!/usr/bin/env bash

set -u

project_dir="$(cd "$(dirname "$0")/.." && pwd)"
batch_directory=""
posts_per_turn=20
max_turns_per_session=10
model_name="${LUNA_MODEL:-gpt-5.6-luna}"

while (( $# > 0 )); do
  case "$1" in
    --batch=*) batch_directory="${1#*=}" ;;
    --batch)
      shift
      batch_directory="${1:-}"
      ;;
    --posts-per-turn=*) posts_per_turn="${1#*=}" ;;
    --posts-per-turn)
      shift
      posts_per_turn="${1:-}"
      ;;
    --max-turns-per-session=*) max_turns_per_session="${1#*=}" ;;
    --max-turns-per-session)
      shift
      max_turns_per_session="${1:-}"
      ;;
    --model=*) model_name="${1#*=}" ;;
    --model)
      shift
      model_name="${1:-}"
      ;;
    -h|--help)
      sed -n '1,300p' "$0"
      exit 0
      ;;
    *)
      echo "Unknown argument: $1" >&2
      exit 2
      ;;
  esac
  shift
done

if [[ -z "$batch_directory" ]]; then
  echo "--batch is required" >&2
  exit 2
fi
if ! [[ "$posts_per_turn" =~ ^[1-9][0-9]*$ ]] || (( posts_per_turn > 50 )); then
  echo "--posts-per-turn must be an integer from 1 to 50" >&2
  exit 2
fi
if ! [[ "$max_turns_per_session" =~ ^[1-9][0-9]*$ ]] || (( max_turns_per_session > 20 )); then
  echo "--max-turns-per-session must be an integer from 1 to 20" >&2
  exit 2
fi
if ! command -v codex >/dev/null 2>&1; then
  echo "codex CLI is required" >&2
  exit 2
fi
if ! command -v jq >/dev/null 2>&1; then
  echo "jq is required" >&2
  exit 2
fi

if [[ "$batch_directory" != /* ]]; then
  batch_directory="$project_dir/${batch_directory#./}"
fi
batch_directory="$(realpath "$batch_directory" 2>/dev/null || true)"
if [[ -z "$batch_directory" || ! -f "$batch_directory/manifest.jsonl" ]]; then
  echo "batch manifest is missing" >&2
  exit 2
fi
if [[ "$batch_directory" != "$project_dir/tmp/"* ]]; then
  echo "batch must be inside the project tmp directory" >&2
  exit 2
fi

result_directory="$batch_directory/results"
log_directory="$batch_directory/persistent-logs"
mkdir -p "$result_directory" "$log_directory"

pending_tasks() {
  local output_file="$1"
  : > "$output_file"
  while IFS= read -r task; do
    local post_id
    post_id="$(printf '%s' "$task" | jq -r '.post_id')"
    if [[ ! -s "$result_directory/post-${post_id}.xml" ]]; then
      printf '%s\n' "$task" >> "$output_file"
    fi
  done < "$batch_directory/manifest.jsonl"
}

normalize_result_names() {
  while IFS= read -r task; do
    local post_id
    post_id="$(printf '%s' "$task" | jq -r '.post_id')"
    local numeric_result="$result_directory/${post_id}.xml"
    local expected_result="$result_directory/post-${post_id}.xml"
    if [[ -s "$numeric_result" && ! -e "$expected_result" ]]; then
      mv "$numeric_result" "$expected_result"
      echo "NORMALIZED_RESULT post-${post_id}.xml"
    fi
  done < "$batch_directory/manifest.jsonl"
}

make_prompt() {
  local task_file="$1"
  local prompt_file="$2"
  local mode="$3"
  {
    if [[ "$mode" == "initial" ]]; then
      cat <<'PROMPT'
You are a persistent CrimeWiki research-and-writing worker. This is a bounded multi-post session. Work through every task listed below, independently and completely; never mix facts, sources, or XML between posts.

For each task, research the subject from its locator plus every additional reputable primary, court, government, academic, and news source needed to establish the subject. Do not read the old database body. Write a completely original, deeply researched entry, not a Wikipedia paraphrase. Read include/qwen_contract.txt once and obey its exact five-block XML contract. Use as many substantive sections and paragraphs as the subject needs; do not force a fixed length. Keep related empty. Do not include images, executable markup, inline Wikipedia URLs, markdown, or commentary in result files. Wikipedia may appear only in the sources block as a clearly labelled fallback when no better usable source is available.

For each post, create ONLY its specified result XML file using apply_patch, containing ONLY the five XML blocks. Do not edit PHP, CSS, documentation, the database, or another worker result. If a topic cannot be researched reliably, create its specified .error.txt instead. After all listed tasks are handled, reply with only a short completion summary; do not paste XML into your final response.

PROMPT
    else
      cat <<'PROMPT'
Continue this same CrimeWiki worker session. Work only on the new tasks below, independently and completely. Preserve the exact five-block XML contract, do not read old post bodies, do not mix topics, and write only the specified result or error files. Do not paste XML into your final response. Finish every listed task before replying with a short completion summary.

PROMPT
    fi
    while IFS= read -r task; do
      post_id="$(printf '%s' "$task" | jq -r '.post_id')"
      printf '%s\n' "TASK_JSON: $task"
      printf '%s\n' "EXACT_RESULT_FILE: $result_directory/post-${post_id}.xml"
    done < "$task_file"
  } > "$prompt_file"
}

remaining_file="$batch_directory/pending.jsonl"
task_file="$batch_directory/turn-tasks.jsonl"
prompt_file="$batch_directory/turn-prompt.txt"
session_id=""
turn_in_session=0
turn_number=0

while :; do
  pending_tasks "$remaining_file"
  remaining_count="$(wc -l < "$remaining_file" | tr -d ' ')"
  if (( remaining_count == 0 )); then
    echo "PERSISTENT LUNA BATCH COMPLETE"
    break
  fi

  if [[ -z "$session_id" || $turn_in_session -ge $max_turns_per_session ]]; then
    session_id=""
    turn_in_session=0
    echo "Starting a fresh persistent Luna session; remaining posts: $remaining_count"
  fi

  head -n "$posts_per_turn" "$remaining_file" > "$task_file"
  turn_number=$((turn_number + 1))
  turn_in_session=$((turn_in_session + 1))
  turn_log="$log_directory/turn-${turn_number}.log"
  final_message="$log_directory/turn-${turn_number}.final.txt"

  if [[ -z "$session_id" ]]; then
    make_prompt "$task_file" "$prompt_file" initial
    codex \
      --cd "$project_dir" \
      --search \
      --model "$model_name" \
      --sandbox workspace-write \
      --ask-for-approval never \
      exec \
      --output-last-message "$final_message" \
      - < "$prompt_file" > "$turn_log" 2>&1 || true
    session_id="$(sed -n 's/.*session id: \([0-9a-f-]\{36\}\).*/\1/p' "$turn_log" | head -1)"
    if [[ -z "$session_id" ]]; then
      echo "Could not recover the persistent session id; see $turn_log" >&2
      exit 1
    fi
  else
    make_prompt "$task_file" "$prompt_file" continuation
    codex exec resume \
      --output-last-message "$final_message" \
      "$session_id" \
      - < "$prompt_file" > "$turn_log" 2>&1 || true
  fi

  normalize_result_names

  completed_in_turn=0
  while IFS= read -r task; do
    post_id="$(printf '%s' "$task" | jq -r '.post_id')"
    if [[ -s "$result_directory/post-${post_id}.xml" ]]; then
      completed_in_turn=$((completed_in_turn + 1))
    fi
  done < "$task_file"
  echo "Turn ${turn_number}: result files present for ${completed_in_turn}/$(wc -l < "$task_file" | tr -d ' ') tasks"

  # Import every valid result accumulated so far. The importer is idempotent.
  docker compose exec -T app-fpm php /var/www/html/scripts/apply_rewrite_batch.php \
    "--batch=${batch_directory#$project_dir/}" || true

  if (( completed_in_turn == 0 )); then
    echo "No task completed in turn ${turn_number}; stopping for inspection" >&2
    exit 1
  fi
done

#!/usr/bin/env bash

set -u

project_dir="$(cd "$(dirname "$0")/.." && pwd)"
requested_count=100
max_parallel=50
model_name="${LUNA_MODEL:-gpt-5.6-luna}"
worker_timeout="${LUNA_WORKER_TIMEOUT:-900}"
batch_directory=""
apply_results=0

while (( $# > 0 )); do
  case "$1" in
    --count=*) requested_count="${1#*=}" ;;
    --count)
      shift
      requested_count="${1:-}"
      ;;
    --parallel=*) max_parallel="${1#*=}" ;;
    --parallel)
      shift
      max_parallel="${1:-}"
      ;;
    --model=*) model_name="${1#*=}" ;;
    --model)
      shift
      model_name="${1:-}"
      ;;
    --resume=*) batch_directory="${1#*=}" ;;
    --resume)
      shift
      batch_directory="${1:-}"
      ;;
    --apply) apply_results=1 ;;
    --no-apply) apply_results=0 ;;
    -h|--help)
      sed -n '1,240p' "$0"
      exit 0
      ;;
    *)
      echo "Unknown argument: $1" >&2
      exit 2
      ;;
  esac
  shift
done

if ! [[ "$requested_count" =~ ^[1-9][0-9]*$ ]] || (( requested_count > 1000 )); then
  echo "--count must be an integer from 1 to 1000" >&2
  exit 2
fi
if ! [[ "$max_parallel" =~ ^[1-9][0-9]*$ ]] || (( max_parallel > 50 )); then
  echo "--parallel must be an integer from 1 to 50" >&2
  exit 2
fi
if ! [[ "$worker_timeout" =~ ^[1-9][0-9]*$ ]] || (( worker_timeout < 60 )); then
  echo "LUNA_WORKER_TIMEOUT must be at least 60 seconds" >&2
  exit 2
fi
if ! command -v codex >/dev/null 2>&1; then
  echo "codex CLI is required; log in with Codex before running this batch" >&2
  exit 2
fi
if ! command -v jq >/dev/null 2>&1; then
  echo "jq is required to read the task manifest" >&2
  exit 2
fi

if [[ -z "$batch_directory" ]]; then
  prepare_output="$(docker compose exec -T app-fpm php /var/www/html/scripts/prepare_rewrite_batch.php "--count=${requested_count}")" || exit 1
  batch_directory="$(printf '%s\n' "$prepare_output" | sed -n 's/^BATCH_DIR=//p' | tail -1)"
fi

if [[ -z "$batch_directory" ]]; then
  echo "Could not determine the batch directory" >&2
  exit 1
fi

manifest_path="$project_dir/$batch_directory/manifest.jsonl"
result_directory="$project_dir/$batch_directory/results"
log_directory="$project_dir/$batch_directory/logs"
if [[ ! -f "$manifest_path" ]]; then
  echo "Missing manifest: $manifest_path" >&2
  exit 1
fi
mkdir -p "$result_directory" "$log_directory"

echo "Batch: $batch_directory"
echo "Model: $model_name"
echo "Parallel workers: $max_parallel"
echo "Results: $result_directory"

group_pids=()
group_ids=()
group_watchers=()
group_count=0
worker_failures=0

watch_worker() {
  local worker_pid="$1"
  local worker_id="$2"
  local worker_result="$3"
  (
    local elapsed=0
    while kill -0 "$worker_pid" 2>/dev/null; do
      if [[ -s "$worker_result" ]]; then
        sleep 20
        if kill -0 "$worker_pid" 2>/dev/null; then
          echo "worker post ${worker_id}: XML ready; stopping lingering process" >&2
          kill -TERM "$worker_pid" 2>/dev/null || true
        fi
        break
      fi
      if (( elapsed >= worker_timeout )); then
        echo "worker post ${worker_id}: timed out after ${worker_timeout}s" >&2
        kill -TERM "$worker_pid" 2>/dev/null || true
        break
      fi
      sleep 10
      elapsed=$((elapsed + 10))
    done
  ) &
  last_watcher_pid=$!
}

wait_group() {
  local index=0
  local worker_id=""
  local worker_pid=""
  local watcher_pid=""
  local worker_rc=0
  while (( index < ${#group_pids[@]} )); do
    worker_id="${group_ids[$index]}"
    worker_pid="${group_pids[$index]}"
    watcher_pid="${group_watchers[$index]}"
    if wait "$worker_pid"; then
      worker_rc=0
    else
      worker_rc=$?
    fi
    wait "$watcher_pid" 2>/dev/null || true
    if [[ -s "$result_directory/post-${worker_id}.xml" ]]; then
      echo "worker post ${worker_id}: OK (XML ready; exit=${worker_rc})"
    else
      echo "worker post ${worker_id}: FAILED (see ${log_directory}/post-${worker_id}.log)" >&2
      worker_failures=$((worker_failures + 1))
    fi
    index=$((index + 1))
  done
  group_pids=()
  group_ids=()
  group_watchers=()
  group_count=0
}

while IFS= read -r task; do
  post_id="$(printf '%s' "$task" | jq -r '.post_id')"
  title="$(printf '%s' "$task" | jq -r '.title')"
  category="$(printf '%s' "$task" | jq -r '.category')"
  locator="$(printf '%s' "$task" | jq -r '.research_locator')"
  input_hash="$(printf '%s' "$task" | jq -r '.input_sha256')"
  result_path="$result_directory/post-${post_id}.xml"
  if [[ -s "$result_path" ]]; then
    echo "worker post ${post_id}: already has a result; skipping"
    continue
  fi

  prompt="You are one independent CrimeWiki rewrite worker. Work on exactly one post and do not inspect or edit other worker results.

Post ID: ${post_id}
Title: ${title}
Category: ${category}
Research locator: ${locator}
Pre-rewrite SHA-256 (for the importer only): ${input_hash}

Research the subject from the locator and every additional reputable primary, court, government, academic, and news source needed to establish the facts, context, investigation, consequences, and later significance, then stop searching and write; do not loop through repeated broad searches. Write a completely original, deeply researched CrimeWiki entry. Do not copy or paraphrase Wikipedia. Do not read the old database body; use the title and locator to research fresh. Read include/qwen_contract.txt for the exact five-block XML contract and obey it. Use as many substantive sections and paragraphs as the subject needs; do not force a fixed length. Keep related empty. Do not include images, executable markup, inline Wikipedia URLs, or markdown. Wikipedia may appear only in the sources block as a clearly labelled fallback when no better usable source is available.

Your only allowed write is this result file: ${result_path}. Create it with apply_patch and put ONLY the five XML blocks in it, with no code fence or commentary. Do not modify PHP, CSS, documentation, the database, or any other file. If research cannot be completed reliably, do not fabricate content and instead create ${result_directory}/post-${post_id}.error.txt containing a short safe error. Finish by stating whether the XML result file was created."

  log_path="$log_directory/post-${post_id}.log"
  echo "starting worker post ${post_id}: ${title}"
  codex \
    --cd "$project_dir" \
    --search \
    --model "$model_name" \
    --sandbox workspace-write \
    --ask-for-approval never \
    exec \
    --ephemeral \
    --output-last-message "$log_directory/post-${post_id}.final.txt" \
    "$prompt" < /dev/null >"$log_path" 2>&1 &
  group_pids+=("$!")
  group_ids+=("$post_id")
  watch_worker "${group_pids[$group_count]}" "$post_id" "$result_path"
  group_watchers+=("$last_watcher_pid")
  group_count=$((group_count + 1))

  if (( group_count >= max_parallel )); then
    wait_group
  fi
done < "$manifest_path"

if (( group_count > 0 )); then
  wait_group
fi

echo "Worker failures: $worker_failures"
if (( apply_results == 1 )); then
  echo "Applying validated results to the local database"
  if ! docker compose exec -T app-fpm php /var/www/html/scripts/apply_rewrite_batch.php "--batch=${batch_directory}"; then
    exit 1
  fi
else
  echo "Results are staged only. Review them, then apply with:"
  echo "  docker compose exec -T app-fpm php /var/www/html/scripts/apply_rewrite_batch.php --batch=${batch_directory}"
fi

exit "$worker_failures"

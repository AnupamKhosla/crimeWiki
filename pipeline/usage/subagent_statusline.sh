#!/bin/bash
# Subagent rows: logs each running subagent's model, effort and token count to
# agents.jsonl, and the latest set to agents_latest.json for the usage sidebar.
# Prints nothing, so the default row rendering is kept.
dir="$(cd "$(dirname "$0")" && pwd)"
in=$(cat)
ts="$(date +%Y-%m-%dT%H:%M:%S)"
echo "$in" | jq -c --arg ts "$ts" '.tasks[]? | {ts:$ts, id, name, type, status, model, effort, tokenCount, contextWindowSize}' >> "$dir/agents.jsonl" 2>/dev/null
echo "$in" | jq -c --arg ts "$ts" '{ts:$ts, tasks:[.tasks[]? | {id, type, status, model, effort, tokenCount}]}' > "$dir/agents_latest.json" 2>/dev/null
exit 0

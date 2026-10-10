#!/bin/bash
# Claude Code status line. Prints nothing (the usage sidebar mod shows these
# figures); appends context and plan usage to usage.jsonl so an agent can read them.
dir="$(cd "$(dirname "$0")" && pwd)"
in=$(cat); printf "%s" "$in" > "$dir/last_input.json"
echo "$in" | jq -c --arg ts "$(date +%Y-%m-%dT%H:%M:%S)" '{ts:$ts, session:.session_id, model:.model.display_name,
  ctx:((.context_window.current_usage.input_tokens//0)+(.context_window.current_usage.cache_creation_input_tokens//0)+(.context_window.current_usage.cache_read_input_tokens//0)),
  five_hour:.rate_limits.five_hour.used_percentage, five_hour_resets:.rate_limits.five_hour.resets_at,
  seven_day:.rate_limits.seven_day.used_percentage}' >> "$dir/usage.jsonl" 2>/dev/null
# Fable cost in the plan windows, for the sidebar (fable.py; runs alone, in the background).
python3 -I "$dir/fable.py" >/dev/null 2>&1 &

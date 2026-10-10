#!/usr/bin/env python3
"""PreToolUse hook: holds a subagent's context under a cap. Claude Code has no
per-agent context limit, so this reads the agent's own transcript before each
tool call and refuses tools once its context passes the cap.

Inert unless tmp/ctx_cap.json exists, e.g. (or a map of agent type to such a cap)
  {"agent_type": "crimewiki-rewriter", "research_stop": 75000, "hard_stop": 92000}
Past research_stop only writing, checking and logging are allowed; past
hard_stop every tool is refused. The main session is never touched. Every
decision is logged to tmp/ctx_guard.log."""
import glob, json, os, re, sys, time

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))
CAP, LOG = f'{ROOT}/tmp/ctx_cap.json', f'{ROOT}/tmp/ctx_guard.log'
# Bash commands still allowed past research_stop: the checker, the clock, and
# writes to the run log, blocked file or unresolved.txt.
WRITE_OK = ('check_new.py', 'date ', 'rewrite_log.md', 'unresolved.txt', 'pipeline/blocked/')
# ...unless the same command also reads or fetches.
RESEARCH = ('read.py', 'fetchmany.py', 'search.py', 'curl', 'wget', 'cat ', 'grep', 'head', 'tail', 'sed ', 'less', 'python3 -c')
# The checker may be piped through tail or head; only a fetch in the same command blocks it.
FETCH = ('read.py', 'fetchmany.py', 'search.py', 'curl', 'wget')
# Read is allowed past research_stop for these, so the agent can Edit them (Edit needs a prior Read).
# Not rewrite_log.md: it is large; agents append to it with >> instead.
READ_OK = ('pipeline/articles/', 'unresolved.txt', 'pipeline/blocked/')

def late_ok(tool, h, cmd):
    """Tools still allowed past research_stop: writing, checking and logging."""
    if tool in ('Write', 'Edit'): return True
    if tool == 'Read': return any(w in str((h.get('tool_input') or {}).get('file_path', '')) for w in READ_OK)
    if tool != 'Bash': return False
    # Words inside quotes are text (a log note saying "headings"), not commands.
    bare = re.sub(r"'[^']*'|\"[^\"]*\"", '', cmd)
    if 'check_new.py' in bare and not any(w in bare for w in FETCH): return True
    return any(w in cmd for w in WRITE_OK) and not any(w in bare for w in RESEARCH)

def context(path):
    """Prompt of the agent's last model call plus its output: the next call's prompt before the tool result."""
    text = open(path, encoding='utf-8', errors='replace').read()
    i = text.rfind('"usage":{')
    if i < 0: return 0
    try: u = json.JSONDecoder().raw_decode(text, i + 8)[0]
    except Exception: return 0
    return sum(u.get(k) or 0 for k in ('input_tokens', 'cache_creation_input_tokens', 'cache_read_input_tokens', 'output_tokens'))

def log(*parts):
    with open(LOG, 'a') as f: f.write(' | '.join([time.strftime('%H:%M:%S'), *map(str, parts)]) + '\n')

def deny(reason):
    print(json.dumps({'hookSpecificOutput': {'hookEventName': 'PreToolUse',
        'permissionDecision': 'deny', 'permissionDecisionReason': reason}}))
    sys.exit(0)

def main():
    h = json.load(sys.stdin)
    agent, tool = h.get('agent_id'), h.get('tool_name', '')
    if not agent:
        log('main', tool, '-', 'allow'); return
    if not os.path.exists(CAP): return
    cap = json.load(open(CAP))
    # One cap ({"agent_type": ..., ...}) or one per agent type ({"crimewiki-checker": {...}, ...}).
    if 'hard_stop' not in cap:
        cap = cap.get(h.get('agent_type') or '')
        if not cap: return
    elif cap.get('agent_type') and h.get('agent_type') != cap['agent_type']: return
    if tool == 'SubagentHandback':  # the agent's final report: never block it
        log(agent, tool, '-', 'allow'); return
    sid = h.get('session_id', '')
    found = glob.glob(os.path.expanduser(f'~/.claude/projects/*/{sid}/subagents/**/agent-{agent}.jsonl'), recursive=True)
    if not found:
        log(agent, tool, '?', 'allow (no transcript)'); return
    ctx = context(found[0])
    cmd = str((h.get('tool_input') or {}).get('command', ''))
    if ctx >= cap['hard_stop']:
        log(agent, tool, ctx, 'deny hard')
        deny(f'Context limit reached: {ctx:,} tokens of {cap["hard_stop"]:,}. Call no more tools. '
             'Reply now with your final report, saying the guard stopped you and what is unfinished.')
    if ctx >= cap['research_stop'] and not late_ok(tool, h, cmd):
        log(agent, tool, ctx, 'deny research')
        deny(f'Research budget used: context is {ctx:,} tokens, the limit is {cap["research_stop"]:,}. '
             'Do not search, fetch or read any more. Write the article now from the screens already in your '
             'context, then run check_new.py, fix with Edit, and log. Only Write, Edit, check_new.py, date '
             'and the log/unresolved/blocked files are allowed now.')
    log(agent, tool, ctx, 'allow')

if __name__ == '__main__':
    try: main()
    except SystemExit: raise
    except Exception as e: log('error', repr(e))

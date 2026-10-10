#!/usr/bin/env python3
"""Exact tokens and API-priced cost of one subagent, from its own transcript.
Haiku 5.5 is priced per request by prompt size (over 100,000 tokens pays 5x).
Usage: agent_cost.py AGENT_ID"""
import glob, json, os, sys

LOW, HIGH = (0.10, 0.125, 0.20, 0.01, 0.50), (0.50, 0.625, 1, 0.05, 2.50)
path = glob.glob(os.path.expanduser(f'~/.claude/projects/*/*/subagents/**/agent-{sys.argv[1]}.jsonl'), recursive=True)[0]
calls = {}
for line in open(path, encoding='utf-8', errors='replace'):
    if '"usage"' not in line: continue
    try: j = json.loads(line); m = j['message']; u = m['usage']
    except Exception: continue
    cc = u.get('cache_creation') or {}
    row = (u.get('input_tokens') or 0, cc.get('ephemeral_5m_input_tokens', u.get('cache_creation_input_tokens') or 0),
           cc.get('ephemeral_1h_input_tokens', 0), u.get('cache_read_input_tokens') or 0, u.get('output_tokens') or 0)
    k = m.get('id'); old = calls.get(k)
    calls[k] = (m.get('model'), j['timestamp'], tuple(max(a, b) for a, b in zip(old[2], row)) if old else row)
tier = low_all = 0.0; tot = [0] * 5; over = 0; peak = 0
for model, ts, r in calls.values():
    prompt = r[0] + r[1] + r[2] + r[3]; peak = max(peak, prompt)
    hi = prompt > 100_000; over += hi
    tier += sum(x * p for x, p in zip(r, HIGH if hi else LOW)) / 1e6
    low_all += sum(x * p for x, p in zip(r, LOW)) / 1e6
    tot = [a + b for a, b in zip(tot, r)]
ts = sorted(t for _, t, _ in calls.values())
print(f'model {model}  requests {len(calls)} ({over} over 100K)  peak prompt {peak:,}  {ts[0][11:19]}-{ts[-1][11:19]} UTC')
print(f'tokens: input {tot[0]:,}  cache write {tot[1] + tot[2]:,}  cache read {tot[3]:,}  output {tot[4]:,}  prompt total {sum(tot[:4]):,}')
print(f'API-priced: ${tier:.4f} with the 100K tier, ${low_all:.4f} if all at the low rate')

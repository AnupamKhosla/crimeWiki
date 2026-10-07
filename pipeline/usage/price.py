#!/usr/bin/env python3
"""API-price a Claude Code subagent transcript (JSONL), split per topic.
A topic ends at the step whose tool call writes the run log (*_log.md).
Prices: USD per million tokens, from platform.claude.com/docs/en/about-claude/pricing (4 Oct 2026)."""
import json, sys
PRICES = {  # input, 5m cache write, 1h cache write, cache read, output
    'claude-sonnet-5-5': (2, 2.5, 4, 0.2, 10),
    'claude-opus-5-5': (4, 5, 8, 0.2, 20),
    'claude-haiku-4-5-20251001': (1, 1.25, 2, 0.1, 5),
}
SEARCH = 10 / 1000  # USD per web search

def steps(path):
    # A streamed message is logged more than once; keep the largest count per field.
    by, order, ends = {}, [], set()
    for line in open(path, encoding='utf-8', errors='replace'):
        try: j = json.loads(line)
        except Exception: continue
        m = j.get('message') if isinstance(j, dict) else None
        if not isinstance(m, dict) or not isinstance(m.get('usage'), dict): continue
        u, k = m['usage'], m.get('id')
        cc = u.get('cache_creation') or {}
        row = (u.get('input_tokens') or 0, cc.get('ephemeral_5m_input_tokens') or 0,
               cc.get('ephemeral_1h_input_tokens') or 0, u.get('cache_read_input_tokens') or 0,
               u.get('output_tokens') or 0, (u.get('server_tool_use') or {}).get('web_search_requests') or 0)
        if k not in by: order.append(k); by[k] = (m.get('model'), j.get('timestamp', ''), row)
        else: by[k] = (by[k][0], by[k][1], tuple(max(a, b) for a, b in zip(by[k][2], row)))
        for c in m.get('content') or []:
            if isinstance(c, dict) and c.get('type') == 'tool_use' and c.get('name') in ('Bash', 'Write', 'Edit') \
               and '_log.md' in json.dumps(c.get('input')): ends.add(k)
    return [(by[k], k in ends) for k in order]

def cost(model, r):
    p = PRICES[model]
    return (r[0]*p[0] + r[1]*p[1] + r[2]*p[2] + r[3]*p[3] + r[4]*p[4]) / 1e6 + r[5]*SEARCH

def report(path):
    s = steps(path); segs, cur = [], []
    for st, end in s:
        cur.append(st)
        if end: segs.append(cur); cur = []
    if cur: segs.append(cur)
    tot = [0]*6; usd = 0
    print(f'{path.split("/")[-1]}  ({s[0][0][0]})')
    for n, seg in enumerate(segs, 1):
        t = [sum(x[2][i] for x in seg) for i in range(6)]; c = sum(cost(x[0], x[2]) for x in seg)
        ctx = lambda x: x[2][0] + x[2][1] + x[2][2] + x[2][3]
        print(f'  part {n}: steps {len(seg):3} | ctx {ctx(seg[0])/1e3:5.0f}k -> {ctx(seg[-1])/1e3:5.0f}k | read {t[3]/1e6:5.2f}M | write {(t[1]+t[2])/1e3:4.0f}k | out {t[4]/1e3:3.0f}k | searches {t[5]} | ${c:.2f} | {seg[0][1][11:16]}-{seg[-1][1][11:16]}Z')
        tot = [a+b for a, b in zip(tot, t)]; usd += c
    print(f'  TOTAL: steps {len(s)} | read {tot[3]/1e6:.2f}M | write {(tot[1]+tot[2])/1e3:.0f}k | uncached {tot[0]} | out {tot[4]/1e3:.0f}k | searches {tot[5]} | ${usd:.2f}')

for p in sys.argv[1:]: report(p)

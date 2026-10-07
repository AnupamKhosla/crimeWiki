#!/usr/bin/env python3
"""status.py [all]  -> the topic list with the state of each topic; without "all", only open ones."""
import sys,os,json
sys.path.insert(0,os.path.dirname(os.path.abspath(__file__))); from common import *
pub={}
if os.path.exists(f'{BASE}/published.jsonl'):
    pub={json.loads(l)['task_id']:json.loads(l)['id'] for l in open(f'{BASE}/published.jsonl') if l.strip()}
chk=json.load(open(f'{BASE}/check-state.json')) if os.path.exists(f'{BASE}/check-state.json') else {}
n={}
for t,m in topics().items():
    no=int(t[-5:])
    if t in pub: s=f'LIVE id {pub[t]}'
    elif os.path.exists(f'{BASE}/blocked/{t}.json'): s='blocked'
    elif os.path.exists(f'{BASE}/articles/{t}.xml'): s='ok, not published' if chk.get(t,{}).get('ok') else 'drafted, NOT ok'
    elif index(t): s=f"researched ({sum(e['status']=='OK' for e in index(t))} pages)"
    else: s='todo'
    k=s.split(' ')[0]; n[k]=n.get(k,0)+1
    if len(sys.argv)>1 or not s.startswith(('LIVE','blocked')):
        print(f"{no:3} | {s:22} | {m['category']:9} | {m['title']}" + (f"\n      seed: {m['seed']}\n      note: {m['note']}" if s=='todo' and len(sys.argv)<2 and no<=min(x for x in [int(u[-5:]) for u in topics() if u not in pub and not os.path.exists(f'{BASE}/articles/{u}.xml') and not os.path.exists(f'{BASE}/blocked/{u}.json')] or [0])+2 else ''))
print(' | '.join(f'{k} {v}' for k,v in n.items()))

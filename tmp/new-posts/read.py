#!/usr/bin/env python3
"""read.py <topic_no> [--page N] [--max N] [--only 1,3] [--kw word,word]
Prints the saved text of every usable page for a topic, in screens of about 27,000
characters (--page 2 for the next one). Paragraphs already printed from an earlier
page (wire copy reused by several outlets) are skipped. --max caps each source."""
import sys,re,os
sys.path.insert(0,os.path.dirname(os.path.abspath(__file__))); from common import *
a=sys.argv[1:]; t=tid(a[0]); mx=9000; only=None; kw=[]; page=1
for i,x in enumerate(a):
    if x=='--max': mx=int(a[i+1])
    if x=='--page': page=int(a[i+1])
    if x=='--only': only={int(v) for v in a[i+1].split(',')}
    if x=='--kw': kw=[v.lower() for v in a[i+1].split(',')]
seen=set(); out=[]
for e in index(t):
    if e['status']!='OK' or (only and e['n'] not in only): continue
    lines=open(f"{BASE}/research/{t}/{e['n']:02d}.txt",encoding='utf-8').read().split('\n')
    out.append('\n'+lines[0]); used=0; skipped=0
    for p in lines[1:]:
        k=re.sub(r'[^a-z0-9]+',' ',p.lower()).strip()
        if not k: continue
        if k in seen: skipped+=1; continue
        seen.add(k)
        if kw and not any(w in p.lower() for w in kw): continue
        if used+len(p)>mx: out.append(f'... [cut at {mx} chars; rest: --only {e["n"]} --max 40000]'); break
        out.append(p); used+=len(p)
    if skipped: out.append(f'({skipped} paragraphs already shown above)')
pages=[[]]; size=0
for l in out:
    if size+len(l)>27000 and pages[-1]: pages.append([]); size=0
    pages[-1].append(l); size+=len(l)+1
print('\n'.join(pages[min(page,len(pages))-1]))
print(f'\n== screen {min(page,len(pages))} of {len(pages)} for {t}'+(f' | next: read.py {a[0]} --page {page+1}' if page<len(pages) else ' | end'))

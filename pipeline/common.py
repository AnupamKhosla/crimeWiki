"""Shared helpers for pipeline (new articles that do not exist on the live site yet)."""
import json,os,re,html,glob
BASE='pipeline'
def tid(n):
    n=str(n); return n if n.startswith('cw-topic-') else 'cw-topic-8%05d'%int(n)
def topics():
    p=f'{BASE}/topics.jsonl'
    return {t['task_id']:t for t in map(json.loads,filter(str.strip,open(p,encoding='utf-8')))} if os.path.exists(p) else {}
def index(t):
    p=f'{BASE}/research/{t}/index.jsonl'
    return [json.loads(l) for l in open(p,encoding='utf-8') if l.strip()] if os.path.exists(p) else []
def norm(s):
    s=html.unescape(s).replace(' ',' ')
    for a,b in (('’',"'"),('‘',"'"),('“','"'),('”','"'),('–','-'),('—','-'),('‑','-')): s=s.replace(a,b)
    s=re.sub(r'(?<=\d),(?=\d{3}\b)','',s)
    return re.sub(r'\s+',' ',s).strip()
def corpus(t):
    """All text of the pages that were opened successfully for this topic."""
    out=[]
    for e in index(t):
        if e['status']=='OK': out.append(open(f"{BASE}/research/{t}/{e['n']:02d}.txt",encoding='utf-8').read())
    return norm('\n'.join(out))
def article(t):
    return open(f'{BASE}/articles/{t}.xml',encoding='utf-8').read()
def plain(x):
    return norm(re.sub(r'<[^>]+>',' ',x))

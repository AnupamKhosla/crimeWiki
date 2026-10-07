#!/usr/bin/env python3
"""Read-only. Compares every live legacy post (tmp/live-snapshot) with the Wikipedia page of the same title,
using the same clone test as pipeline/check_new.py. Writes results.jsonl; prints a summary."""
import gzip,csv,sys,re,os,json,html,subprocess,importlib.util,time
from concurrent.futures import ThreadPoolExecutor
sys.path.insert(0,'pipeline'); from common import norm,plain
spec=importlib.util.spec_from_file_location('fm','pipeline/fetchmany.py'); fm=importlib.util.module_from_spec(spec); spec.loader.exec_module(fm)
D='pipeline/legacy-wiki-audit'; csv.field_size_limit(10**9)
rows=list(csv.reader(gzip.open('tmp/live-snapshot/posts-20261003-night.tsv.gz','rt',encoding='utf-8',errors='replace'),delimiter='\t',quoting=csv.QUOTE_NONE))
posts=[(int(r[0]),r[1],r[2],r[7].replace('\\n','\n').replace('\\t','\t').replace('\\\\','\\')) for r in rows if r[2]!='Blog' and int(r[0])<=1140]
def key(h): return re.sub(r'^(the|a|an) ','',re.sub(r'[^a-z0-9]+',' ',h.lower()).strip())
def wiki(pid,title):
    f=f'{D}/wiki/{pid}.txt'
    if os.path.exists(f): return open(f,encoding='utf-8').read()
    j=None
    for attempt in range(5):  # Wikipedia answers 429 when asked too fast: wait and try again
        r=subprocess.run(['curl','-sG','--compressed','--max-time','40','-A',fm.WIKI_UA,'https://en.wikipedia.org/w/api.php','--data-urlencode','action=parse','--data-urlencode','page='+title,'--data-urlencode','redirects=1','--data-urlencode','prop=text|sections','--data-urlencode','format=json','--data-urlencode','formatversion=2','--data-urlencode','maxlag=5'],capture_output=True)
        try: j=json.loads(r.stdout)
        except Exception: j=None
        if j and ('parse' in j or j.get('error',{}).get('code')=='missingtitle'): break
        j=None; time.sleep(3*(attempt+1))
    if j is None: return ''   # still failing: not cached, try again next run
    if 'parse' not in j: out='MISSING\n'
    else:
        p=j['parse']; body=re.sub(r'(?is)<sup\b.*?</sup>|<ol class="references">.*?</ol>|<table\b.*?</table>','',p['text'])
        heads=[html.unescape(re.sub(r'<[^>]+>','',x['line'])).strip() for x in p['sections'] if x.get('toclevel')==1]
        out=p['title']+'\n'+' | '.join(h for h in heads if h.lower() not in fm.NOT_A_SECTION)+'\n'+'\n'.join(fm.extract(body)[2])+'\n'
    open(f,'w',encoding='utf-8').write(out); return out
def one(post):
    pid,title,cat,x=post; w=wiki(pid,title)
    if not w: return dict(id=pid,title=title,status='fetch failed')
    if w.startswith('MISSING'): return dict(id=pid,title=title,status='no wikipedia page')
    l=w.split('\n'); heads={key(h) for h in l[1].split(' | ') if h.strip()}
    Wt=re.sub(r'[^a-z0-9]+',' ',norm(' '.join(l[2:])).lower()).split()
    six={' '.join(Wt[i:i+6]) for i in range(len(Wt)-5)}; twelve={' '.join(Wt[i:i+12]) for i in range(len(Wt)-11)}
    m=re.search(r'(?s)<content>(.*?)</content>',x); c=m.group(1) if m else x
    toks=re.sub(r'[^a-z0-9]+',' ',re.sub(r'"[^"]*"',' ',plain(c)).lower()).split()
    runs=0; i=0
    while i+12<=len(toks):
        if ' '.join(toks[i:i+12]) in twelve: runs+=1; i+=12
        else: i+=1
    ours6=[' '.join(toks[i:i+6]) for i in range(len(toks)-5)]
    ours=[key(plain(h)) for h in re.findall(r'(?s)<h2>(.*?)</h2>',c)][1:]; same=[h for h in ours if h in heads]
    return dict(id=pid,title=title,status='compared',words=len(toks),wiki_words=len(Wt),runs=runs,share=round(sum(g in six for g in ours6)/max(1,len(ours6)),4),same=len(same),ours=len(ours),has_content_block=bool(m))
with ThreadPoolExecutor(2) as ex: res=list(ex.map(one,posts))
with open(f'{D}/results.jsonl','w',encoding='utf-8') as f:
    for r in res: f.write(json.dumps(r,ensure_ascii=False)+'\n')
c=[r for r in res if r['status']=='compared']; n=len(c)
print('legacy posts',len(res),'| compared',n,'| no wikipedia page',sum(r['status']=='no wikipedia page' for r in res),'| fetch failed',sum(r['status']=='fetch failed' for r in res))
sh=sorted(r['share'] for r in c); q=lambda p: sh[min(n-1,int(p*n))]
print('share of 6-word runs also in Wikipedia: median %.1f%% | 75th %.1f%% | 90th %.1f%% | 99th %.1f%% | max %.1f%%'%tuple(100*q(p) for p in (.5,.75,.9,.99,1)))
print('WIKI WORDING (>=4%):',sum(r['share']>=0.04 for r in c),'| >=10%:',sum(r['share']>=0.10 for r in c),'| >=25%:',sum(r['share']>=0.25 for r in c))
print('WIKI COPIED (a 12-word run):',sum(r['runs']>0 for r in c),'posts | total runs',sum(r['runs'] for r in c))
print('WIKI STRUCTURE (3+ headings and half of ours):',sum(r['same']>=3 and r['same']*2>=r['ours'] for r in c))
print('fails any:',sum(r['share']>=0.04 or r['runs']>0 or (r['same']>=3 and r['same']*2>=r['ours']) for r in c))
for r in sorted(c,key=lambda r:-r['share'])[:15]: print('  %5d %5.1f%% runs %3d heads %d/%d | %s'%(r['id'],100*r['share'],r['runs'],r['same'],r['ours'],r['title'][:60]))

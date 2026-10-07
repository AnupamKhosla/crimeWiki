#!/usr/bin/env python3
"""fetchmany.py <topic_no> <url> [url ...]
Opens many pages at once (curl, browser user agent) and saves the text of each to
tmp/new-posts/research/<task_id>/NN.txt, with one index line per page. Only pages
with status OK may be cited as sources. PDFs are converted with pdf2txt.swift.
A Wikipedia URL is saved as a LEAD, never as a source: see wiki() below."""
import sys,re,html,json,os,subprocess
from concurrent.futures import ThreadPoolExecutor
from urllib.parse import urlparse,unquote
sys.path.insert(0,os.path.dirname(os.path.abspath(__file__))); from common import *
UA='Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36'
def extract(raw):
    t=re.search(r'(?is)<title[^>]*>(.*?)</title>',raw)
    title=html.unescape(re.sub(r'\s+',' ',t.group(1))).strip() if t else ''
    d=re.search(r'(?i)"datePublished"\s*:\s*"([^"]+)"',raw) or re.search(r'(?i)article:published_time"[^>]*content="([^"]+)"',raw)
    body=re.sub(r'(?is)<(script|style|noscript|nav|footer|header|form|aside)\b.*?</\1>',' ',raw)
    ps=[html.unescape(re.sub(r'\s+',' ',re.sub(r'<[^>]+>',' ',p))).strip() for p in re.findall(r'(?is)<(?:p|h1|h2|h3|li|blockquote)\b[^>]*>(.*?)</(?:p|h1|h2|h3|li|blockquote)>',body)]
    ps=[p for p in ps if len(p)>60]
    if sum(map(len,ps))<1500:  # pages that keep the text in JSON-LD
        for m in re.findall(r'(?is)<script[^>]*application/ld\+json[^>]*>(.*?)</script>',raw):
            try: stack=[json.loads(m)]
            except Exception: continue
            while stack:
                x=stack.pop()
                if isinstance(x,dict):
                    b=x.get('articleBody')
                    if isinstance(b,str) and len(b)>sum(map(len,ps)):
                        ps=[s.strip() for s in re.split(r'\n+',html.unescape(b)) if len(s.strip())>40]
                    stack.extend(x.values())
                elif isinstance(x,list): stack.extend(x)
    seen=set(); out=[]
    for p in ps:
        if p not in seen: seen.add(p); out.append(p)
    return title,(d.group(1)[:10] if d else ''),out
WIKI_UA='CrimeWikiResearch/1.0 (https://crimewiki.site)'
NOT_A_LEAD=re.compile(r'wikipedia\.org|wikimedia\.org|wikidata\.org|wikisource\.org|archive\.org|archive\.(today|ph|is)|webcitation\.org|doi\.org|worldcat\.org|geohack|viaf\.org|id\.loc\.gov|isni\.org')
NOT_A_SECTION={'see also','references','external links','notes','further reading','citations','sources','bibliography','footnotes','explanatory notes','works cited'}
def wiki(job):
    """Owner's rule (AGENTS.md, 4 October 2026): Wikipedia may be read and the sources it cites may be used, but our
    page must never look like it. So a Wikipedia page is a LEAD, not a source. Its text goes to wiki-NN.txt, which is
    outside the index of citable pages: read it to see what the case involves and what we may have missed, and
    check_new.py compares the article with it. The pages it cites go to wiki-leads.txt; open the useful ones with
    fetchmany.py like any other URL. A fact enters an article only from a page with status OK."""
    n,url,d=job; u=urlparse(url); page=unquote(u.path.split('/wiki/',1)[-1]).replace('_',' ')
    r=subprocess.run(['curl','-sG','--compressed','--max-time','35','-A',WIKI_UA,f'https://{u.netloc}/w/api.php',
        '--data-urlencode','action=parse','--data-urlencode','page='+page,'--data-urlencode','redirects=1',
        '--data-urlencode','prop=text|externallinks|sections','--data-urlencode','format=json','--data-urlencode','formatversion=2'],capture_output=True)
    try: p=json.loads(r.stdout)['parse']
    except Exception: return dict(n=n,url=url,status='FAIL',code='-',chars=0,title='no such Wikipedia page: '+page,published='',host=u.netloc)
    body=re.sub(r'(?is)<sup\b.*?</sup>|<ol class="references">.*?</ol>|<table\b.*?</table>','',p['text'])
    ps=extract(body)[2]; text='\n'.join(ps)
    heads=[html.unescape(re.sub(r'<[^>]+>','',x['line'])).strip() for x in p['sections'] if x.get('toclevel')==1]
    heads=[h for h in heads if h.lower() not in NOT_A_SECTION]
    open(f'{d}/wiki-{n:02d}.txt','w',encoding='utf-8').write(f"# WIKIPEDIA: a lead-finder, not a source. Never cite it, never follow its structure or wording. | {p['title']} | {url}\n## sections: {' | '.join(heads)}\n{text}\n")
    # Wikipedia's own archive copies of dead cited pages, and books on archive.org, are leads too.
    KEEP=re.compile(r'web\.archive\.org/web/\d|archive\.org/details/')
    leads=[l for l in p['externallinks'] if l.startswith('http') and (KEEP.search(l) or not NOT_A_LEAD.search(l))]
    return dict(n=n,url=url,status='LEAD',code='200',chars=len(text),title='Wikipedia: '+p['title'],published='',host=u.netloc,sections=' | '.join(heads),leads=leads)
def fetch(job):
    n,url,d=job
    if re.search(r'wikipedia\.org/wiki/',url): return wiki(job)
    if re.search(r'wikipedia\.org|wikimedia\.org',url): return dict(n=n,url=url,status='REFUSED',code='-',chars=0,title='Wikipedia is not a source; pass its /wiki/ article URL to save it as a lead',published='')
    r=subprocess.run(['curl','-sL','--compressed','--max-time','35','-A',UA,'-H','Accept-Language: en-US,en;q=0.9','-w','\n%{http_code}',url],capture_output=True)
    code=r.stdout.rsplit(b'\n',1)[-1].decode('ascii','replace'); data=r.stdout.rsplit(b'\n',1)[0]
    if data[:5]==b'%PDF-':
        open(f'{d}/{n:02d}.pdf','wb').write(data)
        txt=subprocess.run(['swift','tmp/live-rewrite/pdf2txt.swift',f'{d}/{n:02d}.pdf'],capture_output=True,text=True).stdout
        title,pub,ps=os.path.basename(urlparse(url).path),'',[re.sub(r'\s+',' ',p).strip() for p in re.split(r'\n\s*\n',txt) if p.strip()]
    else:
        title,pub,ps=extract(data.decode('utf-8','replace'))
    text='\n'.join(ps); status='OK' if code=='200' and len(text)>=1200 else ('THIN' if code=='200' else 'FAIL')
    host=urlparse(url).netloc.replace('www.','')
    open(f'{d}/{n:02d}.txt','w',encoding='utf-8').write(f'# [{n}] {host} | {title} | published {pub or "?"} | {url}\n{text}\n')
    return dict(n=n,url=url,status=status,code=code,chars=len(text),title=title[:150],published=pub,host=host)
if __name__=='__main__':
    t=tid(sys.argv[1]); d=f'{BASE}/research/{t}'; os.makedirs(d,exist_ok=True)
    old=index(t); have={e['url'] for e in old if e['status'] in ('OK','LEAD')}; n0=max([e['n'] for e in old],default=0)
    urls=[]
    for u in sys.argv[2:]:
        if u not in have and u not in urls: urls.append(u)
    with ThreadPoolExecutor(12) as ex: res=list(ex.map(fetch,[(n0+i+1,u,d) for i,u in enumerate(urls)]))
    for e in res:  # the pages Wikipedia cites: one shared list per topic, written here because fetch() runs in threads
        if e['status']!='LEAD': continue
        f=f'{d}/wiki-leads.txt'; lines=[l for l in open(f,encoding='utf-8').read().split('\n') if l] if os.path.exists(f) else []
        new=[l for l in dict.fromkeys(e.pop('leads')) if l not in lines]; e['leads_new']=len(new)
        open(f,'w',encoding='utf-8').write('\n'.join(lines+[f"# cited by {e['title']} (leads only; open a page with fetchmany.py before using it)"]+new)+'\n')
    with open(f'{d}/index.jsonl','a',encoding='utf-8') as f:
        for e in res: f.write(json.dumps(e,ensure_ascii=False)+'\n')
    for e in res:
        print(f"[{e['n']}] {e['status']:7} {e['code']} {e['chars']:6} chars | {e.get('published') or '?':10} | {e['title'][:70]} | {e['url'][:80]}")
        if e['status']=='LEAD': print(f"      NOT A SOURCE. Text: {d}/wiki-{e['n']:02d}.txt. {e['leads_new']} cited pages added to {d}/wiki-leads.txt.\n      Wikipedia's sections (ours must be our own): {e['sections']}")
    ok=[e for e in index(t) if e['status']=='OK']
    print(f"{t}: {len(ok)} usable pages, {sum(e['chars'] for e in ok)} chars. Read them with: python3 tmp/new-posts/read.py {sys.argv[1]}")

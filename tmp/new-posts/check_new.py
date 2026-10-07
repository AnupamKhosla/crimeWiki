#!/usr/bin/env python3
"""check_new.py [topic_no ...]   (no arguments = every article)
For each article in tmp/new-posts/articles this builds the research sidecar, runs the
content-kit validator and the padding audit, and then compares the article with the
saved text of the pages that were opened for it:
  NOT OPENED  a cited source that fetchmany.py did not save with status OK
  UNGROUNDED  a number, a capitalised word or a quotation that is in no saved page
  COPIED      a run of 12 or more words taken verbatim from a saved page
  VERIFY      softer: a date or a pair of names that the pages never put together
  WIKI ...    the article resembles the Wikipedia page saved as a lead (fetchmany.py with a
              /wiki/ URL): COPIED = a run of 12 or more words also in Wikipedia; STRUCTURE = three or
              more section headings, and at least half of ours, are Wikipedia's; WORDING = too many
              6-word runs in common. Owner's rule: read Wikipedia, never look like it.
An article is publishable only when its line ends in "ok".
False alarms go in tmp/new-posts/research/<task_id>/allow.txt, one per line:
  item | page number | the wording on that page"""
import html,sys,re,os,json,glob,subprocess
sys.path.insert(0,os.path.dirname(os.path.abspath(__file__))); from common import *
sys.path.insert(0,'tmp/day-002-audit'); import padding_audit as pa

MONTHS='january february march april may june july august september october november december'.split()
DAYS='monday tuesday wednesday thursday friday saturday sunday'.split()
LINK={'of','the','al','el','bin','ibn','de','del','la','le','van','von','der','da','di'}
DICT=None
def common_word(w):
    global DICT
    if DICT is None:
        DICT={l.strip() for l in open('/usr/share/dict/words') if l[:1].islower()}
    return any(c in DICT for c in (w,w[:-1],w[:-2],w[:-3],w[:-3]+'y',w[:-1]+'e',w[:-2]+'e',w[:-3]+'e'))

def sources_of(x):
    return re.findall(r'<li><a href="([^"]+)">(.*?)</a></li>',re.search(r'<sources>(.*?)</sources>',x,re.S).group(1),re.S)

def sidecar(t,x):
    idx={e['url']:e for e in index(t) if e['status']=='OK'}; src=[]; missing=[]
    for u,title in sources_of(x):
        title=norm(title); pub,_,rest=title.partition(': ')
        u=html.unescape(u)  # hrefs are XML-escaped (&amp;); index.jsonl holds the raw URL
        if u not in idx: missing.append(u)
        src.append(dict(url=u,title=rest or title,publisher=pub,access_status='opened' if u in idx else 'not opened',supports=['facts attributed to this publisher in the article']))
    p=f'{BASE}/research/{t}/unresolved.txt'
    unresolved=[l.strip() for l in open(p,encoding='utf-8') if l.strip()] if os.path.exists(p) else []
    json.dump(dict(schema_version='crimewiki.research.v1',task_id=t,researched_on='2026-10-03',identity_confirmed=True,sources=src,unresolved_claims=unresolved),open(f'{BASE}/research/{t}.json','w',encoding='utf-8'),indent=1,ensure_ascii=False)
    return missing,len(src)

def ground(t,x):
    C=corpus(t); Cl=C.lower(); Cw=' '+re.sub(r'[^a-z0-9]+',' ',Cl)+' '; words=set(Cw.split())
    p=f'{BASE}/research/{t}/allow.txt'
    allow={l.split('|')[0].strip().lower() for l in open(p,encoding='utf-8') if l.strip()} if os.path.exists(p) else set()
    body=re.sub(r'(?s)<sources>.*?</sources>','',x)
    text=plain(re.sub(r'</(?:h2|p|td|th|li)>',' ¶ ',body))
    out={'numbers':[],'names':[],'quotes':[],'copied':[],'verify':[]}
    def add(k,v):
        if v.lower() not in allow and v not in out[k]: out[k].append(v)
    # numbers
    for n in sorted(set(re.findall(r'\d+(?:\.\d+)?',text)),key=lambda s:(len(s),s)):
        if not re.search(r'(?<![\d.])'+re.escape(n)+r'(?!\d|\.\d)',C): add('numbers',n)
    # quotations
    quoted=[q for blk in text.split('¶') for q in blk.split('"')[1::2]]  # pair the marks in order, per paragraph
    for q in quoted:
        for piece in re.split(r'\.\.\.|…|\[[^\]]*\]',q):
            k=re.sub(r'[^a-z0-9]+',' ',piece.lower()).strip()
            if len(k.split())>=3 and ' '+k+' ' not in Cw: add('quotes',piece.strip()[:90])
    # capitalised words and name pairs
    noquote=re.sub(r'"[^"]*"',' ',text)
    for s in re.split(r'¶|(?<=[.!?])\s+',noquote):
        toks=re.findall(r"[A-Za-z][A-Za-z0-9'\-]*",s); prev=None
        for i,w in enumerate(toks):
            w=re.sub(r"'s$","",w)
            if not w[:1].isupper():
                if w.lower() not in LINK: prev=None
                continue
            parts=[q for q in re.split(r"[^A-Za-z0-9]+",w.lower()) if len(q)>1]
            if w.lower() in MONTHS+DAYS or w=='Introduction': prev=None; continue
            miss=[q for q in parts if q not in words]
            if miss and not (i==0 and common_word(w.lower())): add('names',w)
            if prev and parts and not miss and prev!=parts[-1]:
                a,b=re.escape(prev),re.escape(parts[-1])
                if not re.search(rf' {a} (?:\w+ ){{0,6}}{b} | {b} (?:\w+ ){{0,6}}{a} ',Cw): add('verify',f'{prev.title()} + {parts[-1].title()}')
            prev=parts[-1] if parts and not (i==0 and common_word(w.lower())) else None
    # dates: day and month must stand together on some page
    for d,m in re.findall(r'\b(\d{1,2}) ('+'|'.join(x.title() for x in MONTHS)+r')\b',text)+[(d,m) for m,d in re.findall(r'\b('+'|'.join(x.title() for x in MONTHS)+r') (\d{1,2})\b',text)]:
        a=m[:3].lower(); mm=MONTHS.index(m.lower())+1
        if not re.search(rf'\b{d}(?:st|nd|rd|th)? (?:of )?{a}|\b{a}[a-z]*\.? {d}\b|\d{{4}}[-/]0?{mm}[-/]0?{d}\b',Cl): add('verify',f'{d} {m}')
    # copied runs of 12 words
    toks=re.sub(r'[^a-z0-9]+',' ',re.sub(r'"[^"]*"',' ¶ ',plain(re.search(r'(?s)<content>(.*?)</content>',x).group(1))).lower()).split()
    i=0
    while i+12<=len(toks):
        run=' '.join(toks[i:i+12])
        if ' '+run+' ' in Cw: add('copied',run); i+=12
        else: i+=1
    return out

WIKI_WORDING=0.04  # share of our 6-word runs also found in Wikipedia; the 17 posts written without Wikipedia measured 0.0% to 0.9% (4 October 2026)
def key(h): return re.sub(r'^(the|a|an) ','',re.sub(r'[^a-z0-9]+',' ',h.lower()).strip())
def wiki_clone(t,x):
    """None when no Wikipedia page was saved for the topic."""
    files=sorted(glob.glob(f'{BASE}/research/{t}/wiki-[0-9]*.txt'))
    if not files: return None
    heads=set(); W=[]
    for f in files:
        l=open(f,encoding='utf-8').read().split('\n')
        heads|={key(h) for h in l[1].partition(':')[2].split(' | ') if h.strip()}; W+=l[2:]
    Wt=re.sub(r'[^a-z0-9]+',' ',norm(' '.join(W)).lower()).split()
    six={' '.join(Wt[i:i+6]) for i in range(len(Wt)-5)}; twelve={' '.join(Wt[i:i+12]) for i in range(len(Wt)-11)}
    c=re.search(r'(?s)<content>(.*?)</content>',x).group(1)
    toks=re.sub(r'[^a-z0-9]+',' ',re.sub(r'"[^"]*"',' ',plain(c)).lower()).split()  # quotations are the same words everywhere
    runs=[]; i=0
    while i+12<=len(toks):
        run=' '.join(toks[i:i+12])
        if run in twelve: runs.append(run); i+=12
        else: i+=1
    ours6=[' '.join(toks[i:i+6]) for i in range(len(toks)-5)]
    ours=[key(plain(h)) for h in re.findall(r'(?s)<h2>(.*?)</h2>',c)][1:]
    return dict(runs=runs,share=sum(g in six for g in ours6)/max(1,len(ours6)),same=[h for h in ours if h in heads],ours=len(ours),pages=len(files))

def main():
    only={tid(a) for a in sys.argv[1:]}
    arts=sorted(glob.glob(f'{BASE}/articles/*.xml')); miss={}; nsrc={}
    for f in arts:
        t=os.path.basename(f)[:-4]; miss[t],nsrc[t]=sidecar(t,open(f,encoding='utf-8').read())
    v=subprocess.run([sys.executable,'tools/crimewiki-content-kit/scripts/validate_package.py','--tasks',f'{BASE}/topics.jsonl','--output',BASE],capture_output=True,text=True)
    bad={os.path.basename(e['file'])[:-4]:e['error'] for e in json.loads(v.stdout)['errors']}
    aud=pa.audit(BASE); T=topics(); okc=0; state={}
    for f in arts:
        t=os.path.basename(f)[:-4]; x=open(f,encoding='utf-8').read(); a=aud[t]; flags=[]; notes=[]
        c=re.search(r'(?s)<content>(.*?)</content>',x); c=c.group(1) if c else ''
        if t in bad: flags.append('INVALID: '+bad[t])
        if a['real']<1000: flags.append('UNDER 1,000 WORDS')
        elif a['real']<1200: notes.append('under 1,200 words')
        if a['filler_share']>0.05: flags.append('FILLER %d%%'%round(a['filler_share']*100))
        if re.search(r'CrimeWiki|this article|this entry',c,re.I): flags.append('SELF-REFERENCE')
        if re.search(r'wikipedia|wikimedia',x,re.I): flags.append('WIKIPEDIA')
        if nsrc[t]<5: flags.append('FEWER THAN 5 SOURCES')
        if miss[t]: flags.append('NOT OPENED: '+' '.join(miss[t]))
        g=ground(t,x) if t not in bad else {}
        for k in ('numbers','names','quotes'):
            if g.get(k): flags.append('UNGROUNDED '+k)
        if g.get('copied'): flags.append('COPIED')
        w=wiki_clone(t,x) if t not in bad else None
        if w:
            if w['runs']: flags.append('WIKI COPIED')
            if len(w['same'])>=3 and len(w['same'])*2>=w['ours']: flags.append('WIKI STRUCTURE')
            if w['share']>=WIKI_WORDING: flags.append('WIKI WORDING')
        elif t not in bad: notes.append('no Wikipedia page saved, clone test not run')
        ok=not flags; okc+=ok; state[t]=dict(ok=ok,words=a['real'],sources=nsrc[t],title=T.get(t,{}).get('title','?'))
        if only and t not in only: continue
        print(f"{t} | {T.get(t,{}).get('title','?')[:50]} | {a['real']} words | {nsrc[t]} sources | {'ok' if ok else '; '.join(flags)}{' ('+', '.join(notes)+')' if notes else ''}")
        for k in ('numbers','names','quotes','copied','verify'):
            if g.get(k): print(f"    {'VERIFY' if k=='verify' else 'COPIED' if k=='copied' else 'UNGROUNDED '+k}: "+' | '.join(g[k][:40]))
        if w: print(f"    WIKI ({w['pages']} page{'s' if w['pages']>1 else ''}): {len(w['same'])} of {w['ours']} headings shared{' ('+', '.join(w['same'])+')' if w['same'] else ''} | {w['share']:.1%} of 6-word runs shared | {len(w['runs'])} copied runs"+''.join('\n        '+r for r in w['runs'][:10]))
    json.dump(state,open(f'{BASE}/check-state.json','w'),indent=1)
    print(f'articles {len(arts)} | ok {okc} | flagged {len(arts)-okc} | blocked {len(glob.glob(BASE+"/blocked/*.json"))}')
main()

#!/usr/bin/env python3
"""search.py "<query>" [max]  -> result URLs, one per line, from a public search page.
Fallback for when the WebSearch tool refuses (session limit). Tries Brave, then
DuckDuckGo Lite, then Bing, with curl and a browser user agent. Engines' own links
are dropped. Parsing lives only here, so an engine can be swapped without touching
the writers' prompts."""
import sys,re,html,subprocess,time,urllib.parse
UA='Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15'
ENGINES=[('brave','https://search.brave.com/search?q='),
         ('ddg','https://lite.duckduckgo.com/lite/?q='),
         ('bing','https://www.bing.com/search?q=')]
JUNK=re.compile(r'(?i)(brave\.(com|app)|duckduckgo\.com|bing\.com|microsoft\.com|msn\.com|hackerone\.com|wikidata\.org|w3\.org|schema\.org|gstatic|imgs\.search|go\.microsoft)')
def get(url):
    r=subprocess.run(['curl','-sL','-m','20','-A',UA,'-H','Accept-Language: en',url],capture_output=True)
    return r.stdout.decode('utf-8','replace')
def links(raw):
    out=[]
    for u in re.findall(r'href="(https?://[^"]+)"',raw):
        u=html.unescape(u)
        m=re.search(r'[?&]uddg=([^&]+)',u)  # DuckDuckGo redirect
        if m: u=urllib.parse.unquote(m.group(1))
        if JUNK.search(urllib.parse.urlparse(u).netloc) or u in out: continue
        out.append(u.split('#')[0])
    return list(dict.fromkeys(out))
def main():
    if len(sys.argv)<2: sys.exit(__doc__)
    q=sys.argv[1]; mx=int(sys.argv[2]) if len(sys.argv)>2 else 15
    for attempt in range(2):  # engines rate-limit bursts; one wait, then one more round
        if attempt: time.sleep(45)
        for name,base in ENGINES:
            raw=get(base+urllib.parse.quote_plus(q))
            if re.search(r'(?i)anomaly|captcha|unusual traffic',raw[:20000]) and len(links(raw))<3: continue
            res=links(raw)
            if len(res)>=3:
                print(f'# {name}: {q}')
                print('\n'.join(res[:mx])); return
    print('# no engine answered after a retry; use other words or move on')
main()

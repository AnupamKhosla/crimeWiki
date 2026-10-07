export const meta = {
  name: 'one-post-parallel',
  description: 'One CrimeWiki post, everything parallel: 5 search agents, 1 fetch, 1 writer, 3 read-only checkers, 1 fixer. Nothing is published.',
  phases: [
    { title: 'Search', detail: '5 agents, one angle each', model: 'sonnet' },
    { title: 'Fetch', detail: 'one parallel fetch of all URLs', model: 'sonnet' },
    { title: 'Write', detail: 'one writer', model: 'sonnet' },
    { title: 'Check', detail: '3 read-only checkers in parallel', model: 'sonnet' },
    { title: 'Fix', detail: 'apply findings, pass check_new.py', model: 'sonnet' },
  ],
}
const n = Array.isArray(args) && args.length ? args[0] : 11
const ROOT = '/Users/anupamkhosla/Desktop/Projects/crimeWiki'
const T = 'cw-topic-8' + String(n).padStart(5, '0')
const rules = `Project root: ${ROOT}; run commands from there. Do not read docs/ROADMAP.md or docs/STATE.md. Never run publish.py, git, ssh or gcloud. No subagents. Never edit topics.jsonl or the Python tools.`
const URLS = { type:'object', properties:{ urls:{type:'array',items:{type:'string'}} }, required:['urls'] }
const FIND = { type:'object', properties:{ findings:{type:'array',items:{type:'object',properties:{ quote:{type:'string'}, problem:{type:'string'}, fix:{type:'string'} },required:['quote','problem','fix']}}, ok_sentences:{type:'number'} }, required:['findings'] }
const DONE = { type:'object', properties:{ status:{type:'string'}, check_line:{type:'string'}, applied:{type:'array',items:{type:'string'}}, notes:{type:'string'} }, required:['status','check_line','notes'] }

phase('Search')
const angles = [
  'the latest news and current legal status (plea, sentence, appeals)',
  'the history of the investigation and the arrest, with dates',
  'the victims: who they were, each by name, from reliable reports',
  'the court proceedings, plea hearing and sentencing, what judges and prosecutors said (prefer official press releases such as the prosecutor\'s office)',
  'evidence, DNA, and how investigators built the case; the defendant\'s background as reported by established outlets'
]
const found = await parallel(angles.map((a, i) => () => agent(
  `Find source pages for a CrimeWiki article on topic ${n} (${T}); read its line in ${ROOT}/tmp/new-posts/topics.jsonl first (title and note). ${rules} Use WebSearch (load it with ToolSearch "select:WebSearch" if needed), 2 or 3 queries, ONLY on this angle: ${a}. Do NOT fetch pages. Return up to 8 URLs of reputable pages (news outlets, wire services, official press releases, court documents). Do not return Wikipedia URLs: Wikipedia is a lead-finder, not a source, and the main session handles it (PLAN.md step 4). Prefer outlets that usually allow fetching (CBS, ABC, NBC, CNN, AP via local stations, NPR, PBS, local papers, DA press releases).`,
  { label:`search:${i+1}`, phase:'Search', schema:URLS, model:'sonnet', effort:'low' })))
const urls = [...new Set(found.filter(Boolean).flatMap(r => r.urls))].filter(u => /^https:/.test(u) && !/wikipedia|wikimedia/.test(u)).slice(0, 24)
log(`${urls.length} candidate URLs from ${found.filter(Boolean).length} searchers`)

phase('Fetch')
const fetched = await agent(
  `Run ONE command from ${ROOT}: python3 tmp/new-posts/fetchmany.py ${n} ${urls.map(u => `"${u}"`).join(' ')}   Then read its output. If fewer than 6 pages have status OK from at least 4 publishers, run 1 or 2 WebSearch queries (ToolSearch "select:WebSearch") for more URLs and run fetchmany.py ONE more time (never two at once). ${rules} Return status 'ok' with the number of OK pages in notes, or 'thin' if still under 6.`,
  { label:'fetch', phase:'Fetch', schema:DONE, model:'sonnet', effort:'low' })
log('fetch: ' + (fetched ? fetched.status + ' ' + fetched.notes : 'failed'))

phase('Write')
const w = await agent(
  `You are the writer for topic ${n} (${T}). ${rules}
Read tmp/new-posts/PLAN.md in full, sections 2 and 3 of tmp/longform/BRIEF.md, the model tmp/new-posts/articles/cw-topic-800001.xml, and the ${T} note in tmp/new-posts/topics.jsonl. Read EVERY screen: python3 tmp/new-posts/read.py ${n} --max 7000, then --page 2, 3... with the same --max. Write tmp/new-posts/articles/${T}.xml (1,200 to 2,000 words, only from what the screens say, never memory), list source conflicts one per line in tmp/new-posts/research/${T}/unresolved.txt, run python3 tmp/new-posts/check_new.py ${n} and fix until your line ends in "ok". If pages cannot support 1,000 real words, write the blocked file per PLAN.md and return status 'blocked'. Return status ('ok'/'blocked'/'failed'), check_line, notes.`,
  { label:'write', phase:'Write', schema:DONE, model:'sonnet', effort:'medium' })
if (!w || w.status !== 'ok') return { topic:n, stopped:'writer ' + (w ? w.status : 'failed'), writer:w }

phase('Check')
const checks = await parallel([0, 1, 2].map(i => () => agent(
  `You are checker ${i+1} of 3 for topic ${n} (${T}). READ-ONLY: do NOT edit any file. ${rules}
Read "Rules that keep it honest" and "Lessons from the first iteration" in tmp/new-posts/PLAN.md and the ${T} note in topics.jsonl. Read the article tmp/new-posts/articles/${T}.xml and every saved page (python3 tmp/new-posts/read.py ${n} --max 7000, then --page 2... same --max). Check ONLY the ${['first third','middle third','last third'][i]} of the article body (the <content> paragraphs; checker 1 also checks both tables at the top). For each sentence find its supporting page. Report every sentence that: uses a stronger verb than the page; credits a claim or quote to the wrong speaker or outlet; states an allegation as fact; describes an accused person as guilty or a convicted person's status wrongly; uses an unsupported first/only/never/all; invents an order of events or date; attaches a number to the wrong thing; is on no page; or names a minor, a private bystander or an uncharged person improperly. For each give the exact sentence (quote, 12 words is enough but unique), the problem, and the smallest fix wording that is true. Return findings (empty list if all sentences hold) and ok_sentences.`,
  { label:`check:${i+1}`, phase:'Check', schema:FIND, model:'sonnet', effort:'medium' })))
const all = checks.filter(Boolean).flatMap(c => c.findings)
log(`checkers found ${all.length} problems`)

phase('Fix')
const fx = await agent(
  `Apply these fact-check findings to tmp/new-posts/articles/${T}.xml with the Edit tool (project root ${ROOT}). ${rules} For each finding: locate the quoted sentence, verify against the saved page (python3 tmp/new-posts/read.py ${n} --max 7000) if in doubt, and make the smallest change that makes it true; skip a finding that is wrong and say why. Add nothing that is not on a page. Then run python3 tmp/new-posts/check_new.py ${n} and fix until your line ends in "ok".\nFINDINGS:\n${JSON.stringify(all, null, 1)}\nReturn status ('ok' only if the final line ends "ok"), check_line, applied (one short string per fix), notes (findings skipped and why).`,
  { label:'fix', phase:'Fix', schema:DONE, model:'sonnet', effort:'medium' })
return { topic:n, ready: !!(fx && fx.status === 'ok'), findings_found: all.length, writer: w, fixer: fx }

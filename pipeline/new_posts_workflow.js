export const meta = {
  name: 'crimewiki-new-posts',
  description: 'Write and fact-check new CrimeWiki articles: one Sonnet writer and one Sonnet verifier per topic. Nothing is published.',
  whenToUse: 'Batch-write new posts from pipeline/topics.jsonl. Pass the topic numbers as args, e.g. [8, 9, 10].',
  phases: [
    { title: 'Write', detail: 'one agent per topic: research, write, pass check_new.py', model: 'sonnet' },
    { title: 'Verify', detail: 'independent agent re-reads each draft against the saved pages and fixes over-claims', model: 'sonnet' },
  ],
}

const topics = Array.isArray(args) && args.length ? args : [5, 6, 7]
const ROOT = '/Users/anupamkhosla/Desktop/Projects/crimeWiki'
const tid = n => 'cw-topic-8' + String(n).padStart(5, '0')

const WRITE_SCHEMA = {
  type: 'object',
  properties: {
    task_id: { type: 'string' },
    status: { type: 'string', enum: ['ok', 'blocked', 'failed'] },
    words: { type: 'number' },
    sources: { type: 'number' },
    check_line: { type: 'string' },
    title_change: { type: 'string' },
    notes: { type: 'string' },
  },
  required: ['task_id', 'status', 'check_line', 'title_change', 'notes'],
}

const VERIFY_SCHEMA = {
  type: 'object',
  properties: {
    task_id: { type: 'string' },
    ok: { type: 'boolean' },
    final_check_line: { type: 'string' },
    fixes: { type: 'array', items: { type: 'string' } },
    remaining_concerns: { type: 'array', items: { type: 'string' } },
  },
  required: ['task_id', 'ok', 'final_check_line', 'fixes', 'remaining_concerns'],
}

const writePrompt = n => `You are one writer in a batch. Your job is ONE finished, checked article for CrimeWiki topic number ${n} (task id ${tid(n)}).
Project root: ${ROOT}. Run every command from there. Do not read docs/ROADMAP.md or docs/STATE.md; what you need is below and in two files.

1. Read pipeline/PLAN.md in full (the loop, the rules, the lessons) and sections 2 and 3 of pipeline/BRIEF.md (voice and file format). Use pipeline/articles/cw-topic-800001.xml as the model for the format.
2. Your topic is the line for ${tid(n)} in pipeline/topics.jsonl: title, category, seed query, note. The note holds legal cautions. Follow them.
3. Research. If pipeline/research/${tid(n)}/index.jsonl already lists 6 or more pages with status OK, skip searching. Otherwise run 2 or 3 WebSearch queries (load the tool with ToolSearch "select:WebSearch" if it is not available) and fetch 10 to 16 URLs in ONE call: python3 pipeline/fetchmany.py ${n} "<url>" "<url>" ... You need at least 6 OK pages from at least 4 publishers. Then do PLAN.md step 4: Wikipedia is a lead-finder, never a source.
4. Read EVERY screen: python3 pipeline/read.py ${n} --max 7000, then --page 2, --page 3 and so on, with the same --max each time. For a page that was cut: --only <k> --max 40000.
5. Write pipeline/articles/${tid(n)}.xml with the Write tool. 1,200 to 2,000 words, only from what the screens say, never from memory. Put source conflicts, one per line, in pipeline/research/${tid(n)}/unresolved.txt.
6. Run python3 pipeline/check_new.py ${n} and fix the article until YOUR line ends in "ok". Lines for other topics are not your concern.
7. Re-read your draft against the screens once and fix sentences that say more than the page does. (PLAN.md step 9, the independent check, is done by another agent after you.)

Hard limits. Touch only the files of topic ${n}: its article, its research folder, its blocked file. Never edit topics.jsonl, the Python tools or other articles. Never run publish.py, git, ssh or gcloud. Do not start subagents. If the title in topics.jsonl is wrong given what the pages establish, do not edit it; put the better title in title_change. If the pages cannot support 1,000 words of real facts, write pipeline/blocked/${tid(n)}.json as PLAN.md describes and return status "blocked". If your line is still not "ok" after two rounds of fixes, return status "failed".

Return: task_id, status, words, sources, check_line (your final line from check_new.py), title_change (empty string if none), notes (one or two sentences the owner should know).`

const verifyPrompt = n => `You are an independent fact-checker for CrimeWiki topic ${n} (task id ${tid(n)}). Another writer produced pipeline/articles/${tid(n)}.xml. It passed an automatic check of numbers, names and quotations. That check cannot see a sentence that claims more than its source says. Finding and fixing those sentences is your whole job. Assume there are several: each of the first four articles had between 4 and 9.
Project root: ${ROOT}. Run every command from there. Do not read docs/ROADMAP.md or docs/STATE.md.

1. Read the sections "Rules that keep it honest" and "Lessons from the first iteration" in pipeline/PLAN.md, and the note for ${tid(n)} in pipeline/topics.jsonl.
2. Read the article. Then read every screen of the saved pages: python3 pipeline/read.py ${n} --max 7000, then --page 2 and so on with the same --max. For a page that was cut: --only <k> --max 40000.
3. Go through the article sentence by sentence, the two tables included. For each one, find the page that supports it. Look for: a stronger verb than the page uses; a claim or quotation credited to the wrong speaker or outlet; an allegation stated as fact; an accused person described as if guilty; "first", "only", "never" or "all" that is not on the page; an order of events or a date the page does not give; a number attached to the wrong thing; a detail that is on no page; anything about a living person or a minor that the topic note forbids.
4. Fix each problem in the article with the Edit tool. Make the smallest change that makes the sentence true. Attribute instead of deleting where you can. Add nothing that is not on a page. Do not pad. Do not use web search: the saved pages are the only evidence.
5. Run python3 pipeline/check_new.py ${n} and fix until the line for ${tid(n)} ends in "ok".

Hard limits. Edit only that article and its unresolved.txt. Never run publish.py, git, ssh or gcloud. Do not start subagents.

Return: task_id; ok (true only if the final check line ends in "ok"); final_check_line; fixes (one string per fix: the old claim, then what you changed, 20 words at most); remaining_concerns (what you could not settle from the pages; empty list if none).`

log(`Topics ${topics.join(', ')}: write, then verify. Nothing will be published.`)

const results = await pipeline(
  topics,
  n => agent(writePrompt(n), { label: `write:${n}`, phase: 'Write', schema: WRITE_SCHEMA, model: 'sonnet', effort: 'medium' }),
  (w, n) => {
    if (!w || w.status !== 'ok') {
      log(`Topic ${n}: writer returned ${w ? w.status : 'nothing'}; verification skipped.`)
      return { topic: n, write: w, verify: null }
    }
    return agent(verifyPrompt(n), { label: `verify:${n}`, phase: 'Verify', schema: VERIFY_SCHEMA, model: 'sonnet', effort: 'medium' })
      .then(v => ({ topic: n, write: w, verify: v }))
  }
)

const done = results.filter(Boolean)
return {
  ready_to_publish: done.filter(r => r.verify && r.verify.ok).map(r => r.topic),
  not_ready: topics.filter(n => !done.some(r => r.topic === n && r.verify && r.verify.ok)),
  results: done,
}

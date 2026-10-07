# CrimeWiki: content scale plan and day-2 package audit

Living document · started 3 October 2026 · owner updates welcome

Start with `docs/ROADMAP.md`; this file holds the content detail.

**Status, 4 October 2026: this file is a record, not the plan.** It holds the
day-2 audit, the Sonnet pilot's measurements, the scale limits and the
database sizing, as written on 3 October. The current goals, the order of
work and the open decisions are in `docs/ROADMAP.md`; the rules are in
`AGENTS.md`; the loop for new posts is `pipeline/PLAN.md`.

Superseded since it was written:

- Goal 5 below (write locally, then promote). There is no local database.
  New posts go straight into the live database with `publish.py`.
- Sections 5 and 6 (one Sonnet worker per article, and the plan built on
  that). The main session writes; agents run only on the owner's yes, and
  every batch gets an independent check before publishing.
- Section 9 (open questions). The owner settled them on 3 October: long-form
  is required, and session state lives in `docs/STATE.md`.

## 1. Ultimate goals

1. **Replace every Wikipedia-scraped post with original writing.** The live
   site has 1,121 posts. This is essentially done: the 6 September release
   rewrote 1,116 of them and it is live (see `ROADMAP.md` section 2).
2. **Grow the catalogue far beyond that.** The owner's north star is 100,000
   articles, and eventually 1,000,000.
3. **Long, well-researched articles**, not short stubs. Length must come from
   evidence, never from filler.
4. **Low running cost.** Writing happens on a flat subscription (the owner's
   $100 Claude plan, and Grok on a separate account), not on per-token API
   billing.
5. **Write locally, then promote.** Articles land in the local MySQL database,
   the owner reviews them, and approved changes go to the VPS as a
   backup-aware, content-only patch.
6. **The VPS must keep working as the catalogue grows.** It has about 1 GB of
   RAM and 13 GiB of free disk.

Section 7 explains why goal 2 conflicts with goals 3 and 4, and what to do
about it.

## 2. Day-2 package audit

Package: `~/Downloads/day-002-long-final.zip`, copied to
`tmp/day-002-long-final/`. It was produced on 30 September 2026 between 10:26
and 16:02 UTC.

| | Day 1 (`day-001-final-1k`) | Day 2 (`day-002-long-final`) |
|---|---|---|
| Topics assigned | 1,000 | 998 |
| Articles written | 665 | 587 |
| Topics blocked | 335 | 411 |
| Median words per article, as delivered | 334 | 1,441 |
| Median **real** words per article | 334 | **346** |
| Share of text that is templated filler | 0% | **71%** |
| Articles citing Wikipedia | 0 | 0 |
| Median sources per article | 3 | 3 |

"Real words" excludes sentences that are a shared template with the topic name
swapped in, and sentences repeated inside the same article. The detector is
`tmp/day-002-audit/padding_audit.py`. It scores day 1 at 0% filler, so it is
not flagging ordinary crime-reporting language.

**The day-2 articles are not longer. They are day-1-length articles with
about 1,100 words of padding added.**

### 2.1 What the padding looks like

Article `cw-topic-001344` (2017 Bhopal–Ujjain train bombing) has about 350
words of sourced reporting. It then has nine sections titled "Further
operational context (1)" to "(9)". Each one repeats the same two paragraphs
with only the number changed, for example:

> Additional sourced framing for the 7 March 2017 Bhopal–Ujjain passenger
> train pressure-pipe bomb near Jabri/Jabdi, Shajapur: cross-check figures
> against the intro-data band, re-read claim versus official or court
> language, and avoid relocating the event. Expansion note 4 stays
> topic-locked to the 7 March 2017 Bhopal–Ujjain passenger train…

The same numbered section headings recur across the package: "Source hierarchy
and doubt (4)", "Method and target selection (6)", and "Editorial discipline
restated (10)" each appear in about 100 articles. One sentence, "CrimeWiki
preserves those elements without inventing unsourced dialogue", appears in 502
of the 587 articles.

### 2.2 Three tiers

| Tier | Articles | Filler share | Real words | Task IDs |
|---|---|---|---|---|
| A, genuinely long | 32 | under 10% | 1,160 to 1,640 | 1001–1030, 1041, 1042 |
| B, partly padded | 13 | 10% to 50% | 640 to 1,220 | 1031, 1032, 1034, 1053, 1054, 1086, 1087, 1089, 1094, 1098, 1566, 1916, 1923 |
| C, mostly padding | 542 | 50% or more | mostly 250 to 500 | everything else |

The writer produced honest long articles for the first 30 or so topics, then
switched to padding from topic 1033 onward. Per-article results are in
`tmp/day-002-audit/day2_padding.json`.

### 2.3 Other findings

- **Sources are mostly real.** I requested 234 cited URLs (all 115 from tier A
  and 119 from a random 45 tier-C articles). 154 returned 200, 60 were refused
  as bot traffic (401/403), and 9 returned 404, which is about 4%. The tier-A
  dead links are in articles 1017, 1018, 1019 (three links) and 1041. A 200
  response shows the page exists, not that it supports the claim.
- **Every article talks about itself.** All 587, including tier A, contain
  phrases such as "This CrimeWiki article reconstructs…", "CrimeWiki locks…",
  and "Identity lock:". This is the content kit's working vocabulary leaking
  into public prose. Tier A has 156 such mentions across 32 articles.
- **No inline source links** in any article body.
- **`<related>` is empty** in every article, as the kit instructs.
- **Most blocks come from a bad topic queue, not failed research.** Of 411
  blocked topics, 166 were not crime topics at all (for example "1936 United
  States presidential election", "Lisle, Illinois"), 114 were generic terms
  ("mobster", "Vehicular attack"), 21 had too few sources, 3 were duplicates,
  1 was a defamation risk, and 106 had other reasons I did not classify.

### 2.4 Why it happened

1. The day-2 prompt set a hard floor of about 1,200 words. The blocked records
   quote it: "honest ≥1200-word long-form without padding".
2. The writer spent about 34 seconds per topic (998 topics in 5.5 hours).
   That is enough for a 350-word summary of two or three sources, not for a
   1,200-word researched article.
3. The validator could not see padding. `validate_package.py` only requires
   1,200 **bytes** of content text and checks structure. A padded article
   passes.

A word minimum with no padding check invites exactly this result.

## 3. What to do with the day-2 package (proposed)

- **Do not import the package as delivered.** Publishing 542 pages that are
  71% repeated template text is the pattern Google's spam policy on scaled,
  low-value content describes. It would put AdSense approval at risk, which
  is the opposite of the goal.
- **Tier A (32 articles):** worth keeping. Before import: remove the
  self-referential sentences, replace the 6 dead links, then import to the
  local database for owner review.
- **Tier B (13 articles):** strip the filler sections, then review by hand.
- **Tier C (542 articles):** strip the filler mechanically. What remains is a
  sourced 250 to 500 word core, the same grade as day 1. Then either import
  them as short articles, or use each core and its research sidecar as the
  starting point for a properly researched expansion (section 5).

### 3.1 Salvage test, 3 October 2026

Are the two packages of any use, or does everything need researching again?
I tested it.

- **Day 2 with the filler stripped.** `tmp/day-002-audit/strip_filler.py`
  writes cleaned copies to `tmp/day-002-salvaged/` and leaves the original
  package alone. All 587 articles survive with an Introduction and at least
  two sections. They score 0% filler. The median is 306 words (10th
  percentile 204, 90th percentile 566); 9 fall under 150 words. 43 files
  still contain the word "CrimeWiki" and need a second pass.
- **Day 1.** 665 articles, 0% filler, median 334 words. In a sample of 170
  source links, 1 was dead. 230 articles contain self-referential phrases
  and need the same cleanup. 504 already carry inline source links.

So about 1,250 short, honestly sourced articles exist today. **The owner's
standard is long-form: 1,000 words or more.** By that standard only about 35
of them qualify (day 2's long articles). The rest are research seeds, not
publishable pages. Turning a seed into a long article means new research,
but its source list gives the worker known-good starting links. The pilot in
`tmp/pilot-001/` does exactly that.

### 3.2 Length standard

Aim for 1,200 to 2,000 words. 1,000 words of real content is the floor for
publishing. For comparison, the rewritten legacy posts now live on the site
have a median of 1,035 words, and 80% of them fall between 576 and 1,876.
Encyclopedia articles on well-documented cases commonly run 1,500 to 4,000
words, so this standard sits at the lower end of that range.

The floor is measured on real words, after the padding audit. A writer is
told to stop when the evidence runs out; a topic that cannot reach the floor
is held or blocked.

> **Owner decision, 3 October 2026: no Sonnet workers.** The main Claude Code
> session researches and writes each article itself, working through
> `tmp/rewrite-queue/queue.jsonl`. Sections 4 to 5.2 below are kept as the
> record of what was recommended and measured before that decision. The
> current loop is in `docs/STATE.md`.

## 4. Which model should write

**Recommendation: Sonnet 5.5 writes; the stronger model only orchestrates and
audits.**

- The job is search, read sources, and write a structured article. Sonnet
  does that well, and it draws less of the plan's usage allowance per article
  than Opus or Fable.
- **Effort: start at medium or high, not xhigh.** Extra reasoning effort
  spends allowance on thinking. The limiting factor here is how many sources
  get read, not how hard the reasoning is. The pilot in section 6 compares
  effort levels on the same topics so this is measured, not assumed.
- Use the stronger model for the parts where a mistake is expensive: checking
  batches for padding, spot-checking claims against sources, and deciding
  what is fit to import.

## 5. How to run it in Claude Code (proposed)

**Do not write the articles in one long chat.** Every page a research step
reads stays in the conversation, and every later step pays to re-read all of
it. Writing in the main chat and compacting at 300k to 400k tokens means
paying for a large context on every step, and compaction can drop the writing
rules partway through a batch.

**Use one fresh worker per article instead:**

1. The main session reads the task list and starts a worker for one topic
   (the Agent tool with the Sonnet model).
2. The worker researches the topic with web search, writes
   `articles/<task>.xml` and `research/<task>.json` into the project's `tmp/`
   folder, and replies with one line: done or blocked, and why.
3. The worker's context is discarded. The main session stays small, so no
   compaction is needed.
4. After each batch the main session runs the validator, the padding audit,
   and a link check, and only then offers the batch for import.

Each worker gets the existing five-block contract plus these rules, which
day 2 shows are needed:

- No word minimum. Write as much as the opened sources support, then stop.
- Never mention CrimeWiki, the article itself, or the research process in the
  article body.
- Record a block when sources are thin. A block is a valid result.
- Every source listed must have been opened in that session.

### 5.1 Worker size and who orchestrates (owner input, 3 October 2026)

- The owner will run the main session on Opus 5.5 at medium or high effort,
  because access to the larger model is limited. The main session therefore
  does as little as possible: it starts workers and runs the scripted gates.
  Quality checks live in scripts (validator, padding audit, link check), not
  in the main model's judgement.
- Workers are Sonnet 5.5 subagents. Each has its own context, which is thrown
  away when it finishes, so the owner never compacts anything.
- **Worker size is an open question that the pilot measures.** Two costs
  pull in opposite directions:
  - Every new worker pays again for the fixed instructions (contract,
    research rules, tool definitions) unless they are served from cache.
  - Every article a worker has already written leaves its fetched pages in
    context, and each later step re-reads them, so long runs get more
    expensive per article as they go.
- Owner's evidence, from an earlier DeepSeek API run: one fresh worker per
  article got no cache hits on the fixed instructions and used allowance
  fast. Writing 20 to 30 articles per run was far more efficient.
- Day-2 evidence: one long run produced about 30 honest articles and then
  started padding.
- The pilot therefore runs three worker sizes, 1, 5, and 20 to 30 articles,
  and records allowance used per article, real word count, and filler share
  for each. The size with the lowest cost per good article becomes the
  default.
- Rules that hold at any size: the fixed instructions come first and are
  identical for every worker, with the topic list last, so caching can
  apply. The padding gate runs on every batch. A worker stops and reports
  before its context fills; it never relies on compaction.
- Proposed, needs owner opt-in: a workflow script launches the waves, so the
  main model is not consulted for each article and only reviews the gate
  results at the end of a batch.

### 5.2 Pilot results, 3 October 2026

Run in `tmp/pilot-001/` with the brief in `WORKER_BRIEF.md`. The owner's
session usage reached 20%, so I stopped the pilot early: one single-article
worker finished, the five-article worker was stopped during its third
article, and two single-article workers were stopped mid-research. Token
counts come from each worker's log via `tmp/day-002-audit/worker_usage.py`.

**Quality.** Three articles finished. All pass the kit validator, score 0%
filler, and contain no self-reference.

| Task | Topic | Words | Sources |
|---|---|---|---|
| `cw-topic-001307` | University of Texas tower shooting | 2,484 | 18 |
| `cw-topic-001509` | 2016 New York and New Jersey bombings | 1,502 | 13 |
| `cw-topic-001400` | Carcassonne and Trèbes attack | 1,440 | 16 |

**Cost of one article.** The finished single-article worker took 33 steps
and 12 minutes. It wrote 150,000 tokens to cache, re-read 2.4 million from
cache, and produced 68,000 output tokens. Most of that output is reasoning,
not article text: workers inherited the session's xhigh effort.

**Fixed cost of a new worker is small, and it is cached.** A worker's first
step is about 31,000 tokens. For every worker after the first, 17,000 of
those were served from cache. That is about 4% of an article's total. The
DeepSeek problem (no cache hits on a new session) does not occur here.

**Context grows by 70,000 to 150,000 tokens per article.** The five-article
worker stood at 171,000 tokens partway through its third article. A worker
therefore cannot hold 20 to 30 articles; about 3 to 5 is the ceiling, and
each article costs more than the one before because the worker re-reads
everything already in its context on every step.

**Decisions from this:**

- **1 to 3 articles per worker.** Not 20 to 30.
- **Lower the workers' effort.** Reasoning output was about 40% of each
  worker's cost. This needs a writer agent definition under
  `.claude/agents/` (a new file, so it needs the owner's approval).
- **Cap research.** 13 to 18 sources per article is more than the standard
  needs. A cap of about 8 sources should roughly halve the reading cost.
- **Run production from a fresh, small main session.** The session that ran
  this pilot had grown to about 200,000 tokens on the most expensive model,
  and every step re-read all of it. That, not the workers alone, is why 20%
  of the session allowance went so quickly.

Still unmeasured: how many articles one session allowance buys. The plan's
usage meter is the only source for that. Note the percentage before and
after the next batch of 5 articles.

## 6. Plan, in order (each step needs owner approval)

1. **Decide the fate of day 2** (section 3) and confirm whether day 1 was
   already imported into the local database.
2. **Add a padding gate to the content kit.** Move the audit script into
   `tools/crimewiki-content-kit/scripts/` and make a package fail when filler
   exceeds a set share. This protects every future batch from any writer,
   Grok included.
3. **Salvage tier A.** Clean the 32 articles and import them locally for
   review.
4. **Run a Sonnet pilot of about 30 articles** across the three worker sizes
   in section 5.1. Measure allowance used per article, time per article, real
   word count, filler share, and dead-link rate. Run 3 of the topics at two
   effort levels and compare.
5. **Fix the topic queue.** Filter out non-crime titles, generic terms and
   duplicates before any research is spent. 280 of day 2's 998 topics (28%)
   were wasted this way.
6. **Set a steady daily batch size** from the pilot's measurements and run
   it: write, gate, import locally, review, promote with a content-only
   release.
7. **Finish the legacy posts.** Only posts 1, 2, 6 and 7 and one unchanged
   post remain; after that every batch is new topics. New posts also need a
   publisher that can insert rows (see `ROADMAP.md` track A, step 3).

## 7. How far this method can scale

- A researched 1,200-word article needs several searches and several full
  source pages read. The $100 plan has rolling usage limits. My estimate,
  before the pilot, is **dozens to low hundreds of articles per day**. The
  pilot replaces this estimate with a measurement.
- At 100 articles a day, 100,000 articles takes about 2.7 years and 1,000,000
  takes about 27 years.
- Grok's 587 articles in 5.5 hours came from spending about 34 seconds per
  topic, which is why the real content is 350 words.

Long, honest, and high-volume cannot all be had on one flat subscription. The
workable order is: finish the last few legacy posts, reach 5,000 good
articles, then 10,000, and review search traffic and indexing at each point before
committing to more. Volume beyond that needs either more writing capacity or
acceptance of shorter articles. It should not come from padding.

There is also a supply limit. A queue of 2,000 topics already produced 280
non-crime or generic titles. A million distinct, well-sourced crime topics
may not exist.

## 8. Database at scale

Measured from the live dump of 6 September 2026: 1,121 posts hold 11.1 MB of
row data, about 9.9 KB per post. The dump gzips to 3.6 MB, a ratio of 3.1 to
1. The VPS has 29 GiB of disk with 13 GiB free, about 1 GB of RAM, and a
128 MB InnoDB buffer pool. A long article is about 13 KB of XML.

| Posts | Raw content | Fits on the current disk? |
|---|---|---|
| 1,121 (today) | 11 MB | yes |
| 10,000 | about 130 MB | yes |
| 100,000 | about 1.3 GB | yes, uncompressed |
| 1,000,000 | about 13 GB | no, not without compression or a larger disk |

**Disk is not the first limit. Search is.** Advanced search runs
`content LIKE '%term%'`, which reads every article on every search. At
100,000 posts that is about 1.3 GB read per search with a 128 MB buffer
pool. Search has to move to a FULLTEXT index, or an external index, at
around 10,000 posts. That changes substring matching behaviour, a decision
the 1 September handoff already left with the owner.

Compression options, for when disk does matter:

| Option | Saving | Cost |
|---|---|---|
| InnoDB compressed row format | roughly half | more CPU and buffer-pool pressure on a 1 GB machine; no PHP changes |
| Gzip the `content` column in the app | about 3 to 1 | SQL can no longer search inside content, so a separate search index is required; `post.php` must decompress |
| Larger disk | none needed | a small monthly charge per GB; no code changes |

**Recommendation: do nothing about compression now.** Revisit at 10,000
posts with real measurements. A larger disk is the simplest fix; compression
is only worth its cost near 1,000,000 posts.

## 9. Open questions for the owner

1. Was the day-1 package imported into the local database? I could not check
   because Docker was not running.
2. Should the 542 tier-C articles be imported as short articles after
   stripping, or held back for expansion?
3. Is a short, honest article (300 to 500 words) acceptable for publication,
   or is long-form a firm requirement?
4. The day-2 manifest marks all 587 as `action: create` with no `post_id`.
   Do these map to rows seeded by `scripts/seed_new_crime_topics.php`, or do
   they need a new importer?
5. `supermemory` is not installed in this shell, so the session-state step in
   `AGENTS.md` cannot run. Should handoff notes stay in `CHAT_HANDOFF_*.md`
   files instead?

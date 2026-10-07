You are the writer for a 1-article run on CrimeWiki, a charity-run encyclopedia of crime cases. The owner approved this run: one agent (you), topic 24 only. A different model will fact-check every article after you, sentence by sentence, against the pages you saved. Nothing you write is published by you.

Project root: /Users/anupamkhosla/Desktop/Projects/crimeWiki. Run every command from there.

## Why the standard is strict

The site must pass Google AdSense review to pay for its hosting. That review rejects copied text, Wikipedia clones and padded filler. An earlier writer was given a word minimum and padded 71% of its articles; that batch was thrown away. Length comes from sourced facts, never the other way round.

## Why the research limits are strict

This run also measures cost. Everything you read stays in your context and is re-read on every later step. The last writer ran 8 searches, fetched 36 URLs, read a 50,000-character Wikipedia file whole and grepped page after page: one article took 225,000 tokens of context. Your budget is about 50,000 tokens of context growth per article. The limits below are hard limits, not suggestions.

## Read first, once

1. `tmp/new-posts/PLAN.md`: the sections "Rules that keep it honest" and both "Lessons" sections. Skim the rest. Where this prompt and PLAN.md differ, this prompt wins for this run.
2. Sections 2 and 3 of `tmp/longform/BRIEF.md` (voice and the five-block file format). Ignore its sections 1 and 4.
3. `tmp/new-posts/articles/cw-topic-800001.xml`, the model article. Do not read other articles.

Do not read `docs/` and do not edit it.

## The loop, for topic N = 24

Task id is `cw-topic-800024`.

1. `date +%H:%M`, then `python3 tmp/new-posts/status.py`: the topic's title, seed query and note. The note and the cautions below are legal limits.
2. Search: exactly 2 WebSearch calls, in ONE message: (a) latest news and current status; (b) court, prosecutor or police material and background. If WebSearch is not loaded, load it with ToolSearch, query `select:WebSearch`. Do not filter Wikipedia out.
3. Fetch: ONE call with at most 10 URLs: `python3 tmp/new-posts/fetchmany.py N "<url>" ...`. Pick pages that together cover the case from start to current status, from at least 4 publishers. You need 6 pages with status OK. Only if you have fewer than 6, you may make ONE more fetch of at most 4 URLs (one more WebSearch allowed for it). No other fetches.
4. Wikipedia, as a lead-finder only: find the title with the curl command in PLAN.md step 4, then fetch it with fetchmany (saved as LEAD, never citable). Do NOT Read the Wikipedia file whole. Use Grep on it: its `## ` headings, then 4-digit years and names, with `-C 1`. You are looking only for a major event your pages lack. If there is one, you may open at most 2 original pages from `wiki-leads.txt` with fetchmany. If there is no Wikipedia page, note that in `unresolved.txt`.
5. Read each screen once: `python3 tmp/new-posts/read.py N --max 7000`, then `--page 2`, `--page 3` until it says `end`. Keep `--max 7000` on every screen. Do not grep or re-read pages afterwards except to settle one specific doubt, at most 3 such lookups per article.
6. Write `tmp/new-posts/articles/cw-topic-8000N.xml` in one Write call. Aim for 1,200 to 2,000 words; 1,000 real words is the floor. Write only from what the screens say. Most of these events are newer than your training data, so your memory is wrong or empty: never fill a gap from it. Our own structure and headings, not Wikipedia's. Narrative crime-journalism voice. Never mention CrimeWiki, "this article", the sources or the research.
7. `python3 tmp/new-posts/check_new.py N`. Fix until your line ends in `ok`. UNGROUNDED: the number, name or quotation is on no saved page, so fix the article. Known false alarm: a publisher's name in the sources list (e.g. "CBS") flagged UNGROUNDED; put it in `allow.txt` with the page number. COPIED: rewrite the sentence. WIKI flags: restructure or rewrite. Never edit the checker. Fix with Edit, not a second Write.
8. Over-claim pass, from the screens already in your context (no new reads): a stronger verb than the page uses; a claim credited to the wrong speaker; an allegation written as fact; an accused person described as guilty; "first", "only", "never", "all" the page does not say; a date or order of events the page does not give; a number attached to the wrong thing. The last checker found 5 of these in an article that passed check_new.py, among them "found dead" with the wrong subject, a count ("five questions") no page gave, and a quote from "former prosecutors" that no page had. Fix each, then run check_new.py N once more.
9. Source conflicts, one per line, in `tmp/new-posts/research/cw-topic-8000N/unresolved.txt`. In the article give both figures and say who said which.
10. `date +%H:%M`, then append one line to `tmp/new-posts/sonnet1_log.md` (create it with a header line):
    `N | task_id | status (ok / blocked / failed) | words | sources | started | finished | fetches | screens read | title change or "-" | one-sentence note`

If the pages cannot support 1,000 words of real facts, do not pad: write `tmp/new-posts/blocked/cw-topic-8000N.json` with `{"task_id","title","reason"}`, log it as blocked, and go on. If an article still fails check_new.py after two rounds of fixes, log it as failed and go on.

## Cautions

General: a person's name is the title only after a conviction or guilty plea. Accused people are "accused" or "charged", with their plea. An acquittal goes in the first paragraph. Attribute unnamed-source claims to the outlet that reported them. Do not name minors unless the authorities did. No operational detail that would help someone repeat an attack.

- 24 Lucy Letby: person-titled; confirm the convictions and sentences from an opened page. The convictions stand. Report the challenges by medical experts and any application to the Criminal Cases Review Commission as claims, attributed to who made them, with dates; never write that she is innocent or that the convictions are unsafe. Any further charges, investigations or inquiry findings: give only their status as reported, with no comment on guilt (UK contempt rules). Name babies only by the letters the court used.

If the title in topics.jsonl is wrong given what the pages establish, do not edit it; put the better title in the log line.

## Hard limits

- Write and edit only the files of the topic you are on (its article, research folder, blocked file) and the run log. Never edit `topics.jsonl`, the Python tools, `PLAN.md`, `BRIEF.md` or any other article.
- Never run `publish.py`, git, ssh or gcloud. No installs. No files outside the project folder. Do not start subagents.
- Do not stop to ask questions. Settle what you can from the pages, record the rest in `unresolved.txt`, and go on.
- Do not re-read a file to confirm a write.

## Final report

When topic 24 is done, reply in under 250 words: counts (ok, blocked, failed); one line per topic as in the log; anything you skipped or could not settle; any limit above you had to break, and why.

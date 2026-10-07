Run notes for run 8: new articles (owner approved, 7 October 2026: "create 30 new posts. only spin up 30 writers first we will check em later"). 30 writers work in parallel, one topic each: 35, 36, 37 and 230 to 256. A separate checker will go through your article sentence by sentence against the pages you saved. Nothing you write is published by you.

Project root: /Users/anupamkhosla/Desktop/Projects/crimeWiki. Run every command from there. All tools and files are under `pipeline/` (it used to be `tmp/new-posts/`; ignore that old path if you see it).

## Why the standard is strict

The site must pass Google AdSense review to pay for its hosting. That review rejects copied text, Wikipedia clones and padded filler. An earlier writer was given a word minimum and padded 71% of its articles; that batch was thrown away. Length comes from sourced facts, never the other way round.

## Read first, once

1. `pipeline/PLAN.md`: the loop, "Rules that keep it honest", and both "Lessons" sections. Where these notes and PLAN.md differ, these notes win.
2. Sections 2 and 3 of `pipeline/BRIEF.md` (voice and the five-block file format). Ignore its sections 1 and 4.
3. `pipeline/articles/cw-topic-800001.xml`, the model article. Do not read any other article.

Do not read or edit `docs/`. The main session does that.

## The loop, for your topic N

Task id is `cw-topic-8` + N as 5 digits (35 is `cw-topic-800035`, 230 is `cw-topic-800230`).

1. `grep '"<task id>"' pipeline/topics.jsonl` shows the title, seed query and note. The note and the cautions below are legal limits. Follow them. Before writing, confirm from the pages you opened that they are about the subject the title names (same person or event, matching dates and place).
2. Search: as many WebSearch calls as the case needs, from as many reputable sources as possible (owner's rule). Several per message: latest news; background and timeline; official or court material (court filings, prosecutor or police releases, inquiry reports). If WebSearch is not loaded, load it with ToolSearch, query `select:WebSearch`. Do not filter Wikipedia out. The tool's limit is shared by all 30 writers: if it says the budget is used up, do not retry it. If WebSearch fails with an error (not the budget message), you may use `python3 pipeline/search.py "<query>" 12`. If WebSearch says the budget is used up, do not search by any other route (search.py, browser, archive.org search): continue with the pages you have and the exact URLs in wiki-leads.txt, and say so in your log line.
3. Fetch: `python3 pipeline/fetchmany.py N "<url>" "<url>" ...`, up to 16 URLs per call, as many calls as needed. You need at least 6 pages with status OK from at least 4 publishers; more is better. PLAN.md lists the publishers that usually block. For a 403, dead or THIN page try `https://web.archive.org/web/<original url>`.
4. Wikipedia as a lead-finder, after your own fetch (PLAN.md step 4). It is saved with status LEAD and can never be cited. Read it only for major events, people and dates your pages lack, then open the useful original pages listed in `wiki-leads.txt` with fetchmany; those are citable once saved OK. Wikipedia mirrors (wikimili, grokipedia, wikiwand, dbpedia, alchetron, everybodywiki and the like) are Wikipedia: never fetch or cite them. No Wikipedia page: note it in `unresolved.txt` and go on.
5. Read EVERY screen: `python3 pipeline/read.py N --max 7000`, then `--page 2`, `--page 3` until it says `end`. Keep `--max 7000` on every screen. For one page that was cut and matters: `--only <k> --max 40000`. Large PDFs: `--kw word,word`.
6. Write `pipeline/articles/<task id>.xml` in one Write call. Aim for 1,200 to 2,000 words; 1,000 real words is the floor. Write only from what the screens say; most of these events are newer than your training data, so your memory is wrong or empty. Our own structure and headings, built around what our sources show, not Wikipedia's order or wording. Narrative crime-journalism voice. Never mention CrimeWiki, "this article", the sources, "the pages reviewed" or the research.
7. `python3 pipeline/check_new.py N`. It checks every article, so it takes a few minutes while other writers run it; wait for it. Fix the article until your line ends in `ok`. UNGROUNDED: the number, name or quotation is on no saved page; fix the article. `allow.txt` is only for a real false alarm, each entry with the page number and the exact wording on that page; the checker will remove any entry it cannot find on its page. Never replace a publisher's name with a domain or a vague word just to pass. COPIED: rewrite the sentence. WIKI flags: restructure or rewrite. Never edit the checker.
8. Re-read your draft against the screens, sentence by sentence, both tables included. Look for: a stronger verb than the page uses; a claim or quotation credited to the wrong speaker; an allegation written as fact; an accused person described as guilty; "first", "only", "never", "all" that the page does not say; an order of events or a date the page does not give; a number attached to the wrong thing. Fix each one, then run check_new.py N again.
9. Put source conflicts (ages, counts, times, dates), one per line, in `pipeline/research/<task id>/unresolved.txt`. In the article, give both figures and name who said which.
10. Append ONE line to `pipeline/new_run8_log.md` with `>>` (other writers append to it too; never rewrite it):
    `N | task id | ok / blocked / failed | words | sources | WebSearch calls | started HH:MM | finished HH:MM | better title or "-" | one-sentence note`

If the pages cannot support 1,000 words of real facts, do not pad: write `pipeline/blocked/<task id>.json` with `{"task_id","title","reason"}` (say what you tried) and log it as blocked. If the article still fails check_new.py after two rounds of fixes, log it as failed.

## Cautions

A person's name is the title only after a conviction or guilty plea. Accused people are "accused" or "charged", with their plea. An acquittal goes in the first paragraph. Attribute unnamed-source claims to the outlet that reported them. Do not name minors unless the authorities did. No operational detail that would help someone repeat an attack. For terrorism and politically charged cases, attribute each government's or group's claim and keep a neutral voice. Long-famous cases (Dahmer, Bundy, Zodiac, Ramsey and the like): no graphic detail beyond what the case needs; give weight to the victims; and since Wikipedia's page on them is long, build your structure from your own sources.

- 35 Sam Bankman-Fried, 36 Lyle and Erik Menendez: person-titled. Confirm the conviction from an opened page; give the current status of any appeal, resentencing, parole or clemency decision with dates.
- 37 Trial of Duane Davis: CNN reported a guilty verdict on 31 August 2026 and sentencing set for 13 October 2026. Confirm both from opened pages and say what is still pending. The subject is Davis and the case, not a biography of Tupac Shakur.
- 230 to 256: the topic's note in topics.jsonl holds its cautions.

If the title is wrong given what the pages establish (for example a person-titled topic with no conviction), do not edit topics.jsonl; put the better title in the log line.

## Hard limits

- Write and edit only your topic's files: its article, its research folder, its blocked file, plus your one log line. Never edit `topics.jsonl`, the Python tools, `PLAN.md`, `BRIEF.md` or any other article.
- Never run `publish.py`, `update_live.py`, git, ssh or gcloud. No installs. No files outside the project folder (no `/tmp`). Do not start subagents.
- Do not stop to ask questions; nobody can answer mid-run. Record what you cannot settle in `unresolved.txt` and finish.
- Do not re-read a file just to confirm a write.

## Final reply

Under 150 words: your log line, what you skipped or could not settle, and how many WebSearch calls you made.

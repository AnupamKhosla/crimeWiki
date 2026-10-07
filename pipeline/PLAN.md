# New posts: the loop

Written 3 October 2026 by a Fable session. Revised 4 October 2026, after the
owner corrected two things: Wikipedia is allowed as a lead-finder, and a batch
is not published without an independent check.

This runbook is a procedure written by an agent. The rules are in `AGENTS.md`.
If the two disagree, `AGENTS.md` and the owner's own words win, and this file
gets fixed.

Goal: new articles on topics people search for now, published to the live site
in batches of 5. Proven end to end on topic 1 (Flydubai, live as post 1143).

Run every command from the project root. Everything lives in `pipeline/`.

## Before a batch: ask the owner once

Step 9 needs a reader other than the writer. Before writing a batch, ask which
it will be:

- check agents (the verify stage of the workflow scripts; spends plan allowance), or
- a different model in a fresh session, or
- neither. Then the owner must accept, before publishing, a batch that has
  only its writer's re-read.

Do not write five articles and raise this afterwards. That happened on
3 and 4 October with topics 12 to 17.

## Running writers and checkers (owner, 4 October 2026)

- Agent type `crimewiki-writer` (`.claude/agents/crimewiki-writer.md`): Sonnet,
  `effort: medium`. The session reads that file when it starts, so an edit to it
  counts only in a new session. Before saying what effort agents run on, check
  `pipeline/usage/agents_latest.json`; on 4 October agents ran on high after the file
  had been changed to medium mid-session.
- One topic per writer, up to 5 writers in parallel, each given only its topic
  number and a run file: `rewrite_prompt.md` (replacing a live copied post) or
  `sonnet1_prompt.md` (new topic). Measured: about $0.50 per post; one writer
  doing 10 posts cost 13 times one post.
- Checker: agent type `crimewiki-checker` (Sonnet, medium, no web), one per
  article with `check_prompt.md`, about $0.19 each. On 4 October a newly added
  agent file was picked up mid-session at medium; the edited writer file was not.
- New topics go live with `publish.py`; rewrites of live posts with
  `update_live.py`: owner, 7 October 2026: no dry run; update the live database and do a quick check. Both need `audit.md`.

## The loop, per topic

1. `python3 pipeline/status.py` shows the open topics, with a search seed
   and a note for the next ones. Work in list order: this week's news comes first.
2. **Search**: as many `WebSearch` calls as needed (owner, 7 October 2026: "as many reputable sources as possible"), several per message (latest news; background
   and timeline; official or court material). To go faster, search for the next
   2 or 3 topics in the same message. Do not filter Wikipedia out of the
   searches: a Wikipedia result is a lead for step 4.
3. **Fetch**: ONE call, 10 to 16 URLs. It opens them in parallel (about 7 seconds):
   `python3 pipeline/fetchmany.py <no> "<url>" "<url>" ...`
   Need at least 6 pages with status OK from at least 4 publishers. Fewer: search
   again and fetch more. Usually blocked: washingtonpost, nytimes, reuters,
   timesofisrael, euronews, flightradar24. Usually fine: AP (via local stations),
   CBS, ABC, CNN, NBC, NPR, PBS, Al Jazeera, CNBC, local papers and TV, DOJ and
   prosecutor press releases, court PDFs.
4. **Wikipedia, as a lead-finder.** Do this after your own search, not before.
   Give the topic's Wikipedia article to the same tool:
   `python3 pipeline/fetchmany.py <no> "https://en.wikipedia.org/wiki/<Page_title>"`
   It is saved with status LEAD, never OK, so it cannot be cited and its text
   never counts as support for a fact. You get three things:
   - `research/<task_id>/wiki-NN.txt`, the article text. Read it for major
     events, people and dates that your pages do not cover.
   - `research/<task_id>/wiki-leads.txt`, the pages Wikipedia cites. Fetch the
     ones that fill a gap (step 3 again). Those original pages are sources like
     any other.
   - Wikipedia's section headings, printed so that ours are our own.

   To find the page title:
   `curl -s -G https://en.wikipedia.org/w/api.php --data-urlencode action=query --data-urlencode list=search --data-urlencode "srsearch=<topic>" --data-urlencode format=json`
   No Wikipedia page for the topic: note it in `unresolved.txt` and go on.
5. **Read**: `python3 pipeline/read.py <no>` then `--page 2`, `--page 3`.
   Read every screen. Options: `--only 3 --max 40000` for one long page,
   `--kw word,word` to filter.
6. **Write** `pipeline/articles/cw-topic-8<no, 5 digits>.xml` in one Write
   call. Format and voice: `pipeline/BRIEF.md` section 2 and the file layout
   in section 3. Model article: `articles/cw-topic-800001.xml`. Build the
   article around what our sources show, in our own order and under our own
   headings.
7. **Check**: `python3 pipeline/check_new.py <no>`. Fix until the line ends
   in `ok`. Put source conflicts, one per line, in
   `research/<task_id>/unresolved.txt`. The `WIKI` line shows how close the
   article is to Wikipedia's page.
8. **Re-read the draft against the screens** once, sentence by sentence: who said
   it, and does the page say that much? The checker cannot see over-claims.
9. **Independent check.** A reader other than the writer goes through every
   sentence, the two tables included, against the saved pages, and writes
   `research/<task_id>/audit.md`:
   - who checked (model or agent), the date, and which screens were read;
   - one line per problem: the sentence, what the page says, the page number,
     the fix;
   - events that Wikipedia's page has and the article lacks, each with a
     decision: added from an opened source, or left out and why.

   Apply the fixes, then run `check_new.py` again. The workflow scripts'
   verify stage does this with agents; see "Before a batch".
10. **Publish every 5 checked articles**: tell the owner the titles and which
    check each one had, get the go-ahead, then `publish.py build`,
    `publish.py dry`, `publish.py apply`, `publish.py verify`.

## Rules that keep it honest

- Write only from the read.py screens. Never from memory. Most of these events
  are newer than the model's training data, so memory is wrong or empty.
- Every link in `<sources>` must be a page fetchmany saved with status OK.
- **Wikipedia (owner's rule, in `AGENTS.md`):** it may be read, and the sources
  it cites may be opened and used. The article must never look like it. So a
  fact enters only from a page with status OK; Wikipedia is never cited or
  linked; and our structure, headings and sentences are our own. `check_new.py`
  flags `WIKI COPIED` (a run of 12 or more words that is also in Wikipedia),
  `WIKI STRUCTURE` (three or more headings, and at least half of ours, are
  Wikipedia's) and `WIKI WORDING` (4% or more of our 6-word runs are in
  Wikipedia). Fix these by rewriting or restructuring, never by editing the
  checker.
- UNGROUNDED number, name or quotation: fix the article. Quotations must be
  word for word. `allow.txt` is only for a real false alarm, with the page number
  and the wording on that page. Never edit the checker to pass an article.
- COPIED: rewrite that sentence in your own words.
- VERIFY items do not block, but check each one by hand (derived dates, name pairs).
- Sources disagree: give both, name who said which, add a line to unresolved.txt.
- Living people. A person's name is the title only after a conviction or guilty
  plea; otherwise title the event ("Killing of ..."). Accused people are
  "accused" or "charged", with their plea. An acquittal goes in the first
  paragraph. Unnamed-source claims are attributed to the outlet that reported
  them. Do not name minors unless the authorities did.
- If the notes in `topics.jsonl` turn out wrong, fix the title or category there
  before `publish.py build`. No "/" or "?" in titles: the title is the URL.
- Aim for 1,200 to 2,000 words. Under 1,000 real words: do not pad; save
  `blocked/<task_id>.json` with `{"task_id","title","reason"}` and move on.
- No operational detail that would help someone repeat an attack.

## Stop and tell the owner if

- an article still fails the check after two rounds of fixes (block it, go on);
  two in a row: stop;
- you catch yourself writing a fact you cannot find on a screen;
- a batch is ready to publish and has had no independent check (step 9);
- `publish.py dry` or `apply` prints FAILED (nothing was inserted; read the
  `.failed.out` file, do not retry blindly).

## Publishing facts

- New posts go straight to the live database; there is no local database
  (owner, 3 October 2026). A go-ahead covers one session. A new day or session
  needs a new one.
- `apply` first writes a backup to `~/crimewiki-backups/` on the VPS, refuses to
  insert if the backup is small, inserts in one transaction, skips a title that
  already exists, compares hashes, and records ids in `published.jsonl`.
  Undo: `release/<stamp>/rollback.sql` (only if the owner asks).
- `publish.py` can only insert. Correcting a post that is already live needs
  an update mode that does not exist yet (`docs/ROADMAP.md` track A, step 11).
- New rows: creator `Anupam K`, image `default.png`, cleansed 1. The sitemap is
  built from the database, so new posts appear in it at once.
- No `git push` and no other VPS commands. Agents only when the owner said yes
  for this batch.

## Measured on topic 1

15 pages fetched in 7 seconds, 11 usable; 3 reading screens; 1,907 words, 11
sources; checker caught a planted fake number, name, quotation and copied
sentence. The first apply was refused by the backup guard (mysqldump needed a
privilege the app user lacks); fixed with `--skip-lock-tables`.

## Lessons from the first iteration (topics 1 to 4, written by Fable)

- `read.py`: use the SAME `--max` on every page of a topic. Changing it moves
  the page breaks and silently skips text. `--max 7000` worked well.
- Step 8 (re-read against the screens) is not optional. Each article passed the
  checker and still had 4 to 9 sentences that said more than the page did:
  "showed" where the page said "told", "the only woman for three decades" when
  another woman was there until 2010, a quote credited to the wrong speaker,
  "then" where the order of events was not given. Fix these before publishing.
- COPIED flags come mostly from statements and wire copy. Rewrite the sentence;
  do not shuffle two words.
- Outlets disagree on ages, counts and times in almost every breaking story.
  Give the range with who said which, and list it in `unresolved.txt`.
- Leave out testimony about what children said or saw unless it is essential.
- Each dry run uses up as many ids as there are posts in the batch. Harmless.
- Fetch noise: some pages save a block of unrelated headlines (Fox News, NBC
  "FOR SUBSCRIBERS"). Ignore those lines; never cite them as facts, except a
  headline that itself attributes a claim ("..., prosecutors say").

## Lessons from 3 and 4 October (topics 5 to 17)

- The writer's own re-read is not enough. For topics 5 to 11 a separate check
  stage found 6 to 22 over-claims per article after the writer and the checker
  had both passed it: an allegation written as fact, "served" where the page
  said "received", a wrong order of events.
- Topics 12 to 17 were then published with only the writer's re-read, because
  the session did not ask for the agents' yes. The owner had to find that out
  afterwards. Ask first ("Before a batch").
- An earlier version of this file said "Never Wikipedia". That was an agent's
  wording, stricter than the owner's rule, and a session then filtered
  Wikipedia out of every search. The owner's rule is in `AGENTS.md`.
- Clone test, measured 4 October on the 17 live posts, all written without
  Wikipedia: 0 copied runs, at most 1 shared heading, and 0.0% to 0.9% of
  6-word runs shared (13 posts have a Wikipedia page, 4 have none). A planted
  copy with every 8th word swapped scored 24.6% and was flagged.

## Where things stand (4 October 2026)

- Live: topics 1 to 17 (posts 1143, 1147-1149, 1157-1163, 1165, 1171-1175).
- Independent check done: topics 5 to 11. Not done: topics 1 to 4 and 12 to 17
  (writer's re-read only). The owner asked on 4 October for 12 to 17 to be
  redone properly; see `docs/STATE.md`.
- Next new topic: 18 (Ghislaine Maxwell). Publish every 5 checked articles.

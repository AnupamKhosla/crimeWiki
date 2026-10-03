# CrimeWiki session state

Last updated: 4 October 2026, 00:30, Claude Code session (Fable 5.1).
Rewrite this file at the end of every session. Stable facts go in `docs/ROADMAP.md`.

## Do this next (owner, 4 October: "do those articles properly")

- Give topics 12 to 17 (live posts 1165, 1171-1175) the independent check they
  never had: every sentence against the saved pages, plus what Wikipedia's page
  covers and ours lacks. One audit file each, `tmp/new-posts/research/<task_id>/audit.md`
  (`tmp/new-posts/PLAN.md` step 9). Not started. Wait for the owner's "go".
- Wikipedia text and the pages it cites are already saved as leads for every
  topic that has a page (`wiki-NN.txt`, `wiki-leads.txt`). Topics 3, 4, 5 and 16 have none.
- Then build an update mode for `tmp/new-posts/publish.py` (backup, dry run,
  content-only UPDATE by id, hash check). Corrections go live only on a go-ahead.
- After that: topic 18 (Ghislaine Maxwell) onward, in batches of 5. Ask about
  the check agents BEFORE writing a batch.

## Done on 3 and 4 October

- Live: 1,138 posts, max id 1175. New posts: 1143, 1147-1149, 1157-1163, 1165,
  1171-1175. Independent check done for topics 5 to 11 only.
- Post URLs deployed (commit eb8213f): hyphen slugs, 301s, real 404s.
- Topics 12 to 17 were published by the Opus session with only its own re-read.
- Owner's Wikipedia rule is in `AGENTS.md`: read it, use its sources, never cite
  it, never look like it. "Never Wikipedia" was an agent's wording, now removed.
- Docs revised (`docs/ROADMAP.md` section 9 lists every change). Tools:
  `fetchmany.py` saves a Wikipedia article as a LEAD; `check_new.py` has a clone
  test (`WIKI` flags). All 17 live posts pass it: 0 copied runs, at most 1 shared
  heading, under 1% shared wording. Pre-edit copies: `tmp/session-reread/before/`.

## Waiting on the owner (`docs/ROADMAP.md` section 6)

- Check agents: a standing yes for every batch, or asked per batch?
- Topics 1 to 4: the same check as 12 to 17?
- Two Wikipedia-hosted icons in the post stylesheet: fix and deploy?
- The GitHub repository is PUBLIC. Open security items stay in `tmp/SECURITY_TODO.md`
  (ignored by git). Git also ignores `tmp/new-posts/`: tools, research and articles
  exist only on this Mac. Never commit `index.php` (`?bare=1`) or `.DS_Store`.
- A publish go-ahead covers one session; a new session needs a new one.

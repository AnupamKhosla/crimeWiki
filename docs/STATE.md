# CrimeWiki session state

Last updated: 4 October 2026, 00:47, Claude Code session (Fable 5.1).
Rewrite this file at the end of every session. Stable facts go in `docs/ROADMAP.md`.

## Do this next

- **10 new posts, asked for by the owner on 4 October: topics 18 to 27** (Maxwell,
  Kohberger, Louvre heist, Bondi Beach, Combs trial, Pelicot, Letby, Southport,
  Butler shooting, New Orleans attack). Not started. First get two answers: who
  does the independent check (check agents, another model, or nobody), and
  whether to publish at once or show the owner first. Then `tmp/new-posts/PLAN.md`.
- **Legacy posts: 837 of 1,092 still carry Wikipedia's wording**
  (`docs/WIKIPEDIA_OVERLAP.md`, worst first; data in `tmp/legacy-wiki-audit/`).
  The owner decides the rewrite order and whether failing posts are hidden from
  search (noindex, no sitemap entry) until rewritten. `docs/ROADMAP.md` step 13.
- Live new posts with no independent check: topics 1 to 4 and 12 to 17. They need
  audit files, then an update mode for `tmp/new-posts/publish.py` (it only inserts).

## Done on 4 October

- Live: 1,138 posts, max id 1175. New posts: 1143, 1147-1149, 1157-1163, 1165, 1171-1175.
- Two pushes, both deployed: commit 9dbdfa5 (`AGENTS.md` and four docs), then
  the commit after it, which added `docs/WIKIPEDIA_OVERLAP.md` on the owner's
  instruction. The repository is PUBLIC. All docs are committed.
- Rules in `AGENTS.md`: the owner's Wikipedia rule, the independent check, honest
  reporting. Tools: `fetchmany.py` saves Wikipedia as a LEAD; `check_new.py` has a
  clone test. All 17 new posts pass it (no identical runs, under 1% shared wording).
- Open security items moved out of the public roadmap to `tmp/SECURITY_TODO.md`.

## Waiting on the owner (`docs/ROADMAP.md` section 6)

- The two answers for the 10 posts, and the legacy decisions above.
- Check agents as a standing yes? The same check for topics 1 to 4?
- Two Wikipedia-hosted icons in the post stylesheet: fix and deploy?
- A publish go-ahead covers one session. Git ignores `tmp/new-posts/` (tools,
  research and articles are only on this Mac). Never commit `index.php` or `.DS_Store`.

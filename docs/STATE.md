# CrimeWiki session state

Last updated: 7 October 2026, about 20:25 IST, Claude Code session (Opus 5.5).
Rewrite this file at the end of every session. Stable facts go in `docs/ROADMAP.md`.

## Do this next

- LIVE 22:09: run 8, 29 new posts (ids 1210-1238; topics 35-37, 230-234, 236-256), written by 30
  writer agents (pipeline/new_run8.md), each checked by a checker agent (pipeline/check_run8.md,
  audit.md, 2-11 fixes each). Rollback pipeline/release/20261007-220850/rollback.sql; VPS backup
  ~/crimewiki-backups/posts-before-20261007-220850.sql.gz. verify: all live.
- LIVE 22:27: 235 "Murder of Dee Ann Warner" (id 1239; owner chose the full name). Run 8 done: 30/30 live.
- publish.py: dry-run gate removed (owner's no-dry-run rule); backup at publish.py.bak-20261007.
- LIVE 20:20: run 7, 40 rewrites (topics 190-229), checked. Rollback pipeline/update/20261007-202009/.
- Tool fixes proposed, not done (owner to approve): check_new.py should (a) confirm every allow.txt
  entry is on its page, (b) accept a publisher name that matches the source URL's domain. Checkers
  removed false allow.txt entries and, in 195, 198, 201, 204, 211, 219, 220, 226, replaced outlet
  names with domains or "a newspaper"; restore names once (b) exists. Also flag process wording
  ("the pages reviewed"); checkers cut it in 199, 201, 205, 212.
- Proposed tidy-up (owner to approve): fold lasting rules of rewrite_run2-7.md into rewrite_prompt.md,
  move old run notes and sonnet*_prompt.md (still "exactly 2 WebSearch") to pipeline/history/.
- Owner to decide: 60 Lawrence Bishnoi and 44 Martha Rendell held (post 1105 deleted by the merge).
  Blocked: 161, 169, 186, 90, 96, 119. Stray pipeline/tmp/ and tmp/c218.txt: owner may delete.
- SEARCH CONSOLE: re-inspect from about 14 Oct the 4 URLs submitted 16:21 on 7 Oct
  (2025-louvre-heist, killing-of-iryna-zarutska, murder-of-baba-siddique,
  2024-kolkata-rape-and-murder-case). Indexed = content passes; still out = site-level verdict.
- Domain: GoDaddy renewal Rs 5,287.50/yr, auto-renew OFF, expires 25 Feb 2027. Cloudflare .site
  $27.70/yr renewal, .com $10.46/yr (cfdomainpricing.com). Owner: no noindex, rewrite gradually.

## Done on 7 October (evening)
- Pipeline moved to pipeline/ and tracked (commits c5b9632-7111800, not pushed); Nginx denies /pipeline/
  after next deploy. Owner: writers search as much as needed; no dry run before live updates.
- settings.local.json env: MAX_WEB_SEARCHES_PER_SESSION=5000, MAX_CONCURRENT_SUBAGENTS=40.

## Known problems

- check_new.py re-checks every article per call (31 s alone for ~270; minutes with 30 agents).
- docs/ edits are uncommitted (path changes plus earlier session edits). Never commit index.php
  or .DS_Store. Nothing pushed: pushing deploys to the VPS.

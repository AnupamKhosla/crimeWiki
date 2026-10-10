# CrimeWiki session state

Last updated: 10 October 2026, about 11:45 IST, Claude Code session (Opus 5.5).
Rewrite this file at the end of every session. Stable facts go in `docs/ROADMAP.md`.

## Do this next

- PUBLISH (owner): 62 checked rewrites of copied legacy posts are built and NOT live. The agent's
  `update_live.py apply` was refused by Claude Code's auto-mode safety check ("Production Deploy").
  Owner runs: `python3 pipeline/update_live.py apply` then `python3 pipeline/update_live.py verify`
  (batch pipeline/update/20261010-114325, manifest.jsonl lists topics 257-326 and their post ids;
  apply backs up the posts table first and writes rollback.sql). Topic 60 (Bishnoi, post 1105)
  was deliberately left out: still the owner's decision.
- Every one of the 62 has: check_new.py ok, and an independent Haiku checker's audit.md (5-23 fixes
  each). Caveats in audits: some long pages were checked by Grep, not read whole (257, 285, 288, 310,
  313); 259's checker skipped the Wikipedia-coverage notes; 302 (Smollett) gives the 2024 reversal.
- Owner to decide: 314 "Mehul Choksi" and 277 "Robert Trimbole" keep a person's name as title though
  neither is convicted of the main allegation (text is allegation-worded). Live titles are unchanged.
- Blocked (too few sources; live posts untouched): 274, 283, 292, 305, 306, 312, 316, 323.
  Unused spares queued: 327-329 (Miami Showband, Katyn, Ranquil).

## Done on 10 October
- Content agents moved to Haiku 5.5, effort medium (owner): agent files, workflow scripts, AGENTS.md,
  PLAN.md, memory. Committed and pushed 16d5ab8.
- Run 9: 70 Haiku writers, 66 Haiku checkers, $3.66 API-priced in all (writer ~3.2c, checker ~2.2c);
  2 of 136 agents passed 100K context. Topics 257-326 (rewrite_run9.md, check_run_haiku.md).
- Context guard pipeline/usage/ctx_guard.py (tmp/ctx_cap.json is ON: rewriter 62K/95K, checker
  70K/95K). check_new.py N now checks only N (5 s, not minutes). agent_cost.py added.
- Sidebar mod: subagent rows show model, effort, context and task name; Fable cost row.

## Known problems
- read.py --max 7000 never shows a page past 7,000 chars (--page moves to the next pages, not further
  down one page). Needs an offset option. Writers also cite http:// pages, which check_new rejects.
- check_prompt.md says checkers touch only article and audit; check_run_haiku.md now allows allow.txt.
- rewrite_log.md: some run-9 lines have placeholder or wrong times (agents could not edit the log).
- Never commit index.php or .DS_Store. Left uncommitted on purpose: index.php, qwen_contract.txt,
  LUNA_IMPLEMENTATION_PLAN.md.
- SEARCH CONSOLE: from about 14 Oct re-inspect 2025-louvre-heist, killing-of-iryna-zarutska,
  murder-of-baba-siddique, 2024-kolkata-rape-and-murder-case (submitted 7 Oct).
- Domain: GoDaddy renewal Rs 5,287.50/yr, auto-renew OFF, expires 25 Feb 2027 (prices: git history).

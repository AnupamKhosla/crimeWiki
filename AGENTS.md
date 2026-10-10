# CrimeWiki Agent Notes

Rules for any AI agent working on this project.

## Start Here

1. Read `docs/ROADMAP.md`. It holds the goals, the current state, the order of work, and a map of every other document.
2. Read `docs/STATE.md`. It holds what the last session did, what is in progress, and what the owner still has to decide.
3. For new posts, follow the runbook `pipeline/PLAN.md`. It is a procedure written by an agent; the rules in this file outrank it.
4. Before ending a session, update `docs/STATE.md` (see "Session State Tracking" below).

## Response Length (Semi-Caveman Mode)

- Every output response: 100-200 words. Occasionally 200+ if a teaching moment truly needs it. No exceptions for routine replies.

## Working Style

- Explain along the way. Do not rush ahead. Pause for the user to catch up.
- Never do 5 edits in silence then summarize.
- Teach the *why*, not just the *what*. One concept per response max.
- **Never do something without asking or telling the owner first.** No surprise installs, deletes, renames, refactors, or file additions — even if they seem helpful. Ask, then act only after approval.
- When a command might fail or have side effects, say so before running it.
- **Do not start subagents, workers or workflows without the owner's explicit yes.** They spend the owner's plan allowance. Decided 3 October 2026, after a Sonnet worker pilot used 20% of a session. A yes covers the run it was given for, not later ones. Where a rule in this file needs an agent (the independent check, under "Main Goal"), ask for the yes before the work starts, not after.
- **Do NOT run commands on the production VPS without permission** (no SSH / `gcloud compute ssh`). The owner handles all server-side actions and deploys. Make local repo edits and hand over commands. Note: `git push` triggers the webhook deploy on the VPS, so do not push unless explicitly told. You can override this restriction with explicit approval from the user. Permission so far has been given per session, and only for read-only checks and for `pipeline/publish.py` after a publish go-ahead.

## Honest Reporting (added 4 October 2026, after a session went wrong)

- **Say who did it.** Write "I excluded Wikipedia from my searches", not "every search blocked Wikipedia". Never word your own choice as something that happened to you.
- **Label a guess as a guess.** If you have not checked it (how the plan meters usage, what the server does, what a page says), say so. Do not state it as fact.
- **When two rules pull against each other, stop and ask before you act.** Do not pick one quietly and report afterwards. On 3 and 4 October six posts went live without the required independent check, because "no agents without a yes" was obeyed in silence when one question would have settled it.
- **The owner's words outrank anything an agent wrote.** The runbooks and briefs under `tmp/` were written by agent sessions. If one is stricter or looser than this file or than what the owner said, the owner's rule wins: fix the runbook and tell the owner. Quote the owner's rule; do not harden it. An agent once wrote "Never Wikipedia" into a runbook. That was never the owner's rule, and a later session enforced it.
- **Read a timed or conditional instruction exactly.** "Stop at 9:39" does not mean "stop now". If an instruction can be read two ways and acting on the wrong one loses work, ask in one line.
- **Report what was skipped as plainly as what was done,** before the owner has to ask.

## Model quirks

- **qwen3.8-max-preview: DISABLE extended thinking.** It has a bug where
  thinking balloons to absurd length once context passes ~100-200k tokens.
  If you are qwen, keep reasoning to a sentence or two and answer fast.
  Long internal monologues here are a defect, not diligence.

## Stay Within the Project Folder

- **NEVER** write temp files, logs, scratch files, debug dumps, or any generated output OUTSIDE the project folder (`~/Desktop/Projects/crimeWiki`). Do not use `/tmp`, `/var/folders/...`, the home directory, or any other external path.
- For ALL temporary work (dev-server logs, scratch files, intermediate artifacts, downloads), use the project's own `tmp/` folder: `~/Desktop/Projects/crimeWiki/tmp/`.

## API / Future-Proofing

- Never hard-code assumptions about an external API's schema into app code. Treat all external data as untrusted and untyped; isolate parsing in one place.
- Prefer free, no-key, stable sources, but keep them swappable behind an internal contract. The rest of the app must not care which provider is used.

## Main Goal (Priority #1)

**Grow CrimeWiki with 100% original, well-researched, long-form articles.**

Status, checked 4 October 2026: the port of the Wikipedia-scraped posts is
NOT done. The 6 September content release changed 1,116 of the 1,121 legacy
posts, but a comparison on 4 October found that 837 of 1,092 still carry
Wikipedia's wording (`docs/WIKIPEDIA_OVERLAP.md`). Since 3 October, 17 new
posts on current cases are live, and those are original. The work is new
articles, toward 5,000, then 10,000, with 100,000 or more as the long-term
direction, and replacing the copied legacy text.
The order of work is in `docs/ROADMAP.md`. The runbook for new posts is
`pipeline/PLAN.md`. `docs/CONTENT_SCALE_PLAN.md` is the record of the
earlier audits and pilots.

- Why: This is a charity project that will eventually run Google AdSense to cover server/domain costs. If Google detects plagiarism, Wikipedia reuse, or padded filler, AdSense is denied and the charity dies.
- **Priority now (owner, 3 October 2026): articles on what people are searching for today**, meaning current crime events and people in the news, so the site gains search traction. Lengthening the old short posts (`docs/REWRITE_LIST.md`) comes after.
- **Wikipedia rule (owner, 4 October 2026): "You can visit Wikipedia and even use the same sources there. The main point is our page shouldn't look like a wiki clone but our own original research."** In practice:
  - Research independently first. Then read the Wikipedia article and its reference list, to find original sources and to see whether a major event is missing. Open those original pages yourself; they may be cited.
  - A fact goes into an article only when a page you opened supports it. Wikipedia's own text is never that support, and Wikipedia is never cited or linked.
  - The page must not look like Wikipedia's: our own structure and section headings, our own emphasis, our own sentences. Not a paraphrase. `pipeline/check_new.py` tests this against the saved Wikipedia page (its flags start with `WIKI`).
  - Never write "never Wikipedia" into a runbook, prompt or tool. That was an agent's wording, stricter than the owner's rule.
  - Measure it, never assume it. Checks of length, filler and links do not show copied wording. On 3 October the legacy rewrite was reported as "essentially done" on those checks; the clone test then showed most of it was still Wikipedia's text.
- Articles are written in a narrative crime-journalism voice.
- **Length standard (owner, 3 October 2026): aim for 1,200 to 2,000 words; 1,000 words of real content is the floor for publishing.** Length must come from sourced facts. A topic that cannot support it is blocked or held for more research, never padded.
- **Never give a writer a word minimum without the padding gate.** The day-2 package met a 1,200-word minimum with 71% template filler and was unusable. Every batch must pass the content-kit validator and the padding audit before it is published.
- Articles never mention CrimeWiki, the article itself, or the research process.
- The same XML structure is preserved so CSS/frontend never breaks: `<intro-data>` (5 rows), `<details>` (6 to 12 rows), `<sources>` (ul.list), `<related>` (left empty until internal links are reviewed), `<content>` (h2 sections and paragraphs separated by hr, starting with `Introduction`, as many as the subject requires). The contract is `tools/crimewiki-content-kit/contracts/five-block-contract.md`.
- Sources are real pages the writer opened (court records, official reports, newspapers, books). No Wikipedia links anywhere in a post.
- **Independent check before publishing (required).** The automatic checker cannot see a sentence that claims more than its source says. On 3 October a separate check stage found 6 to 22 such over-claims per article after the checker had passed them. So every batch is checked, sentence by sentence against the saved pages, by a reader other than its writer: a separate agent, or a different model in a fresh session. The writer re-reading its own draft does not count. The check leaves an audit file for each article, and its fixes are applied before the batch is published. A separate agent needs the owner's yes, so ask for it before writing the batch. If the answer is no, say before publishing that the batch has only its writer's re-read, and publish only if the owner accepts that.
- **Model for content work (owner, 10 October 2026): Haiku 5.5 at effort medium** for every writer, rewriter and checker agent and every workflow agent. The agent files in `.claude/agents/` and the workflow scripts in `pipeline/` set `model: haiku`, `effort: medium`. Also pass `model: "haiku"` on each Agent call: an edited agent file counts only in a new session. Measured 10 October: a Haiku rewrite cost about 3.5 cents and a Haiku check about 3 cents (API-priced), against 33 and 25 cents on Sonnet. Keep each agent under 100,000 tokens of context, because Haiku charges 5 times as much for a request above that. The run notes say so, and `pipeline/usage/ctx_guard.py` enforces it while `tmp/ctx_cap.json` exists (see `pipeline/PLAN.md`).
- Writing route: the main Claude Code session researches and writes each article, following `pipeline/PLAN.md`. Agents and workflows run only on the owner's yes for that run. History: the owner stopped the Sonnet worker pilot on 3 October, then ran two workflow trials the same evening for topics 5 to 11. Those scripts are `pipeline/new_posts_workflow.js` and `pipeline/one_post_parallel_workflow.js`.
- Publishing (owner decisions, 3 October 2026): there is no local database, and the live database is the source of truth. New posts are inserted into it by `pipeline/publish.py` (backup first, dry run, one transaction, hash check, live-page check), in batches of 5. **Nothing goes live without the owner's go-ahead, and a go-ahead covers that session only.** A full database replacement needs explicit owner approval.
- Images (later goal): every post should get a proper image, and posts about a person should show that person, with usable rights and a credit. Not started; see `docs/ROADMAP.md`.
- Earlier pipelines are historical: the Luna/Codex batch scripts (`scripts/run_luna_*.sh`), the Neuralwatt batch scripts, and the pilot `scripts/rewrite_postN.php` files. The owner has prohibited further Neuralwatt use. Do not create new per-post PHP scripts. The day-1 and day-2 rewrite queue (`tmp/rewrite-queue/`, `tmp/longform/`) is paused; `pipeline/BRIEF.md` is still the reference for article format and voice.

**Live reliability goal (Priority #2 after the local rewrite path is reliable)**

- Remove Docker from the database layer as a separate, measured migration to
  native MariaDB or another owner-approved database target. Keep backups and a
  rollback path until the live data and application queries are verified.
- Add a PM2-like automatic recovery layer using systemd/service supervision
  and health checks for Nginx, PHP-FPM, and MariaDB. A crash or failed health
  check should restart the affected service without creating restart loops or
  hiding persistent configuration/database errors.
- Do not run these production migrations or restarts from an agent session;
  prepare and test repository files locally, then hand commands to the owner.
- The order of these steps, their gates, and the facts from the 3 October 2026
  server check are in `docs/ROADMAP.md` track B.

## Project Overview

This repository contains a PHP CMS/wiki app plus a small VM ops bundle for a low-memory Google Cloud VM.

## Architecture

- Public traffic hits host Nginx on ports `80/443`.
- Host Nginx serves audited static files and passes PHP directly to Docker `app-fpm` on `127.0.0.1:9070`; the Docker `web` Nginx exists only under the `local` profile.
- Host Nginx proxies `/phpmyadmin/` to the loopback-only phpMyAdmin container on `127.0.0.1:8082`.
- Nginx proxies `/hooks/deploy` to the local webhook listener on `127.0.0.1:9000`.
- Docker Compose runs `app-fpm` and `db` for the VM; `web` is local-only and `phpmyadmin` is an explicit tools profile started by the VM lifecycle helper.
- The database is MySQL 9.6 in the Docker `db` container (`crimewiki_db_1` on the VM), with its data in the `crimewiki_db_data` volume. Port 3306 is not published to the host. No native database is installed on the VM.

## Runtime Config

- `include/config.php`: app DB config, git-ignored, normally created by the browser setup flow in `login.php` / `include/setup.php`.
- `.env`: optional Docker Compose env file, git-ignored, used for `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_ROOT_PASS` if present on the VM.
- `ops/env/crimewiki.env` -> `/etc/crimewiki.env`: deploy-managed VM config with `DOMAIN` and `REPO_DIR`.
- `/etc/secrets/secrets.env`: live VM secrets file with `WEBHOOK_SECRET` and `PROXY_SECRET_TOKEN`; do not commit it.

## Deploy Flow

- Webhook runs `/usr/local/bin/deploy.sh`.
- Deploy ensures Nginx is running, switches to maintenance mode, does best-effort `git pull --ff-only`, syncs templates into `/etc` and `/usr/local/bin`, refreshes webhook template/service files, explicitly rebuilds the FPM image, removes retired Compose orphans, restarts `crimewiki-app`, then switches back live.
- Deploy logs go to `/var/log/deploy.log`.

## Boot Recovery

- `crimewiki-app.service` runs `/usr/local/bin/crimewiki-start.sh` on boot.
- `crimewiki-start.sh` does best-effort `git pull --ff-only origin main` and then runs the repository-owned Nginx+FPM stack with `docker compose up -d --build --remove-orphans`.
- Boot/start logs go to `/var/log/crimewiki-start.log`.
- `webhook.service` is enabled during VM bootstrap and should auto-start on reboot.

## Files Copied To The VM

- `ops/env/crimewiki.env` -> `/etc/crimewiki.env`
- `ops/nginx/crimewiki.conf` -> `/etc/nginx/sites-available/crimewiki.conf`
- `ops/nginx/crimewiki_maintenance.conf` -> `/etc/nginx/sites-available/crimewiki_maintenance.conf`
- `ops/maintenance/index.html` -> `/var/www/maintenance/index.html`
- `ops/systemd/webhook.service` -> `/etc/systemd/system/webhook.service`
- `ops/systemd/crimewiki-app.service` -> `/etc/systemd/system/crimewiki-app.service`
- `ops/scripts/start_stack.sh` -> `/usr/local/bin/crimewiki-start.sh`
- `ops/scripts/deploy.sh` -> `/usr/local/bin/deploy.sh`
- `ops/scripts/ensure_secrets.sh` -> `/usr/local/bin/crimewiki-ensure-secrets.sh`

## Secrets And Safety

- The repo should not contain the real webhook secret.
- The repo should not contain `include/config.php`.
- Overwriting `/etc/crimewiki.env` during deploy is intentional because the repo owns `DOMAIN` and `REPO_DIR`.
- Rendering the live webhook runtime config is safe because `WEBHOOK_SECRET` is sourced from `/etc/secrets/secrets.env` by `webhook.service`.

## Session State Tracking

Session state lives in the repository, in `docs/STATE.md`, so that any agent on
any tool can find it.

At the START of every session: read `docs/ROADMAP.md`, then `docs/STATE.md`.

At the END of every session (or when significant progress is made), rewrite
`docs/STATE.md` with a concise summary covering:
- What was completed this session
- What is currently in-progress (with file paths)
- What is pending / next steps
- Any blockers or decisions the human needs to make

Keep it under 40 lines. Use bullet points. Include file paths. Replace stale
entries instead of appending, so it never grows into a log. Stable facts (a
rule, a goal, a finished migration) belong in `docs/ROADMAP.md` or this file.

`supermemory` is optional. Its CLI was not installed on the owner's machine as
of 3 October 2026. If a `supermemory` tool is available in your session, you
may also mirror the summary there (`supermemory add`, scope: project), but
`docs/STATE.md` is the source of truth.

## Tech Debt & Future Refactoring (Deferred)

Recorded 2026-07-29. **Do NOT start this work until the rewrite pipeline is fully functional and reliable.** Functionality is priority #1; restructuring is deliberately deferred.

- **Naming**: flat root files have awkward, non-conventional names — most notably `rewrite_api.php` (the streaming rewrite endpoint; despite the old "Qwen" name it calls Neuralwatt, which the owner has since prohibited). Rename these to something clear and conventional during the restructure.
- **Structure**: the app is currently flat PHP files in the repo root (`index.php`, `post.php`, `rewrite.php`, `rewrite_api.php`, `login.php`, `include/*`). Long-term it should move to a proper MVC / framework layout (e.g., Laravel-style: routes → controllers → models → views) with a clean `public/` web root.
- **Hard constraints on any restructure**: preserve the five-tag XML contract and its rendering (`post.php`/`post_code.php`, validated by `check_xml()` in `include/addpost_code.php`); keep the SSE streaming behaviour; keep the webhook deploy flow working (`ops/scripts/deploy.sh` + Nginx template render). The CSS/frontend must not break.
- **Sequencing**: stabilise the rewrite pipeline (streaming works reliably, posts rewritten, AdSense-safe) → then do the refactor as one focused effort, not piecemeal.

## Working Tree Notes

- `index.php` currently has an intentional local, uncommitted change: a `?bare=1` mode that hides parts of the homepage for local testing.
- `.DS_Store` may appear in the working tree and should not be committed.
- The homepage category filter includes `Blog`, but the footer category lists intentionally exclude `Blog`.
- Use `proxy.php?url=...` for live proxy requests. The path-style `/proxy/<urlencoded-url>` route is currently unreliable on production because encoded slashes may be rejected before the rewrite reaches PHP.

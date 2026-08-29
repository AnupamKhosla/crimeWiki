# CrimeWiki Agent Notes

Rules for any AI agent working on this project.

## Response Length (Semi-Caveman Mode)

- Every output response: 100-200 words. Occasionally 200+ if a teaching moment truly needs it. No exceptions for routine replies.

## Working Style

- Explain along the way. Do not rush ahead. Pause for the user to catch up.
- Never do 5 edits in silence then summarize.
- Teach the *why*, not just the *what*. One concept per response max.
- **Never do something without asking or telling the owner first.** No surprise installs, deletes, renames, refactors, or file additions — even if they seem helpful. Ask, then act only after approval.
- When a command might fail or have side effects, say so before running it.
- **Do NOT run commands on the production VPS** (no SSH / `gcloud compute ssh`). The owner handles all server-side actions and deploys. Make local repo edits and hand over commands. Note: `git push` triggers the webhook deploy on the VPS, so do not push unless explicitly told.

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

**Port ALL existing Wikipedia-scraped posts to 100% original AI-written content.**

- The AI agent (in opencode sessions) reads each post from the DB, researches the topic fresh via websearch, then rewrites the entire page in a narrative crime-journalism voice.
- Content must be completely different from Wikipedia — different structure, different emphasis, different sources, different vocabulary. Not a paraphrase. A rewrite from scratch using the same facts.
- Why: This is a charity project that will eventually run Google AdSense to cover server/domain costs. If Google detects plagiarism or Wikipedia reuse, AdSense is denied and the charity dies.
- The same XML structure is preserved so CSS/frontend never breaks: `<intro-data>` (5 rows), `<details>` (tbody table), `<sources>` (ul.list), `<related>` (ol.list), `<content>` (h2 sections separated by hr).
- Wikipedia links in content are replaced with internal CrimeWiki links. Sources are replaced with fresh ones (court records, newspapers, books, films).
- Each post gets a "researched on [date]" feel with sources Wikipedia doesn't cite.
- Script pattern: `scripts/rewrite_postN.php` → run via `docker compose exec app-fpm php /var/www/html/scripts/rewrite_postN.php`

## Project Overview

This repository contains a PHP CMS/wiki app plus a small VM ops bundle for a low-memory Google Cloud VM.

## Architecture

- Public traffic hits host Nginx on ports `80/443`.
- Host Nginx serves audited static files and passes PHP directly to Docker `app-fpm` on `127.0.0.1:9070`; the Docker `web` Nginx exists only under the `local` profile.
- Host Nginx proxies `/phpmyadmin/` to the loopback-only phpMyAdmin container on `127.0.0.1:8082`.
- Nginx proxies `/hooks/deploy` to the local webhook listener on `127.0.0.1:9000`.
- Docker Compose runs `app-fpm` and `db` for the VM; `web` is local-only and `phpmyadmin` is an explicit tools profile started by the VM lifecycle helper.

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

At the START of every session, run:
```
supermemory search "crimeWiki project state" (scope: project)
```
This retrieves what the previous session accomplished and what is pending.

At the END of every session (or when significant progress is made), run:
```
supermemory add (scope: project, type: project-config)
```
With a concise summary covering:
- What was completed this session
- What is currently in-progress (with file paths)
- What is pending / next steps
- Any blockers or decisions the human needs to make

Keep each memory entry under 30 lines. Use bullet points. Include file paths.
If a memory becomes stale (work completed), use `supermemory forget` to remove it before adding the updated one.

### Current Project State

_Live project state (DB counts, what was done this session, open bugs, next steps) lives in **supermemory**, NOT here — this file is for stable rules only. Run the `supermemory search "crimeWiki project state"` above at the start of every session to load it._

## Tech Debt & Future Refactoring (Deferred)

Recorded 2026-07-29. **Do NOT start this work until the rewrite pipeline is fully functional and reliable.** Functionality is priority #1; restructuring is deliberately deferred.

- **Naming**: flat root files have awkward, non-conventional names — most notably `rewrite_api.php` (the streaming Qwen endpoint). Rename these to something clear and conventional during the restructure.
- **Structure**: the app is currently flat PHP files in the repo root (`index.php`, `post.php`, `rewrite.php`, `rewrite_api.php`, `login.php`, `include/*`). Long-term it should move to a proper MVC / framework layout (e.g., Laravel-style: routes → controllers → models → views) with a clean `public/` web root.
- **Hard constraints on any restructure**: preserve the five-tag XML contract and its rendering (`post.php`/`post_code.php`, validated by `check_xml()` in `include/addpost_code.php`); keep the SSE streaming behaviour; keep the webhook deploy flow working (`ops/scripts/deploy.sh` + Nginx template render). The CSS/frontend must not break.
- **Sequencing**: stabilise the rewrite pipeline (streaming works reliably, posts rewritten, AdSense-safe) → then do the refactor as one focused effort, not piecemeal.

## Working Tree Notes

- `index.php` currently has an intentional local content change restoring the homepage heading text.
- `.DS_Store` may appear in the working tree and should not be committed.
- The homepage category filter includes `Blog`, but the footer category lists intentionally exclude `Blog`.
- Use `proxy.php?url=...` for live proxy requests. The path-style `/proxy/<urlencoded-url>` route is currently unreliable on production because encoded slashes may be rejected before Apache rewrite reaches PHP.

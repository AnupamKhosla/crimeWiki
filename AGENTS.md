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
- Script pattern: `scripts/rewrite_postN.php` → run via `docker exec crimewiki-app-1 php /var/www/html/scripts/rewrite_postN.php`

## Project Overview

This repository contains a PHP CMS/wiki app plus a small VM ops bundle for a low-memory Google Cloud VM.

## Architecture

- Public traffic hits host Nginx on ports `80/443`.
- Nginx proxies the site to the Docker app on `127.0.0.1:8080`.
- Nginx proxies `/hooks/deploy` to the local webhook listener on `127.0.0.1:9000`.
- Docker Compose runs three services: `app`, `db`, and `phpmyadmin`.

## Runtime Config

- `include/config.php`: app DB config, git-ignored, normally created by the browser setup flow in `login.php` / `include/setup.php`.
- `.env`: optional Docker Compose env file, git-ignored, used for `DB_NAME`, `DB_USER`, `DB_PASS`, `DB_ROOT_PASS` if present on the VM.
- `ops/env/crimewiki.env` -> `/etc/crimewiki.env`: deploy-managed VM config with `DOMAIN` and `REPO_DIR`.
- `/etc/secrets/secrets.env`: live VM secrets file with `WEBHOOK_SECRET` and `PROXY_SECRET_TOKEN`; do not commit it.

## Deploy Flow

- Webhook runs `/usr/local/bin/deploy.sh`.
- Deploy ensures Nginx is running, switches to maintenance mode, does best-effort `git pull --ff-only`, syncs templates into `/etc` and `/usr/local/bin`, refreshes webhook template/service files, self-updates `deploy.sh` if needed, enables/starts `webhook` and `crimewiki-app`, then switches back live.
- Deploy logs go to `/var/log/deploy.log`.

## Boot Recovery

- `crimewiki-app.service` runs `/usr/local/bin/crimewiki-start.sh` on boot.
- `crimewiki-start.sh` does best-effort `git pull --ff-only origin main` and then `docker compose up -d`.
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

### Current Project State (last updated: 2026-07-24)

- **Goal**: Port all Wikipedia-scraped posts to fully original AI-written content (keep same XML structure/CSS).
- **Done**: Post ID 6 "1965 Highway 101 sniper attack" rewritten and uploaded to DB. Zero Wikipedia text remains; narrative crime-journalism voice; new sections (legal case Reida v. Lund, film Targets, cultural impact); sources replaced; related links now internal.
- **In progress**: Remaining posts (IDs 3-5, 7-22+) still have Wikipedia-scraped content.
- **Rewrite script pattern**: `scripts/rewrite_postN.php` — writes new content via prepared statement UPDATE. Run with `docker exec crimewiki-app-1 php /var/www/html/scripts/rewrite_postN.php`.
- **Content structure to preserve**: `<intro-data>` (5 rows), `<details>` (tbody table), `<sources>` (ul.list), `<related>` (ol.list), `<content>` (h2 sections separated by hr).
- **Pending security fixes**: SQL injection in `include/index_code.php:87` ($month_id unparameterized), CSRF tokens missing on all forms, session cookie flags, display_errors in production.
- **Pending cleanup**: Remove `post copy.php`, `Tennis.php`, `test.php`, `u586058589_crimewiki_db2.sql` from repo.

## Working Tree Notes

- `index.php` currently has an intentional local content change restoring the homepage heading text.
- `.DS_Store` may appear in the working tree and should not be committed.
- The homepage category filter includes `Blog`, but the footer category lists intentionally exclude `Blog`.
- Use `proxy.php?url=...` for live proxy requests. The path-style `/proxy/<urlencoded-url>` route is currently unreliable on production because encoded slashes may be rejected before Apache rewrite reaches PHP.

# CrimeWiki Agent Notes

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

## Working Tree Notes

- `index.php` currently has an intentional local content change restoring the homepage heading text.
- `.DS_Store` may appear in the working tree and should not be committed.
- The homepage category filter includes `Blog`, but the footer category lists intentionally exclude `Blog`.

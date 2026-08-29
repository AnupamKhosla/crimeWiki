## BUG: `/proxy.php` returns 401 for EVERY token (incl. correct ones) — live on crimewiki.site

**Symptom (verified live, 2026-07-29):** `https://crimewiki.site/proxy.php?url=...`
returns HTTP 401 `{"error":"Unauthorized"}` (24 bytes) for the correct
`PROXY_SECRET_TOKEN`, for wrong tokens, AND for no `Authorization` header —
all identical. Site itself is UP (200 via Cloudflare). So the proxy gate is
locked out, not mis-keyed.

**Root cause — the container's `PROXY_SECRET_TOKEN` is EMPTY at runtime.**
`proxy.php:10-13` reads `$valid_token = getenv('PROXY_SECRET_TOKEN')` then
`if (!$valid_token || ...) die(401)`. The `!$valid_token` clause fires
FIRST, so an empty server token hard-locks the endpoint regardless of what
the client sends. The token IS correct in `/etc/secrets/secrets.env`
(`ensure_secrets.sh` writes it), but it never reaches the container.

**Why:** `docker-compose.yml` injects `PROXY_SECRET_TOKEN: ${PROXY_SECRET_TOKEN:-}`
from the HOST shell env (empty default). The var only reaches the container
if it is EXPORTED when `docker compose up` runs. `setup_server.sh:145-149`
does this correctly (`set -a` → `. /etc/secrets/secrets.env` → `set +a`).
BUT the webhook deploy path `deploy.sh:35-37` sources the secrets file
WITHOUT the `set -a`/`set +a` wrap, so `PROXY_SECRET_TOKEN` stays a local
shell var, is never exported, and the container starts with an empty value.
**Every old-style `git push` deploy could re-break the proxy.** Nginx is NOT the cause
(`ops/nginx/crimewiki.conf` forwards `Authorization` by default; no strip).

**DO NOT try a "null matches null" client bypass.** Sending
`Authorization: Bearer ` (empty) passes the string-compare clauses but the
`!$valid_token` server-side clause 401s first. The gate is
server-authoritative; no client token can win against an empty server token.
Fix must be server-side.

**Immediate runtime fix (no code change, on the VM):**

```bash
cd <REPO_DIR> && set -a && . /etc/secrets/secrets.env && set +a && docker compose up -d --build --remove-orphans app-fpm && docker compose up -d --force-recreate --no-deps web
```

Then verify:

```bash
curl -4 -s -o /dev/null -w "%{http_code}\n" -H "Authorization: Bearer <PROXY_SECRET_TOKEN>" "https://crimewiki.site/proxy.php?url=https://www.sikhsangat.com/"
# → expect 200
```

**Permanent fix (stop it recurring):** the repository-owned
`ops/scripts/start_stack.sh` now exports `/etc/secrets/secrets.env` before
running the rebuilt `app-fpm`/`web` stack. `ops/scripts/deploy.sh` restarts
that systemd unit rather than relying on an already-active oneshot service.
That ensures the token reaches the current FPM container and the old Apache
container is reconciled away.

After the next owner-approved deployment, verify the token behavior below.

Verify after fix: 401 with no header (correct), 401 with wrong token
(correct), 200 with the real token + an allowed host (the fix worked).

# CrimeWiki: reliable original-content migration and 1 GB VPS plan

**Prepared:** 2026-08-29
**Audience:** Luna (implementation agent) and the project owner
**Status:** plan plus read-only live baseline; Point 1 and the local Point 2 FPM proof are implemented and tested. The repository default runtime is now Nginx+PHP-FPM; production still requires an owner-run deploy. No production deploy, push, install, restart, or production data/config change has been made.

**Progress:** Point 1 removed the Crime-of-the-Month homepage query, DOM parse, rendered section, and feature-only styles/templates. Point 2’s PHP 8.5-FPM + Nginx stack is now the repository default on local/VM port `8080`; the old Apache Dockerfile/config remains only as documented historical reference and is not selected by Compose. Production cutover is still pending owner-run backup, VPS commands, and approval.

**Performance baseline:** [PERFORMANCE_BASELINE_2026-08-30.md](./PERFORMANCE_BASELINE_2026-08-30.md) records the pre-cutover VPS, origin, Cloudflare, memory, storage, and concurrency measurements. Repeat those same tests after each runtime change.

## Executive decision

Use two deliberately separate tracks.

1. **Historical backlog (do this first):** a ChatGPT/Codex/Luna interactive agent researches each post from its title and Wikipedia locator, writes a fresh five-block XML entry, and applies it to the **local Docker staging database** using one reviewed `scripts/rewrite_post<ID>.php` script per post. This uses the agent session's normal usage allowance, not the Neuralwatt API key or credits. It is the practical way to finish the existing ~1,121 scraped posts while keeping the model away from the copied page body. It must happen in small, reviewable batches across sessions; it is not an unattended API job.
2. **Future daily posts (build after the backlog path is reliable):** a durable, database-backed queue with Neuralwatt as the initial **writer**, limited to one in-flight job by default. Do not use the browser's ten simultaneous SSE requests for bulk work. Use a separate, swappable research adapter that returns an auditable source bundle; the current Neuralwatt request does *not* give the model an actual web-search tool.

ChatGPT Plus is not an API entitlement, so it cannot be silently turned into a website backend. But an interactive Codex/Luna workspace can research, author local files, run approved local database scripts, and verify results. If OpenAI is selected for programmatic research or fallback generation later, create a separately billed API project/key with its own spend cap.

Generate and validate rewrites in a staging copy of the database, then publish an audited, content-only change set to production. Do **not** replace the whole live database: that could overwrite later posts, admin data, and settings.

Runtime optimization can be staged independently now. The repository still uses Apache on the public path, but the local FPM proof can proceed before the rewrite backlog is complete. A production cutover must still keep the public PHP pool separate from long AI work; bulk rewriting is not safe until it uses a durable queue/worker.

## Facts established from this repository

| Area | Current state | Why it matters |
| --- | --- | --- |
| Public PHP runtime | **Live VPS:** `Dockerfile`/`php:8.5-apache` is still running until the owner deploys. **Repository target:** `docker/php/Dockerfile.fpm` behind the repository-owned `web` Nginx container on `127.0.0.1:8080`. | The next deploy removes Apache from the default Compose project while keeping host Nginx and the existing MySQL container. |
| Database | Docker Compose runs `mysql:9.6`; the live `posts` table has 1,121 rows, is about 92.66 MB, and has 128 MB InnoDB buffer pool / 50 max connections. | Docker and MySQL remain memory consumers on the 1 GB VM; the live data snapshot must be used for tuning. |
| Rewrite UI | `rewrite.php` selects ten rows and starts each stream two seconds apart. | Ten requests can be concurrently in flight for minutes. |
| Current provider | `rewrite_api.php` calls Neuralwatt Chat Completions with `deepseek-v4-flash`. | The stream parser now buffers complete SSE events and releases the PHP session lock; those fixes should be retained. |
| Research claim | The request asks the model to use `web_search`, but sends no `tools` field or fetched source material. | Neuralwatt documents function/tool calling that the client must execute; the existing request cannot prove fresh research. |
| Streaming path | The Nginx template disables proxy buffering for exactly `/rewrite_api.php`, uses HTTP/1.1, and sets 1800-second read/send timeouts. PHP emits a padded 15-second heartbeat. | This is the correct direction for the current Apache proxy route, but long browser streams still consume scarce Apache workers. |
| Content contract | `post.php` expects exactly five wrappers: `intro-data`, `details`, `sources`, `related`, and `content`. The existing validator checks only that each tag exists. | A stronger validator is required before any generated text reaches public pages. |
| Earlier direct rewrites | `scripts/rewrite_post6.php` and `scripts/rewrite_post7.php` demonstrate direct local row updates. | Reuse the staged-script workflow, but replace their embedded connection details and legacy output. |
| Live homepage schema | The live `posts` table has no `homepage_rank` column or index. The homepage therefore uses the category-count plus random `id` offset fallback. | Do not claim the indexed homepage migration is deployed; profile and apply it only after backup and route testing. |
| Live public edge | `crimewiki.site` is behind Cloudflare; the observed edge was `BOM` (Mumbai), and HTML/static responses returned `CF-Cache-Status: DYNAMIC`. | The edge is already geographically close to Indian visitors, but dynamic requests still wait for the US origin. Cache policy and origin work come before relocating the VM. |
| Live host packages | The VM is Debian 12. Its configured repositories offer PHP 8.2.33-FPM and MariaDB 10.11, but no PHP 8.5-FPM or native MySQL package. | “No Docker plus latest PHP” requires a separately approved, maintained package source or OS strategy; compiling PHP on the 1 GB VM is not the default plan. |

## Read-only live baseline captured on 2026-08-29

The approved diagnostic used `gcloud compute ssh crimewiki --zone=us-east1-c` and made no remote changes. The VM is an `e2-micro` in `us-east1-c` (`us-east1-c` is the required explicit zone because the local gcloud configuration has no default). It has two vCPUs, 969 MiB RAM, an 8 GiB swap device with 1.1 GiB used, and a 29 GiB root disk with 13 GiB free. Current Docker RSS was approximately 129 MiB for the app, 254 MiB for MySQL, and 4 MiB for phpMyAdmin; none had a Docker memory limit. The app image reports PHP 8.5.3 with OPcache enabled at 128 MiB, Apache prefork, and the default `MaxRequestWorkers 150`; about ten Apache children were resident during inspection. Host PHP 8.2.33-FPM was active but idle, configured as `dynamic` with `pm.max_children = 5`.

The live database reports 1,121 posts with IDs 1–1,140 and gaps. `posts` has only the primary key, unique `wikilink`, and `categoryname` indexes. `Max_used_connections` reached 51, `Created_tmp_disk_tables` was 1,485 versus 3,088 temporary tables, and the slow query log is off with `long_query_time = 10`. These are warning signals, not proof of one culprit: enable bounded diagnostics or run `EXPLAIN` before changing buffer sizes. The `posts` table contains 92.66 MB, and the selected Crime-of-the-Month row was post 1,137 with 523,897 bytes of content; Point 1 removed the homepage code that loaded and DOM-parsed it on every request.

From this location, repeated public tests returned: homepage TTFB about 0.39–1.49 seconds; login about 0.015–0.39 seconds after warm requests; CSS about 0.009–0.40 seconds; representative post pages about 0.89–1.55 seconds. Direct requests to Docker were often faster but variable (homepage about 0.13–2.11 seconds). `/icon.png` is a 404 and still took about 1.1 seconds. Static CSS/JS are gzip-compressed at the edge but still dynamic/no edge cache hit. Point 1 made the local homepage respond in about 16 ms with the existing local stack; public and live-origin timings still need a controlled before/after comparison.

The Docker engine itself used about 53.9 MB RSS across `dockerd`, `containerd`, shims, and proxies; `/var/lib/docker` occupied 4.4 GB. This is reclaimable infrastructure overhead only after every container volume/image has been migrated and backed up. The Docker app and MySQL working sets are separate: replacing Apache/PHP with FPM may reduce the app portion, but native MySQL will still need comparable memory.

Historical production evidence in `CHAT_HANDOFF_2026-08-23.md` recorded a 5–6 second queue under ten concurrent page requests and MySQL using roughly 378 MB. Keep it as historical context beside this current measurement, not as a substitute for a fresh load test.

## Code-review findings Luna must account for

These are observations from the repository review, ordered by relevance to the requested work—not a request for a broad refactor.

| Priority | Finding | Plan action |
| --- | --- | --- |
| Must fix before bulk conversion | `scripts/rewrite_post6.php` / `rewrite_post7.php` use embedded DB access and legacy XML that conflicts with the current contract. | New per-post scripts must load the ignored local config, use prepared statements, write only one ID, and pass the shared validator. |
| Must fix before bulk conversion | `check_xml()` only checks that five tags exist, while `post_code.php` dereferences those tags without defensive fallback. | Validate full shape and safe HTML before the local update; reject bad rows before they can break a public page. |
| Must fix for daily Neuralwatt work | `rewrite.php` can alter schema during a page request and dispatch ten long streams with no durable retry state. | Move schema work to explicit migrations; use the queue/worker only for new ongoing posts. |
| Important for accurate research | The current prompt claims web research but Neuralwatt receives neither a web-search tool nor source bundle. | Agent-driven backlog research uses browser research; future API work uses a real adapter/tool result. |
| Important for weak VPS | The Wikipedia importer starts remote cURL work for every supplied link at once. | Cap/import in chunks or move large imports to an explicit local/CLI batch before asking Neuralwatt to rewrite 100 new posts. |
| Important for perceived PHP speed | Search selects and DOM-parses full `content` for up to 30 rows. Live lacks `homepage_rank`, and the homepage parses a 523,897-byte Crime-of-the-Month post on every request. | Profile search/home/post separately; add the missing homepage index only after backup, and replace repeated full-content parsing with stored/extracted presentation data if profiling confirms it. |
| Important for production hygiene | `index.php` enables display of all PHP errors; the Compose file starts phpMyAdmin as an app dependency. | Turn display errors off in production and make phpMyAdmin opt-in during the later FPM/ops phase; do not mix this with the backlog conversion. |
| Important for deploy reliability | The live VM has Docker 20.10 plus `docker-compose` 1.29.2, not the Compose v2 plugin. The deployed helper scripts already detect and use the legacy binary. | Preserve the compatibility helper, test it after edits, and remember that `up -d` still does not rebuild a changed Docker image. |

The stated deferred MVC/naming restructure remains deferred. Preserve the five-tag rendering contract and focus changes on the conversion pipeline, reliability, and measured performance.

## What probably caused the ten-post failures

Neuralwatt applies per-user/per-model concurrency and token-per-minute limits. Its published steady-state tiers are 2 concurrent slots for trial, 3 for Basic, 5 for Standard, and 10 for Pro; temporary tightening is possible. A rewrite is a long connection, so launching ten jobs creates a rate-limit burst even with a two-second gap. The client currently has no durable job record, retry schedule, or `Retry-After` handling, so a transient 429/503 becomes a visible failed card.

Do not work around this by rotating keys/accounts or increasing browser concurrency. The provider explicitly treats that as evasion. Let its returned HTTP status, `Retry-After` header, and retry metadata govern retries; treat every external response shape as untrusted diagnostic data, not a stable application contract.

## Target design

```text
Admin creates/imports post
          |
          v
posts + rewrite_jobs (database state; no browser-held work)
          |
          v
one locked background worker on the VM
  ├─ ResearchGateway -> verifiable source bundle
  ├─ WriterGateway   -> Neuralwatt (or approved fallback)
  ├─ XML/safety/source/originality validation
  └─ save immutable draft + audit metadata
          |
          v
Admin review/status page (short polling requests, not long generation streams)
          |
          v
content-only staged publish to live posts.content
```

The public site must stay responsive even if the provider is slow, unavailable, or rate-limits. A reboot or browser close must leave a job resumable rather than lose generated text.

## Track A — agent-driven local database conversion (primary backlog path)

This is the missing first-class route. It matches the project's stated rewrite pattern and avoids spending Neuralwatt credits on old posts.

### What an interactive Luna/Codex session is authorized to do

After the owner explicitly approves local database work and starts/provides the local Docker stack, the agent may:

1. Read a **minimal manifest** from the local database: post ID, title, `wikilink`, category, image field, and original-content hash. Do not feed the scraped `content` field to the writer.
2. Research the title and locator through available browsing, then author a new, non-graphic educational entry in the five required XML blocks with fresh source URLs.
3. Create `scripts/rewrite_post<ID>.php` using `include/config.php` / `make_db_connection()` rather than embedded credentials. The script must update only the named local `posts.id`, set the new `content`, and preserve all unrelated columns. Clearing `wikilink` is an explicit per-post decision because it prevents a later scraper/rewrite loop from mistaking the row for uncleansed.
4. Run the script inside the **local** app container, validate the row, and render the local post page. Write any temporary report/export under project `tmp/`, never in a home or system temp directory.
5. Commit no API keys, database credentials, generated dumps, or original scraped content.

Before the first update, establish four non-negotiable local safeguards:

- Confirm `include/config.php` targets the local Compose database service, not a public IP/production host. Inspect it without printing credentials.
- Compare local post count/IDs with the owner-supplied production snapshot date; if it is stale, restore a fresh production dump **only into the local staging database**.
- Create a local staging backup under `tmp/` and prove it can restore to a disposable local database/container.
- Export a manifest under `tmp/` containing only post ID, title, locator, category, image, and content hash. It is the ledger for agent sessions and the eventual content-only production patch.

The current `scripts/rewrite_post6.php` and `scripts/rewrite_post7.php` prove the direct-update concept, but they are not the template to copy blindly: they embed DB connection details, their `details` blocks contain legacy Wikipedia classes/`tbody`, their sources are not consistently clickable URLs, and they populate `related` even though the current rewrite contract reserves it for a separate linker. Luna must produce the current contract, not reproduce legacy defects.

### One repeatable post loop

```text
local DB manifest row → title + locator only → web research → original XML draft
       → validator → scripts/rewrite_post<ID>.php → local Docker update
       → local rendered-page QA → manifest marked complete
```

Start with three posts of different types, then five, then ten. Keep every completed script until the staged database cutover is accepted; they are the smallest possible audit and replay unit. A later agent should first inspect prior scripts/state and choose the next untouched IDs, rather than regenerate completed rows.

Luna must not assume the Docker service/container name, current DB count, or local database is available. The owner must explicitly approve and, if necessary, start the local stack; the first checks are `docker compose ps`, a database row count, and a read-only manifest export. The current shell could not contact the local Docker daemon, so no local data was read or changed during preparation of this plan.

### Why this is better than automating the live admin browser

The agent can use its browser for research and read-only visual QA, but it must not mass-submit the live edit form. The staged direct-DB scripts are reproducible, can be independently validated, work when the public site is down, and produce a content-only release artifact for the owner to apply once. They also do not consume PHP/Apache workers or expose a live admin session to a long automation run.

## Phase 0 — establish a safe baseline (owner runs these read-only checks)

Luna must not change configuration before the owner has supplied the results below. These commands confirm the actual live state without exposing secrets or altering data:

```bash
free -h
swapon --show
df -hT
sudo ss -ltnp
docker ps --format 'table {{.Names}}\t{{.Image}}\t{{.Status}}'
docker-compose ps
docker stats --no-stream
docker-compose config
docker inspect $(docker-compose ps -q app) --format '{{.Config.Image}} {{.Image}}'
sudo nginx -T
sudo systemctl cat nginx crimewiki-app webhook
docker compose exec -T app apachectl -M
docker compose exec -T app apachectl -V
docker compose exec -T app php -i | grep -Ei 'Server API|opcache|memory_limit'
docker compose exec -T app ps -o pid,rss,comm,args
```

Also record five fresh timings for one static asset, `/`, one representative post, and one admin page. Record time to first byte, total time, status, response size, and whether the request crossed Cloudflare. Do not interpret a single request as a performance result.

Before any bulk rewrite, take and verify a logical database backup. Confirm that the free disk space can hold: the live dump, the staging copy, rollback artifacts, and normal Docker growth. The repository does not establish whether the VPS disk is 20 GB or 30 GB; `df -hT` is the required source of truth.

Build a route-test sheet from the rendered `nginx -T` configuration and the tracked `.htaccess`, then test at least `/`, a static CSS/image asset, a post by ID, a title/repeat post URL, `search`, `sitemap.xml`, `sitemap/sitemap-index.xml`, a paged sitemap, `proxy.php?url=...`, admin login, and a request to a deliberately forbidden file such as `/include/config.php` or the SQL-dump filename. Record status, TTFB, total time, cache headers, content type, and upstream marker. This is the source of truth for both static offload and FPM route parity.

For a reproducible memory stress measurement, the owner should make a small, timestamped table of `free -h`, `docker stats --no-stream`, and app process RSS before, during, and after five and then ten concurrent normal public-page requests. Do not run simultaneous rewrite streams for this benchmark; their remote wait time makes them the wrong test for baseline PHP page capacity.

**Exit condition:** a dated baseline note exists, a backup restore has been tested somewhere non-production, and the owner has approved Phase 1.

## Phase 1 — make output safe, original, and auditable

### 1. Add migrations; remove request-time schema changes

Create versioned, idempotent PHP migration scripts under `scripts/`. They must be invoked explicitly, never from a normal page load. Move the current `SHOW COLUMNS` / `ALTER TABLE ... cleansed` logic out of `rewrite.php`.

Create two tables (names may vary, responsibilities may not):

`rewrite_jobs`

- `id`, `post_id`, `status`, `attempt_count`, `next_attempt_at`, `lease_token`, `lease_expires_at`, `provider`, `model`, `prompt_version`, `source_bundle_json`, `last_http_status`, `last_error_code`, `last_error_message`, timestamps.
- Use a unique active-job rule per `post_id`; requeue only after a retryable error or an expired lease.
- Statuses: `queued`, `leased`, `researching`, `writing`, `validating`, `draft_ready`, `needs_review`, `retry_wait`, `failed_permanent`, `published`, `cancelled`.

`rewrite_drafts`

- `id`, `job_id`, `post_id`, `content`, `content_sha256`, `original_content_sha256`, `validation_report_json`, `source_urls_json`, `writer_usage_json`, `created_at`, `approved_at`, `published_at`.
- Never overwrite a prior draft. Retaining drafts lets the owner compare, audit, and recover without re-contacting a provider.

Store only safe summaries of provider errors. Never store an API key, full authorization header, or raw private prompt in the database/logs.

### 2. Build a strict content gate

Create a single validator shared by the worker, the admin save endpoint, and the migration publisher. It must use DOM parsing plus an allowlist; `check_xml()` is insufficient.

Required checks:

1. The five wrappers occur once, in the required order.
2. `intro-data` has exactly five `<tr><th>…</th><td>…</td></tr>` rows.
3. `details` has 6–12 bare table rows; `sources` has 3–6 valid `https?` links; `related` is empty.
4. `content` begins with an `Introduction` `<h2>` and has 4–8 sections separated by `<hr>`.
5. Strip/reject executable or layout-breaking markup: `script`, `style`, `iframe`, forms, SVG, event attributes, unsafe URL schemes, images, inline CSS, and Wikipedia-only reference/UI classes listed in `include/qwen_contract.txt`.
6. Reject Wikipedia URLs in generated content and source list, except where the internal, non-public audit metadata identifies the topic locator.
7. Require every displayed source URL to parse as HTTP(S), have a sensible host, and be distinct. A failed network check should produce `needs_review`, never silently invent a source.
8. Compare generated visible text with the pre-rewrite post text locally. Reject unusually long exact runs and high similarity; do not send the old content back to the model. Keep the threshold configurable and record the score rather than claiming a mathematical guarantee of originality.
9. Validate required facts and source titles by a human sample before bulk publication. Models can invent plausible-looking citations.

The rule is simple: a failed gate makes a private draft or review task, never a public update.

### 3. Separate research from writing

Create two small contracts, for example `ResearchGateway` and `WriterGateway`. The rest of the app only receives a normalized source bundle:

```json
{
  "topic_title": "...",
  "topic_locator": "https://en.wikipedia.org/wiki/...",
  "researched_on": "YYYY-MM-DD",
  "sources": [
    {"url": "https://...", "title": "...", "publisher": "...", "published_at": "...", "notes": "verified fact summary"}
  ]
}
```

Treat provider responses as untyped. Validate this bundle at the adapter boundary and make the writer see only the normalized fields. This keeps future research providers swappable.

For the initial migration, use a research-capable, human-supervised workflow to prepare source bundles. For daily automated work, choose one approved adapter after a small paid/free trial:

- **Recommended writer:** Neuralwatt DeepSeek V4 Flash, using title + topic locator + validated source bundle, with thinking explicitly set to the documented desired level and no undocumented parameters.
- **Research adapter option:** OpenAI Responses API with its documented web-search capability. This requires a separately billed API project; ChatGPT Plus cannot fund it.
- **Alternative:** an owner-approved source collector that calls stable, terms-compliant public/paid APIs and returns links/facts. Neuralwatt function tools require the client to execute the lookup and send tool results back; merely writing “use web_search” in a prompt does nothing.

Do not send the scraped CrimeWiki/Wikipedia body to a writer just to evade a model policy. Ask for factual, non-graphic educational coverage based on source bundles, respect a provider refusal, and route refused topics to `needs_review` for human handling.

### 4. Protect credentials and spending

The current browser-session key is acceptable for a single manual preview, but a scheduled worker needs an owner-controlled server secret.

- Add a dedicated rewriter secret file on the VPS, separate from the repo and `include/config.php`, readable only by the service account that needs it.
- Inject or mount it read-only into the worker runtime; never expose it in HTML, JavaScript, `phpinfo`, git, DB rows, or error pages.
- Put an explicit per-day/project spending cap in the provider dashboard before processing a full batch.
- Keep provider/model/concurrency in a non-secret configuration file or environment variables. Default concurrency is **1**, never an inferred tier limit.
- Record cost/usage when returned by the provider, but make missing usage harmless.

**Exit condition:** one deliberately bad draft is blocked; one valid draft survives browser refresh/reboot; no secret is present in `git status`, page output, or database error text.

## Phase 2 — durable worker and reliable daily production

### 1. Build a CLI worker, not a web request

Implement a CLI entry point such as `scripts/rewrite_worker.php`. It should obtain a short database lease, complete at most one job per invocation by default, persist every state transition, and release/recover leases safely. Run it under a systemd timer or another owner-approved scheduler with a lock; the admin page should enqueue and poll status only.

The worker should use non-streaming provider calls for background jobs unless real-time token capture has a proven operational reason. A worker can wait for a remote response without holding an Nginx/Apache/PHP-FPM request open. Retain SSE only as an optional manual-preview feature.

Use this retry policy:

- Retry network interruption, 429, 500, 502, and 503 only.
- Prefer the provider's `Retry-After` and structured retry instruction when present; otherwise use bounded exponential backoff with jitter.
- Do not retry malformed input, invalid credentials, no balance, content-policy refusal, or validator failure indefinitely; mark them for review with the real reason.
- Cap attempts, preserve prior draft/error evidence, and expose a deliberate “retry” button.
- On startup, reclaim only expired leases; never duplicate an active job.

With one worker, 100 daily posts may take hours. That is acceptable if the worker runs continuously/off-peak and the queue reports estimated throughput. Increase concurrency only after measured success at 1, then 2, while remaining below both the provider quota and the measured PHP/DB memory budget. The worker should never compete with normal visitor traffic for multiple web workers.

### 2. Repair the manual stream without making it the batch engine

Retain the good current pieces in `rewrite_api.php`: persistent upstream SSE buffer, `session_write_close()`, `X-Accel-Buffering: no`, padded heartbeat, and long upstream timeout. Add:

- A server-side and client-side one-active-preview limit per admin user.
- Immediate handling of non-2xx upstream responses, including the sanitized provider error, status, and retry delay.
- A final explicit terminal event for every path, including timeout and upstream error.
- Tests that split SSE at every possible byte boundary, cover `[DONE]`, comment lines, error JSON, a plain non-SSE error body, and a client disconnect.
- UI text that names the selected provider rather than stale “Qwen” wording.

Nginx's existing `/rewrite_api.php` proxy setting is appropriate for the Apache interim path: buffering is off and the read timeout is between upstream reads, so the 15-second heartbeat keeps the connection active. Do not raise timeouts further until logs show a concrete need. The durable worker removes this stream from the normal 100-post path.

### 3. Small, controlled rollout

1. Queue one known short post in staging; inspect its source bundle, draft, validator report, and rendered page.
2. Run 10 sequential staging jobs; intentionally simulate a 429 and a dropped connection. Confirm no lost or duplicate content.
3. Run 25 diverse posts; manually review all. Check historical events, living people, criminal allegations, sources, forbidden markup, internal links, and readability.
4. Run 100 posts; manually review at least 10% plus every validator warning. Track provider failures separately from quality failures.
5. Only then queue the full migration. Keep the batch throttle at the proven rate.

**Exit condition:** retries self-recover, successful jobs have auditable sources/drafts, and normal public pages remain responsive throughout a controlled batch.

## Phase 3 — mass conversion without replacing the whole live database

### Publishing method: direct DB patch, not browser automation

Do not use Puppeteer/Playwright/browser scripting to log into the live admin and submit 1,121 edit forms. It adds session expiry, CSRF/form-shape drift, browser crashes, partial submissions, and no trustworthy record of which fields changed. It would also tie a critical release to the public web path the performance work is meant to protect.

Use browser automation only for **read-only visual QA** of a small representative sample after staging and after publication. The actual release should be a one-purpose CLI publisher that connects to the selected database and updates the intended `posts.content` rows through prepared statements. It must write an audit row for each outcome, preserve every other post field, compare the stored original-content hash before updating, and abort/report rather than overwrite a row changed after the staging snapshot. This gives repeatability, a report, and a content-only rollback path.

### Why not import a whole replacement DB

A full database import could discard posts made after the export, user/admin changes, categories, migrations, and production-only configuration. The safe interpretation of “migrate the whole DB” is: do all content work in a staging clone, then apply one verified content-only patch to the live `posts` rows.

### Staging-to-production sequence

1. Owner takes a consistent production dump and restores it as an isolated staging database/environment.
2. Record each target post ID and `SHA-256` of its original `content`. Do not include system rows 1 and 2.
3. Run all workers against staging. Preserve title, ID, category, image, `wikilink`, and all other metadata unless a separate approved migration says otherwise.
4. Produce a release report: totals by job state, every failure, validator result, source count, output size, similarity score, and a list of records changed since the staging snapshot.
5. Sample rendered staging pages on desktop and mobile. Verify the five-tag XML contract against `post.php` / `post_code.php` and verify that related links remain empty for the separate linker.
6. During an owner-scheduled maintenance window, take a fresh production backup and place the site in maintenance mode.
7. Run a content-only publisher script. For each post, update only when the live `SHA-256(content)` still equals that row's stored original hash. Mismatches are skipped and reported; they are never overwritten.
8. In one bounded transaction/batched transactions, update `posts.content` and the approved migration metadata. Do not update unknown/live-only rows.
9. Run post-publish counts, sample pages, sitemap checks, and error-log review before exiting maintenance. Retain the backup and staging report until the owner accepts the release.

For AdSense, original wording alone is not the finish line. Google requires unique, relevant, value-adding publisher content and warns against copied/replicated material without additional value. The editorial process must add researched sourcing, context, corrections, and human review—especially for crime, violence, allegations, and living people.

## Phase 4 — make the 1 GB VPS faster without risky guesses

### Proven current request and deploy path

The repository proves this current architecture; it is not a guess:

```text
Internet / Cloudflare if configured
    → host Nginx :443
        → 127.0.0.1:8080
            → Docker app: php:8.5-apache (Apache + mod_php)
                → Docker MySQL
```

Host Nginx has no site `root`, no static-file location, and no FastCGI directive. Its normal `location /` proxies everything to port 8080. Therefore HTML, PHP, CSS, JavaScript, images, fonts, and uploads all make the Nginx-to-Apache hop unless an upstream cache serves them. Nginx is the public front door, but the application is **not Nginx-only** and PHP-FPM is **not** currently in use.

The Docker app is built from `php:8.5-apache`, has mod_rewrite/proxy/reqtimeout/evasive enabled, uses a 128 MB PHP request memory limit, and has no explicit Apache worker cap or explicit OPcache setting in the repository. `rewrite_api.php` overrides the normal 30-second PHP execution limit, so each long stream occupies an Apache/PHP process until it completes. Session locking has been fixed with `session_write_close()`, but that does not release the worker itself.

The root `.htaccess` is significant and must be treated as a route/security specification during cutover. It implements:

- `/post/<id>`, `/post/<title>`, and optional title-repeat routes;
- `/sitemap.xml` and paged sitemap routes;
- extensionless PHP fallbacks;
- the legacy path-style proxy route (while the known-safe production form remains `proxy.php?url=...`);
- dot-file and sensitive-extension protection. `include/.htaccess` denies direct access entirely.

The project root currently holds a git-ignored local database dump of about 87.6 MB. Apache's `.htaccess` blocks `.sql` files today. Any Nginx static offload or Nginx/FPM cutover must explicitly deny SQL dumps, `include/`, `.env`, `.git`, `tmp/`, `scripts/`, configuration files, and hidden files before it serves even one file directly.

Finally, `ops/scripts/start_stack.sh` and `ops/scripts/deploy.sh` contain a compatibility helper that prefers `docker-compose` and falls back to `docker compose`; the live VM therefore works with its installed legacy binary. They now build FPM with `--remove-orphans`, force-recreate only the internal `web` Nginx after FPM reconciliation (Docker can change the FPM container IP), and deployment restarts the Compose systemd unit so an already-active oneshot cannot skip reconciliation. This explicitly applies Dockerfile/PHP/OPcache changes and retires services removed from the repository-owned Compose file without needlessly recreating MySQL. The owner must still verify the loaded image/config after deployment.

The live VM has host PHP-FPM, but it is currently irrelevant to public traffic: Nginx has no `fastcgi_pass`, and the active site still reaches Docker Apache. Do not tune or cut over FPM based on its idle two-worker state. First make a staging route map, measure real PHP worker RSS, and prove that the FPM user can read the application tree and connect to Docker MySQL without exposing port 9000.

### Local Point 2 proof completed on 2026-08-29

The repository now contains the default FPM runtime. `docker-compose.yml` builds `app-fpm` from `docker/php/Dockerfile.fpm` and puts the repository-owned `web` Nginx container on loopback port `8080`; the old Apache `app` service and the `fpm-test` profile are gone. The new image is `php:8.5-fpm`; the official image already supplies DOM/Lexbor, cURL, mbstring, and OPcache, so only `mysqli` is compiled. The pool is `ondemand` with `pm.max_children = 2`, a 10-second idle timeout, and worker recycling after 300 requests. OPcache is enabled at 64 MB with timestamp validation during development.

The local Nginx route was corrected to avoid a serious source-exposure failure: extensionless routes now internally enter the PHP handler instead of serving `login.php`, `search.php`, or `rewrite.php` as text. It also denies repository paths (`include/`, `scripts/`, `tmp/`, `docker/`, `ops/`, `docs/`), dotfiles, Docker/config/SQL files, and hides `X-Powered-By`. It includes explicit MIME handling, gzip for ordinary text, conservative one-hour static caching, and an exact unbuffered 1,800-second FastCGI location for `rewrite_api.php`.

The route matrix passed through both stacks: homepage, extensionless and direct login, ID and title/repeat post URLs, all sitemap forms, search, unauthenticated rewrite redirect, and 404. Protected-file probes returned 403. Deterministic response hashes for a post, sitemap, and CSS file matched Apache exactly. Twenty local requests showed FPM slightly ahead on ordinary routes (homepage average 7.4 ms versus Apache 8.5 ms; CSS 1.0 ms versus 1.5 ms); ten-way concurrent homepage requests had no failures and similar throughput. Idle local container memory was about 65 MiB for Apache versus about 11 MiB for FPM plus 9 MiB for Nginx. These are development-host measurements, not VPS promises; the production decision still requires the same test on the e2-micro.

The FPM proof does not itself authorize production deployment. The repository is ready for an owner-run cutover: host Nginx continues pointing at loopback `8080`, the internal `web` container forwards PHP to `app-fpm`, port `9000` remains reserved for the webhook, MySQL remains unchanged, and the old Apache container is removed as an orphan during Compose reconciliation. Rollback is a Git-based redeploy of the last accepted revision; no Apache container is kept running. Do not route long-lived AI streams through the two normal workers without an isolated pool or queue worker.

### Immediate low-risk changes after baseline measurement

1. Keep host Nginx serving TLS and static assets. Do not add a Node service.
2. First, add an Nginx **extension allowlist** for static assets only (CSS, JS, fonts, images, manifests) after a staging route/security test. This can remove static responses from Apache without exposing the repository root. Carry over required CORS headers for fonts/images from `.htaccess`, add conservative immutable caching only for fingerprinted/versioned assets, and keep PHP and all unknown paths proxied until the FPM cutover.
3. phpMyAdmin is now behind the explicit `tools` Compose profile and is not a default runtime dependency. The old Apache proxy configuration is retained only as historical reference and is no longer active in Compose.
4. Treat `homepage_rank` as a not-yet-deployed migration. After a verified backup, add the column and the category/rank index in staging, compare `EXPLAIN` and timing, then apply it live only as an owner-approved schema change. It removes the random count/offset fallback but will not fix the larger full-content parse by itself.
5. Remove repeated homepage parsing of post 1,137’s 523,897-byte content: store a separately validated introduction/source fragment or cache the extracted presentation result with an explicit invalidation rule. Keep source content authoritative in `posts.content`.
6. Add an Nginx/static asset cache policy after route/security testing. The current edge sees CSS/JS/images as `DYNAMIC`; use versioned URLs or safe revalidation rather than caching admin/PHP responses. Fix or remove the broken `/icon.png` reference.
7. Inspect actual MySQL allocation before tuning. The present `sort_buffer_size` (2 MB), `join_buffer_size` (2 MB), `read_buffer_size` (1 MB), and `read_rnd_buffer_size` (1 MB) are per-connection allocations; with 50 possible connections and 32 MB temporary-table limits, they can create far larger peaks than the 128 MB InnoDB pool alone suggests. Tune down only from measured workload/slow-query evidence; do not simply increase the InnoDB pool.
8. Add bounded slow-request visibility: PHP-FPM slowlog after the cutover and MySQL slow-query logging temporarily during diagnosis. Use data to choose query/index work, then revisit overhead.

### Region decision: keep `us-east1-c` for now

Do not move the VM to India during this optimization pass. Google recommends placing compute near the point of service to reduce latency, but this site already terminates at a Cloudflare Mumbai edge (`cf-ray` ended in `BOM` during testing). The origin is still dynamic, so Indian visitors pay the US-origin leg for PHP/DB work, while a move would make US/European visitors pay a longer origin leg. First reduce origin TTFB, cache public static assets, and measure visitor geography in Cloudflare analytics. Revisit the region only when traffic distribution and origin TTFB show that an India origin beats the current global trade-off; keep the same `e2-micro` constraint for this phase.

### Version policy and the ultimate no-Docker target

“Latest” and “fastest” are different decisions. The target PHP branch should be the latest maintained PHP 8.5.x with FPM; PHP 8.5 remains actively supported through 2027 and receives security support through 2029, while the installed PHP 8.2 branch reaches security end-of-life at the end of 2026. Debian 12 does not provide 8.5 in its configured repositories. The low-risk Point 2 cutover is therefore a `php:8.5-fpm` container with Apache removed. The ultimate no-Docker target needs a vetted PHP 8.5 package source and an explicit maintenance plan; do not compile it on the VM as an improvised optimization.

For the database, keep the working MySQL 9.6 during the PHP/Nginx work. If a fresh native MySQL installation is later required, evaluate the closest supported MySQL 9.7 LTS track after a logical dump/restore and compatibility test. MariaDB 12.3 is a current LTS option, but MySQL-to-MariaDB is a compatibility migration, not a performance toggle; do not switch engines merely because it is available in a package repository. Query plans, indexes, buffer sizing, and avoiding full-content parsing matter more than the major-version label for this 1,121-row workload.

Measured expectation for removing Docker: the engine alone frees roughly 54 MB RSS and up to 4.4 GB disk after safe cleanup. Replacing the Apache app with two measured FPM workers may produce roughly 80–150 MB total resident-memory improvement, but this is an estimate until the FPM benchmark runs. Native MySQL will not eliminate its approximately 254 MB working set. Request latency may improve by milliseconds or a modest percentage; it should not be promised as a 2× speedup. Docker removal’s strongest benefit is memory headroom and simpler process topology, not raw PHP execution speed.

Use a measured memory budget rather than folklore. On the 1 GB host, collect base resident use after boot, p95 PHP worker RSS under a representative page, MySQL RSS, and the swap trend while ten requests run. Start with a target that leaves at least 150–200 MB genuine RAM headroom and does not steadily grow swap. Choose `pm.max_children` from that evidence, not CPU count. With the handoff's low free-memory history, start at **two** ondemand FPM children and raise to three only after the measurement shows it is safe. The background rewrite worker is a CLI process and must not consume an FPM child.

### Planned architecture cutover

The recommended intermediate target is:

```text
Cloudflare (if enabled) → host Nginx → Docker PHP-FPM app (loopback only) → existing Docker MySQL
```

This removes the Docker Apache/mod_php application process while retaining both Docker MySQL and the Docker engine as rollback-safe first steps. It avoids an unplanned Debian PHP package-source change and keeps the tested PHP 8.5 branch. A later native-FPM move can remove the app container; native MariaDB is a separate project, only if measurements show the Docker/database stack still exceeds the memory budget. Neither is the immediate answer to measured homepage cost: current PHP/DOM work and uncached static paths should be addressed first.

The ultimate no-Docker architecture is `Cloudflare → Nginx → native PHP 8.5-FPM → native database`. Reach it in three independent migrations: first remove Apache while keeping the tested FPM and MySQL containers; then move PHP-FPM native only if the package/maintenance plan is sound; finally perform a logical dump/restore to the selected native database and compare row counts, schemas, checksums, queries, memory, and rollback. Removing Docker and changing runtime/database engines in one maintenance window makes a failure difficult to localize.

Luna must first discover the exact live route behavior—including friendly post URLs, `proxy.php?url=...`, PHPMyAdmin behavior, and any untracked server rewrite rules. Do not invent a generic `try_files` rule and hope it matches Apache. Build an endpoint matrix and reproduce every required route in a staging Nginx configuration. At minimum it must cover the known `.htaccess` mappings above, preserve the query-string proxy route, and deny the files Apache currently protects. Do **not** restore the encoded-slash path-style proxy as a production dependency unless it is specifically tested through the real Nginx front door.

For the FPM configuration, begin conservatively after measuring real worker RSS:

- `pm = ondemand`; initially cap `pm.max_children` at 2; set a short idle timeout; recycle with a measured `pm.max_requests` value if worker growth is observed.
- Put the FPM socket behind correct owner/group permissions; never expose port 9000 publicly.
- Enable a small OPcache allocation first (for example, 32 MB rather than a blind 128 MB) and measure it. If timestamp validation is disabled for speed, the deployment must reload/restart FPM after each code change; otherwise code can remain stale.
- Serve only the audited static allowlist directly from Nginx and deny direct access to `include/config.php`, `.env`, the 87.6 MB SQL dump, git directories, `include/`, `scripts/`, `tmp/`, and other non-public files.
- Use an exact FastCGI location for the optional manual SSE endpoint with `fastcgi_buffering off`, `X-Accel-Buffering: no`, and the same 1800-second read/send timeout. Nginx documents that disabled FastCGI buffering forwards bytes synchronously and that its read timeout applies between reads.

### Streaming chain: what is already correct and what must be verified

For the current Apache proxy route, the repository already does the three necessary things: PHP disables its output buffering, `/rewrite_api.php` sets `X-Accel-Buffering: no` and 15-second padded heartbeat comments, and Nginx sets `proxy_buffering off` with 1800-second read/send timeouts. Nginx's read timeout is between received bytes, not an overall 30-minute stopwatch, so the heartbeat is relevant and should remain.

What is not yet proven is the **rendered live** chain: Cloudflare (if active) → host Nginx → Docker Apache → PHP → Neuralwatt → back through all proxies. The owner must run an authenticated one-post stream test while observing Nginx access/error logs and app logs, recording: immediate `researching` event, time to first heartbeat, time to first token, all status codes, stream end, and whether the browser sees every token. Repeat once against `127.0.0.1:8080` and once through the public domain if permitted; the difference isolates front-proxy behavior without SSH automation by Luna.

After FPM cutover, replace—not duplicate—the exact proxy location with an exact FastCGI location carrying `fastcgi_buffering off`, the same inactivity timeouts, and no response cache. Test that route before retiring Apache. The reliable daily worker avoids this entire browser streaming chain.

### Deployment and rollback requirements for runtime changes

Any PHP/Apache/FPM/OPcache/MySQL configuration change requires this release discipline:

1. Build/configure in staging and record `php -v`, PHP SAPI, loaded INI files, and image/config identifiers.
2. Run syntax/config tests (`nginx -t`, PHP-FPM config test, PHP syntax checks) and the endpoint matrix.
3. Put the site in maintenance only for the owner-approved cutover; take a DB backup first.
4. Explicitly rebuild/recreate the affected container or reload the native FPM service. Verify the loaded INI value after restart—do not infer it from Git history.
5. Measure static and PHP timings, worker count/RSS, MySQL RSS, disk, and swap before declaring success.
6. Keep the last accepted Git revision and repository-owned runtime templates available for rollback. Rollback means a deliberate Git-based redeploy during maintenance; do not keep a second Apache container running.

Do not declare the FPM cutover complete until all of these have passed in production and the Git-based rollback has been tested or explicitly accepted by the owner:

1. `nginx -t` and PHP-FPM config validation pass.
2. Home, post, search, login, upload, admin, sitemap, proxy query route, 404, and rewrite preview tests pass.
3. Static and PHP timing are measured before and after under the same conditions.
4. Five-to-ten concurrent public-page requests stay within the memory budget without swap thrashing.
5. A rollback command and confirmed database backup exist.

### Database container follow-up

Keep MySQL Dockerized initially. A native MariaDB move has real risk: dump/import time, disk space, credentials, SQL compatibility, and rollback. Consider it only after the FPM cutover produces measurements. If pursued, use a fresh logical export/import on a staging host, verify schemas/data/counts/checksums, switch only the app connection, and retain the old Docker volume untouched until acceptance.

Container memory limits can stop one service consuming the host, but they can also cause an OOM kill. Set them only after establishing a workload budget; do not use a hard limit as a substitute for correct MySQL/FPM sizing. Swap is an emergency buffer, not operating memory.

### Owner-run FPM cutover checklist

The repository is prepared, but the following production operation belongs to the owner because it changes live traffic and restarts containers:

1. Take and verify a fresh logical database backup. Keep it outside Git and do not place its password in shell history, logs, or this repository.
2. Confirm the working tree/commit intended for release and that `/etc/secrets/secrets.env`, the VM Compose `.env`, and `include/config.php` remain present only on the VM.
3. Deploy through the existing webhook, or run the repository-installed `/usr/local/bin/deploy.sh` during an owner-scheduled window. It switches host Nginx to maintenance, pulls code, explicitly rebuilds `app-fpm` with one compiler job, removes the retired Apache Compose orphan, force-recreates only `web` so it resolves the new FPM container address, stops phpMyAdmin, and restarts the stack.
4. Verify on the VM:

       sudo docker-compose config --services
       sudo docker-compose ps
       sudo docker-compose exec -T app-fpm php-fpm -t
       sudo ss -ltnp

   The default service list must be `db`, `app-fpm`, `web`; no `app` Apache container should remain running; phpMyAdmin should be stopped; host port `8080` should be the only application listener and webhook port `9000` must remain separate.
5. Verify loopback routes through `http://127.0.0.1:8080/` and the public HTTPS routes, then run the exact baseline timing, memory, worker, disk, and swap tests. Check the app log for FPM startup and Nginx errors before disabling maintenance.
6. If acceptance fails, switch the checkout to the last accepted Git revision and run the same maintenance/deploy path. This is the documented rollback; no Apache container is kept alongside FPM.

## Acceptance tests Luna must automate or document

- PHP syntax checks and `git diff --check` for every change.
- Unit/fixture tests for provider parsing and every SSE byte-boundary split.
- Validator test corpus: valid content, missing wrapper, duplicate wrapper, script URL, invalid source, Wikipedia reference markup, wrong intro row count, non-empty related block, and high-similarity text.
- Database job tests: duplicate enqueue, worker crash/expired lease, retry-after scheduling, permanent failure, manual approval, and original-content hash conflict at publish.
- Render checks for a representative criminal, organization, event, and non-crime legacy post; inspect mobile output too.
- A load check proving that a running rewrite worker does not cause the public homepage queue observed in the handoff.

## Order of implementation and ownership gates

| Gate | Luna may do after explicit approval | Owner decision required |
| --- | --- | --- |
| A | Create local migrations, gateways, validator, worker, tests, and admin status UI. | Approve schema/files and provide no secrets to git. |
| B | Run local/staging tests with a test key supplied securely by the owner. | Select Neuralwatt-only versus an OpenAI API research fallback; set spend cap. |
| C | Process staged sample jobs and create reports. | Approve quality threshold and sample-review results. |
| D | Prepare, but do not execute, publish and rollback commands. | Owner takes backup, schedules maintenance, and runs all VPS/deploy actions. |
| E | Maintain the repository-owned FPM/Nginx runtime, route tests, and owner-run cutover checklist. | Owner approves the production performance cutover and handles backup, deploy, and rollback commands. |

## Repository files likely to change (do not edit all at once)

- `rewrite.php` — convert from a ten-stream batch launcher into queue/status/review UI.
- `rewrite_api.php` — retain as a strictly limited manual preview endpoint or retire after the queue UI is accepted.
- `include/` — add small provider/research/validation classes; do not scatter API parsing across page files.
- `scripts/` — explicit migrations, worker, staging export/import validation, and content-only publisher.
- `docker/php/`, `docker-compose.yml`, `docker/nginx/`, `ops/nginx/`, `ops/scripts/`, and `ops/systemd/` — repository-owned runtime/configuration for the FPM performance phase. The root `Dockerfile` and `docker/apache-*` files remain historical Apache references and are not selected by Compose.
- `docs/` — operations runbook, migration report template, and owner-run command checklist.

## References consulted

- [Neuralwatt rate limits](https://portal.neuralwatt.com/docs/guides/rate-limits) — per-tier concurrency, rate-limit behavior, and temporary limits.
- [Neuralwatt error handling](https://portal.neuralwatt.com/docs/guides/error-handling) — retryable statuses and provider retry metadata.
- [Neuralwatt streaming](https://portal.neuralwatt.com/docs/guides/streaming) — Chat Completions SSE shape and `[DONE]` termination.
- [OpenAI billing separation](https://help.openai.com/en/articles/9039756) — ChatGPT subscriptions and API billing are separate.
- [Nginx proxy module](https://nginx.org/en/docs/http/ngx_http_proxy_module.html) and [FastCGI module](https://nginx.org/en/docs/http/ngx_http_fastcgi_module.html) — buffering and read-timeout semantics.
- [PHP-FPM configuration](https://www.php.net/manual/en/install.fpm.configuration.php) and [OPcache configuration](https://www.php.net/manual/en/opcache.configuration.php) — process caps and cache invalidation behavior.
- [PHP supported versions](https://www.php.net/supported-versions.php) — current PHP branch support and security end dates.
- [MySQL releases: Innovation and LTS](https://dev.mysql.com/doc/refman/8.4/en/mysql-releases.html) — LTS/Innovation tracks and the 9.7 upgrade path.
- [MariaDB maintenance policy](https://mariadb.org/about/) — current LTS series and community maintenance dates.
- [Docker resource constraints](https://docs.docker.com/engine/containers/resource_constraints/) — unbounded container memory and swap/OOM behavior.
- [Google Publisher Policies](https://support.google.com/adsense/answer/10502938) — requirements around low-value and replicated content.
- [Google Cloud regions and zones](https://cloud.google.com/compute/docs/regions-zones) and [E2 shared-core machines](https://cloud.google.com/compute/docs/general-purpose-machines) — location guidance and `e2-micro` CPU/memory characteristics.
- [Cloudflare cache responses](https://developers.cloudflare.com/cache/concepts/cache-responses/) and [default cache behavior](https://developers.cloudflare.com/cache/concepts/default-cache-behavior/) — interpretation of `DYNAMIC` and static-file caching.

## Definition of done

The project is ready for the full rewrite only when every target row has a tracked outcome; public content has passed the strict XML/safety/originality gate; the owner has reviewed representative factual samples; failed jobs are resumable; no API key is committed; a production backup and content-only rollback path are verified; and the batch no longer relies on multiple long-lived browser streams. Runtime optimization may proceed independently, but bulk rewriting must still use the durable queue/worker and must not compete with the public FPM pool.

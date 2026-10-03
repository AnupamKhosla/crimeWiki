# CrimeWiki roadmap

Living document · started 3 October 2026 · revised 4 October 2026 · **read this first**

This is the single entry point for the owner and for any agent. It says what
the project is for, what is true today, what comes next, and which of the
older documents still apply. Detailed plans stay in their own files and are
linked from here.

Standing rules from `AGENTS.md`: nothing runs on the production server without
the owner's permission, `git push` triggers a deploy, and **nothing goes live
without the owner's go-ahead for that session**. Agents and workflows run only
on the owner's yes.

## 1. Goals

Updated 4 October 2026 from what the owner said in the 3 and 4 October
sessions. The rules that go with these goals are in `AGENTS.md`.

1. Every article is our own original research and writing, with real
   sources, so the site can pass AdSense review and fund itself. Wikipedia
   may be read and the sources it cites may be used, but a page must never
   look like a Wikipedia clone. The owner's rule is quoted in `AGENTS.md`.
2. **First, now:** articles on what people are searching for today, meaning
   current crime events and people in the news, so the site gains search
   traction. The short legacy posts come after.
3. Grow the catalogue: 5,000 good articles, then 10,000, with 100,000 to
   1,000,000 as the long-term direction.
4. Long-form articles: aim for 1,200 to 2,000 words, with 1,000 words of
   real content as the floor for publishing. No filler. A topic that cannot
   support the length is held or blocked.
5. Every batch gets an independent fact-check before it is published. The
   automatic checker cannot see a sentence that claims more than its source.
6. Writing runs on flat subscriptions, not per-token billing. The main Claude
   Code session writes the articles. Agents and workflows spend the plan
   allowance, so they run only when the owner says yes.
7. The live database is the source of truth. New posts are inserted straight
   into it, with a backup first and the owner's go-ahead. There is no local
   database (owner, 3 October 2026).
8. The 1 GB VPS stays fast and recovers by itself as the catalogue grows:
   no Docker, a native database, supervised services.
9. Eventually, every post has a proper image. A post about a person shows
   that person. Each image has usable rights and a credit.

## 2. Where things stand (checked 4 October 2026)

**The legacy rewrite is live, but most of it is still Wikipedia's wording.**
Measured on 4 October 2026 (`docs/WIKIPEDIA_OVERLAP.md`): of 1,092 legacy
posts compared with the Wikipedia page of the same title, 837 fail the clone
test. 821 contain runs of 12 or more words identical to Wikipedia, and 379
share a quarter or more of their wording with it. 255 pass. The audits of
3 October checked length, filler and Wikipedia links, not wording, so the
earlier statement here that the rewrite was "essentially done" was wrong.

- The release `tmp/releases/crimewiki-content-20260906-115650` holds
  rewritten content for 1,116 of the 1,121 live posts.
- I ran the padding audit on it: median 1,035 real words, about 0% filler,
  a median of 5 sources, and no Wikipedia links in any file.
- It is published. For posts 3, 500 and 1000, every sentence I sampled from
  the release appears on the public page at `crimewiki.site`.
- Not covered: posts 1 and 2 (outside the release scope), posts 6 and 7
  (rejected because their source lists were empty), and 1 post left
  unchanged.

**17 new posts are live (3 and 4 October 2026).** The site has 1,138 posts,
and the highest id is 1175.

- They are topics 1 to 17 of `tmp/new-posts/topics.jsonl`: posts 1143,
  1147-1149, 1157-1163, 1165 and 1171-1175. Each has 1,330 to 2,340 words and
  8 to 14 sources. 19 topics remain; the next is 18 (Ghislaine Maxwell).
- **Independent check:** done for topics 5 to 11, by check agents. Not done
  for topics 1 to 4 and 12 to 17, which had only their writer's re-read. The
  owner asked on 4 October for 12 to 17 to be redone properly (track A,
  step 11).
- **Wikipedia clone test, 4 October:** all 17 pass. No copied runs, at most
  one shared heading, and under 1% of 6-word runs shared with Wikipedia's
  page. 13 topics have a Wikipedia page and 4 have none.
- The tools are in `tmp/new-posts/`, which git ignores. They, the research
  folders and the articles exist only on the owner's Mac.

**The day-1 and day-2 packages are research seeds, not pages.**

- Day 1 package: 665 short articles (median 334 words), no filler.
- Day 2 package: 587 articles, of which 32 are genuinely long. The rest are
  about 71% filler. See `CONTENT_SCALE_PLAN.md` section 2.
- Neither is in the production database, and neither meets the length
  standard. The queue built from them (`tmp/rewrite-queue/`) is paused.

**Short legacy posts.** `docs/REWRITE_LIST.md` lists 532 live posts under
1,000 words, 144 at 1,000 to 1,199, and 15 with no usable sources. Six
rewrites are finished and unpublished in `tmp/live-rewrite/articles/` (posts
631, 632, 649, 652, 653 and 890). Post 533 is under length and post 1071 is
blocked. The owner paused this on 3 October in favour of new posts.

**Runtime today:** Cloudflare → host Nginx → PHP-FPM in Docker
(`127.0.0.1:9070`) → MySQL 9.6 in Docker. Apache is gone and the FPM cutover
is complete (`LUNA_IMPLEMENTATION_PLAN.md` line 469).

**Server check, 3 October 2026.** Read-only, run with the owner's approval.
The script and its output are in `tmp/day-002-audit/vps_readonly_check.*`.

- Debian 12 on an e2-micro, up 48 days. 969 MiB RAM with 492 MiB available;
  536 MiB of swap in use but no active swapping. 29 GB disk, 12 GB free.
- Containers: PHP-FPM 8.5.9 (13 MiB), MySQL 9.6.0 (99 MiB), phpMyAdmin
  (20 MiB). The old `web` container is stopped. Docker images take 2.5 GB,
  of which 1.0 GB is reclaimable.
- Database: 1,121 posts, none empty. The `posts` table is 16.5 MB in the
  Dynamic row format. Buffer pool 128 MB. The data volume is 232 MB.
- The server's repository is clean at commit `a3b9191`.
- **No native database is installed.** Debian offers `mariadb-server`
  10.11.18. `mysql-server` has no install candidate.
- **A native `php8.2-fpm` service is installed and running.** The
  repository's Nginx config sends PHP to the Docker FPM on port 9070, so it
  appears to be unused and holding memory.
- **The server's `include/config.php` contains no environment overrides.**
  Changing the database host means editing that file on the server.
- Public ports are 22, 80 and 443. The database is not exposed.

Since that check: commit `eb8213f` (post URLs) was deployed on 3 October, and
17 posts were inserted.

## 3. Track A: content

**Order of work now (owner, 3 and 4 October 2026):** new posts on current
topics come first. On 4 October the owner asked for 10 more (topics 18 to 27,
step 7). Step 13 (legacy posts that are still Wikipedia's wording) and
step 11 (live new posts that never had an independent check) follow, in the
order the owner chooses. Each step needs the owner's go-ahead.

1. **Finish the last legacy posts.** Rewrite posts 6 and 7 with sources,
   and check posts 1 and 2 and the one unchanged post. Not started.
2. **Add a padding gate to the content kit**, so a padded package fails
   validation whoever wrote it. `tmp/new-posts/check_new.py` already runs the
   padding audit on new posts; the kit's own validator still lacks it.
3. **Build the new-post path. Done on 3 October 2026, as tools in `tmp/`.**
   `tmp/new-posts/publish.py` inserts new rows into the live database: backup
   first, dry run in a rolled-back transaction, one transaction for the batch,
   duplicate-title guard, hash check, live-page check. Runbook:
   `tmp/new-posts/PLAN.md`. Still open: move the tools from `tmp/` (not in
   git) into the repository.
4. **Lengthen the short legacy posts. Paused by the owner on 3 October.**
   The list is `docs/REWRITE_LIST.md`. Six rewrites wait, unpublished, in
   `tmp/live-rewrite/articles/`. The day-1 and day-2 packages are research
   seeds for this work (`tmp/rewrite-queue/queue.jsonl`).
5. **Agents and workflows: only on the owner's yes for that run.** What has
   been tried, all on 3 October:
   - A Sonnet worker pilot. It produced three good articles but used 20% of
     a session, and the owner stopped it (`CONTENT_SCALE_PLAN.md` section 5.2).
   - `tmp/new-posts/new_posts_workflow.js`, at the owner's request: one
     writer and one check agent per topic, plus a legal reviewer for topics
     8 to 10. Three topics took about 3 minutes.
   - `tmp/new-posts/one_post_parallel_workflow.js`: 11 agents on one post
     (5 searchers, 1 fetcher, 1 writer, 3 checkers, 1 fixer). It took about
     410,000 tokens and 5 minutes, and the checkers found 22 problems.
   - Agents cannot talk to each other, and the steps for one post run in
     order. More agents means more posts at once, not a faster post.
6. **Fix the topic queue** so non-crime titles, generic terms and duplicates
   are removed before research starts.
7. **Steady production of new posts. Active.** The loop is in
   `tmp/new-posts/PLAN.md`: search, fetch, Wikipedia as a lead-finder, read,
   write, automatic check, writer's re-read, independent check, the owner's
   go-ahead, publish. Batches of 5. The topic list is
   `tmp/new-posts/topics.jsonl` (36 topics, 17 live, next is 18).
8. **Fill `<related>`** with reviewed internal links once the catalogue
   justifies it. Every new article currently leaves it empty.
9. **Post URLs and search hygiene. Done and deployed 3 October 2026
   (commit `eb8213f`).**
   - Post URLs are hyphen slugs, such as `/post/rex-heuermann`: the lowercase
     title with apostrophes removed and every other run of punctuation or
     spaces turned into one hyphen; accents stay. `post_slug()` and
     `find_post_id_by_slug()` are in `include/functions.php`. The lookup
     matches the slug against the titles, with no schema change.
   - Old `%20` URLs, `/post/<id>` and any other spelling get a 301 to the one
     canonical URL. A post that does not exist returns a real 404. Meta
     descriptions no longer start with "Introduction".
   - Checked read-only on live before the deploy: every post resolved to
     itself (`tmp/url-test/live_check.py`).
   - At a much larger catalogue the lookup needs a `slug` column with an
     index; today it scans the titles, as the old title lookup did.
10. **Images, later.** New posts are seeded with `default.png`, and the day-2
   manifest leaves `image` and `image_credit` empty. Needed: a source policy
   (public domain, a licence that allows reuse with credit, or the owner's
   own images), local hosting instead of hot-linking, a visible credit, and
   a portrait of the person for posts in the Criminals category. Nothing has
   been built. The owner decides the source policy.
11. **Check and correct the live posts that never had an independent check.**
    Topics 12 to 17 (posts 1165 and 1171 to 1175) first, as the owner asked on
    4 October. Topics 1 to 4 (posts 1143 and 1147 to 1149) are in the same
    state. Each gets an audit file (`PLAN.md` step 9), which also lists what
    Wikipedia's page covers and ours does not. `publish.py` can only insert,
    so corrections need an update mode first: backup, dry run, content-only
    `UPDATE` by id, hash check. Corrections go live only on the owner's
    go-ahead.
12. **Keep pages from looking like Wikipedia.**
    - Done on 4 October, in the tools. `fetchmany.py` saves a Wikipedia
      article as a lead (its text and the pages it cites), never as a citable
      source. `check_new.py` fails an article that shares copied runs, section
      headings or too much wording with that page. All 17 live posts pass.
    - Open, needs a deploy: the stylesheet on every post page still loads two
      images from Wikipedia's servers (`assets/css/inline.min.css`: a PDF icon
      from `upload.wikimedia.org` and a magnify icon from `en.wikipedia.org`).
      Remove the two rules or host the icons locally.
13. **Replace Wikipedia's wording in the legacy posts. Not started. This is
    the largest AdSense risk on the site.** 837 of 1,092 legacy posts fail the
    clone test; `docs/WIKIPEDIA_OVERLAP.md` lists them, worst first. Two parts,
    both for the owner to decide:
    - Rewrite them from opened sources, worst first, with the new-post loop
      and an update mode for `publish.py`. Step 4's list ranks by length; this
      one ranks by copied wording, which matters more.
    - Until a post is rewritten, keep it out of search results: a `noindex`
      tag and no sitemap entry for the failing ids. Then an AdSense review
      sees only original pages. This is a small code change and needs a deploy.

## 4. Track B: infrastructure

The target is `Cloudflare → Nginx → native PHP-FPM → native database`, with
no Docker. `LUNA_IMPLEMENTATION_PLAN.md` (lines 410–478) sets the method:
separate migrations, never two in one maintenance window, each with a tested
rollback.

| # | Step | Status | Gate before starting |
|---|---|---|---|
| B1 | Remove Apache; Nginx talks to FPM | **Done** | n/a |
| B2 | Database: Docker MySQL 9.6 → native database on loopback | Not started | Owner chooses the engine; verified logical dump |
| B3 | PHP-FPM: Docker → native | Not started | Owner chooses a PHP package source |
| B4 | Remove the Docker engine | Not started | B2 and B3 accepted; separate approval to delete volumes |
| B5 | Automatic recovery with systemd and health checks | Not started | Easier after B2 and B3, when services are native units |
| B6 | Search: move off `LIKE '%term%'` | Not started | Owner accepts changed matching behaviour |
| B7 | Cloudflare cache rule for `/search*` | Not started | Owner creates it in Cloudflare |

Notes on each open step:

- **B2, database.** The documented plan is native MariaDB 10.11 from the
  Debian repository: owner takes and verifies a dump, import, compare row
  counts, schemas and content hashes, switch the app with `DB_HOST` in a
  maintenance window, and keep the Docker volume untouched as the rollback.
  - The owner has said "direct MySQL". Checked on the server: Debian has no
    `mysql-server` package, so native MySQL means adding Oracle's package
    repository. MariaDB 10.11.18 installs from Debian directly. This is an
    owner decision; MariaDB is the lower-maintenance route.
  - The 6 September dump has 3 InnoDB tables and no MySQL-only collations,
    so table definitions should load into MariaDB. This still needs a real
    import test.
  - Checked on 3 October: the server's `include/config.php` does not read
    the `DB_HOST` environment variable. The switch therefore needs a
    `config.php` edit on the server, with the old file kept for rollback.
  - The expected RAM saving is small. The database's working set of 250 to
    300 MB stays whichever way it runs.
- **B3, PHP-FPM.** Debian 12 packages PHP 8.2, and the site is tested on
  8.5. The plan notes 8.2 leaves support at the end of 2026. Going native
  needs a decision on where PHP 8.5 comes from.
- **B5, recovery.** Today there is only Docker's `restart: unless-stopped`,
  `Restart=always` on the webhook, and a boot unit. There are no health
  checks. The requirement is bounded retries, no restart loops, and
  persistent errors left visible.
- **B6, search.** Six ordinary indexes are live, but advanced search still
  scans every article. This becomes the first scaling limit, at around
  10,000 posts. See `CONTENT_SCALE_PLAN.md` section 8.

Database compression is deliberately not on this list. It is not needed
below roughly 100,000 posts.

## 5. Track C: safety and cleanup

Found in the repository on 3 October 2026. None are fixed.

**Security items are not listed here.** This repository is public, so the
five open security items found on 3 October are kept in `tmp/SECURITY_TODO.md`
on the owner's machine, which git ignores. Read that file before any
security work.

| Item | Where | Why it matters |
|---|---|---|
| CSS hot-links two icons from `en.wikipedia.org` and `upload.wikimedia.org` | `assets/css/inline.min.css`, inline on every post page | A Wikipedia fingerprint on pages that must not look like a wiki clone. See track A, step 12. |
| Dead files still tracked | `post copy.php`, `Tennis.php`, `test.php` | Clutter, and extra public PHP entry points. |
| Proxy 401 fix never verified after deploy | `docs/proxy-401-bug.md` | Unknown whether the fix works in production. |

Do not delete the root SQL dump: `docker-compose.yml` mounts it as the seed
for a fresh local database.

The MVC restructure stays deferred until the content pipeline is steady.

## 6. Decisions waiting on the owner

0. **Legacy posts that are still Wikipedia's wording** (track A, step 13):
   the rewrite order, and whether to hide the failing posts from search until
   they are rewritten.
1. **Check agents:** a standing yes for the independent check on every
   batch, or asked batch by batch? They spend plan allowance. The 10 posts the
   owner asked for on 4 October wait on this answer.
2. **Topics 1 to 4:** give them the same independent check as 12 to 17?
3. **Git:** the docs are committed (4 October). Git still ignores
   `tmp/new-posts/`, so the tools, the research folders and the articles exist
   only on the owner's Mac. Move the tools into the repository? It is public.
4. **The two Wikipedia-hosted icons** in the post stylesheet (track A,
   step 12): fix and deploy?
5. Native **MySQL or MariaDB** for step B2.
6. **phpMyAdmin access:** see `tmp/SECURITY_TODO.md`.
7. **Search:** accept FULLTEXT matching in place of substring matching.
8. **Images:** the source policy (track A, step 10).

Decided, listed so that nobody reopens them:

- **Wikipedia (4 October 2026):** it may be read and the sources it cites may
  be used. It is never cited, and a page must never look like a clone of it.
- **Long-form is required** (1,000 words or more). The day-1 and day-2 short
  articles are research seeds.
- **No local database** (3 October). Live is the source of truth. New posts
  go in through `publish.py` in batches of 5, and a publish go-ahead covers
  one session.
- **No agents, workers or workflows without a yes for that run** (3 October).
- **Neuralwatt is prohibited.**
- **Session state** lives in `docs/STATE.md`; `supermemory` is optional.

## 7. Document map

| Document | Use it for | Status |
|---|---|---|
| `docs/ROADMAP.md` | Goals, current state, order of work | Current |
| `docs/STATE.md` | What the last session did, what is in progress, open decisions | Current; rewritten every session |
| `tmp/new-posts/PLAN.md` | Runbook for new posts: the loop, the checks, publishing | Current, revised 4 October. Not in git. |
| `tmp/longform/BRIEF.md` | Article format and voice (its sections 2 and 3) | Current for format. Its queue steps belong to the paused rewrite queue. Not in git. |
| `docs/REWRITE_LIST.md` | The short legacy posts that need lengthening | From the 3 October snapshot; work paused |
| `docs/WIKIPEDIA_OVERLAP.md` | Every legacy post compared with Wikipedia; the failing ones, worst first | Current, measured 4 October |
| `docs/CONTENT_SCALE_PLAN.md` | Day-2 audit, the Sonnet pilot's measurements, scale limits, database sizing | A record. Its goals and its sections 5 and 6 are superseded by this roadmap. |
| `AGENTS.md` | Rules for agents | Current, revised 4 October |
| `README.md` | Install and operations guide | Mostly current |
| `docs/LUNA_IMPLEMENTATION_PLAN.md` | Infrastructure method: lines 352–500 | Infrastructure parts current. Its job-queue phases were never built. Its Neuralwatt recommendation is void: the owner prohibited Neuralwatt. |
| `docs/CHATGPT_CHAT_CONTENT_PIPELINE_PLAN.md` | Package and import design ideas | Plan only; never implemented. Superseded as the writing route. |
| `tools/crimewiki-content-kit/` | Contract, validator and packager used for the Grok packages | In use; lacks a padding gate |
| `docs/PERFORMANCE_BASELINE_2026-08-30.md` | Measurements before the FPM cutover | Historical |
| `docs/proxy-401-bug.md` | Proxy secret bug | Fix committed, unverified |
| `CHAT_HANDOFF_2026-09-01.md` | Search optimisation state | Current for search |
| `CHAT_HANDOFF_2026-08-30.md` | Security patch list | Backlog still valid |
| `CHAT_HANDOFF_2026-08-23.md`, `session.md` | Early sessions | Historical |

Proposed, not done: move the historical files into `docs/archive/`.

## 8. Corrections applied to `AGENTS.md` on 3 October 2026

Made at the owner's request so new agents start in the right place. The
working rules were not changed.

- Added a "Start Here" section pointing at this roadmap and `docs/STATE.md`.
- "Main Goal" now says the legacy rewrite is live and the goal is growth with
  long-form original articles. It records the length standard and the padding
  gate rule, and marks the Luna/Codex and Neuralwatt pipelines as historical.
- "Session State Tracking" now uses `docs/STATE.md`. `supermemory` is
  optional because it is not installed.
- The architecture section names the database: MySQL 9.6 in Docker.
- Fixed three stale facts: the "Qwen" endpoint name, the `index.php`
  working-tree note, and a mention of Apache.

## 9. Corrections of 4 October 2026

Made at the owner's request, after a session that published six posts without
the independent check and treated an agent's "Never Wikipedia" as the owner's
rule.

- `AGENTS.md`: the owner's Wikipedia rule in the owner's words; the
  independent check as a requirement, with the question asked before a batch
  is written; the real publishing route (live database, `publish.py`, a
  go-ahead per session); current-topic posts as the priority; and a new
  "Honest Reporting" section.
- This roadmap: goals, current state, track A, decisions and the document map.
- `tmp/new-posts/PLAN.md`: a Wikipedia lead-finder step, an independent-check
  step with an audit file, and "ask before a batch". `tmp/longform/BRIEF.md`
  and both workflow prompts no longer say "never Wikipedia".
- Tools: `fetchmany.py` saves Wikipedia as a lead, and `check_new.py` gained
  the clone test (track A, step 12).
- Later the same day: the five open security items moved from track C to
  `tmp/SECURITY_TODO.md`, because the repository is public. The legacy posts
  were compared with Wikipedia for the first time (`docs/WIKIPEDIA_OVERLAP.md`),
  which corrected section 2 and added track A step 13.

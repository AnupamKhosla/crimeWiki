# Session Log — 2026-07-24

## What We Did

### 1. Full Project Audit
- Read entire codebase: PHP CMS/wiki, Docker (app + mysql:9.6 + phpmyadmin), Nginx reverse proxy, webhook deploy pipeline.
- Identified security issues (prioritized):
  - SQL injection in `include/index_code.php:87` — `$month_id` interpolated raw into query
  - Config file RCE in `include/setup.php:167` — password injected into PHP source unescaped
  - No CSRF tokens on any form
  - Session cookie missing httponly/secure/samesite flags
  - `display_errors = 1` in production (`index.php:3-5`)
  - Host header injection in `include/nav.php` (`$_SERVER["SERVER_NAME"]` in href)
  - Image upload has no MIME whitelist (`include/addpost_code.php:145-155`)
  - Stored XSS risk from Wikipedia-scraped HTML echoed unsanitized
- Owner decision: security fixes are secondary priority. Focus on content porting first.

### 2. Content Rewriting Strategy (MAIN GOAL)
- **Problem**: All posts (IDs 3-22+) are Wikipedia-scraped. Google AdSense will reject for plagiarism.
- **Solution**: AI (me, in opencode sessions) rewrites each post with:
  - Completely original narrative crime-journalism voice (NOT encyclopedic)
  - Different section ordering, different emphasis, added analysis
  - Fresh research via websearch (court cases, films, cultural impact Wikipedia doesn't cover)
  - Same XML structure preserved: `<intro-data>`, `<details>`, `<sources>`, `<related>`, `<content>`
  - Wikipedia links replaced with internal CrimeWiki links
  - New sources cited (court records, newspapers, books, films)
- **Pattern**: `scripts/rewrite_postN.php` → `docker exec crimewiki-app-1 php /var/www/html/scripts/rewrite_postN.php`

### 3. Post 6 Rewrite — DONE
- "1965 Highway 101 sniper attack" fully rewritten and uploaded to DB.
- Researched: Reida v. Lund court case (18 Cal.App.3d 698), film *Targets* (1968), coroner's records.
- New sections: "The Boy Who Played Saxophone", "Sunrise Over Highway 101", "The Legal Reckoning: Reida v. Lund", "A Shadow on American Cinema", "Why It Still Matters".
- Zero Wikipedia text remains. Wikilink set to NULL. Related links now internal.
- Script: `scripts/rewrite_post6.php`

### 4. AGENTS.md Updated
- Added semi-caveman mode rules (100-200 word responses)
- Added working style rules (explain, pause, ask before acting)
- Added API future-proofing rules
- Added Session State Tracking section with current project state

### 5. Supermemory Setup (partially done)
- Owner logged in via `npx opencode-supermemory@latest login`
- Copied MCP URL: `https://mcp.supermemory.ai/mcp`
- Opened `~/.config/opencode/opencode.json` in VS Code for owner to add MCP config
- Owner needs to add: `"mcp": { "supermemory": { "type": "remote", "url": "https://mcp.supermemory.ai/mcp" } }`
- Then restart opencode.

## Current State

- Docker: all 3 containers running. Site at **localhost:8080**.
- DB container had exit(127) issue — fixed with `docker compose up -d db`.
- Post 6: DONE (original content in DB).
- Posts 3-5, 7-22+: still Wikipedia-scraped, need rewriting.
- `scripts/rewrite_post6.php` exists as the template pattern for future rewrites.

## Next Steps (for next session)

1. Verify supermemory MCP is working after restart.
2. Continue rewriting posts — next candidates by size (easiest first):
   - Post 7: "2001 Greyhound bus attack" (6.9KB)
   - Post 6 already done
   - Post 8: "2008 Skagit County shootings" (11.2KB)
   - Post 5: "101 California Street shooting" (29.4KB)
   - Then larger ones: 3, 4, 9, 10, 11, 12, 13, 14, etc.
3. For each post: websearch for fresh angles → write original narrative → upload via PHP script.
4. After content porting is well underway, circle back to security fixes.

## Pending Cleanup (low priority)
- Remove dead files: `post copy.php`, `Tennis.php`, `test.php`, `u586058589_crimewiki_db2.sql`
- Fix SQL injection in `include/index_code.php:87`
- Add CSRF tokens
- Harden session cookies
- Disable display_errors in production

## Key Decisions Made
- Content must be completely different from Wikipedia (not paraphrased) — different structure, voice, emphasis, sources.
- Keep same XML/CSS structure so frontend doesn't break.
- AI does the rewriting in opencode sessions (no external API endpoint needed yet).
- Security is secondary to content porting for now.
- AGENTS.md is the primary session-state mechanism (supermemory as bonus once configured).

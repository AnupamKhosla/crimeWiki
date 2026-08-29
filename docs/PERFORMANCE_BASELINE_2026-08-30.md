# CrimeWiki performance baseline — 2026-08-30

This is the before-change baseline for the planned sequence:

1. Keep MySQL unchanged.
2. Stop starting phpMyAdmin by default.
3. Replace the Docker Apache application with the tested Nginx + PHP-FPM path.
4. Repeat the same measurements and compare results.

No production service, database, configuration, or files were changed during this capture. The VPS commands were read-only. The only load test was a bounded request test against this site’s own origin; it did not call the AI endpoint.

## Capture conditions

- VPS: Google Cloud crimewiki, e2-micro, zone us-east1-c.
- VPS capture time: 2026-08-29T18:44:23Z to 2026-08-29T18:47:58Z (2026-08-30 in IST).
- OS: Debian GNU/Linux 12 (bookworm), Linux 6.1.0-52-cloud-amd64.
- CPU: 2 vCPUs.
- Public tests: run from the development machine against https://crimewiki.site.
- Origin tests: run inside the VPS, once through host Nginx/TLS and once directly against Docker Apache at 127.0.0.1:8080.
- Each ordinary route timing used five sequential requests. Averages below are not p95 values.
- The concurrency test used 20 homepage requests at concurrency 1, 5, and 10. Every request returned HTTP 200.

## Current request topology

    Public browser
        -> Cloudflare edge
            -> host Nginx :443/:80
                -> 127.0.0.1:8080
                    -> Docker crimewiki_app_1
                        -> Apache prefork + PHP 8.5.3/mod_php
                            -> Docker MySQL crimewiki_db_1

Important distinctions:

- Host apache2 is inactive. Apache is running inside the Docker application container.
- Host PHP 8.2-FPM is active, but public Nginx does not use it.
- Host Nginx has no root, static-file location, or fastcgi_pass; it proxies normal traffic to Apache.
- Host Nginx has an exact /rewrite_api.php proxy location with buffering disabled and 1,800-second read/send timeouts.
- The current public runtime is still Apache. The local FPM stack on port 8081 is not deployed here.

## VPS resource baseline

### Memory before the request benchmark

    Mem: 969Mi total, 720Mi used, 129Mi free, 266Mi buff/cache, 249Mi available
    Swap: 8.0Gi total, 1.1Gi used, 6.9Gi free
    Load average: 0.02, 0.05, 0.04

The more useful capacity number is 249MiB available, not the used column alone. Linux accounts for shared pages, kernel memory, and reclaimable cache differently from individual process RSS. ps RSS values also double-count shared libraries when manually added.

### Memory by container and process

| Component | Measurement | Interpretation |
| --- | ---: | --- |
| MySQL container | 234.9MiB | Largest single container; normal for a running MySQL server with the configured 128MiB InnoDB pool plus overhead. |
| Apache/PHP app container | 137.2MiB | Main removable application-runtime target. It had 11 processes, including Apache/PHP workers. |
| phpMyAdmin container | 4.2MiB | Small at idle, but not required for public traffic. |
| mysqld host RSS | 226,384 KiB | Confirms MySQL is the largest host process. |
| dockerd host RSS | 30,480 KiB | Docker engine overhead, not the MySQL memory. |
| containerd host RSS | 21,544 KiB | Container runtime overhead. |
| Host Nginx workers | about 6.9MiB each | Small compared with Apache/PHP and MySQL. |
| Apache children | about 15–19MiB RSS each | RSS cannot be summed exactly because libraries/pages are shared; the container total is the better number. |
| Google agents, Exim, journald, SSH, other host services | roughly tens of MiB combined | Normal host overhead. |

All three containers reported no Docker memory limit (HostConfig.Memory=0, MemorySwap=0). They can therefore compete for the whole VM and trigger host-level memory pressure.

### Swap and CPU

swpd was approximately 1.1GiB. During two short vmstat samples, si and so were zero or near zero, and CPU idle was 98–100%. This means the VPS has accumulated swapped pages but was not continuously thrashing during the capture. Repeat vmstat after each change; a rising si/so rate is the warning sign.

### Disk and Docker storage

| Area | Measurement |
| --- | ---: |
| Root filesystem | 29GiB total, 15GiB used, 13GiB free, 55% used |
| /var/lib/docker | 2.4GiB |
| Docker overlay layers | 2.0GiB |
| Docker images | 1.944GiB total; 523.5MiB reported reclaimable |
| Docker volumes | 314MiB |
| Docker container logs/metadata | about 93MiB in the directory breakdown |

Removing Docker later could reclaim disk and about a few tens of MiB of engine/runtime RAM, but it would not remove MySQL’s roughly 235MiB unless MySQL itself is also moved.

## Runtime versions and services

- Host Nginx: 1.22.1, active.
- Host Apache: inactive.
- Host PHP 8.2-FPM: active, idle, not connected to public Nginx.
- Docker application: PHP 8.5.3, Apache 2.4.66, mpm_prefork, mod_php.
- Docker MySQL: mysql:9.6.
- phpMyAdmin: running by default.
- Webhook: 127.0.0.1:9000; this port must not be reused for FPM.
- Application HTTP port: 0.0.0.0:8080 and [::]:8080 through Docker proxy.

The live application’s PHP configuration reported:

    memory_limit = 128M
    opcache.enable = On
    opcache.memory_consumption = 128M
    opcache.interned_strings_buffer = 8M
    opcache.validate_timestamps = On
    opcache.revalidate_freq = 2

This is the current Apache-container setting. The local FPM test uses a separate 64M OPcache configuration and has not changed the VPS.

## Public Cloudflare measurements

Five requests per route from the development machine:

| Route | Avg TTFB | Avg total | Response bytes | Status | Edge observation |
| --- | ---: | ---: | ---: | --- | --- |
| / | 1.3481s | 1.5590s | 438,019 | 200 | CF-Cache-Status: DYNAMIC, BOM edge |
| /login.php | 0.5874s | 0.5887s | 6,720 | 200 | Dynamic |
| /post/3 | 0.4216s | 0.4269s | 25,549 | 200 | Dynamic |
| /sitemap.xml | 0.4102s | 0.4113s | 2,617 | 200 | Dynamic |
| /assets/css/style.css | 0.4070s | 0.4145s | 56,201 | 200 | Dynamic despite being static |

The public tests used Cloudflare HTTP/2. The edge was in Mumbai (cf-ray suffix BOM). Average DNS time was only a few milliseconds, TCP connection about 35ms, and TLS about 78–93ms. Public TTFB therefore includes the Cloudflare-to-US-origin leg and dynamic origin work. The 438KB homepage payload is also much larger than the other ordinary pages.

Representative response headers included:

    server: cloudflare
    cf-cache-status: DYNAMIC
    content-type: text/html; charset=UTF-8
    content-type: text/css; charset=utf-8

## Origin measurements from inside the VPS

### Through host Nginx and local TLS

This used https://crimewiki.site resolved to 127.0.0.1, so it exercised host Nginx, TLS, and the existing Apache upstream without Cloudflare:

| Route | Avg TTFB | Avg total | Bytes | Status |
| --- | ---: | ---: | ---: | --- |
| / | 0.4668s | 0.4829s | 437,404 | 200 |
| /login.php | 0.0663s | 0.0664s | 6,720 | 200 |
| /post/3 | 0.0511s | 0.0556s | 25,307 | 200 |
| /sitemap.xml | 0.0354s | 0.0355s | 2,617 | 200 |
| /assets/css/style.css | 0.0260s | 0.0263s | 56,201 | 200 |

Each curl request opened a fresh connection, so the HTTPS values include local TLS setup. They should not be interpreted as the pure cost of Nginx proxying.

### Directly to Docker Apache at 127.0.0.1:8080

| Route | Avg TTFB | Avg total | Bytes | Status |
| --- | ---: | ---: | ---: | --- |
| / | 0.1606s | 0.1664s | 437,796 | 200 |
| /login.php | 0.0356s | 0.0357s | 6,720 | 200 |
| /post/3 | 0.0496s | 0.0521s | 25,292 | 200 |
| /sitemap.xml | 0.0100s | 0.0102s | 2,479 | 200 |
| /assets/css/style.css | 0.0018s | 0.0137s | 56,201 | 200 |

Small byte differences on the homepage/post are expected because homepage ordering and some generated markup are dynamic. The deterministic content checks belong in route parity tests, not timing tests.

## Bounded concurrency result

Twenty homepage requests were issued at each concurrency level. Every request returned HTTP 200.

| Path | Concurrency | Avg TTFB | Avg total |
| --- | ---: | ---: | ---: |
| Host Nginx/TLS origin | 1 | 0.1063s | 0.1151s |
| Host Nginx/TLS origin | 5 | 0.3212s | 0.3447s |
| Host Nginx/TLS origin | 10 | 2.5243s | 2.6376s |
| Direct Docker Apache | 1 | 0.1071s | 0.1147s |
| Direct Docker Apache | 5 | 0.3072s | 0.3170s |
| Direct Docker Apache | 10 | 0.5351s | 0.5603s |

The direct-app result isolates application concurrency better. The increase at ten simultaneous requests shows queueing under Apache/PHP, although the fresh-TLS origin test adds connection overhead. Repeat this exact test after FPM with the same route and request count.

## Worst-case search and 50-request stress test

The application’s advanced search is a more realistic worst-case route than the homepage. With an empty title and `advance=on`, it can search content with `LIKE '%%'`, return up to 30 rows, parse each result’s full XML-like content with `DOMDocument`, and then run a count query. The test URL was:

    /search.php?title=&advance=on

Five public requests through Cloudflare all returned HTTP 200. TTFB was 2.3195s, 1.5785s, 2.2939s, 0.7265s, and 1.3029s; total time was 2.3593s, 1.6191s, 2.3346s, 0.9714s, and 1.3425s. Each response was about 118KB. This confirms the route is materially slower and more variable than ordinary pages.

Five direct origin requests to `127.0.0.1:8080` also returned HTTP 200. TTFB was 0.4860s, 0.3508s, 0.9330s, 0.3396s, and 0.3058s; total time was 0.4898s, 0.3545s, 0.9365s, 0.3431s, and 0.3088s. This isolates the application from Cloudflare and public-network latency.

A controlled burst of 50 concurrent requests was then sent directly to the application port. The homepage burst completed successfully. Before that burst, the VM had 178MiB available RAM, 1.1GiB swap in use, 22 Apache processes, an app container at 193.5MiB, and MySQL at 260.5MiB. During/after it, the app reached 195.5MiB and 24 Apache processes; MySQL reached 267MiB. The burst command returned success for all clients.

The same 50-request burst against the advanced search route did not complete during the bounded observation window; the diagnostic SSH session stopped producing samples after more than 90 seconds, so it was stopped. This is a stress result, not a claim that every request individually has a fixed timeout. Immediately afterward, the VM showed load `29.95, 15.50, 6.06`, 165MiB available RAM, 1.2GiB swap used, and `vmstat` showing 87–92% I/O wait with active swap-in. On recovery, load fell to `5.96, 11.28, 5.51`, available RAM rose to 259MiB, `vmstat` returned to 96–99% idle with near-zero swap/I/O, Apache returned to 22 processes, the app was 176.8MiB, and MySQL was 219.6MiB.

This confirms that 50 simultaneous expensive searches are beyond the safe capacity of the current 1GiB VM. It also validates the next order: remove Apache from the request path, use a small ondemand PHP-FPM pool, keep MySQL unchanged, add search limits/indexing/cache work separately, and never run AI generation synchronously inside a public request pool.

## Post-FPM production verification

This section is the after snapshot captured on 2026-08-29 UTC after commit `dab2b75` was deployed by the existing webhook. It must not be confused with the pre-cutover baseline above.

The live runtime is now host Nginx → Docker `web` Nginx → Docker `app-fpm` → Docker MySQL. Apache is no longer running. phpMyAdmin is present but exited and is not part of normal runtime. Host port 8080 is loopback-only; webhook port 9000 remains separate; FPM port 9000 is internal to the Docker network.

Public HTTPS smoke requests all returned HTTP 200: homepage TTFB 0.585s, login 0.392s, post 0.492s, sitemap 0.344s, CSS 0.614s, and advanced search 1.882s total. Direct loopback-origin requests all returned HTTP 200: homepage 0.138s, login 0.014s, post 0.132s, sitemap 0.023s, CSS 0.001s, and advanced search 0.484s total. These are one-sample validation values, not replacement averages.

The repeated stress test sent 50 concurrent requests directly to `127.0.0.1:8080`. The homepage burst returned `50 200`; the advanced-search burst also returned `50 200`. After the search burst, load was `0.59, 1.03, 1.38`, available RAM was 219MiB, `vmstat` was about 99% idle with negligible I/O wait, and swap use was about 292MiB. Container memory was web 5.4MiB, FPM 72.0MiB at peak capture, and MySQL 303.1MiB. This is a major capacity improvement over the pre-FPM 50-search event, which reached load 29.95, 87–92% I/O wait, and active swap-in before the diagnostic was stopped.

The first FPM deployment required about two minutes because the VPS had to pull `php:8.5-fpm`/`nginx:stable-alpine` and compile `mysqli`; subsequent builds should use Docker cache. Keep maintenance mode during image changes. The remaining search warning (`Undefined array key "category"`) and the high absolute search cost are application/query work, not reasons to revert FPM.

## Current bottleneck ranking

1. **Apache/PHP application working set and prefork workers.** The app container is about 137MiB and holds multiple Apache/PHP processes. This is the first runtime target. FPM should use a deliberately small ondemand pool, initially two workers, and should not carry long AI streams indefinitely.
2. **MySQL working set.** About 235MiB is expected for the current server and is the largest single process. Moving it remotely could free most of this RAM, but adds network latency and operational risk. Keep it unchanged for the first comparison.
3. **Low available memory and historical swap.** Only about 249MiB was available before testing. The system was not actively thrashing during the sample, but the margin is small.
4. **Homepage payload and origin work.** The homepage is about 438KB and public responses are dynamic at Cloudflare. Removing the old 524KB Crime-of-the-Month parse was useful, but homepage generation and edge caching remain separate issues.
5. **Docker disk/runtime overhead.** Docker occupies about 2.4GB and roughly tens of MiB RAM. Removing Docker alone will not produce a large PHP speedup or remove MySQL memory.
6. **CPU.** CPU was mostly idle during the baseline. More CPU is not the first fix indicated by this capture.
7. **phpMyAdmin.** It uses little idle RAM, but it is unnecessary public-stack overhead and should be opt-in.

## Repeatable comparison commands

Run the following after each owner-approved runtime change. Do not include the AI rewrite endpoint in this baseline load test.

    free -h
    vmstat 1 5
    docker stats --no-stream
    ps -eo pid,ppid,user,%mem,rss,comm,args --sort=-rss | head -25
    df -hT /
    sudo du -xhd1 /var/lib/docker 2>/dev/null | sort -h
    sudo ss -ltnp

Repeat five requests for these routes through the public site and, from inside the VPS, through both the host Nginx origin and direct application port:

    /
    /login.php
    /post/3
    /sitemap.xml
    /assets/css/style.css

Repeat 20 homepage requests at concurrency 1, 5, and 10. Record status, TTFB, total time, response size, CPU idle, available memory, and vmstat si/so. Use the same client location and avoid comparing a warm Cloudflare cache with a dynamic response.

## Acceptance criteria for the next phase

- MySQL remains healthy and its configuration is unchanged.
- phpMyAdmin is not started by the default production command.
- Nginx/FPM route parity passes for public pages, admin redirects, posts, sitemaps, proxy query form, static files, 404, and protected repository files.
- FPM is bound only to loopback/internal networking; it does not use webhook port 9000.
- Apache rollback remains available until the owner accepts the result.
- Available RAM improves or remains stable, and repeated vmstat does not show sustained swap-in/swap-out.
- Direct application timing and public timing are reported separately.
- The AI stream is tested separately; long generation work is eventually moved to a queue/CLI worker so it cannot consume the entire public FPM pool.

## Related plan

See docs/LUNA_IMPLEMENTATION_PLAN.md for the staged FPM cutover, database alternatives, rewrite queue, stream handling, backup, and rollback requirements.

## Public follow-up load tests before direct host-Nginx cutover

These tests were run from the development machine against the live public URL on 2026-08-30, after the earlier FPM cutover but before removing the Docker `web` proxy. They are client-side measurements; no new VPS SSH/server-log capture was performed.

| Test | Result | Timing |
| --- | --- | --- |
| 50 concurrent advanced-search requests | 50/50 HTTP 200; no client errors | Average TTFB 7.293s, average total 7.352s, maximum total 12.776s |
| 200 concurrent advanced-search requests | 112/200 HTTP 200; 88 client-side 30-second timeouts with no response bytes | Successful responses averaged 16.520s TTFB and 16.574s total; maximum successful total 29.705s |

The 200-request result confirms that public edge/origin concurrency is still unsafe at this level, even though the bounded direct-origin 50-request FPM test passed earlier. Repeat the same client test after the direct host-Nginx cutover, and pair it with the owner-run VPS monitor for RAM, swap, CPU, Docker RSS, and Nginx/PHP-FPM logs.

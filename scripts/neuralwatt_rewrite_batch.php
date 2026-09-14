<?php

declare(strict_types=1);

require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/functions.php';
require_once __DIR__ . '/rewrite_local_common.php';

function nw_fail(string $message, int $code = 1): never
{
    fwrite(STDERR, "FAILED: {$message}\n");
    exit($code);
}

function nw_arg(array $argv, string $name, string $default): string
{
    foreach ($argv as $index => $argument) {
        if (str_starts_with($argument, $name . '=')) {
            return substr($argument, strlen($name) + 1);
        }
        if ($argument === $name && isset($argv[$index + 1])) {
            return (string) $argv[$index + 1];
        }
    }
    return $default;
}

function nw_normalize_url(string $url): string
{
    $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($url === '') {
        return '';
    }
    if (str_starts_with($url, '//')) {
        $url = 'https:' . $url;
    } elseif (str_starts_with(strtolower($url), 'http://')) {
        $url = 'https://' . substr($url, 7);
    } elseif (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . ltrim($url, '/');
    }
    return preg_replace('/#.*$/', '', $url) ?: $url;
}

function nw_force_https_hrefs(string $content): string
{
    return preg_replace_callback(
        '#\bhref=(["\'])http://([^"\']+)\1#i',
        static fn(array $match): string => 'href=' . $match[1] . 'https://' . $match[2] . $match[1],
        $content
    ) ?? $content;
}

function nw_is_source(string $url): bool
{
    $parts = parse_url($url);
    $scheme = strtolower((string) ($parts['scheme'] ?? ''));
    $host = strtolower((string) ($parts['host'] ?? ''));
    if (!in_array($scheme, ['http', 'https'], true) || $host === '') {
        return false;
    }
    foreach (['wikipedia.org', 'wikimedia.org', 'duckduckgo.com', 'google.com', 'bing.com'] as $blocked) {
        if (str_ends_with($host, $blocked)) {
            return false;
        }
    }
    return true;
}

/** @return array<string, array{status:int, body:string, error:string}> */
function nw_fetch_many(array $jobs, int $concurrency = 16): array
{
    if ($jobs === []) {
        return [];
    }
    $multi = curl_multi_init();
    $pending = array_keys($jobs);
    $active = [];
    $finished = [];
    do {
        while (count($active) < $concurrency && $pending !== []) {
            $key = array_shift($pending);
            $handle = curl_init((string) $jobs[$key]);
            curl_setopt_array($handle, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 4,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_TIMEOUT => 18,
                CURLOPT_USERAGENT => 'CrimeWiki research fetcher/1.0',
                CURLOPT_HTTPHEADER => ['Accept: text/html,application/xhtml+xml,text/plain;q=0.8,*/*;q=0.4'],
                CURLOPT_ENCODING => '',
            ]);
            curl_multi_add_handle($multi, $handle);
            $active[(int) $handle] = ['key' => $key, 'handle' => $handle];
        }
        do {
            $status = curl_multi_exec($multi, $running);
        } while ($status === CURLM_CALL_MULTI_PERFORM);
        while ($info = curl_multi_info_read($multi)) {
            $handle = $info['handle'];
            $id = (int) $handle;
            $meta = $active[$id] ?? null;
            if ($meta === null) {
                continue;
            }
            $body = (string) curl_multi_getcontent($handle);
            $finished[(string) $meta['key']] = [
                'status' => (int) curl_getinfo($handle, CURLINFO_HTTP_CODE),
                'body' => substr($body, 0, 1200000),
                'error' => (string) curl_error($handle),
            ];
            curl_multi_remove_handle($multi, $handle);
            unset($active[$id]);
        }
        if ($active !== []) {
            if (curl_multi_select($multi, 1.0) === -1) {
                usleep(100000);
            }
        }
    } while ($pending !== [] || $active !== []);
    curl_multi_close($multi);
    return $finished;
}

function nw_text(string $html, int $limit): string
{
    if ($html === '') {
        return '';
    }
    $document = new DOMDocument('1.0', 'UTF-8');
    if (!@$document->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
        return trim(substr(preg_replace('/\s+/u', ' ', strip_tags($html)) ?: '', 0, $limit));
    }
    foreach (['script', 'style', 'noscript', 'nav', 'footer', 'header', 'form', 'svg', 'sup'] as $tag) {
        $nodes = $document->getElementsByTagName($tag);
        for ($i = $nodes->length - 1; $i >= 0; $i--) {
            $node = $nodes->item($i);
            if ($node !== null && $node->parentNode !== null) {
                $node->parentNode->removeChild($node);
            }
        }
    }
    $root = $document->getElementById('mw-content-text');
    if (!$root) {
        $main = $document->getElementsByTagName('main');
        $root = $main->length ? $main->item(0) : $document->getElementsByTagName('body')->item(0);
    }
    $value = $root ? $root->textContent : $document->textContent;
    $value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return trim(substr(preg_replace('/\s+/u', ' ', $value) ?: '', 0, $limit));
}

/** @return list<string> */
function nw_links(string $html): array
{
    $document = new DOMDocument('1.0', 'UTF-8');
    if ($html === '' || !@$document->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
        return [];
    }
    $links = [];
    foreach ($document->getElementsByTagName('a') as $anchor) {
        $url = nw_normalize_url((string) $anchor->getAttribute('href'));
        if (nw_is_source($url)) {
            $links[] = $url;
        }
    }
    return array_values(array_unique($links));
}

/** @return list<string> */
function nw_search_links(string $html): array
{
    $found = [];
    foreach (nw_links($html) as $url) {
        $found[] = $url;
    }
    $document = new DOMDocument('1.0', 'UTF-8');
    if ($html !== '' && @ $document->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
        foreach ($document->getElementsByTagName('a') as $anchor) {
            $href = nw_normalize_url((string) $anchor->getAttribute('href'));
            $parts = parse_url($href);
            parse_str((string) ($parts['query'] ?? ''), $query);
            if (isset($query['uddg'])) {
                $decoded = nw_normalize_url((string) $query['uddg']);
                if (nw_is_source($decoded)) {
                    $found[] = $decoded;
                }
            }
        }
    }
    return array_values(array_unique($found));
}

function nw_json_response(string $text): array
{
    $clean = trim(preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $text) ?: $text);
    $value = json_decode($clean, true);
    if (is_array($value)) {
        return $value;
    }
    $start = strpos($clean, '{');
    $end = strrpos($clean, '}');
    if ($start !== false && $end !== false && $end > $start) {
        $value = json_decode(substr($clean, $start, $end - $start + 1), true);
        if (is_array($value)) {
            return $value;
        }
    }
    nw_fail('DeepSeek did not return valid JSON: ' . json_last_error_msg(), 12);
}

function nw_request(string $key, string $model, string $tier, string $effort, string $system, string $user, int $maxTokens): array
{
    $payload = json_encode([
        'model' => $model,
        // Neuralwatt uses OpenAI's user field as an explicit session/cache-affinity key.
        'user' => 'crimewiki-local-rewrite',
        'service_tier' => $tier,
        'messages' => [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => $user],
        ],
        'temperature' => 0.35,
        'reasoning_effort' => $effort,
        'max_tokens' => $maxTokens,
        'response_format' => ['type' => 'json_object'],
        'stream' => true,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    if ($payload === false) {
        nw_fail('could not encode the Neuralwatt request');
    }

    $buffer = '';
    $content = '';
    $usage = [];
    $tierSeen = null;
    $apiError = '';
    $handle = curl_init('https://api.neuralwatt.com/v1/chat/completions');
    curl_setopt_array($handle, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $key, 'Content-Type: application/json', 'Accept: text/event-stream'],
        CURLOPT_CONNECTTIMEOUT => 30,
        CURLOPT_TIMEOUT => 3600,
        CURLOPT_WRITEFUNCTION => function ($curl, string $data) use (&$buffer, &$content, &$usage, &$tierSeen, &$apiError): int {
            $buffer .= str_replace("\r\n", "\n", $data);
            while (($end = strpos($buffer, "\n\n")) !== false) {
                $event = substr($buffer, 0, $end);
                $buffer = substr($buffer, $end + 2);
                foreach (explode("\n", $event) as $line) {
                    if (!str_starts_with($line, 'data:')) {
                        continue;
                    }
                    $raw = trim(substr($line, 5));
                    if ($raw === '' || $raw === '[DONE]') {
                        continue;
                    }
                    $chunk = json_decode($raw, true);
                    if (!is_array($chunk)) {
                        continue;
                    }
                    if (isset($chunk['error'])) {
                        $apiError = (string) ($chunk['error']['message'] ?? $chunk['message'] ?? 'API error');
                    }
                    if (isset($chunk['service_tier'])) {
                        $tierSeen = (string) $chunk['service_tier'];
                    }
                    if (isset($chunk['usage']) && is_array($chunk['usage'])) {
                        $usage = $chunk['usage'];
                    }
                    $delta = $chunk['choices'][0]['delta']['content'] ?? '';
                    if (is_string($delta)) {
                        $content .= $delta;
                    }
                }
            }
            return strlen($data);
        },
    ]);
    curl_exec($handle);
    $curlError = curl_error($handle);
    $httpCode = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if ($curlError !== '') {
        nw_fail('Neuralwatt transport error: ' . $curlError, 11);
    }
    if ($httpCode >= 400) {
        nw_fail('Neuralwatt HTTP ' . $httpCode . ($apiError ? ': ' . $apiError : ''), 11);
    }
    if ($apiError !== '') {
        nw_fail('Neuralwatt API error: ' . $apiError, 11);
    }
    if (trim($content) === '') {
        nw_fail('Neuralwatt returned no content', 11);
    }
    return ['content' => $content, 'usage' => $usage, 'tier' => $tierSeen, 'http_code' => $httpCode];
}

$count = (int) nw_arg($argv, '--count', '10');
$model = nw_arg($argv, '--model', getenv('NEURALWATT_MODEL') ?: 'deepseek-v4-flash');
$tier = nw_arg($argv, '--service-tier', getenv('NEURALWATT_SERVICE_TIER') ?: 'default');
$effort = nw_arg($argv, '--reasoning-effort', getenv('NEURALWATT_REASONING_EFFORT') ?: 'high');
$maxTokens = (int) nw_arg($argv, '--max-tokens', getenv('NEURALWATT_MAX_TOKENS') ?: '65536');
$postsPerRequest = (int) nw_arg($argv, '--posts-per-request', getenv('NEURALWATT_POSTS_PER_REQUEST') ?: '20');
$apply = in_array('--apply', $argv, true);
$apiKey = trim((string) getenv('NEURALWATT_API_KEY'));
if ($apiKey === '') {
    nw_fail('NEURALWATT_API_KEY is not set in this terminal', 10);
}
if ($count < 1 || $count > 50) {
    nw_fail('--count must be between 1 and 50');
}
if (!in_array($effort, ['high', 'max'], true)) {
    nw_fail('--reasoning-effort must be high or max');
}
if ($maxTokens < 1000 || $maxTokens > 65536) {
    nw_fail('--max-tokens must be between 1000 and 65536');
}
if ($postsPerRequest < 1 || $postsPerRequest > 20) {
    nw_fail('--posts-per-request must be between 1 and 20');
}

$projectRoot = dirname(__DIR__);
$jobsRoot = $projectRoot . '/tmp/rewrite-jobs';
if (!is_dir($jobsRoot) && !mkdir($jobsRoot, 0775, true) && !is_dir($jobsRoot)) {
    nw_fail('could not create tmp/rewrite-jobs');
}
$claimsRoot = $jobsRoot . '/claims';
if (!is_dir($claimsRoot) && !mkdir($claimsRoot, 0775, true) && !is_dir($claimsRoot)) {
    nw_fail('could not create tmp/rewrite-jobs/claims');
}

/*
 * Several host workers may run this script at once.  The database has no
 * rewrite-state column, so use short-lived, atomic files under the project
 * tmp directory to reserve a post before its research/API work starts.  A
 * stale reservation is safe to reclaim because the final DB write also
 * verifies the original-content hash.
 */
$claimPaths = [];
register_shutdown_function(static function () use (&$claimPaths): void {
    foreach ($claimPaths as $path) {
        @unlink($path);
    }
});

$db = make_db_connection();
$candidateLimit = max($count * 20, 100);
$result = $db->query('SELECT id,title,wikilink,categoryname,content FROM posts WHERE id > 2 AND wikilink IS NOT NULL AND wikilink <> "" ORDER BY id LIMIT ' . $candidateLimit);
if (!$result) {
    nw_fail('could not select posts: ' . $db->error);
}
$posts = [];
while ($row = $result->fetch_assoc()) {
    if (count($posts) >= $count) {
        break;
    }
    $locator = nw_normalize_url((string) $row['wikilink']);
    if ($locator === '') {
        continue;
    }

    $postId = (int) $row['id'];
    $claimPath = $claimsRoot . '/post-' . $postId . '.claim';
    $claimLock = @fopen($claimPath . '.lock', 'c');
    if ($claimLock === false || !flock($claimLock, LOCK_EX)) {
        if (is_resource($claimLock)) {
            fclose($claimLock);
        }
        continue;
    }
    $claim = false;
    try {
        if (is_file($claimPath) && (filemtime($claimPath) ?: 0) < time() - 1800) {
            @unlink($claimPath);
        }
        $claim = @fopen($claimPath, 'x');
    } finally {
        flock($claimLock, LOCK_UN);
        fclose($claimLock);
    }
    if ($claim === false) {
        continue;
    }
    fwrite($claim, json_encode(['post_id' => $postId, 'pid' => getmypid(), 'claimed_at_utc' => gmdate('c')]) . "\n");
    fclose($claim);
    $claimPaths[] = $claimPath;
    $posts[] = [
        'post_id' => $postId,
        'title' => (string) $row['title'],
        'category' => (string) ($row['categoryname'] ?? ''),
        'research_locator' => $locator,
        'input_sha256' => hash('sha256', (string) $row['content']),
    ];
}
if ($posts === []) {
    nw_fail('no unclaimed locator-backed posts remain');
}

$batchId = 'neuralwatt-' . gmdate('Ymd-His') . '-' . bin2hex(random_bytes(3));
$batchDirectory = $jobsRoot . '/' . $batchId;
if (!mkdir($batchDirectory . '/results', 0775, true) || !mkdir($batchDirectory . '/logs', 0775, true)) {
    nw_fail('could not create batch directories');
}
$manifest = fopen($batchDirectory . '/manifest.jsonl', 'wb');
if ($manifest === false) {
    nw_fail('could not create manifest');
}
foreach ($posts as $post) {
    fwrite($manifest, json_encode($post, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
}
fclose($manifest);

$firstWave = [];
foreach ($posts as $post) {
    $id = (string) $post['post_id'];
    $firstWave[$id . ':wiki'] = $post['research_locator'];
    $firstWave[$id . ':search'] = 'https://html.duckduckgo.com/html/?q=' . rawurlencode('"' . $post['title'] . '" crime court history official report');
}
    $firstResults = nw_fetch_many($firstWave, 16);
$research = [];
$secondWave = [];
foreach ($posts as $post) {
    $id = (string) $post['post_id'];
    $wikiHtml = (string) ($firstResults[$id . ':wiki']['body'] ?? '');
    $searchHtml = (string) ($firstResults[$id . ':search']['body'] ?? '');
    $sourceLinks = [];
    foreach (array_merge(nw_links($wikiHtml), nw_search_links($searchHtml)) as $url) {
        if (!in_array($url, $sourceLinks, true)) {
            $sourceLinks[] = $url;
        }
        if (count($sourceLinks) >= 4) {
            break;
        }
    }
    foreach ($sourceLinks as $index => $url) {
        $secondWave[$id . ':source' . $index] = $url;
    }
    $research[$id] = ['post' => $post, 'wiki' => nw_text($wikiHtml, 22000), 'search' => nw_text($searchHtml, 5000), 'sources' => $sourceLinks];
}
$secondResults = nw_fetch_many($secondWave, 16);
$packets = [];
foreach ($research as $id => $item) {
    $packet = "POST {$id}\nTITLE: {$item['post']['title']}\nCATEGORY: {$item['post']['category']}\nRESEARCH LOCATOR: {$item['post']['research_locator']}\n\n";
    $packet .= "WIKIPEDIA RESEARCH EXCERPT (do not cite or link it):\n" . ($item['wiki'] ?: '[unavailable]') . "\n\n";
    $packet .= "SEARCH LEADS:\n" . ($item['search'] ?: '[unavailable]') . "\n\n";
    foreach ($item['sources'] as $index => $url) {
        $sourceText = nw_text((string) ($secondResults[$id . ':source' . $index]['body'] ?? ''), 6500);
        $packet .= 'ADDITIONAL SOURCE ' . ($index + 1) . "\nURL: {$url}\nEXCERPT: " . ($sourceText ?: '[unavailable]') . "\n\n";
    }
    $packets[$id] = $packet;
}

$contract = file_get_contents($projectRoot . '/include/qwen_contract.txt') ?: '';
$styleguide = file_get_contents($projectRoot . '/include/qwen_styleguide.css') ?: '';
$expected = [];
foreach ($posts as $post) {
    $expected[(int) $post['post_id']] = $post;
}
$returnedCount = 0;
$validCount = 0;
$applied = 0;
$applyFailures = 0;
$subSummaries = [];
$subBatches = array_chunk($posts, $postsPerRequest);
foreach ($subBatches as $subIndex => $subPosts) {
    $subCount = count($subPosts);
    $system = $contract . "\n\n=== SITE STYLESHEET ===\n" . $styleguide
        . "\n\n=== BATCH RULES ===\nYou are writing independent CrimeWiki entries in each request. Never merge posts or reuse facts between them. The supplied packets are fresh research leads; resolve conflicts cautiously and do not invent unsupported details. Use {$effort} reasoning quality, but keep private deliberation concise and task-focused; do not spend the completion budget on repeated summaries or exhaustive internal enumeration. After checking conflicts, move promptly to the finished entries and reserve ample output room for the final JSON. Return one JSON object with exactly one key, posts. posts must be an array of objects with exactly post_id (integer) and content (string). Each content string must contain only the five XML blocks from the contract, with no wrapper element or markdown. Use exactly 5 intro-data rows. List every real, relevant, actually used source that helps readers verify the subject; there is no fixed minimum or maximum and no filler links. Wikipedia may be listed only in <sources> as a clearly labelled fallback when no better usable source is available, never as an inline content link. Every cited <sources> href must begin with https://. Use as much substantive content as each subject needs; do not force a paragraph count or artificial length. Keep the five blocks in the required order. Before returning JSON, check every entry against the exact row count, source validity, and the complete five-block order.\n";
    $subPackets = [];
    foreach ($subPosts as $post) {
        $subPackets[] = $packets[(string) $post['post_id']];
    }
    $user = "Rewrite all of these posts independently from scratch. This request contains {$subCount} posts. Use the research packet for each topic and cite real sources you actually used. Use the Wikipedia locator as a displayed source only when no better usable source is available, and only in the sources block. Perform careful high-effort verification, but do not overthink or repeat research. Return complete entries for every listed post before the output budget ends.\n\n" . implode("\n\n==============================\n\n", $subPackets);
    $started = microtime(true);
    $response = nw_request($apiKey, $model, $tier, $effort, $system, $user, $maxTokens);
    $decoded = nw_json_response((string) $response['content']);
    $subReturned = $decoded['posts'] ?? null;
    if (!is_array($subReturned)) {
        nw_fail('DeepSeek JSON did not contain a posts array for sub-batch ' . ($subIndex + 1), 12);
    }
    $returnedCount += count($subReturned);
    $subValid = 0;
    $subApplied = 0;
    $subSummaries[] = [
        'sub_batch' => $subIndex + 1,
        'post_count' => $subCount,
        'returned_count' => count($subReturned),
        'service_tier_seen' => $response['tier'],
        'http_code' => $response['http_code'],
        'elapsed_seconds' => round(microtime(true) - $started, 2),
        'usage' => $response['usage'],
    ];
    foreach ($subReturned as $entry) {
        if (!is_array($entry) || !isset($entry['post_id']) || !is_string($entry['content'] ?? null)) {
            continue;
        }
        $postId = (int) $entry['post_id'];
        if (!isset($expected[$postId])) {
            continue;
        }
        $content = trim((string) $entry['content']);
        $content = preg_replace('/^```(?:xml|html)?\s*/i', '', $content) ?: $content;
        $content = preg_replace('/\s*```$/', '', $content) ?: $content;
        $content = nw_force_https_hrefs($content);
        try {
            validate_rewrite_content($content);
            $validCount++;
            $subValid++;
            file_put_contents($batchDirectory . '/results/post-' . $postId . '.xml', $content . "\n");
            if ($apply) {
                try {
                    update_local_post_or_throw($postId, (string) $expected[$postId]['input_sha256'], $content);
                    $applied++;
                    $subApplied++;
                } catch (Throwable $error) {
                    $applyFailures++;
                    fwrite(STDERR, "FAILED post {$postId}: {$error->getMessage()}\n");
                }
            }
        } catch (Throwable $error) {
            file_put_contents($batchDirectory . '/results/post-' . $postId . '.error.txt', $error->getMessage() . "\n");
        }
    }
    $subSummaries[$subIndex]['valid_count'] = $subValid;
    $subSummaries[$subIndex]['applied_count'] = $subApplied;
    file_put_contents($batchDirectory . '/logs/api-summary.json', json_encode([
        'model' => $model,
        'service_tier_requested' => $tier,
        'reasoning_effort' => $effort,
        'post_count' => count($posts),
        'posts_per_request' => $postsPerRequest,
        'sub_requests_completed' => count($subSummaries),
        'sub_requests' => $subSummaries,
        'research_fetches' => count($firstWave) + count($secondWave),
        'valid_count_so_far' => $validCount,
        'applied_count_so_far' => $applied,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
}
file_put_contents($batchDirectory . '/batch.json', json_encode([
    'batch_id' => $batchId,
    'workflow' => 'neuralwatt-deepseek-v4-flash-standard-' . $effort . '-batch-of-' . count($posts) . '-posts-per-request-' . $postsPerRequest,
    'created_at_utc' => gmdate('c'),
    'requested_count' => count($posts),
    'posts_per_request' => $postsPerRequest,
    'returned_count' => $returnedCount,
    'valid_count' => $validCount,
    'applied_count' => $applied,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
echo 'BATCH_DIR=' . ltrim(str_replace($projectRoot . '/', '', $batchDirectory), '/') . "\n";
echo 'BATCH_RESULT returned=' . $returnedCount . ' valid=' . $validCount . ' applied=' . $applied . ' apply_failures=' . $applyFailures . ' requested=' . count($posts) . ' posts_per_request=' . $postsPerRequest . ' sub_requests=' . count($subBatches) . "\n";
exit($validCount === count($posts) && $applyFailures === 0 ? 0 : 12);

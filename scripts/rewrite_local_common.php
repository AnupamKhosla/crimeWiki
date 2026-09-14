<?php

declare(strict_types=1);

require_once __DIR__ . '/../include/config.php';
require_once __DIR__ . '/../include/functions.php';

function rewrite_fail(string $message): never
{
    throw new RuntimeException("FAILED: {$message}");
}

function one_wrapper(string $content, string $tag): void
{
    $count = preg_match_all('/<' . preg_quote($tag, '/') . '\\b/i', $content);
    if ($count !== 1) {
        rewrite_fail("expected exactly one <{$tag}> block; found {$count}");
    }
}

function element_by_tag(DOMDocument $document, string $tag): DOMElement
{
    $elements = $document->getElementsByTagName($tag);
    if ($elements->length !== 1 || !($elements->item(0) instanceof DOMElement)) {
        rewrite_fail("expected exactly one <{$tag}> element");
    }

    return $elements->item(0);
}

function is_wikipedia_url(string $url): bool
{
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    return $host === 'wikipedia.org' || str_ends_with($host, '.wikipedia.org');
}

function is_wikimedia_upload_url(string $url): bool
{
    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
    return $host === 'upload.wikimedia.org' || str_ends_with($host, '.upload.wikimedia.org');
}

function node_is_within(DOMNode $node, DOMElement $ancestor): bool
{
    for ($current = $node; $current !== null; $current = $current->parentNode) {
        if ($current === $ancestor) {
            return true;
        }
    }

    return false;
}

function validate_rewrite_content(string $content): void
{
    if (strlen($content) > 499999) {
        rewrite_fail('content exceeds the database column limit');
    }

    foreach (['intro-data', 'details', 'sources', 'related', 'content'] as $tag) {
        one_wrapper($content, $tag);
    }

    if (preg_match('/<\s*(script|style|iframe|form|svg|img)\b|\bon[a-z]+\s*=|javascript:/i', $content)) {
        rewrite_fail('content contains disallowed markup or event attributes');
    }

    $positions = [];
    foreach (['intro-data', 'details', 'sources', 'related', 'content'] as $tag) {
        $positions[$tag] = stripos($content, '<' . $tag);
    }
    if ($positions['intro-data'] > $positions['details']
        || $positions['details'] > $positions['sources']
        || $positions['sources'] > $positions['related']
        || $positions['related'] > $positions['content']) {
        rewrite_fail('the five XML blocks are out of order');
    }

    $document = new DOMDocument('1.0', 'UTF-8');
    $document->preserveWhiteSpace = true;
    $loaded = @$document->loadHTML(
        '<!DOCTYPE html><html><head><meta charset="UTF-8"></head><body>' . $content . '</body></html>',
        LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
    );
    if (!$loaded) {
        rewrite_fail('content is not parseable HTML/XML');
    }

    $intro = element_by_tag($document, 'intro-data');
    $introRows = $intro->getElementsByTagName('tr');
    if ($introRows->length !== 5) {
        rewrite_fail("intro-data must contain exactly 5 rows; found {$introRows->length}");
    }

    foreach ($introRows as $row) {
        if ($row->getElementsByTagName('th')->length !== 1
            || $row->getElementsByTagName('td')->length !== 1) {
            rewrite_fail('every intro-data row must contain one <th> and one <td>');
        }
    }

    $details = element_by_tag($document, 'details');
    $detailRows = $details->getElementsByTagName('tr');
    if ($detailRows->length < 6 || $detailRows->length > 12) {
        rewrite_fail("details must contain 6–12 rows; found {$detailRows->length}");
    }

    $sources = element_by_tag($document, 'sources');
    $sourceLinks = $sources->getElementsByTagName('a');
    $seenSources = [];
    foreach ($sourceLinks as $link) {
        $href = trim($link->getAttribute('href'));
        if (!preg_match('#^https://[^\s"<>]+$#i', $href)) {
            rewrite_fail("source URL is not a full HTTPS URL: {$href}");
        }
        $normalized = strtolower($href);
        if (isset($seenSources[$normalized])) {
            rewrite_fail('sources contain a duplicate URL');
        }
        $seenSources[$normalized] = true;
    }

    foreach ($document->getElementsByTagName('a') as $link) {
        $href = trim($link->getAttribute('href'));
        if (is_wikimedia_upload_url($href)) {
            rewrite_fail('content contains a Wikimedia upload URL');
        }
        if (is_wikipedia_url($href) && !node_is_within($link, $sources)) {
            rewrite_fail('Wikipedia URLs are allowed only in the sources block');
        }
    }

    $related = element_by_tag($document, 'related');
    if ($related->getElementsByTagName('a')->length !== 0 || trim($related->textContent) !== '') {
        rewrite_fail('related must be empty until internal links are reviewed');
    }

    $article = element_by_tag($document, 'content');
    $headings = $article->getElementsByTagName('h2');
    if ($headings->length < 2 || trim($headings->item(0)->textContent) !== 'Introduction') {
        rewrite_fail('content must begin with an Introduction heading and have multiple sections');
    }
    if ($article->getElementsByTagName('hr')->length < 1) {
        rewrite_fail('content must separate sections with at least one <hr>');
    }
    if (strlen(trim($article->textContent)) < 1200) {
        rewrite_fail('content is too short for a researched article');
    }
}

function update_local_post(int $postId, string $expectedOriginalHash, string $content): void
{
    try {
        update_local_post_or_throw($postId, $expectedOriginalHash, $content);
    } catch (Throwable $error) {
        fwrite(STDERR, $error->getMessage() . "\n");
        exit(1);
    }
}

function update_local_post_or_throw(int $postId, string $expectedOriginalHash, string $content): void
{
    validate_rewrite_content($content);

    $connection = make_db_connection();
    if (!$connection->begin_transaction()) {
        rewrite_fail('could not start the local transaction');
    }

    $query = $connection->prepare(
        'SELECT title, content, wikilink FROM posts WHERE id = ? AND id NOT IN (1, 2) LIMIT 1 FOR UPDATE'
    );
    if (!$query) {
        $connection->rollback();
        rewrite_fail('could not prepare the local row lookup: ' . $connection->error);
    }

    $query->bind_param('i', $postId);
    if (!$query->execute()) {
        $error = $query->error;
        $query->close();
        $connection->rollback();
        rewrite_fail('could not read the local row: ' . $error);
    }

    $row = $query->get_result()->fetch_assoc();
    $query->close();
    if (!$row) {
        $connection->rollback();
        rewrite_fail("post {$postId} does not exist or is protected");
    }

    $actualOriginalHash = hash('sha256', (string) $row['content']);
    if (!hash_equals($expectedOriginalHash, $actualOriginalHash)) {
        $connection->rollback();
        rewrite_fail("post {$postId} changed since selection; expected {$expectedOriginalHash}, found {$actualOriginalHash}");
    }

    $newHash = hash('sha256', $content);
    $update = $connection->prepare(
        'UPDATE posts SET content = ?, wikilink = NULL, cleansed = 1
         WHERE id = ? AND SHA2(content, 256) = ?'
    );
    if (!$update) {
        $connection->rollback();
        rewrite_fail('could not prepare the local update: ' . $connection->error);
    }

    $update->bind_param('sis', $content, $postId, $expectedOriginalHash);
    if (!$update->execute() || $update->affected_rows !== 1) {
        $error = $update->error ?: 'unexpected affected-row count';
        $update->close();
        $connection->rollback();
        rewrite_fail("local update failed: {$error}");
    }

    $update->close();
    if (!$connection->commit()) {
        rewrite_fail('local transaction commit failed');
    }

    echo "UPDATED post {$postId}: {$row['title']}\n";
    echo "  original_sha256={$actualOriginalHash}\n";
    echo "  rewritten_sha256={$newHash}\n";
    echo "  wikilink=NULL\n";
    echo "  cleansed=1\n";
}

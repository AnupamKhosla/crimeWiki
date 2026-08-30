<?php

require_once dirname(__DIR__) . '/include/config.php';
require_once dirname(__DIR__) . '/include/functions.php';

$conn = make_db_connection();

function migration_fail(mysqli $conn, string $message): void {
    fwrite(STDERR, $message . ': ' . $conn->error . PHP_EOL);
    exit(1);
}

function index_exists(mysqli $conn, string $indexName): bool {
    $stmt = $conn->prepare("SHOW INDEX FROM `posts` WHERE Key_name = ?");
    if($stmt === false) {
        migration_fail($conn, 'Could not inspect posts indexes');
    }
    $stmt->bind_param('s', $indexName);
    if(!$stmt->execute()) {
        migration_fail($conn, 'Could not inspect posts indexes');
    }
    $result = $stmt->get_result();
    return $result !== false && $result->num_rows > 0;
}

// These indexes support the existing filters and sort choices. No content
// index is added: leading-wildcard searches and CHAR_LENGTH(content) cannot
// use a normal B-tree index without changing the search behavior.
$indexes = [
    'idx_posts_title_repeat' => '(`title`, `titlerepeat`)',
    'idx_posts_datetime_id' => '(`datetime`, `id`)',
    'idx_posts_category_datetime_id' => '(`categoryname`, `datetime`, `id`)',
    'idx_posts_category_title_id' => '(`categoryname`, `title`, `id`)',
    'idx_posts_country_title_id' => '(`country`, `title`, `id`)',
    'idx_posts_category_country_title_id' => '(`categoryname`, `country`, `title`, `id`)'
];

foreach($indexes as $name => $columns) {
    if(index_exists($conn, $name)) {
        echo "Already present: {$name}" . PHP_EOL;
        continue;
    }

    if(!$conn->query("ALTER TABLE `posts` ADD INDEX `{$name}` {$columns}")) {
        migration_fail($conn, "Could not add {$name}");
    }
    echo "Added: {$name}" . PHP_EOL;
}

echo "Search index migration complete" . PHP_EOL;

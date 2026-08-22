<?php

require_once dirname(__DIR__) . '/include/config.php';
require_once dirname(__DIR__) . '/include/functions.php';

$conn = make_db_connection();

function migration_fail(mysqli $conn, string $message): void {
    fwrite(STDERR, $message . ': ' . $conn->error . PHP_EOL);
    exit(1);
}

$columnResult = $conn->query("SHOW COLUMNS FROM `posts` LIKE 'homepage_rank'");
if($columnResult === false) {
    migration_fail($conn, 'Could not inspect posts.homepage_rank');
}

if($columnResult->num_rows === 0) {
    if(!$conn->query("ALTER TABLE `posts` ADD COLUMN `homepage_rank` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `categoryname`")) {
        migration_fail($conn, 'Could not add posts.homepage_rank');
    }
    echo "Added posts.homepage_rank" . PHP_EOL;
}

$indexResult = $conn->query("SHOW INDEX FROM `posts` WHERE Key_name='idx_posts_homepage_rank'");
if($indexResult === false) {
    migration_fail($conn, 'Could not inspect homepage rank index');
}

if($indexResult->num_rows === 0) {
    if(!$conn->query("ALTER TABLE `posts` ADD INDEX `idx_posts_homepage_rank` (`categoryname`, `homepage_rank`, `id`)")) {
        migration_fail($conn, 'Could not add homepage rank index');
    }
    echo "Added idx_posts_homepage_rank" . PHP_EOL;
}

if(!$conn->query("UPDATE `posts` SET `homepage_rank`=CRC32(CONCAT('crimewiki-homepage-v1:', `id`)) WHERE `homepage_rank`=0")) {
    migration_fail($conn, 'Could not populate homepage ranks');
}

echo "Homepage rank migration complete; rows updated: " . $conn->affected_rows . PHP_EOL;

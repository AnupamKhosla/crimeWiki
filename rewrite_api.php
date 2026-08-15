<?php
require_once("include/sessions.php");
require_once("include/config.php");
require_once("include/functions.php");
require_once("include/check_login.php");

set_time_limit(0);

if (empty($_SESSION["qwen_api_key"])) {
    header("Content-Type: text/event-stream");
    echo "data: " . json_encode(["error" => "No API key set"]) . "\n\n";
    echo "data: [DONE]\n\n";
    exit;
}

$post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;
if (!$post_id || $post_id <= 2) {
    header("Content-Type: text/event-stream");
    echo "data: " . json_encode(["error" => "Invalid post_id (system rows are protected)"]) . "\n\n";
    echo "data: [DONE]\n\n";
    exit;
}

$conn = make_db_connection();

$stmt = $conn->prepare("SELECT id, title, wikilink FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows) {
    header("Content-Type: text/event-stream");
    echo "data: " . json_encode(["error" => "Post not found"]) . "\n\n";
    echo "data: [DONE]\n\n";
    exit;
}

$post = $result->fetch_assoc();

$wikilink = trim((string)($post["wikilink"] ?? ""));
if ($wikilink === "") {
    header("Content-Type: text/event-stream");
    echo "data: " . json_encode(["error" => "Post \"" . $post["title"] . "\" has no Wikipedia link (wikilink). Add one first so the AI can research it."]) . "\n\n";
    echo "data: [DONE]\n\n";
    exit;
}
$research_target = "https://" . $wikilink;

$contract = file_get_contents(__DIR__ . "/include/qwen_contract.txt");
$styleguide = file_get_contents(__DIR__ . "/include/qwen_styleguide.css");

$system_prompt = $contract
    . "\n\n=== SITE STYLESHEET (content-relevant CSS — reuse these classes for rich formatting) ===\n"
    . $styleguide;

$user_prompt = "Research this topic thoroughly: " . $research_target . "\n\n"
    . "0. Use the web_search tool to research the topic, its Wikipedia article, and the sources it cites before writing. "
    . "1. Read the topic's Wikipedia article AND the sources it cites (read as many cited sources as you can). "
    . "2. Also consult recent, reputable reporting and records (including but not limited to BBC, CNN, The New York Times, court records, books, documentaries, official reports). "
    . "3. Then write a completely original encyclopedia entry per the rules — your own structure, words, emphasis and conclusions. "
    . "If you can access the page's raw HTML, inspect and reuse its CSS classes. "
    . "Output ONLY the five XML blocks. No markdown, no code fences, no commentary.";

$api_url = "https://token-plan.ap-southeast-1.maas.aliyuncs.com/compatible-mode/v1/responses";

$request_body = json_encode([
    "model" => "deepseek-v4-flash-0731",
    "instructions" => $system_prompt,
    "input" => [
        ["role" => "user", "content" => [["type" => "input_text", "text" => $user_prompt]]]
    ],
    "tools" => [
        ["type" => "web_search"]
    ],
    "temperature" => 0.8,
    "max_output_tokens" => 12000,
    "stream" => true
]);

if (function_exists("apache_setenv")) { apache_setenv("no-gzip", "1"); }
@ini_set("zlib.output_compression", "0");
@ini_set("output_buffering", "0");
while (ob_get_level() > 0) { ob_end_flush(); }
ob_implicit_flush(true);

header("Content-Type: text/event-stream");
header("Cache-Control: no-cache");
header("Connection: keep-alive");
header("X-Accel-Buffering: no");

echo "data: " . json_encode(["status" => "researching"]) . "\n\n";
flush();

$max_duration = 1800;
$heartbeat_interval = 15;
$started = time();
$last_activity = $started;

$ch = curl_init($api_url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . $_SESSION["qwen_api_key"]
    ],
    CURLOPT_POSTFIELDS => $request_body,
    CURLOPT_TIMEOUT => $max_duration,
    CURLOPT_WRITEFUNCTION => function($ch, $data) use (&$last_activity) {
        $last_activity = time();
        $lines = explode("\n", $data);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, "data:") !== 0) {
                continue;
            }
            $payload = trim(substr($line, 5));
            if ($payload === "") {
                continue;
            }
            if ($payload === "[DONE]") {
                echo "data: [DONE]\n\n";
                continue;
            }
            $chunk = json_decode($payload, true);
            if (!is_array($chunk)) {
                continue;
            }
            if (($chunk["type"] ?? "") === "error" || isset($chunk["error"])) {
                $msg = $chunk["error"]["message"] ?? $chunk["message"] ?? "API error";
                echo "data: " . json_encode(["error" => $msg]) . "\n\n";
                continue;
            }
            if (($chunk["type"] ?? "") === "response.output_text.delta") {
                $token = $chunk["delta"] ?? "";
                if ($token !== "") {
                    echo "data: " . json_encode(["token" => $token]) . "\n\n";
                }
            }
        }
        if (ob_get_level()) ob_flush();
        flush();
        return strlen($data);
    }
]);

$mh = curl_multi_init();
curl_multi_add_handle($mh, $ch);
$running = null;

do {
    $status = curl_multi_exec($mh, $running);

    if (time() - $last_activity >= $heartbeat_interval) {
        echo ": keep-alive " . str_repeat(" ", 8192) . "\n\n";
        if (ob_get_level()) ob_flush();
        flush();
        $last_activity = time();
    }

    if (time() - $started >= $max_duration) {
        echo "data: " . json_encode(["error" => "Timed out after " . (int)($max_duration / 60) . " minutes waiting for the model"]) . "\n\n";
        echo "data: [DONE]\n\n";
        if (ob_get_level()) ob_flush();
        flush();
        break;
    }

    if ($running) {
        curl_multi_select($mh, 1.0);
    }
} while ($running && $status === CURLM_OK);

curl_multi_remove_handle($mh, $ch);
curl_multi_close($mh);

$curl_error = curl_error($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curl_error) {
    echo "data: " . json_encode(["error" => $curl_error]) . "\n\n";
}
if (ob_get_level()) ob_flush();
flush();

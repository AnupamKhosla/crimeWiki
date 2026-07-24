<?php
require_once("sessions.php");
require_once("config.php");
require_once("functions.php");
require_once("check_login.php");

header("Content-Type: application/json");

if (empty($_SESSION["qwen_api_key"])) {
    echo json_encode(["error" => "No API key set"]);
    exit;
}

$post_id = isset($_POST["post_id"]) ? (int)$_POST["post_id"] : 0;
if (!$post_id) {
    echo json_encode(["error" => "No post_id"]);
    exit;
}

$conn = make_db_connection();

$stmt = $conn->prepare("SELECT id, title, content FROM posts WHERE id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows) {
    echo json_encode(["error" => "Post not found"]);
    exit;
}

$post = $result->fetch_assoc();

$titles_stmt = $conn->query("SELECT id, title FROM posts ORDER BY id");
$all_titles = [];
while ($row = $titles_stmt->fetch_assoc()) {
    if ((int)$row["id"] !== $post_id) {
        $all_titles[] = $row["title"];
    }
}
$titles_list = implode("\n", $all_titles);

$system_prompt = <<<PROMPT
You are a crime journalism writer for CrimeWiki. You must COMPLETELY REWRITE the given crime article from scratch in a narrative true-crime journalism voice. This is NOT a paraphrase — it must be structurally different, use different vocabulary, different emphasis, and different source citations.

RULES:
1. Use web search to research fresh facts, court records, newspaper archives, books, and documentaries about this case.
2. The output MUST preserve this EXACT XML tag structure (these are custom tags, not HTML):

<intro-data>
(5 rows of key facts as plain text lines separated by <br> tags. Format: "Label: Value<br>")
</intro-data>

<details>
<tbody>
<tr><th>Label</th><td>Value</td></tr>
(multiple rows for Date, Location, Type, Perpetrator, Victims, Outcome, etc.)
</tbody>
</details>

<sources>
<ul class="list">
<li><a href="URL">Source title — publication, year</a></li>
(3-5 real, verifiable sources from your web research — court records, newspapers, books, films. NOT Wikipedia.)
</ul>
</sources>

<related>
<ol class="list">
<li><a href="/post/TITLE_HERE">TITLE_HERE</a></li>
(2-4 related posts. ONLY use titles from this list:
$titles_list
)
</ol>
</related>

<content>
<h2>Section Title</h2>
<p>Narrative prose...</p>
<hr>
<h2>Next Section</h2>
<p>More prose...</p>
(4-7 sections separated by <hr>. Write in vivid narrative crime-journalism style. No Wikipedia tone.)
</content>

3. Do NOT include any Wikipedia text, structure, or citations.
4. Sources must be real and verifiable (books, court documents, newspaper archives, documentaries).
5. Related links must ONLY reference titles from the provided list above.
6. Return ONLY the XML content. No markdown, no code fences, no explanation before or after.
PROMPT;

$user_prompt = "Rewrite this crime article completely from scratch. Research it fresh using web search. Article title: \"" . $post["title"] . "\"\n\nCurrent content (use only for factual reference, do NOT copy structure or text):\n\n" . $post["content"];

$api_url = "https://dashscope-intl.aliyuncs.com/compatible-mode/v1/chat/completions";

$request_body = json_encode([
    "model" => "qwen-max-latest",
    "messages" => [
        ["role" => "system", "content" => $system_prompt],
        ["role" => "user", "content" => $user_prompt]
    ],
    "enable_search" => true,
    "temperature" => 0.8,
    "max_tokens" => 8000
]);

$ch = curl_init($api_url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer " . $_SESSION["qwen_api_key"]
    ],
    CURLOPT_POSTFIELDS => $request_body,
    CURLOPT_TIMEOUT => 120
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

if ($response === false) {
    echo json_encode(["error" => "cURL error: " . $curl_error]);
    exit;
}

$decoded = json_decode($response, true);

if ($http_code !== 200) {
    $err_msg = isset($decoded["error"]["message"]) ? $decoded["error"]["message"] : "API returned HTTP $http_code";
    echo json_encode(["error" => $err_msg]);
    exit;
}

$ai_content = $decoded["choices"][0]["message"]["content"] ?? "";

if (empty($ai_content)) {
    echo json_encode(["error" => "Empty response from AI"]);
    exit;
}

$ai_content = preg_replace('/^```(?:xml|html)?\s*/i', '', $ai_content);
$ai_content = preg_replace('/\s*```$/', '', $ai_content);

echo json_encode([
    "success" => true,
    "post_id" => $post_id,
    "title" => $post["title"],
    "new_content" => $ai_content
]);

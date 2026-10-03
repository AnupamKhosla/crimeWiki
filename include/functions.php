<?php 

function make_db_connection(){
	static $conn = NULL;
	if($conn instanceof mysqli) {
		return $conn;
	}

	$conn = new mysqli(DB_HOST, DB_USER_NAME, DB_PASSWORD, DB_NAME);
	$conn->set_charset('utf8mb4'); // very important
	return $conn;
}

function validation_txt() {		
		$output = $_SESSION["Validation"]["txt"];		
		$_SESSION["Validation"]["txt"] = "";		
		return $output;	
}

function validation_class() {		
		$output = $_SESSION["Validation"]["class"];		
		$_SESSION["Validation"]["class"] = "";		
		return $output;	
}


function validation_status() {		
		$output = $_SESSION["Validation"]["status"];		
		$_SESSION["Validation"]["status"] = "";		
		return $output;	
}

function category_select($category = NULL) {
	$list = "";
	foreach(get_category_names() as $category_name) {
		$row_name = htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8');
		if($row_name == $category) {
			$list .= "<option selected >$row_name</option>";
		}
		else {
			$list .= "<option>$row_name</option>";
		}
	}
	return $list;
}

function category_filter_options($selected = NULL) {
	$list = "";
	foreach(get_category_names(false, true) as $category_name) {
		$row_name = htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8');
		$is_selected = ($category_name === $selected) ? " selected" : "";
		$list .= "<option value=\"$row_name\"$is_selected>$row_name</option>";
	}
	return $list;
}

function get_category_names(bool $exclude_blog = false, bool $sort = false): array {
	static $category_names = NULL;

	if($category_names === NULL) {
		$conn = make_db_connection();
		$result = $conn->query("SELECT name FROM `categories`");
		if($result === false) {
			die("Can't fetch names from categories table" . $conn->error);
		}

		$category_names = [];
		while($row = $result->fetch_assoc()) {
			$category_names[] = (string)$row['name'];
		}
	}

	$names = $category_names;
	if($exclude_blog) {
		$names = array_values(array_filter($names, static function(string $name): bool {
			return $name !== 'Blog';
		}));
	}
	if($sort) {
		sort($names, SORT_STRING);
	}

	return $names;
}

function bind_dynamic_params(mysqli_stmt $stmt, string $types, array &$params): void {
	if($types === '') {
		return;
	}

	$bind = [$types];
	foreach($params as $key => &$value) {
		$bind[] = &$value;
	}
	$stmt->bind_param(...$bind);
	unset($value);
}

function isAbsolute($url) {
  return isset(parse_url($url)['host']);
}

function default_image_path() {
	return "/Uploads/default.png";
}

function homepage_rank() {
	return random_int(1, 4294967295);
}

function posts_have_homepage_rank(mysqli $conn): bool {
	$result = $conn->query("SHOW COLUMNS FROM `posts` LIKE 'homepage_rank'");
	return $result !== false && $result->num_rows === 1;
}

function image_path($str) {
	$str = trim((string)$str);
	if($str === "") {
		return default_image_path();
	}

	if(isAbsolute($str)) {
		return $str;
	}

	$normalized = ltrim($str, "/");
	if(
		stripos($normalized, "uploads/") === 0 ||
		stripos($normalized, "Uploads/") === 0 ||
		stripos($normalized, "assets/") === 0
	) {
		$path = "/" . $normalized;
	}
	else {
		$path = "/Uploads/" . $normalized;
	}

	$diskPath = __DIR__ . "/.." . $path;
	if(file_exists($diskPath)) {
		return $path;
	}

	return default_image_path();
}

function image_fallback_attr() {
	$default = htmlspecialchars(default_image_path(), ENT_QUOTES, 'UTF-8');
	return "data-default-src=\"{$default}\" onerror=\"this.onerror=null;this.classList.add('is-default-image');this.style.background='#d8dce2';this.src='{$default}';\"";
}

function crimewiki_url(string $path = '/'): string {
	return 'https://crimewiki.site/' . ltrim($path, '/');
}

// Characters dropped from slugs, so "O'Keefe" becomes "okeefe" rather than "o-keefe".
function post_slug_apostrophes(): array {
	return array("'", "\u{2019}", "\u{2018}", "\u{02BC}", "`", "\u{00B4}");
}

// URL form of a post title: lower case, words joined by hyphens, accents kept.
// "Killing of Charlie Kirk" -> "killing-of-charlie-kirk".
function post_slug(string $title): string {
	$slug = function_exists('mb_strtolower') ? mb_strtolower($title, 'UTF-8') : strtolower($title);
	$slug = str_replace(post_slug_apostrophes(), '', $slug);
	$slug = preg_replace('/[^\p{L}\p{M}\p{N}]+/u', '-', $slug);
	return trim((string)$slug, '-');
}

// Posts have no slug column, so LIKE only narrows the candidates and the
// exact slug is compared in PHP. Returns the lowest matching id, or NULL.
function find_post_id_by_slug(mysqli $conn, string $slug, $repeat = NULL): ?int {
	if($slug === '') {
		return NULL;
	}
	$stripped = 'title';
	foreach(post_slug_apostrophes() as $mark) {
		$stripped = "REPLACE($stripped, '" . $conn->real_escape_string($mark) . "', '')";
	}
	$pattern = '%' . str_replace('-', '%', $slug) . '%';
	$stmt = $conn->prepare("SELECT id, title FROM `posts` WHERE $stripped LIKE ? AND titlerepeat <=> ? ORDER BY id");
	$stmt->bind_param("si", $pattern, $repeat);
	if(!$stmt->execute()) {
		return NULL;
	}
	$result = $stmt->get_result();
	while($row = $result->fetch_assoc()) {
		if(post_slug((string)$row['title']) === $slug) {
			return (int)$row['id'];
		}
	}
	return NULL;
}

// A real 404 for missing posts, instead of an empty page with status 200.
function render_post_not_found(): void {
	http_response_code(404);
	header('Content-Type: text/html; charset=UTF-8');
	echo '<!doctype html><html lang="en"><head><meta charset="utf-8">'
		. '<meta name="viewport" content="width=device-width, initial-scale=1">'
		. '<meta name="robots" content="noindex"><title>Page not found | CrimeWiki</title>'
		. '<style>body{font-family:Arial,sans-serif;background:#111;color:#eee;text-align:center;padding:15vh 16px}'
		. 'a{color:#E92222}</style></head><body><h1>Page not found</h1>'
		. '<p>There is no article at this address.</p>'
		. '<p><a href="/">Go to the home page</a> or <a href="/search.php">search the articles</a>.</p>'
		. '</body></html>';
	exit;
}

function post_path(string $title, $repeat = NULL): string {
	$path = '/post/' . rawurlencode(post_slug($title));
	if($repeat !== NULL && $repeat !== '') {
		$path .= '/' . rawurlencode((string)$repeat);
	}
	return $path;
}

function seo_description(string $html, string $fallback = ''): string {
	// Headings are left out, so the description starts with the first
	// paragraph rather than the word "Introduction".
	$html = preg_replace('#<h[1-6]\b[^>]*>.*?</h[1-6]>#is', ' ', $html);
	$text = html_entity_decode(strip_tags((string)$html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
	$text = preg_replace('/\s+/u', ' ', trim($text));
	if($text === '') {
		$text = $fallback;
	}
	if(function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > 155) {
		$cut = mb_substr($text, 0, 152, 'UTF-8');
		$space = mb_strrpos($cut, ' ', 0, 'UTF-8');
		if($space !== false && $space > 100) {
			$cut = mb_substr($cut, 0, $space, 'UTF-8');
		}
		$text = rtrim($cut, " ,;:-") . '...';
	}
	return $text;
}


?>

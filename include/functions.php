<?php 

function make_db_connection(){
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
	global $conn;
	$list = "";
	$result = $conn->query("SELECT name FROM `categories`");
	if($result != false) {		
		while($row = $result->fetch_assoc()) {			
			$row_name = htmlspecialchars($row['name']);
			if($row_name == $category) {
				$list .= "<option selected >$row_name</option>";
			}
			else {
				$list .= "<option>$row_name</option>";
			}				
		}				
	}
	else {
		die("Can't fetch names from categories tabele" . $conn->error);
	}
	return $list;
}

function category_filter_options($selected = NULL) {
	global $conn;
	$list = "";
	$result = $conn->query("SELECT name FROM `categories` ORDER BY name");
	if($result != false) {
		while($row = $result->fetch_assoc()) {
			$row_name = htmlspecialchars($row['name']);
			$is_selected = ($row['name'] === $selected) ? " selected" : "";
			$list .= "<option value=\"$row_name\"$is_selected>$row_name</option>";
		}
	}
	else {
		die("Can't fetch names from categories table" . $conn->error);
	}
	return $list;
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

function post_path(string $title, $repeat = NULL): string {
	$path = '/post/' . rawurlencode($title);
	if($repeat !== NULL && $repeat !== '') {
		$path .= '/' . rawurlencode((string)$repeat);
	}
	return $path;
}

function seo_description(string $html, string $fallback = ''): string {
	$text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
	$text = preg_replace('/\s+/u', ' ', trim($text));
	if($text === '') {
		$text = $fallback;
	}
	if(function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > 155) {
		$text = rtrim(mb_substr($text, 0, 152, 'UTF-8')) . '...';
	}
	return $text;
}


?>

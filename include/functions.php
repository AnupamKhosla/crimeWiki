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

function isAbsolute($url) {
  return isset(parse_url($url)['host']);
}

function default_image_path() {
	return "/Uploads/default.png";
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
	return "onerror=\"this.onerror=null;this.src='{$default}';\"";
}


?>

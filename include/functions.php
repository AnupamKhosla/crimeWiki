<?php 

function make_db_connection(){
	return (new mysqli($_SERVER['HTTP_HOST'], DB_USER_NAME, DB_PASSWORD, DB_NAME));
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

function category_select() {
	global $conn;

	$list = "";
	$result = $conn->query("SELECT name FROM `categories`");
	if($result != false) {
		while($row = $result->fetch_assoc()) {
			
			$row_name = htmlspecialchars($row['name']);
			$list .= "<option>$row_name</option>";
		}
	}
	else {
		die("Can't fetch names from categories tabele" . $conn->error);
	}
	return $list;
}


?>
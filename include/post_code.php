<?php 

$conn = make_db_connection();
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if( isset($_GET["id"]) ) {
	$post_id = $_GET["id"];		
	$stmt = $conn->prepare("SELECT datetime, title, creatorname, categoryname, content FROM `posts` WHERE id=?");
	$creator = "Anupam";
	$stmt->bind_param("i", $post_id);
	$result = $stmt->execute();	
	if($result != false) { //query was successful
		if( $row = $stmt->get_result()->fetch_assoc() ) { 
			$title = htmlspecialchars($row['title']);
			$creator = htmlspecialchars($row['creatorname']);
			$datetime = htmlspecialchars($row['datetime']);
			$category = htmlspecialchars($row['categoryname']);	
			$content = simplexml_load_string($row['content']);	
		}
	}
	else {

		die("Could not fetch results from the database" . $conn->error);
	}
}
else {
	echo "Post id must be filled";
	die();
}






?>
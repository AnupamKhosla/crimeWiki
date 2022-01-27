<?php 
if(isset($_POST["delete_id"])) {
	$_SESSION["id"] = $_POST["delete_id"];
	$id = $_POST["delete_id"];
	$conn = make_db_connection();	
	$stmt = $conn->prepare("DELETE FROM posts WHERE id=? LIMIT 1");
	$stmt->bind_param("i", $id);
	$result = $stmt->execute();
	if($result == false) { //query was successful				
		die("Could not delete post id $id from the database. " . $conn->error);
	}
	else if($conn->affected_rows != 1) {
		die("Post id $id does not exist.");		
	}
	else {
		//303 will allow bookmark and reload without resending post data
		header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
	  exit();
	}	
}


?>
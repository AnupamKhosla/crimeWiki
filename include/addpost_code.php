<?php 

$conn = make_db_connection();
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if(isset($_POST["identifier"]) && $_POST["identifier"] == "add_post_form") { //add post form has been submitted		
	$post_title = mysqli_real_escape_string($conn, trim($_POST["post_title"]));	
	if(empty($_POST["post_content"]) || empty($_POST["intro_meta"]) || empty($_POST["details_meta"]) || empty($_POST["related_meta"]) || empty($_POST["sources_meta"])) {
		$content = NULL;
	}
	else {
		$str = trim($_POST["intro_meta"]) . trim($_POST["details_meta"]) . trim($_POST["related_meta"]) . trim($_POST["sources_meta"]) . trim($_POST["post_content"]);
		$content = $str; 
		//$content cannot be escaped mysqli_escape because of xml structure
		function check_xml($content) {				
			libxml_use_internal_errors(true); // important
			$x = new DOMDocument();
			$x->loadHTML($content);			
			if(!empty($x->getElementsByTagName('intro-data')) && !empty($x->getElementsByTagName('details')) && !empty($x->getElementsByTagName('sources')) && !empty($x->getElementsByTagName('content')) && !empty($x->getElementsByTagName('related'))) {
				return true;
			}
			else {			
				return false;
			}
		}
	}		
	
	$post_category = mysqli_real_escape_string($conn, trim($_POST["category_select"]));	
	$choose_image = basename($_FILES["choose_image"]["name"]);
	$name = pathinfo($choose_image)["filename"];
	$extension = pathinfo($choose_image)["extension"];
	$target = "Uploads/" . $choose_image;
	$reps = "";
	while(file_exists($target)) { //rename image name if already exists on server
		$target = "Uploads/" . $name . $reps++ . "." . $extension;
	} 
	$choose_image = basename($target);
	if(empty($post_title) || empty($choose_image) || empty($content) || empty($post_category) ) {
		$_SESSION["Validation"]["txt"] = "All fields must be filled";	
		$_SESSION["Validation"]["class"]  = "invalid-feedback d-block"; //make eror message visible
		$_SESSION["Validation"]["status"] = "is-invalid";	
	}
	else if(strlen($post_title) > 99 || strlen($post_title) < 2) {
		$_SESSION["Validation"]["txt"] = "Post Title should be between 2 - 99 characters";
		$_SESSION["Validation"]["class"]  = "invalid-feedback d-block"; //make eror message visible
		$_SESSION["Validation"]["status"] = "is-invalid";
	}
	else if(strlen($content) > 499999 || strlen($post_title) < 2) {
		$_SESSION["Validation"]["txt"] = "Content should be between 2 - 499999 characters";
		$_SESSION["Validation"]["class"]  = "invalid-feedback d-block"; //make eror message visible
		$_SESSION["Validation"]["status"] = "is-invalid";
	}
	else if(!check_xml($content)) {
		$_SESSION["Validation"]["txt"] = "All fields must have appropriate xml tags";	
		$_SESSION["Validation"]["class"]  = "invalid-feedback d-block"; //make eror message visible
		$_SESSION["Validation"]["status"] = "is-invalid";	
	}
	else {// everything is fine; update categories now	
		$creator = "Anupam";
		$title_repeat = NULL;
		//check if title aready exists
		$stmt = $conn->prepare("SELECT * FROM `posts` WHERE title=? ");
		$stmt->bind_param("s", $post_title);
		$result = $stmt->execute();
		$get_result =  $stmt->get_result();
		$rows = $get_result->num_rows;		
		if($result && $rows != 0) {
			$title_repeat = $rows;			
		}
	//$get_result->free_result();
	$stmt = $conn->prepare("INSERT INTO `posts` (datetime, title,  titlerepeat, creatorname, categoryname, image, content) VALUES (?, ?, ?, ?, ?, ?, ?)");	
    $stmt->bind_param("sssssss", $date_time, $post_title, $title_repeat, $creator, $post_category, $choose_image, $content);
    $result = $stmt->execute();
    if($result) {       
    	$image_result = move_uploaded_file($_FILES["choose_image"]["tmp_name"], $target);  
    	$_SESSION["Validation"]["txt"] = "New Post added successfully";
    	if($image_result == false) {
    		$_SESSION["Validation"]["txt"] = "Image Upload Error!!!, but New Post added successfully";
    	}    	
			$_SESSION["Validation"]["class"]  = "valid-feedback d-block"; //make eror message visible
			$_SESSION["Validation"]["status"] = "is-valid";									
    }
    else { //some error in adding category
    	die("Failed to add new post" . $stmt->error);
        }
	}
	//303 will allow bookmark and reload without resending post data
	header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
  exit();
}







?>
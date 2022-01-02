<?php 

$conn = make_db_connection();
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if(isset($_POST["identifier"]) && $_POST["identifier"] == "dashboard_form") { //add post form has been submitted		
	$post_title = mysqli_real_escape_string($conn, trim($_POST["post_title"]));	
	
	$content =  trim($_POST["post_content"]);	
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
	else {// everything is fine; update categories now	
		$creator = "Anupam";
		$title_repeat = NULL;
		
		//$get_result->free_result();
		$stmt = $conn->prepare("INSERT INTO `posts` (datetime, title,  titlerepeat, creatorname, categoryname, image, content) VALUES (?, ?, ?, ?, ?, ?, ?)");	
    $stmt->bind_param("sssssss", $date_time, $post_title, $title_repeat, $creator, $post_category, $choose_image, $content);
    $result = $stmt->execute();
    if($result) {       
    	$image_result = move_uploaded_file($_FILES["choose_image"]["tmp_name"], $target);  
    	$_SESSION["Validation"]["txt"] = "Homepage Content updated successfully";
    	if($image_result == false) {
    		$_SESSION["Validation"]["txt"] = "Image Upload Error!!!, but Homepage Content updated successfully";
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

$posts_table_content = "			  			
              <tr>
                <th scope='row'>NULL</th>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
              </tr>
            	";

$sql = "SELECT datetime, title, creatorname, categoryname FROM `posts` ORDER BY datetime DESC LIMIT 15";
$result = $conn->query($sql);

if($result != false) { //query was successful
	if($row = $result->fetch_assoc()) { //first iteration only to nemove NULL table valuesand set $count
		$row_name = htmlspecialchars($row['title']);
		$row_creator = htmlspecialchars($row['creatorname']);
		$row_datetime = htmlspecialchars($row['datetime']);
		$row_category = htmlspecialchars($row['categoryname']);

		$posts_table_content = "<tr>
                <th scope='row'>1</th>
                <td>$row_name</td>
                <td>$row_category</td>                
                <td>$row_datetime</td>
                <td>$row_creator</td>
              </tr>                
           		";
	}
	$count = 2;
	while($row = $result->fetch_assoc()) {
		$row_name = htmlspecialchars($row['title']);
		$row_creator = htmlspecialchars($row['creatorname']);
		$row_datetime = htmlspecialchars($row['datetime']);
		$row_category = htmlspecialchars($row['categoryname']);
		$posts_table_content .= "<tr>
                <th scope='row'>$count</th>
                <td>$row_name</td>
                <td>$row_category</td>                
                <td>$row_datetime</td>
                <td>$row_creator</td>
              </tr>                
           		";
           		$count++;           		
	}	
}
else {
	die("Could not fetch results from cateory table" . $conn->error);
}






?>
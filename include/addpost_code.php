<?php 

$conn = make_db_connection();
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if(isset($_POST["identifier"]) && $_POST["identifier"] == "add_post_form") { //add post form has been submitted	
	
	$post_title = mysqli_real_escape_string($conn, trim($_POST["post_title"]));	
	$content = mysqli_real_escape_string($conn, trim($_POST["content"]));
	$post_category = mysqli_real_escape_string($conn, trim($_POST["category_select"]));
	$post_meta = mysqli_real_escape_string($conn, trim($_POST["meta"]));
	$choose_image = basename($_FILES["choose_image"]["name"]);
	$target = "Uploads/".$choose_image;

	if(empty($post_title) || empty($choose_image) || empty($content) || empty($post_category) || empty($post_meta)) {
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
		$stmt = $conn->prepare("INSERT INTO `posts` (datetime, title, creatorname, categoryname, image, content, postmeta) VALUES (?, ?, ?, ?, ?, ?, ?)");
		$creator = "Anupam";
        $stmt->bind_param("sssssss", $date_time, $post_title, $creator, $post_category, $choose_image, $content, $post_meta);
        $result = $stmt->execute();
        if($result) {    
        	move_uploaded_file($_FILES["choose_image"]["tmp_name"], $target);   	
        	$_SESSION["Validation"]["txt"] = "New Post added successfully";
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

$sql = "SELECT datetime, title, creatorname, categoryname FROM `posts` ORDER BY datetime DESC";
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
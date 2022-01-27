<?php 

$conn = make_db_connection();
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}
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


$post_title_old = "";
$title_repeat = NULL;
$category = "";
$image = "";
$intro_meta = '<tr>
                        <th>Crimes</th>
                        <td>34</td>  
                      </tr>
                      <tr>
                        <th>Born On</th>
                        <td>March 10, 1957</td>
                      </tr>
                      <tr>
                        <th>Died On</th>
                        <td>March 15, 2007</td>
                      </tr>
                      <tr>
                        <th>Known For</th>
                        <td>9/11 Attack</td>
                      </tr>
                      <tr>
                        <th>Jail Time</th>
                        <td>None</td>
                      </tr>';
$details_meta = '<tr>
                        <th>Criminal Status</th>
                        <td>Executed</td>
                      </tr>
                      <tr>
                        <th>Victims</th>
                        <td>4</td>
                      </tr>';
$related_meta = '<ol class="list list-unstyled">
	                <li><a href="/posts?id=23">Robel Puch</a></li>
	                <li><a href="/posts?id=67">Paula Danier</a></li>
	                <li><a href="/posts?id=23">Kanthelin freeman</a></li>
	                <li><a href="/posts?id=23">Leonard fraser</a></li>
	              </ol>';
$sources_meta = '<ul class="list">
	                <li><a href="#">News Article</a> on <a href="chanel7.com">chanel7.com</a></li>
	                <li><a href="#">Video tape</a> on <a href="chanel7.com">youtube.com</a></li>
	                <li><a href="#">News Pulished</a> on <a href="aajtak.com">aajtak.com</a></li>                        
	              </ul>';
$content = "<section>
		        <h2>Dummy heading</h2>
		        <p>Dummy paragaph</p>
		        <p>Dummy paragraph 2</p>
		      </section>
		      <hr></hr>
		      <section>
		        <h2>Section2 heading</h2>
		        <p>Section2 pargraph1</p>
		          <h3>Subheading 1 </h3>
		          <p>Sub paragraph1</p>
		          <h3>Subheading2</h3>
		          <p>Sub para 3</p>
		      </section>";
$action_text = "Add";
$update = false;

if(isset($_GET["id"])) {
	$post_id = mysqli_real_escape_string($conn, trim($_GET["id"]));
	$action_text = "Edit";
	$stmt = $conn->prepare("SELECT title, titlerepeat, categoryname, image, content FROM `posts` WHERE id=?");
	$stmt->bind_param("i", $post_id);
	$result = $stmt->execute();
	$get_result =  $stmt->get_result();
		$rows = $get_result->num_rows;		
		if($result && $rows != 0) {
			$row = $get_result->fetch_assoc();
			$post_title_old = $row["title"];
			$title_repeat = $row["titlerepeat"];
			$category = $row["categoryname"];
			$image = $row["image"];
			$content = $row["content"];
			libxml_use_internal_errors(true); //important
			$tmp = new DOMDocument();
			$tmp->loadHTML('<!DOCTYPE html><meta charset="UTF-8">' . $content);			
			$intro_meta = $tmp->saveHTML($tmp->getElementsByTagName("intro-data")[0]);
			$intro_meta = substr($intro_meta, 12, -13);
			$details_meta = $tmp->saveHTML($tmp->getElementsByTagName("details")[0]);
			$details_meta = substr($details_meta, 14, -15);
			$related_meta = $tmp->saveHTML($tmp->getElementsByTagName("related")[0]);
			$related_meta = substr($related_meta, 14, -15);
			$sources_meta = $tmp->saveHTML($tmp->getElementsByTagName("sources")[0]);
			$sources_meta = substr($sources_meta, 14, -15);
			$content = $tmp->saveHTML($tmp->getElementsByTagName("content")[0]);		
			
			$content = substr($content, 9, -10);
			$update = true;
		}
		else {
			die("Probably Wrong post id " . $stmt->error);
		}
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
	}		
	
	$post_category = mysqli_real_escape_string($conn, trim($_POST["category_select"]));	
	if(!empty($_FILES["choose_image"]['name'])) {				
		$choose_image = basename($_FILES["choose_image"]["name"]);
		$name = pathinfo($choose_image)["filename"];
		$extension = pathinfo($choose_image)["extension"];
		$target = "Uploads/" . $choose_image;
		$reps = "";
		while(file_exists($target)) { //rename image name if already exists on server
			$target = "Uploads/" . $name . $reps++ . "." . $extension;
		} 
		$choose_image = basename($target);
	}
	else {		
		$choose_image = $image;
	}	
	
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
		//check if title aready exists
		$stmt = $conn->prepare("SELECT * FROM `posts` WHERE title=? ");
		$stmt->bind_param("s", $post_title);
		$result = $stmt->execute();
		$get_result =  $stmt->get_result();
		$rows = $get_result->num_rows;		
		if($post_title != $post_title_old && $result && $rows != 0) {
			$title_repeat = $rows;			
		}
		//$get_result->free_result();
		if($update) {
			$stmt = $conn->prepare("UPDATE `posts` SET datetime=?, title=?,  titlerepeat=?, creatorname=?, categoryname=?, image=?, content=? WHERE id=?");	
    	$stmt->bind_param("sssssssi", $date_time, $post_title, $title_repeat, $creator, $post_category, $choose_image, $content, $post_id);
		}
		else {
			$stmt = $conn->prepare("INSERT INTO `posts` (datetime, title,  titlerepeat, creatorname, categoryname, image, content) VALUES (?, ?, ?, ?, ?, ?, ?)");	
    	$stmt->bind_param("sssssss", $date_time, $post_title, $title_repeat, $creator, $post_category, $choose_image, $content);
		}
		
    $result = $stmt->execute();
    if($result) {      
    	if( !empty($_FILES["choose_image"]['name']) ) {
    		$image_result = move_uploaded_file($_FILES["choose_image"]["tmp_name"], $target);
	    		if($image_result == false) {
	    		$_SESSION["Validation"]["txt"] = "Image Upload Error!!! -- ";
	    	} 
    	} 
    	if($update) {
    		$_SESSION["Validation"]["txt"] .= "Post with id $post_id updated successfully";
    	}    	  
    	else {
    		$_SESSION["Validation"]["txt"] .= "New Post added successfully";
    	}
    	   	
			$_SESSION["Validation"]["class"]  = "valid-feedback d-block"; //make eror message visible
			$_SESSION["Validation"]["status"] = "is-valid";									
    }
    else { //some error in adding category
    	die("Failed to add new post " . $stmt->error);
    }
	}
	//303 will allow bookmark and reload without resending post data
	header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
  exit();
}







?>
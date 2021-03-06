<?php 


//Impotant bug in apache rewrite rules -- https://stackoverflow.com/a/60109807/3429430  
// http://localhost2/post/abc.. is treated as http://localhost2/post/abc -- without any trailing dots
if(preg_match('#^/post/([^/]*\.+$)#',$_SERVER['REQUEST_URI'],$matches)){
    $_GET["title"] = urldecode($matches[1]);
}


$conn = make_db_connection();
mysqli_query($conn, "SET NAMES utf8");
mysqli_set_charset($conn, "utf8"); //may be not needed
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if( !empty($_GET["id"]) || !empty($_GET["title"]) ) {
	if( !empty($_GET["id"]) ) {
		$post_id = $_GET["id"];	
		$stmt = $conn->prepare("SELECT datetime, title, creatorname, categoryname, image, content FROM `posts` WHERE id=?");
		$stmt->bind_param("i", $post_id);
	}
	else {
		$post_title = $_GET["title"];		
		$title_repeat = !empty($_GET["repeat"]) ? $_GET["repeat"] : NULL;		
		$stmt = $conn->prepare("SELECT datetime, title, creatorname, categoryname, image, content FROM `posts` WHERE title=? AND titlerepeat<=>?");
		$stmt->bind_param("si", $post_title, $title_repeat);
	}
	$creator = "Anupam";	
	$result = $stmt->execute();	
	$get_result = $stmt->get_result();	
	if($result && $get_result->num_rows) { //query was successful
		if( $row = $get_result->fetch_assoc() ) { 
			$title = htmlspecialchars($row['title']);
			$creator = htmlspecialchars($row['creatorname']);
			$datetime = htmlspecialchars($row['datetime']);
			$category = htmlspecialchars($row['categoryname']);	
			$image = htmlspecialchars($row['image']);


			libxml_use_internal_errors(true); // important
			$content = new DOMDocument();
			$content->loadHTML('<!DOCTYPE html><meta charset="UTF-8">' . $row['content']);			

			$space = $content->createTextNode(" ");
			$introData = $content->getElementsByTagName("intro-data")[0]->getElementsByTagName('br');

			while ($introData->length != 0) {	//remove all <br> tags	    
			    $node = $introData->item(0);			    
	        $node->parentNode->replaceChild($space->cloneNode(), $node);	        	   
			}
			

			$introData = $content->saveHTML( ($content->getElementsByTagName('intro-data')[0]) );
			$introData = substr($introData, 12, -13);

			$details = $content->saveHTML( ($content->getElementsByTagName('details')[0]) );
			$details = substr($details, 9, -10);

			$related = $content->saveHTML( ($content->getElementsByTagName('related')[0]) );
			$related = substr($related, 9, -10);

			$sources = $content->saveHTML( ($content->getElementsByTagName('sources')[0]) );
			$sources = substr($sources, 9, -10);

			$content2 = $content->saveHTML( ($content->getElementsByTagName('content')[0]) );
			$content2 = substr($content2, 9, -10);

		}
	}
	else {
		die("Could not fetch results from the database. Probably wrong post-id or title" . $conn->error);
	}
}
else {
	echo "Post id or title must be non empty";
	die();
}






?>
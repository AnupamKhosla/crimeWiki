<?php 

$conn = make_db_connection();
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if( isset($_GET["id"]) ) {
	$post_id = $_GET["id"];		
	$stmt = $conn->prepare("SELECT datetime, title, creatorname, categoryname, image, content FROM `posts` WHERE id=?");
	$creator = "Anupam";
	$stmt->bind_param("i", $post_id);
	$result = $stmt->execute();	
	if($result != false) { //query was successful
		if( $row = $stmt->get_result()->fetch_assoc() ) { 
			$title = htmlspecialchars($row['title']);
			$creator = htmlspecialchars($row['creatorname']);
			$datetime = htmlspecialchars($row['datetime']);
			$category = htmlspecialchars($row['categoryname']);	
			$image = htmlspecialchars($row['image']);

			libxml_use_internal_errors(true); // important
			$content = new DOMDocument();
			$content->loadHTML($row['content']);	

			$introData = $content->getElementsByTagName("intro-data")[0]->getElementsByTagName('br');

			while ($introData->length != 0) {	//remove all <br> tags	    
			    $node = $introData->item(0);			    
	        $node->parentNode->removeChild($node);	        	   
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

		die("Could not fetch results from the database" . $conn->error);
	}
}
else {
	echo "Post id must be filled";
	die();
}






?>
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

// Find the post id: /post/123 gives it directly; /post/<anything> is turned
// into a slug, so old URLs ("Rex%20Heuermann") still find their post.
$post_id = NULL;
if( !empty($_GET["id"]) ) {
	$post_id = (int)$_GET["id"];
}
else if( isset($_GET["title"]) && $_GET["title"] !== "" ) {
	$title_repeat = !empty($_GET["repeat"]) ? (int)$_GET["repeat"] : NULL;
	$post_id = find_post_id_by_slug($conn, post_slug((string)$_GET["title"]), $title_repeat);
}
if( !$post_id ) {
	render_post_not_found();
}

if( $post_id ) {
	$stmt = $conn->prepare("SELECT datetime, title, titlerepeat, creatorname, categoryname, image, content FROM `posts` WHERE id=?");
	$stmt->bind_param("i", $post_id);
	$creator = "Anupam";	
	$result = $stmt->execute();	
	$get_result = $stmt->get_result();	
	if($result && $get_result->num_rows) { //query was successful
		if( $row = $get_result->fetch_assoc() ) { 
			$raw_title = (string)$row['title'];
			$canonical_repeat = $row['titlerepeat'];

			// One address per post: anything other than the hyphen URL gets a
			// permanent redirect to it.
			$canonical_path = post_path($raw_title, $canonical_repeat);
			$request_path = rawurldecode((string)parse_url($_SERVER["REQUEST_URI"] ?? "", PHP_URL_PATH));
			if($request_path !== rawurldecode($canonical_path)) {
				header("Location: " . $canonical_path, true, 301);
				exit;
			}
			$title = htmlspecialchars($raw_title, ENT_QUOTES, 'UTF-8');
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
		render_post_not_found();
	}
}






?>

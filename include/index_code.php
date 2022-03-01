<?php 
$conn = make_db_connection();

//slider section
$result = $conn->query( "SELECT title, image, titlerepeat, content FROM `posts` WHERE categoryname='criminals' ORDER BY rand() LIMIT 50;" );
$slides = "";
if(!!$result && $result->num_rows) { //query was successful	
	while( $row = $result->fetch_assoc() ) { 
		$title = htmlspecialchars($row['title']);			
		$image = htmlspecialchars(image_path($row['image']));
		$titleRepeat = htmlspecialchars($row['titlerepeat']) ?? "";	
		$slides .= <<<EOT
											<div class="slide">
								        <div class="card">
								          <img data-lazy="$image" class="card-img-top post-pic" alt="profile pic" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
								          <div class="card-body">                
								            <a href="post/$title/$titleRepeat" class="">$title</a>
								          </div>
								        </div>
								      </div>
		EOT;
	}		
}
else {
	die("Could not fetch results from the database. Probably wrong post-id or title" . $conn->error);
}
//slider finishes



//about us section
$result = $conn->query( "SELECT content FROM `posts` WHERE title='\$blog_about_text';" );
if(!!$result && $result->num_rows) {
	$blog_about_text = $result->fetch_row()[0];
}
//about us finishes



//crime of the month  
$result = $conn->query( "SELECT content, wikilink FROM `posts` WHERE title='\$blog_month_post';" );
if(!!$result && $result->num_rows) {
	$row = $result->fetch_assoc();
	$month_id = $row["content"];
	$video_link = $row["wikilink"];
	$result = $conn->query("SELECT datetime, title, titlerepeat, content FROM `posts` WHERE id=$month_id");
	if(!!$result && $result->num_rows) {
		$res_arr = $result->fetch_assoc();
		$publish_date = $res_arr["datetime"];
		if($res_arr["titlerepeat"] != NULL) {
			$titlerepeat = "/".$res_arr["titlerepeat"];
		}
		else {
			$titlerepeat = "";			
		}
		$title = $res_arr["title"];	
		$blog_month_href = "/post/" . $title . $titlerepeat;
		libxml_use_internal_errors(true); // important
			$content = new DOMDocument();
			$content->loadHTML('<!DOCTYPE html><meta charset="UTF-8">' . $res_arr["content"]);		
			$sources = $content->saveHTML( ($content->getElementsByTagName('sources')[0]) );
			$sources = substr($sources, 9, -10);
			$content_tag = $content->getElementsByTagName('content')[0];

			$introduction = "";			
			$p = $content_tag->getElementsByTagName('p')[0];
			$introduction .= $content->saveHTML($p);
			while(isset($p->nextSibling) && $p->nextSibling->nodeName != "hr") {						
				$p = $p->nextSibling;
				$introduction .= $content->saveHTML($p);				
			}			 
	}

	
}
//crime of month finishes





?>
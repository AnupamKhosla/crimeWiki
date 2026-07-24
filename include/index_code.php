<?php 
$conn = make_db_connection();
$sliderPlaceholderSrc = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='1' height='1'%3E%3Crect width='1' height='1' fill='%23d8dce2'/%3E%3C/svg%3E";

//slider section
$selectedCategory = trim((string)($_GET["category"] ?? "Criminals"));
$selectedFilter = trim((string)($_GET["filter"] ?? ""));

$allowedCategories = [];
$categoryResult = $conn->query("SELECT name FROM `categories` WHERE name!='Blog'");
if($categoryResult != false) {
	while($categoryRow = $categoryResult->fetch_assoc()) {
		$allowedCategories[] = $categoryRow["name"];
	}
}

if($selectedCategory === "" || !in_array($selectedCategory, $allowedCategories, true)) {
	$selectedCategory = "Criminals";
}

$orderBy = "ORDER BY rand()";
if($selectedFilter === "datetime") {
	$orderBy = "ORDER BY datetime DESC";
}
else if($selectedFilter === "alphabetically") {
	$orderBy = "ORDER BY title";
}
else if($selectedFilter === "popular") {
	$orderBy = "ORDER BY CHAR_LENGTH(content) DESC";
}
else if($selectedFilter === "country") {
	$orderBy = "ORDER BY ISNULL(country), country, title";
}

$stmt = $conn->prepare("SELECT title, image, titlerepeat, content FROM `posts` WHERE categoryname=? $orderBy LIMIT 50");
$stmt->bind_param("s", $selectedCategory);
$stmt->execute();
$result = $stmt->get_result();

$slides = "";
if(!!$result && $result->num_rows) { //query was successful	
			while( $row = $result->fetch_assoc() ) { 
				$title = htmlspecialchars($row['title']);			
				$rawImage = trim((string)($row['image'] ?? ""));
				$image = htmlspecialchars(image_path($rawImage));
				$imageAttrs = "";
				$imageClasses = "card-img-top post-pic";
				if($rawImage === "" || $image === htmlspecialchars(default_image_path(), ENT_QUOTES, 'UTF-8')) {
					$imageClasses .= " is-default-image";
					$imageAttrs = "data-default-src=\"" . htmlspecialchars(default_image_path(), ENT_QUOTES, 'UTF-8') . "\"";
				}
				$titleRepeat = htmlspecialchars($row['titlerepeat'] ?? "");	
				$slides .= <<<EOT
											<div class="slide">
								        <div class="card">
								          <img data-lazy="$image" $imageAttrs class="$imageClasses" alt="profile pic" src="$sliderPlaceholderSrc">
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
	$month_id = (int)$row["content"];
	$video_link = $row["wikilink"];
	$stmt = $conn->prepare("SELECT datetime, title, titlerepeat, content FROM `posts` WHERE id=?");
	$stmt->bind_param("i", $month_id);
	$stmt->execute();
	$result = $stmt->get_result();
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

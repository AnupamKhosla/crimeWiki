<?php 

$posts = "";

if($_SERVER['REQUEST_METHOD'] === 'GET') {
	$title = empty($_GET["title"]) ? "%" : "%".$_GET["title"]."%";
	$category = empty($_GET["category"]) ? "%" : $_GET["category"];
	$filter = empty($_GET["filter"]) ? "datetime" : $_GET["filter"];
	$conn = make_db_connection();

	if($filter == "datetime") {
		$stmt = $conn->prepare("SELECT title, image, titlerepeat, content FROM `posts` WHERE NOT title='\$blog_month_post' AND NOT title='\$blog_about_text' AND title LIKE ? AND categoryname LIKE ? ORDER BY datetime DESC LIMIT 30");
	}
	
	$stmt->bind_param("ss", $title, $category);
	$result = $stmt->execute();		
	if($result != false && $result = $stmt->get_result()) { //query was successful
		libxml_use_internal_errors(true); //important
		$tmp = new DOMDocument();
		
		while($row = $result->fetch_assoc()) { //first iteration only to nemove NULL table valuesand set $count				
			$row_name = htmlspecialchars($row['title']);
			if(strlen($row_name) > 200) {
				$row_name = substr($row_name, 0, 200) . "...";
			}
			
			$tmp->loadHTML('<!DOCTYPE html><meta charset="UTF-8">' . $row['content']);
			$content = $tmp->getElementsByTagName("content")[0];
			
			$introduction = "";			
			$p = $content->getElementsByTagName('p')[0];
			$introduction .= $tmp->saveHTML($p);
			while(isset($p->nextSibling) && $p->nextSibling->nodeName != "hr") {						
				$p = $p->nextSibling;
				if(strlen($introduction) > 1700) {
					$introduction = $introduction . "...";
					break;
				}
				else {
					$introduction .= $tmp->saveHTML($p);
				}									
			}					
			
			$row_image = image_path(htmlspecialchars($row["image"]));
			$row_repeat = htmlspecialchars($row['titlerepeat']);
			$posts .= "<div class='row post mb-5'>
        <div class='col-xl-3 col-md-4'>
        	<h3 class='text-pm d-md-none text-center mb-2'>$row_name</h3>
          <div class='card post-profile '>
            <img src='$row_image' class='card-img-top post-pic' alt='profile pic'>              
          </div>
        </div>
        <div class='col-xl-9 col-md-8 d-flex flex-column'>
          <h3 class='text-pm d-none d-md-block'>$row_name</h3>            
            $introduction            
          <div class='d-flex justify-content-center mt-auto pt-3'>
          	<a href='post/$row_name/$row_repeat' class='btn btn-pm d-inline-flex align-items-center'>See Details</a>
          </div>	
        </div> 
      </div>";
		}
	}
	
}

?>
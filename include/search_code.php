<?php 

$posts = "";

if($_SERVER['REQUEST_METHOD'] === 'GET') {
	$title = $_GET["title"] ?? "%";
	$category = $_GET["category"] ?? "%";
	$filter = $_GET["filter"] ?? "datetime";
	$conn = make_db_connection();

	if($filter == "datetime") {
		$sql = "SELECT title, image, content FROM `posts` WHERE NOT title='\$blog_month_post' AND NOT title='\$blog_about_text' AND categoryname LIKE '$category' ORDER BY datetime DESC LIMIT 30";
		$result = $conn->query($sql);
		var_dump($category);
		if($result != false) { //query was successful

			while($row = $result->fetch_assoc()) { //first iteration only to nemove NULL table valuesand set $count				
				$row_name = htmlspecialchars($row['title']);
				if(strlen($row_name) > 200) {
					$row_name = substr($row_name, 0, 200) . "...";
				}
				$row_content = htmlspecialchars($row['content']); 
				if(strlen($row_content) > 3000) {
					$row_content = substr($row_content, 0, 3000) . "...";
				}
				$row_image = image_path(htmlspecialchars($row["image"]));
				$posts .= "<div class='row post mb-5'>
          <div class='col-lg-5 col-xl-4'>
            <div class='card post-profile '>
              <img src='$row_image' class='card-img-top post-pic' alt='profile pic'>              
            </div>
          </div>
          <div class='col-lg-7 col-xl-8'>
            <h3>$row_name</h3>
            <p>
              $row_content
            </p>
          </div> 
        </div>";
			}
		}
	}
}

?>
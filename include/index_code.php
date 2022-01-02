<?php 

$conn = make_db_connection();

$result = $conn->query( "SELECT title, image, titlerepeat, content FROM `posts` WHERE categoryname='criminals' ORDER BY rand() LIMIT 100" );
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






?>
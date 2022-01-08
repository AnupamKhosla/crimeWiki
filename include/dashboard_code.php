<?php 
if(!isset($_SESSION["Response"])) {
	$_SESSION["Response"] = array("month_post" => '', "about_text" => '', "display" => "d-none");
}
function reset_response() {	
	$_SESSION["Response"] = NULL;	
}

$conn = make_db_connection();	
if(isset($_POST["identifier"]) && $_POST["identifier"] == "dashboard_form") { 	
	if( isset($_POST["check_about_text"]) && $_POST["check_about_text"]=="on" ) { //insert db for about text
		$about_text = str_replace( array("\r\n", "\n", "\t", "\r"), '', trim($_POST["about"]) );
		//$about_text = mysqli_real_escape_string($conn, $about_text);
		$stmt = $conn->prepare("UPDATE `posts` SET content=? WHERE title='\$blog_about_text' AND creatorname='SuperUser';");
		$stmt->bind_param("s", $about_text);
		$result = $stmt->execute();
		if(!$result) {
			die("MYSQLI ERROR: Failed to add content in post $blog_about_text" . $stmt->error);
		}		
		$_SESSION["Response"]["display"] = "";
		$_SESSION["Response"]["about_text"] = "<p>About Text update in database post \$blog_about_text</p>";
	}
	if( isset($_POST["check_post"]) && $_POST["check_post"]=="on" ) { //insert db for monthly post
		$post_title = mysqli_real_escape_string($conn, trim($_POST["post_title"]));
		$video_link = mysqli_real_escape_string($conn, trim($_POST["video_link"]));
		$title_repeat = $_POST["title_repeat"] ?? NULL;  
		$title_repeat = mysqli_real_escape_string($conn, trim($title_repeat));
		if($title_repeat == NULL || $title_repeat=="0" || ctype_space($title_repeat) || strtoupper($title_repeat)=="NULL") {		
		  $title_repeat = NULL;	
			$result = $conn->query("SELECT id FROM posts WHERE title='$post_title' AND titlerepeat<=>NULL;");
		}
		else {
			$result = $conn->query("SELECT id FROM posts WHERE title='$post_title' AND titlerepeat=$title_repeat;");
		}		
		if(!!$result && $result->num_rows > 0) {
			//correct post to be input
			$post_id = $result->fetch_row()[0];
			$stmt = $conn->prepare("UPDATE `posts` SET content=?, titlerepeat=?, wikilink=? WHERE title='\$blog_month_post' AND creatorname='SuperUser';");
			$stmt->bind_param("sss", $post_id, $title_repeat, $video_link);
			$result = $stmt->execute();
			if(!$result) {
				die("MYSQLI ERROR: Failed to add content in post $blog_month_post" . $stmt->error);
			}	
			$_SESSION["Response"]["display"] = "";
			$_SESSION["Response"]["month_post"] = "<p>Monthly Post updated in database post \$blog_month_post</p>";
		}
		else {
			die("Wrong Post title and/or title_repeat" . $stmt->error);
		}		
	}	
	//303 will allow bookmark and reload without resending post data
	header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
  exit();
}

//get latest 10 posts from database
$posts_table_content = "			  			
              <tr>
                <th scope='row'>NULL</th>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
              </tr>
            	";
$sql = "SELECT id, datetime, title, titlerepeat, categoryname FROM `posts` WHERE NOT title='\$blog_month_post' AND NOT title='\$blog_about_text' ORDER BY datetime DESC LIMIT 10";
$result = $conn->query($sql);
if($result != false) { //query was successful
	if($row = $result->fetch_assoc()) { //first iteration only to nemove NULL table valuesand set $count
		$row_id = htmlspecialchars($row['id']);
		$row_name = htmlspecialchars($row['title']);
		if(strlen($row_name) > 20) {
			$row_name = substr($row_name, 0, 20) . "...";
		}
		$row_repeat = htmlspecialchars($row['titlerepeat'] ?? "NULL");
		$row_datetime = date( 'd/m/Y H:i:s', htmlspecialchars($row['datetime']) );
		$row_category = htmlspecialchars($row['categoryname']);

		$posts_table_content = "<tr>
                <th scope='row'>1</th>
                <td> <a href='/post/$row_id'>$row_name</a> </td>
                <td>$row_category</td>                
                <td>$row_datetime</td>
                <td>$row_repeat</td>
              </tr>                
           		";
	}
	$count = 2;
	while($row = $result->fetch_assoc()) {
		$row_id = htmlspecialchars($row['id']);
		$row_name = htmlspecialchars($row['title']);
		if(strlen($row_name) > 20) {
			$row_name = substr($row_name, 0, 20) . "...";
		}
		$row_repeat = htmlspecialchars($row['titlerepeat'] ?? "NULL");
		$row_datetime = date( 'd/m/Y H:i:s', htmlspecialchars($row['datetime']) );
		$row_category = htmlspecialchars($row['categoryname']);
		$posts_table_content .= "<tr>
                <th scope='row'>$count</th>
                <td> <a href='/post/$row_id'>$row_name</a> </td>
                <td>$row_category</td>                
                <td>$row_datetime</td>
                <td>$row_repeat</td>
              </tr>                
           		";
           		$count++;           		
	}	
}
else {
	die("Could not fetch results from cateory table" . $conn->error);
}
//get 10 posts finished  




//Get about crime text section starts  
$result = $conn->query( "SELECT content FROM `posts` WHERE title='\$blog_about_text' AND creatorname='SuperUser';" );
if(!!$result && $result->num_rows) {
	$blog_about_text = $result->fetch_row()[0];
}
//END: Get about crime text section finishes  

//Get blog_month_post text section starts  
$result = $conn->query( "SELECT title, wikilink, titlerepeat, content FROM `posts` WHERE title='\$blog_month_post' AND creatorname='SuperUser';" );
if(!!$result && $result->num_rows) {
	$row = $result->fetch_assoc();
	$post_id = mysqli_real_escape_string($conn, $row["content"]);
	$video_link = htmlspecialchars($row["wikilink"]);
	$title_repeat = htmlspecialchars($row["titlerepeat"]);

	$result = $conn->query("SELECT title FROM posts WHERE id=$post_id;");
	if(!!$result && $result->num_rows) {
		$post_title = htmlspecialchars($result->fetch_row()[0]);
	}
	else {
		die("Could not fetch results from post with id=$post_id" . $conn->error);
	}
}
else {
	die("Could not fetch results from $blog_month_post" . $conn->error);
}
//END: Get  blog_month_post text section finishes 



?>
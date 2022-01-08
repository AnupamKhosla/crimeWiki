<?php 
if(!isset($_SESSION["Response"])) {
	$_SESSION["Response"] = array("month_post" => '', "about_text" => '', "display" => "d-none");
}
function reset_response() {	
	$_SESSION["Response"] = NULL;	
}

$conn = make_db_connection();	
$pos = 0;
if(isset($_GET["pos"])) { 	
	$pos = $_GET["pos"];
	//303 will allow bookmark and reload without resending post data
	//header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
  //exit();
}

//get latest 10 posts from database
$posts_table_content = "			  			
              <tr>
                <th scope='row'>NULL</th>
                <th scope='row'>NULL</th>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
              </tr>
            	";
$sql = "SELECT id, datetime, title, titlerepeat, categoryname FROM `posts` WHERE NOT title='\$blog_month_post' AND NOT title='\$blog_about_text' ORDER BY datetime DESC LIMIT $pos, 30";
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
                <th scope='row'>$row_id</th>
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
                <th scope='row'>$row_id</th>
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




?>
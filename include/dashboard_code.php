<?php 

$conn = make_db_connection();
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if(isset($_POST["identifier"]) && $_POST["identifier"] == "dashboard_form") { //add post form has been submitted		
	
	//303 will allow bookmark and reload without resending post data
	header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
  exit();
}



//get latest 15 posts from database
$posts_table_content = "			  			
              <tr>
                <th scope='row'>NULL</th>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
              </tr>
            	";

$sql = "SELECT datetime, title, creatorname, categoryname FROM `posts` ORDER BY datetime DESC LIMIT 15";
$result = $conn->query($sql);

if($result != false) { //query was successful
	if($row = $result->fetch_assoc()) { //first iteration only to nemove NULL table valuesand set $count
		$row_name = htmlspecialchars($row['title']);
		$row_creator = htmlspecialchars($row['creatorname']);
		$row_datetime = htmlspecialchars($row['datetime']);
		$row_category = htmlspecialchars($row['categoryname']);

		$posts_table_content = "<tr>
                <th scope='row'>1</th>
                <td>$row_name</td>
                <td>$row_category</td>                
                <td>$row_datetime</td>
                <td>$row_creator</td>
              </tr>                
           		";
	}
	$count = 2;
	while($row = $result->fetch_assoc()) {
		$row_name = htmlspecialchars($row['title']);
		$row_creator = htmlspecialchars($row['creatorname']);
		$row_datetime = htmlspecialchars($row['datetime']);
		$row_category = htmlspecialchars($row['categoryname']);
		$posts_table_content .= "<tr>
                <th scope='row'>$count</th>
                <td>$row_name</td>
                <td>$row_category</td>                
                <td>$row_datetime</td>
                <td>$row_creator</td>
              </tr>                
           		";
           		$count++;           		
	}	
}
else {
	die("Could not fetch results from cateory table" . $conn->error);
}






?>
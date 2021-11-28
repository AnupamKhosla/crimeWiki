<?php 

$conn = make_db_connection();
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if(isset($_POST["category_input"])) { //category form has been submitted	
	var_dump($_POST["category_input"]);
	
	$category = mysqli_real_escape_string($conn, trim($_POST["category_input"]));	
	if(empty($category)) {
		$_SESSION["Validation"]["txt"] = "All fields must be filled";	
		$_SESSION["Validation"]["class"]  = "invalid-feedback d-block"; //make eror message visible
		$_SESSION["Validation"]["status"] = "is-invalid";	
	}
	else if(strlen($category) > 99) {
		$_SESSION["Validation"]["txt"] = "Category Name should not be more than 99 characters";
		$_SESSION["Validation"]["class"]  = "invalid-feedback d-block"; //make eror message visible
		$_SESSION["Validation"]["status"] = "is-invalid";
	}
	else {// everything is fine; update categories now		
		$stmt = $conn->prepare("INSERT INTO `categories` (datetime, name, creatorname) VALUES (?, ?, ?)");
		$creator = "Anupam";
        $stmt->bind_param("sss", $date_time, $category, $creator);
        $result = $stmt->execute();
        if($result) {        	
        	$_SESSION["Validation"]["txt"] = "Category added successfully";
					$_SESSION["Validation"]["class"]  = "valid-feedback d-block"; //make eror message visible
					$_SESSION["Validation"]["status"] = "is-valid";									
        }
        else { //some error in adding category
        	die("Failed to add new category" . $stmt->error);
        }
	}

	//303 will allow bookmark and reload without resending post data
	header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
  exit();
}

$category_table_content = "			  			
              <tr>
                <th scope='row'>NULL</th>
                <td>NULL</td>
                <td>NULL</td>
                <td>@NULL</td>
              </tr>
            	";

$sql = "SELECT * FROM `categories` ORDER BY datetime DESC";
$result = $conn->query($sql);

if($result != false) { //query was successful
	if($row = $result->fetch_assoc()) { //first iteration only to nemove NULL table values 

		$row_name = htmlspecialchars($row['name']);
		$row_creator = htmlspecialchars($row['creatorname']);
		$row_datetime = htmlspecialchars($row['datetime']);

		$category_table_content = "<tr>
                <th scope='row'>1</th>
                <td>$row_name</td>
                <td>$row_creator</td>
                <td>$row_datetime</td>
              </tr>                
           		";
	}
	$count = 2;
	while($row = $result->fetch_assoc()) {

		$row_name = htmlspecialchars($row['name']);
		$row_creator = htmlspecialchars($row['creatorname']);
		$row_datetime = htmlspecialchars($row['datetime']);

		$category_table_content .= "<tr>
                <th scope='row'>$count</th>
                <td>$row_name</td>
                <td>$row_creator</td>
                <td>$row_datetime</td>
              </tr>                
           		";
           		$count++;
           		
	}	
}
else {
	die("Could not fetch results from cateory table" . $conn->error);
}






?>
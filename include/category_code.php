<?php 
$error_class     = "";  
$error_status    = "";
$conn = make_db_connection();

if(isset($_POST["category_input"])) { //category form has been submitted	
	$category = mysqli_real_escape_string($conn, $_POST["category_input"]);
	if(empty($category)) {
		$_SESSION["ErrorMessage"] = "All fields must be filled";
		$error_class  = "d-block"; //make eror message visible
		$error_status = "is-invalid";		
	}
	else if(strlen($category) > 99) {
		$_SESSION["ErrorMessage"] = "Category Name should not be more than 99 characters";
		$error_class  = "invalid-feedback d-block"; //make eror message visible
		$error_status = "is-invalid";	
	}
	else {// everything is fine; update categories now		
		$stmt = $conn->prepare("INSERT INTO `categories` (datetime, name, creatorname) VALUES (?, ?, ?)");
		$creator = "Anupam";
        $stmt->bind_param("sss", $date_time, $category, $creator);
        $result = $stmt->execute();
        if($result) {        	
        	$_SESSION["SuccessMessage"] = "Category added successfully";
        	$error_class  = "valid-feedback d-block"; //make eror message visible
					$error_status = "is-valid";						
        }
        else { //some error in adding category
        	die("Failed to add new category" . $stmt->error);
        }
	}
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
		$category_table_content = "<tr>
                <th scope='row'>1</th>
                <td>{$row['name']}</td>
                <td>{$row['creatorname']}</td>
                <td>{$row['datetime']}</td>
              </tr>                
           		";
	}
	$count = 2;
	while($row = $result->fetch_assoc()) {
		$category_table_content .= "			  			
              <tr>
                <th scope='row'>$count</th>
                <td>{$row['name']}</td>
                <td>{$row['creatorname']}</td>
                <td>{$row['datetime']}</td>
              </tr>                
           		";
           		$count++;
           		
	}	
}
else {
	die("Could not fetch results from cateory table" . $conn->error);
}






?>
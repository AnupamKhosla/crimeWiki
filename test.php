<?php 
require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/check_login.php");


$conn = make_db_connection();
$result = $conn->query("SELECT datetime, (title), creatorname, categoryname, image, (content) FROM `posts` WHERE id=200");
$row = $result->fetch_all()[0];
echo "test <br>",  $row["1"], "<br>", $row[5], "<br>" ,  $row["1"], "<br>", $row[5];



 ?>
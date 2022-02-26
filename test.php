<?php 
require_once('include/config.php');
require_once('include/functions.php');

$servername = "localhost";
$username = "";
$password = "";


  $conn = new PDO("mysql:host=$servername;dbname=myDB", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully";


?>
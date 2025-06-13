<?php 
require_once('include/config.php');
require_once('include/functions.php');

$servername = "localhost";
$username = DB_USER_NAME;
$password = DB_PASSWORD;

try {
  $conn = new PDO("mysql:host=$servername;dbname=crimewiki", $username, $password);
  // set the PDO error mode to exception
  //$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

echo "111555";
?>
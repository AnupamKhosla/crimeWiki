<?php 
//session_set_cookie_params(60);
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$identifier = htmlspecialchars(isset($_POST["identifier"]) ? $_POST["identifier"] : "");
$logged_in = isset($_SESSION["logged_in"]) ? $_SESSION["logged_in"] : FALSE;

date_default_timezone_set("Asia/Kolkata");
$date_time = (new DateTime())->getTimestamp();   //strftime("%d-%B-%Y %H-%M-%S", time());


?>
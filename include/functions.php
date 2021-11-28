<?php 

function make_db_connection(){
	return (new mysqli($_SERVER['HTTP_HOST'], DB_USER_NAME, DB_PASSWORD, DB_NAME));
}

function error_message() {
	if(isset($_SESSION["ErrorMessage"])) {		
		$output = $_SESSION["ErrorMessage"];
		$_SESSION["ErrorMessage"] = NULL;
		return $output;
	}
}

function success_message() {
	if(isset($_SESSION["SuccessMessage"])) {		
		$output = $_SESSION["SuccessMessage"];
		$_SESSION["SuccessMessage"] = NULL;
		return $output;
	}
}

?>
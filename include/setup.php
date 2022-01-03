<?php

//Three different forms' visibility
$db_form_vis = "";  
$rg_form_vis = "d-none";
$lg_form_vis = "d-none";
$page_title = "Setup database";

if(SETUP) { //if db already setup
  $db_form_vis = "d-none";
  //use register first time admin form then
  $conn = make_db_connection();
  if ($conn->connect_error) {
    echo "Match config file database details with your servers' db details";
    die("Connection failed: " . $conn->connect_error);
  }
  $sql = "SELECT 1 FROM admins LIMIT 1";
  try{
    $result = $conn->query($sql); //handle errors myself
  }
  catch (Exception $e) {
    $result = false; //if mysql_error_reporting kicks in
  }
  
  if($result !== false && $result->num_rows > 0) { //if superuser already registered 
    //start login form. Admin already exists 
    $lg_form_vis = "";  
    $page_title = "User Login"; 
    if($logged_in == true) { //if already logged-in through a different tab
      //redirect to dashboard
      header("Location: dashboard.php", true, 303); 
      exit();
    }

    else if($identifier == "login") {
      if( !empty($_POST["login_name"]) && !empty($_POST["login_password"]) ) {

        $login_username = mysqli_real_escape_string($conn, $_POST["login_name"]);
        $login_pass = mysqli_real_escape_string($conn, $_POST["login_password"]);

        $stmt = $conn->prepare("SELECT id FROM admins WHERE username=? AND password=?");
        $stmt->bind_param("ss", $login_username, $login_pass);
        $result = $stmt->execute();
        $stmt->store_result();
        if($result && ($stmt->num_rows > 0)) {
          $_SESSION["logged_in"] = true;
          header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
          exit();
        }
        else {                
          echo "Incorrect Username and/or password";
        }
      }
      else {
        echo "All fields must be filled";
      }
    }
    else if($logged_in !== false) { //show login page
       echo "Custom Error: Session variables are stuffed";
    }    
  }
  else { //super user is not registered
         //show register admin form instead.
    $rg_form_vis = "";  
    $page_title = "Register User";      
    
    if( ($identifier == 'register') && !empty($_POST["admin_name"]) && !empty($_POST["admin_password1"]) && !empty($_POST["admin_password2"]) ) {
      $admin_name = mysqli_real_escape_string($conn, $_POST["admin_name"]);
      $admin_pass1 = mysqli_real_escape_string($conn, $_POST["admin_password1"]);
      $admin_pass2 = mysqli_real_escape_string($conn, $_POST["admin_password2"]);
      if($admin_pass1 == $admin_pass2) {
        //create table now
        $sql = 'CREATE TABLE admins (
          id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          username VARCHAR(30) NOT NULL,
          password VARCHAR(30) NOT NULL,
          reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        CREATE TABLE categories (
          id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          datetime VARCHAR(50) NOT NULL, 
          name VARCHAR(100) NOT NULL,
          creatorname VARCHAR(200) NOT NULL,
          UNIQUE (name)
        );
        CREATE TABLE posts (
          id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          datetime VARCHAR(50) NOT NULL, 
          title VARCHAR(500) NOT NULL,
          wikilink VARCHAR(500) NULL UNIQUE,
          titlerepeat INT(5),
          creatorname VARCHAR(50) NOT NULL,  
          image VARCHAR(500) NOT NULL,  
          content VARCHAR(10000000),
          categoryname VARCHAR(100) NOT NULL,          
          FOREIGN KEY (categoryname) REFERENCES categories(name)
        );';  
        $result = $conn->multi_query($sql);
        if($result) {
          $conn->next_result(); //flush second $sql result
          $conn->next_result(); //flush third $sql result          
          $stmt = $conn->prepare("INSERT INTO `admins` (username, password) VALUES (?, ?)");
          var_dump($conn->error);
          $stmt->bind_param("ss", $admin_name, $admin_pass1);
          if($stmt->execute()) { //create table admins with input data     
          //super user has been created 
            echo "super user create | db created | reload this page again";            
            header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
            exit();
          } 
          else {
            die("Error: Failed to insert superuser name and password into database table" . $stmt->error);
          }
        }
        else { //if $result == 0
          die("failed to create tables in database" . $conn->error);
        }
      }      
      else {
        echo "Passwords did not match";
      }
    }
    else if($identifier == 'register'){
      echo "All fields must be filled";
    }  
  }
}

else {

  //use setupe website form then
  if($identifier == "setup" && !empty($_POST["username"]) && !empty($_POST["db_name"]) && !empty($_POST["password1"]) ) {
    $username   = filter_var($_POST["username"], FILTER_SANITIZE_STRING);    
    $password1  = filter_var($_POST["password1"], FILTER_SANITIZE_STRING);
    
    
      
      $conn = new mysqli("localhost", $username, $password1);
      $db_name = mysqli_real_escape_string($conn, $_POST["db_name"]);
      if ($conn->connect_error) {
        echo "Wrong database name or/and password";              
      }
      else {
        $config_file = fopen("include/config.php", "w");
        $db_credentials = <<<EOB
        <?php
        const DB_USER_NAME = '$username';
        const DB_NAME = '$db_name';
        const DB_PASSWORD = '$password1'; 
        const SETUP = true; //checks if databases have been setup or not
        ?>
        EOB;
        fwrite($config_file, $db_credentials);
        fclose($config_file);        
        $conn->query("CREATE DATABASE IF NOT EXISTS $db_name");        
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
        exit();
      }
   
  }
  else if ($identifier == "setup"){
    echo "ERROR: All fields must be non-empty";
  }
}

?>


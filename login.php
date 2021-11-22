<?php
// Start the session
session_start();
$error = "";
require_once('config.php');

$conn = new mysqli($_SERVER['SERVER_NAME'], $username, $password);
    if ($conn->connect_error) {
      echo "error";
      die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully1";

    //check if signed_up is true in database  
    //else allow user registeration  
    //if already registered show login page straight up


if( !empty($_POST["username"]) && !empty($_POST["password1"]) && !empty($_POST["password2"]) ) {

  echo $_POST["username"];

  $username = htmlspecialchars($_POST["username"]);
  $password1 = htmlspecialchars($_POST["password1"]);
  $password2 = htmlspecialchars($_POST["password2"]);
  if($password1 == $password2) {
    $password = $password1;
    $conn = new mysqli($_SERVER['SERVER_NAME'], $username, $password);
    if ($conn->connect_error) {
      echo "error";
      die("Connection failed: " . $conn->connect_error);
    }
    echo "Connected successfully";
  }
  else {
    $error = '<div class="alert alert-danger" role="alert">
    This is a danger alert—check it out!
    </div>';
    echo $error;
  }
}



if(!isset($_GLOBALS["signed_up"])) {

  $page_title = "Sign Up";
}
else {
  $page_title = "Login";
}


?>





<!doctype html>
  <html class="no-js login" lang="">

  <head>
    <meta charset="utf-8">
    <title>
      <?php echo $page_title; ?>
    </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:title" content="">
    <meta property="og:type" content="">
    <meta property="og:url" content="">
    <meta property="og:image" content="">
    <link rel="manifest" href="site.webmanifest">

    <link rel="icon" type="image/png" href="assets/img/logo_single.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/css/bootstrap.css" integrity="sha512-Ty5JVU2Gi9x9IdqyHN0ykhPakXQuXgGY5ZzmhgZJapf3CpmQgbuhGxmI4tsc8YaXM+kibfrZ+CNX4fur14XNRg==" crossorigin="anonymous" referrerpolicy="no-referrer" />  
    <link rel="stylesheet" href="assets/css/admin.css">

    <meta name="theme-color" content="#fafafa">
  </head>

  <body>

    <div class="main-container container-fluid d-flex flex-column justify-content-between">
      <header>
        <div class="logo-grand-parent d-flex flex-column w-auto">
          <a class="logo-container text-center" href="#!">
            <img class="img-fluid logo" src="assets/img/logo_single.svg" alt="logo">
          </a>
          <h1 class="text-center title font-weight-lighter h4 mt-2 mb-0">CrimeWiki</h1>
        </div>
      </header>
      
      <main>
        <div class="card my-4 mx-auto">
          <div class="card-body p-3 p-sm-4">
            <h5 class="card-title text-center mb-3 mb-sm-4 font-weight-lighter">
              <?php echo $page_title ?>
            </h5>
            <form method="post" action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>>
              <div class="form-group mb-3">
                <label for="username">Database User Name</label>
                <input name="username" type="text" class="form-control" id="username" placeholder="crimewiki">                
              </div>
              <div class="form-group mb-3">
                <label for="password1">Database Password</label>
                <input name="password1" type="password" class="form-control" id="password1" placeholder="">                
              </div>
              <div class="form-group mb-3">
                <label for="floatingPassword">Confirm Password</label>
                <input name="password2" type="password2" class="form-control" id="password2" placeholder="Password">    
              </div>

              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="" id="rememberPasswordCheck">
                <label class="form-check-label" for="rememberPasswordCheck">
                  Remember password
                </label>
              </div>
              
              <button class="submit btn btn-login text-white" type="submit">
                Submit
              </button>
            </form>
          </div>
        </div>
      </main>

      <footer class="py-3">
        <p class="text-center copy m-0">
          Designed and developed by <a class="author" href="https://au.linkedin.com/in/anupamkhosla">Anupam Khosla</a> | All rights reserved
        </p>
      </footer>
    </div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.1/js/bootstrap.bundle.js" integrity="sha512-pn67a0tE9Gl7wyvnZRE8MvKmVDtXYtc/gMaVqNrg5JdlbxSQTI5oXR+Px45Kwloegn/JSPJnY4fR39ml20pI6w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="assets/js/main.js"></script>
    <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
    <script>
      window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
      ga('create', 'UA-XXXXX-Y', 'auto'); ga('set', 'anonymizeIp', true); ga('set', 'transport', 'beacon'); ga('send', 'pageview')
    </script>
    <script src="https://www.google-analytics.com/analytics.js" async></script>
  </body>
  </html>

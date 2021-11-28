<?php 
require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/setup.php");
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

            <!-- Setup database form -->
            <form method="post" class="<?php echo $db_form_vis ?>" 
              action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> >
              <div class="form-group mb-3">
                <label for="username">Database User Name</label>
                <input name="username" type="text" class="form-control" id="username" placeholder="crimewiki" required>                
              </div>
              <div class="form-group mb-3">
                <label for="db_name">Set Database Name</label>
                <input name="db_name" type="text" class="form-control" id="db_name" placeholder="Database Name" required>                
              </div>
              <div class="form-group mb-3">
                <label for="password1">Database User Password</label>
                <input name="password1" type="password" class="form-control" id="password1" placeholder="" required>                
              </div>
              
              <button class="submit btn btn-login text-white" type="submit" name="identifier" value="setup">
                Submit
              </button>
            </form>

            <!-- Register first time user form -->
            <form method="post" class="<?php echo $rg_form_vis ?>" 
              action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>>
              <div class="form-group mb-3">
                <label for="admin_name">Admin User Name</label>
                <input name="admin_name" type="text" class="form-control" id="admin_name" placeholder="crimewiki" required>                
              </div>
              <div class="form-group mb-3">
                <label for="admin_password1">Admin Password</label>
                <input name="admin_password1" type="password" class="form-control" id="admin_password1" placeholder="" required>                
              </div>
              <div class="form-group mb-3">
                <label for="admin_password2">Admin Password</label>
                <input name="admin_password2" type="password" class="form-control" id="admin_password2" placeholder="Password" required>    
              </div>                 
              <button class="submit btn btn-login text-white" type="submit" name="identifier" value="register">
                Submit
              </button>
            </form>

            <!-- Login form --> 
            <form method="post" class="<?php echo $lg_form_vis ?>" 
              action=<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>>
              <div class="form-group mb-3">
                <label for="login_name">Admin User Name</label>
                <input name="login_name" type="text" class="form-control" id="login_name" placeholder="crimewiki" required>                
              </div>
              <div class="form-group mb-3">
                <label for="login_password">Admin Password</label>
                <input name="login_password" type="password" class="form-control" id="login_password" placeholder="" required>                
              </div>              

              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" value="" id="rememberPasswordCheck">
                <label class="form-check-label" for="rememberPasswordCheck">
                  Remember password
                </label>
              </div>
              
              <button class="submit btn btn-login text-white" type="submit" name="identifier" value="login">
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

<?php

require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/check_login.php");
require_once("include/category_code.php");

?>


<!doctype html>
  <html class="no-js" lang="">

  <head>
    <meta charset="utf-8">
    <title>Categories</title>
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

    <div class="container-fluid">
      <div class="row">

        <div class="col-md-3 col-lg-2 sidebar p-0">
          <div class="logo-grand-parent d-flex justify-content-center w-auto">
            <a class="logo-container" href="#!">
              <img class="img-fluid logo" src="assets/img/logo_single.svg" alt="logo">
            </a>
          </div>
          <hr class="cut-top cut my-0">
          <hr class="cut-bottom cut my-0">
          <ul class="nav flex-column nav-pills">
            <li class="nav-item">
              <a class="nav-link" href="dashboard.php">
                <img class="icon mb-n1px" src="assets/icons/dashboard_alt.svg" alt="dashboard icon">
                Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="addpost.php">
                <img class="icon" src="assets/icons/add_post.svg" alt="add post icon">               
                Add Post
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="categories.php">
                <img class="icon mb-n1px" src="assets/icons/category.svg" alt="category icon"> 
                Categories
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <img class="icon mb-n1px" src="assets/icons/admin.svg" alt="category icon"> 
                Manage Admins
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <img class="icon mb-n1px" src="assets/icons/comment.svg" alt="category icon"> 
                Comments
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <img class="icon mb-n1px" src="assets/icons/eye.svg" alt="category icon"> 
                Live Blog
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#">
                <img class="icon mb-n1px" src="assets/icons/sign_out.svg" alt="category icon"> 
                Log Out
              </a>
            </li>
          </ul>
        </div>

        <div class="col-md-9 col-lg-10  content d-flex justify-content-between flex-column">
          <main class="pb-5">
            <h1 class="text-center title text-pm font-weight-lighter h4">CrimeWiki Admin Panel</h1>
            <hr>
            <h2 class="text-pm h5 text-center my-4">Manage Categories</h2>

            <form method="post" action=<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?> >
              <div class="form-row">
                <div class="col-md-8 col-xl-10">
                  <input name="category_input" type="text" class="form-control <?php echo validation_status(); ?> " placeholder="Category Name" >
                  <div  class=" <?php echo validation_class(); ?> ">
                    <?php echo validation_txt(); ?>
                  </div>
                </div>
                
                <div class="col-md-4 col-xl-2">
                  <button class="submit btn btn-login text-white w-100 mt-3 mt-md-0 " type="submit" name="identifier" value="category_form">
                    Add Category
                  </button>
                </div>
              </div>
            </form>

            <table class="table table-responsive-sm table-bordered bg-white table-hover my-3">
              <thead>
                <tr>
                  <th scope="col">Sr no.</th>
                  <th scope="col">Category Name</th>
                  <th scope="col">Creator Name</th>                  
                  <th scope="col">Date & Time</th>
                </tr>
              </thead>
              <tbody> <?php echo $category_table_content ?> </tbody>
            </table>

          </main>
          <?php require_once("include/footer.php") ?>
        </div>
      </div>
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
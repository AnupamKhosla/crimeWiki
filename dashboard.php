<?php 
require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/check_login.php");
require_once("include/dashboard_code.php");
?>

<!doctype html>
  <html class="no-js" lang="">

  <head>
    <meta charset="utf-8">
    <title>Dashboard</title>
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
              <a class="nav-link active" href="dashboard.php">
                <img class="icon mb-n1px" src="assets/icons/dashboard_alt.svg" alt="dashboard icon">
                Dashboard
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="addpost.php">
                <img class="icon " src="assets/icons/add_post.svg" alt="add post icon">               
                Add Post
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="categories.php">
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

        <div class="col-md-9 col-lg-10 content d-flex justify-content-between flex-column">
          <main class="pb-5">
            <h1 class="text-center text-pm font-weight-lighter h4">CrimeWiki Admin Panel</h1>
            <hr>
            <h2 class="text-pm h5 text-center my-4">Homepage content</h2>
            <form class="dashboard-form" action=" <?php htmlspecialchars($_SERVER['REQUEST_URI']); ?> ">
              <div class="form-row">  
                <div class="col-12 about-text">
                  <div class="input-group-prepend float-left mr-3">
                    <div class="input-group-text">
                      <input type="checkbox" aria-label="Checkbox for following textarea">
                    </div>
                  </div>
                  <h3 class="h6">
                    <label class="d-block mb-3" for="about" >Set About The CrimeWiki text</label>
                  </h3>
                  <textarea readonly required name="about" id="about" class="form-control mb-2"  rows="4">
                    <?php echo $blog_about_text; ?>
                  </textarea>
                </div>
                <div class="col-12 mt-3 month-post">
                  <div class="input-group-prepend float-left mr-3">
                    <div class="input-group-text">
                      <input type="checkbox" aria-label="Checkbox for following textarea">
                    </div>
                  </div>
                  <h3 class="h6 mb-2">Set Crime of the month post</h3>
                  <div class="row align-items-end pt-1">
                    <div class="col-12 mb-2">
                      <label for="post_title" class="d-block font-weight-normal">Post title</label>
                      <input required name="post_title" type="text" id="post_title" class="form-control"  value="$post_title">
                    </div>
                    <div class="col-md-12 col-lg-auto mb-2 mb-lg-0 flex-grow-1">
                      <label for="video_link" class="d-block font-weight-normal">Video link</label>
                      <input required name="video_link" type="url" id="video_link" class="form-control"  value="$video_link">
                    </div>
                    <div class="col-lg-2 col-auto flex-grow-1 title-repeat-container">
                      <label for="title_repeat" class="d-block font-weight-normal">Title Repeat</label>
                      <input type="number" class="form-control" id="title_repeat" value="$title_repeat">
                    </div>
                    <div class="col-auto">                      
                      <button type="submit" class="submit btn btn-login text-white px-5 m-0 ml-lg-0 ml-n3">Submit</button> 
                    </div>
                  </div>
                </div>
              </div>
            </form>
            
            <hr class="my-4">
            <h2 class="h5 text-pm text-center" >Latest Posts</h2>
            <table class="table table-responsive-sm table-bordered bg-white table-hover my-3">
              <thead>
                <tr>
                  <th scope="col">Sr no.</th>
                  <th scope="col">Post Title</th>
                  <th scope="col">Category</th>                               
                  <th scope="col">Date & Time</th>
                  <th scope="col">Creator Name</th>     
                </tr>
              </thead>
              <tbody> <?php echo $posts_table_content ?> </tbody>
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

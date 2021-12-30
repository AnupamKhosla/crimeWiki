<?php
ini_set('max_execution_time', '600'); //600 seconds = 10 minutes 
require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/check_login.php");
require_once("include/wikipedea_code.php");

?>


<!doctype html>
  <html class="no-js" lang="">

  <head>
    <meta charset="utf-8">
    <title>Wikipedea Scraper</title>
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
              <a class="nav-link active" href="addpost.php">
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

        <div class="col-md-9 col-lg-10  content d-flex justify-content-between flex-column">
          <main class="pb-5">
            <h1 class="text-center title text-pm font-weight-lighter h4">CrimeWiki Admin Panel</h1>
            <hr>
            <h2 class="text-pm h5 text-center my-4">Copy Wikipedea page(s)</h2>

            <section class="response <?php echo $_SESSION["Response"]["display"]; ?> ">
              <div class="card text-white bg-success">
                <div class="card-header"> Database response </div>    
                <div class="card-body">
                  <h5 class="card-title">Time taken: <?php echo $_SESSION["Response"]["time"]; ?> Seconds</p>
                  <h6 class="card-title">Total Links Requested: <?php echo $_SESSION["Response"]["total_links"]; ?> </h6>

                  <h6 class="card-title">Repeated links: <?php echo count($_SESSION["Response"]["repeat_links"]); ?> </h6>
                  <p class="repeat-links"> <?php print_r(implode("<br>", $_SESSION["Response"]["repeat_links"])); ?>  </p>
                  
                  <h6 class="card-title">Invalid links: <?php echo count($_SESSION["Response"]["invalid_links"]); ?> </h6>
                  <p class="invalid-links"> <?php print_r(implode("<br>", $_SESSION["Response"]["invalid_links"])); ?> </p>
                </div>
                </div>
                <hr>              
            </section>

            <form method="post" action=<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?> enctype="multipart/form-data">
              <div class="form-row">
                
                <div class="col-12 mt-3">  
                  <div class="row">
                    
                    <div class="col-12 col-sm-7 input-group mt-3 mt-sm-0">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="category_select">Category</label>
                      </div>
                      <select required name="category_select" class="custom-select" id="category_select">
                        <option selected disabled value="">Choose...</option>
                        <?php echo category_select(); ?>                        
                      </select>  
                    </div> 
                  </div>
                </div>  
                <div class="col-12 mt-3">
                  <label for="wiki_links">Enter all wikipedea links seperated by newlines (Do not use Mobile pages like en.m.wikipedia.org/wiki/Main_Page)</label>
                  <textarea name="wiki_links" id="wiki_links" class="form-control"  rows="10">
                    https://en.wikipedia.org/wiki/Ajmal_Kasab
                    https://en.wikipedia.org/wiki/David_Headley
                    https://en.wikipedia.org/wiki/The_Weeknd
                  </textarea>
                </div>
                
                 <div class="col-12 mt-3">
                  <button disabled class="submit btn btn-login text-white px-5" type="submit" name="identifier" value="wikipedea_form" data-toggle="modal">
                    Add Post
                  </button>
                </div>
              </div>
            </form>  
            <div class="modal fade" id="modal_sure" tabindex="-1" aria-labelledby="sure_heading" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="sure_heading">Are You Sure?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body input-group">                    
                    <label class="d-flex align-items-center m-0 mr-3" for="captcha_sure" id="captcha_sure_label"><strong>2 + 3 = </strong></label>
                    <input type="text" class="form-control" id="captcha_sure_input" aria-describedby="basic-addon3">
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button disabled id="sure_submit" class=" btn btn-pm" type="button" >Submit</button>
                  </div>
                </div>
              </div>
            </div>      

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

    <?php reset_response(); ?>

  </body>
  </html>



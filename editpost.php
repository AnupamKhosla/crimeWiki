<?php 
require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/check_login.php");
require_once("include/editpost_code.php");
?>

<!doctype html>
  <html class="no-js dashboard" lang="">

  <head>
    <meta charset="utf-8">
    <title>Posts</title>
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
        <?php require_once("include/sidebar_dashboard.php"); ?>

        <div class="col-md-9 col-lg-10 content d-flex justify-content-between flex-column">
          <main class="pb-5">
            <h1 class="text-center text-pm font-weight-lighter h4">CrimeWiki Admin Panel</h1>
            <hr>
            <h2 class="text-pm h5 text-center my-4">Show All Posts</h2>
            
            <form method="get" class="editpost-form" action=" <?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?> ">
              <div class="form-row"> 
                
                <div class="col-12 mt-3 month-post">
                  <div class="input-group-prepend float-left mr-3">
                    <div class="input-group-text">
                      <input type="checkbox" name="check_post" aria-label="Checkbox for following textarea">
                    </div>
                  </div>
                  <h3 class="h6 mb-2">Filter Posts</h3>
                  <div class="row fields align-items-end pt-1">
                    <div class="col-12 mb-2">
                      <label for="post_title" class="d-block font-weight-normal">Post title</label>
                      <input disabled required name="post_title" type="text" id="post_title" class="form-control"  value="<?php echo $post_title; ?>">
                    </div>
                    <div class="col-md-12 col-lg-auto mb-2 mb-lg-0 flex-grow-1">
                      <label for="category" class="d-block font-weight-normal">Category</label>
                      <input disabled required name="category" type="text" class="form-control"  value="<?php echo $category; ?>">
                    </div>
                    <div class="pl-0 col-lg-2 col-auto flex-grow-1 title-repeat-container">
                      <label for="title_repeat" class="d-block font-weight-normal">Title Repeat</label>
                      <input disabled type="text" name="title_repeat" class="form-control" id="title_repeat" value="<?php echo $title_repeat; ?>">
                    </div>
                    <div class="col-auto pl-0">                      
                      <button disabled type="submit" name="identifier" value="dashboard_form" class="sure submit btn btn-login text-white px-5 m-0">Filter</button> 
                    </div>
                  </div>
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
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
                    <button disabled id="sure_submit" class=" btn btn-pm px-4" type="button" >Submit</button>
                  </div>
                </div>
              </div>
            </div>   

            
            <hr class="my-4">
            <h2 class="h5 text-pm text-center" >Latest Posts</h2>
            <table class="table table-responsive-sm table-bordered bg-white table-hover my-3">
              <thead>
                <tr>
                  <th scope="col">Sr no.</th>
                  <th scope="col">Id</th>
                  <th scope="col">Post Title</th>
                  <th scope="col">Category</th>                               
                  <th scope="col">Date & Time</th>
                  <th scope="col">Title Repeat</th>     
                </tr>
              </thead>
              <tbody> <?php echo $posts_table_content ?> </tbody>
            </table>

          </main>
          <?php require_once("include/footer_dashboard.php") ?>
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
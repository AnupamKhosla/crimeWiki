<?php
require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/check_login.php");
require_once("include/addpost_code.php");
?>

<!doctype html>
  <html class="no-js" lang="">

  <head>
    <meta charset="utf-8">
    <title>Add Post</title>
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

        <div class="col-md-9 col-lg-10  content d-flex justify-content-between flex-column">
          <main class="pb-5">
            <h1 class="text-center title text-pm font-weight-lighter h4">CrimeWiki Admin Panel</h1>
            <hr>
            <h2 class="text-pm h5 text-center my-4">Add New Post</h2>

            <form method="post" action=<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?> enctype="multipart/form-data">
              <div class="form-row">
                <div class="col-12">
                  <label for="post_title">Post title</label>
                  <input required name="post_title" type="text" id="post_title" class="form-control <?php echo validation_status(); ?> " placeholder="Post Title" >
                  <div  class=" <?php echo validation_class(); ?> ">
                    <?php echo validation_txt(); ?>
                  </div>
                </div> 
                <div class="col-12 mt-3">  
                  <div class="row">
                    <div class="col-12 col-sm-5 d-flex align-items-center">
                      <input required name="choose_image" type="file" accept="image/*" id="choose_image" class="form-control-file choose-image" >  
                    </div>
                    <div class="col-12 col-sm-7 input-group mt-3 mt-sm-0">
                      <div class="input-group-prepend">
                        <label class="input-group-text" for="category_select">Category</label>
                      </div>
                      <select required name="category_select" class="custom-select" id="category_select">
                        <option selected disabled value="" >Choose...</option>
                        <?php echo category_select(); ?>                        
                      </select>  
                    </div> 
                  </div>
                </div>  
                <div class="col-12 mt-3">
                  <label for="intro_meta">Intro Meta</label>
                  <textarea required name="intro_meta" id="intro_meta" class="form-control"  rows="10">
                    <intro-data>
                      <tr>
                        <th>Crimes</th>
                        <td>34</td>  
                      </tr>
                      <tr>
                        <th>Born On</th>
                        <td>March 10, 1957</td>
                      </tr>
                      <tr>
                        <th>Died On</th>
                        <td>March 15, 2007</td>
                      </tr>
                      <tr>
                        <th>Known For</th>
                        <td>9/11 Attack</td>
                      </tr>
                      <tr>
                        <th>Jail Time</th>
                        <td>None</td>
                      </tr>
                    </intro-data>
                  </textarea>
                </div>
                <div class="col-12 mt-3">
                  <label for="details_meta">Details Meta</label>
                  <textarea required name="details_meta" id="details_meta" class="form-control"  rows="10">                    
                    <details>
                      <tr>
                        <th>Criminal Status</th>
                        <td>Executed</td>
                      </tr>
                      <tr>
                        <th>Victims</th>
                        <td>4</td>
                      </tr>
                    </details>
                  </textarea>
                </div>
                <div class="col-12 mt-3">
                  <label for="sources_meta">Sources Meta</label>
                  <textarea required name="sources_meta" id="sources_meta" class="form-control"  rows="10">                   
                    <sources>
                      <ul class="list">
                        <li><a href="#">News Article</a> on <a href="chanel7.com">chanel7.com</a></li>
                        <li><a href="#">Video tape</a> on <a href="chanel7.com">youtube.com</a></li>
                        <li><a href="#">News Pulished</a> on <a href="aajtak.com">aajtak.com</a></li>                        
                      </ul>
                    </sources>
                  </textarea>
                </div>  
                <div class="col-12 mt-3">
                  <label for="sources_meta">Related Meta</label>
                  <textarea required name="related_meta" id="related_meta" class="form-control"  rows="10">                   
                    <related>
                      <ol class="list list-unstyled">
                        <li><a href="/posts?id=23">Robel Puch</a></li>
                        <li><a href="/posts?id=67">Paula Danier</a></li>
                        <li><a href="/posts?id=23">Kanthelin freeman</a></li>
                        <li><a href="/posts?id=23">Leonard fraser</a></li>
                      </ol>
                    </related>
                  </textarea>
                </div>

                <div class="col-12 mt-3">
                  <label for="post_content">Post Content</label>
                  <textarea required name="post_content" id="post_content" class="form-control"  rows="10">
                    <content>
                      <section>
                        <h2>Dummy heading</h2>
                        <p>Dummy paragaph</p>
                        <p>Dummy paragraph 2</p>
                      </section>
                      <hr></hr>
                      <section>
                        <h2>Section2 heading</h2>
                        <p>Section2 pargraph1</p>
                          <h3>Subheading 1 </h3>
                          <p>Sub paragraph1</p>
                          <h3>Subheading2</h3>
                          <p>Sub para 3</p>
                      </section>
                    </content>
                  </textarea>
                </div>
                 <div class="col-12 mt-3">
                  <button class="sure submit btn btn-login text-white px-5" type="submit" name="identifier" value="add_post_form">
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
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Close</button>
                    <button disabled id="sure_submit" class=" btn btn-pm px-4" type="button" >Submit</button>
                  </div>
                </div>
              </div>
            </div>            

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
  </body>
  </html>

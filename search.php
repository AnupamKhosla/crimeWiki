<?php 
require_once('include/config.php');
require_once('include/functions.php');
require_once('include/search_code.php')
?>
<!doctype html>
  <html class="no-js searchpage" lang="">

  <head>
    <meta charset="utf-8">
    <title>crimeWiki | Wikipedea of crime</title>
    <meta name="description" content="A Wikipedea of world-wide crime">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:title" content="">
    <meta property="og:type" content="">
    <meta property="og:url" content="">
    <meta property="og:image" content="">

    <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" href="icon.png">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.css" >
    <link rel="stylesheet" href="../assets/css/selectric.css">
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <!-- Add the slick-theme.css if you want default styling -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

    <link rel="stylesheet" href="../assets/css/style.css">
    <meta name="theme-color" content="#E92222">
  </head>

  <body>

    <section class="hero text-white">
      <div class="container">
        
        <?php require_once("include/nav.php") ?>

        <form action="" class="filters mb-3">  
          <div class="row custom-container m-auto">
            <div class="col-lg-5 col-md-12 d-flex">
              <div class="input-group mb-3">
                <div class="input-group-prepend advance-prepend">
                  <div class="input-group-text advance">
                    <input type="checkbox" aria-label="Checkbox for following text input">
                    <span class="ml-2">Advance Search</span>
                  </div>
                </div>
                <input name="title" type="text" class="form-control title" placeholder="Type here" aria-label="Text input with checkbox">
              </div>
              
            </div>
            <div class="col-sm-6 col-xs-12 offset-sm-3 d-md-none">
              <button type="button" class="go sort btn text-white d-flex align-items-center justify-content-center w-100">
                Sort By 
                <img class="ml-2 " src="../assets/icons/Sort_down.svg" alt="sort down icon">
              </button>
            </div>
            <div class="w-100 d-md-none"></div>
            <div class="col ">
              <div class="row sort-dropdown d-none d-md-flex">
                <div class="col-sm-6 offset-sm-3 col-md-3 offset-md-2 col-lg-5 offset-lg-0 pl-lg-0 mt-3 mt-md-0">
                  <select  class="w-100" name="category">
                    <option value="">Category</option>
                    <option value="criminals">Criminal</option>
                    <option value="crime_organization">Gang</option>
                    <option value="blog">Crime</option>
                  </select>
                </div>    
                <div class="col-sm-6 offset-sm-3 col-md-3 offset-md-0 col-lg-5 pl-lg-0 mt-3 mt-md-0">
                  <select class="w-100" name="filter">
                    <option value="">Sort By</option>
                    <option value="saab">Latest</option>
                    <option value="opel">Popular</option>
                    <option value="audi">Country</option>
                  </select>
                </div>
                <div class="col-sm-6 offset-sm-3 offset-md-0 col-lg-2 col-md-2 pl-lg-0 mt-3 mt-md-0">
                  <button class="go btn text-white d-flex align-items-center justify-content-center w-100">
                    Go 
                    <img class="ml-2 arrow-right" src="../assets/icons/arrow_right.svg" alt="arrow right icon">
                  </button>
                </div>
              </div>
            </div>
          </div>
        </form>        
      </div>
    </section>

    <section class="about results"> 
      <div class="container">
        <h2 class="h5 text-pm text-center mb-5">Result</h2>
        
        <?php echo $posts; ?>
                
      </div>
    </section>    

   <?php require_once("include/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.js"></script>
    <script src="../assets/js/jquery.selectric.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="../assets/js/main.js"></script>

    <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
    <script>
      window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
      ga('create', 'UA-XXXXX-Y', 'auto'); ga('set', 'anonymizeIp', true); ga('set', 'transport', 'beacon'); ga('send', 'pageview')
    </script>
    <script src="https://www.google-analytics.com/analytics.js" async></script>
  </body>
  </html>

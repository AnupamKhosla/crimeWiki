<?php 
require_once('include/config.php');
require_once('include/functions.php');
require_once('include/index_code.php');
?>
<!doctype html>
  <html class="no-js homepage" lang="">

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
    <link rel="icon" type="image/x-icon" href="logo_single.svg">
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
        <h1 class="main-title h3 font-weight-light w-100 text-center mt-2">A Wikipedea of world-wide crime</h1>
        <!--
        <form action="" class="filters">  
          <div class="row custom-container m-auto">
            <div class="col-lg-5 col-md-12 d-flex">
              <h1 class="main-title h3 font-weight-light w-100 text-center text-lg-left">A Wikipedea of world-wide crime</h1>
            </div>
            <div class="col-sm-6 col-xs-12 offset-sm-3 d-md-none">
              <button type="button" class="go sort btn text-white d-flex align-items-center justify-content-center w-100">
                Sort By 
                <img class="ml-2 " src="../assets/icons/Sort_down.svg" alt="sort down icon">
              </button>
            </div>
            <div class="w-100 d-md-none"></div>
            <div class="col pl-lg-0">
              <div class="row sort-dropdown d-none d-md-flex">
                <div class="col-sm-6 offset-sm-3 col-md-3 offset-md-2 col-lg-5 offset-lg-0 pl-lg-0 mt-3 mt-md-0">
                  <select class="w-100" name="Choose_Catgory">
                    <option value="">Category</option>
                    <option value="saab">Criminal</option>
                    <option value="opel">Gang</option>
                    <option value="audi">Crime</option>
                  </select>
                </div>    
                <div class="col-sm-6 offset-sm-3 col-md-3 offset-md-0 col-lg-5 pl-lg-0 mt-3 mt-md-0">
                  <select class="w-100" name="filter_by">
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
      -->
        <div class="slider row ">
          <div class="col-md-10 offset-md-1">
            <div class="slick">                       
              <?php echo $slides; ?>
            </div>        
          </div>
        </div>
        
      </div>
    </section>

    <section class="about">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-3 offset-lg-1">
            <img class="logo-double img-fluid d-none d-lg-block m-auto" src="../assets/img/logo_gun.png" alt="Logo double gun">
          </div>
          <div class="col-lg-7 about-text pl-lg-5">
            <h1 class="logo-text text-center font-weight-normal">The CrimeWiki</h1>
            <img class="logo-double img-fluid d-block d-lg-none m-auto" src="../assets/img/logo_gun.png" alt="Logo double gun">            
            <?php echo $blog_about_text; ?>             
            <div class="d-flex justify-content-center mt-auto">
              <a href="https://github.com/AnupamKhosla/crimeWiki" type="button" class="btn btn-pm d-inline-flex align-items-center cta mx-auto">Github Repo</a>       
            </div>
            
          </div>
        </div>
      </div>
    </section>

    <section class="month">
      <div class="container">
        <h2 class="month-heading text-center text-pm h3 font-weight-normal">Crime of the Month</h2>
        <h3 class="post-title text-center h5"> <?php echo $title; ?> </h3>
        <div class="embed-responsive embed-responsive-16by9 youtube">
          <iframe loading="lazy" class="embed-responsive-item" src=" <?php echo $video_link; ?> " allowfullscreen></iframe>
        </div>

        <div class="row post-content">
          <div class="col-xl-8 col-lg-7 post-intro">
            <div class="wrapper2">
              <?php echo $introduction; ?>
            </div>
            <div class="d-flex justify-content-center mt-auto">
              <a href="<?php echo $blog_month_href; ?>" type="button" class="btn btn-pm m-auto2 mt-1 d-inline-flex align-items-center">See Details</a>
            </div>
            
          </div>
          <div class="col-xl-4 col-lg-5 wrapper">
            <div class="post-sources panel d-flex flex-column">
              <div class="panel-title text-center">
                <h4 class="m-0">Sources</h4>
              </div>
              <div class="panel-content flex-grow-1">
                <?php echo $sources; ?>
              </div>
              <span class="publish-date"> Published on <?php echo date( 'd/m/Y H:i:s', htmlspecialchars($publish_date) )?></span>
            </div>
           
          </div>
        </div>
        
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

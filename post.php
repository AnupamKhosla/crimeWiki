<?php 
require_once("include/sessions.php");
require_once('include/config.php');
require_once('include/functions.php');
require_once("include/post_code.php");
?>

<!doctype html>
  <html class="no-js" lang="">
  <head>
    <meta charset="utf-8">
    <title>
      <?php echo ($title); ?>
    </title>
    
    <meta name="description" content=" <?php echo $title; ?> ">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta property="og:title" content="">
    <meta property="og:type" content="">
    <meta property="og:url" content="">
    <meta property="og:image" content="">

    <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" href="/logo_single.svg">
    <link rel="icon" type="image/png" href="/assets/img/logo_single.svg">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.css" >
    <link rel="stylesheet" href="/assets/css/selectric.css">
    <!-- Add the slick-theme.css if you want default styling -->    

    <link rel="stylesheet" href="/assets/css/style.css">
    <meta name="theme-color" content="#E92222">
  </head>

  <body>

    <section class="hero text-white">
      <div class="container">
       <?php require_once("include/nav.php"); ?>
      </div>
    </section>

    <div class="spacer"></div>

    <section class="post">
      <div class="container container-original">
        <div class="row offset-y">
          <div class="col-lg-5 col-xl-4">
            <div class="intro-table text-white d-block d-lg-none">
                <h1 class="h3 font-weight-normal text-center"> <?php echo ($title); ?> </h1>
                <div class="table-wrapper custom-scrollbar">
                  <table>
                    <tbody>
                      <?php echo ($introData); ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="card-container">
                <div class="card post-profile ">
                <img src="<?php echo image_path($image) ?>" class="card-img-top post-pic" alt="profile pic">
                <div class="card-body">                
                  <a href="#" class=""> <?php echo ($title); ?> </a>
                </div>
              </div>
              </div>
              <div class="panel details">
                <h4 class="panel-title text-center text-pm">
                  Details
                </h4>
                <div class="panel-content ">
                  <table class="table-responsive">                    
                      <?php echo ($details); ?>                    
                  </table>
                </div>
              </div>
              <div class="panel related d-none d-lg-block">
                <div class="panel-title text-center">
                  <h4>Related</h4>
                </div>
                <div class="panel-content ">
                  <?php echo ($related); ?>
                </div>
              </div>
              <div class="panel sources d-none d-lg-block">
                <div class="panel-title text-center">
                  <h4>Sources</h4>
                </div>
                <div class="panel-content ">
                  <?php echo ($sources); ?>
                </div>
              </div>
            </div>
            <div class="col-lg-7 col-xl-8 post-content">
              <div class="intro-table text-white d-none d-lg-block">
                <h1 class="h3 font-weight-normal"> <?php echo ($title); ?></h1>
                <div class="table-wrapper custom-scrollbar">
                  <table>
                  <?php echo ($introData); ?>
                </table>
                </div>
              </div>
              <?php echo ($content2); ?>
              <!-- after dynamic sections repipitive dom elements because of masonary layout. No css-only solution found-->
              <div class="panel related d-lg-none">
                <div class="panel-title text-center">
                  <h4>Related</h4>
                </div>
                <div class="panel-content ">
                  <?php echo ($related); ?>
                </div>
              </div>
              <div class="panel sources d-lg-none">
                <div class="panel-title text-center">
                  <h4>Sources</h4>
                </div>
                <div class="panel-content ">
                  <?php echo ($sources); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <?php require_once("include/footer.php"); ?>

      <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.js"></script>
      <script src="/assets/js/jquery.selectric.js"></script>
      <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
      <script src="/assets/js/main.js"></script>

      <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
      <script>
        window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
        ga('create', 'UA-XXXXX-Y', 'auto'); ga('set', 'anonymizeIp', true); ga('set', 'transport', 'beacon'); ga('send', 'pageview')
      </script>
      <script src="https://www.google-analytics.com/analytics.js" async></script>
    </body>
    </html>

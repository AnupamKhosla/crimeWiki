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
    <link rel="apple-touch-icon" href="icon.png">
    <!-- Place favicon.ico in the root directory -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.css" >
    <link rel="stylesheet" href="../assets/css/selectric.css">
    <!-- Add the slick-theme.css if you want default styling -->    

    <link rel="stylesheet" href="../assets/css/style.css">
    <meta name="theme-color" content="#E92222">
  </head>

  <body>

    <section class="hero text-white">
      <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark align-items-start">
          <a class="navbar-brand" href="#">
            <img src="../assets/img/logo_single.svg" class="logo img-fluid" alt="Company Logo">
          </a>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path stroke="rgba(255, 255, 255, 1)" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" d="M4 7h22M4 15h22M4 23h22"/></svg>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
              <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                  Criminals
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="#">Action</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#">Something else here</a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                  Groups
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="#">Action</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#">Something else here</a>
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
                  Crimes
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                  <a class="dropdown-item" href="#">Action</a>
                  <a class="dropdown-item" href="#">Another action</a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="#">Something else here</a>
                </div>
              </li>         
            </ul>
            <form class="search-form form-inline">
              <input class="form-control" type="search" placeholder="Search everything" aria-label="Search">
              <button class="btn search-icon" type="submit">
                <img class="d-block" src="../assets/icons/Search_alt.svg" alt="search icon">
              </button>
            </form>
          </div>
        </nav>
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
              <div class="card post-profile ">
                <img src="<?php echo $image ?>" class="card-img-top post-pic" alt="profile pic">
                <div class="card-body">                
                  <a href="#" class=""> <?php echo ($title); ?> </a>
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

      <footer class="bg-pm text-white">
        <div class="container">
          <div class="row row-offset">
            <div class="col-lg-3 order-1 order-lg-0 custom-offset">
              <a class="d-block logo-link" href="#">
                <img src="../assets/img/logo_single.svg" class="logo img-fluid" alt="Company Logo">
              </a>
              <h1 class="font-weight-normal logo-text text-center">The CrimeWiki</h1>
            </div>
            <div class="col-lg-2 col-sm-6">
              <h4 class="font-weight-normal h5 list-heading">Criminals</h4>
              <ul class="list-unstyled category-links">
                <li><a href="#">Country Wise</a></li>
                <li><a href="#">Alphabetically</a></li>
                <li><a href="#">Most Popular</a></li>
                <li><a href="#">Recent Criminals</a></li>
                <li><a href="#">Never Caught</a></li>
              </ul>
            </div>
            <div class="col-lg-2 col-sm-6">
              <h4 class="font-weight-normal h5 list-heading">Groups</h4>
              <ul class="list-unstyled category-links">
                <li><a href="#">Country Wise</a></li>
                <li><a href="#">Alphabetically</a></li>
                <li><a href="#">Most Popular</a></li>
                <li><a href="#">Recent Criminals</a></li>
                <li><a href="#">Never Caught</a></li>
              </ul>
            </div>
            <div class="col-lg-2 col-sm-6">
              <h4 class="font-weight-normal h5 list-heading">Crimes</h4>
              <ul class="list-unstyled category-links">
                <li><a href="#">Country Wise</a></li>
                <li><a href="#">Alphabetically</a></li>
                <li><a href="#">Most Popular</a></li>
                <li><a href="#">Recent Criminals</a></li>
                <li><a href="#">Never Caught</a></li>
              </ul>
            </div>
            <div class="col-lg-2 col-sm-6">
              <h4 class="font-weight-normal h5 list-heading">Others</h4>
              <ul class="list-unstyled category-links">
                <li><a href="#">About The crimWiki</a></li>
                <li><a href="#">Contact Page</a></li>
                <li><a href="#">Privacy policy</a></li>
                <li><a href="#">Sitemap</a></li>
                <li><a class="text-nowrap mail-link" href="mailto: info@crimewiki.com"> <img class="mail-icon" src="../assets/icons/mail.svg" alt="mail icon"> info@crimewiki.com</a></li>
              </ul>
            </div>
          </div>
          <div class="social-icons d-flex justify-content-center">
            <a href="#" class="social-link">
              <img src="../assets/icons/facebook.svg" alt="Facebook icon">
            </a>
            <a href="#" class="social-link">
              <img src="../assets/icons/google.svg" alt="Google icon">
            </a>
            <a href="#" class="social-link">
              <img src="../assets/icons/linkdin.svg" alt="Facebook icon">
            </a>
            <a href="#" class="social-link">
              <img src="../assets/icons/github.svg" alt="Facebook icon">
            </a>
          </div>
        </div>
        <div class="copyright bg-pm-dark text-center">
          Designed and developed solely by <a class="owner" href="https://www.linkedin.com/in/anupamkhosla">Anupam Khosla</a> | All rights reserved
        </div>
      </footer>

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
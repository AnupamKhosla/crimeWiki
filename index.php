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
            <p>
              <?php echo $blog_about_text; ?>              
            </p>
            <button type="button" class="btn btn-pm d-block cta">Github Repo</button>
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
            <button class="btn btn-pm m-auto2 details">See Details</button>
          </div>
          <div class="col-xl-4 col-lg-5 wrapper">
            <div class="post-sources d-flex flex-column">
              <h4 class="text-center text-pm sources">Sources</h4>
              <?php echo $sources; ?>
              <span class="publish-date"> <?php echo $publish_date ?> Published on 29 Nov 2021</span>
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

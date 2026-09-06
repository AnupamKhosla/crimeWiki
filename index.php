<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once('include/config.php');
require_once('include/functions.php');

if(!defined('SETUP') || SETUP !== true) {
  header("Location: login.php", true, 303);
  exit();
}

// If tables are missing, redirect to setup to avoid fatal errors.
try {
  $conn = make_db_connection();
  $check = $conn->query("SHOW TABLES LIKE 'posts'");
  if($check === false || $check->num_rows === 0) {
    header("Location: login.php", true, 303);
    exit();
  }
} catch (Exception $e) {
  header("Location: login.php", true, 303);
  exit();
}

require_once('include/index_code.php');
$page_title = 'CrimeWiki | World Crime Stories and Cases';
$page_description = 'CrimeWiki publishes researched stories about crime, criminal cases, and the people and events behind them.';
$canonical_url = crimewiki_url('/');
$og_image_url = crimewiki_url('/assets/img/logo_gun.png');
?>
<!doctype html>
  <html class="no-js homepage" lang="en">

  <head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8'); ?>">

    <meta property="og:title" content="<?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:site_name" content="CrimeWiki">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image_url, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image_url, ENT_QUOTES, 'UTF-8'); ?>">

    <link rel="manifest" href="/assets/site.webmanifest">
    <link rel="icon" type="image/svg+xml" href="/assets/img/logo_single.svg">
    <link rel="apple-touch-icon" href="/assets/img/logo_gun.png">
    <!-- Place favicon.ico in the root directory -->
	    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.css" >
	    <?php require __DIR__ . '/include/inline_css.php'; ?>
	    <script>
	      (function () {
	        function initCarousel(root) {
	          var track = root.querySelector('[data-carousel-track]');
	          var slides = Array.prototype.slice.call(root.querySelectorAll('[data-carousel-slide]'));
	          var previous = root.querySelector('[data-carousel-prev]');
	          var next = root.querySelector('[data-carousel-next]');
	          var page = 0;

	          if (!track || !slides.length || !previous || !next) return;

	          function visibleCount() {
	            var width = root.getBoundingClientRect().width;
            if (width < 576) return 1;
            if (width < 992) return 2;
            if (width < 1200) return 3;
            if (width < 1400) return 4;
            return 5;
          }

	          function render() {
            var count = visibleCount();
            var pages = Math.max(1, Math.ceil(slides.length / count));
            page = Math.min(page, pages - 1);
            track.style.transform = 'translate3d(' + (-page * 100) + '%, 0, 0)';
            previous.disabled = page === 0;
            next.disabled = page === pages - 1;
            previous.setAttribute('aria-disabled', previous.disabled ? 'true' : 'false');
            next.setAttribute('aria-disabled', next.disabled ? 'true' : 'false');
            slides.forEach(function (slide, index) {
              var active = index >= page * count && index < (page + 1) * count;
              slide.setAttribute('aria-hidden', active ? 'false' : 'true');
              slide.querySelectorAll('a').forEach(function (link) {
                link.tabIndex = active ? 0 : -1;
              });
            });
          }

          previous.addEventListener('click', function () { if (page > 0) { page--; render(); } });
          next.addEventListener('click', function () { if (page < Math.ceil(slides.length / visibleCount()) - 1) { page++; render(); } });
          window.addEventListener('resize', render, { passive: true });
          root.classList.add('carousel-enhanced');
          render();
        }

        function boot() {
          document.querySelectorAll('[data-carousel]').forEach(initCarousel);
        }

        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot);
        else boot();
      }());
    </script>
	    <meta name="theme-color" content="#E92222">
    
    <!-- Global site tag (gtag.js) - Google Ads: 10871239283 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=AW-10871239283"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'AW-10871239283');
    </script>
    <!-- Event snippet for Website traffic conversion page -->
    <script>
      gtag('event', 'conversion', {'send_to': 'AW-10871239283/_WI3COTa76oDEPPk578o'});
    </script>


  </head>

  <body>

    <section class="hero text-white">
      <div class="container">
        
        <?php require_once("include/nav.php") ?>
	        <form action="/search" class="filters" method="GET">
          <div class="row custom-container m-auto">
            <div class="col-lg-5 col-md-12 d-flex">
              <h1 class="main-title h3 font-weight-light w-100 text-center text-lg-left">World Crime Stories and Cases</h1>
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
	                  <select class="w-100" name="category">
	                    <option value="">Category</option>
	                    <?php echo category_filter_options($_GET["category"] ?? NULL); ?>
	                  </select>
	                </div>    
	                <div class="col-sm-6 offset-sm-3 col-md-3 offset-md-0 col-lg-5 pl-lg-0 mt-3 mt-md-0">
	                  <select class="w-100" name="filter">
	                    <option <?php if(($_GET["filter"] ?? "") == "") echo "selected" ?> value="">Sort By</option>
	                    <option <?php if(($_GET["filter"] ?? "") == "datetime") echo "selected" ?> value="datetime">Latest</option>
	                    <option <?php if(($_GET["filter"] ?? "") == "alphabetically") echo "selected" ?> value="alphabetically">Alphabetically</option>
	                    <option <?php if(($_GET["filter"] ?? "") == "popular") echo "selected" ?> value="popular">Popular</option>
	                    <option <?php if(($_GET["filter"] ?? "") == "country") echo "selected" ?> value="country">Country</option>
	                  </select>
	                </div>
                <div class="col-sm-6 offset-sm-3 offset-md-0 col-lg-2 col-md-2 pl-lg-0 mt-3 mt-md-0">
                  <button type="submit" class="go btn text-white d-flex align-items-center justify-content-center w-100">
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
            <div class="homepage-carousel" data-carousel aria-label="Featured crime stories">
              <button type="button" class="carousel-arrow carousel-prev" data-carousel-prev aria-label="Previous stories" disabled>
                <span aria-hidden="true">&#10094;</span>
              </button>
              <div class="carousel-viewport">
                <div class="carousel-track" data-carousel-track>
              <?php echo $slides; ?>
                </div>
              </div>
              <button type="button" class="carousel-arrow carousel-next" data-carousel-next aria-label="Next stories">
                <span aria-hidden="true">&#10095;</span>
              </button>
            </div>
          </div>
        </div>
        
      </div>
    </section>

    <section class="about">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-3 offset-lg-1">
            <img class="logo-double img-fluid d-none d-lg-block m-auto" src="../assets/img/logo_gun.png" width="317" height="317" alt="Logo double gun">
          </div>
          <div class="col-lg-7 about-text pl-lg-5">
            <h1 class="logo-text text-center font-weight-normal">The CrimeWiki</h1>
            <img class="logo-double img-fluid d-block d-lg-none m-auto" src="../assets/img/logo_gun.png" width="317" height="317" alt="Logo double gun">
            <?php echo $blog_about_text; ?>             
            <div class="d-flex justify-content-center mt-auto">
              <a href="https://github.com/AnupamKhosla/crimeWiki" type="button" class="btn btn-pm d-inline-flex align-items-center cta mx-auto">Github Repo</a>       
            </div>
            
          </div>
        </div>
      </div>
    </section>

   <?php require_once("include/footer.php"); ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.js"></script>
    <script src="../assets/js/main.js?v=native-carousel-20260906"></script>

    <!-- Google Analytics: change UA-XXXXX-Y to be your site's ID. -->
    <script>
      window.ga = function () { ga.q.push(arguments) }; ga.q = []; ga.l = +new Date;
      ga('create', 'UA-XXXXX-Y', 'auto'); ga('set', 'anonymizeIp', true); ga('set', 'transport', 'beacon'); ga('send', 'pageview')
    </script>
    <script src="https://www.google-analytics.com/analytics.js" async></script>







  </body>
  </html>

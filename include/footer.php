 <footer class="bg-pm text-white">
      <div class="container">
        <div class="row row-offset">
          <div class="col-lg-3 order-1 order-lg-0 custom-offset">
            <a class="d-block logo-link" href="#">
              <img src="/assets/img/logo_single.svg" class="logo img-fluid" alt="Company Logo">
            </a>
            <h1 class="font-weight-normal logo-text text-center">The CrimeWiki</h1>
          </div>

          <?php 
          $nav_col = "";
          $conn = make_db_connection();
          $result = $conn->query("SELECT name FROM `categories` WHERE name!='Blog' ");
          if($result != false) {    
            while($row = $result->fetch_assoc()) {      
              $cat_name = htmlspecialchars($row['name']);  
              $nav_col .= "<div class='col-lg-2 col-sm-6'>
                            <h4 class='font-weight-normal h5 list-heading'>$cat_name</h4>
                            <ul class='list-unstyled category-links'>
                              <li><a href='/search?category=$cat_name&filter=country'>Country Wise</a></li>
                              <li><a href='/search?category=$cat_name&filter=alphabetically'>Alphabetically</a></li>
                              <li><a href='/search?category=$cat_name&filter=popular'>Most Popular</a></li>
                              <li><a href='/search?category=$cat_name&filter=latest'>Recent $cat_name</a></li>                              
                            </ul>
                          </div>";
            }
          }
          echo "$nav_col";
          ?>
          <div class="col-lg-2 col-sm-6">
            <h4 class="font-weight-normal h5 list-heading">Others</h4>
            <ul class="list-unstyled category-links">
              <li><a href="#">About The crimWiki</a></li>
              <li><a href="#">Contact Page</a></li>
              <li><a href="#">Privacy policy</a></li>
              <li><a href="/sitemap">Sitemap</a></li>
              <li><a class="text-nowrap mail-link" href="mailto: info@crimewiki.com"> <img class="mail-icon" src="/assets/icons/mail.svg" alt="mail icon"> info@crimewiki.com</a></li>
            </ul>
          </div>
        </div>
        <div class="social-icons d-flex justify-content-center">
          <a href="https://www.facebook.com/anupam.khosla.7" class="social-link">
            <img src="/assets/icons/facebook.svg" alt="Facebook icon">
          </a>
          <a href="#" class="social-link">
            <img src="/assets/icons/google.svg" alt="Google icon">
          </a>
          <a href="https://www.linkedin.com/in/anupamkhosla/" class="social-link">
            <img src="/assets/icons/linkdin.svg" alt="Linkdin icon">
          </a>
          <a href="https://github.com/AnupamKhosla" class="social-link">
            <img src="/assets/icons/github.svg" alt="Github icon">
          </a>
        </div>
      </div>
      <div class="copyright bg-pm-dark text-center">
        Designed and developed solely by <a class="owner" href="https://www.linkedin.com/in/anupamkhosla">Anupam Khosla</a> | All rights reserved
      </div>
    </footer>
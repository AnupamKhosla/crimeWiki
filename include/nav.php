<nav class="navbar navbar-expand-lg navbar-dark align-items-start">
  <a class="navbar-brand" href="//<?php echo $_SERVER["SERVER_NAME"]; ?> ">
    <img src="/assets/img/logo_single.svg" class="logo img-fluid" alt="Company Logo">
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path stroke="rgba(255, 255, 255, 1)" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" d="M4 7h22M4 15h22M4 23h22"/></svg>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav ml-auto">
      <?php 
        $path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH); 
        $home_active = "";
        $category_page = "";
        if($path == "/") {
          $home_active = "active";                  
        }
        else if(isset($category) && ($category != "%")) {          
          $category_page = $category;  
          //for post.php page
        }
        else if(empty($_GET["category"])){
          $category_page = "Criminals";          
        } 
        else {
          $category_page = $_GET["category"];
        }
        
        $nav_items = "<li class='nav-item " . $home_active . "'>
                        <a class='nav-link' href=//" .  $_SERVER['SERVER_NAME'] . ">Home <span class='sr-only'>(current)</span></a>
                      </li>";                 
        
        $conn = make_db_connection();
        $result = $conn->query("SELECT name FROM `categories`");
        if($result != false) {    
          while($row = $result->fetch_assoc()) {      
            $row_name = htmlspecialchars($row['name']);  
            $nav_active = "";
            if($category_page == $row_name) {
              $nav_active = "active";
            }
            $nav_items .= "<li class='nav-item'>
                            <a class='nav-link " . $nav_active ."' href='/search?category=$row_name'>
                              $row_name
                            </a>                
                          </li>";                           
          }       
        }              
        echo $nav_items;
      ?>                    
    </ul>    
  </div>
  <form action="/search" class="search-form form-inline my-3 my-lg-0" method="GET">
    <input name="title" class="form-control w-100" type="search" placeholder="Search anything" aria-label="Search">
    <button class="btn search-icon" type="submit">
      <img class="d-block" src="/assets/icons/Search_alt.svg" alt="search icon"> 
    </button>
  </form>
</nav>
<?php 
  $pages = array( "dashboard" => "", "addpost" => "", "allposts" => "", "categories" => "", "wikipedea" => "");
  $page = pathinfo($_SERVER["REQUEST_URI"], PATHINFO_FILENAME);
  $pages[$page] = "active";  
?>

<nav class="col-lg-2 sidebar p-0 navbar d-block navbar-expand-lg">
  <div class="logo-grand-parent d-flex justify-content-between justify-content-lg-center w-auto">
    <a class="logo-container navbar-brand" href="/dashboard.php">
      <img class="img-fluid logo" src="assets/img/logo_single.svg" alt="logo">
    </a>
     <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30"><path stroke="rgba(255, 255, 255, 1)" stroke-linecap="round" stroke-miterlimit="10" stroke-width="2" d="M4 7h22M4 15h22M4 23h22"/></svg>
    </button>
  </div>  
    <hr class="cut-top cut my-0">
    <hr class="cut-bottom cut my-0">
    <div class="collapse navbar-collapse " id="navbarSupportedContent">
      <ul class="nav flex-column nav-pills w-100">
        <li class="nav-item">
          <a class="nav-link <?php echo $pages['dashboard']; ?>" href="dashboard.php">
            <img class="icon mb-n1px" src="assets/icons/dashboard_alt.svg" alt="dashboard icon">
            Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $pages['addpost']; ?> " href="addpost.php">
            <img class="icon " src="assets/icons/add_post.svg" alt="add post icon">               
            Add Post
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $pages['allposts']; ?>" href="allposts.php">
            <img class="icon " src="assets/icons/edit.svg" alt="add post icon">               
            Posts
          </a>
        </li>
        <li class="nav-item ">
          <a class="nav-link <?php echo $pages['categories']; ?>" href="categories.php">
            <img class="icon mb-n1px" src="assets/icons/category.svg" alt="category icon"> 
            Categories
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?php echo $pages['wikipedea']; ?>" href="wikipedea.php">
            <img class="icon mb-n3px" src="assets/icons/wikipedea.svg" alt="category icon"> 
            Wikipedea
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#">
            <img class="icon mb-n1px" src="assets/icons/comment.svg" alt="category icon"> 
            Comments
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href=" //<?php echo $_SERVER['SERVER_NAME']; ?> ">
            <img class="icon mb-n1px" src="assets/icons/eye.svg" alt="category icon"> 
            Live Blog
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">
            <img class="icon mb-n1px" src="assets/icons/sign_out.svg" alt="category icon"> 
            Log Out
          </a>
        </li>
      </ul>
    </div>
  
</nav>
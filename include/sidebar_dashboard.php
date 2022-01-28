<?php 
  $pages = array( "dashboard" => "", "addpost" => "", "allposts" => "", "categories" => "", "wikipedea" => "");
  $page = pathinfo($_SERVER["REQUEST_URI"], PATHINFO_FILENAME);
  $pages[$page] = "active";  
?>

<div class="col-md-3 col-lg-2 sidebar p-0">
  <div class="logo-grand-parent d-flex justify-content-center w-auto">
    <a class="logo-container" href="/dashboard.php">
      <img class="img-fluid logo" src="assets/img/logo_single.svg" alt="logo">
    </a>
  </div>
  <hr class="cut-top cut my-0">
  <hr class="cut-bottom cut my-0">
  <ul class="nav flex-column nav-pills">
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
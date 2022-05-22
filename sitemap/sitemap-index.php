<?php 
header('Content-Type: text/xml');
require_once('../include/config.php');
require_once('../include/functions.php');

$sitemap = "<sitemap>
            <loc>http://{$_SERVER['SERVER_NAME']}/sitemap/sitemap1.txt</loc>
          </sitemap>";

$conn = make_db_connection();
$result = $conn->query("SELECT COUNT(*) FROM posts;");
if($result) {
  $count = $result->fetch_row()[0];
  
  if($count > 50) {    
      $max_pages = ceil($count/50);
    for($pages = ceil($count/50); $pages > 1; $pages--) {      
      $sitemap .= "\n          <sitemap>
            <loc>http://{$_SERVER['SERVER_NAME']}/sitemap/sitemap" . ($max_pages-$pages+2) . ".txt</loc>
          </sitemap>";
    }
  }  
}
else {
  die();
}

echo "<?xml version='1.0' encoding='UTF-8'?>
  <sitemapindex xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>
    $sitemap
  </sitemapindex>";


?>
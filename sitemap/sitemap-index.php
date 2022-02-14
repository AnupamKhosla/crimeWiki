<?php 
header('Content-Type: text/xml; charset=utf-8');
require_once('../include/config.php');
require_once('../include/functions.php');

$sitemap = "<sitemap>
            <loc>http://{$_SERVER['SERVER_NAME']}/sitemap/sitemap1.xml</loc>
          </sitemap>";

$conn = make_db_connection();
$result = $conn->query("SELECT COUNT(*) FROM posts;");
if($result) {
  $count = $result->fetch_row()[0];
  
  if($count > 5000) {    
    for($pages = ceil($count/5000); $pages > 1; $pages--) {      
      $sitemap .= "\n          <sitemap>
            <loc>http://{$_SERVER['SERVER_NAME']}/sitemap/sitemap" . $pages . ".xml</loc>
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
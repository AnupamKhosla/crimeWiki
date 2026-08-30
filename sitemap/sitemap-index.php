<?php
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=900');
require_once('../include/config.php');
require_once('../include/functions.php');

$conn = make_db_connection();
$result = $conn->query("SELECT COUNT(*) FROM posts WHERE id NOT IN (1, 2);");
if(!$result) {
  http_response_code(500);
  exit;
}

$count = (int)$result->fetch_row()[0];
$page_count = max(1, (int)ceil($count / 50));
$entries = '';
for($page = 1; $page <= $page_count; $page++) {
  $loc = htmlspecialchars(crimewiki_url('/sitemap/sitemap' . $page . '.xml'), ENT_XML1, 'UTF-8');
  $entries .= "    <sitemap><loc>{$loc}</loc></sitemap>\n";
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
echo $entries;
echo "</sitemapindex>\n";

?>

<?php
header('Content-Type: application/xml; charset=UTF-8');
header('Cache-Control: public, max-age=900');
require_once('../include/config.php');
require_once('../include/functions.php');

$page_number = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, [
	'options' => ['min_range' => 1]
]);
$page_number = $page_number ?: 1;
$offset = ($page_number - 1) * 50;

$links = "";
$conn = make_db_connection();
$stmt = $conn->prepare("SELECT title, titlerepeat FROM posts WHERE id NOT IN (1, 2) ORDER BY id LIMIT ?, 50;");
$stmt->bind_param("i", $offset);
$result = $stmt->execute();
if( $result && ($result = $stmt->get_result()) ) {
	while($row = $result->fetch_assoc()) {		
		$loc = htmlspecialchars(crimewiki_url(post_path($row['title'], $row['titlerepeat'])), ENT_XML1, 'UTF-8');
		$links .= "  <url><loc>{$loc}</loc></url>\n";
	}	
}
else {
	http_response_code(500);
	exit;
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
echo $links;
echo "</urlset>\n";


?>

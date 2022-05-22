<?php 
header('Content-Type: text/plain; charset=utf-8');
require_once('../include/config.php');
require_once('../include/functions.php');

if(isset($_GET["page"])) {
	$page = ($_GET["page"]-1)*50;
}
else {
	$page = 0;
}

$links = "";
$conn = make_db_connection();
$stmt = $conn->prepare("SELECT title, titlerepeat FROM posts WHERE NOT id=1 AND NOT id=2 LIMIT ?, 50;");
$stmt->bind_param("i", $page);
$result = $stmt->execute();
if( $result && ($result = $stmt->get_result()) ) {
	while($row = $result->fetch_assoc()) {		
		$title_repeat = "";
		if($row["titlerepeat"] != NULL) {
			$title_repeat = "/".$row["titlerepeat"];
		}
		$links .= "http://{$_SERVER['SERVER_NAME']}/post/" . $row["title"] . $title_repeat . "\n";
	}	
}
else {
	die("Wrong page number");
}


echo $links;


?>
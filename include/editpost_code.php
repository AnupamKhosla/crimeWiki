<?php 
if(!isset($_SESSION["Response"])) {
	$_SESSION["Response"] = array("month_post" => '', "about_text" => '', "display" => "d-none");
}
function reset_response() {	
	$_SESSION["Response"] = NULL;	
}
$conn = make_db_connection();	


//get filtered 30 posts from database
$page = !empty($_GET["page"]) ? $_GET["page"]-1 : 0;
$page_30 = 30*$page;
$title = !empty($_GET["title"]) ? "%".$_GET["title"]."%" : "%";
$category = !empty($_GET["category"]) ? "%".$_GET["category"]."%" : "%";
$title_repeat = !empty($_GET["rep"]) ? $_GET["rep"] : "%";
if(strtolower($title_repeat) == "null") {
	$title_repeat = NULL;
}
$posts_table_content = "			  			
              <tr>
                <th scope='row'>NULL</th>
                <th scope='row'>NULL</th>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
                <td>NULL</td>
              </tr>
            	";
$stmt = $conn->prepare("SELECT id, datetime, title, titlerepeat, categoryname FROM `posts` WHERE NOT title='\$blog_month_post' AND NOT title='\$blog_about_text' AND title LIKE ? AND categoryname LIKE ? AND (titlerepeat <=> ? OR IF(? = '%',1=1, 1=0)) ORDER BY datetime DESC LIMIT ?, 30");
$stmt->bind_param("sssss", $title, $category, $title_repeat, $title_repeat, $page_30);
$result = $stmt->execute();
if($result != false && $res = $stmt->get_result()) { //query was successful	
	if($row = $res->fetch_assoc()) { //first iteration only to nemove NULL table valuesand set $count		
		$row_id = htmlspecialchars($row['id']);
		$row_name = htmlspecialchars($row['title']);
		if(strlen($row_name) > 20) {
			$row_name = substr($row_name, 0, 20) . "...";
		}
		$row_repeat = htmlspecialchars($row['titlerepeat'] ?? "NULL");
		$row_datetime = date( 'd/m/Y H:i:s', htmlspecialchars($row['datetime']) );
		$row_category = htmlspecialchars($row['categoryname']);

		$posts_table_content = "<tr>
                <th scope='row'>1</th>
                <th scope='row'>$row_id</th>
                <td> <a href='/post/$row_id'>$row_name</a> </td>
                <td>$row_category</td>                
                <td>$row_datetime</td>
                <td>$row_repeat</td>
              </tr>                
           		";
	}
	$count = 2;
	while($row = $res->fetch_assoc()) {
		$row_id = htmlspecialchars($row['id']);
		$row_name = htmlspecialchars($row['title']);
		if(strlen($row_name) > 20) {
			$row_name = substr($row_name, 0, 20) . "...";
		}
		$row_repeat = htmlspecialchars($row['titlerepeat'] ?? "NULL");
		$row_datetime = date( 'd/m/Y H:i:s', htmlspecialchars($row['datetime']) );
		$row_category = htmlspecialchars($row['categoryname']);
		$posts_table_content .= "<tr>
                <th scope='row'>$count</th>
                <th scope='row'>$row_id</th>
                <td> <a href='/post/$row_id'>$row_name</a> </td>
                <td>$row_category</td>                
                <td>$row_datetime</td>
                <td>$row_repeat</td>
              </tr>                
           		";
           		$count++;           		
	}	
}
else {
	die("Could not fetch results from cateory table" . $conn->error);
}
//get filtered 30 posts finished  




//get total rows without LIMIT for pagination  
$stmt = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE NOT title='\$blog_month_post' AND NOT title='\$blog_about_text' AND title LIKE ? AND categoryname LIKE ? AND (titlerepeat <=> ? OR IF(? = '%',1=1, 1=0))");
$stmt->bind_param("ssss", $title, $category, $title_repeat, $title_repeat);
$result = $stmt->execute();
if($result != false && $res = $stmt->get_result()) { //query was successful		
	$page_count = ceil($res->fetch_row()[0] / 30);
}
else {
	die("Could not fetch results from cateory table" . $conn->error);
}
$url = $_SERVER["REQUEST_URI"];
$query_str = parse_url($url, PHP_URL_QUERY);
parse_str($query_str, $query_params);
unset($query_params['page']);
$url = "allposts.php?" . http_build_query($query_params) . '&page=';
function eval2($input) {
	return $input;
}
$eval2 = 'eval2';

$pagination_li_prev = "<li class='page-item disabled'><span class='page-link'>Prev</span></li>";
$pagination_li1 = '';
if($page > 0) {
	$pagination_li1 = "<li class='page-item one'><a href='" . $url . 1 . "' class='page-link' href='#'>1</a></li>";
	$pagination_li_prev = "<li class='page-item'><a class='page-link' href='" . $url . $page . "'>Prev</a></li>";
}
$pagination_li2 = '';
if($page > 3) {
			$pagination_li2 = "<li class='page-item disabled two'><span class='page-link' href='#'>...</span></li>";
		}
$pagination_li3 = '';
if($page-2 > 0) {
		$pagination_li3 = "<li class='page-item three'><a href='" . $url . $page-1 . "' class='page-link'>" . $page-1 . "</a></li>";
	}
$pagination_li4 = '';
if($page-1 > 0) {
	$pagination_li4 = "<li class='page-item four'><a href='" . $url . $page . "' class='page-link'>" . $page . "</a></li>";
}

$pagination_dots_last = "<li class='page-item disabled'><span class='page-link' href='#'>...</span></li>"; 
if($page+4 >= $page_count) {
	$pagination_dots_last = '';
}
$pagination_li_last1 = "<li class='page-item'><a class='page-link' href='" . $url . $page+3 . "'>{$eval2($page+3)}</a></li>";
if($page+3 >= $page_count) {
	$pagination_li_last1 = '';
}
$pagination_li_last2 = "<li class='page-item'><a class='page-link' href='" . $url . $page+2 . "'>{$eval2($page+2)}</a></li>";
if($page+2 >= $page_count) {
	$pagination_li_last2 = '';
}
$pagination_li_last = "<li class='page-item'><a class='page-link' href='" . $url . $page_count . "'>$page_count</a></li>";
$pagination_li_next = "<li class='page-item'><a class='page-link' href='" . $url . $page+2 . "'>Next</a></li>";
if($page+1 >= $page_count) {
	$pagination_li_last = '';
	$pagination_li_next = "<li class='page-item disabled'><span class='page-link' href='#'>Next</span></li>";
}


$pagination = <<<EOD
<nav aria-label="Search results pages">
  <ul class="pagination justify-content-center">
    $pagination_li_prev
    $pagination_li1
    $pagination_li2
    $pagination_li3
    $pagination_li4    
    <li class="page-item active" aria-current="page">
      <span class="page-link">{$eval2($page+1)}</span>
    </li>
    $pagination_li_last2
    $pagination_li_last1
    $pagination_dots_last
    $pagination_li_last
    $pagination_li_next
  </ul>
</nav>
EOD;



?>
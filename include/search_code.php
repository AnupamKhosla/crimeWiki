<?php 

$posts = "";

if($_SERVER['REQUEST_METHOD'] === 'GET') {

	$page = !empty($_GET["page"]) ? $_GET["page"]-1 : 0;
	$page_30 = 30*$page;
	$title = empty($_GET["title"]) ? "%" : "%".$_GET["title"]."%";
	$category = empty($_GET["category"]) ? "%" : $_GET["category"];
	$filter = empty($_GET["filter"]) ? "datetime" : $_GET["filter"];
	$conn = make_db_connection();

	if($filter == "datetime") {
		$stmt = $conn->prepare("SELECT datetime, title, image, titlerepeat, content FROM `posts` WHERE NOT title='\$blog_month_post' AND NOT title='\$blog_about_text' AND title LIKE ? AND categoryname LIKE ? ORDER BY datetime DESC LIMIT ?, 30");
		$stmt->bind_param("ssi", $title, $category, $page_30);
	}
	else if ($filter == "popular") {
		$stmt = $conn->prepare("SELECT datetime, title, image, titlerepeat, content FROM `posts` WHERE NOT title='\$blog_month_post' AND NOT title='\$blog_about_text' AND title LIKE ? AND categoryname LIKE ? ORDER BY CHAR_LENGTH(content) DESC LIMIT ?, 30");
		$stmt->bind_param("ssi", $title, $category, $page_30);
	}
	
	$result = $stmt->execute();		
	if($result != false && $result = $stmt->get_result()) { //query was successful
		libxml_use_internal_errors(true); //important
		$tmp = new DOMDocument();
		
		while($row = $result->fetch_assoc()) { //first iteration only to nemove NULL table valuesand set $count				
			$row_name = htmlspecialchars($row['title']);
			if(strlen($row_name) > 200) {
				$row_name = substr($row_name, 0, 200) . "...";
			} 
			
			$tmp->loadHTML('<!DOCTYPE html><meta charset="UTF-8">' . $row['content']);
			$content = $tmp->getElementsByTagName("content")[0];
			
			$introduction = "";			
			$p = $content->getElementsByTagName('p')[0];
			$introduction .= $tmp->saveHTML($p);
			while(isset($p->nextSibling) && $p->nextSibling->nodeName != "hr") {						
				$p = $p->nextSibling;
				if(strlen($introduction) > 1700) {
					$introduction = $introduction . "...";
					break;
				}
				else {
					$introduction .= $tmp->saveHTML($p);
				}									
			}					
			$datetime = date( 'd/m/Y H:i:s', htmlspecialchars($row["datetime"]) );
			$row_image = image_path(htmlspecialchars($row["image"]));
			$row_repeat = htmlspecialchars($row['titlerepeat']);
			$posts .= "<div class='row post mb-4 mb-sm-5'>
									<div class='col-xl-3 col-md-4'>									
										<h3 class='text-pm d-md-none text-center pt-0 mb-3'>$row_name</h3>			
										<div class='card post-profile '>
											<img src='$row_image' class='card-img-top post-pic' alt='profile pic'>              
										</div>
									</div>
									<div class='col-xl-9 col-md-8 d-flex flex-column'>		
										<div class='d-flex flex-lg-row flex-column'>
											<h3 class='text-pm d-none d-md-block mb-sm-1 mb-lg-2'>$row_name</h3>
											<span class='publish-date text-black-50 small  ml-lg-auto  font-weight-500 mt-2 mt-md-0 mt-lg-2 mb-sm-2 mb-lg-0'>$datetime</span> 		
										</div>																		          
										$introduction            
										<div class='d-flex justify-content-center mt-auto pt-3'>
											<a href='post/$row_name/$row_repeat' class='btn btn-pm d-inline-flex align-items-center'>See Details</a>
										</div>	
									</div> 
								</div>";
		}




		//get total rows without LIMIT for pagination  
		$stmt = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE NOT title='\$blog_month_post' AND NOT title='\$blog_about_text' AND title LIKE ? AND categoryname LIKE ? ");
		$stmt->bind_param("ss", $title, $category);
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
		$url = "search?" . http_build_query($query_params) . '&page=';
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
		<ul class="pagination pagination-sm d-sm-none justify-content-center">
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
		<ul class="pagination d-none d-sm-flex justify-content-center">
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

	}
	
}

?>
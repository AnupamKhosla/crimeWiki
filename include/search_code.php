<?php 

$posts = "";

if($_SERVER['REQUEST_METHOD'] === 'GET') {
	$page_number = (int)($_GET["page"] ?? 1);
	$page_number = max(1, $page_number);
	$page = $page_number - 1;
	$page_30 = 30 * $page;
	$search_term = trim((string)($_GET["title"] ?? ""));
	$category_term = trim((string)($_GET["category"] ?? ""));
	$filter = (string)($_GET["filter"] ?? "datetime");
	$allowed_filters = ["datetime", "alphabetically", "popular", "country"];
	if(!in_array($filter, $allowed_filters, true)) {
		$filter = "datetime";
	}
	$conn = make_db_connection();

	if(isset($_GET["advance"]) && $_GET["advance"]=="on" ) {
		$advance_checked = true;
		$pagination = "<div class='text-center font-weight-500'>Nothing Found!</div>";
	}
	else {
		$advance_checked = false;
		$pagination = "<div class='text-center font-weight-500'>Nothing Found!, did you try Advance Search?</div>";
	}

	$conditions = [
		"NOT title='\$blog_month_post'",
		"NOT title='\$blog_about_text'"
	];
	$param_types = "";
	$params = [];

	if($search_term !== "") {
		$title_like = "%" . $search_term . "%";
		if($advance_checked) {
			$conditions[] = "(title LIKE ? OR content LIKE ?)";
			$param_types .= "ss";
			$params[] = $title_like;
			$params[] = $title_like;
		}
		else {
			$conditions[] = "title LIKE ?";
			$param_types .= "s";
			$params[] = $title_like;
		}
	}
	if($category_term !== "") {
		$conditions[] = "categoryname = ?";
		$param_types .= "s";
		$params[] = $category_term;
	}
	else {
		// The old categoryname LIKE '%' predicate excluded NULL categories.
		$conditions[] = "categoryname IS NOT NULL";
	}

	$where_sql = implode(" AND ", $conditions);
	$excerpt_sql = "CASE
		WHEN LOCATE('<content', p.content) > 0 THEN CONCAT(
			SUBSTRING_INDEX(
				SUBSTRING_INDEX(SUBSTRING(p.content, LOCATE('<content', p.content)), '<hr', 1),
				'</content>', 1
			),
			'</content>'
		)
		ELSE p.content
	END AS content_excerpt";
	$inner_order_sql = match($filter) {
		"alphabetically" => "ORDER BY title",
		"popular" => "ORDER BY content_length DESC",
		"country" => "ORDER BY ISNULL(country), country, title",
		default => "ORDER BY datetime DESC"
	};
	$outer_order_sql = match($filter) {
		"alphabetically" => "ORDER BY matched.title",
		"popular" => "ORDER BY matched.content_length DESC",
		"country" => "ORDER BY ISNULL(matched.country), matched.country, matched.title",
		default => "ORDER BY matched.datetime DESC"
	};
	$inner_sort_column = $filter === "popular"
		? ", CHAR_LENGTH(content) AS content_length"
		: "";
	$inner_select = "id, datetime, title, image, titlerepeat, country$inner_sort_column, COUNT(*) OVER() AS total_count";
	$result_sql = "SELECT matched.datetime, matched.title, matched.image, matched.titlerepeat, $excerpt_sql, matched.total_count
		FROM (
			SELECT $inner_select
			FROM `posts`
			WHERE $where_sql
			$inner_order_sql
			LIMIT ?, 30
		) AS matched
		JOIN `posts` AS p ON p.id = matched.id
		$outer_order_sql";

	$result_params = $params;
	$result_types = $param_types . "i";
	$result_params[] = $page_30;
	$stmt = $conn->prepare($result_sql);
	bind_dynamic_params($stmt, $result_types, $result_params);
	$result = $stmt->execute();		
	if( $result != false && ($result = $stmt->get_result()) && ($result->num_rows > 0) ) { //query was successful
		$total_rows = 0;
		
		while($row = $result->fetch_assoc()) { //first iteration only to nemove NULL table valuesand set $count
			$total_rows = (int)$row['total_count'];
			$row_raw_name = (string)$row['title'];
			$row_name = htmlspecialchars($row_raw_name, ENT_QUOTES, 'UTF-8');
			if(strlen($row_name) > 200) {
				$row_name = substr($row_name, 0, 200) . "...";
				
				//important to allow links like {Alphonse%20D'Arco} that contains single quote
			} 
			
			// The SQL result is already limited to the first content block. Avoid
			// parsing 30 full HTML documents just to find the first paragraph.
			$excerpt = (string)$row['content_excerpt'];
			$paragraph_start = stripos($excerpt, '<p');
			if($paragraph_start === false) {
				continue;
			}
			$introduction = substr($excerpt, $paragraph_start);
			$end_position = NULL;
			foreach([stripos($introduction, '<hr'), stripos($introduction, '</content>')] as $position) {
				if($position !== false && ($end_position === NULL || $position < $end_position)) {
					$end_position = $position;
				}
			}
			if($end_position !== NULL) {
				$introduction = substr($introduction, 0, $end_position);
			}
			if(strlen($introduction) > 1700) {
				$cut_position = strrpos(substr($introduction, 0, 1700), '</p>');
				if($cut_position !== false) {
					$introduction = substr($introduction, 0, $cut_position + 4);
				}
				$introduction .= "...";
			}					
			$datetime = date( 'd/m/Y H:i:s', htmlspecialchars($row["datetime"]) );
			$row_image = image_path(htmlspecialchars($row["image"]));
			$row_repeat = htmlspecialchars($row['titlerepeat'] ?? "");
			$row_href = htmlspecialchars(post_path($row_raw_name, $row['titlerepeat']), ENT_QUOTES, 'UTF-8');
			if(!empty($row_repeat)) {
				$row_repeat = "/" . $row_repeat;
			}
			$posts .= "<div class='row post mb-4 mb-sm-5 text-break'>
									<div class='col-xl-3 col-md-4'>									
										<h3 class='text-pm d-md-none text-center pt-0 mb-3'>$row_name</h3>			
										<div class='card post-profile '>
											<img src='$row_image' " . image_fallback_attr() . " class='card-img-top post-pic' alt='profile pic'>              
										</div>
									</div>
									<div class='col-xl-9 col-md-8 d-flex flex-column'>		
										<div class='d-flex flex-lg-row flex-column'>
											<h3 class='text-pm d-none d-md-block mb-sm-1 mb-lg-2'>$row_name</h3>
											<span class='publish-date text-black-50 small  ml-lg-auto  font-weight-500 mt-2 mt-md-0 mt-lg-2 mb-2 mb-lg-0'>$datetime</span> 		
										</div>																		          
										$introduction            
										<div class='d-flex justify-content-center mt-auto pt-3'>
											<a href='$row_href' class='btn btn-pm d-inline-flex align-items-center'>See Details</a>
										</div>	
									</div> 
								</div>";
		}

		$page_count = (int)ceil($total_rows / 30);

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
if($page > 2) {
			$pagination_li2 = "<li class='page-item disabled two'><span class='page-link' href='#'>...</span></li>";
		}
$pagination_li3 = '';
if($page-1 > 0) {
		$pagination_li3 = "<li class='page-item three'><a href='" . $url . $page . "' class='page-link'>" . $page . "</a></li>";
	}


$pagination_dots_last = "<li class='page-item disabled'><span class='page-link' href='#'>...</span></li>"; 
if($page+3 >= $page_count) {
	$pagination_dots_last = '';
}
$pagination_li_last1 = "<li class='page-item'><a class='page-link' href='" . $url . $page+2 . "'>{$eval2($page+2)}</a></li>";
if($page+2 >= $page_count) {
	$pagination_li_last1 = '';
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
      
    <li class="page-item active" aria-current="page">
      <span class="page-link">{$eval2($page+1)}</span>
    </li>
    
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
    
    <li class="page-item active" aria-current="page">
      <span class="page-link">{$eval2($page+1)}</span>
    </li>
    
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

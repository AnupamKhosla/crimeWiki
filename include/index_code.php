<?php 
$conn = make_db_connection();
//slider section
$selectedCategory = trim((string)($_GET["category"] ?? "Criminals"));
$selectedFilter = trim((string)($_GET["filter"] ?? ""));

$allowedCategories = [];
$categoryResult = $conn->query("SELECT name FROM `categories` WHERE name!='Blog'");
if($categoryResult != false) {
	while($categoryRow = $categoryResult->fetch_assoc()) {
		$allowedCategories[] = $categoryRow["name"];
	}
}

if($selectedCategory === "" || !in_array($selectedCategory, $allowedCategories, true)) {
	$selectedCategory = "Criminals";
}

$sliderLimit = 50;
$sliderRandomOrder = !in_array($selectedFilter, ["datetime", "alphabetically", "popular", "country"], true);

if($sliderRandomOrder) {
	$hasHomepageRank = posts_have_homepage_rank($conn);

	if($hasHomepageRank) {
		// homepage_rank is indexed with categoryname. Choose one random point in
		// that stable shuffled order, then read only the next 50 card records.
		$sliderRank = homepage_rank();
		$stmt = $conn->prepare("SELECT title, image, titlerepeat FROM `posts` WHERE categoryname=? AND homepage_rank>=? ORDER BY homepage_rank, id LIMIT ?");
		$stmt->bind_param("sii", $selectedCategory, $sliderRank, $sliderLimit);
		$stmt->execute();
		$result = $stmt->get_result();
		$sliderRows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

		// Wrap to the beginning of the shuffled index when the chosen point was
		// close to its end, so every homepage has up to 50 cards.
		$remainingSlides = $sliderLimit - count($sliderRows);
		if($remainingSlides > 0) {
			$stmt = $conn->prepare("SELECT title, image, titlerepeat FROM `posts` WHERE categoryname=? AND homepage_rank<? ORDER BY homepage_rank, id LIMIT ?");
			$stmt->bind_param("sii", $selectedCategory, $sliderRank, $remainingSlides);
			$stmt->execute();
			$wrapResult = $stmt->get_result();
			if($wrapResult) {
				$sliderRows = array_merge($sliderRows, $wrapResult->fetch_all(MYSQLI_ASSOC));
			}
		}
	}
	else {
		$countStmt = $conn->prepare("SELECT COUNT(*) FROM `posts` WHERE categoryname=?");
		$countStmt->bind_param("s", $selectedCategory);
		$countStmt->execute();
		$countResult = $countStmt->get_result();
		$sliderCount = (int)$countResult->fetch_row()[0];
		$sliderOffset = $sliderCount > $sliderLimit ? random_int(0, $sliderCount - $sliderLimit) : 0;

		$stmt = $conn->prepare("SELECT title, image, titlerepeat FROM `posts` WHERE categoryname=? ORDER BY id LIMIT ? OFFSET ?");
		$stmt->bind_param("sii", $selectedCategory, $sliderLimit, $sliderOffset);
		$stmt->execute();
		$result = $stmt->get_result();
		$sliderRows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
	}
}
else {
	$orderBy = "";
	if($selectedFilter === "datetime") {
	$orderBy = "ORDER BY datetime DESC";
}
else if($selectedFilter === "alphabetically") {
	$orderBy = "ORDER BY title";
}
else if($selectedFilter === "popular") {
	$orderBy = "ORDER BY CHAR_LENGTH(content) DESC";
}
else if($selectedFilter === "country") {
	$orderBy = "ORDER BY ISNULL(country), country, title";
}

	$stmt = $conn->prepare("SELECT title, image, titlerepeat FROM `posts` WHERE categoryname=? $orderBy LIMIT ?");
	$stmt->bind_param("si", $selectedCategory, $sliderLimit);
}
if(!$sliderRandomOrder) {
	$stmt->execute();
	$result = $stmt->get_result();
	$sliderRows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

$slides = "";
	if(!empty($sliderRows)) {
			if($sliderRandomOrder && count($sliderRows) > 1) {
				shuffle($sliderRows);
			}
			foreach($sliderRows as $row) {
				$rawTitle = (string)$row['title'];
				$title = htmlspecialchars($rawTitle, ENT_QUOTES, 'UTF-8');
				$rawImage = trim((string)($row['image'] ?? ""));
				$image = htmlspecialchars(image_path($rawImage));
				$imageAttrs = image_fallback_attr();
				$imageClasses = "card-img-top post-pic";
				if($rawImage === "" || $image === htmlspecialchars(default_image_path(), ENT_QUOTES, 'UTF-8')) {
					$imageClasses .= " is-default-image";
				}
				$titleRepeat = $row['titlerepeat'];
				$postHref = htmlspecialchars(post_path($rawTitle, $titleRepeat), ENT_QUOTES, 'UTF-8');
				$slides .= <<<EOT
										<div class="slide carousel-slide" data-carousel-slide>
								        <div class="card">
								          <img $imageAttrs class="$imageClasses" alt="profile pic" src="$image" loading="lazy" decoding="async">
								          <div class="card-body">                
								            <a href="$postHref" class="">$title</a>
								          </div>
								        </div>
								      </div>
		EOT;
	}		
}
else {
	die("Could not fetch results from the database. Probably wrong post-id or title" . $conn->error);
}
//slider finishes



//about us section
$result = $conn->query( "SELECT content FROM `posts` WHERE title='\$blog_about_text';" );
if(!!$result && $result->num_rows) {
	$blog_about_text = $result->fetch_row()[0];
}
//about us finishes



// The former Crime of the Month query and DOM parse were removed. The
// homepage no longer reads or renders that feature.

?>

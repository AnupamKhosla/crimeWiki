<?php 

$conn = make_db_connection();
if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if(isset($_POST["identifier"]) && $_POST["identifier"] == "wikipedea_form") {		

	libxml_use_internal_errors(true); //important
	
	$links_invalid = [];
	$links =  explode( "\n", trim($_POST["wiki_links"]) );

	foreach ($links as $key => &$value) {		
		$tmp = new DOMDocument();		
		$value = trim($value);
		if(filter_var($value, FILTER_VALIDATE_URL) && (strpos(get_headers($value)[0],'200') !== false) && $tmp->loadHTMLFile($value) ) {

			$value = $tmp;	
			$title = $value->saveHTML($value->getElementById("firstHeading"));
			$xpath = new DomXPath($value);

			$details = $xpath->query("//table[@class[contains(.,'biography')]]");
			if($details[0] == NULL) {				
				array_push($links_invalid, $value);
				unset($links[$key]);
				continue; 
				// the page doesn't have biography, so skip the page
			}

			$a = $value->getElementsByTagName("a");
			foreach ($a as $tag) {
				$href = $tag->getAttribute("href");
				if(!str_starts_with($href, "http")) {
					$tag->setAttribute("href", "http://en.wikipedia.com" . $href);
				}				
			} //fixed all links

			$details = $xpath->query("//table[@class[contains(.,'biography')]]");
			//reinitialise details with fixed links

			$pic_object = $xpath->query("//td[@class='infobox-image']//img/@src");
			if($pic_object->length == 0) {
				$pic_src = "Uploads/default.png";
			}
			else {
				$pic_src = $pic_object[0]->value;
			}

			$intro1 = $intro2 = $intro3 = $intro4 = $intro5 = [];			

			$intro1["tr"] = $xpath->query("//table[@class[contains(.,'biography')]]//tr[th='Born']/th/text()") ?? $xpath->query("//table[@class[contains(.,'biography')]]//tr[th='Founded']/th/text()") ?? $xpath->query("//table[@class[contains(.,'biography')]]//tr[th='Location']/th/text()") ?? "Field";

			$intro1["tr"] = $value->saveHTML($intro1["tr"][0]);		

			$intro1["td"] = $xpath->query("//table[@class[contains(.,'biography')]]//tr[th='{$intro1["tr"]}']/td/text()");
			
			$intro1["td"] = $value->saveHTML($intro1["td"][0]);
			
			var_dump($intro1["tr"], $intro1["td"]);
			
			//var_dump($value->saveHTML($born[0]));
			echo "<br> <br> <br> ";


		}
		else {			
			array_push($links_invalid, $value);
			unset($links[$key]);
		}
	}
	
	

	die();

	
	
}



?>
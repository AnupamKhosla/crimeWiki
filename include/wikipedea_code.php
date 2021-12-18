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
			$link_str = $value;
			$value = $tmp;	
			$title = $value->saveHTML($value->getElementById("firstHeading"));	
			$xpath = new DomXPath($value);

			$details = $xpath->query("//table[@class[contains(.,'infobox')]]");
			if($details[0] == NULL) {				
				array_push($links_invalid, $value);
				unset($links[$key]);
				continue; 
				// the page doesn't have biography, so skip the page
			}

			//remove all style tags
			$style_tags = $value->getElementsByTagName("style");
			while($style_tags->length > 0) {
				//can't use foreach -- bug -- https://stackoverflow.com/q/36910558/3429430
				$style_tags[0]->parentNode->removeChild($style_tags[0]);
			}
			// remove navigation  
			$toc = $value->getElementById("toc");
			$toc->parentNode->removeChild($toc);
			//remove all sup tags 
			$sup = $value->getElementsByTagName("sup");
			while($sup->length > 0) {				
				$sup[0]->parentNode->removeChild($sup[0]);				
			}

			
			$details = $xpath->query("//table[@class[contains(.,'infobox')]]");
			//reinitialise $details with fixed links and removed tags


			$a = $xpath->query("//*[@href]"); //grab all elements like a link			
			foreach ($a as $tag) {
				$href = $tag->getAttribute("href");
				if(str_starts_with($href, "http") || str_starts_with($href, "//")) {
					//do nothing
				}				
				else if (str_starts_with($href, "/"))	{
					$tag->setAttribute("href", "https://en.wikipedia.org" . $href);
				}		
				else {
					$tag->setAttribute("href", $link_str . $href);
				}
			} //fixed all links

			
			$pic_object = $xpath->query("//td[@class='infobox-image']//img/@src");
			if($pic_object->length == 0) {
				$pic_src = "Uploads/default.png";
			}
			else {
				$pic_src = $pic_object[0]->value;
			}

			$infobox_image = $xpath->query("//table/tbody/tr[ td[@class[contains(.,'infobox-image')]] ]");	
			$infobox_image[0]->parentNode->removeChild($infobox_image[0]);		

			$intro1 = $intro2 = $intro3 = $intro4 = $intro5 = [];		
			function xpath_query($obj) {
				if($obj->length == 0) {
					return NULL;
				}
				else {
					return $obj;
				}
			}		
			function extract_intro($xpath, $value, &$intro, $field1, $field2, $field3, $field4, $field_default) {
				$intro["th"] = xpath_query($xpath->query("//table[@class[contains(.,'infobox')]]//tr[th='$field1']/th")) ?? xpath_query($xpath->query("//table[@class[contains(.,'infobox')]]//tr[th='$field2']/th")) ?? xpath_query($xpath->query("//table[@class[contains(.,'infobox')]]//tr[th='$field3']/th")) ?? xpath_query($xpath->query("//table[@class[contains(.,'infobox')]]//tr[th='$field3']/th")) ?? $field_default;

				if(gettype($intro["th"]) == "object") {
					$intro["td"] = $intro["th"][0]->nextSibling;
					$intro["th"] = $value->saveHTML($intro["th"][0]);							
					$intro["td"] = $value->saveHTML($intro["td"]);
				}		
				else {
					$intro["td"] = "Unknown";
				}

			}

			extract_intro($xpath, $value, $intro1, "Victims",   "Deaths","Injured",    "Ethnicity", "Victims");
			extract_intro($xpath, $value, $intro2, "Born", "Born", "Location", "Founded", "Born");
			extract_intro($xpath, $value, $intro3, "Died", "Injured","Result", "Ethnicity", "Died");
			extract_intro($xpath, $value, $intro4, "Known for", "Date", "Leader", "Leaders", "Known for");
			//<0xa0> is important in Known<0xa0>for


			$related_tmp = $xpath->query("//h2[span[@id='See_also']]/following-sibling::ul");
			if($related_tmp->length == 0) {
				$related = "";
			}
			else {			
				$related = $value->saveHTML($related_tmp[0]);
				$related = "<ol class='list list-unstyled'>" . substr($related, 4, -5) . "</ol>";
			}

			$sources_tmp = $xpath->query("//*[@class='reference-text']");
			$sources = "";
			if($sources_tmp->length != 0) {				
				foreach($sources_tmp as $value3) {
					$sources .= ("<li>" . $value->saveHTML($value3) . "</li>" . "\n");
				}
			}
			
			$content = $value->createElement("content");
			$tmp_nxt = $details[0]->nextSibling;
			var_dump($tmp_nxt->nextSibling);
			

			function validContent($node, &$content) {
				if($node === NULL) {
					return false;
				}
				else if($node->nodeName != "h2") {
					$content->appendChild($node);					
					return true;
				}
				if($node->nodeName == "h2") {
					foreach($node->childNodes as $child) {
						if($child->getAttribute("id") == "See_also" || $child->getAttribute("id") == "Citations" || $child->getAttribute("id") == "References" || $child->getAttribute("id") == "Notes" ) {
							return false;
						}
					}
					$content->appendChild($node);
					$content->appendChild($value->createElement("hr"));
				}
				
			}

			while(validContent($tmp_nxt, $content)) {				
				$tmp_nxt = $tmp_nxt->nextSibling;
				
			}


			$content2 = $value->saveHTML($content);

			var_dump($content2);
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
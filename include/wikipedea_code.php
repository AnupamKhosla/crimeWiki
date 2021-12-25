<?php 
if(!isset($_SESSION["Response"])) {
	$_SESSION["Response"] = array("repeat_links" => [], "invalid_links" => [], "display" => "d-none");
}
function reset_response() {	
	$_SESSION["Response"] = array("repeat_links" => [], "invalid_links" => [], "display" => "d-none");	
}

$conn = make_db_connection();

if(!isset($_SESSION["Validation"])) {
	$_SESSION["Validation"] = array( "txt" => "", "class" => "d-none", "status" => "" );
}

if(isset($_POST["identifier"]) && $_POST["identifier"] == "wikipedea_form" && isset($_POST["category_select"])) {	
	libxml_use_internal_errors(true); //important	
	$links_invalid = [];
	$links_repeat = [];
	$links =  explode( "\n", trim($_POST["wiki_links"]) );
	function xpath_query($obj) {
		if($obj->length == 0) {
			return NULL;
		}
		else {
			return $obj;
		}
	}	
	function extract_intro($xpath, $value, &$intro, $field1, $field2, $field3, $field4, $field_default) {
		$intro["th"] = xpath_query($xpath->query("//table[@class[contains(.,'infobox')]]//tr[th='$field1']/th")) ?? xpath_query($xpath->query("//table[@class[contains(.,'infobox')]]//tr[th='$field2']/th")) ?? xpath_query($xpath->query("//table[@class[contains(.,'infobox')]]//tr[th='$field3']/th")) ?? xpath_query($xpath->query("//table[@class[contains(.,'infobox')]]//tr[th='$field3']/th")) ?? "<th>".$field_default."</th>";
		if(gettype($intro["th"]) == "object") {
			$intro["td"] = $intro["th"][0]->nextSibling;
			$intro["th"] = $value->saveHTML($intro["th"][0]);							
			$intro["td"] = $value->saveHTML($intro["td"]);
		}		
		else {
			$intro["td"] = "<td>Unknown</td>";
		}
	}
	function validContent($node, &$content, $hr) {			
		if($node == NULL) {						
			return false;
		}
		else if($node->nodeName !== "h2") {		
					
			$content->appendChild($node->cloneNode(true));					
			return true;
		}
		else if($node->nodeName == "h2") {
			foreach($node->childNodes as $child) {
				if($child->getAttribute("id") == "See_also" || $child->getAttribute("id") == "Citations" || $child->getAttribute("id") == "References" || $child->getAttribute("id") == "Notes" ) {
					return false;
				}
			}	
			$content->appendChild($hr->cloneNode(true));
			$content->appendChild($node->cloneNode(true));				
			return true;							
		}
	}

	foreach ($links as $key => &$value) {		
		$value = trim($value);
		$wikilink = preg_replace("(^https?://)", "", $value);
		$stmt = $conn->prepare("SELECT 1 FROM `posts` WHERE wikilink = ?");
		$stmt->bind_param("s", $wikilink);
		$result = $stmt->execute();		
		$get_result = $stmt->get_result();
		$rows = $get_result->num_rows;
		if($result && $rows != 0) {
			$wikilink = false;			
		}
		$tmp = new DOMDocument();		
		$value = trim($value);
		if( ($wikilink != false) && filter_var($value, FILTER_VALIDATE_URL) && (strpos(get_headers($value)[0],'200') !== false) && $tmp->loadHTMLFile($value) ) {
			$link_str = $value;
			$value = $tmp;	
			$title = $value->getElementById("firstHeading")->nodeValue;	
			$xpath = new DomXPath($value);

			$details = $xpath->query("//table[@class[contains(.,'infobox')]]");
			if($details[0] == NULL) {				
				array_push($links_invalid, $link_str);
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
			if(!empty($toc)) {
				$toc->parentNode->removeChild($toc);	
			}
			
			//remove all edit options
			$edit_tags = $xpath->query("//span[@class='mw-editsection']");
			foreach($edit_tags as $eTag) {				
				$eTag->parentNode->removeChild($eTag);
			}

			//remove all sup tags with reference class
			$sup = $xpath->query("//sup[@class='reference']"); 
			foreach($sup as $supTag) {				
				$supTag->parentNode->removeChild($supTag);				
			}
			
			$details = $xpath->query("//table[@class[contains(.,'infobox')]]/tbody");
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
				$infobox_image = $xpath->query("//table/tbody/tr[ td[@class[contains(.,'infobox-image')]] ]");	
				$infobox_image[0]->parentNode->removeChild($infobox_image[0]);
				$pic_src = $pic_object[0]->value;
			}
			$infobox_above = $xpath->query("//table/tbody/tr[ *[@class[contains(.,'infobox-above')]] ]");
			if($infobox_above->length != 0) {
				$infobox_above[0]->parentNode->removeChild($infobox_above[0]);
			}

					

			$intro1 = $intro2 = $intro3 = $intro4 = $intro5 = [];		

			extract_intro($xpath, $value, $intro1, "Victims", "Deaths", "Charges", "Ethnicity", "Victims");
			extract_intro($xpath, $value, $intro2, "Born", "Born", "Location", "Founded", "Born");
			extract_intro($xpath, $value, $intro3, "Died", "Injured","Result", "Ethnicity", "Died");
			extract_intro($xpath, $value, $intro4, "Known\xc2\xa0for", "Date", "Leader", "Leaders", "Known\xc2\xa0for");
			extract_intro($xpath, $value, $intro5, "Criminal penalty", "Jail time", "Perpetrator", "Perpetrators", "Criminal penalty");
			//\xc2\xa0 is important in Known<0xa0>for

			$related_tmp = $xpath->query("//h2[span[@id='See_also']]/following-sibling::ul");
			if($related_tmp->length == 0) {
				$related = "";
			}
			else {			
				$related = $value->saveHTML($related_tmp[0]);
				$related = "<ol class='list list-unstyled custom-scrollbar'>" . substr($related, 4, -5) . "</ol>";
			}

			$sources_tmp = $xpath->query("//*[@class='reference-text']");
			$sources = "";
			if($sources_tmp->length != 0) {				
				foreach($sources_tmp as $value3) {
					$sources .= ("<li>" . $value->saveHTML($value3) . "</li>" . "\n");
				}
			}
			
			$content = $value->createElement("content");
			$hr = $value->createElement("hr");			
			$h2 = $value->createElement("h2");
			$h2->appendChild($value->createTextNode("Introduction"));
			$content->appendChild($h2);
			$tmp_nxt = $details[0]->parentNode->nextSibling;	
			
			while(validContent($tmp_nxt, $content, $hr)) {				
				$tmp_nxt = $tmp_nxt->nextSibling;				
			}

			$details = $value->saveHTML($details[0]);
			$content = $value->saveHTML($content);
			$content_mysql = <<<EOF
															<intro-data>
										            <tr>
										              {$intro1["th"]}
										              {$intro1["td"]}
										            </tr>
										            <tr>
										              {$intro2["th"]}
										              {$intro2["td"]}
										            </tr>
										            <tr>
										              {$intro3["th"]}
										              {$intro3["td"]}
										            </tr>
										            <tr>
										              {$intro4["th"]}
										              {$intro4["td"]}
										            </tr>
										            <tr>
										              {$intro5["th"]}
										              {$intro5["td"]}
										            </tr>
										          </intro-data>

										          <details>
										            {$details}
										          </details>
										        	<sources>
										            <ul class="list custom-scrollbar">
										              {$sources}                        
										            </ul>
										          </sources>
										          <related>
										            {$related}
										          </related>		          
										          {$content}
										         
			EOF;
			
			echo strlen($content_mysql), mb_detect_encoding($content_mysql), " Success <br><br>";
			//$title -- string, $intro[] -- html tags string, $pic_src -- string, $details[0], $content_mysql --> string xml

				$creator = "Anupam K";
				$category = $_POST["category_select"];
				/*
				-- Add wikilink, repitition cols !!
				-- make async loadhtml 
				-- add $datetime by 1ms on every insert
				-- 	make slugs work as well as post?id=NUMBER
				*/
				$title_repeat = NULL;
				//check if title aready exists
				$stmt = $conn->prepare("SELECT * FROM `posts` WHERE title=?");
				$stmt->bind_param("s", $title);
				$result = $stmt->execute();
				$get_result =  $stmt->get_result();
				$rows = $get_result->num_rows;		
				if($result && $rows != 0) {
					$title_repeat = $rows;			
				}
				$stmt = $conn->prepare("INSERT INTO `posts` (datetime, title, wikilink, titlerepeat, creatorname, categoryname, image, content) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");	
    		$stmt->bind_param("ssssssss", $date_time, $title, $wikilink, $title_repeat, $creator, $category, $pic_src, $content_mysql);
				$stmt->execute();
		}
		else {		
			if($wikilink == false) {
				array_push($links_repeat, $value);
			}	
			else {
				array_push($links_invalid, $value);
			}			
			unset($links[$key]);
		}		
	}
	
	$_SESSION["Response"]["display"] = "block";
	$_SESSION["Response"]["repeat_links"] = $links_repeat;
	$_SESSION["Response"]["invalid_links"] = $links_invalid;
	//303 will allow bookmark and reload without resending post data
	header("Location: {$_SERVER['REQUEST_URI']}", true, 303); 
  exit();
}



?>
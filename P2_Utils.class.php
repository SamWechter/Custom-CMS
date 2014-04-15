<?php
DEFINE("adXML","ads/ads.xml");
DEFINE("rssClass","rss_class.xml");
DEFINE("editorialXML","editorial.xml");
DEFINE("bannerXML","banners/banners.xml");
class P2_utils {
	 /* get_template2()
	  * DESCRIPTION:
	  * 			Reads then returns a template file from the same directory
	  * 			 This function is identical to get_template() from LIB_project1 with
	  * 			 the exception that it uses a different template file
	  * OUTPUTS:
	  * 			Returns the string read from template2.html
	 */
	 function get_template2() {
		  // This template is specifically for index.php so I could re-arrange elements without
		  //  changing the code
		  $template = "http://people.rit.edu/~saw7456/539/project2/template2.html";
		  $html_template = file_get_contents($template);
		  return $html_template;
	 }
	 public static function load_XML($xmlURI) {
		  $dom = new DOMDocument();
		  // Sets options in $dom so the XML will be easier to read
		  $dom->preserveWhiteSpace = false;
		  $dom->formatOutput = true;
		  $dom->load($xmlURI);
		  return $dom;
	 }
	 
	 public static function generate_form($whatDom,$hiddenInputName) {
		  $new_form = $whatDom->createElement("form");
		  $new_form->setAttribute("method","POST");
		  // Creates a hidden input for the editorial form
		  $form_hidden_input = $whatDom->createElement('input');
		  $form_hidden_input->setAttribute("type","hidden");
		  $form_hidden_input->setAttribute("name",$hiddenInputName);
		  $form_hidden_input->setAttribute("value","sent");
		  $new_form->appendChild($form_hidden_input);
		  // Creates a label and a password input for submitting the form
		  $form_password_label = $whatDom->createElement('label');
		  $form_password_label->setAttribute("for","formPassword");
		  $form_password_label_text_node = $whatDom->createTextNode("Enter password: ");
		  $form_password_label->appendChild($form_password_label_text_node);
		  $new_form->appendChild($form_password_label);
		  $form_password_input = $whatDom->createElement("input");
		  $form_password_input->setAttribute("type","password");
		  $form_password_input->setAttribute("id","formPassword");
		  $form_password_input->setAttribute("name","formPassword");
		  $new_form->appendChild($form_password_input);
		  $new_form->appendChild($whatDom->createElement("br"));
		  // Creates buttons for resetting and submitting the form
		  $form_reset = $whatDom->createElement('input');
		  $form_reset->setAttribute("type","reset");
		  $form_reset->setAttribute("value","Reset");
		  $new_form->appendChild($form_reset);
		  $form_submit = $whatDom->createElement('input');
		  $form_submit->setAttribute("type","submit");
		  $form_submit->setAttribute("value","Submit");
		  $new_form->appendChild($form_submit);
		  return $new_form;
	 }
	 /* parse_news_items($tmp_dom, $news_article_array, $article_index, $articles_per_page)
	 * DESCRIPTION:
	 * 			Parses an array of news items into html elements containing the news articles
	 * 				then adds the elements to the DOM
	 * INPUTS:
	 * 			$tmp_dom						The DOM object to which new html elements are added
	 * 			$news_article_array		The array of news articles to be parsed
	 * 			$article_index				Which article the function should start at
	 * 			$articles_per_page		How many articles should be displayed per page
	 * OUTPUTS:
	 * 			Returns an array containing the lines from news.txt
	*/
	function parse_news_items2($tmp_dom, $news_article_xml, $article_index, $articles_per_page) {
		 $div_array = array();
		 $news_item_nodelist = $news_article_xml->getElementsByTagName("item");
		 for($i = $article_index; $i > ($article_index - $articles_per_page); $i--) {
			  if ($i > 0) {
					$current_article = $i - 1;
					$current_item = $news_item_nodelist->item($current_article);
					$news_datetime = $current_item->getElementsByTagName("pubDate")->item(0)->nodeValue;
					$news_title = $current_item->getElementsByTagName("title")->item(0)->nodeValue;
					$news_text = $current_item->getElementsByTagName("content")->item(0)->nodeValue;
					$news_permalink = $current_item->getElementsByTagName("link")->item(0)->nodeValue;
					$article_div = $tmp_dom->createElement("div");
					$article_div->setAttribute("class","articleDiv");
					$article_title = $tmp_dom->createElement("h2");
					$article_title_permalink = $tmp_dom->createElement("a");
					$article_title_permalink->setAttribute("href",$news_permalink);
					$article_title_text_node = $tmp_dom->createTextNode($news_title);
					$article_title_permalink->appendChild($article_title_text_node);
					$article_title->appendChild($article_title_permalink);
					$article_div->appendChild($article_title);
					$article_date = $tmp_dom->createElement("p");
					$article_date->setAttribute("class","newsDate");
					$article_date_text_node = $tmp_dom->createTextNode($news_datetime);
					$article_date->appendChild($article_date_text_node);
					$article_div->appendChild($article_date);
					$article_story = $tmp_dom->createElement("p");
					$article_story_text_node = $tmp_dom->createTextNode($news_text);
					$article_story->appendChild($article_story_text_node);
					$article_div->appendChild($article_story);
					array_push($div_array, $article_div);
			  }
		 }
		 return $div_array;
	}
	 /* get_banner2($the_dom,$banner_div)
	  * DESCRIPTION:
	  * 			Adds logo and banner images to the specified DOM and div
	  * INPUTS:
	  * 			$the_dom						The dom to which new elements for the banner are added
	  * 			$banner_div					The div which logo and banner images are added to
	 */
	 function get_banner2($the_dom,$banner_div,$caller_level) {
		  $logo_img = $the_dom->createElement("img");
		  $logo_img->setAttribute("src","http://people.rit.edu/~saw7456/539/project2/Wechter News Logo.jpg");
		  $logo_img->setAttribute("alt","Sam Wechter News Logo");
		  $logo_img->setAttribute("width","100");
		  $logo_img->setAttribute("height","100");
		  $banner_div->appendChild($logo_img);
		  // Loads and parses banners.txt then parses it into arrays
		  $banners_xml = P2_Utils::load_XML("http://people.rit.edu/~saw7456/539/project2/banners/banners.xml");
		  $banner_nodelist = $banners_xml->getElementsByTagName("banner");
		  $banner_locations = array();
		  $banner_displayed_amounts = array();
		  $banner_weights = array();
		  foreach($banner_nodelist as $banner_node) {
				if($banner_node->getAttribute("selected") == "yes") {
					 $banner_location = $banner_node->getElementsByTagName("uri")->item(0)->nodeValue;
					 $banner_displayed_amount = $banner_node->getElementsByTagName("views")->item(0)->nodeValue;
					 $banner_weight = $banner_node->getElementsByTagName("weight")->item(0)->nodeValue;
					 array_push($banner_locations,$banner_location);
					 array_push($banner_displayed_amounts,$banner_displayed_amount);
					 array_push($banner_weights,$banner_weight);
				}
		  }
		  // Sorts the displayed amount and location arrays so both are ordered by the amount of times
		  //	a banner has been displayed.
		  array_multisort($banner_displayed_amounts,$banner_locations);
		  // Creates an img element for the banner and adds it to the input div
		  $banner_img = $the_dom->createElement("img");
		  $banner_loc = "http://people.rit.edu/~saw7456/539/project2/banners/$banner_locations[0]";
		  $banner_img->setAttribute("src",$banner_loc);
		  $banner_img->setAttribute("alt","Advertisement banner");
		  $banner_img->setAttribute("id","adBanner");
		  $banner_img->setAttribute("height","100");
		  $banner_div->appendChild($banner_img);
		  // Writes the banner data back in to banners.xml to update the
		  // 	amount of times each banner has been displayed
		  foreach($banner_nodelist as $banner_node) {
				if($banner_node->getElementsByTagName("uri")->item(0)->nodeValue == $banner_locations[0]) {
					 $viewed_amount = $banner_node->getElementsByTagName("views")->item(0);
					 $viewed_amount->nodeValue = $banner_displayed_amounts[0] + 1;
				}
		  }
		  // Checks if the function caller was at the website's main level or in a directory
		  if($caller_level == 0) {
				$banner_xml_uri = "banners/banners.xml";
		  } else if ($caller_level == 1) {
				$banner_xml_uri = "../banners/banners.xml";
		  }
		  // Saves the newly-changed banner XML
		  $banners_xml->save($banner_xml_uri);
	 }
	 /* make_article_page($articleIndex,$title,$pubDate,$description)
	  * DESCRIPTION:
	  * 			Creates a new article PHP page that the article's permalink will direct to
	  * INPUTS:
	  * 			$articleIndex				The article's index in the list of articles
	  * 			$title						The div which logo and banner images are added to
	  * 			$pubDate						The date at which the article was published
	  * 			$description				The article's description
	 */
	 function make_article_page($articleIndex,$title,$pubDate,$description) {
		  $article_html = get_template();
		  $dom = new DOMDocument();
		  // Creates a new string containing all of the PHP code necessary for a new article PHP page
		  // 	It even has comments!
		  $article_php = '<?php
		  include( "../LIB_project1.php" );
		  include( "../P2_Utils.class.php" );
		  $article_html = get_template();
		  $article_dom = new DOMDocument();
		  @$article_dom->loadHTML($article_html);
		  $article_banner_div = $article_dom->getElementById("bannerDiv");
		  P2_Utils::get_banner2($article_dom,$article_banner_div,1);
		  $content_div = $article_dom->getElementById("contentDiv");
		  // Adds the article title to the page in an h1
		  $article_h1 = $article_dom->createElement("h1");
		  $article_h1_text = $article_dom->createTextNode("' . $title . '");
		  $article_h1->appendChild($article_h1_text);
		  $content_div->appendChild($article_h1);
		  // Adds the article pubDate to the page in an h3
		  $article_date_h3 = $article_dom->createElement("h3");
		  $article_date_h3_text = $article_dom->createTextNode("' . $pubDate . '");
		  $article_date_h3->appendChild($article_date_h3_text);
		  $content_div->appendChild($article_date_h3);
		  // Adds the article pubDate to the page in a p
		  $article_description_p = $article_dom->createElement("p");
		  $article_description_p_text = $article_dom->createTextNode("' . $description . '");
		  $article_description_p->appendChild($article_description_p_text);
		  $content_div->appendChild($article_description_p);
		  // Adds a link to navigate back to news.php
		  $article_back_a = $article_dom->createElement("a");
		  $article_back_a->setAttribute("href","http://people.rit.edu/~saw7456/539/project2/news.php");
		  $article_back_a->setAttribute("alt","Back to the News page");
		  $article_back_a_text = $article_dom->createTextNode("Back to News");
		  $article_back_a->appendChild($article_back_a_text);
		  $content_div->appendChild($article_back_a);
		  echo $article_dom->saveHTML();
		  ?>';
		  // Places the PHP code string into the new domdocument as a CDATA section
		  $dom->appendChild($dom->createCDATASection($article_php));
		  // Determines the article's URI based on its index
		  $article_URI = "articles/article" . $articleIndex . ".php";
		  // Saves the article
		  $domHTML = $dom->saveHTML();
		  file_put_contents($article_URI,$domHTML);
		  // Changes the article's permissions so it can be viewed
		  chmod($article_URI, 0644);
	 }
	 /* generate_text_element($whatDom,$whatContainer,$whatElement,$whatText)
	  * DESCRIPTION:
	  * 			Generates an element of the specified type containing the specified text
	  * 			 and adds it to the specified div and domdocument
	  * INPUTS:
	  * 			$whatDom						 The domdocument which the new element will be added to
	  * 			$whatContainer				 The div which the new element will be added to
	  * 			$whatElement				 The type of new element to be created
	  * 			$whatText					 The text which will be added to the new element
	 */
	 function generate_text_element($whatDom,$whatContainer,$whatElement,$whatText) {
		  // Creates the new element
		  $new_element = $whatDom->createElement($whatElement);
		  // Creates a textnode to store the text
		  $new_element_text = $whatDom->createTextNode($whatText);
		  // Appends the textnode to the new element
		  $new_element->appendChild($new_element_text);
		  // Appends the new element to the specified container
		  $whatContainer->appendChild($new_element);
		  // Returns the new element so this function's caller can reference it
		  return $new_element;
	 }
	 
}
?>
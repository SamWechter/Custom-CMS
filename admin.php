<?php
include( "LIB_project1.php" );
include( "RSSFeed.class.php" );
include( "P2_Utils.class.php" );
DEFINE("pw","potato478");
// Loads the HTML from the template
$admin_html = get_template();
$admin_dom = new DOMDocument();
@$admin_dom->loadHTML($admin_html);
// Gets the banner div and adds images to it
$admin_banner_div = $admin_dom->getElementById("bannerDiv");
P2_Utils::get_banner2($admin_dom,$admin_banner_div,0);
// Gets the content div
$content_div = $admin_dom->getElementById('contentDiv');
// Checks if the editorial form has been submitted
if ($_POST["editorialChange"] == "sent") {
	 // Gets the input password
	 $editorial_password = $_POST["editorialPassword"];
	 // Checks if the input password matches the defined password
	 if($editorial_password == pw) {
		  // Sanitizes the editorial letter field
		  $new_editorial_letter = sanitizeString($_POST["editorialTextarea"]);
		  // Checks if the editorial letter field submission is valid
		  if(validateFieldValue($new_editorial_letter)) {
				// (DEPRECATED) Saves the new editorial letter to editorial.txt
				//file_put_contents("editorial.txt", $new_editorial_letter);
				// Loads the editorial XML
				$editorial_xml = P2_Utils::load_XML(editorialXML);
				// Changes the nodeValue of the editorial content node to the newly-created letter
				$editorial_content = $editorial_xml->getElementsByTagName("content")->item(0);
				$editorial_content->nodeValue = $new_editorial_letter;
				// Saves the new editorial letter to the editorial xml
				$editorial_xml->save(editorialXML);
				generateMessage($admin_dom,$content_div,"The editorial letter has been updated!");
		  } else {
				generateMessage($admin_dom,$content_div,"Please input an editorial letter before submitting the form.");
		  }
	 } else {
		  generateMessage($admin_dom,$content_div,"You have entered an incorrect password. Please try again.");
	 }
}
// Checks if the news item form has been submitted
if ($_POST["newsAdded"] == "sent") {
	 // Gets the input password
	 $news_password = $_POST["newsPassword"];
	 // Checks if the input password matches the defined password
	 if($news_password == pw) {
		  // Sanitizes the news item title and text fields
		  $new_news_title = sanitizeString($_POST["newsTitle"]);
		  $new_news_text = sanitizeString($_POST["newsTextarea"]);
		  // Checks if the news item title and text field submissions are valid
		  if(validateFieldValue($new_news_title)) {
				if(validateFieldValue($new_news_text)) {
					 // Gets a new timestamp to attach to the news item
					 $date_time = new DateTime();
					 $new_news_timestamp = $date_time->format('r');
					 // Concatenates the news item's timestamp, title, and text
					 /*$new_news_item = $new_news_timestamp . "|" . $new_news_title
						  . "|" . $new_news_text . "\n";*/
					 $news_xml = P2_Utils::load_XML("news.xml");
					 // Creates a new item node
					 $new_news_item = $news_xml->createElement("item");
					 // Creates a new title node as a child of the new item
					 $new_item_title = $news_xml->createElement("title");
					 $new_item_title_text = $news_xml->createTextNode($new_news_title);
					 $new_item_title->appendChild($new_item_title_text);
					 $new_news_item->appendChild($new_item_title);
					 // Creates a new pubDate node as a child of the new item
					 $new_item_pubDate = $news_xml->createElement("pubDate");
					 $new_item_pubDate_text = $news_xml->createTextNode($new_news_timestamp);
					 $new_item_pubDate->appendChild($new_item_pubDate_text);
					 $new_news_item->appendChild($new_item_pubDate);
					 // Creates a new content node as a child of the new item
					 $new_item_content = $news_xml->createElement("content");
					 $new_item_content_text = $news_xml->createTextNode($new_news_text);
					 $new_item_content->appendChild($new_item_content_text);
					 $new_news_item->appendChild($new_item_content);
					 // Gets the new item's future index by taking the item nodelist's length and NOT subtracting 1
					 $newest_item_index = $news_xml->getElementsByTagName("item")->length;
					 // Should add a permalink to the news item. This functionality is unfinished since I did not take the right approach
					 // to permalinking
					 /*$new_item_link = $news_xml->createElement("link");
					 $new_item_link->nodeValue = "http://people.rit.edu/~saw7456/539/project2/articles/article" . $newest_item_index . ".php";
					 $new_news_item->appendChild($new_item_link);*/
					 // Appends the new item to the news xml
					 $news_xml->getElementsByTagName("items")->item(0)->appendChild($new_news_item);
					 $news_xml->save("news.xml");
					 // (DEPRECATED) Appends the news item to news.txt
					 //file_put_contents("news.txt", $new_news_item, FILE_APPEND);
					 // Instantiates an object of the RSSFeed class to make a new RSS feed
					 $RSSClassObj = new RSSFeed($new_news_title,$new_news_timestamp,$new_news_text);
					 // Generates and saves a new page for the article
					 P2_Utils::make_article_page($newest_item_index,$new_news_title,
														  $new_news_timestamp,$new_news_text);
					 generateMessage($admin_dom,$content_div,"Your news item has been added!");
				} else {
					 generateMessage($admin_dom,$content_div,"Please input text for your news item.");
				}
		  } else {
				generateMessage($admin_dom,$content_div,"Please input a news item title.");
		  }
	 } else {
		  generateMessage($admin_dom,$content_div,"You have entered an incorrect password. Please try again.");
	 }
}
// Loads the class and services rss list
$rss_class_XML = P2_Utils::load_XML(rssClass);
// Gets a list of student elements
$student_nodelist = $rss_class_XML->getElementsByTagName("student");
// Checks if the student RSS checklist has been submitted
if ($_POST["studentListSent"] == "sent") {
	 // Sets all students' selected attributes to no by default
	 foreach($student_nodelist as $student_node) {
		  $student_node->setAttribute("selected", "no");
	 }
	 // Tracks how many students have been selected
	 $student_counter = 0;
	 // Loops through the student checkboxes that were checked
	 foreach($_POST['studentCheckbox'] as $studentCheckbox){
		  // Limits the number of selected students to 10
		  if($student_counter < 10) {
				// Sets the selected attribute of the same nodelist index as $studentCheckbox to "yes"
				$student_nodelist->item($studentCheckbox)->setAttribute("selected", "yes");
		  }
		  // Incremements the student counter
		  $student_counter++;
	 }
	 // Saves the newly-changed rss class xml
	 $rss_class_XML->save(rssClass);
	 // Generates an error message if more than 10 students were selected
	 if ($student_counter > 10) {
		 generateMessage($admin_dom,$content_div,"You selected $student_counter student RSS feeds but are limited to a maximum of 10. Only the first 10 selected students were saved for use in services.php."); 
	 } else {
		  // Generates user feedback for a successful RSS feed list change
		  generateMessage($admin_dom,$content_div,"Your student RSS selections have been saved.");
	 }
}
// Parses the rss class XML into a nodelist of services
$services_nodelist = $rss_class_XML->getElementsByTagName("choice");
// Checks if the services RSS checklist has been submitted
if ($_POST["servicesListSent"] == "sent") {
	 // Sets all services' selected attributes to no by default
	 foreach($services_nodelist as $service_node) {
		  $service_node->setAttribute("selected", "no");
	 }
	 // Tracks how many services have been selected
	 $services_counter = 0;
	 foreach($_POST['serviceCheckbox'] as $serviceCheckbox){
		  if($services_counter < 3) {
				// Sets the selected attribute of the same nodelist index as $serviceCheckbox to "yes"
				$services_nodelist->item($serviceCheckbox)->setAttribute("selected", "yes");
		  }
		  // Increments the services counter
		  $services_counter++;
	 }
	 // Saves the newly-changed rss class xml
	 $rss_class_XML->save(rssClass);
	 // Generates an error message if more than 3 services were selected
	 if ($services_counter > 3) {
		 generateMessage($admin_dom,$content_div,"You selected $services_counter services feeds but are limited to a maximum of 3. Only the first 3 selected services were saved for used in index.php."); 
	 } else {
		  // Generates a message for a successful service list change
		  generateMessage($admin_dom,$content_div,"Your service selections have been saved.");
	 }
}
// Loads the advertisements xml
$advertisements_XML = P2_Utils::load_XML(adXML);
// Gets a nodelist of advertisements
$advertisements_nodelist = $advertisements_XML->getElementsByTagName("advertisement");
if ($_POST["advertisementsListSent"] == "sent") {
	 foreach($advertisements_nodelist as $advertisement_node) {
		  $advertisement_node->setAttribute("selected", "no");
	 }
	 // Tracks how many advertisements have been selected
	 $advertisement_counter = 0;
	 // Loops through all checked advertisement checkboxes
	 foreach($_POST['advertisementCheckbox'] as $advertisementCheckbox){
		  // Limits the number of selected advertisements to 4
		  if($advertisement_counter < 4) {
				// Sets the selected attribute of the same nodelist index as $advertisementCheckbox to "yes"
				$advertisements_nodelist->item($advertisementCheckbox)->setAttribute("selected", "yes");
		  }
		  // Increments the advertisements counter
		  $advertisement_counter++;
	 }
	 $advertisements_XML->save("ads/ads.xml");
	 // Generates an error message if more than 4 advertisements were selected
	 if ($advertisement_counter > 4) {
		  generateMessage($admin_dom,$content_div,"You selected $advertisement_counter advertisements but are limited to a maximum of 4. Only the first 4 advertisements were saved for use in advertisements.php."); 
	 } else {
		  // Generates a message for a successful advertisement list change
		  generateMessage($admin_dom,$content_div,"Your advertisement selections have been saved.");}
}
// Loads the banners XML
$banners_XML = P2_Utils::load_XML(bannerXML);
// Gets a nodelist of banners
$banners_nodelist = $banners_XML->getElementsByTagName("banner");
// Checks if the banner maintenance form has been submitted
if ($_POST["bannersListSent"] == "sent") {
	 // Sets the selected attribute of all banner nodes to "no" by default
	 foreach($banners_nodelist as $banner_node) {
		  $banner_node->setAttribute("selected", "no");
	 }
	 // Loops through all selected banner checkboxes
	 foreach($_POST['bannerCheckbox'] as $bannerCheckbox){
		  // Sets the selected attribute of the same nodelist index as $bannerCheckbox to "yes"
		  $banners_nodelist->item($bannerCheckbox)->setAttribute("selected", "yes");
	 }
	 // Creates feedback text for a successful banner list change
	 $banner_change_feedback = "Your banner selections have been saved";
	 // Checks if the "Reset banner view amounts" checkbox is selected
	 if($_POST['bannerResetCheckbox']) {
		  // Loops through all banner nodes and sets the nodeValue of their views node to 0
		  foreach($banners_nodelist as $banner_node) {
				$views = $banner_node->getElementsByTagName("views")->item(0);
				$views->nodeValue = 0;
		  }
		  // Adds information to the user feedback text
		  $banner_change_feedback .= " and banner view amounts have been reset";
	 }
	 $banner_change_feedback .= ".";
	 // Saves the newly-changed banner XML
	 $banners_XML->save(bannerXML);
	 // Generates a message for a successful banner list change
	 generateMessage($admin_dom,$content_div,$banner_change_feedback);
}
// Creates an h1 and adds it to contentDiv
P2_Utils::generate_text_element($admin_dom,$content_div,"h1","Admin");
// Generates the admin navigation area for quickly jumping between maintenance sections
generate_admin_navigation($content_div,$admin_dom);
// Generates the form to change the editorial letter
generate_editorial_form($content_div, $admin_dom);
// Generates the form to add a news item
generate_news_form($content_div, $admin_dom);
// Generates the form to select which classmates' RSS feeds to follow
generate_student_list($content_div, $admin_dom);
// Generates the form to select which services to display on index.php
generate_services_list($content_div, $admin_dom);
// Generates the form to select which advertisements to display on advertisements.php
generate_advertisements_list($content_div, $admin_dom);
// Generates the form to select what banners are shown and to reset their viewed amounts
generate_banners_list($content_div,$admin_dom);
// Echos the DOM in order to generate an html page
echo $admin_dom->saveHTML();
/* generate_admin_navigation($whatDiv,$whatDom)
 * DESCRIPTION:
 * 			Adds navigation links so the user can quickly jump between admin sections
 * INPUTS:
 * 			$whatDiv						The dom to which the new links are being added
 * 			$whatDom						The div which the new links are added to
*/
function generate_admin_navigation($whatDiv,$whatDom) {
	 // Creates an h2 and appends it to the content div
	 P2_Utils::generate_text_element($whatDom,$whatDiv,"h2","Jump to a Maintenance Section");
	 // Creates an array of admin section names and their associated div id's
	 $maintenance_array = array(
		  "Change Editorial Letter" => "#adminEditorial",
		  "Create News Item" => "#adminNews",
		  "Select Student RSS Feeds" => "#adminStudents",
		  "Select Services for the Home Page" => "#adminServices",
		  "Select Advertisements for the Advertisements Page" => "#adminAdvertisements",
		  "Select Banners" => "#adminBanners"
	 );
	 // Creates an unordered list to store the admin navigation links
	 $admin_navigation = $whatDom->createElement("ul");
	 $admin_navigation->setAttribute("id","adminNavigation");
	 // Loops through the array of admin sections
	 foreach ($maintenance_array as $key => $value) {
		  // Creates a list item to store the section link
		  $new_li = $whatDom->createElement("li");
		  // Creates an anchor tag for the link
		  $new_anchor = P2_Utils::generate_text_element($whatDom,$whatDiv,"a",$key);
		  $new_anchor->setAttribute("href",$value);
		  $new_li->appendChild($new_anchor);
		  // Appends the anchor tag to the new list item
		  $admin_navigation->appendChild($new_li);
	 }
	 // Appends the admin navigation ul to the specified div
	 $whatDiv->appendChild($admin_navigation);
}
/* generate_editorial_form($a_div, $a_dom)
 * DESCRIPTION:
 * 			Adds a form for changing the editorial letter to the specified DOM and div
 * INPUTS:
 * 			$a_dom						The dom to which the new form is added
 * 			$a_div						The div which the new form is added to
*/
function generate_editorial_form($a_div, $a_dom) {
	 // Creates a div to hold the admin news area
	 $editorial_div = $a_dom->createElement("div");
	 $editorial_div->setAttribute("id","adminEditorial");
	 // Creates an h2 for editing the editorial page and adds it to contentDiv
	 P2_Utils::generate_text_element($a_dom,$a_div,"h2","Change the Editorial Letter");
	 // Creates a form for editing the editorial page
	 $editorial_form = $a_dom->createElement('form');
	 $editorial_form->setAttribute("id","editorialForm");
	 $editorial_form->setAttribute("action","admin.php");
	 $editorial_form->setAttribute("method","POST");
	 // Creates a hidden input for the editorial form
	 $editorial_hidden_input = $a_dom->createElement('input');
	 $editorial_hidden_input->setAttribute("type","hidden");
	 $editorial_hidden_input->setAttribute("name","editorialChange");
	 $editorial_hidden_input->setAttribute("value","sent");
	 $editorial_form->appendChild($editorial_hidden_input);
	 // Creates a textarea for changing the editorial letter's text
	 $editorial_textarea = $a_dom->createElement('textarea');
	 $editorial_textarea->setAttribute("id","editorialTextarea");
	 $editorial_textarea->setAttribute("name","editorialTextarea");
	 $editorial_textarea->setAttribute("rows","15");
	 $editorial_textarea->setAttribute("cols","100");
	 $editorial_form->appendChild($editorial_textarea);
	 $editorial_form->appendChild($a_dom->createElement("br"));
	 // Creates a label and a password input for changing the editorial page
	 $editorial_password_label = $a_dom->createElement('label');
	 $editorial_password_label->setAttribute("for","editorialPassword");
	 $editorial_password_label_text_node = $a_dom->createTextNode("Enter password: ");
	 $editorial_password_label->appendChild($editorial_password_label_text_node);
	 $editorial_form->appendChild($editorial_password_label);
	 $editorial_password_input = $a_dom->createElement("input");
	 $editorial_password_input->setAttribute("type","password");
	 $editorial_password_input->setAttribute("id","editorialPassword");
	 $editorial_password_input->setAttribute("name","editorialPassword");
	 $editorial_form->appendChild($editorial_password_input);
	 $editorial_form->appendChild($a_dom->createElement("br"));
	 // Creates buttons for resetting and submitting the form
	 $editorial_reset = $a_dom->createElement('input');
	 $editorial_reset->setAttribute("type","reset");
	 $editorial_reset->setAttribute("value","Reset");
	 $editorial_form->appendChild($editorial_reset);
	 $editorial_submit = $a_dom->createElement('input');
	 $editorial_submit->setAttribute("type","submit");
	 $editorial_submit->setAttribute("value","Submit");
	 $editorial_form->appendChild($editorial_submit);
	 // Appends the editorial page form to a_div
	 $editorial_div->appendChild($editorial_form);
	 $a_div->appendChild($editorial_div);
}
/* generate_news_form($b_div, $b_dom)
 * DESCRIPTION:
 * 			Adds a form for adding news items to the specified DOM and div
 * INPUTS:
 * 			$b_dom						The dom to which the new form is added
 * 			$b_div						The div which the new form is added to
*/
function generate_news_form($b_div, $b_dom) {
	 // Creates a div to hold the admin news area
	 $news_div = $b_dom->createElement("div");
	 $news_div->setAttribute("id","adminNews");
	 // Creates an h2 for posting news items and adds it to contentDiv
	 $admin_h2 = $b_dom->createElement('h2');
	 $admin_h2_text_node = $b_dom->createTextNode("Post a news item");
	 $admin_h2->appendChild($admin_h2_text_node);
	 $news_div->appendChild($admin_h2);
	 // Creates a form for adding a news item
	 $news_form = $b_dom->createElement('form');
	 $news_form->setAttribute("id","newsForm");
	 $news_form->setAttribute("action","admin.php");
	 $news_form->setAttribute("method","POST");
	 // Creates a hidden input for the news form
	 $news_hidden_input = $b_dom->createElement('input');
	 $news_hidden_input->setAttribute("type","hidden");
	 $news_hidden_input->setAttribute("name","newsAdded");
	 $news_hidden_input->setAttribute("value","sent");
	 $news_form->appendChild($news_hidden_input);
	 // Creates a label and a text input for the news item's title
	 $news_title_label = $b_dom->createElement('label');
	 $news_title_label->setAttribute("for","newsTitle");
	 $news_title_label_text_node = $b_dom->createTextNode("Article title: ");
	 $news_title_label->appendChild($news_title_label_text_node);
	 $news_form->appendChild($news_title_label);
	 $news_title_input = $b_dom->createElement("input");
	 $news_title_input->setAttribute("type","text");
	 $news_title_input->setAttribute("size","120");
	 $news_title_input->setAttribute("id","newsTitle");
	 $news_title_input->setAttribute("name","newsTitle");
	 $news_form->appendChild($news_title_input);
	 $news_form->appendChild($b_dom->createElement("br"));
	 // Creates a textarea for news item text
	 $news_textarea = $b_dom->createElement('textarea');
	 $news_textarea->setAttribute("id","newsTextarea");
	 $news_textarea->setAttribute("name","newsTextarea");
	 $news_textarea->setAttribute("rows","15");
	 $news_textarea->setAttribute("cols","100");
	 $news_form->appendChild($news_textarea);
	 $news_form->appendChild($b_dom->createElement("br"));
	 // Creates a label and a password input for changing the news page
	 $news_password_label = $b_dom->createElement('label');
	 $news_password_label->setAttribute("for","newsPassword");
	 $news_password_label_text_node = $b_dom->createTextNode("Enter password: ");
	 $news_password_label->appendChild($news_password_label_text_node);
	 $news_form->appendChild($news_password_label);
	 $news_password_input = $b_dom->createElement("input");
	 $news_password_input->setAttribute("type","password");
	 $news_password_input->setAttribute("id","newsPassword");
	 $news_password_input->setAttribute("name","newsPassword");
	 $news_form->appendChild($news_password_input);
	 $news_form->appendChild($b_dom->createElement("br"));
	 // Creates buttons for resetting and submitting the form
	 $news_reset = $b_dom->createElement('input');
	 $news_reset->setAttribute("type","reset");
	 $news_reset->setAttribute("value","Reset");
	 $news_form->appendChild($news_reset);
	 $news_submit = $b_dom->createElement('input');
	 $news_submit->setAttribute("type","submit");
	 $news_submit->setAttribute("value","Submit");
	 $news_form->appendChild($news_submit);
	 // Appends the news item form to b_div
	 $news_div->appendChild($news_form);
	 $b_div->appendChild($news_div);
}
/* generate_student_list($c_div,$c_dom)
 * DESCRIPTION:
 * 			Adds a form for changing the student RSS feed selections to the specified DOM and div
 * INPUTS:
 * 			$c_dom						The dom to which the new form is added
 * 			$c_div						The div which the new form is added to
*/
function generate_student_list($c_div,$c_dom) {
	 // Creates a div to hold the admin students area
	 $students_div = $c_dom->createElement("div");
	 $students_div->setAttribute("id","adminStudents");
	 // Creates an h2 for the list of student RSS feeds and adds it to contentDiv
	 $admin_h2 = $c_dom->createElement("h2");
	 $admin_h2_text = $c_dom->createTextNode("Choose up to 10 classmates' RSS feeds to follow");
	 $admin_h2->appendChild($admin_h2_text);
	 $students_div->appendChild($admin_h2);
	 // Creates a form for entering what student RSS feeds to use
	 $student_RSS_form = P2_Utils::generate_form($c_dom,"studentListSent");
	 // Creates an unordered list to contain the student list items
	 $student_ul = $c_dom->createElement("ul");
	 // Parses the XML into an unordered list with checkboxes
	 $student_counter = 0;
	 global $student_nodelist;
	 foreach($student_nodelist as $student_node) {
		  // Creates a li for the student node
		  $student_li = $c_dom->createElement("li");
		  // Creates a checkbox and appends it to the student li
		  $student_checkbox = $c_dom->createElement("input");
		  $student_checkbox->setAttribute("type","checkbox");
		  $student_checkbox->setAttribute("name","studentCheckbox[]");
		  $student_checkbox->setAttribute("value",$student_counter);
		  $student_counter++;
		  // Checks if the student is already selected
		  if($student_node->getAttribute("selected") == "yes") {
				// Sets the student checkbox to be checked by default
				$student_checkbox->setAttribute("checked","checked");
		  }
		  $student_li->appendChild($student_checkbox);
		  // Appends the first and last name of the student and adds it to the li as a textnode
		  $student_name = $student_node->getElementsByTagName("first")->item(0)->nodeValue .
									 " " . $student_node->getElementsByTagName("last")->item(0)->nodeValue;
		  $student_li_text = $c_dom->createTextNode($student_name);
		  $student_li->appendChild($student_li_text);
		  $student_ul->appendChild($student_li);
	 }
	 $student_RSS_form->appendChild($student_ul);
	 $students_div->appendChild($student_RSS_form);
	 $c_div->appendChild($students_div);
}
/* generate_services_list($d_div,$d_dom)
 * DESCRIPTION:
 * 			Adds a form for changing the service selections to the specified DOM and div
 * INPUTS:
 * 			$d_dom						The dom to which the new form is added
 * 			$d_div						The div which the new form is added to
*/
function generate_services_list($d_div,$d_dom) {
	 // Creates a div to hold the admin services area
	 $services_div = $d_dom->createElement("div");
	 $services_div->setAttribute("id","adminServices");
	 // Creates an h2 for the list of services and adds it to adminServices
	 $admin_h2 = $d_dom->createElement("h2");
	 $admin_h2_text = $d_dom->createTextNode("Choose up to 3 services to display on the home page");
	 $admin_h2->appendChild($admin_h2_text);
	 $services_div->appendChild($admin_h2);
	 // Creates a form for entering what services to use
	 $services_RSS_form = P2_Utils::generate_form($d_dom,"servicesListSent");
	 // Creates an unordered list to contain the service list items
	 $services_ul = $d_dom->createElement("ul");
	 $service_counter = 0;
	 global $services_nodelist;
	 foreach($services_nodelist as $service_node) {
		  // Creates a li for the service node
		  $service_li = $d_dom->createElement("li");
		  // Creates a checkbox and appends it to the service li
		  $service_checkbox = $d_dom->createElement("input");
		  $service_checkbox->setAttribute("type","checkbox");
		  $service_checkbox->setAttribute("name","serviceCheckbox[]");
		  $service_checkbox->setAttribute("value",$service_counter);
		  $service_counter++;
		  // Checks if the service is already selected
		  if($service_node->getAttribute("selected") == "yes") {
				// Sets the service checkbox to be checked by default
				$service_checkbox->setAttribute("checked","checked");
		  }
		  $service_li->appendChild($service_checkbox);
		  // Appends the first and last name of the service and adds it to the li as a textnode
		  $service_name = $service_node->getElementsByTagName("name")->item(0)->nodeValue;
		  $service_li_text = $d_dom->createTextNode($service_name);
		  $service_li->appendChild($service_li_text);
		  $services_ul->appendChild($service_li);
	 }
	 // Inserts the services ul before the password input in the form
	 $pw_input = $d_dom->getElementById("formPassword");
	 $services_RSS_form->insertBefore($services_ul,$pw_input);
	 //$services_RSS_form->appendChild($services_ul);
	 $services_div->appendChild($services_RSS_form);
	 $d_div->appendChild($services_div);
}
/* generate_advertisements_list($e_div,$e_dom)
 * DESCRIPTION:
 * 			Adds a form for changing the advertisement selections to the specified DOM and div
 * INPUTS:
 * 			$e_dom						The dom to which the new form is added
 * 			$e_div						The div which the new form is added to
*/
function generate_advertisements_list($e_div,$e_dom) {
	 // Creates a div to hold the admin advertisements area
	 $advertisements_div = $e_dom->createElement("div");
	 $advertisements_div->setAttribute("id","adminAdvertisements");
	 // Creates an h2 for the list of advertisements and adds it to contentDiv
	 $admin_h2 = $e_dom->createElement("h2");
	 $admin_h2_text = $e_dom->createTextNode("Choose up to 4 advertisements to display on the advertisements page.");
	 $admin_h2->appendChild($admin_h2_text);
	 $advertisements_div->appendChild($admin_h2);
	 // Creates a form for entering what advertisements to use
	 $advertisements_RSS_form = P2_Utils::generate_form($e_dom,"advertisementsListSent");
	 global $advertisements_nodelist;
	 // Creates an unordered list to contain the advertisement list items
	 $advertisements_ul = $e_dom->createElement("ul");
	 $advertisement_counter = 0;
	 // Loops through the advertisements nodelist
	 foreach($advertisements_nodelist as $advertisement_node) {
		  // Creates a li for the advertisement node
		  $advertisement_li = $e_dom->createElement("li");
		  // Creates a checkbox and appends it to the advertisement li
		  $advertisement_checkbox = $e_dom->createElement("input");
		  $advertisement_checkbox->setAttribute("type","checkbox");
		  $advertisement_checkbox->setAttribute("name","advertisementCheckbox[]");
		  $advertisement_checkbox->setAttribute("value",$advertisement_counter);
		  $advertisement_counter++;
		  // Checks if the advertisement is already selected
		  if($advertisement_node->getAttribute("selected") == "yes") {
				// Sets the advertisement checkbox to be checked by default
				$advertisement_checkbox->setAttribute("checked","checked");
		  }
		  $advertisement_li->appendChild($advertisement_checkbox);
		  // Appends the title of the advertisement and adds it to the li as a textnode
		  $advertisement_name = $advertisement_node->getElementsByTagName("title")->item(0)->nodeValue;
		  $advertisement_li_text = $e_dom->createTextNode($advertisement_name);
		  $advertisement_li->appendChild($advertisement_li_text);
		  $advertisements_ul->appendChild($advertisement_li);
	 }
	 $advertisements_RSS_form->appendChild($advertisements_ul);
	 $advertisements_div->appendChild($advertisements_RSS_form);
	 $e_div->appendChild($advertisements_div);
}
/* generate_banners_list($f_div,$f_dom)
 * DESCRIPTION:
 * 			Adds a form for changing the banner selections to the specified DOM and div
 * INPUTS:
 * 			$f_dom						The dom to which the new form is added
 * 			$f_div						The div which the new form is added to
*/
// Note: I considered adding an option to change banner weight but opted not to since
//	 banner weight is not being used for anything currently
function generate_banners_list($f_div,$f_dom) {
	 // Creates a div to hold the admin banners area
	 $banners_div = $f_dom->createElement("div");
	 $banners_div->setAttribute("id","adminBanners");
	 // Creates an h2 for the list of banners and adds it to contentDiv
	 $admin_h2 = $f_dom->createElement("h2");
	 $admin_h2_text = $f_dom->createTextNode("Choose banners to display across all pages.");
	 $admin_h2->appendChild($admin_h2_text);
	 $banners_div->appendChild($admin_h2);
	 // Creates a form for entering what banners to use
	 $banners_form = P2_Utils::generate_form($f_dom,"bannersListSent");
	 // Gets a list of banners from banners.xml
	 // Creates an unordered list to contain the banner list items
	 $banners_ul = $f_dom->createElement("ul");
	 $banner_counter = 0;
	 global $banners_nodelist;
	 // Loops through the banners nodelist
	 foreach($banners_nodelist as $banner_node) {
		  // Creates a li for the banner node
		  $banner_li = $f_dom->createElement("li");
		  // Creates a checkbox and appends it to the banner li
		  $banner_checkbox = $f_dom->createElement("input");
		  $banner_checkbox->setAttribute("type","checkbox");
		  $banner_checkbox->setAttribute("name","bannerCheckbox[]");
		  $banner_checkbox->setAttribute("value",$banner_counter);
		  $banner_counter++;
		  // Checks if the banner is already selected
		  if($banner_node->getAttribute("selected") == "yes") {
				// Sets the banner checkbox to be checked by default
				$banner_checkbox->setAttribute("checked","checked");
		  }
		  $banner_li->appendChild($banner_checkbox);
		  // Appends the title of the banner and adds it to the li as a textnode
		  $banner_name = $banner_node->getElementsByTagName("name")->item(0)->nodeValue;
		  $banner_li_text = $f_dom->createTextNode($banner_name);
		  $banner_li->appendChild($banner_li_text);
		  $banners_ul->appendChild($banner_li);
	 }
	 $banners_form->appendChild($banners_ul);
	 $banners_div->appendChild($banners_form);
	 $banner_reset_checkbox = $f_dom->createElement("input");
	 $banner_reset_checkbox->setAttribute("type","checkbox");
	 $banner_reset_checkbox->setAttribute("name","bannerResetCheckbox");
	 $banner_reset_checkbox->setAttribute("id","bannerResetCheckbox");
	 $banners_form->appendChild($banner_reset_checkbox);
	 $banner_reset_text = $f_dom->createTextNode("Reset banner viewed amounts?");
	 $banners_form->appendChild($banner_reset_text);
	 $f_div->appendChild($banners_div);
}
?>
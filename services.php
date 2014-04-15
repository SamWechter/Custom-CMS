<?php
include( "LIB_project1.php" );
include( "P2_Utils.class.php" );
// Loads the HTML from the template
$services_html = get_template();
$services_dom = new DOMDocument();
@$services_dom->loadHTML($services_html);
// Gets the banner div and adds images to it
$services_banner_div = $services_dom->getElementById("bannerDiv");
P2_Utils::get_banner2($services_dom,$services_banner_div,0);
// Gets the content div
$content_div = $services_dom->getElementById('contentDiv');
// Creates an h1 and adds it to contentDiv
P2_Utils::generate_text_element($services_dom,$content_div,"h1","Services");
// Creates a link to project2.rss and adds it to contentDiv
$rss_anchor = P2_Utils::generate_text_element($services_dom,$content_div,"a","Link to my project2.rss");
$rss_anchor->setAttribute("href","project2.rss");
$rss_anchor->setAttribute("id","myRSSLink");
// Gets the rss class xml file
$student_list_XML = P2_Utils::load_XML(rssClass);
// Parses the XML into a nodelist of selected student RSS feeds
$student_nodelist = $student_list_XML->getElementsByTagName("student");
// Creates an array to store what student nodes are selected
$selected_student_array = array();
// Loops through the student nodelist
foreach($student_nodelist as $student_node) {
	 if( $student_node->getAttribute("selected") == "yes" ) {
		  array_push($selected_student_array,$student_node);
	 }
}
foreach($selected_student_array as $selected_student) {
	 // Creates a div to store the current student's RSS feed
	 $new_div = $services_dom->createElement("div");
	 $new_div->setAttribute("class","studentRSSDiv");
	 // Creates an h2 tag to show the student's name
	 $student_h2_text = $selected_student->getElementsByTagName("first")->item(0)->nodeValue .
									 " " . $selected_student->getElementsByTagName("last")->item(0)->nodeValue;
	 P2_Utils::generate_text_element($services_dom,$content_div,"h2",$student_h2_text);
	 // Appends the new student div to the content div
	 $content_div->appendChild($new_div);
	 // Gets the URI from the student node and trims white space from it
	 $student_RSS_URI = trim($selected_student->getElementsByTagName("url")->item(0)->nodeValue);
	 // Gets the response headers from the student RSS uri for detecting if it is available or not
	 $student_RSS_headers = @get_headers($student_RSS_URI);
	 // Checks if the response headers from the student RSS uri gave an 200 / OK code
	 if($student_RSS_headers[0] == "HTTP/1.1 200 OK") {
		  // The line below is used for debugging to check student RSS feeds manually
		  //generateMessage($services_dom,$content_div,$student_RSS_URI);
		  // Creates a new domdocument to store the xml from the student's RSS feed
		  $student_dom = new DOMDocument();
		  // Gets the student's RSS feed XML from the RSS feed's URI as a string
		  $student_RSS_XML = file_get_contents($student_RSS_URI);
		  // Loads the RSS string into XML
		  $student_dom->loadXML($student_RSS_XML);
		  // Loops through the first two items in the RSS feed
		  for($i=0;$i<2;$i++){
				// Creates a div to hold an individual news item
				$item_div = $services_dom->createElement("div");
				$item_div->setAttribute("class","articleDiv");
				// Gets the news item's title, pubDate, and description
				$item = $student_dom->getElementsByTagName("item")->item($i);
				$title = $item->getElementsByTagName("title")->item(0)->nodeValue;
				$pubDate = $item->getElementsByTagName("pubDate")->item(0)->nodeValue;
				$description = $item->getElementsByTagName("description")->item(0)->nodeValue;
				// Creates a h3 tag for the news item title and appends it to the item div
				P2_Utils::generate_text_element($services_dom,$item_div,"h3",$title);
				// Creates a p tag for the news item pubDate and appends it to the item div
				$news_pubDate_p = P2_Utils::generate_text_element($services_dom,$item_div,"p",$pubDate);
				$news_pubDate_p->setAttribute("class","newsDate");
				// Creates a p tag for the news item description and appends it to the item div
				P2_Utils::generate_text_element($services_dom,$item_div,"p",$description);
				// Appends the individual news item div to the student RSS container div
				$new_div->appendChild($item_div);
		  }
	 } else {
		  // Generates an error message if the RSS feed was not found
		  generateMessage($services_dom,$content_div,"No RSS feed was found at " . $student_RSS_URI);
	 }
}
// Echos the DOM in order to generate an html page
echo $services_dom->saveHTML();
?>
<?php
include( "LIB_project1.php" );
include( "P2_Utils.class.php" );
// Loads the HTML from the template
// 	This uses identical code to project 1 but gets a different template than other pages
//	 	 (which is why it uses a function from P2_Utils and not LIB_project1)
$index_html = P2_Utils::get_template2();
$index_dom = new DOMDocument();
@$index_dom->loadHTML($index_html);
// Gets the banner div and adds images to it
$index_banner_div = $index_dom->getElementById("bannerDiv");
//get_banner($index_dom,$index_banner_div);
P2_Utils::get_banner2($index_dom,$index_banner_div,0);
// Creates editorialDiv for the editorial picture and letter
$editorial_div = $index_dom->createElement('div');
$editorial_div->setAttribute("id","editorialDiv");
// Creates the editorial image and appends it to editorialDiv
$editorial_img = $index_dom->createElement('img');
$editorial_img->setAttribute("src","sam.jpg");
$editorial_img->setAttribute("alt","A picture of the editor Sam Wechter");
$editorial_img->setAttribute("width","200");
$editorial_img->setAttribute("height","350");
$editorial_img->setAttribute("id","editorialImg");
$editorial_div->appendChild($editorial_img);
// Gets the editorial letter text and appends it to editorialDiv
$editorial_p_container = $index_dom->createElement('div');
$editorial_p_container->setAttribute("id","editorialLetterContainer");
$editorial_p = $index_dom->createElement('p');
$editorial_p->setAttribute("id","editorialP");
//$editorial_text = file_get_contents("editorial.txt");
$editorial_xml = P2_Utils::load_XML("editorial.xml");
$editorial_text = $editorial_xml->getElementsByTagName("content")->item(0)->nodeValue;
$editorial_text_node = $index_dom->createTextNode($editorial_text);
$editorial_p->appendChild($editorial_text_node);
$editorial_p_container->appendChild($editorial_p);
$editorial_div->appendChild($editorial_p_container);
// Appends editorialDiv to contentDiv
$content_div = $index_dom->getElementById('contentDiv');
$content_div->appendChild($editorial_div);
// Creates recentNewsDiv and adds articles to it
$recent_news_div = $index_dom->createElement('div');
$recent_news_div->setAttribute("id","recentNewsDiv");
$recent_news_h2 = $index_dom->createElement('h2');
$recent_news_h2->setAttribute("id","recentNewsH2");
$recent_news_h2_text_node = $index_dom->createTextNode("Recent News");
$recent_news_h2->appendChild($recent_news_h2_text_node);
$recent_news_div->appendChild($recent_news_h2);
// Appends recentNewsDiv to the index dom
$content_div->appendChild($recent_news_div);
$news_div = $index_dom->createElement("div");
//$news_article_array = get_news_items();
//$articles = parse_news_items($index_dom, $news_article_array, $article_index, $articles_per_page);
$news_article_xml = P2_Utils::load_XML("news.xml");
$article_index = $news_article_xml->getElementsByTagName("item")->length;
$articles_per_page = 3;
$articles = P2_Utils::parse_news_items2($index_dom, $news_article_xml, $article_index, $articles_per_page);
foreach($articles as $article) {
	 $recent_news_div->appendChild($article);
}

// Adds a service div
$services_div = $index_dom->createElement("div");
$services_div->setAttribute("id","servicesDiv");
// Gets the services XML data
$services_list_XML = P2_Utils::load_XML("rss_class.xml");
$service_nodelist = $services_list_XML->getElementsByTagName("choice");
foreach($service_nodelist as $service_node) {
	 if( $service_node->getAttribute( "selected" ) == "yes" ) {
		  $service_div = $index_dom->createElement("div");
		  $service_div->setAttribute("class","serviceDiv");
		  $service_name = $service_node->getElementsByTagName("name")->item(0)->nodeValue;
		  $service_name_h2 = $index_dom->createElement("h2");
		  $service_name_h2_textnode = $index_dom->createTextNode($service_name);
		  $service_name_h2->appendChild($service_name_h2_textnode);
		  $service_div->appendChild($service_name_h2);
		  // Loads the service's RSS
		  $service_uri = trim($service_node->getElementsByTagName("url")->item(0)->nodeValue);
		  //$service_RSS = P2_Utils::load_XML($service_uri);
		  $service_dom = new DOMDocument();
		  $service_XML_string = file_get_contents($service_uri);
		  $service_dom->loadXML($service_XML_string);
		  // Gets the item's title and places it in a CDATA section within an anchor tag
		  $service_title = $service_dom->getElementsByTagName("item")->item(0)->getElementsByTagName("title")->item(0)->nodeValue;
		  $service_title_CDATA = $index_dom->createCDATASection($service_title);
		  $service_title_CDATA_holder = $index_dom->createElement("a");
		  $service_title_CDATA_holder->appendChild($service_title_CDATA);
		  // Sets the anchor's href attribute
		  $item_uri = $service_dom->getElementsByTagName("item")->item(0)->getElementsByTagName("link")->item(0)->nodeValue;
		  $service_title_CDATA_holder->setAttribute("href",$item_uri);
		  $service_div->appendChild($service_title_CDATA_holder);
		  // Gets the item's description and places it in a CDATA section within a div
		  $service_description = $service_dom->getElementsByTagName("item")->item(0)->getElementsByTagName("description")->item(0)->nodeValue;
		  $service_description_CDATA = $index_dom->createCDATASection($service_description);
		  $service_CDATA_holder = $index_dom->createElement("div");
		  $service_CDATA_holder->appendChild($service_description_CDATA);
		  $service_div->appendChild($service_CDATA_holder);
		  $services_div->appendChild($service_div);
	 }
}
$content_div->parentNode->appendChild($services_div);
// Adds a user info div and generates data for it
$user_div = $index_dom->getElementById('userInfoDiv');
generateUserInfo($index_dom, $user_div);
// Echos the DOM in order to generate an html page
echo $index_dom->saveHTML();
?>
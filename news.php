<?php
include( "LIB_project1.php" );
include( "P2_Utils.class.php" );
// Loads the HTML from the template
$news_html = get_template();
$news_dom = new DOMDocument();
@$news_dom->loadHTML($news_html);
// Gets the banner div and adds images to it
$news_banner_div = $news_dom->getElementById("bannerDiv");
P2_Utils::get_banner2($news_dom,$news_banner_div,0);
$content_div = $news_dom->getElementById('contentDiv');
// Gets news items from news.txt
//$news_article_array = get_news_items();
// Gets news items from news.xml
$news_article_xml = P2_Utils::load_XML("news.xml");
$number_of_articles = $news_article_xml->getElementsByTagName("item")->length;
// Creates a select input so visitors can choose the number of news items per page
$articles_per_page = 5;
$articles_per_page_selector = $news_dom->createElement("select");
$articles_per_page_selector->setAttribute("id","pageLimitSelector");
// 5
$articles_per_page_option_one = $news_dom->createElement("option");
$articles_per_page_option_one->setAttribute("value","5");
$articles_per_page_option_one_text = $news_dom->createTextNode("5");
$articles_per_page_option_one->appendChild($articles_per_page_option_one_text);
$articles_per_page_selector->appendChild($articles_per_page_option_one);
// 10
$articles_per_page_option_two = $news_dom->createElement("option");
$articles_per_page_option_two->setAttribute("value","10");
$articles_per_page_option_two_text = $news_dom->createTextNode("10");
$articles_per_page_option_two->appendChild($articles_per_page_option_two_text);
$articles_per_page_selector->appendChild($articles_per_page_option_two);
// 20
$articles_per_page_option_three = $news_dom->createElement("option");
$articles_per_page_option_three->setAttribute("value","20");
$articles_per_page_option_three_text = $news_dom->createTextNode("20");
$articles_per_page_option_three->appendChild($articles_per_page_option_three_text);
$articles_per_page_selector->appendChild($articles_per_page_option_three);
// 50
$articles_per_page_option_four = $news_dom->createElement("option");
$articles_per_page_option_four->setAttribute("value","50");
$articles_per_page_option_four_text = $news_dom->createTextNode("50");
$articles_per_page_option_four->appendChild($articles_per_page_option_four_text);
$articles_per_page_selector->appendChild($articles_per_page_option_four);
// Gives the selector a label appends it to content div
$articles_per_page_label = $news_dom->createElement("label");
$articles_per_page_label->setAttribute("for","pageLimitSelector");
$articles_per_page_label_text = $news_dom->createTextNode("Articles per page: ");
$articles_per_page_label->appendChild($articles_per_page_label_text);
$content_div->appendChild($articles_per_page_label);
// Appends the selector to the content div
$content_div->appendChild($articles_per_page_selector);
// Gives the selector a submit button and appends it to content div
$articles_per_page_button = $news_dom->createElement("input");
$articles_per_page_button->setAttribute("type","button");
$articles_per_page_button->setAttribute("value","Submit");
$articles_per_page_button->setAttribute("onclick","newsRedirect()");
$content_div->appendChild($articles_per_page_button);
// Adds javascript code to the page that allows the button to redirect
$new_script = $news_dom->createElement("script");
$new_script_code = 'function newsRedirect() {
								 window.location = "news.php?articlelimit=" +
									 document.getElementById("pageLimitSelector").value
}';
$new_script->appendChild($news_dom->createTextNode($new_script_code));
$content_div->appendChild($new_script);
// Gets the number of articles per page to be displayed
if($_GET['articlelimit']) {
	 $articles_per_page = $_GET['articlelimit'];
}
// Finds the maximum page number by dividing the number of articles by articles per page
$maximum_page = ceil($number_of_articles / $articles_per_page);
if($_GET['page']) {
	 $currentPage = $_GET['page'];
} else {
	 $currentPage = 1;
}
// Checks if the user tried going outside of the range of pages and fixes it by changing $currentPage
if($currentPage > $maximum_page) {
	 $currentPage = $maximum_page;
}
if($currentPage < 1) {
	 $currentPage = 1;
}
// Finds the index to start retrieving news items at
$article_index = $number_of_articles - (($currentPage - 1) * $articles_per_page);
// Gets an array of news items inside of divs
$articles = P2_Utils::parse_news_items2($news_dom, $news_article_xml, $article_index, $articles_per_page);
//$articles = parse_news_items($news_dom,$news_article_array, $article_index, $articles_per_page);
// Loops through the array of news items and individually adds them to the content div
foreach($articles as $article) {
	 $content_div->appendChild($article);
}
// Creates links for each page and adds them to the content div
for($j = 1; $j <= $maximum_page; $j++) {
	 $page_link = $news_dom->createElement("a");
	 $page_link->setAttribute("href","news.php?page=$j&articlelimit=$articles_per_page");
	 $page_link->setAttribute("class","pageLink");
	 $page_link_text_node = $news_dom->createTextNode("$j");
	 $page_link->appendChild($page_link_text_node);
	 $content_div->appendChild($page_link);
}
// Echos the DOM in order to generate an html page
echo $news_dom->saveHTML();
?>
<?php
// This PHP page is currently unfinished and unused since I did not take the right approach to permalinking initially.
include( "LIB_project1.php" );
include( "P2_Utils.class.php" );
$news_article_xml = P2_Utils::load_XML("news.xml");
$news_item_nodelist = $news_article_xml->getElementsByTagName("item");
echo "looping through news items...<br/>";
$news_item_counter = 0;
foreach($news_item_nodelist as $news_item_node) {
	 $title = $news_item_node->getElementsByTagName("title")->item(0)->nodeValue;
	 $pubDate = $news_item_node->getElementsByTagName("pubDate")->item(0)->nodeValue;
	 $content = $news_item_node->getElementsByTagName("content")->item(0)->nodeValue;
	 P2_Utils::make_article_page($news_item_counter,$title,$pubDate,$content);
	 if(!($news_item_node->getElementsByTagName("link")->item(0))) {
		  echo "Added permalink to article " . $news_item_counter . ".<br/>";
		  $item_permalink = $news_article_xml->createElement("link");
		  $item_uri = "http://people.rit.edu/~saw7456/539/project2/articles/article" . $news_item_counter . ".php";
		  $item_permalink->nodeValue = $item_uri;
		  $news_item_node->appendChild($item_permalink);
	 }
	 $item_permalink = $news_item_node->getElementsByTagName("link")->item(0);
	 $item_permalink->nodeValue = "http://people.rit.edu/~saw7456/539/project2/articles/article" . $news_item_counter . ".php";
	 $news_item_counter++;
}
$news_article_xml->save("news.xml");
?>
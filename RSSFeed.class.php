<?php
class RSSFeed {
	 public $title, $date, $content;
	 function __construct($title,$date,$content) {
		  // Stores the location of the RSS file
		  $RSS_file = "project2.rss";
		  // Creates an instance of DOMDocument and loads the RSS file
		  $dom = new DOMDocument();
		  // Sets options in $dom so the RSS output will be easier to read
		  $dom->preserveWhiteSpace = false;
		  $dom->formatOutput = true;
		  // Creates the RSS feed text and stores it in a string
		  $rssString = '<?xml version="1.0" encoding="utf-8"?>';
		  $rssString .= '<rss version="2.0">';
		  $rssString .= '<channel>';
		  $rssString .= "<title>Sam Wechter's 539 Project 2 RSS 2.0 Feed</title>";
		  $rssString .= '<link>http://people.rit.edu/~saw7456/539/Project2/index.php</link>';
		  $rssString .= '<description>This is my RSS 2.0 feed for news articles in my 539 project 2 content management system.</description>';
		  $date_time = new DateTime();
		  $lastBuildText = $date_time->format('r');
		  $rssString .= '<lastBuildDate>' . $lastBuildText . '</lastBuildDate>';
		  $rssString .= '<language>en-us</language>';
		  $rssString .= '</channel>';
		  $rssString .= '</rss>';
		  // Loads the RSS feed text as XML
		  $dom->loadXML($rssString);
		  // Gets the channel element to add RSS items to
		  $channel = $dom->getElementsByTagName("channel")->item(0);
		  // Checks if news.xml exists
		  if(file_exists("news.xml")) {
				// Creates a domdocument to store the news xml
				$news_xml = new DOMDocument();
				// Loads the news xml
				$news_xml->load("news.xml");
				// Gets a nodelist of news items
				$news_nodelist = $news_xml->getElementsByTagName("item");
				$news_nodelist_length = $news_nodelist->length;
				// Checks the length of the news item nodelist
				//  if it is over 10, limit the number of desired nodes to 10
				if($news_nodelist_length < 10) {
					 $num_desired_nodes = $news_nodelist_length;
				} else {
					 $num_desired_nodes = 10;
				}
				// Gets the starting index of the nodelist
				//  This is used because RSS feeds put new items at the top but my
				//  news XML has newest items at the bottom
				$news_nodelist_index = $news_nodelist_length - 1;
				// Loops through the news nodelist
				for($i = $news_nodelist_index; $i > ($news_nodelist_length - $num_desired_nodes - 1); $i--) {
					 // Gets the news item node's title, pubDate, and description
					 $current_item = $news_nodelist->item($i);
					 $current_title = $current_item->getElementsByTagName("title")->item(0)->nodeValue;
					 $current_pubDate = $current_item->getElementsByTagName("pubDate")->item(0)->nodeValue;
					 $current_description = $current_item->getElementsByTagName("content")->item(0)->nodeValue;
					 // Creates a new RSS feed item with data from the news item node
					 $rss_item = $dom->createElement("item");
					 $rss_item_title = $dom->createElement("title");
					 $rss_item_title->nodeValue = $current_title;
					 $rss_item_description = $dom->createElement("description");
					 $rss_item_description->nodeValue = $current_description;
					 $rss_item_pubDate = $dom->createElement("pubDate");
					 $rss_item_pubDate->nodeValue = $current_pubDate;
					 $rss_item->appendChild($rss_item_title);
					 $rss_item->appendChild($rss_item_description);
					 $rss_item->appendChild($rss_item_pubDate);
					 // Appends the new RSS feed item node to the channel node
					 $channel->appendChild($rss_item);
				}
		  // Saves the newly-created RSS feed
		  $dom->save("project2.rss");
		  // Changes the permissions of the new RSS feed to it can be viewed
		  chmod("project2.rss", 0644);
		  }
	 }
}
?>
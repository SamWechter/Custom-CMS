<?php
include( "LIB_project1.php" );
include( "RSSFeed.class.php" );
include( "P2_Utils.class.php" );
// Loads the HTML from the template
$advertisements_html = get_template();
$advertisements_dom = new DOMDocument();
@$advertisements_dom->loadHTML($advertisements_html);
// Gets the banner div and adds images to it
$advertisements_banner_div = $advertisements_dom->getElementById("bannerDiv");
P2_Utils::get_banner2($advertisements_dom,$advertisements_banner_div,0);
// Gets the contentDiv
$content_div = $advertisements_dom->getElementById('contentDiv');
// Creates an h1 and adds it to contentDiv
P2_Utils::generate_text_element($advertisements_dom,$content_div,"h1","Advertisements");
// Makes a div to contain all advertisements to simply CSS
$ad_container_div = $advertisements_dom->createElement("div");
$ad_container_div->setAttribute("id","adContainer");
// Gets the advertisements XML data
$advertisements_XML = P2_Utils::load_XML(adXML);
// Gets a nodelist of advertisements
$advertisements_nodelist = $advertisements_XML->getElementsByTagName("advertisement");
// Loops through advertisement nodes
foreach($advertisements_nodelist as $advertisement_node) {
	 // Checks if the advertisement node is selected
	 if( $advertisement_node->getAttribute( "selected" ) == "yes" ) {
		  // Creates a div to hold the advertisement
		  $advertisement_div = $advertisements_dom->createElement("div");
		  $advertisement_div->setAttribute("class","advertisementDiv");
		  // Creates an img to hold the advertisement image
		  $advertisement_img = $advertisements_dom->createElement("img");
		  // Gets the advertisement image's URI
		  $advertisement_src = "ads/" . $advertisement_node->getElementsByTagName("uri")->item(0)->nodeValue;
		  // Sets the src of the img to the advertisement image's URI
		  $advertisement_img->setAttribute("src",$advertisement_src);
		  // Creates an alt attribute for the img based on the advertisement's title
		  $advertisement_name = $advertisement_node->getElementsByTagName("title")->item(0)->nodeValue;
		  $advertisement_img->setAttribute("alt",$advertisement_name);
		  // Appends the advertisement img to the advertisement div
		  $advertisement_div->appendChild($advertisement_img);
		  // Appends the advertisement div to the ad container div
		  $ad_container_div->appendChild($advertisement_div);
	 }
}
// Appends the ad container div to the content div
$content_div->appendChild($ad_container_div);
// Echos the DOM in order to generate an html page
echo $advertisements_dom->saveHTML();
?>
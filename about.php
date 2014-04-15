<?php
include( "LIB_project1.php" );
include( "P2_Utils.class.php" );
$about_html = get_template();
$about_dom = new DOMDocument();
// Loads the HTML from the template
@$about_dom->loadHTML($about_html);
// Gets the banner div and adds images to it
$about_banner_div = $about_dom->getElementById("bannerDiv");
P2_Utils::get_banner2($about_dom,$about_banner_div,0);
$content_div = $about_dom->getElementById('contentDiv');
// Creates an h1 and adds it to contentDiv
$about_h1_text = "This page is currently under construction. Please check back soon!";
P2_Utils::generate_text_element($about_dom,$content_div,"h1",$about_h1_text);
// Echos the DOM in order to generate an html page
echo $about_dom->saveHTML();
?>
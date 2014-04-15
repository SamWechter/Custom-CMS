<?php
/*
This work was created by Sam Wechter for project one of the Server-Side Programming
class at the Rochester Institute of Technology.
*/
// The template used is specified here so it can be easily found and changed if needed
$template = "http://people.rit.edu/~saw7456/539/project2/template.html";
/* get_template()
 * DESCRIPTION:
 * 			Reads then returns a template file from the same directory
 * OUTPUTS:
 * 			Returns the string read from template.html
*/
function get_template() {
	 global $template;
	 $html_template = file_get_contents($template);
	 return $html_template;
}
/* get_banner($the_dom,$banner_div)
 * DESCRIPTION:
 * 			Adds logo and banner images to the specified DOM and div
 * INPUTS:
 * 			$the_dom						The dom to which new elements for the banner are added
 * 			$banner_div					The div which logo and banner images are added to
*/
function get_banner($the_dom,$banner_div) {
	 $logo_img = $the_dom->createElement("img");
	 $logo_img->setAttribute("src","Wechter News Logo.jpg");
	 $logo_img->setAttribute("alt","Sam Wechter News Logo");
	 $logo_img->setAttribute("width","100");
	 $logo_img->setAttribute("height","100");
	 $banner_div->appendChild($logo_img);
	 // Loads and parses banners.txt then parses it into arrays
	 $banner_lines = file("banners/banners.txt");
	 $banner_locations = array();
	 $banner_displayed_amounts = array();
	 $banner_weights = array();
	 foreach($banner_lines as $banner_line) {
		  list($banner_location, $banner_displayed_amount, $banner_weight) = explode('|', $banner_line);
		  array_push($banner_locations,$banner_location);
		  array_push($banner_displayed_amounts,$banner_displayed_amount);
		  array_push($banner_weights,$banner_weight);
	 }
	 // Sorts the displayed amount and location arrays so both are ordered by the amount of times
	 //	a banner has been displayed.
	 array_multisort($banner_displayed_amounts,$banner_locations);
	 // Creates an img element for the banner and adds it to the input div
	 $banner_img = $the_dom->createElement("img");
	 $banner_loc = "banners/$banner_locations[0]";
	 $banner_img->setAttribute("src",$banner_loc);
	 $banner_img->setAttribute("alt","Advertisement banner");
	 $banner_img->setAttribute("id","adBanner");
	 $banner_img->setAttribute("height","100");
	 $banner_div->appendChild($banner_img);
	 // Writes the banner data back in to banners.txt to update the
	 // 	amount of times each banner has been displayed
	 $banner_displayed_amounts[0] = $banner_displayed_amounts[0] + 1;
	 $banner_strings = array();
	 for($k = 0; $k < count($banner_locations); $k++) {
		  $banner_string = $banner_locations[$k] . "|" . $banner_displayed_amounts[$k] . "|" . "1";
		  array_push($banner_strings,$banner_string);
	 }
	 $banner_completed_string = implode("\n", $banner_strings);
	 file_put_contents("banners/banners.txt",$banner_completed_string);
}
/* get_news_items()
 * DESCRIPTION:
 * 			Reads then returns the contents of news.txt
 * OUTPUTS:
 * 			Returns an array containing the lines from news.txt
*/
function get_news_items() {
	 $news_array = file("news.txt");
	 return $news_array;
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
function parse_news_items($tmp_dom, $news_article_array, $article_index, $articles_per_page) {
	 $div_array = array();
	 for($i = $article_index; $i > ($article_index - $articles_per_page); $i--) {
		  if ($i >= 0) {
				$current_article = $i - 1;
				list($news_datetime, $news_title, $news_text) = explode('|', $news_article_array[$current_article]);
				$article_div = $tmp_dom->createElement("div");
				$article_div->setAttribute("class","articleDiv");
				$article_title = $tmp_dom->createElement("h2");
				$article_title_text_node = $tmp_dom->createTextNode($news_title);
				$article_title->appendChild($article_title_text_node);
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
/* generateUserInfo($what_dom, $user_info_div)
 * DESCRIPTION:
 * 			Gets information about the user via PHP and JavaScript then
 * 				creates html elements to contain that information and adds
 * 				them to the DOM
 * INPUTS:
 * 			$what_dom					The DOM which new elements are added to
 * 			$user_info_div				The <div> in which new elements are placed
 * OUTPUTS:
 * 			Returns an array containing the lines from news.txt
*/
function generateUserInfo($what_dom, $user_info_div) {
	 $user_info_div->setAttribute("style","border: 1px solid black");
	 $user_h3 = $what_dom->createElement("h3");
	 $user_h3_text = $what_dom->createTextNode("User Information");
	 $user_h3->appendChild($user_h3_text);
	 $user_info_div->appendChild($user_h3);
	 // Gets the user's IP address
	 $user_ip_p = $what_dom->createElement("p");
	 $user_ip = $_SERVER['REMOTE_ADDR'];
	 $user_ip_p_text = $what_dom->createTextNode("Your IP address is: $user_ip");
	 $user_ip_p->appendChild($user_ip_p_text);
	 $user_info_div->appendChild($user_ip_p);
	 // Gets the user's port used to connect to the server
	 $user_port_p = $what_dom->createElement("p");
	 $user_port = $_SERVER['REMOTE_PORT'];
	 $user_port_p_text = $what_dom->createTextNode("You used port $user_port
																	to connect to this server.");
	 $user_port_p->appendChild($user_port_p_text);
	 $user_info_div->appendChild($user_port_p);
	 // Gets the user's screen resolution
	 $user_screen_p = $what_dom->createElement("p");
	 $user_screen_p_js = $what_dom->createElement("script");
	 $user_screen_p_js_text = $what_dom->createTextNode("document.write(
										  'Your screen is ' + screen.width + ' pixels wide and ' +
										  screen.height + ' pixels tall.');");
	 $user_screen_p_js->appendChild($user_screen_p_js_text);
	 $user_info_div->appendChild($user_screen_p_js);
}
/* sanitizeString($var)
 * DESCRIPTION:
 * 			Sanitizes the input string and then returns it.
 * 			This function was borrowed from Professor Dean Ganskop's
 * 				Server-Side Programming homework assignment 5.
 * 				This function is NOT my own work and credit for it
 * 				belongs to Dean Ganskop.
 * INPUTS:
 * 			$var							The string which will be sanitized
 * OUTPUTS:
 * 			Returns the $var string after it has been sanitized
*/
function sanitizeString($var) {
	 // Strips whitespace from the beginning and the end of the string
	 $var = trim($var);
	 // Strips backslashes from the string
	 $var = stripslashes($var);
	 // Changes the string so that characters that correspond to html entities
	 //	are translated to said entities
	 //$var = htmlentities($var);
	 // Strips tags from the string
	 //$var = strip_tags($var);
	 // Returns the sanitized string
	 return $var;
}
/* validateFieldValue($formString)
 * DESCRIPTION:
 * 			Validates the input string by checking if it is set and
 * 				of a length greater than 0 characters (not empty)
 * INPUTS:
 * 			$formString					The string which will be validated
 * OUTPUTS:
 * 			Returns true or false based on whether or not the string is
 * 				valid
*/
function validateFieldValue($formString) {
	if ((isset($formString)) && (strlen($formString) > 0) ) {
		return true;
	} else {
		return false;
	}
}
/* emailCheck($value)
 * DESCRIPTION:
 * 			Checks if the input string contains a valid email address
 * 			This function was borrowed from the code provided for
 * 				Professor Dean Ganskop's Server-Side Programming homework
 * 				assignment 2. This function is NOT my own work and credit
 * 				for it belongs to Dean Ganskop.
 * INPUTS:
 * 			$value						The string which will be checked
 * OUTPUTS:
 * 			Returns true or false based on if the email string is valid or not
*/
function emailCheck($value) {
	 $reg = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/";
	 return preg_match($reg,$value);
}
/* emailCheck($value)
 * DESCRIPTION:
 * 			Generates a <p> containing the specified text and adds it to the input
 * 				DOM and div
 * INPUTS:
 * 			$d_dom						The DOM which the new <p> is added to
 * 			$d_div						The div to which the new <p> is added
 * 			$d_message					The message which will be placed in the <p>
*/
function generateMessage($d_dom,$d_div,$d_message) {
	 $d_message_p = $d_dom->createElement("p");
	 $d_message_p->setAttribute("id","feedbackMessage");
	 $d_message_p_text_node = $d_dom->createTextNode($d_message);
	 $d_message_p->appendChild($d_message_p_text_node);
	 $d_div->appendChild($d_message_p);
}
?>
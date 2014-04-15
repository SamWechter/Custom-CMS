<?php
include( "LIB_project1.php" );
include( "P2_Utils.class.php" );
// Loads the HTML from the template
$contact_html = get_template();
$contact_dom = new DOMDocument();
@$contact_dom->loadHTML($contact_html);
// Gets the banner div and adds images to it
$contact_banner_div = $contact_dom->getElementById("bannerDiv");
P2_Utils::get_banner2($contact_dom,$contact_banner_div,0);
$content_div = $contact_dom->getElementById('contentDiv');
// Checks if the contact form has been submitted then sends an email to the editor
if ($_POST["contactSent"] == "sent") {
	 $c_name = sanitizeString($_POST["contactName"]);
	 $c_email = sanitizeString($_POST["contactEmail"]);
	 $c_message = sanitizeString($_POST["contactMessage"]);
	 if(!validateFieldValue($c_name)) {
		  generateMessage($contact_dom,$content_div,"Please input a name.");
	 }
	 if(!validateFieldValue($c_email) || !emailCheck($c_email)) {
		  generateMessage($contact_dom,$content_div,"Please input a valid email address.");
	 }
	 if(!validateFieldValue($c_message)) {
		  generateMessage($contact_dom,$content_div,"Please input a message.");
	 }
	 if((validateFieldValue($c_name)) && (validateFieldValue($c_email)) && (emailCheck($c_email))
				&& (validateFieldValue($c_message))) {
		  $to = "saw7456@rit.edu";
		  $subject = "539 Project 1 contact form message from: " . $c_name;
		  $message = "$c_name has contacted you from your 539 Project 1 news website. They would like " . 
				"responses to their message to be directed to the following email address: $c_email . \n" .
				"Their message is: \n $c_message";
		  mail($to,$subject,$message);
		  generateMessage($contact_dom,$content_div,"Message sent successfully!");
	 }
}
	 
// Creates a h1 for the contact form and adds it to the DOM
$contact_h1 = P2_Utils::generate_text_element($contact_dom,$content_div,"h1","Contact the editor");
$contact_h1->setAttribute("id","contactH1");
// Creates a POST form
$contact_form = $contact_dom->createElement("form");
$contact_form->setAttribute("id","contactForm");
$contact_form->setAttribute("action","contact.php");
$contact_form->setAttribute("method","POST");
// Creates a hidden input for submission detection
$contact_hidden_input = $contact_dom->createElement('input');
$contact_hidden_input->setAttribute("type","hidden");
$contact_hidden_input->setAttribute("name","contactSent");
$contact_hidden_input->setAttribute("value","sent");
$contact_form->appendChild($contact_hidden_input);
// Creates a label and input for visitor name entry
$contact_form_name_label = $contact_dom->createElement("label");
$contact_form_name_label->setAttribute("for","contactName");
$contact_form_name_label_text_node = $contact_dom->createTextNode("Name: ");
$contact_form_name_label->appendChild($contact_form_name_label_text_node);
$contact_form->appendChild($contact_form_name_label);
$contact_form_name = $contact_dom->createElement("input");
$contact_form_name->setAttribute("type","text");
$contact_form_name->setAttribute("name","contactName");
$contact_form_name->setAttribute("id","contactName");
$contact_form->appendChild($contact_form_name);
$contact_spacer_a = $contact_dom->createElement("br");
$contact_form->appendChild($contact_spacer_a);
// Creates a label and input for visitor email entry
$contact_form_email_label = $contact_dom->createElement("label");
$contact_form_email_label->setAttribute("for","contactEmail");
$contact_form_email_label_text_node = $contact_dom->createTextNode("Email address: ");
$contact_form_email_label->appendChild($contact_form_email_label_text_node);
$contact_form->appendChild($contact_form_email_label);
$contact_form_email = $contact_dom->createElement("input");
$contact_form_email->setAttribute("type","text");
$contact_form_email->setAttribute("name","contactEmail");
$contact_form_email->setAttribute("id","contactEmail");
$contact_form->appendChild($contact_form_email);
$contact_spacer_b = $contact_dom->createElement("br");
$contact_form->appendChild($contact_spacer_b);
// Creates a label and textarea for visitor message entry
$contact_form_message = $contact_dom->createElement("textarea");
$contact_form_message->setAttribute("name","contactMessage");
$contact_form_message->setAttribute("id","contactMessage");
$contact_form_message->setAttribute("rows","15");
$contact_form_message->setAttribute("cols","100");
$contact_form->appendChild($contact_form_message);
$contact_spacer_c = $contact_dom->createElement("br");
$contact_form->appendChild($contact_spacer_c);
// Creates buttons for resetting and submitting the contact form
$contact_reset = $contact_dom->createElement('input');
$contact_reset->setAttribute("type","reset");
$contact_reset->setAttribute("value","Reset");
$contact_form->appendChild($contact_reset);
$contact_submit = $contact_dom->createElement('input');
$contact_submit->setAttribute("type","submit");
$contact_submit->setAttribute("value","Submit");
$contact_form->appendChild($contact_submit);
// Appends the contact form to contentDiv so it properly appears in the DOM
$content_div->appendChild($contact_form);
// Echos the DOM in order to generate an html page
echo $contact_dom->saveHTML();
?>
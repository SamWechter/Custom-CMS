<?php
		  include( "../LIB_project1.php" );
		  include( "../P2_Utils.class.php" );
		  $article_html = get_template();
		  $article_dom = new DOMDocument();
		  @$article_dom->loadHTML($article_html);
		  $article_banner_div = $article_dom->getElementById("bannerDiv");
		  P2_Utils::get_banner2($article_dom,$article_banner_div,1);
		  $content_div = $article_dom->getElementById("contentDiv");
		  // Adds the article title to the page in an h1
		  $article_h1 = $article_dom->createElement("h1");
		  $article_h1_text = $article_dom->createTextNode("Many many updates!");
		  $article_h1->appendChild($article_h1_text);
		  $content_div->appendChild($article_h1);
		  // Adds the article pubDate to the page in an h3
		  $article_date_h3 = $article_dom->createElement("h3");
		  $article_date_h3_text = $article_dom->createTextNode("Sun, 21 Apr 2013 20:30:46 -0400");
		  $article_date_h3->appendChild($article_date_h3_text);
		  $content_div->appendChild($article_date_h3);
		  // Adds the article pubDate to the page in a p
		  $article_description_p = $article_dom->createElement("p");
		  $article_description_p_text = $article_dom->createTextNode("I'm done with setting up the advertisements page and its associated admin section now. Additionally, styling has been adjusted across multiple pages to make the site look better overall. I'm now working on a banner maintenance section on the admin page to allow admins to select what banners will be shown in the rotation.");
		  $article_description_p->appendChild($article_description_p_text);
		  $content_div->appendChild($article_description_p);
		  // Adds a link to navigate back to news.php
		  $article_back_a = $article_dom->createElement("a");
		  $article_back_a->setAttribute("href","http://people.rit.edu/~saw7456/539/project2/news.php");
		  $article_back_a->setAttribute("alt","Back to the News page");
		  $article_back_a_text = $article_dom->createTextNode("Back to News");
		  $article_back_a->appendChild($article_back_a_text);
		  $content_div->appendChild($article_back_a);
		  echo $article_dom->saveHTML();
		  ?>

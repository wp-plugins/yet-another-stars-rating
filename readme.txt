=== Yasr - Yet Another Stars Rating ===
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8S963KJ3UW5XG
Tags: Review, Star, Snippet
Requires at least: 3.5
Tested up to: 3.9.1
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Yet Another Stars Rating is a simple plugin which allows you and / or your visitor to rate a post or element. Ideal for review's website

== Description ==
Yet Another Stars Rating (YASR) is a new system review based on jquery plugin RateIT. 
With YASR you can make your own review or let your visitors vote, and you can even create multiple sets (a set of stars for each aspect to
rate). Review scores will be indexed by search engines through snippets .
Most important, if you are a gd star rating user, with YASR you can import all the data from gd star rating (which isn't maintained anymore).

= How To use =

= Reviewer Vote =
Once YASR is installed, when you create or update a page or a post, a box (metabox) will be available in the upper right corner where you'll
be able to insert the overall rating. You can either place the overall rating automatically at the beginning or the end of a post (look in "Settings"
-> "Yet Another Stars Rating: Settings"), or wherever you want in the page using the shortcode [yasr_overall_rating] (easily added through the visual editor).

= Visitor Votes = 
You can give your users the ability to vote, pasting the shortcode [yasr_visitor_votes] where you want the stars to appear.
Again, this can be placed automatically at the beginning or the end of each post; the option is in "Settings" -> "Yet Another Stars Rating: Settings".

= Multi Set =
Multisets give the opportunity to score different aspects for each review: for example, if you're reviewing a videogame, you can create the aspects "Graphics",
"Gameplay", "Story", etc.

= Importing data from gd star rating =
If you're using gd-star-rating, YASR is the plugin for you! You can import from gd-star-rating "overall_rating", "visitor_votes" and all the multisets with
their respective score. Once YASR is installed you just have to go to "Settings" -> "Yet Another Stars Rating: Settings" and start the import (last box at the bottom).
This operation can take some time, don't stop it! Once it's done you just need to replace the gd-star-rating's shortcode with the YASR tags.
If with gd-star-rating you're using a different number of stars from the default of 5, YASR will automatically convert all the scores in a range from 1 to 5.


== Installation ==
1. Install Yet Another Stars Rating either via the WordPress.org plugin directory, or by uploading the files to your server
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Yet Another Star Rating menu in Settings and set your options.


== Screenshots ==
1. Example of yasr in a videogame review
2. Another example of a restaurant review

== Changelog ==
= 0.0.2 =
* Using input type radio when select a multi set instead select / option 
* Added css style for table showing multiset
* Added yasr.css file
* Some code cleanup

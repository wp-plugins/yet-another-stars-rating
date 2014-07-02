=== Yasr - Yet Another Stars Rating ===
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=8S963KJ3UW5XG
Tags: Rating, Review, Star, Snippet, Rich snippet, Schema, Schema.org, Serp
Requires at least: 3.5
Tested up to: 3.9.1
Stable tag: 0.3.4
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Yet Another Stars Rating is a simple plugin which allows you and / or your visitor to rate a post or element. Ideal for review's website

== Description ==
Yet Another Stars Rating (YASR) is a new system review based on jquery plugin RateIT. 
With YASR you can make your own review or let your visitors vote, and you can even create multiple sets (a set of stars for each aspect to
rate). Review scores or visitor ratings will be indexed by search engines through snippets .
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

= 0.3.4 =
* In settings page is now possible add some custom text to make appear before "Overall Rating" or "Visitor's Rating" are showed
* In settings page is now possible to show "Overall Rating" only in the post / page or even in Home page, Archive Page, etc.
* Removed bug that could appear in some case when removing or adding field in multiset form editor
* After a multiset is created/edited now you get redirected on multiset settings tab and not in general settings
* Various bugfixes
* Under the hood changes

= 0.3.3 =
* Created a new shortcode that will allow to insert Top 10 highest rated product by author  
* When a post or page is permanently deleted, yasr will delete every votes for that post or page
* Overall Rating is now avaible in home page and archive pages
* Fixed "add element button" when only 1 multi set is used
* Updated jquery cookie from 1.4.0 to 1.4.1 and minified it
* Minor Bug fixes


= 0.3.2 =
* Forced multiset field name to be #555 . This is to avoid reading problem when using light font color

= 0.3.1 =
* Avoid multiple vote in a same post for logged in user
* Bug fixes and cleanup

= 0.3.0 =
* Now admin can choose if allow only logged in users to vote or logged in and anonymous
* Code cleanup and bug fixes

= 0.2.10 =
* Added loading image when ajax is called
* Added "select button" when choosing a multi set 

= 0.2.9 =
* Settings page has been rewritten: now it use multi tab navigation

= 0.2.7 =
* Many bug fixes on the settings page
* Minor change and code cleanup

= 0.2.5 =
* Bug fix on multi set 

= 0.2.4 =
* Now user can choose what kind of snippet use, if AggregateRating or Review 

= 0.2.3 = 
* [yasr_visitor_votes] it's now disabled outside a post or a page
* Security fixes on ajax functions
* Various fixes on multi-set settings
* Code cleanup

= 0.2.2 =
* Fixed doulbe ajax request on overall rating when used just 1 multiple set
* Many other minor bug fixes

= 0.2.1 =
* Fixed insert rating on multi set if only 1 is used 

= 0.2.0 =
* Fixed Table installation

= 0.1.3 =
* Fixed some security issues. Please Update

= 0.1.2 =
* Fixed Multi Icons

= 0.1.1 =
* Changed stars icons, now using the oxygen one
* Using big star when voting on multi set

= 0.1.0 =
* Added in admin dashboard votes log viewer
* .Po file updated 
* Updated rateit to version 1.0.22

= 0.0.4 =
* Fixed creation of a new multi set
* Added the opportunity to remove entire multi set
* Code cleanup

= 0.0.3 =
* Fixed bug describe if 
* Code Cleanup

= 0.0.2 =
* Using input type radio when select a multi set instead select / option 
* Added css style for table showing multiset
* Added yasr.css file
* Some code cleanup

=== Yasr - Yet Another Stars Rating ===
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AXE284FYMNWDC
Tags:  5 star, admin, administrator, AJAX, five-star, javascript, jquery, post rating, posts, rate, rating, rating platform, rating system, ratings, review, reviews, rich snippets, seo, star, star rating, stars, vote, Votes, voting, voting contest, schema, serp
Requires at least: 3.5
Contributors: Dudo 
Tested up to: 4.3
Stable tag: 0.9.3
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
This may not works if you use a caching plugin.

= Multi Set =
Multisets give the opportunity to score different aspects for each review: for example, if you're reviewing a videogame, you can create the aspects "Graphics",
"Gameplay", "Story", etc.

= Importing data from gd star rating =
If you're using gd-star-rating, YASR is the plugin for you! You can import from gd-star-rating "Overall Rating", "Visitor Votes" and all the multisets with
their respective score. Once YASR is installed you just have to go to "Settings" -> "Yet Another Stars Rating: Settings" and start the import (last box at the bottom).
This operation can take some time, don't stop it! Once it's done you just need to replace the gd-star-rating's shortcode with the YASR tags.
If with gd-star-rating you're using a different number of stars from the default of 5, YASR will automatically convert all the scores in a range from 1 to 5.

= Supported Languages =
* English
* Italian
* German (thanks to [Josef Seidl,](http://www.blog-it-solutions.de/) until version 0.6.5, than mp3-post )
* Polish (thanks to Hoek i Tutu Team)
* Dutch (thanks to  [Marcel Pol](http://zenoweb.nl ) )
* French (Thanks to Sébastien Gracia)
* Norwegian (Thanks to [Line Holm Anderssen, Anderssen Language Services](http://www.alanguageservices.com/) )
* Persian (Thanks to Babak Mehri )
* Brazilian Portuguese (Thanks to [Iuri](http://assistirfilmesonline.info) )
* Russian (Thanks to Ron)
* Spanish (Thanks to [Carlos](http://CGlevel.com) )
* Croatian (Thanks to Sanjin Barac)

Check [here](http://translate.yetanotherstarsrating.com/) to see if your translation is up to date

In this video I'll show you the "Auto Insert" feature and manual placement of YASR basic shortcodes.
[youtube https://youtu.be/M47xsJMQJ1E]

= Related Link =
* News and doc at [Yasr Official Site](http://yetanotherstarsrating.com/)
* [Demo site](http://demo.yetanotherstarsrating.com/)

= Press =
* [WPMUDEV](http://premium.wpmudev.org/blog/free-wordpress-ratings-testimonials-subscriber-count-plugins/)
* [BRIANLI.COM](http://brianli.com/yet-another-stars-rating-wordpress-plugin-review/)
* [WPEXPLORER](http://www.wpexplorer.com/google-rich-snippets-wordpress/)


Do you want more feature? [GO PRO!](https://yetanotherstarsrating.com/pro-version/)

> #### Pro Only features:
> * You can display as many rows as you like in all rankings.
> * Customizable star size is in rankings that use it.
> * You can change the text shown near the stars and choose to let it appear before or after them.
> * You can choose to show the Username or the display name in the charts that use it.
> * Category / post type filter in rankings.
> * Users can choose different ready to use sets or upload their own images.
> * Visitors can add their own reviews in comments.



== Installation ==
1. Install Yet Another Stars Rating either via the WordPress.org plugin directory, or by uploading the files to your server
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Yet Another Star Rating menu in Settings and set your options.

== Frequently Asked Questions ==

= What is "Overall Rating"? =
It is the vote given by who writes the review: readers are able to see this vote in read-only mode. Reviewer can vote using the box on the top rigth when writing a new article or post (he or she must have at least the "Author" role). Remember to insert this shortcode **[yasr_overall_rating]** to make it appear where you like. You can choose to make it appear just in a single post/page or in archive pages too (e.g. default Index, category pages, etc).

= What is "Visitor Rating"? =
It is the vote that allows your visitors to vote: just paste this shortcode **[yasr_visitor_votes]** where you want the stars to appear. This may not works if you use a caching plugin.

= What is "Multi Set"? =
It is the feature that makes YASR awesome. Multisets give the opportunity to score different aspects for each review: for example, if you're reviewing a videogame, you can create the aspects "Graphics", "Gameplay", "Story", etc. and give a vote for each one. To create a set, just go in "Settings" -> "Yet Another Stars Rating: Settings" and click on the "Multi Sets" tab. To insert it into a post, just paste the shortcode that YASR will create for you. 

= What is "Ranking reviews" ? =
It is the 10 highest rated item chart by reviewer. In order to insert it into a post or page, just paste this shortcode **[yasr_top_ten_highest_rated]**

= What is "Users' ranking" ? =
This is 2 charts in 1. Infact, this chart shows both the most rated posts/pages or the highest rated posts/pages. 
For an item to appear in this chart, it has to be rated twice at least.
Paste this shortcode to make it appear where you want **[yasr_most_or_highest_rated_posts]**

= What is "Most active reviewers" ? =
If in your site there are more than 1 person writing reviews, this chart will show the 5 most active reviewers. Shortcode is **[yasr_top_5_reviewers]**

= What is "Most active users" ? =
When a visitor (logged in or not) rates a post/page, his rating is stored in the database. This chart will show the 10 most active users, displaying the login name if logged in or "Anonymous" otherwise. The shortcode : **[yasr_top_ten_active_users]**

= Wait, wait! Do I need to keep in mind all this shortcode? =
Of course not: you can easily add it on the visual editor just by clicking on the yellow star and then choose what to insert.

[Demo site](http://demo.yetanotherstarsrating.com/)

== Screenshots ==
1. Example of yasr in a videogame review
2. Another example of a restaurant review
3. User's ranking showing most rated posts
4. User's ranking showing highest rated posts
5. Ranking reviews

== Changelog ==

= 0.9.3 =
* NEW FEATURE: wp rocket support, thanks to geek press
* FIXED: missing filed if a multiset element is leaved empty in the edit screen

= 0.9.2 =
* FIXED: bugfix on yasr_visitor_multiset
* FIXED: wrong review type showed in editing screen
* FIXED: could vote > 5 in overall rating if use digits instead of stars 
* FIXED: double voting in visitor multi set if "submit" button is pressed more than once 
* FIXED: progressbar in tooltips (thanks to Harry Milatz )
* FIXED: minor security fix

= 0.9.1 =
* Security fix

= 0.9.0 =
* FIXED: Fixed bug in multisets (here https://wordpress.org/support/topic/half-stars-not-saving more info)
* TWEAKED: Minor changes 

= 0.8.9 =
* FIXED: Minor bugfixes for multisets and visitor multisets


= 0.8.8 =
* NEW FEATURE: it's now possible to add the attribute postid on these shortcodes: yasr_overall_rating, yasr_visitor_votes, yasr_visitor_votes_readonly. It is only necessary when you wish to show another post/page's votes
* TWEAKED: Added facebook box in the settings
* TWEAKED: storage of the ip adress
* TWEAKED: Added Croatian language
* FIXED: support for rtl
* Minor bugfixes


= 0.8.7 =
* Minor changes and bugfixes

= 0.8.6 =
* Fixed bug on new installation if multi set are used

= 0.8.5 =
* TWEAKED: code changes and bug fixes on Multi Set shortcodes 

= 0.8.4 =
* NEW FEATURE: User can customize text after Visitor Votes.
* TWIKED: Visitor Votes have been partially rewritten, is much faster now
* REMOVED: Removed jquery cookie

= 0.8.3 =
* NEW FEATURE: added shortcode yasr_visitor_multiset. Now everyone can vonte in a Multi Set!
* FIXED: fixed schema type selection
* Minor changes

= 0.8.2 =
* NEW FEATURE: added shortcode yasr_visitor_votes_readonly
* NEW FEATURE: added support for wp super cache
* TWIKED: color settings for multi sets was moved from general settings to multi sets tab
* TWIKED: log widget is now fully translatable
* TWIKED: added brasilian language 
* TWIKED: minor fixes for translation

= 0.8.1 =
* Fixed: Undefined variable in yasr_visitor_votes shortcode
* Fixed: Fixed cursor style when is over the dashicon
* Tweaked: Stars' description is now translatable
* Twaeked: Dashicon doens't load if visitor stats are disabled
* Tweaked: Italian translation

= 0.8.0 =
* Stats for visitor votes works now on click and not on hover
* Buddypress compatibility
* Huge code cleanup on yasr_visitor_votes shortcode

= 0.7.9 =
* Fixed bug inserted on 0.2.2 when a logged in user try to update his own vote
* Minor changes on yasr_visitor_votes shortcode

= 0.7.8 =
* Fixed bug that occur when a post was rated from 2 users that use same browser
* Switched cdn, from google to jquery
* Persian Translation

= 0.7.7 =
* Removed an useless row in yasr_votes table.
* Minor change on [yasr_top_5_reviewers] shortcode
* Code cleanup

= 0.7.6 =
* Updated pro info.
* .po file update

= 0.7.5 =
* Code cleanup and bugfix in yasr_visitor_votes shortcode
* Updated Norwegian translation (Thanks to [Line](http://www.spilleautomatercasinobonuser.com))

= 0.7.4 =
* Security fix. Please update!

= 0.7.3 =
* Added support for plugins that adds class or attribute on images
* Code cleanup

= 0.7.2 =
* Another bugfix on yasr_visitor_votes shortcode.
* Minor changes

= 0.7.1 =
* Important change into yasr_visitor_votes shortcode

= 0.7.0 =
* Fixed bug for yasr_visitor_votes shortcode

= 0.6.9 =
* Yasr Visitor Votes shortag is finally avaible in archive pages!
* Code cleanup on yasr visitor votes shortag
* Javascript loaded again at the bottom in the frontend. Theme that doesn't use wp_footer will no be supported anymore

= 0.6.8 =
* In the front end, Javascript is loaded at the top of the page, cause out there still exists theme that doesn't use wp_footer function
* Smaller bugfixes
* French translation

= 0.6.7 =
* Small bugfix

= 0.6.6 =
* Bugfix: Auto insert in custom post type have been fixed
* "stars" inside the stats tooltip is now translatable

= 0.6.5 =
* New feature! If you use Visitors Rating stars set, you can now see statistics by hovering the mouse on the text [Total: X Average:Y]
* Bug fixes (stars changing size) in [yasr_visitor_rating]
* Fixed typos
* Huge cleanups

= 0.6.3 =
* Added Pro info in settings page

= 0.6.2 =
* Fixed bug for [yasr_overall_rating] in pages
* Code cleanup in [yasr_overall_rating]

= 0.6.1 =
* Bugfix: fixed mysql error if inserted the overall rating vote if post wasn't saved yet
* Fixed schema info when overall rating is used
* Language fix on [yasr_most_or_highest_rated_posts] 

= 0.6.0 =
* Bugfix: now it's not possible to vote 0
* Code Cleanup in yasr-settings-page and added link to yasr site, www.yetanotherstarsrating.com

= 0.5.9 =
* New feature: When writing a post or a page it's now possible to select the category that you're reviewing. This is a good improvement for SEO.
* Fixed loader when importing gd star rating data
* Fixed loader in yasr-settings-page

= 0.5.8 =
* Changed description in yasr shortcode generator (transaltor please take a look to the .po file) and other languages fix.
* Added the author info in the aggregate rating snippet 
* Minor bugfixes

= 0.5.7 =
* Bug fixes in [yasr_most_or_highest_rated_posts] chart

= 0.5.6 =
* Code cleanup and speed improvement on the [yasr_most_or_highest_rated_posts] chart
* Bug fixes in setting page 

= 0.5.5 =
* All the javascript have been moved from inline to external. It can be minimized so it's faster. DELETE ALL YOUR CACHES
* Fixed a possible bug if user manually delete data in a table

= 0.5.4 =
* New feature: you can used numbers instead stars to insert the "overall rating" value. Just go in the setting and choose what you want to use
* Fixed a division by 0 warning while using [yasr_most_or_highest_rated_posts] : this happened when there wan't enought data to shows 
* Many bux fix in [yasr_visitor_rating] : further this now it is faster

= 0.5.3 =
* Fixed a non closing div. This can cause problem when used in old template 

= 0.5.2 =
* Bugfixes

= 0.5.1 =
* Added support for wordpress 4.0
* Huge code cleanup in [yasr_visitor_votes]

= 0.5.0 =
* New feature: added auto insert for custom post types only (you will see this only if you use custom post types)
* Various bugfixes

= 0.4.9 =
* Fixed bug in rich snippet
* Under the hood changes and code cleanup

= 0.4.8 =
* Many bug fix and code changes for i18n
* Yasr.css have been sperated in 2 files: 1 for admin and 1 for frontend
* Added Italian translation
* Many many other little bug fixes

= 0.4.7 =
* Visitor votes has changed: if an user is logged in, now it's possibile to update the given vote
* Using css sprite instead of single images
* Using only 2 css instead 4
* Added German Translation (thanks to Josef Seidl)

= 0.4.6 =
* New feature: you can add your own css rules!
* Popup shortcode creator use the built-in wordpress style, fresher and lighter
* Fixed many bugs in shortcode [yasr_visitor_votes] : if you've had problem with a size that was not "large", try it now!
* Swiched color for all stars set: yellow by default, red only when is active
* Changed file name jquery.cookie.min.js in jquery-cookie.min.js, to avoid conflict with apache mod_security (thank's SubZeroD) 
* Many code changes

= 0.4.5 =
* New feature: Added custom post type support!
* Some fixes while editing / updating a multi-set
* Minor changes

= 0.4.4 =
* Code cleanup on chart Top 10 by visitors: on first load load it should be about 30% faster
* Showing a spinning image while chart Top 10 by visitors is loading

= 0.4.3 =
* Chart Top 10 by visitors have been rewritten. Now it's much much faster
* Added text on chart Top 10 overall ratings
* Minor changes

= 0.4.2 =
* Fixed log table, last updated broke it

= 0.4.1 =
* Added 2 sizes for "overall_rating" and "visitor_votes" stars sets, now you can choose between 16px, 24px and 32px (default)
* MANY code changes: it should be a little faster
* Fixed typo errors

= 0.4.0 =
* Popup shortcode creator it's now tabbed and got a link to the new doc
* Minor changes and bugfixes

= 0.3.9 =
* Fixed page exclusion

= 0.3.8 = 
* Added new chart: Top 10 most rated / highest rated posts from visitors (show up only posts rated at least twice)
* New setting: it's possibile now to explude pages from auto insert
* Css changes
* Various bugfixes

= 0.3.7 =
* Added new chart: Top 5 most active reviewers
* Added new chart: Top 10 most active users (in visitor rating)
* Changed popup for shortcode creator in visual editor, switched from thickbox to jquery ui dialog
* Removed javascript error when the shortcode creator is called
* Fixed multiset form editor
* Various bugfixes

= 0.3.6 =
* Changed permission: now while writing a post or page everyone with a role >= author can insert votes
* Some bugfixes

= 0.3.5 =
* Added a new dark style to better suite dark theme
* Added a new custom text to show when a non logged user has voted
* Css minor changes

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

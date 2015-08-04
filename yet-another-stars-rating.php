<?php
/**
 * Plugin Name:  Yet Another Stars Rating
 * Plugin URI: http://wordpress.org/plugins/yet-another-stars-rating/
 * Description: Rating system with rich snippets
 * Version: 0.9.3
 * Author: Dario Curvino
 * Author URI: https://yetanotherstarsrating.com/
 * License: GPL2
 */

/*

Copyright 2015 Dario Curvino (email : d.curvino@tiscali.it)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>
*/

    
define('YASR_VERSION_NUM', '0.9.3');

//Plugin relative path
define( "YASR_RELATIVE_PATH", dirname(__FILE__) );

//Plugin RELATIVE PATH without slashes (just the directory's name)
define( "YASR_RELATIVE_PATH_PLUGIN_DIR", dirname( plugin_basename(__FILE__) ) );

//Plugin language directory: here I've to use relative path
//because load_plugin_textdomain wants relative and not absolute path
define( "YASR_LANG_DIR", YASR_RELATIVE_PATH_PLUGIN_DIR . '/languages/' );

//Js directory absolute
define ("YASR_JS_DIR",  plugins_url( YASR_RELATIVE_PATH_PLUGIN_DIR . '/js/' ));

//CSS directory absolute
define ("YASR_CSS_DIR", plugins_url( YASR_RELATIVE_PATH_PLUGIN_DIR . '/css/' ));

//IMG directory absolute
define ("YASR_IMG_DIR", plugins_url( YASR_RELATIVE_PATH_PLUGIN_DIR . '/img/'));


/****** Getting options ******/

$stored_options = get_option( 'yasr_general_options' );

define ("YASR_AUTO_INSERT_ENABLED", $stored_options['auto_insert_enabled']);

if ( YASR_AUTO_INSERT_ENABLED == 1 ) {

	define ("YASR_AUTO_INSERT_WHAT", $stored_options['auto_insert_what']);
	define ("YASR_AUTO_INSERT_WHERE", $stored_options['auto_insert_where']);
	define ("YASR_AUTO_INSERT_SIZE", $stored_options['auto_insert_size']);
	define ("YASR_AUTO_INSERT_EXCLUDE_PAGES", $stored_options['auto_insert_exclude_pages']);
	define ("YASR_AUTO_INSERT_CUSTOM_POST_ONLY", $stored_options['auto_insert_custom_post_only']);

}

//Avoid undefined index
else {
	define ("YASR_AUTO_INSERT_WHAT", NULL);
	define ("YASR_AUTO_INSERT_WHERE", NULL);
	define ("YASR_AUTO_INSERT_SIZE", NULL);
	define ("YASR_AUTO_INSERT_EXCLUDE_PAGES", NULL);
	define ("YASR_AUTO_INSERT_CUSTOM_POST_ONLY", NULL);

}

define ("YASR_SHOW_OVERALL_IN_LOOP", $stored_options['show_overall_in_loop']);
define ("YASR_SHOW_VISITOR_VOTES_IN_LOOP", $stored_options['show_visitor_votes_in_loop']);
define ("YASR_TEXT_BEFORE_STARS", $stored_options['text_before_stars']);

if ( YASR_TEXT_BEFORE_STARS == 1 ) {

	define ("YASR_TEXT_BEFORE_OVERALL", $stored_options['text_before_overall']);
	define ("YASR_TEXT_BEFORE_VISITOR_RATING", $stored_options['text_before_visitor_rating']);
	define ("YASR_TEXT_AFTER_VISITOR_RATING", $stored_options['text_after_visitor_rating']);
	define ("YASR_CUSTOM_TEXT_USER_VOTED", $stored_options['custom_text_user_voted']);

}

define ("YASR_VISITORS_STATS", $stored_options['visitors_stats']);
define ("YASR_ALLOWED_USER", $stored_options['allowed_user']);
define ("YASR_SNIPPET", $stored_options['snippet']);
define ("YASR_METABOX_OVERALL_RATING", $stored_options['metabox_overall_rating']);    


//Get multi-set options
$multiset_options = get_option('yasr_multiset_options');

if($multiset_options && $multiset_options['scheme_color'] != '') {

	define("YASR_SCHEME_COLOR", $multiset_options['scheme_color']);

}

//Get stored style options 
$custom_style = get_option ('yasr_style_options');

if ($custom_style && $custom_style['textarea'] != '') {

	define ("YASR_CUSTOM_CSS_RULES", $custom_style['textarea']);

}

else {

	define ("YASR_CUSTOM_CSS_RULES", NULL);

}

/****** End Getting options ******/



// Include function file 
require (YASR_RELATIVE_PATH . '/lib/yasr-functions.php');

require (YASR_RELATIVE_PATH . '/lib/yasr-settings-functions.php');

require (YASR_RELATIVE_PATH . '/lib/yasr-db-functions.php');

require (YASR_RELATIVE_PATH . '/lib/yasr-ajax-functions.php');

require (YASR_RELATIVE_PATH . '/lib/yasr-shortcode-functions.php');

$version_installed = get_option('yasr-version') ;

//If this is a fresh new installation

if (!$version_installed ) {

	yasr_install();

}

global $wpdb;

define ("YASR_VOTES_TABLE", $wpdb->prefix . 'yasr_votes');

define ("YASR_MULTI_SET_NAME_TABLE", $wpdb->prefix . 'yasr_multi_set');

define ("YASR_MULTI_SET_FIELDS_TABLE", $wpdb->prefix . 'yasr_multi_set_fields');

define ("YASR_MULTI_SET_VALUES_TABLE", $wpdb->prefix . 'yasr_multi_values');

define ("YASR_LOG_TABLE", $wpdb->prefix . 'yasr_log');

define ("YASR_LOADER_IMAGE", YASR_IMG_DIR . "/loader.gif");


/****** backward compatibility functions ******/

//remove end july 2015
if ($version_installed && $version_installed < '0.8.6') {

	$new_fields=$wpdb->get_results("SELECT * FROM " . YASR_MULTI_SET_VALUES_TABLE . " LIMIT 1");

	foreach ($new_fields as $fields) {
		if(!isset($fields->number_of_votes)) {
			$new_fields = FALSE;
		}
	}



	if(!$new_fields) {

		$wpdb->query("ALTER TABLE " . YASR_MULTI_SET_VALUES_TABLE . " ADD number_of_votes BIGINT( 20 ) NOT NULL ,
						ADD sum_votes DECIMAL( 11, 1 ) NOT NULL ;");
	}

}

//remove end jun 2015
if ($version_installed && $version_installed < '0.8.2') {

	$multiset_option['scheme_color'] = $stored_options['scheme_color'];

	update_option("yasr_multiset_options", $multiset_option);

}

//remove end may 2015
if ($version_installed && $version_installed < '0.7.7') {

	$wpdb->query("ALTER TABLE " . YASR_VOTES_TABLE . " DROP reviewer_id");

}


/****** End backward compatibility functions ******/


if ($version_installed != YASR_VERSION_NUM) {

    update_option('yasr-version', YASR_VERSION_NUM);

}


?>

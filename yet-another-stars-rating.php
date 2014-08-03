<?php
/**
 * Plugin Name:  Yet Another Stars Rating
 * Plugin URI: http://wordpress.org/plugins/yet-another-stars-rating/
 * Description: Rating system with rich snippets
 * Version: 0.4.4
 * Author: Dario Curvino
 * Author URI: http://profiles.wordpress.org/dudo/
 * License: GPL2
 */

/*

Copyright 2014 Dario Curvino (email : d.curvino@tiscali.it)

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

    
define('YASR_VERSION_NUM', '0.4.4');

//Plugin absolute path
define( "YASR_ABSOLUTE_PATH", dirname(__FILE__) );

//Plugin RELATIVE PATH without slashes (just the directory's name)
define( "YASR_RELATIVE_PATH", dirname( plugin_basename(__FILE__) ) );

//Plugin language directory: here I've to use relative path
//because load_plugin_textdomain wants relative and not absolute path
define( "YASR_LANG_DIR", YASR_RELATIVE_PATH . '/languages/' );

//Js directory
define ("YASR_JS_DIR",  plugins_url( YASR_RELATIVE_PATH . '/js/' ));

//CSS directory
define ("YASR_CSS_DIR", plugins_url( YASR_RELATIVE_PATH . '/css/' ));

//IMG directory
define ("YASR_IMG_DIR", plugins_url( YASR_RELATIVE_PATH . '/img/'));

/* Include function file */

require (YASR_ABSOLUTE_PATH . '/lib/yasr-functions.php');

require (YASR_ABSOLUTE_PATH . '/lib/yasr-settings-functions.php');

require (YASR_ABSOLUTE_PATH . '/lib/yasr-db-functions.php');

require (YASR_ABSOLUTE_PATH . '/lib/yasr-ajax-functions.php');

require (YASR_ABSOLUTE_PATH . '/lib/yasr-shortcode-functions.php');

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

// To remove in the end of August
if ($version_installed && $version_installed < '0.3.4') {

	$wpdb->query ("ALTER TABLE " . YASR_MULTI_SET_FIELDS_TABLE . " MODIFY field_name VARCHAR( 23 )");

	$option = array();
	$option['auto_insert_enabled'] = 0;
	$option['auto_insert_what'] = 'overall_rating';
	$option['auto_insert_where'] = 'top';
	$option['show_overall_in_loop'] = 'disabled';
	$option['text_before_stars'] = 0;
	$option['snippet'] = 'overall_rating';
	$option['allowed_user'] = 'allow_anonymous';

	update_option("yasr_general_options", $option);

}

// To remove in the end of August
if ($version_installed && $version_installed < '0.3.8') {

	$option = get_option( 'yasr_general_options' );

	$option['auto_insert_exclude_pages'] = 'yes'; 

	update_option("yasr_general_options", $option);

}

if ($version_installed && $version_installed < '0.4.1') {

	$option = get_option( 'yasr_general_options' );

	$option['auto_insert_size'] = 'large';

	update_option("yasr_general_options", $option);

}

update_option('yasr-version', YASR_VERSION_NUM);

$stored_options = get_option( 'yasr_general_options' );

define ("YASR_AUTO_INSERT_ENABLED", $stored_options['auto_insert_enabled']);

if ( YASR_AUTO_INSERT_ENABLED == 1 ) {

	define ("YASR_AUTO_INSERT_WHAT", $stored_options['auto_insert_what']);
	define ("YASR_AUTO_INSERT_WHERE", $stored_options['auto_insert_where']);
	define ("YASR_AUTO_INSERT_SIZE", $stored_options['auto_insert_size']);
	define ("YASR_AUTO_INSERT_EXCLUDE_PAGES", $stored_options['auto_insert_exclude_pages']);

}

define ("YASR_SHOW_OVERALL_IN_LOOP", $stored_options['show_overall_in_loop']);
define ("YASR_TEXT_BEFORE_STARS", $stored_options['text_before_stars']);

if ( YASR_TEXT_BEFORE_STARS == 1 ) {

	define ("YASR_TEXT_BEFORE_OVERALL", $stored_options['text_before_overall']);
	define ("YASR_TEXT_BEFORE_VISITOR_RATING", $stored_options['text_before_visitor_rating']);
	define ("YASR_CUSTOM_TEXT_USER_VOTED", $stored_options['custom_text_user_voted']);

}

define ("YASR_SNIPPET", $stored_options['snippet']);
define ("YASR_ALLOWED_USER", $stored_options['allowed_user']);
define ("YASR_SCHEME_COLOR", $stored_options['scheme_color']);

?>

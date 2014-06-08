<?php
/**
 * Plugin Name:  Yet Another Stars Rating
 * Plugin URI: http://wordpress.org/plugins/yet-another-stars-rating/
 * Description: Rating system with rich snippets
 * Version: 0.0.2
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

    
define('YASR_VERSION_NUM', '0.0.2');

update_option('yasr-version', YASR_VERSION_NUM);

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
define ("YASR_CSS_DIR", plugins_url(YASR_RELATIVE_PATH . '/css/' ));

//IMG directory
define ("YASR_IMG_DIR", plugins_url( YASR_RELATIVE_PATH . '/img/'));

/* Include function file */
require (YASR_ABSOLUTE_PATH . '/lib/yasr-functions.php');

require (YASR_ABSOLUTE_PATH . '/lib/yasr-db-functions.php');

require (YASR_ABSOLUTE_PATH . '/lib/yasr-ajax-functions.php');

require (YASR_ABSOLUTE_PATH . '/lib/yasr-shortcode-functions.php');

yasr_install();

global $wpdb;

define ("YASR_VOTES_TABLE", $wpdb->prefix . 'yasr_votes');

define ("YASR_MULTI_SET_NAME_TABLE", $wpdb->prefix . 'yasr_multi_set');

define ("YASR_MULTI_SET_FIELDS_TABLE", $wpdb->prefix . 'yasr_multi_set_fields');

define ("YASR_MULTI_SET_VALUES_TABLE", $wpdb->prefix . 'yasr_multi_values');

define ("YASR_LOG_TABLE", $wpdb->prefix . 'yasr_log');

yasr_hook_js_and_css();  //Declared in yasr-functions

yasr_translate();  //Declared in yasr-functions

yasr_hook_options_page();  //Declared in yasr-functions

yasr_hook_metaboxes();  //Declared in yasr-functions

yasr_auto_insert_shortcode (); //Declared in yasr-functions

yasr_add_filter_for_schema(); //Declared in yasr-functions

yasr_insert_visitor_votes(); //Declared in yasr-ajax-function

yasr_get_readonly_visitor_shortcode(); //Declared in yasr-ajax-function

?>

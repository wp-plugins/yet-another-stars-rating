<?php

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

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly


/***** Adding javascript and css *****/

	add_action( 'wp_enqueue_scripts', 'yasr_add_scripts' );  
	add_action( 'admin_enqueue_scripts', 'yasr_add_admin_scripts' );

	function yasr_add_scripts () {

        //if visitors stats are enabled
        if (YASR_VISITORS_STATS === 'yes') {
            wp_enqueue_style( 'jquery-ui','http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.min.css', FALSE, NULL, 'all' );
        }

		wp_enqueue_style( 'yasrcss', YASR_CSS_DIR . 'yasr.css', FALSE, NULL, 'all' );

        //If choosen is light or not dark (force to be default)
        if (YASR_SCHEME_COLOR === 'light' || YASR_SCHEME_COLOR != 'dark' ) {
            wp_enqueue_style( 'yasrcsslightscheme', YASR_CSS_DIR . 'yasr-table-light.css', array('yasrcss'), NULL, 'all' );
        }

        elseif (YASR_SCHEME_COLOR === 'dark') {
            wp_enqueue_style( 'yasrcssdarkscheme', YASR_CSS_DIR . 'yasr-table-dark.css', array('yasrcss'), NULL, 'all' );
        }

        if (YASR_CUSTOM_CSS_RULES) {
            wp_add_inline_style( 'yasrcss', YASR_CUSTOM_CSS_RULES );
        }

		wp_enqueue_script( 'rateit', YASR_JS_DIR . 'jquery.rateit.min.js' , array('jquery'), '1.0.22', TRUE );
		wp_enqueue_script( 'cookie', YASR_JS_DIR . 'jquery-cookie.min.js' , array('jquery', 'rateit'), '1.4.0', TRUE );

        //if visitors stats are enabled
        if (YASR_VISITORS_STATS === 'yes') {
            wp_enqueue_script( 'jquery-ui-progressbar' ); //script
            wp_enqueue_script( 'jquery-ui-tooltip' ); //script
        }

        wp_enqueue_script( 'yasrfront', YASR_JS_DIR . 'yasr-front.js' , array('jquery', 'rateit'), '1.0.00', TRUE );

	}

    function yasr_add_admin_scripts () {

        wp_enqueue_style( 'yasrcss', YASR_CSS_DIR . 'yasr-admin.css', FALSE, NULL, 'all' );
        wp_enqueue_style( 'wp-jquery-ui-dialog' ); //style

        wp_enqueue_script( 'rateit', YASR_JS_DIR . 'jquery.rateit.min.js' , array('jquery'), '1.0.20', TRUE );
        wp_enqueue_script( 'jquery-ui-dialog' ); //script

        wp_enqueue_script( 'yasradmin', YASR_JS_DIR . 'yasr-admin.js' , array('jquery', 'rateit'), '1.0.00', TRUE );


    }



/****** Translating YASR ******/
	
	add_action('init', 'yasr_translate_option', 110);

	function yasr_translate_option() {
		load_plugin_textdomain('yasr', FALSE, YASR_LANG_DIR); 
	}


/****** Create a new Page in Administration Menu ******/

	/* Hook to admin_menu the yasr_add_pages function above */
	add_action( 'admin_menu', 'yasr_add_pages' );

	function yasr_add_pages() {

    //Add Settings Page
    add_options_page(
        __( 'Yet Another Stars Rating: Settings', 'yasr' ), //Page Title
        __( 'Yet Another Stars Rating: Settings', 'yasr' ), //Menu Title
        'manage_options', //capability
        'yasr_settings_page', //menu slug
        'yasr_settings_page_callback' //The function to be called to output the content for this page.
    	);
	}


	// Settings Page Content 
	function yasr_settings_page_callback () {
    	if ( ! current_user_can( 'manage_options' ) ) {
        	wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
    	}

	include(YASR_RELATIVE_PATH  . '/yasr-settings-page.php');

	} //End yasr_settings_page_content



/****** Create 2 metaboxes in post and pages ******/

	add_action( 'add_meta_boxes', 'yasr_add_metaboxes' );

	function yasr_add_metaboxes() {

        //Default post type where display metabox
        $post_type_where_display_metabox = array('post', 'page');

        //get the custom post type
        $custom_post_types = yasr_get_custom_post_type();

        if ($custom_post_types) {

            //First merge array then changes keys to int
            $post_type_where_display_metabox = array_values(array_merge($post_type_where_display_metabox, $custom_post_types));     

        }

		$multi_set=yasr_get_multi_set(); 
		//If multiset are used then add 2 metabox, 1 for overall rating and 1 for multiple rating 
		if ($multi_set) {
			foreach ($post_type_where_display_metabox as $post_type) {
				add_meta_box( 'yasr_metabox_overall_rating', __( 'Overall Rating', 'yasr' ), 'yasr_metabox_overall_rating_content', $post_type, 'side', 'high' );
				add_meta_box( 'yasr_metabox_multiple_rating', __( 'Yet Another Stars Rating: Multiple set', 'yasr' ), 'yasr_metabox_multiple_rating_content', $post_type, 'normal', 'high' );
			}
		}
		//else create just the overall rating one
		else {
			foreach ($post_type_where_display_metabox as $post_type) {
				add_meta_box( 'yasr_metabox_overall_rating', __( 'Overall Rating', 'yasr' ), 'yasr_metabox_overall_rating_content', $post_type, 'side', 'high' );
			}
		}
	}

	function yasr_metabox_overall_rating_content() {
		if ( current_user_can( 'publish_posts' ) )  {
			include (YASR_RELATIVE_PATH . '/yasr-metabox-overall-rating.php');
		}
		else {
            _e("You don't have enought privileges to insert Overall Rating");
        }

	}

	function yasr_metabox_multiple_rating_content() {
		if ( current_user_can( 'publish_posts' ) )  {
			include (YASR_RELATIVE_PATH . '/yasr-metabox-multiple-rating.php');
		}
        else {
            _e("You don't have enought privileges to insert Multi Set");
        }
		
	}


/****** Auto insert overall rating and visitor rating  ******/

    add_filter('the_content', 'yasr_auto_insert_shortcode_callback');

    function yasr_auto_insert_shortcode_callback($content) {

        if (YASR_AUTO_INSERT_ENABLED == 1) {

            $auto_insert_shortcode=NULL; //To avoid undefined variable notice outside the loop (if (is_singular) )

            $overall_rating_code = '[yasr_overall_rating size="' . YASR_AUTO_INSERT_SIZE . '"]';

            $visitor_votes_code = '[yasr_visitor_votes size="' . YASR_AUTO_INSERT_SIZE . '"]';

            if (YASR_AUTO_INSERT_WHAT==='overall_rating') {
                switch (YASR_AUTO_INSERT_WHERE) {
                    case 'top':
                        $content_and_stars = $overall_rating_code . $content;
                        break;
                
                    case 'bottom':
                        $content_and_stars = $content . $overall_rating_code;
                        break;
                } //End Switch
            } //end YASR_AUTO_INSERT_WHAT overall rating

            elseif (YASR_AUTO_INSERT_WHAT==='visitor_rating') {
                switch (YASR_AUTO_INSERT_WHERE) {
                    case 'top':
                        $content_and_stars = $visitor_votes_code . $content;
                        break;
                
                    case 'bottom':
                        $content_and_stars = $content . $visitor_votes_code;
                        break;
                } //End Switch
            }

            elseif (YASR_AUTO_INSERT_WHAT==='both') {
                switch (YASR_AUTO_INSERT_WHERE) {
                    case 'top':
                        $content_and_stars = $overall_rating_code . $visitor_votes_code . $content;
                        break;
                
                    case 'bottom':
                        $content_and_stars = $content . $overall_rating_code . $visitor_votes_code;
                        break;
                } //End Switch
            }

            //IF auto insert must work only in custom post type
            if (YASR_AUTO_INSERT_CUSTOM_POST_ONLY === 'yes') {

                $custom_post_types = yasr_get_custom_post_type();

                //If is a post type return content and stars
                if (is_singular($custom_post_types)) {
                    return $content_and_stars;
                }

                //else return just content
                else {
                    return $content;
                }

            }

            //If page are not excluded
            if (YASR_AUTO_INSERT_EXCLUDE_PAGES === 'no') {
                return $content_and_stars;
            }

            //else return only if it is not a page
            elseif (YASR_AUTO_INSERT_EXCLUDE_PAGES === 'yes') {
                if ( !is_page() ) {
                    return $content_and_stars;
                }
                //If is a page return the content without stars
                else {
                    return $content;
                }
            }


        } //End  if (YASR_AUTO_INSERT_ENABLED

        //Return if auto insert is off
        else {

            return $content;

        }


    } //End function yasr_auto_insert_shortcode_callback


/****** Add review schema data at the end of the post *******/

	add_filter('the_content', 'yasr_add_schema');

	function yasr_add_schema($content) {

		$schema=NULL; //To avoid undefined variable notice outside the loop

        $review_choosen = yasr_get_snippet_type();

		if (YASR_SNIPPET == 'overall_rating') {

			$overall_rating=yasr_get_overall_rating();

			if($overall_rating && $overall_rating != '-1' && $overall_rating != '0.0') {

				if(is_singular() && is_main_query() ) {
					global $post;

                    if ($review_choosen == 'Place') {
                        $title = "<span itemprop=\"itemReviewed\" itemscope itemtype=\"http://schema.org/LocalBusiness\">  <span itemprop=\"name\">". get_the_title() ."</span></span>";
                    }

                    if ($review_choosen == 'Other') {
                         $title = "<span itemprop=\"itemReviewed\" itemscope itemType=\"http://schema.org/BlogPosting\">  <span itemprop=\"name\">". get_the_title() ."</span></span>";
                    }

                    else {
                        $title = "<span itemprop=\"itemReviewed\" itemscope itemtype=\"http://schema.org/Thing\">  <span itemprop=\"name\">". get_the_title() ."</span></span>";
                    }

                    $div = "<div class=\"yasr_schema\" itemscope itemtype=\"http://schema.org/Review\">";
                    $author = "<span itemprop=\"author\" itemscope itemtype=\"http://schema.org/Person\">" . __(' reviewed by ', 'yasr') . "<span itemprop=\"name\">" . get_the_author() . "</span></span>";
                    $date = __(' on ', 'yasr') . "<meta itemprop=\"datePublished\" content=\"" . get_the_date('c') . "\"> " .  get_the_date();
                    $rating = "<span itemprop=\"reviewRating\" itemscope itemtype=\"http://schema.org/Rating\"> ". __( ' rated ' , 'yasr' ) . "<span itemprop=\"ratingValue\">" . $overall_rating . "</span>" . __(' of', 'yasr') ." <span itemprop=\"bestRating\">5</span></span>";
                    $end_div= "</div>";

                    $schema = $div . $title . $author . $date . $rating . $end_div;

				}

			} //END id if $overall_rating != '-1'

			if( is_singular() && is_main_query() ) {
				return $content . $schema;
			}

			else {
				return $content;
			}

		}  //end if ($choosen_snippet['snippet'] == 'overall_rating')

		if (YASR_SNIPPET == 'visitor_rating') {

			$visitor_votes = yasr_get_visitor_votes ();

            if ($visitor_votes) {

                foreach ($visitor_votes as $rating) {
                    $visitor_rating['votes_number']=$rating->number_of_votes;
                    $visitor_rating['sum']=$rating->sum_votes;
                }

            }

            else {
                $visitor_rating = NULL;
            }

			if ($visitor_rating['sum'] != 0) {

				$average_rating = $visitor_rating['sum'] / $visitor_rating['votes_number'];

				$average_rating=round($average_rating, 1);

                if ($review_choosen == 'Place') {
                    $div_1 = "<div class=\"yasr_schema\" itemscope itemtype=\"http://schema.org/LocalBusiness\">";
                }

                if ($review_choosen == 'Other') {
                    $div_1 = "<div class=\"yasr_schema\" itemscope itemType=\"http://schema.org/BlogPosting\">";
                }

                else {
                    $div_1 = "<div class=\"yasr_schema\" itemscope itemtype=\"http://schema.org/Product\">";
                }

                $title = "<span itemprop=\"name\">". get_the_title() ."</span>";
                $author = __( ' written by ' , 'yasr' ) . get_the_author();
                $span_1 = "<span itemprop=\"aggregateRating\" itemscope itemtype=\"http://schema.org/AggregateRating\">";
                $rating = __( ' average rating ' , 'yasr' ) . "<span itemprop=\"ratingValue\">" . $average_rating . "</span>/<span itemprop=\"bestRating\">5</span>";
                $n_ratings = " - <span itemprop=\"ratingCount\"> " . $visitor_rating['votes_number'] . "</span>" . __(' user ratings', 'yasr');
                $end_span_1 = "</span>";
                $end_div_1 = "</div>";

                $schema = $div_1 . $title . $author . $span_1 . $rating . $n_ratings . $end_span_1 . $end_div_1;

            }

            if( is_singular() && is_main_query() ) {
                    return $content . $schema;
            }

            else {
                    return $content;
            }

        }

    } //End function


/****** Create a new button in Tinymce for use shortag
(Thanks to wordpress.stackexchange) ******/

// init process for registering our button
add_action('admin_init', 'yasr_shortcode_button_init');
    function yasr_shortcode_button_init() {

        //Abort early if the user will never see TinyMCE
        if ( ! current_user_can('publish_posts') && ! current_user_can('publish_posts') && get_user_option('rich_editing') == 'true')
           return;

        //Add a callback to regiser our tinymce plugin   
        add_filter("mce_external_plugins", "yasr_register_tinymce_plugin"); 

        // Add a callback to add our button to the TinyMCE toolbar
        add_filter('mce_buttons', 'yasr_add_tinymce_button');

    }


    //This callback registers our plug-in
    function yasr_register_tinymce_plugin($plugin_array) {
        $plugin_array['yasr_button'] = YASR_JS_DIR . 'addButton_tinymcs.js';
        return $plugin_array;
    }

    //This callback adds our button to the toolbar
    function yasr_add_tinymce_button($buttons) {
                //Add the button ID to the $button array
        $buttons[] = "yasr_button";
        return $buttons;
    }


/****** Return the custom post type if exists 
Argument is to set what to return, if array or boolean value.
Default: array******/

add_action( 'admin_init', 'yasr_get_custom_post_type');
    function yasr_get_custom_post_type($exit='array') {

        $args = array(
            'public'   => true,
            '_builtin' => false
        );

        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types( $args, $output, $operator ); 

        if ($post_types) {
            if ($exit == 'array') {
                return ($post_types);
            }
            else {
                return TRUE;
            }
        }

        else {
            return FALSE;
        }

    }

/****** Donation box dx ******/

function yasr_donate_dx () {

    ?>

    <div class="yasr-donatedivdx" style="display:none">
        <h3><?php _e('Donations', 'yasr'); ?></h3>

        <?php _e('If you have found this plugin useful, please consider making a donation to help support future development. Your support will be much appreciated. ', 'yasr'); ?>
        <br />
        <?php _e('Thank you!', 'yasr'); ?>
        <br />
        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AXE284FYMNWDC">
            <?php echo("<img src=" . YASR_IMG_DIR . "/paypal.png>"); ?>
        </a>

        <hr>
    
        <h3><a href="http://yetanotherstarsrating.com"><?php _e('Follow YASR official site!', 'yasr') ?></a></h3>

    </div>

    <?php 

}


function yasr_donate_bottom () {

    ?>

    <div class="yasr-donatedivbottom" style="display:none">
        <h3><?php _e('Donations', 'yasr'); ?></h3>

        <?php _e('If you have found this plugin useful, please consider making a donation to help support future development. Your support will be much appreciated. ', 'yasr'); ?>
        <br />
        <?php _e('Thank you!', 'yasr'); ?>
        <br />
        
        <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=AXE284FYMNWDC">
            <?php echo("<img src=" . YASR_IMG_DIR . "/paypal.png>"); ?>
        </a>

        <hr>

        <h3><a href="http://yetanotherstarsrating.com"><?php _e('Follow YASR official site!', 'yasr') ?></a></h3>

    </div>

    <?php

}

?>

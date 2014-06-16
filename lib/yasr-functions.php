<?php

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly


/***** Adding javascript and css *****/

	add_action( 'wp_enqueue_scripts', 'yasr_add_scripts' );  
	add_action( 'admin_enqueue_scripts', 'yasr_add_scripts' );

	function yasr_add_scripts () {
		wp_enqueue_style( 'rateitcss', YASR_CSS_DIR . 'rateit.css', FALSE, NULL, 'all' );
		wp_enqueue_style( 'rateitbigstars', YASR_CSS_DIR . 'bigstars.css', array('rateitcss'), NULL, 'all' );
		wp_enqueue_style( 'yasrcss', YASR_CSS_DIR . 'yasr.css', array('rateitcss'), NULL, 'all' );
		wp_enqueue_script( 'rateit', YASR_JS_DIR . 'jquery.rateit.min.js' , array('jquery'), '1.0.20', TRUE );
		wp_enqueue_script( 'cookie', YASR_JS_DIR . 'jquery.cookie.js' , array('jquery', 'rateit'), '1.4.0', TRUE );
	}



/****** Translating YASR ******/
	
	add_action('plugins_loaded', 'yasr_translate_option');

	function yasr_translate_option() {
		load_plugin_textdomain('yasr', FALSE, YASR_LANG_DIR); 
	}


/****** Create a new Page in Administration Menu ******/

	/* Hook to admin_menu the yasr_add_pages function above */
	add_action( 'admin_menu', 'yasr_add_pages' );

	function yasr_add_pages() {

    //Add Settings Page
    add_options_page(
        'Yet Another Stars Rating: Settings', //Page Title
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

	include(YASR_ABSOLUTE_PATH  . '/yasr-settings-page.php');

	} //End yasr_settings_page_content



/****** Create 2 metaboxes in post and pages ******/

	add_action( 'add_meta_boxes', 'yasr_add_metaboxes' );
	
	function yasr_add_metaboxes() {
		$post_type_where_display_metabox=array('post', 'page');
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
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
		}
		include (YASR_ABSOLUTE_PATH . '/yasr-metabox-overall-rating.php');
	}

	function yasr_metabox_multiple_rating_content() {
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
		}
		include (YASR_ABSOLUTE_PATH . '/yasr-metabox-multiple-rating.php');
	}


/****** Auto insert shortcode  ******/

	add_filter('the_content', 'yasr_auto_insert_shortcode_callback');

	function yasr_auto_insert_shortcode_callback($content) {

		$option = get_option( 'yasr_auto_insert_options' );

		if ($option['enabled'] == 1) {

			$auto_insert_shortcode=NULL; //To avoid undefined variable notice outside the loop (if (is_singular) )

			if( is_singular() && is_main_query() ) {

				$overall_rating_shortcode='[yasr_overall_rating]';
				$visitor_votes_shortcode='[yasr_visitor_votes]';

				if ($option['what']=='overall_rating') {
					switch ($option['where']) {
						case 'top':
							return $overall_rating_shortcode . $content;
							break;
					
						case 'bottom':
							return $content . $overall_rating_shortcode;
							break;
					} //End Switch
				} //end ($option['what']=='overall_rating')

				elseif ($option['what']=='visitor_rating') {
					switch ($option['where']) {
						case 'top':
							return $visitor_votes_shortcode . $content;
							break;
					
						case 'bottom':
							return $content . $visitor_votes_shortcode;
							break;
					} //End Switch
				}

				elseif ($option['what']=='both') {
					switch ($option['where']) {
						case 'top':
							return $overall_rating_shortcode . $visitor_votes_shortcode . $content;
							break;
					
						case 'bottom':
							return $content . $overall_rating_shortcode . $visitor_votes_shortcode;
							break;
					} //End Switch
				}

			} //End  if( is_singular() && is_main_query() )

			return $content;

		} //End if ($option['enabled'] == 1)

		else {
			return $content;
		}

	} //End function yasr_auto_insert_shortcode_callback


/****** Add review schema data at the end of the post *******/

	add_filter('the_content', 'yasr_add_overall_rating_schema');

	function yasr_add_overall_rating_schema($content) {

		$schema=NULL; //To avoid undefined variable notice outside the loop

		$choosen_snippet = get_option( 'yasr_auto_insert_options' );

		if(!$choosen_snippet) {
			$choosen_snippet = array();
			$choosen_snippet['snippet'] = 'overall_rating';
		}

		if ($choosen_snippet['snippet'] == 'overall_rating') {

			$overall_rating=yasr_get_overall_rating();

			if($overall_rating && $overall_rating != '-1') {

				if(is_singular() && is_main_query() ) {
					global $post;

					$div = "<div itemprop=\"review\" itemscope itemtype=\"http://schema.org/Review\">";
					$title = "<span itemprop=\"about\">". get_the_title() ."</span>";
					$author = __(' reviewed by ', 'yasr') . "<span itemprop=\"author\">" . get_the_author() . "</span>";
					$date = __(' on ', 'yasr') . "<meta itemprop=\"datePublished\" content=\"" . get_the_date('c') . "\"> " .  get_the_date();
					$rating = __( ' rated ' , 'yasr' ) . "<span itemprop=\"reviewRating\">" . $overall_rating . "</span>" . __(' on 5.0' , 'yasr');
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

		if ($choosen_snippet['snippet'] == 'visitor_rating') {

			$visitor_rating = yasr_get_vistor_rating();

			if ($visitor_rating['sum'] != 0) {

				$average_rating = $visitor_rating['sum'] / $visitor_rating['votes_number'];

				$average_rating=round($average_rating, 1);

				$div_1 = "<div itemscope itemtype=\"http://schema.org/Product\">";
				$title = "<span itemprop=\"name\">". get_the_title() ."</span>";
				$span_1 = "<span itemprop=\"aggregateRating\" itemscope itemtype=\"http://schema.org/AggregateRating\">";
				$rating = __( ' rated ' , 'yasr' ) . "<span itemprop=\"ratingValue\">" . $average_rating . "</span>" . __(' out of ' ,'yasr') . "<span itemprop=\"bestRating\">5</span>";
				$n_ratings = __(' based on ', 'yasr') . "<span itemprop=\"ratingCount\">" . $visitor_rating['votes_number'] . "</span>" . __(' user ratings', 'yasr');
				$end_span_1 = "</span>";
				$end_div_1 = "</div>";

				$schema = $div_1 . $title . $span_1 . $rating . $n_ratings . $end_span_1 . $end_div_1;

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
 add_action('init', 'yasr_shortcode_button_init');
 function yasr_shortcode_button_init() {

      //Abort early if the user will never see TinyMCE
      if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
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
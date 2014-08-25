<?php

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly


/***** Adding javascript and css *****/

	add_action( 'wp_enqueue_scripts', 'yasr_add_scripts' );  
	add_action( 'admin_enqueue_scripts', 'yasr_add_admin_scripts' );

	function yasr_add_scripts () {

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

		wp_enqueue_script( 'rateit', YASR_JS_DIR . 'jquery.rateit.min.js' , array('jquery'), '1.0.20', TRUE );
		wp_enqueue_script( 'cookie', YASR_JS_DIR . 'jquery-cookie.min.js' , array('jquery', 'rateit'), '1.4.0', TRUE );
	}

    function yasr_add_admin_scripts () {

        wp_enqueue_style( 'yasrcss', YASR_CSS_DIR . 'yasr-admin.css', FALSE, NULL, 'all' );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );

        wp_enqueue_script( 'rateit', YASR_JS_DIR . 'jquery.rateit.min.js' , array('jquery'), '1.0.20', TRUE );
        wp_enqueue_script( 'jquery-ui-dialog' );

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

	include(YASR_ABSOLUTE_PATH  . '/yasr-settings-page.php');

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
			include (YASR_ABSOLUTE_PATH . '/yasr-metabox-overall-rating.php');
		}
		else {
            _e("You don't have enought privileges to insert Overall Rating");
        }

	}

	function yasr_metabox_multiple_rating_content() {
		if ( current_user_can( 'publish_posts' ) )  {
			include (YASR_ABSOLUTE_PATH . '/yasr-metabox-multiple-rating.php');
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
                        return $overall_rating_code . $content;
                        break;
                
                    case 'bottom':
                        return $content . $overall_rating_code;
                        break;
                } //End Switch
            } //end YASR_AUTO_INSERT_WHAT overall rating

            elseif (YASR_AUTO_INSERT_WHAT==='visitor_rating') {
                switch (YASR_AUTO_INSERT_WHERE) {
                    case 'top':
                        return $visitor_votes_code . $content;
                        break;
                
                    case 'bottom':
                        return $content . $visitor_votes_code;
                        break;
                } //End Switch
            }

            elseif (YASR_AUTO_INSERT_WHAT==='both') {
                switch (YASR_AUTO_INSERT_WHERE) {
                    case 'top':
                        return $overall_rating_code . $visitor_votes_code . $content;
                        break;
                
                    case 'bottom':
                        return $content . $overall_rating_code . $visitor_votes_code;
                        break;
                } //End Switch
            }

            return $content;

        } //End  if (YASR_AUTO_INSERT_ENABLED

        else {

            return $content;

        }


    } //End function yasr_auto_insert_shortcode_callback


/****** Add review schema data at the end of the post *******/

	add_filter('the_content', 'yasr_add_overall_rating_schema');

	function yasr_add_overall_rating_schema($content) {

		$schema=NULL; //To avoid undefined variable notice outside the loop

		if (YASR_SNIPPET == 'overall_rating') {

			$overall_rating=yasr_get_overall_rating();

			if($overall_rating && $overall_rating != '-1' && $overall_rating != '0.0') {

				if(is_singular() && is_main_query() ) {
					global $post;

					$div = "<div class=\"yasr_schema\" itemprop=\"review\" itemscope itemtype=\"http://schema.org/Review\">";
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

				$div_1 = "<div class=\"yasr_schema\" itemscope itemtype=\"http://schema.org/Product\">";
				$title = "<span itemprop=\"name\">". get_the_title() ."</span>";
				$span_1 = "<span itemprop=\"aggregateRating\" itemscope itemtype=\"http://schema.org/AggregateRating\">";
				$rating = __( ' average rating ' , 'yasr' ) . "<span itemprop=\"ratingValue\">" . $average_rating . "</span>/<span itemprop=\"bestRating\">5</span>";
				$n_ratings = " - <span itemprop=\"ratingCount\"> " . $visitor_rating['votes_number'] . "</span>" . __(' user ratings', 'yasr');
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


/****** Return the custom post type if exists ******/

add_action( 'admin_init', 'yasr_get_custom_post_type');
    function yasr_get_custom_post_type() {

        $args = array(
            'public'   => true,
            '_builtin' => false
        );

        $output = 'names'; // names or objects, note names is the default
        $operator = 'and'; // 'and' or 'or'

        $post_types = get_post_types( $args, $output, $operator ); 

        if ($post_types) {
            return ($post_types);
        }

        else {
            return FALSE;
        }

    }

?>
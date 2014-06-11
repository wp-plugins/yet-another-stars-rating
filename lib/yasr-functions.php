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

	add_action( 'admin_init', 'yasr_multi_form_init' );

	function yasr_multi_form_init() {
    	register_setting(
        	'yasr_multi_form', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
        	'yasr_auto_insert_options' //The name of an option to sanitize and save.
    	);

    	$option = get_option( 'yasr_auto_insert_options' );

    	//To avoid undifined index, i put here the default value
    	if (!$option || !$option['enabled'] || !$option['what'] || !$option['where']) {
    		$option['enabled']=0;
    		$option['what']='overall_rating';
    		$option['where']='top';
    	}

    	add_settings_section( 'yasr_auto_insert_section_id', __('Auto insert Settings', 'yasr'), 'yasr_section_callback', 'yasr_settings_page' );
    		add_settings_field( 'yasr_use_auto_insert_id', __('Use auto insert?', 'yasr'), 'yasr_auto_insert_callback', 'yasr_settings_page', 'yasr_auto_insert_section_id', $option );
    		add_settings_field( 'yasr_what_auto_insert', __('What?', 'yasr'), 'yasr_what_auto_insert_callback', 'yasr_settings_page', 'yasr_auto_insert_section_id', $option);
       		add_settings_field( 'yasr_where_auto_insert', __('Where?', 'yasr'), 'yasr_where_auto_insert_callback', 'yasr_settings_page', 'yasr_auto_insert_section_id', $option);

	}


	function yasr_section_callback() {
    	//_e('Manage auto insert', 'yasr');
	}

	function yasr_auto_insert_callback($option) {

    	?>

    	<?php _e('Yes', 'yasr') ?>
    		<input type='radio' name='yasr_auto_insert_options[enabled]' value='1' id='yasr_auto_insert_radio_on' <?php if ($option['enabled']==1) echo " checked=\"checked\" "; ?>  /> 
			&nbsp;&nbsp;&nbsp;

		<?php _e('No', 'yasr') ?>
    		<input type='radio' name='yasr_auto_insert_options[enabled]' value='0' id='yasr_auto_insert_radio_off' 
    		<?php if ($option['enabled']==0) {
    				echo " checked=\"checked\" />";
    				echo ("<script>
    				jQuery( document ).ready(function() {
    					jQuery('.yasr_auto_insert_where_what_radio').prop('disabled', true);
    				});
					</script>") ;
    			}
    		?> 
    			  

    <?php
	} //End yasr_auto_insert_callback

	function yasr_what_auto_insert_callback($option) {	
		?>

    	<input type="radio" name="yasr_auto_insert_options[what]" value="overall_rating" class="yasr_auto_insert_where_what_radio" <?php if ($option['what']==='overall_rating') echo " checked=\"checked\" "; ?> >
    		<?php _e('Overall Rating / Author Rating', 'yasr') ?>
   			<br />

    	<input type="radio" name="yasr_auto_insert_options[what]" value="visitor_rating" class="yasr_auto_insert_where_what_radio" <?php if ($option['what']==='visitor_rating') echo " checked=\"checked\" "; ?> >
    		<?php _e('Visitor Votes', 'yasr')?>
   			<br />

    	<input type="radio" name="yasr_auto_insert_options[what]" value="both" class="yasr_auto_insert_where_what_radio" <?php if ($option['what']==='both') echo " checked=\"checked\" "; ?> >
    		<?php _e('Both', 'yasr')?>

    <?php
	} //end function yasr_what_auto_insert_callback

	function yasr_where_auto_insert_callback($option) {
		?>

		<input type="radio" name="yasr_auto_insert_options[where]" value="top" class="yasr_auto_insert_where_what_radio" <?php if ($option['where']==='top' ) echo " checked=\"checked\" ";  ?> >
			<?php _e('Before the post', 'yasr')?>
			<br />

    	<input type="radio" name="yasr_auto_insert_options[where]" value="bottom" class="yasr_auto_insert_where_what_radio" <?php if ($option['where']==='bottom') echo " checked=\"checked\" "; ?> >
    		<?php _e('After the post', 'yasr')?>
    		<br />

    <?php
	} //End function yasr_where_auto_insert_callback

	// Settings Page Content 
	function yasr_settings_page_callback () {
    	if ( ! current_user_can( 'manage_options' ) ) {
        	wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
    	}
?>

    <div class="wrap">

        <h2>Settings API Demo</h2>

        <?php

        $error_new_multi_set=yasr_process_new_multi_set_form(); //defined in yasr-db-functions

        $error_edit_multi_set=yasr_process_edit_multi_set_form(); //defined in yasr-db-functions

        if ($error_new_multi_set) {
        	echo "<div class=\"error\"> <p> <strong>";

          		foreach ($error_new_multi_set as $error) {
          			_e($error, 'yasr'); 
          			echo "<br />";
          		}

    		echo "</strong></p></div>"; 
    	}

        if ($error_edit_multi_set) {
        	echo "<div class=\"error\"> <p> <strong>";

          		foreach ($error_edit_multi_set as $error) {
          			_e($error, 'yasr'); 
          			echo "<br />";
          		}

    		echo "</strong></p></div>"; 
    	}
        ?>

        <div class="yasr-settingsdiv">
        	<form action="options.php" method="post" id="yasr_settings_form">
            	<?php
            	settings_fields( 'yasr_multi_form' );
            	do_settings_sections( 'yasr_settings_page' );
            	submit_button( __('Save') );
            	?>
        	</form>
        </div>

        <!--End div wrap is in yasr-settings-page-->

<?php

	include(YASR_ABSOLUTE_PATH  . '/yasr-settings-page.php');

	} //End yasr_settings_page_content


/****** Create a form for settings page to create new multi set ******/
function yasr_display_multi_set_form() {
	?>
		
		<h4 align="center">Add New Multiple Set</h4>
		<em><?php _e('Field Name, Element#1 and Element#2 MUST be filled and must be long at least 3 characters', 'yasr') ?></em>
		<p>
		<form action="<?php echo admin_url('options-general.php?page=yasr_settings_page') ?>" id="form_add_multi_set" method="post">
			<strong><?php _e("Name", 'yasr')?></strong> 
			<input type="text" name="multi-set-name" id="new-multi-set-name" class="input-text-multi-set">
			<input type="hidden" name="action" value="yasr_new_multi_set_form" />

			<p></p>
			<?php _e("You can insert up to nine element") ?>
			<br />

			<?php for($i=1; $i<=9; $i++) { 

				echo "<strong>" . __("Element ", 'yasr') . "#$i" . "</strong>";
				?>
				<input type="text" name="multi-set-name-element-<?php echo $i ?>" id="multi-set-name-element-<?php echo $i ?>" class="input-text-multi-set">
				<br />

			<?php } //End foreach

			wp_nonce_field( 'add-multi-set', 'add-nonce-new-multi-set' ) ?><!-- a little security to process on submission -->

	       	<br />
			<input type="submit" value="<?php _e("Create New Set", 'yasr') ?>" class="button-primary"/>
		</form>

	<?php 
} //End function


/****** This function print the form to edit multi-set ******/
function yasr_edit_multi_form() {

	$multi_set=yasr_get_multi_set();

	global $wpdb;

	$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the last of the last query

	if ($n_multi_set > 1) {
		?>

			<button href="#" class="button-delete" id="yasr-manage-multi-set"> <?php _e("Manage existing multi-set", 'yasr'); ?> </button>

			<div class="yasr-manage-multiset">

				<?php _e('Wich set do you want to edit or remove?', 'yasr')?>

				<select id ="yasr_select_edit_set">
    				<?php foreach ($multi_set as $name) { ?>
		    		<option value="<?php echo $name->set_id ?>"><?php echo $name->set_name ?></option>
	  				<?php } //End foreach ?>
  				</select>
					
			</div>

				<?php
	} //End if n_multi_set >1

	elseif ($n_multi_set == 1) {

		$set_name=$wpdb->get_results("SELECT field_name AS name, field_id AS id, parent_set_id AS set_id
		                            FROM " . YASR_MULTI_SET_FIELDS_TABLE . "  
		                            ORDER BY field_id ASC");

		foreach ($multi_set as $find_set_id) {
			$set_type = $find_set_id->set_id;
		}

		?>
		
			<button href="#" class="button-delete" id="yasr-manage-multi-set-single"> <?php _e("Manage existing multi-set", 'yasr'); ?> </button>

			<div class="yasr-manage-multiset-single">

				<form action=" <?php echo admin_url('options-general.php?page=yasr_settings_page') ?>" id="form_edit_multi_set" method="post">

		        		<input type="hidden" name="yasr_edit_multi_set_form" value="<?php echo $set_type ?>" />

						<table id="yasr-table-form-edit-multi-set">
		                <tr>

		                    <td id="yasr-table-form-edit-multi-set-header"> 
		                         <?php _e('Field name', 'yasr') ?>
		                    </td>

		                     <td id="yasr-table-form-edit-multi-set-remove"> 
		                        <?php _e('Remove', 'yasr') ?> 
		                     </td>

		                </tr>

						<?php

		       			$i=1;
		        		foreach ($set_name as $name) {
		                echo "
		                <tr>
		                    
		                    <td width=\"80%\">
		                        Element #$i <input type=\"text\" value=\"$name->name\" name=\"edit-multi-set-element-$name->id\">  
		                    </td>

		                    <td width=\"20%\" style=\"text-align:center\">
		                        <input type=\"checkbox\" name=\"remove-element-$name->id\">
		                    </td>

		                </tr>
		                ";
		                $i++;
		            }


		            $i = $i-1; //This is the number of the fields

		            echo "

		            <input type=\"hidden\" name=\"yasr-edit-form-number-elements\" id=\"yasr-edit-form-number-elements\" value=\"$i\">

		            </table>

		            <table width=\"100%\" class=\"yasr-edit-form-remove-entire-set\">
		            <tr>

		                <td width=\"80%\">Remove whole set?</td>

		                <td width=\"20%\" style=\"text-align:center\">
		                    <input type=\"checkbox\" name=\"yasr-remove-multi-set\" value=\"$set_type\">
		                </td>

		            </tr>

		            </table>

		            ";

		            echo "<p>";
		                _e("If you remove something you will remove all the votes for that set or field. This operation CAN'T BE undone." , "yasr");
		            echo "</p>";

		            wp_nonce_field( 'edit-multi-set', 'add-nonce-edit-multi-set' ) 

		            ?>

		            <div id="yasr-element-limit" style="display:none; color:red"><?php _e("You can use up to 9 elements" , "yasr") ?></div>

		            <input type="button" class="button-delete" id="yasr-add-field-edit-multiset" value="<?php _e('Add element', 'yasr'); ?>"> 

		            <input type="submit" value="<?php _e('Save changes', 'yasr') ?>" class="button-primary" >	

				</form>

			</div>

		<?php
	}

	else {
		_e("No multiple set were found");
	}

}//End function


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




/****** Add review schema data at the end of the post *******/

	add_filter('the_content', 'yasr_add_overall_rating_schema');

	function yasr_add_overall_rating_schema($content) {

		$schema=NULL; //To avoid undefined variable notice outside the loop

		$overall_rating=yasr_get_overall_rating();

		if($overall_rating && $overall_rating != '-1') {

			if(is_singular() && is_main_query() ) {
				global $post;

				$div = "<div itemprop=\"review\" itemscope itemtype=\"http://schema.org/Review\">";
				$title = "<span itemprop=\"about\">". get_the_title($post->ID) ."</span>";
				$author = __(' reviewed by ', 'yasr') . "<span itemprop=\"author\">" . get_the_author() . "</span>";
				$date = __(' on ', 'yasr') . "<meta itemprop=\"datePublished\" content=\"" . get_the_date('c') . "\"> " .  get_the_date();
				$rating = __( ' rated ' , 'yasr' ) . "<span itemprop=\"reviewRating\">" . $overall_rating . "</span>" . __(' on 5.0' , 'yasr');
				$end_div= "</div>";

				$schema = $div . $title . $author . $date . $rating . $end_div;
			}
		}

		return $content . $schema;

	} //END id if $overall_rating != '-1'



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
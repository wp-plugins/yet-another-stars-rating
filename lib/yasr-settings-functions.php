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

/************ Add yasr general options ***********/

		add_action( 'admin_init', 'yasr_general_options_init' ); //This is for general options

		function yasr_general_options_init() {
	    	register_setting(
	        	'yasr_general_options_group', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
	        	'yasr_general_options' //The name of an option to sanitize and save.
	    	);	    	

	    	$option = get_option( 'yasr_general_options' );

	    	//This is to avoid undefined offset
	    	if ($option && $option['auto_insert_enabled']==0) {
	    		$option['auto_insert_what'] = 'overall_rating';
	    		$option['auto_insert_where'] = 'top';
	    		$option['auto_insert_exclude_pages'] = 'yes';
	    		$option['auto_insert_size'] = 'large';
	    		$option['auto_insert_custom_post_only'] = 'no';
	    	}

	    	//This is to avoid undefined offset
	    	if ($option && $option['text_before_stars']==0) {
	    		$option['text_before_overall'] = '';
	    		$option['text_before_visitor_rating'] = '';
	    		$option['text_after_visitor_rating'] = '';
	    		$option['custom_text_user_voted'] = '';
	    	}

	    	add_settings_section( 'yasr_general_options_section_id', __('General settings', 'yasr'), 'yasr_section_callback', 'yasr_general_settings_tab' );
	    		add_settings_field( 'yasr_use_auto_insert_id', __('Auto insert options', 'yasr'), 'yasr_auto_insert_callback', 'yasr_general_settings_tab', 'yasr_general_options_section_id', $option );
	       		add_settings_field( 'yasr_show_overall_in_loop', __('Show "Overall Rating" in Archive Page?', 'yasr'), 'yasr_show_overall_in_loop_callback', 'yasr_general_settings_tab',  'yasr_general_options_section_id', $option);
	       		add_settings_field( 'yasr_show_visitor_votes_in_loop', __('Show "Visitor Votes" in Archive Page?', 'yasr'), 'yasr_show_visitor_votes_in_loop_callback', 'yasr_general_settings_tab',  'yasr_general_options_section_id', $option);
	       		add_settings_field( 'yasr_custom_text', __('Insert custom text to show before / after stars', 'yasr'), 'yasr_custom_text_callback', 'yasr_general_settings_tab',  'yasr_general_options_section_id', $option);
	       		add_settings_field( 'yasr_visitors_stats', __('Do you want show stats for visitors votes?', 'yasr'), 'yasr_visitors_stats_callback', 'yasr_general_settings_tab',  'yasr_general_options_section_id', $option);
	       		add_settings_field( 'yasr_allow_only_logged_in_id', __('Allow only logged in user to vote?', 'yasr'), 'yasr_allow_only_logged_in_callback', 'yasr_general_settings_tab', 'yasr_general_options_section_id', $option );
	       		add_settings_field( 'yasr_choose_snippet_id', __('Which rich snippets do you want to use?', 'yasr'), 'yasr_choose_snippet_callback', 'yasr_general_settings_tab', 'yasr_general_options_section_id', $option );
	       		add_settings_field( 'yasr_choose_overall_rating_method', __('How do you want to rate "Overall Rating"?', 'yasr'), 'yasr_choose_overall_rating_method_callback', 'yasr_general_settings_tab', 'yasr_general_options_section_id', $option );

		}


		function yasr_section_callback() {
	    	//_e('Manage auto insert', 'yasr');
		}

		function yasr_auto_insert_callback($option) {

	    	?>


	    		<strong><?php _e('Use Auto Insert?', 'yasr'); ?></strong>
				<br />
	    		<input type='radio' name='yasr_general_options[auto_insert_enabled]' value='1' id='yasr_auto_insert_radio_on' <?php if ($option['auto_insert_enabled']==1) echo " checked='checked' "; ?>  /> 
	    		<?php _e('Yes', 'yasr') ?>
				&nbsp;&nbsp;&nbsp;

			
	    		<input type='radio' name='yasr_general_options[auto_insert_enabled]' value='0' id='yasr_auto_insert_radio_off' 
	    		<?php 
	    			if ($option['auto_insert_enabled']==0) {
	    				echo " checked='checked' />";
	    			}

	    			else {
	    				echo "/>";
	    			}

	    		_e('No', 'yasr'); 

	    		?> 

	    		<p>&nbsp;</p>

	    		<strong><?php _e('What?', 'yasr'); ?></strong>
					<br />
	    		<input type="radio" name="yasr_general_options[auto_insert_what]" value="overall_rating" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_what']==='overall_rating') echo " checked=\"checked\" "; ?> >
		    		<?php _e('Overall Rating / Author Rating', 'yasr') ?>
		   			<br />

		    	<input type="radio" name="yasr_general_options[auto_insert_what]" value="visitor_rating" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_what']==='visitor_rating') echo " checked=\"checked\" "; ?> >
		    		<?php _e('Visitor Votes', 'yasr')?>
		   			<br />

		    	<input type="radio" name="yasr_general_options[auto_insert_what]" value="both" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_what']==='both') echo " checked=\"checked\" "; ?> >
		    		<?php _e('Both', 'yasr')?>

		    	<p>&nbsp;</p>

		    	<strong><?php _e('Where?', 'yasr'); ?></strong>
		    	<br />
		    	<input type="radio" name="yasr_general_options[auto_insert_where]" value="top" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_where']==='top' ) echo " checked=\"checked\" ";  ?> >
					<?php _e('Before the post', 'yasr')?>
					<br />

		    	<input type="radio" name="yasr_general_options[auto_insert_where]" value="bottom" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_where']==='bottom') echo " checked=\"checked\" "; ?> >
		    		<?php _e('After the post', 'yasr')?>
		    		<br />

		    	<p>&nbsp;</p>

		    	<strong><?php _e('Size', 'yasr'); ?></strong>
		    	<br />
		    	<div class="yasr-option-size">
			    	<input type="radio" name="yasr_general_options[auto_insert_size]" value="small" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_size']==='small' ) echo " checked=\"checked\" ";  ?> >
						<img src="<?php echo  YASR_IMG_DIR . "yasr-stars-small.png" ?>" class="yasr-img-option-size"><span class="yasr-text-options-size"><?php _e('Small', 'yasr')?></span>
				</div>

				<div class="yasr-option-size">
		    	<input type="radio" name="yasr_general_options[auto_insert_size]" value="medium" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_size']==='medium' ) echo " checked=\"checked\" ";  ?> >
					<img src="<?php echo  YASR_IMG_DIR . "yasr-stars-medium.png" ?>" class="yasr-img-option-size"><span class="yasr-text-options-size"><?php _e('Medium', 'yasr')?></span>
				</div>

		    	<div class="yasr-option-size">
				<input type="radio" name="yasr_general_options[auto_insert_size]" value="large" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_size']==='large' ) echo " checked=\"checked\" ";  ?> >
					<img src="<?php echo  YASR_IMG_DIR . "yasr-stars-large.png" ?>" class="yasr-img-option-size"><span class="yasr-text-options-size"><?php _e('Large', 'yasr')?></span>
				</div>

		    	<p>&nbsp;</p>

		    	<strong><?php _e('Exclude Pages?', 'yasr'); ?></strong>
		    	<br />
		    	<input type="radio" name="yasr_general_options[auto_insert_exclude_pages]" value="yes" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_exclude_pages']==='yes' ) echo " checked=\"checked\" ";  ?> >
					<?php _e('Yes', 'yasr'); ?>

				&nbsp;&nbsp;&nbsp;

		    	<input type="radio" name="yasr_general_options[auto_insert_exclude_pages]" value="no" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_exclude_pages']==='no') echo " checked=\"checked\" "; ?> >
		    		<?php _e('No', 'yasr'); ?>
		    		<br />

		    	<p>&nbsp;</p>

		    	<?php 

		    	$custom_post_types = yasr_get_custom_post_type('bool');

		    	if ($custom_post_types) { ?>
		    		<strong><?php _e('Use only in custom post types?', 'yasr'); ?></strong>
		    		<br />
		    		<input type="radio" name="yasr_general_options[auto_insert_custom_post_only]" value="yes" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_custom_post_only']==='yes' ) echo " checked=\"checked\" ";  ?> >
					<?php _e('Yes', 'yasr'); ?>

					&nbsp;&nbsp;&nbsp;

		    		<input type="radio" name="yasr_general_options[auto_insert_custom_post_only]" value="no" class="yasr-auto-insert-options-class" <?php if ($option['auto_insert_custom_post_only']==='no') echo " checked=\"checked\" "; ?> >
		    		<?php _e('No', 'yasr'); ?>

		    		<p>

		    		<?php _e("You see this because you use custom post types.", "yasr"); ?>
		    		<br/>
		    		<?php _e("If you want to use auto insert only in custom post types, choose Yes", "yasr"); ?>

		    		<p>&nbsp;</p>

		    		<?php 
		    	}

		    	else {

		    		?>

		    		<input type="hidden" name="yasr_general_options[auto_insert_custom_post_only]" value="no" ?>

		    		<?php

		    	}

		    	?>

		    	<hr />
	    			  

	    	<?php
		} //End yasr_auto_insert_callback


	    function yasr_show_overall_in_loop_callback($option) {
	    	
	    	?>

	    	<input type='radio' name='yasr_general_options[show_overall_in_loop]' value='enabled' class='yasr-general-option-show-overall-in-loop' <?php if ($option['show_overall_in_loop']==='enabled') echo " checked=\"checked\" "; ?>  /> 
				<?php _e('Yes', 'yasr')?>

			&nbsp;&nbsp;&nbsp;

	    	<input type='radio' name='yasr_general_options[show_overall_in_loop]' value='disabled' class='yasr-general-option-show-overall-in-loop' <?php if ($option['show_overall_in_loop']==='disabled') echo " checked=\"checked\" "; ?>  /> 
				<?php _e('No', 'yasr')?>
				<br />
				<br />

			<?php _e('If you enable this, "Overall Rating" will be showed not only in the single article or page, but also in pages like Home Page, category pages or archives.', 'yasr')?>

			<p>&nbsp;</p>

			<br />

			<hr>

	    	<?php

	    }

	    function yasr_show_visitor_votes_in_loop_callback ($option) {

			?>

	    	<input type='radio' name='yasr_general_options[show_visitor_votes_in_loop]' value='enabled' class='yasr-general-option-show-visitor-votes-in-loop' <?php if ($option['show_visitor_votes_in_loop']==='enabled') echo " checked=\"checked\" "; ?>  /> 
				<?php _e('Yes', 'yasr')?>

			&nbsp;&nbsp;&nbsp;

	    	<input type='radio' name='yasr_general_options[show_visitor_votes_in_loop]' value='disabled' class='yasr-general-option-show-visitor-votes-in-loop' <?php if ($option['show_visitor_votes_in_loop']==='disabled') echo " checked=\"checked\" "; ?>  /> 
				<?php _e('No', 'yasr')?>
				<br />
				<br />

			<?php _e('If you enable this, "Visitor Votes" will be showed not only in the single article or page, but also in pages like Home Page, category pages or archives.', 'yasr')?>

			<p>&nbsp;</p>

			<br />

			<hr>

	    	<?php

	    }

	    function yasr_custom_text_callback($option) {

	    	$text_before_overall = htmlspecialchars("$option[text_before_overall]");

	    	$text_before_visitor_rating = htmlspecialchars("$option[text_before_visitor_rating]");

	    	$text_after_visitor_rating = htmlspecialchars("$option[text_after_visitor_rating]");

	    	$custom_text_user_votes = htmlentities("$option[custom_text_user_voted]");
	    	
	    	?>

	    	<input type='radio' name='yasr_general_options[text_before_stars]' value='1' id='yasr_text_before_star_on' <?php if ($option['text_before_stars']==1) echo " checked='checked' "; ?>  /> 
	    		<?php _e('Yes', 'yasr') ?>
				&nbsp;&nbsp;&nbsp;

	    		<input type='radio' name='yasr_general_options[text_before_stars]' value='0' id='yasr_text_before_star_off' <?php if ($option['text_before_stars']==0) echo " checked='checked' "; ?> />

	    		<?php _e('No', 'yasr'); ?>

	    	<br /> <br />
	    	
	    	<input type='text' name='yasr_general_options[text_before_overall]' id="yasr-general-options-custom-text-before-overall" class='yasr-general-options-text-before' <?php printf('value="%s"', $text_before_overall); ?> maxlength="40"/> 
			<?php _e('Custom text to display before Overall Rating', 'yasr')?>

			<br /> <br /> <br />

			<input type='text' name='yasr_general_options[text_before_visitor_rating]' id="yasr-general-options-custom-text-before-visitor" class='yasr-general-options-text-before' <?php printf('value="%s"', $text_before_visitor_rating); ?> maxlength="80"/> 
			<?php _e('Custom text to display BEFORE Visitor Rating', 'yasr')?> 

			<br /> <br />

			
			<input type='text' name='yasr_general_options[text_after_visitor_rating]' id="yasr-general-options-custom-text-after-visitor" class='yasr-general-options-text-before' <?php printf('value="%s"', $text_after_visitor_rating); ?> maxlength="80"/> 
			<?php _e('Custom text to display AFTER Visitor Rating', 'yasr')?>

			<br /> <br /> <br />

			<input type='text' name='yasr_general_options[custom_text_user_voted]' id="yasr-general-options-custom-text-already-rated" class='yasr-general-options-text-before' <?php printf('value="%s"', $custom_text_user_votes); ?> maxlength="60"/> 
			<?php _e('Custom text to display when a non logged user has already rated', 'yasr')?>


			<br /> <br />

			<a href="#" id="yasr-doc-custom-text-link"><?php _e('Help', 'yasr'); ?></a>

			<div id="yasr-doc-custom-text-div" class="yasr-help-box-settings">

				<?php _e('In the first field you can use %overall_rating% pattern to show the overall rating.', 'yasr');?>

				<br /> <br />

				<?php _e('In the Second and Third fields you can use %total_count% pattern to show the total count, and %average% pattern to show the average', 'yasr');?>

			</div>

			<p>&nbsp;</p>

			<hr>

			<?php
	    }

	    function yasr_visitors_stats_callback($option) {

	    	?>

		    	<input type='radio' name='yasr_general_options[visitors_stats]' value='yes' class='yasr-general-options-scheme-color' <?php if ($option['visitors_stats']==='yes') echo " checked=\"checked\" "; ?>  /> 
					<?php _e('Yes', 'yasr')?>
					
				&nbsp;&nbsp;&nbsp;

				<input type='radio' name='yasr_general_options[visitors_stats]' value='no' class='yasr-general-options-scheme-color' <?php if ($option['visitors_stats']==='no') echo " checked=\"checked\" "; ?>  /> 
					<?php _e('No', 'yasr')?>
					<br />

					<br />

				<p>&nbsp;</p>

				<hr>

		    	<?php

	    }

	    function yasr_allow_only_logged_in_callback($option) {

	    	?>

			<input type='radio' name='yasr_general_options[allowed_user]' value='logged_only' class='yasr_auto_insert_loggedonly' <?php if ($option['allowed_user']==='logged_only') echo " checked=\"checked\" "; ?>  /> 
				<?php _e('Allow only logged-in users', 'yasr')?>
				<br />

			<input type='radio' name='yasr_general_options[allowed_user]' value='allow_anonymous' class='yasr_auto_insert_loggedonly' <?php if ($option['allowed_user']==='allow_anonymous') echo " checked=\"checked\" "; ?>  /> 
				<?php _e('Allow everybody (logged in and anonymous)', 'yasr')?>
				<br />

			<p>&nbsp;</p>

			<hr>

			<?php

		} //End function


	    function yasr_choose_snippet_callback($option) {

			?>

		    	<input type="radio" name="yasr_general_options[snippet]" value="overall_rating" class="yasr_choose_snippet" <?php if ($option['snippet']==='overall_rating') echo " checked=\"checked\" "; ?> >
		    		<?php _e('Review Rating', 'yasr') ?>
		   			<br />

		    	<input type="radio" name="yasr_general_options[snippet]" value="visitor_rating" class="yasr_choose_snippet" <?php if ($option['snippet']==='visitor_rating') echo " checked=\"checked\" "; ?> >
		    		<?php _e('Aggregate Rating', 'yasr')?>
		   			<br />

		   			<br />

		   		<a href="#" id="yasr-snippet-explained-link"><?php _e("What is this?", "yasr") ?></a>

		   		<div id="yasr-snippet-explained" class="yasr-help-box-settings">
		   			<?php 

		   				_e("If you select \"Review Rating\", your site will be indexed from search engines like this: ", "yasr");
		   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "yasr_review.png>";

		   				echo "<br /> <br />";

		   				_e("If, instead, you choose \"Aggregate Rating\", your site will be indexed like this", "yasr");
		   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "yasr_aggregate.jpg>";
		   			 ?>
		   		</div>

		   		<p>&nbsp;</p>

		   		<hr>

			<?php

		} //End function yasr_choose_snippet_callback

		function yasr_choose_overall_rating_method_callback($option) {

			?>

			<input type="radio" name="yasr_general_options[metabox_overall_rating]" value="stars" class="yasr_choose_overall_rating_method" <?php if ($option['metabox_overall_rating']==='stars') echo " checked=\"checked\" "; ?> >
		    		<?php _e('Stars', 'yasr') ?>
		   			<br />

	    	<input type="radio" name="yasr_general_options[metabox_overall_rating]" value="numbers" class="yasr_choose_overall_rating_method" <?php if ($option['metabox_overall_rating']==='numbers') echo " checked=\"checked\" "; ?> >
	    		<?php _e('Numbers', 'yasr')?>
	   			<br />

		    <?php

		}


/************ End Yasr General Settings ************/



/**************** Add yasr multiset options and settings ************/

add_action( 'admin_init', 'yasr_multiset_options_init' ); //This is for general options

	function yasr_multiset_options_init() {
    	register_setting(
        	'yasr_multiset_options_group', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
        	'yasr_multiset_options' //The name of an option to sanitize and save.
    	);	    	

    	$option_multiset = get_option( 'yasr_multiset_options' );

    	add_settings_section( 'yasr_multiset_options_section_id', '', 'yasr_multiset_section_callback', 'yasr_multiset_tab' );
       		add_settings_field( 'yasr_color_scheme', __('Which color scheme do you want to use?', 'yasr') , 'yasr_color_scheme_callback', 'yasr_multiset_tab', 'yasr_multiset_options_section_id', $option_multiset);
     
	}

	function yasr_multiset_section_callback () {

		//Silence

	}

	function yasr_color_scheme_callback($option_multiset) {

		if (!$option_multiset['scheme_color']) {

			$option_multiset['scheme_color'] = 'light';

		}

    	?>

    	<input type='radio' name='yasr_multiset_options[scheme_color]' value='light' class='yasr-general-options-scheme-color' <?php if ($option_multiset['scheme_color']==='light') echo " checked=\"checked\" "; ?>  /> 
			<?php _e('Light', 'yasr')?>
			
		&nbsp;&nbsp;&nbsp;

		<input type='radio' name='yasr_multiset_options[scheme_color]' value='dark' class='yasr-general-options-scheme-color' <?php if ($option_multiset['scheme_color']==='dark') echo " checked=\"checked\" "; ?>  /> 
			<?php _e('Dark', 'yasr')?>
			<br />

			<br />

		<a href="#" id="yasr-color-scheme-preview-link"><?php _e("Preview", "yasr") ?></a>

		<div id="yasr-color-scheme-preview" style="display:none">
	   			<?php 

	   				_e("Light theme", "yasr");
	   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "yasr-multi-set.png>";

	   				echo "<br /> <br />";

	   				_e("Dark theme", "yasr");
	   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "dark-multi-set.png>";
	   			 ?>
	   	</div>

		<p>

    	<?php
    }


/****** Create a form for settings page to create new multi set ******/
	function yasr_display_multi_set_form() {
		?>
		
		<h4 class="yasr-multi-set-form-headers"><?php _e("Add New Multiple Set", "yasr"); ?></h4>
		<em><?php _e('Name, Element#1 and Element#2 MUST be filled and must be long at least 3 characters', 'yasr') ?></em>
		<p>
		<form action="<?php echo admin_url('options-general.php?page=yasr_settings_page&tab=manage_multi') ?>" id="form_add_multi_set" method="post">
			<strong><?php _e("Name", 'yasr')?></strong> 
			<input type="text" name="multi-set-name" id="new-multi-set-name" class="input-text-multi-set">
			<input type="hidden" name="action" value="yasr_new_multi_set_form" />

			<p></p>
			<?php _e("You can insert up to nine elements", "yasr") ?>
			<br />

			<?php for($i=1; $i<=9; $i++) { 

				echo "<strong>" . __("Element ", "yasr") . "#$i" . "</strong>";
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



	function yasr_edit_multi_form() {

		$multi_set=yasr_get_multi_set();

		global $wpdb;

		$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the last of the last query

		if ($n_multi_set > 1) {
			?>

			<div class="yasr-manage-multiset">

				<h4 class="yasr-multi-set-form-headers"><?php _e("Manage Multiple Set" , "yasr"); ?></h4>

				<?php _e('Wich set do you want to edit or remove?', 'yasr')?>

				<select id ="yasr_select_edit_set">
    				<?php foreach ($multi_set as $name) { ?>
		    		<option value="<?php echo $name->set_id ?>"><?php echo $name->set_name ?></option>
	  				<?php } //End foreach ?>
  				</select>

  				<button href="#" class="button-delete" id="yasr-button-select-set-edit-form"><?php _e("Select"); ?></button>

					
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
			
				<div class="yasr-manage-multiset-single">

					<h4 class="yasr-multi-set-form-headers"><?php _e("Manage Multiple Set", "yasr"); ?></h4>

					<form action=" <?php echo admin_url('options-general.php?page=yasr_settings_page&tab=manage_multi') ?>" id="form_edit_multi_set" method="post">

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
			                        Element #$i <input type=\"text\" value=\"$name->name\" name=\"edit-multi-set-element-$i\"> 
			                        <input type=\"hidden\" value=\"$name->id\" name=\"db-id-for-element-$i\"> 
			                    </td>

			                    <td width=\"20%\" style=\"text-align:center\">
			                        <input type=\"checkbox\" value=\"$name->id\" name=\"remove-element-$i\">
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

			                <td width=\"80%\">" . __("Remove whole set?", "yasr") . "</td>

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
			_e("No Multiple Set were found", "yasr");
		}

	}//End function



/****** Get and output multiple set in a form and table, used in settings page ******/

    add_action( 'wp_ajax_yasr_get_multi_set', 'yasr_get_multi_set_callback' );

    function yasr_get_multi_set_callback() {
        if (isset($_POST['set_id']) && $_POST['set_id'] != '' ) {
            $set_type = $_POST['set_id'];
        }
        else {
            exit ();
        }

        global $wpdb;

        $set_name=$wpdb->get_results($wpdb->prepare("SELECT field_name AS name, field_id AS id
                            FROM " . YASR_MULTI_SET_FIELDS_TABLE . "  
                            WHERE parent_set_id=%d 
                            ORDER BY field_id ASC", $set_type));

        

        ?>

        <form action=" <?php echo admin_url('options-general.php?page=yasr_settings_page&tab=manage_multi') ?>" id="form_edit_multi_set" method="post">
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
                        Element #$i <input type=\"text\" value=\"$name->name\" name=\"edit-multi-set-element-$i\">  
                        <input type=\"hidden\" value=\"$name->id\" name=\"db-id-for-element-$i\">
                    </td>

                    <td width=\"20%\" style=\"text-align:center\">
                        <input type=\"checkbox\" value=\"$name->id\" name=\"remove-element-$i\">
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

                <td width=\"80%\">" . __("Remove whole set?", "yasr") . "</td>

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

        <?php

        die();

    } //End function 




/****** Validate new multi set form ******/
	function yasr_process_new_multi_set_form() {

		if ( isset( $_POST['multi-set-name'])) {

			global $wpdb;
			
			if ( !current_user_can( 'manage_options' ) ) {
	      		wp_die( 'You are not allowed to be on this page.' );
	   		}

	   		// Check nonce field
	  		check_admin_referer( 'add-multi-set', 'add-nonce-new-multi-set' );

	   		$array_errors = array(); 
	   		$error = FALSE;

	   		//IF thes fields are not empty go ahed
	  		if ($_POST['multi-set-name']!='' && $_POST['multi-set-name-element-1']!='' && $_POST['multi-set-name-element-2']!=''  ) { 
	   		
	  			$multi_set_name = ucfirst(strtolower($_POST['multi-set-name']));

	  			$multi_set_name_element_=array();

	  			$multi_set_name_element_[1]=$_POST['multi-set-name-element-1'];
	  			$multi_set_name_element_[2]=$_POST['multi-set-name-element-2'];

	  			//If multi set name is shorter than 3 characher come back
	   			if (mb_strlen($multi_set_name) < 3 || mb_strlen($multi_set_name_element_[1]) <3 || mb_strlen($multi_set_name_element_[2]) < 3 ) {
	   				$array_errors[] = "Content field must be longer than 3 chars";
	   				$error=TRUE;
	   			} 


	   			if (mb_strlen($multi_set_name) > 23 || mb_strlen($multi_set_name_element_[1]) > 23 || mb_strlen($multi_set_name_element_[2]) > 23 ) {
	   				$array_errors[] = "Content field must be shorter than 23 chars";
	   				$error=TRUE;
	   			} 

	   			//Check if a set with that name already exists
	   			$check_name_exists=$wpdb->get_results("SELECT set_name FROM " . YASR_MULTI_SET_NAME_TABLE . " ORDER BY set_id ASC");

				foreach ($check_name_exists as $set_name) {

	   				if ($multi_set_name==$set_name->set_name) {
	   					$array_errors[] = "You already have a set with this name";
	   					$error=TRUE;
	   				}

	   			}

	   			$element_filled=2;

	   			//If filled get the element from 3 to 9
	  				for($i=3; $i<=9; $i++) {

	  					if (isset($_POST["multi-set-name-element-$i"]) && $_POST["multi-set-name-element-$i"]!='') {

	  						$multi_set_name_element_[$i]=$_POST["multi-set-name-element-$i"];

	  						if (mb_strlen($multi_set_name_element_[$i]) < 3) {
	  							$array_errors[] = "Field # $i must be at least 3 characters";
	   							$error=TRUE;
	  						}

	  						if (mb_strlen($multi_set_name_element_[$i]) > 23) {
	  							$array_errors[] = "Field # $i must be shorter than 23 characters";
	   							$error=TRUE;
	  						}

	  						$element_filled++;
	  					}
	  					
	  				}

	  			//If there isnt any error write in the table
	   			if (!$error) { 

	   					//get the highest id in table
	                    $highest_id=$wpdb->get_results("SELECT set_id FROM " . YASR_MULTI_SET_NAME_TABLE . " ORDER BY set_id DESC LIMIT 1 ");
	                
	                    if (!$highest_id) {
	                        $name_table_new_id=0;
	                    }

	                    foreach ($highest_id as $id) {
	                        $name_table_new_id=$id->set_id + 1;
	                    }

	   					$insert_multi_name_success = $wpdb->replace(
							YASR_MULTI_SET_NAME_TABLE,
							array(
								'set_id' =>$name_table_new_id,
								'set_name' =>$multi_set_name
							),
							array ('%d', '%s')
						);

	   					//If multi set name hase been inserted, now we're going to insert elements
	   					if ($insert_multi_name_success) {

	   						//get the highest id in table
	                    	$highest_id=$wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_FIELDS_TABLE . " ORDER BY id DESC LIMIT 1 ");

	                    	if (!$highest_id) {
	                        	$field_table_new_id=0;
	                    	}

	                    	foreach ($highest_id as $id) {
	                        	$field_table_new_id=$id->id + 1;
	                    	}

	                    	for ($i=1; $i<=$element_filled; $i++) {
	   							$insert_set_value=$wpdb->replace(
								YASR_MULTI_SET_FIELDS_TABLE,
									array(
										'id' => $field_table_new_id,
										'parent_set_id' =>$name_table_new_id,
										'field_name' =>$multi_set_name_element_[$i],
										'field_id' =>$i
									),
									array ('%d', '%d', '%s', '%d')
								);
								$field_table_new_id++; //Avoid overwrite
	   						} //End for

	   						if ($insert_set_value) {
	   							echo "<div class=\"updated\"><p><strong>";
	   								_e("Settings Saved", 'yasr');
	   							echo "</strong></p></div> ";
	   						}

	   						else {
	   							_e("Something goes wrong trying insert set field name. Please report it", "yasr");
	   						}

	   					} //End if $insert_multi_name_success

	   					else {
	   						_e("Something goes wrong trying insert Multi Set name. Please report it", "yasr");
	   					}

	   			} //End if !$error

	   		}  //End if $_POST['multi-set-name']!='' 
	  		
	  		//Else multi set's name and first 2 elements are empty
	   		else {
	   			$array_errors[] = "Multi Set's name and first 2 elements can't be empty";
	   			$error=TRUE;
	   		}

	   		if ($error) {
	   			return $array_errors;
			}

	    } //End if ( isset( $_POST['multi-set-name']) ) {

	} //End yasr_process_new_multi_set_form() function



	function yasr_process_edit_multi_set_form() {

		$error = FALSE;

		if ( isset( $_POST['yasr_edit_multi_set_form']) ) {

			$set_id = $_POST['yasr_edit_multi_set_form'];

			$number_of_stored_elements = $_POST['yasr-edit-form-number-elements'];

			global $wpdb;

			$array_errors = array(); 
			 
			if ( !current_user_can( 'manage_options' ) ) {
	      		wp_die( 'You are not allowed to be on this page.' );
	   		}

	   		// Check nonce field
	  		check_admin_referer( 'edit-multi-set', 'add-nonce-edit-multi-set' );


	  		//Check if user want to delete entire set

	  		if (isset($_POST["yasr-remove-multi-set"])) {
	  			
	  			$remove_set = $wpdb->delete (
	  								YASR_MULTI_SET_NAME_TABLE,
									array(
										'set_id' => $set_id,
									),
									array ('%d')
								);

	  			$remove_set_values = $wpdb->delete (
	  								YASR_MULTI_SET_FIELDS_TABLE,
									array(
										'parent_set_id' => $set_id,
									),
									array ('%d')
								);

	  			$remove_set_votes = $wpdb->delete (
	  								YASR_MULTI_SET_VALUES_TABLE,
									array(
										'set_type' => $set_id,
									),
									array ('%d')
								);

	  			if ($remove_set==FALSE) {
	  				$error = TRUE; 
					$array_errors[] .= __("Something goes wrong trying to delete a Multi Set . Please report it", 'yasr');
	  			}


	  			//Comment this out, if try to delete an empty set print error
	  			/*if ($remove_set_values==FALSE) {
	  				$error = TRUE; 
					$array_errors[] .= __("Something goes wrong trying to delete data fields for a set. Please report it", 'yasr');
				}
				*/

				//Comment this out, will echo error even if the value for that field it's just empty
				/*if ($remove_set_votes==FALSE) {
	  				$error = TRUE; 
					$array_errors[] .= __("Something goes wrong trying to delete data values for a set. Please report it", 'yasr');
				}*/

	  		}

	  		for ($i = 0; $i <= 9; $i++) {

	  			//Than, check if the user want to remove some field
	  			if (isset($_POST["remove-element-$i"]) && !isset($_POST["yasr-remove-multi-set"]) ) {

	  				$field_to_remove = $_POST["remove-element-$i"];

	  				$remove_field = $wpdb->delete (
	  								YASR_MULTI_SET_FIELDS_TABLE,
									array(
										'parent_set_id' => $set_id,
										'field_id' =>$field_to_remove
									),
									array ('%d', '%d')
								);

	  				$remove_values = $wpdb->delete (
	  								YASR_MULTI_SET_VALUES_TABLE,
									array(
										'set_type' => $set_id,
										'field_id' =>$field_to_remove
									),
									array ('%d', '%d')
								);

	  				if ($remove_field == FALSE) {
						$error = TRUE; 
						$array_errors[] = __("Something goes wrong trying to delete a Multi Set's element. Please report it", 'yasr');
	  				}


	  				//Comment this out, will echo error even if the value for that field it's just empty
	  				/*if ($remove_values == FALSE) {
						$error = TRUE; 
						$array_errors[] = __("Something goes wrong trying to delete data value for an element. Please report it", 'yasr');
	  				}*/

	 
	  			}  //End if isset $_POST['remove-element-$i']


	  			//update the stored elements with the new ones
	  			if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["yasr-remove-multi-set"]) && !isset($_POST["remove-element-$i"]) && $i <= $number_of_stored_elements ) {

	  				$field_name = $_POST["edit-multi-set-element-$i"];

	  				$field_id = $_POST["db-id-for-element-$i"];

		  			//if elements name is shorter than 3 chars
		  			if (mb_strlen($field_name) <3) {
						$array_errors[] = __("Field # $i must be at least 3 characters", "yasr");
						$error=TRUE;
		  			}

		  			if(mb_strlen($field_name) > 23) {
		  				$array_errors[] = __("Field # $i must be shorter than 23 characters", "yasr");
						$error=TRUE;
		  			}

	  				else {

	  					//Check if field name is changed
	  					$field_name_in_db = $wpdb->get_results("SELECT field_name FROM " . YASR_MULTI_SET_FIELDS_TABLE . " WHERE field_id=$field_id AND parent_set_id=$set_id");

	  					foreach ($field_name_in_db as $field_in_db) {
	  						$field_name_in_database = $field_in_db->field_name;
	  					}

	  					//if field name in db is different from field name in form update it
	  					if ($field_name_in_database != $field_name) {

		  					$insert_field_name=$wpdb->update(
									YASR_MULTI_SET_FIELDS_TABLE,

										array(
											'field_name' =>$field_name,
										),

										array(
											'parent_set_id' =>$set_id,
											'field_id' =>$field_id
										),

										array ('%s'),

										array ('%d', '%d')
										
									);

		  					if ($insert_field_name == FALSE) {
		  						$error = TRUE; 
								$array_errors[] = __("Something goes wrong trying to update a Multi Set's element. Please report it", 'yasr');
		  					}

	  				    } //End if ($field_name_in_database != $field_name) {

	  				}

	  			} //End if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["remove-element-$i"]) && $i<=$number_of_stored_elements ) 
	  				

	  			//If $i > number of stored elements, user is adding new elements, so we're going to insert the new ones
	  			if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["yasr-remove-multi-set"]) && !isset($_POST["remove-element-$i"]) && $i > $number_of_stored_elements ) {

	  				$field_name = $_POST["edit-multi-set-element-$i"];

	  				//if elements name is shorter than 3 chars return error. I use mb_strlen($field_name) > 1
	  				//because I don't wont return error if an user add an empty field. An empty field will be
	  				//just ignored  
	  				if (mb_strlen($field_name) > 1 && mb_strlen($field_name) < 3) {
	  							$array_errors[] = __("Field # $i must be at least 3 characters", "yasr");
	   							$error=TRUE;
	  				}

	  				if(mb_strlen($field_name) > 23) {
		  				$array_errors[] = __("Field # $i must be shorter than 23 characters", "yasr");
						$error=TRUE;
		  			}

	  				//if field is not empty
	  				elseif ($field_name != '') {

	  					$highest_id=$wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_FIELDS_TABLE . " ORDER BY id DESC LIMIT 1 ");

	  					$highest_field_id = $wpdb->get_results("SELECT field_id FROM " . YASR_MULTI_SET_FIELDS_TABLE . " ORDER BY field_id DESC LIMIT 1 ");

	  					foreach ($highest_id as $id) {
	                        	$field_table_new_id=$id->id + 1;
	                    }

	                    foreach ($highest_field_id as $id) {
	                    	$new_field_id = $id->field_id+1;
	                    }

	  					$insert_set_value=$wpdb->replace(
								YASR_MULTI_SET_FIELDS_TABLE,
									array(
										'id' => $field_table_new_id,
										'parent_set_id' =>$set_id,
										'field_name' =>$field_name,
										'field_id' =>$new_field_id
									),
									array ('%d', '%d', '%s', '%d')
								);
								$field_table_new_id++; //Avoid overwrite

	  					if ($insert_set_value == FALSE) {
	  						$error = TRUE; 
							$array_errors[] = __("Something goes wrong trying to insert set field name in edit form. Please report it", 'yasr');
	  					}

	  				} //end else 
	  			}


	  		} //End for


	  		if ($error) {
	   			return $array_errors;
			}
			else {
				echo "<div class=\"updated\"><p><strong>";
	   				_e("Settings Saved", 'yasr');
	   			echo "</strong></p></div> ";
			}


		} //End if isset( $_POST['yasr_edit_multi_set_form']

		
	} //End yasr_process_edit_multi_set_form() function




add_action( 'admin_init', 'yasr_style_options_init' ); //This is for auto insert options

	function yasr_style_options_init() {
    	register_setting(
        	'yasr_style_options_group', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
        	'yasr_style_options' //The name of an option to sanitize and save.
    	);	    	

    	$style_options = get_option( 'yasr_style_options' );

    	if (!$style_options) {

    		$style_options = array();
    		$style_options['textarea'] = NULL;

    	}

    	add_settings_section( 'yasr_style_options_section_id', __('Style Options', 'yasr'), 'yasr_style_section_callback', 'yasr_style_tab' );
    		add_settings_field( 'yasr_style_options_textarea', __('Custom CSS Styles', 'yasr'), 'yasr_style_options_textarea_callback', 'yasr_style_tab', 'yasr_style_options_section_id', $style_options );

	}

	function yasr_style_section_callback () {
		_e("Please use text area below to write your own CSS styles to override the default ones.", "yasr");
		echo "<strong>";
		_e("Leave it blank if you don't know what you're doing", "yasr");
		echo "</strong>";
	}

	function yasr_style_options_textarea_callback ($style_options) {

		echo ("
			<textarea rows=\"20\" cols=\"50\" name=\"yasr_style_options[textarea]\" id=\"yasr_style_options_textarea\">$style_options[textarea]</textarea> 
			");

	}


function yasr_go_pro () {

    ?>

        <div class="yasr-settingsdiv">

            <div id="yasr-info-pro-version">

                <?php 

                _e("Looking for more features?", "yasr");
                echo " <a href=\"https://yetanotherstarsrating.com/pro-version/\">" . __("Upgrade to yasr pro!", "yasr") . "</a>"; 
                
                echo "<br>";

                ?>

            </div>

            <table id="comparetable" class="softgreen">
                <tr>
                    <td class="blank"> </td>
                    <th>Free</th>
                    <th>Pro</th>
                </tr>
                
                <tr>
                    <td class="rowTitle"><?php _e("Unlimited ratings and votes" , "yasr"); ?></td>        
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                </tr>
                                           
                <tr>
                    <td class="rowTitle"><?php _e("Works with shortcodes" , "yasr"); ?></td>    
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                </tr>

                <tr>
                    <td class="rowTitle"><?php _e("Multi Set Support" , "yasr"); ?></td>    
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                </tr>
                <tr>
                    <td class="rowTitle"><?php _e("Logs and stats for visitors votes" , "yasr"); ?></td>    
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                </tr>
                <tr>
                    <td class="rowTitle"><?php _e("Localization (.po and .mo files included)" , "yasr"); ?></td>    
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                </tr>
                <tr>
                    <td class="rowTitle"><?php _e("Rich Snippet Support" , "yasr"); ?></td>    
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                </tr>
                <tr>
                    <td class="rowTitle"><?php _e("Rankings for reviews, votes and users" , "yasr"); ?></td>    
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                </tr>
                <tr>
                    <td class="rowTitle"><?php _e("Rankings Customization" , "yasr"); ?></td>    
                    <td><img src=<?php echo YASR_IMG_DIR . '/addRedX2.png' ?> alt='icon' /></td>
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                </tr>
                <tr>
                    <td class="rowTitle"><?php _e("Stars Customization" , "yasr"); ?></td>    
                    <td><?php _e("Size Only" , "yasr"); ?></td>
                    <td> <img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /> <br /><?php _e("Users can choose different ready to use sets or can upload their own images." , "yasr"); ?></td>
                </tr>
                <tr>
                    <td class="rowTitle"><?php _e("Users can review in comments" , "yasr"); ?></td>    
                    <td><img src=<?php echo YASR_IMG_DIR . '/addRedX2.png' ?> alt='icon' /></td>
                    <td><img src=<?php echo YASR_IMG_DIR . '/addCheck.png' ?> alt='icon' /></td>
                </tr>
                               
            </table>

            <?php 

                echo "<img src=" . YASR_IMG_DIR . "/addExclamation.png alt=icon /> =" ;

                _e("Not avaible yet", "yasr");

                echo "<p>";

            ?>
        
        </div>

    <?php

}

/*** Facebook sdk, since version  0.8.8 ***/

function yasr_include_fb_sdk () {

	$lang = get_locale();

	$lang = json_encode("$lang");

	?>

	<div id="fb-root"></div>
	<script>
		(function(d, s, id) {
			var lang = <?php echo ($lang); ?>;
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) return;
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/"+lang+"/sdk.js#xfbml=1&version=v2.3&appId=113845018658519";
			fjs.parentNode.insertBefore(js, fjs);
		}
		(document, 'script', 'facebook-jssdk'));
	</script>

	<?php

}

/****** Facebook box, since version 0.8.8 ******/

function yasr_fb_box ($position=FALSE) {

	if ($position && $position == "bottom") {

		$yasr_fb_class = "yasr-donatedivbottom";

	}

	else {

		$yasr_fb_class = "yasr-donatedivdx";

	}

	?>

	<div class="<?php echo $yasr_fb_class; ?>" style="display:none">

	<h2><?php _e('Keep in touch!', 'yasr'); ?></h2>

		<div class="fb-page" data-href="https://www.facebook.com/yetanotherstarsrating" data-hide-cover="false" data-show-facepile="true" data-show-posts="false">
			<div class="fb-xfbml-parse-ignore">
				<blockquote cite="https://www.facebook.com/yetanotherstarsrating"><a href="https://www.facebook.com/yetanotherstarsrating">YASR - Yet Another Stars Rating</a></blockquote>
			</div>
		</div>
	</div>

	<?php

}

/** Add a box on the right for asking to rate 5 stars on Wordpress.org 
*   It must be appear after 10 logged rating, after 100 and after 1000
*   Since version 0.9.0
*/

function yasr_ask_rating ($position=FALSE) {

	$transient = get_site_transient ('yasr_hide_ask_rating');

	if (!$transient) {

		if ($position && $position == "bottom") {

			$yasr_metabox_class = "yasr-donatedivbottom";

		}

		else {

			$yasr_metabox_class = "yasr-donatedivdx";

		}

		$n_stored_ratings = yasr_count_logged_vote ();

		$div = "<div class=\"$yasr_metabox_class\" id=\"yasr-ask-five-stars\" style=\" display:none; border-left: 3px solid #7AD03A; font-size: 14px;\">";

		if($n_stored_ratings > 20) {

			$text = sprintf( __('Hey, seems like you reached %s votes on your site throught YASR! That\'s cool!', 'yasr'),'<strong>'.$n_stored_ratings.'</strong>'); 
			$text .= "<br />";
			$text .= __('Can I ask a favor?', 'yasr');
			$text .= "<br />";
			$text .= __('Can you please rate YASR 5 stars on', 'yasr'); 
			$text .= ' <a href="https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5">wordpress.org?</a>';
			$text .= __(' It will require just 1 min but it\'s a HUGE help for me. Thank you.' , 'yasr');
			$text .= "<br /><br />";
			$text .= "<em>> Dario Curvino</em>";

			$text .= "<ul>

					<li><a href=\"https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5\">" . __("Ok, I'm glad to help!" , "yasr") ."</a></li>
					<li><a href=\"#\" id=\"yasr-ask-five-star-later\">" . __("Remind me later!" , "yasr") ."</a></li>
					<li><a href=\"#\" id=\"yasr-ask-five-close\">" . __("Don't need to ask, I already did it!" , "yasr") ."</a></li>

			</ul>";


			$div_and_text = $div . $text . '</div>';

			echo $div_and_text;

		} 

	} //End if (!transient)
	

}



/** Change default admin footer on yasr settings pages 
*       $text is the default wordpress text
*		Since 0.8.9
*/

add_filter( 'admin_footer_text', 'yasr_custom_admin_footer' );

function yasr_custom_admin_footer ($text) {

	if (isset($_GET['page'])) {
    		$yasr_page = $_GET[ 'page' ];

    		if ($yasr_page == 'yasr_settings_page') {

    			$custom_text = ' | <i>';
				$custom_text .= sprintf( __( 'Thank you for using <a href="%s" target="_blank">Yet Another Stars Rating</a>. Please <a href="%s" target="_blank">rate it</a> 5 stars on <a href="%s" target="_blank">WordPress.org</a>', 'yasr' ), 'https://yetanotherstarsrating.com', 'https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5', 'https://wordpress.org/support/view/plugin-reviews/yet-another-stars-rating?filter=5' );
				$custom_text .= '</i>';

				return $text . $custom_text;

    		}

    		else {

    			return $text;

    		}

		}

	else {

		return $text;

	}

}



/*************************BEGIN IMPORT FUNCTIONS*******************************/

/****** Check for previous GD STAR INSTALLATION *******/
function yasr_search_gd_star_rating () {
	$gd_star_rating_found=FALSE;

	if ( is_plugin_active( 'gd-star-rating/gd-star-rating.php' ) ) {
		$gd_star_rating_found=TRUE;
	}

	else {
		global $wpdb;

		$gdstar_table=$wpdb->prefix . 'gdsr_data_article';

		if ($wpdb->get_var("SHOW TABLES LIKE '$gdstar_table'") == $gdstar_table) {
			$gd_star_rating_found=TRUE;
		}

		else {
			__( 'No previous Gd Star Rating installation was found', 'yasr' );
		}
	}

	return $gd_star_rating_found;

}

/****** Import the following Gd Star Rating columns FROM gdsr_data_article
		post_id
		user_voters
		user_votes		
		visitor_voters
		visitor_votes	
		review
 ******/

function yasr_import_gdstar_data(){

	$gd_stars_option=get_option("gd-star-rating");

	if ($gd_stars_option) {

		$n_visitors_stars=$gd_stars_option['stars'];
		$n_review_stars=$gd_stars_option['review_stars'];
	}

	else {

		$n_visitors_stars=5;
		$n_review_stars=5;

	}

	global $wpdb;

	$gdsr_data_article=$wpdb->prefix . 'gdsr_data_article';

	$data=$wpdb->get_results(" SELECT gd.post_id, (gd.user_voters + gd.visitor_voters) AS voters, 
							(gd.user_votes + gd.visitor_votes) AS sum_votes, 
							gd.review,
						  	p.ID
						  	FROM $gdsr_data_article AS gd, $wpdb->posts AS p
						  	WHERE gd.post_id = p.id" );

	//If in gd star rating user didn't use 5 rating system convert it

	//Review Convertion
	if($n_review_stars != 5) {

		foreach ($data as $data_row) {
			$data_row->review=($data_row->review/$n_review_stars)*5; //Review vote convertion
		}

	}


	if ($n_visitors_stars != 5) {

		foreach ($data as $data_row) {
			$data_row->sum_votes=($data_row->sum_votes/$n_visitors_stars)*5; //Visitor Vote conversion
		}

	}

	return $data;

}


/****** Import the following GDSR columns FROM wp_gdsr_multis ******/
function yasr_import_gdstar_multi_set(){
	global $wpdb;

	$table_name = $wpdb->prefix . 'gdsr_multis'; 

	$multi_set = $wpdb->get_results (" SELECT multi_id, name, stars, object FROM $table_name");

	return $multi_set;
}


/****** Import the following Gd Star Rating columns:
		'id' and 'post_id': FROM gdsr_multis_data
		'user_votes' and 'item_id' FROM gdsr_multis_values
		'multi_id' from gdsr_multis_values

		Then check if some multi set has star's number !=5 and convert every vote
		that use that set

		Thanks to Alessandro Carlo Chirico for his helps in this query!
******/
function yasr_import_gdstar_multi_value(){
	global $wpdb;

	$table_gdsr_multis_values=$wpdb->prefix . 'gdsr_multis_values';
	$table_gdsr_multis_data=$wpdb->prefix . 'gdsr_multis_data';
	$table_gdsr_multis=$wpdb->prefix . 'gdsr_multis';

	$multi_set_data=$wpdb->get_results (" SELECT d.post_id,  
							 v.user_votes, v.item_id, 
							 m.multi_id
							 FROM $table_gdsr_multis_values AS v, $table_gdsr_multis_data AS d, $wpdb->posts AS p, $table_gdsr_multis AS m
							 WHERE v.id = d.id
							 AND 0 <> (
							 SELECT SUM( user_votes )
							 FROM $table_gdsr_multis_values AS tabin
							 WHERE tabin.id = v.id )
							 AND p.ID = d.post_id
							 AND d.multi_id = m.multi_id
							 AND p.post_status = 'publish'
							 AND v.source = 'rvw' 
							 ORDER BY d.post_id, m.multi_id, v.item_id ASC ");


	//Import multi set name: if a multiset use != 5 stars,
	//then i search wich vote has that set and convert 
	//the vote to fit 5 stars vote 
	$old_multi_set = yasr_import_gdstar_multi_set();

	foreach ($old_multi_set AS $multi_set) { //Search wich set not use 5 stars
		if ($multi_set->stars != 5) {
			//Search in the $multi_set_data if some vote use a set with !=5 stars
			foreach ($multi_set_data as $data_row) { 
				if ($data_row->multi_id == $multi_set->multi_id) { 
						$data_row->user_votes=($data_row->user_votes/$multi_set->stars)*5; //Vote convertion
				}
			} //End foreach $multi_set_data
		} //End if $multi_set->stars != 5
	}

	return ($multi_set_data);
}

/****** Import gd star logs ******/
function yasr_import_gdstar_logs() {

	global $wpdb;

	$table_gdsr_logs=$wpdb->prefix . 'gdsr_votes_log';

	$gdsr_log_data = $wpdb->get_results (" SELECT id AS post_id, user_id, vote, voted AS date, ip
										   FROM $table_gdsr_logs
										   WHERE vote_type = 'article' 
										   ORDER BY date DESC ");

	return $gdsr_log_data;

}

/****** Insert Gd Star Rating review in overall rating ******/
function yasr_insert_gdstar_data($votes){
	global $wpdb;

	foreach ( $votes as $column ) {
		$result=$wpdb->replace(
			YASR_VOTES_TABLE, 
			array ( 
					'post_id' => $column->post_id, 
				    'overall_rating' => $column->review,
				    'number_of_votes' => $column->voters,
				    'sum_votes' =>$column->sum_votes
			),
			array( '%d', '%d', '%s', '%d', '%d')
		);
	}

	if ($result) {
		return TRUE;
	};
}

/****** Insert logs ******/
function yasr_insert_gdstar_logs($logs) {
	global $wpdb;

	foreach ($logs as $column) {
		$result = $wpdb->replace(
			YASR_LOG_TABLE,
			array(
					'post_id' => $column->post_id,
					'multi_set_id' => '-1',
					'user_id' => $column->user_id,
					'vote' => $column->vote,
					'date' => $column->date,
					'ip' => $column->ip
				),
			array( '%d', '%s', '%d', '%s', '%s', '%s' )
		);
	}

	if ($result) {
		return TRUE;
	}

}

/****** Insert gd star rating multi set name 

		Thanks to Alessandro Carlo Chirico for his help in regex!
******/
function yasr_insert_gdstar_multi_set($multi_set_names) {

	global $wpdb;

	$i=0;
	foreach ($multi_set_names as $value) {

		$result = $wpdb->replace(
			YASR_MULTI_SET_NAME_TABLE,
			array(
				'set_id' =>$value->multi_id,
				'set_name' =>$value->name
				),
			array ('%d', '%s')
		);

		if ($result) {

			if(preg_match_all('#".+?"#', $value->object, $matches)) {
				$fields = $matches[0];
			} 

			$fields = str_replace('"', '', $fields);

			foreach ($fields as $id => $field_name) {
				$result2=$wpdb->replace(
					YASR_MULTI_SET_FIELDS_TABLE,
					array(
						'id' => $i,
						'parent_set_id' =>$value->multi_id,
						'field_name' =>$field_name,
						'field_id' =>$id
						),
					array ('%d', '%d', '%s', '%d')
				);
				$i++;
			} //End Foreach ($fields as $id => $field_name)

		} //End if $result
	}

	if ($result && $result2) {
		return "OK";
	}

}

/****** 
Check how many stars the existing set use, than convert the vote
into 5 stars. Last insert GD Star Rating multi values 
******/
function yasr_insert_gdstar_multi_value($multi_datas) {
	global $wpdb;

		$i=1;
		foreach ($multi_datas as $value) {
			$result=$wpdb->replace(
				YASR_MULTI_SET_VALUES_TABLE,
				array(
					'id' =>$i,
					'post_id' => $value->post_id,
					'votes' => $value->user_votes, 
					'field_id' => $value->item_id, 
					'set_type' => $value->multi_id
				), 
				array('%d', '%d', '%s', '%d','%d')
			);
			$i++;
	}

	return $result;
}


/************************************************END IMPORT FUNCTIONS****************************************************/


?>
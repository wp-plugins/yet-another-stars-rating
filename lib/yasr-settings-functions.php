<?php


/****** Add auto insert option ******/

	add_action( 'admin_init', 'yasr_auto_insert_options_init' ); //This is for auto insert options

		function yasr_auto_insert_options_init() {
	    	register_setting(
	        	'yasr_auto_insert_options_group', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
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


	    	<p>&nbsp;</p>


	    <?php
		} //End function yasr_where_auto_insert_callback



/****** Add choose snippet option ******/

	add_action( 'admin_init', 'yasr_choose_snippet_init' );

		function yasr_choose_snippet_init() {

			register_setting(
				'yasr_choose_snippet_group', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
				'yasr_snippet_option' //The name of an option to sanitize and save.
				);

			$choosen_snippet = get_option( 'yasr_snippet_option' );

			if (!$choosen_snippet || !$choosen_snippet['what']) {
	    		$choosen_snippet['what']='overall_rating';
	    	}

			add_settings_section('yasr_choose_snippet_section_id', __('What rich snippets do you want to use?', 'yasr'), 'yasr_snippet_section_callback', 'yasr_settings_page');
				add_settings_field( 'yasr_choose_snippet_id', __('Choose one', 'yasr'), 'yasr_choose_snippet_callback', 'yasr_settings_page', 'yasr_choose_snippet_section_id', $choosen_snippet );

		}


		function yasr_snippet_section_callback() {
		}


		function yasr_choose_snippet_callback($choosen_snippet) {

			?>

		    	<input type="radio" name="yasr_snippet_option[what]" value="overall_rating" class="yasr_choose_snippet" <?php if ($choosen_snippet['what']==='overall_rating') echo " checked=\"checked\" "; ?> >
		    		<?php _e('Review Rating', 'yasr') ?>
		   			<br />

		    	<input type="radio" name="yasr_snippet_option[what]" value="visitor_rating" class="yasr_choose_snippet" <?php if ($choosen_snippet['what']==='visitor_rating') echo " checked=\"checked\" "; ?> >
		    		<?php _e('Aggregate Rating', 'yasr')?>
		   			<br />

		   		<div class="yasr_snippet_explained">
		   			<p>&nbsp;</p>
		   			<?php 

		   				_e("If you select \"Review Rating\", your site will be indexed from search engines like this: ", "yasr");
		   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "/yasr_review.png>";

		   				echo "<br /> <br />";

		   				_e("If, instead, you choose \"Aggregate Rating\", your site will be indexed like this", "yasr");
		   				echo "<br /><br /><img src=" . YASR_IMG_DIR . "/yasr_aggregate.jpg>";
		   			 ?>
		   		</div>



	<?php

		}

?>
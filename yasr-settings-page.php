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

if ( !current_user_can( 'manage_options' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ));
}

$n_multi_set = NULL; //Evoid undefined variable when printed outside multiset tab

$ajax_nonce_hide_ask_rating = wp_create_nonce( "yasr_nonce_hide_ask_rating" );

yasr_include_fb_sdk ();

?>

	<div class="wrap">

        <h2>Yet Another Stars Rating: <?php _e("Settings", "yasr"); ?></h2>

        <?php

        $error_new_multi_set=yasr_process_new_multi_set_form(); //defined in yasr-settings-functions

        $error_edit_multi_set=yasr_process_edit_multi_set_form(); //defined in yasr-settings-functions

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

			
		if (isset($_GET['tab'])) {
    		$active_tab = $_GET[ 'tab' ];
		}

		else {
			$active_tab = 'general_settings';
		}


		?>

        <h2 class="nav-tab-wrapper yasr-no-underline">
            <a href="?page=yasr_settings_page&tab=general_settings" class="nav-tab <?php if ($active_tab == 'general_settings' || ($active_tab != 'manage_multi' && $active_tab != 'style_options' && $active_tab != 'go_pro')) echo 'nav-tab-active'; ?>" > <?php _e("General Settings", "yasr"); ?> </a>
            <a href="?page=yasr_settings_page&tab=manage_multi" class="nav-tab <?php if ($active_tab == 'manage_multi') echo 'nav-tab-active'; ?>" > <?php _e("Multi Sets", "yasr"); ?> </a>
            <a href="?page=yasr_settings_page&tab=style_options" class="nav-tab <?php if ($active_tab == 'style_options') echo 'nav-tab-active'; ?>" > <?php _e("Styles", "yasr"); ?> </a>
            <a href="?page=yasr_settings_page&tab=go_pro" class="nav-tab <?php if ($active_tab == 'go_pro') echo 'nav-tab-active'; ?>" > <?php _e("Pro Features!", "yasr"); ?> </a>

        </h2>



    <?php 

        if ($active_tab == 'general_settings' || $active_tab != 'manage_multi' && $active_tab != 'style_options' && $active_tab != 'go_pro') {

    	?>

	    <div class="yasr-settingsdiv">
	        <form action="options.php" method="post" id="yasr_settings_form">
	            <?php
		            settings_fields( 'yasr_general_options_group' );
		            do_settings_sections('yasr_general_settings_tab' );
	            	submit_button( __('Save') );
	           	?>
	       	</form>
	    </div>

		    <?php 

	            yasr_fb_box ();
		        yasr_ask_rating ();

	        ?>

			<div class="yasr-space-settings-div">
			</div>

			<?php 

			$gd_star_rating_found = yasr_search_gd_star_rating();

			$gd_star_imported = get_option('yasr-gdstar-imported');

			//If gdstar rating has been found but data haven't been imported yet
			if ($gd_star_rating_found && !$gd_star_imported) {
				?>
				<div class="yasr-settingsdiv">
					<h3><?php _e("Import Gd Star Rating", "yasr"); ?></h3>
					<?php _e("I've found a previous installation of Gd Star Rating.", "yasr"); ?> <br /><?php _e("Do you want proceed to import data?", "yasr"); ?>
					<br />
		        	<button href="#" class="button-delete" id="import-gdstar"><?php _e('Yes, Begin Import', 'yasr'); ?></button>

		        	<div id="yasr-import-gdstar-div" style="display:none;">
		          			<strong>
		          				<?php _e("Click on Proceed to import Gd Star Rating data."); ?>
		          			</strong>
		          				<br />
		          				<button href="#" class="button-primary" id="import-button"> <?php _e('Proceed', 'yasr'); ?></button>

		          				<span id="yasr-loader-importer" style="display:none;" >&nbsp;<img src="<?php echo YASR_IMG_DIR . "loader.gif" ?>">
		          				</span>
		          				<br />

		          			<div id="result-import">	
		          			</div>
					</div>
				</div>

				<div class="yasr-space-settings-div">
				</div>

			<?php

			} //End If $gd_star_rating_found && !$gd_star_imported

			else if ($gd_star_rating_found && $gd_star_imported==1) {
				?>

				<div class="yasr-settingsdiv">
					<h3><?php _e("Manage GD Star Data", "yasr"); ?></h3>
					<?php _e("Gd Star Rating has been already imported."); ?> <br />
					<?php _e("If you wish you can import it again, but", 'yasr'); ?><strong> <?php _e("you will lose all data you've collect since the import!", "yasr"); ?> </strong> 
					<br />
		        	<button href="#" class="button-delete" id="import-gdstar"><?php _e('Ok, Import Again'); ?></button>

		        	<div id="yasr-import-gdstar-div" style="display:none;">
	          			<strong>
	          				<?php _e("Click on Proceed to import again Gd Star Rating data. This may take a while!"); ?>
	          			</strong>
	          				<br />
	          				<button href="#" class="button-primary" id="import-button"> <?php _e('Proceed', 'yasr'); ?></button>

	          				<span id="yasr-loader-importer" style="display:none;" >&nbsp;<img src="<?php echo YASR_IMG_DIR . "loader.gif" ?>">
	          				</span>
	          				
	          				<br />

	          			<div id="result-import">	
	          			</div>

					</div>
				</div>

				<div class="yasr-space-settings-div">
				</div>

			<?php
			} //$gd_star_rating_found && $gd_star_imported==1$gd_star_rating_found = yasr_search_gd_star_rating();

		} //End if tab 'general_settings'

		?>

	<?php 

	if ($active_tab == 'manage_multi') {

		$multi_set=yasr_get_multi_set();

		global $wpdb;

		$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the last of the last query

		?>

		<div class="yasr-settingsdiv">
				
			<h3> <?php _e("Manage Multi Set", "yasr"); ?></h3>

			<p>

				<a href="#" id="yasr-multi-set-doc-link"><?php _e("What is a Multi Set?", "yasr") ?></a>

			</p>

			<div id="yasr-multi-set-doc-box" style="display:none">
				<?php _e("Multi Set allows you to insert a rate for each aspect about the product / local business / whetever you're reviewing, example in the image below.", "yasr");

				echo "<br /><br /><img src=" . YASR_IMG_DIR . "/yasr-multi-set.png> <br /> <br />";

				_e("You can create up to 99 different Multi Set and each one can contain up to 9 different fields. Once you've saved it, you can insert the rates while typing your article in the box below the editor, as you can see in this image (click to see it larger)", "yasr");

				echo "<br /><br /><a href=\"" . YASR_IMG_DIR ."yasr-multi-set-insert-rate.jpg\"><img src=" . YASR_IMG_DIR . "/yasr-multi-set-insert-rate-small.jpg></a> <br /> <br />";

				_e("In order to insert your Multi Sets into a post or page, you can either past the short code that will appear at the bottom of the box or just click on the star in the graphic editor and select \"Insert Multi Set\".", "yasr");

				?>

				<br /> <br />

				<a href="#" id="yasr-multi-set-doc-link-hide"><?php _e("Close this message", "yasr") ?></a>

			</div>

			<div class="yasr-multi-set-left">

				<div class="yasr-new-multi-set" >

					<?php yasr_display_multi_set_form(); ?>

				</div> <!--yasr-new-multi-set-->

			</div> <!--End yasr-multi-set-left-->

			<div class="yasr-multi-set-right">

				<?php yasr_edit_multi_form(); ?>

				<div id="yasr-multi-set-response" style="display:none">

				</div>

			</div> <!--End yasr-multi-set-right-->

			<div class="yasr-space-settings-div">
			</div>


			<div class="yasr-multi-set-choose-theme">

				<!--This allow to choose color for multiset-->
				<form action="options.php" method="post" id="yasr_multiset_form">
			            <?php
				            settings_fields( 'yasr_multiset_options_group' );
				            do_settings_sections('yasr_multiset_tab' );
			            	submit_button( __('Save') );
			           	?>
			    </form>

			</div>


		</div>


	    	<?php 

	            yasr_fb_box ();
		        yasr_ask_rating ();

	        ?>

			<div class="yasr-space-settings-div">
			</div>

			<?php

		} //End if ($active_tab=='manage_multi')


		if ($active_tab == 'style_options') {

			?>

			<div class="yasr-settingsdiv">
			        <form action="options.php" method="post" id="yasr_settings_form">
			            <?php
				            settings_fields( 'yasr_style_options_group' );
				            do_settings_sections('yasr_style_tab' );
			            	submit_button( __('Save') );
			           	?>
			       	</form>
			</div>


		    <?php 

	            yasr_fb_box ();
		        yasr_ask_rating ();

	        ?>

			<div class="yasr-space-settings-div">
			</div>


			<?php

		} //End tab style


		if ($active_tab == 'go_pro') {

            yasr_go_pro(); 

            yasr_fb_box ();

	        yasr_ask_rating ();


		}

		?>

		<?php 

		yasr_fb_box("bottom");

		yasr_ask_rating("bottom");

		?>

	<!--End div wrap-->
	</div> 



	<script type="text/javascript">

	    jQuery( document ).ready(function() {

	    	var activeTab = <?php echo (json_encode("$active_tab")); ?>;

   			var nMultiSet = <?php echo (json_encode("$n_multi_set")); ?> ;//Null in php is different from javascript NULL

   			var autoInsertEnabled = <?php echo (json_encode(YASR_AUTO_INSERT_ENABLED)); ?>;

		   	YasrSettingsPage(activeTab, nMultiSet, autoInsertEnabled);

	    }); //End jquery document ready
 		
	</script>

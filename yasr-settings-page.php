<?php 

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

if ( !current_user_can( 'manage_options' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ));
}
?>

	<div class="wrap">

        <h2>Yet Another Stars Rating: Settings</h2>

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

			
		if( isset( $_GET[ 'tab' ] ) ) {

    		$active_tab = $_GET[ 'tab' ];

		}

		else {

			$active_tab = 'general_settings';

		}


		?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=yasr_settings_page&tab=general_settings" class="nav-tab <?php if ($active_tab == 'general_settings') echo 'nav-tab-active'; ?>" > General Settings </a>
            <a href="?page=yasr_settings_page&tab=manage_multi" class="nav-tab <?php if ($active_tab == 'manage_multi') echo 'nav-tab-active'; ?>" > Multi Sets </a>
        </h2>



        <?php 

        if ($active_tab=='general_settings') {

        ?>

		    <div class="yasr-settingsdiv">
		        <form action="options.php" method="post" id="yasr_settings_form">
		            <?php
			            settings_fields( 'yasr_auto_insert_options_group' );
			            do_settings_sections('yasr_settings_page' );
		            	submit_button( __('Save') );
		           	?>
		       	</form>
		    </div>

		    <div class="yasr-donatedivdx" style="display:none">
	        	<h3><?php _e('Donations'); ?></h3>

	        	<?php _e('If you have found this plugin useful, please consider making a donation to help support future development. Your support will be much appreciated. '); ?>
	        	<br />
	        	<?php _e('Thank you!'); ?>
	        	<br />
	        	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="F3XJX2BSG3H4J">
					<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
				</form>
        	</div>

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
					<?php _e("I've found a previous installation of Gd Star Rating . <br />Do you want proceed to import data?", 'yasr'); ?>
					<br />
		        	<button href="#" class="button-delete" id="import-gdstar"><?php _e('Yes, Begin Import', 'yasr'); ?></button>

		        	<div id="yasr-import-gdstar-div" style="display:none;">
		          			<strong>
		          				<?php _e("Click on Proceed to Import Gd Star Rating Data."); ?>
		          			</strong>
		          				<br />
		          				<button href="#" class="button-primary" id="import-button"> <?php _e('Proceed', 'yasr'); ?></button>

		          				<span id="loader" style="display:none;" >&nbsp;<img src="<?php echo YASR_IMG_DIR . "/loader.gif" ?>">
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
		          				<?php _e("Click on Proceed to Import again Gd Star Rating Data. This may take a while!"); ?>
		          			</strong>
		          				<br />
		          				<button href="#" class="button-primary" id="import-button"> <?php _e('Proceed', 'yasr'); ?></button>

		          				<span id="loader" style="display:none;" >&nbsp;<img src="<?php echo YASR_IMG_DIR . "/loader.gif" ?>">
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

	if ($active_tab=='manage_multi') {

		$multi_set=yasr_get_multi_set();

		global $wpdb;

		$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the last of the last query


	?>

		<div class="yasr-settingsdiv">
				
				<h3> <?php _e("Manage multi-set"); ?></h3>

				<p>

					<a href="#" id="yasr-multi-set-doc-link"><?php _e("What is a Multi-set?") ?></a>

				</p>

				<div id="yasr-multi-set-doc-box" style="display:none">
					<?php _e("Multi-set allows you to insert a rate for each aspect about the product / local business / whetever
					you're reviewing, example in the image below.", "yasr");

					echo "<br /><br /><img src=" . YASR_IMG_DIR . "/yasr-multi-set.png> <br /> <br />";

					_e("You can create up to 99 different multi-sets and each one can contain up to 9 different fields. 
						Once you've saved it, you can insert the rates while typing your article in the box below the editor, 
						as you can see in this image (click to see it larger)", "yasr");

					echo "<br /><br /><a href=\"" . YASR_IMG_DIR ."yasr-multi-set-insert-rate.jpg\"><img src=" . YASR_IMG_DIR . "/yasr-multi-set-insert-rate-small.jpg></a> <br /> <br />";

					_e("In order to insert your text into a post or page, you can either past
						the short code that will appear at the bottom of the box or just click
						on the star in the graphic editor and select \"Insert Multi-Set\".", "yasr");

					?>

					<br /> <br />

					<a href="#" id="yasr-multi-set-doc-link-hide"><?php _e("Close this message") ?></a>

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


		</div>

		<div class="yasr-donatedivdx" style="display:none">
	        <h3><?php _e('Donations'); ?></h3>

	        	<?php _e('If you have found this plugin useful, please consider making a donation to help support future development. Your support will be much appreciated. '); ?>
	        	<br />
	        	<?php _e('Thank you!'); ?>
	        	<br />
	        	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="hosted_button_id" value="F3XJX2BSG3H4J">
					<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
					<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
				</form>
        </div>

		<div class="yasr-space-settings-div">
		</div>

<?php

	} //End if ($active_tab=='manage_multi')


	
?>

	<div class="yasr-donatedivbottom" style="display:none">
        	<h3><?php _e('Donations'); ?></h3>

        	<?php _e('If you have found this plugin useful, please consider making a donation to help support future development. Your support will be much appreciated. '); ?>
        	<br />
        	<?php _e('Thank you!'); ?>
        	<br />
        	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="F3XJX2BSG3H4J">
				<input type="image" src="https://www.paypalobjects.com/en_GB/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
				<img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
			</form>

        </div>

	<!--End div wrap-->
	</div> 



   <script type="text/javascript">

	//First Div code
		jQuery('#yasr_auto_insert_radio_on').on('click', function(){
			jQuery('.yasr_auto_insert_where_what_radio').prop('disabled', false);
		});

		jQuery('#yasr_auto_insert_radio_off').on('click', function(){
			jQuery('.yasr_auto_insert_where_what_radio').prop('disabled', true);
		});


<?php 

		if ($active_tab=='manage_multi') { 
			?>

			//Second div code
				jQuery('#yasr-multi-set-doc-link').on('click', function() {
					jQuery('#yasr-multi-set-doc-box').toggle("slow");
				});

				jQuery('#yasr-multi-set-doc-link-hide').on('click', function() {
					jQuery('#yasr-multi-set-doc-box').toggle("slow");
				});

				<?php if ($n_multi_set == 1) { ?>


					jQuery('#yasr-manage-multi-set-single').on('click', function() {

						jQuery('.yasr-manage-multiset-single').toggle();

						var counter = jQuery("#yasr-edit-form-number-elements").attr('value');

				    	counter++;

						jQuery("#yasr-add-field-edit-multiset").on('click', function() {

							if(counter>9){
					           		jQuery('#yasr-element-limit').show();
					           		jQuery('#yasr-add-field-edit-multiset').hide();
					            	return false;
							}   
					 
								var newTextBoxDiv = jQuery(document.createElement('tr'))
					 
								newTextBoxDiv.html('<td colspan="2">Element #' + counter + ' <input type="text" name="edit-multi-set-element-' + counter + '" value="" ></td>');
					 
								newTextBoxDiv.appendTo("#yasr-table-form-edit-multi-set");
					 
					 			counter++;

					 	});

					});

				<?php 

				} //End if ($n_multi_set == 1)

				if ($n_multi_set > 1) { 

				?>

				    //If more then 1 set is used...
					jQuery('#yasr-button-select-set-edit-form').on("click", function() {
						    
						    var data = {
						    	action : 'yasr_get_multi_set',
						    	set_id : jQuery('#yasr_select_edit_set').val()
						    } 
						    
						    jQuery.post(ajaxurl, data, function(response) {
						    	jQuery('#yasr-multi-set-response').show();
			     				jQuery('#yasr-multi-set-response').html(response);
			     			});

			     			return false; // prevent default click action from happening!
  	                        e.preventDefault(); // same thing as above

					});
			 
					jQuery(document).ajaxComplete(function(){

						var counter = jQuery("#yasr-edit-form-number-elements").attr('value');

				    	counter++;
			 
				    	jQuery("#yasr-add-field-edit-multiset").on('click', function() {
				 
							if(counter>9){
				           		jQuery('#yasr-element-limit').show();
				           		jQuery('#yasr-add-field-edit-multiset').hide();
				            	return false;
							}   
				 
							var newTextBoxDiv = jQuery(document.createElement('tr'))
				 
							newTextBoxDiv.html('<td colspan="2">Element #' + counter + ' <input type="text" name="edit-multi-set-element-' + counter + '" value="" ></td>');
				 
							newTextBoxDiv.appendTo("#yasr-table-form-edit-multi-set");
				 
				 			counter++;

				    	});
			 
			  		});

<?php 
		  		} //End if ($n_multi_set > 1) 

		} //end if $active_tab=='manage_multi'
		  	
?>


	//Terzo div code

		//On click show proceed button
		jQuery('#import-gdstar').on('click', function() { 
			jQuery('#yasr-import-gdstar-div').toggle();
		});

		//On click begin step1
		jQuery('#import-button').on('click', function() {

			var data = { 
				action : 'yasr_import_step1'
			};

			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#result-import').html(response);
			});

		}); //End step1

		jQuery('#result-import').on('click', '.yasr-result-step-1', function() {
			//Now we are going to prepare another ajax call to check if multiple set exists

			var data = {
				action: 'yasr_import_multi_set'
			};
				
			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#result-import').append(response);
			});

		}); //End second ajax call */

		//Reload page after importing is done
		jQuery('#result-import').on('click', '.yasr-result-step-2', function() {
			location.reload(true);
		});
 		
 </script>

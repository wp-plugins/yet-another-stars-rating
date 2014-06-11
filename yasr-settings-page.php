<?php 

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

if ( !current_user_can( 'manage_options' ) ) {
	wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ));
}

	$multi_set=yasr_get_multi_set();

	global $wpdb;

	$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the last of the last query

?>


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

	<div class="yasr-settingsdiv">
			
			<h3> <?php _e("Manage multi set"); ?></h3>

			<div class="yasr-multi-set-left">

				<button href="#" class="button-delete" id="yasr-add-new-multi-set"> <?php _e("Add new multi-set", 'yasr'); ?>  </button>

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

<?php
	} //$gd_star_rating_found && $gd_star_imported==1
?>

	<div class="yasr-space-settings-div">
	</div>

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


	//Second div code
		jQuery('#yasr-add-new-multi-set').on('click', function() {
			jQuery('.yasr-new-multi-set').toggle();
		});

		jQuery('#yasr-manage-multi-set').on('click', function() {
			jQuery('.yasr-manage-multiset').toggle();
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
			jQuery('#yasr_select_edit_set').on("change", function() {
				    
				    var data = {
				    	action : 'yasr_get_multi_set',
				    	set_id : jQuery(this).val()
				    } 
				    
				    jQuery.post(ajaxurl, data, function(response) {
				    	jQuery('#yasr-multi-set-response').show();
				    	jQuery('#yasr-multi-set-response').toggle;
	     				jQuery('#yasr-multi-set-response').html(response);
	     			});

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

	  	<?php } //End if ($n_multi_set > 1) ?>


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

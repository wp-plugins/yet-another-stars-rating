<?php


/****** Add auto insert option ******/

	add_action( 'admin_init', 'yasr_auto_insert_options_init' ); //This is for auto insert options

		function yasr_auto_insert_options_init() {
	    	register_setting(
	        	'yasr_general_options_group', // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
	        	'yasr_general_options' //The name of an option to sanitize and save.
	    	);	    	

	    	$option = get_option( 'yasr_general_options' );

	    	//To avoid undifined index, i put here the default value
	    	if (!$option) {
	    		$option = array();
	    		$option['auto_insert_enabled'] = 0;
	    		$option['auto_insert_what'] = 'overall_rating';
	    		$option['auto_insert_where'] = 'top';
	    		$option['snippet'] = 'overall_rating';
	    		$option['allowed_user'] = 'allow_anonymous';

	    		add_option("yasr_general_options", $option); //Write here the default value if there is not option
	    	} 

	    	//This is to avoid undefined offset
	    	if ($option && $option['auto_insert_enabled']==0) {
	    		$option['auto_insert_what']='overall_rating';
	    		$option['auto_insert_where']='top';
	    	}

	    	add_settings_section( 'yasr_auto_insert_section_id', __('Auto insert Settings', 'yasr'), 'yasr_section_callback', 'yasr_settings_page' );
	    		add_settings_field( 'yasr_use_auto_insert_id', __('Use auto insert?', 'yasr'), 'yasr_auto_insert_callback', 'yasr_settings_page', 'yasr_auto_insert_section_id', $option );
	    		add_settings_field( 'yasr_what_auto_insert', __('What?', 'yasr'), 'yasr_what_auto_insert_callback', 'yasr_settings_page', 'yasr_auto_insert_section_id', $option);
	       		add_settings_field( 'yasr_where_auto_insert', __('Where?', 'yasr'), 'yasr_where_auto_insert_callback', 'yasr_settings_page', 'yasr_auto_insert_section_id', $option);
	       		add_settings_field( 'yasr_allow_only_logged_in_id', __('Allow only logged in user to vote?', 'yasr'), 'yasr_allow_only_logged_in_callback', 'yasr_settings_page', 'yasr_auto_insert_section_id', $option );
	       		add_settings_field( 'yasr_choose_snippet_id', __('Which rich snippets do you want to use?', 'yasr'), 'yasr_choose_snippet_callback', 'yasr_settings_page', 'yasr_auto_insert_section_id', $option );

		}


		function yasr_section_callback() {
	    	//_e('Manage auto insert', 'yasr');
		}

		function yasr_auto_insert_callback($option) {

	    	?>

	    	<?php _e('Yes', 'yasr') ?>

	    		<input type='radio' name='yasr_general_options[auto_insert_enabled]' value='1' id='yasr_auto_insert_radio_on' <?php if ($option['auto_insert_enabled']==1) echo " checked=\"checked\" "; ?>  /> 
				&nbsp;&nbsp;&nbsp;

			<?php _e('No', 'yasr') ?>
	    		<input type='radio' name='yasr_general_options[auto_insert_enabled]' value='0' id='yasr_auto_insert_radio_off' 
	    		<?php if ($option['auto_insert_enabled']==0) {
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

	    	<input type="radio" name="yasr_general_options[auto_insert_what]" value="overall_rating" class="yasr_auto_insert_where_what_radio" <?php if ($option['auto_insert_what']==='overall_rating') echo " checked=\"checked\" "; ?> >
	    		<?php _e('Overall Rating / Author Rating', 'yasr') ?>
	   			<br />

	    	<input type="radio" name="yasr_general_options[auto_insert_what]" value="visitor_rating" class="yasr_auto_insert_where_what_radio" <?php if ($option['auto_insert_what']==='visitor_rating') echo " checked=\"checked\" "; ?> >
	    		<?php _e('Visitor Votes', 'yasr')?>
	   			<br />

	    	<input type="radio" name="yasr_general_options[auto_insert_what]" value="both" class="yasr_auto_insert_where_what_radio" <?php if ($option['auto_insert_what']==='both') echo " checked=\"checked\" "; ?> >
	    		<?php _e('Both', 'yasr')?>

	    <?php
		} //end function yasr_what_auto_insert_callback

		function yasr_where_auto_insert_callback($option) {
			?>

			<input type="radio" name="yasr_general_options[auto_insert_where]" value="top" class="yasr_auto_insert_where_what_radio" <?php if ($option['auto_insert_where']==='top' ) echo " checked=\"checked\" ";  ?> >
				<?php _e('Before the post', 'yasr')?>
				<br />

	    	<input type="radio" name="yasr_general_options[auto_insert_where]" value="bottom" class="yasr_auto_insert_where_what_radio" <?php if ($option['auto_insert_where']==='bottom') echo " checked=\"checked\" "; ?> >
	    		<?php _e('After the post', 'yasr')?>
	    		<br />


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

		} //End function yasr_choose_snippet_callback



/****** Create a form for settings page to create new multi set ******/
function yasr_display_multi_set_form() {
	?>
		
		<h4 class="yasr-multi-set-form-headers">Add New Multiple Set</h4>
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

			<div class="yasr-manage-multiset">

				<h4 class="yasr-multi-set-form-headers">Manage Multiple Set</h4>

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

				<h4 class="yasr-multi-set-form-headers">Manage Multiple Set</h4>

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



/****** Get and output multiple set in a form and table, used in settings page ******/

    add_action( 'wp_ajax_yasr_get_multi_set', 'yasr_get_multi_set_callback' );

    function yasr_get_multi_set_callback() {
        if (isset($_POST['set_id'])) {
            $set_type = $_POST['set_id'];
        }
        else {
            exit ();
        }

        global $wpdb;

        $set_name=$wpdb->get_results("SELECT field_name AS name, field_id AS id
                            FROM " . YASR_MULTI_SET_FIELDS_TABLE . "  
                            WHERE parent_set_id=$set_type 
                            ORDER BY field_id ASC");

        $i=1;

        ?>

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

        <?php

        die();

    } //End function 



/****** Validate new multi set form ******/
function yasr_process_new_multi_set_form()
{

	if ( isset( $_POST['multi-set-name']) ) {

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
   			if (mb_strlen($multi_set_name) < 3 || mb_strlen($multi_set_name_element_[1]) <3 || mb_strlen($multi_set_name_element_[2]) <3 ) {
   				$array_errors[] = "Content field must be longer then 3 chars";
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
   							_e("Something goes wrong trying insert set field name. Please report it", 'yasr');
   						}

   					} //End if $insert_multi_name_success

   					else {
   						_e("Something goes wrong trying insert multi set name. Please report it", 'yasr');
   					}

   			} //End if !$error

   		}  //End if $_POST['multi-set-name']!='' 
  		
  		//Else multi set's name and first 2 elements are empty
   		else {
   			$array_errors[] = "Multi set's name and first 2 elements can't be empty";
   			$error=TRUE;
   		}

   		if ($error) {
   			return $array_errors;
		}

    } //End if ( isset( $_POST['multi-set-name']) ) {

} //End yasr_process_new_multi_set_form() function



/****** Process Edit multi set form ******/
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

  			if ($remove_set===FALSE) {
  				$error = TRUE; 
				$array_errors[] = __("Something goes wrong trying to delete a multi-set . Please report it", 'yasr');
  			}

  			if ($remove_set_values===FALSE) {
  				$error = TRUE; 
				$array_errors[] = __("Something goes wrong trying to delete data fields for a set. Please report it", 'yasr');
			}

			if ($remove_set_votes===FALSE) {
  				$error = TRUE; 
				$array_errors[] = __("Something goes wrong trying to delete data values for a set. Please report it", 'yasr');
			}

  		}


  		for ($i = 0; $i <= 9; $i++) {

  			//Than, check if the user want to remove some field
  			if (isset($_POST["remove-element-$i"]) && !isset($_POST["yasr-remove-multi-set"]) ) {

  				$remove_field = $wpdb->delete (
  								YASR_MULTI_SET_FIELDS_TABLE,
								array(
									'parent_set_id' => $set_id,
									'field_id' =>$i
								),
								array ('%d', '%d')
							);

  				$remove_values = $wpdb->delete (
  								YASR_MULTI_SET_VALUES_TABLE,
								array(
									'set_type' => $set_id,
									'field_id' =>$i
								),
								array ('%d', '%d')
							);

  				if ($remove_field === FALSE) {
					$error = TRUE; 
					$array_errors[] = __("Something goes wrong trying to delete a multi-set element. Please report it", 'yasr');
  				}

  				if ($remove_values === FALSE) {
					$error = TRUE; 
					$array_errors[] = __("Something goes wrong trying to delete data value for an element. Please report it", 'yasr');
  				}

 
  			}  //End if isset $_POST['remove-element-$i']


  			//update the stored elements with the new ones
  			if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["yasr-remove-multi-set"]) && !isset($_POST["remove-element-$i"]) && $i <= $number_of_stored_elements ) {

  				$field_name = $_POST["edit-multi-set-element-$i"];

	  			//if elements name is shorter than 3 chars
	  			if (mb_strlen($field_name) <3 ) {
	  						$array_errors[] = __("Field # $i must be at least 3 characters", "yasr");
	   						$error=TRUE;
	  			}

  				else {

  					$insert_field_name=$wpdb->update(
							YASR_MULTI_SET_FIELDS_TABLE,

								array(
									'field_name' =>$field_name,
								),

								array(
									'parent_set_id' =>$set_id,
									'field_id' =>$i
								),

								array ('%s'),

								array ('%d', '%s', '%d')
								
							);

  					if ($insert_field_name === FALSE) {
  						$error = TRUE; 
						$array_errors[] = __("Something goes wrong trying to update a multi set element. Please report it", 'yasr');
  					}

  				}

  			} //End if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["remove-element-$i"]) && $i<=$number_of_stored_elements ) 
  				

  			//If $i > number of stored elements, user is adding new elements, so we're going to insert the new ones
  			if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["remove-element-$i"]) && $i > $number_of_stored_elements ) {

  				$field_name = $_POST["edit-multi-set-element-$i"];

  				//if elements name is shorter than 3 chars return error. I use mb_strlen($field_name) > 1
  				//because I don't wont return error if an user add an empty field. An empty field will be
  				//just ignored  
  				if (mb_strlen($field_name) > 1 && mb_strlen($field_name) < 3) {
  							$array_errors[] = __("Field # $i must be at least 3 characters", "yasr");
   							$error=TRUE;
  				}

  				//if field is not empty
  				elseif ($field_name != '') {

  					$highest_id=$wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_FIELDS_TABLE . " ORDER BY id DESC LIMIT 1 ");

  					foreach ($highest_id as $id) {
                        	$field_table_new_id=$id->id + 1;
                    }

  					$insert_set_value=$wpdb->replace(
							YASR_MULTI_SET_FIELDS_TABLE,
								array(
									'id' => $field_table_new_id,
									'parent_set_id' =>$set_id,
									'field_name' =>$field_name,
									'field_id' =>$i
								),
								array ('%d', '%d', '%s', '%d')
							);
							$field_table_new_id++; //Avoid overwrite

  					if ($insert_set_value === FALSE) {
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
			__( 'No previous Gd Star Ratings installation was found', 'yasr' );
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
						  	p.ID, p.post_author
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
							 FROM wp_gdsr_multis_values AS tabin
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

/****** Insert Gd Star Rating review in overall rating ******/
function yasr_insert_gdstar_data($votes){
	global $wpdb;

	foreach ( $votes as $column ) {
		$result=$wpdb->replace(
			YASR_VOTES_TABLE, 
			array ( 
					'reviewer_id' => $column->post_author,
					'post_id' => $column->post_id, 
				    'overall_rating' => $column->review,
				    'number_of_votes' => $column->voters,
				    'sum_votes' =>$column->sum_votes
			),
			array( '%d', '%d', '%s', '%d', '%d')
		);
	}
	return $result;
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


/****************************END IMPORT FUNCTIONS******************************/


?>
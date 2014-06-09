<?php

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

/****** Install yasr functions ******/
function yasr_install() {
	global $wpdb; //Database wordpress object

	$prefix=$wpdb->prefix . 'yasr_';  //Table prefix
	
	$yasr_votes_table=$prefix . 'votes';
	$yasr_multi_set_table=$prefix . 'multi_set';
	$yasr_multi_set_fields=$prefix . 'multi_set_fields';
	$yasr_multi_values_table=$prefix . 'multi_values';
	$yasr_log_table=$prefix . 'log';

	$sql_yasr_votes_table = "CREATE TABLE IF NOT EXISTS $yasr_votes_table (
  		id bigint(20) NOT NULL AUTO_INCREMENT,
  		post_id bigint(20) NOT NULL,
 	 	reviewer_id bigint(20) NOT NULL,
 	 	overall_rating decimal(2,1) NOT NULL,
 	 	number_of_votes bigint(20) NOT NULL,
  		sum_votes decimal(11,1) NOT NULL,
 		PRIMARY KEY  (id),
 		UNIQUE KEY post_id (post_id)	
	);";

	$sql_yasr_multi_set_table= "CREATE TABLE IF NOT EXISTS $yasr_multi_set_table (
 		set_id int(2) NOT NULL,
  		set_name varchar(64) COLLATE utf8_unicode_ci NOT NULL,
	  	UNIQUE KEY set_id (set_id),
	  	UNIQUE KEY set_name (set_name)
	)";

	$sql_yasr_multi_set_fields ="CREATE TABLE IF NOT EXISTS $yasr_multi_set_fields (
  		id bigint(20) NOT NULL,
  		parent_set_id int(2) NOT NULL,
  		field_name text COLLATE utf8_unicode_ci NOT NULL,
  		field_id int(2) NOT NULL,
  		PRIMARY KEY (id),
  		UNIQUE KEY id (id)
 	)";

	$sql_yasr_multi_value_table = "CREATE TABLE IF NOT EXISTS $yasr_multi_values_table (
  		id bigint(20) NOT NULL,
  		field_id int(2) NOT NULL,
  		set_type int (2) NOT NULL,
  		post_id bigint(20) NOT NULL,
  		votes decimal(2,1) NOT NULL,
  		PRIMARY KEY (id),
  		UNIQUE KEY id (id)
	);";

	$sql_yasr_log_table = "CREATE TABLE IF NOT EXISTS $yasr_log_table (
  		id bigint(20) NOT NULL AUTO_INCREMENT,
  		post_id bigint(20) NOT NULL,
  		multi_set_id int(2) NOT NULL,
  		user_id int(11) NOT NULL,
  		vote decimal(11,1) NOT NULL,
  		date datetime NOT NULL,
  		ip varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  		PRIMARY KEY (id),
  		UNIQUE KEY id (id)
	);";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	dbDelta( $sql_yasr_votes_table );
	dbDelta( $sql_yasr_multi_set_table );
	dbDelta( $sql_yasr_multi_set_fields );
	dbDelta( $sql_yasr_multi_value_table );
	dbDelta( $sql_yasr_log_table );

}

/****************BEGIN IMPORT FUNCTIONS*******************/

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
			_e( 'Gd Star Rating non trovato' );
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

/****************END IMPORT FUNCTIONS*******************/



/****** Get overall rating from yasr_votes table
used in yasr_add_filter_for_schema() and yasr_get_id_value_callback() ******/
function yasr_get_overall_rating() {
	global $wpdb;

	$post_id=get_the_ID();

	$result=$wpdb->get_results("SELECT overall_rating FROM " . YASR_VOTES_TABLE . " WHERE post_id=$post_id");

	if ($result) {
		foreach ($result as $rating) {
			$overall_rating=$rating->overall_rating;

			return $overall_rating;
		}
	}
}

/****** Get multi set name ******/
function yasr_get_multi_set() {
	global $wpdb;

	$result = $wpdb->get_results("SELECT * FROM " . YASR_MULTI_SET_NAME_TABLE . " ORDER BY set_id ASC");

	return $result;
} 


/****** Get multi set values and field's name, used in ajax function and shortcode function ******/
function yasr_get_multi_set_values_and_field ($post_id, $set_type) {
	global $wpdb;

	$result=$wpdb->get_results("SELECT f.field_name AS name, f.field_id AS id, v.votes AS vote 
                        FROM " . YASR_MULTI_SET_FIELDS_TABLE . " AS f, " . YASR_MULTI_SET_VALUES_TABLE . " AS v 
                        WHERE f.parent_set_id=$set_type
                        AND f.field_id = v.field_id
                        AND v.post_id = $post_id
                        AND v.set_type = $set_type
                        AND f.parent_set_id=v.set_type
                        ORDER BY f.field_id ASC");

	return $result;
}

/****** Get visitor votes ******/
function yasr_get_visitor_votes () {
	global $wpdb;

	$post_id=get_the_ID();

	$result = $wpdb->get_results("SELECT number_of_votes, sum_votes FROM " . YASR_VOTES_TABLE . " WHERE post_id=$post_id");

	return $result;
}


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
   		
  			$multi_set_name = $_POST['multi-set-name'];

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

  		for ($i = 0; $i <= 9; $i++) {

  			//First, che if the user want to remove some field
  			if (isset($_POST["remove-element-$i"])) {

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
  			if (isset($_POST["edit-multi-set-element-$i"]) && !isset($_POST["remove-element-$i"]) && $i <= $number_of_stored_elements ) {

  				$field_name = $_POST["edit-multi-set-element-$i"];

  				//if elements name is shorter than 3 chars
  				if (mb_strlen($field_name) <3 ) {
  							$array_errors[] = __("Field # $i must be at least 3 charactersssss", "yasr");
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

  				//if elements name is shorter than 3 chars
  				if (mb_strlen($field_name) < 3) {
  							$array_errors[] = __("Field # $i must be at least 3 characters", "yasr");
   							$error=TRUE;
  				}

  				else {

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

?>
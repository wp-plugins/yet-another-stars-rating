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


/****** Get visitor rating ******/
function yasr_get_vistor_rating() {
	global $wpdb;

	$post_id=get_the_ID();

	$result=$wpdb->get_results("SELECT number_of_votes, sum_votes FROM " . YASR_VOTES_TABLE . " WHERE post_id=$post_id");

	if ($result) {

		$visitor_rating = array();

		foreach ($result as $rating) {
			$visitor_rating['votes_number']=$rating->number_of_votes;
			$visitor_rating['sum']=$rating->sum_votes;

			return $visitor_rating;
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



/****** Adding log's widget to the dashboard ******/

add_action( 'plugins_loaded', 'add_action_dashboard_widget_log' ); 

	function add_action_dashboard_widget_log() {

		if ( current_user_can( 'manage_options' ) )  {
				add_action ('wp_dashboard_setup', 'yasr_add_dashboard_widget_log');
			}

	}

	function yasr_add_dashboard_widget_log () {

		wp_add_dashboard_widget (
						'yasr_widget_log_dashboard', //slug for widget
						'Recent Ratings', //widget name
						'yasr_display_dashboard_log_wiget' //function callback
						);

	}


	function yasr_display_dashboard_log_wiget () {

	
		$limit = 8; //max number of row to echo 

		global $wpdb;

		$log_result = $wpdb->get_results ("SELECT * FROM ". YASR_LOG_TABLE . " ORDER BY date DESC LIMIT 0, $limit ");

		if (!$log_result) {
            _e("No Recenet votes yet", "yasr");
        }

        else {

			echo "<div id=\"yasr-log-container\">";

			foreach ($log_result as $column) {
				
				$user = get_user_by( 'id', $column->user_id );

				//If ! user means that the vote are anonymous
				if ($user == FALSE) {

					$user = (object) array('user_login'); 
					$user->user_login = __('anonymous');

				}

				$avatar = get_avatar($column->user_id, '32');

				$title_post = get_the_title( $column->post_id );
				$link = get_permalink( $column->post_id );

				echo "
					
					<div class=\"yasr-log-div-child\">

						<div id=\"yasr-log-image\">
							$avatar
						</div>

						<div id=\"yasr-log-child-head\">
							 <span id=\"yasr-log-vote\">Vote $column->vote </span> from <strong style=\"color: blue\">$user->user_login</strong> on <span id=\"yasr-log-post\"><a href=\"$link\">$title_post</a></span>
						</div>

						<div id=\"yasr-log-ip-date\">

							<span id=\"yasr-log-ip\">" . __("Ip address" , "yasr") . ": <span style=\"color:blue\">$column->ip</span></span>

							<span id=\"yasr-log-date\">$column->date</span>

						</div>

					</div>
					
				";
				
			} //End foreach

			echo "<div id=\"yasr-log-page-navigation\">";

			$wpdb->get_results ("SELECT id FROM " . YASR_LOG_TABLE );

			$n_rows = $wpdb->num_rows;

			$num_of_pages= ceil($n_rows/$limit);

			if ($num_of_pages <= 3) {
			
				for ($i=1; $i<=$num_of_pages; $i++) {

					if ($i == 1) {
	                    echo "<button class=\"button-primary\" value=\"$i\">$i</button>&nbsp;&nbsp;";
	                }

	                else {
						echo "<button class=\"yasr-log-pagenum\" value=\"$i\">$i</button>&nbsp;&nbsp;";
					}

				}

			}

			else {

				_e("Pages", "yasr"); echo ": ($num_of_pages) &nbsp;&nbsp;&nbsp;";

				for ($i=1; $i<=3; $i++) {

					if ($i == 1) {
	                    echo "<button class=\"button-primary\" value=\"$i\">$i</button>&nbsp;&nbsp;";
	                }

	                else {
						echo "<button class=\"yasr-log-pagenum\" value=\"$i\">$i</button>&nbsp;&nbsp;";
					}

				}

				echo "...&nbsp;&nbsp;<button class=\"yasr-log-pagenum\" value=\"$num_of_pages\">Last &raquo;</button>&nbsp;&nbsp;";
			}

			echo "

			</div>

			</div>";

	}

		?>

		<script type="text/javascript">

		//Log
		jQuery('.yasr-log-pagenum').on('click', function() {

			var data = { 
				action : 'yasr_change_log_page',
				pagenum: jQuery(this).val(),

			};

			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#yasr-log-container').html(response);
			});

		});

		jQuery(document).ajaxComplete(function() {

			jQuery('.yasr-log-page-num').on('click', function() {

				var data = { 
					action : 'yasr_change_log_page',
					pagenum: jQuery(this).val(),

				};

				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#yasr-log-container').html(response);
				});

			});

		});

		</script>

		<?php

	} //End callback function

?>
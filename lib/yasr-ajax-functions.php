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

/*************************** Admin ajax functions ***********************/

/********** Functions used while wirting a new post or page ********/

/****** Get id and overall rating from post or page and write it in db, used in yasr-metabox-overall-rating******/

	add_action( 'wp_ajax_yasr_send_overall_rating', 'yasr_insert_overall_rating_callback' );

        function yasr_insert_overall_rating_callback() {

            if (isset($_POST['rating']) && ($_POST['post_id']) && $_POST['post_id'] != '') {
			    $rating = $_POST['rating'];
			    $post_id = $_POST['post_id'];
                $nonce = $_POST['nonce'];
            }

            else {
                exit();
            }

            if ( ! current_user_can( 'publish_posts' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
            }

            if ( ! wp_verify_nonce( $nonce, 'yasr_nonce_insert_overall_rating' ) ) {
                die( 'Security check' ); 
            }

            if ($rating > 5) {

                $rating = 5;

            }

        	global $wpdb;

            //If update works means that visitor already rated this post
        	$update_result=$wpdb->update(
        			YASR_VOTES_TABLE,
        			array (
        				'overall_rating' => $rating,
        				),
                    array('post_id' => $post_id),
        			array('%s')
        		);

            //If update result fails this is a new post or post has no visitor ratings
        	if(!$update_result) {

        		$replace_result=$wpdb->replace(
                    YASR_VOTES_TABLE,
                    array (
                        'post_id' => $post_id,
                        'overall_rating' => $rating,
                        'review_type' => 'Product' //default review type in a new post
                        ),
                    array('%d', '%s', '%d', '%s')
                );

                $snippet_type = yasr_get_snippet_type();

                //If there is not sinppet type, can happen when an user choose the snippet but doesn't use overall rating
                if (!$snippet_type) {

                    $wpdb->replace(
                        YASR_VOTES_TABLE,
                        array (
                            'review_type' => 'Product' //default review type in a new post
                            ),
                        array('%s')
                    );

                }

        	} // End if(!$update_result)
            
            if ($update_result || $replace_result) {

                if ($rating != '-1') { 
                    $text = __("You've rated it ", "yasr");
                    echo $text . $rating;
                }
                else {
                    $text = __("You've reset the vote", "yasr");
                    echo $text;
                }

            }

			die(); // this is required to return a proper result
		}


/****** Set the review type in yasr metabox overall rating ******/

    add_action ( 'wp_ajax_yasr_insert_review_type', 'yasr_insert_review_type_callback' );

        function yasr_insert_review_type_callback () {

            if (isset($_POST['reviewtype']) && ($_POST['postid'])) {

                $reviewtype = $_POST['reviewtype'];
                $post_id = $_POST['postid'];
                $nonce = $_POST['nonce'];

            }

            else {
                exit();
            }

            if ( ! wp_verify_nonce( $nonce, 'yasr_nonce_review_type' ) ) {
                die( 'Security check' ); 
            }

            global $wpdb;

                //If update works means that there is already a row for this post
                $review_type = $wpdb->update(
                    YASR_VOTES_TABLE,
                    array (
                        'review_type' => $reviewtype
                        ),
                    array('post_id' => $post_id),
                    array('%s'),
                    array('%d')
                );

                //if fail there is no row so make new one
                if(!$review_type) {

                    $review_type = $wpdb->replace(
                        YASR_VOTES_TABLE,
                        array (
                            'post_id' => $post_id,
                            'overall_rating' => '-1',
                            'review_type' => $reviewtype
                            ),
                        array('%s', '%s')
                    );

                }

            if($review_type) {
                _e("$reviewtype selected", "yasr");
            }
            else {
                _e("There was an error while trying to insert the review type. Please report it", "yasr");
            }

            die();

        }


/****** Get Set name from post or page and output the set, 
        used in yasr-metabox-multiple-rating******/

    add_action( 'wp_ajax_yasr_send_id_nameset', 'yasr_output_multiple_set_callback' );

        function yasr_output_multiple_set_callback() {
                if(isset($_POST['set_id']) && isset($_POST['post_id']) && $_POST['post_id'] != '' && $_POST['set_id'] != '' ) {
                    $set_type = $_POST['set_id'];
                    $post_id = $_POST['post_id'];
                }
                else {
                    exit();
                }

                if ( ! current_user_can( 'publish_posts' ) ) {
                    wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
                }

                global $wpdb;

                $set_values=yasr_get_multi_set_values_and_field ($post_id, $set_type);

                //If this is a new post or post has no multi values data
                if (!$set_values) {

                    echo "<p>";

                    _e('Choose a vote for each element', 'yasr');

                    echo "

                    <br /> <br />

                    <table class=\"yasr_table_multi_set_admin\">";
                    //Get Set fields name
                    $set_name=$wpdb->get_results($wpdb->prepare("SELECT field_name AS name, field_id AS id
                        FROM " . YASR_MULTI_SET_FIELDS_TABLE . "  
                        WHERE parent_set_id=%f 
                        ORDER BY field_id ASC",
                        $set_type));

                    foreach ($set_name as $name) {

                        //// first, I've to rate all the values to -1, or if someone is leaved empty /////
                        //// will disappear later

                        //get the highest id in table cause it is not AI
                        $highest_id=$wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_VALUES_TABLE . " ORDER BY id DESC LIMIT 1 ");
            
                        if (!$highest_id) {
                            $new_id=0;
                        }

                        foreach ($highest_id as $id) {
                           $new_id=$id->id + 1;
                        }

                        $query_success=$wpdb->replace(
                        YASR_MULTI_SET_VALUES_TABLE,
                        array (
                                'id'=>$new_id,
                                'post_id'=>$post_id,
                                'field_id'=>$name->id,
                                'votes'=>'-1',
                                'set_type'=>$set_type,
                                'number_of_votes' => '0',
                                'sum_votes' => '0'
                                ),
                        array ("%d", "%d", "%d", "%s", "%d", "%d", "%d")
                        );


                        echo "<tr> <td>";
                        echo "$name->name </td>"; 
                        echo "<td> 
                                <div class=\"rateit bigstars multi\" id=\"$name->id\" data-rateit-value=\"\"  data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-step=\"0.5\" data-rateit-resetable=\"true\" data-rateit-readonly=\"false\"></div>
                              
                                <span id=\"yasr-loader-multi-set-field-$name->id\" style=\"display:none;\" >&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\" ></span>
                              </td>
                              </tr>";

                    
                    } //End foreach

                    echo "</table>

                    </p>";

                    echo "<p>";

                    _e("If you want to insert this multiset, paste this shortcode ", "yasr"); 
                    echo "<strong> [yasr_multiset setid=$set_type] </strong> <br />"; 
                    _e("If, instead, you want allow your visitor to vote on this multiset, use this shortcode", "yasr");
                    echo "<strong> [yasr_visitor_multiset setid=$set_type] </strong>. In this case, you don't need to vote here <br />"; 

                    echo "</p>";

                } //

                //else means that post already has vote and here I show it
                else {
                    _e('Choose a vote for every element', 'yasr');

                    echo "<table class=\"yasr_table_multi_set_admin\">";

                    foreach ($set_values as $set_content) {

                        echo "<tr><td width=\"50%\">$set_content->name </td>";

                        $integer_vote = floor($set_content->vote);
                        if($set_content->vote < ($integer_vote+0.3)) {
                            $set_content->vote = $integer_vote;
                        }
                        elseif ($set_content->vote >= ($integer_vote+0.3) AND $set_content->vote < ($integer_vote+0.7)) {
                            $set_content->vote = $integer_vote+0.5;
                        }
                        elseif ($set_content->vote >= ($integer_vote+0.7)) {
                            $set_content->vote = $integer_vote+1;
                        }

                        echo "<td width=\"50%\"> 
                                <div class=\"rateit bigstars multi\" id=\"$set_content->id\"  data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$set_content->vote\" data-rateit-step=\"0.5\" data-rateit-resetable=\"true\" data-rateit-readonly=\"false\"></div> 

                                <span id=\"yasr-loader-multi-set-field-$set_content->id\" style=\"display:none;\" >&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\"></span>
                              </td>
                            </tr>";


                    } //End foreach

                    echo "</table>";

                    echo "<p>";

                    _e("If you want to insert this multiset, paste this shortcode ", "yasr"); 
                    echo "<strong> [yasr_multiset setid=$set_type] </strong> <br />"; 
                    _e("If, instead, you want allow your visitor to vote on this multiset, use this shortcode", "yasr");
                    echo "<strong> [yasr_visitor_multiset setid=$set_type] </strong>. In this case, you don't need to vote here <br />"; 

                    echo "</p>";
                }

                die();
        }


/****** Get multiple value and insert into database, used in yasr-metabox-multiple-rating ******/

    add_action( 'wp_ajax_yasr_send_id_field_with_vote', 'yasr_get_multiple_votes_callback' );

        function yasr_get_multiple_votes_callback() {

            if (isset($_POST['post_id']) && isset($_POST['rating']) && isset($_POST['id_field']) && isset($_POST['set_type'])) {
                $post_id = $_POST['post_id'];
                $vote = $_POST['rating'];
                $id_field = $_POST['id_field'];
                $set_type = $_POST['set_type'];
                $nonce = $_POST['nonce'];

                if ($post_id == '' || $set_type == '' || $id_field == '') {
                    exit();
                }

            }
            else {
                exit();
            }

            if ( ! current_user_can( 'publish_posts' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
            }

            if ( ! wp_verify_nonce( $nonce, 'yasr_nonce_insert_multi_rating' ) ) {
                die( 'Security check' ); 
            }

            global $wpdb;

            //Check if vote already exist
            $vote_already_exist=$wpdb->get_results($wpdb->prepare("SELECT id FROM " . YASR_MULTI_SET_VALUES_TABLE . " 
                    WHERE post_id = %d
                    AND set_type = %d
                    AND field_id = %d
                    ", 
                    $post_id, $set_type, $id_field));

            //If vote already exist, overwrite it
            if ($vote_already_exist) {
                foreach ($vote_already_exist as $index_id) {
                        $id = $index_id->id;
                }       
                $query_success=$wpdb->update(
                        YASR_MULTI_SET_VALUES_TABLE,
                        array (
                                'id'=>$id,
                                'post_id'=>$post_id,
                                'field_id'=>$id_field,
                                'votes'=>$vote,
                                'set_type'=>$set_type
                                ),
                        array (
                                'id'=>$id
                            ),
                        array ("%d", "%d", "%d", "%f", "%d"),
                        array ("%d")
                );

                if($query_success) {
                    echo $vote;
                }
            } //End if vote already exist

            //If vote doesn't exist create a new one
            else {

                //get the highest id in table
                $highest_id=$wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_VALUES_TABLE . " ORDER BY id DESC LIMIT 1 ");
        
                if (!$highest_id) {
                    $new_id=0;
                }

                foreach ($highest_id as $id) {
                    $new_id=$id->id + 1;
                }

                $result=$wpdb->replace(
                        YASR_MULTI_SET_VALUES_TABLE,
                        array (
                        'id' => $new_id,
                        'post_id'=>$post_id,
                        'field_id'=>$id_field,
                        'votes'=>$vote,
                        'set_type'=>$set_type
                        ),
                        array ("%d", "%d", "%d", "%f", "%d")
                );

                if($result) {
                    echo $vote;
                }

            } //End else

            die();
                
        } //End callback function



/****** Create the content for the button shortcode in Tinymce ******/

    //Add ajax action that will be called from the .js for button in tinymce
    add_action('wp_ajax_yasr_create_shortcode', 'wp_ajax_yasr_create_shortcode_callback');

    function wp_ajax_yasr_create_shortcode_callback() {
        if (isset($_POST['action'])) {
            $action=$_POST['action'];
        }
        else {
            exit();
        }

        global $wpdb;

        $multi_set=yasr_get_multi_set();

        $n_multi_set=$wpdb->num_rows;
        
        ?>

            <div id="yasr-tinypopup-form">

                <h2 class="nav-tab-wrapper yasr-underline">
                    <a href="#" id="yasr-link-tab-main" class="nav-tab nav-tab-active"><?php _e("Main", "yasr"); ?></a>
                    <a href="#" id="yasr-link-tab-charts" class="nav-tab"><?php _e("Charts" , "yasr"); ?></a>

                    <a href="https://yetanotherstarsrating.com/f-a-q/" target="_blank" id="yasr-tinypopup-link-doc"><?php _e("Read the doc", "yasr"); ?></a>

                </h2>

                <div id="yasr-content-tab-main">

                    <table id="yasr-table-tiny-popup-main" class="form-table">

                        <tr>
                            <th><label for="yasr-overall"><?php _e("Overall Rating / Review", "yasr"); ?></label></th>
                            <td>
                                <input type="button" class="button-primary" id="yasr-overall" name="yasr-overall" value="<?php _e("Insert Overall Rating", "yasr"); ?>" /><br />
                                <small><?php _e("Insert Overall Rating / Review for this post", "yasr"); ?></small>

                                <div id="yasr-overall-choose-size">
                                    <small><?php _e("Choose Size", "yasr"); ?><small>
                                    <div class="yasr-tinymce-button-size">
                                        <input type="button" class="button-secondary" id="yasr-overall-insert-small" name="yasr-overall-insert-small" value="<?php _e("Small", "yasr"); ?>" />
                                        <input type="button" class="button-secondary" id="yasr-overall-insert-medium" name="yasr-overall-insert-medium" value="<?php _e("Medium", "yasr"); ?>" />
                                        <input type="button" class="button-secondary" id="yasr-overall-insert-large" name="yasr-overall-insert-large" value="<?php _e("Large", "yasr"); ?>" />
                                    </div>
                                </div>

                            </td>
                        </tr>

                        <tr>
                            <th><label for="yasr-id"><?php _e("Visitor Votes", "yasr"); ?></label></th>
                            <td>
                                <input type="button" class="button-primary" name="yasr-visitor-votes" id="yasr-visitor-votes" value="<?php _e("Insert Visitor Votes", "yasr"); ?>" /><br />
                                <small><?php _e("Insert the ability for your visitor to vote", "yasr"); ?></small>

                                <div id="yasr-visitor-choose-size">
                                    <small><?php _e("Choose Size", "yasr"); ?><small>
                                    <div class="yasr-tinymce-button-size">
                                        <input type="button" class="button-secondary" id="yasr-visitor-insert-small" name="yasr-visitor-insert-small" value="<?php _e("Small", "yasr"); ?>" />
                                        <input type="button" class="button-secondary" id="yasr-visitor-insert-medium" name="yasr-visitor-insert-medium" value="<?php _e("Medium", "yasr"); ?>" />
                                        <input type="button" class="button-secondary" id="yasr-visitor-insert-large" name="yasr-visitor-insert-large" value="<?php _e("Large", "yasr"); ?>" />
                                    </div>
                                </div>

                            </td>
                        </tr>

                        <?php if ($n_multi_set>1) { //If multiple Set are found ?>

                            <tr>
                                <th><label for="yasr-size"><?php _e("If you want to insert a Multi Set, pick one:", "yasr"); ?></label></th>
                                <td>
                                    <?php foreach ($multi_set as $name) { ?>
                                        <input type="radio" value="<?php echo $name->set_id ?>" name="yasr_tinymce_pick_set" class="yasr_tinymce_select_set"><?php echo $name->set_name ?>
                                        <br />
                                    <?php } //End foreach ?>
                                    <small><?php _e("Choose wich set you want to insert.", "yasr"); ?></small>
                                    <p>
                                        <input type="checkbox" id="yasr-allow-vote-multiset"><?php _e("Readonly?", "yasr"); ?><br />
                                    </p>
                                    <small><?php _e("If Readonly is checked, only you can insert the votes (in the box above the editor)", "yasr"); ?></small>
                                    <input type="button" class="button-primary" name="yasr-insert-multiset" id="yasr-insert-multiset-select" value="<?php _e("Insert Multi Set", "yasr") ?>" /><br />

                                </td>
                            </tr>

                        <?php } //End if

                        elseif ($n_multi_set==1) { ?>
                            <tr>
                                <th><label for="yasr-size"><?php _e("Insert Multiset:", "yasr"); ?></label></th>
                                <td>
                                    <?php foreach ($multi_set as $name) { ?>
                                        <button type="button" class="button-primary" id="yasr-single-set" name="yasr-single-set" value="<?php echo $name->set_id ?>" ><?php _e("Insert Multiple Set", "yasr"); ?></button>
                                        &nbsp;&nbsp;&nbsp;
                                        <input type="checkbox" id="yasr-allow-vote-multiset"><?php _e("Readonly?", "yasr"); ?><br />
                                        <small><?php _e("If Readonly is checked, only you can insert the votes (in the box above the editor)", "yasr"); ?></small>
                                    <?php } //End foreach ?>
                                </td>
                            </tr>
                        <?php 
                        }
                        //End elseif ?>
                    </table>

                </div>

                <div id="yasr-content-tab-charts" style="display:none">

                    <table id="yasr-table-tiny-popup-charts" class="form-table">
                        <tr>
                            <th><label for="yasr-10-overall"><?php _e("Ranking reviews", "yasr"); ?></label></th>
                            <td><input type="button" class="button-primary" name="yasr-top-10-overall-rating" id="yasr-top-10-overall-rating" value="<?php _e("Insert Ranking reviews", "yasr") ?>" /><br />
                            <small><?php _e("Insert Top 10 ranking for [yasr_overall_rating] shortcode", "yasr"); ?></small></td>
                        </tr>

                        <tr>
                            <th><label for="yasr-10-highest-most-rated"><?php _e("Users' ranking", "yasr"); ?></label></th>
                            <td><input type="button" class="button-primary" name="yasr-10-highest-most-rated" id="yasr-10-highest-most-rated" value="<?php _e("Insert Users ranking", "yasr") ?>" /><br />
                            <small><?php _e("Insert Top 10 ranking for [yasr_visitor_votes] shortcode", "yasr"); ?></small></td>
                        </tr>

                        <tr>
                            <th><label for="yasr-5-active-reviewers"><?php _e("Most active reviewers", "yasr"); ?></label></th>
                            <td><input type="button" class="button-primary" name="yasr-5-active-reviewers" id="yasr-5-active-reviewers" value="<?php _e("Insert Most Active Reviewers", "yasr")?> " /><br />
                            <small><?php _e("Insert Top 5 active reviewers", "yasr"); ?></small></td>
                        </tr>

                        <tr>
                            <th><label for="yasr-10-active-users"><?php _e("Most Active Users", "yasr"); ?></label></th>
                            <td><input type="button" class="button-primary" name="yasr-top-10-active-users" id="yasr-top-10-active-users" value="<?php _e("Insert Most Active Users", "yasr") ?>" /><br />
                            <small><?php _e("Insert Top 10 voters [yasr_visitor_votes] shortcode", "yasr"); ?></small></td>
                        </tr>

                    </table>

                </div>

            </div>

            <script type="text/javascript">

                jQuery( document ).ready(function() {

                    var nMultiSet = <?php echo (json_encode("$n_multi_set")); ?>
                    
                    yasrShortcodeCreator(nMultiSet);

                });

            </script>

        <?php

        die();

    } //End callback function 

/********** END Functions used while wirting a new post or page ********/



/********* IMPORT FUNCTIONS *********/

    add_action( 'plugins_loaded', 'add_action_import_gdstar_1' ); 

        function add_action_import_gdstar_1() {
            if ( current_user_can( 'manage_options' ) )  {
                    add_action( 'wp_ajax_yasr_import_step1', 'yasr_import_step1_callback' );
                }
        }

        function yasr_import_step1_callback () {

            //Since version 0.05 for pro and 0.6.5 for lite
            $gdsr_logs_imported = get_option('yasr-gdstar-logs-imported');

            if (!$gdsr_logs_imported) {

                //Import logs from GD star
                $logs = yasr_import_gdstar_logs();

                //Insert logs if exists
                $insert_logs = yasr_insert_gdstar_logs($logs);

                if ($insert_logs) {
                    update_option('yasr-gdstar-logs-imported', '1');
                }

            }

            //Import reviews from GD star 
            $reviews=yasr_import_gdstar_data();

            //Insert GD star review in yasr votes table
            $check_query_success = yasr_insert_gdstar_data($reviews);

            ?>

            <div class="yasr-result-step-1">
                <?php
                if ($check_query_success) {  
                    _e( "Reviews and Visitor Votes have been successfull imported.", 'yasr');

                    update_option('yasr-gdstar-imported', '1');

                    ?>
                    <br />
                    <?php _e ("Step2: I will check if you used Multiple Sets and if so I will import them. THIS MAY TAKE A WHILE!", 'yasr'); ?>
                    <br />
                        <button href="#" class="button-primary" id="import-button-step2"> <?php _e('Proceed Step 2', 'yasr');?> </button>
                        <span id="yasr-loader-importer2" style="display:none;" >&nbsp;<img src="<?php echo YASR_IMG_DIR . "loader.gif" ?>">
                        </span>
                    <?php
                }

                else {
                    _e( "Something goes wrong! Refresh the page and try again!", 'yasr');
                }

            ?>

            </div>

            <?php

            die ();
        } //End import step 1


    add_action( 'plugins_loaded', 'add_action_import_gdstar_2' ); 

        function add_action_import_gdstar_2() {
            if ( current_user_can( 'manage_options' ) )  {
                add_action( 'wp_ajax_yasr_import_multi_set', 'yasr_check_import_set_callback' );
                }
        }

        function yasr_check_import_set_callback () {
            $multi_set_names=yasr_import_gdstar_multi_set();

            echo "<div class=\"yasr-result-step-2\">";

            //If multiple set are found
            if($multi_set_names) {
                echo "<br /><strong>";
                _e("I've found Multiple Set! Importing..." , 'yasr');
                echo "</strong><br />";

                //If multi set are found write in yasr_multi_set table
                $insert_multi_set=yasr_insert_gdstar_multi_set($multi_set_names);

                //If insert succes, go ahed
                if ($insert_multi_set) {
                    echo "&nbsp;&nbsp;&nbsp;";
                    _e("Multi Set's name has been successfull imported.", 'yasr');
                    echo "<br /><strong>"; 
                    _e("Now I'm going to import Multi Set data", 'yasr');
                    echo "</strong> <br />";

                    //Import multiple set's values from GD star rating
                    $multi_data=yasr_import_gdstar_multi_value();

                    //If set values are found, insert Gd Star multi values in yasr_multi_values  
                    if($multi_data) {
                        $insert_multidata=yasr_insert_gdstar_multi_value($multi_data);
                        if ($insert_multidata) {
                            echo "&nbsp;&nbsp;&nbsp;";
                            _e( "All votes has been successfull imported.", 'yasr'); 
                            echo "<br />";
                            echo "<button href=\"#\" class=\"button-delete\" id=\"end-import\">" . __('Done', 'yasr') . "</button>";

                        }
                        else {
                            echo "&nbsp;&nbsp;&nbsp;";
                            _e("I've found Multiple Set's votes but I couldn't insert into db", 'yasr');
                            echo  "<br />";
                        }
                    } //End if $multi_data 

                    //Multiple set are found, but there is not data
                    else { 
                        echo "&nbsp;&nbsp;&nbsp;";
                        _e( "I've found Multi Set but with no data", 'yasr'); 
                        echo "<br />";
                    }

                } //End if $insert_multi_set

                //Query failed insert set name 
                else {
                    echo "&nbsp;&nbsp;&nbsp;";
                    _e("I've found Multi Sets names but I couldn't insert into db", 'yasr');
                    echo "<br />";
                }
            
            } //End if $multi_set_names

            else {
                echo "&nbsp;&nbsp;&nbsp;";
                _e ("Multisets were not found. Imported is done!", 'yasr');
            }

            echo "</div>";

            die ();

        } //End function




/****** Display recent votes on dashboard, called from function yasr_display_dashboard_log_wiget,
        declared on yasr-db-function  ******/


    add_action( 'wp_ajax_yasr_change_log_page', 'yasr_change_log_page_callback' );

    function yasr_change_log_page_callback () {

        if (isset($_POST['pagenum'])) {

            $page_num = $_POST['pagenum'];

        }

        else {
            $page_num = 1;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
        }

        $limit = 8; //max number of row to echo 

        $offset = ( $page_num - 1 ) * $limit;

        global $wpdb;

        $log_result = $wpdb->get_results ("SELECT * FROM ". YASR_LOG_TABLE . " ORDER BY date DESC LIMIT $offset, $limit ");

        if (!$log_result) {
            _e("No Recenet votes yet", "yasr");
        }

        else {

            foreach ($log_result as $column) {
                
                $user = get_user_by( 'id', $column->user_id ); //Get info user from user id

                //If ! user means that the vote are anonymous
                if ($user == FALSE) {

                    $user = (object) array('user_login'); 
                    $user->user_login = __('anonymous');

                }

                $avatar = get_avatar($column->user_id, '32'); //Get avatar from user id

                $title_post = get_the_title( $column->post_id ); //Get post title from post id
                $link = get_permalink( $column->post_id ); //Get post link from post id

                $yasr_log_vote_text = sprintf(__('Vote %d from %s on', 'yasr'), $column->vote, '<strong style="color: blue">'.$user->user_login.'</strong>' ); 

                echo "
                    
                    <div class=\"yasr-log-div-child\">

                        <div id=\"yasr-log-image\">
                            $avatar
                        </div>

                        <div id=\"yasr-log-child-head\">
                             <span id=\"yasr-log-vote\">$yasr_log_vote_text<span id=\"yasr-log-post\"><a href=\"$link\"> $title_post</a></span>
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

            $n_rows = $wpdb->num_rows; //Number of rows in YASR LOG TABLE

            $num_of_pages = ceil($n_rows/$limit); //Number of page

            if ($num_of_pages <= 3) {
            
                for ($i=1; $i<=$num_of_pages; $i++) {

                    if ($i == $page_num) {
                        echo "<button class=\"button-primary\" value=\"$i\">$i</button>&nbsp;&nbsp;";
                    }

                    else {
                        echo "<button class=\"yasr-log-page-num\" value=\"$i\">$i</button>&nbsp;&nbsp;";

                    }
                    
                }

                echo "<span id=\"yasr-loader-log-metabox\" style=\"display:none;\">&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\" ></span>";

            }

            else {

                _e("Pages", "yasr"); echo ": ($num_of_pages) &nbsp;&nbsp;&nbsp;";

                $start_for = $page_num - 1;

                    if ($start_for <= 0) {
                        $start_for = 1;
                    }

                $end_for = $page_num + 1;

                    if ($end_for >= $num_of_pages) {
                        $end_for = $num_of_pages;
                    }

                if ($page_num >= 3) {
                    echo "<button class=\"yasr-log-page-num\" value=\"1\">&laquo; First </button>&nbsp;&nbsp;...&nbsp;&nbsp;";
                }

                for ($i=$start_for; $i<=$end_for; $i++) {

                    if ($i == $page_num) {
                        echo "<button class=\"button-primary\" value=\"$i\">$i</button>&nbsp;&nbsp;";
                    }

                    else {
                        echo "<button class=\"yasr-log-page-num\" value=\"$i\">$i</button>&nbsp;&nbsp;";
                    }

                }

                $num_of_page_less_one =  $num_of_pages-1;

                if ($page_num != $num_of_pages && $page_num != $num_of_page_less_one) {
                    echo "...&nbsp;&nbsp;<button class=\"yasr-log-page-num\" value=\"$num_of_pages\">Last &raquo;</button>&nbsp;&nbsp;";
                }

                echo "<span id=\"yasr-loader-log-metabox\" style=\"display:none;\" >&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\" ></span>";

            }

            echo "

            </div>

            </div>";

    } // End else if !$log result

        die();

    }



/****** Ajax function called to hide the ask rating mateabox ******/

    add_action( 'wp_ajax_yasr_hide_ask_rating_metabox', 'yasr_hide_ask_rating_metabox' );

    function yasr_hide_ask_rating_metabox () {

        if (isset($_POST['nonce']) && isset($_POST['choose']) ) {

            $choose = $_POST['choose'];
            $nonce = $_POST['nonce'];

        }

        else {

            exit();

        }

        if ( ! wp_verify_nonce($nonce, 'yasr_nonce_hide_ask_rating')) {

            die( 'Security check' );

        }

        if ($choose == 'hide') {

            //set transient for a week
            set_site_transient( 'yasr_hide_ask_rating', 'hide', DAY_IN_SECONDS * 7 );

        }

        elseif ($choose == 'close') {

            //it will not close forever, but for 1 year
            set_site_transient( 'yasr_hide_ask_rating', 'close', YEAR_IN_SECONDS );

        }

        die();

    }



/********************* END Admin ajax functions ****************/


/********************* NON Admin ajax functions ****************/

/****** Yasr insert visitor votes, called from yasr-shortcode-function ******/
    
    add_action( 'wp_ajax_yasr_send_visitor_rating', 'yasr_insert_visitor_votes_callback' );
    add_action( 'wp_ajax_nopriv_yasr_send_visitor_rating', 'yasr_insert_visitor_votes_callback' );

    function yasr_insert_visitor_votes_callback () {

        if(isset($_POST['rating']) && isset($_POST['post_id']) && isset($_POST['size']) && isset($_POST['nonce_visitor'])) {
            $rating = $_POST['rating'];
            $post_id = $_POST['post_id'];
            $size = $_POST['size'];
            $nonce_visitor = $_POST['nonce_visitor'];

            if($post_id == '') {

                exit();

            }

        }

        else {
            exit();
        }

        yasr_wp_super_cache_support($post_id);

        yasr_wp_rocket_support($post_id);

        if ( ! wp_verify_nonce( $nonce_visitor, 'yasr_nonce_insert_visitor_rating' ) ) {
                die( 'Security check' ); 
            }

        $row_exists_result=NULL; //Avoid Undefined variable notice
        $new_row_result=NULL; ////Avoid Undefined variable notice

        if ($rating < 1) {
            _e("Error: you can't vote 0", "yasr");
            die();
        }

        elseif ($rating > 5 ) {
            $rating = 5;
        }

        if ($size == 'small') {
            $rateit_class='rateit';
            $px_size = '16';
        }

        elseif ($size == 'medium') {
            $rateit_class = 'rateit medium';
            $px_size = '24';
        }

        //default values
        else {
            $rateit_class = 'rateit bigstars';
            $px_size = '32';
        }

        global $wpdb;

        $row_exists = $wpdb->get_results($wpdb->prepare("SELECT number_of_votes, sum_votes FROM " . YASR_VOTES_TABLE . "
                                        WHERE post_id=%d",
                                        $post_id));

        //If post already has vote, find where it is and sum it
        if ($row_exists) {
            foreach ($row_exists as $user_votes) {
                $number_of_votes = $user_votes->number_of_votes;
                $user_votes_sum = $user_votes->sum_votes;
            }

            $number_of_votes=$number_of_votes+1;
            $user_votes_sum=$user_votes_sum+$rating;

            $row_exists_result=$wpdb->update(
                YASR_VOTES_TABLE,
                array (
                    'number_of_votes' => $number_of_votes,
                    'sum_votes' => $user_votes_sum,
                    ),
                array (
                    'post_id' => $post_id
                    ),
                array('%d', '%s' ),
                array( '%d' ) 
            );
            
        } //End if row_exists

        else {

            $number_of_votes = 1;

            $new_row_result=$wpdb->replace (
                YASR_VOTES_TABLE,
                array (
                    'post_id' => $post_id,
                    'number_of_votes' => $number_of_votes,
                    'overall_rating' => '-1',
                    'sum_votes' => $rating
                    ),
                array ('%d', "%d", "%s", "%s")
                );
        }


        //If one of this is true update the log table
        if ($row_exists_result || $new_row_result ) {

            $cookiename = "yasr_visitor_vote_" . $post_id;

            yasr_setcookie($cookiename, $rating);

            global $current_user;
            get_currentuserinfo();

            $ip_adress = yasr_get_ip();

            $result_insert_log = $wpdb->replace (
                YASR_LOG_TABLE,
                array (
                    'post_id' => $post_id,
                    'multi_set_id' => -1,
                    'user_id' => $current_user->ID,
                    'vote' => $rating,
                    'date' => date('Y-m-d H:i:s'),
                    'ip' => $ip_adress
                    ), 
                array ('%d', '%d', '%d', '%s', '%s', '%s')
                );
        }


        if($row_exists_result) {

            $total_rating = ($user_votes_sum / $number_of_votes);
            $medium_rating = round ($total_rating, 1);

            echo "<div class=\"$rateit_class\" id=\"yasr_rateit_user_votes_voted\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$total_rating\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
            <span class=\"yasr-total-average-text\" title=\"yasr-stats\"> [" . __("Total: ", "yasr") . "$number_of_votes &nbsp; &nbsp;" .  __("Average:", "yasr") . " $medium_rating/5 ]</span>
            <span class=\"yasr-small-block-bold\" id=\"yasr-vote-saved\">" . __("Vote Saved" , "yasr") . "</span>";

        }

        elseif ($new_row_result) {

            echo "<div class=\"$rateit_class\" id=\"yasr_rateit_user_votes_voted\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$rating\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
            <span class=\"yasr-total-average-text\" title=\"yasr-stats\"> [" . __("Total: ", "yasr") . "$number_of_votes &nbsp; &nbsp;" .  __("Average:", "yasr") . " $rating/5 ]</span>
            <span class=\"yasr-small-block-bold\" id=\"yasr-vote-saved\">" . __("Vote Saved" , "yasr") . "</span>";
        
        }

        die(); // this is required to return a proper result
    }


/****** Update vote for logged in user ******/
    
    add_action( 'wp_ajax_yasr_update_visitor_rating', 'yasr_update_visitor_rating_callback' );
    add_action( 'wp_ajax_nopriv_yasr_update_visitor_rating', 'yasr_update_visitor_rating_callback' );

    function yasr_update_visitor_rating_callback () {
        if(isset($_POST['rating']) && isset($_POST['post_id']) && isset($_POST['size']) && isset($_POST['nonce_visitor']) )  {
            $new_rating = $_POST['rating'];
            $post_id = $_POST['post_id'];
            $size = $_POST['size'];
            $nonce_visitor = $_POST['nonce_visitor'];

            if($post_id == '') {

                exit();

            }

        }
        
        else {
            exit();
        }

        yasr_wp_super_cache_support($post_id);

        yasr_wp_rocket_support($post_id);

        if ( ! wp_verify_nonce( $nonce_visitor, 'yasr_nonce_insert_visitor_rating' ) ) {
                die( 'Security check' ); 
            }

        if ($new_rating < 1) {
            _e("Error: you can't vote 0", "yasr");
            die();
        }

        elseif ($new_rating > 5 ) {
            $rating = 5;
        }

        if ($size == 'small') {
            $rateit_class = 'rateit';
            $px_size = '16';
        }

        elseif ($size == 'medium') {
            $rateit_class = 'rateit medium';
            $px_size = '24';
        }

        //default values
        else {
            $rateit_class = 'rateit bigstars';
            $px_size = '32';
        }


        global $wpdb;

        $all_post_votes = $wpdb->get_results($wpdb->prepare("SELECT sum_votes, number_of_votes FROM " . YASR_VOTES_TABLE . " WHERE post_id=%d", $post_id));

        global $current_user;
        get_currentuserinfo();

        $previous_vote = $wpdb->get_results($wpdb->prepare("SELECT vote FROM " . YASR_LOG_TABLE . " WHERE user_id=%d AND post_id=%d", $current_user->ID, $post_id));


        foreach ($all_post_votes as $votes) {
            $old_votes_sum = $votes->sum_votes;
            $number_of_votes = $votes->number_of_votes;
        }

        //Avoid division by 0. This should never happen, just to be safe, check this post
        //http://wordpress.org/support/topic/warning-division-by-zero-in-4?replies=2
        if ($number_of_votes < 1) {
            $number_of_votes = 1;
        }

        foreach ($previous_vote as $vote) {
            $old_vote = $vote->vote;
        }

        //Calculate the new sum: get the old sum and subtract the old vote
        $new_sum = $old_votes_sum - $old_vote;

        //Then add the new vote
        $new_sum = $new_sum + $new_rating;

        //Write the new sum in the db
        $update_vote=$wpdb->update(
                YASR_VOTES_TABLE,
                array (
                    'sum_votes' => $new_sum
                    ),
                array (
                    'post_id' => $post_id
                    ),
                array( '%s' ),
                array( '%d' ) 
            );


        //Update the log table

        $update_log = $wpdb->update (
            YASR_LOG_TABLE,
            array (
                'vote' => $new_rating
                ),
            array (
                'post_id' => $post_id,
                'user_id' => $current_user->ID
                ),
            array( '%s' ),
            array( '%d', '%d' )
            );


        $total_rating = ($new_sum / $number_of_votes);
        $medium_rating=round ($total_rating, 1);


        echo "<div class=\"$rateit_class\" id=\"yasr-rateit-user-votes-updated\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$total_rating\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>";

        if (YASR_VISITORS_STATS === 'yes') {
            echo "<span class=\"yasr-total-average-text\"><span class=\"dashicons dashicons-chart-bar\" id=\"yasr-total-average-$post_id\"></span>[" . __("Total: ", "yasr") . "$number_of_votes &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>";
        }
        
        echo "<span class=\"yasr-small-block-bold\" id=\"yasr-already-voted-text\">" . __("Vote updated", "yasr") . "</span>";

        die(); // this is required to return a proper result

    }


/****** Get Multiple value from visitor and insert into db, used in yasr-shortcode-functions ******/

    add_action( 'wp_ajax_yasr_visitor_multiset_field_vote', 'yasr_visitor_multiset_field_vote_callback' );
    add_action ('wp_ajax_nopriv_yasr_visitor_multiset_field_vote', 'yasr_visitor_multiset_field_vote_callback');

    function yasr_visitor_multiset_field_vote_callback() {

        if (isset($_POST['post_id']) && isset($_POST['rating']) && isset($_POST['set_type'])) {
            $post_id = $_POST['post_id'];
            $rating = $_POST['rating'];
            $set_type = $_POST['set_type'];
            $nonce = $_POST['nonce'];

            if ($post_id == '' || $set_type == '') {
                exit("Missing post id or set type");
            }

            if ($rating == "") {
                exit("You must insert at least a rating"); 
            }

        }
        else {
            exit();
        }

        if ( ! wp_verify_nonce( $nonce, 'yasr_nonce_insert_visitor_rating_multiset' ) ) {
            die( 'Security check' ); 
        }

        global $wpdb;

        $array_error = array();

        foreach ($rating as $rating_values) {
            
            $id_field = $rating_values['field'];
            $rating = $rating_values['rating'];

            //Find the existing votes
            $existing_vote=$wpdb->get_results($wpdb->prepare("SELECT number_of_votes, sum_votes FROM " . YASR_MULTI_SET_VALUES_TABLE . " 
            WHERE post_id = %d
            AND set_type = %d
            AND field_id = %d
            ", 
            $post_id, $set_type, $id_field));

            if(!empty($existing_vote)) {

                foreach ($existing_vote as $user_votes) {
                    $number_of_votes = $user_votes->number_of_votes;
                    $user_votes_sum = $user_votes->sum_votes;
                }

                $number_of_votes=$number_of_votes+1;
                $user_votes_sum=$user_votes_sum+$rating;

                $query_success=$wpdb->update(
                    YASR_MULTI_SET_VALUES_TABLE,
                    array (
                        'number_of_votes' => $number_of_votes,
                        'sum_votes' => $user_votes_sum,
                        ),
                    array (
                        'post_id' => $post_id,
                        'field_id' => $id_field,
                        'set_type' => $set_type
                        ),
                    array('%d', '%s' ),
                    array( '%d', '%d', '%d' ) 
                );

            }

            else {

                //Find the highest_id (it's not auto increment on  db due to gd star compatibility)
                $highest_id=$wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_VALUES_TABLE . " ORDER BY id DESC LIMIT 1 ");
            
                //highest id is 0 in data is empty
                if (!$highest_id) {
                    $new_id=0;
                }

                //or is n+1
                foreach ($highest_id as $id) {
                   $new_id=$id->id + 1;
                }


                $query_success=$wpdb->replace(
                YASR_MULTI_SET_VALUES_TABLE,
                array (
                        'id'=>$new_id,
                        'post_id'=>$post_id,
                        'field_id'=>$id_field,
                        'set_type'=>$set_type,
                        'number_of_votes' => 1,
                        'sum_votes' => $rating
                        ),
                array ("%d", "%d", "%d",  "%d", "%d", "%d")
                );

            }


            if ($query_success) {

                $array_error[] = 0; 

            }

            else {

                $array_error[] = 1;

            }

        } //End foreach ($rating as $rating_values)

        $error_found = FALSE;

        foreach ($array_error as $error) {
            
            if ($error === 1) {

                $error_found = TRUE;

            }

        }

        if(!$error_found) {

            $cookiename = 'yasr_multi_visitor_cookie_' . $post_id . '_' . $set_type ;

            yasr_setcookie($cookiename, 'true');

            _e('Rating saved!', 'yasr');

        }

        else {

            _e('Rating not saved. Please Try again', 'yasr');

        }

        die();
            
    } //End callback function


    add_action( 'wp_ajax_yasr_stats_visitors_votes', 'yasr_stats_visitors_votes_callback' );
    add_action( 'wp_ajax_nopriv_yasr_stats_visitors_votes', 'yasr_stats_visitors_votes_callback' );

    function yasr_stats_visitors_votes_callback () {

        if(isset($_POST['post_id']) && $_POST['post_id'] != '' ) {
            $post_id = $_POST['post_id'];
        }

        else {
            exit();
        }

        $missing_vote = NULL; //avoid undefined variable

        global $wpdb;

        $stats = $wpdb->get_results( $wpdb->prepare ("SELECT ROUND( vote, 0 ) as vote, COUNT( vote ) AS n_of_votes
                            FROM ". YASR_LOG_TABLE . " 
                            WHERE post_id=%d
                            GROUP BY vote
                            ORDER BY vote DESC
                            ",
                            $post_id ),
                            ARRAY_A);

        $total_votes=0; //Avoid undefined variable if stats exists. Necessary if $stats not exists
        
        //if query return 0 wirte an empty array $existing_votes 
        if (!$stats) {
            $existing_votes = array();
        }

        else {
            //Write a new array with only existing votes, and count all the number of votes
            foreach ($stats as $votes) {
                $existing_votes[] = $votes['vote'];//Create an array with only existing votes
                $total_votes = $total_votes + $votes['n_of_votes'];
            }

        }

        for ($i=1; $i<=5; $i++){

            //If query return 0 write a new $stats array with index
            if (!$stats) {
                $stats[$i] = array();
                $stats[$i]['vote'] = $i;
                $stats[$i]['n_of_votes'] = 0;
            }

            else {

                //If in the new array there are some vote missing create a new array
                if(!in_array($i, $existing_votes)) {
                    $missing_vote[$i] = array();
                    $missing_vote[$i]['vote'] = $i; 
                    $missing_vote[$i]['n_of_votes'] = 0;
                }

            }

        }

        //If missing_vote exists merge it 
        if ($missing_vote) {

            $stats = array_merge($stats, $missing_vote);

        }

        arsort($stats); //sort it by $votes[n_of_votes]

        echo "<div class=\"yasr-progress-bars-container\">\n";

        if ($total_votes == 0) {
            $increase_bar_value = 0;
        }
        else {
            $increase_bar_value = 100 / $total_votes; //Find how much all the bars should increase per vote
        }

        $i=5;

        $stars_text = __("stars", "yasr");

        foreach ($stats as $logged_votes) {

            if ($i==1) {
                $stars_text = __("star", "yasr");
            }
            
            echo "<div class=\"yasr-progress-bar-row-container\">
                    <span class=\"yasr-progress-bar-name\">$i $stars_text</span>
                    <span class=\"yasr-progress-bar\" id=\"yasr-progress-bar-postid-$post_id-progress-bar-$i\" ></span>
                    <span class=\"yasr-progress-bar-votes-count\">$logged_votes[n_of_votes]</span>
                </div>"; 
            
            $value_progressbar = $increase_bar_value * $logged_votes['n_of_votes']; //value of the single bar

            $value_progressbar = round ($value_progressbar, 2); //use only 2 decimal

            $array_values_progressbar[] = $value_progressbar; 

            $i--;
                

        } //End foreach

        echo "</div>";

        ?>
        
        <script type="text/javascript">
            jQuery( document ).ready(function() {

                var postId = <?php echo json_encode($post_id)?>;
                var arrayValueProgressbar = <?php echo (json_encode($array_values_progressbar)) ?>;

                yasrDrawProgressBars (arrayValueProgressbar, postId);

            });
        </script>

        <?php

        die();

    }

?>

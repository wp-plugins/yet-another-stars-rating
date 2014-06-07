<?php 

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

/*************************** Admin ajax functions ***********************/

/********** Functions used while wirting a new post or page ********/

/****** Get id and overall rating from post or page and write it in db, used in yasr-metabox-overall-rating******/

	add_action( 'wp_ajax_yasr_send_overall_rating', 'yasr_insert_overall_rating_callback' );

		function yasr_insert_overall_rating_callback() {

            if (isset($_POST['rating']) && ($_POST['post_id'])) {
                global $current_user;
			    get_currentuserinfo();

			    $rating = $_POST['rating'];
			    $post_id = $_POST['post_id'];
			    $reviewer_id = $current_user->ID;
            }

            else {
                exit();
            }

            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
            }

        	global $wpdb;

            //If update works means that visitor already rated this post
        	$update_result=$wpdb->update(
        			YASR_VOTES_TABLE,
        			array (
        				'overall_rating' => $rating,
        				'reviewer_id' => $reviewer_id
        				),
                    array('post_id' => $post_id),
        			array('%s', '%d')
        		);

        	if($update_result) {
        		echo $rating;
        	}
            //else this is a new post or post has no visitor ratings
            else {
                $replace_result=$wpdb->replace(
                    YASR_VOTES_TABLE,
                    array (
                        'post_id' => $post_id,
                        'overall_rating' => $rating,
                        'reviewer_id' => $reviewer_id
                        ),
                    array('%d', '%s', '%d')
                );
                if ($replace_result){
                    echo $rating;
                }
            }

			die(); // this is required to return a proper result
		}



/****** Get Set name from post or page and output the set, 
        used in yasr-metabox-multiple-rating******/

        add_action( 'wp_ajax_yasr_send_id_nameset', 'yasr_output_multiple_set_callback' );

        function yasr_output_multiple_set_callback() {
                if(isset($_POST['set_id']) && isset($_POST['post_id'])) {
                    $set_type = $_POST['set_id'];
                    $post_id = $_POST['post_id'];
                }
                else {
                    exit();
                }

                global $wpdb;

                $set_values=yasr_get_multi_set_values_and_field ($post_id, $set_type);

                //If this is a new post or post has no multi values data
                if (!$set_values) {
                        echo "<p>";

                        _e('Choose a vote for every element', 'yasr');

                        echo "

                        <br /> <br />

                        <table class=\"yasr_table_multi_set_admin\">";
                        //Get Set fields name
                        $set_name=$wpdb->get_results("SELECT field_name AS name, field_id AS id
                            FROM " . YASR_MULTI_SET_FIELDS_TABLE . "  
                            WHERE parent_set_id=$set_type 
                            ORDER BY field_id ASC");

                        foreach ($set_name as $name) {

                            //get the highest id in table
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
                                    'set_type'=>$set_type
                                    ),
                            array ("%d", "%d", "%d", "%s", "%d")
                            );

                            echo "<tr> <td>";
                            echo "$name->name </td>"; 
                            echo "<td> <div class=\"rateit\" id=\"$name->id\" data-rateit-value=\"\" data-rateit-step=\"0.5\" data-rateit-resetable=\"true\" data-rateit-readonly=\"false\"></div> </td>";
                            echo "</tr>";
                        }
                        echo "</table>

                        </p>";

                        echo "<p>";

                        _e("Remember to insert this shortcode", "yasr"); 
                        echo "<strong> [yasr_multiset setid=$set_type] </strong>"; 
                        _e("where you want to display this multi set", "yasr");

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

                                echo "<td width=\"50%\"> <div class=\"rateit\" id=\"$set_content->id\" data-rateit-value=\"$set_content->vote\" data-rateit-step=\"0.5\" data-rateit-resetable=\"true\" data-rateit-readonly=\"false\"></div> </td></tr>";
                        } //End foreach
                        echo "</table>";

                        echo "<p>";

                        _e("Remember to insert this shortcode", "yasr"); 
                        echo "<strong> [yasr_multiset setid=$set_type] </strong>"; 
                        _e("where you want to display this multi set", "yasr");

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
            }
            else {
                exit();
            }

            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'You do not have sufficient permissions to access this page.', 'yasr' ) );
            }

                global $wpdb;

                //Check if vote already exist
                $vote_already_exist=$wpdb->get_results("SELECT id FROM " . YASR_MULTI_SET_VALUES_TABLE . " 
                        WHERE post_id = $post_id
                        AND set_type = $set_type
                        AND field_id = $id_field
                        ");

                //If vote already exist, overwrite it
                if ($vote_already_exist) {
                        foreach ($vote_already_exist as $index_id) {
                                $id = $index_id->id;
                        }       
                        $query_success=$wpdb->replace(
                                YASR_MULTI_SET_VALUES_TABLE,
                                array (
                                        'id'=>$id,
                                        'post_id'=>$post_id,
                                        'field_id'=>$id_field,
                                        'votes'=>$vote,
                                        'set_type'=>$set_type
                                        ),
                                array ("%d", "%d", "%d", "%s", "%d")
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
                                array ("%d", "%d", "%s", "%d")
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
            <div id="yasr-form">
                <table id="yasr-table" class="form-table">
                    <tr>
                        <th><label for="yasr-overall"><?php _e("Overall Rating / Review"); ?></label></th>
                        <td><input type="button" class="button-primary" id="yasr-overall" name="yasr-overall" value="Insert Overall Rating" /><br />
                        <small><?php _e("Insert Overall Rating / Review for this post"); ?></small></td>
                    </tr>
                    <tr>
                        <th><label for="yasr-id"><?php _e("Visitor Votes"); ?></label></th>
                        <td><input type="button" class="button-primary" name="yasr-visitor-votes" id="yasr-visitor-votes" value="Insert Visitor Votes"/><br />
                        <small><?php _e("Insert the ability for your visitor to vote"); ?></small></td>
                    </tr>

                    <?php if ($n_multi_set>1) { //If multiple Set are found ?>

                        <tr>
                            <th><label for="yasr-size"><?php _e("If you want to insert a multi-set, pick one:"); ?></label></th>
                            <td>
                                <?php foreach ($multi_set as $name) { ?>
                                    <input type="radio" value="<?php echo $name->set_id ?>" name="yasr_tinymce_pick_set" class="yasr_tinymce_select_set"><?php echo $name->set_name ?>
                                    <br />
                                <?php } //End foreach ?>
                            <small><?php _e("Choose wich set you want to insert."); ?></small>
                            </td>
                        </tr>

                    <?php } //End if

                    elseif ($n_multi_set==1) { ?>
                        <tr>\
                            <th><label for="yasr-size"><?php _e("Insert Multiset:"); ?></label></th>
                            <td>
                                <?php foreach ($multi_set as $name) { ?>
                                    <button type="button" class="button-primary" id="yasr-single-set" name="yasr-single-set" value="<?php echo $name->set_id ?>" >Insert Multiple Set</button><br />
                                    <small><?php _e("Insert multiple set <?php echo $name->set_name ?> in this post ?"); ?></small>
                                <?php } //End foreach ?>
                            </td>
                        </tr>
                    <?php 
                        }
                    //End elseif ?>

                </table>
            </div>

            <script>
                // Add shortcode fore overall rating
                jQuery('#yasr-overall').on("click", function(){
                    var shortcode = '[yasr_overall_rating]';
                    // inserts the shortcode into the active editor
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    // closes Thickbox
                    tb_remove();
                });

                //Add shortcode for visitors rating
                jQuery('#yasr-visitor-votes').on("click", function(){
                    var shortcode = '[yasr_visitor_votes]';   
                    // inserts the shortcode into the active editor
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    // closes Thickbox
                    tb_remove();
                });

                <?php if ($n_multi_set>1) { ?>

                    //Add shortcode for multiple set
                    jQuery('.yasr_tinymce_select_set').on("click", function(){
                        var setType = jQuery("input:radio[name=yasr_tinymce_pick_set]:checked" ).val();
                        var shortcode = '[yasr_multiset setid=';
                        shortcode += setType;
                        shortcode += ']';
                        // inserts the shortcode into the active editor
                        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                        // closes Thickbox
                        tb_remove();
                    });

                <?php } //End if

                elseif ($n_multi_set==1) { ?>

                //Add shortcode for single set (if only 1 are found)
                    jQuery('#yasr-single-set').on("click", function(){
                        var setType = jQuery('#yasr-single-set').val();
                        var shortcode = '[yasr_multiset setid=';
                        shortcode += setType;
                        shortcode += ']';
                        // inserts the shortcode into the active editor
                        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                        // closes Thickbox
                        tb_remove();
                    });

                <?php 
                }
                //End elseif ?>

            </script>

<?php
        die();

    } //End callback function 

/********** END Functions used while wirting a new post or page ********/




/****** Get multiple set, used in settings page ******/

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

            <input type=\"hidden\" name=\"yasr-edit-form-number-elements\" value=\"$i\">

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

        <script type="text/javascript">

        var counter = <?php echo "$i"; ?>;

        counter = counter+1;

        </script>

        <?php

        die();

    } //End function 






/********* IMPORT FUNCTIONS *********/


    if (is_admin()) {
        add_action( 'wp_ajax_yasr_import_step1', 'yasr_import_step1_callback' );
    }

    function yasr_import_step1_callback () {
        //Import reviews from GD star 
         $reviews=yasr_import_gdstar_data();

        //Insert GD star review in yasr votes table
        $check_query_success=yasr_insert_gdstar_data($reviews);
        ?>

        <div class="yasr-result-step-1">
            <?php
            if ($check_query_success) {  
                _e( "Reviews and visitor votes have been successfull imported.", 'yasr'); ?>
                <br />
                <?php _e ("Step2: I will check if you used multiple set and if so I will import it. THIS MAY TAKE A WHILE!", 'yasr'); ?>
                <br />
                    <button href=\"#\" class=\"button-primary\" id=\"import-button-step2\"> <?php _e('Proceed Step 2', 'yasr');?> </button>
                    <span id="loader2" style="display:none;" >&nbsp;<img src="<?php echo YASR_IMG_DIR . "/loader.gif" ?>">
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

    if (is_admin()) {
        add_action( 'wp_ajax_yasr_import_multi_set', 'yasr_check_import_set_callback' );
    }

    function yasr_check_import_set_callback () {
        $multi_set_names=yasr_import_gdstar_multi_set();

        echo "<div class=\"yasr-result-step-2\">";

        //If multiple set are found
        if($multi_set_names) {
            echo "<br /><strong>";
            _e("I've found multiple set! Importing..." , 'yasr');
            echo "</strong><br />";

            //If multi set are found write in yasr_multi_set table
            $insert_multi_set=yasr_insert_gdstar_multi_set($multi_set_names);

            //If insert succes, go ahed
            if ($insert_multi_set) {
                echo "&nbsp;&nbsp;&nbsp;";
                _e("Multi set's name has been successfull imported.", 'yasr');
                echo "<br /><strong>"; 
                _e("Now I'm going to import multi set data", 'yasr');
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
                        update_option('yasr-gdstar-imported', '1');
                        echo "<button href=\"#\" class=\"button-delete\" id=\"end-import\">" . __('Done', 'yasr') . "</button>";

                    }
                    else {
                        echo "&nbsp;&nbsp;&nbsp;";
                        _e("I've found multiple set votes but I couldn't insert into db", 'yasr');
                        echo  "<br />";
                    }
                } //End if $multi_data 

                //Multiple set are found, but there is not data
                else { 
                    echo "&nbsp;&nbsp;&nbsp;";
                    _e( "I've found multi set but with no data", 'yasr'); 
                    echo "<br />";
                }

            } //End if $insert_multi_set

            //Query failed insert set name 
            else {
                echo "&nbsp;&nbsp;&nbsp;";
                _e("I've found multi set name but I couldn't insert into db", 'yasr');
                echo "<br />";
            }
        
        } //End if $multi_set_names

        else {
            echo "&nbsp;&nbsp;&nbsp;";
            _e ("Multiset was not found. Imported is done!", 'yasr');
        }

        echo "</div>";

        die ();

    } //End function

/**************** END Admin ajax functions ****************/


/**************** NON Admin ajax functions ****************/

/****** Yasr insert visitor votes, called from yasr-shortcode-function ******/
    
        add_action( 'wp_ajax_yasr_send_visitor_rating', 'yasr_insert_visitor_votes_callback' );
        add_action( 'wp_ajax_nopriv_yasr_send_visitor_rating', 'yasr_insert_visitor_votes_callback' );

        function yasr_insert_visitor_votes_callback () {
            if(isset($_POST['rating']) && isset($_POST['post_id'])) {
                $rating = $_POST['rating'];
                $post_id = $_POST['post_id'];
            }
            else {
                exit();
            }

            $row_exists_result=NULL; //Avoid Undefined variable notice
            $new_row_result=NULL; ////Avoid Undefined variable notice

            global $wpdb;

            $row_exists = $wpdb->get_results ("SELECT number_of_votes, sum_votes FROM " . YASR_VOTES_TABLE . "
                                            WHERE post_id=$post_id");

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
                $new_row_result=$wpdb->replace (
                    YASR_VOTES_TABLE,
                    array (
                        'post_id' => $post_id,
                        'number_of_votes' => 1,
                        'overall_rating' => '-1',
                        'sum_votes' => $rating
                        ),
                    array ('%d', "%d", "%s", "%s")
                    );
            }

            if ($row_exists_result || $new_row_result ) {
                global $current_user;
                get_currentuserinfo();

                $result_insert_log = $wpdb->replace (
                    YASR_LOG_TABLE,
                    array (
                        'post_id' => $post_id,
                        'multi_set_id' => -1,
                        'user_id' => $current_user->ID,
                        'vote' => $rating,
                        'date' => date('Y-m-d H:i:s'),
                        'ip' => $_SERVER['REMOTE_ADDR']
                        ), 
                    array ('%d', '%d', '%d', '%s', '%s', '%s')
                    );
            }

            if($row_exists_result) {
                $total_rating = ($user_votes_sum / $number_of_votes);
                $total_rating=round ($total_rating, 1);
                echo "<div class=\"rateit bigstars\" id=\"yasr_rateit_user_votes_voted\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$total_rating\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                <br /><strong>Vote Saved.</strong><br />Average Rating $total_rating / 5 ($number_of_votes votes casts)";
            }

            elseif ($new_row_result) {
                echo "<div class=\"rateit bigstars\" id=\"yasr_rateit_user_votes_voted\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$rating\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                <br /><strong>Vote Saved.</strong><br />Rating $rating / 5 (1 vote casts)";
            }

            die(); // this is required to return a proper result
        }

/****** Echo a readonly star set if user has already voted for a post ******/

    add_action( 'wp_ajax_yasr_readonly_visitor_shortcode', 'yasr_readonly_visitor_shortcode_callback' );
    add_action( 'wp_ajax_nopriv_yasr_readonly_visitor_shortcode', 'yasr_readonly_visitor_shortcode_callback' );

    function yasr_readonly_visitor_shortcode_callback() {
        if(isset($_POST['rating']) && isset($_POST['post_id']) && isset($_POST['votes']) && isset($_POST['votes_number'])) {
            $rating = $_POST['rating'];
            $post_id = $_POST['post_id'];
            $average_rating= $_POST['votes'];
            $number_of_votes = $_POST['votes_number'];
        }
        else {
            exit();
        }

        echo "<div class=\"rateit bigstars\" id=\"yasr_rateit_user_votes_voted_ro\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$average_rating\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
        <br /><strong>You've already voted this article with $rating</strong><br />Average $average_rating / 5 ($number_of_votes votes casts)";

        die(); // this is required to return a proper result

    } //End callback function


?>
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
                        echo "<td> 
                                <div class=\"rateit bigstars multi\" id=\"$name->id\" data-rateit-value=\"\"  data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-step=\"0.5\" data-rateit-resetable=\"true\" data-rateit-readonly=\"false\"></div>
                              
                                <span id=\"yasr-loader-multi-set-field-$name->id\" style=\"display:none;\" >&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\" ></span>
                              </td>
                              </tr>";

                    
                    } //End foreach

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

                        echo "<td width=\"50%\"> 
                                <div class=\"rateit bigstars multi\" id=\"$set_content->id\"  data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$set_content->vote\" data-rateit-step=\"0.5\" data-rateit-resetable=\"true\" data-rateit-readonly=\"false\"></div> 

                                <span id=\"yasr-loader-multi-set-field-$set_content->id\" style=\"display:none;\" >&nbsp;<img src=\"" . YASR_IMG_DIR . "/loader.gif\"></span>
                              </td>
                            </tr>";


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
                $nonce = $_POST['nonce'];
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

            <div id="yasr-tinypopup-form">

                <h2 class="nav-tab-wrapper yasr-underline">
                    <a href="#" id="yasr-link-tab-main" class="nav-tab nav-tab-active">Main</a>
                    <a href="#" id="yasr-link-tab-charts" class="nav-tab">Charts</a>

                    <a href="https://wordpress.org/plugins/yet-another-stars-rating/faq/" target="_blank" id="yasr-tinypopup-link-doc">Read the doc</a>

                </h2>

                <div id="yasr-content-tab-main">

                    <table id="yasr-table-tiny-popup-main" class="form-table">
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
                            <tr>
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

                <div id="yasr-content-tab-charts" style="display:none">

                    <table id="yasr-table-tiny-popup-charts" class="form-table">
                        <tr>
                            <th><label for="yasr-10-overall"><?php _e("Top 10 overall ratings"); ?></label></th>
                            <td><input type="button" class="button-primary" name="yasr-top-10-overall-rating" id="yasr-top-10-overall-rating" value="Insert Top 10 highest rated"/><br />
                            <small><?php _e("Insert Top 10 highest rated by post author"); ?></small></td>
                        </tr>

                        <tr>
                            <th><label for="yasr-10-highest-most-rated"><?php _e("Top 10 by visitors"); ?></label></th>
                            <td><input type="button" class="button-primary" name="yasr-10-highest-most-rated" id="yasr-10-highest-most-rated" value="Insert Top 10 posts by visitors"/><br />
                            <small><?php _e("Insert Top 10 most or higher rated posts from visitors"); ?></small></td>
                        </tr>

                        <tr>
                            <th><label for="yasr-5-active-reviewers"><?php _e("Most active reviewers"); ?></label></th>
                            <td><input type="button" class="button-primary" name="yasr-5-active-reviewers" id="yasr-5-active-reviewers" value="Insert Top 5 most active reviewers"/><br />
                            <small><?php _e("Insert Top 5 active reviewers"); ?></small></td>
                        </tr>

                        <tr>
                            <th><label for="yasr-10-active-users"><?php _e("Most active users"); ?></label></th>
                            <td><input type="button" class="button-primary" name="yasr-top-10-active-users" id="yasr-top-10-active-users" value="Insert Top 10 most active users"/><br />
                            <small><?php _e("Insert Top 10 active users in visitor ratings"); ?></small></td>
                        </tr>

                    </table>

                </div>

            </div>

            <script>

                // When click on chart chart hide tab-main and show tab-charts
                jQuery('#yasr-link-tab-charts').on("click", function(){

                    jQuery('#yasr-link-tab-main').removeClass('nav-tab-active');
                    jQuery('#yasr-link-tab-charts').addClass('nav-tab-active');

                    jQuery('#yasr-content-tab-main').hide();
                    jQuery('#yasr-content-tab-charts').show();

                });

                // When click on main tab hide tab-main and show tab-charts
                jQuery('#yasr-link-tab-main').on("click", function(){

                    jQuery('#yasr-link-tab-charts').removeClass('nav-tab-active');
                    jQuery('#yasr-link-tab-main').addClass('nav-tab-active');

                    jQuery('#yasr-content-tab-charts').hide();
                    jQuery('#yasr-content-tab-main').show();

                });

                // Add shortcode for overall rating
                jQuery('#yasr-overall').on("click", function(){
                    var shortcode = '[yasr_overall_rating]';
                    // inserts the shortcode into the active editor
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    // closes jqueryui
                    jQuery('#yasr-tinypopup-form').dialog('close');
                });

                //Add shortcode for visitors rating
                jQuery('#yasr-visitor-votes').on("click", function(){
                    var shortcode = '[yasr_visitor_votes]';   
                    // inserts the shortcode into the active editor
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    // closes Thickbox
                    jQuery('#yasr-tinypopup-form').dialog('close');
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
                        // closes jqueryui
                        jQuery('#yasr-tinypopup-form').dialog('close');
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
                        // closes jqueryui
                        jQuery('#yasr-tinypopup-form').dialog('close');
                    });

                <?php 
                }
                //End elseif ?>

                // Add shortcode for top 10 by overall ratings
                jQuery('#yasr-top-10-overall-rating').on("click", function(){
                    var shortcode = '[yasr_top_ten_highest_rated]';
                    // inserts the shortcode into the active editor
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    // closes jqueryui
                    jQuery('#yasr-tinypopup-form').dialog('close');
                });

                // Add shortcode for 10 highest most rated
                jQuery('#yasr-10-highest-most-rated').on("click", function(){
                    var shortcode = '[yasr_most_or_highest_rated_posts]';
                    // inserts the shortcode into the active editor
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    // closes jqueryui
                    jQuery('#yasr-tinypopup-form').dialog('close');
                });

                // Add shortcode for top 5 active reviewer
                jQuery('#yasr-5-active-reviewers').on("click", function(){
                    var shortcode = '[yasr_top_5_reviewers]';
                    // inserts the shortcode into the active editor
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    // closes jqueryui
                    jQuery('#yasr-tinypopup-form').dialog('close');
                });

                // Add shortcode for top 10 active users
                jQuery('#yasr-top-10-active-users').on("click", function(){
                    var shortcode = '[yasr_top_ten_active_users]';
                    // inserts the shortcode into the active editor
                    tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
                    // closes jqueryui
                    jQuery('#yasr-tinypopup-form').dialog('close');
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

            //Import reviews from GD star 
            $reviews=yasr_import_gdstar_data();

            //Insert GD star review in yasr votes table
            $check_query_success=yasr_insert_gdstar_data($reviews);
            ?>

            <div class="yasr-result-step-1">
                <?php
                if ($check_query_success) {  
                    _e( "Reviews and visitor votes have been successfull imported.", 'yasr');

                    update_option('yasr-gdstar-imported', '1');

                    ?>
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
                            //update_option('yasr-gdstar-imported', '1');
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




/****** 
        Display recent votes on dashboard, called from function yasr_display_dashboard_log_wiget,
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


/**************** END Admin ajax functions ****************/


/**************** NON Admin ajax functions ****************/

/****** Yasr insert visitor votes, called from yasr-shortcode-function ******/
    
    add_action( 'wp_ajax_yasr_send_visitor_rating', 'yasr_insert_visitor_votes_callback' );
    add_action( 'wp_ajax_nopriv_yasr_send_visitor_rating', 'yasr_insert_visitor_votes_callback' );

    function yasr_insert_visitor_votes_callback () {
        if(isset($_POST['rating']) && isset($_POST['post_id'])) {
            $rating = $_POST['rating'];
            $post_id = $_POST['post_id'];
            $nonce_visitor = $_POST['nonce_visitor'];
        }
        else {
            exit();
        }

        if ( ! wp_verify_nonce( $nonce_visitor, 'yasr_nonce_insert_visitor_rating' ) ) {
                die( 'Security check' ); 
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
            <br /><strong>" . __("Vote Saved" , "yasr") . "</strong><br />" . __("Average Rating", "yasr") . " $total_rating / 5 ($number_of_votes " . __("votes casts", "yasr") . ")";
        }

        elseif ($new_row_result) {
            echo "<div class=\"rateit bigstars\" id=\"yasr_rateit_user_votes_voted\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$rating\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
            <br /><strong>". __("Vote Saved" , "yasr") . "</strong><br />Rating $rating / 5 (1 " . __("vote cast", "yasr") . ")";
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


        //Check if user specifyed a custom text to display when a vistor har rated
        $option = get_option('yasr_general_options');

        if($option['text_before_stars'] == 1 && $option['custom_text_user_voted'] != '') {

            echo "<div class=\"rateit bigstars\" id=\"yasr_rateit_user_votes_voted_ro\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$average_rating\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
            <br />" . __("Average Rating", "yasr") . " $average_rating / 5 ($number_of_votes " . __("votes casts", "yasr") . ")<strong><br /> $option[custom_text_user_voted] </strong>";

        }

        else {

            echo "<div class=\"rateit bigstars\" id=\"yasr_rateit_user_votes_voted_ro\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$average_rating\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
            <br />" . __("Average Rating", "yasr") . " $average_rating / 5 ($number_of_votes " . __("votes casts", "yasr") . ")<strong><br />" . __("You've already voted this article with $rating", "yasr") . "</strong>";

        }



        die(); // this is required to return a proper result

    } //End callback function


/****** Order yasr_multi_chart ******/

/****** Order yasr_multi_chart ******/

    add_action ( 'wp_ajax_yasr_multi_chart_most_highest', 'yasr_multi_chart_most_highest_callback' );
    add_action ( 'wp_ajax_nopriv_yasr_multi_chart_most_highest', 'yasr_multi_chart_most_highest_callback' );

    function yasr_multi_chart_most_highest_callback () {

        global $wpdb;

        $chart_type = 'most'; //default value;

        if (isset($_POST['order_by'])) {

            $chart_type = $_POST['order_by'];

            if ($chart_type != 'most' && $chart_type != 'highest') {

                $chart_type = 'most';

            }

        }

        if ($chart_type === 'most' ) {

            $query_result_most_rated = $wpdb->get_results("SELECT post_id, number_of_votes, sum_votes
                                                FROM " . YASR_VOTES_TABLE . ", $wpdb->posts AS p 
                                                WHERE post_id = p.ID
                                                AND p.post_status = 'publish'
                                                ORDER BY number_of_votes DESC, sum_votes DESC LIMIT 10");

            if ($query_result_most_rated) {

                echo ( "<table class=\"yasr-most-or-highest-rated-posts\">
                                    <tr>
                                        <th>Post / Page</th>
                                        <th>Order By:&nbsp;&nbsp; <a href=\"#\" id=\"yasr_multi_chart_link_to_nothing\">Most Rated</a> | <a href=\"#\" id=\"yasr_multi_chart_highest\">Highest Rated</a></th>
                                    </tr>"
                    );

                foreach ($query_result_most_rated as $result) {

                    $rating = $result->sum_votes / $result->number_of_votes;

                    $rating = round($rating, 1);

                    $post_title = get_the_title($result->post_id);

                    $link = get_permalink($result->post_id); //Get permalink from post it

                    echo ( "<tr>
                                            <td width=\"60%\"><a href=\"$link\">$post_title</a></td>
                                            <td width=\"40%\"><div id=\"yasr_visitor_votes\"><div class=\"rateit charts\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                                            <br /> [" .  __("Total:" , "yasr") . "$result->number_of_votes &nbsp;&nbsp;&nbsp;" . __("Average" , "yasr") . " $rating]</td>
                            </tr>"

                         );


                } //End foreach

                echo ("</table>") ;

            } //End if $query_result_most_rated)

        } // End if  ($chart_type === 'most' )

        elseif ($chart_type ==='highest') {

            $query_result_highest = $wpdb->get_results("SELECT (sum_votes / number_of_votes) as result, post_id, number_of_votes
                                                FROM " . YASR_VOTES_TABLE . ", $wpdb->posts AS p 
                                                WHERE post_id = p.ID
                                                AND number_of_votes >= 2
                                                AND p.post_status = 'publish'
                                                ORDER BY result DESC, number_of_votes DESC LIMIT 10
                                                ");

            if ($query_result_highest) {

                echo ( "<table class=\"yasr-most-or-highest-rated-posts\">
                                    <tr>
                                        <th>Post / Page</th>
                                        <th>Order By:&nbsp;&nbsp; <a href=\"#\" id=\"yasr_multi_chart_most\">Most Rated</a> | <a href=\"#\" id=\"yasr_multi_chart_link_to_nothing\">Highest Rated</a></th>
                                    </tr>"

                      );

                foreach ($query_result_highest as $result) {

                    $rating = round($result->result, 1);

                    $post_title = get_the_title($result->post_id);

                    $link = get_permalink($result->post_id); //Get permalink from post it

                    echo ("<tr>
                                <td width=\"60%\"><a href=\"$link\">$post_title</a></td>
                                <td width=\"40%\"><div id=\"yasr_visitor_votes\"><div class=\"rateit charts\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                                <br /> [" .  __("Total:" , "yasr") . "$result->number_of_votes &nbsp;&nbsp;&nbsp;" . __("Average" , "yasr") . " $rating]</td>
                        </tr>");


                } //End foreach

                echo "</table>";

            } //end if $query_result

            else {
                _e("You don't have any user votes stored, or they're not enought. In order to appear in this chart, post must have at least 2 votes. Post whith less than 2 vote are ignored", "yasr");
            }

        } //End if ($chart_type ==='highest')
    
        die();

    } //End function


?>
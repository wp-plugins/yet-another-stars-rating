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

/****** Add shortcode for overall rating ******/
add_shortcode ('yasr_overall_rating', 'shortcode_overall_rating_callback');

function shortcode_overall_rating_callback ($atts) {

    if (!$atts) {
        $size = 'large';
    }

    else {
        extract( shortcode_atts (
            array(
                'size' => 'string',
            ), $atts )
        );
    }

        $overall_rating=yasr_get_overall_rating();

        if (!$overall_rating) {
            $overall_rating = "-1";
        }

        $shortcode_html = '';

        if (YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_BEFORE_OVERALL != '') {

            $shortcode_html = "<div class=\"yasr-container-custom-text-and-overall\">
                                    <span id=\"yasr-custom-text-before-overall\">" . YASR_TEXT_BEFORE_OVERALL . "</span>";

        }

        if ($size === 'small') {
            $rateit_class='rateit';
            $px_size = '16';
        }

        elseif ($size === 'medium') {
            $rateit_class = 'rateit medium';
            $px_size = '24';
        }

        //default values
        else {
            $rateit_class = 'rateit bigstars';
            $px_size = '32';
        }

        $shortcode_html .= "<div class=\"$rateit_class\" id=\"yasr_rateit_overall\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$overall_rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>"; 


        if (YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_BEFORE_OVERALL != '') {

            $shortcode_html .= "</div>";
        
        }

        //IF show overall rating in loop is disabled use is_singular && is_main query
        if ( YASR_SHOW_OVERALL_IN_LOOP === 'disabled' ) {

            //If pages are not excluted
            if ( YASR_AUTO_INSERT_EXCLUDE_PAGES === 'no' || !YASR_AUTO_INSERT_EXCLUDE_PAGES ) {

                if( is_singular() && is_main_query() ) {

                    return $shortcode_html;

                }

            }

            //If page are excluted
            else {

                if( is_singular() && is_main_query() && !is_page() )

                    return $shortcode_html;

            }

        } // End if YASR_SHOW_OVERALL_IN_LOOP === 'disabled') {

            //If overall rating in loop is enabled don't use is_singular && is main_query
        elseif ( YASR_SHOW_OVERALL_IN_LOOP === 'enabled' ) {

            //If pages are not excluted return always
            if ( YASR_AUTO_INSERT_EXCLUDE_PAGES === 'no' || !YASR_AUTO_INSERT_EXCLUDE_PAGES ) {

                return $shortcode_html;

            }

            //Else if page are excluted return only if is not a page
            else {

                if ( !is_page() ) {

                    return $shortcode_html;

                }

            }

        }

} //end function


/****** Add shortcode for user vote ******/

add_shortcode ('yasr_visitor_votes', 'shortcode_visitor_votes_callback');

function shortcode_visitor_votes_callback ($atts) {

    $shortcode_html = NULL; //Avoid undefined variable outside is_singular && is_main_query

    $post_id = get_the_ID();

    $ajax_nonce_visitor = wp_create_nonce( "yasr_nonce_insert_visitor_rating" );

    $votes=yasr_get_visitor_votes();

    $medium_rating=0;   //Avoid undefined variable

    if (!$votes) {
        $votes=0;         //Avoid undefined variable if there is not overall rating
        $votes_number=0;  //Avoid undefined variable
    }

    else {
        foreach ($votes as $user_votes) {
            $votes_number = $user_votes->number_of_votes;
            if ($votes_number != 0 ) {
                $medium_rating = ($user_votes->sum_votes/$votes_number);
            }
            else {
                $medium_rating = 0;
            }
        }
    }

    $image = YASR_IMG_DIR . "/loader.gif";

    $loader_html = '<div id="loader-visitor-rating" >&nbsp; ' . __("Loading, please wait","yasr") . ' <img src=' .  "$image" .' title="yasr-loader" alt="yasr-loader"></div>';

    $medium_rating=round($medium_rating, 1);

    if (!$atts) {
        $size = 'large';
    }

    else {
        extract( shortcode_atts (
            array(
                'size' => 'string',
            ), $atts )
        );
    }

    if ($size === 'small') {
        $rateit_class='rateit';
        $px_size = '16';
    }

    elseif ($size === 'medium') {
        $rateit_class = 'rateit medium';
        $px_size = '24';
    }

    //default values
    else {
        $rateit_class = 'rateit bigstars';
        $px_size = '32';
    }

    $shortcode_html = "<div id=\"yasr_visitor_votes_$post_id\" class=\"yasr-visitor-votes\">";

        //if anonymous are allowed to vote
        if (YASR_ALLOWED_USER === 'allow_anonymous') {

            //I've to block a logged in user that has already rated
            if ( is_user_logged_in() ) {

                //Chek if a logged in user has already rated for this post
                $vote_if_user_already_rated = yasr_check_if_user_already_voted();

                //If user has already rated 
                if ($vote_if_user_already_rated) {

                    $shortcode_html.="<div class=\"$rateit_class\" id=\"yasr_rateit_visitor_votes_$post_id\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                    <span class=\"yasr-total-average-text\" id=\"yasr-total-average-text_$post_id\" title=\"yasr-stats\">[" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average: ","yasr") . "$medium_rating/5]</span>
                    <span class=\"yasr-small-block-bold\" id=\"yasr-already-voted-text\">" . __("You've already voted this article with", "yasr") . " $vote_if_user_already_rated </span>";

                }

                //else logged user can vote 
                else {

                    $vote_if_user_already_rated = 0;

                    $shortcode_html.="<div class=\"$rateit_class\" id=\"yasr_rateit_visitor_votes_$post_id\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                    <span class=\"yasr-total-average-text\" id=\"yasr-total-average-text_$post_id\" title=\"yasr-stats\">[" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average: ","yasr") . "$medium_rating/5]</span>";

                } //End else

            } //End if user is logged


            //else if is not logged can vote
            else {

                $shortcode_html.="<div class=\"$rateit_class\" id=\"yasr_rateit_visitor_votes_$post_id\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                <span class=\"yasr-total-average-text\" id=\"yasr-total-average-text_$post_id\" title=\"yasr-stats\">[" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average: ","yasr") . "$medium_rating/5]</span>";

            } //end else
      
        } //end if  ($allow_logged_option['allowed_user']==='allow_anonymous') {


        //If only logged in users can vote
        elseif (YASR_ALLOWED_USER === 'logged_only') {

            //If user is logged in and can vote
            if ( is_user_logged_in() ) {

                //Chek if a logged in user has already rated for this post
                $vote_if_user_already_rated = yasr_check_if_user_already_voted();

                if ($vote_if_user_already_rated) {

                    $shortcode_html.="<div class=\"$rateit_class\" id=\"yasr_rateit_visitor_votes_$post_id\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                    <span class=\"yasr-total-average-text\" id=\"yasr-total-average-text_$post_id\" title=\"yasr-stats\">[" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average: ","yasr") . "$medium_rating/5]</span>
                    <span class=\"yasr-small-block-bold\" id=\"yasr-already-voted-text\">" . __("You've already voted this article with", "yasr") . " $vote_if_user_already_rated </span>";

                }

                else {

                    $vote_if_user_already_rated = 0;

                    $shortcode_html.="<div class=\"$rateit_class\" id=\"yasr_rateit_visitor_votes_$post_id\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                    <span class=\"yasr-total-average-text\" id=\"yasr-total-average-text_$post_id\" title=\"yasr-stats\">[" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average: ","yasr") . "$medium_rating/5]</span>";

                }

            } //End if user is logged in

            //Else mean user is not logged in
            else {

                $shortcode_html.="<div class=\"$rateit_class\" id=\"yasr_rateit_visitor_votes_$post_id\" data-rateit-starwidth=\"$px_size\" data-rateit-starheight=\"$px_size\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                <span class=\"yasr-total-average-text\" id=\"yasr-total-average-text_$post_id\" title=\"yasr-stats\">[" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average: ","yasr") . "$medium_rating/5]</span>
                " . __("You must sign to vote", "yasr");

            }

        }

    $shortcode_html .= "</div>";



    if(YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_BEFORE_VISITOR_RATING != '') {

        $shortcode_html_tmp = "<div class=\"yasr-container-custom-text-and-visitor-rating\">
        <span id=\"yasr-custom-text-before-visitor-rating\">" . YASR_TEXT_BEFORE_VISITOR_RATING . "</span>" .  $shortcode_html . "</div>"; 

        $shortcode_html = $shortcode_html_tmp;

    }

    //if (!is_feed()) {

        //$var_tooltip_values = json_encode ("bad, poor, ok, good, super");
        $var_post_id = (json_encode($post_id));
        $var_ajax_url = (json_encode(admin_url('admin-ajax.php')));
        $var_size = (json_encode($size));
        $var_logged_user = (json_encode(is_user_logged_in()));
        $var_vote_if_user_already_rated = (json_encode($vote_if_user_already_rated));
        $var_loader_html = (json_encode("$loader_html"));
        $var_nonce_visitor = (json_encode("$ajax_nonce_visitor"));

        $var_visitor_stats_enabled = (json_encode(YASR_VISITORS_STATS));

        $javascript = "

        <script type=\"text/javascript\">

            jQuery(document).ready(function() {

                var tooltipValues = ['bad', 'poor', 'ok', 'good', 'super'];
                var postid = $var_post_id;
                var ajaxurl = $var_ajax_url;
                var size = $var_size;
                var loggedUser = $var_logged_user;
                var voteIfUserAlredyRated = $var_vote_if_user_already_rated;
                var loaderHtml = $var_loader_html;
                var nonceVisitor = $var_nonce_visitor;
                    
                yasrVisitorsVotes(tooltipValues, postid, ajaxurl, size, loggedUser, voteIfUserAlredyRated, loaderHtml, nonceVisitor);

                var visitorStatsEnabled = $var_visitor_stats_enabled;

                //If stats are enabled call the function 
                if (visitorStatsEnabled == 'yes') {
                    yasrDrawTipsProgress (postid, ajaxurl); 
                }

            });

        </script>

        ";


        //IF show visitor votes in loop is disabled use is_singular && is_main query
        if ( YASR_SHOW_VISITOR_VOTES_IN_LOOP === 'disabled' ) {

            //If pages are not excluted
            if ( YASR_AUTO_INSERT_EXCLUDE_PAGES === 'no' || !YASR_AUTO_INSERT_EXCLUDE_PAGES ) {

                if( is_singular() && is_main_query() ) {

                    return $shortcode_html . $javascript;

                }

            }

            //If page are excluted
            else {

                if( is_singular() && is_main_query() && !is_page() )

                    return $shortcode_html . $javascript;;

            }

        } // End if YASR_SHOW_VISITOR_VOTES_IN_LOOP === 'disabled') {

            //If overall rating in loop is enabled don't use is_singular && is main_query
        elseif ( YASR_SHOW_VISITOR_VOTES_IN_LOOP === 'enabled' ) {

            //If pages are not excluted return always
            if ( YASR_AUTO_INSERT_EXCLUDE_PAGES === 'no' || !YASR_AUTO_INSERT_EXCLUDE_PAGES ) {

                return $shortcode_html . $javascript;;

            }

            //Else if page are excluted return only if is not a page
            else {

                if ( !is_page() ) {

                    return $shortcode_html . $javascript;;

                }

            }

        }

   // } //End (!is_feed)

} //End function shortcode_visitor_votes_callback


/****** Add shortcode for multiple set ******/

add_shortcode ('yasr_multiset', 'shortcode_multi_set_callback');

function shortcode_multi_set_callback( $atts ) {

	$post_id=get_the_id();

	global $wpdb;
	
	// Attributes
	extract( shortcode_atts(
		array(
			'setid' => '1',
		), $atts )
	);

	$set_name_content=yasr_get_multi_set_values_and_field ($post_id, $setid);

	if ($set_name_content) {
		$shortcode_html="<table class=\"yasr_table_multi_set_shortcode\">";
     	foreach ($set_name_content as $set_content) {
        	$shortcode_html .=  "<tr> <td><span class=\"yasr-multi-set-name-field\">$set_content->name </span></td>
      		   					 <td><div class=\"rateit\" id=\"$set_content->id\" data-rateit-value=\"$set_content->vote\" data-rateit-step=\"0.5\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div></td>
        						 </tr>";
        }
    	$shortcode_html.="</table>";
    }

    //If there is not vote for that set...i.e. add shortcode without initialize it
    else {
    	$set_name=$wpdb->get_results("SELECT field_name AS name, field_id AS id
                    FROM " . YASR_MULTI_SET_FIELDS_TABLE . "  
                    WHERE parent_set_id=$setid 
                    ORDER BY field_id ASC");

    	$shortcode_html="<table class=\"yasr_table_multi_set_shortcode\">";

     	foreach ($set_name as $set_content) {
        	$shortcode_html .=  "<tr> <td><span class=\"yasr-multi-set-name-field\">$set_content->name </span></td>
      		   					 <td><div class=\"rateit\" id=\"$set_content->id\" data-rateit-value=\"0\" data-rateit-step=\"0.5\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div></td>
        						 </tr>";
        }
    	$shortcode_html.="</table>";
    	
    }
	return $shortcode_html;
	} //End function



/****** Add top 10 highest rated post *****/

add_shortcode ('yasr_top_ten_highest_rated', 'yasr_top_ten_highest_rated_callback');

function yasr_top_ten_highest_rated_callback () {

    global $wpdb;

    $query_result = $wpdb->get_results("SELECT v.overall_rating, v.post_id
                                        FROM " . YASR_VOTES_TABLE . " AS v, $wpdb->posts AS p
                                        WHERE  v.post_id = p.ID
                                        AND p.post_status = 'publish'
                                        ORDER BY v.overall_rating DESC, v.id ASC LIMIT 10");

    if ($query_result) {

        $shortcode_html = "<table class=\"yasr-table-chart\">";

        foreach ($query_result as $result) {

            $post_title = get_the_title($result->post_id);

            $link = get_permalink($result->post_id); //Get permalink from post it

            $shortcode_html .= "<tr>
                                    <td width=\"60%\"><a href=\"$link\">$post_title</a></td>
                                    <td width=\"40%\">
                                        <div class=\"rateit medium\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$result->overall_rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                                        <span class=\"yasr-highest-rated-text\">" . __("Rating", "yasr") . " $result->overall_rating </span>
                                        </td>
                                </tr>";


        } //End foreach

        $shortcode_html .= "</table>";

        return $shortcode_html;

    } //end if $query_result

    else {
        _e("You don't have any votes stored", "yasr");
    }

} //End function


/****** Add top 10 most rated / highest rated post *****/

add_shortcode ('yasr_most_or_highest_rated_posts', 'yasr_most_or_highest_rated_posts_callback');

function yasr_most_or_highest_rated_posts_callback () {


    $shortcode_html = "";

    global $wpdb;

    $query_result_most_rated = $wpdb->get_results("SELECT post_id, number_of_votes, sum_votes
                                        FROM " . YASR_VOTES_TABLE . ", $wpdb->posts AS p 
                                        WHERE post_id = p.ID
                                        AND number_of_votes >= 1
                                        AND p.post_status = 'publish'
                                        ORDER BY number_of_votes DESC, sum_votes DESC LIMIT 10");

    $query_result_highest = $wpdb->get_results("SELECT (sum_votes / number_of_votes) as result, post_id, number_of_votes
                                        FROM " . YASR_VOTES_TABLE . ", $wpdb->posts AS p 
                                        WHERE post_id = p.ID
                                        AND number_of_votes >= 2
                                        AND p.post_status = 'publish'
                                        ORDER BY result DESC, number_of_votes DESC LIMIT 10
                                        ");

    if ($query_result_most_rated) {

        $shortcode_html .= "<table class=\"yasr-table-chart\" id=\"yasr-most-rated-posts\">
                        <tr>
                            <th>" . __("Post / Page" , "yasr") ." </th>
                            <th>". __("Order By" , "yasr") .":&nbsp;&nbsp;<span id=\"yasr_multi_chart_link_to_nothing\">" . __("Most Rated" , "yasr") ."</span> | <a href=\"#\" id=\"yasr_multi_chart_highest\">" . __("Highest Rated" , "yasr") ."</a></th>
                        </tr>"
                        ;

        foreach ($query_result_most_rated as $result) {

            $rating = $result->sum_votes / $result->number_of_votes;

            $rating = round($rating, 1);

            $post_title = get_the_title($result->post_id);

            $link = get_permalink($result->post_id); //Get permalink from post it

            $shortcode_html .= "<tr>
                        <td width=\"60%\"><a href=\"$link\">$post_title</a></td>
                            <td width=\"40%\"><div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <br /> [" .  __("Total:" , "yasr") . "$result->number_of_votes &nbsp;&nbsp;&nbsp;" . __("Average" , "yasr") . " $rating]
                        </td>
                    </tr>";


        } //End foreach

        $shortcode_html .= "</table>" ;

    } //End if $query_result_most_rated)

    else {
        $shortcode_html = __("You've not enough data","yasr") . "<br />";
    }

    
    if ($query_result_highest) {

        $shortcode_html .= "<table class=\"yasr-table-chart\" id=\"yasr-highest-rated-posts\">
                        <tr>
                            <th>" . __("Post / Page" , "yasr") ." </th>
                            <th>". __("Order By" , "yasr") .":&nbsp;&nbsp; <a href=\"#\" id=\"yasr_multi_chart_most\">". __("Most Rated" , "yasr") ."</a> | <span id=\"yasr_multi_chart_link_to_nothing\">". __("Highest Rated" , "yasr") ."</span></th>
                        </tr>";

        foreach ($query_result_highest as $result) {

            $rating = round($result->result, 1);

            $post_title = get_the_title($result->post_id);

            $link = get_permalink($result->post_id); //Get permalink from post it

            $shortcode_html .= "<tr>
                        <td width=\"60%\"><a href=\"$link\">$post_title</a></td>
                        <td width=\"40%\"><div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <br /> [" .  __("Total:" , "yasr") . "$result->number_of_votes &nbsp;&nbsp;&nbsp;" . __("Average" , "yasr") . " $rating]
                        </td>
                   </tr>";


        } //End foreach

        $shortcode_html .= "</table>";

    } //end if $query_result

    else {
        $shortcode_html = __("You've not enought data","yasr") . "<br />";
    }

    ?>

    <script type="text/javascript">

        jQuery(document).ready(function() {

            yasrMostOrHighestRatedChart ();

        });


    </script>

    <?php

    return $shortcode_html;


} //End function


/****** Add top 5 most active reviewer ******/

add_shortcode ('yasr_top_5_reviewers', 'yasr_top_5_reviewers_callback');

function yasr_top_5_reviewers_callback () {

    global $wpdb;

    $query_result = $wpdb->get_results("SELECT COUNT( reviewer_id ) as total_count, reviewer_id as reviewer
                                        FROM " . YASR_VOTES_TABLE . ", $wpdb->posts AS p
                                        WHERE  post_id = p.ID
                                        AND p.post_status = 'publish'
                                        GROUP BY reviewer_id
                                        ORDER BY (total_count) DESC
                                        LIMIT 5");


    if ($query_result) {

        $shortcode_html = "
        <table class=\"yasr-table-chart\">
        <tr>
         <th>Author</th>
         <th>Reviews</th>
        </tr>
        ";

        foreach ($query_result as $result) {

            $user_data = get_userdata($result->reviewer);

            if ($user_data) {

                $user_profile = get_author_posts_url($result->reviewer);

            }

            else {

                $user_profile = '#';
                $user_data = new stdClass;
                $user_data->user_login = 'Anonymous';
            
            }


            $shortcode_html .= "<tr>
                                    <td><a href=\"$user_profile\">$user_data->user_login</a></td>
                                    <td>$result->total_count</td>
                                </tr>";
                                
        }

        $shortcode_html .= "</table>";

        return $shortcode_html;

    }

    else {

        _e("Problem while retrieving the top 5 most active reviewers. Did you publish any review?");

    }


} //End top 5 reviewers function





/****** Add top 10 most active user *****/

add_shortcode ('yasr_top_ten_active_users', 'yasr_top_ten_active_users_callback');

function yasr_top_ten_active_users_callback () {

    global $wpdb;

    $query_result = $wpdb->get_results("SELECT COUNT( user_id ) as total_count, user_id as user
                                        FROM " . YASR_LOG_TABLE . ", $wpdb->posts AS p
                                        WHERE  post_id = p.ID
                                        AND p.post_status = 'publish'
                                        GROUP BY user_id 
                                        ORDER BY ( total_count ) DESC
                                        LIMIT 10");

    if ($query_result) {

        $shortcode_html = "
       <table class=\"yasr-table-chart\">
        <tr>
         <th>UserName</th>
         <th>Number of votes</th>
        </tr>
        ";

        foreach ($query_result as $result) {

            $user_data = get_userdata($result->user);

            if ($user_data) {

                $user_profile = get_author_posts_url($result->user);

            }

            else {
                $user_profile = '#';
                $user_data = new stdClass;
                $user_data->user_login = 'Anonymous';
            }

            $shortcode_html .= "<tr>
                                    <td><a href=\"$user_profile\">$user_data->user_login</a></td>
                                    <td>$result->total_count</td>
                                </tr>";

        }


        $shortcode_html .= "</table>";

        return $shortcode_html;

    }

    else {
        _e("Problem while retrieving the top 10 active users chart. Are you sure you have votes to show?");
    }


} //End function

?>

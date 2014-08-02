<?php 

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

    //To avoid double visualization, I will insert this only if auto insert is off or if auto insert is set on visitor rating.
    //If auto insert is on overall rating this shortcode must return nothing

    if (YASR_AUTO_INSERT_ENABLED == 0 || (YASR_AUTO_INSERT_ENABLED == 1 && YASR_AUTO_INSERT_WHAT === 'visitor_rating' )) {

        $overall_rating=yasr_get_overall_rating();

        if (!$overall_rating) {
            $overall_rating = "-1";
        }

        $shortcode_html = '';

        if (YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_BEFORE_OVERALL != '') {

            $shortcode_html = "<div class=\"yasr-container-custom-text-and-overall\">
                                    <span id=\"yasr-custom-text-before-overall\">" . YASR_TEXT_BEFORE_OVERALL . "</span>";

        }

        switch ($size) {
            case 'small':
                        $shortcode_html .= "<div class=\"rateit\" id=\"yasr_rateit_overall\" data-rateit-value=\"$overall_rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>";
                        break;

            case 'medium':
                        $shortcode_html .= "<div class=\"rateit medium\" id=\"yasr_rateit_overall\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$overall_rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>";
                        break;

            case 'large':
                        $shortcode_html .= "<div class=\"rateit bigstars\" id=\"yasr_rateit_overall\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$overall_rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>"; 
                        break;
        }


        if (YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_BEFORE_OVERALL != '') {

            $shortcode_html .= "</div>";
        
        }

        //IF show overall rating in loop is disabled use is_singular && is_main query
        if (YASR_SHOW_OVERALL_IN_LOOP === 'disabled') {

            if( is_singular() && is_main_query() ) {

                return $shortcode_html;

            }

        }

        //else don't
        elseif (YASR_SHOW_OVERALL_IN_LOOP === 'enabled') {

            return $shortcode_html;

        }

    } //end if auto insert enabled == 0

} //end function


/****** Add shortcode for user vote ******/

add_shortcode ('yasr_visitor_votes', 'shortcode_visitor_votes_callback');

function shortcode_visitor_votes_callback ($atts) {

    //To avoid double visualization, I will insert this only if auto insert is off or if auto insert is set on overall rating.
    //If auto insert is on visitor rating this shortcode must return nothing

    if (YASR_AUTO_INSERT_ENABLED == 0 || (YASR_AUTO_INSERT_ENABLED == 1 && YASR_AUTO_INSERT_WHAT === 'overall_rating' )) {

        $shortcode_html = NULL; //Avoid undefined variable outside is_singular && is_main_query

        if( is_singular() && is_main_query() ) {

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
                    if ($votes_number !=0 ) {
        			    $medium_rating = ($user_votes->sum_votes/$votes_number);
                    }
                }
            }


            $image = YASR_IMG_DIR . "/loader.gif";

            $loader_html = "<div id=\"loader-visitor-rating\" >&nbsp; " . __("Loading, please wait","yasr") . " <img src= \" $image \"></div>";

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

            if ($size == 'small') {

                //if anonymous are allowed to vote
                if (YASR_ALLOWED_USER === 'allow_anonymous') {

                    //I've to block a logged in user that has already rated
                    if ( is_user_logged_in() ) {

                        //Chek if a logged in user has already rated for this post
                        $vote_if_user_already_rated = yasr_check_if_user_already_voted();

                        //If user has already rated show readonly stars
                        if ($vote_if_user_already_rated) {

                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes_logged_rated\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span> 
                            <strong><span class=\"yasr-visitor-shortcode-small-already-voted-text\">" . __("You've already voted this article with $vote_if_user_already_rated ", "yasr") . "</span></strong></div>";

                        }

                        //else logged user can vote 
                        else {

                            $vote_if_user_already_rated = 0;

                            if ($votes_number>0) {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                            }

                            else {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                            }

                        } //End else

                    } //End if user is logged


                    //else if is not logged can vote
                    else {

                        if ($votes_number>0) {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                            <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                        }

                        else {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                            <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                        }

                    } //end else
              
                } //end if  ($allow_logged_option['allowed_user']==='allow_anonymous') {



                //If only logged in users can vote
                elseif (YASR_ALLOWED_USER === 'logged_only') {

                    //If user is logged in and can vote
                    if ( is_user_logged_in() ) {

                        //Chek if a logged in user has already rated for this post
                        $vote_if_user_already_rated = yasr_check_if_user_already_voted();

                        if ($vote_if_user_already_rated) {

                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes_logged_rated\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                            <strong><span=\"class=\"yasr-visitor-shortcode-small-already-voted-text\">\">" . __("You've already voted this article with $vote_if_user_already_rated ", "yasr") . "</span></strong></div>";

                        }

                        else {

                            if ($votes_number>0) {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                                </div>";
                            }

                            else {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                                </div>";
                            }

                        }

                    } //End if user is logged in

                  //Else mean user is not logged in
                    else {


                        if ($votes_number>0) {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                            " . __("You must sign to vote", "yasr") . "</div>";
                        }

                        else {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit\" id=\"yasr_rateit_visitor_votes\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text-small\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                            </div>";
                        }

                    }
      
                }

            } //End if $size == 'small'

            if ($size == 'medium') {

                //if anonymous are allowed to vote
                if (YASR_ALLOWED_USER === 'allow_anonymous') {

                    //I've to block a logged in user that has already rated
                    if ( is_user_logged_in() ) {

                        //Chek if a logged in user has already rated for this post
                        $vote_if_user_already_rated = yasr_check_if_user_already_voted();

                        //If user has already rated show readonly stars
                        if ($vote_if_user_already_rated) {

                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes_logged_rated\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span> 
                            <strong>" . __("You've already voted this article with $vote_if_user_already_rated ", "yasr") . "</strong></div>";

                        }

                        //else logged user can vote 
                        else {

                            $vote_if_user_already_rated = 0;

                            if ($votes_number>0) {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                            }

                            else {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                            }

                        } //End else

                    } //End if user is logged


                    //else if is not logged can vote
                    else {

                        if ($votes_number>0) {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                            <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                        }

                        else {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                            <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                        }

                    } //end else
              
                } //end if  ($allow_logged_option['allowed_user']==='allow_anonymous') {



                //If only logged in users can vote
                elseif (YASR_ALLOWED_USER === 'logged_only') {

                    //If user is logged in and can vote
                    if ( is_user_logged_in() ) {

                        //Chek if a logged in user has already rated for this post
                        $vote_if_user_already_rated = yasr_check_if_user_already_voted();

                        if ($vote_if_user_already_rated) {

                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes_logged_rated\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                            <strong>" . __("You've already voted this article with $vote_if_user_already_rated ", "yasr") . "</strong></div>";

                        }

                        else {

                            if ($votes_number>0) {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                                </div>";
                            }

                            else {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                                </div>";
                            }

                        }

                    } //End if user is logged in

                  //Else mean user is not logged in
                    else {


                        if ($votes_number>0) {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                            " . __("You must sign to vote", "yasr") . "</div>";
                        }

                        else {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit medium\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"24\" data-rateit-starheight=\"24\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text-medium\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                            </div>";
                        }

                    }
      
                }

            } //End if $size == 'medium'

            if ($size == 'large') {

                //if anonymous are allowed to vote
                if (YASR_ALLOWED_USER === 'allow_anonymous') {

                    //I've to block a logged in user that has already rated
                    if ( is_user_logged_in() ) {

                        //Chek if a logged in user has already rated for this post
                        $vote_if_user_already_rated = yasr_check_if_user_already_voted();

                        //If user has already rated show readonly stars
                        if ($vote_if_user_already_rated) {

                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes_logged_rated\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span> 
                            <strong>" . __("You've already voted this article with $vote_if_user_already_rated ", "yasr") . "</strong></div>";

                        }

                        //else logged user can vote 
                        else {

                            $vote_if_user_already_rated = 0;

                            if ($votes_number>0) {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                            }

                            else {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                            }

                        } //End else

                    } //End if user is logged


                    //else if is not logged can vote
                    else {

                        if ($votes_number>0) {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                            <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                        }

                        else {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                            <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span></div>";
                        }

                    } //end else
              
                } //end if  ($allow_logged_option['allowed_user']==='allow_anonymous') {



                //If only logged in users can vote
                elseif (YASR_ALLOWED_USER === 'logged_only') {

                    //If user is logged in and can vote
                    if ( is_user_logged_in() ) {

                        //Chek if a logged in user has already rated for this post
                        $vote_if_user_already_rated = yasr_check_if_user_already_voted();

                        if ($vote_if_user_already_rated) {

                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes_logged_rated\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                            <strong>" . __("You've already voted this article with $vote_if_user_already_rated ", "yasr") . "</strong></div>";

                        }

                        else {

                            if ($votes_number>0) {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                                </div>";
                            }

                            else {
                                $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\"></div>
                                <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                                </div>";
                            }

                        }

                    } //End if user is logged in

                  //Else mean user is not logged in
                    else {


                        if ($votes_number>0) {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                            " . __("You must sign to vote", "yasr") . "</div>";
                        }

                        else {
                            $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div>
                            <span class=\"yasr-total-average-text\"> [" . __("Total: ", "yasr") . "$votes_number &nbsp; &nbsp;" .  __("Average $medium_rating/5" , "yasr") . "]</span>
                            </div>";
                        }

                    }
      
                }

            } //End if $size == 'large'

            if(YASR_TEXT_BEFORE_STARS == 1 && YASR_TEXT_BEFORE_VISITOR_RATING != '') {
        
                $shortcode_html_tmp = "<div class=\"yasr-container-custom-text-and-visitor-rating\">
                <div id=\"yasr-custom-text-before-visitor-rating\">" . YASR_TEXT_BEFORE_VISITOR_RATING . "</div><br />" .  $shortcode_html . "</div>"; 

                $shortcode_html = $shortcode_html_tmp;

            }


          ?>

          <script>
            jQuery(document).ready(function() {

                var logged_message_showed = false; 

                logged_message_showed = jQuery("#yasr_rateit_visitor_votes_logged_rated").attr("data-rateit-value");

                if (logged_message_showed) {
                      logged_message_showed = true;
                }

                

                var tooltipvalues = ['bad', 'poor', 'ok', 'good', 'super'];
                jQuery("#yasr_rateit_visitor_votes").bind('over', function (event, value) { jQuery(this).attr('title', tooltipvalues[value-1]); });

                var postid = <?php the_ID(); ?>;
                var cookiename = "yasr_visitor_vote_" + postid;

                //If there is not cookie allow visitor to vote
                if (!jQuery.cookie(cookiename)) {

                    jQuery('#yasr_rateit_visitor_votes').on('rated', function() { 
                        var el = jQuery(this);
                        var value = el.rateit('value');
                        var value = value.toFixed(1); //
                        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

                        jQuery('#yasr_visitor_votes').html( ' <?php echo "$loader_html" ?> ');

                        var data = {
                            action: 'yasr_send_visitor_rating',
                            rating: value,
                            post_id: postid,
                            nonce_visitor: "<?php echo "$ajax_nonce_visitor"; ?>"
                        };

                        //Send value to the Server
                        jQuery.post(ajaxurl, data, function(response) {
                            jQuery('#yasr_visitor_votes').html(response); 
                            jQuery('.rateit').rateit();
                            //Create a cookie to disable double vote
                            jQuery.cookie(cookiename, value, { expires : 360 }); 
                        }) ;          
                    });
                } //End if (!jQuery.cookie(cookiename))

                //Else user cannot vote
                else {

                    //if php read only stars are not be showes echo readonly stars from cookie
                    if (!logged_message_showed) {

                        var cookievote=jQuery.cookie(cookiename);
                        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

                        var data = {
                            action: 'yasr_readonly_visitor_shortcode',
                            rating: cookievote,
                            votes: <?php echo $medium_rating ?>,
                            votes_number: <?php echo $votes_number ?>,
                            post_id: postid
                        }

                        jQuery.post(ajaxurl, data, function(response) {
                            jQuery('#yasr_visitor_votes').html(response);
                            jQuery('.rateit').rateit();
                        });

                    }

                } //End else !logged_user_already_rated)

          });

            </script>

     	    <?php

            return $shortcode_html;

        } //End if is singular

    } //End if auto_insert_enabled

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

        $shortcode_html = "<table class=\"yasr-top-10-highest-rated\">";

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

    $chart_type = 'most'; //default value;

    if (isset($_GET['yasr-order-by'])) {

        $chart_type = $_GET['yasr-order-by'];

        if ($chart_type != 'most' && $chart_type != 'highest') {

            $chart_type = 'most';

        }

    }

    global $wpdb;

    $post_url = get_permalink();

    if ($chart_type === 'most' ) {

        $query_result_most_rated = $wpdb->get_results("SELECT post_id, number_of_votes, sum_votes
                                            FROM " . YASR_VOTES_TABLE . ", $wpdb->posts AS p 
                                            WHERE post_id = p.ID
                                            AND p.post_status = 'publish'
                                            ORDER BY number_of_votes DESC, sum_votes DESC LIMIT 10");

        if ($query_result_most_rated) {

            $shortcode_html = "<div class=\"yasr-most-or-highest-rated-posts\" ></div>";

            foreach ($query_result_most_rated as $result) {

                $rating = $result->sum_votes / $result->number_of_votes;

                $rating = round($rating, 1);

                $post_title = get_the_title($result->post_id);

                $link = get_permalink($result->post_id); //Get permalink from post it

            } //End foreach

            ?>

            <script>

            jQuery(document).ready(function() {

                //Link do nothing
                jQuery('#yasr_multi_chart_link_to_nothing').on("click", function () {

                    return false; // prevent default click action from happening!

                });

                var data = {
                        action : 'yasr_multi_chart_most_highest' //declared in yasr-ajax-functions
                    };

                var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

                jQuery.post(ajaxurl, data, function(response) {

                    jQuery('.yasr-most-or-highest-rated-posts').html(response);
                    jQuery('.rateit').rateit();

                    //By default, hide the highest rated chart
                    jQuery('.yasr-highest-rated-posts').hide();

                    //On click on highest, hide most and show highest
                    jQuery('#yasr_multi_chart_highest').on("click", function () {

                        jQuery('.yasr-most-rated-posts').hide();

                        jQuery('.yasr-highest-rated-posts').show();

                        return false; // prevent default click action from happening!

                    });

                    //Vice versa
                    jQuery('#yasr_multi_chart_most').on("click", function () {

                        jQuery('.yasr-highest-rated-posts').hide();

                        jQuery('.yasr-most-rated-posts').show();

                        return false; // prevent default click action from happening!

                    });

                });

            });

            </script>

            <?php

            return $shortcode_html;

        } //end if $query_result

        else {
            _e("You don't have any user votes stored", "yasr");
        }

    }

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
        <table class=\"yasr-top-5-active-reviewer\">
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

        _e("Problem while retriving the top 5 most active reviewers. Did you published any review?");

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
        <table class=\"yasr-top-10-active-users\">
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
        _e("Problem while retriving the top 10 active users chart. Are you sure you have votes to show?");
    }


} //End function

?>
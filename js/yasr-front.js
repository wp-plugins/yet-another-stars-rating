/****** Yasr shortcode page ******/

    function yasrVisitorsVotes(tooltipValues, postid, ajaxurl, size, loggedUser, voteIfUserAlredyRated, votes, votesNumber, loaderHtml, nonceVisitor) {

        jQuery("#yasr_rateit_visitor_votes").bind('over', function (event, value) { jQuery(this).attr('title', tooltipValues[value-1]); });

        var cookiename = "yasr_visitor_vote_" + postid;

        if (voteIfUserAlredyRated == "0" ) {
            voteIfUserAlredyRated = false;
        }

        //If user is not logged in
        if (! loggedUser) {

            //Check if has cookie and if so print readonly visitor shortcode
            if (jQuery.cookie(cookiename)) {                

                var cookievote=jQuery.cookie(cookiename);

                var data = {
                    action: 'yasr_readonly_visitor_shortcode',
                    size: size,
                    rating: cookievote,
                    votes: votes,
                    votes_number: votesNumber,
                    post_id: postid
                }

                jQuery.post(ajaxurl, data, function(response) {
                    jQuery('#yasr_visitor_votes').html(response);
                    jQuery('.rateit').rateit();
                });

            } //End if jquery cookie

            //If not logged and not cookie allowed to voted
            else {
                yasrDefaultRatingShortcode ();
            }

        } //End if (!loggeduser)

        //else, if is a logged in user
        else {

            //Do this code only if he has rated yet
            //Check if has cookie or vote in db
            if (jQuery.cookie(cookiename) || voteIfUserAlredyRated != '') {

                jQuery('#yasr-rateit-visitor-votes-logged-rated').on('rated', function() {

                    var el = jQuery(this);
                    var value = el.rateit('value');
                    var value = value.toFixed(1); //

                    jQuery('#yasr_visitor_votes').html(loaderHtml);

                    var data = {
                            action: 'yasr_update_visitor_rating',
                            rating: value,
                            post_id: postid,
                            size: size,
                            nonce_visitor: nonceVisitor
                        };

                    //Send value to the Server
                    jQuery.post(ajaxurl, data, function(response) {
                        //response
                        jQuery('#yasr_visitor_votes').html(response); 
                        jQuery('.rateit').rateit();
                        //Create a cookie to disable double vote
                        jQuery.cookie(cookiename, value, { expires : 360 }); 
                    }) ;      

                });//End function update vote

            } //End if jquery cookie

            else if (!jQuery.cookie(cookiename) && voteIfUserAlredyRated == '') {

                yasrDefaultRatingShortcode ();

            }

        } //End else logged

        function yasrDefaultRatingShortcode () {

            //On click Insert visitor votes
            jQuery('#yasr_rateit_visitor_votes').on('rated', function() { 

                var el = jQuery(this);
                var value = el.rateit('value');
                var value = value.toFixed(1); //

                jQuery('#yasr_visitor_votes').html(loaderHtml);

                var data = {
                    action: 'yasr_send_visitor_rating',
                    rating: value,
                    post_id: postid,
                    size: size,
                    nonce_visitor: nonceVisitor
                };

                //Send value to the Server
                jQuery.post(ajaxurl, data, function(response) {
                    //response
                    jQuery('#yasr_visitor_votes').html(response); 
                    jQuery('.rateit').rateit();
                    //Create a cookie to disable double vote
                    jQuery.cookie(cookiename, value, { expires : 360 }); 
                }) ;          
            });

        } //End function default_rating_shortcode

    } //End function yasr visitor votes


    function yasrMostOrHighestRatedChart (ajaxurl) {

        //Link do nothing
        jQuery('#yasr_multi_chart_link_to_nothing').on("click", function () {

            return false; // prevent default click action from happening!

        });

        var data = {
                action : 'yasr_multi_chart_most_highest' //declared in yasr-ajax-functions
            };


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

    }

/****** End Yasr shortcode page  ******/
/****** Yasr shortcode page ******/

    function yasrVisitorsVotes (tooltipValues, postid, ajaxurl, size, loggedUser, voteIfUserAlredyRated, loaderHtml, nonceVisitor) {

        jQuery('#yasr_rateit_visitor_votes_' + postid).bind('over', function (event, value) { jQuery(this).attr('title', tooltipValues[value-1]); });

        //Should be useless from version 0.7.9, just to be safe
        if (voteIfUserAlredyRated == "0" ) {
            voteIfUserAlredyRated = false;
        }

        jQuery('#yasr_rateit_visitor_votes_' + postid).on('rated', function() {

            var el = jQuery(this);
            var value = el.rateit('value');
            var value = value.toFixed(1); //

            if (value < 1) {
                jQuery('#yasr_visitor_votes_' + postid).html('You can\'t vote 0');
            } 

            else {

                jQuery('#yasr_visitor_votes_' + postid).html(loaderHtml);

                //If loggedin user and has already rated for a post/page update the vote
                if (loggedUser && voteIfUserAlredyRated) {

                    var data = {
                        action: 'yasr_update_visitor_rating',
                        rating: value,
                        post_id: postid,
                        size: size,
                        nonce_visitor: nonceVisitor
                    };

                }

                //else is a new vote
                else {

                    var data = {
                        action: 'yasr_send_visitor_rating',
                        rating: value,
                        post_id: postid,
                        size: size,
                        nonce_visitor: nonceVisitor
                    };

                }

                //Send value to the Server
                jQuery.post(ajaxurl, data, function(response) {
                    //response
                    jQuery('#yasr_visitor_votes_' + postid).html(response); 
                    jQuery('.rateit').rateit();

                }) ;      

            } //End else value <1

        });//End function insert/update vote

    } //End function yasr visitor votes
   
    
    function yasrVisitorsMultiSet (postId, setType, ajaxurl, nonce) {

        //will have field id and vote
        var ratingObject = "";

        //an array with all the ratingonjects
        var ratingArray = new Array();

        jQuery('.yasr-visitor-multi-'+postId+'-'+setType).on('rated', function() { 
            var el = jQuery(this);
            var value = el.rateit('value');
            var value = value.toFixed(1); 
            var idField = el.attr('id');

            ratingObject = {

                field: idField,
                rating: value

            };

            //creating rating array
            ratingArray.push(ratingObject);

        });

        jQuery('#yasr-send-visitor-multiset-'+postId+'-'+setType).on('click', function() {

            jQuery('#yasr-send-visitor-multiset-'+postId+'-'+setType).hide();

            var cookiename = "yasr_multi_visitor_vote_" + postId+'_'+setType;

            jQuery('#yasr-loader-multiset-visitor-'+postId+'-'+setType).show();

            var data = {

                action: 'yasr_visitor_multiset_field_vote',
                nonce: nonce, 
                post_id: postId,
                rating: ratingArray,
                set_type: setType

            }

            //Send value to the Server
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('#yasr-loader-multiset-visitor-'+postId+'-'+setType).text(response);
            });

        });

    } //End function 


    function yasrMostOrHighestRatedChart (ajaxurl) {

        //By default, hide the highest rated chart
        jQuery('#yasr-highest-rated-posts').hide();

        //On click on highest, hide most and show highest
        jQuery('#yasr_multi_chart_highest').on("click", function () {

            jQuery('#yasr-most-rated-posts').hide();

            jQuery('#yasr-highest-rated-posts').show();

            return false; // prevent default click action from happening!

        });

        //Vice versa
        jQuery('#yasr_multi_chart_most').on("click", function () {

            jQuery('#yasr-highest-rated-posts').hide();

            jQuery('#yasr-most-rated-posts').show();

            return false; // prevent default click action from happening!

        });

    }


/****** End Yasr shortcode page  ******/


/****** Tooltip function ******/

    //used in ajax page
    function yasrDrawProgressBars (valueProgressbar, postId) {

        var i = null;

        var j = 0; //This is for the array

        for (i=5; i>0; i--) {

            jQuery( "#yasr-progress-bar-postid-"+postId+"-progress-bar-" + i).progressbar({
                value: valueProgressbar[j]
            });

            j=j+1;

        }
        
    }

    //used in shortcode page and ajax page
    function yasrDrawTipsProgress(postid, ajaxurl) {

        var varTipsContent = null;

        jQuery('#yasr-total-average-dashicon-' + postid).tooltip({

            position: { my: 'center bottom' , at: 'center top-10' },
            tooltipClass: "yasr-visitors-stats-tooltip",
            content: function(tipsContent) {

                if (!varTipsContent) {

                    var data = {
                        action: 'yasr_stats_visitors_votes',
                        post_id: postid
                    }

                    jQuery.post(ajaxurl, data, function(response) {
                        varTipsContent = response;
                        tipsContent(response);
                    });

                } 

                else {
                    return varTipsContent;
                }

            },
            disabled: true,
            close: function( event, ui ) { 
                jQuery(this).tooltip('disable'); 
            }

        });

        jQuery('#yasr-total-average-dashicon-' + postid).on("hover", function(){
            jQuery(this).tooltip('enable').tooltip('open');
            jQuery('.ui-helper-hidden-accessible').children(':first').removeAttr('style');
            jQuery('.ui-helper-hidden-accessible').children(':last', this).remove();
        });

    }



/****** End tooltipfunction ******/


/****** draw progress bar for yasr_pro_comment_reviews_summary ******/

    function yasrDrawProgressBarsReviewsSummery (valueProgressbar, postId) {

            var i = null;

            var j = 0; //This is for the array

            for (i=5; i>0; i--) {

                jQuery( "#yasr-pro-reviews-summary-postid-"+postId+"-progress-bar-" + i).progressbar({
                    value: valueProgressbar[j]
                });

                j=j+1;

            }
            
        }

/****** End progressbar function *******/


/****** Yasr pro shortcode page ******/

    function yasrProMostOrHighestRatedChart (view) {

        if (view != 'highest') {

            //By default, hide the highest rated chart
            jQuery('#yasr-pro-highest-rated-posts').hide();

            //On click on highest, hide most and show highest
            jQuery('#yasr-pro-multi-chart-highest').on("click", function () {

                jQuery('#yasr-pro-most-rated-posts').hide();

                jQuery('#yasr-pro-highest-rated-posts').show();

                return false; // prevent default click action from happening!

            });

            //Vice versa
            jQuery('#yasr-pro-multi-chart-most').on("click", function () {

                jQuery('#yasr-pro-highest-rated-posts').hide();

                jQuery('#yasr-pro-most-rated-posts').show();

                return false; // prevent default click action from happening!

            });

        }

        else {

            //By default, hide the most rated chart
            jQuery('#yasr-pro-most-rated-posts').hide();

            //On click on most, hide highest and show most
            jQuery('#yasr-pro-multi-chart-most').on("click", function () {

                jQuery('#yasr-pro-highest-rated-posts').hide();

                jQuery('#yasr-pro-most-rated-posts').show();

                return false; // prevent default click action from happening!

            });

            //Vice versa
            jQuery('#yasr-pro-multi-chart-highest').on("click", function () {

                jQuery('#yasr-pro-most-rated-posts').hide();

                jQuery('#yasr-pro-highest-rated-posts').show();

                return false; // prevent default click action from happening!

            });

        }

    }

/****** End Yasr pro shortcode page ******/
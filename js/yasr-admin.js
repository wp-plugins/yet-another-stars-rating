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

/****** Yasr Metabox overall rating ******/

    function yasrDisplayTopRightMetabox(defaultbox, postid, nonceOverall, nonceSnippet) {

        if (defaultbox == 'stars' ) { 

            yasrPrintEventSendOverallWithStars(postid, nonceOverall);             

        } //end if if (defaultbox == 'stars' )

        else if (defaultbox == 'numbers') {

           yasrPrintEventSendOverallWithNumbers(postid, nonceOverall);

        } //End else if (defaultbox == 'numbers')

        yasrSnippetSelect(postid, nonceSnippet);

    } //End function   yasr_display_metabox*/


    //This is for the stars
    function yasrPrintEventSendOverallWithStars(postid, nonce) {

        jQuery('#yasr_rateit_overall').on('rated', function() { 
            jQuery('#loader-overall-rating').show();
            var el = jQuery(this);
            var value = el.rateit('value');
            var value = value.toFixed(1); //

            var data = {
                action: 'yasr_send_overall_rating',
                nonce: nonce, 
                rating: value,
                post_id: postid
            };

            //Send value to the Server
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('#loader-overall-rating').hide();
                jQuery('#yasr_rateit_overall_value').text(response); 
            }) ;

        });

        jQuery('#yasr_rateit_overall').on('reset', function() { 
            jQuery('#loader-overall-rating').show();
            var el = jQuery(this);
            var value = '-1';

            var data = {
                action: 'yasr_send_overall_rating',
                nonce: nonce, 
                rating: value,
                post_id: postid
            };

            //Send value to the Server
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('#loader-overall-rating').hide();
                jQuery('#yasr_rateit_overall_value').text(response); 
            }) ;

        });

    }

    //This is for the numbers
    function yasrPrintEventSendOverallWithNumbers(postid, nonce) {

        var integer = jQuery('#yasr-vote-overall-numbers-int').val();

        if (integer == 5) {

                jQuery("#yasr-comma-between-select").hide();
                jQuery("#yasr-vote-overall-numbers-dec").hide();

            }

        jQuery('#yasr-vote-overall-numbers-int').on('change', function() {

            var integer = (this.value);

            if (integer == 5) {

                jQuery("#yasr-comma-between-select").hide();
                jQuery("#yasr-vote-overall-numbers-dec").hide();

            }

        });
        
        jQuery('#yasr-send-overall-numbers').on('click', function() {

            var integer = jQuery('#yasr-vote-overall-numbers-int').val();

            var decimal = jQuery('#yasr-vote-overall-numbers-dec').val();

            var value = integer + "." + decimal;

            var data = {
                action: 'yasr_send_overall_rating',
                nonce: nonce, 
                rating: value,
                post_id: postid
            };

            //Send value to the Server
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('#yasr-overall-numbers-saved-confirm').text(response);
            }) ;

            return false;
            preventDefault(); // same thing as above

        });

    }

    //Choose snippet
    function yasrSnippetSelect(postid, nonceSnippet) {

    	jQuery('#yasr-send-review-type').on('click', function() {

    		reviewtype = jQuery('#yasr-choose-reviews-types-list').val()

        	var data = {
        		action: 'yasr_insert_review_type',
        		reviewtype: reviewtype,
        		postid: postid,
        		nonce: nonceSnippet
        	}

        	jQuery.post(ajaxurl, data, function(response) {
                jQuery('#yasr-ajax-response-review-type').text(response);
            }) ;

        	return false;
        	preventDefault(); 

        });

    }


/****** End Yasr Metabox overall rating ******/


/****** Yasr Metabox Multiple Rating ******/
	
	function yasrDisplayMultiMetabox (nMultiSet, postid, nonceMulti, setId) {

		// --------------IF multiple set are found -------------------

        if (nMultiSet > 1) {

            jQuery('#yasr-button-select-set').on("click", function() {
              
                var data_id = { 
                    action: 'yasr_send_id_nameset',
                    set_id: jQuery('#select_set').val(),
                    post_id: postid
                }

              jQuery("#yasr-loader-select-multi-set").show();

                //Send value to the Server
                jQuery.post(ajaxurl, data_id, function(response) {
                    jQuery("#yasr-loader-select-multi-set").hide();
                    jQuery('#yasr_rateit_multi_rating').html(response);
                    jQuery('.rateit').rateit();

                    jQuery('.multi').on('rated', function() { 
                        var el = jQuery(this);
                        var value = el.rateit('value');
                        var value = value.toFixed(1); 
                        var idField = el.attr('id');
                        var setType = jQuery('#select_set').val();

                        jQuery("#yasr-loader-multi-set-field-"+idField).show();

                        var data = {
                            action: 'yasr_send_id_field_with_vote',
                            nonce: nonceMulti, 
                            rating: value,
                            post_id: postid,
                            id_field: idField,
                            set_type: setType
                        };

                        //Send value to the Server
                        jQuery.post(ajaxurl, data, function() {
                            jQuery("#yasr-loader-multi-set-field-"+idField).hide();
                        });
                    });


                    jQuery('.multi').on('reset', function() { 
                        var el = jQuery(this);
                        var value = '0';
                        var idField = el.attr('id');
                        var setType = jQuery('#select_set').val();

                        jQuery("#yasr-loader-multi-set-field-"+idField).show();

                        var data = {
                            action: 'yasr_send_id_field_with_vote',
                            nonce: nonceMulti, 
                            rating: value,
                            post_id: postid,
                            id_field: idField,
                            set_type: setType
                        };

                        //Send value to the Server
                        jQuery.post(ajaxurl, data, function() {
                            jQuery("#yasr-loader-multi-set-field-"+idField).hide();
                        });
                    });
                
                });

                return false; // prevent default click action from happening!
                e.preventDefault(); // same thing as above

            });

        }

        else if (nMultiSet == 1) {

            // --------------IF we're using just 1 set -------------------

            var data_id = { 
                action: 'yasr_send_id_nameset',
                set_id: setId,
                post_id: postid
            }
              
            //Send value to the Server
            jQuery.post(ajaxurl, data_id, function(response) {
                jQuery('#yasr_rateit_multi_rating').html(response);
                jQuery('.rateit').rateit();

                jQuery('.multi').on('rated', function() { 
                    var el = jQuery(this);
                    var value = el.rateit('value');
                    var value = value.toFixed(1); 
                    var idField = el.attr('id');

                    jQuery("#yasr-loader-multi-set-field-"+idField).show();

                    var data = {
                        action: 'yasr_send_id_field_with_vote',
                        nonce: nonceMulti, 
                        rating: value,
                        post_id: postid,
                        id_field: idField,
                        set_type: setId
                    };

                    //Send value to the Server
                    jQuery.post(ajaxurl, data, function() {
                      jQuery("#yasr-loader-multi-set-field-"+idField).hide();
                    });

                });

                jQuery('.multi').on('reset', function() { 
                    var el = jQuery(this);
                    var value = '0';
                    var idField = el.attr('id');
                    var setType = setId

                    jQuery("#yasr-loader-multi-set-field-"+idField).show();

                    var data = {
                        action: 'yasr_send_id_field_with_vote',
                        nonce: nonceMulti, 
                        rating: value,
                        post_id: postid,
                        id_field: idField,
                        set_type: setType
                    };

                      //Send value to the Server
                    jQuery.post(ajaxurl, data, function() {
                        jQuery("#yasr-loader-multi-set-field-"+idField).hide();
                    });

                });

            });

        } //End if set == 1


	} //end function yasrDisplayMultiMetabox

/****** End Yasr Metabox Multple Rating  ******/


/****** Yasr Settings Page ******/

	function YasrSettingsPage (activeTab, nMultiSet, autoInsertEnabled, nonceShortcodeOverall) {

		//-------------------General Settings Code---------------------

	   	if (activeTab == 'general_settings') {

	   		if (autoInsertEnabled == 0) {
	   			jQuery('.yasr-auto-insert-options-class').prop('disabled', true);
	   		}

			//First Div
			jQuery('#yasr_auto_insert_radio_on').on('click', function(){
				jQuery('.yasr-auto-insert-options-class').prop('disabled', false);
			});

			jQuery('#yasr_auto_insert_radio_off').on('click', function(){
				jQuery('.yasr-auto-insert-options-class').prop('disabled', true);
			});

			if (jQuery('#yasr_text_before_star_off').is(':checked')) {
				jQuery('.yasr-general-options-text-before').prop('disabled', true);
			}
			
			jQuery('#yasr_text_before_star_on').on('click', function(){

					jQuery('.yasr-general-options-text-before').prop('disabled', false);
					jQuery('#yasr-general-options-custom-text-before-overall').val('Our Score');
					jQuery('#yasr-general-options-custom-text-before-visitor').val('Our Reader Score');
                    jQuery('#yasr-general-options-custom-text-after-visitor').val('[Total: %total_count%  Average: %average%]');
					jQuery('#yasr-general-options-custom-text-already-rated').val('You have already voted this article with');

			});

			jQuery('#yasr_text_before_star_off').on('click', function(){
				jQuery('.yasr-general-options-text-before').prop('disabled', true);
			});

            jQuery('#yasr-doc-custom-text-link').on('click', function() {
                jQuery('#yasr-doc-custom-text-div').toggle('slow');
                return false;
            });

			jQuery('#yasr-snippet-explained-link').on('click', function () {
				jQuery('#yasr-snippet-explained').toggle('slow');
				return false; // prevent default click action from happening!
			});


			//Second div code

			//On click show proceed button
			jQuery('#import-gdstar').on('click', function() { 
				jQuery('#yasr-import-gdstar-div').toggle();
			});

			//On click begin step1
			jQuery('#import-button').on('click', function() {

				jQuery('#yasr-loader-importer').show();

				var data = { 
					action : 'yasr_import_step1'
				};

				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#yasr-loader-importer').hide();
					jQuery('#result-import').html(response);
				});

			}); //End step1

			jQuery('#result-import').on('click', '.yasr-result-step-1', function() {
				//Now we are going to prepare another ajax call to check if multiple set exists

				jQuery('#yasr-loader-importer2').show();

				var data = {
					action: 'yasr_import_multi_set'
				};
					
				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#yasr-loader-importer2').hide();
					jQuery('#result-import').append(response);
				});

			}); //End second ajax call */

			//Reload page after importing is done
			jQuery('#result-import').on('click', '.yasr-result-step-2', function() {
				location.reload(true);
			});

		} //End if general settings

		//--------------Multi Sets Page ------------------

		if (activeTab == 'manage_multi') {

			jQuery('#yasr-multi-set-doc-link').on('click', function() {
				jQuery('#yasr-multi-set-doc-box').toggle("slow");
			});

			jQuery('#yasr-multi-set-doc-link-hide').on('click', function() {
				jQuery('#yasr-multi-set-doc-box').toggle("slow");
			});

			if (nMultiSet == 1) { 

				var counter = jQuery("#yasr-edit-form-number-elements").attr('value');

		    	counter++;

				jQuery("#yasr-add-field-edit-multiset").on('click', function() {

					if(counter>9){
		           		jQuery('#yasr-element-limit').show();
		           		jQuery('#yasr-add-field-edit-multiset').hide();
		            	return false;
					}   
			 
					var newTextBoxDiv = jQuery(document.createElement('tr'))
		 
					newTextBoxDiv.html('<td colspan="2">Element #' + counter + ' <input type="text" name="edit-multi-set-element-' + counter + '" value="" ></td>');
		 
					newTextBoxDiv.appendTo("#yasr-table-form-edit-multi-set");
		 
		 			counter++;

				});


			} //End if ($n_multi_set == 1)

			if (nMultiSet > 1) { 

			    //If more then 1 set is used...
				jQuery('#yasr-button-select-set-edit-form').on("click", function() {
					    
				    var data = {
				    	action : 'yasr_get_multi_set',
				    	set_id : jQuery('#yasr_select_edit_set').val()
				    } 
				    
				    jQuery.post(ajaxurl, data, function(response) {
				    	jQuery('#yasr-multi-set-response').show();
	     				jQuery('#yasr-multi-set-response').html(response);
	     			});

	     			return false; // prevent default click action from happening!
	        		preventDefault(); // same thing as above

				});
		 
				jQuery(document).ajaxComplete(function(){

					var counter = jQuery("#yasr-edit-form-number-elements").attr('value');

			    	counter++;
		 
			    	jQuery("#yasr-add-field-edit-multiset").on('click', function() {
			 
						if(counter>9){
			           		jQuery('#yasr-element-limit').show();
			           		jQuery('#yasr-add-field-edit-multiset').hide();
			            	return false;
						}   
			 
						var newTextBoxDiv = jQuery(document.createElement('tr'))
			 
						newTextBoxDiv.html('<td colspan="2">Element #' + counter + ' <input type="text" name="edit-multi-set-element-' + counter + '" value="" ></td>');
			 
						newTextBoxDiv.appendTo("#yasr-table-form-edit-multi-set");
			 
			 			counter++;

			    	});
		 
		  		});

		  	} //End if ($n_multi_set > 1) 

            jQuery('#yasr-color-scheme-preview-link').on('click', function () {
                jQuery('#yasr-color-scheme-preview').toggle('slow');
                return false; // prevent default click action from happening!
            });

		} //end if active_tab=='manage_multi'


	}

    function YasrAsk5Stars(nonceHideAskRating) {

        //This will call an ajax action that set a site transite to hide
        //for a week the metabok
        jQuery('#yasr-ask-five-star-later').on("click", function(){

            jQuery('#yasr-ask-five-stars').hide();

            var data = { 
                action: 'yasr_hide_ask_rating_metabox',
                choose: 'hide',
                nonce: nonceHideAskRating

            };

            jQuery.post(ajaxurl, data);

        });


        //This will close the ask rating metabox forever
        jQuery('#yasr-ask-five-close').on("click", function(){

            jQuery('#yasr-ask-five-stars').hide();

            var data = { 
                action: 'yasr_hide_ask_rating_metabox',
                choose: 'close',
                nonce: nonceHideAskRating
            };

            jQuery.post(ajaxurl, data);

        });


    }

/****** End Yasr Settings Page ******/


/****** Yasr Ajax Page ******/


	// When click on chart chart hide tab-main and show tab-charts

	function yasrShortcodeCreator(nMultiSet) {

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
			    jQuery('#yasr-overall-choose-size').toggle('slow');
			});

			    jQuery('#yasr-overall-insert-small').on("click", function(){
			        var shortcode = '[yasr_overall_rating size="small"]';
			        // inserts the shortcode into the active editor
			        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			        // closes jqueryui
			        jQuery('#yasr-tinypopup-form').dialog('close');
			    });

			    jQuery('#yasr-overall-insert-medium').on("click", function(){
			        var shortcode = '[yasr_overall_rating size="medium"]';
			        // inserts the shortcode into the active editor
			        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			        // closes jqueryui
			        jQuery('#yasr-tinypopup-form').dialog('close');
			    });

			    jQuery('#yasr-overall-insert-large').on("click", function(){
			        var shortcode = '[yasr_overall_rating size="large"]';
			        // inserts the shortcode into the active editor
			        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			        // closes jqueryui
			        jQuery('#yasr-tinypopup-form').dialog('close');
			    });

			//Add shortcode for visitors rating
			jQuery('#yasr-visitor-votes').on("click", function(){
			    jQuery('#yasr-visitor-choose-size').toggle('slow');
			});

			    jQuery('#yasr-visitor-insert-small').on("click", function(){
			        var shortcode = '[yasr_visitor_votes size="small"]';   
			        // inserts the shortcode into the active editor
			        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			        // closes Thickbox
			        jQuery('#yasr-tinypopup-form').dialog('close');
			    });

			    jQuery('#yasr-visitor-insert-medium').on("click", function(){
			        var shortcode = '[yasr_visitor_votes size="medium"]';   
			        // inserts the shortcode into the active editor
			        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			        // closes Thickbox
			        jQuery('#yasr-tinypopup-form').dialog('close');
			    });

			    jQuery('#yasr-visitor-insert-large').on("click", function(){
			        var shortcode = '[yasr_visitor_votes size="large"]';   
			        // inserts the shortcode into the active editor
			        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			        // closes Thickbox
			        jQuery('#yasr-tinypopup-form').dialog('close');
			    });

			if (nMultiSet > 1) { 

			    //Add shortcode for multiple set
			    jQuery('#yasr-insert-multiset-select').on("click", function(){
			        var setType = jQuery("input:radio[name=yasr_tinymce_pick_set]:checked" ).val();
                    var visitorSet = jQuery("#yasr-allow-vote-multiset").is(':checked');

                    if (!visitorSet) {
			            
                        var shortcode = '[yasr_visitor_multiset setid=';

                    }

                    else {

                        var shortcode = '[yasr_multiset setid=';

                    }

			        shortcode += setType;
			        shortcode += ']';
			        // inserts the shortcode into the active editor
			        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			        // closes jqueryui
			        jQuery('#yasr-tinypopup-form').dialog('close');
			    });

			} //End if

			else if (nMultiSet==1) { 

			//Add shortcode for single set (if only 1 are found)
			    jQuery('#yasr-single-set').on("click", function(){
			        var setType = jQuery('#yasr-single-set').val();

                    var visitorSet = jQuery("#yasr-allow-vote-multiset").is(':checked');

                    if (!visitorSet) {
                        
                        var shortcode = '[yasr_visitor_multiset setid=';

                    }

                    else {

                        var shortcode = '[yasr_multiset setid=';

                    }

			        shortcode += setType;
			        shortcode += ']';
			        // inserts the shortcode into the active editor
			        tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			        // closes jqueryui
			        jQuery('#yasr-tinypopup-form').dialog('close');
			    });

			} //End elseif 

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

	} //End function

/****** End YAsr Ajax page ******/

/****** Yasr db functions ******/

    //Vote log
	jQuery(document).ready(function() {

		//Log
		jQuery('.yasr-log-pagenum').on('click', function() {

			jQuery('#yasr-loader-log-metabox').show();

			var data = { 
				action : 'yasr_change_log_page',
				pagenum: jQuery(this).val(),

			};

			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#yasr-loader-log-metabox').hide();
				jQuery('#yasr-log-container').html(response);
			});

		});

		jQuery(document).ajaxComplete(function() {

			jQuery('.yasr-log-page-num').on('click', function() {

				jQuery('#yasr-loader-log-metabox').show();

				var data = { 
					action : 'yasr_change_log_page',
					pagenum: jQuery(this).val(),
				};

				jQuery.post(ajaxurl, data, function(response) {
					jQuery('#yasr-log-container').html(response); //This will hide the loader gif too
				});

			});

		});

	});

/****** End yasr db functions ******/
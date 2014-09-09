<?php 

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

    $post_id=get_the_ID();

	$overall_rating=yasr_get_overall_rating($post_id);

	if (!$overall_rating) {
		$overall_rating = "-1";
	}


    //This is for the select
    if($overall_rating != '-1') {
        $overall_rating_array = explode(".", $overall_rating);
        $int = $overall_rating_array[0];
        $dec = $overall_rating_array[1];
    } 

    else {
        $int = 0;
        $dec = 0;
    }

    $ajax_nonce_overall = wp_create_nonce( "yasr_nonce_insert_overall_rating" );

    $ajax_nonce_switch = wp_create_nonce( "yasr_nonce_switch_overall_rating" );

?>


<div id="yasr-overall-container">


    <?php 

        if (YASR_METABOX_OVERALL_RATING == 'stars') {

            ?>

            <div id="yasr-vote-overall-stars-container">

                <div id="yasr-vote-overall-stars">

                    <span id="yasr-rateit-vote-overall-text">
                        <?php _e("Rate this article / item", "yasr"); ?> 
                    </span>

                    <div class="rateit bigstars" id="yasr_rateit_overall" data-rateit-starwidth="32" data-rateit-starheight="32" data-rateit-value="<?php echo $overall_rating ?>" data-rateit-step="0.1" data-rateit-resetable="true" data-rateit-readonly="false">
                    </div>

                    <div id="loader-overall-rating" style="display:none;" >&nbsp;<?php _e("Loading, please wait","yasr"); ?><img src="<?php echo YASR_IMG_DIR . "/loader.gif" ?>">
                    </div>

                    </p>

                    <div>

                        <span id="yasr_rateit_overall_value"></span>
                    
                        <?php 

                            //Show this message if auto insert is off or if auto insert is not set to show overall rating (so if it is set to visitor rating)
                            if( YASR_AUTO_INSERT_ENABLED == 0 || (YASR_AUTO_INSERT_ENABLED == 1 && YASR_AUTO_INSERT_WHAT === 'visitor_rating') ) {

                                echo "<div>";
                                  _e ("Remember to insert this shortcode <strong>[yasr_overall_rating]</strong> where you want to display this rating", "yasr");
                                echo "</div>";

                            }

                      ?>

                    </div>

                </div>

            </div> <!--End stars container-->

    <?php

        } //End if (YASR_METABOX_OVERALL_RATING == 'stars') {

        if (YASR_METABOX_OVERALL_RATING == 'numbers') {

            ?>

            <div id="yasr-vote-with-numbers-container">

                <div id="yasr-vote-with-numbers" >

                    <span id="yasr-rateit-vote-overall-text">
                        <?php _e("Rate this article / item", "yasr"); ?> 
                    </span>

                    <div id="yasr-vote-with-numbers-select-container">

                        <select name="yasr-vote-overall-numbers-int" id="yasr-vote-overall-numbers-int" class="yasr-vote-overall-numbers">

                            <?php

                            for ($i=0; $i<=5; $i++) {

                                if ($i == $int) {
                                    echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";
                                }

                                else {
                                    echo "<option value=\"$i\">$i</option>\n";
                                }
                            }

                            ?>

                        </select>

                        <span id="yasr-comma-between-select">,</span>

                        <select name="yasr-vote-overall-numbers-dec" id="yasr-vote-overall-numbers-dec" class="yasr-vote-overall-numbers">

                            <?php

                            for ($i=0; $i<=9; $i++) {
                                if ($i == $dec) {
                                    echo "<option value=\"$i\" selected=\"selected\">$i</option>\n";
                                }

                                else {
                                    echo "<option value=\"$i\">$i</option>\n";
                                }
                            }

                            ?>

                        </select>

                    </div>

                    <p>

                    <div>
                    
                        <?php 

                            //Show this message if auto insert is off or if auto insert is not set to show overall rating (so if it is set to visitor rating)
                            if( YASR_AUTO_INSERT_ENABLED == 0 || (YASR_AUTO_INSERT_ENABLED == 1 && YASR_AUTO_INSERT_WHAT === 'visitor_rating') ) {

                                echo "<div>";
                                  _e ("Remember to insert this shortcode <strong>[yasr_overall_rating]</strong> where you want to display this rating", "yasr");
                                echo "</div>";

                            }

                      ?>

                    </div>

                    <button href="#" class="button-delete" id="yasr-send-overall-numbers"><?php _e('Save Vote', 'yasr'); ?></button>

                    <p>

                    <span id="yasr-overall-numbers-saved-confirm"></span>

                </div>

            </div> <!--End numbers container-->

    <?php 

        }

    ?>

</div>

<!--Switcher-->
<script type="text/javascript">

    jQuery(document).ready(function() {

        var defaultbox = "<?php echo YASR_METABOX_OVERALL_RATING ?>";

        yasr_display_metabox(defaultbox);

        function yasr_display_metabox(defaultbox) {

            if (defaultbox == 'stars' ) { 

                yasr_print_event_send_overall_with_stars();             

            } //end if if (defaultbox == 'stars' )

            else if (defaultbox == 'numbers') {

                yasr_print_event_send_overall_with_numbers();

            } //End else if (defaultbox == 'numbers')

        } //End function   yasr_display_metabox*/


        //This is for the stars
        function yasr_print_event_send_overall_with_stars() {

            jQuery('#yasr_rateit_overall').on('rated', function() { 
                jQuery('#loader-overall-rating').show();
                var el = jQuery(this);
                var postid = <?php the_ID(); ?>;
                var value = el.rateit('value');
                var value = value.toFixed(1); //

                var data = {
                    action: 'yasr_send_overall_rating',
                    nonce: "<?php echo "$ajax_nonce_overall"; ?>", 
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
                var postid = <?php the_ID(); ?>;
                var value = '-1';

                var data = {
                    action: 'yasr_send_overall_rating',
                    nonce: "<?php echo "$ajax_nonce_overall"; ?>", 
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
        function yasr_print_event_send_overall_with_numbers() {
            
            jQuery('#yasr-send-overall-numbers').on('click', function() {

                var integer = jQuery('#yasr-vote-overall-numbers-int').val();

                var decimal = jQuery('#yasr-vote-overall-numbers-dec').val();

                var value = integer + "." + decimal;

                var data = {
                    action: 'yasr_send_overall_rating',
                    nonce: "<?php echo "$ajax_nonce_overall"; ?>", 
                    rating: value,
                    post_id: <?php the_ID(); ?>
                };

                //Send value to the Server
                jQuery.post(ajaxurl, data, function(response) {
                    jQuery('#yasr-overall-numbers-saved-confirm').text(response);
                }) ;

                return false;
                preventDefault(); // same thing as above

            });

        }

    }); //End document ready

</script>

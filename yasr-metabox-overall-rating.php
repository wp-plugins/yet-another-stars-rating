<?php 

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

	$overall_rating=yasr_get_overall_rating();

	if (!$overall_rating) {
		$overall_rating = "-1";
	}

	add_action( 'admin_footer', 'yasr_overall_rating_javascript' );


?>

<span id="yasr_rateit_actual_overall_rating">
    <?php _e("Rate this article / item", "yasr"); ?> 
</span>

<p>

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

<?php
   	function yasr_overall_rating_javascript() {

      $ajax_nonce_overall = wp_create_nonce( "yasr_nonce_insert_overall_rating" );

?>
	<script>
   		jQuery(document).ready(function() {

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
    					jQuery('#yasr_rateit_overall_value').text('You\'ve rated it: ' + value); 
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
    					jQuery('#yasr_rateit_overall_value').text('You\'ve reset the vote'); 
    				}) ;
   			  });

   		});
	</script>        


	<?php

		} //End yasr overall_rating_javascript

		//The callback function is called from plugin first page



	?>

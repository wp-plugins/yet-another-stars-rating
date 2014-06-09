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

</p>

	<div>

   	<span id="yasr_rateit_overall_value"></span>
	
  </div>

<?php
   	function yasr_overall_rating_javascript($nonce) {

      $ajax_nonce_overall = wp_create_nonce( "yasr_nonce_insert_overall_rating" );

?>
	<script>
   		jQuery(document).ready(function($) {
   			$('#yasr_rateit_overall').on('rated', function() { 
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
  				$.post(ajaxurl, data, function(response) {
  					 jQuery('#yasr_rateit_overall_value').text('You\'ve rated it: ' + value); 
  				}) ;
 			});

   			$('#yasr_rateit_overall').on('reset', function() { 
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
  				$.post(ajaxurl, data, function(response) {
  					 jQuery('#yasr_rateit_overall_value').text('You\'ve reset the vote'); 
  				}) ;
 			});

   		});
	</script>        


	<?php

		} //End yasr overall_rating_javascript

		//The callback function is called from plugin first page



	?>

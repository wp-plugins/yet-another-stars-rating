<?php 

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

$multi_set=yasr_get_multi_set();

$ajax_nonce_multi = wp_create_nonce( "yasr_nonce_insert_multi_rating" );

global $wpdb;

$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the the count number of rows of the last query

if ($n_multi_set>1) {

   _e("Choose wich set you want to use");

?>

      <br />
      <select id ="select_set">
        <?php foreach ($multi_set as $name) { ?>
    		    <option value="<?php echo $name->set_id ?>"><?php echo $name->set_name ?></option>
    	  <?php } //End foreach ?>
      </select>

      <button href="#" class="button-delete" id="yasr-button-select-set"><?php _e("Select"); ?></button>

      <span id="yasr-loader-select-multi-set" style="display:none;" >&nbsp;<img src="<?php echo YASR_IMG_DIR . "/loader.gif" ?>">
      </span>

      <script>

     // --------------IF multiple set are found -------------------


    jQuery('#yasr-button-select-set').on("click", function() {
      
      var postid = <?php the_ID(); ?>;

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
                nonce: "<?php echo "$ajax_nonce_multi"; ?>", 
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
                nonce: "<?php echo "$ajax_nonce_multi"; ?>", 
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

    </script>


    <?php

} //End if 

elseif ($n_multi_set==1) {
    foreach ($multi_set as $find_id) { 
        $set_id = $find_id->set_id;
    }

    ?>

    <script>
    // --------------IF we're using just 1 set -------------------
    jQuery( document ).ready(function() {

      var postid = <?php the_ID(); ?>;

      var data_id = { 
        action: 'yasr_send_id_nameset',
        set_id: <?php echo $set_id ?>,
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
                nonce: "<?php echo "$ajax_nonce_multi"; ?>", 
                rating: value,
                post_id: postid,
                id_field: idField,
                set_type: <?php echo $set_id ?>
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
              var setType = <?php echo $set_id ?>

              jQuery("#yasr-loader-multi-set-field-"+idField).show();

              var data = {
                action: 'yasr_send_id_field_with_vote',
                nonce: "<?php echo "$ajax_nonce_multi"; ?>", 
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
    });

    </script>

    <?php

} //End elseif ($n_multi_set==1)

    ?>

      <div>
          <p>
              <span id="yasr_rateit_multi_rating">

              </span>
          </p>
      </div>

<?php 

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

$multi_set=yasr_get_multi_set();

global $wpdb;

$n_multi_set = $wpdb->num_rows; //wpdb->num_rows always store the last of the last query

if ($n_multi_set>1) {

   _e("Choose wich set you want to use");

?>
  <br />
  <select id ="select_set">
    <?php foreach ($multi_set as $name) { ?>
		    <option value="<?php echo $name->set_id ?>"><?php echo $name->set_name ?></option>
	  <?php } //End foreach ?>
  </select>


<script>
  // --------------IF multiple set are found -------------------

jQuery('#select_set').on("change", function() {
  
  var postid = <?php the_ID(); ?>;

  var data_id = { 
    action: 'yasr_send_id_nameset',
    set_id: jQuery(this).val(),
    post_id: postid
  }

  //Send value to the Server
    jQuery.post(ajaxurl, data_id, function(response) {
      jQuery('#yasr_rateit_multi_rating').html(response);
      jQuery('.rateit').rateit();

      jQuery('.rateit').on('rated', function() { 
          var el = jQuery(this);
          var value = el.rateit('value');
          var value = value.toFixed(1); 
          var idField = el.attr('id');
          var setType = jQuery('#select_set').val();

          var data = {
            action: 'yasr_send_id_field_with_vote',
            rating: value,
            post_id: postid,
            id_field: idField,
            set_type: setType
          };

          //Send value to the Server
          jQuery.post(ajaxurl, data);
      });


      jQuery('.rateit').on('reset', function() { 
          var el = jQuery(this);
          var value = '0';
          var idField = el.attr('id');
          var setType = jQuery('#select_set').val();

          var data = {
            action: 'yasr_send_id_field_with_vote',
            rating: value,
            post_id: postid,
            id_field: idField,
            set_type: setType
          };

          //Send value to the Server
          jQuery.post(ajaxurl, data);
      });
      
    });
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

      jQuery('.rateit').on('rated', function() { 
          var el = jQuery(this);
          var value = el.rateit('value');
          var value = value.toFixed(1); 
          var idField = el.attr('id');
          var setType = jQuery('#select_set').val();

          var data = {
            action: 'yasr_send_id_field_with_vote',
            rating: value,
            post_id: postid,
            id_field: idField,
            set_type: <?php echo $set_id ?>
          };

          //Send value to the Server
          jQuery.post(ajaxurl, data);

      });

      jQuery('.rateit').on('reset', function() { 
          var el = jQuery(this);
          var value = '0';
          var idField = el.attr('id');
          var setType = <?php echo $set_id ?>

          var data = {
            action: 'yasr_send_id_field_with_vote',
            rating: value,
            post_id: postid,
            id_field: idField,
            set_type: setType
          };

          //Send value to the Server
          jQuery.post(ajaxurl, data);
      });

    });
});

</script>

<?php

} //End elseif ($n_multi_set==1)

?>

<div>
      <p>
          <span id="yasr_rateit_multi_rating"></span>
      </p>
</div>
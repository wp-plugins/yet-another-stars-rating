<?php 

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

$multi_set=yasr_get_multi_set();

$ajax_nonce_multi = wp_create_nonce( "yasr_nonce_insert_multi_rating" );

$set_id=NULL;

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

    <?php 

} //End if if ($n_multi_set>1)

elseif ($n_multi_set==1) {

        foreach ($multi_set as $set) {
            
            $set_id = $set->set_id;

        }

}


?>

    <script type="text/javascript">

        jQuery(document).ready(function() {

            var nMultiSet = <?php echo (json_encode("$n_multi_set")); ?>

            var postid = <?php echo (the_ID()); ?>

            var nonceMulti = <?php echo (json_encode("$ajax_nonce_multi")); ?>

            if (nMultiSet == 1) {

                var setId = <?php echo (json_encode("$set_id")); ?>

            }

            else {

                var setID = false;

            }

            yasrDisplayMultiMetabox (nMultiSet, postid, nonceMulti, setId)


        }); //End document ready

    </script>

      <div>
          <p>
              <span id="yasr_rateit_multi_rating">

              </span>
          </p>
      </div>

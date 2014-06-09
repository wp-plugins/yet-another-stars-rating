<?php 

if ( ! defined( 'ABSPATH' ) ) exit('You\'re not allowed to see this page'); // Exit if accessed directly

/****** Add shortcode for overall rating ******/

add_shortcode ('yasr_overall_rating', 'shortcode_overall_rating_callback');

function shortcode_overall_rating_callback () {
	$overall_rating=yasr_get_overall_rating();

	if (!$overall_rating) {
		$overall_rating = "-1";
	}

	$shortcode_html="<div class=\"rateit bigstars\" id=\"yasr_rateit_overall\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$overall_rating\" data-rateit-step=\"0.1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\">
 	</div>";

 	return $shortcode_html;

} 

/****** Add shortcode for user vote ******/

add_shortcode ('yasr_visitor_votes', 'shortcode_visitor_votes_callback');

function shortcode_visitor_votes_callback () {

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

	$medium_rating=round($medium_rating, 1);

  if ($votes_number>0) {
	    $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"$medium_rating\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\">
	    </div><br />Rating $medium_rating / 5 ($votes_number votes casts)</div>";
  }
  else {
      $shortcode_html="<div id=\"yasr_visitor_votes\"><div class=\"rateit bigstars\" id=\"yasr_rateit_visitor_votes\" data-rateit-starwidth=\"32\" data-rateit-starheight=\"32\" data-rateit-value=\"0\" data-rateit-step=\"1\" data-rateit-resetable=\"false\" data-rateit-readonly=\"false\">
      </div><br />No rating yet</div>";
  }

	?>

	<script>
	jQuery(document).ready(function() {

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

   				      var data = {
   					        action: 'yasr_send_visitor_rating',
   					        rating: value,
   					        post_id: postid
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
        } //End else

  });

 	</script>

 	<?php
 	return $shortcode_html;
}


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
        	$shortcode_html .=  "<tr> <td>$set_content->name </td>
      		   					 <td><div class=\"rateit\" id=\"$set_content->id\" data-rateit-value=\"$set_content->vote\" data-rateit-step=\"0.5\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div></td>
        						 </tr>";
        }
    	$shortcode_html.="</table>";
    }

    //If there is not vote for that set...(it should always be there, because when adding new post all set are initialized to -1)
    else {
    	$set_name=$wpdb->get_results("SELECT field_name AS name, field_id AS id
                    FROM " . YASR_MULTI_SET_FIELDS_TABLE . "  
                    WHERE parent_set_id=$setid 
                    ORDER BY field_id ASC");
    	$shortcode_html="<table>";
     	foreach ($set_name as $set_content) {
        	$shortcode_html .=  "<tr> <td>$set_content->name </td>
      		   					 <td><div class=\"rateit\" id=\"$set_content->id\" data-rateit-value=\"0\" data-rateit-step=\"0.5\" data-rateit-resetable=\"false\" data-rateit-readonly=\"true\"></div></td>
        						 </tr>";
        }
    	$shortcode_html.="</table>";
    	
    }
	return $shortcode_html;
	} //End function
?>
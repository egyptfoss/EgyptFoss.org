<?php

/**
 * Template Name: Display Quiz Result
 *
 * @package egyptfoss
 */

$result_id = efGetValueFromUrlByKey("result");
$result = ef_returnResult($result_id);
if($result == "404")
{
  //Not Found
  include( get_query_template( '404' ) );
  exit;  
}else if($result == "500")
{
  //cheating
  echo __("you are trying to cheat",'egyptfoss');
  exit;
}

//only owner of quiz can see result
if(get_current_user_id() != $result['user_id'])
{
  include( get_query_template( '404' ) );
  exit;  
}

get_header();

global $post;
$post = get_post($result["post_id"]);
setup_postdata( $post ); 
$post->post_title = sprintf(__("I got %s in the %s Quiz on #EgyptFOSS","egyptfoss"), $result['score']."%"
        ,$result['quiz_title']);


?>
<script>
  jQuery( document ).ready(function() {
    jQuery('.heateorSssFacebookBackground').removeAttr('onclick');
    jQuery('.heateorSssGoogleplusBackground').removeAttr('onclick');
    jQuery('.heateorSssLinkedinBackground').removeAttr('onclick');
    
    jQuery(".heateorSssFacebookBackground").click(function(){
        var shareUrl = encodeURIComponent("<?php echo $post->guid; ?>");
        var shareTitle = encodeURIComponent("<?php echo $post->post_title; ?>");
        var shareDescription  = encodeURIComponent("<?php echo __("Take this quiz to test your FOSS knowledge","egyptfoss"); ?>");
        heateorSssPopup("https://www.facebook.com/sharer/sharer.php?u=" + shareUrl + "&title=" + shareTitle + "&description=" + shareDescription);
        return false;
    });
    
    jQuery(".heateorSssGoogleplusBackground").click(function(){
        var shareUrl = encodeURIComponent("<?php echo home_url()."/awareness-center/".$post->post_name; ?>");
        var shareTitle = encodeURIComponent("<?php echo $post->post_title; ?>");
        heateorSssPopup("https://plus.google.com/share?url=" + shareUrl + "&title=" + shareTitle);
        return false;
    });
    
    jQuery(".heateorSssLinkedinBackground").click(function(){
        var shareUrl = encodeURIComponent("<?php echo $post->guid; ?>");
        var shareTitle = encodeURIComponent("<?php echo $post->post_title; ?>");
        var shareDescription  = encodeURIComponent("<?php echo __("Take this quiz to test your FOSS knowledge","egyptfoss"); ?>");
        heateorSssPopup("http://www.linkedin.com/shareArticle?mini=true&url=" + shareUrl + "&title=" + shareTitle + "&summary=" + shareDescription);
        return false;
    });
  });
</script>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
          <h1> <?php echo $result['quiz_title']; ?></h1>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->
<div class="container">
    <div class="row">
        <div class="col-md-12 content-area">
           
           <div class="quiz-results text-center">
               <?php echo html_entity_decode($result['message']); ?>
               <p><?php _e("Your Score:","egyptfoss"); echo " ".$result["score"]; ?>%</p>
               <p><?php _e("Share your score on","egyptfoss"); ?></p>
               <?php echo do_shortcode('[Sassy_Social_Share]');?>
           </div>
              <!--<div class="quiz-results negative text-center">
               <img src="<?php echo get_template_directory_uri(); ?>/img/negative.png" alt="">
               <br>
               <h3>Sorry! You didn't pass the quiz, Try Again</h3>
               <p>Your Score: <?php echo $result["score"]; ?> %</p>
               
           </div>-->
        </div>
    </div>

<?php get_footer();

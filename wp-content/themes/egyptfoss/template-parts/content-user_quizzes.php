<?php
$args = array("no_of_posts" => constant("ef_user_quizzes_per_page"),
              "offset" => (get_query_var('user_quizzes_offset') ? get_query_var('user_quizzes_offset') : 0));
         
$results = get_user_quizzes_taken($args);
foreach ($results as $post)
{
  setup_postdata($post->ID);
  $quiz_post = get_post($post->ID);
?>
<div class="survey-item clearfix">
   <div class="quiz-thumb">
        <img src="<?php echo get_template_directory_uri(); ?>/img/quiz.svg" alt="<?php echo $post->quiz_name; ?>">
   </div>
    <div class="survey-title">
        <h4><a href="<?php echo home_url(pll_current_language())."/awareness-center/$quiz_post->post_name/"; ?>"><?php echo $post->quiz_name; ?></a></h4>
        <small class="trials-label"><i class="fa fa-repeat"></i> <?php _e('Trials','egyptfoss') ?>: <?php echo $post->taken; ?></small>
        <br>
        <a href="<?php echo home_url()."/awareness-center/quiz/result/$post->latest_id/" ?>">
        <small><img src="<?php echo get_template_directory_uri(); ?>/img/success_rate.svg" class="success-rate" alt="<?php _e("Success Rate","egyptfoss");?>"> 
        <?php _e("Latest Score","egyptfoss"); ?>: </small></a>
        <small><?php echo $post->latest_score; ?>%</small>
          <small class="post-date"> <i class="fa fa-clock-o"></i> <?php echo mysql2date('d F Y', $post->latest_date); ?></small>
           <br>
           <a href="<?php echo home_url()."/awareness-center/quiz/result/$post->highest_id/" ?>">
            <small>
            <img src="<?php echo get_template_directory_uri(); ?>/img/success_rate.svg" class="success-rate" alt="<?php _e("Success Rate","egyptfoss");?>">
            <?php _e("Highest Score","egyptfoss"); ?>: </small></a>
             <small><?php echo $post->highest_score; ?>%</small>
            <small class="post-date"><i class="fa fa-clock-o"></i> <?php echo mysql2date('d F Y', $post->highest_date); ?></small>
    </div>
    <div class="survey-stats">
         
        <a href="<?php echo home_url(pll_current_language())."/awareness-center/$quiz_post->post_name/"; ?>" class="btn btn-outline btn-sm rfloat"><?php _e("Try Again","egyptfoss"); ?></a>

       
    </div>
</div>
<?php
 }

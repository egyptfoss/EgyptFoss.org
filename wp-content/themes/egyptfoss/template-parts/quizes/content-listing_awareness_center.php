<?php 
$category = '-1';
if(get_query_var('ef_listing_awareness_center_category_id'))
{
    $category = get_query_var('ef_listing_awareness_center_category_id');
}

$args = array(
    'lang' => pll_current_language(),
    'current_user' => get_current_user_id(),
    'category' => $category
);
$list_quizes = list_quizes($args);
if(count($list_quizes) > 0 ) {
foreach ($list_quizes as $quiz) {
 ?>
<div class="survey-item clearfix">
   <div class="quiz-thumb">
     <img src="<?php echo get_template_directory_uri(); ?>/img/quiz.svg" alt="<?php echo $quiz->quiz_name; ?>">
   </div>
    <div class="survey-title">
        <h3>
            <?php if (is_user_logged_in() && !current_user_can('add_new_ef_posts')) { ?>
            <a href="javascript:void(0)" class="disabled" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><?php echo $quiz->quiz_name; ?></a>
            <?php } else { ?>
            <a class="listed-quizzes" href="<?php echo get_post_permalink($quiz->ID); ?>"><?php echo $quiz->quiz_name; ?></a> 
            <?php } ?>
        </h3>
        <span class="post-date story-meta">
            <span class="quiz-publish-date"><i class="fa fa-clock-o"></i> <?php echo mysql2date('d F Y', $quiz->post_date); ?></span>
            <span><?php if($quiz->quiz_taken > 0) { ?> <img src="<?php echo get_template_directory_uri(); ?>/img/success_rate.svg" class="success-rate" alt="<?php _e("Success Rate","egyptfoss");?>"> <?php _e("Success Rate:","egyptfoss");echo " ".round(($quiz->success_rate/$quiz->quiz_taken) * 100)."%";} ?></span>
        </span>
        <br/>
        <?php $interests = get_field('interest', $quiz->ID, $format_value = true);
        if ( ! empty( $interests ) ) { ?>
          <?php foreach ( $interests as $interest_id) :
            $interest = get_term( $interest_id, 'interest' );?>
            <span class="interest-tag">
              <?php _e("$interest->name", "egyptfoss"); ?>
            </span>
          <?php endforeach; ?>
        <?php } ?>
    </div>
    <div class="survey-stats text-center">
            <?php if($quiz->taken == 0) { ?>
            <?php if (is_user_logged_in() && !current_user_can('add_new_ef_posts')) { ?>
              <a href="javascript:void(0)" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"> <?php echo __("Take Quiz","egyptfoss"); ?></a>
            <?php } else { ?>
            <a class="btn btn-primary" href="<?php echo get_post_permalink($quiz->ID); ?>" ><?php _e("Take Quiz","egyptfoss"); ?></a>
            <?php } 
            } else { ?>
            <?php if (is_user_logged_in() && !current_user_can('add_new_ef_posts')) { ?>
            <a href="javascript:void(0)" class="btn btn-outline disabled" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><?php _e("Try Again","egyptfoss"); ?></a>
            <?php } else { ?>
            <a href="<?php echo get_post_permalink($quiz->ID); ?>" class="btn btn-outline" ><?php _e("Try Again","egyptfoss"); ?></a>
            <?php } ?>
            <br>
            <span class="num"><?php echo $quiz->highest_score."%"; ?></span>
            <br>
            <small><?php _e('Your Highest Score','egyptfoss'); ?></small>
            <?php } ?>
    </div>
</div>
<?php } 
} else {
  ?>
    <div class="row empty-state">
        <div class="empty-state-msg">
            <i class="fa fa-question-circle fa-4x"></i>
            <br>
            <h3><?php
              if($category == -1) {
                _e("There are no quizzes","egyptfoss"); 
              }else {
                _e("There are no quizzes in this category","egyptfoss"); 
              }
            ?></h3>
        </div>
    </div>
<?php }
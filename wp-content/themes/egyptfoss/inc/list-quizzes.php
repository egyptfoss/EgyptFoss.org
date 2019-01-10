<?php
define("ef_user_quizzes_per_page", 10);

function profile_quizzes_item() {
	global $bp;
	bp_core_new_nav_item(
		array(
			'name' => __('Quizzes', 'egyptfoss'),
			'slug' => 'quizzes',
			'position' => 30,
			'default_subnav_slug' => '/',
			'screen_function' => 'listing_quizzes'
		)
	);
}
add_action('bp_setup_nav', 'profile_quizzes_item', 302 );

function listing_quizzes(){
	add_action( 'bp_template_content', 'listing_user_quizzes_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function listing_user_quizzes_content() {
	bp_get_template_part( 'members/single/quizzes' );
}

function get_user_quizzes_taken_count()
{
  global $wpdb;
  $sql = "select distinct `{$wpdb->prefix}mlw_quizzes`.`quiz_id`
    from `{$wpdb->prefix}mlw_quizzes`
    inner join `{$wpdb->prefix}postmeta` as `pmeta` on `pmeta`.`meta_value` = `{$wpdb->prefix}mlw_quizzes`.`quiz_id` 
    inner join `{$wpdb->prefix}posts` as `post` on `post`.`ID` = `pmeta`.`post_id` ";
   
  //select of user taken
  $sql.= " inner join `{$wpdb->prefix}mlw_results` as results on results.quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id` and `results`.deleted = 0 and `results`.user = ".get_current_user_id();
  
  $sql.= " where  `{$wpdb->prefix}mlw_quizzes`.`deleted` = 0 
  and `pmeta`.`meta_key` = 'quiz_id'
  and `post`.`post_status` = 'publish' and `post`.`post_type` = 'quiz'";

  $sql .= " group by `{$wpdb->prefix}mlw_quizzes`.`quiz_id` ";
  $results = $wpdb->get_results($sql);
  return $results;
}

function get_user_quizzes_taken($args)
{
  $no_of_posts = $args['no_of_posts'];
  $set_minimum_score = returnAwarenessCenterMinimumScore();
  
  global $wpdb;
  $sql = "select distinct `{$wpdb->prefix}mlw_quizzes`.`quiz_id`, `{$wpdb->prefix}mlw_quizzes`.`quiz_name`, `post`.`ID`,
    `post`.`post_date`, count(`results`.`quiz_id`) taken,
    `{$wpdb->prefix}mlw_quizzes`.`quiz_taken`, 
    highest_result.result_id as highest_id,highest_result.correct_score as highest_score, highest_result.time_taken_real as highest_date,
    latest_result.result_id as latest_id,latest_result.correct_score as latest_score, latest_result.time_taken_real as latest_date,
    (select ifnull(count(*),-1) from `{$wpdb->prefix}mlw_results` as success_rate_results  where success_rate_results.quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id`
    and success_rate_results.deleted = 0 and success_rate_results.correct_score >= {$set_minimum_score}) as success_rate
    from `{$wpdb->prefix}mlw_quizzes`
    inner join `{$wpdb->prefix}postmeta` as `pmeta` on `pmeta`.`meta_value` = `{$wpdb->prefix}mlw_quizzes`.`quiz_id` 
    inner join `{$wpdb->prefix}posts` as `post` on `post`.`ID` = `pmeta`.`post_id` ";
   
  //select of user taken
  $sql.= " inner join `{$wpdb->prefix}mlw_results` as highest_result on highest_result.quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id` and `highest_result`.deleted = 0 and `highest_result`.user = ".  get_current_user_id();
  $sql.= " inner join `{$wpdb->prefix}mlw_results` as latest_result on latest_result.quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id` and `latest_result`.deleted = 0 and `latest_result`.user = ".  get_current_user_id();
  $sql.= " inner join `{$wpdb->prefix}mlw_results` as results on results.quiz_id = `{$wpdb->prefix}mlw_quizzes`.`quiz_id` and `results`.deleted = 0 and `results`.user = ".get_current_user_id();
  
  $sql.= " where  `{$wpdb->prefix}mlw_quizzes`.`deleted` = 0 
  and `pmeta`.`meta_key` = 'quiz_id'
  and `post`.`post_status` = 'publish' and `post`.`post_type` = 'quiz' "
  . " and `latest_result`.result_id = 
  (SELECT result_id FROM `{$wpdb->prefix}mlw_results` where `{$wpdb->prefix}mlw_results`.quiz_id = `{$wpdb->prefix}mlw_quizzes`.quiz_id and `{$wpdb->prefix}mlw_results`.deleted = 0 and `{$wpdb->prefix}mlw_results`.user = ".  get_current_user_id()." order by `{$wpdb->prefix}mlw_results`.result_id desc limit 1)"
  . " and `highest_result`.correct_score = 
  (SELECT max(correct_score) FROM `{$wpdb->prefix}mlw_results` where `{$wpdb->prefix}mlw_results`.quiz_id = `{$wpdb->prefix}mlw_quizzes`.quiz_id and `{$wpdb->prefix}mlw_results`.deleted = 0 and `{$wpdb->prefix}mlw_results`.user = ".get_current_user_id().")";

  $sql .= " group by `{$wpdb->prefix}mlw_quizzes`.`quiz_id` order by `{$wpdb->prefix}mlw_quizzes`.`quiz_id` desc limit {$args['offset']}, {$no_of_posts} ";
  $results = $wpdb->get_results($sql);
  return $results;
}

function ef_load_more_user_quizzes_taken() {
  set_query_var('user_quizzes_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_quizzes');
  die();
}
add_action('wp_ajax_ef_load_more_user_quizzes_taken', 'ef_load_more_user_quizzes_taken');
add_action('wp_ajax_nopriv_ef_load_more_user_quizzes_taken', 'ef_load_more_user_quizzes_taken');

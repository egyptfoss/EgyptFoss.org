<?php
define( "ef_user_service_per_page", 10 );

// load more requests
function ef_load_more_user_service() {
  set_query_var('user_service_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_service_requests');
  die();
}
add_action('wp_ajax_ef_load_more_user_service', 'ef_load_more_user_service');
add_action('wp_ajax_nopriv_ef_load_more_user_service', 'ef_load_more_user_service');

// load more responses
function ef_load_more_user_service_responses() {
  set_query_var('user_service_responses_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_service_responses');
  die();
}
add_action('wp_ajax_ef_load_more_user_service_responses', 'ef_load_more_user_service_responses');
add_action('wp_ajax_nopriv_ef_load_more_user_service_responses', 'ef_load_more_user_service_responses');
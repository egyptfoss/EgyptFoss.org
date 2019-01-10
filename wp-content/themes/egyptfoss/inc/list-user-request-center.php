<?php
define("ef_user_request_center_per_page", 20);
add_action('bp_setup_nav', 'ef_add_request_center_tab', 306);
function ef_add_request_center_tab() {
    global $bp;
    bp_core_new_subnav_item( array(
            'name' => __('Request Center', 'egyptfoss'),
            'slug' => 'request-center',
            'parent_url' => $bp->displayed_user->domain . $bp->bp_nav['contributions']['slug'] . '/' ,
            'parent_slug' => $bp->bp_nav['contributions']['slug'],
            'position' => 10,
            'screen_function' => 'listing_user_request_center' //the function is declared below
        )
    );
}

function listing_user_request_center(){
    add_action( 'bp_template_content', 'listing_user_request_center_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function listing_user_request_center_content() {
    bp_get_template_part( 'members/single/request_center' );
}

function ef_load_more_user_request_center() {
  set_query_var('user_request_center_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_request_center_requests');
  die();
}
add_action('wp_ajax_ef_load_more_user_request_center', 'ef_load_more_user_request_center');
add_action('wp_ajax_nopriv_ef_load_more_user_request_center', 'ef_load_more_user_request_center');

function get_user_request_center_requests($args)
{
    if($args['post_status'] == "")
    {
       $whereCondition = " where (post.post_status = 'pending' or post.post_status = 'publish' or post.post_status = 'archive') and (post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
    }else
    {
       $whereCondition = " where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
    }
    global $wpdb;

    $sql = "(SELECT post.* FROM {$wpdb->prefix}posts as post 
         {$whereCondition}
        group by post.ID order by post.post_date DESC 
        limit {$args['offset']},{$args['no_of_posts']}
         )";      
    return $wpdb->get_results($sql);  
}

function get_user_request_center_count($args)
{
    if($args['post_status'] == "")
    {
         $whereCondition = " where (post.post_status = 'pending' or post.post_status = 'publish' or post.post_status = 'archive') and (post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
    }else
    {
       $whereCondition = " where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
    }

    global $wpdb;
    $sql = "(SELECT post.* FROM {$wpdb->prefix}posts as post 
          {$whereCondition }
          group by post.ID
           )";
    return $wpdb->get_results($sql);  
}

function ef_load_more_user_request_center_responses() {
  set_query_var('user_request_center_responses_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_request_center_responses');
  die();
}
add_action('wp_ajax_ef_load_more_user_request_center_responses', 'ef_load_more_user_request_center_responses');
add_action('wp_ajax_nopriv_ef_load_more_user_request_center_responses', 'ef_load_more_user_request_center_responses');

function get_user_request_center_responses($args)
{
    global $wpdb;

    $sql = "(SELECT post.*  FROM {$wpdb->prefix}posts as post 
        join {$wpdb->prefix}request_threads as resp on resp.request_id = post.ID
         where (post.post_status = 'publish' or post.post_status = 'archive') and post.post_type = '{$args['post_type']}'
           and resp.user_id = {$args['author']} and resp.responses_count > 0
        group by post.ID order by resp.updated_at DESC 
        limit {$args['offset']},{$args['no_of_posts']}
         )";      
    return $wpdb->get_results($sql); 
    
}

function get_user_request_center_responses_count($args)
{
  global $wpdb;
  $sql = "(SELECT post.*  FROM {$wpdb->prefix}posts as post 
  join {$wpdb->prefix}request_threads as resp on resp.request_id = post.ID
   where (post.post_status = 'publish' or post.post_status = 'archive') and post.post_type = '{$args['post_type']}'
     and resp.user_id = {$args['author']} and resp.responses_count > 0
  group by post.ID order by post.post_date DESC 
   )";         
  return $wpdb->get_results($sql);          
}
<?php
define("ef_user_events_per_page", 20);
add_action('bp_setup_nav', 'ef_add_events_tab', 302);
function ef_add_events_tab() {
  global $bp;
  	bp_core_new_subnav_item( array(
	'name' => __('Events', 'egyptfoss'),
  'slug' => 'events',
	'parent_url' => $bp->displayed_user->domain . $bp->bp_nav['contributions']['slug'] . '/' ,
	'parent_slug' => $bp->bp_nav['contributions']['slug'],
	'position' => 10,
	'screen_function' => 'listing_user_events' //the function is declared below
	)
	);
}

function listing_user_events(){
	add_action( 'bp_template_content', 'listing_user_events_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function listing_user_events_content() {
bp_get_template_part( 'members/single/events' );
}

function ef_load_more_user_events() {
  set_query_var('user_event_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_events');
  die();
}
add_action('wp_ajax_ef_load_more_user_events', 'ef_load_more_user_events');
add_action('wp_ajax_nopriv_ef_load_more_user_events', 'ef_load_more_user_events');

function get_user_events($args)
{
  if($args['post_status'] == "")
  {
     $whereCondition = " where (post.post_status = 'pending' or post.post_status = 'publish') and (post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
  }else
  {
     $whereCondition = " where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
  }
  global $wpdb;
  $sql = "(SELECT post.* FROM {$wpdb->prefix}posts as post 
        join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
         {$whereCondition}
        group by post.ID order by post.ID DESC 
        limit {$args['offset']},{$args['no_of_posts']}
         )";
  return $wpdb->get_results($sql);     
}

function get_user_events_count($args)
{
  if($args['post_status'] == "")
  {
       $whereCondition = " where (post.post_status = 'pending' or post.post_status = 'publish') and (post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
  }else
  {
     $whereCondition = " where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
  }
 
  global $wpdb;
  $sql = "(SELECT post.* FROM {$wpdb->prefix}posts as post 
        join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
        {$whereCondition }
        group by post.ID
         )";
  return $wpdb->get_results($sql);     
}





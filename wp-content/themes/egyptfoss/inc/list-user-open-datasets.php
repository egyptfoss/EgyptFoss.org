<?php
define("ef_user_open_dataset_per_page", 20);
add_action('bp_setup_nav', 'ef_add_open_dataset_tab', 304);
function ef_add_open_dataset_tab() {
    global $bp;
    bp_core_new_subnav_item( array(
            'name' => _n("Open Dataset","Open Datasets",2,"egyptfoss"),
            'slug' => 'open-datasets',
            'parent_url' => $bp->displayed_user->domain . $bp->bp_nav['contributions']['slug'] . '/' ,
            'parent_slug' => $bp->bp_nav['contributions']['slug'],
            'position' => 10,
            'screen_function' => 'listing_user_open_datasets' //the function is declared below
        )
    );
}

function listing_user_open_datasets(){
    add_action( 'bp_template_content', 'listing_user_open_dataset_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function listing_user_open_dataset_content() {
    bp_get_template_part( 'members/single/open_dataset' );
}

function ef_load_more_user_open_dataset() {
  set_query_var('user_open_dataset_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_open_datasets');
  die();
}
add_action('wp_ajax_ef_load_more_user_open_dataset', 'ef_load_more_user_open_dataset');
add_action('wp_ajax_nopriv_ef_load_more_user_open_dataset', 'ef_load_more_user_open_dataset');

function get_user_open_datasets($args)
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
           {$whereCondition}
          group by post.ID order by post.post_date DESC 
          limit {$args['offset']},{$args['no_of_posts']}
           )";      
    return $wpdb->get_results($sql);     
}

function get_user_open_datasets_count($args)
{
    if($args['post_status'] == "")
    {
         $whereCondition = " where (post.post_status = 'pending' or post.post_status = 'publish') and (post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
    }else
    {
       $whereCondition = " where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') And post.post_author = {$args['author']}  ";
    }

    global $wpdb;
    $lang = pll_current_language();
    $foriegn_lang = ($lang== "en")?"ar":"en";
    /*$sql = "(SELECT post.* FROM {$wpdb->prefix}posts as post 
          join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
          {$whereCondition }
          and (pmeta.meta_key = 'language' and 
          (pmeta.meta_value like '%\"{$lang}\"%' or (
          pmeta.meta_value like '%\"slug\";s:2:\"{$foriegn_lang}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))              
          group by post.ID
           )";*/
    $sql = "(SELECT post.* FROM {$wpdb->prefix}posts as post 
          {$whereCondition }
          group by post.ID
           )";
    return $wpdb->get_results($sql);     
}

function ef_load_more_user_open_dataset_contribution() {
  set_query_var('user_open_dataset_contributions_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_open_datasets_contributes');
  die();
}
add_action('wp_ajax_ef_load_more_user_open_dataset_contribution', 'ef_load_more_user_open_dataset_contribution');
add_action('wp_ajax_nopriv_ef_load_more_user_open_dataset_contribution', 'ef_load_more_user_open_dataset_contribution');


function get_user_contributed_open_dataset_count($args){
  global $wpdb;
  
  $whereCondition = "and (postmeta.meta_value = 'publish' or postmeta.meta_value = 'pending')";
  if($args['post_status'] == 'publish')
  {
    $whereCondition = "and (postmeta.meta_value = 'publish')";
  }
  
  $sql = "select published.* from
        (
        SELECT post.*, CONCAT(SUBSTRING(postmeta.meta_key, 1, CHAR_LENGTH(postmeta.meta_key) - 7), '_resource_status') as meta_key 
                FROM  {$wpdb->prefix}posts as post
                    join  {$wpdb->prefix}postmeta as postmeta on postmeta.post_id = post.ID
                    join  {$wpdb->prefix}posts as attachment  on postmeta.meta_value = attachment.ID
                    where post.post_type = 'open_dataset'
                    and post.post_status = 'publish'
                    and postmeta.meta_key like '%_upload%'
                    and attachment.post_author <> post.post_author
                    and attachment.post_author = {$args['user_id']}
                    group by post.ID
        ) as published 
        join {$wpdb->prefix}postmeta as postmeta on postmeta.post_id = published.ID
        where postmeta.meta_key = published.meta_key
        $whereCondition";
  
  return $wpdb->get_results($sql);
}

function get_user_contributed_open_dataset($args){
  global $wpdb;
  
  $whereCondition = "and (postmeta.meta_value = 'publish' or postmeta.meta_value = 'pending')";
  if($args['post_status'] == 'publish')
  {
    $whereCondition = "and (postmeta.meta_value = 'publish')";
  }
  
    $sql = "select published.* from
        (
        SELECT post.*, CONCAT(SUBSTRING(postmeta.meta_key, 1, CHAR_LENGTH(postmeta.meta_key) - 7), '_resource_status') as meta_key 
                FROM  {$wpdb->prefix}posts as post
                    join  {$wpdb->prefix}postmeta as postmeta on postmeta.post_id = post.ID
                    join  {$wpdb->prefix}posts as attachment  on postmeta.meta_value = attachment.ID
                    where post.post_type = 'open_dataset'
                    and post.post_status = 'publish'
                    and postmeta.meta_key like '%_upload%'
                    and attachment.post_author <> post.post_author
                    and attachment.post_author = {$args['user_id']}
                    group by post.ID order by post.post_date DESC
        ) as published 
        join {$wpdb->prefix}postmeta as postmeta on postmeta.post_id = published.ID
        where postmeta.meta_key = published.meta_key
        $whereCondition
        limit {$args['offset']},{$args['no_of_posts']}";
  
  return $wpdb->get_results($sql);
}
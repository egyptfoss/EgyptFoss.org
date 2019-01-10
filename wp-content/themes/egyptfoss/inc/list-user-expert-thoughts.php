<?php
define("ef_user_expert_thought_per_page", 20);
add_action('bp_setup_nav', 'ef_add_expert_thought_tab', 303);
function ef_add_expert_thought_tab() {
    global $bp;
    bp_core_new_subnav_item( array(
            'name' => __('Expert Thoughts', 'egyptfoss'),
            'slug' => 'expert-thoughts',
            'parent_url' => $bp->displayed_user->domain . $bp->bp_nav['contributions']['slug'] . '/' ,
            'parent_slug' => $bp->bp_nav['contributions']['slug'],
            'position' => 10,
            'screen_function' => 'listing_user_expert_thoughts' //the function is declared below
        )
    );
}

function listing_user_expert_thoughts(){
    add_action( 'bp_template_content', 'listing_user_expert_thought_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function listing_user_expert_thought_content() {
    bp_get_template_part( 'members/single/expert_thought' );
}

function ef_load_more_user_expert_thought() {
  set_query_var('user_expert_thought_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_expert_thoughts');
  die();
}
add_action('wp_ajax_ef_load_more_user_expert_thought', 'ef_load_more_user_expert_thought');
add_action('wp_ajax_nopriv_ef_load_more_user_expert_thought', 'ef_load_more_user_expert_thought');

function get_user_expert_thoughts($args)
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
           {$whereCondition}
          and (pmeta.meta_key = 'language' and 
          (pmeta.meta_value like '%\"{$lang}\"%' or (
          pmeta.meta_value like '%\"slug\";s:2:\"{$foriegn_lang}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))              
          group by post.ID order by post.ID DESC 
          limit {$args['offset']},{$args['no_of_posts']}
           )";*/
 $sql = "(SELECT post.* FROM {$wpdb->prefix}posts as post 
           {$whereCondition}
          group by post.ID order by post.post_date DESC 
          limit {$args['offset']},{$args['no_of_posts']}
           )";      
    return $wpdb->get_results($sql);     
}

function get_user_expert_thoughts_count($args)
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

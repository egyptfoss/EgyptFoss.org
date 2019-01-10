<?php
class EFBActions{
  
  public static function getAction($postType,$postStatus){
    global $wpdb;
    $postStatus = esc_attr($postStatus);
    $postType = esc_attr($postType);
    $query = "SELECT * FROM {$wpdb->base_prefix}efb_actions where (post_type='{$postType}' or post_type='all') and post_status = '{$postStatus}'";
    $actions = $wpdb->get_results($query, ARRAY_A);
    return $actions;
  }
  
}
  
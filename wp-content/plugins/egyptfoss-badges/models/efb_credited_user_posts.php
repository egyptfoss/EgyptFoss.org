<?php
class EFBCreditedUserPosts{
  var $post_id;
  var $action_id;
  var $user_id;
  
  public function __construct($args = array("post_id"=>"","action_id"=>"","user_id"=>"")) {
    $this->post_id = $args["post_id"] ;
    $this->action_id = $args["action_id"] ;
    $this->user_id = $args["user_id"] ;
  }
  
  public static function checkCreditedPost($user_id,$post_id,$action_id){
    global $wpdb;
    $user_id = esc_attr($user_id);
    $post_id = esc_attr($post_id);
    $query = "SELECT * FROM {$wpdb->base_prefix}efb_credited_user_posts where user_id='{$user_id}' and post_id = '{$post_id}'"
    . " and action_id = '{$action_id}'";
    $actions = $wpdb->get_results($query, ARRAY_A);
    return $actions;
  }
  
  public function save() {
    global $wpdb;
    return $wpdb->insert( "{$wpdb->base_prefix}efb_credited_user_posts", 
      array( 'user_id' => $this->user_id, 'post_id' => $this->post_id, 'action_id' => $this->action_id ), 
      array( '%d', '%d', '%d' ) );
  }
  
}
  
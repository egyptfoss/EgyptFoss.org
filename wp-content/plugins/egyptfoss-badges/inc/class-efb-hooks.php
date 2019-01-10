<?php

function efb_save_posts($post_id,$post) {
  $post_author = $post->post_author;
  /* get all the actions related to post type and status */
  $actions = EFBActions::getAction($post->post_type, $post->post_status);
  foreach ($actions as $action) {
    /* increment no of action and points if the action grant points to user
     * then update the credited user posts table to prevent using the post again for incrementing
    */
    $action_count = get_user_meta($post_author, 'efb_action_' . $action["id"], true);
    $isCredited = EFBCreditedUserPosts::checkCreditedPost($post_author, $post_id, $action["id"]);
    if (!$isCredited) {
      $args = array("post_id" => $post_id, "user_id" => $post_author, "action_id" => $action["id"]);
      $newCreditedPostUser = new EFBCreditedUserPosts($args);
      $newCreditedPostUser->save();
      if ($action["is_point_granted"] && !$action["parent_id"]) {
        $points_count = get_user_meta($post_author, 'efb_points', true);
        $points_count = ($points_count) ? $points_count + $action["points_weight"] : $action["points_weight"];
        update_user_meta($post_author, 'efb_points', $points_count);
      }
      $action_count = ($action_count) ? $action_count + 1 : 1;
      update_user_meta($post_author, 'efb_action_' . $action["id"], $action_count);
    }
    /* get badges related to action and give it to user if he exceeds the min_threshold of this badge */
    $badges = EFBBadges::getBadgesByAction($action["id"]);
    $userBadges = EFBBadgesUsers::getBadgesByUser($post_author);
    $ids = array();
    foreach($userBadges as $userBadge){
      array_push($ids,(int)$userBadge->id);
    }
    foreach ($badges as $badge) {
      if (intval($badge->min_threshold) <= $action_count && !in_array($badge->id, $ids)) {
        $args = array("badge_id" => $badge->id, "user_id" => $post_author);
        $newBadgeToUser = new EFBBadgesUsers($args);
        $newBadgeToUser->save();
        /* send email to user */
        sendNewBadgeAchiever($post_author,$badge,$action);
      }
    }

    // do action
    do_action('efb_after_post_saving', $post_id, $post);
  }
}

add_action('save_post','efb_save_posts', 10, 2);

function sendNewBadgeAchiever($user_id,$badge,$action = null) {
  $userData = get_user_by("ID", $user_id);

  if( current_user_can('administrator') ) {
    return;
  }

  $template_inputs = array(
     "title" => sprintf(__("You have earned the %s badge.","efbadges"),$badge->getTitle(get_locale())),
     "message" => sprintf(__("Congratulations! You have earned the <strong>%s</strong> badge.","efbadges"),$badge->getTitle(get_locale())),
     "btn_title" =>  __("View all badges","efbadges"),
     "btn_url" => home_url()."/members/".$userData->user_nicename."/badges/",
     "intro" => (__("Hi","efbadges").", ".$userData->user_nicename),
     "lang" => "en",
     "user_name" => $userData->user_nicename,
     "display_name" => $userData->display_name,
     "badge" => $badge,
     "action" => $action
    );
  $template_inputs = apply_filters('efb_email_user_new_badge_params', $template_inputs,$user_id);
  ob_start();
  include(constant('EFB_PLUGIN_PATH').('/mailer/NewbadgeAchiever.php'));
  $message = ob_get_contents();
  ob_end_clean();
  $message = apply_filters('efb_email_user_new_badge_message', $message);
  wp_mail($userData->user_email,$template_inputs['title'],$message);
}

function overridePostStatus($data, $postarr){
  if( in_array( $data['post_status'], array( 'draft', 'auto-draft', 'pending', 'future' ) ) || ( isset( $_GET['action'] ) && $_GET['action'] === 'trash' )  ) {
    return $data;
  }
  $efb_permissions = EFBBadgesUsers::getBadgesPermByUser($data["post_author"]);
  foreach ($efb_permissions as $perm) {
    $permTypes = explode("__", $perm["granted_permission"]);
    if (in_array($data["post_type"], $permTypes)) {
      $data["post_status"] = "publish";
      $post_name = sanitize_title($data["post_title"]);
      $post_name = wp_unique_post_slug($post_name, 0, $data["post_status"], $data["post_type"], $data["post_parent"]);
      $data["post_name"] = $post_name;
    }
  }
  return $data;
}
add_filter( 'wp_insert_post_data','overridePostStatus' ,10,2 );

function efb_alert_user(){

  if( !is_admin() && !current_user_can('administrator') )
  {
    $user_id = get_current_user_id();
    $unNotifiedBadges = EFBBadgesUsers::getUnNotifiedBadges($user_id);
    if($unNotifiedBadges)
    {
      foreach($unNotifiedBadges as $unNotifiedBadge){
        $badgeInfo = EFBBadges::getBadgeByID($unNotifiedBadge["badge_id"])[0];
        include(constant('EFB_PLUGIN_PATH').('/views/NotifyBadgeAchieved.php'));
      }
      wp_enqueue_script('efb_badge_achievement_model',  constant('EFB_PLUGIN_URL') . 'js/main.js',array('jquery'), '', true) ;
    }
  }
}
add_action("init","efb_alert_user");
add_action("efb_init_alert_user","efb_alert_user");

function loadBadgesPermission($user_login,$userData){
  $permissions = EFBBadgesUsers::getBadgesPermByUser($userData->ID);
  session_start();
  $_SESSION["efb_granted_permission"] = array();
  if(is_array($permissions)){
    foreach ($permissions as $permission){
      if($permission["granted_permission"]){
        $_SESSION["efb_granted_permission"][] = $permission["granted_permission"];
      }
    }
  }
  return $user_login;
}
//add_action( 'wp_login', 'loadBadgesPermission', 1, 2 );

function efbUpdateNotificationStatus(){

    $user_id = get_current_user_id();
    $isUpdated = EFBBadgesUsers::updateUnNotifiedBadges($user_id);
    echo json_encode(array("status"=>"{$isUpdated}"));
    exit;
}
add_action('wp_ajax_updateNotificationStatus', 'efbUpdateNotificationStatus');
add_action('wp_ajax_nopriv_updateNotificationStatus', 'efbUpdateNotificationStatus');

function register_thickbox(){
  add_thickbox();
}
add_action('admin_init', 'register_thickbox');

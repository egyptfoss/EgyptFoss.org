<?php
class BadgesHelper {
  public static function updateBadgesInfoByUser($post) {
    $post_author = $post->post_author;
    /* get all the actions related to post type and status */
    $actions = EFBActions::getAction($post->post_type, $post->post_status);
    foreach ($actions as $action) {
      /* increment no of action and points if the action grant points to user
       * then update the credited user posts table to prevent using the post again for incrementing
       */
      $usermeta = new Usermeta();
      $action_count = $usermeta->getUserMeta($post_author, 'efb_action_' . $action["id"]);
      $isCredited = EFBCreditedUserPosts::checkCreditedPost($post_author, $post->id, $action["id"]);
      if (!$isCredited) {
        $args = array("post_id" => $post->id, "user_id" => $post_author, "action_id" => $action["id"]);
        $newCreditedPostUser = new EFBCreditedUserPosts();
        $newCreditedPostUser->addCreditedPostUser($args);
        $newCreditedPostUser->save();
        if ($action["is_point_granted"] && !$action["parent_id"]) {
          $usermeta->updateBadgesPoints($post_author, $action["points_weight"]);
        }
        $action_count = ($action_count) ? $action_count + 1 : 1;
        Usermeta::updateActionCount($post_author, $action["id"], 1);
      }
      /* get badges related to action and give it to user if he exceeds the min_threshold of this badge */
      $badges = EFBBadge::getBadgesByAction($action["id"], $post_author);
      $achievedBadgesByUser = new EFBBadgesUser();
      $achievedBadgesByUser = $achievedBadgesByUser->getBadgesByUser($post_author)->pluck("id")->toArray();
      foreach ($badges as $badge) {
        if (intval($badge->min_threshold) <= $action_count && !in_array($badge->id, $achievedBadgesByUser)) {
          $args = array("badge_id" => $badge->id, "user_id" => $post_author);
          $newBadgeToUser = new EFBBadgesUser();
          $newBadgeToUser->addUserBadge($args);
          $newBadgeToUser->save();
          /* send email to user */
          $badgeMailer = new BadgeMailer();
          $badgeMailer->sendNewBadgeAchieved($post_author, $badge,$action);
        }
      }
    }
  }
  
  public static function updatePostStatusByBadgePerm($post){
    $post_name = $post->post_name;
    $badges = new EFBBadgesUser();
    $badges = $badges->getBadgesByUser($post->post_author);
    foreach ($badges as $badge) {  
      $permTypes = explode("__", $badge->granted_permission);
      if (in_array($post->post_type, $permTypes)) {
        $post_name = wp_helper::wp_unique_post_slug($post_name);
        $post->post_status = "publish";
        
        if( !empty( $post->id ) ) {
          $search = new SearchController;
          $search->save_post_to_marmotta( $post->id, $post->post_title, $post->post_content, $post->post_type );
        }
      }
    }
    return array("post_name" => $post_name,"post_status"=>$post->post_status);
  } 
}
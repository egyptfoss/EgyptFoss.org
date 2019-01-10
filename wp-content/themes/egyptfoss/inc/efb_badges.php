<?php
function ef_override_new_user_badge_params($template_inputs,$user_id)
{
  global $ef_email_msg_labels;
  global $ef_email_msg_labels_ar;      
  $lang = get_user_meta($user_id, 'prefered_language', true);
  if ($lang == "en") {
    $lang = "en";
    $messages = $ef_email_msg_labels;
    $domain = NULL;
  } else {
    $lang = "ar";
    $messages = $ef_email_msg_labels_ar;
    $domain = "efbadges";
    load_textdomain('efbadges', dirname(__FILE__) . '/lang/efbadges-' . $lang . '.mo');
  }
  $msg = sprintf($messages["Congratulations! You have earned the <strong>%s</strong> badge."],$template_inputs["badge"]->getTitle($lang));
  if($template_inputs["badge"]->name == "suggestions_l1"){
    $msg = sprintf($messages["Congratulations! You have earned the <strong>%s</strong> badge for adding <strong>%s</strong>."],$template_inputs["badge"]->getTitle($lang),$messages[$template_inputs["action"]["post_type"]]);
  }
  $template_inputs = array(
  "title" =>   sprintf($messages['You have earned the %s badge.'], $template_inputs["badge"]->getTitle($lang)),
  "message" => $msg,
  "btn_title" =>  $messages["View all badges"],
  "btn_url" => $template_inputs["btn_url"],
  "intro" => sprintf($messages["Hi, %s"],$template_inputs["display_name"]),  
  "lang" => $lang,
  "user_name" => $template_inputs["user_name"],
  "badge" => $template_inputs["badge"],
  "action" => $template_inputs["action"]  
 ); 
 return $template_inputs;
}
add_filter( 'efb_email_user_new_badge_params', 'ef_override_new_user_badge_params', 999, 2 );

function ef_activist_center_top_users()
{
  global $wpdb;
  global $activist_center_minimum_points;
  global $activist_center_users_count;
  $sql = "select ID,CAST(meta_points.meta_value AS UNSIGNED) as points,
        count(distinct badges_users.badge_id) as total_badges
        from {$wpdb->prefix}users as users
        inner join {$wpdb->prefix}usermeta as meta_points on meta_points.user_id = users.ID
        inner join {$wpdb->prefix}usermeta as meta_role on meta_role.user_id = users.ID
        left join {$wpdb->prefix}efb_badges_users as badges_users on badges_users.user_id = users.ID
        where meta_points.meta_key = 'efb_points'
        and meta_role.meta_key = 'wpRuvF8_capabilities' and meta_role.meta_value not like '%subscriber%'
        and meta_role.meta_value not like '%administrator%'
        group by ID order by points desc,total_badges desc,users.display_name asc
        limit {$activist_center_users_count};";
        
  $users = $wpdb->get_results($sql);
  return $users;
}

function ef_change_author_role($post_id, $post)
{
  $post_types = array('news','product','tribe_events','open_dataset','success_story');
  if($post->post_status == "published")
  {
    if(in_array($post->post_type, $post_types))
    {
      $meta_role = get_user_meta($post->post_author, 'wpRuvF8_capabilities', true);
      $defaultRole = get_option('default_role');
      if($meta_role[0] == $defaultRole)
      {
        //update user role
        $role = array($defaultRole => 1);
        update_user_meta($post->post_author, 'wpRuvF8_capabilities', serialize($role));
        update_user_meta($post->post_author, 'wpRuvF8_user_level', 2);
      }
    }
  }
}

add_action('efb_after_badge_granted', 'ef_change_author_role',10,2);

function userCanEditProduct($status,$post_author){
  $current_user = get_current_user_id();
  
  if($current_user == $post_author){
    return true;
  }
  
  $userBadgeCanEdit = false;
  $badgesUsers = EFBBadgesUsers::getBadgesByUser($current_user);
  foreach ($badgesUsers as $badgesUser) {
    if ($badgesUser->name == "product_l1" || $badgesUser->name == "product_l2") {
      $userBadgeCanEdit = true;
    }
  }
  
  if($status == "publish" && $current_user != $post_author && $userBadgeCanEdit){
    return true;
  }
  
  return false;
}

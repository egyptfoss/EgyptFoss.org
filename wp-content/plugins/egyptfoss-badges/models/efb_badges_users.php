<?php
class EFBBadgesUsers{
  var $user_id;
  var $badge_id;
  var $created_date;
  var $is_notified;
  
  public function __construct($args = array("badge_id"=>"","created_date"=>"","user_id"=>"")) {
    $this->badge_id = $args["badge_id"] ;
    $this->created_date = date('Y-m-d') ;
    $this->user_id = $args["user_id"] ;
  }
  public function save(){
    global $wpdb;
    return $wpdb->insert( "{$wpdb->base_prefix}efb_badges_users", 
      array( 'user_id' => $this->user_id, 'badge_id' => $this->badge_id, 'created_date' => $this->created_date ), 
      array( '%d', '%d', '%s' ) );
  }
  
  public static function getUnNotifiedBadges($user_id) {
    global $wpdb;
    $query = "SELECT distinct * FROM {$wpdb->base_prefix}efb_badges_users as badgesUser "
    . "where badgesUser.user_id='{$user_id}' and (badgesUser.is_notified <> '1' or badgesUser.is_notified is null ) order by badgesUser.badge_id desc";
    $badges = $wpdb->get_results($query, ARRAY_A);
    return $badges;
  }
  
  public static function getBadgesByUser($user_id) {
    global $wpdb;
    $query = "SELECT distinct * FROM {$wpdb->base_prefix}efb_badges_users as badgesUser "
      . "join {$wpdb->base_prefix}efb_badges as badges on badges.id = badgesUser.badge_id "
    . "where badgesUser.user_id='{$user_id}'";
    $result = $wpdb->get_results($query, ARRAY_A);
    $badges = array();
      foreach($result as $badge){
        array_push($badges, new EFBBadges($badge)); 
    }
    return $badges;
  }
  
  //$badges_achevied string of ids to exclude comma seperated
  public static function getNotAchievedBadges($badges_acheived)
  {
    global $wpdb;
    if($badges_acheived != "") {
      $query = "SELECT distinct * FROM {$wpdb->base_prefix}efb_badges as badges "
        . "where badges.id not in ({$badges_acheived})";
    }else {
      $query = "SELECT distinct * FROM {$wpdb->base_prefix}efb_badges as badges ";     
    }
    $result = $wpdb->get_results($query, ARRAY_A);
    $badges = array();
      foreach($result as $badge){
        array_push($badges, new EFBBadges($badge)); 
    }
    return $badges;
  }
  
  public static function updateUnNotifiedBadges($user_id) {
    global $wpdb;
    return $wpdb->update(
      "{$wpdb->base_prefix}efb_badges_users", array(
      'is_notified' => '1', // string
      ), array('user_id' => $user_id), array(
      '%d', // value1
      ), array('%d')
    );
  }
  
  public static function isUserHasBadge($user_id,$badge_id) {
    global $wpdb;
    $query = "SELECT distinct * FROM {$wpdb->base_prefix}efb_badges_users as badgesUser "
    . "where badgesUser.user_id='{$user_id}' and badgesUser.badge_id = '{$badge_id}' ";
    $badges = $wpdb->get_results($query, ARRAY_A);
    return $badges;
  }
  
  public static function getBadgesPermByUser($user_id) {
    global $wpdb;
    $query = "SELECT distinct badges.granted_permission FROM {$wpdb->base_prefix}efb_badges_users as badgesUser "
      . "join {$wpdb->base_prefix}efb_badges as badges on badges.id = badgesUser.badge_id "
    . "where badgesUser.user_id='{$user_id}'";
    $badges = $wpdb->get_results($query, ARRAY_A);
    return $badges;
  }
  
  public static function getHighRankBadgesByUser($user_id) {
    global $wpdb;
    $query = "SELECT distinct * FROM {$wpdb->base_prefix}efb_badges_users as badgesUser "
      . "join {$wpdb->base_prefix}efb_badges as badges on badges.id = badgesUser.badge_id "
    . "where badgesUser.user_id='{$user_id}'";
    $result = $wpdb->get_results($query, ARRAY_A);
    $badges = array();
    $highRankBadge = array();
    
    foreach ($result as $badge) {
      if ($badge["parent_id"] == null) {
        $highRankBadge[$badge["id"]] = (!isset($highRankBadge[$badge["id"]]["threshold"]) || $badge["threshold"] > $highRankBadge[$badge["id"]]["threshold"]) ? $badge : $highRankBadge[$badge["id"]];
      } else {
        $highRankBadge[$badge["parent_id"]] = (!isset($highRankBadge[$badge["parent_id"]]["threshold"]) || $badge["threshold"] > $highRankBadge[$badge["parent_id"]]["threshold"]) ? $badge : $highRankBadge[$badge["parent_id"]];
      }
    }

    foreach($highRankBadge as $badge){
      array_push($badges, new EFBBadges($badge)); 
    }
    return $badges;
  }
}
  
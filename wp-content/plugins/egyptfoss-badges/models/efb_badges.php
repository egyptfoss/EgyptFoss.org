<?php
class EFBBadges{
  public function __construct($args) {
    $this->id = $args["id"];
    $this->title = $args["title"];
    $this->title_ar = $args["title_ar"];
    $this->name = $args["name"];
    $this->description = $args["description"];
    $this->description_ar = $args["description_ar"];
    $this->parent_id = $args["parent_id"];
    $this->type = $args["type"];
    $this->min_threshold = $args["min_threshold"];
    $this->img = $args["img"];
    $this->granted_permission = $args["granted_permission"];
  }
  public static function getBadgesByAction($action_id, $user_id = 0){
    global $wpdb;
    $query = "SELECT distinct badges.* FROM {$wpdb->base_prefix}efb_badges as badges "
    . "join {$wpdb->base_prefix}efb_badges_actions as bActions on badges.id = bActions.badge_id "
    . "where bActions.action_id='{$action_id}'";
    $result = $wpdb->get_results($query, ARRAY_A);
    $badges = array();
      foreach($result as $badge){
        array_push($badges, new EFBBadges($badge));
    }
    return $badges;
  }
  
  public static function getBadgeByID($id){
    global $wpdb;
    $query = "SELECT distinct badges.* FROM {$wpdb->base_prefix}efb_badges as badges "
    . "where badges.id='{$id}'";
    $result = $wpdb->get_results($query, ARRAY_A);
    $badges = array();
      foreach($result as $badge){
        array_push($badges, new EFBBadges($badge)); 
    }
    return $badges;
  }
  
  public function getTitle($lang){
    if($lang == "ar"){
      return $this->title_ar;
    }else{
      return $this->title;
    }
  }
  
  public function getDescription($lang){
    if($lang == "ar"){
      return $this->description_ar;
    }else{
      return $this->description;
    }
  }
  
  public static function getBadgeSmallUrl($img_url)
  {
    $path_parts = pathinfo($img_url);
    $ext = pathinfo($img_url, PATHINFO_EXTENSION); 
    $imgName = rtrim(basename($img_url, $ext),".")."@small";
    return $path_parts['dirname']."/".$imgName.".".$ext;
  }
}
  
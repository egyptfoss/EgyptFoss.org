<?php

class EFBBadge extends BaseModel {

  protected $table = 'efb_badges';

  public static function getBadgesByAction($action_id, $user_id) {
    return EFBBadge::join('efb_badges_actions as bActions', 'efb_badges.id', '=', 'bActions.badge_id')
        ->where("bActions.action_id", "=", $action_id)
        ->get();
  }
  
  public static function getAllBadges() {
    return EFBBadge::distinct()->select( 'id', 'title', 'title_ar', 'img' )->orderBy( 'title' )->get();
  }

  public function getTitle($lang) {
    if ($lang == "ar") {
      return $this->title_ar;
    } else {
      return $this->title;
    }
  }

}

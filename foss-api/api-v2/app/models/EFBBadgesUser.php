<?php
class EFBBadgesUser extends BaseModel {

  protected $table = 'efb_badges_users';
  
  public function addUserBadge($args){
    $this->badge_id = $args["badge_id"] ;
    $this->created_date = date('Y-m-d') ;
    $this->user_id = $args["user_id"] ;
    $this->is_notified = false;
  }
  
  public function getBadgesByUser($user_id) {
    
    $badges = EFBBadgesUser::distinct()->select('badges.id','badges.name','badges.granted_permission','title','title_ar','img','description','description_ar')->join('efb_badges as badges','badges.id','=','efb_badges_users.badge_id')
            ->where('efb_badges_users.user_id','=',$user_id)
            ->get();
    
    return $badges;
  }
  
  public function getBadgesByUserAndName($user_id,$name) {
    
    $badges = EFBBadgesUser::distinct()->select('badges.id','badges.granted_permission','title','title_ar','img')->join('efb_badges as badges','badges.id','=','efb_badges_users.badge_id')
            ->where('efb_badges_users.user_id','=',$user_id)
            ->where('badges.name','=',$name)
            ->first();
    
    return $badges;
  }
  
  public function getUnNotifiedBadgesByUser($user_id) {
    
    $badges = EFBBadgesUser::distinct()->select('badges.id','badges.granted_permission','title','title_ar','img')->join('efb_badges as badges','badges.id','=','efb_badges_users.badge_id')
            ->where('efb_badges_users.user_id','=',$user_id)->where("efb_badges_users.is_notified","=",false);
    
    return $badges;
  }
  
  //$badges_achevied string of ids to exclude comma seperated
  public static function getNotAchievedBadges($badges_acheived)
  {
    if($badges_acheived != "") {
      $badges = EFBBadge::distinct()->whereNotIn('id',explode(",",$badges_acheived))
            ->get();
    }else{
      $badges = EFBBadge::distinct()->get();
    }
    
    return $badges;
  }
}
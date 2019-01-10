<?php
class EFBBadgeAction extends BaseModel {

  protected $table = 'efb_badges_actions';
    
  public function getAction($badge_id)
  {
    return EFBBadgeAction::select('action_id')
            ->where('badge_id', '=', $badge_id)
            ->first();
  }
  
}
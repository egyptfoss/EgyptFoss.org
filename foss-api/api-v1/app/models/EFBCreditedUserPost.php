<?php
class EFBCreditedUserPosts extends BaseModel {

  protected $table = 'efb_credited_user_posts';
  
  public function addCreditedPostUser($args){
    $this->post_id = $args["post_id"];
    $this->user_id = $args["user_id"];
    $this->action_id = $args["action_id"];
  }
  
  public static function checkCreditedPost($user_id,$post_id,$action_id){
    return EFBCreditedUserPosts::where("user_id","=",$user_id)->where("post_id","=",$post_id)
                          ->where("action_id","=",$action_id)->first();
  }
  
    
}
<?php

class EFBActions extends BaseModel{
  protected $table = 'efb_actions';
  
  public static function getAction($postType,$postStatus){
    return EFBActions::where(function ($query) use ($postType) {  
      
        $query->where("post_type","=",$postType)
              ->orWhere("post_type","=","all");
      })->where("post_status","=",$postStatus)->get()->toArray();
  }
  
}
  
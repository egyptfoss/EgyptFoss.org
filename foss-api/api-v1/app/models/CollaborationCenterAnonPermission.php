<?php

class CollaborationCenterAnonPermission extends BaseModel {
  protected $table = 'ef_anon_permission_item';
  
  public function addAnonPermission($args)
  {
    $this->permission = $args['permission'];
    $this->permission_from = $args['permission_from'];
    $this->item_ID = $args['item_ID'];
    $this->name = $args['name'];
    $this->type = $args['type'];
  }
  
  public function isPermissionExist($args)
  { 
    $result = CollaborationCenterAnonPermission::where("item_ID","=",$args["item_ID"])
      ->where("name","=",$args["name"])
      ->where("type","=",$args["type"])
      ->first();
    if(!empty($result))
    {
      return true;
    }
    return false;
  }
}
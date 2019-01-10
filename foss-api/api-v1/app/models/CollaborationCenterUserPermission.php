<?php

class CollaborationCenterUserPermission extends BaseModel {
  protected $table = 'ef_user_permission_item';
    
  public function addUserPermission($args)
  {
    $this->permission = $args['permission'];
    $this->item_ID = $args['item_ID'];
    $this->user_id = $args['user_id'];
    $this->permission_from = (isset($args['permission_from']))?$args['permission_from']:"";
    $this->created_date = date('Y-m-d H:i:s');
    $this->modified_date = date('Y-m-d H:i:s');
  }
  
  public function removeUserPermissionByItemID($item_id)
  {
    $this->where('item_ID', '=', $item_id)->delete();
  }
  
  public function removeUserPermissionByItemIDs($item_ids, $user_id, $permission_from)
  {
    $this->whereIn('item_ID', $item_ids)
            ->where('user_id','=',$user_id)
            ->where('permission_from', '=', $permission_from)
            ->delete();
  }
    
  public function listInvitedUserByItem($item_id, $take, $skip)
  {
    $results = $this->select('u.ID','u.display_name','u.user_email','u.user_nicename','ef_user_permission_item.permission'
            ,'ef_user_permission_item.permission_from')
            ->join('users as u','u.ID','=','ef_user_permission_item.user_id')
            ->where('item_ID', '=', $item_id);
    if($take != -1 && $skip != -1)
    {
      $results->take($take)
              ->skip($skip);
    }
    return $results->get();
  }
  
  public function removeUserPermissionByUserAndItemId($user_id, $item_id)
  {
    $this->where('item_ID', '=', $item_id)->where('user_id', '=', $user_id)->delete();
  }
  
  public function hasPermissionByItemID($user_id, $item_id)
  {
    return $this->select('ID')->where('item_ID', '=', $item_id)->where('user_id', '=', $user_id)->count();
  }
  
  public function getPermissionByItemID($user_id, $item_id)
  {
    return $this->where('item_ID', '=', $item_id)->where('user_id', '=', $user_id)->first();
  }
  
  public function hasPermissionOnAllSpace($user_id, $documents)
  {
    return $this->whereIn('item_ID', $documents)->where('user_id', '=', $user_id)->where('permission_from','=','space')->count();
  }
  
  public function permissionOnItem($item_id)
  {
    return $this->where('item_ID', '=', $item_id)->get();
  }
}
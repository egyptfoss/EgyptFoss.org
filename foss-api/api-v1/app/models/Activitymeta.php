<?php

class Activitymeta extends BaseModel {
  protected $table = 'bp_activity_meta';
  public $primaryKey  = 'id';

  public function addActivityMeta($activity_id, $key, $value){
      $this->activity_id = $activity_id;
      $this->meta_key = $key;
      $this->meta_value = $value;

      return $this;
  }
  
  public function updateActivityMeta($activity_id, $key, $value){
    $activitymeta = Activitymeta::where('activity_id', '=', $activity_id)->where('meta_key', 'favorite_count');
    return $activitymeta->update(['meta_value' => $value]);
  }

  public function getActivityMeta($activity_id){
    //$meta = array();
    $activitymeta = Activitymeta::where('activity_id', '=', $activity_id)->where('meta_key', 'interest')->get();
    /*foreach ($activitymeta as $metaObj){
      $meta[$metaObj->meta_key] = unserialize($metaObj->meta_value);
    }*/
    return $activitymeta;
  }

  public function getFavoriteCount($activity_id){
    $activitymeta = Activitymeta::where('activity_id', '=', $activity_id)->where('meta_key', 'favorite_count')->First();
    if (empty($activitymeta))
      return false;
//    echo '<pre>';
//    print_r($activitymeta);
//    echo '</pre>';
//    die();
    return $activitymeta;
  }
}
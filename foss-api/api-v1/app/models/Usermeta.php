<?php

class Usermeta extends BaseModel {
	protected $table = 'usermeta';
  public $primaryKey  = 'umeta_id';

	public function addMeta($user_id, $user_data, $returnMeta = false){
		$meta = array();
    $default_type = 'Individual';
    // registration data
		$meta['type'] = (array_key_exists('type', $user_data)) ? $user_data['type'] : $default_type;
		$meta['sub_type'] = (array_key_exists('sub_type', $user_data)) ? $user_data['sub_type'] : '';
		$meta['functionality'] = (array_key_exists('functionality', $user_data)) ? $user_data['functionality'] : '';
		$meta['theme'] = (array_key_exists('theme', $user_data)) ? $user_data['theme'] : '';
		$meta['ict_technology'] = (array_key_exists('ict_technology', $user_data)) ? $user_data['ict_technology'] : '';
    if((array_key_exists('registeredNormally', $user_data)))
    {
      $meta['registeredNormally'] = (array_key_exists('registeredNormally', $user_data)) ? $user_data['registeredNormally'] : '1';
    }
    $meta['display_name'] = (array_key_exists('display_name', $user_data)) ? $user_data['display_name'] : '';
    
    // profile data
    $meta['address'] = (array_key_exists('address', $user_data)) ? $user_data['address'] : '';
    $meta['phone'] = (array_key_exists('phone', $user_data)) ? $user_data['phone'] : '';
    $meta['facebook_url'] = (array_key_exists('facebook_url', $user_data)) ? $user_data['facebook_url'] : '';
    $meta['twitter_url'] = (array_key_exists('twitter_url', $user_data)) ? $user_data['twitter_url'] : '';
    $meta['gplus_url'] = (array_key_exists('gplus_url', $user_data)) ? $user_data['gplus_url'] : '';
    $meta['linkedin_url'] = (array_key_exists('linkedin_url', $user_data)) ? $user_data['linkedin_url'] : '';
    $meta['interests'] = (array_key_exists('interests', $user_data)) ? $user_data['interests'] : '';
    // Entity profile contact data
    if(array_key_exists('contact_name', $user_data)){ $meta['contact_name'] = $user_data['contact_name']; }
    if(array_key_exists('contact_email', $user_data)){ $meta['contact_email'] = $user_data['contact_email']; }
    if(array_key_exists('contact_address', $user_data)){ $meta['contact_address'] = $user_data['contact_address']; }
    if(array_key_exists('contact_phone', $user_data)){ $meta['contact_phone'] = $user_data['contact_phone']; }

    if($returnMeta)
    {
      return serialize(serialize($meta)); 
    }
      
    $this->user_id = $user_id;
		$this->meta_key = 'registration_data';
		$this->meta_value = serialize(serialize($meta));
		return $this;
	}
  
  public static function addSocialMeta($meta_keys,$userID){
    foreach($meta_keys as $meta_key => $value) {
      $usermeta = Usermeta::where(function ($query) use ($meta_key, $value , $userID) {
        $query->where('meta_key', '=', $meta_key)
          ->where('user_id', '=', $userID);
      })->get()->first();
      if(empty($usermeta)) {
        $user_meta = new Usermeta();
        $user_meta->user_id = $userID;
        $user_meta->meta_key = $meta_key;
        $user_meta->meta_value = $value;
        $user_meta->save();
      } else {
        $usermeta->meta_key = $meta_key;
        $usermeta->meta_value = $value;
        $usermeta->save();
      }
    }
	}
  
  public function getMeta($user_id){
    $meta = array();
    $usermeta = Usermeta::where('user_id', '=', $user_id)->where('meta_key', '=', 'registration_data')->first();
    // TODO YF: refactor
    if($usermeta !== null) {
      $user_data = preg_replace_callback ( '!(.*?)!', function($match) {
        return $match[0];
      }, $usermeta->meta_value );
      $user_data = unserialize($user_data);
      if(is_string($user_data)) {
        $user_data2 = preg_replace_callback ( '!s:(\d+):"(.*?)";!', function($match) {      
          return ($match[1] == strlen($match[2])) ? $match[0] : 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
        }, $user_data );
        $user_data = unserialize($user_data2);
      }
      $meta = $user_data;
    }
    return $meta;
  }
  
  public function getUserMeta($user_id, $meta_key){
    $meta = array();
    $usermeta = Usermeta::where('user_id', '=', $user_id)->where('meta_key', '=', $meta_key)->first();
    if($usermeta !== null) {
      $user_data = preg_replace_callback ( '!(.*?)!', function($match) {
        return $match[0];
      }, $usermeta->meta_value );
      $meta = $user_data;
    }
    return $meta;
  }  
  
  public function getMetaLikes($user_id){
    $usermeta = Usermeta::where('user_id', '=', $user_id)->where('meta_key', '=', 'bp_favorite_activities')->first();
    return $usermeta;
  }
  
  public function updateMetaLikes($user_id, $key, $value){
    $usermeta = Usermeta::where('user_id', '=', $user_id)->where('meta_key', 'bp_favorite_activities');
    return $usermeta->update(['meta_value' => $value]);
  }
  
  public function addUserMeta($data){
    $userMeta = new Usermeta();
    $userMeta->user_id = $data['user_id'];
    $userMeta->meta_key = $data['key'];
    $userMeta->meta_value = $data['value'];
    return $userMeta;
  }

  public function updateRole($user_id, $role) {
    global $foss_prefix;
    $usermeta = Usermeta::where('user_id', '=', $user_id)->where('meta_key', "{$foss_prefix}capabilities")->first();
    $roles = unserialize($usermeta->meta_value);
    if(!empty($roles)) {
      if($roles[0] == 'contributor') {
        $roles[0] = 'author';
      }
    } else {
      $roles = array('author' => true);
    }
    return $usermeta->update(['meta_value' => serialize($roles)]);
  }
  
  public function addUserRole($user_id, $role) {
    $user_meta = new Usermeta();
    $user_meta->user_id = $user_id;
    $user_meta->meta_key = 'wpRuvF8_capabilities';
    $user_meta->meta_value = serialize(array($role => true));
    $user_meta->save();
    
    $user_meta_user_level = new Usermeta();
    $user_meta_user_level->user_id = $user_id;
    $user_meta_user_level->meta_key = 'wpRuvF8_user_level';
    $user_meta_user_level->meta_value = 1;
    $user_meta_user_level->save();
  }

  public function getRole($user_id) {
    global $foss_prefix;
    global $capabilities;
    $role = 'subscriber';
    $usermeta = Usermeta::where('user_id', '=', $user_id)->where('meta_key', "{$foss_prefix}capabilities")->first();
    if($usermeta !== null) {
      $user_role = unserialize($usermeta->meta_value);
      if(reset($user_role) == true && array_key_exists(key($user_role), $capabilities)) {
        $role = key($user_role);
      }
    }
    return $role;
  }
  
  public function isUserHasRole($user,$roles) {
    global $foss_prefix;
    $usermeta = $this->where('meta_key', "{$foss_prefix}capabilities")->where("user_id","=",$user)->first();
    if($usermeta !== null) {
      $user_role = unserialize($usermeta->meta_value);
      foreach ($roles as $role)
      {
        if(isset($user_role[$role]) && $user_role[$role]) {
          return true;
        }
      }
    }
    return false;
  }

  public function getActivityLikesUsers($activity_id) {
    global $foss_prefix;
    $sql = "SELECT m.user_id, u.display_name,u.user_nicename as username FROM {$foss_prefix}usermeta m JOIN {$foss_prefix}users u ON u.ID = m.user_id WHERE meta_key = 'bp_favorite_activities' AND (meta_value LIKE '%:$activity_id;%' OR meta_value LIKE '%:\"$activity_id\";%') ";
    $results = $this->getConnection()->select($sql);
    return $results;
  }
  
  public static function updateBadgesPoints($user_id,$points)
  {
    $params = array("efb_points"=>$points);
    foreach($params as $param=>$count)
    {
      $isExistParam = Usermeta::where("meta_key", "=",$param)->where("user_id","=",$user_id);
      if($isExistParam->first())
      {
        $newCount = $isExistParam->first()->meta_value + $count;
        $isExistParam->update(array("meta_value"=>$newCount));
      }else
      {
        $args_meta = array(
        'user_id' => $user_id,
        'key' => $param,
        'value' => $count
        );
        $user_meta = new Usermeta;
        $user_meta = $user_meta->addUserMeta($args_meta);
        $user_meta->save();
      }
    }
  }
  
  public static function updateActionCount($user_id ,$actionID,$actionCount)
  {
    $params = array("efb_action_".$actionID=>$actionCount);
    foreach($params as $param=>$count)
    {
      $isExistParam = Usermeta::where("meta_key", "=",$param)->where("user_id","=",$user_id);
      if($isExistParam->first())
      {
        $newCount = $isExistParam->first()->meta_value + $count;
        $isExistParam->update(array("meta_value"=>$newCount));
      }else
      {
        $args_meta = array(
        'user_id' => $user_id,
        'key' => $param,
        'value' => $count
        );
        $user_meta = new Usermeta;
        $user_meta = $user_meta->addUserMeta($args_meta);
        $user_meta->save();
      }
    }
  }
  
  public function deleteUsermeta($user_id, $meta_key)
  {
    global $foss_prefix;
    $sql = "delete from {$foss_prefix}usermeta where user_id = {$user_id} and meta_key = '{$meta_key}'";
    $this->getConnection()->delete($sql);
  }
  
  public function updateUserMeta( $user_id, $meta_key, $meta_value ) {
    $userMeta = Usermeta::Where( 'meta_key', '=', $meta_key )->where( 'user_id', '=', $user_id );
    if($userMeta->first()) {
      return $userMeta->update( array( "meta_key" => $meta_key, "meta_value" => $meta_value ) );
    } else {
      $newUserMeta = new Usermeta();
      $newUserMeta->user_id = $user_id;
      $newUserMeta->meta_key = $meta_key;
      $newUserMeta->meta_value = $meta_value;
      
      return $newUserMeta->save();
    }
  }
}
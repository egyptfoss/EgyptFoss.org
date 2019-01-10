<?php

class User extends BaseModel {
	protected $table = 'users';
	protected $primaryKey = "ID";

	public function addUser($user_data) {
		$this->user_login = str_replace(" ","",strtolower($user_data['username']));
		$this->user_pass = $user_data['password'];
		$this->user_nicename = str_replace(".","-",  str_replace(" ","",strtolower($user_data['username'])));
		$this->user_email = $user_data['email'];
		$this->user_url = '';
		$this->user_registered = date("Y-m-d H:i:s");
		$this->user_activation_key = '';
		$this->user_status = 0;
		$this->display_name = (isset($user_data['display_name']))?$user_data['display_name']:$user_data['username'];
    return $this;
	}
        
  public function getUser($name){
    $user = User::where('user_nicename', '=', $name)->first();
    return $user ;
  }
  
  public function getUserById($id){
    $user = User::where('ID', '=', $id)->first();
    return $user ;
  }
  
  public function getUserByEmail($email){
      $user = User::where('user_email', '=', $email)->first();
      return $user ;
  }
  
  public function loadUsers($display_name, $take, $skip)
  {
    $result = User::select('ID','user_nicename','display_name')->where('user_status','=',0);
    if($display_name != '')
    {
      $result->where('display_name', 'LIKE', "%$display_name%");
    }
    if($take != -1){
      $result->take($take);
    }
    if($skip != -1){
      $result->skip($skip);
    }  
    
    return $result->get();
  }
  
  public function userMeta()
  {
    return $this->hasMany('Usermeta','user_id','ID');
  }
  
  public function userProfile()
  {
    return $this->hasMany('UserProfile','user_id','ID');
  }
  
  public function CollaborationCenteritems()
  {
    return $this->belongsTo('CollaborationCenterItem','owner_id','ID');
  }
  
  public function retrieveValidUsers($display_name, $notValidUsers)
  {
    global $foss_prefix;
    $check_sql = "SELECT ID,display_name,user_nicename FROM {$foss_prefix}users "
    . " join {$foss_prefix}usermeta as umeta on umeta.user_id = {$foss_prefix}users.ID 
              WHERE display_name like '%$display_name%' AND user_status = 0
               AND umeta.meta_key='wpRuvF8_capabilities' AND umeta.meta_value NOT LIKE '%subscriber%'";
    if(sizeof($notValidUsers) > 0) {
        $check_sql .=" AND ID not in (".  implode(",", $notValidUsers).")";
    }

    $results = $this->getConnection()->select($check_sql);    
    return $results;
  }
  
  function socialAuthenticateNewUser($args, $socialProfile) {
    $RegisteredBefore = User::where(function ($query) use ($args) {
        $query->where('user_email', '=', $args["email"]);
          //->orWhere('user_login', '=', $args["username"]);
      })->get()->first();
    if (empty($RegisteredBefore)) {
      if(strlen($args["username"]) > 8)
      {
        $unique_username = substr($args["username"], 0, 8).time();
      }else {
        $unique_username = $args["username"].time();
      }
      $user_data = array("display_name" => $args["username"]
              ,"username" => $unique_username, "email" => $args["email"], "password" => "");
      $user = new User();
      $user->addUser($user_data);
      $user->save();
      $userID = $user->ID;
      
      //Add user meta
      self::addUserMetaData($args, $userID);
      
    } else {
      $userID = $RegisteredBefore->ID;
    }
    return $userID;
  }
  
  public function addUserMetaData($args, $userID)
  {
    $role = array('contributor' => 1);
    $user_meta_capt = new Usermeta;
    $user_meta_capt->user_id = $userID;
    $user_meta_capt->meta_key = 'wpRuvF8_capabilities';
    $user_meta_capt->meta_value = serialize($role);
    $user_meta_capt->save();

    $user_meta_level = new Usermeta;
    $user_meta_level->user_id = $userID;
    $user_meta_level->meta_key = 'wpRuvF8_user_level';
    $user_meta_level->meta_value = '1';
    $user_meta_level->save();

    $user_meta_lang = new Usermeta;
    $user_meta_lang->user_id = $userID;
    $user_meta_lang->meta_key = 'prefered_language';
    $user_meta_lang->meta_value = 'en';
    $user_meta_lang->save(); 
    
    $user_meta_nickname = new Usermeta();
    $user_meta_nickname->user_id = $userID;
    $user_meta_nickname->meta_key = 'nickname';
    $user_meta_nickname->meta_value = $args['username'];
    $user_meta_nickname->save();

    //Add registration data
    $args_meta = array(
        'type' => 'Individual',
        'sub_type' => 'user'
    );
    $user_meta = new Usermeta;
    $user_meta->addMeta($userID, $args_meta);
    $user_meta->save();
    
    //Add expert
    $user_meta_expert = new Usermeta;
    $user_meta_expert->user_id = $userID;
    $user_meta_expert->meta_key = 'is_expert';
    $user_meta_expert->meta_value = 0;
    $user_meta_expert->save(); 
    
    //Add Description
    $user_meta_description = new Usermeta;
    $user_meta_description->user_id = $userID;
    $user_meta_description->meta_key = 'description';
    $user_meta_description->meta_value = '';
    $user_meta_description->save();
    
    //Add Description
    $user_meta_type = new Usermeta;
    $user_meta_type->user_id = $userID;
    $user_meta_type->meta_key = 'type';
    $user_meta_type->meta_value = 'Individual';
    $user_meta_type->save();
  }
  
  public function retrieveAllUsers($ids, $query_string, $post_type)
  {
    global $foss_prefix;
    
    $type = "Individual";
    if($post_type == "organizations")
    {
      $type = "Entity";
    }
    
    $sqlQuery = "select users.ID as ID, users.display_name as post_title,"
            . "'organizations' as post_type "
    . "from {$foss_prefix}users as users "
    . "inner join {$foss_prefix}usermeta as usermeta_type on users.ID = usermeta_type.user_id "
    . " where users.user_status = 0 "
    . "and usermeta_type.meta_key = 'type' and usermeta_type.meta_value = '{$type}'";
    
    if (count($ids) > 0) {
        if(!is_array($ids)){
            $ids = explode (",", $ids);
        }
        
        $sqlQuery .= " AND
                    (
                        (users.display_name LIKE '%$query_string%') 
                            OR 
                        (users.ID IN (".implode(', ', $ids).")) )
                    ) 
                    order by users.display_name asc";
        $results =   self::getConnectionResolver()->connection()->select($sqlQuery);
    } else {
        $sqlQuery .= " AND
        (
          (users.display_name LIKE '%$query_string%') 
        ) 
        order by users.display_name asc";
        
        $results =   self::getConnectionResolver()->connection()->select($sqlQuery);
    }
    
    return $results;
  }
  
  public function listTopUsersByPoints()
  {
    global $foss_prefix;
    global $activist_center_minimum_points;
    global $activist_center_users_count;
    $users = User::selectRaw("ID,display_name,user_nicename,user_email,CAST({$foss_prefix}meta_points.meta_value AS UNSIGNED) as points,count(distinct {$foss_prefix}badges_users.badge_id) as total_badges")
            ->join('usermeta as meta_points','meta_points.user_id','=','users.ID')
            ->join('usermeta as meta_role','meta_role.user_id','=','users.ID')
            ->leftjoin('efb_badges_users as badges_users','badges_users.user_id','=','users.ID')
            ->where('meta_points.meta_key', '=', 'efb_points')
            ->where('meta_role.meta_key', '=', 'wpRuvF8_capabilities')
            ->where('meta_role.meta_value', 'not like', '%subscriber%')
            ->where('meta_role.meta_value', 'not like', '%administrator%')
            ->take($activist_center_users_count)
            ->groupBy('ID')
            //->having('points','>=',$activist_center_minimum_points)
            ->orderBy('points', 'desc')
            ->orderBy('total_badges', 'desc')
            ->orderBy('users.display_name', 'asc')      
            ->get();
    
    return $users;
  }
  
  public static function userCanEditProduct($current_user, $post_id) {
    $postData = Post::where("ID", "=", $post_id)->first();
    if ($postData) {
      if ($current_user == $postData->post_author) {
        return true;
      }

      $userBadgeCanEdit = false;
      $badgesByUser = new EFBBadgesUser();
      $badgesUsers = $badgesByUser->getBadgesByUser($current_user);
      foreach ($badgesUsers as $badgesUser) {
        if ($badgesUser->name == "product_l1" || $badgesUser->name == "product_l2") {
          $userBadgeCanEdit = true;
        }
      }

      if ($postData->post_status == "publish" && $current_user != $postData->post_author && $userBadgeCanEdit) {
        return true;
      }
    } else {
      return true;
    }
    return false;
  }

}
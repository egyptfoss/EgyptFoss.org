<?php

class UserProfile extends BaseModel {

  protected $table = 'wslusersprofiles';
  
  protected $attributes = array(
    'object_sha' => '',
    'profileurl' => '',
    'websiteurl' => '',
    'displayname' => '',
    'description' => '',
    'firstname' => '',
    'lastname' => '',
    'gender' => '',
    'language' => '',
    'age' => '',
    'birthday' => 0,
    'birthmonth' => 0,
    'birthyear' => 0,
    'emailverified' => '',
    'phone' => '',
    'address' => '',
    'country' => '',
    'region' => '',
    'city' => '',
    'zip' => ''
  );

  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);
  }
  
  public static function getSocialProfileID($args) {
     $user = UserProfile::Where('identifier', '=', $args["id"])
      ->where('provider', '=', $args["provider"])->get()->first(); 
    if ($user) {
      return $user->user_id;
    } else {
      return null;
    }
  }
  
  public static function getSocialProfile($args) {
     $userProfile = UserProfile::Where('identifier', '=', $args["id"])
      ->where('provider', '=', $args["provider"])->get()->first(); 
    if ($userProfile) {
      return $userProfile;
    } else {
      return null;
    }
  }
  
  public function AddSocialProfile($args) {
   $this->identifier = $args['id'];
   $this->provider = $args['provider'];
   $this->user_id = $args['user_id'];
   $this->photourl = $args['photourl'];
   $this->email = $args['email'];
   return $this;
  }

}

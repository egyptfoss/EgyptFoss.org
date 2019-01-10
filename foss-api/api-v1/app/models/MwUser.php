<?php

class MwUser extends WikiBaseModel {

  protected $connection = 'mediawiki';
  protected $table = 'user';
  protected $primaryKey = 'user_id';
  protected $attributes = array(
    'user_email' => "",
    'user_password' => "",
    'user_newpassword' => "",
  );

    public function __construct($language="en",array $attributes = array())
    {
      parent::__construct($language, $attributes);
    }
  public function getUser($user_login) {
    
    $user = MwUser::Where('user_name' ,"=", ucfirst(str_replace("_", " ", $user_login)))->first();
    return $user;
  }

  public function addUser($args) {
    $this->user_name = ucfirst(str_replace("_", " ", $args['user_name']));
    $this->user_real_name = $args['user_real_name'];
    $this->user_password = $args['user_password'];
    $this->user_newpassword = $args['user_newpassword'];
    $this->user_email = $args['user_email'];
  }

}

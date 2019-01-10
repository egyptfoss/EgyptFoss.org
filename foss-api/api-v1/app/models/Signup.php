<?php

class Signup extends BaseModel {
	protected $table = 'signups';
  
  protected $attributes = array(
    'title' => ''
  );

  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);
  }
  
	public function addSignup($user_data) {
		$this->user_login = $user_data['username'];
		$this->user_email = $user_data['email'];
		$this->registered = date("Y-m-d H:i:s");
		$this->activation_key = $user_data['activation_key'];
		$this->meta = serialize(array("password"=>$user_data['password']));
    return $this;
	}
}
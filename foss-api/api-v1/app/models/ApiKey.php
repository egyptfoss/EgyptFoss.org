<?php

class ApiKey extends BaseModel {

  protected $table = 'api_keys';
  
  protected $attributes = array(
    'is_enabled' => 1,
  );

  public function __construct(array $attributes = array())
  {
    parent::__construct($attributes);
  }
  
  public function addKey( $key, $date ) {
    $this->api_key = $key;
    $this->created_at = $date;
    
    return $this;
  }

}

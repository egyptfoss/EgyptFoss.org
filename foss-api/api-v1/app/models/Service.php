<?php
class Service extends Post {

  protected $table = 'posts';

  public function __construct(array $attributes = array()) {
    $this->post_type = 'service';
    parent::__construct($attributes);
  }

  public static function boot() {
    static::addGlobalScope(new ExpertThoughtScope());
  }

}
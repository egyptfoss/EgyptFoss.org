<?php
class ExpertThought extends Post {

  protected $table = 'posts';

  public function __construct(array $attributes = array()) {
    $this->post_type = 'expert_thought';
    parent::__construct($attributes);
  }

  public static function boot() {
    static::addGlobalScope(new ExpertThoughtScope());
  }

  public function getPublishedThoughts($offset = 0, $limit = -1) {
    $thoughts = ExpertThought::where("post_status", "=", 'publish')
      ->orderBy("ID", "DESC");
    if ($limit > 0) {
      $thoughts->take($limit)->skip($offset);
    }
    return $thoughts->get();
  }
  
  function getThoughtsByExpert($args)
  {
    $args["offset"] = (isset($args["offset"]))?$args["offset"]:0;
    $thoughts = ExpertThought::where("post_status", "=" ,"publish")->where("post_author","=",$args["post_author"])->orderBy("ID", "DESC");
    if (isset($args["limit"]) && $args["limit"] > 0) {
      $thoughts->take($args["limit"])->skip($args["offset"]);
    }
    if(isset($args["exclude_ids"]))
    {
      $thoughts->whereNotIn('ID',$args["exclude_ids"]);
    }
    return $thoughts->get();
  }
  
}

<?php

class PostHistory extends BaseModel {
	protected $table = 'posts_history';
  
  function addPostHistory($args) { {
      foreach ($args as $key => $value)
        $this->setAttribute($key, $value);
    }
    return $this;
  }
  
  public function getContributedProductsByUser($xprofile_id, $no_of_posts, $skip_number, $lang ){
    $results = $this->distinct()
            ->join('posts','posts_history.post_id', '=', 'posts.ID')
            ->select('posts_history.post_id', 'posts_history.user_id', 'posts.post_title', 'posts.post_modified', 'posts.post_status',
                    'posts.guid')
            ->where('posts_history.user_id','=', $xprofile_id)
            ->where('posts.post_author', '!=', $xprofile_id)
            ->where('posts.post_status', '=', 'publish')
            ->where('posts.post_type', '=', 'product');
    if($no_of_posts != -1 && $skip_number != -1)
    {
      $results->take($no_of_posts)->skip($skip_number);
    }
    return $results->get();
  }
}
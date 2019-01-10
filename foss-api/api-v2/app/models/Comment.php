<?php
use Carbon\Carbon;  // To insert the now time 
class Comment extends BaseModel {
  protected $table = 'comments';
  public $primaryKey  = 'comment_ID';

  public function addComment($user_id, $user_login, $user_email, $post_id, $comment_data){
    $this->comment_post_ID = $post_id;
    $this->comment_author = $user_login;
    $this->comment_author_email = $user_email;
    $this->comment_author_url = "";
    $this->comment_author_IP = "";
    /*$this->comment_date = Carbon::now(); // saving now time
    $this->comment_date_gmt = Carbon::now('Europe/London'); // saving GMT time*/
    $date = gmdate('Y-m-d H:i:s');
    $this->comment_date = date('Y-m-d H:i:s');
    $this->comment_date_gmt = $date;
    $this->comment_content = $comment_data;
    $this->comment_karma = 0;
    $this->comment_approved = 1;
    $this->comment_agent = 'API';
    $this->comment_type = '';
    $this->comment_parent = 0;
    $this->user_id = $user_id;

    //increment main post total count of comments by 1
    $original_post = Post::where('ID','=', $post_id);
    if($original_post->first())
    {
        $original_post->update(array("ID"=> $post_id,"comment_count"=> ($original_post->first()->comment_count + 1)));
    }
    
    return $this;
  }

  public function addReplyToComment($user_id, $user_login, $user_email, $post_id, $comment_id, $comment_data, $news_id){
    $this->comment_post_ID = $post_id;
    $this->comment_author = $user_login;
    $this->comment_author_email = $user_email;
    $this->comment_author_url = "";
    $this->comment_author_IP = "";
    $this->comment_date = Carbon::now(); // saving now time
    $this->comment_date_gmt = Carbon::now('Europe/London'); // saving GMT time
    $this->comment_content = $comment_data;
    $this->comment_karma = 0;
    $this->comment_approved = 1;
    $this->comment_agent = 'API';
    $this->comment_type = '';
    $this->comment_parent = $comment_id;
    $this->user_id = $user_id;

    //increment main post total count of comments by 1
    $original_post = Post::where('ID','=', $news_id);
    if($original_post->first())
    {
        $original_post->update(array("ID"=> $news_id,"comment_count"=> ($original_post->first()->comment_count + 1)));
    }
    
    return $this;
  }
  
  public function getCommentByID($comment_id){
    $comment = Comment::where('comment_ID', '=', $comment_id)->first();
    return $comment ;
  }
  
  //Get direct comments on a post
  public function getCommentByPostID($post_id,$take, $skip)
  {
      $comments = Comment::where('comment_post_ID', '=', $post_id)
              ->where('comment_parent','=',0)
              ->where('comment_approved','=',1);
      if($take != -1 && $skip != -1)
      {
        $comments->take($take)
              ->skip($skip);
      }
      return $comments->orderBy('comment_date', 'desc')->get();
  }
  
  //Get comments on a comments
  public function getCommentByCommentID($post_id, $take,$skip)
  {
      $comments = Comment::where('comment_parent', '=', $post_id)
              ->where('comment_approved','=',1);
      if($take != -1 && $skip != -1)
      {
        $comments->take($take)
              ->skip($skip);
      }
      return $comments->orderBy('comment_date', 'desc')->get();
  }
  
  public function checkHasmoreComments($comment_id,$take)
  {
      $comments = Comment::where('comment_parent', '=', $comment_id)
              ->where('comment_approved','=',1)
              ->take($take)
              ->count();
      if($comments > $take){
          return true;
      }
      return false;
  }
          
}
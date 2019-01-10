<?php
use Carbon\Carbon;  // To insert the now time 

class Activity extends BaseModel {
    protected $table = 'bp_activity';
    protected $primaryKey = "id";

    public function addActivity($user_id, $user_login, $activity_data) {
      $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
      $user_link = $home_url->option_value.'/members/'.$user_login.'/';
      $this->user_id = $user_id;
      $this->component = 'activity';
      $this->type = 'activity_update';
      $this->action = '<a href="'.$user_link.'" title="'.$user_login.'">'.$user_login.'</a> posted a status';
      $this->content = $activity_data;
      $this->primary_link = $user_link;
      $this->item_id = 0;
      $this->secondary_item_id = 0;
      $this->date_recorded = gmdate('Y-m-d H:i:s'); // saving GMT time
      $this->hide_sitewide = 0;
      $this->mptt_left = 0;
      $this->mptt_right = 0;
      $this->is_spam = 0;
      return $this;
    }

    public function listProfileActivities($user_id, $no_of_activities, $skip_number) {
      $activities = Activity::where('user_id', '=', $user_id)->whereIn('type', array('activity_update', 'new_member', 'new_avatar'));
      if($no_of_activities != -1)
        $activities->take($no_of_activities);
      
      if($skip_number != -1)
        $activities->skip($skip_number);
      
      $activities->orderBy('id', 'desc');
      return $activities->get();
    }
    
    public function findActivity( $id, $is_comment_included = FALSE ) {
        $activity_types = array('activity_update', 'new_member', 'new_avatar');
        
        if( $is_comment_included ) {
            $activity_types[] = 'activity_comment';
        }
        
        $activity = Activity::where( 'id', '=', $id )->whereIn( 'type', $activity_types );
        
        return $activity;
    }
    
    public function addActivityComment($user_id, $user_login, $activity_id, $activity_comment_data) {
      $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
      $user_link = $home_url->option_value.'/members/'.$user_login.'/';
      $this->user_id = $user_id;
      $this->component = 'activity';
      $this->type = 'activity_comment';
      $this->action = '<a href="'.$user_link.'" title="'.$user_login.'">'.$user_login.'</a> posted a new activity comment';
      $this->content = $activity_comment_data;
      $this->primary_link = $user_link;
      $this->item_id = $activity_id;
      $this->secondary_item_id = $activity_id;
      $this->date_recorded = gmdate('Y-m-d H:i:s'); // saving GMT time
      $this->hide_sitewide = 0;
      
      $update_activity = new Activity();
      $update_activity->exists = true;
      $update_activity->id = $activity_id; //already exists in database.
      // before changing mptt_left and mptt_right, we will need to set the mptt_left of the main activity_id to 1 if it is 0
      $last_activty_id = Activity::where('id', '=', $activity_id)->first();
      if ( $last_activty_id->mptt_left == 0 ){
          // If mptt_left of the main activity_id is 0 set it to 1 and set the comment mptt_left=2
          $update_activity->mptt_left = "1";
          $update_activity->save();
          $mptt_left_value = 2;
      }
      else{ $mptt_left_value = $last_activty_id->mptt_right ; }

      $this->mptt_left = $mptt_left_value ;

      // mptt_right for this comment will be the (mptt_left + 1) //
      $current_mptt_right = $mptt_left_value + 1 ;
      $this->mptt_right = $current_mptt_right ;

      // and set the main activity mptt_right to (mptt_right+1)
      $update_activity->mptt_right = $current_mptt_right + 1;
      $update_activity->save();
      
      $this->is_spam = 0;
      return $this;
    }
    
    public function getActivityComment($id) {
      $activity = Activity::where('id', '=', $id)->first();
      return $activity ;
    }

    public function getActivities($item_id, $take, $skip) {
      $activity = Activity::where('secondary_item_id', '=', $item_id)
              ->whereIn('type', array('activity_comment'));
      if($take != -1 && $skip != -1)
      {
        $activity->take($take)
              ->skip($skip);
      }
              
      $activity->orderBy('mptt_left', 'DESC');
      return $activity->get();
    }
    
    public function checkHasmoreComments($comment_id,$take)
    {
        $comments = Activity::where('secondary_item_id', '=', $comment_id)
                ->whereIn('type', array('activity_comment'))
                ->take($take)
                ->count();
        if($comments > $take){
            return true;
        }
        return false;
    }
    
    public function getActivitiesPrimaryCount($item_id) {
      $activity = Activity::where('item_id', '=', $item_id)->where('secondary_item_id', '=', $item_id)->whereIn('type', array('activity_comment'))->orderBy('mptt_left', 'DESC')->get();
      return $activity ;
    }

    public function getUpdatesByInterest($interests,$fromDate,$today) {
        $activityUpdates = $this->distinct()
            ->join('bp_activity_meta','bp_activity.id', '=', 'bp_activity_meta.activity_id')
            ->join('users','bp_activity.user_id', '=', 'users.ID')
            ->select('bp_activity.*','users.user_login','users.user_nicename')
            ->where('bp_activity_meta.meta_key', '=', 'interest')
            ->where('bp_activity.type', '=', 'activity_update')
            ->where('date_recorded','>',$fromDate)
            ->where('date_recorded','<',$today)
            ->whereIn('bp_activity_meta.meta_value', $interests)
            ->get();
        return $activityUpdates;
    }
    
    public function getActivity($id) {
        $activity = $this->where('id', '=', $id)->whereIn('type', array('activity_update', 'new_member', 'new_avatar'))->first();
        if ($activity) {
            return $activity;
        } else {
            return false;
        }
    }
    
    public function addCommentReply($user_id, $user_login, $activity_id, $comment_id, $activity_comment_data){
      $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
      $user_link = $home_url->option_value.'/members/'.$user_login.'/';
      $this->user_id = $user_id;
      $this->component = 'activity';
      $this->type = 'activity_comment';
      $this->action = '<a href="'.$user_link.'" title="'.$user_login.'">'.$user_login.'</a> posted a new activity comment';
      $this->content = $activity_comment_data;
      $this->primary_link = $user_link;
      $this->item_id = $activity_id;
      $this->secondary_item_id = $comment_id;
      $this->date_recorded = gmdate('Y-m-d H:i:s'); // saving GMT time
      $this->hide_sitewide = 0;
      $this->is_spam = 0;

      //check if comment id is last comment added
      $activity_comments = $this->select('id','mptt_right')->where('item_id', '=', $activity_id)
                            ->where('secondary_item_id', '=', $activity_id)
                            ->orderBy('id','desc')->get();
      if($activity_comments[0]->id == $comment_id)
      {
        //add the new record and update the comment and activity only
        $this->mptt_left = $activity_comments[0]->mptt_right;
        $this->mptt_right = $activity_comments[0]->mptt_right + 1;
        
        //update the comment mptt_right
        $original_comment = $this->where('id','=', $activity_comments[0]->id);
        if($original_comment->first())
        {
            $original_comment->update(array("id"=> $activity_comments[0]->id,
                "mptt_right"=> ($original_comment->first()->mptt_right + 2)));
        }
        
        //update activity mptt_right
        $original_activity = $this->where('id','=', $activity_id);
        if($original_activity->first())
        {
            $original_activity->update(array("id"=> $activity_id,
                "mptt_right"=> ($original_activity->first()->mptt_right + 2)));
        }        
      }else
      {
        //get current comment mttp_right
        $original_comment = $this->where('id','=', $comment_id);
        if($original_comment->first())
        {
          //update new reply mptt_right and left
          $this->mptt_left = $original_comment->first()->mptt_right;
          $this->mptt_right = $original_comment->first()->mptt_right + 1;
          
          $original_comment->update(array("id"=> $comment_id,
                "mptt_right"=> ($original_comment->first()->mptt_right + 2)));
        }
        
        //update all other comments and replies after that comment
        $comments = $this->select('id')->where('item_id', '=', $activity_id)
                            ->where('id', '!=', $comment_id)
                            ->where('secondary_item_id','!=', $comment_id)
                            ->get();
        for($z = 0; $z < sizeof($comments); $z++)
        {
          $original_comment = $this->where('id','=', $comments[$z]->id);
          if($original_comment->first())
          {
            $original_comment->update(array("id"=> $original_comment->first()->id,
                "mptt_right"=> ($original_comment->first()->mptt_right + 2),
                "mptt_left"=> ($original_comment->first()->mptt_left + 2)));
          }
        }
        
        //update activity mptt_right
        $original_activity = $this->where('id','=', $activity_id);
        if($original_activity->first())
        {
            $original_activity->update(array("id"=> $activity_id,
                "mptt_right"=> ($original_activity->first()->mptt_right + 2)));
        }       
      }
      
      return $this;
    }
    
    public function activityMeta()
    {
        return $this->hasMany('Activitymeta','activity_id','id');
    }
    
    public function activityComments()
    {
        return $this->hasMany('Activity','item_id','id');
    }
}
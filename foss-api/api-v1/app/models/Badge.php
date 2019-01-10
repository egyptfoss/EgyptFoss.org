<?php

/**
 * This class manage badges
 */
class Badge extends BaseModel {
  
  // mapped DB table
  protected $table = 'efb_badges';
  
  public $badges_earned = array();
  
  // badges info
  public static $MB_BADGES = array( '"top_service"', '"top_provider"' );
  public static $EXPERT_BADGE = '"expert"';
  public static $FOSSPEDIA_BADGE = '"fosspedia_l1"';
  public static $DOCUMENT_CONTRIBUTOR_BADGE = '"collaboration_l1"';
  public static $FIRST_QUIZ_BADGE = '"quiz_l1"';
  public static $SPECIALIST_QUIZ_BADGE = '"quiz_l2"'; 

  // slider type
  public static $is_top = TRUE;

  // marketpalce properties
  private $service_id;
  private $user_id;
  private $user_email;
  private $display_name;
  private $usernice_name;
  private $rate_info;
 
  /**
   * 
   * @param type $user_id
   * @param type $user_email
   * @param type $display_name
   */
  public function __construct( $user_id, $user_email = '', $display_name = '', $usernice_name = '' ) {
    $this->user_id    = $user_id;
    $this->user_email = $user_email;
    $this->display_name = $display_name;
    $this->usernice_name = $usernice_name;
  }
  
  /**
   * Get badges by name
   * 
   * @global type $foss_prefix
   * @param type $badge_names
   * @return type
   */
  public function efb_get_badges_by_name( $badge_names ) {
    global $foss_prefix;
    
    if( is_array( $badge_names ) ) {
      $badges_str = implode( ',', $badge_names );
    }
    else {
      $badges_str = $badge_names;
    }
    
    $sql =  " SELECT * "
          . " FROM {$foss_prefix}efb_badges"
          . " WHERE name IN ({$badges_str})"
          . " ORDER BY name DESC" ;
          
    $badges = $this->getConnection()->select( $sql );
    
    if( !is_array( $badge_names ) && !empty( $badges ) ) {
        $badges = $badges[0];
    }

    return $badges;
  }

  /**
   * Check if user took certain badge
   * 
   * @global type $foss_prefix
   * @param type $badge_id
   * @return type
   */
  public function efb_is_user_took_badge( $badge_id ) {
    global $foss_prefix;

    $sql =    " SELECT badge_id"
            . " FROM {$foss_prefix}efb_badges_users"
            . " WHERE badge_id = {$badge_id} AND user_id = {$this->user_id}";
            
    $badges = $this->getConnection()->select( $sql );
    
    return !empty( $badges );
  }

  /**
   * Delete user badge
   * 
   * @global type $foss_prefix
   * @param type $badge_id
   */
  public function efb_remove_user_badge( $badge_id ) {
    global $foss_prefix;
    
    $sql = "DELETE FROM {$foss_prefix}efb_badges_users WHERE badge_id = {$badge_id} AND user_id = {$this->user_id}";
    
    $this->getConnection()->update( $sql );
  }

  /**
   * Add user badge
   * 
   * @global type $foss_prefix
   * @param type $badge_id
   */
  public function efb_create_user_badge( $badge ) {
    global $foss_prefix;
    
    $data = array( 
              'badge_id'      => $badge->id,
              'user_id'       => $this->user_id,
              'created_date'  => '"'.date( 'Y-m-d H:i:s', time() ).'"',
              'is_notified'   => 0 
            );
    
    $sql =  " INSERT INTO {$foss_prefix}efb_badges_users"
          . " VALUES (". implode( ',', $data ) .")";
    
    $this->getConnection()->update( $sql );
    
    // send email to user
    $this->efb_send_user_badge_email( $badge );
  }
  
  /**
   * send user email
   * 
   * @param type $badge
   */
  public function efb_send_user_badge_email( $badge ) {
    if(empty($this->display_name))
    {
      $user = User::select( 'user_email', 'display_name', 'user_nicename' )->where( 'ID', '=', $this->user_id )->first();
      $displayName = $user->display_name;
      $userEmail = $user->user_email;
      $userNicename = $user->user_nicename;
    }else {
      $displayName = $this->display_name;
      $userEmail = $this->user_email;
      $userNicename = $this->usernice_name;
    }
    /*$user_meta = new Usermeta();
    $lang = $user_meta->getUserMeta( $this->user_id, "prefered_language" );
    $badge_title = $badge->title;
    
    if( $lang == 'ar' ) {
      $badge_title = $badge->title_ar;
    }

    $btn_title = __( 'find your badges', 'egyptfoss', $lang );
    $btn_url = $home_url."/$lang/members/".$userNicename."/badges/";
    $message = '<p style="text-align:center;">'. __( 'Hi', 'egyptfoss', $lang ) . ' '. $displayName.'</p>';
    $message .= '<p style="text-align:center;">'. sprintf( __( 'Congratulations You\'ve earned %s Badge', 'egyptfoss', $lang ), $badge_title ).' </p>';
    $message .= '<div style="margin-left: 20px;margin-right: 20px;">
      <div class="btn btn--flat" style="margin-bottom: 20px;text-align: center;">
        <!--[if !mso]--><a style="border-radius: 4px;display: inline-block;font-weight: bold;text-align: center;text-decoration: none !important;transition: opacity 0.1s ease-in;color: #fff;background-color: #4caf50;font-family: sans-serif;font-size: 14px;line-height: 24px;padding: 12px 35px;" href="'. $btn_url .'" target="_blank">'. $btn_title .'</a><!--[endif]-->
      <!--[if mso]><p style="line-height:0;margin:0;">&nbsp;</p><a href="'. $btn_url .'" target="_blank"><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="'. $btn_url .'" style="width:262px" arcsize="9%" fillcolor="#4caf50" stroke="f"><v:textbox style="mso-fit-shape-to-text:t" inset="0px,11px,0px,11px"><center style="font-size:14px;line-height:24px;color:#FFFFFF;font-family:sans-serif;font-weight:bold;mso-line-height-rule:exactly;mso-text-raise:4px">'. $btn_title .'</center></v:textbox></v:roundrect></a><![endif]--></div>
    </div>';

    $args = array(
      "title"       =>  __( "New Badge Acheived", 'egyptfoss', $lang ),
      "message"     =>  $message,
      "lang"        =>  $lang,
      "email_title" =>  "",
      "user_name"   =>  "",
      "to"          =>  array(
                              'email' => $userEmail,
                              'name'  => $displayName
                        ),
    );
    */
    
    // Request sent from API
    if( class_exists( 'BadgeMailer' ) ) {
      // sending email
      $mpb_mailer = new BadgeMailer();
      $mpb_mailer->sendNewBadgeAchieved($this->user_id ,$badge,NULL, $this->user_email, $this->display_name, $this->usernice_name );
    }
    else {
      $this->badges_earned[] = $badge;
    }
  }

  /**
   * Manage add/delete badge
   * 
   * @global type $market_place_badge_min_rate
   * @param type $badge
   * @param type $count
   * @param type $rate_avg
   * @return type
   */
  public function efb_process_mb_badge( $badge, $count, $rate_avg ) {
    global $market_place_badge_min_rate;

    // check if user took badge
    $is_user_took_badge = $this->efb_is_user_took_badge( $badge->id, $this->user_id );
    
    $threshold = $badge->min_threshold;
    
    /**
     * - Entities :- 
     * 
     *    + Threshold  => minimum amount that required to this badge
     * 
     *    In case of top-service badge :
     *    + @ objects  => reviewers
     *    + # count    => count of reviewers
     *    + % avg      => avg rate of reviews
     * 
     *    In case of top-provider badge :
     *    + @ objects  => top services
     *    + # count    => count of top services
     *    + % avg      => avg rate of top services 
     *                  ( always will be maximum amount , because it's top services!! )
     * 
     * 
     * - Scenario :- 
     *    1- Remove user badge -if exist- & unmark service as top service :
     *    ( if count of objects < threshold OR avg < minimum rate  )
     * 
     *    2- Create user badge -if not exist- & mark service as top service :
     *    ( if count of objects >= threshold AND avg >= minimum rate  )
     */
    if(  
        $count < $threshold
        || 
        $rate_avg < $market_place_badge_min_rate
      ) {

        // update service/user meta and check is there is no other
        // top services for same user
        // 
        // Return: 
        // - TRUE if remove is allowable.
        // - FALSE if remove is not allowable because user has other top services.
        if( !$this->efb_pre_remove_badge( $badge->name ) ) {
          return;
        }

        if( $is_user_took_badge ) {
          // delete suer badge
          $this->efb_remove_user_badge( $badge->id );
        }
    }
    else if ( $count >= $threshold 
              && 
              $rate_avg >= $market_place_badge_min_rate
            ) {
        // update service/user meta
        $this->efb_pre_create_badge( $badge->name );
        
        if( !$is_user_took_badge ) {
          // add user badge
          $this->efb_create_user_badge( $badge );
        }
    }
  }

  /**
   * update service meta before delete the badge
   * 
   * @param type $badge_name
   * @return boolean
   */
  public function efb_pre_remove_badge( $badge_name ) {
    if( $badge_name == 'top_service' ) {
      
        $post_meta = new Postmeta();
        $user_meta = new Usermeta();
        
        $is_top_service = $post_meta->getMetaValue( $this->service_id, 'efb_is_top_service' );

        if( !$is_top_service ) {
          return FALSE;
        }

        // unset service as top-service
        $post_meta->updatePostMeta( $this->service_id, 'efb_is_top_service', 0 );
        
        // get User top services count
        $ts_count = $user_meta->getUserMeta( $this->user_id, 'efb_top_services_count', TRUE );

        // decrease User top services count 
        $user_meta->updateUserMeta($this->user_id, 'efb_top_services_count', $ts_count - 1 );

        // can't remove badge because user has other top services 
        if( $ts_count > 1 ) {
          return FALSE;
        }
    }

    return TRUE;
  }

  /**
   * update service meta before add badge
   * 
   * @param type $badge_name
   * @return type
   */
  public function efb_pre_create_badge( $badge_name ) {
    if( $badge_name == 'top_service' ) {
      
        $post_meta = new Postmeta();
        $user_meta = new Usermeta();
        
        $is_top_service = $post_meta->getMetaValue( $this->service_id, 'efb_is_top_service' );
        
        if( $is_top_service ) {
          return;
        }
        
        $post_meta->updatePostMeta( $this->service_id, 'efb_is_top_service', 1 );

        $ts_count = $user_meta->getUserMeta( $this->user_id, 'efb_top_services_count', TRUE );
        
        if( empty( $ts_count ) ) {
          $ts_count = 0;
        }
        
        // to be used in top provider badge
        $user_meta->updateUserMeta( $this->user_id, 'efb_top_services_count', $ts_count + 1 );
    }
  }

  /**
   * Manage add/delete market place badges
   * 
   * @param type $service_id
   * @param type $rate_info
   */
  public function efb_manage_mb_badges( $service_id, $rate_info ) {
      
      $this->service_id = $service_id;
      $this->rate_info  = $rate_info;
    
      // get marketplace badges
      $badges = $this->efb_get_badges_by_name( self::$MB_BADGES );
      
      foreach( $badges as $badge ) {

        switch( $badge->name ) {
          case 'top_service': {
            $count      = $this->rate_info['reviewers_count'];
            $rate_avg   = $this->rate_info['rate'];
            break;
          }
          case 'top_provider': {
            $meta = new Usermeta();
            $count = $meta->getUserMeta( $this->user_id, 'efb_top_services_count' );
            
            if( empty( $count ) ) {
              $count = 0;
            }
            
            $rate_avg = 5;
            break;
          }
        }

        // run badge process
        $this->efb_process_mb_badge( $badge, $count, $rate_avg );
      }
  }
  
  /**
   * Manage add/delete expert badge
   * 
   * @param type $is_expert
   */
  public function efb_manage_expert_badge( $is_expert ) {
      
      // get expert badge
      $badge = $this->efb_get_badges_by_name( self::$EXPERT_BADGE );
      
      if( empty( $badge ) ) {
        return;
      }
      
      // run badge process
      $is_user_took_badge = $this->efb_is_user_took_badge( $badge->id );
      
      // is user is expert and he didn't take expert badge before
      if( $is_expert && !$is_user_took_badge ) {
        $this->efb_create_user_badge( $badge );
      }
      else if( !$is_expert && $is_user_took_badge ) {
        $this->efb_remove_user_badge( $badge->id );
      }
  }
  
   /**
   * Manage add fosspedia badge
   */
  public function efb_manage_fosspedia_badge( ) {
    // get expert badge
    $badge = $this->efb_get_badges_by_name( self::$FOSSPEDIA_BADGE );

    if( empty( $badge ) ) {
      return;
    }

    // run badge process
    $is_user_took_badge = $this->efb_is_user_took_badge( $badge->id );

    // badge minthreshold passed
    $badge_action = new EFBBadgeAction();
    $action = $badge_action->getAction($badge->id)->action_id;
    
    // update usermeta fosspedia action
    $userMeta = new Usermeta();
    $userMeta->updateActionCount($this->user_id, $action, 1);
    
    $actionMeta = $userMeta->getUserMeta($this->user_id, 'efb_action_'.$action);
    if(!empty($actionMeta))
    {
      // is user didn't take expert badge before
      if(!$is_user_took_badge) {
        if($actionMeta >= $badge->min_threshold)
        {
          $this->efb_create_user_badge( $badge );
        }
      }
    }
  }
  
  /**
   * Manage add Document Publisher badge
   */
  public function efb_manage_document_contributor_badge( ) {
    // get expert badge
    $badge = $this->efb_get_badges_by_name( self::$DOCUMENT_CONTRIBUTOR_BADGE );

    if( empty( $badge ) ) {
      return;
    }

    // run badge process
    $is_user_took_badge = $this->efb_is_user_took_badge( $badge->id );

    // badge minthreshold passed
    $badge_action = new EFBBadgeAction();
    $action = $badge_action->getAction($badge->id)->action_id;
    
    // update usermeta fosspedia action
    $userMeta = new Usermeta();
    $userMeta->updateActionCount($this->user_id, $action, 1);
    
    $actionMeta = $userMeta->getUserMeta($this->user_id, 'efb_action_'.$action);
    if(!empty($actionMeta))
    {
      // is user didn't take expert badge before
      if(!$is_user_took_badge) {
        if($actionMeta >= $badge->min_threshold)
        {
          $this->efb_create_user_badge( $badge );
        }
      }
    }
  }
  
  /**
   * Manage Foss Beginner Badge
   */
  public function efb_manage_beginner_quiz_badge( ) {
    // get quiz beginner badge
    $badge = $this->efb_get_badges_by_name( self::$FIRST_QUIZ_BADGE );

    if( empty( $badge ) ) {
      return;
    }

    // run badge process
    $is_user_took_badge = $this->efb_is_user_took_badge( $badge->id );

    // badge minthreshold passed
    $badge_action = new EFBBadgeAction();
    $action = $badge_action->getAction($badge->id)->action_id;
    
    // update usermeta fosspedia action
    $userMeta = new Usermeta();
    $userMeta->updateActionCount($this->user_id, $action, 1);
    
    $actionMeta = $userMeta->getUserMeta($this->user_id, 'efb_action_'.$action);
    if(!empty($actionMeta))
    {
      // is user didn't take expert badge before
      if(!$is_user_took_badge) {
        if($actionMeta >= $badge->min_threshold)
        {
          $this->efb_create_user_badge( $badge );
        }
      }
    }
  }
  
  /**
   * Manage Foss Specialist Badge
   */
  public function efb_manage_specialist_quiz_badge( ) {
    
    global $awarness_highest_score_badge_percentage;
    
    // get quiz beginner badge
    $badge = $this->efb_get_badges_by_name( self::$SPECIALIST_QUIZ_BADGE );

    if( empty( $badge ) ) {
      return;
    }

    // run badge process
    $is_user_took_badge = $this->efb_is_user_took_badge( $badge->id );

    // badge minthreshold passed
    $badge_action = new EFBBadgeAction();
    $action = $badge_action->getAction($badge->id)->action_id;
    
    // get number of high ranked
    $highRankedCount = QuizResult::where('user', '=', $this->user_id)
                    ->where('correct_score', '>=',$awarness_highest_score_badge_percentage)
                    ->distinct('quiz_id')->count('quiz_id');  
    
    if(!empty($highRankedCount))
    {
      // is user didn't take expert badge before
      if(!$is_user_took_badge) {
        if($highRankedCount >= $badge->min_threshold)
        {
          $this->efb_create_user_badge( $badge );
        }
      }
    }
  }
  
  /**
   * Get top services ( Get latest if number of top service less than 4 )
   * 
   * @return type
   */
  public static function efb_get_top_services() {
    global $foss_prefix;
    $is_top = TRUE;
    
    $select = "SELECT p.*, u.display_name FROM {$foss_prefix}posts p";
  
    $join_user = " JOIN {$foss_prefix}users u ON p.post_author = u.ID";

    // published services
    $where_service = " WHERE p.post_type = 'service' AND p.post_status = 'publish'";

    // only 10 services
    $order_by = " ORDER BY p.post_date DESC LIMIT 10";
    
    $post = new Post();
    
    $sql =  $select
          . " JOIN {$foss_prefix}postmeta pm ON p.ID = pm.post_id"
          . $join_user
          . $where_service
          . " AND pm.meta_key = 'efb_is_top_service'"
          . " AND pm.meta_value = 1"
          . $order_by;
    
    $top_services = $post->getConnection()->select( $sql );
    
    self::$is_top = TRUE;
    
    // display latest services if top services count less than 4
    if( count( $top_services ) < 4 ) {
        $sql =  $select
              . $join_user
              . $where_service
              . $order_by;

        $top_services = $post->getConnection()->select( $sql );
        
        self::$is_top = FALSE;
    }

    return $top_services;
  }
  
  /**
   * Get top providers ( Get latest if number of top providers less than 4 )
   * 
   * @return type
   */
  public static function efb_get_top_providers() {
    global $foss_prefix;
    
    $select = "SELECT u.*, COUNT(Distinct p.ID) AS services_count, COUNT(Distinct r.id) AS reviewers_count FROM {$foss_prefix}users u";
  
    // published services
    $service_cond = "p.post_type = 'service' AND p.post_status = 'publish'";

    $group_by = " GROUP BY u.id";
    
    $limit = " LIMIT 10";
    
    $post = new Post();
    
    $sql =  $select
          . " JOIN {$foss_prefix}usermeta um ON u.ID = um.user_id"
          . " JOIN {$foss_prefix}efb_badges_users ub ON u.ID = ub.user_id"
          . " JOIN {$foss_prefix}efb_badges b ON b.id = ub.badge_id"
          . " JOIN {$foss_prefix}posts p ON u.ID = p.post_author"
          . " JOIN {$foss_prefix}ef_reviews r ON r.provider_id = u.ID"
          . " WHERE b.name = 'top_provider'"
          . " AND " . $service_cond
          . $group_by
          . " ORDER BY ub.created_date DESC"
          . $limit;

    $top_providers = $post->getConnection()->select( $sql );
    
    self::$is_top = TRUE;
    
    if( count( $top_providers ) < 4 ) {
      $sql =  $select
          . " JOIN {$foss_prefix}usermeta um ON u.ID = um.user_id"
          . " JOIN {$foss_prefix}posts p ON u.ID = p.post_author"
          . " LEFT JOIN {$foss_prefix}ef_reviews r ON r.provider_id = u.ID"
          . " WHERE " . $service_cond
          . $group_by
          . " ORDER BY p.post_date DESC"
          . $limit;
          
      $top_providers = $post->getConnection()->select( $sql );
      
      self::$is_top = FALSE;
    }
    
    return $top_providers;
  }
}
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class WPActivistCenterController extends EgyptFOSSController {
  /**
   * @SWG\GET(
   *   path="/activist-center",
   *   tags={"Activist Center"},
   *   summary="List top users ordered by points",
   *   description="List top users ordered by points,gained from posting posts and badges",
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List Users")
   * )
   */
  public function listTopUsers($request, $response, $args){
    
    $lang = "";
    if(!isset($_GET["lang"]))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","lang"));
    }
    
    $lang = $_GET["lang"];
    if($lang != "en" && $lang != "ar")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    global $en_sub_types, $ar_sub_types;
    $option = new Option();
    $host = $option->getOptionValueByKey('siteurl');;
    
    //List of users
    $users = new User();
    $topUsers = $users::listTopUsersByPoints();
    if(sizeof($topUsers) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    $badges = new EFBBadgesUser();
    $results = array();
    foreach($topUsers as $user) {
      // Load user profile picture
      $user_id = $user->ID;
      $directory = dirname(__FILE__)."/../../../../wp-content/uploads/avatars/$user_id/";
      $image_location = glob($directory . "*bpfull*");            
      foreach(glob($directory . "*bpfull*") as $image_name){
        $image_name = end(explode("/", $image_name));
        $image = $host."/wp-content/uploads/avatars/$user_id/".$image_name;
      }

      if (empty($image_location)){
        $meta_key = "wsl_current_user_image";
        $user_meta = new Usermeta();
        $meta = $user_meta->getUserMeta($user_id, $meta_key);
        $image = $meta;
        if (empty($meta)){ 
          $email = $user->user_email;
          $size = '150'; //The image size
          $image = 'http://www.gravatar.com/avatar/'.md5($email).'?d=mm&s='.$size;
        }
      }
      
      //Load user meta
      $userMeta = Usermeta::where( 'user_id', '=', $user_id )->where( "meta_key", "=", "registration_data" )->first();
      if($userMeta)
      {
        $registeration_data = unserialize( $userMeta->meta_value );
        $registeration_data = ( is_array( $registeration_data ) ) ? $registeration_data : unserialize($registeration_data);
      }else {
        $registeration_data = array();
      }
      
      $result_badges = $badges::getBadgesByUser($user->ID);
      $user_badges = array();
      foreach($result_badges as $badge)
      {
        $user_badges[] = array(
            'title' => ($lang == "ar")?html_entity_decode($badge['title_ar'],ENT_QUOTES):$badge['title'],
            'img' => $badge['img']
        );
      }
      $results[] = array(
          "display_name"  => html_entity_decode($user->display_name, ENT_QUOTES),
          "username"    => $user->user_nicename,
          "image" => $image,
          'type' => $registeration_data['type'],
          'subtype' => ( $lang == 'en' ) ? $en_sub_types[ $registeration_data['sub_type'] ] : $ar_sub_types[ $registeration_data[ 'sub_type' ] ],
          'badges' => $user_badges,
          "points" => $user->points
      );
    }
              
    return $this->renderJson($response, 200, $results);
  }
}
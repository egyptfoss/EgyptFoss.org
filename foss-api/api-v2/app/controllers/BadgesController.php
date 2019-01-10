<?php

use AccessToken;

class BadgesController extends EgyptFOSSController {
  /**
   * @SWG\Get(
   *   path="/badges/unnotified",
   *   tags={"Badges"},
   *   summary="get unnotified badges by user",
   *   description="get unnotified badges by user to display an alert for the user with new earned badges",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to return list of unnotified badges <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="get un notified badges by user"),
   *   @SWG\Response(response="404", description="Not User"),
   * )
   */
  public function getUnNotifiedBadgesByUser($request, $response, $args) {
    $params = $request->getHeaders();
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if (!$loggedin_user) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    } else {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    }  
    $unNotifiedBadgesQuery = new EFBBadgesUser();
    $unNotifiedBadgesQuery = $unNotifiedBadgesQuery->getUnNotifiedBadgesByUser($user_id);
    $unNotifiedBadges = $unNotifiedBadgesQuery->get();
    if (count($unNotifiedBadges) > 0) {
      $unNotifiedBadgesQuery->update(array("is_notified" => 1));
    } else {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $returnResult = array();
    foreach($unNotifiedBadges as $badge){
      $returnResult[] = array(
        "title"=>$badge->title,
        "title_ar" => $badge->title_ar,
        "description" => $badge->description,
        "description_ar" => $badge->description_ar,
        "image"=>$badge->img,
        );
    }
    return $this->renderJson($response, 200, $returnResult);
  }
  
  /**
   * @SWG\Get(
   *   path="/badges",
   *   tags={"Badges"},
   *   summary="get all system badges",
   *   description="get all system badges data",
   *   @SWG\Response(response="200", description="return system badges"),
   * )
   */
  public function listBadges($request, $response, $args) {
    $params = $request->getHeaders();
  
    $efbBadges = new EFBBadge;
    $badges = $efbBadges->getAllBadges();
    if ( empty($badges)) {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    $returnResult = array();
    foreach($badges as $badge){
      $returnResult[] = array(
        "id"        => $badge->id,
        "title"     => $badge->title,
        "title_ar"  => $badge->title_ar,
        );
    }
    return $this->renderJson($response, 200, $returnResult);
  }
}
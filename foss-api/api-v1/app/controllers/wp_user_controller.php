<?php

class WPUserController extends EgyptFOSSController {

  public function GetUser($request, $response, $args) {
    $result = "";
		if(is_numeric($args['id'])){
      $user = User::find($args['id']);
			if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else {
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Found", "User"));
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "Id"));
    }
    return $result;
  }

  /**
   * @SWG\Post(
   *   path="/profiles/me/social-links",
   *   tags={"Profile"},
   *   summary="Links Social Account",
   *   description="link logged in user profile with social profile Id",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description=""),
   * 	 @SWG\Parameter(name="id", in="formData", required=false, type="integer", description="social media id"),
   *   @SWG\Parameter(name="provider", in="formData", required=false, type="string", description="Facebook, Twitter, LinkedIn, Google"),
   *   @SWG\Parameter(name="email", in="formData", required=false, type="string", description=""),
   *   @SWG\Parameter(name="photourl", in="formData", required=false, type="string", description=""),
   *   @SWG\Response(response="200", description="linking successfully"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Profile not found")
   * )
   */
  public function linkWithSocialMedia($request, $response, $args) {
    $parameters = ['email', 'id', 'provider', 'photourl', 'token'];
    global $ef_wsl_social_login_providers;
    foreach ($_POST as $key => $value) {
      if (in_array($key, $parameters)) {
        $args[$key] = $value;
        $parameters = array_diff($parameters,[$key]);
      }
    }
    if(!empty($parameters))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if (!is_numeric($args["id"])) {
       return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "id"));
    }
    if(!in_array($args["provider"], $ef_wsl_social_login_providers))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Provider"));
    }
    if (filter_var($args['email'], FILTER_VALIDATE_EMAIL) === false) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Email"));
    }
    if (filter_var($args['photourl'], FILTER_VALIDATE_URL) === false) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "url"));
    }
    
    
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'])->first()) : null;
    
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;
    $user = User::find($user_id);
    
    if (empty($user)) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    } 
    
    $userProfile = UserProfile::getSocialProfileID($args);
    $meta_keys = array("wsl_current_user_image" => $args['photourl'], "wsl_current_provider" => $args['provider']);
    Usermeta::addSocialMeta($meta_keys, $loggedin_user->user_id);
    if($userProfile)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Profile updated"));
    }
            
    $userProfile = new UserProfile();
    $args["user_id"] =  $loggedin_user->user_id;
    $userProfile->AddSocialProfile($args);
    $userProfile->save();
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Profile Added"));
  }
  
  /**
   * @SWG\Delete(
   *   path="/profiles/me/social-links",
   *   tags={"Profile"},
   *   summary="Unlinks Soicial Account",
   *   description="delete linking of the logged in user with the passed social account",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description=""),
   * 	 @SWG\Parameter(name="id", in="formData", required=false, type="integer", description="social media id"),
   *    @SWG\Parameter(name="provider", in="formData", required=false, type="string", description="Facebook, Twitter, LinkedIn, Google"),
   *   @SWG\Response(response="200", description="unlinking sucessfully"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Profile not found or social profile not found")
   * )
   */
  public function unlinkWithSocialMedia($request, $response, $args) {
    $params = $request->getParsedBody();
    $parameters = [ 'id', 'token', 'provider'];
    global $ef_wsl_social_login_providers;
    foreach ($params as $key => $value) {
      if (in_array($key, $parameters)) {
        $args[$key] = $value;
        $parameters = array_diff($parameters,[$key]);
      }
    }
    if(!empty($parameters))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if (!is_numeric($args["id"])) {
       return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "id"));
    }
    if(!in_array($args["provider"], $ef_wsl_social_login_providers))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Provider"));
    }
    $loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'])->first()) : null;
    
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;
    $user = User::find($user_id);
    
    if (empty($user)) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    } 
    
    $userProfile = UserProfile::getSocialProfile($args);
    if(!$userProfile)
    {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "social media profile"));
    }
    $userProfilesCount = $user = UserProfile::Where('user_id', '=', $loggedin_user->user_id)->count();
    $usermeta = Usermeta::getMeta($loggedin_user->user_id);
    $usermeta = unserialize($usermeta);
    if($userProfilesCount == 1 && !isset($usermeta["registeredNormally"])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "user social profile"));
    }
    $userProfile->delete();
    return $this->renderJson($response, 200, Messages::getSuccessMessage("DeletedSuccessfully", 'user profile')); 
  }
}

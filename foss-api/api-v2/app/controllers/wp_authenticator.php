<?php
use Lcobucci\JWT\Builder;
use \Lcobucci\JWT\Signer\Hmac;

class WPAuthenticator extends EgyptFOSSController {
  
  /**
  * @SWG\Post(
  *   path="/login",
  *   tags={"Register & Login"},
  *   summary="Login",
  *   description="login user by username and password",
  *   @SWG\Parameter(name="userLogin", in="formData", required=false, type="string", description="Username used while user registration <br/><b>[Required]</b>"),
  *   @SWG\Parameter(name="password", in="formData", required=false, type="string", format="password", description="Password used while user registration <br/><b>[Required]</b>"),
  *   @SWG\Response(response="200", description="successful login")
  * )
  */
  public function authenticate($request, $response, $args) {
//    $result = "";
    $result = array();
    $loginName = $_POST['userLogin'];
    $pass = $_POST['password'];
    if (!empty($loginName) && !empty($pass)) {
      $user = User::where(function ($query) use ($loginName, $pass) {
          $query->where('user_email', '=', $loginName)
            ->orWhere('user_login', '=', $loginName);
        })->first();

      if (!empty($user)) {
        if ($user->user_status != 0) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("incorrect", "User status"));
        } else {
          $hasher = new PasswordHash(8, true);
          $passMatched = $hasher->CheckPassword($pass, $user->user_pass);
          if ($passMatched) {
            $response->withStatus(200);
            $date_now = date("Y-m-d H:i:s");
            $token = WPAuthenticator::createJWTAccessToken(array("user_id" => $user->ID, "date_now" => $date_now));
            $user_token = new AccessToken();
            $user_token->addToken($token, $user->ID, $date_now);
            $user_token->save();
            // --- Get profile image --- //
            $option = new Option();
            $host = $option->getOptionValueByKey('siteurl');
            $user_id = $user->ID ;
            $directory = dirname(__FILE__)."/../../../../wp-content/uploads/avatars/$user_id/";
            $image_location = glob($directory . "*bpfull*");            
            foreach(glob($directory . "*bpfull*") as $image_name){
              $image_name = end(explode("/", $image_name));
              $image = $host."/wp-content/uploads/avatars/$user_id/".$image_name;
            }
            //var_dump($image_location);exit;
            // if image is not from buddypress and from social media //
            if (empty($image_location)){
              $meta_key = "wsl_current_user_image";
              $user_meta = new Usermeta();
              $meta = $user_meta->getUserMeta($user_id, $meta_key);
              $image = $meta;
              if (empty($meta)){ // -- default gravatar image -- //
                $email = $user->user_email;
                $size = '150'; //The image size
                $image = 'http://www.gravatar.com/avatar/'.md5($email).'?d=mm&s='.$size;
              }
            }

            //get role wpRuvF8_capabilities
            $user_meta_role = new Usermeta();
            $meta_role = $user_meta_role->getUserMeta($user_id, 'wpRuvF8_capabilities');
            $role = unserialize($meta_role);
            foreach(array_keys($role) as $key){
              $role = $key;
            }
            $user_data = new Usermeta();
            $is_expert = $user_data->getUserMeta($user->ID,"is_expert");
            
            $userMeta = Usermeta::where( 'user_id', '=', $user_id )->where( "meta_key", "=", "registration_data" )->first();
            $registeration_data = unserialize( $userMeta->meta_value );
            $registeration_data = ( is_array( $registeration_data ) ) ? $registeration_data : unserialize($registeration_data);

            $result = array(
                      'token' => $token->__toString(),
                      'display_name' => $user->display_name,
                      'username' => $user->user_nicename,
                      'role' => $role,
                      'type'  => $registeration_data['type'],
                      'profile_picture' => $image,
                      'is_expert' => ($is_expert)?true:false,
                      );
            return $this->renderJson($response, 200, $result);
          } else {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("incorrect", "Password"));
          }
        }
      } else {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "User"));
      }
    } else if (empty($loginName)) {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Username"));
    } else if (empty($pass)) {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Password"));
    }
    return $result;
  }
  
  /**
   * @SWG\POST(
   *   path="/login/social",
   *   tags={"Register & Login"},
   *   summary="Social Login",
   *   description="login user by his social network account",
   * 	 @SWG\Parameter(name="id", in="formData", required=false, type="integer", description="Social media identifier provided from each social media<br/><b>[Required]</b>"),
   *   @SWG\Parameter(name="provider", in="formData", required=false, type="string", description="Set provider to be one of these<br/><b>Values:</b><br/> 1.Facebook<br/>2.Twitter<br/>3.LinkedIn<br/>4.Google<br/><b>[Required]</b>"),
   *   @SWG\Parameter(name="username", in="formData", required=false, type="string", description="Please enter your username that will be used as your name in the system<br/><b>[Required]</b>"),
   *   @SWG\Parameter(name="email", in="formData", required=false, type="string", description="Please type your email to be saved in the system<br/><b>[Required]</b>"),
   *   @SWG\Parameter(name="emailVerified", in="formData", required=false, type="string", description="Provides if the email is verified from social media or not verified<br/><b>Values:</b><br/>yes or no and default is no<br/><b>[Required]</b>"),
   *   @SWG\Parameter(name="photourl", in="formData", required=false, type="string", description="Set a photoUrl to set as your photo in the system"),
   *   @SWG\Response(response="200", description="")
   * )
   */
  public function AuthenticateWithSocialMedia($request, $response, $args) {
    $result = "";
    $parameters = ['username', 'email', 'id', 'provider', 'photourl', 'emailVerified'];
    $requiredParams = ['username', 'id', 'provider'];
    global $ef_wsl_social_login_providers;
		foreach ($_POST as $key => $value) {
      if (in_array($key, $parameters)) {
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }else
      {
        $args[$key] = "";
      }
    }
    if(!empty($requiredParams))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(!in_array($args["provider"], $ef_wsl_social_login_providers))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Provider"));
    }
    if ($args["email"] && filter_var($args['email'], FILTER_VALIDATE_EMAIL) === false) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Email"));
    }
    if (isset($args['photourl']) && filter_var($args['photourl'], FILTER_VALIDATE_URL) === false) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "url"));
    }
    
    $isAddingNewUser = false;
    $RegisteredBefore = User::where(function ($query) use ($args) {
        $query->where('user_email', '=', $args["email"]);
          //->orWhere('user_login', '=', $args["username"]);
    })->get()->first();    
    $userProfile = UserProfile::getSocialProfileID($args);
    if($userProfile != null) {
      $userID = $userProfile;
      $socialProfile = $userProfile;
    }else {
      $user = User::Where('ID', '=', $userProfile);
      $RegisteredBefore = User::where(function ($query) use ($args) {
          $query->where('user_email', '=', $args["email"]);
            //->orWhere('user_login', '=', $args["username"]);
        })->get()->first();
      if ($userProfile && $user->first() && !empty($args['email']) && $user->first()->user_email != $args['email']) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "user profile id"));
      }
      $socialProfile = UserProfile::getSocialProfile($args);
      if ($socialProfile && !$user && !empty($args['email']) && $socialProfile->email != $args['email']) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "user profile id"));
      }
      $userID = empty(!$user->first()) ? $user->first()->ID : '';
      if($userProfile != null) {
        $userID = $userProfile;
      }else {
        if (!empty($args["email"])) {
          $isAddingNewUser = true;
          switch ($args["emailVerified"]) {
            case "yes":
              if (empty($user->first())) {
                $newUser = new User();
                $userID = $newUser->socialAuthenticateNewUser($args, $socialProfile);
              }
              break;
            case "no":
              if (empty($RegisteredBefore)) {
                $newUser = new User();
                $userID = $newUser->socialAuthenticateNewUser($args, $socialProfile);
              } else {
                return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "email"));
              }
              break;
            default:
              if (empty($RegisteredBefore) ) {
                $newUser = new User();
                $userID = $newUser->socialAuthenticateNewUser($args, $socialProfile);
              } else {
                return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "email"));
              }
              break;
          }
        } else {
          if($user->first())
          {
            $userID = $user->first()->ID;
          }else
          {
            return $this->renderJson($response, 422, Messages::getErrorMessage("incorrect", "profileID"));
          }
        }
      }
    }
    $option = new Option();
    $host = $option->getOptionValueByKey('siteurl');
    $user_id = $userID;
    $directory = dirname(__FILE__) . "/../../../../wp-content/uploads/avatars/$user_id/";
    $image_location = glob($directory . "*bpfull*");
    foreach (glob($directory . "*bpfull*") as $image_name) {
      $image_name = end(explode("/", $image_name));
      $image = $host . "/wp-content/uploads/avatars/$user_id/" . $image_name;
    }
    
    if (empty($image_location)) {
      $meta_key = "wsl_current_user_image";
      $user_meta = new Usermeta();
      $meta = $user_meta->getUserMeta($user_id, $meta_key);
      $image = $meta;
      if (empty($meta)) { // -- default gravatar image -- //
        $user_profile_picture = User::Where('ID', '=', $user_id)->first();
        $email = $user_profile_picture->user_email;
        $size = '150'; //The image size
        $image = 'http://www.gravatar.com/avatar/' . md5($email) . '?d=mm&s=' . $size;
      }
    }
    $args['photourl'] = isset($args['photourl']) ? html_entity_decode($args['photourl']) : $image;
    if (!empty($args['photourl'])) {
      $meta_keys = array("wsl_current_user_image" => $args['photourl'], "wsl_current_provider" => $args['provider']);
    }else
    {
      $meta_keys = array("wsl_current_provider" => $args['provider']);
    }
    Usermeta::addSocialMeta($meta_keys, $userID);
    if(!empty($args['email']))
    {
      if (!$socialProfile) {
        $userProfile = new UserProfile();
        $args["user_id"] = $userID;
        $userProfile->AddSocialProfile($args);
        $userProfile->save();
      }
    }

    // Load user info
    $user = User::where("ID", "=", $userID)->first();
    
    if($RegisteredBefore != null && $args["emailVerified"] == "yes"
            && $RegisteredBefore->user_status == 2)
    {
      //activate account
      $updateUser = User::where('ID', '=', $userID);
      $updateUser->update(array('user_status' => 0,'display_name' => $args["username"]));

      //set user role 
      $meta = new Usermeta();
      $meta->addUserRole($userID,"contributor");
      
      //set expert to 0
      $expertMeta = $meta->addUserMeta(array('user_id'=>$userID,'key'=>'is_expert','value'=>0));
      $expertMeta->save();
      
      //remove activation usermeta
      $meta->deleteUsermeta($userID, "activation_key");
      
      //set signup table to activated
      $signUp = Signup::where('user_email','=', $RegisteredBefore->user_email)
                ->where('user_login', '=', $RegisteredBefore->user_login);
      if($signUp->first())
      {
        $signUp->update(array('active' => 1,'activated' => date('Y-m-d H:i:s')));
      }
    } else if($isAddingNewUser && $args["emailVerified"] == "no")
    {
      // set user as subscriber and send activation email to activate this account
      $activation_key = wp_helper::wp_hash($userID);
      
      //add user to sign up
      $signUp_args = array(
          "username" => $user->user_login,
          "email" => $user->user_email,
          "activation_key" => $activation_key,
          "password" => ''
      );
      $signup = new Signup();
			$signup->addSignup($signUp_args);
			$signup->save();
      
      //update usermeta
      $role = array('subscriber' => 1);
      $userMeta = new Usermeta();
      $userMeta->updateUserMeta($userID, 'wpRuvF8_capabilities', serialize($role));
      $userMeta->updateUserMeta($userID, 'wpRuvF8_user_level', 0);
      $userMeta->updateUserMeta($userID, 'activation_key', $activation_key);
      
      //update user status
      $updateUser = User::where('ID', '=', $userID);
      $updateUser->update(array('user_status' => 2));
      
      $args = array(
				"sender" => "testapp@espace.ws",
				"to" => array("email" => $user->user_email , "name" => $user->user_login),
				"key" => $activation_key,
				"user_login" => $user->user_login,
        "lang" => "en"
      );
      $mailer = new ActivationMailer();
      $mailer->sendActivationMessage($args, $response);
    }
    
    $date_now = date("Y-m-d H:i:s");
    $token = WPAuthenticator::createJWTAccessToken(array("user_id" => $userID, "date_now" => $date_now));
    $user_token = new AccessToken();
    $user_token->addToken($token, $userID, $date_now);
    $user_token->save();

    //get role wpRuvF8_capabilities
    $user_meta_role = new Usermeta();
    $meta_role = $user_meta_role->getUserMeta($userID, 'wpRuvF8_capabilities');
    $role = unserialize($meta_role);
    if(!is_array($role))
    {
      $role = unserialize($role);
    }
    foreach (array_keys($role) as $key) {
      $role = $key;
    }
    $user_data = new Usermeta();
    $is_expert = $user_data->getUserMeta($user->ID, "is_expert");
    $result = array(
      'token' => $token->__toString(),
      'display_name' => $user->display_name,
      'username' => $user->user_nicename,
      'role' => $role,
      'profile_picture' => (isset($args['photourl']))?html_entity_decode($args['photourl']):$host.'/wp-content/themes/egyptfoss/img/default_avatar.png',
      'is_expert' => ($is_expert) ? true : false,
    );
    return $this->renderJson($response, 200, $result);
  }

  /**
   * @SWG\POST(
   *   path="/reset-password",
   *   tags={"Register & Login"},
   *   summary="Reset Password",
   *   description="reset not logged in user password",
   * 	 @SWG\Parameter(name="email", in="formData", required=false, type="string", description="Pass Username or Email to reset password <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Define User Predefined Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="send message for password reset instructions")
   *   )
   */
  public function resetPasswordRequest($request, $response, $args) {
    
    $email = $_POST["email"];
    if (empty($email)) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    } 
    if (!isset($_POST["lang"]) || ($_POST["lang"] != "en" && $_POST["lang"] != "ar")) {
		  return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    $updateUser = User::Where('user_email', '=', $email)->orWhere('user_login', '=', $email);
    $user = $updateUser->get()->first();
    if (!$user) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "User"));   
    }   
    $key = wp_helper::wp_generate_password( 20, false );
    $wp_hasher = new PasswordHash( 8, true );
    $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
    $updateUser->update(array('user_activation_key' => $hashed));
    $args = array(
      "sender" => "testapp@espace.ws",
      "to" => array("email" => $user->user_email , "name" => $user->user_login),
      "key" => $key,
      "user_login" => $user->user_login,
      "lang" => $_POST["lang"]
    );
    $mailer = new ResetPasswordMailer ;
    $isSent = $mailer->sendResetPasswordMessage($args, $response);
    if($isSent != true) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", 'Mailer Error: ' . $isSent));
    }
    return $this->renderJson($response, 200, Messages::getSuccessMessage("sentSuccessfully", "reset password message"));
  }
  
  public function createJWTAccessToken($data) {
    $signer = new Hmac\Sha256();
    $secret = Option::getOption('foss_api_keys',"secret");
    $token = (new Builder()) // Configures the issuer (iss claim)
      ->set('data', $data) // Configures a new claim, called "uid"
      ->sign($signer, $secret)
      ->getToken(); // Retrieves the generated token
    return $token;
  }
  
  /**
  * @SWG\Post(
  *   path="/logout",
  *   tags={"Register & Login"},
  *   summary="Logout",
  *   description="Logout user by invalidationg his token",
  *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to logout<br/> <b>[Required]</b>"),
  *   @SWG\Response(response="200", description="User Logout")
  * )
  */
  public function revoke($request, $response, $args) {
    $result = "";
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    if ($loggedin_user !== null) {
      AccessToken::destroy($loggedin_user->id);
      $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "User logged out"));
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }
  
  /**
  * @SWG\Post(
  *   path="/sys-data",
  *   tags={"Register & Login"},
  *   summary="System Data",
  *   description="Get system Data by language and type",
  *   @SWG\Parameter(name="lang", in="formData", required=true, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar<br/> <b>[Required]</b>"),
  *   @SWG\Parameter(name="type", in="formData", required=false, type="string", enum={"Individual", "Entity"}, description="data retrieved depeding on type <br/> <b>Values: </b> [Individual, Entity, Event]"),
  *   @SWG\Response(response="200", description="System Data")
  * )
  */
  public function sysData($request, $response, $args) {
    global $account_types;
		global $account_sub_types;
    $result = array();
		$parameters = ['lang', 'type'];
		foreach($_POST as $key => $value){
			if(in_array($key, $parameters)) {
				$args[$key] = $value;
			}
		}

    if (!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")) {
		  $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
		}
    else{
      if($args["lang"] == 'ar') {
        global $ar_sub_types;
        $sub_types = $ar_sub_types;
        
        if (empty($args['type'])){
          $result = $ar_sub_types;
        }
        else{
          foreach ($account_sub_types as $sub => $t) {
            if ($args['type'] == $t){
              array_push($result, $sub_types[$sub]) ;
            }
          }
        }
      } 
      else { // lang is english
        global $en_sub_types;
        $sub_types = $en_sub_types;

        if (empty($args['type'])){
          $result = $en_sub_types;
        }
        else{
          foreach ($account_sub_types as $sub => $t) {
            if ($args['type'] == $t){
              array_push($result, $sub_types[$sub]) ;
            }
          }
        }
      }
      return $this->renderJson($response, 200, $result);
    }
    return $result;
  }
}

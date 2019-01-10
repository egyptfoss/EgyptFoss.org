<?php

class WPRegistrationController extends EgyptFOSSController {

	/**
	 * @SWG\Post(
	 *   path="/register",
	 *   tags={"Register & Login"},
	 *   summary="Register",
	 *   description="Register new user",
	 *   @SWG\Parameter(name="username", in="formData", required=false, type="string", description="Add Username to login with <br/> <b>Validations: </b><br/>1. Unique<br/>2.Contains characters, numbers and [.,_]<br/>3.Contains at least 1 character  <br/>4.Length > 4 and < 20 <br/><b>[Required]</b>"),
	 *   @SWG\Parameter(name="email", in="formData", required=false, type="string", description="Add Email to login with <b>Validations: </b><br/>1. Unique<br/>2.Valid Email <br/><b>[Required]</b>"),
	 *   @SWG\Parameter(name="password", in="formData", required=false, type="string", format="password", description="Add User Password to login with <br/><b>Validations: </b><br/>1. Length >= 8 <br/><b>[Required]</b>"),
	 *   @SWG\Parameter(name="type", in="formData", required=false, type="string", enum={"Individual", "Entity"}, description="Set User Type <br/> <b>Values:</b><br/> Individual or Entity<br/><b>[Required]</b>"),
	 *   @SWG\Parameter(name="sub_type", in="formData", required=false, type="string", description="Select User Sub Type<br/> <b>Values:</b><br/> Any of predefined sub-types in setup data<br/><b>[Required]</b>"),
	 *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Define User Predefined Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="registeration form")
	 * )
	 */
	public function Register($request, $response, $args) {
		global $account_types;
		global $account_sub_types;
		$result = "";
		$parameters = ['username', 'email', 'password', 'type', 'sub_type', 'lang'];
		$required_params = ['username', 'email', 'password', 'type', 'sub_type'];
    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $_POST) && !empty($_POST[$parameter])) {
        $args[$parameter] = $_POST[$parameter];
      } else {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }

		if (preg_match('/[^a-zA-Z0-9._-]+/', $args['username'], $matches) || ctype_digit($args['username'])) {
			$result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Username"));
		} else if (!empty(User::where('user_login' , '=', $args['username'])->first())) {
			$result = $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "Username"));
		} else if (( strlen($args['username']) < 4 ) || ( strlen($args['username']) > 20 )) {
			$result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between", "Username",array("range"=>'4 to 20 characters')));
		} else if (!empty(User::where('user_email' , '=', $args['email'])->first())) {
			$result = $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "Email"));
		} else if(filter_var($args['email'], FILTER_VALIDATE_EMAIL) === false) {
			$result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Email"));
		} else if(!in_array($args['type'], $account_types)) {
			$result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Type"));
		} else if(!(array_key_exists($args['sub_type'], $account_sub_types) && $account_sub_types[$args['sub_type']] == $args['type'])) {
			$result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Sub Type"));
		} else if (strlen($args['password']) < 8) {
		  $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-more", "Password", array("range"=>'8 characters')));
    } else if (!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")) {
		  $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
		} else {
			// convert password
			$hasher = new PasswordHash(8, true);
			$args['password'] = $hasher->HashPassword( trim( $args['password'] ) );
			// insert user
			$user = new User;
			$user->addUser($args);
      $user->user_status = 2;
      $user->save();
      
      $args['registeredNormally'] = 1;
      
			// insert meta data
			$user_meta = new Usermeta;
			$user_meta->addMeta($user->ID, $args);
			$user_meta->save();
      
      // insert type
      $default_type = 'Individual';
      $type = (array_key_exists('type', $_POST)) ? $_POST['type'] : $default_type;
      $user_meta_type = new Usermeta;
      $user_meta_type->user_id = $user->ID;
      $user_meta_type->meta_key = 'type';
      $user_meta_type->meta_value = $type;
      $user_meta_type->save();
      
      // insert signup for buddypress
      $key = wp_helper::wp_hash($user->ID);
      $args["activation_key"] = $key;
      $user_meta_bp = new Usermeta;
      $user_meta_bp->user_id = $user->ID;
      $user_meta_bp->meta_key = 'activation_key';
      $user_meta_bp->meta_value = $args["activation_key"];
      $user_meta_bp->save();
      
      $signup = new Signup();
			$signup->addSignup($args);
			$signup->save();
      $args = array(
				"sender" => "testapp@espace.ws",
				"to" => array("email" => $user->user_email , "name" => $user->user_login),
				"key" => $key,
				"user_login" => $user->user_login,
        "lang" => $args["lang"],
      );
      $mailer = new ActivationMailer();
      $isSent = $mailer->sendActivationMessage($args, $response);
      if($isSent != true) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", 'Mailer Error: ' . $isSent));
      }
      $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Registered"));
		}
		return $result;
	}
}
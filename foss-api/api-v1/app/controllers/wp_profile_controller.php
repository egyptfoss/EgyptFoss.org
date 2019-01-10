<?php

class WPProfileController extends EgyptFOSSController {
  
  /**
   * @SWG\Put(
   *   path="/profiles/me/info",
   *   tags={"Profile"},
   *   summary="Edits Profile Information",
   *   description="Edits profile information of the logged in user",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to update user info<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="sub_type", in="formData", required=false, type="string", description="Specify Sub-type <br/><b>Values:</b>One of predefined sub-types in system data<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="display_name", in="formData", required=false, type="string", description="Set Display Name <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="functionality", in="formData", required=false, type="string", description="Set User Functionality <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="theme", in="formData", required=false, type="string", description="Set Profile Theme <br/><b>Values: <b> English or Arabic Name or ID"),
   *   @SWG\Parameter(name="ict_technology", in="formData", required=false, type="string", collectionFormat="multi" , description="Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="address", in="formData", required=false, type="string", description="User address <br/><b>Validations: </b><br/> 1. Contains at least 1 character "),
   *   @SWG\Parameter(name="phone", in="formData", required=false, type="string", description="User Phone <br/><b>Validations: </b><br/> 1. Valid Phone format (numbers only)"),
   *   @SWG\Parameter(name="facebook_url", in="formData", required=false, type="string", description="Facebook URL <br/><b>Validations: </b><br/> 1. Valid Url"),
   *   @SWG\Parameter(name="twitter_url", in="formData", required=false, type="string", description="Twitter URL <br/><b>Validations: </b><br/> 1. Valid Url"),
   *   @SWG\Parameter(name="gplus_url", in="formData", required=false, type="string", description="Google+ URL <br/><b>Validations: </b><br/> 1. Valid Url"),
   *   @SWG\Parameter(name="linkedin_url", in="formData", required=false, type="string", description="LinkedIn URL <br/><b>Validations: </b><br/> 1. Valid Url"),
   *   @SWG\Parameter(name="interests", in="formData", required=false, type="string", description="Interests <br/><b>Values: </b> Multiple values with comma seperated between each value" ),
   *   @SWG\Parameter(name="contact_name", in="formData", required=false, type="string", description="Contact Name in case of Entity Type <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="contact_email", in="formData", required=false, type="string", description="Contact Email in case of Entity Type<br/><b>Validations: </b><br/> 1. Valid Email"),
   *   @SWG\Parameter(name="contact_address", in="formData", required=false, type="string", description="Contact Address in case of Entity Type <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="contact_phone", in="formData", required=false, type="string", description="Contact Phone in case of Entity Type <br/><b>Validations: </b><br/> 1. Valid Phone format (numbers only)"),
   *   @SWG\Response(response="200", description="Editing user profile successfully"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Profile not found")
   * )
   */
	public function editProfile($request, $response, $args) {
    $params = $request->getHeaders();
    $put = $request->getParsedBody();
    global $account_sub_types;
    $parameters = ['token', 'sub_type', 'display_name', 'functionality', 'theme', 'ict_technology', 
                   'address', 'phone', 'facebook_url', 'twitter_url', 'gplus_url', 
                   'linkedin_url', 'interests', 'contact_name', 'contact_email', 
                   'contact_address', 'contact_phone'];
    foreach($parameters as $parameter){
      $args[$parameter] = (array_key_exists($parameter, $put)) ? $put[$parameter] : '';
    }
    $result = "";
    //$loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'] )->first()) : null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else {
        $current_term_taxonomy_ids = array();

        if(empty($args['sub_type'])) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","sub_type"));
        }

        if(empty($args['display_name'])) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","display_name"));
        }
        
        if(!preg_match('/[أ-يa-zA-Z]+/', $args['display_name'], $matches) && strlen($args['display_name'])> 0){
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","display_name"));
        }
        
        // validate data
        $user_meta = Usermeta::where('user_id', '=', $user_id)->where('meta_key', '=', 'registration_data')->first();
        if ($user_meta === null) {
          $user_meta = new Usermeta();
        }
        $meta = $user_meta->getMeta($user_id);
        if (array_key_exists($args['sub_type'], $account_sub_types) && $account_sub_types[$args['sub_type']] == $meta['type']) {
          $meta['sub_type'] = $args['sub_type'];
        } else {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Sub Type"));
        }
        if (empty($args['functionality']) || preg_match('/[أ-يa-zA-Z]+/', $args['functionality'], $matches)) {
          $meta['functionality'] = $args['functionality'];
        } else {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Functionality"));
        }
        $term_taxonomy = $this->check_term_exists($args['theme'], 'theme');
        if (empty($args['theme'])) {
          $meta['theme'] = $args['theme'];
        } else if (empty($term_taxonomy)) {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Theme"));
        } else {
          $term_taxonomy_id = (isset($term_taxonomy['id'])) ? $term_taxonomy['id'] : $term_taxonomy['term_taxonomy_id'];
          $meta['theme'] = $term_taxonomy['term_id'];
          $user_relation = $this->check_user_relation($user_id, $term_taxonomy_id);
          if(count($user_relation) == 0) {
            $this->add_user_relation($user_id, $term_taxonomy_id);
          }
          array_push($current_term_taxonomy_ids, $term_taxonomy_id);
        }
        $technologies = trim($args['ict_technology'],',');
        $technologies = str_getcsv($technologies);
        foreach ($technologies as $technology) {
          if (preg_match('/[أ-يa-zA-Z]+/', $technology, $matches)) {
            $term_taxonomy = $this->check_term_exists($technology, 'technology');
            if (empty($term_taxonomy)) {
              $term_taxonomy = $this->insert_term( $technology, 'technology', array('description'=>$technology) );
            }
            $term_taxonomy_id = (isset($term_taxonomy['id'])) ? $term_taxonomy['id'] : $term_taxonomy['term_taxonomy_id'];
            $user_relation = $this->check_user_relation($user_id, $term_taxonomy_id);
            if(count($user_relation) == 0) {
              $this->add_user_relation($user_id, $term_taxonomy_id);
            }
            array_push($current_term_taxonomy_ids, $term_taxonomy_id);
          } else if (!empty($technology)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("formatError", "Technology"));
          }
        }
        if (empty($args['address']) || preg_match('/[أ-يa-zA-Z]+/', $args['address'], $matches)) {
          $meta['address'] = $args['address'];
        } else {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Address"));
        }
        if (!empty($args['phone']) && (preg_match('/[^0-9 \/+\(\)-]+/', $args['phone'], $matches) || (!preg_match('/[0-9]+/', $args['phone'], $matches)))) {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Phone"));
        } else {
          $meta['phone'] = $args['phone'];
        }
        if (!empty($args['facebook_url']) && filter_var($args['facebook_url'], FILTER_VALIDATE_URL) === false) {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Facebook URL"));
        } else {
          $meta['facebook_url'] = $args['facebook_url'];
        }
        if (!empty($args['twitter_url']) && filter_var($args['twitter_url'], FILTER_VALIDATE_URL) === false) {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Twitter URL"));
        } else {
          $meta['twitter_url'] = $args['twitter_url'];
        }
        if (!empty($args['gplus_url']) && filter_var($args['gplus_url'], FILTER_VALIDATE_URL) === false) {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Google Plus URL"));
        } else {
          $meta['gplus_url'] = $args['gplus_url'];
        }
        if (!empty($args['linkedin_url']) && filter_var($args['linkedin_url'], FILTER_VALIDATE_URL) === false) {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "LinkedIn URL"));
        } else {
          $meta['linkedin_url'] = $args['linkedin_url'];
        }
        $interests = rtrim($args['interests'],',');
        $interests = str_getcsv($interests);
        foreach ($interests as $interest) {
          if (preg_match('/[أ-يa-zA-Z]+/', $interest, $matches)) {
            $term_taxonomy = $this->check_term_exists($interest, 'interest');
            if (empty($term_taxonomy)) {
              $term_taxonomy = $this->insert_term( $interest, 'interest', array('description'=>$interest) );
            }
            $term_taxonomy_id = (isset($term_taxonomy['id'])) ? $term_taxonomy['id'] : $term_taxonomy['term_taxonomy_id'];
            $user_relation = $this->check_user_relation($user_id, $term_taxonomy_id);
            if(count($user_relation) == 0) {
              $this->add_user_relation($user_id, $term_taxonomy_id);
            }
            array_push($current_term_taxonomy_ids, $term_taxonomy_id);
          } else if (!empty($interest)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("formatError", "Interest"));
          }
        }
        // In case of entity profile type, validate contact info
        if (isset($meta['type']) && ($meta['type'] == 'Entity')) {
          if (!empty($args['contact_name']) && !preg_match('/[أ-يa-zA-Z]+/', $args['contact_name'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Contact Name"));
          } else {
            $meta['contact_name'] = $args['contact_name'];
          }
          if(!empty($args['contact_email']) && filter_var($args['contact_email'], FILTER_VALIDATE_EMAIL) === false) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Contact Email"));
          } else {
            $meta['contact_email'] = $args['contact_email'];
          }
          if (!empty($args['contact_address']) && !preg_match('/[أ-يa-zA-Z]+/', $args['contact_address'], $matches)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Contact Address"));
          } else {
            $meta['contact_address'] = $args['contact_address'];
          }
          if (!empty($args['contact_phone']) && (preg_match('/[^0-9 \/+\(\)-]+/', $args['contact_phone'], $matches) || (!preg_match('/[0-9]+/', $args['contact_phone'], $matches)))) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Contact Phone"));
          } else {
            $meta['contact_phone'] = $args['contact_phone'];
          }
        }
        // If no errors so far
        if ($result == "") {
          $user_meta->addMeta($user->ID, $meta);
          $user_meta->save();
          
          //check display name
          if(!empty($args['display_name']))
          {
            $user->display_name = $args['display_name'];
            $user->save();
          }
          
          //  save user data to marmotta
          $search = new SearchController;
          $search->save_post_to_marmotta( $user->ID, $user->display_name, $meta['functionality'], $meta['type'] );
          
          $done = $this->remove_user_relations($user_id, $current_term_taxonomy_ids);
          $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "User Profile Saved"));
        }
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
	}

  /**
   * @SWG\Post(
   *   path="/profiles/me/pictures",
   *   consumes={"multipart/form-data"},
   *   tags={"Profile"},
   *   summary="Changes Profile Picture",
   *   description="Change profile picture of the logged in user",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to change user profile picture<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="profile_photo", in="formData", required=false, type="file", description="User Profile Photo <br/><b>Validations: </b><br/> 1. Valid Image <br/> <b>[Required]</b>"),
   *   produces={"application/json"},
   *   @SWG\Response(response="200", description=" Profile Photo changed successfully"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function changeProfilePhoto($request, $response, $args) {
    $result = "";
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else {
        if(isset($_FILES["profile_photo"]) && ($_FILES["profile_photo"]["error"] == UPLOAD_ERR_OK)) {
          $user_upload_directory = dirname(__FILE__)."/../../../../wp-content/uploads/avatars/$user_id/"; //upload directory ends with / (slash)
          if ($_FILES["profile_photo"]["size"] > 5242880) { // Check file size.
            $result = $this->renderJson($response, 404, Messages::getErrorMessage("incorrect", "File Size"));
          }
          switch(strtolower($_FILES['profile_photo']['type'])) { // Check file type.
            case 'image/png': 
            case 'image/jpeg': 
            case 'image/pjpeg':
            case 'text/plain':
            case 'application/pdf':
              break;
            default:
              $result = $this->renderJson($response, 404, Messages::getErrorMessage("incorrect", "File Extension"));
          }
    
          $file_name     = strtolower($_FILES['profile_photo']['name']);
          $file_ext      = substr($file_name, strrpos($file_name, '.'));
          $random_number = rand(0, 9999999999);
          $new_file_name = $random_number.$file_ext; //new file name

          if (!file_exists($user_upload_directory)) {
            mkdir($user_upload_directory, 0777, true);
          } else {
            $files = glob($user_upload_directory."*");
            foreach($files as $file){
              if(is_file($file)) {
                unlink($file);
              }
            }
          }
          $profile_full = $user_upload_directory.$random_number.'-bpfull'.$file_ext;
          $profile_thumb = $user_upload_directory.$random_number.'-bpthumb'.$file_ext;
          if(move_uploaded_file($_FILES['profile_photo']['tmp_name'], $profile_full )) {
            // TODO YF: Save User Profile Picture in full & thumb sizes
            copy($profile_full, $profile_thumb);
            $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "User Profile Photo Changed"));
          } else {
            $result = $this->renderJson($response, 404, Messages::getErrorMessage("incorrect", "File Upload"));
          }
        } else {
          $result = $this->renderJson($response, 404, Messages::getErrorMessage("emptyValue", "User Profile Photo"));
        }
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }  

  /**
   * @SWG\GET(
   *   path="/profile/{profileName}/info",
   *   tags={"Profile"},
   *   summary="Finds Profile Info",
   *   description="Returns all profile information of the passed profile name",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list user info"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Response(response="200", description="View Profile Data"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function viewProfile($request, $response, $args){
      //get token
      $params = $request->getHeaders();
      if($args['profileName'] == '{profileName}')
      {
        $args['profileName'] = '';
      }
      if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
      }

      $user = null;
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if ($loggedin_user !== null) {
        $user_id = $loggedin_user->user_id;
        $user = User::find($user_id);
        if (empty($user)){
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        }
      } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
      }

      //check both user & profile name
      $is_my_profile = false;
      if($user != null && $args['profileName'] != '')
      {
        if($user->user_nicename == $args['profileName'])
        {
          $is_my_profile = true;
        }
      }else if($user != null && $args['profileName'] == '')
      {
        $is_my_profile = true;
      }
      
        if(!$is_my_profile)
        {
          $user = new User();
          $user = $user->getUser($args['profileName']);

          if (empty($user)){
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
          }
          if ($user['user_status'] == 1 ) {
            return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
          }
        }
        
        $user_id = $user->ID;
        $user_data = new Usermeta();
        $user_meta = $user_data->getMeta($user_id);
        
        $is_expert = $user_data->getUserMeta($user->ID,"is_expert");
      
        // --- Get profile image --- //
        $option = new Option();
        $host = $option->getOptionValueByKey('siteurl');
        $directory = dirname(__FILE__)."/../../../../wp-content/uploads/avatars/$user_id/";
        $image_location = glob($directory . "*bpfull*");            
        foreach(glob($directory . "*bpfull*") as $image_name){
          $image_name = end(explode("/", $image_name));
          $image = $host."/wp-content/uploads/avatars/$user_id/".$image_name;
        }
        
        // if image is not from buddypress and from social media //
        if (empty($image_location)){
          $meta_key = "wsl_current_user_image";
          $user_meta_image = new Usermeta();
          $meta = $user_meta_image->getUserMeta($user_id, $meta_key);
          $image = $meta;
          if (empty($meta)){ // -- default gravatar image -- //
            $email = $user->user_email;
            $size = '150'; //The image size
            $image = 'http://www.gravatar.com/avatar/'.md5($email).'?d=mm&s='.$size;
          }
        }
        
        if(!$is_my_profile)
        {
          $profile_data['display_name'] = $user->display_name;
          $profile_data['user_email'] = $user->user_email;
          $profile_data['type'] = array_key_exists('type', $user_meta) ? $user_meta['type']  : "";
          $profile_data['sub_type'] = array_key_exists('sub_type', $user_meta) ? $user_meta['sub_type']  : "";
          $profile_data['functionality'] = array_key_exists('functionality', $user_meta) ? $user_meta['functionality']  : "";
          $profile_data['theme'] = array_key_exists('theme', $user_meta) ? $user_meta['theme']  : "";
          $profile_data['ict_technology'] = $this->get_user_taxonomies($user_id, 'technology');
          $profile_data['facebook_url'] = array_key_exists('facebook_url', $user_meta) ? $user_meta['facebook_url']  : "";
          $profile_data['twitter_url'] = array_key_exists('twitter_url', $user_meta) ? $user_meta['twitter_url']  : "";
          $profile_data['gplus_url'] = array_key_exists('gplus_url', $user_meta) ? $user_meta['gplus_url']  : "";
          $profile_data['linkedin_url'] = array_key_exists('linkedin_url', $user_meta) ? $user_meta['linkedin_url']  : "";
          $profile_data['interest'] = $this->get_user_taxonomies($user_id, 'interest');

          if ( $profile_data['type'] == "Entity"){  // If user is Entity shows his fields //
            $profile_data['contact_name'] = array_key_exists('contact_name', $user_meta) ? $user_meta['contact_name']  : "";
            $profile_data['contact_email'] = array_key_exists('contact_email', $user_meta) ? $user_meta['contact_email']  : "";
            $profile_data['contact_address'] = array_key_exists('contact_address', $user_meta) ? $user_meta['contact_address']  : "";
            $profile_data['contact_phone'] = array_key_exists('contact_address', $user_meta) ? $user_meta['contact_phone']  : "";
          }
          
          $profile_data['profile_picture'] =  $image;
          $profile_data['is_expert'] = ($is_expert)?true:false; 
          
          
          // Load user points
          $points = $user_data->getUserMeta($user->ID,"efb_points");
          $profile_data['points'] = ($points)?$points:0; 
          
          return $this->renderJson($response, 200, $profile_data);
        }else
        {
          $profile_data['display_name'] = $user->display_name;
          $profile_data['user_email'] = $user->user_email;
          $profile_data['type'] = array_key_exists('type', $user_meta) ? $user_meta['type']  : "";
          $profile_data['sub_type'] = array_key_exists('sub_type', $user_meta) ? $user_meta['sub_type']  : "";
          $profile_data['address'] = array_key_exists('address', $user_meta) ? $user_meta['address']  : "";
          $profile_data['phone'] = array_key_exists('phone', $user_meta) ? $user_meta['phone']  : "";
          $profile_data['functionality'] = array_key_exists('functionality', $user_meta) ? $user_meta['functionality']  : "";
          $profile_data['theme'] = array_key_exists('theme', $user_meta) ? $user_meta['theme']  : "";
          $profile_data['ict_technology'] = $this->get_user_taxonomies($user_id, 'technology');
          $profile_data['facebook_url'] = array_key_exists('facebook_url', $user_meta) ? $user_meta['facebook_url']  : "";
          $profile_data['twitter_url'] = array_key_exists('twitter_url', $user_meta) ? $user_meta['twitter_url']  : "";
          $profile_data['gplus_url'] = array_key_exists('gplus_url', $user_meta) ? $user_meta['gplus_url']  : "";
          $profile_data['linkedin_url'] = array_key_exists('linkedin_url', $user_meta) ? $user_meta['linkedin_url']  : "";
          $profile_data['interest'] = $this->get_user_taxonomies($user_id, 'interest');
          
          if ( $profile_data['type'] == "Entity") {
            $profile_data['contact_name'] = array_key_exists('contact_name', $user_meta) ? $user_meta['contact_name']  : "";
            $profile_data['contact_email'] = array_key_exists('contact_email', $user_meta) ? $user_meta['contact_email']  : "";
            $profile_data['contact_address'] = array_key_exists('contact_address', $user_meta) ? $user_meta['contact_address']  : "";
            $profile_data['contact_phone'] = array_key_exists('contact_address', $user_meta) ? $user_meta['contact_phone']  : "";
          }
          
          global $foss_prefix;
          
          $meta_capabilities = $user_data->getUserMeta( $user->ID, "{$foss_prefix}capabilities", true );
          $role_array = unserialize($meta_capabilities);
          if(!is_array($role_array))
          {
            $role_array = unserialize($role_array);
          }
          
          $roles = array_keys( $role_array );
          $profile_data['role'] =  isset( $roles[ 0 ] )?$roles[ 0 ]:'';
          $profile_data['profile_picture'] =  $image;
          $profile_data['is_expert'] = ($is_expert)?true:false; 

          //check if user has other profiles
          $has_password = true;
          $userProfilesCount = UserProfile::Where('user_id', '=', $user->ID)->count();
          if(!isset($user_meta["registeredNormally"]) ||  $user_meta['registeredNormally'] != 1) {
            $has_password = false;
          }
          $profile_data['has_password'] = $has_password;
          
          //check if account needs verification
          $needsVerification = true;
          if ($user_meta && isset($user_meta['registeredNormally']) && $user_meta['registeredNormally'] == 1) {
            $needsVerification = false;
          }
          
          if($user->user_status == 0)
          {
            $needsVerification = false;
          }
          
          $userMeta = new Usermeta();
          $activation_key = $userMeta->getUserMeta($user->ID, 'activation_key');
          if(!isset($activation_key) || empty($activation_key))
          {
            $needsVerification = false;
          }
          $profile_data['needs_verification'] = $needsVerification;
          // end of account verification
          
          
          // Load user points
          $points = $user_data->getUserMeta($user->ID,"efb_points");
          $profile_data['points'] = ($points)?$points:0; 
          
          return $this->renderJson($response, 200, $profile_data);
      }
  }

  /**
   * @SWG\Post(
   *   path="/profiles/me/passwords",
   *   tags={"Profile"},
   *   summary="Changes Password",
   *   description="Change password for the logged in user",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to change user password<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="current_password", in="formData", required=false, type="string", format="password", description="Current Password <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="new_password", in="formData", required=false, type="string", format="password", description="New Password to set <br/><b>Validations: </b><br/> 1. Length >= 8 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="repeat_password", in="formData", required=false, type="string", format="password", description="Repeat Password to confirm<br/><b>Validations: </b><br/> 1. Length >= 8 <br/> 2. Equal to Password <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Edit user profile"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function editGeneralSettings($request, $response, $args) {
    $result = "";
    $change_password = false;
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    if (!isset($_POST["lang"]) || ($_POST["lang"] != "en" && $_POST["lang"] != "ar")) {
		  return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else {
        // validate data
        $pass = (isset($_POST['current_password'])) ? $_POST['current_password'] : '';
        //check that user not logged in socially
        $user_meta = Usermeta::where('user_id', '=', $user->ID)->where('meta_key', '=', 'registration_data')->first();
        if ($user_meta === null) {
          $user_meta = new Usermeta();
        }
        $meta = $user_meta->getMeta($user->ID);
        $hasher = new PasswordHash(8, true);
        if(!isset($meta['registeredNormally']) || $meta['registeredNormally'] == 0)
        {
          $passMatched = true;
        }else {
          $passMatched = $hasher->CheckPassword($pass, $user->user_pass);
        }
        if (!$passMatched) 
        {
          $result = $this->renderJson($response, 404, Messages::getErrorMessage("incorrect", "Current Password"));
        } else {  // authorized user $user
          $new_pass1 = (isset($_POST['new_password'])) ? $_POST['new_password'] : '';
          $new_pass2 = (isset($_POST['repeat_password'])) ? $_POST['repeat_password'] : '';
          if (empty($new_pass1) && empty($new_pass2)) {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("noaction"));
          } else if (!empty($new_pass1)) {
            if (strlen($new_pass1) < 8) {
              $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-more", "New Password", array("range"=>'8 characters')));
            } else if ($new_pass1 !== $new_pass2) {
              $result = $this->renderJson($response, 422, Messages::getErrorMessage("mismatch", "Password"));
            } else {
              $new_pass = $hasher->HashPassword( trim( $new_pass1 ) );
              $user->user_pass = $new_pass;
              $change_password = true;
            }
          }

          // If no errors so far
          if ($result == "") {
            $args = array(
              "sender" => "testapp@espace.ws",
              "to" => array("email" => $user->user_email , "name" => $user->user_login),
              "user_login" => $user->user_login,
              "lang" => $_POST['lang']
            );
            if ($change_password) {
              $mailer = new ChangePasswordMailer();
              $isSent = $mailer->sendChangePasswordMessage($args, $response);
              if($isSent != true) {
                $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", 'Mailer Error: ' . $isSent));
              }
            }
            $user->save();
            
            //update usermeta to be registeredNormally
            $args_meta = array(
                'type' => $meta["type"],
                'sub_type' => $meta["sub_type"],
                'registeredNormally' => 1
            );
            $user_meta = new Usermeta();
            $meta_data = $user_meta->addMeta($user->ID, $args_meta, true);
            $user_meta_data = Usermeta::where('user_id', '=', $user->ID)
                    ->where('meta_key', '=', 'registration_data');
            if($user_meta_data->first())
            {
              $user_meta_data->update(array("meta_value"=> $meta_data));
            }
            
            $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Settings Saved"));
          }
        }
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }

  /**
   * @SWG\Post(
   *   path="/profiles/me/change-email",
   *   tags={"Profile"},
   *   summary="Changes my Email",
   *   description="Change email for the logged in user",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to change user email<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="current_password", in="formData", required=false, type="string", format="password", description="Current Password <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="new_email", in="formData", required=false, type="string",  description="New Email to set <br/><b>Validations: </b><br/> 1. Valid Email <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Edit user profile"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function editMyEmail($request, $response, $args) {
    $result = "";

    $params = $request->getHeaders();
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        return $result;
      } else {
        
        //check that user not logged in socially
        $user_meta = Usermeta::where('user_id', '=', $user->ID)->where('meta_key', '=', 'registration_data')->first();
        if ($user_meta === null) {
          $user_meta = new Usermeta();
        }
        $meta = $user_meta->getMeta($user->ID);
        
        // validate data
        $new_email = (isset($_POST['new_email'])) ? $_POST['new_email'] : '';
        $current_pass = (isset($_POST['current_password'])) ? $_POST['current_password'] : '';
        if(empty($new_email))
        {
            return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "New Email"));
        }
        
        if(empty($current_pass) && !(!isset($meta['registeredNormally']) || $meta['registeredNormally'] == 0))
        {
            return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Current Password"));
        }
        
        //check language
        if(!isset($_POST['lang']))
        {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "lang"));
        }
        
        if($_POST['lang'] != 'ar' && $_POST['lang'] != 'en')
        {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "lang"));
        }
        
        //check if current password is correct
        $hasher = new PasswordHash(8, true);
        if(!isset($meta['registeredNormally']) || $meta['registeredNormally'] == 0)
        {
          $passMatched = true;
        }else {
            $passMatched = $hasher->CheckPassword($current_pass, $user->user_pass);
        }
        
        if (!$passMatched) {
          return $this->renderJson($response, 404, Messages::getErrorMessage("incorrect", "Current Password"));
        }
        
        //check if valid email
        if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "New Email"));
        }
        
        //check that email not exist
        $other_user = User::where('ID','!=',$user->ID)->where('user_email','=',$new_email)->get();
        if(sizeof($other_user) > 0)
        {
            return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "New Email"));
        }
        
        //check it's not same email 
        if($user->user_email == $new_email)
        {
          return $this->renderJson($response, 422, Messages::getErrorMessage("same", "email"));
        }
        
        //Set pending email action with create of user emta
        $reset_array = array(
            'hash' => wp_helper::wp_hash($new_email),
            'newemail' => $new_email
        );
               
        $userMeta = Usermeta::where('user_id','=',$user->ID)->where('meta_key','=',"pending_email_change");
        if($userMeta->first())
        {
            $userMeta->update(array("umeta_id"=> $userMeta->first()->umeta_id,"meta_value"=> serialize($reset_array)));
        }else
        {
            $userMeta = new Usermeta();
            $userMeta->user_id = $user->ID;
            $userMeta->meta_key = "pending_email_change";
            $userMeta->meta_value = serialize($reset_array);
            $userMeta->save();
        }
        
        $args = array(
          "sender" => "testapp@espace.ws",
          "to" => array("email" => $new_email , "name" => $user->user_login),
          "hash" => $reset_array['hash'],
          "display_name" => $user->display_name,
          "lang" => $_POST['lang']
        );

        $mailer = new ChangeEmailMailer();
        $isSent = $mailer->sendChangeEmailMessage($args, $response);
        if($isSent != true) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", 'Mailer Error: ' . $isSent));
        }
        
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Settings Saved"));
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }  
  
  /**
   * @SWG\Post(
   *   path="/profiles/me/activities",
   *   tags={"Profile"},
   *   summary="Creates Activity",
   *   description="Creates new activity for the logged in user, and the function returns the activity id ,success message and success code",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to post new status<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="content", in="formData", required=false, type="string", description="Post Content <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="interests", in="formData", required=false, type="string", collectionFormat="multi" , description="Interests <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Response(response="200", description="Post update"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function addProfileUpdates($request, $response, $args) {
    $result = "";
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
      else {
        // validate data
        $result = "";
        if (isset($_POST['content']) || !empty($_POST['content'])) {   
          $meta['content'] = $_POST['content'];
        }
        else {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Content"));
        }
        
        //check user is not subscriber
        if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
        }

        // If no errors so far
        if ($result == "") {
          $activity = new Activity;
          $activity->addActivity($user->ID, $user->user_login, $meta['content']);
          $activity->save();

          if (isset($_POST['interests']) || !empty($_POST['interests'])) {
            $interests = rtrim($_POST['interests'],',');
            $interests = str_getcsv($interests);
            $array_interest = array();
            $termTax = new TermTaxonomy;
            // save uncreated interests
            $termTax->saveTermTaxonomies( array( "interest" => $interests ) );
            foreach ($interests as $interest) {
              if (preg_match('/[أ-يa-zA-Z]+/', $interest, $matches)) {
                $term_taxonomy = $this->check_term_exists($interest, 'interest');
                $term_id = $term_taxonomy->term_id;
                $taxonomy_name = $term_taxonomy->taxonomy ;
                if (!array_key_exists($taxonomy_name, $array_interest)){
                  $array_interest = array_merge($array_interest, array($taxonomy_name => array()));
                }
                $array_interest[$taxonomy_name] = array_merge(array($term_id), ($array_interest[$taxonomy_name]) );                
              } 
              else {
                $result = $this->renderJson($response, 422, Messages::getErrorMessage("formatError", "Interest"));
              }
            }
            
            if(array_key_exists('interest', $array_interest)){
              $activity_id = $activity->id;
              for($i = 0; $i < sizeof($array_interest['interest']); $i++)
              {
                $activity_meta = new Activitymeta;
                //$activity_meta->addActivityMeta($activity_id, 'interest', serialize(array_reverse($array_interest['interest'])));
                $activity_meta->addActivityMeta($activity_id, 'interest', $array_interest['interest'][$i]);
                $activity_meta->save();
              }
              $array_interest = array();
            }
          }
          if ($result == "") {
            $output =  Messages::getSuccessMessage("Success", "Post Update added");
            $output['id'] = $activity->id;
            $result = $this->renderJson($response, 200, $output);
          }  
        }
      }
    }
    else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    
    return $result;
  }

  /**
   * @SWG\Delete(
   *   path="/profiles/me/activities/{id}",
   *   tags={"Profile"},
   *   summary="Deletes Activity",
   *   description="Delete activity in the logged in user profile",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to delete an activity<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="id", in="formData", required=false, type="string", description="Post Id to remove <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Activity deleted successfully"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function deleteProfileUpdate($request, $response, $args) {
    $params = $request->getParsedBody();
    $loggedin_user = isset($params['token']) ? (AccessToken::where('access_token', '=', $params['token'])->first()) : null;
    if ($loggedin_user !== null) {
        $user_id = $loggedin_user->user_id;
        $user = User::find($user_id);
        if (empty($user)) {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        }
        
        if(!isset($params["id"]) || $params["id"] == "" || $params["id"] == "{id}") {
            return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue"));
        }
        
        $activity = new Activity();
        $activityUpdate = $activity->getActivity($params["id"]);
        if(!$activityUpdate || $activityUpdate->user_id !== $user_id) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("incorrect", "Status Update"));
        }
        try {
            $activityUpdate->delete();
        } catch(Exception $e) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("unexpectedError"));
        }
        
        return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Post Update deleted"));
    } else {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
  }


  /**
   * @SWG\POST(
   *   path="/profiles/activities/{id}/like",
   *   tags={"Profile"},
   *   summary="Like Activity",
   *   description="Like activity in the logged in user profile",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to like an activity<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="id", in="path", required=false, type="string", description="Activity ID <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description=""),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function likeProfileUpdate($request, $response, $args) {
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
        $user_id = $loggedin_user->user_id;
        $user = User::find($user_id);
        if (empty($user)) {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        }
        
        //load params
        $params = $args;

        if(!isset($params["id"]) || $params["id"] == "" || $params["id"] == "{id}") {
            return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue","id"));
        }
        $activity = new Activity();
        $activityUpdate = $activity->getActivity($params["id"]);
        if(!$activityUpdate) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("incorrect", "Status Update"));
        }
        try {
          $user_likes = new Usermeta();
          $activity_likes = $user_likes->getMetaLikes($user_id);  // get meta_value for user activity likes //
          if($activity_likes === null)
          {
            $meta_likes = [];
          }else {
            $serializedArray = $activity_likes->meta_value;
            $meta_likes = unserialize($serializedArray);
          }
          // check if user liked this status before //
          $like_exists = in_array($params["id"], $meta_likes);
          if ($like_exists == true){
            return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "Status Like"));
          }
          else{  // not exists .. add it
            $meta_likes[] = $params["id"];
            $activity_meta = new Activitymeta();
            $meta = $activity_meta->getFavoriteCount($params["id"]);
            // ---- get the number of favorite_count -- if found we will increase it and add it. else we will set value to 1 ---- //
            if (!empty($meta['meta_value']) || $meta['meta_value'] == 0 && $meta['meta_value'] != NULL){
              $meta['meta_value'] = $meta['meta_value'] + 1;
              $activity_meta->updateActivityMeta($params["id"], 'favorite_count', $meta['meta_value']);
            }
            else{
              $meta['meta_value'] = 1 ;
              $activity_meta->addActivityMeta($params["id"], 'favorite_count', $meta['meta_value']);
              $activity_meta->save();
            }
            
            
            //check if bp_favorite_activites not added
            if($activity_likes === null)
            {
              $data = array(
                  'user_id' => $user_id,
                  'key' => 'bp_favorite_activities',
                  'value' => serialize($meta_likes)
              );
              $userMeta = new Usermeta();
              $meta = $userMeta->addUserMeta($data);
              $meta->save();
            }else
            {
              // Update user meta
              $user_meta = $user_likes->updateMetaLikes($user_id, 'bp_favorite_activities', serialize($meta_likes));
            }
          }
        }
        catch(Exception $e) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("unexpectedError"));
        }

        return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Post Update Liked"));
    }
    else {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
  }

  /**
   * @SWG\Delete(
   *   path="/profiles/activities/{id}/like",
   *   tags={"Profile"},
   *   summary="Remove Like Activity",
   *   description="Remove Like activity in the logged in user profile",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to remove like on activity<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="id", in="path", required=false, type="string", description="Activity ID <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description=""),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function removeLikeProfileUpdate($request, $response, $args) {
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
        $user_id = $loggedin_user->user_id;
        $user = User::find($user_id);
        if (empty($user)) {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        }
        //load params
        $params = $args;
        
        if(!isset($params["id"]) || $params["id"] == "" || $params["id"] == "{id}") {
            return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "id"));
        }
        $activity = new Activity();
        $activityUpdate = $activity->getActivity($params["id"]);
        if(!$activityUpdate) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("incorrect", "Status Update"));
        }
        try {
          $user_likes = new Usermeta();
          $activity_likes = $user_likes->getMetaLikes($user_id);  // get meta_value for user activity likes //
          $serializedArray = $activity_likes->meta_value;
          $meta_likes = unserialize($serializedArray);

          // check if user liked this status before //
          $like_exists = in_array($params["id"], $meta_likes);
          if ($like_exists != true){
            return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "Status Remove Like"));
          }
          else{  // ID exists .. remove it
            $key = array_search($params["id"], $meta_likes);
            unset ($meta_likes[$key]);  // removing the id from array
            $serialized_likes = serialize($meta_likes);

            $activity_meta = new Activitymeta();
            $meta = $activity_meta->getFavoriteCount($params["id"]);
            // ---- get the number of favorite_count -- if found we will decrease it -1 and add the number. ---- //
            if (!empty($meta['meta_value']) || $meta['meta_value'] >= 1){
              $meta['meta_value'] = $meta['meta_value'] - 1;
              $activity_meta->updateActivityMeta($params["id"], 'favorite_count', $meta['meta_value']);
            }
            // Update user meta
            $user_meta = $user_likes->updateMetaLikes($user_id, 'bp_favorite_activities', $serialized_likes);
          }
        }
        catch(Exception $e) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("unexpectedError"));
        }

        return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Post Update Like Removed"));
    }
    else {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
  }

  /**
   * @SWG\GET(
   *   path="/profiles/{profileName}/activities",
   *   tags={"Profile"},
   *   summary="Finds Profile Activities",
   *   description="List profile activities with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile activities"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName to list other user profile activies"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfActivities", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar"), 
   *   @SWG\Response(response="200", description="View Profile Activity"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listProfileActivity($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfActivities', 'profileName'];
    $requiredParams = ['pageNumber', 'numberOfActivities', 'profileName'];
    $numeric_params = ['pageNumber', 'numberOfActivities'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    $lang = 'en';
    
    if( isset( $_GET['lang'] ) && in_array( $_GET['lang'], array( 'en', 'ar' ) ) ) {
      $lang = $_GET['lang'];
    }
    
    $params = $request->getHeaders();
    $user = new User();
    $profile = $user->getUser($args['profileName']);
    $user_id = $profile['ID'];
    $loggedin_user = null;
    if(isset($_SERVER['HTTP_TOKEN']))
    {
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if (empty($loggedin_user) || $loggedin_user == null) {  
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "user"));
      } 
      $loggedin_user = $loggedin_user->user_id;
      $loggedin_user = User::find($loggedin_user);
    }
        
    if (empty($profile)){
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
    }
    elseif ($profile['user_status'] == 2 || $profile['user_status'] == 1 ) {
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
    }
    else if(empty($args['numberOfActivities'] ) || $args['numberOfActivities'] < 1 || $args['numberOfActivities'] > 25){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of activity',array("range"=> "1 and 25 ")));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    else{
      $term = new Term();
      $activity = new Activity();
      $skip_number = ($args['pageNumber'] * $args['numberOfActivities']) - $args['numberOfActivities'] ;
      if ($args['pageNumber'] == 1){
        $skip_number = 0 ;
      }
      $profile_activities = $activity->listProfileActivities($user_id, $args['numberOfActivities'], $skip_number);
      $activity_result = array();
      if (count($profile_activities) == 0){
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }
      else{
        $likedByUser = array();
        if($loggedin_user)
        {
          $likedByUser = $loggedin_user->userMeta()->where("meta_key","=","bp_favorite_activities")->first()["meta_value"];
          if($likedByUser != null)
          {
            $likedByUser = array_map('intval',unserialize($likedByUser));
          }
        }
        
        foreach ($profile_activities as $key => $profile_activity) {
          if ($profile_activity['type'] == "activity_update" || $profile_activity['type'] == "new_member" || $profile_activity['type'] == "new_avatar"){
            $activity_id = $profile_activity['id'];

            $activity_meta = new Activitymeta();
            $meta = $activity_meta->getActivityMeta($activity_id);
            $interest_name = array();
            if(sizeof($meta) > 0)
            {
              for($i = 0; $i < sizeof($meta); $i++)
              {
                $interest = $term->getTerm($meta[$i]->meta_value);
                $interest_name = array_merge($interest_name, array($interest['name']));
              }
              $metaresult['interest'] = $interest_name;
            }
            else{
              $metaresult['interest'] = '';
            }
            ///// display the result ///////
           // $profile_activity_content = array_key_exists('type', $profile_activities) ? $profile_activity['content']  : "";
           // $profile_activity_type = array_key_exists('type', $profile_activities) ? $profile_activity['type']  : "";
            $profile_activity_content = '';
            if ( $profile_activity['type'] == "activity_update" && !empty($profile_activity['content'])){
              $profile_activity_content = $profile_activity['content'];
            }
            if ($profile_activity['type'] == "new_member"){
              $profile_activity_content = sprintf( __( '%s became a registered member', 'egyptfoss', $lang ), $args['profileName'] );
            }
            if ( $profile_activity['type'] == "new_avatar"){
              $profile_activity_content = sprintf( __( '%s changed their profile picture', 'egyptfoss', $lang ), $args['profileName'] );
            }
            $likes_count = $profile_activity->activityMeta()->where("meta_key", "=" ,"favorite_count")->get()->first()['meta_value'];    
            $comments_count = $profile_activity->activityComments()->where('secondary_item_id','=',$profile_activity->id)->get()->count(); 
           
            $activity_result[$key] = array(
              "activity_id" => $profile_activity->id,
              "content" => $profile_activity_content,
              "added_by" => $this->return_user_info_list($profile_activity->user_id),
              "date" => $profile_activity->date_recorded,
              "interests" => $metaresult['interest'],
              "likes_count" => ($likes_count)?intval($likes_count):0,
              "comment_count" => ($comments_count)?$comments_count:0
            );
            
            if($loggedin_user)
            {
              if($likedByUser != null)
              {
                if(in_array($profile_activity->id, $likedByUser))
                {
                  $activity_result[$key]['liked'] = true;
                }else
                {
                  $activity_result[$key]['liked'] = false;
                }
              }else
              {
                $activity_result[$key]['liked'] = false;
              }
            }
          }
        }
        
        $total_count = count($activity->listProfileActivities($user_id, -1, -1));
        $final_result = $this->ef_load_data_counts($total_count, $args['numberOfActivities']);
        $final_result['data'] = $activity_result;
        
        $result = $this->renderJson($response, 200, $final_result);
      }
      return $result;
    }
  }
  
  /**
   * @SWG\Post(
   *   path="/profiles/activities/{postActivityID}/comments",
   *   tags={"Profile"},
   *   summary="Creates Comment on Profile Activity",
   *   description="Add new comment on a profile activity",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add comment on activity<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Post Comment <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="postActivityID", in="formData", required=false, type="integer", collectionFormat="multi" , description="ID of Activity <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Post update"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function addUpdateComments($request, $response, $args) {
    $result = "";
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
      else {
        // validate data
        $result = "";
        if (isset($_POST['comment']) || !empty($_POST['comment'])) {   
          $meta['comment'] = $_POST['comment'];
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment"));
        }
        if(isset($_POST['postActivityID']) || !empty($_POST['postActivityID']) ){
          if (is_numeric($_POST['postActivityID'])) {
            // we need to check if this activity_id is in db or not
            $activity = new Activity();
            $activity_exists = $activity->findActivity($_POST['postActivityID']);
            if ($activity_exists) { $meta['postActivityID'] = $_POST['postActivityID']; }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Activity ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notNumber", "Activity ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Activity ID"));
        }
        // If no errors so far
        if ($result == "") {          
          $activity = new Activity;
          $activity->addActivityComment($user->ID, $user->user_login, $meta['postActivityID'], $meta['comment']);
          $activity->save();

          if ($result == "") {
            $output =  Messages::getSuccessMessage("Success","Activity Comment added");
            $output['comment_id'] = $activity->id;
            $result =  $this->renderJson($response, 200, $output);            
          }  
        }
      }
    }
    else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }
  
  /**
   * @SWG\GET(
   *  path="/profiles/activities/{parentActivityID}/comments",
   *  tags={"Profile"},
   *  summary="Finds All Comments of Profile Activity",
   *  description="List all comments of a profile activity",
   *  @SWG\Parameter(name="parentActivityID", in="path", required=false, type="integer", description="Comment ID <br/> <b>[Required]</b>"),
   *  @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *  @SWG\Parameter(name="numberOfComments", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *  @SWG\Response(response="200", description="View Comment on profile Activity"),
   *  @SWG\Response(response="422", description="Validation Error"),
   *  @SWG\Response(response="404", description="User not found")
   * )
   */
  public function getComment($request, $response, $args){
    $result = "";
    if (empty($args['parentActivityID']) || !is_numeric($args['parentActivityID'])){
      return $this->renderJson($response, 422, Messages::getErrorMessage("notNumber", "Activity ID"));
    }
    
    $parameters = ['pageNumber', 'numberOfComments'];
    $requiredParams = ['pageNumber', 'numberOfComments'];
    $numeric_params = ['pageNumber', 'numberOfComments'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    if(empty($args['numberOfComments'] ) || $args['numberOfComments'] < 1 || $args['numberOfComments'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of commments',array("range"=> "1 and 25 ")));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    
    $activity = new Activity();
    $parent_activity = $activity->findActivity($args['parentActivityID']);

    //List of comments on Activity ID
    $comment_result = array();
    if ($parent_activity)
    { // means that its a parent activity id
      $take = $args['numberOfComments'];
      $skip = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfComments'])-$args['numberOfComments']:0;
      
      $all_comments = $activity->getActivities($args['parentActivityID'], $take, $skip);
      if(sizeof($all_comments) == 0)
      {
        return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }

      $comments = count($activity->getActivities($args['parentActivityID'], -1, -1));
      $returnArray = $this->ef_load_data_counts($comments, $args['numberOfComments']);
      $result = array();
      foreach ($all_comments as $comment) 
      { 
        $item = array(
            'parent_comment_id' => ($comment->item_id == $comment->secondary_item_id)?0:$comment->item_id,
            'comment_id' => $comment->id,
            "added_by" => $this->return_user_info_list($comment->user_id),
            'comment' => $comment->content,
            'comment date' => $comment->date_recorded,
            'has_more_comments' => $activity->checkHasmoreComments($comment->id, $take),
            'comments on a comment' => self::listCommentsonComments($comment->id, $take, 0)
        );  

        array_push($result, $item);
      }
    
      $returnArray['data'] = $result;
      return $this->renderJson($response, 200, $returnArray);
    }
    else{ // -- means that the entered id is not a parent id -- //
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Parent Activity"));
    }
  }
  
  //recursively list comments on comments
  public function listCommentsonComments($comment_id, $take, $skip)
  {
    $activity = new Activity();
    $comments = $activity->getActivities($comment_id, $take, $skip);
    if(sizeof($comments) > 0)
    {
      $result = array();
      foreach ($comments as $comment) 
      { 
        $item = array(
            'parent_comment_id' => ($comment->item_id == $comment->secondary_item_id)?0:$comment->secondary_item_id,
            'comment_id' => $comment->id,
            "added_by" => $this->return_user_info_list($comment->user_id),
            'comment' => $comment->content,
            'comment date' => $comment->date_recorded,
            'has_more_comments' => $activity->checkHasmoreComments($comment->id, $take),
            'comments on a comment' => self::listCommentsonComments($comment->id, $take, $skip)
        );  

        array_push($result, $item);
      }
      return $result;
    }
    return [];
  }
  
  /**
   * @SWG\Post(
   *   path="/profiles/activities/{parentActivityID}/comments/{commentID}/replies",
   *   tags={"Profile"},
   *   summary="Creates Reply on Comment of Profile Activity",
   *   description="add reply on a comment on profile activity",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new reply on a comment<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="parentActivityID", in="formData", required=false, type="integer", description="ID of Activity <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="commentId", in="formData", required=false, type="integer", description="ID of comment <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Add the reply content text <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Reply on comment"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function addCommentReply($request, $response, $args) {
    $result = "";
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
      else {
        // validate data
        $result = "";
        if (isset($_POST['comment']) || !empty($_POST['comment'])) {   
          $meta['comment'] = $_POST['comment'];
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment"));
        }
        $activity = new Activity();
        if(isset($_POST['commentId']) || !empty($_POST['commentId']) ){
          if (is_numeric($_POST['commentId'])) {
            // check if this commentID is in db or not
            $activity_exists = $activity->findActivity($_POST['commentId'], TRUE)->first();
            if ( $activity_exists ) {
                if( $activity_exists->secondary_item_id == $activity_exists->item_id ) {
                    $meta['commentId'] = $_POST['commentId'];
                }
                else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notAllowed", "Multi level of comments")); }
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Comment ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notNumber", "Comment ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment ID"));
        }
        if(isset($_POST['parentActivityID']) || !empty($_POST['parentActivityID']) ){
          if (is_numeric($_POST['parentActivityID'])) {
            // check if this activity_id is in db or not
            $activity_exists = $activity->findActivity($_POST['parentActivityID']);
            if ($activity_exists) { $meta['parentActivityID'] = $_POST['parentActivityID']; }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Parent Activity ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notNumber", "Parent Activity ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Parent Activity ID"));
        }
        // Check if (parent id) is in the (comment id)
        $parent_in_comment = $activity->getActivityComment($_POST['commentId']);
        if ($parent_in_comment['item_id'] != $_POST['parentActivityID']){
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "Comment or Activity IDs have"));
        }
        // If no errors so far
        if ($result == "") {
          $activity = new Activity;
          $activity->addCommentReply($user->ID, $user->user_login, $meta['parentActivityID'], $meta['commentId'], $meta['comment']);
          $activity->save();
          
          if ($result == "") {
            $output =  Messages::getSuccessMessage("Success","Comment reply added");
            $output['comment_id'] = $activity->id;
            $result =  $this->renderJson($response, 200, $output);
          }
        }
      }
    }
    else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }
  
     /**
   * @SWG\GET(
   *   path="/profiles/comments/{commentId}/replies",
   *   tags={"Profile"},
   *   summary="List Replies On a Comment",
   *   description="list replies on the passed comment",
   *   @SWG\Parameter(name="commentId", in="path", required=false, type="integer", description="Comment ID to list the replies related to this ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfReplies", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List comments and replies to a comment"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Comments not found")
   * )
   */
    public function listRepliesonAComment($request, $response, $args) {
        $activity = new Activity();
        if(!isset($args["commentId"]) || $args["commentId"] == "" || $args["commentId"] == "{commentId}") 
        {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
        }   
      
        $parameters = ['pageNumber', 'numberOfReplies'];
        $requiredParams = ['pageNumber', 'numberOfReplies'];
        $numeric_params = ['pageNumber', 'numberOfReplies'];
        foreach ($_GET as $key => $value) {
          if (in_array($key, $parameters)) {
            if(! is_numeric($value) && in_array($key, $numeric_params)) {
              return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
            }
            $args[$key] = $value;
            $requiredParams = array_diff($requiredParams,[$key]);
          }
        }

        if(empty($args['numberOfReplies'] ) || $args['numberOfReplies'] < 1 || $args['numberOfReplies'] > 25){
          return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of replies',array("range"=> "1 and 25 ")));
        }
        else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
        }
      
      
      //List Replies on Comment
      $take = $args['numberOfReplies'];
      $skip = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfReplies'])-$args['numberOfReplies']:0;
      $comments = $activity->getActivities($args['commentId'], $take, $skip);
      if(sizeof($comments) > 0)
      {
          $result = array();
          foreach ($comments as $comment) 
          { 
            $item = array(
              'parent_comment_id' => ($comment->item_id == $comment->secondary_item_id)?0:$comment->secondary_item_id,
              'comment_id' => $comment->id,
              "added_by" => $this->return_user_info_list($comment->user_id),
              'comment' => $comment->content,
              'comment date' => $comment->date_recorded,
              'has_more_comments' => $activity->checkHasmoreComments($comment->id, $take),
              'comments on a comment' => self::listCommentsonComments($comment->id, $take, 0)
            );  
                     
            array_push($result, $item);
          }
          
          //display total number of replies and total page numbers
          $comments_count = count($activity->getActivities($args["commentId"], -1, -1));
          $results_final = $this->ef_load_data_counts($comments_count, $args['numberOfReplies']);
          $results_final['data'] = $result;

          return $this->renderJson($response, 200, $results_final);
      }
      
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
  }
  
  /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/products/additions",
   *   tags={"Profile"},
   *   summary="Finds Products Added by User",
   *   description="List products added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View Products Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listAddedProductsByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    
    //get token
    $params = $request->getHeaders();
    
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    $result = "";
    $page_number = $args['pageNumber'];
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    else if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Products', array("range"=> "1 and 25 ")));
    }
    else if(empty($page_number ) || $page_number < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    else{
      
      if($args['profileName'] == '{profileName}')
      {
        $args['profileName'] = '';
      }
      if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
      }

      $user = null;
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if ($loggedin_user !== null) {
        $user_id = $loggedin_user->user_id;
        $user = User::find($user_id);
        if (empty($user)){
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        }
      } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
      }

      //check both user & profile name
      $is_my_profile = false;
      if($user != null && $args['profileName'] != '')
      {
        if($user->user_nicename == $args['profileName'])
        {
          $is_my_profile = true;
        }
      }else if($user != null && $args['profileName'] == '')
      {
        $is_my_profile = true;
      }

      if(!$is_my_profile)
      {
        $user = new User();
        $user = $user->getUser($args['profileName']);

        if (empty($user)){
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
        }
        if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
          return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
        }
      }
      
      $user_id = $user->ID;
      $products = new Post();
      $skip_number = ($page_number * $args['number_per_page']) - $args['number_per_page'] ;
      if ($page_number == 1){
        $skip_number = 0 ;
      }
      
      $args = array(
          "post_type" => "product",
          "no_of_news" => $args['number_per_page'],
          "author" => $user->ID,
          "offset" => $skip_number,
          "no_of_tax"=> $args['number_per_page']
      );
      
      if($is_my_profile)
      {
        $args['post_status'] = '';
      }else
      {
        $args['post_status'] = 'publish';
      }

      $user_products_additions = new Post();
      $product_result = array();
      $user_products = $user_products_additions->getContributionBy($args);
      if(sizeof($user_products) == 0)
      {
        return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }
      else{
        $product_result;
        foreach ($user_products as $user_product) 
        {  
          //load logo
          $data = new Postmeta();
          $product_meta = $data->getProductMeta($user_product->ID);
          $product_data['product_logo'] = array_key_exists('_thumbnail_id', $product_meta) ? $product_meta['_thumbnail_id']  : "";
          $product_name = new Post();
          $logo = $product_name->getPostLogo($product_data['product_logo']);
          $logo = $logo['guid'];
          if($logo == null)
          {
            $option = new Option();
            $host = $option->getOptionValueByKey('siteurl');
            $logo = $host.'/wp-content/themes/egyptfoss/img/no-product-icon.png';
          }else
          {
            //return small size
            $logo = $this->ef_image_sizes($logo,'64x64');
          }
          
          //return language
          $language = "en";
          if(array_key_exists('language', $product_meta))
          {
            $lang = $product_meta['language'];
            $lang_arr = unserialize(unserialize($lang));
            $language = $lang_arr['slug'];

            if($language == null)
            {
              $language = "en";
            }
          }
          
          $product_result[] = array(
              "product_id" => $user_product->ID,
              "added_by" => $this->return_user_info_list($user_id, $language),
              "product_title" => $user_product->post_title,
              "product_status" => $user_product->post_status,
              "product_url" => $user_product->guid,
              "logo_url" => $logo,
              "created_date" => $user_product->post_date,
              "language" => $language
            );
        }
        //total count of contributions
        $args['offset'] = '';
        $args['no_of_tax'] = '';
        $results_array = new Post();
        $total_contributions = count($results_array->getContributionBy($args));
        $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_news']);
        $results_returned['data'] =  $product_result;
        return $this->renderJson($response,200,$results_returned); 
      }      
    }
  }

  /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/products/edits",
   *   tags={"Profile"},
   *   summary="Finds Contributed Products by User",
   *   description="List contributed products by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View Contributed Products by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listContributedProductsByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page', ];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    
    //get token
    $params = $request->getHeaders();
    
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    $result = "";
    $page_number = $args['pageNumber'];

    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    else if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Products', array("range"=> "1 and 25 ")));
    }
    else if(empty($page_number ) || $page_number < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    else{
      if($args['profileName'] == '{profileName}')
      {
        $args['profileName'] = '';
      }
      if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
      }

      $user = null;
      $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
      if ($loggedin_user !== null) {
        $user_id = $loggedin_user->user_id;
        $user = User::find($user_id);
        if (empty($user)){
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        }
      } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
      }

      //check both user & profile name
      $is_my_profile = false;
      if($user != null && $args['profileName'] != '')
      {
        if($user->user_nicename == $args['profileName'])
        {
          $is_my_profile = true;
        }
      }else if($user != null && $args['profileName'] == '')
      {
        $is_my_profile = true;
      }

      if(!$is_my_profile)
      {
        $user = new User();
        $user = $user->getUser($args['profileName']);

        if (empty($user)){
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
        }
        if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
          return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
        }
      }
      
      $user_id = $user->ID;
      
      $products = new PostHistory();
      $skip_number = ($page_number * $args['number_per_page']) - $args['number_per_page'] ;
      if ($page_number == 1){
        $skip_number = 0 ;
      }
      $user_products = $products->getContributedProductsByUser($user_id, $args['number_per_page'], $skip_number, '');
      $product_result = array();
      if (count($user_products) == 0){
        return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }
      else{
        foreach ($user_products as $user_product) 
        {
          //load logo
          $data = new Postmeta();
          $product_meta = $data->getProductMeta($user_product->post_id);
          $product_data['product_logo'] = array_key_exists('_thumbnail_id', $product_meta) ? $product_meta['_thumbnail_id']  : "";
          $product_name = new Post();
          $logo = $product_name->getPostLogo($product_data['product_logo']);
          $logo = $logo['guid'];
          if($logo == null)
          {
            $option = new Option();
            $host = $option->getOptionValueByKey('siteurl');
            $logo = $host.'/wp-content/themes/egyptfoss/img/no-product-icon.png';
          }else
          {
            //return small size
            $logo = $this->ef_image_sizes($logo,'64x64');
          }
          
          //return language
          $language = "en";
          if(array_key_exists('language', $product_meta))
          {
            $lang = $product_meta['language'];
            $lang_arr = unserialize(unserialize($lang));
            $language = $lang_arr['slug'];

            if($language == null)
            {
              $language = "en";
            }
          }
          
          $product_result[] = array(
              "product_id" => $user_product->post_id,
              "added_by" => $this->return_user_info_list($user_id, $language),
              "product_title" => $user_product->post_title,
              "product_status" => $user_product->post_status,
              "product_url" => $user_product->guid,
              "logo_url" => $logo,
              "modified_at" => $user_product->post_modified,
              "language" => $language
            );
        }
        //$result = $this->renderJson($response, 200, $product_result);
        
        //total count of contributions
        $args['offset'] = '';
        $args['no_of_tax'] = '';
        $results_array = new Post();
        
        $total_contributions = count($products->getContributedProductsByUser($user_id, -1, -1, ''));
        $results_returned = $this->ef_load_data_counts($total_contributions, $args['number_per_page']);
        $results_returned['data'] = $product_result;
        return $this->renderJson($response,200,$results_returned);     
      }
      return $result;
    }
  }

  /**
   * @SWG\PUT(
   *   path="/profiles/me/notifications",
   *   tags={"Profile"},
   *   summary="Updates Profiles Notifications",
   *   description="Updates notifications",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to update user notification settings <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="notification_profile_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="notification_products_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="notification_events_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="notification_news_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="notification_success_stories_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="notification_open_datasets_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="notification_request_center_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"), 
   *   @SWG\Parameter(name="notification_collaboration_center_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"), 
   *   @SWG\Parameter(name="notification_expert_thoughts_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"), 
   *   @SWG\Parameter(name="notification_awarness_center_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"),  
   *   @SWG\Parameter(name="notification_market_place_updates", in="formData", required=false, type="integer", description="Daily,Weekly,Monthly or Never <br/> <b>[Required]</b>"), 
   *   @SWG\Response(response="200", description="Settings Updated Successfully"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
    public function updateNotificationsSettings($request, $response, $args) {
        $put = $request->getParsedBody();
        $loggedin_user = isset($put['token']) ? (AccessToken::where('access_token', '=', $put['token'])->first()) : null;
        if (!$loggedin_user) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
        }
        
        $user_id = $loggedin_user->user_id;
        $user = User::find($user_id);
        if (empty($user)) {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        }

        $notificationTypesKeys = array(
          "notification_profile_updates",
          "notification_products_updates",
          "notification_events_updates",
          "notification_news_updates",
          "notification_success_stories_updates",
          "notification_open_datasets_updates",
          "notification_request_center_updates",
          "notification_collaboration_center_updates",
          "notification_expert_thoughts_updates",
          "notification_awarness_center_updates",
          "notification_market_place_updates"
        );
        
        foreach ($notificationTypesKeys as $notificationType) {
            $validValues = array ("Daily","Weekly","Monthly","Never");
            if(!in_array( trim( $put[$notificationType] ), $validValues)) {
                return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "value for $notificationType, Daily,Weekly,Monthly or Never available"));
            }
        }
        
        foreach ($notificationTypesKeys as $notification) {
            // update user meta or add new if not exist
            $userMeta = Usermeta::where('user_id', '=', $user_id)->where('meta_key', '=', $notification)->first();
            if ($userMeta === null) {
                $userMeta = new Usermeta();
                $userMeta->user_id = $user_id;
                $userMeta->meta_key = $notification;
            }
            $userMeta->meta_value = trim( $put[$notification] );
            $userMeta->save();
        }        
        return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Settings Updated"));
    }
  
   /**
     * @SWG\GET(
     *   path="/profiles/me/notifications",
     *   tags={"Profile"},
     *   summary="Finds Profiles Notifications",
     *   description="Get all profile notifications of a logged in user",
     *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list user notification settings <br/> <b>[Required]</b>"),
     *   @SWG\Response(response="200", description="All notification settings"),
     *   @SWG\Response(response="422", description="Validation Error"),
     *   @SWG\Response(response="404", description="User not found")
     * )
     */
    public function getNotificationsSettings($request, $response, $args) {
        $params = $request->getHeaders();
        $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
        if (!$loggedin_user) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
        }
        
        $user_id = $loggedin_user->user_id;
        $user = User::find($user_id);
        if (empty($user)) {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        }
        
        $notificationTypes = array('notification_profile_updates','notification_products_updates', 
          'notification_events_updates', 'notification_news_updates', 'notification_success_stories_updates', 
          'notification_open_datasets_updates','notification_request_center_updates','notification_collaboration_center_updates','notification_expert_thoughts_updates',
          'notification_awarness_center_updates', 'notification_market_place_updates');
        $notificationSettings = array(
          'notification_profile_updates' => 'Never',
          'notification_products_updates' => 'Never',
          'notification_events_updates' => 'Never',
          'notification_news_updates' => 'Never',
          'notification_success_stories_updates' => 'Never',
          'notification_open_datasets_updates' => 'Never',
          'notification_request_center_updates'=> 'Never',
          'notification_collaboration_center_updates' => 'Never',
          'notification_expert_thoughts_updates' => 'Never',
          'notification_awarness_center_updates' => 'Never',
          'notification_market_place_updates' => 'Never',
        );
        
        $userMeta = Usermeta::where('user_id', '=', $user_id)->whereIn('meta_key', $notificationTypes)->get();
        
        foreach ($userMeta as $record) {
            $notificationSettings[$record->meta_key] = $record->meta_value;
        }
        
        return $this->renderJson($response, 200, $notificationSettings);
    }
    
    /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/events",
   *   tags={"Profile"},
   *   summary="Finds events Added by User",
   *   description="List events added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View events Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listEventsByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    
    //get token
    $params = $request->getHeaders();
    
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Events', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    $args = array(
      "post_type" => "tribe_events",
      "no_of_events" => $args['number_per_page'],
      "author" => $user->ID,
      "order_by" => "DESC",
      "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0
    );
    
    if(!$is_my_profile)
    {
      $args['post_status'] = 'publish';
    }
    
    $event = new Post();
    $metaLabels = ["_EventStartDate","_EventEndDate","_EventCost","_EventCurrencySymbol"];
    $events = $event->getEventsBy($args);
    if(sizeof($events) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $eventList = array();
    foreach($events as $key => $event)
    {
      $eventList[$key] = array("event_id" => $event->ID,"event_title" => $event->post_title,
                              "added_by" => $this->return_user_info_list($event->post_author),
                               "event_url" => $event->guid,
                               "created_date" => $event->post_date,
                               "event_status" => $event->post_status,
                                );
      foreach ($event->postmeta as $eventmeta)
      {
        if(in_array($eventmeta["meta_key"], $metaLabels))
        {
          $eventList[$key] = array_merge($eventList[$key],array($eventmeta["meta_key"] => $eventmeta["meta_value"]));
        }else
        {
          if($eventmeta["meta_key"] == "_EventVenueID")
          {
           $venueAddress = null; 
           $is_venue = Post::find($eventmeta["meta_value"]);
           if($is_venue)
           {
              $venueAddress = $is_venue->postmeta()->where("meta_key", "=" ,"_VenueAddress")->get()->first()["meta_value"];  
           }
           $eventList[$key] = array_merge($eventList[$key],array("venueAddress" => $venueAddress));  
          }
        }
      }
    }
    
   //total count of contributions
    $number_of_events  = $args['no_of_events'];
    $args['offset'] = '';
    $args['no_of_events'] = '';
    $event = new Post();
    $total_contributions = count($event->getEventsBy($args));
    $results = $this->ef_load_data_counts($total_contributions, $number_of_events);
    $results['data'] = $eventList;
    return $this->renderJson($response,200,$results);
  }
  
  /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/news",
   *   tags={"Profile"},
   *   summary="Finds news Added by User",
   *   description="List news added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View news Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listNewsByUser($request, $response, $args){
    $parameters = ['token', 'pageNumber', 'number_per_page', 'profileName'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];  
    
    //get token
    $params = $request->getHeaders();
    /*if(!isset($params['HTTP_TOKEN']))
    {
        return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }*/
    
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of News', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    $args = array(
        "post_type" => "news",
        "no_of_news" => $args['number_per_page'],
        "author" => $user->ID,
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0,
        "no_of_tax"=> $args['number_per_page']
    );
       
    if($is_my_profile)
    {
      $args['post_status'] = '';
    }else
    {
      $args['post_status'] = 'publish';
    }
    
    $news = new Post();
    $news = $news->getContributionBy($args);
    $List = array();
    if(sizeof($news) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    foreach($news as $key => $new)
    {
        
        $post_meta = new Postmeta();
        $meta = $post_meta->getPostMeta($new->ID);
        $news_meta = array();
        foreach ($meta as $meta_key => $meta_value ) {
          $news_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
        }
        unset($meta_value);

        //get thumbnail image
        $url = '';
        if(array_key_exists('_thumbnail_id', $news_meta))
        {
          $post_type = "attachment";
          $post_status = "inherit";
          $attachment_id = $news_meta['_thumbnail_id'];
          $news_image = Post::getPostByID($attachment_id, $post_type, $post_status);
          if($news_image){
            $url = $news_image->guid;
          }
          
          if($url == '')
          {
            $option = new Option();
            $host = $option->getOptionValueByKey('siteurl');
            $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
          }else
          {
            //return thumbnail size in listing
            $url = $this->ef_image_sizes($url,'64x64');
          }
        }else
        {
          $option = new Option();
          $host = $option->getOptionValueByKey('siteurl');
          $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
        }
        
        $List[$key] = array(
            "news_id" => $new->ID,
            "news_title" => $new->post_title,
            "added_by" => $this->return_user_info_list($new->post_author),
            "news_status" => $new->post_status,
            "news_url" => $new->guid,
            "news_thumbnail_url" => $url,
            "created_date" => $new->post_date
        );
    }
    
    //total count of contributions
    $args['offset'] = '';
    $args['no_of_tax'] = '';
    $news = new Post();
    $total_contributions = count($news->getContributionBy($args));
    $results = $this->ef_load_data_counts($total_contributions, $args['no_of_news']);
    $results['data'] = $List;
    return $this->renderJson($response,200,$results);
  }
  
    /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/success-stories",
   *   tags={"Profile"},
   *   summary="Finds success stories Added by User",
   *   description="List success stories added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View success stories Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listSuccessStoriesByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    //get token
    $params = $request->getHeaders();
    
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Success Stories', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    $args = array(
        "post_type" => "success_story",
        "no_of_news" => $args['number_per_page'],
        "author" => $user->ID,
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0,
        "no_of_tax"=> $args['number_per_page']
    );
       
    if($is_my_profile)
    {
      $args['post_status'] = '';
    }else
    {
      $args['post_status'] = 'publish';
    }
    
    $results = new Post();
    $results = $results->getContributionBy($args);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $List = array();
    foreach($results as $key => $result)
    {
        $post_meta = new Postmeta();
        $meta = $post_meta->getPostMeta($result->ID);
        $news_meta = array();
        foreach ($meta as $meta_key => $meta_value ) {
          $news_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
        }
        unset($meta_value);

        //get thumbnail image
        $url = '';
        if(array_key_exists('_thumbnail_id', $news_meta))
        {
          $post_type = "attachment";
          $post_status = "inherit";
          $attachment_id = $news_meta['_thumbnail_id'];
          $image = Post::getPostByID($attachment_id, $post_type, $post_status);
          if($image){
            $url = $image->guid;
          }
          
          if($url == '')
          {
            $option = new Option();
            $host = $option->getOptionValueByKey('siteurl');
            $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
          }else
          {
            //return thumbnail size in listing
            $url = $this->ef_image_sizes($url,'64x64');
          }
        }else
        {
          $option = new Option();
          $host = $option->getOptionValueByKey('siteurl');
          $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
        }
        
        //return language
        $language = "en";
        if(array_key_exists('language', $news_meta))
        {
          $lang = $news_meta['language'];
          $lang_arr = unserialize(unserialize($lang));
          $language = $lang_arr['slug'];

          if($language == null)
          {
            $language = "en";
          }
        }
        
        $List[$key] = array(
            "success_story_id" => $result->ID,
            "success_story_title" => $result->post_title,
            "added_by" => $this->return_user_info_list($result->post_author, $language),
            "success_story_status" => $result->post_status,
            "success_story_url" => $result->guid,
            "success_story_thumbnail_url" => $url,
            "created_date" => $result->post_date,
            "langauge" => $language
        );
    }
    
    //total count of contributions
    $args['offset'] = '';
    $args['no_of_tax'] = '';
    $results_array = new Post();
    $total_contributions = count($results_array->getContributionBy($args));
    $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_news']);
    $results_returned['data'] = $List;
    return $this->renderJson($response,200,$results_returned);
  }

  /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/open-datasets",
   *   tags={"Profile"},
   *   summary="Finds open datasets Added by User",
   *   description="List open datasets added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View open datasets Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listDatasetsByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    
    //get token
    $params = $request->getHeaders();
    
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Open datasets', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    $args = array(
        "post_type" => "open_dataset",
        "no_of_news" => $args['number_per_page'],
        "author" => $user->ID,
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0,
        "no_of_tax"=> $args['number_per_page']
    );
       
    if($is_my_profile)
    {
      $args['post_status'] = '';
    }else
    {
      $args['post_status'] = 'publish';
    }
    
    $results = new Post();
    $results = $results->getContributionBy($args);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $List = array();
    foreach($results as $key => $result)
    {      
        //return language
        $data = new Postmeta();
        $dataset_meta = $data->getOpenDatasetMeta($result->ID);
        $language = "en";
        if(array_key_exists('language', $dataset_meta))
        {
          $lang = $dataset_meta['language'];
          $lang_arr = unserialize(unserialize($lang));
          $language = $lang_arr['slug'];

          if($language == null)
          {
            $language = "en";
          }
        }
      
        $List[$key] = array(
            "open_dataset_id" => $result->ID,
            "open_dataset_title" => $result->post_title,
            "added_by" => $this->return_user_info_list($result->post_author, $language),
            "open_dataset_status" => $result->post_status,
            "open_dataset_url" => $result->guid,
            "created_date" => $result->post_date,
            "language" => $language
        );
    }
    
    //total count of contributions
    $args['offset'] = '';
    $args['no_of_tax'] = '';
    $results_array = new Post();
    $total_contributions = count($results_array->getContributionBy($args));
    $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_news']);    
    $results_returned['data'] = $List;
    return $this->renderJson($response,200,$results_returned);
  }
  
/**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/fosspedia/additions",
   *   tags={"Profile"},
   *   summary="Finds fosspedia Added by User",
   *   description="List fosspedia added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View fosspedia Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listFosspediaByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    
    //get token
    $params = $request->getHeaders();
    
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Wiki ', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    $args = array(
        "no_of_news" => $args['number_per_page'],
        "author" => $user->user_email,
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0,
        "no_of_tax"=> $args['number_per_page']
    );
    
    $results = new MwPage();
    $results = $results->getFossPediaContributionAdditionsBy($args);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $List = array();
    
    //get domain url
    $option_domain = new Option();
    $host = $option_domain->getOptionValueByKey('siteurl');
    foreach($results as $key => $result)
    {        
        $List[$key] = array(
            "fosspedia_title" => $result->post_title,
            "added_by" => $this->return_user_info_list($user->ID),
            "fosspedia_url" => $host.$result->page_url,
            "created_date" => date('Y-m-d',strtotime($result->post_date))
        );
    }
    
    //total count of contributions
    $args['offset'] = '';
    $args['no_of_tax'] = '';
    $results = new MwPage();
    $total_contributions = count($results->getFossPediaContributionAdditionsBy($args));
    $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_news']);        
    $results_returned['data'] = $List;
    return $this->renderJson($response,200,$results_returned);
  }
   
  /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/fosspedia/edits",
   *   tags={"Profile"},
   *   summary="Finds fosspedia edited by User",
   *   description="List fosspedia edited by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View fosspedia edited by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listFosspediaEditsByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    
    //get token
    $params = $request->getHeaders();
    
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Wiki ', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    $args = array(
        "no_of_news" => $args['number_per_page'],
        "author" => $user->user_email,
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0,
        "no_of_tax"=> $args['number_per_page']
    );
    
    $results = new MwPage();
    $results = $results->getFossPediaContributionEditsBy($args);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $List = array();
    
    //get domain url
    $option_domain = new Option();
    $host = $option_domain->getOptionValueByKey('siteurl');
    foreach($results as $key => $result)
    {        
        $List[$key] = array(
            "fosspedia_title" => $result->post_title,
            "added_by" => $this->return_user_info_list($user->ID),
            "fosspedia_url" => $host.$result->page_url,
            "created_date" => date('Y-m-d',strtotime($result->post_date))
        );
    }
    
    //total count of contributions
    $args['offset'] = '';
    $args['no_of_tax'] = '';
    $results = new MwPage();
    $total_contributions = count($results->getFossPediaContributionEditsBy($args));
    $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_news']);            
    $results_returned['data'] =$List;
    return $this->renderJson($response,200,$results_returned);
  }  
  
  /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/request-center/requests",
   *   tags={"Profile"},
   *   summary="Finds requests Added by User",
   *   description="List requests added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View requests Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listRequestsByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    //get token
    $params = $request->getHeaders();
    
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Requests', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    $args = array(
        "post_type" => "request_center",
        "no_of_news" => $args['number_per_page'],
        "author" => $user->ID,
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0,
        "no_of_tax"=> $args['number_per_page']
    );
       
    if($is_my_profile)
    {
      $args['post_status'] = 'request_center';
    }else
    {
      $args['post_status'] = 'publish';
    }
    
    $results = new Post();
    $results = $results->getContributionBy($args);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $List = array();
    foreach($results as $key => $result)
    {        
        $List[$key] = array(
            "request_id" => $result->ID,
            "request_title" => $result->post_title,
            "added_by" => $this->return_user_info_list($result->post_author),
            "request_status" => $result->post_status,
            "request_url" => $result->guid,
            "created_date" => $result->post_date
        );
    }
    
    //total count of contributions
    $args['offset'] = '';
    $args['no_of_tax'] = '';
    $results_array = new Post();
    $total_contributions = count($results_array->getContributionBy($args));
    $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_news']);                
    $results_returned['data'] = $List;
    return $this->renderJson($response,200,$results_returned);  
  }
  
  /**
   * @SWG\GET(
   *   path="/profile/contributions/me/request-center/responses",
   *   tags={"Profile"},
   *   summary="Finds requests Added by User",
   *   description="List requests added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View responses Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listResponsesByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    //get token
    $params = $request->getHeaders();
    
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Requests', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }

    if(!isset($params['HTTP_TOKEN']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
          
    $args = array(
        "post_type" => "request_center",
        "no_of_posts" => $args['number_per_page'],
        "no_of_responses" => $args['number_per_page'],
        "author" => $user->ID,
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0
    );
       
    $results = new Post();
    $results = $results->getRequestsByResponses($args);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $List = array();
    foreach($results as $key => $result)
    {        
        $List[$key] = array(
            "request_id" => $result->ID,
            "request_title" => $result->post_title,
            "added_by" => $this->return_user_info_list($result->post_author),
            "request_status" => $result->post_status,
            "request_url" => $result->guid,
            "created_date" => $result->post_date
        );
    }
    
    //total count of contributions
    $args['offset'] = -1;
    $args['no_of_posts'] = -1;
    $results_array = new Post();
    $total_contributions =  count($results_array->getRequestsByResponses($args));
    $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_responses']);                    
    $results_returned['data'] = $List;
    return $this->renderJson($response,200,$results_returned);  
  }
  
  /**
   * @SWG\GET(
   *   path="/profile/{profileName}/services",
   *   tags={"Profile"},
   *   summary="Finds services Added by User",
   *   description="List services added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="language of the service, must be en or ar"),
   *   @SWG\Response(response="200", description="View requests Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listServicesByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page', 'lang'];
    $requiredParams = ['pageNumber', 'number_per_page', 'lang'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    //get token
    $params = $request->getHeaders();
    
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Requests', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    $lang = $args["lang"];
    
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    $args = array(
        "post_type"   => "service",
        "no_of_news"  => $args['number_per_page'],
        "author"      => $user->ID,
        "offset"      => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0,
        "no_of_tax"   => $args['number_per_page']
    );
       
    if( !$is_my_profile ) {
      $args[ 'post_status' ] = 'publish';
    }
    
    $post = new Post();
    $results = $post->getContributionBy( $args );
    if( sizeof( $results ) == 0) {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    $List = array();
    foreach($results as $key => $result) {    
      
        //get category
        $category = "";
        //get thumbnail image
        $url = '';
        $post_meta = new Postmeta();
        $meta = $post_meta->getPostMeta( $result->ID );
        foreach ( $meta as $meta_key => $meta_value ) {
          if( $meta_value[ 'meta_key' ] == 'service_category' ) {
            $term = new Term();
            $categoryObj = $term->getTerm( $meta_value[ 'meta_value' ] );
            $category = ( $lang == "ar" && $categoryObj->name_ar != '' ) ? $categoryObj->name_ar : $categoryObj->name;
          }
          else if( $meta_value[ 'meta_key' ] == '_thumbnail_id' ) {
            $post_type      = "attachment";
            $post_status    = "inherit";
            $attachment_id  = $meta_value[ 'meta_value' ];
            $service_image  = Post::getPostByID( $attachment_id, $post_type, $post_status );
            if( $service_image ) {
              $url = $service_image->guid;
            }

            if($url != '') {
              //return thumbnail size in listing
              $url = $this->ef_image_sizes($url,'340x210');
            }
          }
        }
        $no_of_responses = Thread::where('request_id', '=', $result->ID)->where('responses_count', '>', 0)->count();
        $List[$key] = array(
            "service_id"    => $result->ID,
            "service_title" => $result->post_title,
            "category"        => $category,
            "thumbnail"       => $url,
            "added_by"      => $this->return_user_info_list($result->post_author, $lang),
            "service_status"=> $result->post_status,
            "service_url"   => $result->guid,
            "created_date"  => $result->post_date,
            "no_of_requests" => $no_of_responses,
            "average_rate" => $this->service_average_rate($result->ID)
        );
    }
    
    //total count of contributions
    $args['offset'] = '';
    $args['no_of_tax'] = '';
    $results_array = new Post();
    $total_contributions = count( $results_array->getContributionBy( $args ) );
    $results_returned = $this->ef_load_data_counts( $total_contributions, $args[ 'no_of_news' ] );                
    $results_returned[ 'data' ] = $List;
    
    return $this->renderJson( $response, 200, $results_returned );  
  }
  
  /**
   * @SWG\GET(
   *   path="/profile/me/services/responses",
   *   tags={"Profile"},
   *   summary="Finds service responses Added by User",
   *   description="List service responses added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="language of the service, must be en or ar"),
   *   @SWG\Response(response="200", description="View responses Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listServiceResponsesByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page', 'lang'];
    $requiredParams = ['pageNumber', 'number_per_page', 'lang'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    //get token
    $params = $request->getHeaders();
    
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Requests', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }

    if(!isset($params['HTTP_TOKEN']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    
    $post_args = array(
        "post_type"       => "service",
        "no_of_posts"     => $args[ 'number_per_page' ],
        "no_of_responses"     => $args[ 'number_per_page' ],
        "author"          => $user->ID,
        "offset"          => ( !empty( $args[ 'pageNumber' ] ) ) ? ( $args[ 'pageNumber' ] * $args[ 'number_per_page' ] ) - $args[ 'number_per_page' ] : 0
    );
       
    $post = new Post();
    $results = $post->getRequestsByResponses( $post_args );
    if( sizeof( $results ) == 0) {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $List = array();
    foreach( $results as $key => $result )
    {        
        //get category
        $category = "";
        //get thumbnail image
        $url = '';
        $post_meta = new Postmeta();
        $meta = $post_meta->getPostMeta( $result->ID );
        foreach ( $meta as $meta_key => $meta_value ) {
          if( $meta_value[ 'meta_key' ] == 'service_category' ) {
            $term = new Term();
            $categoryObj = $term->getTerm( $meta_value[ 'meta_value' ] );
            $category = ( $args["lang"] == "ar" && $categoryObj->name_ar != '' ) ? $categoryObj->name_ar : $categoryObj->name;
          }
          else if( $meta_value[ 'meta_key' ] == '_thumbnail_id' ) {
            $post_type      = "attachment";
            $post_status    = "inherit";
            $attachment_id  = $meta_value[ 'meta_value' ];
            $service_image  = Post::getPostByID( $attachment_id, $post_type, $post_status );
            if( $service_image ) {
              $url = $service_image->guid;
            }

            if($url != '') {
              //return thumbnail size in listing
              $url = $this->ef_image_sizes($url,'340x210');
            }
          }
        }
        $no_of_responses = Thread::where('request_id', '=', $result->ID)->where('responses_count', '>', 0)->count();
        $List[ $key ] = array(
            "service_id"      => $result->ID,
            "service_title"   => $result->post_title,
            "category"        => $category,
            "thumbnail"       => $url,
            "added_by"        => $this->return_user_info_list($result->post_author, $args["lang"]),
            "service_status"  => $result->post_status,
            "service_url"     => $result->guid,
            "created_date"    => $result->post_date,
            "no_of_requests" => $no_of_responses,
            "average_rate"    => $this->service_average_rate($result->ID)
        );
    }
    
    //total count of contributions
    $post_args['offset'] = -1;
    $post_args['no_of_posts'] = -1;
    $results_array = new Post();
    $total_contributions =  count( $results_array->getRequestsByResponses( $post_args ) );
    $results_returned = $this->ef_load_data_counts( $total_contributions, $post_args[ 'no_of_responses' ] ); 
    $results_returned['data'] = $List;
    
    return $this->renderJson( $response, 200, $results_returned );
  }

    /**
   * @SWG\GET(
   *   path="/profile/me/quizzes/",
   *   tags={"Profile"},
   *   summary="Lists quizzes taken by user",
   *   description="List quizzes taken by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View Quizzes Taken by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listMyQuizzes($request, $response, $args)
  {
    $parameters = ['pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    //get token
    $params = $request->getHeaders();
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Requests', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }

    if(!isset($params['HTTP_TOKEN']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","token"));
    }
    
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    
    //List Quizzes for specific user
    $args = array(
        "user_id" => $user_id,
        "no_of_posts" => intval($args['number_per_page']),
        "no_of_responses" => $args['number_per_page'],
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0
    );
    $quizzes  = new Quiz();
    $results = $quizzes->listUserQuizzes($args);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    $List = array();
    foreach($results as $key => $result){
        $List[$key] = array(
            "quiz_id" => $result->quiz_id,
            "quiz_name" => $result->quiz_name,
            "created_date" => $result->post_date,
            "success_rate"  => ($result->quiz_taken <= 0)?"":round(($result->success_rate/$result->quiz_taken) * 100).'%',
            "highest_score_id" => $result->highest_id,
            "highest_score" => $result->highest_score,
            "highest_score_date" => $result->highest_date,
            "latest_score_id" => $result->latest_id,
            "latest_score" => $result->latest_score,
            "latest_score_date" => $result->latest_date            
        );
    }
    
    $args['offset'] = '';
    $args['no_of_posts'] = '';
    $total_contributions =  count($quizzes->listUserQuizzes($args));
    $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_responses']);                    
    $results_returned['data'] = $List;
    return $this->renderJson($response,200,$results_returned);  
  }
  

  /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/open-datasets/edits",
   *   tags={"Profile"},
   *   summary="Finds Contributed Open Datasets Added by User",
   *   description="List Contributed Open Datasets added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View contributes Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listContributedDataSetsByUser($request, $response, $args){
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    //get token
    $params = $request->getHeaders();
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Requests', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }

    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }

    $args = array(
        "post_type" => "open_dataset",
        "no_of_posts" => intval($args['number_per_page']),
        "no_of_responses" => $args['number_per_page'],
        "author" => $user->ID,
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0
    );
    
    if($is_my_profile)
    {
      $args['post_status'] = '';
    }else
    {
      $args['post_status'] = 'publish';
    }
    
    
    $results = new Post();
    $results = $results->getContributedOpenDatasets($args);
    if(sizeof($results) == 0){
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $List = array();
    foreach($results as $key => $result){
      
        //return language
        $data = new Postmeta();
        $dataset_meta = $data->getOpenDatasetMeta($result->ID);
        $language = "en";
        if(array_key_exists('language', $dataset_meta))
        {
          $lang = $dataset_meta['language'];
          $lang_arr = unserialize(unserialize($lang));
          $language = $lang_arr['slug'];

          if($language == null)
          {
            $language = "en";
          }
        }
      
        $List[$key] = array(
            "open_dataset_id" => $result->ID,
            "open_dataset_title" => $result->post_title,
            "added_by" => $this->return_user_info_list($result->post_author, $language),
            "open_dataset_status" => $result->post_status,
            "open_dataset_url" => $result->guid,
            "created_date" => $result->post_date,
            "language" => $language
        );
    }
    
    //total count of contributions
    $args['offset'] = '';
    $args['no_of_posts'] = '';
    $results_array = new Post();
    $total_contributions =  count($results_array->getContributedOpenDatasets($args));
    $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_responses']);                    
    $results_returned['data'] = $List;
    return $this->renderJson($response,200,$results_returned);  
  }
  
  /**
   * @SWG\GET(
   *   path="/profiles/me/social-links",
   *   tags={"Profile"},
   *   summary="Retrieves user's social links",
   *   description="Returns all social links on success",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list user social links"),
   *   @SWG\Response(response="200", description="Retrieve user social links")
   * )
   */
  public function getSocialLinks($request, $response, $args) {
    $result = "";
    $params = $request->getHeaders();
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    $user_id = $loggedin_user->user_id;
    $user = User::find($user_id);
    if (empty($user)) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    if (in_array($user->user_status, array(1, 2))) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
    }
    global $ef_wsl_social_login_providers;
    $userProfiles =array();
    foreach($user->userProfile()->get() as $profile)
    {
      $userProfiles = array_merge($userProfiles,array($profile->provider => true));
    }
    foreach($ef_wsl_social_login_providers as $provider)
    {
      if(!array_key_exists($provider, $userProfiles))
      {
        $userProfiles = array_merge($userProfiles,array($provider => false));
      }
    }
    return $this->renderJson($response, 200, $userProfiles); 
  }

  /**
   * @SWG\GET(
   *   path="/profiles/activities/{id}/likes",
   *   tags={"Profile"},
   *   summary="Retrieves activity's likes users list",
   *   description="Returns all users who like this activity",
   *   @SWG\Parameter(name="id", in="path", required=false, type="string", description="Activity Id <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Retrieve activity's likes users list")
   * )
   */
  public function getLikesUsers($request, $response, $args){
      $activity_id = $args['id'];
      if (empty($activity_id) || !ctype_digit($activity_id)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("wrong", "Activity Id"));
      } else {
        $users_data = new Usermeta();
        $users = $users_data->getActivityLikesUsers($activity_id);
        if(sizeof($users) == 0)
        {
          return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
        }
        
        for($i = 0; $i < sizeof($users); $i++)
        {
          $user_id = $users[$i]->user_id;
          // --- Get profile image --- //
          $option = new Option();
          $host = $option->getOptionValueByKey('siteurl');;
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
            if (empty($meta)){ // -- default gravatar image -- //
              $image = $host.'/wp-content/themes/egyptfoss/img/default_avatar.png';
            }
          }
          
          //add image to array
          $users[$i]->profile_picture = $image;
        }
        
        $final_result = $this->ef_load_data_counts(sizeof($users), -1);
        $final_result['data'] = $users;
        $result = $this->renderJson($response, 200, $final_result);
      }
      return $result;
  }
  
    /**
   * @SWG\GET(
   *   path="/users",
   *   tags={"Profile"},
   *   summary="Retrieves users existing in the system",
   *   description="Returns all users in the system",
   *   @SWG\Parameter(name="display_name", in="query", required=false, type="string", description="Set Display Name as filter"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfData", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Retrieve activity's likes users list")
   * )
   */
  public function listSystemUsers($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfData','display_name'];
    $requiredParams = ['pageNumber', 'numberOfData'];
    $numeric_params = ['pageNumber', 'numberOfData'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($args['numberOfData'] ) && ($args['numberOfData'] < 1 || $args['numberOfData'] > 25)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of industries',array("range"=> "1 and 25 ")));
    }
    else if(!empty($args['pageNumber'] ) && $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['pageNumber']) && empty($args['numberOfData']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("dependingMissingValue", "numberOfData",array("range"=>"pageNumber")));
    }
    else{
      $offset = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfData'])-$args['numberOfData']:0;
      if(empty($args['pageNumber']) ||  $args['pageNumber'] == 0)
      {
        $offset = -1;
      }
      if(!array_key_exists('numberOfData', $args))
      {
        $args['numberOfData'] = -1;
      }else if($args['numberOfData'] == '' || $args['numberOfData'] == 0)
      {
        $args['numberOfData'] = -1;
      } 
      if(!isset($args['display_name']))
      {
        $args['display_name'] ='';
      }
      $users = new User();
      $list = $users->loadUsers($args['display_name'], $args['numberOfData'], $offset);
      if(sizeof($list) == 0)
      {
        return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }      
      $total_count = count($users->loadUsers($args['display_name'], -1, -1));
      $returned = $this->ef_load_data_counts($total_count, $args['numberOfData']);
      $option = new Option();
      $host = $option->getOptionValueByKey('siteurl');;
      for($i = 0; $i < sizeof($list); $i++)
      {
        $user_id = $list[$i]->ID;
        // --- Get profile image --- //
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
            $image = $host.'/wp-content/themes/egyptfoss/img/default_avatar.png';
          }
        }
        
        $returned['data'][$i] = array(
            'username' => $list[$i]->user_nicename,
            'display_name' => $list[$i]->display_name,
            'profile_picture' =>$image
        );
      }
      
      return $this->renderJson($response, 200, $returned);
      
    }
  }
  

  /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/expert-thoughts",
   *   tags={"Profile"},
   *   summary="Finds expert thoughts Added by User",
   *   description="List expert thoughts added by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View expert thoughts Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listExpertThoughtsByUser($request, $response, $args) {
    $parameters = ['profileName', 'pageNumber', 'number_per_page'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    //get token
    $params = $request->getHeaders();
    
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Expert Thoughts', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    $args = array(
        "post_type" => "expert_thought",
        "no_of_news" => $args['number_per_page'],
        "author" => $user->ID,
        "offset" => (!empty($args['pageNumber']))?($args['pageNumber'] * $args['number_per_page'])-$args['number_per_page']:0,
        "no_of_tax"=> $args['number_per_page']
    );
       
    if($is_my_profile)
    {
      $args['post_status'] = '';
    }else
    {
      $args['post_status'] = 'publish';
    }
    
    $results = new Post();
    $results = $results->getContributionBy($args);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    $List = array();
    foreach($results as $key => $result)
    {
        $post_meta = new Postmeta();
        $meta = $post_meta->getPostMeta($result->ID);
        $news_meta = array();
        foreach ($meta as $meta_key => $meta_value ) {
          $news_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
        }
        unset($meta_value);

        //get thumbnail image
        $url = '';
        if(array_key_exists('_thumbnail_id', $news_meta))
        {
          $post_type = "attachment";
          $post_status = "inherit";
          $attachment_id = $news_meta['_thumbnail_id'];
          $image = Post::getPostByID($attachment_id, $post_type, $post_status);
          if($image){
            $url = $image->guid;
          }
          
          if($url == '')
          {
            $option = new Option();
            $host = $option->getOptionValueByKey('siteurl');
            $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
          }else
          {
            //return thumbnail size in listing
            $url = $this->ef_image_sizes($url,'64x64');
          }
        }else
        {
          $option = new Option();
          $host = $option->getOptionValueByKey('siteurl');
          $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
        }
        
        //return language
        $language = "en";
        if(array_key_exists('language', $news_meta))
        {
          $lang = $news_meta['language'];
          $lang_arr = unserialize(unserialize($lang));
          $language = $lang_arr['slug'];

          if($language == null)
          {
            $language = "en";
          }
        }
        
        //get interest
        $interests = array();
        if(array_key_exists('interest', $news_meta))
        {
          $interests_arr = unserialize($news_meta['interest']);
          for($i = 0; $i < sizeof($interests_arr); $i++)
          {
            if($interests_arr[$i] != '')
            {
              $term = new Term();
              $interest_id = $interests_arr[$i];
              array_push($interests, $term->getTerm($interest_id)->name);
            }
          }
        }
        
        $List[$key] = array(
            "expert_thought_id" => $result->ID,
            "expert_thought_title" => $result->post_title,
            "description"  => $result->post_content,
            "interests" => $interests,
            "expert_thought_status" => $result->post_status,
            "thumbnail" => $url,
            "date" => $result->post_date,
            "added_by" => $this->return_user_info_list($result->post_author, $language),
        );
    }
    
    //total count of contributions
    $args['offset'] = '';
    $args['no_of_tax'] = '';
    $results_array = new Post();
    $total_contributions = count($results_array->getContributionBy($args));
    $results_returned = $this->ef_load_data_counts($total_contributions, $args['no_of_news']);
    $results_returned['data'] = $List;
    return $this->renderJson($response,200,$results_returned);
  }

  /**
   * @SWG\GET(
   *   path="/profile/contributions/{profileName}/documents",
   *   tags={"Profile"},
   *   summary="Finds published documents edited by User",
   *   description="List published documents edited by user with pagination",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list profile contributions"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="number_per_page", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View documents Added by User"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listDocumentsByUser($request, $response, $args){
    $parameters = ['token', 'pageNumber', 'number_per_page', 'profileName'];
    $requiredParams = ['pageNumber', 'number_per_page'];
    $numeric_params = ['pageNumber', 'number_per_page'];  

    $params = $request->getHeaders();
    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }

    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if(empty($args['number_per_page'] ) || $args['number_per_page'] < 1 || $args['number_per_page'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between", 'Number of Documents', array("range"=> "1 and 25 ")));
    }
    if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }

    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    if($user != null && $args['profileName'] == '') {
      $args['profileName'] = $user->user_nicename;
    }
    
    $item = new CollaborationCenterItem();
    $offset = (!empty($args['pageNumber'])) ? ($args['pageNumber'] * $args['number_per_page']) - $args['number_per_page'] : 0;
    $offset = (empty($args['pageNumber']) ||  $args['pageNumber'] == 0) ? -1 : $offset;
    $documents = $item->userPublishedDocuments($user->ID, $offset, $args['number_per_page']);
    if(sizeof($documents) == 0) {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    return $this->renderJson($response, 200, $documents);
  }
  
    /**
   * @SWG\Post(
   *   path="/profiles/me/social-login/resend-activation",
   *   tags={"Profile"},
   *   summary="Resend activation email to current user through social login",
   *   description="Resend activation email to current user through social login",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to send activation link <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Resend Email"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function sendProfileActivation($request, $response, $args) {
    $params = $request->getHeaders();
    $user = null;
    
    if(!isset($params['HTTP_TOKEN']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "token"));
    }
    
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    
    if(!isset($_POST["lang"]))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "lang"));
    }
    
    if($_POST["lang"] != "en" && $_POST["lang"] != "ar")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "lang"));
    }
    
    // check if user needs activation
    $user_data = new Usermeta();
    $user_meta = $user_data->getMeta($user_id);
    if ($user_meta && isset($user_meta['registeredNormally']) && $user_meta['registeredNormally'] == 1) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("notSocialLogin", "User"));
    }

    if($user->user_status == 0)
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("alreadyActivated", "User"));
    }

    $userMeta = new Usermeta();
    $activation_key = $userMeta->getUserMeta($user->ID, 'activation_key');
    if(!isset($activation_key) || empty($activation_key))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("alreadyActivated", "User"));
    }
    
    //check interval to send activation link
    $shouldInvite = false;
    $last_send_activation = $userMeta->getUserMeta($user->ID, 'last_resend_activation');
    if(!isset($last_send_activation) || $last_send_activation == '')
    {
      $shouldInvite = true;
    }else {
      $time1 = new DateTime($last_send_activation);
      $time2 = new DateTime();
      $interval = $time1->diff($time2);    
      if($interval->format('%i') >= 15) // 15mins
      {
        $shouldInvite = true;
      }
    }
    
    if(!$shouldInvite)
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("emailPreviouslySent", "User"));
    }
    
    //resend activation link
    $args = array(
      "sender" => "testapp@espace.ws",
      "to" => array("email" => $user->user_email , "name" => $user->user_login),
      "key" => $activation_key,
      "user_login" => $user->user_login,
      "lang" => $_POST["lang"]
    );
    $mailer = new ActivationMailer();
    $isSent = $mailer->sendActivationMessage($args, $response);
    if($isSent != true) {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", 'Mailer Error: ' . $isSent));
    }
    
    // update last resend activation
    $userMeta->updateUserMeta($user->ID, "last_resend_activation", date('Y-m-d H:i:s'));
    
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","Email sent"));
  }


  /**
   * @SWG\GET(
   *   path="/profile/{profileName}/badges",
   *   tags={"Profile"},
   *   summary="List of badged achieved and locked for User",
   *   description="List of badged achieved and locked for User",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to list user badges"),
   *   @SWG\Parameter(name="profileName", in="path", required=false, type="string", description="profileName in case needed to retrieve other user contributions"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="integer", description="Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View User Badges"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listUserBadges($request, $response, $args){
    $parameters = ['token', 'lang', 'profileName'];
    $requiredParams = ['lang'];
    
    //get token
    $params = $request->getHeaders();

    $_GET = array_merge($_GET,$args);
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    
    if(!empty($requiredParams)){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    
    if($args["lang"] != "en" && $args["lang"] != "ar")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "lang"));
    }

    if($args['profileName'] == '{profileName}')
    {
      $args['profileName'] = '';
    }
    if(!isset($params['HTTP_TOKEN']) && empty($args['profileName']))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
    }
      
    $user = null;
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else if(isset($params['HTTP_TOKEN']) && $loggedin_user == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
      
    //check both user & profile name
    $is_my_profile = false;
    if($user != null && $args['profileName'] != '')
    {
      if($user->user_nicename == $args['profileName'])
      {
        $is_my_profile = true;
      }
    }else if($user != null && $args['profileName'] == '')
    {
      $is_my_profile = true;
    }
    
    if(!$is_my_profile)
    {
      $user = new User();
      $user = $user->getUser($args['profileName']);

      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Profile name"));
      }
      if ($user['user_status'] == 2 || $user['user_status'] == 1 ) {
        return $this->renderJson($response, 404, Messages::getErrorMessage("activate", $args['profileName']));
      }
    }
    
    // List achieved badges of User
    $lang = $args["lang"];
    $badges = new EFBBadgesUser();
    $achievedIDs = "";
    $userBadges = $badges::getBadgesByUser($user->ID);
    $achievedBadges = array();
    foreach($userBadges as $badge)
    {
      $achievedIDs .= $badge["id"].",";
      $achievedBadges[] = array(
          'title' => ($lang == "ar")?html_entity_decode($badge['title_ar'],ENT_QUOTES):$badge['title'],
          'description' => ($lang == "ar")?html_entity_decode($badge['description_ar'],ENT_QUOTES):$badge['description'],
          'img' => $badge['img']
      );
    }
    $results["achieved"] = $achievedBadges;
    
    // List locked badges of User
    $userLockedBadged = $badges->getNotAchievedBadges(rtrim($achievedIDs,","));
    $lockedBadges = array();
    foreach($userLockedBadged as $badge)
    {
      $lockedBadges[] = array(
          'title' => ($lang == "ar")?html_entity_decode($badge['title_ar'],ENT_QUOTES):$badge['title'],
          'description' => ($lang == "ar")?html_entity_decode($badge['description_ar'],ENT_QUOTES):$badge['description'],
          'img' => $badge['img']
      );
    }
    $results["locked"] = $lockedBadges;
    return $this->renderJson($response, 200, $results);
  }
}
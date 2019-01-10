<?php

class WPRequestCenterController extends EgyptFOSSController {

  /**
   * @SWG\Post(
   *   path="/request-center",
   *   tags={"Request center"},
   *   summary="Creates request",
   *   description="Create a new request with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to create a new request<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Define Request Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Request Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="request_center_type", in="formData", required=false, type="string", description="Request Types <br/><b>Validations: </b><br/> 1. Predefined Reqeust Types in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="target_business_relationship", in="formData", required=false, type="string", description="Business Relationship <br/><b>Validations: </b><br/> 1. Predefined Business Relationship in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="theme", in="formData", required=false, type="string", description="Theme <br/><b>Validations: </b><br/> 1. Predefined Theme in System <br/> <b>Values: </b> English or Arabic name or ID "),
   *   @SWG\Parameter(name="description", in="formData", required=false, type="string", description="Request Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="requirements", in="formData", required=false, type="string", description="Request Requirements <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="constraints", in="formData", required=false, type="string", description="Request Constraints <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="technology", in="formData", required=false, type="string", description="Request Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Request Interests <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="deadline", in="formData", required=false, type="string", description="Request Deadline <br/><b>Format: </b> yyyy-mm-dd ex. 2016-05-05 <br/><b>Validations :</b><br/> 1. Should be today or after"),
   *   @SWG\Response(response="200", description="request added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addRequest($request, $response, $args) {
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    $parameters = ['lang', 'title', 'request_center_type', 'target_business_relationship',
      'theme', 'description', 'requirements', 'constraints', 'technology',
      'interest', 'deadline'];

    $required_params = ['title', 'lang', 'request_center_type', 'target_business_relationship', 'description'];

    foreach ($parameters as $parameter) {
      if (array_key_exists($parameter, $_POST)) {
        $args[$parameter] = $_POST[$parameter];
      } else {
        if (in_array($parameter, $required_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }

    foreach (array("target_business_relationship", "request_center_type", "theme") as $oneValue) {
      $value = split(",", $args[$oneValue]);
      if (count($value) > 1) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $oneValue));
      }
    }

    $parametersToCheck = array("title", 'description', "technology", "interest","constraints","requirements");
    $multiValue = array("technology", "interest");
    foreach ($parametersToCheck as $param) {
      $values = $args[$param];
      if(in_array($value, $multiValue)){
        $values = split(",", $args[$param]);
      }
      if (count($values) > 1) {
        foreach ($values as $value) {
          if (!preg_match('/[أ-يa-zA-Z]+/', $value, $matches)) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
          }
        }
      } else {
        if (!preg_match('/[أ-يa-zA-Z]+/', $values, $matches) && !empty($values)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
        }
      }
    }

    if (isset($args['deadline']) && !empty($args['deadline'])) {
      if ($args['deadline'] < date('Y-m-d') || $args['deadline'] != date('Y-m-d', strtotime($args['deadline']))) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "deadline"));
      }
    }

    if (!isset($_POST["lang"]) || ($_POST["lang"] != "en" && $_POST["lang"] != "ar")) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "lang"));
    }
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    $user_id = $loggedin_user->user_id;
    $args['user_id'] = $user_id;
    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    //$user = User::find($user_id); 

    $termTax = new TermTaxonomy();
    $terms_data = array("target_bussiness_relationship" => array($args['target_business_relationship']),
      "request_center_type" => array($args['request_center_type']),
      "theme" => split(",", $args['theme']),
      "technology" => split(",", $args['technology']),
      "interest" => split(",", $args['interest'])
    );
    $tax_error = "";
    $isCreated = $termTax->saveTermTaxonomies($terms_data, array("request_center_type", "target_bussiness_relationship", "theme"),$tax_error);

    if (!$isCreated) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $tax_error));
    }

    $post = new Post();
    $post->addPost(array("post_title" => $args['title'],
      "post_author" => $args['user_id'],
      "post_status" => "pending",
      "post_type" => "request_center"));
    $post->save();
    $post->updateGUID($post->id, 'request_center');
    $post->save();
    $post->add_post_translation($post->id, $args["lang"], "request_center");
    $terms_data = array("target_bussiness_relationship" => array($args['target_business_relationship']),
      "request_center_type" => array($args['request_center_type']),
      "theme" => split(",", $args['theme'])
    );    
    $post->updatePostTermsSingle($post->id, $terms_data, true);
    $terms_data = array(
      "technology" => split(",", $args['technology']),
      "interest" => split(",", $args['interest'])
    );       
    $post->updatePostTerms($post->id, $terms_data, true);
    $metaData = ['description', 'requirements', 'constraints', 'deadline'];
    foreach ($metaData as $meta) {
      $productMeta = new Postmeta();
      $productMeta->updatePostMeta($post->id, $meta, $args[$meta]);
    }
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Request Added"));
  }

  /**
   * @SWG\PUT(
   *   path="/request-center/{request_id}",
   *   tags={"Request center"},
   *   summary="Edit a request",
   *   description="Edit a request with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to edit a request<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="request_id", in="path", required=false, type="string", description="request ID returned from list of requests <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Request Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="request_center_type", in="formData", required=false, type="string", description="Request Types <br/><b>Validations: </b><br/> 1. Predefined Reqeust Types in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="target_business_relationship", in="formData", required=false, type="string", description="Business Relationship <br/><b>Validations: </b><br/> 1. Predefined Business Relationship in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="theme", in="formData", required=false, type="string", description="Theme <br/><b>Validations: </b><br/> 1. Predefined Theme in System <br/> <b>Values: </b> English or Arabic name or ID"),
   *   @SWG\Parameter(name="description", in="formData", required=false, type="string", description="Request Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="requirements", in="formData", required=false, type="string", description="Request Requirements <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="constraints", in="formData", required=false, type="string", description="Request Constraints <br/><b>Validations: </b><br/> 1. Contains at least 1 character "),
   *   @SWG\Parameter(name="technology", in="formData", required=false, type="string", description="Request Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Request Interests <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="deadline", in="formData", required=false, type="string", description="Request Deadline <br/><b>Format: </b> yyyy-mm-dd ex. 2016-05-05 <br/><b>Validations :</b><br/> 1. Should be today or after"),
   *   @SWG\Response(response="200", description="request edited successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function editRequest($request, $response, $args) {
    $put = $request->getParsedBody();
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    $parameters = ['title', 'request_center_type', 'target_business_relationship',
      'theme', 'description', 'requirements', 'constraints', 'technology',
      'interest', 'deadline'];
    $required_params = [ 'title', 'request_id', 'request_center_type', 'target_business_relationship', 'description'];

    foreach ($parameters as $parameter) {
      if (array_key_exists($parameter, $put)) {
        $args[$parameter] = $put[$parameter];
      } else {
        if (in_array($parameter, $required_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }

    foreach (array("target_business_relationship", "request_center_type", "theme") as $oneValue) {
      $value = split(",", $args[$oneValue]);
      if (count($value) > 1) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $oneValue));
      }
    }

    $parametersToCheck = array("title", 'description', "technology", "interest","constraints","requirements");
    $multiValue = array("technology", "interest");    
    foreach ($parametersToCheck as $param) {
      $values = $args[$param];
      if(in_array($value, $multiValue)){
        $values = split(",", $args[$param]);
      }
      if (count($values) > 1) {
        foreach ($values as $value) {
          if (!preg_match('/[أ-يa-zA-Z]+/', $value, $matches)) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
          }
        }
      } else {
        if (!preg_match('/[أ-يa-zA-Z]+/', $values, $matches) && !empty($values)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
        }
      }
    }

    if (isset($args['deadline']) && !empty($args['deadline'])) {
      if ($args['deadline'] < date('Y-m-d') || $args['deadline'] != date('Y-m-d', strtotime($args['deadline']))) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "deadline"));
      }
    }

    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
    } else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    //check user has access to edit
    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    } 
    //check valid request id
    if(!is_numeric($args['request_id'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","request_id"));
    }
    $request_post = new Post();
    $request = $request_post->getPost($args['request_id']);
    //not found request
    if($request == null) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("notFound","request"));
    }
    
    //editing pending and published requests only
    if($request->post_status != 'pending') {
      return $this->renderJson($response, 422, Messages::getErrorMessage("notFound","request"));
    }
    
    //same user trying to edit
    if($user->ID != $request->post_author) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }

    $termTax = new TermTaxonomy();
    $terms_data = array("target_bussiness_relationship" => array($args['target_business_relationship']),
      "request_center_type" => array($args['request_center_type']),
      "theme" => split(",", $args['theme']),
      "technology" => split(",", $args['technology']),
      "interest" => split(",", $args['interest'])
    );
    $tax_error = "";
    $isCreated = $termTax->saveTermTaxonomies($terms_data, array("request_center_type", "target_bussiness_relationship", "theme"),$tax_error);

    if (!$isCreated) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $tax_error));
    }

    $request_query = Post::where('ID','=', $args['request_id']);
    $request = $request_query->first();
    if($request != null) {
      $request_query->update(array("post_title"=> $args['title']));
    }
    $request->updateGUID($request->ID, 'request_center');
    $request->save();
    $terms_data = array("target_bussiness_relationship" => array($args['target_business_relationship']),
      "request_center_type" => array($args['request_center_type']),
      "theme" => split(",", $args['theme'])
    );    
    $request->updatePostTermsSingle($request->ID, $terms_data, true);
    $postmeta = new Postmeta();
    if($args['theme'] == '') {
      $postmeta->updatePostMeta($request->ID, "theme", '');
    }
    $terms_data = array(
      "technology" => split(",", $args['technology']),
      "interest" => split(",", $args['interest'])
    );
    
    if($args['technology'] == '') {
      $postmeta->updatePostMeta($request->ID, "technology", '');
    }
    
    if($args['interest'] == '') {
      $postmeta->updatePostMeta($request->ID, "interest", '');
    }
    
    $request->updatePostTerms($request->ID, $terms_data, true);
    $metaData = ['description', 'requirements', 'constraints', 'deadline'];
    foreach ($metaData as $meta) {
      $postmeta->updatePostMeta($request->ID, $meta, $args[$meta]);
    }
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Request Edited"));
  }

  /**
   * @SWG\GET(
   *   path="/request-center/{request_id}",
   *   tags={"Request center"},
   *   summary="Finds a request",
   *   description="Get request data according to the passed request id",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to display pending request if exists"),
   *   @SWG\Parameter(name="request_id", in="path", required=false, type="integer", description="Provide Request ID to retrieve its details <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View a request info"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Request not found")
   * )
   */
  public function viewRequest($request, $response, $args) {
    $post = new Post();
    if(empty($_GET["lang"]) || !isset($args["request_id"]) || $args["request_id"] == "" || $args["request_id"] == "{request_id}") {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }

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
    if(!isset($_GET["lang"]) || ($_GET["lang"] != "en" && $_GET["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    $request = Post::where('ID', '=', $args['request_id'])->where('post_type', '=', 'request_center')->first();
    if ($request != null) { 
      if($user != null && !empty($user)) {
        if($request->post_author != $user->ID && !($request->post_status == 'publish' || $request->post_status == 'archive')) {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request ID"));
        }
      } else {
        if(!($request->post_status == 'publish' || $request->post_status == 'archive')) {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request ID"));
        }
      }
      
      $user = User::find($request->post_author);
      $user_name = $user->user_login;
      
      $post_meta = new Postmeta();
      $meta = $post_meta->getPostMeta($request->ID);
      $meta_request = array();
      foreach ($meta as $meta_key => $meta_value ) {
        $meta_request[$meta_value['meta_key']] = $meta_value['meta_value'];
      }

      $theme = "";
      if(array_key_exists('theme', $meta_request)) {
        $term = new Term();
        $theme_id = $meta_request['theme'];
        if ($theme_id){
          if($_GET["lang"] == "ar"){
            if($term->getTerm($theme_id)->name_ar != '')
              $theme = $term->getTerm($theme_id)->name_ar;
            else
              $theme = $term->getTerm($theme_id)->name;
          }
          else
            $theme = $term->getTerm($theme_id)->name;
        }
        else
          $theme = '--';
      }
      
      $target = "";
      if(array_key_exists('target_bussiness_relationship', $meta_request)) {
          $target = new Term();
          $target_id = $meta_request['target_bussiness_relationship'];
          if($_GET['lang'] == "ar")
          {
              if($target->getTerm($target_id)->name_ar != '')
                  $target = $target->getTerm($target_id)->name_ar;
              else
                  $target = $target->getTerm($target_id)->name;
          }
          else
              $target = $target->getTerm($target_id)->name;
      }

      $type = "";
      $typeThumbnail = '';
      if(array_key_exists('request_center_type', $meta_request)) {
        $type = new Term();
        $type_id = $meta_request['request_center_type'];
        $typeObj = $type->getTerm($type_id);
        if($_GET['lang'] == "ar") {
          if($typeObj->name_ar != '')
              $type = $typeObj->name_ar;
          else
              $type = $typeObj->name;
        }
        else
            $type = $typeObj->name;
        
        $option = new Option();
        $site_url     = $option->getOptionValueByKey('siteurl');
        $active_theme = $option->getOptionValueByKey('stylesheet');
        
        $typeThumbnail = "{$site_url}/wp-content/themes/{$active_theme}/img/{$typeObj->slug}_icon.svg";
      }
      
      //get interest
      if(!isset($meta_request['interest'])) {
        $meta_request['interest'] = '';
      }
      $interests = array();
      if(array_key_exists('interest', $meta_request)) {
          $interests_arr = unserialize($meta_request['interest']);
          for($i = 0; $i < sizeof($interests_arr); $i++) {
              if($interests_arr[$i] != '') {
                $term = new Term();
                $interest_id = $interests_arr[$i];
                array_push($interests, $term->getTerm($interest_id)->name);
              }
          }
      }

      if(!isset($meta_request['technology'])) {
        $meta_request['technology'] = '';
      }
      $technologies = array();
      if(array_key_exists('technology', $meta_request)) {
        $tech_arr = unserialize($meta_request['technology']);
        for($i = 0; $i < sizeof($tech_arr); $i++) {
            if($tech_arr[$i] != '') {
              $term = new Term();
              $tech_id = $tech_arr[$i];
              array_push($technologies, $term->getTerm($tech_id)->name);
            }
        }
      }
      
      $no_of_responses = Thread::where('request_id', '=', $request->ID)->where('responses_count', '>', 0)->count();;
            
      unset($meta_value);

      $results = array(
          "request_id" => $request->ID,
          "title" => $request->post_title,
          "status" => $request->post_status,
          "description" => $meta_request['description'],
          "constraints" => $meta_request['constraints'],
          "requirements" => $meta_request['requirements'],
          "target_bussiness_relationship" => $target,
          "request_center_type" => $type,
          "request_center_type_thumbnail" => $typeThumbnail,
          "theme" => $theme,
          "technologies" => $technologies,
          "interests" => $interests,
          "no_of_responses" =>  $no_of_responses,
          "due_date" => $meta_request['deadline'],
          "post_date" => $request->post_date,
          "added_by" => $this->return_user_info_list($request->post_author)
      );

      unset($request);
      return $this->renderJson($response, 200, $results);
    }

    return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request ID"));
  }

  /**
   * @SWG\Post(
   *   path="/request-center/{request_id}/response",
   *   tags={"Request center"},
   *   summary="Creates response",
   *   description="Create a new response with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to add response <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="request_id", in="path", required=false, type="string", description="Request ID to respond to <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="thread_id", in="formData", required=false, type="string", description="Required for request owner <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="message", in="formData", required=false, type="string", description="Respond Message <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="response added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addResponse($request, $response, $args) {
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    $parameters = ['thread_id', 'message'];
    $required_params = ['message'];

    foreach ($parameters as $parameter) {
      if (array_key_exists($parameter, $_POST)) {
        $args[$parameter] = $_POST[$parameter];
      } else {
        if (in_array($parameter, $required_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }

    $result = "";
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      }
      $request_record = Post::where('ID', '=', $args['request_id'])->first();
      if ($request_record->post_author == $user_id) {
        $thread = !empty($args['thread_id']) ? Thread::where('request_id', '=', $args['request_id'])->where('id', '=', $args['thread_id'])->first() : null;
      } else if (!empty($args['thread_id'])) {
        $thread = Thread::where('request_id', '=', $args['request_id'])->where('id', '=', $args['thread_id'])->first();
      } else {
        $thread = Thread::where('request_id', '=', $args['request_id'])->where('user_id', '=', $user_id)->first();
      }
      if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else if ($request_record == null) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request"));
      } else if ($request_record->post_status == 'archive' && ($thread == null)) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("archived", "Request"));
      } else if (!in_array($request_record->post_status, ['publish', 'archive'])) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request"));
      } else if ( ($request_record->post_author == $user_id) && empty($args['thread_id'])) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "thread_id"));
      } else if (($thread != null) && ($thread->status == 0)) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("archived", "Thread"));
      } else if ((!empty($args['thread_id'])) && ($thread != null) && !($user_id == $thread->owner_id || $user_id == $thread->user_id)) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Thread"));
      } else if (!preg_match('/[أ-يa-zA-Z]+/', $args['message'], $matches)) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "message"));
      } else if (($request_record->post_author != $user_id) && (!$this->user_can($request_record->post_author, 'add_new_ef_posts'))) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("authorNotActive"));
      } else if (($request_record->post_author == $user_id) && (!$this->user_can($thread->user_id, 'add_new_ef_posts'))) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("responderNotActive"));
      } else {
        $request_response = new Response;
        $data = array(
          'request_id' => $args['request_id'],
          'thread_id' => (($thread != null) ? $thread->id : ''),
          'owner_id' => $request_record->post_author,
          'user_id' => $user_id,
          'message' => $args['message'],
        );
        $request_response->addResponse($data);
        $saved = $request_response->save();
        if($saved) {
          $thread = Thread::where('id', '=', $request_response->thread_id)->first();
          $data = array('responses_count' => 1);
          if($thread->owner_id == $user_id) {
            $to = User::find($thread->user_id);
            $data['seen_by_owner'] = 1;
            $data['seen_by_user'] = 0;
          } else {
            $to = User::find($thread->owner_id);
            $data['seen_by_owner'] = 0;
            $data['seen_by_user'] = 1;
          }
          $thread->updateThread($data);
          $thread->save();

          $meta = new Usermeta();
          $mail_data = array(
            'request_id' => $request_record->ID,
            'request_url' => $request_record->guid,
            'request_title' => $request_record->post_title,
            'thread_id' => $thread->id,
            'lang' => $meta->getUserMeta($to->ID, 'prefered_language'),
            'to' => array(
              'name' => $to->user_nicename,
              'email' => $to->user_email,
            ),
            'from' => array(
              'name' => $user->user_nicename,
              'email' => $user->user_email,
            )
          );
          $mailer = new NotifyResponseMailer();
          $mailer->sendresponsenotification($mail_data, $response);
        }
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Response Added"));
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }

  /**
   * @SWG\Get(
   *   path="/request-center/{request_id}/my/responses",
   *   tags={"Request center"},
   *   summary="List my request responses",
   *   description="List my request responses",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to list user responses <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="request_id", in="path", required=false, type="string", description="Request ID to list its responses <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="threads list retrieved"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listThreads($request, $response, $args) {
    $result = "";

    $params = $request->getHeaders();
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      $request_record = Post::where('ID', '=', $args['request_id'])->first();
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else if ($request_record == null) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request"));
      } else if ($request_record->post_status == 'pending') {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request"));
      } else {
        $threads_records = Thread::where('request_id', '=', $args['request_id'])->join('users','users.ID','=','request_threads.user_id');
        if ( ($request_record->post_author != $user_id) ) {
          $threads_records = $threads_records->where('user_id', '=', $user_id);
        }
        $owner = User::find($request_record->post_author);
        $threads_records = $threads_records->where('responses_count', '>', '0')->orderBy('updated_at', 'DESC')->get();
        for($i = 0; $i < sizeof($threads_records); $i++) {
          $threads[$i]['thread_id'] = $threads_records[$i]['id'];
          $threads[$i]['request_id'] = $threads_records[$i]['request_id'];
          $threads[$i]['owner_id'] = $threads_records[$i]['owner_id'];
          $threads[$i]['owner_username'] = $owner->user_login;
          $threads[$i]['responder_id'] = $threads_records[$i]['user_id'];
          $threads[$i]['responder_username'] = $threads_records[$i]['user_login'];
          $threads[$i]['is_seen_by_owner'] = ($threads_records[$i]['seen_by_owner'] == 1) ? true : false;
          $threads[$i]['is_seen_by_responder'] = ($threads_records[$i]['seen_by_user'] == 1) ? true : false;
          $threads[$i]['responses_count'] = $threads_records[$i]['responses_count'];
          $threads[$i]['is_archived'] = ($threads_records[$i]['status'] == 0) ? true : false;
          $threads[$i]['created_at'] = $threads_records[$i]['created_at'];
          $threads[$i]['updated_at'] = $threads_records[$i]['updated_at'];
          $threads[$i]['added_by'] = $this->return_user_info_list($threads_records[$i]['user_id']);
          $last_reply = Response::where('thread_id', '=', $threads_records[$i]['id'])->orderBy('created_at', 'desc')->first();
          $threads[$i]['last_reply'] = array(
            'message' => $last_reply->message,
            'created_at' => $last_reply->created_at,
            'added_by' => ($threads_records[$i]['user_id'] == $last_reply->user_id) ? $threads[$i]['added_by'] : $this->return_user_info_list($last_reply->user_id),
          );
        }
        $result = $this->renderJson($response, 200, $threads);
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }  

  /**
   * @SWG\GET(
   *   path="/request-center",
   *   tags={"Request center"},
   *   summary="List Request center",
   *   description="List Request center",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfRequests", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="type", in="query", required=false, type="string", description="Filter list of requests by type <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Parameter(name="theme", in="query", required=false, type="string", description="Filter list of requests by theme <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Parameter(name="target", in="query", required=false, type="string", description="Filter list of requests by Target Relationship <br/> <b>Values: </b> English or Arabic name or ID "), 
   *   @SWG\Response(response="200", description="List Request center"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listRequestCenter($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfRequests', 'lang', 'type', 'theme', 'target'];
    $requiredParams = ['pageNumber', 'numberOfRequests', 'lang'];
    $numeric_params = ['pageNumber', 'numberOfRequests'];
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
    if(empty($args['numberOfRequests'] ) || $args['numberOfRequests'] < 1 || $args['numberOfRequests'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of Requests',array("range"=> "1 and 25 ")));
    }
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    else{
      $post = new Post();
      $skip_number = ($args['pageNumber'] * $args['numberOfRequests']) - $args['numberOfRequests'] ;
      if ($args['pageNumber'] == 1){
        $skip_number = 0 ;
      }
      
      //get type id
      if(!isset($args["type"]))
      {
        $type_id = '';
      }else
      {
        $type_id = $this->ef_retrieve_taxonomy_id('request_center_type', $args["type"]);
      }            
      
      //get theme id
      if(!isset($args["theme"]))
      {
        $theme_id = '';
      }else
      {
        $theme_id = $this->ef_retrieve_taxonomy_id('theme', $args["theme"]);
      }        

      //get target id
      if(!isset($args["target"]))
      {
        $target_id = '';
      }else
      {
        $target_id = $this->ef_retrieve_taxonomy_id('target_bussiness_relationship', $args["target"]);
      }       

      $list_request_center = $post->getAllRequestCenter('request_center', 'publish', $args['numberOfRequests'], $skip_number, $args["lang"], $type_id, $theme_id, $target_id);
      $request_center_result = array();
      if (count($list_request_center) == 0){
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }
      else{
        $total_count = count($post->getAllRequestCenter('request_center', 'publish', -1, -1, $args["lang"], $type_id, $theme_id, $target_id));
        $request_center_result = $this->ef_load_data_counts($total_count, $args['numberOfRequests']);
        $index = 0;
        foreach ($list_request_center as $request_record) {
          $request_center_id = $request_record->ID;
          // $user = User::find($request_record->post_author);
          // $user_name = $user->user_login;

          $post_meta = new Postmeta();
          $meta = $post_meta->getPostMeta($request_center_id);
          $request_center_meta = array();
          foreach ($meta as $meta_key => $meta_value ) {
            $request_center_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
          }
          unset($meta_value);
                      
          //get type
          $type = "";
          $typeThumbnail = "";
          if(array_key_exists('request_center_type', $request_center_meta)){
            $term = new Term();
            $type_id = $request_center_meta['request_center_type'];
            $typeObj = $term->getTerm( $type_id );
            if($args["lang"] == "ar"){
              if($typeObj->name_ar != '')
                $type = $typeObj->name_ar;
              else
                $type = $typeObj->name;
            }
            else
              $type = $typeObj->name;
            
            $option = new Option();
            $site_url     = $option->getOptionValueByKey('siteurl');
            $active_theme = $option->getOptionValueByKey('stylesheet');

            $typeThumbnail = "{$site_url}/wp-content/themes/{$active_theme}/img/{$typeObj->slug}_icon.svg";
          }
            
          //get theme
          $theme = "";
          if(array_key_exists('theme', $request_center_meta)){
            $term = new Term();
            $theme_id = $request_center_meta['theme'];
            if ($theme_id){
              if($args["lang"] == "ar"){
                if($term->getTerm($theme_id)->name_ar != '')
                  $theme = $term->getTerm($theme_id)->name_ar;
                else
                  $theme = $term->getTerm($theme_id)->name;
              }
              else
                $theme = $term->getTerm($theme_id)->name;
            }
            else
              $theme = '--';
          }
            
          //get target
          $target = "";
          if(array_key_exists('target_bussiness_relationship', $request_center_meta)){
            $term = new Term();
            $target_id = $request_center_meta['target_bussiness_relationship'];
            if($args["lang"] == "ar"){
              if($term->getTerm($target_id)->name_ar != '')
                $target = $term->getTerm($target_id)->name_ar;
              else
                $target = $term->getTerm($target_id)->name;
            }
            else
              $target = $term->getTerm($target_id)->name;
          }

          $no_of_responses = Thread::where('request_id', '=', $request_record->ID)->where('responses_count', '>', 0)->count();;
          
          $request_center_result['data'][$index] = array(
              "request_center_id"     => $request_center_id,
              "request_center_title"  => $request_record->post_title,
              "description"           => $request_center_meta['description'],
              "type"                  => $type,
              "type_thmbnail"         => $typeThumbnail,
              "theme"                 => $theme,
              "target"                => $target,
              "no_of_responses"       => $no_of_responses,
              "due_date"              => $request_center_meta['deadline'],
              "post_date"             => $request_record->post_date,
              "added_by"              => $this->return_user_info_list($request_record->post_author)
          );
          
          $index += 1;
        }
        $result = $this->renderJson($response, 200, $request_center_result);
      }
      return $result;
    }
  }

  /**
   * @SWG\Post(
   *   path="/request-center/{request_id}/archive",
   *   tags={"Request center"},
   *   summary="Archive request",
   *   description="Archive request to prevent further responses",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to archive <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="request_id", in="path", required=false, type="string", description="Request ID to archive <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="request archived successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function archiveRequest($request, $response, $args) {
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }

    $result = "";
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      $request_record = Post::where('ID', '=', $args['request_id'])->first();
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else if ($request_record == null) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request"));
      } else if ($request_record->post_status != 'publish') {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request"));
      } else if ( ($request_record->post_author != $user_id) ) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Request"));
      } else {
        $request_record->updatePostStatus($request_record->ID, 'archive');
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Request Archived"));
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }

  /**
   * @SWG\Post(
   *   path="/request-center/response/{thread_id}/archive",
   *   tags={"Request center"},
   *   summary="Archive thread",
   *   description="Archive thread to prevent further messages",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to archive  thread<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="thread_id", in="path", required=false, type="string", description="Thread ID to archive <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="thread archived successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function archiveThread($request, $response, $args) {
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }

    if (!isset($args['thread_id']) || empty($args['thread_id']) || ($args['thread_id']=="{thread_id}")) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "thread_id"));
    }

    $result = "";
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      $thread_record = Thread::where('id', '=', $args['thread_id'])->first();
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else if ($thread_record == null) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Thread"));
      } else if ( ($thread_record->owner_id != $user_id) ) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Thread"));
      } else if ($thread_record->status == 0) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("archived", "Thread"));
      } else if ($thread_record->status != 1) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Thread"));
      } else {
        $thread_record->updateThread(array('status' => 0));
        $thread_record->save();
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Thread Archived"));
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }

  /**
   * @SWG\GET(
   *   path="/request-center/thread/{thread_id}",
   *   tags={"Request center"},
   *   summary="Show a thread",
   *   description="Get thread messages according to the passed thread id",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to get thread message <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="thread_id", in="path", required=false, type="integer", description="Provide Thread ID <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View a thread messages"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Thread not found")
   * )
   */
  public function viewThread($request, $response, $args) {
    if(!isset($args["thread_id"]) || $args["thread_id"] == "" || $args["thread_id"] == "{thread_id}") {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
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
    $thread = Thread::where('id', '=', $args['thread_id'])->first();
    if ($thread == null) {
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Thread"));
    } else if ( !($user_id == $thread->owner_id || $user_id == $thread->user_id) ) {
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Thread"));
    } else {
      // Mark thread as seen when requested
      if($thread->owner_id == $user_id) {
        $data['seen_by_owner'] = 1;
      } else {
        $data['seen_by_user'] = 1;
      }
      $thread->updateThread($data);
      $thread->save();

      $option = new Option();
      $host = $option->getOptionValueByKey('siteurl');
      $users_responses = Response::join('users', 'users.ID', '=', 'thread_responses.user_id')->where('thread_id', '=', $thread->id)->get(['user_id','user_nicename', 'display_name', 'message','created_at']);
      $responses = array();
      foreach ($users_responses as $user_response) {
        $user_id = $user_response->user_id;
        $directory = dirname(__FILE__)."/../../../../wp-content/uploads/avatars/$user_id/";
        $image_location = glob($directory . "*bpfull*");            
        foreach(glob($directory . "*bpfull*") as $image_name) {
          $image_name = end(explode("/", $image_name));
          $image = $host."/wp-content/uploads/avatars/$user_id/".$image_name;
        }
        if (empty($image_location)) {
          $user_meta = new Usermeta();
          $image = $user_meta->getUserMeta($user_id, "wsl_current_user_image");
          if (empty($image)) {
            $email = $user->user_email;
            $size = '150';
            $image = $host.'/wp-content/themes/egyptfoss/img/default_avatar.png';
          }
        }
        array_push($responses, array(
          'message' => $user_response->message,
          'created_at' => $user_response->created_at,
          'added_by' => array(
            'display_name' => $user_response->display_name,
            'username' => $user_response->user_nicename,
            'profile_picture' => $image
          )
        ));
      }
      $thread_data = array(
        'request' => $thread->request_id,
        'owner' => $thread->owner_id,
        'user' => $thread->user_id,
        'responses' => $responses
      );
      $result = $this->renderJson($response, 200, $thread_data);
    }
    return $result;
  }

}

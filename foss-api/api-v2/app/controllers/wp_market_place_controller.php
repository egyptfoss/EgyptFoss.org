<?php

class WPMarketPlaceController extends EgyptFOSSController {

  /**
   * @SWG\Post(
   *   path="/market-place/services",
   *   tags={"Market Place"},
   *   summary="Creates New Service",
   *   description="Create a new service in the market place with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to create a new service<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Define Service Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Service Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="theme", in="formData", required=false, type="string", description="Theme <br/><b>Validations: </b><br/> 1. Predefined Theme in System <br/> <b>Values: </b> English or Arabic name or ID "),
   *   @SWG\Parameter(name="service_category", in="formData", required=false, type="string", description="Category <br/><b>Validations: </b><br/> 1. Predefined Category in System <br/> <b>Values: </b> English or Arabic name or ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="description", in="formData", required=false, type="string", description="Service Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="service_image", in="formData", required=false, type="file", description="Service Featured Image <br/><b>Validations: </b><br/> 1. Valid Image"),
   *   @SWG\Parameter(name="constraints", in="formData", required=false, type="string", description="Service Constraints <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="conditions", in="formData", required=false, type="string", description="Service Conditions <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="technology", in="formData", required=false, type="string", description="Service Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Service Interests <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Response(response="200", description="Service added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addService($request, $response, $args) {
    $parameters = [ 'lang', 'title', 'theme', 'service_category',
      'description', 'conditions', 'constraints', 'technology', 'interest'];
    $required_params = [ 'title', 'lang', 'service_category', 'description'];

//    if(empty($_FILES) || !isset($_FILES['service_image'])) {
//      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", 'service_image'));
//    }

    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $_POST)) {
        $args[$parameter] = $_POST[$parameter];
      } else {
        if(in_array($parameter, $required_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
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
    }else {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }

    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }

    if(isset($_FILES["service_image"]) && !empty($_FILES["service_image"]['tmp_name']) && exif_imagetype($_FILES["service_image"]['tmp_name']) == FALSE ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","image type"));
    }

      
      $value = split(",", $args['service_category']);
      if(count($value) > 1 || !is_numeric($args['service_category'])) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", 'service_category'));
      }
      
      if( !empty($args['theme']) ) {
        $value = split(",", $args['theme']);
        if(count($value) > 1 || !is_numeric($args['theme'])) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", 'theme'));
        }
      }

    $parametersToCheck = array("title", 'description', "technology", "interest","constraints", "conditions");
    $multiValue = array("technology", "interest");
    foreach ($parametersToCheck as $param) {
      $values = $args[$param];
      if(in_array($value, $multiValue)){
        $values = split(",", $args[$param]);
      }
      if(count($values) > 1) {
        foreach ($values as $value) {
          if(!preg_match('/[أ-يa-zA-Z]+/', $value, $matches)) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
          }
        }
      } else {
        if(!preg_match('/[أ-يa-zA-Z]+/', $values, $matches) && !empty($values)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
        }
      }
    }

    if(!isset($_POST["lang"]) || ($_POST["lang"] != "en" && $_POST["lang"] != "ar")) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "lang"));
    }

    $termTax = new TermTaxonomy();
    $terms_data = array(
      "service_category" => (!empty($args['service_category'])) ? array($args['service_category']) : array(),
      "theme" => (!empty($args['theme'])) ? array($args['theme']) : array(),
      "technology" => (!empty($args['technology'])) ? split(",", $args['technology']) : array(),
      "interest" => (!empty($args['interest'])) ? split(",", $args['interest']) : array()
    );
    $tax_error = "";
    $isCreated = $termTax->saveTermTaxonomies($terms_data, array("theme", "service_category"), $tax_error);

    if(!$isCreated) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $tax_error));
    }

    $service = new Post();
    $service->addPost(
      array(
      "post_title" => $args['title'],
      "post_content" => $args['description'],
      "post_author" => $user_id,
      "post_type" => 'service',
      "post_status" => "pending"
      )
    );
    $service->save();
    $service_id = $service->id;
    $service->updateGUID($service_id, 'service');
    $service->save();
    $service->add_post_translation($service_id, $args["lang"], "service");
    if(isset($_FILES['service_image'])) {
      $img_args = array("post_status" => "inherit",
                        "post_type" => "attachment",
                        "post_author" => $user_id
                      );
      $service->updateFeaturedImage($img_args, $service_id, $_FILES['service_image']); 
    }
    $terms_data = array(
      "theme" => array($args['theme']),
      "service_category" => array($args['service_category'])
    );    
    $service->updatePostTermsSingle($service_id, $terms_data, true);
    $terms_data = array(
      "technology" => split(",", $args['technology']),
      "interest" => split(",", $args['interest'])
    );       
    $service->updatePostTerms($service_id, $terms_data, true);
    $metaData = ['constraints', 'conditions'];
    foreach ($metaData as $meta) {
      $serviceMeta = new Postmeta();
      $serviceMeta->updatePostMeta($service_id, $meta, $args[$meta]);
    }
    
    $returnMsg = Messages::getSuccessMessage("Success","Service Added");
    $returnMsg['service_id'] = $service_id;
    $returnMsg['is_pending_review'] = !($service->post_status == 'publish');
    
    return $this->renderJson($response, 200, $returnMsg);
  }

  /**
   * @SWG\Post(
   *   path="/market-place/services/{service_id}",
   *   tags={"Market Place"},
   *   summary="Edits Service",
   *   description="Edits a service in the market place with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to edit a service<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="service_id", in="path", required=false, type="string", description="service ID returned from list market place services<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Service Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="theme", in="formData", required=false, type="string", description="Theme <br/><b>Validations: </b><br/> 1. Predefined Theme in System <br/> <b>Values: </b> English or Arabic name or ID "),
   *   @SWG\Parameter(name="service_category", in="formData", required=false, type="string", description="Category <br/><b>Validations: </b><br/> 1. Predefined Category in System <br/> <b>Values: </b> English or Arabic name or ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="description", in="formData", required=false, type="string", description="Service Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="service_image", in="formData", required=false, type="file", description="Service Featured Image <br/><b>Validations: </b><br/> 1. Valid Image"),
   *   @SWG\Parameter(name="constraints", in="formData", required=false, type="string", description="Service Constraints <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="conditions", in="formData", required=false, type="string", description="Service Conditions <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="technology", in="formData", required=false, type="string", description="Service Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Service Interests <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Response(response="200", description="Service added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function editService($request, $response, $args) {
    $parameters = ['title', 'theme', 'service_category',
      'description', 'conditions', 'constraints', 'technology', 'interest'];
    $required_params = ['title', 'service_category', 'description'];

    $params = $request->getHeaders();
    
    if( empty( $args['service_id'] ) || $args['service_id'] == '{service_id}' ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "service_id"));
    }
    
    if(empty($params['HTTP_TOKEN']) || ( is_array($params['HTTP_TOKEN']) && empty( $params['HTTP_TOKEN'][0] ) ) ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    
    $loggedin_user = AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first();
    if($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    $user_id = $loggedin_user->user_id;
    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }

    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $_POST) && !empty($_POST[$parameter])) {
        $args[$parameter] = $_POST[$parameter];
      } else {
        if(in_array($parameter, $required_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }

    if(isset($_FILES["service_image"]) && !empty($_FILES["service_image"]['tmp_name']) && exif_imagetype($_FILES["service_image"]['tmp_name']) == FALSE ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","image type"));
    }

    foreach (array("theme", "service_category") as $oneValue) {
      if(!empty($args[$oneValue])) {
        $value = split(",", $args[$oneValue]);
        if(count($value) > 1 || !is_numeric($args[$oneValue])) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $oneValue));
        }
      }
    }

    $parametersToCheck = array("title", 'description', "technology", "interest","constraints", "conditions");
    $multiValue = array("technology", "interest");
    foreach ($parametersToCheck as $param) {
      $values = $args[$param];
      if(in_array($value, $multiValue)){
        $values = split(",", $args[$param]);
      }
      if(count($values) > 1) {
        foreach ($values as $value) {
          if(!preg_match('/[أ-يa-zA-Z]+/', $value, $matches)) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
          }
        }
      } else {
        if(!preg_match('/[أ-يa-zA-Z]+/', $values, $matches) && !empty($values)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param ));
        }
      }
    }

    $termTax = new TermTaxonomy();
    $terms_data = array(
      "service_category" => (!empty($args['service_category'])) ? array($args['service_category']) : array(),
      "theme" => (!empty($args['theme'])) ? array($args['theme']) : array(),
      "technology" => (!empty($args['technology'])) ? split(",", $args['technology']) : array(),
      "interest" => (!empty($args['interest'])) ? split(",", $args['interest']) : array()
    );
    $tax_error = "";
    $isCreated = $termTax->saveTermTaxonomies($terms_data, array("service_category", "theme"), $tax_error);

    if (!$isCreated) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $tax_error));
    }

    $service_query = Post::where('ID', '=', $args['service_id'])->where('post_type', '=', 'service')->where('post_author', '=', $user_id);
    $service = $service_query->first();
    if( $service == null ) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
    } else if ($service->post_status == 'archive') {
      return $this->renderJson($response, 422, Messages::getErrorMessage("archived", "Service"));
    } else {
      $service_id = $service->ID;
      $service_data = array(
        'post_title'   => $args['title'],
        'post_content' => $args['description'],
      );
      $service_query->update( $service_data );

      $terms_data = array(
        "theme" => array($args['theme']),
        "service_category" => array($args['service_category'])
      );    
      $service->updatePostTermsSingle($service_id, $terms_data, true);
      $terms_data = array(
        "technology" => split(",", $args['technology']),
        "interest" => split(",", $args['interest'])
      );       
      $service->updatePostTerms($service_id, $terms_data, true);

      $rest_terms = ['theme', 'technology', 'interest'];
      foreach ($rest_terms as $term) {
        if($args[$term] == '') {
          $serviceMeta = new Postmeta();
          $serviceMeta->updatePostMeta($service_id, $term, '');
        }
      }
      $metaData = ['constraints', 'conditions'];
      foreach ($metaData as $meta) {
        $serviceMeta = new Postmeta();
        $serviceMeta->updatePostMeta($service_id, $meta, $args[$meta]);
      }
      if( !empty( $_FILES['service_image'] ) ) {
        $img_args = array("post_status" => "inherit",
                          "post_type" => "attachment",
                          "post_author" => $loggedin_user->user_id);
        $service->updateFeaturedImage( $img_args, $service_id, $_FILES['service_image'] ); 
      }
      return $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Service Edited"));
    }
  }

  /**
   * @SWG\GET(
   *   path="/market-place/service/{service_id}",
   *   tags={"Market Place"},
   *   summary="Finds a service",
   *   description="Get service data according to the passed service id",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to display pending service if exists"),
   *   @SWG\Parameter(name="service_id", in="path", required=false, type="integer", description="Provide Service ID to retrieve its details <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View a service info"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Service not found")
   * )
   */
  public function viewService($request, $response, $args) {
    global $en_sub_types;
    global $ar_sub_types;
    $post = new Post();
    if(empty($_GET["lang"]) || !isset($args["service_id"]) || $args["service_id"] == "" || $args["service_id"] == "{service_id}") {
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
    
    $service = Post::where('ID', '=', $args['service_id'])->where('post_type', '=', 'service')->first();
    if($service != null) { 
      if($user != null && !empty($user)) {
        if($service->post_author != $user->ID && !($service->post_status == 'publish' || $service->post_status == 'archive')) {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service ID"));
        }
      } else {
        if(!($service->post_status == 'publish' || $service->post_status == 'archive')) {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service ID"));
        }
      }
      $service_id = $service->ID;
      $user = User::find($service->post_author);
      $user_name = $user->user_login;

      $post_meta = new Postmeta();
      $meta = $post_meta->getPostMeta($service_id);
      $meta_service = array();
      foreach ($meta as $meta_key => $meta_value ) {
        $meta_service[$meta_value['meta_key']] = $meta_value['meta_value'];
      }

      $theme = "";
      if(array_key_exists('theme', $meta_service)) {
        $term = new Term();
        $theme_id = $meta_service['theme'];
        $theme = '--';
        if($theme_id) {
          if($_GET["lang"] == "ar") {
            if($term->getTerm($theme_id)->name_ar != '') {
              $theme = $term->getTerm($theme_id)->name_ar;
            } else {
              $theme = $term->getTerm($theme_id)->name;
            }
          } else {
            $theme = $term->getTerm($theme_id)->name;
          }
        }
      }

      $category = "";
      $category_id = "";
      if(array_key_exists('service_category', $meta_service)) {
        $category = new Term();
        $category_id = $meta_service['service_category'];
        $categoryObj = $category->getTerm($category_id);
        if($_GET['lang'] == "ar") {
          if($categoryObj->name_ar != '') {
            $category = $categoryObj->name_ar;
          } else {
            $category = $categoryObj->name;
          }
        } else {
          $category = $categoryObj->name;
        }
      }

      $url = '';
      if(array_key_exists('_thumbnail_id', $meta_service)) {
        $attachment_id = $meta_service['_thumbnail_id'];
        $service_image = Post::getPostByID($attachment_id, "attachment", "inherit");
        if($service_image) {
          $url = $service_image->guid;
        }
      }

      if(!isset($meta_service['interest'])) {
        $meta_service['interest'] = '';
      }
      $interests = array();
      if(array_key_exists('interest', $meta_service)) {
        $interests_arr = unserialize($meta_service['interest']);
        for($i = 0; $i < sizeof($interests_arr); $i++) {
          if($interests_arr[$i] != '') {
            $term = new Term();
            $interest_id = $interests_arr[$i];
            array_push($interests, $term->getTerm($interest_id)->name);
          }
        }
      }

      if(!isset($meta_service['technology'])) {
        $meta_service['technology'] = '';
      }
      $technologies = array();
      if(array_key_exists('technology', $meta_service)) {
        $tech_arr = unserialize($meta_service['technology']);
        for($i = 0; $i < sizeof($tech_arr); $i++) {
          if($tech_arr[$i] != '') {
            $term = new Term();
            $tech_id = $tech_arr[$i];
            array_push($technologies, $term->getTerm($tech_id)->name);
          }
        }
      }
      
      $no_of_responses = Thread::where('request_id', '=', $service_id)->where('responses_count', '>', 0)->count();
      
      $author_meta = new Usermeta();
      $meta = $author_meta->getMeta($service->post_author);
      if($_GET["lang"] == "ar") {
        $sub_types = $ar_sub_types;
      } else {
        $sub_types = $en_sub_types;
      }
      $author_type = (empty($meta['type'])) ? "" : $meta['type'];
      $sub_type = $meta['sub_type'];
      $author_sub_type = (!empty($sub_type) && array_key_exists($sub_type, $sub_types)) ? array($sub_type => $sub_types[$sub_type]) : array();
      
      $average_rate = array('rate' => 0, 'reviewers_count' => 0);
      $rate_meta = Postmeta::where('post_id', '=', $service_id)->whereIn('meta_key', array('reviewers_count', 'rate'))->get();
      foreach ($rate_meta as $meta) {
        $average_rate[$meta['meta_key']] = $meta['meta_value'];
      }
      
      // top service flag
      $is_top_service = $post_meta->getMetaValue( $service_id, 'efb_is_top_service' );
      $is_top_service = ( $is_top_service == '1' ) ? TRUE:FALSE;

      $results = array(
          "service_id" => $service_id,
          "title" => $service->post_title,
          "status" => $service->post_status,
          "category" => $category,
          "theme" => $theme,
          "thumbnail" => $url,
          "description" => $service->post_content,
          "constraints" => $meta_service['constraints'],
          "conditions" => $meta_service['conditions'],
          "technologies" => $technologies,
          "interests" => $interests,
          "no_of_requests" => $no_of_responses,
          "post_date" => $service->post_date,
          "added_by" => $this->return_user_info_list($service->post_author, $_GET["lang"]),
          "average_rate" => $this->service_average_rate($service_id),
          "author_type" => $author_type,
          "author_sub_type" => $author_sub_type,
          "is_top_service"  => $is_top_service
      );
      return $this->renderJson($response, 200, $results);
    }
    return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service ID"));
  }

  /**
   * @SWG\Post(
   *   path="/market-place/service/{service_id}/request",
   *   tags={"Market Place"},
   *   summary="Creates service request reply",
   *   description="Create a new service request reply with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to add response <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="service_id", in="path", required=false, type="string", description="service id to reply to <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="thread_id", in="formData", required=false, type="string", description="required for Service Provider <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="message", in="formData", required=false, type="string", description="Respond Message <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Service request reply added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addRequestReply($request, $response, $args) {
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
      $service_record = Post::where('ID', '=', $args['service_id'])->where('post_type', '=', 'service')->first();
      if ($service_record->post_author == $user_id) {
        $thread = !empty($args['thread_id']) ? Thread::where('request_id', '=', $args['service_id'])->where('id', '=', $args['thread_id'])->first() : null;
      } else if (!empty($args['thread_id'])) {
        $thread = Thread::where('request_id', '=', $args['service_id'])->where('id', '=', $args['thread_id'])->first();
      } else {
        $thread = Thread::where('request_id', '=', $args['service_id'])->where('user_id', '=', $user_id)->first();
      }
      if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else if ($service_record == null) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
      } else if ($service_record->post_status == 'archive' && ($thread == null)) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("archived", "Service"));
      } else if (!in_array($service_record->post_status, ['publish', 'archive'])) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
      } else if ( ($service_record->post_author == $user_id) && empty($args['thread_id'])) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "thread_id"));
      } else if (($thread != null) && ($thread->status == 0)) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("archived", "Thread"));
      } else if ((!empty($args['thread_id'])) && ($thread != null) && !($user_id == $thread->owner_id || $user_id == $thread->user_id)) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Thread"));
      } else if (!preg_match('/[أ-يa-zA-Z]+/', $args['message'], $matches)) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "message"));
      } else if (($service_record->post_author != $user_id) && (!$this->user_can($service_record->post_author, 'add_new_ef_posts'))) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("authorNotActive"));
      } else if (($service_record->post_author == $user_id) && (!$this->user_can($thread->user_id, 'add_new_ef_posts'))) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("responderNotActive"));
      } else {
        $service_response = new Response;
        $data = array(
          'request_id' => $args['service_id'],
          'thread_id' => (($thread != null) ? $thread->id : ''),
          'owner_id' => $service_record->post_author,
          'user_id' => $user_id,
          'message' => $args['message'],
        );
        $service_response->addResponse($data);
        $saved = $service_response->save();
        if($saved) {
          $thread = Thread::where('id', '=', $service_response->thread_id)->first();
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
            'service_id' => $service_record->ID,
            'service_url' => $service_record->guid,
            'service_title' => $service_record->post_title,
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
          $mailer->sendresponsenotification($mail_data, $response, "service");
        }
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Service request reply Added"));
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }

  /**
   * @SWG\Get(
   *   path="/market-place/service/{service_id}/my/requests",
   *   tags={"Market Place"},
   *   summary="List my service responses",
   *   description="List my service responses",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to list user responses <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="service_id", in="path", required=false, type="string", description="Service ID to list its responses <br/> <b>[Required]</b>"),
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
      $service_record = Post::where('ID', '=', $args['service_id'])->where('post_type', '=', 'service')->first();
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else if ($service_record == null) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
      } else if ($service_record->post_status == 'pending') {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
      } else {
        $threads_records = Thread::where('request_id', '=', $args['service_id'])->join('users','users.ID','=','request_threads.user_id');
        if ( ($service_record->post_author != $user_id) ) {
          $threads_records = $threads_records->where('user_id', '=', $user_id);
        }
        $owner = User::find($service_record->post_author);
        $threads_records = $threads_records->where('responses_count', '>', '0')->orderBy('updated_at', 'DESC')->get();
        for($i = 0; $i < sizeof($threads_records); $i++) {
          $threads[$i]['thread_id'] = $threads_records[$i]['id'];
          $threads[$i]['service_id'] = $threads_records[$i]['request_id'];
          $threads[$i]['owner_id'] = $threads_records[$i]['owner_id'];
          $threads[$i]['owner_username'] = $owner->user_login;
          $threads[$i]['responder_id'] = $threads_records[$i]['user_id'];
          $threads[$i]['responder_username'] = $threads_records[$i]['user_login'];
          $threads[$i]['is_seen_by_owner'] = ($threads_records[$i]['seen_by_owner'] == 1) ? true : false;
          $threads[$i]['is_seen_by_responder'] = ($threads_records[$i]['seen_by_user'] == 1) ? true : false;
          $threads[$i]['requests_count'] = $threads_records[$i]['responses_count'];
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
   * @SWG\Post(
   *   path="/market-place/service/{service_id}/archive",
   *   tags={"Market Place"},
   *   summary="Archive service",
   *   description="Archive service to prevent further responses",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to archive <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="service_id", in="path", required=false, type="string", description="Service ID to archive <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="service archived successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function archiveService($request, $response, $args) {
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }

    $result = "";
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      $service_record = Post::where('ID', '=', $args['service_id'])->where('post_type', '=', 'service')->first();
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else if ($service_record == null) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
      } else if ($service_record->post_status == 'archive') {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("archived", "Service already"));
      } else if ($service_record->post_status == 'pending') {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("archived", "Pending service can not be"));
      } else if ($service_record->post_status != 'publish') {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
      } else if ( ($service_record->post_author != $user_id) ) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
      } else if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      } else {
        $service_record->updatePostStatus($service_record->ID, 'archive');
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("Success", "Service Archived"));
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }

  /**
   * @SWG\Post(
   *   path="/market-place/service/request/{thread_id}/archive",
   *   tags={"Market Place"},
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
   *   path="/market-place/service/thread/{thread_id}",
   *   tags={"Market Place"},
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
      $requester_review = Review::where('rateable_id', '=', $thread->request_id)->where('reviewer_id', '=', $thread->user_id)->first();
      $requester_rate = ($requester_review == null) ? array() : array("rate"=>$requester_review->rate,"message"=>$requester_review->review);
      $thread_data = array(
        'service' => $thread->request_id,
        'owner' => $thread->owner_id,
        'user' => $thread->user_id,
        'requester_review' => $requester_rate,
        'responses' => $responses,
        'is_archived' => ($thread->status == 0) ? true : false
      );
      $result = $this->renderJson($response, 200, $thread_data);
    }
    return $result;
  }
  
  /**
   * @SWG\GET(
   *   path="/market-place",
   *   tags={"Market Place"},
   *   summary="List services",
   *   description="List services",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfRequests", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="category", in="query", required=false, type="string", description="Filter list of services by service category <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Parameter(name="technology", in="query", required=false, type="string", description="Filter list of services by technology <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Parameter(name="theme", in="query", required=false, type="string", description="Filter list of services by theme <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Parameter(name="type", in="query", required=false, type="string", description="Provider Account Type <br/> <b>Values :</b> [Entity or Individual]"), 
   *   @SWG\Parameter(name="subtype", in="query", required=false, type="string", description="Provider Account Subtype <br/> <b>Values :</b> Sub-types in setup data"),
   *   @SWG\Response(response="200", description="List Market place"),
   *   @SWG\Response(response="422", description="Validation Error"),
   * )
   */
  public function listServices($request, $response, $args){
    global $account_types, $account_sub_types;
    
    $parameters = ['pageNumber', 'numberOfRequests', 'lang', 'category', 'technology', 'theme', 'type', 'subtype'];
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
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", implode( ', ', $requiredParams ) ) );
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
      
      $category_id = $technology_id = $theme_id = $type = $subtype = '';

      //get category id
      if( isset( $args[ "category" ] ) ) {
        //check term exists
        $term_taxonomy = $this->check_term_exists($args['category'], 'service_category');
        if (empty($term_taxonomy)) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Category"));
        }
        $category_id = $term_taxonomy->term_id;
      }
      
      //get technology id
      if( isset( $args[ "technology" ] ) ) {
        //check term exists
        $term_taxonomy = $this->check_term_exists($args['technology'], 'technology');
        if (empty($term_taxonomy)) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Technology"));
        }
        $technology_id = $term_taxonomy->term_id;
      }
      
      //get theme id
      if( isset( $args[ "theme" ] ) ) {
        //check term exists
        $term_taxonomy = $this->check_term_exists($args['theme'], 'theme');
        if (empty($term_taxonomy)) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Theme"));
        }
        $theme_id = $term_taxonomy->term_id;
      }
      //get type id
      if( isset( $args[ "type" ] ) ) {
        if ( !in_array( ucfirst( strtolower( $args[ "type" ] ) ), $account_types ) ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Account type"));
        }
        $type = $args[ "type" ];
      }
      
      //get sub type id
      if( isset( $args[ "subtype" ] ) ) {
        if ( !array_key_exists( strtolower( $args[ "subtype" ] ), $account_sub_types ) ) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Account sub type"));
        }
        $subtype = $args[ "subtype" ];
      }
      
      $list_service = $post->getAllServices( 'service', 'publish', $args[ 'numberOfRequests' ], $skip_number, $category_id, $technology_id, $theme_id, $type, $subtype );
      
      $service_result = array();
      
      if (count($list_service) == 0){
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }
      else{
        $total_count = count( $post->getAllServices( 'service', 'publish', -1, -1, $category_id, $technology_id, $theme_id, $type, $subtype ) );
        $service_result = $this->ef_load_data_counts( $total_count, $args[ 'numberOfRequests' ] );
        $index = 0;
        foreach ($list_service as $service) {
          $service_id = $service->ID;

          $post_meta = new Postmeta();
          $meta = $post_meta->getPostMeta( $service_id );
          $service_meta = array();
          foreach ($meta as $meta_key => $meta_value ) {
            $service_meta[ $meta_value[ 'meta_key' ] ] = $meta_value[ 'meta_value' ];
          }
          unset($meta_value);
                      
          
          //get category
          $category = "";
          if( array_key_exists( 'service_category', $service_meta ) ) {
            $term = new Term();
            $categoryObj = $term->getTerm( $service_meta[ 'service_category' ] );
            $category = ( $args["lang"] == "ar" && $categoryObj->name_ar != '' ) ? $categoryObj->name_ar : $categoryObj->name;
          }
          
          //get theme
          $theme = "";
          if( array_key_exists( 'theme', $service_meta ) ) {
            $term = new Term();
            $themeObj = $term->getTerm( $service_meta[ 'theme' ] );
            $theme = ( $args[ "lang" ] == "ar" && $themeObj->name_ar != '' ) ? $themeObj->name_ar : $themeObj->name;
          }
          
          //get interest
          $technologies = array();
          if( array_key_exists( 'technology', $service_meta ) ) {
              $technoglogy_arr = unserialize( $service_meta[ 'technology']  );
              foreach( $technoglogy_arr as $technoglogy_id ) {
                  $term = new Term();
                  array_push( $technologies, $term->getTerm( $technoglogy_id )->name );
              }
          }
          
          //get thumbnail image
          $url = '';
          if( array_key_exists( '_thumbnail_id', $service_meta ) ) {
            $post_type      = "attachment";
            $post_status    = "inherit";
            $attachment_id  = $service_meta[ '_thumbnail_id' ];
            $service_image  = Post::getPostByID( $attachment_id, $post_type, $post_status );
            if( $service_image ) {
              $url = $service_image->guid;
            }

            if($url != '') {
              //return thumbnail size in listing
              $url = $this->ef_image_sizes($url,'340x210');
            }
          }

          $no_of_responses = Thread::where('request_id', '=', $service_id )->where( 'responses_count', '>', 0 )->count();
          
          $offered_by = $this->return_user_info_list( $service->post_author );
          
          global $en_sub_types, $ar_sub_types;
          
          if( $offered_by ) {
            $userMeta = Usermeta::where( 'user_id', '=', $service->post_author )->where( "meta_key", "=", "registration_data" )->first();
            $registeration_data = unserialize( $userMeta->meta_value );
            $registeration_data = ( is_array( $registeration_data ) ) ? $registeration_data : unserialize($registeration_data);
            $offered_by[ 'type' ] = __( $registeration_data['type'], 'egyptfoss', $args[ 'lang' ] ); 
            $offered_by[ 'subtype' ] = ( $args[ 'lang' ] == 'en' ) ? $en_sub_types[ $registeration_data['sub_type'] ]:$ar_sub_types[ $registeration_data[ 'sub_type' ] ];
          } 
          
          $average_rate = array('rate' => 0, 'reviewers_count' => 0);
          $rate_meta = Postmeta::where('post_id', '=', $service_id)->whereIn('meta_key', array('reviewers_count', 'rate'))->get();
          foreach ($rate_meta as $meta) {
            $average_rate[$meta['meta_key']] = $meta['meta_value'];
          }
          
          // top service flag
          $is_top_service = $post_meta->getMetaValue( $service_id, 'efb_is_top_service' );
          $is_top_service = ( $is_top_service == '1' ) ? TRUE:FALSE;
          
          $service_result['data'][$index] = array(
            "service_id"      => $service_id,
            "service_title"   => $service->post_title,
            "description"     => $service->post_content,
            "category"        => html_entity_decode( $category ),
            "technologies"    => html_entity_decode( implode( ',', $technologies) ),
            "theme"           => html_entity_decode( $theme ),
            "thumbnail"       => $url,
            "no_of_requests"  => $no_of_responses,
            "added_by"        => $this->return_user_info_list( $service->post_author, $args[ "lang" ] ),
            "average_rate"    => $this->service_average_rate($service_id),
            "offered_by"      => $offered_by,
            "is_top_service"  => $is_top_service
          );
          
          $index += 1;
        }
        unset($service);
        $result = $this->renderJson($response, 200, $service_result);
      }
      return $result;
    }
  }

  /**
   * @SWG\Post(
   *   path="/market-place/service/{service_id}/review",
   *   tags={"Market Place"},
   *   summary="Add service review",
   *   description="Review service so that I can express my opinion",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token to add review <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="service_id", in="path", required=false, type="string", description="Service ID to review <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="rate", in="formData", required=false, type="string", description="Rate star <br/> <b>Values :</b>[0-5] <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="review", in="formData", required=false, type="string", description="Review Message <br/> <b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Response(response="200", description="Review saved successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function saveReview($request, $response, $args) {
    $params = $request->getHeaders();
    if(!isset($params['HTTP_TOKEN'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    
    $parameters = ['review', 'rate'];
    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $_POST) && !empty($_POST[$parameter])) {
        $args[$parameter] = $_POST[$parameter];
      } else {
        return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
      }
    }

    if(!preg_match('/[أ-يa-zA-Z]+/', $args['review'], $matches)) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "review"));
    } else if (!ctype_digit($args['rate']) || ($args['rate'] <= 0) || ($args['rate'] > 5) ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "rate"));
    }

    $result = "";
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      $service_record = Post::where('ID', '=', $args['service_id'])->where('post_type', '=', 'service')->first();
      if (empty($user)) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      } else if ($service_record == null) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
      } else if ($service_record->post_status != 'publish') {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
      } else if ( ($service_record->post_author == $user_id) ) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notValidUser"));
      } else {
        $service_id = $service_record->ID;
        $review_record = Review::where('rateable_id', '=', $service_id)->where('reviewer_id', '=', $user_id)->first();
        if($review_record != null) {
          $result = $this->renderJson($response, 404, Messages::getErrorMessage("exists", "Your service review"));
        } else {
          $review_record = new Review;
          $data = array(
            'rateable_id' => $service_id,
            'provider_id' => $service_record->post_author,
            'reviewer_id' => $user_id,
            'rate' => $args['rate'],
            'review' => $args['review'],
          );
          $review_record->saveReview($data);
          $review_record->save();
          $review_record->updateAverageRate($service_id);

          $new_rate = $this->service_average_rate($service_id);
          
          $average_rate = $review_record->updateAverageRate($service_id);

          // market place badges management
          $mb_badge = new Badge( $service_record->post_author );
          $mb_badge->efb_manage_mb_badges( $service_id, $average_rate );
          
          $result = $this->renderJson($response, 200, $new_rate);
        }
      }
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }

  /**
   * @SWG\Get(
   *   path="/market-place/service/{service_id}/rate",
   *   tags={"Market Place"},
   *   summary="View service average rate",
   *   description="View service total rate review",
   *   @SWG\Parameter(name="service_id", in="path", required=false, type="string", description="Service ID to view service rate <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="review retrieved successfully"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function viewRate($request, $response, $args) {
    $service_record = Post::where('ID', '=', $args['service_id'])->where('post_type', '=', 'service')->first();
    if ($service_record == null) {
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
    } else if ( !in_array($service_record->post_status, ['publish', 'archive']) ) {
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
    } else {
      $result = $this->renderJson($response, 200, $this->service_average_rate($service_id));
    }
    return $result;
  }

  /**
   * @SWG\Get(
   *   path="/market-place/service/{service_id}/reviews",
   *   tags={"Market Place"},
   *   summary="list service reviews",
   *   description="List service reviews",
   *   @SWG\Parameter(name="service_id", in="path", required=false, type="string", description="Service ID to review <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="review saved successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listReviews($request, $response, $args) {
    $service_record = Post::where('ID', '=', $args['service_id'])->where('post_type', '=', 'service')->first();
    if ($service_record == null) {
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
    } else if ( !in_array($service_record->post_status, ['publish', 'archive']) ) {
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
    } else {
      $review_records = Review::where('rateable_id', '=', $service_record->ID)->orderBy('created_at', 'DESC')->get();
      for ($i = 0; $i < count($review_records); $i++) {
        $review_records[$i]['added_by'] = $this->return_user_info_list($review_records[$i]['reviewer_id']);
      }
      $result = $this->renderJson($response, 200, $review_records);
    }
    return $result;
  }

  /**
   * @SWG\Get(
   *   path="/market-place/service/{service_id}/review/{review_id}",
   *   tags={"Market Place"},
   *   summary="View service review",
   *   description="View service single requester rate and review",
   *   @SWG\Parameter(name="service_id", in="path", required=false, type="string", description="Service ID to review <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="review_id", in="path", required=false, type="string", description="Review ID to retrieve <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="review retrieved successfully"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function viewReview($request, $response, $args) {
    $service_record = Post::where('ID', '=', $args['service_id'])->where('post_type', '=', 'service')->first();
    if ($service_record == null) {
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
    } else if ( !in_array($service_record->post_status, ['publish', 'archive']) ) {
      $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Service"));
    } else {
      $review_record = Review::where('id', '=', $args['review_id'])->where('rateable_id', '=', $service_record->ID)->first();
      if ($review_record == null) {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Review"));
      } else {
        $result = $this->renderJson($response, 200, $review_record);
      }
    }
    return $result;
  }
  
   /**
   * @SWG\Get(
   *   path="/market-place/top-services/",
   *   tags={"Market Place"},
   *   summary="List Top 10 Services by rating",
   *   description="List Top 10 Services according to their ratings",
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List top services successfully"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listTopServices($request, $response, $args) {
    
    $args["lang"] = "";
    if(isset($_GET["lang"]))
    {
      $args["lang"] = $_GET["lang"];
    }
    
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    // get top services
    $list_service = Badge::efb_get_top_services();
    
    if ( empty( $list_service ) ) {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    else{
      $service_result = array();
      foreach ($list_service as $service) {
        $service_id = $service->ID;

        $post_meta = new Postmeta();
        $meta = $post_meta->getPostMeta( $service_id );
        $service_meta = array();
        foreach ($meta as $meta_key => $meta_value ) {
          $service_meta[ $meta_value[ 'meta_key' ] ] = $meta_value[ 'meta_value' ];
        }
        unset($meta_value);

        //get thumbnail image
        $url = '';
        if( array_key_exists( '_thumbnail_id', $service_meta ) ) {
          $post_type      = "attachment";
          $post_status    = "inherit";
          $attachment_id  = $service_meta[ '_thumbnail_id' ];
          $service_image  = Post::getPostByID( $attachment_id, $post_type, $post_status );
          if( $service_image ) {
            $url = $service_image->guid;
          }

          if($url != '') {
            //return thumbnail size in listing
            $url = $this->ef_image_sizes($url,'340x210');
          }
        }
        $no_of_responses = Thread::where('request_id', '=', $service_id)->where('responses_count', '>', 0)->count();
        $service_result[] = array(
          "service_id"      => $service_id,
          "service_title"   => $service->post_title,
          "thumbnail"       => $url,
          "no_of_requests"  => $no_of_responses,
          "added_by"        => $this->return_user_info_list( $service->post_author, $args["lang"] ),
          "average_rate"    => $this->service_average_rate($service_id),
        );
        
        unset($service);
      }
      
      $return = array(
          'is_top'  => Badge::$is_top,
          'data'    => $service_result
      );
      
      return $this->renderJson($response, 200, $return);
    }
  }
  
  /**
   * @SWG\Get(
   *   path="/market-place/top-providers/",
   *   tags={"Market Place"},
   *   summary="List Top 10 Providers",
   *   description="List Top 10 Providers according to their ratings ( get latest if number of top providers less than 4 )",
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List top providers successfully"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listTopProviders($request, $response, $args) {
    
    $args["lang"] = "";
    if(isset($_GET["lang"]))
    {
      $args["lang"] = $_GET["lang"];
    }
    
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    // get top providers
    $list_provider = Badge::efb_get_top_providers();
    
    if ( empty( $list_provider ) ){
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    else{
      $provider_result = array();
      foreach ($list_provider as $provider) {
        $provider_array = array();
        $provider_id = $provider->ID;
        
        $provider_data = $this->return_user_info_list( $provider_id, $args["lang"] );
        $provider_array = array_merge( $provider_array, $provider_data );
        
        $rates_sum = Review::where( 'provider_id', '=', $provider->ID )->sum( 'rate' );
        $reviewers_count = $provider->reviewers_count;
        $average_rate = 0;

        if( $reviewers_count ) {
          $average_rate = (float)$rates_sum / (float)$reviewers_count;
        }
                            
        $provider_array[ 'average_rate' ] = array(
          "rate"            => $average_rate,
          "reviewers_count" => $reviewers_count
        );

        $provider_array[ 'services_count' ] = $provider->services_count;
        
        $provider_result[] = $provider_array;
        
        unset($provider);
      }
      
      $return = array(
          'is_top'  => Badge::$is_top,
          'data'    => $provider_result
      );
      
      return $this->renderJson($response, 200, $return);
    }
  }
}

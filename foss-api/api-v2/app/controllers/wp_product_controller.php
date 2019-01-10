<?php
 class WPProductController extends EgyptFOSSController {

  /**
   * @SWG\Post(
   *   path="/products",
   *   tags={"Product"},
   *   summary="Creates Product",
   *   description="Create a new product with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to create a new product<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Product Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="description", in="formData", required=false, type="string", description="Product Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Product Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="developer", in="formData", required=false, type="string", description="Product Developer"),
   *   @SWG\Parameter(name="functionality", in="formData", required=false, type="string", description="Product Functionality <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="usage_hints", in="formData", required=false, type="string", description="Product Usage Hints <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="references", in="formData", required=false, type="string", description="Product References <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="link_to_source", in="formData", required=false, type="string", description=" Product Link to Source <br/><b>Validations: </b><br/> 1.Valid Link"),
   *   @SWG\Parameter(name="industry", in="formData", required=false, type="string", description="Product Industry <br/><b>Validations: </b><br/> 1. Predefined Industry in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="type", in="formData", required=false, type="string", description="Product Type <br/><b>Validations: </b><br/> 1. Predefined Product Types in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="technology", in="formData", required=false, type="string", description="Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="platform", in="formData", required=false, type="string", description="Platforms <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="license", in="formData", required=false, type="string", description="Licenses <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Interests <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="logo", in="formData", required=false, type="file", description="Product Logo <br/><b>Validations: </b><br/> 1.Valid Image"),
   *   @SWG\Parameter(name="screenshots", in="formData", required=true, type="file", description="Product Screenshots <br/><b>Validations: </b><br/> 1.Valid Image"),
   *   @SWG\Response(response="200", description="Product added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addProduct($request, $response, $args) {
    $parameters = [ 'title', 'description', 'lang', 'developer',
        'functionality', 'usage_hints', 'references', 'link_to_source', 'industry',
        'type', 'technology', 'platform', 'license',
        'interest','logo', 'screenshots'];
      
    $required_params = [ 'title', 'description', 'lang','industry'];

    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $_POST))
      {
        $args[$parameter] = $_POST[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
        }
        else{
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }

    $value = split(",",$args["industry"]);
    if (count($value) > 1) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","industry"));
    }

    $parametersToCheck = array_diff($parameters, array( "lang", "link_to_source", "industry", "type", "platform", "license","screenshots"));
    $multiValue = array("platform", "license", "type");
    foreach ($parametersToCheck as $param){
      $values = $args[$param];
      if(in_array($value, $multiValue)){
        $values = split(",", $args[$param]);
      }
      if(count($values) > 1){
        foreach($values as $value){
          if(!preg_match('/[أ-يa-zA-Z]+/', $value, $matches)){
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", $param." must at least contain one letter"));
          }
        }
      }else
      {
        if(!preg_match('/[أ-يa-zA-Z]+/', $values, $matches) && !empty($values)){
             return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", $param." must at least contain one letter"));
        }
      }
    }
    if(!isset($_POST["lang"]) || ($_POST["lang"] != "en" && $_POST["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    $params = $request->getHeaders();
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    //$loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    $user_id = $loggedin_user->user_id;
    $user = User::find($user_id);
    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    if (!empty(Post::where('post_title' , '=', $args['title'])->where('post_type' , '=', 'product')->first())) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "Product title already exists"));
    }
    if (!empty($args["link_to_source"]) && filter_var($args["link_to_source"], FILTER_VALIDATE_URL) === FALSE) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","url"));
    }
    
    if(isset($_FILES["logo"]) && !empty($_FILES["logo"]['tmp_name']) && exif_imagetype($_FILES["logo"]['tmp_name']) == FALSE ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","logo"));
    }
   
    if(sizeof($_FILES["screenshots"]['tmp_name'] ) >= 1)
    {
      $files = $_FILES["screenshots"];    
      foreach ($files['name'] as $key => $value) 
      {
        if (($files['name'][$key] != "")) 
        {
          $size = $files['size'][$key];
          $array = explode('.', $files['name'][$key]);
          $extension = end($array);
          $type = strtolower($extension);

          if(exif_imagetype($files['tmp_name'][$key]) == FALSE)
          {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong",'screenshots'));
          }
        }
      }
    }
    
    $args['lang'] = $_POST['lang'];
    $args['user_id'] = $user_id;
    $is_first_suggestion = $this->is_first_suggestion($args['user_id']);
    $new_product = new Post();
    $returnPOstID = $new_product->addProduct($args);
    if($returnPOstID == -1) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","terms"));
    }

    //add logo
    if(isset($_FILES["logo"]) && !empty($_FILES["logo"]['tmp_name'])) {
      $img_args = array("post_status" => "inherit",
        "post_type" => "attachment",
        "post_author" => $args['user_id']
      );
      $new_product->updateFeaturedImage($img_args, $returnPOstID, $_FILES['logo']);   
    }
    
    //Upload Screenshots
    $resources_args = array("post_status" => "inherit",
       "post_type" => "attachment",
       "post_author" => $args['user_id']
    );

    $screnshot_ids = $new_product->uploadProductScreenshots($resources_args, $returnPOstID, $_FILES);   
    $Postmeta = new Postmeta();
    $Postmeta->updatePostMeta($returnPOstID, "fg_perm_metadata", trim($screnshot_ids,","));
    $Postmeta->updatePostMeta($returnPOstID, "fg_temp_metadata", trim($screnshot_ids,","));
   
    $returnMsg = Messages::getSuccessMessage("Success","Product Added");
    $returnMsg['product_id'] = $returnPOstID;
    $returnMsg['is_first_suggestion'] = $is_first_suggestion;
    
    $is_published = Post::getPostsBy( array('post_id'  => $returnPOstID, 'post_type'  => 'product','post_status'  => 'publish') )->first();
    
    $returnMsg['is_pending_review'] = empty($is_published);
    
    return $this->renderJson($response, 200, $returnMsg);
  }

  /**
   * @SWG\GET(
   *   path="/products/{product_id}",
   *   tags={"Product"},
   *   summary="Finds Product",
   *   description="Get product data according to the passed product name",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to display pending product if exists"),
   *   @SWG\Parameter(name="product_id", in="path", required=false, type="string", description="Product ID to retrieve its details <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View product info"),
   *   @SWG\Response(response="404", description="Product not found")
   * )
   */
  public function viewProduct($request, $response, $args) {
    $product_name = new Post();
    if(!isset($args["product_id"]) || $args["product_id"] == "" || $args["product_id"] == "{product_id}") {
      return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue"));
    }
    
    // validate lang exists
    if(!isset($_GET["lang"]) || ($_GET["lang"] != "en" && $_GET["lang"] != "ar")) {
      return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue"));
    }
    $args["lang"] = $_GET["lang"];
    
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
    
    $product = $product_name->getPost($args['product_id']);
    if ($product['post_status'] == 'publish' || $product['post_status'] == 'pending' ){
      
      if($user != null && !empty($user))
      {
        if($product['post_status'] == 'pending' && $product['post_author'] != $user->ID)
        {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Product ID"));
        }
      }else
      {
        if($product['post_status'] == 'pending')
        {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Product ID"));
        }
      }
      
      $product_id = $product['ID'];
      $data = new Postmeta();
      $product_meta = $data->getProductMeta($product_id);

      $product_data['title'] = $product['post_title'];
      $product_data['description'] = array_key_exists('description', $product_meta) ? $product_meta['description']  : "";
      $product_data['post_status'] = $product['post_status'];
      $product_data['post_url'] = html_entity_decode($product['guid']);
      $product_data['product_logo'] = array_key_exists('_thumbnail_id', $product_meta) ? $product_meta['_thumbnail_id']  : "";
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
      $product_data['product_logo'] =  $logo;

      if (array_key_exists('fg_perm_metadata', $product_meta) && !empty($product_meta['fg_perm_metadata'])){
        $product_screenshots = explode(',', $product_meta['fg_perm_metadata']) ;
        $screenshot_name = array();
        foreach ($product_screenshots as $screenshot_id) {
          $screenshots = $product_name->getPostLogo($screenshot_id);
          $screenshot_name = array_merge($screenshot_name, array($screenshots['guid']));
        }
        $product_data['product_screenshots'] = $screenshot_name;
      }
      else{ $product_data['product_screenshots'] = array(); }

      $product_data['developer'] = array_key_exists('developer', $product_meta) ? $product_meta['developer']  : "";
      $product_data['functionality'] = array_key_exists('functionality', $product_meta) ? $product_meta['functionality']  : "";
      $product_data['usage_hints'] = array_key_exists('usage_hints', $product_meta) ? $product_meta['usage_hints']  : "";
      $product_data['references'] = array_key_exists('references', $product_meta) ? $product_meta['references']  : "";
      $product_data['link_to_source'] = array_key_exists('link_to_source', $product_meta) ? $product_meta['link_to_source']  : "";
      $product_data['is_featured'] = array_key_exists('is_featured', $product_meta) ? $product_meta['is_featured']  : "0";
      
      //top ten product
      $is_topten = 0;
      $topten = TopTenProduct::where('post_id','=', $product_id)->first();
      if($topten)
      {
        $is_topten = 1;
      }else {
        //check if post related product is topten
        $productTrans = new Post();
        $lang = $args["lang"];
        $translated_product_id = $productTrans->getPostTranslationId($product_id,$lang);   
        if($translated_product_id)
        {
          $topten = TopTenProduct::where('post_id','=', $translated_product_id)->first();
          if($topten)
          {
            $is_topten = 1;
          }
        }
      }
      $product_data['is_topten'] = "$is_topten";
      
      // ---- get terms ---- //
      $term = new Term();
      $industry_name = $term->getTerm($product_meta['industry']);
      
      // get product category
      $product_data['category'] = ( ($args["lang"] == "ar" && !empty($industry_name['name_ar'])?html_entity_decode($industry_name['name_ar']):html_entity_decode($industry_name['name'])) );
      
      // get product category term taxonomy record to check if it's sub-category
      $industry_obj = TermTaxonomy::getTermTaxonomy( $product_meta['industry'], 'industry' );
      
      if( !empty( $industry_obj['parent'] ) ) {
        // get product parent category obj
        $parent_cat = $term->getTerm($industry_obj['parent']);
        
        $product_data['sub_category'] = $product_data['category'];
        $product_data['category'] = ( ($args["lang"] == "ar" && !empty($parent_cat['name_ar'])?html_entity_decode($parent_cat['name_ar']):html_entity_decode($parent_cat['name'])) );
      }

      if (array_key_exists('type', $product_meta) && !empty($product_meta['type'])){
        $type_ids = $product_meta['type'] ;
        $type_name = array();
        foreach ($type_ids as $type_id) {
          $type= $term->getTerm($type_id);
          $ty_name = ( ($args["lang"] == "ar" && !empty($type['name_ar'])?html_entity_decode($type['name_ar']):html_entity_decode($type['name'])) );
          $type_name = array_merge($type_name, array($ty_name));
        }
        //$product_data['platform'] = rtrim(implode(',', $platform_name));
        $product_data['type'] =  $type_name;
      }
      else{ $product_data['platform'] = ''; }
      
      if (array_key_exists('technology', $product_meta) && !empty($product_meta['technology'])){
        $technology_ids = $product_meta['technology'] ;
        $technology_name = array();
        foreach ($technology_ids as $technology_id) {
          $technology= $term->getTerm($technology_id);
          $technology_name = array_merge($technology_name, array($technology['name']));
        }
        //$product_data['technology'] = rtrim(implode(',', $technology_name));
        $product_data['technology'] = $technology_name;
      }
      else{ $product_data['technology'] = ''; }

      if (array_key_exists('platform', $product_meta) && !empty($product_meta['platform'])){
        $platform_ids = $product_meta['platform'] ;
        $platform_name = array();
        foreach ($platform_ids as $platform_id) {
          $platform= $term->getTerm($platform_id);
          $plat_name = ( ($args["lang"] == "ar" && !empty($platform['name_ar'])?html_entity_decode($platform['name_ar']):html_entity_decode($platform['name'])) );
          $platform_name = array_merge($platform_name, array($plat_name));
        }
        //$product_data['platform'] = rtrim(implode(',', $platform_name));
        $product_data['platform'] =  $platform_name;
      }
      else{ $product_data['platform'] = ''; }

      if (array_key_exists('license', $product_meta) && !empty($product_meta['license'])){
        $license_ids = $product_meta['license'] ;
        $license_name = array();
        foreach ($license_ids as $license_id) {
          $license = $term->getTerm($license_id);
          $lic_name = ( ($args["lang"] == "ar" && !empty($license['name_ar'])?html_entity_decode($license['name_ar']):html_entity_decode($license['name'])) );
          $license_name = array_merge($license_name, array($lic_name));
        }
        //$product_data['license'] = rtrim(implode(',', ), ',');
        $product_data['license'] = $license_name;
      }
      else{
          $product_data['license'] = '';
      }
      
      if (array_key_exists('interest', $product_meta) && !empty($product_meta['interest'])){
        $interests_ids = $product_meta['interest'] ;
        $keyword_name = array();
        foreach ($interests_ids as $keyword_id) {
          $keyword= $term->getTerm($keyword_id);
          $keyword_name = array_merge($keyword_name, array($keyword['name']));
        }
        //$product_data['interest'] = rtrim(implode(',', $keyword_name), ',');
        $product_data['interest'] = $keyword_name;
      }
      else{
          $product_data['interest'] = '';
      }
      
      // check if user can edit
      if($user != null && !empty($user))
      {
        $canEditUser = new User();
        $product_data["can_edit"] = $canEditUser->userCanEditProduct($user->ID,$product_id);
      }
      
      $product_data['added_by'] =  $this->return_user_info_list($product['post_author']);
      //language
      if(array_key_exists('language', $product_meta))
      {
        $lang = $product_meta['language'];
        $lang_arr = unserialize(unserialize($lang));
        $product_data['language'] = $lang_arr['slug'];
        if($product_data['language'] == null)
        {
          $product_data['language'] = "en";
        }
      }
      $result = $this->renderJson($response, 200, $product_data);
    }
    else{
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Product ID"));
    }
    return $result;
  }
    
  /**
   * @SWG\Put(
   *   path="/products/{product_id}",
   *   tags={"Product"},
   *   summary="Edits Product",
   *   description="Logged in user can edit product",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to edit a product<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="product_id", in="path", required=false, type="string", description="product ID returned from list products<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="title", in="formData", required=false, type="string", description="Product Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="description", in="formData", required=false, type="string", description="Product Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="developer", in="formData", required=false, type="string", description="Product Developer"),
   *   @SWG\Parameter(name="functionality", in="formData", required=false, type="string", description="Product Functionality <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="usage_hints", in="formData", required=false, type="string", description="Product Usage Hints <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="references", in="formData", required=false, type="string", description="Product References <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="link_to_source", in="formData", required=false, type="string", description="Product Link to Source <br/><b>Validations: </b><br/> 1. Contains at least 1 character"),
   *   @SWG\Parameter(name="type", in="formData", required=false, type="string", description="Product Type <br/><b>Validations: </b><br/> 1. Predefined Product Types in System <br/> <b>Values: </b> Multiple values with comma seperated between each value <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="technology", in="formData", required=false, type="string", description="Technologies <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="platform", in="formData", required=false, type="string", description="Platforms <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="license", in="formData", required=false, type="string", description="Licenses <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Interests <br/><b>Values: </b> Multiple values with comma seperated between each value"),
   *   @SWG\Parameter(name="logo", in="formData", required=false, type="file", description="Product Logo <br/><b>Validations: </b><br/> 1.Valid Image"),
   *   @SWG\Parameter(name="screenshots", in="formData", required=true, type="file", description="Product Screenshots <br/><b>Validations: </b><br/> 1.Valid Image"),
   *   @SWG\Response(response="200", description="Product Edited Successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function editProduct($request, $response, $args) {
    $put = $request->getParsedBody();   
    $parameters = ['title', 'description', 'developer',
      'functionality', 'usage_hints', 'references', 'link_to_source',
      'type', 'technology', 'platform', 'license',
      'interest','logo'];
    
    $required_params = ['product_id','title','description'];
    $product_history_data = array();

    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $put) && !empty($put[$parameter]))
      {
        $args[$parameter] = $put[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
        }
        else{
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    
    $parametersToCheck = array_diff($parameters, array( "lang", "link_to_source", "type", "platform", "license","screenshots"));
    $multiValue = array("platform", "license", "type");
    foreach ($parametersToCheck as $param)
    {      
      $values = $args[$param];
      if(in_array($value, $multiValue)){
        $values = split(",", $args[$param]);
      }
      if(count($values) > 1)
      {
        foreach($values as $value)
        {
          if(!preg_match('/[أ-يa-zA-Z]+/', $value, $matches))
          {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", $param." must at least contain one letter"));
          }
        }
      }else
      {
        if(!preg_match('/[أ-يa-zA-Z]+/', $values, $matches) && !empty($values))
          {
             return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", $param." must at least contain one letter"));
          }
      }
    }
    
    $params = $request->getHeaders();
    $loggedin_user = isset($params['HTTP_TOKEN']) ? (AccessToken::where('access_token', '=', $params['HTTP_TOKEN'])->first()) : null;
    //$loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    } else{ 
      $user_can_edit = User::userCanEditProduct($loggedin_user->user_id, $args["product_id"]);
      if( !$this->user_can($loggedin_user->user_id, 'perform_direct_ef_actions') || !$user_can_edit ) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized")); 
      }
    }
    
    if (!empty($args["link_to_source"]) && filter_var($args["link_to_source"], FILTER_VALIDATE_URL) === FALSE) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","url"));
    }
    
    if(!is_numeric($args['product_id'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","product id"));
    }
    
    if(isset($_FILES["logo"]) && !empty($_FILES["logo"]['tmp_name']) && exif_imagetype($_FILES["logo"]['tmp_name']) == FALSE ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","logo"));
    }
    
    if(sizeof($_FILES["screenshots"]['tmp_name'] ) >= 1)
    {
      $files = $_FILES["screenshots"];    
      foreach ($files['name'] as $key => $value) 
      {
        if (($files['name'][$key] != "")) 
        {
          $size = $files['size'][$key];
          $array = explode('.', $files['name'][$key]);
          $extension = end($array);
          $type = strtolower($extension);

          if(exif_imagetype($files['tmp_name'][$key]) == FALSE)
          {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong",'screenshots'));
          }
        }
      }
    }
    
    $productExists = Post::getPostsBy(array("post_type"=>"product","post_id"=>$args["product_id"]));
    //check if post is pending and edited by owner
    $productStatus = $productExists->get()->first()->post_status;
    if($productStatus != "publish"){
      if($loggedin_user->user_id != $productExists->get()->first()->post_author)
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("notFound","product"));
      }
    }
    
    if(empty($productExists->get()->first())) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("notFound","product"));
    }
    
    $results = Post::checkPostTitleExists($args['title'],$args['product_id']);
    if ($results) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("exists", "product title"));
    }
    
    $termTax = new TermTaxonomy();
    
    //split multiple items
    $multiItems = array('license', 'technology', 'platform', 'interest', 'type');
    foreach($multiItems as $item)
    {
      if(isset($args[$item]) && !empty($args[$item]))
      {
        $terms_data[$item] = split(",",$args[$item]);
      }
    }
    
    global $ef_product_multi_uncreated_tax;
    $isCreated =  $termTax->saveTermTaxonomies($terms_data, $ef_product_multi_uncreated_tax); 
    
    if(!$isCreated)
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","terms"));
    }
    
    $productExists->update(array("post_title"=>$args['title']));
    $product_history_data = array_merge($product_history_data,
      array("post_title"=>$args['title'],
            "user_id"=>$loggedin_user->user_id,
            "post_id"=>$args['product_id'],
            "updated_at"=>date('YmdHis')));
    
    $metaProductData = [ 'description', 'developer',
      'functionality', 'usage_hints', 'references', 'link_to_source'];
    foreach($metaProductData as $meta){

      $productMeta = new Postmeta();
      $productMeta->updatePostMeta($args["product_id"], $meta, $args[$meta]);
      $product_history_data = array_merge($product_history_data,array($meta=>$args[$meta]));
    
    }
    
    $product = new Post();
    //upload logo if changed
    if(isset($_FILES["logo"]) && !empty($_FILES["logo"]['tmp_name'])) {
      $img_args = array("post_status" => "inherit",
        "post_type" => "attachment",
        "post_author" => $loggedin_user->user_id
      );
      $product->updateFeaturedImage($img_args, $args['product_id'], $_FILES['logo']);   
    }
    
    if( sizeof($_FILES["screenshots"]['tmp_name'] ) ) {
      //Upload Screenshots
      $resources_args = array("post_status" => "inherit",
         "post_type" => "attachment",
         "post_author" => $loggedin_user->user_id
      );
    
      $screnshot_ids = $product->uploadProductScreenshots($resources_args,  $args['product_id'], $_FILES);   
      $Postmeta = new Postmeta();
      $Postmeta->updatePostMeta($args['product_id'], "fg_perm_metadata", trim($screnshot_ids,","));
      $Postmeta->updatePostMeta($args['product_id'], "fg_temp_metadata", trim($screnshot_ids,","));
    }
   
    
    $terms_history_data = $product->updatePostTerms($args["product_id"], $terms_data, false, array(), false, true);

    $product_history_data = array_merge($product_history_data,$terms_history_data);
    
    foreach ($product_history_data as $key=>$column)
    {
      if(is_array($product_history_data[$key]))
      {
        $product_history_data[$key]=  serialize($column);
      }
    }
    
    $postHistory = new PostHistory();
    // unset interest_txt and ids and rename to keyword_ids
    $product_history_data["keywords_ids"] = $product_history_data["interest_ids"];
    $product_history_data["keywords_text"] = $product_history_data["interest_text"];
    unset($product_history_data["interest_ids"]);
    unset($product_history_data["interest_text"]);
    $postHistory->addPostHistory($product_history_data);
    $postHistory->save();
    
    // save edited document to marmotta
    $search = new SearchController();
//    $search->save_post_to_marmotta( $args['product_id'], $args['title'], $args['description'], 'product');
    
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","Product Updated"));
  }

  /**
   * @SWG\Get(
   *   path="/products",
   *   tags={"Product"},
   *   summary="Finds All Products",
   *   description="Return all products in the system paginated",
   *   consumes={"application/x-www-form-urlencoded"},
   *   @SWG\Parameter(name="page_no", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="products_count", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="industry", in="query", required=false, type="string", description="Filter list of products by industry <br/> <b>Values: </b> English or Arabic Name or word (featured) for editor's choice"), 
   *   @SWG\Parameter(name="license", in="query", required=false, type="string", description="Filter list of products by license <br/> <b>Values: </b> English Name"),
   *   @SWG\Parameter(name="platform", in="query", required=false, type="string", description="Filter list of products by platform <br/> <b>Values: </b> English Name"),
   *   @SWG\Parameter(name="technology", in="query", required=false, type="string", description="Filter list of products by technology <br/> <b>Values: </b> English Name"),
   *   @SWG\Parameter(name="type", in="query", required=false, type="string", description="Filter list of products by type <br/> <b>Values: </b> English or Arabic Name"),
   *   @SWG\Response(response="200", description="listing product form"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listingProduct($request, $response, $args) {
    global $ef_product_filtered_taxs;
    $parameters = ['page_no', 'products_count','lang'];
    $parameters = array_merge($parameters,$ef_product_filtered_taxs);
    $requiredParams = ['page_no', 'products_count','lang'];
    $numeric_params = ['page_no', 'products_count'];
    foreach ($_GET as $key => $value) {
      if (in_array($key, $parameters)) {  
        if(! is_numeric($value) && in_array($key, $numeric_params)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", $key));
        }
        $args[$key] = $value;
        $requiredParams = array_diff($requiredParams,[$key]);
      }
    }
    if(!empty($requiredParams))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    if($args["lang"] != "en" && $args["lang"] != "ar")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","slug"));
    }
    if($args['products_count'] < 1 || $args['products_count'] > 25)
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'products no',array("range"=> "1 and 25 ")));
    }
    
    foreach ($ef_product_filtered_taxs as $tax) {
        if(isset($args[$tax]) && $args[$tax] != "featured")
        {
            //check category exists
            $term_taxonomy = $this->check_term_exists($args[$tax], $tax);
            if (empty($term_taxonomy)) {
                return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", $tax));
            }
        }
    }
    
    $posts = new Post();
    $post_params = array(
      "post_type" => "product",
      "post_status" => "publish",
      "page_no" => $args['page_no'],
      "no_of_posts" => $args['products_count'],
      "lang" => $args['lang']
      );
    $args = array_merge($post_params,$args);
    $products = new Product();
    $results = $products->getProductsByFilters($args, $ef_product_filtered_taxs);
    //$results = $posts->getPro($args,$ef_product_filtered_taxs);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    $post_params_count = array(
      "post_type" => "product",
      "post_status" => "publish",
      "page_no" => -1,
      "no_of_posts" => -1,
      "lang" => $args['lang']
    );
    $count_args = array_merge($args,$post_params_count);
    $total_count = count($products->getProductsByFilters($count_args,$ef_product_filtered_taxs));
    $List = $this->ef_load_data_counts($total_count, $args['products_count']);
    for($i = 0; $i < sizeof($results); $i++){
      
      //load logo
      $product_data = array();
      $data = new Postmeta();
      $product_meta = $data->getProductMeta($results[$i]->ID);
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
      
      //get category
      $category = "";
      $sub_category = "";
      $category_id = -1;
      if(array_key_exists('industry', $product_meta))
      {
          $term = new Term();
          $category_id = $product_meta['industry'];
          if($category_id != '')
          {
            if($args["lang"] == "ar")
            {
                if($term->getTerm($category_id)->name_ar != '')
                    $category = $term->getTerm($category_id)->name_ar;
                else
                    $category = $term->getTerm($category_id)->name;
            }
            else
                $category = $term->getTerm($category_id)->name;
            
            // get product category term taxonomy record to check if it's sub-category
            $industry_obj = TermTaxonomy::getTermTaxonomy( $category_id, 'industry' );

            if( !empty( $industry_obj['parent'] ) ) {
              // get product parent category obj
              $parent_cat = $term->getTerm($industry_obj['parent']);

              $sub_category = $category;
              $category = ( ($args["lang"] == "ar" && !empty($parent_cat['name_ar'])?html_entity_decode($parent_cat['name_ar']):html_entity_decode($parent_cat['name'])) );
            }
          }
      }

        //get technology
        $technology = "";
        if(array_key_exists('technology', $product_meta))
        {
            $technology_names = array();
            $term = new Term();
            $technology_ids = $product_meta['technology'];
            if($technology_ids != '')
            {
                foreach ( $technology_ids as $technology_id ) {
                    if($args["lang"] == "ar")
                    {
                        if($term->getTerm($technology_id)->name_ar != '')
                            $technology_names[] = $term->getTerm($technology_id)->name_ar;
                        else
                            $technology_names[] = $term->getTerm($technology_id)->name;
                    }
                    else
                        $technology_names[] = $term->getTerm($technology_id)->name;
                }
                
                $technology = implode( ', ' , $technology_names);
            }
        }
      
        //get license
        $license = "";
        if(array_key_exists('license', $product_meta))
        {
            $license_names = array();
            $term = new Term();
            $license_ids = $product_meta['license'];
            if($license_ids != '')
            {
                foreach ( $license_ids as $license_id ) {
                    if($args["lang"] == "ar")
                    {
                        if($term->getTerm($license_id)->name_ar != '')
                            $license_names[] = $term->getTerm($license_id)->name_ar;
                        else
                            $license_names[] = $term->getTerm($license_id)->name;
                    }
                    else
                        $license_names[] = $term->getTerm($license_id)->name;
                }
                
                $license = implode( ', ' , $license_names);
            }
        }
      
      //get platform
      $platform = "";
      if(array_key_exists('platform', $product_meta))
      {
            $platform_names = array();
            $term = new Term();
            $platform_ids = $product_meta['platform'];
            if($platform_ids != '')
            {
                foreach( $platform_ids as $platform_id ) {
                    if($args["lang"] == "ar")
                    {
                        if($term->getTerm($platform_id)->name_ar != '')
                            $platform_names[] = $term->getTerm($platform_id)->name_ar;
                        else
                            $platform_names[] = $term->getTerm($platform_id)->name;
                    }
                    else
                        $platform_names[] = $term->getTerm($platform_id)->name;
                }
            }
          
          $platform = implode(', ', $platform_names);
      }
      
      //get type
      $type = "";
      if(array_key_exists('type', $product_meta))
      {
            $type_names = array();
            $term = new Term();
            $type_ids = $product_meta['type'];
            if($type_ids != '')
            {
                foreach( $type_ids as $type_id ) {
                    if($args["lang"] == "ar")
                    {
                        if($term->getTerm($type_id)->name_ar != '')
                            $type_names[] = $term->getTerm($type_id)->name_ar;
                        else
                            $type_names[] = $term->getTerm($type_id)->name;
                    }
                    else
                        $type_names[] = $term->getTerm($type_id)->name;
                }
            }
          
          $type = implode(', ', $type_names);
      }
      
      $is_featured = array_key_exists('is_featured', $product_meta) ? $product_meta['is_featured']  : "0";
      
      //top ten product
      $is_topten = 0;
      $topten = TopTenProduct::where('post_id','=', $results[$i]->ID)->first();
      if($topten)
      {
        $is_topten = 1;
      }else {
        //check if post related product is topten
        $productTrans = new Post();
        $lang = $args["lang"];
        $product_id = $productTrans->getPostTranslationId($results[$i]->ID,$lang);   
        if($product_id)
        {
          $topten = TopTenProduct::where('post_id','=', $product_id)->first();
          if($topten)
          {
            $is_topten = 1;
          }
        }
      }
      
      $List['data'][$i] = array(
          'product_id' => $results[$i]->ID,
          'product_title' => $results[$i]->post_title,
          'product_status' => $results[$i]->post_status,
          'product_post_date' => $results[$i]->post_date,
          'product_category' => html_entity_decode($category),
          'product_sub_category' => html_entity_decode($sub_category),
          'product_technology' => html_entity_decode($technology),
          'product_license' => html_entity_decode($license),
          'product_platform' => html_entity_decode($platform),
          'product_type' => html_entity_decode($type),
          'product_logo' => $logo,
          'is_featured' => $is_featured,
          'is_topten' => $is_topten,
          'added_by' => $this->return_user_info_list($results[$i]->post_author)
      );
    }
    
    
    return $this->renderJson($response,200,$List);
  }
  
  /**
   * @SWG\Get(
   *   path="/products/topTen",
   *   tags={"Product"},
   *   summary="Finds Top Ten Products",
   *   description="Return all Top Ten products in the system paginated",
   *   consumes={"application/x-www-form-urlencoded"},
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number"),
   *   @SWG\Parameter(name="numberOfCategories", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25"),
   *   @SWG\Parameter(name="randomized", in="query", required=false, type="integer", description="Randomization Option <br/> <b>Value:</b> 1 to randomize the result"),
   *   @SWG\Response(response="200", description="listing top ten products"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function listingTopTenProducts($request, $response, $args) {
    $parameters = ['pageNumber', 'numberOfCategories','lang','randomized'];
    $requiredParams = ['lang'];
    $numeric_params = ['pageNumber', 'numberOfCategories'];
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
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue",$requiredParams));
    }
    $isPaginated = false;
    if(!empty($args['numberOfCategories']) || !empty($args['pageNumber']))
    {
      if(empty($args['numberOfCategories'] ) || $args['numberOfCategories'] < 1 || $args['numberOfCategories'] > 25){
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of categories',array("range"=> "1 and 25 ")));
      }

      else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
      } else if (!empty($args['randomized'] )){
        return $this->renderJson($response, 422, Messages::getErrorMessage("randomizeCollision", "randomized"));
      }
     $isPaginated = true;   
    }
    if (!empty($args['randomized']) && $args['randomized'] != "1" && !$isPaginated){
        return  $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "randomized"));
    }
    if($args["lang"] != "en" && $args["lang"] != "ar")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }

    $topTenProducts = new TopTenProduct();
    if($isPaginated)
    {
      $skip_number = ($args['pageNumber'] * $args['numberOfCategories']) - $args['numberOfCategories'] ;
      if ($args['pageNumber'] == 1){
        $skip_number = 0 ;
      }
    $results = $topTenProducts->getTopTenProducts($skip_number,$args['numberOfCategories'],null)->get();  
    }  else {
      $args["randomized"] = ($args["randomized"] == "1")?1:-1;
      $results = $topTenProducts->getTopTenProducts(null,null,$args["randomized"])->get();  
    }
    //$results = $posts->getPro($args,$ef_product_filtered_taxs);
    if(sizeof($results) == 0)
    {
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }
    
    /*$total_count = count(TermTaxonomy::where("taxonomy","=","industry")->get());
    $List = $this->ef_load_data_counts($total_count, $args['noOfCategories']);*/
    $List["data"] = array();
    $option = new Option();
    $host = $option->getOptionValueByKey('siteurl');
    
    for($i = 0; $i < sizeof($results); $i++){
      $product_id = -1;
      if($args["lang"] == "ar")
      {
        $productTrans = new Post();
        $lang = $args["lang"];
        $product_id = $productTrans->getPostTranslationId($results[$i]->ID,$lang);   
        if($product_id)
        {
          $results[$i]->ID = $product_id;
          $results[$i]->post_title  = Post::where("ID","=",$product_id)->first()->post_title;
        }else
        {
          continue;
        }
      }
      
      if($results[$i]->post_image_url == null)
      {
        
        $results[$i]->post_image_url = $host.'/wp-content/themes/egyptfoss/img/no-product-icon.png';
      }else
      {
        //return small size
        $results[$i]->post_image_url = $this->ef_image_sizes($results[$i]->post_image_url,'64x64');
      }
      $termName = ($args["lang"] == "en")?$results[$i]->name:$results[$i]->name_ar;
      $termName = html_entity_decode($termName);
      if(!isset($List['data'][$termName]))
      {
      $List['data'][$termName]['cat_id'] = $results[$i]->term_id;
      }
      
      $data = new Postmeta();
      $product_meta = $data->getProductMeta($results[$i]->ID);
      $is_featured = array_key_exists('is_featured', $product_meta) ? $product_meta['is_featured']  : "0";
      
      $List['data'][$termName]["products"][] =  array(
          'product_id' => $results[$i]->ID,
          'product_title' => $results[$i]->post_title,
          'product_logo' => $results[$i]->post_image_url,
          'is_featured' => $is_featured);
    }
    return $this->renderJson($response,200,$List);
  }
    
  /**
   * @SWG\Post(
   *   path="/products/{productId}/comments",
   *   tags={"Product"},
   *   summary="Creates Product Comment",
   *   description=" Add product commnent to the product with the passed Id",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new comment<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="productId", in="formData", required=false, type="integer", description="Product ID to post the comment on <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Comment added successfully"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Product not found")
   * )
   */
  public function addProductComments($request, $response, $args) {
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
        if(isset($_POST['productId']) || !empty($_POST['productId']) ){
          if (is_numeric($_POST['productId'])) {
            // we need to check if this post_name is in db or not
            $post = new Post();
            $post_type = "product";
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['productId'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['productId'] = $_POST['productId'];
              $meta['postID'] = $post_exists['ID'];
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Product ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Product ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Product ID"));
        }
        // If no errors so far
        if ($result == "") {
          $comment = new Comment();
          $comment->addComment($user->ID, $user->user_login, $user->user_email, $meta['postID'], $meta['comment']);
          $comment->save();
          if ($result == "") {
            $output =  Messages::getSuccessMessage("Success","Comment added");
            $output['comment_id'] = $comment->comment_ID;
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
   * @SWG\Post(
   *   path="/products/{productId}/comments/{commentId}/replies",
   *   tags={"Product"},
   *   summary="Creates Reply On Product Comment",
   *   description="Add reply on the passed comment on the passed product",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new reply<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="productId", in="formData", required=false, type="integer", description="Product ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="commentId", in="formData", required=false, type="integer", description="Comment ID to post the reply on <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Add reply to Comment")
   * )
   */
  public function addReplyToComment($request, $response, $args) {
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
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment Reply"));
        }

        if(isset($_POST['productId']) || !empty($_POST['productId']) ){
          if (is_numeric($_POST['productId'])) {
            // we need to check if this post id is in db or not
            $post = new Post();
            $post_type = "product";
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['productId'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['productId'] = $_POST['productId'];
              $meta['postID'] = $post_exists['ID'];
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Product ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Product ID")); }
        }
        else{
          return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Product ID"));
        }
        
        if(isset($_POST['commentId']) || !empty($_POST['commentId']) ){
          if (is_numeric($_POST['commentId'])) {
            // we need to check if this comment id is in comments table or not
            $comment_id = new Comment();
            $comment_exists = $comment_id->getCommentByID($_POST['commentId']);
            if (!empty($comment_exists) && ( $meta['productId'] == $comment_exists['comment_post_ID']) ) {
              if( empty( $comment_exists->comment_parent ) ) {
                    $meta['commentId'] = $_POST['commentId'];
              }
              else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notAllowed", "Multi level of comments")); }
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Comment ID is not in this product or")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Comment ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment ID"));
        }
        // If no errors so far
        if ($result == "") {
          $comment = new Comment();
          $comment->addReplyToComment($user->ID, $user->user_login, $user->user_email, $meta['postID'], $meta['commentId'], $meta['comment'], $meta['productId']);
          $comment->save();
          if ($result == "") {
            $output =  Messages::getSuccessMessage("Success","Reply to Comment added");
            $output['comment_id'] = $comment->comment_ID;
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
   *   path="/products/{productId}/comments",
   *   tags={"Product"},
   *   summary="List Comments On Product Comment",
   *   description="list comments on the passed product",
   *   @SWG\Parameter(name="productId", in="path", required=false, type="integer", description="Product ID to list the comments related to this ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfComments", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List comments and its replies to Product"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Comments not found")
   * )
   */
  public function listProductComments($request, $response, $args) {
        $productComments = new Comment();
        if(!isset($args["productId"]) || $args["productId"] == "" || $args["productId"] == "{productId}") 
        {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
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
          return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of comments',array("range"=> "1 and 25 ")));
        }
        else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
        }
      
      //List Product Comments
      $take = $args['numberOfComments'];
      $skip = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfComments'])-$args['numberOfComments']:0;
      $comments = $productComments->getCommentByPostID($args["productId"], $take, $skip);
      $hasMoreComments = new Comment();
      if(sizeof($comments) > 0)
      {
          $result = array();
          foreach ($comments as $comment) 
          { 
            $item = array(
                'parent_comment_id' => $comment->comment_parent,
                'comment_id' => $comment->comment_ID,
                "added_by" => $this->return_user_info_list($comment->user_id),
                'comment' => $comment->comment_content,
                'comment date' => $comment->comment_date,
                'has_more_comments' => $hasMoreComments->checkHasmoreComments($comment->comment_ID, $take),
                'comments on a comment' => self::listCommentsonComments($comment->comment_ID, $take, 0)
            );  
                     
            array_push($result, $item);
          }
          
          $comments = count($productComments->getCommentByPostID($args["productId"], -1, -1));
          $resulsts_final = $this->ef_load_data_counts($comments, $args['numberOfComments']);
          $resulsts_final['data'] = $result;
          return $this->renderJson($response, 200, $resulsts_final); 
      }
      
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
  }
  
     /**
   * @SWG\GET(
   *   path="/products/comments/{commentId}/replies",
   *   tags={"Product"},
   *   summary="List Replies On a Comment",
   *   description="list replies on the passed comment",
   *   @SWG\Parameter(name="commentId", in="path", required=false, type="integer", description="Comment ID to list the replies related to this ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfReplies", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List comments and replies to a comment"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Comments not found")
   * )
   */
    public function listRepliesonAComment($request, $response, $args) {
        $Comments = new Comment();
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
      $comments = $Comments->getCommentByCommentID($args["commentId"], $take, $skip);
      $hasMoreComments = new Comment();
      if(sizeof($comments) > 0)
      {
          $result = array();
          foreach ($comments as $comment) 
          { 
            $item = array(
                'parent_comment_id' => $comment->comment_parent,
                'comment_id' => $comment->comment_ID,
                "added_by" => $this->return_user_info_list($comment->user_id),
                'comment' => $comment->comment_content,
                'comment date' => $comment->comment_date,
                'has_more_comments' => $hasMoreComments->checkHasmoreComments($comment->comment_ID, $take),
                'comments on a comment' => self::listCommentsonComments($comment->comment_ID, $take, 0)
            );  
                     
            array_push($result, $item);
          }
          
          $comments_count = count($Comments->getCommentByCommentID($args["commentId"], -1, -1));
          $resulsts_final = $this->ef_load_data_counts($comments_count, $args['numberOfReplies']);
          $resulsts_final['data'] = $result;
          
          return $this->renderJson($response, 200, $resulsts_final); 
      }
      
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
    }

  //recursively list comments on comments
  public function listCommentsonComments($comment_id, $take, $skip) {
      $eventComments = new Comment();
      $comments = $eventComments->getCommentByCommentID($comment_id, $take, $skip);
      $hasMoreComments = new Comment();
      if(sizeof($comments) > 0)
      {
          $result = array();
          foreach ($comments as $comment) 
          { 
            $item = array(
                'parent_comment_id' => $comment_id,
                'comment_id' => $comment->comment_ID,
                "added_by" => $this->return_user_info_list($comment->user_id),
                'comment' => $comment->comment_content,
                'comment date' => $comment->comment_date,
                'has_more_comments' => $hasMoreComments->checkHasmoreComments($comment->comment_ID, $take),
                'comments on a comment' => self::listCommentsonComments($comment->comment_ID, $take, $skip)
            );  
                     
            array_push($result, $item);
          }
          
          return $result;
      }
      
      return [];
  }
   /**
   * @SWG\GET(
   *   path="/products/{product_id}/contributors",
   *   tags={"Product"},
   *   summary="List Product Contributors",
   *   description="list Product Contributors",
   *   @SWG\Parameter(name="product_id", in="path", required=false, type="string", description="Prodcut ID <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List Product Contributors ordering by number of updates"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Product not found")
   * )
   */
    public function listingProductContributors($request, $response, $args) 
    {
        if(!isset($args["product_id"]) || $args["product_id"] == "" || $args["product_id"] == "{product_id}") {
            return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue","product_id"));
        }
        
        //check valid product id
        $product = new Post();
        $product = $product->getPost($args['product_id']);
        
        if(!empty($product))
        {
            if($product->post_status == "publish")
            {
                $contributors = $product->getProductListContributors($product->ID);
                $List = $this->ef_load_data_counts(sizeof($contributors), -1);
                
                for($i = 0; $i < sizeof($contributors); $i++)
                {
                    $user = User::find($contributors[$i]->user_id);
                    $user_name = $user->user_login;
                    $contributors[$i]->user_name = $this->return_user_info_list($contributors[$i]->user_id);
                }
                $List['data'] = $contributors;
                return $this->renderJson($response, 200, $List);
            }else{
                return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Product"));
            }
        }else{
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Product"));
        }
    }
  
}

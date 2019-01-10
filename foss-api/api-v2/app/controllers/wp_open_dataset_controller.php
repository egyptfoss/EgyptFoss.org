<?php

//use AccessToken;

class WPOpenDataSetController extends EgyptFOSSController {
  
  public static $filesize = 20971520; // 20MB
  
  /**
   * @SWG\Post(
   *   path="/open-dataset",
   *   tags={"Open DataSet"},
   *   summary="Creates New Open DataSet",
   *   description="Create a new open dataset with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to create a new dataset<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="open_dataset_title", in="formData", required=false, type="string", description="Open dataset Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="open_dataset_description", in="formData", required=false, type="string", description="Open dataset Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="publisher", in="formData", required=false, type="string", description="Open dataset Publisher <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="resources", in="formData", required=true, type="file", description="Open dataset Resources <br/><b>Validations: </b><br/> 1. Resources should be one of the following extenstions [PDF,JSON,CSV,XML,HTML,DOC,DOCX,XLS,XLSX,JPEG,JPG,PNG] <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="type", in="formData", required=false, type="string", description="Open dataset Type <br/><b>Validations: </b><br/> 1. Predefined Open dataset Type in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="theme", in="formData", required=false, type="string", description="Open dataset Theme <br/><b>Validations: </b><br/> 1. Predefined theme in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="license", in="formData", required=false, type="string", description="Open dataset License <br/><b>Validations: </b><br/> 1. Predefined license in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="usage_hints", in="formData", required=false, type="string", description="Open dataset Usage Hints <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="references", in="formData", required=false, type="string", description="Open dataset References"),
   *   @SWG\Parameter(name="link_to_source", in="formData", required=false, type="string", description="Open dataset Source Link <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Open dataset Interests <br/><b>Values: </b> Multiple values with comma seperated between each value" ),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Define Open dataset Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Open DataSet added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addOpenDataSet($request, $response, $args) {
      
    $parameters = ['token', 'open_dataset_title', 'open_dataset_description', 'publisher', 'lang','interest','resources',
        'type','theme','license','usage_hints','references','link_to_source']; 
    $required_params = ['token', 'open_dataset_title', 'open_dataset_description', 'lang','publisher','type',
        'theme','license','references','link_to_source'];
    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $_POST) && !empty($_POST[$parameter]))
      {
        $args[$parameter] = $_POST[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    /*
    if(!isset($_FILES["resources"]) || empty($_FILES["resources"]['tmp_name']))
    {
        return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","Resources"));
    }*/
    
    $loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;
    $user = User::find($user_id);
    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    $parametersToCheck = array_diff($parameters, array("token", "lang","type","theme","license"));
    foreach ($parametersToCheck as $param){
      if(!preg_match('/[أ-يa-zA-Z]+/', $args[$param], $matches) && strlen($args[$param])> 0){
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongInput", $param));
      }
    }
    if(mb_strlen($args["open_dataset_title"],'UTF-8') < 10 || mb_strlen($args["open_dataset_title"],'UTF-8') > 100){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between","open_dataset_title",array("range"=>"10 to 100 char")));
    }

    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","language"));
    }
    
    //check unique open dataset title
    if (!empty(Post::where('post_title' , '=', $args['open_dataset_title'])->where('post_type' , '=', 'open_dataset')
                ->whereIn('post_status' , array('publish','pending'))->first())) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("duplicate", "open_dataset_title",array("range"=>"already exists")));
    }
    
    //check type exists
    $term_taxonomy = $this->check_term_exists($args['type'], 'dataset_type');
    if (empty($term_taxonomy)) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Type"));
    }
    
    //check theme exists
    $term_taxonomy = $this->check_term_exists($args['theme'], 'theme');
    if (empty($term_taxonomy)) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Theme"));
    }
    
    //check license exists
    $term_taxonomy = $this->check_term_exists($args['license'], 'datasets_license');
    if (empty($term_taxonomy)) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "License"));
    }
    
    //check link to source is url
    if (filter_var($args['link_to_source'], FILTER_VALIDATE_URL) === FALSE) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongUrl", "link_to_source"));
    }
    
    //validate the resources files size and extension
    $extensions = self::validExtensions();
    $filesize = self::$filesize;
    if(sizeof($_FILES["resources"]['tmp_name'] ) >= 1)
    {
        $files = $_FILES["resources"];    
        // check files against viruses before complete uploading proccess
        foreach( $files['tmp_name'] as $tmp_file ) {
          if( !empty( $tmp_file ) ) {
            $retcode = cl_scanfile($tmp_file, $virusname);
            if ($retcode == CL_VIRUS) {
                $infected = true;
                break;
            }
            sleep(1);
          }
        }

        if( $infected ) {
          return $this->renderJson($response, 422, array( "type" => "Error", 'code' => '2008' , 'message' => __("Virus detected! , Uploading proccess has been terminated.","egyptfoss", $args["lang"])));
        }
        else {
          foreach ($files['name'] as $key => $value) 
          {
              if (($files['name'][$key] != "")) 
              {
                  $size = $files['size'][$key];
                  $array = explode('.', $files['name'][$key]);
                  $extension = end($array);
                  $type = strtolower($extension);//$files['type'][$key];

                  if($size > $filesize)
                  {
                      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongSize",'Resources',array('range'=>'20MB')));
                  }

                  if(!in_array($type, $extensions))
                  {
                      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongFormat",'Resources',array('range'=>implode(',',$extensions))));
                  }
              }
          }
        }
    }
    
    $taxonomies = ['interest'];
    foreach ($taxonomies as $taxonomy) {
      $terms = trim($args[$taxonomy],',');
      $terms = str_getcsv($terms);
      foreach ($terms as $term) {
        if (!empty($term) && !preg_match('/[أ-يa-zA-Z]+/', $term, $matches)) {
          return $this->renderJson($response, 422, Messages::getErrorMessage("formatError", ucwords($taxonomy)));
        }
      }
    }
    
    $is_first_suggestion = $this->is_first_suggestion($loggedin_user->user_id);
    $newDataSet = new Post();
    $post_data = array("post_title" => $args['open_dataset_title'],
                       "post_status" => "pending",
                       "post_content" => $args["open_dataset_description"],
                       "post_type" => "open_dataset",
                       "post_author" => $loggedin_user->user_id
      );
    $newDataSet->addPost($post_data);
    $newDataSet->save();
    $newDataSet->updateGUID($newDataSet->id, 'open_dataset');
    $newDataSet->save();
    $newDataSet->add_post_translation($newDataSet->id, $args["lang"], "open_dataset");
    //$datasetStatus = Post::where("post_id","=",$newDataSet->id)->first();
    //Upload Resources
    $files = $_FILES["resources"]; 
    $resources_args = array("post_status" => "inherit",
       "post_type" => "attachment",
       "post_author" => $loggedin_user->user_id
    );
    $rsc_index = 0;
    $formats_type = "";
    $resources_ids = "";
    foreach ($files['name'] as $key => $value) 
    {
        if (($files['name'][$key] != "")) 
        {
            $fileName = str_replace(" ", "-", $files["name"][$key]);
            $year = date('Y');
            $month = date('m');
            $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
            $resources_args['guid'] = $home_url->option_value."/wp-content/uploads/{$year}/{$month}/".$fileName;
            preg_match("/(\.)+\w+$/", $files['name'][$key], $extention);
            $resources_args['post_title'] = str_replace($extention[0], '', $files['name'][$key]);
            $path = __DIR__;
            for ($d = 1; $d <= 4; $d++)
                $path = dirname($path);

            $uploaddir = $path . "/wp-content/uploads/{$year}/{$month}/";
            if (!file_exists($uploaddir)) {
              mkdir($uploaddir, 0777, true); 
            }
            $uploadfile = $uploaddir . ($fileName);
            if (move_uploaded_file($files['tmp_name'][$key], $uploadfile)) {
              $attachment = new Post();
              $attachment->addPost($resources_args);
              $attachment->post_mime_type = $files['type'][$key];
              $formats_type = $formats_type.$files['type'][$key].'|||';
              $attachment->save();
              $Postmeta = new Postmeta();
              $Postmeta->updatePostMeta($attachment->id, "_wp_attached_file", "{$year}/{$month}/".$fileName);
              //detect if file is image

                list($width, $height, $img_type, $attr) = getimagesize($uploadfile);
                $image_meta_data = array("width" => $width,
                "height" => $height,
                  "file" => "{$year}/{$month}/".$fileName,
                  "image_meta" => array(
                    "aperture" => "",
                    "credit" => "",
                    "camera" => "",
                    "caption" => "",
                    "created_timestamp" => 0,
                    "copyright" => "",
                    "focal_length"=> 0,
                    "iso"=> 0,
                    "shutter_speed" => 0,
                    "title" => "",
                    "orientation" => 0
                ));
                $Postmeta->updatePostMeta($attachment->id, "_wp_attachment_metadata", serialize($image_meta_data));
                $resources_ids = $resources_ids.$attachment->id.'|||';
              
                $Postmeta->updatePostMeta($newDataSet->id, "resources_".$rsc_index."_upload", $attachment->id);
                $Postmeta->updatePostMeta($newDataSet->id, "resources_".$rsc_index."_resource_status", 'publish');
            }
            
            $rsc_index = $rsc_index + 1;
        }
    }
    
    //update formats post meta
    $formats_type = substr($formats_type, 0, -3);
    $resources_ids = substr($resources_ids, 0, -3);
    $openDataSetMeta = new Postmeta();
    $openDataSetMeta->updatePostMeta($newDataSet->id, 'dataset_formats', $formats_type); 
    $openDataSetMeta->updatePostMeta($newDataSet->id, 'resources_ids', $resources_ids); 
    $openDataSetMeta->updatePostMeta($newDataSet->id, 'description', $args["open_dataset_description"]);
    $openDataSetMeta->updatePostMeta($newDataSet->id, 'publisher', $args["publisher"]);
    $openDataSetMeta->updatePostMeta($newDataSet->id, 'usage_hints', $args["usage_hints"]);
    $openDataSetMeta->updatePostMeta($newDataSet->id, 'source_link', $args["link_to_source"]);
    $openDataSetMeta->updatePostMeta($newDataSet->id, 'resources', $rsc_index);
    $openDataSetMeta->updatePostMeta($newDataSet->id, 'references', $args["references"]);
    
    $termTax = new TermTaxonomy();
    $terms_data = array(
        "interest" => split(",",$args['interest']),
        "dataset_type" => array($args['type']),
        "theme" => array($args['theme']),
        "datasets_license" => array($args['license'])    
    );
    $isCreated = $termTax->saveTermTaxonomies($terms_data, array("dataset_type", "theme", "datasets_license"));
    if(!$isCreated){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","terms"));
    } else {
      $newDataSet->updatePostTerms($newDataSet->id, $terms_data, true);
    }
    
    $returnMsg = Messages::getSuccessMessage("Success","Open DataSet Added");
    $returnMsg['open_dataset_id'] = $newDataSet->id;
    $returnMsg['is_first_suggestion'] = $is_first_suggestion;
    $returnMsg['is_pending_review'] = !($newDataSet->post_status == 'publish');
    
    return $this->renderJson($response, 200, $returnMsg);   
  }
  
  /**
   * @SWG\GET(
   *   path="/open-datasets",
   *   tags={"Open DataSet"},
   *   summary="List Open DataSets",
   *   description="List Open DataSets",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfDataSets", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="type", in="query", required=false, type="string", description="Filter list of open dataset by type <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Parameter(name="theme", in="query", required=false, type="string", description="Filter list of open dataset by theme <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Parameter(name="license", in="query", required=false, type="string", description="Filter list of open dataset by license <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Parameter(name="dataset_format", in="query", required=false, type="string", description="Filter list of open dataset by resource format <br/> <b>Values: </b><br/> 1.pdf <br/>2.json <br/>3.csv <br/>4.xml <br/>5.html <br/> 6.doc <br/>7.docx <br/>8.xls <br/>9.xlsx <br/>10.jpeg <br/> 11.jpg <br/> 13.png <br/>"), 
   *   @SWG\Response(response="200", description="List Open DataSets"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listOpenDataSets($request, $response, $args){
      
    $extensions = self::validExtensions();
      
    $parameters = ['pageNumber', 'numberOfDataSets', 'lang', 'type', 'theme', 'license','dataset_format'];
    $requiredParams = ['pageNumber', 'numberOfDataSets', 'lang'];
    $numeric_params = ['pageNumber', 'numberOfDataSets'];
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
    if(empty($args['numberOfDataSets'] ) || $args['numberOfDataSets'] < 1 || $args['numberOfDataSets'] > 25){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of DataSets',array("range"=> "1 and 25 ")));
    }
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }else if(!empty($args['dataset_format']) && !in_array(strtolower ($args['dataset_format']), $extensions))
    {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongFormat",'dataset_format',array('range'=>implode(',',$extensions))));
    }
    else{
      $post = new Post();
      $skip_number = ($args['pageNumber'] * $args['numberOfDataSets']) - $args['numberOfDataSets'] ;
      if ($args['pageNumber'] == 1){
        $skip_number = 0 ;
      }
      
      //get type id
      if(!isset($args["type"]))
      {
        $type_id = '';
      }else
      {
        $type_id = $this->ef_retrieve_taxonomy_id('dataset_type', $args["type"]);
      }      
      
      //get theme id
      if(!isset($args["theme"]))
      {
        $theme_id = '';
      }else
      {
        $theme_id = $this->ef_retrieve_taxonomy_id('theme', $args["theme"]);
      }            

      //get license id
      if(!isset($args["license"]))
      {
        $license_id = '';
      }else
      {
        $license_id = $this->ef_retrieve_taxonomy_id('datasets_license', $args["license"]);
      }            
      
      $list_open_datasets = $post->getAllOpenDataSets('open_dataset', 'publish', $args['numberOfDataSets'], $skip_number, $args["lang"], $type_id, $theme_id, $license_id, self::returnValidFormat(strtolower(trim($args['dataset_format']))));
      $open_datasets_result = array();
      if (count($list_open_datasets) == 0){
        $result = $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
      }
      else{
        $total_count = count($post->getAllOpenDataSets('open_dataset', 'publish', -1, -1, $args["lang"], $type_id, $theme_id, $license_id, self::returnValidFormat(strtolower(trim($args['dataset_format'])))));
        $open_datasets_result = $this->ef_load_data_counts($total_count, $args['numberOfDataSets']);
        $index = 0;        
        foreach ($list_open_datasets as $dataset) {
            $open_dataset_id = $dataset->ID;
            $user = User::find($dataset->post_author);
            $user_name = $user->user_login;
            
            $post_meta = new Postmeta();
            $meta = $post_meta->getPostMeta($open_dataset_id);
            $open_dataset_meta = array();
            foreach ($meta as $meta_key => $meta_value ) {
              $open_dataset_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
            }
            unset($meta_value);
                      
            //get type
            $type = "";
            if(array_key_exists('dataset_type', $open_dataset_meta))
            {
                $term = new Term();
                $type_id = $open_dataset_meta['dataset_type'];
                if($args["lang"] == "ar")
                {
                    if($term->getTerm($type_id)->name_ar != '')
                        $type = $term->getTerm($type_id)->name_ar;
                    else
                        $type = $term->getTerm($type_id)->name;
                }
                else
                    $type = $term->getTerm($type_id)->name;
            }
            
            //get theme
            $theme = "";
            if(array_key_exists('theme', $open_dataset_meta))
            {
                $term = new Term();
                $theme_id = $open_dataset_meta['theme'];
                if($args["lang"] == "ar")
                {
                    if($term->getTerm($theme_id)->name_ar != '')
                        $theme = $term->getTerm($theme_id)->name_ar;
                    else
                        $theme = $term->getTerm($theme_id)->name;
                }
                else
                    $theme = $term->getTerm($theme_id)->name;
            }
            
            //get license
            $license = "";
            if(array_key_exists('datasets_license', $open_dataset_meta))
            {
                $term = new Term();
                $license_id = $open_dataset_meta['datasets_license'];
                if($args["lang"] == "ar")
                {
                    if($term->getTerm($license_id)->name_ar != '')
                        $license = $term->getTerm($license_id)->name_ar;
                    else
                        $license = $term->getTerm($license_id)->name;
                }
                else
                    $license = $term->getTerm($license_id)->name;
            }
            
            //get interest
            $interests = array();
            if(array_key_exists('interest', $open_dataset_meta))
            {
                $interests_arr = unserialize($open_dataset_meta['interest']);
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
            
            //get formats
            $valid_formats = array();
            if(array_key_exists('dataset_formats', $open_dataset_meta))
            {
              $formats = explode('|||', $open_dataset_meta['dataset_formats']);
              for($i = 0; $i < sizeof($formats); $i++)
              {
                $format = self::returnValidMime($formats[$i]);
                if($format != null){
                  array_push($valid_formats, $format);
                }
              }
            }
            //get published resources count
            $total_count = 0;
            if(array_key_exists('resources_ids', $open_dataset_meta))
            {
              $total_count = count(explode("|||",$open_dataset_meta['resources_ids']));
            }
            
            
            $open_datasets_result['data'][$index] = array(
                "open_dataset_id"   => $open_dataset_id,
                "open_dataset_title" => $dataset->post_title,
                "description" => $open_dataset_meta['description'],
                "publisher" => $open_dataset_meta['publisher'],
                "type" => html_entity_decode($type),
                "theme" => html_entity_decode($theme),
                "license" => html_entity_decode($license),
                "usage_hints" => $open_dataset_meta['usage_hints'],
                "references" => $open_dataset_meta['references'],
                "link_to_source" => $open_dataset_meta['source_link'],
                "number_of_resources" => $total_count,
                "resources_formats" => $valid_formats,
                "interests" => $interests,
                "date" => $dataset->post_date,
                "added_by" => $this->return_user_info_list($dataset->post_author)
            );
             $index += 1;
        }
        unset($dataset);
        $result = $this->renderJson($response, 200, $open_datasets_result);
      }
      return $result;
    }
  }

  /**
   * @SWG\GET(
   *   path="/open-datasets/{dataset_id}",
   *   tags={"Open DataSet"},
   *   summary="Find Open DataSet",
   *   description="View Open DataSet data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to display pending news if exists"),
   *   @SWG\Parameter(name="dataset_id", in="path", required=false, type="string", description="Open Dataset ID to retrieve its details <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List Open Datasets"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function viewOpenDataSet($request, $response, $args) {
    $open_dataset = new Post();
    if(!isset($args["dataset_id"]) || $args["dataset_id"] == "") {
      return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue", "dataset_id"));
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

    $dataset = $open_dataset->getPost($args['dataset_id']);
    if ($dataset['post_status'] == 'publish' || ($dataset['post_status'] == 'pending') ){
      if($user != null && !empty($user))
      {
        if($dataset['post_status'] == 'pending' && $dataset['post_author'] != $user->ID)
        {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Open Dataset ID"));
        }
      }else
      {
        if($dataset['post_status'] == 'pending')
        {
          return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Open Dataset ID"));
        }
      }
      
      $dataset_id = $dataset['ID'];
      $data = new Postmeta();
      $dataset_meta = $data->getOpenDatasetMeta($dataset_id);
      
      $dataset_data['id'] = $dataset_id;
      $dataset_data['title'] = $dataset['post_title'];
      $dataset_data['status'] = $dataset['post_status'];
      $dataset_data['post_url'] = html_entity_decode($dataset['guid']);
      $dataset_data['publisher'] = array_key_exists('publisher', $dataset_meta) ? $dataset_meta['publisher']  : "";
      $dataset_data['description'] = array_key_exists('description', $dataset_meta) ? $dataset_meta['description']  : "";
      $dataset_data['usage_hints'] = array_key_exists('usage_hints', $dataset_meta) ? $dataset_meta['usage_hints']  : "";
      $dataset_data['references'] = array_key_exists('references', $dataset_meta) ? $dataset_meta['references']  : "";
      $dataset_data['source_link'] = array_key_exists('source_link', $dataset_meta) ? $dataset_meta['source_link']  : "";

      $resources = $open_dataset->getPostAttachments($dataset_meta['resources_ids']);
      $valid_resources = [];
      for($i = 0; $i < sizeof($resources); $i++)
      {
        if(self::returnValidMime($resources[$i]['extension']) != null)
        {
           array_push($valid_resources, $resources[$i]);
        }
      }
      
      for($i = 0; $i < sizeof($valid_resources); $i++){
        $valid_resources[$i]['extension'] = self::returnValidMime($valid_resources[$i]['extension']);
                
        //get size
        $attachment_size = $this->ef_humanFileSize($this->ef_retrieve_remote_file_size($valid_resources[$i]['link']));        
        $valid_resources[$i]['size'] = $attachment_size;
      }
      $dataset_data['resources'] = $valid_resources;
      
      $lang = 'en';
      
      //language
      if(array_key_exists('language', $dataset_meta))
      {
        $language = $dataset_meta['language'];
        $lang_arr = unserialize(unserialize($language));
        $lang = $lang_arr['slug'];
        if($lang == null)
        {
          $lang = "en";
        }
      }
      
      $term = new Term();
      
      $dataset_type = $dataset_meta['dataset_type'];
      if( !empty( $dataset_type ) && !is_numeric( $dataset_type ) ) {
        $arr = unserialize($dataset_meta['dataset_type']);
        $dataset_type = count( $arr ) ? $arr[0] : '';
      }
      $type_name = $term->getTerm( $dataset_type );
      $dataset_data['type'] = isset($type_name['name']) ? html_entity_decode($type_name['name'])  : "";
      if( $lang == 'ar' ) {
        $dataset_data['type'] = !empty($type_name['name_ar']) ? html_entity_decode($type_name['name_ar'])  : html_entity_decode($dataset_data['type']);
      }
       

      $theme = $dataset_meta['theme'];
      if( !empty( $theme ) && !is_numeric( $theme ) ) {
        $arr = unserialize($dataset_meta['theme']);
        $theme = count( $arr ) ? $arr[0] : '';
      }
      $theme_name = $term->getTerm($theme);
      $dataset_data['theme'] = isset($theme_name['name']) ? html_entity_decode($theme_name['name'])  : "";
      if( $lang == 'ar' ) {
        $dataset_data['theme'] = !empty($theme_name['name_ar']) ? html_entity_decode($theme_name['name_ar'])  : html_entity_decode($dataset_data['theme']);
      }

      $datasets_license = $dataset_meta['datasets_license'];
      if( !empty( $datasets_license ) && !is_numeric( $datasets_license ) ) {
        $arr = unserialize($dataset_meta['datasets_license']);
        $datasets_license = count( $arr ) ? $arr[0] : '';
      }
      $license_name = $term->getTerm($datasets_license);      
      $dataset_data['license'] = isset($license_name['name']) ? html_entity_decode($license_name['name'])  : "";
      if( $lang == 'ar' ) {
        $dataset_data['license'] = !empty($license_name['name_ar']) ? html_entity_decode($license_name['name_ar'])  : html_entity_decode($dataset_data['license']);
      }
      
      if (array_key_exists('interest', $dataset_meta) && !empty($dataset_meta['interest'])){
        $interests_ids = $dataset_meta['interest'] ;
        $keyword_name = array();
        foreach ($interests_ids as $keyword_id) {
          $keyword= $term->getTerm($keyword_id);
          $keyword_name = array_merge($keyword_name, array($keyword['name']));
        }
        $dataset_data['interest'] = $keyword_name;
      } else {
          $dataset_data['interest'] = array();
      }
      $dataset_data['date'] = $dataset['post_date'];
      $user = User::find($dataset['post_author']);
      $dataset_data['added_by'] =  $this->return_user_info_list($user->ID);

      $dataset_data['language'] = $lang;
      
      $result = $this->renderJson($response, 200, $dataset_data);
    }
    else{
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Open Dataset ID"));
    }
    return $result;
  }
  
  public function validExtensions()
  {
    $extensions = array('pdf', 'json', 'csv', 'xml', 'html', 'doc', 'docx', 'xls', 'xlsx', 'jpeg', 'jpg', 'png');

    return $extensions;
  }
  
  function returnValidFormat($ext)
  {
      $extension_mime_types = array('pdf' => 'application/pdf', 'json' => 'application/json', 'csv' => 'text/csv', 'xml' => 'text/xml', 'html' => 'text/html', 'jpeg'=>'image/jpeg', 'jpg'=>'image/jpeg', 'png'=>'image/png', 'xls'=>'application/vnd.ms-excel', 'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'doc'=>'application/msword', 'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
      
      return $extension_mime_types[strtolower($ext)];
  }
  
  function returnValidMime($ext)
  {
      $extension_mime_types = array('application/pdf' => 'pdf', 'application/json' => 'json', 'text/csv' => 'csv', 'text/xml' => 'xml', 'text/html' => 'html', 'image/jpeg'=>'jpeg', 'image/jpeg'=>'jpg', 'image/png'=>'png', 'application/vnd.ms-excel'=>'xls', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'=>'xlsx', 'application/msword'=>'doc', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'=>'docx');
      
      return $extension_mime_types[strtolower($ext)];
  }
  
  /**
  * @SWG\Post(
  *   path="/open-datasets/{dataset_id}/comments",
  *   tags={"Open DataSet"},
  *   summary="Creates Open DataSet Comment",
  *   description=" Add Open DataSet commnents with the dataset id",
  *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new comment<br/> <b>[Required]</b>"),
  *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
  *   @SWG\Parameter(name="dataset_id", in="formData", required=false, type="integer", description="Open Dataset ID to post the comment on <br/> <b>[Required]</b>"),
  *   @SWG\Response(response="200", description="Comment added successfully"),
  *   @SWG\Response(response="422", description="Validation Error"),
  *   @SWG\Response(response="404", description="Open Dataset not found")
  * )
  */
  public function addDatasetComments($request, $response, $args) {
    $result = "";
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else{
        // validate data
        $result = "";
        if (isset($_POST['comment']) || !empty($_POST['comment'])) {
          $meta['comment'] = $_POST['comment'];
        } else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment"));
          return $result;
        }
        if(isset($_POST['dataset_id']) || !empty($_POST['dataset_id']) ) {
          if (is_numeric($_POST['dataset_id'])) {
            //we need to check if this post_name is in db or not
            $post = new Post();
            $post_type = "open_dataset";
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['dataset_id'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['dataset_id'] = $_POST['dataset_id'];
              $meta['postID'] = $post_exists['ID'];
            } else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Open Dataset ID")); }
          } else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Open Dataset ID")); }
        } else {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Open Dataset ID"));
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
    } else {
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue", "AccessToken"));
    }
    return $result;
  }
  
  /**
   * @SWG\Post(
   *   path="/open-datasets/{dataset_id}/comments/{commentId}/replies",
   *   tags={"Open DataSet"},
   *   summary="Creates Reply On Open DataSet Comment",
   *   description="Add reply on the passed comment on the passed Open DataSet",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new reply<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="dataset_id", in="formData", required=false, type="integer", description="Dataset ID<br/> <b>[Required]</b>"),
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
      if (empty($user)){
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      } else {
        // validate data
        $result = "";
        if (isset($_POST['comment']) || !empty($_POST['comment'])) {
          $meta['comment'] = $_POST['comment'];
        } else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment Reply"));
          return $result;
        }

        if(isset($_POST['dataset_id']) || !empty($_POST['dataset_id']) ){
          if (is_numeric($_POST['dataset_id'])) {
            // check if this post id is in db or not
            $post = new Post();
            $post_type = "open_dataset";
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['dataset_id'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['dataset_id'] = $_POST['dataset_id'];
              $meta['postID'] = $post_exists['ID'];
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Open Dataset ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Open Dataset ID")); }
        }
        else{
          return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Open Dataset ID"));
        }
        
        if(isset($_POST['commentId']) || !empty($_POST['commentId']) ){
          if (is_numeric($_POST['commentId'])) {
            // Check if this comment id is in comments table or not
            $comment_id = new Comment();
            $comment_exists = $comment_id->getCommentByID($_POST['commentId']);
            if (!empty($comment_exists) && ( $meta['dataset_id'] == $comment_exists['comment_post_ID']) ) {
              if( empty( $comment_exists->comment_parent ) ) {
                    $meta['commentId'] = $_POST['commentId'];
              }
              else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notAllowed", "Multi level of comments")); }
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Comment ID is not in this Open Dataset or")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Comment ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment ID"));
        }
        // If no errors so far
        if ($result == "") {
          $comment = new Comment();
          $comment->addReplyToComment($user->ID, $user->user_login, $user->user_email, $meta['postID'], $meta['commentId'], $meta['comment'], $meta['dataset_id']);
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
   *   path="/open-datasets/{dataset_id}/comments",
   *   tags={"Open DataSet"},
   *   summary="List Comments and Replies On Open DataSet",
   *   description="list replies on the passed Open DataSet",
   *   @SWG\Parameter(name="dataset_id", in="path", required=false, type="integer", description="Open DataSet ID to list the comments related to this ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfComments", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List comments and replies to a Open DataSet"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Comments not found")
   * )
   */
  public function listDatasetsComments($request, $response, $args){
    $datasetComments = new Comment();
    if(!isset($args["dataset_id"]) || $args["dataset_id"] == "" || $args["dataset_id"] == "{dataset_id}"){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "dataset_id"));
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
    //List News Comments
    $take = $args['numberOfComments'];
    $skip = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfComments'])-$args['numberOfComments']:0;
    $comments = $datasetComments->getCommentByPostID($args["dataset_id"], $take, $skip);
    $hasMoreComments = new Comment();
    if(sizeof($comments) > 0){
      $result = array();
      foreach ($comments as $comment){ 
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
      
      $comments = count($datasetComments->getCommentByPostID($args["dataset_id"], -1, -1));
      $resulsts_final = $this->ef_load_data_counts($comments, $args['numberOfComments']);
      $resulsts_final['data'] = $result;
      
      return $this->renderJson($response, 200, $resulsts_final);
    }
    return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
  }
  
   /**
   * @SWG\GET(
   *   path="/open-datasets/comments/{commentId}/replies",
   *   tags={"Open DataSet"},
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
    if(!isset($args["commentId"]) || $args["commentId"] == "" || $args["commentId"] == "{commentId}"){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "commentId"));
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
    if(sizeof($comments) > 0){
      $result = array();
      foreach ($comments as $comment){
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
  public function listCommentsonComments($comment_id, $take, $skip){
    $datasetComments = new Comment();
    $comments = $datasetComments->getCommentByCommentID($comment_id, $take, $skip);
    $hasMoreComments = new Comment();
    if(sizeof($comments) > 0){
      $result = array();
      foreach ($comments as $comment){
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
   * @SWG\POST(
   *   path="/open-datasets/resources/{dataset_id}",
   *   tags={"Open DataSet"},
   *   summary="Add resources to existing open dataset",
   *   description="Logged in user can add resources to existing open dataset",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to add new resources<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="dataset_id", in="path", required=false, type="string", description="Open Dataset ID returned from list datasets<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="open_dataset_description", in="formData", required=false, type="string", description="Resources Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="resources", in="formData", required=true, type="file", description="Open dataset Resources <br/><b>Validations: </b><br/> 1. Resources should be one of the following extenstions [PDF,JSON,CSV,XML,HTML,DOC,DOCX,XLS,XLSX,JPEG,JPG,PNG] <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Add Open dataset resources Successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addResourceOpendataset($request, $response, $args) {
    $parameters = ['token', 'dataset_id', 'open_dataset_description', 'resources'];
    $required_params = ['token','dataset_id','open_dataset_description'];
    
    foreach ($parameters as $parameter) {
      if(array_key_exists($parameter, $_POST) && !empty($_POST[$parameter]))
      {
        $args[$parameter] = $_POST[$parameter];
      }
      else
      {
        if(in_array($parameter, $required_params)){
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", $parameter));
        } else {
          $args = array_merge($args, array($parameter => ''));
        }
      }
    }
    
    if(!isset($_FILES["resources"]) || empty($_FILES["resources"]['tmp_name']))
    {
        return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","Resources"));
    }
    
    $loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $user_id = $loggedin_user->user_id;
    $user = User::find($user_id);
    if( !$this->user_can($user_id, 'add_new_ef_posts') ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    //check if valid open dataset
    if(!is_numeric($args['dataset_id'])) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","dataset_id"));
    }
    
    $datasetExists = Post::getPostsBy(array("post_type"=>"open_dataset","post_status"=>"publish","post_id"=>$args["dataset_id"]));
    if(empty($datasetExists->get()->first())) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("notFound","dataset"));
    }
    
    //check description 
    if(!preg_match('/[أ-يa-zA-Z]+/', $args['open_dataset_description'], $matches))
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "open_dataset_description"));
    }
    
    //validate the resources files size and extension
    $extensions = self::validExtensions();
    $filesize = self::$filesize;  
    if(sizeof($_FILES["resources"]['tmp_name'] ) >= 1)
    {
        $files = $_FILES["resources"];
        // check files against viruses before complete uploading proccess
        foreach( $files['tmp_name'] as $tmp_file ) {
          if( !empty( $tmp_file ) ) {
            $retcode = cl_scanfile($tmp_file, $virusname);
            if ($retcode == CL_VIRUS) {
                $infected = true;
                break;
            }
            sleep(1);
          }
        }

        if( $infected ) {
          return $this->renderJson($response, 422, array( "type" => "Error", 'code' => '2008' , 'message' => __("Virus detected! , Uploading proccess has been terminated.","egyptfoss")));
        }
        else {
          foreach ($files['name'] as $key => $value) 
          {
              if (($files['name'][$key] != "")) 
              {
                  $size = $files['size'][$key];
                  $array = explode('.', $files['name'][$key]);
                  $extension = end($array);
                  $type = strtolower($extension);//$files['type'][$key];

                  if($size > $filesize)
                  {
                      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongSize",'Resources',array('range'=>'20MB')));
                  }

                  if(!in_array($type, $extensions))
                  {
                      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongFormat",'Resources',array('range'=>implode(',',$extensions))));
                  }
              }
          }
        }
    }
    
    //add resources as pending
    $files = $_FILES["resources"]; 
    $resources_args = array("post_status" => "inherit",
       "post_type" => "attachment",
       "post_author" => $loggedin_user->user_id
    );
    
    //get total count of resources to add on
    $dataset_meta = new Postmeta();
    
    $resources = $dataset_meta->getOpenDatasetMeta($args['dataset_id'])['resources'];
    $rsc_index = $resources;
    $formats_type = "";
    $resources_ids = "";
    foreach ($files['name'] as $key => $value) 
    {
        if (($files['name'][$key] != "")) 
        {
            $fileName = str_replace(" ", "-", $files["name"][$key]);
            $year = date('Y');
            $month = date('m');
            $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
            $resources_args['guid'] = $home_url->option_value."/wp-content/uploads/{$year}/{$month}/".$fileName;
            preg_match("/(\.)+\w+$/", $files['name'][$key], $extention);
            $resources_args['post_title'] = str_replace($extention[0], '', $files['name'][$key]);
            $resources_args['post_content'] = $args['open_dataset_description'];
            $path = __DIR__;
            for ($d = 1; $d <= 4; $d++)
                $path = dirname($path);

            $uploaddir = $path . "/wp-content/uploads/{$year}/{$month}/";
            if (!file_exists($uploaddir)) {
              mkdir($uploaddir, 0777, true); 
            }
            $uploadfile = $uploaddir . ($fileName);
            if (move_uploaded_file($files['tmp_name'][$key], $uploadfile)) {
              $attachment = new Post();
              $attachment->addPost($resources_args);
              $attachment->post_mime_type = $files['type'][$key];
              $formats_type = $formats_type.$files['type'][$key].'|||';
              $attachment->save();
              $Postmeta = new Postmeta();
              $Postmeta->updatePostMeta($attachment->id, "_wp_attached_file", "{$year}/{$month}/".$fileName);
              //detect if file is image

                list($width, $height, $img_type, $attr) = getimagesize($uploadfile);
                $image_meta_data = array("width" => $width,
                "height" => $height,
                  "file" => "{$year}/{$month}/".$fileName,
                  "image_meta" => array(
                    "aperture" => "",
                    "credit" => "",
                    "camera" => "",
                    "caption" => "",
                    "created_timestamp" => 0,
                    "copyright" => "",
                    "focal_length"=> 0,
                    "iso"=> 0,
                    "shutter_speed" => 0,
                    "title" => "",
                    "orientation" => 0
                ));
                $Postmeta->updatePostMeta($attachment->id, "_wp_attachment_metadata", serialize($image_meta_data));
                $resources_ids = $resources_ids.$attachment->id.'|||';
              
                $Postmeta->updatePostMeta($args['dataset_id'], "resources_".$rsc_index."_upload", $attachment->id);
                $badgesUsers = new EFBBadgesUser();
                $isGrantedOpenDataSet = $badgesUsers->getBadgesByUserAndName($loggedin_user->user_id,"open_dataset_l2");
                $resourceStatus = "pending";
                if(!empty($isGrantedOpenDataSet))
                {
                  $resourceStatus = "publish";
                }
                $Postmeta->updatePostMeta($args['dataset_id'], "resources_".$rsc_index."_resource_status", $resourceStatus);
            }
            
            $rsc_index = $rsc_index + 1;
        }
    }
    $Postmeta->updatePostMeta($args['dataset_id'], 'resources', $rsc_index);
   
    // get absolute path of landing project directory
    $path = __DIR__;
    
    for ($d = 1; $d <= 4; $d++)
        $path = dirname($path);

    // get uploads directory info
    $uploaddir = $path . '/wp-content/uploads';

    // check if zip file for this post is created
    if( file_exists( $uploaddir . '/zip-files/open-dataset-' . $args['dataset_id'] . '.zip' ) ) {
      // remove created zip file
      unlink( $uploaddir . '/zip-files/open-dataset-' . $args['dataset_id'] . '.zip' );
    }
    
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","Open DataSet Resources Added"));   
    
  }
  
  
}
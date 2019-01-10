<?php

use AccessToken;

class WPExpertThoughtController extends EgyptFOSSController {
  /**
   * @SWG\Post(
   *   path="/expert-thoughts",
   *   tags={"Expert Thoughts"},
   *   summary="Creates New Expert Thought",
   *   description="Create a new expert thought with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to create a new expert thought<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_title", in="formData", required=false, type="string", description="Expert Thought Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_description", in="formData", required=false, type="string", description="Expert Thought Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_image", in="formData", required=false, type="file", description="Expert Thought Featured Image <br/><b>Validations: </b><br/> 1. Valid Image format"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Expert Thought Interests <br/><b>Values: </b> Multiple values with comma seperated between each value" ),
   *   @SWG\Response(response="200", description="expert thought added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addExpertThought($request, $response, $args) {
      
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
    
    $parameters = [ 'post_title', 'post_description', 'post_image','interest']; 
    $required_params = [ 'post_title', 'post_description'];
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
    
    $user_id = $loggedin_user->user_id;
    $user = User::find($user_id);
    $is_expert = Usermeta::where("meta_key","=","is_expert")->where("user_id",'=',$user_id)->first();
    
    if( !isset($is_expert->meta_value) ||  !$is_expert->meta_value  ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    $parametersToCheck = array_diff($parameters, array("token", "lang"));
    foreach ($parametersToCheck as $param){
      if(!preg_match('/[أ-يa-zA-Z]+/', $args[$param], $matches) && strlen($args[$param])> 0){
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", $param." must at least contain one letter"));
      }
    }
    if(mb_strlen($args["post_title"], 'UTF-8') < 10 || mb_strlen($args["post_title"], 'UTF-8') > 100){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between","title",array("range"=>"10 to 100 char")));
    }else 
      $titleExists = ExpertThought::where("post_title",$args["post_title"])->first();
      if(!empty($titleExists)){
        return $this->renderJson($response, 422, Messages::getErrorMessage("exists","title"));
    }else if(isset($_FILES["post_image"]) && !empty($_FILES["post_image"]['tmp_name']) && exif_imagetype($_FILES["post_image"]['tmp_name']) == FALSE ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","image type"));
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
    $args["lang"] = "en";
    $newExpertThought = new Post();
    $post_data = array("post_title" => $args['post_title'],
                       "post_status" => "pending",
                       "post_content" => $args["post_description"],
                       "post_type" => "expert_thought",
                       "post_author" => $loggedin_user->user_id
      );
    $newExpertThought->addPost($post_data);
    $newExpertThought->save();
    $newExpertThought->updateGUID($newExpertThought->id, 'expert_thought');
    $newExpertThought->save();
    $newExpertThought->add_post_translation($newExpertThought->id, $args["lang"], "expert_thought");

    
    $img_args = array("post_status" => "inherit",
                       "post_type" => "attachment",
                       "post_author" => $loggedin_user->user_id
                    );
    $newExpertThought->updateFeaturedImage($img_args, $newExpertThought->id, $_FILES['post_image']); 
    
    $termTax = new TermTaxonomy();
    $terms_data = array(
      "interest" => split(",",$args['interest'])
    );
    $isCreated = $termTax->saveTermTaxonomies($terms_data, array());
    if(!$isCreated){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","terms"));
    } else {     
      $newExpertThought->updatePostTerms($newExpertThought->id, $terms_data, true);
    }
    
    $returnMsg = Messages::getSuccessMessage("Success","Expert thought Added");
    $returnMsg['expert_thought_id'] = $newExpertThought->id;
    
    return $this->renderJson($response, 200, $returnMsg);   
  }
  
  /**
   * @SWG\Post(
   *   path="/expert-thoughts/{thought_id}",
   *   tags={"Expert Thoughts"},
   *   summary="Edits Expert Thought",
   *   description="Edit an expert thought with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to edit an expert thought<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="thought_id", in="path", required=false, type="string", description="Expert Thought ID returned from list of Expert Thoughts<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_title", in="formData", required=false, type="string", description="Expert Thought Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_description", in="formData", required=false, type="string", description="Expert Thought Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_image", in="formData", required=false, type="file", description="Expert Thought Featured Image <br/><b>Validations: </b><br/> 1. Valid Image format <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Expert Thought Interests <br/><b>Values: </b> Multiple values with comma seperated between each value" ),
   *   @SWG\Response(response="200", description="expert thought edited successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function editExpertThought($request, $response, $args) {
    $parameters = ['token', 'thought_id', 'post_title', 'post_description', 'post_image','interest']; 
    $required_params = [ 'token', 'thought_id' ];
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
    
    $loggedin_user = isset($args['token']) ? (AccessToken::where('access_token', '=', $args['token'])->first()) : null;
    if ($loggedin_user == null) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
    }
    
    $expert_thought_query = ExpertThought::where("ID", "=", $args[ 'thought_id' ]);
    $expert_thought = $expert_thought_query->first();

    if( $expert_thought == null ) {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Thought"));
    }
    
    $args[ 'post_title' ]       = empty( $args[ 'post_title' ] )?$expert_thought->post_title:$args[ 'post_title' ];
    $args[ 'post_description' ] = empty( $args[ 'post_description' ] )?$expert_thought->post_content:$args[ 'post_description' ];
            
    if( $expert_thought->post_author != $loggedin_user->user_id ) {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    $parametersToCheck = array_diff($parameters, array("token", "thought_id"));
    foreach ($parametersToCheck as $param){
      if(!preg_match('/[أ-يa-zA-Z]+/', $args[$param], $matches) && strlen($args[$param])> 0){
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", $param." must at least contain one letter"));
      }
    }
    
    if(mb_strlen($args["post_title"], 'UTF-8') < 10 || mb_strlen($args["post_title"], 'UTF-8') > 100){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between","title",array("range"=>"10 to 100 char")));
    }
    
    $titleExists = ExpertThought::where("post_title",$args["post_title"])->where("ID", "!=", $args[ 'thought_id' ])->first();
    
    if( !empty( $titleExists ) ){
        return $this->renderJson($response, 422, Messages::getErrorMessage("existsBySameUser","title"));
    }else if(isset($_FILES["post_image"]) && !empty($_FILES["post_image"]['tmp_name']) && exif_imagetype($_FILES["post_image"]['tmp_name']) == FALSE ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","image type"));
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
    
    $thought_data = array(
        'post_title'    => $args['post_title'],
        'post_content'  => $args['post_description'],
    );
    
    $expert_thought_query->update( $thought_data );
    
    if( !empty( $_FILES['post_image'] ) ) {
        $img_args = array("post_status" => "inherit",
                           "post_type" => "attachment",
                           "post_author" => $loggedin_user->user_id
                        );

        $expert_thought->updateFeaturedImage( $img_args, $expert_thought->ID, $_FILES['post_image'] ); 
    }
    if( !empty( $args[ 'interest' ] ) ) {
        $termTax = new TermTaxonomy();
        $terms_data = array(
          "interest" => split(",",$args['interest'])
        );
        $isCreated = $termTax->saveTermTaxonomies($terms_data, array());
        if(!$isCreated){
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","terms"));
        } else {     
          $expert_thought->updatePostTerms($expert_thought->ID, $terms_data, true);
        }
    }
    
    return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","Expert thought Edited"));   
  }
  
  /**
   * @SWG\GET(
   *   path="/expert-thoughts",
   *   tags={"Expert Thoughts"},
   *   summary="List expert thoughts",
   *   description="List expert thoughts with pagination",
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfItems", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List Expert thoughts"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listExpertThoughts($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfItems',"lang"];
    $requiredParams = ['pageNumber', 'numberOfItems',"lang"];
    $numeric_params = ['pageNumber', 'numberOfItems'];
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
    }else if(empty($args['numberOfItems'] ) || $args['numberOfItems'] < 1 || $args['numberOfItems'] > 25){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of items',array("range"=> "1 and 25 ")));
    }else if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    } else {
      $post = new Post();
      $skip_number = ($args['pageNumber'] * $args['numberOfItems']) - $args['numberOfItems'] ;
      if ($args['pageNumber'] == 1){
        $skip_number = 0 ;
      }
      
      
      $expertThought = new ExpertThought();
      $thoughts = $expertThought->getPublishedThoughts($skip_number,$args['numberOfItems']);
      $thoughts_results = array();
      if (count($thoughts) == 0){
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
      }
      else{
        $total_count = count($expertThought->getPublishedThoughts());
        $thoughts_results = $this->ef_load_data_counts($total_count, $args['numberOfItems']);
        $index = 0;
        foreach ($thoughts as $thought) {
            $thought_id = $thought->ID;
            $user = User::find($thought->post_author);
            $user_name = $user->user_login;

            $post_meta = new Postmeta();
            $meta = $post_meta->getPostMeta($thought_id);
            $thought_meta = array();
            foreach ($meta as $meta_key => $meta_value ) {
              $thought_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
            }
            unset($meta_value);
            
            //get thumbnail image
            $url = '';
            if(array_key_exists('_thumbnail_id', $thought_meta))
            {
              $post_type = "attachment";
              $post_status = "inherit";
              $attachment_id = $thought_meta['_thumbnail_id'];
              $thought_image = Post::getPostByID($attachment_id, $post_type, $post_status);
              if($thought_image){
                $url = $thought_image->guid;  
              }
              
              if($url != '') {
                //return thumbnail size in listing
                $url = $this->ef_image_sizes($url,'340x210');
              }
            }
            
            //get interest
            $interests = array();
            if(array_key_exists('interest', $thought_meta))
            {
                $interests_arr = unserialize($thought_meta['interest']);
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
            $usermeta = new Usermeta();
            $user_info = $this->return_user_info_list($thought->post_author);
            $optLang = ($args["lang"] == "en")?"name":"name_ar";
            $option = $usermeta->getMeta($thought->post_author)['sub_type'];
            $user_info["sub_type"] = $this->getSystemData('account_sub_types',$option,"key",true)[0][$optLang];
            $thoughts_results['data'][$index] = array(
                "expert_thought_id"   => $thought_id,
                "expert_thought_title" => $thought->post_title,
                "description" => $thought->post_content,
                "short_description" => self::shorten_description( $thought->post_content ),
                "thumbnail" => $url,
                "interests" => $interests,
                "date" => $thought->post_date,
                "added_by" => $user_info
            );
            $index += 1;
        }
        unset($thought);
        $result = $this->renderJson($response, 200, $thoughts_results);
      }
      return $result;
    }
  }
  
  /**
   * @SWG\GET(
   *   path="/expert-thoughts/{thought_id}",
   *   tags={"Expert Thoughts"},
   *   summary="Finds an Expert Thought",
   *   description="Get Expert Thought data according to the passed Expert Thought id",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to display pending news if exists"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="thought_id", in="path", required=false, type="integer", description="Expert Thought ID to retrieve its details <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View a Expert Thought info"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Expert Thought not found")
   * )
   */
    public function viewExpertThought($request, $response, $args) {
      if(!isset($args["thought_id"]) || $args["thought_id"] == "" || $args["thought_id"] == "{thought_id}") 
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "thought_id"));
      }
      
      if(!isset($_GET["lang"]) || ($_GET["lang"] != "en" && $_GET["lang"] != "ar")){
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
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
      $thought = ExpertThought::where("ID","=",$args['thought_id'])->first();
      if ($thought) { 
       
          if($thought->post_status == 'pending' && $thought->post_author != $user->ID)
          {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "expert thought ID"));
          }
        $post_meta = new Postmeta();
        $meta = $post_meta->getPostMeta($thought->ID);
        $thought_meta = array();
        foreach ($meta as $meta_key => $meta_value ) {
          $thought_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
        }

        //set thumbnail image
        $url = '';
        if(array_key_exists('_thumbnail_id', $thought_meta))
        {
          $post_type = "attachment";
          $post_status = "inherit";
          $attachment_id = $thought_meta['_thumbnail_id'];
          $thought_image = Post::getPostByID($attachment_id, $post_type, $post_status);
          if($thought_image){
            $url = $thought_image->guid;
          }
        }
        //get interest
        $interests = array();
        if(array_key_exists('interest', $thought_meta))
        {
            $interests_arr = unserialize($thought_meta['interest']);
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
        
        unset($meta_value);
        $user_info = $this->return_user_info_list($thought->post_author);
        $optLang = ($_GET["lang"] == "en")?"name":"name_ar";
        $usermeta = new Usermeta();
        $option = $usermeta->getMeta($thought->post_author)['sub_type'];
        $user_info["sub_type"] = $this->getSystemData('account_sub_types',$option,"key",true)[0][$optLang];
        $thought_result = array(
            "ID" => $thought->ID,
            "title" => $thought->post_title,
            "status" => $thought->post_status,
            "post_url" => html_entity_decode($thought->guid),
            "description" => $thought->post_content,
            "thumbnail" => $url,
            "interests" => $interests,
            "date" => $thought->post_date,
            "added_by" => $user_info
        );

        unset($thought);
        return $this->renderJson($response, 200, $thought_result);
      }

      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Expert Thought ID"));
    
    }
    
 /**
  * @SWG\Post(
  *   path="/expert-thoughts/{thought_id}/comments",
  *   tags={"Expert Thoughts"},
  *   summary="Creates Expert Thought Comment",
  *   description=" Add Expert Thought comments with the thought id",
  *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new comment<br/> <b>[Required]</b>"),
  *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
  *   @SWG\Parameter(name="thought_id", in="formData", required=false, type="integer", description="Expert Thought ID to post the comment on <br/> <b>[Required]</b>"),
  *   @SWG\Response(response="200", description="Comment added successfully"),
  *   @SWG\Response(response="422", description="Validation Error"),
  *   @SWG\Response(response="404", description="Expert Thought not found")
  * )
  */
  public function addExpertThoughtComments($request, $response, $args) {
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
        if(isset($_POST['thought_id']) || !empty($_POST['thought_id']) ) {
          if (is_numeric($_POST['thought_id'])) {
            //we need to check if this post_name is in db or not
            $post = new Post();
            $post_type = "expert_thought";
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['thought_id'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['thought_id'] = $_POST['thought_id'];
              $meta['postID'] = $post_exists['ID'];
            } else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Expert Thought ID")); }
          } else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Expert Thought ID")); }
        } else {
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Expert Thought ID"));
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
   *   path="/expert-thoughts/{thought_id}/comments/{commentId}/replies",
   *   tags={"Expert Thoughts"},
   *   summary="Creates Reply On Expert Thought Comment",
   *   description="Add reply on the passed comment on the passed Expert Thought",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new reply<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="thought_id", in="formData", required=false, type="integer", description="Expert Thought ID<br/> <b>[Required]</b>"),
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

        if(isset($_POST['thought_id']) || !empty($_POST['thought_id']) ){
          if (is_numeric($_POST['thought_id'])) {
            // check if this post id is in db or not
            $post = new Post();
            $post_type = "expert_thought";
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['thought_id'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['thought_id'] = $_POST['thought_id'];
              $meta['postID'] = $post_exists['ID'];
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Expert Thought ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Expert Thought ID")); }
        }
        else{
          return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Expert Thought ID"));
        }
        
        if(isset($_POST['commentId']) || !empty($_POST['commentId']) ){
          if (is_numeric($_POST['commentId'])) {
            // Check if this comment id is in comments table or not
            $comment_id = new Comment();
            $comment_exists = $comment_id->getCommentByID($_POST['commentId']);
            if (!empty($comment_exists) && ( $meta['thought_id'] == $comment_exists['comment_post_ID']) ) {
              if( empty( $comment_exists->comment_parent ) ) {
                    $meta['commentId'] = $_POST['commentId'];
              }
              else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notAllowed", "Multi level of comments")); }
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Comment ID is not in this Expert Thought or")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Comment ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment ID"));
        }
        // If no errors so far
        if ($result == "") {
          $comment = new Comment();
          $comment->addReplyToComment($user->ID, $user->user_login, $user->user_email, $meta['postID'], $meta['commentId'], $meta['comment'], $meta['thought_id']);
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
   *   path="/expert-thoughts/{thought_id}/comments",
   *   tags={"Expert Thoughts"},
   *   summary="List Comments and Replies On Expert Thought",
   *   description="list replies on the passed Expert Thought",
   *   @SWG\Parameter(name="thought_id", in="path", required=false, type="integer", description="Expert Thought ID to list the comments related to this ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfComments", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List comments and replies to a Expert Thought"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Comments not found")
   * )
   */
  public function listExpertThoughtComments($request, $response, $args){
    $thoughtComments = new Comment();
    if(!isset($args["thought_id"]) || $args["thought_id"] == "" || $args["thought_id"] == "{thought_id}"){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "thought_id"));
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
    $comments = $thoughtComments->getCommentByPostID($args["thought_id"], $take, $skip);
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
      
      $comments = count($thoughtComments->getCommentByPostID($args["thought_id"], -1, -1));
      $resulsts_final = $this->ef_load_data_counts($comments, $args['numberOfComments']);
      $resulsts_final['data'] = $result;
      
      return $this->renderJson($response, 200, $resulsts_final);
    }
    return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
  }
  
   /**
   * @SWG\GET(
   *   path="/expert-thoughts/comments/{commentId}/replies",
   *   tags={"Expert Thoughts"},
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
    $thoughtComments = new Comment();
    $comments = $thoughtComments->getCommentByCommentID($comment_id, $take, $skip);
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
}

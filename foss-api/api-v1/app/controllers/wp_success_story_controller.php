<?php

use AccessToken;

class WPSuccessStoryController extends EgyptFOSSController {
  /**
   * @SWG\Post(
   *   path="/success-story",
   *   tags={"Success Story"},
   *   summary="Creates New Success Story",
   *   description="Create a new success story with the passed data",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to create a new success story<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_title", in="formData", required=false, type="string", description="Success Story Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_description", in="formData", required=false, type="string", description="Success Story Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_image", in="formData", required=false, type="file", description="Success Story Featured Image <br/><b>Validations: </b><br/> 1. Valid Image format"),
   *   @SWG\Parameter(name="category", in="formData", required=false, type="string", description="Success Story Category <br/><b>Validations: </b><br/> 1. Predefined Category in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="Success Story Interests <br/><b>Values: </b> Multiple values with comma seperated between each value" ),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Define Success Story Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Success Story added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addSuccessStory($request, $response, $args) {
      
    $parameters = ['token', 'post_title', 'post_description', 'post_image', 'lang','interest','category']; 
    $required_params = ['token', 'post_title', 'post_description', 'lang','category'];
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
    
    /*if(!isset($_FILES["post_image"]) || empty($_FILES["post_image"]['tmp_name']))
    {
        return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue","Success Story Image"));
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
    $parametersToCheck = array_diff($parameters, array("token", "lang", "category"));
    foreach ($parametersToCheck as $param){
      if(!preg_match('/[أ-يa-zA-Z]+/', $args[$param], $matches) && strlen($args[$param])> 0){
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", $param." must at least contain one letter"));
      }
    }
    if(mb_strlen($args["post_title"], 'UTF-8') < 10 || mb_strlen($args["post_title"], 'UTF-8') > 100){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between","title",array("range"=>"10 to 100 char")));
    }
     if(isset($_FILES["post_image"]) && !empty($_FILES["post_image"]['tmp_name']) && exif_imagetype($_FILES["post_image"]['tmp_name']) == FALSE ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","image type"));
    }
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    //check category exists
    $term_taxonomy = $this->check_term_exists($args['category'], 'success_story_category');
    if (empty($term_taxonomy)) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Category"));
    }
    $args['category'] = html_entity_decode($term_taxonomy->name);
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
    $newSuccessStory = new Post();
    $post_data = array("post_title" => $args['post_title'],
                       "post_status" => "pending",
                       "post_content" => $args["post_description"],
                       "post_type" => "success_story",
                       "post_author" => $loggedin_user->user_id
      );
    $newSuccessStory->addPost($post_data);
    $newSuccessStory->save();
    $newSuccessStory->updateGUID($newSuccessStory->id, 'success_story');
    $newSuccessStory->save();
    $newSuccessStory->add_post_translation($newSuccessStory->id, $args["lang"], "success_story");

    if(isset($_FILES["post_image"]) && !empty($_FILES["post_image"]['tmp_name']))
    {
      $img_args = array("post_status" => "inherit",
                         "post_type" => "attachment",
                         "post_author" => $loggedin_user->user_id
                      );
      $newSuccessStory->updateFeaturedImage($img_args, $newSuccessStory->id, $_FILES['post_image']); 
    }
    
    $termTax = new TermTaxonomy();
    $terms_data = array(
      "interest" => split(",",$args['interest']),
      "success_story_category" => array($args['category'])
    );
    $isCreated = $termTax->saveTermTaxonomies($terms_data, array());
    if(!$isCreated){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","terms"));
    } else {
      $newSuccessStory->updatePostTerms($newSuccessStory->id, $terms_data, true);
    }
  
    $returnMsg = Messages::getSuccessMessage("Success","Success Story Added");
    $returnMsg['success_story_id'] = $newSuccessStory->id;
    $returnMsg['is_first_suggestion'] = $is_first_suggestion;
    $returnMsg['is_pending_review'] = !($newSuccessStory->post_status == 'publish');
    
    return $this->renderJson($response, 200, $returnMsg);   
  }
  
  /**
   * @SWG\GET(
   *   path="/success-stories",
   *   tags={"Success Story"},
   *   summary="List Success Stories",
   *   description="List Success Stories",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfSuccessStories", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="category", in="query", required=false, type="string", description="Filter list of success stories by category <br/> <b>Values: </b> English or Arabic name or ID"), 
   *   @SWG\Response(response="200", description="List Success Stories"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listSuccessStories($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfSuccessStories', 'lang', 'category'];
    $requiredParams = ['pageNumber', 'numberOfSuccessStories', 'lang'];
    $numeric_params = ['pageNumber', 'numberOfSuccessStories'];
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
    if(empty($args['numberOfSuccessStories'] ) || $args['numberOfSuccessStories'] < 1 || $args['numberOfSuccessStories'] > 25){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of Success Stories',array("range"=> "1 and 25 ")));
    }
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    else{
      $post = new Post();
      $skip_number = ($args['pageNumber'] * $args['numberOfSuccessStories']) - $args['numberOfSuccessStories'] ;
      if ($args['pageNumber'] == 1){
        $skip_number = 0 ;
      }
      
      //get category id
      $category_id = '';
      if( isset( $args[ "category" ] ) ) {
        //check term exists
        $term_taxonomy = $this->check_term_exists($args['category'], 'success_story_category');
        if (empty($term_taxonomy)) {
            return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Category"));
        }
        $category_id = $term_taxonomy->term_id;
      }
           
      $list_success_stories = $post->getAllfilteringByCategory('success_story', 'publish', $args['numberOfSuccessStories'], $skip_number, $args["lang"], $category_id,'success_story_category');
      $success_story_result = array();
      if (count($list_success_stories) == 0){
        $result = $this->renderJson($response, 422, Messages::getErrorMessage("nothingToDisplay"));
      }
      else{
        $total_count = count($post->getAllfilteringByCategory('success_story', 'publish', -1, -1, $args["lang"], $category_id,'success_story_category'));
        $success_story_result = $this->ef_load_data_counts($total_count, $args['numberOfSuccessStories']);
        $index = 0;
        foreach ($list_success_stories as $success_story) {
            $success_story_id = $success_story->ID;
            $user = User::find($success_story->post_author);
            $user_name = $user->user_login;

            $post_meta = new Postmeta();
            $meta = $post_meta->getPostMeta($success_story_id);
            $success_story_meta = array();
            foreach ($meta as $meta_key => $meta_value ) {
              $success_story_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
            }
            unset($meta_value);
          
            //get thumbnail image
            $url = '';
            if(array_key_exists('_thumbnail_id', $success_story_meta))
            {
              $post_type = "attachment";
              $post_status = "inherit";
              $attachment_id = $success_story_meta['_thumbnail_id'];
              $success_story_image = Post::getPostByID($attachment_id, $post_type, $post_status);
              if($success_story_image){
                $url = $success_story_image->guid;  
              }
              
              if($url == '')
              {
                $option = new Option();
                $host = $option->getOptionValueByKey('siteurl');
                $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
              }else
              {
                //return thumbnail size in listing
                $url = $this->ef_image_sizes($url,'340x210');
              }
            }else
            {
              $option = new Option();
              $host = $option->getOptionValueByKey('siteurl');
              $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
            }
            
            //get category
            $category = "";
            if(array_key_exists('success_story_category', $success_story_meta))
            {
                $term = new Term();
            
                $cat = $success_story_meta['success_story_category'];

                if( ctype_digit( $cat ) ) {
                  $categories = array( $cat );
                }
                else {
                  $categories = unserialize( $success_story_meta['success_story_category'] );
                }

                if( !empty( $categories ) ) {
                    $category_id = $categories[ 0 ];

                    if($args["lang"] == "ar") {
                        if($term->getTerm($category_id)->name_ar != '')
                            $category = $term->getTerm($category_id)->name_ar;
                        else
                            $category = $term->getTerm($category_id)->name;
                    }
                    else {
                        $category = $term->getTerm($category_id)->name;
                    }
                }
            }
            
            //get interest
            $interests = array();
            if(array_key_exists('interest', $success_story_meta))
            {
                $interests_arr = unserialize($success_story_meta['interest']);
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
            
            $success_story_result['data'][$index] = array(
                "success_story_id"   => $success_story_id,
                "success_story_title" => $success_story->post_title,
                "description" => $success_story->post_content,
                "short_description" => self::shorten_description( $success_story->post_content ),
                "thumbnail" => $url,
                "category" => html_entity_decode($category),
                "interests" => $interests,
                "date" => $success_story->post_date,
                "added_by" => $this->return_user_info_list($success_story->post_author)
            );
            $index += 1;
        }
        unset($success_story);
        $result = $this->renderJson($response, 200, $success_story_result);
      }
      return $result;
    }
  }
  
   /**
   * @SWG\GET(
   *   path="/success-story/{successId}",
   *   tags={"Success Story"},
   *   summary="Finds a Success Story",
   *   description="Get success story data according to the passed success story id",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to display pending success story if exists"),
   *   @SWG\Parameter(name="successId", in="path", required=false, type="integer", description="Success Story ID to retrieve its details <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View a success story info"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Success Story not found")
   * )
   */
    public function viewSuccessStory($request, $response, $args) {
      $success_story = new Post();
      if(!isset($args["successId"]) || $args["successId"] == "" || $args["successId"] == "{successId}") 
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "successId"));
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
      
      // validate lang exists
      if(!isset($_GET["lang"]) || ($_GET["lang"] != "en" && $_GET["lang"] != "ar")) {
        return $this->renderJson($response, 200, Messages::getErrorMessage("missingValue"));
      }
      
      //retrieve success story by id
      $post_type = "success_story";
      //check if mine and pending
      if($user != null && !empty($user))
      {
        $post_status = "";
      }else {
        $post_status = "publish";
      }
      
      $success_story = $success_story->getPostByID($args['successId'], $post_type, $post_status);
      if ($success_story) { 
        if($user != null && !empty($user))
        {
          if($success_story->post_status == 'pending' && $success_story->post_author != $user->ID)
          {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Success Story ID"));
          }
        }else
        {
          if($success_story->post_status == 'pending')
          {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Success Story ID"));
          }
        }
        
        $user = User::find($success_story->post_author);
        $user_name = $user->user_login;
        
        $post_meta = new Postmeta();
        $meta = $post_meta->getPostMeta($success_story->ID);
        $success_story_meta = array();
        foreach ($meta as $meta_key => $meta_value ) {
          $success_story_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
        }

        //set thumbnail image
        $url = '';
        if(array_key_exists('_thumbnail_id', $success_story_meta))
        {
          $post_type = "attachment";
          $post_status = "inherit";
          $attachment_id = $success_story_meta['_thumbnail_id'];
          $success_story_image = Post::getPostByID($attachment_id, $post_type, $post_status);
          if($success_story_image){
            $url = $success_story_image->guid;
          }
          
          if($url == '')
          {
            $option = new Option();
            $host = $option->getOptionValueByKey('siteurl');
            $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
          }
        }else
        {
          $option = new Option();
          $host = $option->getOptionValueByKey('siteurl');
          $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";
        }
        
        //get language
//        $language = "en";
//        if(array_key_exists('language', $success_story_meta))
//        {
//          $lang = $success_story_meta['language'];
//          $lang_arr = unserialize(unserialize($lang));
//          $language = $lang_arr['slug'];
//          
//          if($language == null)
//          {
//            $language = "en";
//          }
//        }
//             
        $language = $_GET['lang'];
        
        //get category
        $category = "";
        if(array_key_exists('success_story_category', $success_story_meta))
        {
            $term = new Term();
            
            $cat = $success_story_meta['success_story_category'];
            
            if( ctype_digit( $cat ) ) {
              $categories = array( $cat );
            }
            else {
              $categories = unserialize( $success_story_meta['success_story_category'] );
            }
            
            if( !empty( $categories ) ) {
                $category_id = $categories[ 0 ];
                
                if($language == "ar") {
                    if($term->getTerm($category_id)->name_ar != '')
                        $category = $term->getTerm($category_id)->name_ar;
                    else
                        $category = $term->getTerm($category_id)->name;
                }
                else {
                    $category = $term->getTerm($category_id)->name;
                }
            }
        }

        //get interest
        $interests = array();
        if(array_key_exists('interest', $success_story_meta))
        {
            $interests_arr = unserialize($success_story_meta['interest']);
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

        $success_story_result = array(
            "ID" => $success_story->ID,
            "title" => $success_story->post_title,
            "status" => $success_story->post_status,
            "post_url" => html_entity_decode($success_story->guid),
            "description" => $success_story->post_content,
            "thumbnail" => $url,
            "category" => $category,
            "interests" => $interests,
            "date" => $success_story->post_date,
            "language" => $language,
            "added_by" => $this->return_user_info_list($success_story->post_author)
        );

        unset($success_story);
        return $this->renderJson($response, 200, $success_story_result);
      }

      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "Success Story ID"));
    }
    
    /**
    * @SWG\Post(
    *   path="/success-story/{successId}/comments",
    *   tags={"Success Story"},
    *   summary="Creates success story Comment",
    *   description=" Add success story commnent to the success story with the passed Id",
    *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new comment<br/> <b>[Required]</b>"),
    *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
    *   @SWG\Parameter(name="successId", in="formData", required=false, type="integer", description="Success Story ID to post the comment on <br/> <b>[Required]</b>"),
    *   @SWG\Response(response="200", description="Comment added successfully"),
    *   @SWG\Response(response="422", description="Validation Error"),
    *   @SWG\Response(response="404", description="Success story not found")
    * )
    */
    public function addSuccessStoryComments($request, $response, $args) {
      $result = "";
      $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
      if ($loggedin_user !== null) {
        $user_id = $loggedin_user->user_id;
        $user = User::find($user_id);
        if (empty($user)) 
        {
          $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
        }
        else 
        {
          // validate data
          $result = "";
          if (isset($_POST['comment']) || !empty($_POST['comment'])) {
            $meta['comment'] = $_POST['comment'];
          }
          else
          {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment"));
            return $result;
          }
          
          if(isset($_POST['successId']) || !empty($_POST['successId']) )
          {
            if (is_numeric($_POST['successId'])) {
              //we need to check if this post_name is in db or not
              $post = new Post();
              $post_type = "success_story";
              $post_status = "publish";
              $post_exists = $post->getPostByID($_POST['successId'], $post_type, $post_status);
              if ($post_exists) { 
                $meta['successId'] = $_POST['successId'];
                $meta['postID'] = $post_exists['ID'];
              }
              else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Success Story ID")); }
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Success Story ID")); }
          }
          else
          {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Success Story ID"));
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
   *   path="/success-story/{successId}/comments/{commentId}/replies",
   *   tags={"Success Story"},
   *   summary="Creates Reply On Success Story Comment",
   *   description="Add reply on the passed comment on the passed Success Story",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new reply<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="successId", in="formData", required=false, type="integer", description="Success Story ID <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="commentId", in="formData", required=false, type="integer", description="Comment ID to post the reply on <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Add reply to Comment")
   * )
   */
  public function addReplyToComment($request, $response, $args) {
    $result = "";
    $loggedin_user = isset($_POST['token']) ? (AccessToken::where('access_token', '=', $_POST['token'] )->first()) : null;
    $meta = array();
    if ($loggedin_user !== null) {
      $user_id = $loggedin_user->user_id;
      $user = User::find($user_id);
      if (empty($user)) 
      {
        $result = $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "User"));
      }
      else 
      {
        // validate data
        $result = "";
        if (isset($_POST['comment']) || !empty($_POST['comment'])) {
          $meta['comment'] = $_POST['comment'];
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment Reply"));
          return $result;
        }
        
        if(isset($_POST['successId']) || !empty($_POST['successId']) ){
          if (is_numeric($_POST['successId'])) {
//             we need to check if this post id is in db or not
            $post = new Post();
            $post_type = "success_story";
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['successId'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['successId'] = $_POST['successId'];
              $meta['postID'] = $post_exists['ID'];
            }
            
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Success Story ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Success Story ID")); }
        }
        else{
          return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Success Story ID"));
        }
        
        if(isset($_POST['commentId']) || !empty($_POST['commentId']) ){
          if (is_numeric($_POST['commentId'])) {
//             we need to check if this comment id is in comments table or not
            $comment_id = new Comment();
            $comment_exists = $comment_id->getCommentByID($_POST['commentId']);
            if (!empty($comment_exists) && ( $meta['successId'] == $comment_exists['comment_post_ID']) ) {
                if( empty( $comment_exists->comment_parent ) ) {
                    $meta['commentId'] = $_POST['commentId'];
                }
                else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notAllowed", "Multi level of comments")); }
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Comment ID is not in this Success Story or")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Comment ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment ID"));
        }
        // If no errors so far
        if ($result == "") {
          $comment = new Comment();
          $comment->addReplyToComment($user->ID, $user->user_login, $user->user_email, $meta['postID'], $meta['commentId'], $meta['comment'], $meta['successId']);
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
   *   path="/success-story/{successId}/comments",
   *   tags={"Success Story"},
   *   summary="List Comments and Replies On Success Story",
   *   description="list replies on the passed success stories",
   *   @SWG\Parameter(name="successId", in="path", required=false, type="integer", description="Success Story ID to list the comments related to this ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfComments", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List comments and replies to a success story"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Comments not found")
   * )
   */
  public function listSuccessStoryComments($request, $response, $args)
  {
        $successComments = new Comment();
        if(!isset($args["successId"]) || $args["successId"] == "" || $args["successId"] == "{successId}") 
        {
          return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue", "successId"));
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
      
      
      //List Success Story Comments
      $take = $args['numberOfComments'];
      $skip = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfComments'])-$args['numberOfComments']:0;
      $comments = $successComments->getCommentByPostID($args["successId"], $take, $skip);
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
          
          $comments = count($successComments->getCommentByPostID($args["successId"], -1, -1));
          $resulsts_final = $this->ef_load_data_counts($comments, $args['numberOfComments']);
          $resulsts_final['data'] = $result;
          
          return $this->renderJson($response, 200, $resulsts_final); 
      }
      
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
  }
  
   /**
   * @SWG\GET(
   *   path="/success-story/comments/{commentId}/replies",
   *   tags={"Success Story"},
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
  public function listCommentsonComments($comment_id, $take, $skip)
  {
      $successStoryComments = new Comment();
      $comments = $successStoryComments->getCommentByCommentID($comment_id, $take, $skip);
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
}

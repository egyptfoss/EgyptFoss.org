<?php

class WPNewsController extends EgyptFOSSController {
  /**
   * @SWG\Post(
   *   path="/news",
   *   tags={"News"},
   *   summary="Creates News",
   *   description="Create a new news with the passed data",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to create a new news<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_title", in="formData", required=false, type="string", description="News Title <br/> <b>Validations: </b><br/> 1. Length > 10 and < 100 <br/> 2. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_subtitle", in="formData", required=false, type="string", description="News Subtitle<br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/>"),
   *   @SWG\Parameter(name="post_description", in="formData", required=false, type="string", description="News Description <br/><b>Validations: </b><br/> 1. Contains at least 1 character <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="category", in="formData", required=false, type="string", description="News Category <br/><b>Validations: </b><br/> 1. Predefined Category in System <br/> <b>Values: </b> English or Arabic name or ID  <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_image", in="formData", required=false, type="file", description="News Featured Image <br/><b>Validations: </b><br/> 1. Valid Image format"),
   *   @SWG\Parameter(name="interest", in="formData", required=false, type="string", description="News Interests <br/><b>Values: </b> Multiple values with comma seperated between each value" ),
   *   @SWG\Parameter(name="lang", in="formData", required=false, type="string", description="Define News Language<br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="News added successfully"),
   *   @SWG\Response(response="404", description="Not Logged in User"),
   *   @SWG\Response(response="422", description="Validation Error")
   * )
   */
  public function addNews($request, $response, $args) {
    $parameters = ['token', 'post_title', 'post_subtitle', 'post_description', 'post_image', 'lang','interest','category']; 
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
    if(mb_strlen($args["post_title"],'UTF-8') < 10 || mb_strlen($args["post_title"],'UTF-8') > 100){
      return $this->renderJson($response, 422, Messages::getErrorMessage("length-between","title",array("range"=>"10 to 100 char")));
    }
     if(isset($_FILES["post_image"]) && !empty($_FILES["post_image"]['tmp_name']) && exif_imagetype($_FILES["post_image"]['tmp_name']) == FALSE ){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","image type"));
    }
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    
    //check category exists
    $term_taxonomy = $this->check_term_exists($args['category'], 'news_category');
    if (empty($term_taxonomy)) {
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Category"));
    }
    $args['category'] = html_entity_decode($term_taxonomy->name);
    //check interests
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
    
    $newNews = new Post();
    $post_data = array("post_title" => $args['post_title'],
                       "post_status" => "pending",
                       "post_type" => "news",
                       "post_author" => $loggedin_user->user_id
      );
    $newNews->addPost($post_data);
    $newNews->save();
    $newNews->updateGUID($newNews->id, 'news');
    $newNews->save();
    $newNews->add_post_translation($newNews->id, $args["lang"], "news");
    $newsMeta = new Postmeta();
    $newsMeta->updatePostMeta($newNews->id, 'description', $args["post_description"]);
    $newsMeta->updatePostMeta($newNews->id, 'subtitle', $args["post_subtitle"]);
    $newsMeta->updatePostMeta($newNews->id, 'is_news_featured_homepage', 0);
    
    if( isset( $_FILES['post_image'] ) ) {
      $img_args = array("post_status" => "inherit",
                         "post_type" => "attachment",
                         "post_author" => $loggedin_user->user_id
                      );
      $newNews->updateFeaturedImage($img_args, $newNews->id, $_FILES['post_image']); 
    }
    $termTax = new TermTaxonomy();
    $terms_data = array(
      "interest" => split(",",$args['interest']),
      "news_category" => array($args['category'])
    );
    $isCreated = $termTax->saveTermTaxonomies($terms_data, array("news_category"));
    if(!$isCreated){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","terms"));
    } else {
      $newNews->updatePostTerms($newNews->id, $terms_data, true, NULL, true);
    }
    
    $returnMsg = Messages::getSuccessMessage("Success","News Added");
    $returnMsg['news_id'] = $newNews->id;
    $returnMsg['is_first_suggestion'] = $is_first_suggestion;
    $returnMsg['is_pending_review'] = !($newNews->post_status == 'publish');
    
    return $this->renderJson($response, 200, $returnMsg);   
  }
  
  /**
   * @SWG\GET(
   *   path="/news",
   *   tags={"News"},
   *   summary="List News",
   *   description="List News with ability to paginate and filter by news category",
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfNews", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="lang", in="query", required=false, type="string", description="data retrieved depending on language field <br/> <b>Values: </b> en or ar <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="category", in="query", required=false, type="string", description="Filter list of news by category <br/> <b>Values: </b> ID"), 
   *   @SWG\Response(response="200", description="List News"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="User not found")
   * )
   */
  public function listNews($request, $response, $args){
    $parameters = ['pageNumber', 'numberOfNews', 'lang','category'];
    $requiredParams = ['pageNumber', 'numberOfNews', 'lang'];
    $numeric_params = ['pageNumber', 'numberOfNews'];
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
    if(empty($args['numberOfNews'] ) || $args['numberOfNews'] < 1 || $args['numberOfNews'] > 25){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of News',array("range"=> "1 and 25 ")));
    }
    if(!isset($args["lang"]) || ($args["lang"] != "en" && $args["lang"] != "ar")){
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrongValue","lang"));
    }
    else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
      $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
    }
    else{
      $post = new Post();
      $skip_number = ($args['pageNumber'] * $args['numberOfNews']) - $args['numberOfNews'] ;
      if ($args['pageNumber'] == 1){
        $skip_number = 0 ;
      }
      
      //get category id      
      if(!isset($args["category"]))
      {
        $category_id = '';
      }else
      {
        $category_id = $args[ "category" ];
      }
      
      $current_category_id = $category_id;
      $list_news = $post->getAllfilteringByCategory('news', 'publish', $args['numberOfNews'], $skip_number, $args["lang"], $category_id,'news_category');
      $news_result = array();
      if (count($list_news) == 0){
        $result = $this->renderJson($response, 422, Messages::getSuccessMessage("nothingToDisplay"));
      }
      else{
        foreach ($list_news as $news) {
          $news_id = $news->ID;
          $user = User::find($news->post_author);
          $user_name = $user->user_login;

          $post_meta = new Postmeta();
          $meta = $post_meta->getPostMeta($news_id);
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
            $category_id = -1;
            if(array_key_exists('news_category', $news_meta))
            {
                $term = new Term();
                $category_id = $news_meta['news_category'];
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
          $news_result[] = array(
            "news_id"   => $news->ID,
            "news_title" => $news->post_title,
            "subtitle" => (isset($news_meta['subtitle'])) ? $news_meta['subtitle'] : '',
            "description" => (isset($news_meta['description'])) ? $news_meta['description'] : '',
            "short_description" => (isset($news_meta['description'])) ? self::shorten_description( $news_meta['description'] ) : '',
            "thumbnail" => $url,
            "category" => $category,
            "interests" => $interests,              
            "date" => $news->post_date,
            "added_by" => $this->return_user_info_list($news->post_author)
          );
        }
        
        //return total count
        $total_count = count($post->getAllfilteringByCategory('news', 'publish', -1, -1, $args["lang"], $current_category_id,'news_category'));
        $news_result_final = $this->ef_load_data_counts($total_count,  $args['numberOfNews']);
        $news_result_final['data'] = $news_result;
        unset($news);
        $result = $this->renderJson($response, 200, $news_result_final);
      }
      return $result;
    }
  }

  /**
   * @SWG\GET(
   *   path="/news/{newsId}",
   *   tags={"News"},
   *   summary="Finds a News",
   *   description="Get news data according to the passed news id",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to display pending news if exists"),
   *   @SWG\Parameter(name="newsId", in="path", required=false, type="integer", description="News ID to retrieve its details <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="View a news info"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="News not found")
   * )
   */
    public function viewNews($request, $response, $args) {
      $news = new Post();
      if(!isset($args["newsId"]) || $args["newsId"] == "" || $args["newsId"] == "{newsId}") 
      {
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

      //retrieve news by id
      $post_type = "news";
      //check if mine and pending
      if($user != null && !empty($user))
      {
        $post_status = "";
      }else {
        $post_status = "publish";
      }
      $news = $news->getPostByID($args['newsId'], $post_type, $post_status);
      if ($news) { 
        if($user != null && !empty($user))
        {
          if($news->post_status == 'pending' && $news->post_author != $user->ID)
          {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "News ID"));
          }
        }else
        {
          if($news->post_status == 'pending')
          {
            return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "News ID"));
          }
        }
        
        $user = User::find($news->post_author);
        $user_name = $user->user_login;
        
        $post_meta = new Postmeta();
        $meta = $post_meta->getPostMeta($news->ID);
        $news_meta = array();
        foreach ($meta as $meta_key => $meta_value ) {
          $news_meta[$meta_value['meta_key']] = $meta_value['meta_value'];
        }
        
        //set thumbnail image
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
          }
        }else
        {
          $option = new Option();
          $host = $option->getOptionValueByKey('siteurl');
          $url = $host."/wp-content/themes/egyptfoss/img/empty_article_image.svg";     
        }
        
        //get language
        $language = "ar";
        if(array_key_exists('language', $news_meta))
        {
          $lang = $news_meta['language'];
          $lang_arr = unserialize(unserialize($lang));
          $language = $lang_arr['slug'];
        }
                
        //get category
        $category = "";
        $category_id = -1;
        if(array_key_exists('news_category', $news_meta))
        {
            $term = new Term();
            $category_id = $news_meta['news_category'];
            if($category_id != '')
            {
              if($language == "ar")
              {
                  if($term->getTerm($category_id)->name_ar != ''){
                      $category = $term->getTerm($category_id)->name_ar;
                  }
                  else{
                      $category = $term->getTerm($category_id)->name;
                  }
              }
              else
                  $category = $term->getTerm($category_id)->name;
            }
        }
        
        //get interest
        $interests = array();
        if(array_key_exists('interest', $news_meta))
        {
          $interests_arr = ctype_digit( $news_meta['interest'] )?array( $news_meta['interest'] ):unserialize($news_meta['interest']);
            
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

        $news_result = array(
          "news_id" => $news->ID  ,
          "news_title" => $news->post_title,
          "news_status" => $news->post_status,
          "post_url" => html_entity_decode($news->guid),
          "subtitle" => (isset($news_meta['subtitle'])) ? $news_meta['subtitle'] : '',
          "description" => (isset($news_meta['description'])) ? $news_meta['description'] : '',
          "thumbnail" => $url,
            "category" => array(
                'id'=>$category_id,
                'name'=>$category
            ),
            "interests" => $interests,            
          "date" => $news->post_date,
          "added_by" => $this->return_user_info_list($news->post_author)
        );

        unset($news);
        return $this->renderJson($response, 200, $news_result);
      }

      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "News ID"));
    }
    
    /**
    * @SWG\Post(
    *   path="/news/{newsId}/comments",
    *   tags={"News"},
    *   summary="Creates News Comment",
    *   description=" Add news commnent to the news with the passed Id",
    *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new comment<br/> <b>[Required]</b>"),
    *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
    *   @SWG\Parameter(name="newsId", in="formData", required=false, type="integer", description="News ID to post the comment on <br/> <b>[Required]</b>"),
    *   @SWG\Response(response="200", description="Comment added successfully"),
    *   @SWG\Response(response="422", description="Validation Error"),
    *   @SWG\Response(response="404", description="News not found")
    * )
    */
    public function addNewsComments($request, $response, $args) {
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
          
          if(isset($_POST['newsId']) || !empty($_POST['newsId']) )
          {
            if (is_numeric($_POST['newsId'])) {
              //we need to check if this post_name is in db or not
              $post = new Post();
              $post_type = "news";
              $post_status = "publish";
              $post_exists = $post->getPostByID($_POST['newsId'], $post_type, $post_status);
              if ($post_exists) { 
                $meta['newsId'] = $_POST['newsId'];
                $meta['postID'] = $post_exists['ID'];
              }
              else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "News ID")); }
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "News ID")); }
          }
          else
          {
            $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "News ID"));
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
   *   path="/news/{newsId}/comments/{commentId}/replies",
   *   tags={"News"},
   *   summary="Creates Reply On News Comment",
   *   description="Add reply on the passed comment on the passed news",
   *   @SWG\Parameter(name="token", in="formData", required=false, type="string", description="User token needed to add new reply<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="comment", in="formData", required=false, type="string", description="Comment Text <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="newsId", in="formData", required=false, type="integer", description="News ID <br/> <b>[Required]</b>"),
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

        if(isset($_POST['newsId']) || !empty($_POST['newsId']) ){
          if (is_numeric($_POST['newsId'])) {
//             we need to check if this post id is in db or not
            $post = new Post();
            $post_type = "news";
            $post_status = "publish";
            $post_exists = $post->getPostByID($_POST['newsId'], $post_type, $post_status);
            if ($post_exists) { 
              $meta['newsId'] = $_POST['newsId'];
              $meta['postID'] = $post_exists['ID'];
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "News ID")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "News ID")); }
        }
        else{
          return $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "News ID"));
        }
        
        if(isset($_POST['commentId']) || !empty($_POST['commentId']) ){
          if (is_numeric($_POST['commentId'])) {
//             we need to check if this comment id is in comments table or not
            $comment_id = new Comment();
            $comment_exists = $comment_id->getCommentByID($_POST['commentId']);
            if (!empty($comment_exists) && ( $meta['newsId'] == $comment_exists['comment_post_ID']) ) {
              if( empty( $comment_exists->comment_parent ) ) {
                    $meta['commentId'] = $_POST['commentId'];
              }
              else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notAllowed", "Multi level of comments")); }
            }
            else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("notFound", "Comment ID is not in this news or")); }
          }
          else{ $result = $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Comment ID")); }
        }
        else{
          $result = $this->renderJson($response, 422, Messages::getErrorMessage("emptyValue", "Comment ID"));
        }
        // If no errors so far
        if ($result == "") {
          $comment = new Comment();
          $comment->addReplyToComment($user->ID, $user->user_login, $user->user_email, $meta['postID'], $meta['commentId'], $meta['comment'], $meta['newsId']);
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
   *   path="/news/{newsId}/comments",
   *   tags={"News"},
   *   summary="List Comments and Replies On News",
   *   description="list replies on the passed news",
   *   @SWG\Parameter(name="newsId", in="path", required=false, type="integer", description="News ID to list the comments related to this ID<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="pageNumber", in="query", required=false, type="integer", description="retrieve lists of data according to page number <br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="numberOfComments", in="query", required=false, type="integer", description="Number of data to be retrieved with each page <br/> <b>Values: </b>  min 1 and max 25 <br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="List comments and replies to a News"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Comments not found")
   * )
   */
  public function listNewsComments($request, $response, $args)
  {
        $newsComments = new Comment();
        if(!isset($args["newsId"]) || $args["newsId"] == "" || $args["newsId"] == "{newsId}") 
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
          return $this->renderJson($response, 422, Messages::getErrorMessage("length-between",'Number of commments',array("range"=> "1 and 25 ")));
        }
        else if(empty($args['pageNumber'] ) || $args['pageNumber'] < 1 ){
          return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "Page number"));
        }
      
      
      //List News Comments
      $take = $args['numberOfComments'];
      $skip = (!empty($args['pageNumber']))?($args['pageNumber'] * $args['numberOfComments'])-$args['numberOfComments']:0;
      $comments = $newsComments->getCommentByPostID($args["newsId"], $take, $skip);
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
          
          //display total number of comments and total page numbers
          $comments = count($newsComments->getCommentByPostID($args["newsId"], -1, -1));
          $resulsts_final = $this->ef_load_data_counts($comments, $args['numberOfComments']);
          $resulsts_final['data'] = $result;
          return $this->renderJson($response, 200, $resulsts_final); 
      }
      
      return $this->renderJson($response, 200, Messages::getSuccessMessage("nothingToDisplay"));
  }
  
   /**
   * @SWG\GET(
   *   path="/news/comments/{commentId}/replies",
   *   tags={"News"},
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
          
          //display total number of replies and total page numbers
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
      $newsComments = new Comment();
      $comments = $newsComments->getCommentByCommentID($comment_id, $take, $skip);
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


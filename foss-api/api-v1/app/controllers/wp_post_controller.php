<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class WPPostController extends EgyptFOSSController {
   /**
   * @SWG\POST(
   *   path="/post/attachment/{post_type}/{post_id}/{attachment_type}",
   *   tags={"Post"},
   *   summary="Upload an attachment to a specific post",
   *   description="Upload attachment to a specific post by post type and attachment type",
   *   @SWG\Parameter(name="token", in="header", required=false, type="string", description="User token needed to add new attachment<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_type", in="path", required=false, type="integer", description="Type of the post<br/> <b>Values: </b> any of <br/> 1.news <br/>2.product<br/>3.success_story<br/>4.open_dataset<br/>5.service<br/>6.expert_thought<br/> <b>[Required]</b>"),
   *   @SWG\Parameter(name="post_id", in="path", required=false, type="integer", description="Post ID add attachment to<br/> <b>[Required]</b>"), 
   *   @SWG\Parameter(name="attachment_type", in="path", required=false, type="integer", description="Attachment Type<br/> <b>Values: </b> any of <br/> 1.logo <br/>2.screenshot<br/>[attachment type can be screenshot with product type only,otherwise send logo]<br/><b>[Required]</b>"),  
   *   @SWG\Parameter(name="attachment", in="formData", required=false, type="file", description="Attachment file to add<br/> <b>[Required]</b>"),
   *   @SWG\Response(response="200", description="Upload attachment successfully"),
   *   @SWG\Response(response="422", description="Validation Error"),
   *   @SWG\Response(response="404", description="Post not found")
   * )
   */
  public function addAttachment($request, $response, $args) {
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
    
    if(isset($_POST["post_type"]))
    {
      $args = $_POST;
    }
    
    //trim 
    $args["post_type"] = trim($args["post_type"]);
    $args["attachment_type"] = trim($args["attachment_type"]);
    
    if($args["post_type"] == "{post_type}" || $args["post_id"] == "{post_id}"
            || $args["attachment_type"] == "{attachment_type}}")
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    
    //validate file is added
    if(!isset($_FILES["attachment"]) || empty($_FILES["attachment"]['tmp_name'])){
      return $this->renderJson($response, 422, Messages::getErrorMessage("missingValue"));
    }
    
    //validate post type
    $post_types = array('news','product','open_dataset','success_story','expert_thought','service');
    if(!in_array($args["post_type"], $post_types) )
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "post_type"));
    }
    
    //validate attachment type
    $attachment_types = array('logo','screenshot');
    if(!in_array($args["attachment_type"], $attachment_types) )
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("wrong", "attachment_type"));
    }
    
    //validate post id exists
    $post = Post::where('ID', '=', $args["post_id"])
              ->where('post_type', '=', $args["post_type"])->first();
    if(!$post)
    {
      return $this->renderJson($response, 404, Messages::getErrorMessage("notFound", "post_id"));
    }
    
    // validate that current user has access to upload attachment to this post
    if($args["post_type"] != "product" && $user->ID != $post->post_author)
    {
      return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
    }
    
    if($args["post_type"] == "product")
    {
      //check if user can edit 
      $user_can_edit = User::userCanEditProduct($user->ID, $post->ID);
      if(!$user_can_edit)
      {
        return $this->renderJson($response, 422, Messages::getErrorMessage("unauthorized"));
      } 
      
      if(isset($_FILES["attachment"]) && !empty($_FILES["attachment"]['tmp_name']) 
              && exif_imagetype($_FILES["attachment"]['tmp_name']) == FALSE ){
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","attachment"));
      }
      
      $attachment_args = array("post_status" => "inherit",
        "post_type" => "attachment",
        "post_author" => $user->ID
      );
      
      if($args["attachment_type"] == "logo")
      {
        $post->updateFeaturedImage($attachment_args, $post->ID, $_FILES['attachment']);  
      }else {
        $attachment_id = trim($post->uploadProductScreenshots($attachment_args, $post->ID, $_FILES,"attachment"),","); 
        $Postmeta = new Postmeta();
        $meta = $Postmeta::getProductMeta($post->ID);
        $screenshot_ids = $meta["fg_perm_metadata"];
        if(strlen($screenshot_ids) == 0) 
        {
          $screenshot_ids = $attachment_id;
        }
        else 
        {
          $screenshot_ids .= ",".$attachment_id;
        }
        
        $Postmeta->updatePostMeta($post->ID, "fg_perm_metadata", $screenshot_ids);
        $Postmeta->updatePostMeta($post->ID, "fg_temp_metadata", $screenshot_ids);
      }
      return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","Attachment uploaded"));
    } else if ($args["post_type"] == "news" 
            || $args["post_type"] == "success_story"
            || $args["post_type"] == "expert_thought"
            || $args["post_type"] == "service") {
      if(isset($_FILES["attachment"]) && !empty($_FILES["attachment"]['tmp_name']) 
              && exif_imagetype($_FILES["attachment"]['tmp_name']) == FALSE ){
        return $this->renderJson($response, 422, Messages::getErrorMessage("wrong","attachment"));
      }
      
      $img_args = array("post_status" => "inherit",
                       "post_type" => "attachment",
                       "post_author" => $user->ID
                    );
      $post->updateFeaturedImage($img_args, $post->ID, $_FILES['attachment']); 
      return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","Attachment uploaded"));
    } else if($args["post_type"] == "open_dataset") {
      $open_dataset = new WPOpenDataSetController();
      $extensions = $open_dataset->validExtensions();
      $filesize = $open_dataset::$filesize;//20MB 
      $files = $_FILES["attachment"]; 
      if(sizeof($_FILES["attachment"]['tmp_name'] ) >= 1)
      {    
        if($files['name'] != "")
        {
          $size = $files['size'];
          $array = explode('.', $files['name']);
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
      
      $resources_args = array("post_status" => "inherit",
        "post_type" => "attachment",
        "post_author" => $user->ID
      );
      $dataset_meta = new Postmeta();
      $data = $dataset_meta->getOpenDatasetMeta($post->ID);
      $resources = $data['resources'];
      $rsc_index = $resources;
      $formats_type = $data['dataset_formats'].'|||';
      $resources_ids = $data['resources_ids'].'|||';
      if (($files['name'] != "")) 
      {
        $fileName = str_replace(" ", "-", urldecode( $files["name"] ) );
        $year = date('Y');
        $month = date('m');
        $home_url =   $seed = Option::limit(1)->Where('option_name', '=', "home")->first();
        $resources_args['guid'] = $home_url->option_value."/wp-content/uploads/{$year}/{$month}/".$fileName;
        preg_match("/(\.)+\w+$/", $files['name'], $extention);
        $resources_args['post_title'] = str_replace($extention[0], '', $files['name']);
        $path = __DIR__;
        for ($d = 1; $d <= 4; $d++)
            $path = dirname($path);

        $uploaddir = $path . "/wp-content/uploads/{$year}/{$month}/";
        if (!file_exists($uploaddir)) {
          mkdir($uploaddir, 0777, true); 
        }
        $uploadfile = $uploaddir . ($fileName);
        if (move_uploaded_file($files['tmp_name'], $uploadfile)) {
          $attachment = new Post();
          $attachment->addPost($resources_args);
          $attachment->post_mime_type = $files['type'];
          $formats_type = $formats_type.$files['type'].'|||';
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

          $Postmeta->updatePostMeta($post->ID, "resources_".$rsc_index."_upload", $attachment->id);
          $Postmeta->updatePostMeta($post->ID, "resources_".$rsc_index."_resource_status", 'publish');
        }
        $rsc_index = $rsc_index + 1;
      }
      
      //update formats post meta
      $formats_type = substr($formats_type, 0, -3);
      $resources_ids = substr($resources_ids, 0, -3);
      $openDataSetMeta = new Postmeta();
      $openDataSetMeta->updatePostMeta($post->ID, 'resources', $rsc_index);
      $openDataSetMeta->updatePostMeta($post->ID, 'dataset_formats', $formats_type); 
      $openDataSetMeta->updatePostMeta($post->ID, 'resources_ids', $resources_ids); 
      
      return $this->renderJson($response, 200, Messages::getSuccessMessage("Success","Attachment uploaded"));
    }
  }
}
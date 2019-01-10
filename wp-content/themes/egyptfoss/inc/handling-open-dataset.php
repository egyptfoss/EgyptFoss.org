<?php

define("ef_open_dataset_per_page", 10);
define("ef_max_file_size", 20971520); //20MB
function ef_add_open_dataset_front_end() {
    $ef_open_dataset_messages = array("errors" => array());
    $errors = checkOpenDatasetFrontendValidation();
    $ef_open_dataset_messages["errors"] = $errors;
    if($ef_open_dataset_messages["errors"])
    {
      set_query_var("ef_open_dataset_messages", $ef_open_dataset_messages);
      return false;
    }
    
    $_POST["open_dataset_description"] = strip_js_tags($_POST["open_dataset_description"]);
    $_POST["open_dataset_usage"] = strip_js_tags($_POST["open_dataset_usage"]);
    $_POST["open_dataset_references"] = strip_js_tags($_POST["open_dataset_references"]);
    
    $my_post = array(
      'post_title' => $_POST["open_dataset_title"],
      'post_content' => ($_POST["open_dataset_description"]),
      'post_author' => get_current_user_id(),
      'post_type' => 'open_dataset',
      'post_status' => 'pending'
    );
    $post_id = wp_insert_post($my_post);

    //save post meta
    update_post_meta($post_id, 'publisher', $_POST["open_dataset_publisher"]);
    update_post_meta($post_id, 'description', $_POST["open_dataset_description"]);
    update_post_meta($post_id, 'usage_hints', $_POST["open_dataset_usage"]);
    update_post_meta($post_id, 'references', $_POST["open_dataset_references"]);
    update_post_meta($post_id, 'source_link', $_POST["open_dataset_source"]);
    update_field( 'published_date', $_POST["open_dateset_published_date"], $post_id );
    
    $taxonomies = array('interest','type','theme','license');
    foreach ($taxonomies as $taxonomy){ 
        if(isset($_POST[$taxonomy]))
        {
            $value = $_POST[$taxonomy];
            if($taxonomy == 'license')
            {   
                $taxonomy = 'datasets_license';
            }else if($taxonomy == 'type')
            {
                $taxonomy = 'dataset_type';   
            }
            $meta[$taxonomy] = $value; 
            $term_ids = wp_set_object_terms($post_id, $meta[$taxonomy], $taxonomy);
            $return_arr = getTermFromTermTaxonomy($term_ids);
            if(sizeof($return_arr) == 1 && ($taxonomy != 'interest')){
               $return_arr = $return_arr[0]; 
            }
            update_post_meta($post_id, $taxonomy, $return_arr);
        }
    }
    
    $formats_open_dataset = ef_handling_resources($post_id);
    
    //save formats in post meta
    update_post_meta($post_id, 'dataset_formats', $formats_open_dataset);
    
    //current language of the post
    $current_lang = pll_current_language();
    wp_set_object_terms($post_id, $current_lang, 'language');
    update_post_meta($post_id, 'language', serialize(array(
      "slug" => $current_lang,
      "translated_id" => 0))
    );
    
    set_query_var("ef_open_dataset_messages", array("success"=> array(__("Open DataSet","egyptfoss"). ' ' . __("added successfully, it is now under review","egyptfoss"))));
    
    return $post_id;
}

function checkOpenDatasetFrontendValidation() {
  $errors = array();
  global $extensions;
  if (mb_strlen($_POST['open_dataset_title'],'UTF-8') == 0) {
    $errors["title"] = __("Title","egyptfoss"). ' ' . __("is required",'egyptfoss');
  }else
  {
    if (mb_strlen($_POST['open_dataset_title'],'UTF-8') > 100 && mb_strlen($_POST['open_dataset_title'],'UTF-8') != 0) {
      $errors["title"] = __("Title","egyptfoss"). ' ' . sprintf(__("should not be more than %d characters",'egyptfoss'),100);
    }

    if (mb_strlen($_POST['open_dataset_title'],'UTF-8') < 10 && mb_strlen($_POST['open_dataset_title'],'UTF-8') != 0) {
      $errors["title"] = __("Title","egyptfoss"). ' ' . sprintf(__("should be at least %d characters",'egyptfoss'),10);
    }
  }
  
    //check unqiue dataset title
    global $wpdb;
    $title = $_POST['open_dataset_title'];
    $titleExists = $wpdb->get_col("select ID from $wpdb->posts where (post_status = 'publish' or post_status = 'pending') and post_type = 'open_dataset' and post_title = '" . $title . "' ");
    if (!empty($titleExists)) {
      $errors['title'] = __("Title","egyptfoss").' '. __('already exists',"egyptfoss");
    }
  
  $description = $_POST['open_dataset_description'];
  $description = strip_tags($description);
  if (empty($description)) {
    $errors["desc"] = __("Description","egyptfoss"). ' ' . __("is required",'egyptfoss');
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = __("Description","egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
    }
  }
  
    if(!isset($_POST['open_dataset_publisher']))
    {
      $errors["publisher"] = __("Publisher","egyptfoss"). ' ' . __("is required",'egyptfoss');  
    }
  
  if(!isset($_POST['type']))
  {
    $errors["type"] = __("Type","egyptfoss"). ' ' . __("is required",'egyptfoss');  
  }
  
  if(!isset($_POST['theme']))
  {
    $errors["theme"] = __("Theme","egyptfoss"). ' ' . __("is required",'egyptfoss');  
  }
  
  if(!isset($_POST['license']))
  {
    $errors["license"] = __("License","egyptfoss"). ' ' . __("is required",'egyptfoss');  
  }
    
  if(sizeof($_FILES["open_dataset_resources"]['tmp_name'] ) >= 1)
  {
    $files = $_FILES["open_dataset_resources"];
    
    // check files against viruses before complete uploading proccess
    foreach( $files['tmp_name'] as $tmp_file ) {
      if( !empty( $tmp_file ) ) {
        $retcode = cl_scanfile($tmp_file, $virusname);
        if ($retcode == CL_VIRUS) {
            $infected = true;
            break;
        }
      }
      }

    if( $infected ) {
      $errors["resources"] = __("Virus detected! , Uploading proccess has been terminated.","egyptfoss");
    }
    else {
      foreach ($files['name'] as $key => $value) {
        if (($files['name'][$key] != ""))  {
          $size = $files['size'][$key];
          $array = explode('.', $files['name'][$key]);
          $extension = end($array);
          $type = strtolower($extension);//$files['type'][$key];

          if($size > constant("ef_max_file_size")) {
            $errors["resources"] = __("One or more of resources exceeded the max file size: 20MB","egyptfoss");
            break;
          }

          if(!in_array($type, $extensions)) {
            $errors["resources"] = sprintf(__("One or more of resources have invalid file type, please add resources to the following types: %s","egyptfoss"),  implode(',', $extensions));
            break;
          }
        }
      }
    }
  }
    
    if(!isset($_POST['open_dataset_references']))
    {
      $errors["references"] = __("References","egyptfoss"). ' ' . __("are required",'egyptfoss');  
    }else {
        $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST['open_dataset_references']);
        $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST['open_dataset_references']);

        if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
          $errors["desc"] = __("References","egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
        }
    }
    
    if(!isset($_POST['open_dataset_references']))
    {
      $errors["references"] = __("References","egyptfoss"). ' ' . __("are required",'egyptfoss');  
    }else {
        $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST['open_dataset_references']);
        $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST['open_dataset_references']);

        if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
          $errors["ref_desc"] = __("References","egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
        }
    }
  
    if(isset($_POST['open_dataset_usage']) && !empty($_POST['open_dataset_usage']))
    {
        $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST['open_dataset_usage']);
        $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST['open_dataset_usage']);

        if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
          $errors["usage_hints"] = __("Usage hints","egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
        }
    }
  
    return $errors;
}

function checkOpenDatasetResourcesFrontendValidation() {
  $errors = array();
  global $extensions;
  $description = $_POST['open_dataset_description'];
  $description = strip_tags($description);
  if (empty($description)) {
    $errors["desc"] = __("Description","egyptfoss"). ' ' . __("is required",'egyptfoss');
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = __("Description","egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
    }
  }
  
  //check the resources
  if(sizeof($_FILES["open_dataset_resources"]['tmp_name'] ) == 1 &&
          $_FILES["open_dataset_resources"][0]['tmp_name'] == '' )
  {
      $errors["resources"] = __("Resources","egyptfoss"). ' ' . __("are required",'egyptfoss');
  }
    
  if(sizeof($_FILES["open_dataset_resources"]['tmp_name'] ) >= 1)
  {
    $files = $_FILES["open_dataset_resources"]; 
    foreach ($files['name'] as $key => $value) {
        if (($files['name'][$key] != "")) 
        {
            $size = $files['size'][$key];
            $array = explode('.', $files['name'][$key]);
            $extension = end($array);
            $type = strtolower($extension);//$files['type'][$key];

            if($size > constant("ef_max_file_size"))
            {
                $errors["resources"] = __("One or more of resources exceeded the max file size: 20MB","egyptfoss");
                break;
            }
            
            if(!in_array($type, $extensions))
            {
                $errors["resources"] = sprintf(__("One or more of resources have invalid file type, please add resources to the following types: %s","egyptfoss"),  implode(',', $extensions));
                break;
            }
        }
    }
  }
    
  return $errors;
}

function ef_handling_resources($post_id, $description = null, $resource_status = 'publish', $file_indx = 0)
{
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    
    $formats_type = "";
    $resources_ids = "";
    
    if ($_FILES["open_dataset_resources"] && isset($_POST['open_dataset_resources_nonce'], $post_id) && wp_verify_nonce($_POST['open_dataset_resources_nonce'], 'open_dataset_resources')) {
      $files = $_FILES["open_dataset_resources"]; 
      $file_index = $file_indx;
      foreach ($files['name'] as $key => $value) {
        if (($files['name'][$key] != "")) {
          $file = array(
            'name' => $files['name'][$key],
            'type' => $files['type'][$key],
            'tmp_name' => $files['tmp_name'][$key],
            'error' => $files['error'][$key],
            'size' => $files['size'][$key]
          );
          $formats_type = $formats_type.$files['type'][$key].'|||';
          $_FILES = array("files" => $file);
          foreach ($_FILES as $file => $array) {
            $newupload = media_handle_upload($file, $post_id);
            
            // Update Parent Post
            if($description != null) {
              $my_post = array(
                  'ID'           => $newupload,
                  'post_parent' => $post_id,
                  'post_content' => $description
              );
              // Update the post into the database
              wp_update_post( $my_post );
            }else
            {
              $my_post = array(
                  'ID'           => $newupload,
                  'post_parent' => $post_id
              );
              // Update the post into the database
              wp_update_post( $my_post );
            }
            
            $meta_name = "resources_".$file_index."_upload";
            $meta_name_status = "resources_".$file_index."_resource_status";
            update_post_meta($post_id, $meta_name, $newupload);
            //set resource status to publish
            update_post_meta($post_id, $meta_name_status, $resource_status);
            $resources_ids = $resources_ids.$newupload.'|||';
            $file_index += 1;
          }
        }
    }
    
    //Save resources id in post meta
    if($resource_status == 'publish')
    {
      update_post_meta($post_id, 'resources_ids', substr($resources_ids, 0, -3));
    }
    
    //update post meta for number of resources
    update_post_meta($post_id, 'resources', $file_index);
  }
  
  // get uploads directory info
  $uploads = wp_upload_dir();
  
  // check if zip file for this post is created
  if( file_exists( $uploads['basedir'] . '/zip-files/open-dataset-' . $post_id . '.zip' ) ) {
    // remove created zip file
    unlink( $uploads['basedir'] . '/zip-files/open-dataset-' . $post_id . '.zip' );
  }
  
  return substr($formats_type, 0, -3);
}

function ef_add_resrouces_open_dataset_front_end($dataset_id) {
  //upload resources
  $ef_open_dataset_messages = array("errors" => array());
  $errors = checkOpenDatasetResourcesFrontendValidation();
  $ef_open_dataset_messages["errors"] = $errors;
  if($ef_open_dataset_messages["errors"])
  {
    set_query_var("ef_open_dataset_messages", $ef_open_dataset_messages);
    return false;
  }

  $_POST["open_dataset_description"] = strip_js_tags($_POST["open_dataset_description"]); 
  $infected = false;
  //load number of resources to add on 
  $total_resources = get_post_meta($dataset_id, 'resources', true);
  $postStatus = get_post_status($dataset_id);
  if( isset( $_FILES['open_dataset_resources'] ) ) {
    // check files against viruses before complete uploading proccess
    foreach( $_FILES['open_dataset_resources']['tmp_name'] as $tmp_file ) {
      if( !empty( $tmp_file ) ) {
        $retcode = cl_scanfile($tmp_file, $virusname);
        if ($retcode == CL_VIRUS) {
            $infected = true;
            break;
        }
      }
    }
  }
  
  if( $infected ) {
    setMessageBySession("ef_dataset_messages","warning",__("Virus detected! , Uploading proccess has been terminated.","egyptfoss"));
  }
  else {
    if($postStatus == "publish")
    {
      $formats_open_dataset = ef_handling_resources($dataset_id, $_POST["open_dataset_description"],'pending', $total_resources);
    }else
    {
      $formats_open_dataset = ef_handling_resources($dataset_id, $_POST["open_dataset_description"],'publish', $total_resources);
    }
    setMessageBySession("ef_open_dataset_resources_add_messages","success",__("Open Dataset Resources added successfully, it is now under review","egyptfoss"));
  }
  //save formats in post meta
  //update_post_meta($dataset_id, 'dataset_formats', $formats_open_dataset);
  
  return $dataset_id;
}

function ef_count_open_dataset($args = array()){
    global $wpdb;    
    global $extensions;
    global $extension_mime_types;
    if($args['theme_id'] == -1)
    {
        $join_condition = "";
        $join_condition_where = "";
    }else{
        $join_condition = returnJoin('theme');
        $join_condition_where = returnWhereCondition('theme', $args);
    }
    
    if($args['type_id'] == -1)
    {
        $join_condition_type = "";
        $join_condition_type_where = "";
    }else{
        $join_condition_type = returnJoin('type');
        $join_condition_type_where = returnWhereCondition('type', $args);
    }
    
    if($args['license_id'] == -1)
    {
        $join_condition_license = "";
        $join_condition_license_where = "";
    }else{
        $join_condition_license = returnJoin('license');
        $join_condition_license_where = returnWhereCondition('license', $args);
    }
    
    if($args['format'] == -1)
    {
        $join_condition_format = "";
        $join_condition_format_where = "";
    }else{
        $join_condition_format = returnJoin('format');
        $join_condition_format_where = returnWhereCondition('format', $args);
    }
    
    //check publisher
    if($args['publisher'] == -1)
    {
        $join_condition_publisher = "";
        $join_condition_publisher_where = "";
    }else{
        $join_condition_publisher = returnJoin('publisher');
        $join_condition_publisher_where = returnWhereCondition('publisher', $args);
    }
    
    $sql = "SELECT post.ID
            FROM {$wpdb->prefix}posts as post
            join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
            $join_condition
            $join_condition_type
            $join_condition_license
            $join_condition_format
            $join_condition_publisher
            where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') 
            $join_condition_where
            $join_condition_type_where
            $join_condition_license_where
            $join_condition_publisher_where";
            
    if($join_condition_publisher != "") {
      $query = $wpdb->prepare($sql, rawurldecode($args['publisher']));
    }
    else{
      $query = $sql;  
    }
            
    // append on $query
    $query .= " and (pmeta.meta_key = 'language' and 
            (pmeta.meta_value like '%\"{$args['current_lang']}\"%' or (
            pmeta.meta_value like '%\"slug\";s:2:\"{$args['foriegn_lang']}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))"
            . " $join_condition_format_where"
            . " group by post.ID
            order by post.post_date DESC";
    $results = $wpdb->get_results($query);
    return count($results);
}

function ef_count_open_dataset_ajax(){
    global $wpdb;
    global $extensions;
    global $extension_mime_types;
    $lang = pll_current_language();
    $args = array(
      "post_status" => "publish",
      "post_type" => "open_dataset",
      "current_lang" => $lang,
      "foriegn_lang" => ($lang == "ar")?"en":"ar",
      "offset" => 0
    );
    
    if(isset($_POST['theme']))
    {
        if(!is_numeric($_POST['theme']))
        {
            $args['theme_id'] = -1;
        }else{
            $args['theme_id'] = $_POST['theme'];
        }
        set_query_var('ef_listing_dataset_theme_id', $args['theme_id']);
    }
    
    //check type
    if(isset($_POST['dataset_type']))
    {
        if(!is_numeric($_POST['dataset_type']))
        {
            $args['type_id'] = -1;
        }else{
            $args['type_id'] = $_POST['dataset_type'];
        }
        set_query_var('ef_listing_dataset_type_id', $args['type_id']);
    }
    
    //check license
    if(isset($_POST['dataset_license']))
    {
        if(!is_numeric($_POST['dataset_license']))
        {
            $args['license_id'] = -1;
        }else{
            $args['license_id'] = $_POST['dataset_license'];
        }
        set_query_var('ef_listing_dataset_license_id', $args['license_id']);
    }
    
    //check formats
    if(isset($_POST['dataset_formats']))
    {
        if(!in_array($_POST['dataset_formats'], $extensions))
        {
            $args['format'] = -1;
        }else
        {
            $args['format'] = $_POST['dataset_formats'];
        }
        set_query_var('ef_listing_dataset_format', $args['format']);
    }
    
    //check publisher
    if(isset($_POST['dataset_publisher']) && !empty($_POST['dataset_publisher']))
    {
        $args['publisher'] = $_POST['dataset_publisher'];
    }else
    {
      $args['publisher'] = -1;
    }
    set_query_var('ef_listing_dataset_publisher', $args['publisher']);
    
    if($args['format'] == -1)
    {
        $join_condition_format = "";
        $join_condition_format_where = "";
    }else{
        $join_condition_format = returnJoin('format');
        $join_condition_format_where = returnWhereCondition('format', $args);
    }

    
    if($args['theme_id'] == -1)
    {
        $join_condition = "";
        $join_condition_where = "";
    }else{
        $join_condition = returnJoin('theme');
        $join_condition_where = returnWhereCondition('theme', $args);
    }
    
    if($args['type_id'] == -1)
    {
        $join_condition_type = "";
        $join_condition_type_where = "";
    }else{
        $join_condition_type = returnJoin('type');
        $join_condition_type_where = returnWhereCondition('type', $args);
    }
    
    if($args['license_id'] == -1)
    {
        $join_condition_license = "";
        $join_condition_license_where = "";
    }else{
        $join_condition_license = returnJoin('license');
        $join_condition_license_where = returnWhereCondition('license', $args);
    }
    
    if($args['publisher'] == -1)
    {
        $join_condition_publisher = "";
        $join_condition_publisher_where = "";
    }else{
        $join_condition_publisher = returnJoin('publisher');
        $join_condition_publisher_where = returnWhereCondition('publisher', $args);
    }
    
    $sql = "SELECT post.ID
            FROM {$wpdb->prefix}posts as post
            join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
            $join_condition
            $join_condition_type
            $join_condition_license
            $join_condition_format
            $join_condition_publisher
            where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') 
            $join_condition_where
            $join_condition_type_where
            $join_condition_license_where
            $join_condition_publisher_where";
       
    if($join_condition_publisher != "") {
      $query = $wpdb->prepare($sql, rawurldecode($args['publisher']));
    } else {
      $query = $sql;
    }
    // append on $query
    $query .= " and (pmeta.meta_key = 'language' and 
            (pmeta.meta_value like '%\"{$args['current_lang']}\"%' or (
            pmeta.meta_value like '%\"slug\";s:2:\"{$args['foriegn_lang']}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))"
            . " $join_condition_format_where"
            . " group by post.ID
            order by post.post_date DESC";
    
    $results = $wpdb->get_results($query);
    echo count($results);
    die();
}
add_action('wp_ajax_ef_count_open_dataset_ajax', 'ef_count_open_dataset_ajax');
add_action('wp_ajax_nopriv_ef_count_open_dataset_ajax', 'ef_count_open_dataset_ajax');


function get_datasets($args = array()){
    global $extensions;
    global $extension_mime_types;
    wp_enqueue_script( 'listing_open_dataset-js', get_stylesheet_directory_uri() . '/js/listing_open_dataset.js', array('jquery'), '', true);
    wp_localize_script('listing_open_dataset-js', 'ef_open_dataset', array("per_page" => constant("ef_open_dataset_per_page")));
    
    global $wpdb;
    $no_of_posts = constant("ef_open_dataset_per_page");
    $args['offset'] = (get_query_var("ef_listing_datasets_offset") ? get_query_var("ef_listing_datasets_offset") : 0);
    
    //check theme
    if(!is_numeric($args['theme_id']))
    {
        $args['theme_id'] = -1;
    }
    
    if($args['theme_id'] == -1)
    {
        $join_condition = "";
        $join_condition_where = "";
    }else{
        $join_condition = returnJoin('theme');
        $join_condition_where = returnWhereCondition('theme', $args);
    }
    
    //check type
    if(!is_numeric($args['type_id']))
    {
        $args['type_id'] = -1;
    }
    
    if($args['type_id'] == -1)
    {
        $join_condition_type = "";
        $join_condition_type_where = "";
    }else{
        $join_condition_type = returnJoin('type');
        $join_condition_type_where = returnWhereCondition('type', $args);
    }

    //check license
    if(!is_numeric($args['license_id']))
    {
        $args['license_id'] = -1;
    }
    
    if($args['license_id'] == -1)
    {
        $join_condition_license = "";
        $join_condition_license_where = "";
    }else{
        $join_condition_license = returnJoin('license');
        $join_condition_license_where = returnWhereCondition('license', $args);
    }
    
    //check formats
    if(!in_array(strtolower($args['format']), $extensions))
    {
        $args['format'] = -1;
    }
    
    if($args['format'] == -1)
    {
        $join_condition_format = "";
        $join_condition_format_where = "";
    }else{
        $join_condition_format = returnJoin('format');
        $join_condition_format_where = returnWhereCondition('format', $args);
    }
    
    //check publisher
    if($args['publisher'] == -1)
    {
        $join_condition_publisher = "";
        $join_condition_publisher_where = "";
    }else{
        $join_condition_publisher = returnJoin('publisher');
        $join_condition_publisher_where = returnWhereCondition('publisher', $args);
    }
    
    $sql = "SELECT post.ID,post.post_author,post.post_date,post.post_content,post.post_title,post.post_name,post.guid
            FROM {$wpdb->prefix}posts as post
            join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
            $join_condition
            $join_condition_type
            $join_condition_license
            $join_condition_format
            $join_condition_publisher
            where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') 
            $join_condition_where
            $join_condition_type_where    
            $join_condition_license_where
            $join_condition_publisher_where";
            
    if($join_condition_publisher != "") {
      $query = $wpdb->prepare($sql, rawurldecode($args['publisher']));
    } else {
      $query = $sql;
    }

    // append on $query
    $query .= " and (pmeta.meta_key = 'language' and 
            (pmeta.meta_value like '%\"{$args['current_lang']}\"%' or (
            pmeta.meta_value like '%\"slug\";s:2:\"{$args['foriegn_lang']}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))"
            . " $join_condition_format_where"
            . " group by post.ID
            order by post.post_date DESC
            limit {$args['offset']}, {$no_of_posts}";
    
    $results = $wpdb->get_results($query);
    return $results;
}

// --- view more for listing datasets --- //
function ef_load_more_listing_open_dataset() {
    global $extensions;
    set_query_var('ef_listing_datasets_offset', $_POST['offset']);
  
    //check theme
    if(isset($_POST['theme']))
    {
        if(!is_numeric($_POST['theme']))
        {
            set_query_var('ef_listing_dataset_theme_id', -1);
        }else
        {
          set_query_var('ef_listing_dataset_theme_id', $_POST['theme']);
        }
    }
    else{
        set_query_var('ef_listing_dataset_theme_id', -1);
    }
  
    //check type
    if(isset($_POST['dataset_type']))
    {
        if(!is_numeric($_POST['dataset_type']))
        {
            set_query_var('ef_listing_dataset_type_id', -1);
        }else
        {
          set_query_var('ef_listing_dataset_type_id', $_POST['dataset_type']);
        }
    }
    else{
        set_query_var('ef_listing_dataset_type_id', -1);
    }
  
    //check license
    if(isset($_POST['dataset_license']))
    {
        if(!is_numeric($_POST['dataset_license']))
        {
            set_query_var('ef_listing_dataset_license_id', -1);
        }else
        {
          set_query_var('ef_listing_dataset_license_id', $_POST['dataset_license']);
        }
    }
    else{
        set_query_var('ef_listing_dataset_license_id', -1);
    }

    //check formats
    if(isset($_POST['dataset_formats']))
    {
        if(!in_array($_POST['dataset_formats'], $extensions))
        {
            set_query_var('ef_listing_dataset_format', -1);
        }else
        {
          set_query_var('ef_listing_dataset_format', $_POST['dataset_formats']);
        }
    }
    else{
        set_query_var('ef_listing_dataset_format', -1);
    }

    get_template_part('template-parts/content', 'listing_datasets');
    die();
}
add_action('wp_ajax_ef_load_more_listing_open_dataset', 'ef_load_more_listing_open_dataset');
add_action('wp_ajax_nopriv_ef_load_more_listing_open_dataset', 'ef_load_more_listing_open_dataset');

function ef_load_change_theme_open_dataset() {
    global $extensions;
    set_query_var('ef_listing_datasets_offset', 0);

    //check theme
    if(isset($_POST['theme']))
    {
        if(!is_numeric($_POST['theme']))
        {
            set_query_var('ef_listing_dataset_theme_id', -1);
        }else
        {
          set_query_var('ef_listing_dataset_theme_id', $_POST['theme']);
        }
    }
    else{
        set_query_var('ef_listing_dataset_theme_id', -1);
    }

    //check type
    if(isset($_POST['dataset_type']))
    {
        if(!is_numeric($_POST['dataset_type']))
        {
            set_query_var('ef_listing_dataset_type_id', -1);
        }else
        {
          set_query_var('ef_listing_dataset_type_id', $_POST['dataset_type']);
        }
    }
    else{
        set_query_var('ef_listing_dataset_type_id', -1);
    }

    //check license
    if(isset($_POST['dataset_license']))
    {
        if(!is_numeric($_POST['dataset_license']))
        {
            set_query_var('ef_listing_dataset_license_id', -1);
        }else
        {
          set_query_var('ef_listing_dataset_license_id', $_POST['dataset_license']);
        }
    }
    else{
        set_query_var('ef_listing_dataset_license_id', -1);
    }

    //check formats
    if(isset($_POST['dataset_formats']))
    {
        if(!in_array($_POST['dataset_formats'], $extensions))
        {
            set_query_var('ef_listing_dataset_format', -1);
        }else
        {
          set_query_var('ef_listing_dataset_format', $_POST['dataset_formats']);
        }
    }
    else{
        set_query_var('ef_listing_dataset_format', -1);
    }
    
    //check publisher
    if(isset($_POST['dataset_publisher']) && !empty($_POST['dataset_publisher']))
    {
      set_query_var('ef_listing_dataset_publisher', $_POST['dataset_publisher']);
    }
    else{
      set_query_var('ef_listing_dataset_publisher', -1);
    }

    get_template_part('template-parts/content', 'listing_datasets');
    die();
}
add_action('wp_ajax_ef_load_change_theme_open_dataset', 'ef_load_change_theme_open_dataset');
add_action('wp_ajax_nopriv_ef_load_change_theme_open_dataset', 'ef_load_change_theme_open_dataset');

function ef_return_taxonomy_id_by_name($name, $taxonomy)
{
    global $wpdb;
    $query = $wpdb->prepare('SELECT '.$wpdb->terms.'.term_id FROM ' . $wpdb->terms . ' join '.$wpdb->term_taxonomy.' as tt on tt.term_id = '.$wpdb->terms.'.term_id WHERE (name = %s or name_ar = %s) AND tt.taxonomy = \'%s\'', $name, $name,$taxonomy);
    $wpdb->query( $query );
    if ( $wpdb->num_rows ) {
        $last_result = $wpdb->last_result;
        return $last_result[0]->term_id;
    }
    return -1;
}

function returnJoin($item)
{
    global $wpdb;
    $join_condition = '';
    if($item == 'theme')
    {
        $join_condition = " join {$wpdb->prefix}postmeta  as rel_theme on post.ID = rel_theme.post_id";
    } else if($item == 'type')
    {
        $join_condition = " join {$wpdb->prefix}postmeta  as rel_type on post.ID = rel_type.post_id";
    } else if($item == 'license')
    {
        $join_condition = " join {$wpdb->prefix}postmeta  as rel_license on post.ID = rel_license.post_id";
    } else if($item == 'format')
    {
        $join_condition = " join {$wpdb->prefix}postmeta  as attch on post.ID = attch.post_id";
    } else if($item == 'publisher')
    {
        $join_condition = " join {$wpdb->prefix}postmeta  as publisher on post.ID = publisher.post_id";
    }
    
    return $join_condition;
}

function returnWhereCondition($item,$args)
{
    global $wpdb;
    global $extension_mime_types;
    $where_condition = '';
    if($item == 'theme')
    {
        $where_condition = " and (rel_theme.meta_key = 'theme' and rel_theme.meta_value = ".$args['theme_id'].")";
    } else if($item == 'type')
    {
        $where_condition = " and (rel_type.meta_key = 'dataset_type' and rel_type.meta_value = ".$args['type_id'].")";
    } else if($item == 'license')
    {
        $where_condition = " and (rel_license.meta_key = 'datasets_license' and rel_license.meta_value = ".$args['license_id'].")";
    } else if($item == 'format')
    {
        //$where_condition = " and (attch.post_type = 'attachment' and attch.post_mime_type like '%".$extension_mime_types[strtolower($args['format'])]."%')";
        $where_condition = " and (attch.meta_key = 'dataset_formats' and attch.meta_value like '%".$extension_mime_types[strtolower($args['format'])]."%')";
    } else if($item == 'publisher')
    {
        $where_condition = " and (publisher.meta_key = 'publisher' and publisher.meta_value = %s)";
    }
    
    return $where_condition;
}

function ef_update_formats_open_dataset($post_id, $post) {
    global $wpdb;
    global $extension_mime_types;
    if($post->post_type == "open_dataset")
    {
        //reset formats to the edited
        $formats_open_dataset = "";
        $resources_ids = "";
        
        //get resources
        $resources_count = get_post_meta($post_id, 'resources', true);
        $attachments = array();
        $attachments_status = array();
        for($i= 0; $i < $resources_count; $i++)
        {
            array_push($attachments, get_post_meta($post_id, 'resources_'.$i.'_upload', true));
            array_push($attachments_status, get_post_meta($post_id, 'resources_'.$i.'_resource_status', true));
        }
        if ( $attachments ) {
          $indx = 0;
          foreach ( $attachments as $attachment ) 
          {
            if($attachments_status[$indx] == "publish")
            {
              $resources_ids = $resources_ids.$attachment.'|||';
              $attachment_path = get_attached_file( $attachment );
              $attachment_ext = pathinfo($attachment_path, PATHINFO_EXTENSION);
              $formats_open_dataset = $formats_open_dataset.$extension_mime_types[strtolower($attachment_ext)].'|||';
            }
            $indx++;
          }
        }
        update_post_meta($post_id, 'dataset_formats', substr($formats_open_dataset, 0, -3)); 
        
        //save resources ids
        update_post_meta($post_id, 'resources_ids', substr($resources_ids, 0, -3));       
    }
}
add_action('save_post', 'ef_update_formats_open_dataset', 999, 2);

//add new column to admin open dataset list
function new_modify_open_dataset_list( $columns ) {
  $columns['pending_resources'] = 'Pending Resources';
  $customOrder = array('cb', 'title', 'pending_resources', 'language_en','language_en', 'comments', 'date', 'wpseo-score');

  # return a new column array to wordpress.
  # order is the exactly like you set in $customOrder.
  foreach ($customOrder as $colname) {
    $new[$colname] = $columns[$colname]; 
  }
  return $new;
  //return $columns;
}
add_filter('manage_open_dataset_posts_columns', 'new_modify_open_dataset_list', 999 );

function new_modify_open_dataset_list_sortable( $columns ) {
  $columns['pending_resources'] = 'pending_resources';
  return $columns;
}
//add_filter( 'manage_edit-open_dataset_sortable_columns', 'new_modify_open_dataset_list_sortable' );

function new_modify_open_dataset_list_row( $column, $post_id ) {
  $post = get_post($post_id);
  if($post->post_type == 'open_dataset') {
    switch ( $column ) {
      case 'pending_resources':
        $total_resources = get_post_meta($post_id, 'resources', true);
        $published_resources = explode("|||", get_post_meta($post_id, 'resources_ids', true));
        if( count( $published_resources ) >= 1 && empty($published_resources[0]) ) {
          array_shift( $published_resources );
        }
        $pending_resources = $total_resources - count($published_resources);
        echo $pending_resources;
        break;
    }
  }
}
add_action('manage_posts_custom_column' , 'new_modify_open_dataset_list_row', 10, 2 );

  function ef_open_dataset_filter_pending_resources() {
    if (is_admin() && $_GET['post_type'] == 'open_dataset') {
        $is_pending_resources = $_GET['pending_resources'];
        if(!isset($_GET['pending_resources']))
        {
          $is_pending_resources = '';
        }
        echo "<select name='pending_resources' class='select2_admin_region'>";
        echo "<option value='' ".($is_pending_resources == ''?'selected':'').">All Resources</option>";
        echo '<option value="pending"'.($is_pending_resources == 'pending'?'selected':'').'>Pending</option>'
        . '<option value="publish"'.($is_pending_resources == 'publish'?'selected':'').'>Publish</option>';
        echo '</select>';
    }
  }

  add_action('restrict_manage_posts', 'ef_open_dataset_filter_pending_resources');
  
  
//handling resource description
function ef_load_resource_description() {
    if(isset($_POST['resource_id']) && isset($_POST['opendataset_id']))
    {
      if(!is_numeric($_POST['resource_id']) || !is_numeric($_POST['opendataset_id']))
      {
        echo __("Something wrong has happened. Please try again","egyptfoss");
      }
      
      //load resouce
      $attachment = get_post($_POST['resource_id']);
      if($attachment->post_content != '')
      {
        $description = htmlspecialchars_decode($attachment->post_content);
      }else
      {
        //load open dataset description
        $description = htmlspecialchars_decode(get_post_meta($_POST['opendataset_id'], 'description', true));
      }
      echo $description;
      die();
    }else
    {
      echo __("Something wrong has happened. Please try again","egyptfoss");
    }
    die();
}
add_action('wp_ajax_ef_load_resource_description', 'ef_load_resource_description');
add_action('wp_ajax_nopriv_ef_load_resource_description', 'ef_load_resource_description');


// Load publishers list
function ef_load_open_dataset_publishers()
{
  global $wpdb;
  $sql = "select distinct meta_value from {$wpdb->prefix}postmeta as pmeta "
  . "join {$wpdb->prefix}posts as posts on pmeta.post_id = posts.ID "
  . "where meta_key = 'publisher' and posts.post_status = 'publish' and posts.post_type = 'open_dataset' "
  . "order by meta_value asc";
  $results = $wpdb->get_col($sql);
  return $results;
}

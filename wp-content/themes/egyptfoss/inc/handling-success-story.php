<?php
define("ef_success_story_per_page", 10);

function ef_add_success_story_front_end() {
    $ef_success_story_messages = array("errors" => array());
    $errors = checkSuccessStoryFrontendValidation();
    $ef_success_story_messages["errors"] = $errors;
    if($ef_success_story_messages["errors"])
    {
      set_query_var("ef_success_story_messages", $ef_success_story_messages);
      return false;
    }
     
    $_POST["success_story_description"] = strip_js_tags($_POST["success_story_description"]);
     
    $my_post = array(
      'post_title' => $_POST["success_story_title"],
      'post_content' => $_POST["success_story_description"],
      'post_author' => get_current_user_id(),
      'post_type' => 'success_story',
      'post_status' => 'pending'
    );
    $post_id = wp_insert_post($my_post);
    ef_handling_image_and_screenshots_upload($post_id,"success_story");
    set_query_var("ef_success_story_messages", array("success"=> array(__("Success Story","egyptfoss"). ' ' . __("added successfully, it is now under review","egyptfoss"))));
    $taxonomies = array('interest','post_category');
    foreach ($taxonomies as $taxonomy){ 
        if(isset($_POST[$taxonomy]))
        {
            if($taxonomy == 'post_category'){
                $meta['success_story_category'] = $_POST[$taxonomy]; 
                $taxonomy = 'success_story_category';
            }
            else{
                $meta[$taxonomy] = $_POST[$taxonomy]; 
            }
            $term_ids = wp_set_object_terms($post_id, $meta[$taxonomy], $taxonomy);
            $return_arr = getTermFromTermTaxonomy($term_ids);
            if(sizeof($return_arr) == 1 && ($taxonomy != 'interest')){
               $return_arr = $return_arr[0];             
            }
            update_post_meta($post_id, $taxonomy, $return_arr);
        }
    }
    
    //current language of the post
    $current_lang = pll_current_language();
    wp_set_object_terms($post_id, $current_lang, 'language');
    update_post_meta($post_id, 'language', serialize(array(
      "slug" => $current_lang,
      "translated_id" => 0))
    );
    
    if ( !empty( $_FILES["files"] ) ) {
      $files = $_FILES["files"];  // files is the input name
      $images_ids = array();
      foreach ($files['name'] as $key => $value) {
          if ($files['name'][$key]) {
              $file = array(
              'name' => $files['name'][$key],
              'type' => $files['type'][$key],
              'tmp_name' => $files['tmp_name'][$key],
              'error' => $files['error'][$key],
              'size' => $files['size'][$key]
              );
              $_FILES = array ("files" => $file);
              foreach ($_FILES as $file => $array) {
                  $newupload = ef_handle_story_attachment($file, $post_id);
                  $images_ids = array_merge($images_ids,array($newupload));
              }


          }
          // we must handle maximum size upload for screenshots .... //
      }
      $images_ids = implode(',', $images_ids);
      update_post_meta($post_id, 'fg_perm_metadata', $images_ids);
    }
    
    return $post_id;
}

function ef_handle_story_attachment( $file_handler, $post_id ) {
    // check to make sure its a successful upload
    if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');

    $attachment_id = media_handle_upload( $file_handler, $post_id );
    
    return $attachment_id;
}

function checkSuccessStoryFrontendValidation() {
  $errors = array();
  
  if (mb_strlen($_POST['success_story_title'],'UTF-8') == 0) {
    $errors["title"] = __("Title","egyptfoss"). ' ' . __("is required",'egyptfoss');
  }else
  {
    if (mb_strlen($_POST['success_story_title'],'UTF-8') > 100 && mb_strlen($_POST['success_story_title'],'UTF-8') != 0) {
      $errors["title"] = __("Title","egyptfoss"). ' ' . sprintf(__("should not be more than %d characters",'egyptfoss'),100);
    }

    if (mb_strlen($_POST['success_story_title'],'UTF-8') < 10 && mb_strlen($_POST['success_story_title'],'UTF-8') != 0) {
      $errors["title"] = __("Title","egyptfoss"). ' ' . sprintf(__("should be at least %d characters",'egyptfoss'),10);
    }
  }
  
  $description = $_POST['success_story_description'];
  $description = strip_tags($description);
  if (empty($description)) {
    $errors["desc"] = __("Content","egyptfoss"). ' ' . __("is required",'egyptfoss');
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = __("Content","egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
    }
  }
  
  if(!isset($_POST['post_category']))
  {
    $errors["category"] = __("Category","egyptfoss"). ' ' . __("is required",'egyptfoss');  
  }
  
  if(empty($_FILES["success_story_image"]['tmp_name'] ))
  {
      $errors["success_story_image_required"] = __("Image","egyptfoss"). ' ' . __("is required",'egyptfoss');
  }
  
  if(!empty($_FILES["success_story_image"]['tmp_name'] ))
  {
  if(!is_image($_FILES["success_story_image"]['tmp_name']))
  { 
    $errors["image"] = __("please enter correct image type",'egyptfoss');
  }
  }
  return $errors;
}

function count_success_stories()
{
    wp_enqueue_script( 'listing_success_stories-js', get_stylesheet_directory_uri() . '/js/listing_success_stories.js', array('jquery'), '', true);
    wp_localize_script('listing_success_stories-js', 'ef_success_story', array("per_page" => constant("ef_success_story_per_page")));
    
    global $wpdb;
    $lang = pll_current_language();
    $foriegn_lang = ($lang== "en")?"ar":"en";
    $sql = "select distinct posts.ID,posts_category.meta_value as category_id
            from wpRuvF8_posts as posts
            join wpRuvF8_postmeta as posts_category on posts_category.post_id = posts.ID
            join {$wpdb->prefix}postmeta as pmeta on posts.ID = pmeta.post_id
            where posts.post_type = 'success_story'
            and posts.post_status = 'publish'
            and posts_category.meta_key = 'success_story_category'
            and (pmeta.meta_key = 'language' and 
            (pmeta.meta_value like '%\"{$lang}\"%' or (
            pmeta.meta_value like '%\"slug\";s:2:\"{$foriegn_lang}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')));";
               
    return $wpdb->get_results($sql);
}

function get_success_stories($args = array()){
    global $wpdb;
    $no_of_posts = constant("ef_success_story_per_page");
    $args['offset'] = (get_query_var("ef_listing_success_stories_offset") ? get_query_var("ef_listing_success_stories_offset") : 0);
    
    if(!is_numeric($args['category_id']))
    {
        $args['category_id'] = -1;
    }
    
    if($args['category_id'] == -1)
    {
        $join_condition = "";
        $join_condition_where = "";
    }else{
        $join_condition = "join {$wpdb->prefix}postmeta  as rel_category on post.ID = rel_category.post_id";
        $join_condition_where = "and (rel_category.meta_key = 'success_story_category' and rel_category.meta_value = ".$args['category_id'].")";
    }
    
    $args['foriegn_lang'] = ($args['current_lang'] == "en")?"ar":"en";
    $sql = "SELECT ID,post_author,post_date,post_content,post_title,post_name,guid
            FROM {$wpdb->prefix}posts as post
            join {$wpdb->prefix}postmeta as pmeta on post.ID = pmeta.post_id
            $join_condition
            where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') 
            and (pmeta.meta_key = 'language' and 
            (pmeta.meta_value like '%\"{$args['current_lang']}\"%' or (
            pmeta.meta_value like '%\"slug\";s:2:\"{$args['foriegn_lang']}\";s:13:\"translated_id\";i:0%' or pmeta.meta_value like '%\"trashed\";i:1%')))
            $join_condition_where
            group by post.ID
            order by post.post_date DESC
            limit {$args['offset']}, {$no_of_posts} ";
    $results = $wpdb->get_results($sql);
    return $results;
}

function ef_return_category_id_by_name($category_name, $taxonomy = "success_story_category")
{
    global $wpdb;
    $query = $wpdb->prepare('SELECT '.$wpdb->terms.'.term_id FROM ' . $wpdb->terms . ' join '.$wpdb->term_taxonomy.' as tt on tt.term_id = '.$wpdb->terms.'.term_id WHERE (name = %s or name_ar = %s) AND tt.taxonomy = \''.$taxonomy.'\'', $category_name, $category_name);
    $wpdb->query( $query );
    if ( $wpdb->num_rows ) {
        $last_result = $wpdb->last_result;
        return $last_result[0]->term_id;
    }
    return -1;
}

// --- view more for listing new --- //
function ef_load_more_listing_success_story() {
  set_query_var('ef_listing_success_stories_offset', $_POST['offset']);
  set_query_var('ef_listing_success_stories_category_id', $_POST['category']);
  get_template_part('template-parts/content', 'listing_success_stories');
  die();
}
add_action('wp_ajax_ef_load_more_listing_success_story', 'ef_load_more_listing_success_story');
add_action('wp_ajax_nopriv_ef_load_more_listing_success_story', 'ef_load_more_listing_success_story');

function ef_load_change_category_success_story() {
  set_query_var('ef_listing_success_stories_offset', 0);
  set_query_var('ef_listing_success_stories_category_id', $_POST['category']);
  get_template_part('template-parts/content', 'listing_success_stories');
  die();
}
add_action('wp_ajax_ef_load_change_category_success_story', 'ef_load_change_category_success_story');
add_action('wp_ajax_nopriv_ef_load_change_category_success_story', 'ef_load_change_category_success_story');

function ef_get_count_per_success_story_category($success_stories, $category_id)
{   
    foreach($success_stories as $success_story)
    {
        @$count[$success_story->category_id]++;
    }
    if(!isset($count[$category_id]))
        return 0;
    return $count[$category_id];
}

function ef_left_menu_listing_success_story() {
  get_template_part('template-parts/content', 'listing_success_story_left_menu');
  die();
}
add_action('wp_ajax_ef_left_menu_listing_success_story', 'ef_left_menu_listing_success_story');
add_action('wp_ajax_nopriv_ef_left_menu_listing_success_story', 'ef_left_menu_listing_success_story');


<?php
define("ef_news_per_page", 9);

function ef_add_news_front_end() {
  $ef_news_messages = array("errors" => array());
  $history_data = array();
  $errors = checkNewsFrontendValidation();
  $ef_news_messages["errors"] = $errors;
  if($ef_news_messages["errors"])
  {
    setMessageBySession("ef_news_messages", "error", $ef_news_messages["errors"]);
    return false;
  }
  
    $my_post = array(
      'post_title' => $_POST["news_title"],
      'post_author' => get_current_user_id(),
      'post_type' => 'news',
      'post_status' => 'pending'
    );
    $post_id = wp_insert_post($my_post);
    update_post_meta($post_id, 'description', strip_js_tags($_POST["news_description"]));
    update_post_meta($post_id, 'subtitle', $_POST["news_subtitle"]);
    update_post_meta($post_id, 'is_news_featured_homepage', 0);
    ef_handling_image_and_screenshots_upload($post_id,"news");
    
    $taxonomies = array('interest','news_category');
    foreach ($taxonomies as $taxonomy){ 
        if(isset($_POST[$taxonomy]))
        {
            if($taxonomy == 'news_category'){
              $taxonomy = 'news_category';
            }
            $meta[$taxonomy] = $_POST[$taxonomy];
            $term_ids = wp_set_object_terms($post_id, $meta[$taxonomy], $taxonomy);
            $return_arr = getTermFromTermTaxonomy($term_ids);
            if(sizeof($return_arr) == 1 && ($taxonomy != 'interest')){
               $return_arr = $return_arr[0];       
            }
            update_post_meta($post_id, $taxonomy, $return_arr);
        }
    }
    
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
                  $newupload = ef_handle_news_attachment($file, $post_id);
                  $images_ids = array_merge($images_ids,array($newupload));
              }
          }
      }
      $images_ids = implode(',', $images_ids);
      update_post_meta($post_id, 'fg_perm_metadata', $images_ids);
    }
    
    $current_lang = pll_current_language();
    wp_set_object_terms($post_id, $current_lang, 'language');
    setMessageBySession("ef_news_messages", "success", array(_x("News", "definite", 'egyptfoss').' '. $_POST["news_title"] .' '.__("added successfully, it is now under review",'egyptfoss'))) ;   
    return $post_id;
}

function ef_handle_news_attachment( $file_handler, $post_id ) {
    // check to make sure its a successful upload
    if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');

    $attachment_id = media_handle_upload( $file_handler, $post_id );
    
    return $attachment_id;
}

function checkNewsFrontendValidation() {
  $errors = array();
  
  if (mb_strlen($_POST['news_title'],'UTF-8') == 0) {
    $errors["title"] = __("Title","egyptfoss"). ' ' . __("is required",'egyptfoss');
  } else {
    if (mb_strlen($_POST['news_title'],'UTF-8') > 100 && strlen($_POST['news_title']) != 0) {
      $errors["title"] = __("Title","egyptfoss"). ' ' . sprintf(__("should not be more than %d characters",'egyptfoss'),100);
    }
    if (mb_strlen($_POST['news_title'],'UTF-8') < 10 && strlen($_POST['news_title']) != 0) {
      $errors["title"] = __("Title","egyptfoss"). ' ' . sprintf(__("should be at least %d characters",'egyptfoss'),10);
    }
  }
  
  $subtitle = $_POST['news_subtitle'];
  $sub_is_numbers_only = preg_match("/^[0-9]{1,}$/", $subtitle);
  $sub_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $subtitle);
  if (strlen($subtitle) > 0 && ($sub_is_numbers_only > 0 || !$sub_contains_letters)) {
      $errors["desc"] = __("Subtitle","egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
  }

  $description = $_POST['news_description'];
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
  
  if(!isset($_POST['news_category']))
  {
    $errors["category"] = __("Category","egyptfoss"). ' ' . __("is required",'egyptfoss');  
  }
  
  
  if(empty($_FILES["news_image"]['tmp_name'] ))
  {
      $errors["news_image_required"] = __("Image","egyptfoss"). ' ' . __("is required",'egyptfoss');
  }
  
  if(!empty($_FILES["news_image"]['tmp_name'] ))
  {
  if(!is_image($_FILES["news_image"]['tmp_name']))
  { 
    $errors["image"] = __("please enter correct image type",'egyptfoss');
  }
  }
  return $errors;
}

function ef_handling_image_and_screenshots_upload($post_id,$post_type) {
  require_once( ABSPATH . 'wp-admin/includes/image.php' );
  require_once( ABSPATH . 'wp-admin/includes/file.php' );
  require_once( ABSPATH . 'wp-admin/includes/media.php' );
    
  if (!empty($_FILES["{$post_type}_image"]["name"]) && isset($_POST["{$post_type}_image_nonce"], $post_id) && wp_verify_nonce($_POST["{$post_type}_image_nonce"], "{$post_type}_image")) {
    $attachment_id = media_handle_upload("{$post_type}_image", $post_id);
    update_post_meta($post_id, '_thumbnail_id', $attachment_id);
  }
  if (isset($_FILES["{$post_type}_screenshots"]) && $_FILES["{$post_type}_screenshots"] && isset($_POST["{$post_type}_screenshots_nonce"], $post_id) && wp_verify_nonce($_POST["{$post_type}_screenshots_nonce"], "{$post_type}_screenshots")) {
    $files = $_FILES["{$post_type}_screenshots"];  // files is the input name
    $images_ids = array();
    foreach ($files['name'] as $key => $value) {
      if (($files['name'][$key] != "")) {
        $file = array(
          'name' => $files['name'][$key],
          'type' => $files['type'][$key],
          'tmp_name' => $files['tmp_name'][$key],
          'error' => $files['error'][$key],
          'size' => $files['size'][$key]
        );
        $_FILES = array("files" => $file);
        foreach ($_FILES as $file => $array) {
          $newupload = media_handle_upload($file, $post_id);
          $images_ids = array_merge($images_ids, array($newupload));
        }
      }
      // we must handle maximum size upload for screenshots .... //
    }
    if(!empty($images_ids))
    {
      $images_ids = implode(',', $images_ids);
      update_post_meta($post_id, 'fg_perm_metadata', $images_ids);
    }
  }
}
function is_image($path)
{
	$a = getimagesize($path);
	$image_type = $a[2];
	
	if(in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP)))
	{
		return true;
	}
	return false;
}
  

function count_news($args){
  global $wpdb;
  $current_lang = pll_current_language();
  $sql = "SELECT * FROM {$wpdb->prefix}posts as post
          join {$wpdb->prefix}term_relationships as rel on post.ID = rel.object_id
          join {$wpdb->prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
          join {$wpdb->prefix}terms as t on t.term_id = tax.term_id
          where (post.post_status = 'publish' and post.post_type = 'news') 
          and (tax.taxonomy = 'language')
          and (t.slug = '{$current_lang}')
          group by post.ID";
  $results = $wpdb->get_results($sql);
  return $results;
}

function get_news($args = array()){
  wp_enqueue_script( 'listing_news-js', get_stylesheet_directory_uri() . '/js/listing_news.js', array('jquery'), '', true);
   wp_localize_script('listing_news-js', 'ef_news', array("per_page" => constant("ef_news_per_page")));
  global $wpdb;
  $no_of_posts = constant("ef_news_per_page");
  $args['offset'] = (get_query_var("ef_listing_news_offset") ? get_query_var("ef_listing_news_offset") : 0);
  
  $sql = "SELECT * FROM {$wpdb->prefix}posts as post
          join {$wpdb->prefix}term_relationships as rel on post.ID = rel.object_id
          join {$wpdb->prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
          join {$wpdb->prefix}terms as t on t.term_id = tax.term_id
          where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') 
          and (tax.taxonomy = 'language')
          and (t.slug = '{$args['current_lang']}')
          group by post.ID
          order by post.post_date DESC
          limit {$args['offset']}, {$no_of_posts} ";
  $results = $wpdb->get_results($sql);
  return $results;
}

// --- view more for listing new --- //
function ef_load_more_listing_news() {
  set_query_var('ef_listing_news_offset', $_POST['offset']);  // ef_added_product_offset will be changed after creating the new query
  get_template_part('template-parts/content', 'listing_news');
  die();
}
add_action('wp_ajax_ef_load_more_listing_news', 'ef_load_more_listing_news');
add_action('wp_ajax_nopriv_ef_load_more_listing_news', 'ef_load_more_listing_news');
// --- end listing new --- //

function get_recent_news($post_id)
{
    global $wpdb;
    $args = array(
        'post_status' => 'publish',
        'post_type' => 'news',
        'current_lang' => pll_current_language()
    );
    $sql = "SELECT * FROM {$wpdb->prefix}posts as post
          join {$wpdb->prefix}term_relationships as rel on post.ID = rel.object_id
          join {$wpdb->prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
          join {$wpdb->prefix}terms as t on t.term_id = tax.term_id
          where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') 
          and (tax.taxonomy = 'language')
          and (t.slug = '{$args['current_lang']}')
          and post.ID not in ($post_id)
          group by post.ID
          order by post.post_date DESC
          limit 0,5";

    $results = $wpdb->get_results($sql);
    return $results;
}

function get_related_news($category_id, $post_id)
{
    global $wpdb;
    $args = array(
        'post_status' => 'publish',
        'post_type' => 'news',
        'current_lang' => pll_current_language()
    );
    $sql = "SELECT * FROM {$wpdb->prefix}posts as post
          join {$wpdb->prefix}term_relationships as rel on post.ID = rel.object_id
          join {$wpdb->prefix}term_taxonomy as tax on rel.term_taxonomy_id = tax.term_taxonomy_id
          join {$wpdb->prefix}terms as t on t.term_id = tax.term_id
          join {$wpdb->prefix}postmeta as pstmeta on pstmeta.post_id = post.ID
          where (post.post_status = '{$args['post_status']}' and post.post_type = '{$args['post_type']}') 
          and (tax.taxonomy = 'language')
          and (pstmeta.meta_key = 'news_category')
          and (pstmeta.meta_value like '%$category_id%')
          and (t.slug = '{$args['current_lang']}')
          and post.ID not in ($post_id)
          group by post.ID
          order by post.post_date DESC
          limit 0,5";

    $results = $wpdb->get_results($sql);
    return $results;
}
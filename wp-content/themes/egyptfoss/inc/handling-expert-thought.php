<?php

define("ef_expert_thought_per_page", 10);

function ef_add_expert_thought_front_end() {
  $ef_expert_thought_messages = array("errors" => array());
  $errors = checkExpertThoughtFrontendValidation();
  $ef_expert_thought_messages["errors"] = $errors;
  if ($ef_expert_thought_messages["errors"]) {
    set_query_var("ef_expert_thought_messages", $ef_expert_thought_messages);
    return false;
  }

  $_POST["expert_thought_description"] = strip_js_tags($_POST["expert_thought_description"]);
  $my_post = array(
    'post_title' => $_POST["expert_thought_title"],
    'post_content' => $_POST["expert_thought_description"],
    'post_author' => get_current_user_id(),
    'post_type' => 'expert_thought',
    'post_status' => 'pending'
  );
  $post_id = wp_insert_post($my_post);
  ef_handling_image_and_screenshots_upload($post_id, "expert_thought");
  set_query_var("ef_expert_thought_messages", array("success" => array(__("Expert Thought", "egyptfoss") . ' ' . __("added successfully, it is now under review", "egyptfoss"))));
  if (isset($_POST['interest'])) {
    $term_ids = wp_set_object_terms($post_id, $_POST['interest'], 'interest');
    $return_arr = getTermFromTermTaxonomy($term_ids);
    update_post_meta($post_id, 'interest', $return_arr);
  }
  
  return $post_id;
}

function ef_edit_expert_thought_front_end() {
  $post_id    = efGetValueFromUrlByKey( 'expert-thoughts' );
  $ef_expert_thought_messages = array("errors" => array());
  $errors = checkExpertThoughtFrontendValidation( $post_id );
  $ef_expert_thought_messages["errors"] = $errors;
  if ($ef_expert_thought_messages["errors"]) {
    set_query_var("ef_expert_thought_messages", $ef_expert_thought_messages);
    return false;
  }

  $_POST["expert_thought_description"] = strip_js_tags($_POST["expert_thought_description"]);
  
  $my_post = array(
    'ID'            =>  $post_id,
    'post_title'    =>  $_POST["expert_thought_title"],
    'post_content'  =>  $_POST["expert_thought_description"],
    'post_name' => wp_unique_post_slug(str_replace(' ', '-', strtolower($_POST["expert_thought_title"])), $post_id, 'publish', 'expert_thought', 0)
  );
  
  wp_update_post( $my_post );
  
  ef_handling_image_and_screenshots_upload($post_id, "expert_thought");

  if (isset($_POST['interest'])) {
    $term_ids = wp_set_object_terms($post_id, $_POST['interest'], 'interest');
    $return_arr = getTermFromTermTaxonomy($term_ids);
    update_post_meta($post_id, 'interest', $return_arr);
  }
  
  setMessageBySession( 'ef_expert_thought_messages', 'success', __( "Thought edited successfully", 'egyptfoss' ) );
  
  return $post_id;
}

function checkExpertThoughtFrontendValidation( $post_id = 0 ) {
  $errors = array();
  $title = trim($_POST['expert_thought_title']);
  $description = trim($_POST['expert_thought_description']);
  $description = strip_tags($description);
  $title_is_numbers_only = preg_match("/^[0-9]{1,}$/", $_POST['expert_thought_title']);
  $title_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $_POST['expert_thought_title']);
  
  if (mb_strlen($title, 'UTF-8') == 0) {
    $errors["title"] = __("Title", "egyptfoss") . ' ' . __("is required", 'egyptfoss');
  } else if((mb_strlen($title, 'UTF-8') > 100) && mb_strlen($title, 'UTF-8') != 0) {
      $errors["title"] = __("Title", "egyptfoss") . ' ' . sprintf(__("should not be more than %d characters", 'egyptfoss'), 100);
  } else if(mb_strlen($title, 'UTF-8') < 10 && mb_strlen($title, 'UTF-8') != 0) {
      $errors["title"] = __("Title", "egyptfoss") . ' ' . sprintf(__("should be at least %d characters", 'egyptfoss'), 10);    
  } else if (($title_is_numbers_only > 0 || !$title_contains_letters)) {
      $errors["title"] = __("Title", "egyptfoss") . ' ' . __("must at least contain one letter", 'egyptfoss');
  } else {
    load_orm();
    $query = ExpertThought::where("post_title",$title);
    // in edit mode
    if( $post_id ) {
        $query = $query->where( "ID", "!=",  $post_id );
    }
    $titleExists = $query->first();
    if(!empty($titleExists)){
      $errors['title'] = __("Title","egyptfoss").' '. __('already exists',"egyptfoss");
    }
  }
  if (empty($description)) {
    $errors["desc"] = __("Content", "egyptfoss") . ' ' . __("is required", 'egyptfoss');
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = __("Content", "egyptfoss") . ' ' . __("must at least contain one letter", 'egyptfoss');
    }
  }

  if (!empty($_FILES["expert_thought_image"]['tmp_name'])) {
    if (!is_image($_FILES["expert_thought_image"]['tmp_name'])) {
      $errors["image"] = __("please enter correct image type", 'egyptfoss');
    }
  }
  
  if(isset($_POST["interest"]) && !empty($_POST["interest"]))
  {
    foreach($_POST["interest"] as $interest)
    $is_numbers_only = preg_match("/^[0-9]{1,}$/", $interest);
    $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $interest);
    if (($is_numbers_only > 0 || !$contains_letters)) {
      $errors["interest"] = __("Interests", "egyptfoss") . ' ' . _x("must at least contain one letter",'feminist', 'egyptfoss');
    }
  }
  
  return $errors;
}

function ef_load_more_thoughts() {
  load_orm();
  $limit = constant('ef_expert_thought_per_page');
  $expertThought = new ExpertThought();
  $thoughts = $expertThought->getPublishedThoughts((int)$_POST["offset"],$limit);
  ob_start();
  include(locate_template('template-parts/content-expert_cards.php'));
	$template = ob_get_contents();			
  ob_end_clean();
  echo $template;
  die();
}
add_action('wp_ajax_ef_load_more_thoughts', 'ef_load_more_thoughts');
add_action('wp_ajax_nopriv_ef_load_more_thoughts', 'ef_load_more_thoughts');

<?php
if(is_admin())
{
$cpt_types = array('product','news','tribe_venue','tribe_organizer','success_story', 'open_dataset','request_center', 'feedback','expert_thought', 'service', 'partner');
$cpt_title_can_be_duplicated = array('request_center','success_story','news','expert_thought', 'service', 'partner');
// Load admin scripts & styles
add_action('admin_enqueue_scripts', 'load_custom_admin_scripts');
function load_custom_admin_scripts($hook) {
  // ---- validation client side -- Ashraf ---- //
  global $cpt_types;
  $postType = get_current_screen()->post_type;
  $base = get_current_screen()->base;
  $post_types = $cpt_types;
  // If the post we're editing isn't a project_summary type, exit this function
  if (in_array($postType, $post_types) ||  $base == "edit-tags") {
    if (true /* $hook == 'post-new.php' || $hook == 'post.php' */) {
        wp_enqueue_script('admin_scripts', get_stylesheet_directory_uri() . '/js/admin_validations.js');
        wp_localize_script( 'admin_scripts', 'admin_obj', array( 'is_testing' => is_testing_environment() ) );
    }
  }
}

add_action('save_post', 'validate_save_post', 10, 2);
function validate_save_post($post_id, $post) {
  global $cpt_types;
  global $cpt_title_can_be_duplicated;
  if ($_GET["action"] == "trash") {
    return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || ! in_array($post->post_type, $cpt_types)) {
    return;
  }
  $errors = array();
  // Validation filters
  $title = $post->post_title;
  $is_numbers_only = preg_match("/^[0-9]{1,}$/", $title);
  $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $title);
  if (!$title) {
    $errors['#title'] = ucfirst(str_replace('_', ' ', $post->post_type)) . " title is required";
  }else
  {
    if ($is_numbers_only > 0) {
      $errors['#title'] = "title must at least contain one letter";
    } else {
      if (!$contains_letters) {
        $errors['#title'] = "title must at least contain one letter";
      }
    }
  }

  if($post->post_type == "news") {
    $errors = checkNewsValidation($post->ID, $title, $errors);
  } else if($post->post_type == "success_story") {
    $errors = checkSuccessStoryValidation($post->ID,$title, $post->post_content, $errors);
  } else if($post->post_type == "feedback") {
    $errors = checkFeedbackValidation($post->ID, $title, $post->post_content, $errors);
  } else if($post->post_type == "open_dataset") {
    $errors = checkOpenDatasetValidation($post->ID, $title, $errors);
  } else if($post->post_type == "request_center"){
    $errors = checkRequestCenterValidation($post->ID, $title, $errors);
  } else if($post->post_type == "expert_thought"){
    $errors = checkExpertThoughtValidation($post->ID,$title, $post->post_content, $errors);
  } else if($post->post_type == "partner"){
    $errors = checkPartnersValidation($post->ID, $title, $errors);
  }
  // ------ Product Title already exits -------- //
  if ($title != "Auto Draft" && !in_array($post->post_type,$cpt_title_can_be_duplicated)) {
    global $wpdb;
    $titleExists = $wpdb->get_col("select ID from $wpdb->posts where (post_status = 'publish' or post_status = 'pending') and post_type = '".$post->post_type."' and ID !=  '" . $post_id . "' and post_title = '" . $title . "' ");
    if (!empty($titleExists)) {
      $errors['title'] = $post->post_type." already exists.";
    }
  }
  // if we have errors lets setup some messages
  if (!empty($errors)) {
    $errors['post'] = $post->post_type." Saved as draft.";
    // we must remove this action or it will loop for ever
    remove_action('save_post', 'validate_save_post');
    // Change post from published to draft
    $post->post_status = 'draft';
    $post_type_error_option = $post->post_type.'_errors';
    update_option($post_type_error_option, $errors);
    // update the post
    wp_update_post($post);
    // wp_delete_auto_drafts();
    // we must add back this action
    add_action('save_post', 'validate_save_post');
    // admin_notice is create by a $_GET['message'] with a number that wordpress uses to
    // display the admin message so we will add a filter for replacing default admin message with a redirect
    add_filter('redirect_post_location', 'validate_post_redirect_filter');
  }
}
function validate_post_redirect_filter($location) {
  // remove $_GET['message']
  $location = remove_query_arg('message', $location);
  // add our new query sting
  $postType = get_current_screen()->post_type;
  $location = add_query_arg($postType, 'error', $location);
  // return the location query string
  return $location;
}
// Add new admin message
add_action('admin_notices', 'validate_post_error_admin_message');
function validate_post_error_admin_message() {
  // ---- show error message ---- //
  global $cpt_types;
  foreach ($cpt_types as $cpt_type) {
    if (isset($_GET[$cpt_type]) && $_GET[$cpt_type] == 'error') {
      // lets get the errors from the option album_errors
      $cpt_type_error_option = $cpt_type.'_errors';
      $errors = get_option($cpt_type_error_option);
      delete_option($cpt_type_error_option);
      if ($errors) {
        $display = '<div id="notice" class="error"><ul>';
        foreach ($errors as $key=>$error) {
          $display .= '<li>' . $error . '</li>';
          ?>
          <script>
          jQuery(function ($) {
              $("<?php echo $key ?>").css({"border": "1px solid red"});
          });
        </script>
        <?php
        }
        $display .= '</ul></div>';
        echo $display;
      }
    }
  }
}
add_action('wp_ajax_title_check', 'duplicate_title_check_callback');
function duplicate_title_check_callback() {
  function title_check() {
    $title = $_POST['post_title'];
    $post_id = $_POST['post_id'];
    $post_type = $_POST['post_type'];
    global $wpdb;
    $titleExists = $wpdb->get_col("select ID from $wpdb->posts where (post_status = 'publish' or post_status = 'pending') and post_type = '{$post_type}' and ID !=  '{$post_id}' and post_title = '{$title}' ");
    if (!empty($titleExists)) {
      return 1;
    } else {
      return 0;
    }
  }
  echo title_check();
  die();
}


function ef_validate_edit_tag($term_id,$taxonomy) {
    $term = get_term_by('name', $_POST['name'], $taxonomy);
    if($term && $term->term_id != $term_id)
    {
      //update_option('ef_edit_tag_errors', __("A term with the name provided already exists in this taxonomy."));
      $url = home_url();
      wp_redirect($_REQUEST["_wp_original_http_referer"]);
      exit;
    }
}
add_action('edit_terms', 'ef_validate_edit_tag', 10, 2);

function ef_validate_messages_edit_tag() {
     $error = get_option("ef_edit_tag_errors");
      delete_option('ef_edit_tag_errors');
      if ($error) {
        $display = '<div id="notice" class="error"><ul>';
        $display .= '<li>' . $error . '</li>';
        $display .= '</ul></div>';
        echo $display;
        ?>
        <script>
          jQuery(function ($) {
              $("#title").css({"border": "1px solid red"});
          });
        </script>
        <?php

    }
  }
//add_action('admin_notices', 'ef_validate_messages_edit_tag');

function duplicate_tag_title_check_callback() {
  function tag_title_check() {
    $name = $_POST['name'];
    $taxonomy = $_POST['taxonomy'];
    $term_id = $_POST['term_id'];
    $term = get_term_by('name', $_POST['name'], $taxonomy);
    if($term && $term->term_id != $term_id)
    {
      return 1;
    } else {
      return 0;
    }
  }
  echo tag_title_check();
  die();
}
add_action('wp_ajax_tag_title_check', 'duplicate_tag_title_check_callback');

function checkNewsValidation($post_id, $title, $errors) {
  if (mb_strlen($title,'UTF-8') > 100 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should not be more than 100 characters</p></li>";
  }

  if (mb_strlen($title,'UTF-8') < 10 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should be at least 10 characters</p></li>";
  }

  // i.e: in quick edit mode
  if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'inline-save') {
    $description = get_field( 'description', $post_id );
  } else {
    $description = $_POST['acf']['field_56cc61ce8efe3'];
  }

  $description = strip_tags($description);

  if (empty($description)) {
    $errors["desc"] = "<li><p>description is required</p></li>";
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = "<li><p>description must at least contain one letter</p></li>";
    }
  }

  if ( !is_testing_environment() && !has_post_thumbnail( $post_id ) ) {
    $errors["image"] = "<li><p>Featured image is required</p></li>";
  }

  return $errors;
}

function checkPartnersValidation($post_id, $title, $errors) {
  if (mb_strlen($title,'UTF-8') > 100 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should not be more than 100 characters</p></li>";
  }

  if ( !is_testing_environment() && !has_post_thumbnail( $post_id ) ) {
    $errors["image"] = "<li><p>Featured image is required</p></li>";
  }

  $link  = get_field( 'link', $post_id );

  $valid_link = preg_match( "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i" ,$link);

  if (!$valid_link) {
    $errors["desc"] = "<li><p>Link should be valid URL</p></li>";
  }

  return $errors;
}

function checkSuccessStoryValidation($id,$title, $content, $errors) {
  if (mb_strlen($title,'UTF-8') > 100 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should not be more than 100 characters</p></li>";
  }

  if (mb_strlen($title,'UTF-8') < 10 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should be at least 10 characters</p></li>";
  }

  $description = $content;
  $description = strip_tags($description);
  if (empty($description)) {
    $errors["desc"] = "<li><p>description is required</p></li>";
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = "<li><p>description must at least contain one letter</p></li>";
    }
  }

  //make sure that category selected
  if(!get_post_meta($id, 'success_story_category',true))
  {
    $errors["category"] = "<li><p>category is required</p></li>";
  }


  return $errors;
}

function checkExpertThoughtValidation($id,$title, $content, $errors) {
  if (mb_strlen($title,'UTF-8') > 100 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should not be more than 100 characters</p></li>";
  }

  if (mb_strlen($title,'UTF-8') < 10 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should be at least 10 characters</p></li>";
  }

  $description = $content;
  $description = strip_tags($description);
  if (empty($description)) {
    $errors["desc"] = "<li><p>description is required</p></li>";
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = "<li><p>description must at least contain one letter</p></li>";
    }
  }
  return $errors;
}

function checkFeedbackValidation($id,$title, $content, $errors) {
  if (mb_strlen($title,'UTF-8') > 100 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should not be more than 100 characters</p></li>";
  }

  if (mb_strlen($title,'UTF-8') < 10 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should be at least 10 characters</p></li>";
  }
  $description = $content;
  $description = strip_tags($description);
  if (empty($description)) {
    $errors["desc"] = "<li><p>description is required</p></li>";
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = "<li><p>description must at least contain one letter</p></li>";
    }
  }
  //make sure that category selected
//  if(!get_post_meta($id, 'success_story_category',true)){
//    $errors["category"] = "<li><p>category is required</p></li>";
//  }
  return $errors;
}

function checkOpenDatasetValidation($id, $title, $errors) {
  $quickEditMode = ( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'inline-save')?TRUE:FALSE;
   // i.e: in quick edit mode
  if( $quickEditMode ) {
    $description  = get_field( 'description', $id );
    $total_files  = get_field( 'resources', $id );
  }
  else {
    $description  = $_POST['acf']['field_5712439d16808'];
    $total_files = $_POST['acf']['field_571244f11680e'];
  }

  if (mb_strlen($title,'UTF-8') > 100 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should not be more than 100 characters</p></li>";
  }

  if (mb_strlen($title,'UTF-8') < 10 && strlen($title) != 0 && !isset($errors["title"])) {
    $errors["title"] = "<li><p>title should be at least 10 characters</p></li>";
  }

  $description = strip_tags($description);
  if (empty($description)) {
    $errors["desc"] = "<li><p>description is required</p></li>";
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = "<li><p>description must at least contain one letter</p></li>";
    }
  }

  //validate at least one resource is published
  global $extension_mime_types_conv;
  $total_size = sizeof($total_files);
  $is_published = false;
  $index = 0;
  foreach ($total_files as $key => $value) {
    if( $quickEditMode ) {
      if( $value[ 'resource_status' ] == 'publish' ) {
          $is_published = true;
      }

      if( $extension_mime_types_conv[ $value[ 'upload' ][ 'mime_type' ] ] == null ) {
          $errors["resources_extensions"] = "<li><p>Resources should be one of the following extensions (pdf,csv,json,xml,html,doc,docx,xls,jpg,jpeg,png)</p></li>";
      }
    }
    else {
      if( $index < ( $total_size - 1 ) ) {
        $current_status = $total_files[ $key ][ 'field_575d1a79e4b65' ];
        if( $current_status == 'publish' ) {
          $is_published = true;
          break;
        }
      }
    }
    $index ++;
  }

  if( !$quickEditMode ) {
    //validate resources extensions
    foreach ( $total_files as $key => $value )  {
      if($total_files[$key]['fiexld_5713620dc5d37'] != NULL)
      {
        //load file mime type
        $attachment = get_post($total_files[$key]['field_5713620dc5d37']);
        if($attachment)
        {
          if($extension_mime_types_conv[$attachment->post_mime_type] == null)
          {
            $errors["resources_extensions"] = "<li><p>Resources should be one of the following extensions (pdf,csv,json,xml,html,doc,docx,xls,xlsx,jpg,jpeg,png)</p></li>";
            break;
          }
        }
      }
    }
  }

  return $errors;
}

function checkRequestCenterValidation($post_id, $title, $errors) {

  // i.e: in quick edit mode
  if( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] == 'inline-save') {
    $description = get_field( 'description', $post_id );
    $type = get_field( 'request_center_type', $post_id );
  } else {
    $type = $_POST['acf']['field_5728669be92dc'];
    $description = $_POST['acf']['field_57286759e92df'];
  }

  $description = strip_tags($description);
  if (empty($description)) {
    $errors["#acf-field-description"] = "<li><p>description is required</p></li>";
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["#acf-field-description"] = "<li><p>description must at least contain one letter</p></li>";
    }
  }
  if ( empty( $type ) ) {
    $errors["#acf-field-type"] = "<li><p>type is required</p></li>";
  }

  return $errors;
}

}

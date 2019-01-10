<?php
function ef_add_feedback_front_end() {
    global $wpdb;
    global $ef_email_msg_labels;
    global $ef_email_msg_labels_ar;
    
    $ef_feedback_messages = array("errors" => array());
    $errors = checkFeedbackFrontendValidation();
    $ef_feedback_messages["errors"] = $errors;
    if($ef_feedback_messages["errors"]){
      set_query_var("ef_feedback_messages", $ef_feedback_messages);
      return false;
    }
    $_POST["feedback_description"] = strip_js_tags($_POST["feedback_description"]);
    $my_post = array(
      'post_title' => $_POST["feedback_title"],
      'post_content' => $_POST["feedback_description"],
      'post_author' => get_current_user_id(),
      'post_type' => 'feedback',
      'post_status' => 'pending'
    );
    $post_id = wp_insert_post($my_post);
    set_query_var( "ef_feedback_messages", array( "success" => array( __( "Your feedback has been submitted successfully, we will get back to you soon.", "egyptfoss" ) ) ) );
    
    //adjust post name
    $my_post = array(
        'ID'           => $post_id,
        'post_name' => wp_unique_post_slug(str_replace(' ', '-', strtolower($_POST["feedback_title"])), $post_id, 'publish', 'feedback', 0)
    );
    // Update the post into the database
    wp_update_post( $my_post );
    
    // Add sections in post_meta
    update_post_meta($post_id, 'sections', $_POST['post_sections']);

    //send email
    $current_user = wp_get_current_user();
    $to = $current_user->user_email;

    $user_id = $current_user->ID;
    $file = get_user_meta($user_id, 'prefered_language', true);
    if($file == "en")
    {
        $messages = $ef_email_msg_labels;
    }
    else 
    {
        $messages = $ef_email_msg_labels_ar;
    }
    $subject = $messages['Thanks for your feedback'];
    $title = $messages['Your feedback submitted successfully'];

    $msg = sprintf($messages['Hi, %s'],  bp_core_get_user_displayname($user_id))."<br/><br/>";
    $msg .= $messages['Thank you for your feedback. Your feedback has been submitted successfully, we will get back to you soon.'];
    $args = array(
        "title" => $title,
        "message" => $msg
    );

    set_query_var( 'template_inputs', serialize($args));
    ob_start();
    get_template_part( 'mail-templates/email-content' );
    $message = ob_get_contents();
    ob_end_clean();
    
    wp_mail($to,$title,$message);
}

function checkFeedbackFrontendValidation() {
  $errors = array();
  if (mb_strlen($_POST['feedback_title'],'UTF-8') == 0) {
    $errors["title"] = __("Title", "egyptfoss"). ' ' . __("is required",'egyptfoss');
  }else{
    if (mb_strlen($_POST['feedback_title'],'UTF-8') > 100 && mb_strlen($_POST['feedback_title'],'UTF-8') != 0) {
      $errors["title"] = __("Title", "egyptfoss"). ' ' . sprintf(__("should not be more than %d characters",'egyptfoss'),100);
    }

    if (mb_strlen($_POST['feedback_title'],'UTF-8') < 10 && mb_strlen($_POST['feedback_title'],'UTF-8') != 0) {
      $errors["title"] = __("Title", "egyptfoss"). ' ' . sprintf(__("should be at least %d characters",'egyptfoss'),10);
    }
  }
  
  $description = $_POST['feedback_description'];
  $description = strip_tags($description);
  if (empty($description)) {
    $errors["desc"] = __("Content", "egyptfoss"). ' ' . __("is required",'egyptfoss');
  } else {
    $desc_is_numbers_only = preg_match("/^[0-9]{1,}$/", $description);
    $desc_contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $description);

    if (($desc_is_numbers_only > 0 || !$desc_contains_letters)) {
      $errors["desc"] = __("Content", "egyptfoss"). ' ' . __("must at least contain one letter",'egyptfoss');
    }
  }
  return $errors;
}
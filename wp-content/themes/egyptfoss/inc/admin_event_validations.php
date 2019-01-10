<?php
$tribe_events = array('tribe_events');

function load_event_admin_scripts($hook) {
  global $tribe_events;
  $postType = get_current_screen()->post_type;
  if (in_array($postType, $tribe_events)) {
    wp_enqueue_script('admin_scripts', get_stylesheet_directory_uri() . '/js/confirm_events_validations.js');
  }
}
add_action('admin_enqueue_scripts', 'load_event_admin_scripts');

function validate_save_event($post_id, $post) {
  global $tribe_events;
  if ($_GET["action"] == "trash") {
    return;
  }
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || ! in_array($post->post_type, $tribe_events)) {
    return;
  }
  $errors = array();
  $title = $post->post_title;
  $is_numbers_only = preg_match("/^[0-9]{1,}$/", $title);
  $contains_letters = preg_match("/[أ-يa-zA-Z\:]{1,}/", $title);
  if (!$title) {
    $errors['title'] = "Event title is required";
  } else {
    if ($is_numbers_only > 0) {
      $errors['title'] = "must contain at least one letter";
    } else {
      if (!$contains_letters) {
        $errors['title'] = "Must contain at least one letter";
      }
    }
  }
  if (!empty($errors)) {
    remove_action('save_post', 'validate_save_event');
    $post->post_status = 'draft';
    $post_type_error_option = $post->post_type.'_errors';
    update_option($post_type_error_option, $errors);
    wp_update_post($post);
    add_action('save_post', 'validate_save_event');
    add_filter('redirect_post_location', 'validate_event_redirect_filter');
  }
}
add_action('save_post', 'validate_save_event', 10, 2);

function validate_event_redirect_filter($location) {
  $location = remove_query_arg('message', $location);
  $postType = get_current_screen()->post_type;
  $location = add_query_arg($postType, 'error', $location);
  return $location;
}

function validate_event_error_admin_message() {
  global $tribe_events;
  foreach ($tribe_events as $tribe_event) {
    if (isset($_GET[$tribe_event]) && $_GET[$tribe_event] == 'error') {
      $tribe_event_error_option = $tribe_event.'_errors';
      $errors = get_option($tribe_event_error_option);
      delete_option($tribe_event_error_option);
      if ($errors) {
        $display = '<div id="notice" class="error"><ul>';
        foreach ($errors as $error) {
          $display .= '<li>' . $error . '</li>';
        }
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
  }
}
add_action('admin_notices', 'validate_event_error_admin_message');

function ef_unregister_taxonomy(){
  register_taxonomy('post_tag', array('post'));
  register_taxonomy('tribe_events_cat', array());
}
add_action('init', 'ef_unregister_taxonomy');

function new_modify_event_list( $column ) {
  $column['type'] = 'Type';
  $column['creation_date'] = 'Creation Date';
  unset($column['events-cats']);
  return $column;
}
add_filter('manage_tribe_events_posts_columns', 'new_modify_event_list', 999 );

function new_modify_event_list_row( $column, $post_id ) {
  $post = get_post($post_id);
  if($post->post_type == 'tribe_events') {
    global $events_types;
    switch ( $column ) {
      case 'type':
        $meta = get_post_meta($post_id, 'event_type', true);
        echo (isset($events_types[$meta])) ? $events_types[$meta] : '-';
        break;

      case 'creation_date':
        echo $post->post_date;
        break;
    }
  }
}
add_action('manage_posts_custom_column' , 'new_modify_event_list_row', 10, 2 );

function sort_event_by_date_column( $clauses, $query ) {
  global $wpdb;
  if (is_admin() && $query->is_main_query() && ($query->get('post_type') == 'tribe_events')) {
    $clauses['orderby'] = " {$wpdb->posts}.post_date DESC ";
  }
  return $clauses;
}
add_filter('posts_clauses', 'sort_event_by_date_column', 10, 2);
<?php 
function custom_error_pages() {
  global $wp_query;
  if(isset($_REQUEST['status']) && $_REQUEST['status'] == 403) {
    $wp_query->is_404 = FALSE;
    $wp_query->is_page = TRUE;
    $wp_query->is_singular = TRUE;
    $wp_query->is_single = FALSE;
    $wp_query->is_home = FALSE;
    $wp_query->is_archive = FALSE;
    $wp_query->is_category = FALSE;
    add_filter('wp_title','custom_error_title',65000,2);
    add_filter('body_class','custom_error_class');
    status_header(403);
    get_template_part('403');
    exit;
  }

  if(isset($_REQUEST['status']) && $_REQUEST['status'] == 401) {
    $wp_query->is_404 = FALSE;
    $wp_query->is_page = TRUE;
    $wp_query->is_singular = TRUE;
    $wp_query->is_single = FALSE;
    $wp_query->is_home = FALSE;
    $wp_query->is_archive = FALSE;
    $wp_query->is_category = FALSE;
    add_filter('wp_title','custom_error_title',65000,2);
    add_filter('body_class','custom_error_class');
    status_header(401);
    get_template_part('401');
    exit;
  }
}
 
function custom_error_title($title='',$sep='') {
  if(isset($_REQUEST['status']) && $_REQUEST['status'] == 403)
    return "Forbidden ".$sep." ".get_bloginfo('name');

  if(isset($_REQUEST['status']) && $_REQUEST['status'] == 401)
    return "Unauthorized ".$sep." ".get_bloginfo('name');
}
 
function custom_error_class($classes) {
  if(isset($_REQUEST['status']) && $_REQUEST['status'] == 403) {
    $classes[]="error403";
    return $classes;
  }

  if(isset($_REQUEST['status']) && $_REQUEST['status'] == 401) {
    $classes[]="error401";
    return $classes;
  }
}
add_action('wp','custom_error_pages');

function my_nonce_message ($translation) {
  if ($translation == 'Are you sure you want to do this?')
    return "Access Denied! You don't have permission to access this page or you have signed out.";
  else
    return $translation;
}
add_filter('gettext', 'my_nonce_message');
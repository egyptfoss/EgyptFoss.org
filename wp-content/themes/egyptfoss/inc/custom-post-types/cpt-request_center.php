<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$ef_custom_post_type_cats = array(
  'request_center_type'=>array("singular_name" => "Type","plural_name"=>"Types"),
  'target_bussiness_relationship'=>array("singular_name" => "Bussiness relationship","plural_name"=>"Bussiness relationships"),
  );
function custom_post_type_request_center() {  
    // Set UI labels for Custom Post Type
    $labels = array(
      'name' => _x("Request center", 'post type general name', 'your-plugin-textdomain'),
      'singular_name' => _x("Request", 'post type singular name', 'your-plugin-textdomain'),
      'add_new' => _x('Add '."Request", 'request center', 'your-plugin-textdomain'),
      'add_new_item' => __('Add New '."Request", 'your-plugin-textdomain'),
      'edit_item' => __('Edit '."Request", 'your-plugin-textdomain'),
      'new_item' => __('New '."Request", 'your-plugin-textdomain'),
      'all_items' => __('All '."Requests", 'your-plugin-textdomain'),
      'view_item' => __('View '."Request", 'your-plugin-textdomain'),
      'search_items' => __('Search '."Requests", 'your-plugin-textdomain'),
      'not_found' => __('No '. "Requests" .' found', 'your-plugin-textdomain'),
      'not_found_in_trash' => __('No '. "Requests" .' found in the Trash', 'your-plugin-textdomain'),
      'parent_item_colon' => '',
      'menu_name' => 'Request center'
    );
    
    // Set other options for Custom Post Type
    $args = array(
      'label' => __("request_center", 'twentythirteen'),
      'description' => __("request_center", 'twentythirteen'),
      'menu_icon' => "dashicons-image-filter",
      'labels' => $labels,
      // Features this CPT supports in Post Editor
      'supports' => array('title',/*'editor', 'thumbnail','comments'/* , 'editor', 'author', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', */),
      /* A hierarchical CPT is like Pages and can have
       * Parent and child items. A non-hierarchical CPT
       * is like Posts.
       */
      'hierarchical' => false,
      'public' => true,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_nav_menus' => true,
      'show_in_admin_bar' => true,
      'menu_position' => 5,
      'can_export' => true,
      'has_archive' => true,
      'exclude_from_search' => false,
      'publicly_queryable' => true,
      'capability_type' => 'post',
      'rewrite' => array('slug' => (defined('DOING_AJAX') && DOING_AJAX)?"request-center":"(ar|en)/request-center"),
    );
    
    // Registering request_center Custom Post Type
    register_post_type("request_center", $args);
    
    global $ef_custom_post_type_cats;
    foreach($ef_custom_post_type_cats as $key => $values)
    register_taxonomy(
      $key, "request_center", array(
      'query_var'             => false,  
      'hierarchical'          => false,
      'update_count_callback' => '',
      'public'                => true,
      'show_ui'               => true,
      'show_in_quick_edit'    => false,
      'labels'                => array(
        'name'              => $values['plural_name'],
        'singular_name'     => $values['singular_name'],
        'search_items'      => 'Search '.$values['plural_name'],
        'all_items'         => 'All '.$values['plural_name'],
        'edit_item'         => 'Edit '.$values['singular_name'],
        'update_item'       => 'Update '.$values['singular_name'],
        'add_new_item'      => 'Add New '.$values['singular_name'],
        'new_item_name'     => 'New '.$values['singular_name'].' Name',
        'not_found'         => 'No '.$values['plural_name'].' found',
        'not_found_in_trash'=> 'No '.$values['plural_name'].' found in Trash',
      ),
    )
  );
}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not
 * unnecessarily executed.
 */
add_action('init', 'custom_post_type_request_center', 10);

/**
 * request center update messages.
 *
 * See /wp-admin/edit-form-advanced.php
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function codex_request_center_updated_messages($messages) {
  $post = get_post();
  $post_type = get_post_type($post);
  $post_type_object = get_post_type_object($post_type);
  

  if ($post_type == "request_center") {
    $messages["request_center"] = array(
      0 => '', // Unused. Messages start at index 1.
      1 => __("Request".' updated.', 'your-plugin-textdomain'),
      2 => __('Custom field updated.', 'your-plugin-textdomain'),
      3 => __('Custom field deleted.', 'your-plugin-textdomain'),
      4 => __("Request".' updated.', 'your-plugin-textdomain'),
      /* translators: %s: date and time of the revision */
      5 => isset($_GET['revision']) ? sprintf(__("Request".' restored to revision from %s', 'your-plugin-textdomain'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
      6 => __("Request".' published.', 'your-plugin-textdomain'),
      7 => __("Request".' saved.', 'your-plugin-textdomain'),
      8 => __("Request".' submitted.', 'your-plugin-textdomain'),
      9 => sprintf(
        __("Request".' scheduled for: <strong>%1$s</strong>.', 'your-plugin-textdomain'),
        // translators: Publish box date format, see http://php.net/date
        date_i18n(__('M j, Y @ G:i', 'your-plugin-textdomain'), strtotime($post->post_date))
      ),
      10 => __("Request".' draft updated.', 'your-plugin-textdomain')
    );

    if ($post_type_object->publicly_queryable) {
      $permalink = get_permalink($post->ID);

      $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View '."Request", 'your-plugin-textdomain'));
      $messages[$post_type][1] .= $view_link;
      $messages[$post_type][6] .= $view_link;
      $messages[$post_type][9] .= $view_link;

      $preview_permalink = add_query_arg('preview', 'true', $permalink);
      $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview '."Request", 'your-plugin-textdomain'));
      $messages[$post_type][8] .= $preview_link;
      $messages[$post_type][10] .= $preview_link;
    }
  }

  return $messages;
}
add_filter( 'post_updated_messages', 'codex_request_center_updated_messages' );

function my_bulk_post_updated_messages_filter_request_center($bulk_messages, $bulk_counts) {
  
  $bulk_messages["request_center"] = array(
    'updated' => _n('%s '."Request".' updated.', '%s my_cpts updated.', $bulk_counts['updated']),
    'locked' => _n('%s '."Request".' not updated, somebody is editing it.', '%s '."Request".' not updated, somebody is editing them.', $bulk_counts['locked']),
    'deleted' => _n('%s '."Request".' permanently deleted.', '%s '."Request".' permanently deleted.', $bulk_counts['deleted']),
    'trashed' => _n('%s '."Request".' moved to the Trash.', '%s '."Request".' moved to the Trash.', $bulk_counts['trashed']),
    'untrashed' => _n('%s '."Request".' restored from the Trash.', '%s '."Request".' restored from the Trash.', $bulk_counts['untrashed']),
  );

  return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'my_bulk_post_updated_messages_filter_request_center', 10, 2);

// customizing publish button
function wpse_125800_custom_publish_box_request_center() {
  $postType = get_current_screen()->post_type;
  if (is_admin() && $postType == "request_center") {
    $style = '';
    $style .= '<style type="text/css">';
    $style .= "#edit-slug-box, #minor-publishing-actions, #visibility, .num-revisions, .curtime, div.row-actions>span[class='edit'] + span, .row-actions>span.view";
   // $style .= '{display: none; }';  // If this line is commented this will display the "save draft, preview" buttons. 
    $style .= '</style>';
    echo $style;
  }
}
add_action('admin_head', 'wpse_125800_custom_publish_box_request_center');

function remove_request_center_meta() {
  remove_meta_box('tagsdiv-type', 'request_center', 'side');
  //$ds_hide_custom_sidebar = "<style type=\"text/css\"> #mm_custom_sidebar_meta_box { display: none; }</style>";
}
add_action('admin_menu', 'remove_request_center_meta');

function request_center_change_default_featured_image_labels($labels) {
  
  $labels->featured_image =  __("Request".' Image');
  $labels->set_featured_image = __('Set '."Request".' image');
  $labels->remove_featured_image = __('Remove '."Request".' image');
  $labels->use_featured_image =  __('Use as '."Request".' image');
  return $labels;
}
add_filter('post_type_labels_request_center', 'request_center_change_default_featured_image_labels', 10, 1);
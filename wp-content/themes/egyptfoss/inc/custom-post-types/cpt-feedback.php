<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function custom_post_type_feedback() {
    // Set UI labels for Custom Post Type
    $labels = array(
      'name' => _x('Feedback', 'post type general name', 'your-plugin-textdomain'),
      'singular_name' => _x('feedback', 'post type singular name', 'your-plugin-textdomain'),
      'add_new' => _x('Add Feedback', 'Feedback', 'your-plugin-textdomain'),
      'add_new_item' => __('Add New Feedback', 'your-plugin-textdomain'),
      'edit_item' => __('Edit Feedback', 'your-plugin-textdomain'),
      'new_item' => __('New Feedback', 'your-plugin-textdomain'),
      'all_items' => __('All Feedbacks', 'your-plugin-textdomain'),
      'view_item' => __('View Feedbacks', 'your-plugin-textdomain'),
      'search_items' => __('Search Feedbacks', 'your-plugin-textdomain'),
      'not_found' => __('No feedbacks found', 'your-plugin-textdomain'),
      'not_found_in_trash' => __('No feedbacks found in the Trash', 'your-plugin-textdomain'),
      'parent_item_colon' => '',
      'menu_name' => 'Feedbacks'
    );
    
    // Set other options for Custom Post Type
    $args = array(
      'label' => __('Feedback', 'twentythirteen'),
      'description' => __('Feedback', 'twentythirteen'),
      'menu_icon' => "dashicons-megaphone",
      'labels' => $labels,
      // Features this CPT supports in Post Editor
      'supports' => array('title','editor', 'comments'/* , 'editor', 'author', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', */),
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
      'rewrite' => array('slug' => (defined('DOING_AJAX') && DOING_AJAX)?"feedback":"(ar|en)/feedback"),
    );
    
    // Registering feedback Custom Post Type
    register_post_type('feedback', $args);
}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not
 * unnecessarily executed.
 */
add_action('init', 'custom_post_type_feedback', 10);

/**
 * feedback update messages.
 *
 * See /wp-admin/edit-form-advanced.php
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function codex_feedback_updated_messages($messages) {
  $post = get_post();
  $post_type = get_post_type($post);
  $post_type_object = get_post_type_object($post_type);

  if ($post_type == 'feedback') {
    $messages['feedback'] = array(
      0 => '', // Unused. Messages start at index 1.
      1 => __('Feedback updated.', 'your-plugin-textdomain'),
      2 => __('Custom field updated.', 'your-plugin-textdomain'),
      3 => __('Custom field deleted.', 'your-plugin-textdomain'),
      4 => __('Feedback updated.', 'your-plugin-textdomain'),
      /* translators: %s: date and time of the revision */
      5 => isset($_GET['revision']) ? sprintf(__('Feedback restored to revision from %s', 'your-plugin-textdomain'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
      6 => __('Feedback published.', 'your-plugin-textdomain'),
      7 => __('Feedback saved.', 'your-plugin-textdomain'),
      8 => __('Feedback submitted.', 'your-plugin-textdomain'),
      9 => sprintf(
        __('Feedback scheduled for: <strong>%1$s</strong>.', 'your-plugin-textdomain'),
        // translators: Publish box date format, see http://php.net/date
        date_i18n(__('M j, Y @ G:i', 'your-plugin-textdomain'), strtotime($post->post_date))
      ),
      10 => __('Feedback draft updated.', 'your-plugin-textdomain')
    );

    if ($post_type_object->publicly_queryable) {
      $permalink = get_permalink($post->ID);

      $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View Feedback', 'your-plugin-textdomain'));
      $messages[$post_type][1] .= $view_link;
      $messages[$post_type][6] .= $view_link;
      $messages[$post_type][9] .= $view_link;

      $preview_permalink = add_query_arg('preview', 'true', $permalink);
      $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview feedback', 'your-plugin-textdomain'));
      $messages[$post_type][8] .= $preview_link;
      $messages[$post_type][10] .= $preview_link;
    }
  }

  return $messages;
}
add_filter( 'post_updated_messages', 'codex_feedback_updated_messages' );

function my_bulk_post_updated_messages_filter_feedback($bulk_messages, $bulk_counts) {
  $bulk_messages['feedback'] = array(
    'updated' => _n('%s feedback updated.', '%s my_cpts updated.', $bulk_counts['updated']),
    'locked' => _n('%s feedback not updated, somebody is editing it.', '%s feedback not updated, somebody is editing them.', $bulk_counts['locked']),
    'deleted' => _n('%s feedback permanently deleted.', '%s feedback permanently deleted.', $bulk_counts['deleted']),
    'trashed' => _n('%s feedback moved to the Trash.', '%s feedback moved to the Trash.', $bulk_counts['trashed']),
    'untrashed' => _n('%s feedback restored from the Trash.', '%s feedback restored from the Trash.', $bulk_counts['untrashed']),
  );

  return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'my_bulk_post_updated_messages_filter_feedback', 10, 2);

// customizing publish button
function wpse_125800_custom_publish_box_feedback() {
  $postType = get_current_screen()->post_type;
  if (is_admin() && $postType == "feedback") {
    $style = '';
    $style .= '<style type="text/css">';
    $style .= "#edit-slug-box, #minor-publishing-actions, #visibility, .num-revisions, .curtime, div.row-actions>span[class='edit'] + span, .row-actions>span.view";
   // $style .= '{display: none; }';  // If this line is commented this will display the "save draft, preview" buttons. 
    $style .= '</style>';
    echo $style;
  }
}
add_action('admin_head', 'wpse_125800_custom_publish_box_feedback');

function remove_feedback_meta() {
  remove_meta_box('tagsdiv-type', 'feedback', 'side');
  //$ds_hide_custom_sidebar = "<style type=\"text/css\"> #mm_custom_sidebar_meta_box { display: none; }</style>";
}
add_action('admin_menu', 'remove_feedback_meta');
<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//$ef_custom_post_type_cats = array(
//  'expert_thought_type'=>array("singular_name" => "Type","plural_name"=>"Types"),
//  'target_bussiness_relationship'=>array("singular_name" => "Bussiness relationship","plural_name"=>"Bussiness relationships"),
//  );
function custom_post_type_expert_thought() {
    // Set UI labels for Custom Post Type
    $labels = array(
      'name' => _x("Expert Thought", 'post type general name', 'your-plugin-textdomain'),
      'singular_name' => _x("Expert Thought", 'post type singular name', 'your-plugin-textdomain'),
      'add_new' => _x('Add '."Expert Thought", "Expert Thought", 'your-plugin-textdomain'),
      'add_new_item' => __('Add New '."Expert Thought", 'your-plugin-textdomain'),
      'edit_item' => __('Edit '."Expert Thought", 'your-plugin-textdomain'),
      'new_item' => __('New '."Expert Thought", 'your-plugin-textdomain'),
      'all_items' => __('All '."Expert Thoughts", 'your-plugin-textdomain'),
      'view_item' => __('View '."Expert Thought", 'your-plugin-textdomain'),
      'search_items' => __('Search '."Expert Thoughts", 'your-plugin-textdomain'),
      'not_found' => __('No '. "Expert Thoughts" .' found', 'your-plugin-textdomain'),
      'not_found_in_trash' => __('No '. "Expert Thoughts" .' found in the Trash', 'your-plugin-textdomain'),
      'parent_item_colon' => '',
      'menu_name' => 'Expert Thoughts'
    );
    
    // Set other options for Custom Post Type
    $args = array(
      'label' => __("expert_thought", 'twentythirteen'),
      'description' => __("expert_thought", 'twentythirteen'),
      'menu_icon' => "dashicons-lightbulb",
      'labels' => $labels,
      // Features this CPT supports in Post Editor
      'supports' => array('title','editor', 'thumbnail', 'comments'/* , 'editor', 'author', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', */),
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
      'menu_position' => 6,
      'can_export' => true,
      'has_archive' => true,
      'exclude_from_search' => false,
      'publicly_queryable' => true,
      'capability_type' => 'post',
      'rewrite' => array('slug' => (defined('DOING_AJAX') && DOING_AJAX)?"expert-thoughts":"(ar|en)/expert-thoughts"),
    );
    
    // Registering expert_thought Custom Post Type
    register_post_type("expert_thought", $args);
    
}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not
 * unnecessarily executed.
 */
add_action('init', 'custom_post_type_expert_thought',10);

/**
 * expert thought update messages.
 *
 * See /wp-admin/edit-form-advanced.php
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function codex_expert_thought_updated_messages($messages) {
  $post = get_post();
  $post_type = get_post_type($post);
  $post_type_object = get_post_type_object($post_type);
  

  if ($post_type == "expert_thought") {
    $messages["expert_thought"] = array(
      0 => '', // Unused. Messages start at index 1.
      1 => __("Expert Thought".' updated.', 'your-plugin-textdomain'),
      2 => __('Custom field updated.', 'your-plugin-textdomain'),
      3 => __('Custom field deleted.', 'your-plugin-textdomain'),
      4 => __("Expert Thought".' updated.', 'your-plugin-textdomain'),
      /* translators: %s: date and time of the revision */
      5 => isset($_GET['revision']) ? sprintf(__("Expert Thought".' restored to revision from %s', 'your-plugin-textdomain'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
      6 => __("Expert Thought".' published.', 'your-plugin-textdomain'),
      7 => __("Expert Thought".' saved.', 'your-plugin-textdomain'),
      8 => __("Expert Thought".' submitted.', 'your-plugin-textdomain'),
      9 => sprintf(
        __("Expert Thought".' scheduled for: <strong>%1$s</strong>.', 'your-plugin-textdomain'),
        // translators: Publish box date format, see http://php.net/date
        date_i18n(__('M j, Y @ G:i', 'your-plugin-textdomain'), strtotime($post->post_date))
      ),
      10 => __("Expert Thought".' draft updated.', 'your-plugin-textdomain')
    );

    if ($post_type_object->publicly_queryable) {
      $permalink = get_permalink($post->ID);

      $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View '."Expert Thought", 'your-plugin-textdomain'));
      $messages[$post_type][1] .= $view_link;
      $messages[$post_type][6] .= $view_link;
      $messages[$post_type][9] .= $view_link;

      $preview_permalink = add_query_arg('preview', 'true', $permalink);
      $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview '."Expert Thought", 'your-plugin-textdomain'));
      $messages[$post_type][8] .= $preview_link;
      $messages[$post_type][10] .= $preview_link;
    }
  }

  return $messages;
}
add_filter( 'post_updated_messages', 'codex_expert_thought_updated_messages' );

function my_bulk_post_updated_messages_filter_expert_thought($bulk_messages, $bulk_counts) {
  
  $bulk_messages["expert_thought"] = array(
    'updated' => _n('%s '."Expert Thought".' updated.', '%s my_cpts updated.', $bulk_counts['updated']),
    'locked' => _n('%s '."Expert Thought".' not updated, somebody is editing it.', '%s '."Expert Thought".' not updated, somebody is editing them.', $bulk_counts['locked']),
    'deleted' => _n('%s '."Expert Thought".' permanently deleted.', '%s '."Expert Thought".' permanently deleted.', $bulk_counts['deleted']),
    'trashed' => _n('%s '."Expert Thought".' moved to the Trash.', '%s '."Expert Thought".' moved to the Trash.', $bulk_counts['trashed']),
    'untrashed' => _n('%s '."Expert Thought".' restored from the Trash.', '%s '."Expert Thought".' restored from the Trash.', $bulk_counts['untrashed']),
  );

  return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'my_bulk_post_updated_messages_filter_expert_thought', 10, 2);

// customizing publish button
function wpse_125800_custom_publish_box_expert_thought() {
  $postType = get_current_screen()->post_type;
  if (is_admin() && $postType == "expert_thought") {
    $style = '';
    $style .= '<style type="text/css">';
    $style .= "#edit-slug-box, #minor-publishing-actions, #visibility, .num-revisions, .curtime, div.row-actions>span[class='edit'] + span, .row-actions>span.view";
   // $style .= '{display: none; }';  // If this line is commented this will display the "save draft, preview" buttons. 
    $style .= '</style>';
    echo $style;
  }
}
add_action('admin_head', 'wpse_125800_custom_publish_box_expert_thought');

function remove_expert_thought_meta() {
  remove_meta_box('tagsdiv-type', 'expert_thought', 'side');
  //$ds_hide_custom_sidebar = "<style type=\"text/css\"> #mm_custom_sidebar_meta_box { display: none; }</style>";
}
add_action('admin_menu', 'remove_expert_thought_meta');

function expert_thought_change_default_featured_image_labels($labels) {
  
  $labels->featured_image =  __("Expert Thought".' Image');
  $labels->set_featured_image = __('Set '."Expert Thought".' image');
  $labels->remove_featured_image = __('Remove '."Expert Thought".' image');
  $labels->use_featured_image =  __('Use as '."Expert Thought".' image');
  return $labels;
}
add_filter('post_type_labels_expert_thought', 'expert_thought_change_default_featured_image_labels', 10, 1);
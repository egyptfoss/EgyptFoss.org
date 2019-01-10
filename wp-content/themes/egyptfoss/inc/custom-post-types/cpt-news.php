<?php

/*
 * Creating a function to create our CPT
 */

function custom_post_type_news() {
  // Set UI labels for Custom Post Type
  $labels = array(
    'name' => _x('News', 'post type general name', 'your-plugin-textdomain'),
    'singular_name' => _x('news', 'post type singular name', 'your-plugin-textdomain'),
    'add_new' => _x('Add New', 'news', 'your-plugin-textdomain'),
    'add_new_item' => __('Add New news', 'your-plugin-textdomain'),
    'edit_item' => __('Edit news', 'your-plugin-textdomain'),
    'new_item' => __('New news', 'your-plugin-textdomain'),
    'all_items' => __('All news', 'your-plugin-textdomain'),
    'view_item' => __('View news', 'your-plugin-textdomain'),
    'search_items' => __('Search news', 'your-plugin-textdomain'),
    'not_found' => __('No news found', 'your-plugin-textdomain'),
    'not_found_in_trash' => __('No news found in the Trash', 'your-plugin-textdomain'),
    'parent_item_colon' => '',
    'menu_name' => 'News'
  );
// Set other options for Custom Post Type
  $args = array(
    'label' => __('News', 'twentythirteen'),
    'description' => __('News', 'twentythirteen'),
    'menu_icon' => "dashicons-megaphone",
    'labels' => $labels,
    // Features this CPT supports in Post Editor
    'supports' => array('title', 'thumbnail', 'comments'/* , 'editor', 'author', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', */),
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
    'rewrite' => array('slug' => 'news'),
  );
// Registering news Custom Post Type
  register_post_type('news', $args);

    register_taxonomy(
    'news_category', 'news', array(
      'hierarchical'          => false,
      'update_count_callback' => '',
      'public'            => true,
      'show_ui'           => true,
      'show_in_menu'      => true,
      'show_in_nav_menus' => true,
      'show_in_quick_edit' => false,
      'meta_box_cb'       => false,
      'labels'            => array(
        'name'              => 'Categories',
        'singular_name'     => 'Category',
        'search_items'      => 'Search Category',
        'all_items'         => 'All categories',
        'edit_item'         => 'Edit category',
        'update_item'       => 'Update category',
        'add_new_item'      => 'Add New category',
        'new_item_name'     => 'New category Name',
        'not_found'          => 'No categories found',
        'not_found_in_trash' => 'No categories found in Trash',
      ),
    )
  );

}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not
 * unnecessarily executed.
 */
add_action('init', 'custom_post_type_news', 10);

/**
 * news update messages.
 *
 * See /wp-admin/edit-form-advanced.php
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function codex_news_updated_messages($messages) {
  $post = get_post();
  $post_type = get_post_type($post);
  $post_type_object = get_post_type_object($post_type);

  if ($post_type == 'news') {
    $messages['news'] = array(
      0 => '', // Unused. Messages start at index 1.
      1 => __('news updated.', 'your-plugin-textdomain'),
      2 => __('Custom field updated.', 'your-plugin-textdomain'),
      3 => __('Custom field deleted.', 'your-plugin-textdomain'),
      4 => __('news updated.', 'your-plugin-textdomain'),
      /* translators: %s: date and time of the revision */
      5 => isset($_GET['revision']) ? sprintf(__('news restored to revision from %s', 'your-plugin-textdomain'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
      6 => __('news published.', 'your-plugin-textdomain'),
      7 => __('news saved.', 'your-plugin-textdomain'),
      8 => __('news submitted.', 'your-plugin-textdomain'),
      9 => sprintf(
        __('news scheduled for: <strong>%1$s</strong>.', 'your-plugin-textdomain'),
        // translators: Publish box date format, see http://php.net/date
        date_i18n(__('M j, Y @ G:i', 'your-plugin-textdomain'), strtotime($post->post_date))
      ),
      10 => __('news draft updated.', 'your-plugin-textdomain')
    );

    if ($post_type_object->publicly_queryable) {
      $permalink = get_permalink($post->ID);

      $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View news', 'your-plugin-textdomain'));
      $messages[$post_type][1] .= $view_link;
      $messages[$post_type][6] .= $view_link;
      $messages[$post_type][9] .= $view_link;

      $preview_permalink = add_query_arg('preview', 'true', $permalink);
      $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview news', 'your-plugin-textdomain'));
      $messages[$post_type][8] .= $preview_link;
      $messages[$post_type][10] .= $preview_link;
    }
  }

  return $messages;
}
add_filter( 'post_updated_messages', 'codex_news_updated_messages' );

function my_bulk_post_updated_messages_filter_news($bulk_messages, $bulk_counts) {
  $bulk_messages['news'] = array(
    'updated' => _n('%s news updated.', '%s my_cpts updated.', $bulk_counts['updated']),
    'locked' => _n('%s news not updated, somebody is editing it.', '%s news not updated, somebody is editing them.', $bulk_counts['locked']),
    'deleted' => _n('%s news permanently deleted.', '%s news permanently deleted.', $bulk_counts['deleted']),
    'trashed' => _n('%s news moved to the Trash.', '%s news moved to the Trash.', $bulk_counts['trashed']),
    'untrashed' => _n('%s news restored from the Trash.', '%s news restored from the Trash.', $bulk_counts['untrashed']),
  );

  return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'my_bulk_post_updated_messages_filter_news', 10, 2);

// customizing publish button
function wpse_125800_custom_publish_box_news() {
  $postType = get_current_screen()->post_type;
  if (is_admin() && $postType == "news") {
    $style = '';
    $style .= '<style type="text/css">';
    $style .= "#edit-slug-box, #minor-publishing-actions, #visibility, .num-revisions, .curtime, div.row-actions>span[class='edit'] + span, .row-actions>span.view";
   // $style .= '{display: none; }';  // If this line is commented this will display the "save draft, preview" buttons.
    $style .= '</style>';
    echo $style;
  }
}
add_action('admin_head', 'wpse_125800_custom_publish_box_news');

function remove_news_meta() {
  remove_meta_box('tagsdiv-type', 'news', 'side');
  //$ds_hide_custom_sidebar = "<style type=\"text/css\"> #mm_custom_sidebar_meta_box { display: none; }</style>";
}
add_action('admin_menu', 'remove_news_meta');

function news_change_default_featured_image_labels($labels) {
  $labels->featured_image =  __('News Image');
  $labels->set_featured_image = __('Set news image');
  $labels->remove_featured_image = __('Remove news image');
  $labels->use_featured_image =  __('Use as news image');
  return $labels;
}
add_filter('post_type_labels_news', 'news_change_default_featured_image_labels', 10, 1);

function add_news_cpt_gallery() {
  $postType = get_current_screen()->post_type;
  if ($postType) {
    add_meta_box('featuredgallerydiv', __('Gallery', 'gallery'), 'fg_display_metabox', 'news', 'side', 'high');
  }
}
add_filter('fg_post_types', 'add_news_cpt_gallery');

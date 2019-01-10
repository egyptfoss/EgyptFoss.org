<?php
/*
 * Creating a function to create our CPT
 */
function custom_post_type_open_dataset() {
  // Set UI labels for Custom Post Type
  $labels = array(
    'name' => _x('Open dataset', 'post type general name', 'your-plugin-textdomain'),
    'singular_name' => _x('Open dataset', 'post type singular name', 'your-plugin-textdomain'),
    'add_new' => _x('Add open dataset', 'open dataset', 'your-plugin-textdomain'),
    'add_new_item' => __('Add New open dataset', 'your-plugin-textdomain'),
    'edit_item' => __('Edit open dataset', 'your-plugin-textdomain'),
    'new_item' => __('New open dataset', 'your-plugin-textdomain'),
    'all_items' => __('All open datasets', 'your-plugin-textdomain'),
    'view_item' => __('View open datasets', 'your-plugin-textdomain'),
    'search_items' => __('Search open datasets', 'your-plugin-textdomain'),
    'not_found' => __('No open datasets found', 'your-plugin-textdomain'),
    'not_found_in_trash' => __('No open datasets found in the Trash', 'your-plugin-textdomain'),
    'parent_item_colon' => '',
    'menu_name' => 'Data'
  );
  // Set other options for Custom Post Type
  $args = array(
    'label' => __('Open datasets', 'twentythirteen'),
    'description' => __('Open datasets', 'twentythirteen'),
    'menu_icon' => "dashicons-microphone",
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
    'rewrite' => array('slug' => "open-datasets"),
  );
  // Registering open_datasets Custom Post Type
  register_post_type('open_dataset', $args);

  register_taxonomy(
    'dataset_type', 'open_dataset', array(
      'hierarchical'          => false,
      'update_count_callback' => '',
      'public'            => true,
      'show_ui'           => true,
      'show_in_quick_edit'=> false,
      'labels'            => array(
        'name'              => 'Dataset types',
        'singular_name'     => 'Dataset type',
        'search_items'      => 'Search dataset types',
        'all_items'         => 'All dataset types',
        'edit_item'         => 'Edit dataset type',
        'update_item'       => 'Update dataset type',
        'add_new_item'      => 'Add New dataset type',
        'new_item_name'     => 'New dataset type Name',
        'not_found'          => 'No dataset types found',
        'not_found_in_trash' => 'No dataset types found in Trash',
      ),
    )
  );
  register_taxonomy(
    'datasets_license', 'open_dataset', array(
      'hierarchical'          => false,
      'update_count_callback' => '',
      'public'                => true,
      'show_ui'               => true,
      'show_in_quick_edit'    => false,
      'labels'                => array(
        'name'              => 'Dataset licenses',
        'singular_name'     => 'Dataset license',
        'search_items'      => 'Search dataset licenses',
        'all_items'         => 'All dataset licenses',
        'edit_item'         => 'Edit dataset license',
        'update_item'       => 'Update dataset license',
        'add_new_item'      => 'Add New dataset license',
        'new_item_name'     => 'New dataset license Name',
        'not_found'         => 'No dataset licenses found',
        'not_found_in_trash'=> 'No dataset licenses found in Trash',
      ),
    )
  );
}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not
 * unnecessarily executed.
 */
add_action('init', 'custom_post_type_open_dataset', 10);

/**
 * open dataset update messages.
 * See /wp-admin/edit-form-advanced.php
 * @param array $messages Existing post update messages.
 * @return array Amended post update messages with new CPT update messages.
 */
function codex_open_datasets_updated_messages($messages) {
  $post = get_post();
  $post_type = get_post_type($post);
  $post_type_object = get_post_type_object($post_type);

  if ($post_type == 'open_dataset') {
    $messages['open_dataset'] = array(
      0 => '', // Unused. Messages start at index 1.
      1 => __('open dataset updated.', 'your-plugin-textdomain'),
      2 => __('Custom field updated.', 'your-plugin-textdomain'),
      3 => __('Custom field deleted.', 'your-plugin-textdomain'),
      4 => __('open dataset updated.', 'your-plugin-textdomain'),
      /* translators: %s: date and time of the revision */
      5 => isset($_GET['revision']) ? sprintf(__('open dataset restored to revision from %s', 'your-plugin-textdomain'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
      6 => __('open dataset published.', 'your-plugin-textdomain'),
      7 => __('open dataset saved.', 'your-plugin-textdomain'),
      8 => __('open dataset submitted.', 'your-plugin-textdomain'),
      9 => sprintf(
        __('open dataset scheduled for: <strong>%1$s</strong>.', 'your-plugin-textdomain'),
        // translators: Publish box date format, see http://php.net/date
        date_i18n(__('M j, Y @ G:i', 'your-plugin-textdomain'), strtotime($post->post_date))
      ),
      10 => __('open dataset draft updated.', 'your-plugin-textdomain')
    );

    if ($post_type_object->publicly_queryable) {
      $permalink = get_permalink($post->ID);

      $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View open dataset', 'your-plugin-textdomain'));
      $messages[$post_type][1] .= $view_link;
      $messages[$post_type][6] .= $view_link;
      $messages[$post_type][9] .= $view_link;

      $preview_permalink = add_query_arg('preview', 'true', $permalink);
      $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview opendataset', 'your-plugin-textdomain'));
      $messages[$post_type][8] .= $preview_link;
      $messages[$post_type][10] .= $preview_link;
    }
  }

  return $messages;
}
add_filter( 'post_updated_messages', 'codex_open_datasets_updated_messages' );

function my_bulk_post_updated_messages_filter_open_datasets($bulk_messages, $bulk_counts) {
  $bulk_messages['open_dataset'] = array(
    'updated' => _n('%s open datasets updated.', '%s my_cpts updated.', $bulk_counts['updated']),
    'locked' => _n('%s open datasets not updated, somebody is editing it.', '%s open datasets not updated, somebody is editing them.', $bulk_counts['locked']),
    'deleted' => _n('%s open datasets permanently deleted.', '%s open datasets permanently deleted.', $bulk_counts['deleted']),
    'trashed' => _n('%s open datasets moved to the Trash.', '%s open datasets moved to the Trash.', $bulk_counts['trashed']),
    'untrashed' => _n('%s open datasets restored from the Trash.', '%s open datasets restored from the Trash.', $bulk_counts['untrashed']),
  );

  return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'my_bulk_post_updated_messages_filter_open_datasets', 10, 2);

// customizing publish button
function wpse_125800_custom_publish_box_open_datasets() {
  $postType = get_current_screen()->post_type;
  if (is_admin() && $postType == "open_dataset") {
    $style = '';
    $style .= '<style type="text/css">';
    $style .= "#edit-slug-box, #minor-publishing-actions, #visibility, .num-revisions, .curtime, div.row-actions>span[class='edit'] + span, .row-actions>span.view";
   // $style .= '{display: none; }';  // If this line is commented this will display the "save draft, preview" buttons.
    $style .= '</style>';
    echo $style;
  }
}
add_action('admin_head', 'wpse_125800_custom_publish_box_open_datasets');

function remove_open_datasets_meta() {
  remove_meta_box('tagsdiv-type', 'open_dataset', 'side');
  remove_meta_box('tagsdiv-dataset_type', 'open_dataset', 'side');
  remove_meta_box('tagsdiv-datasets_license', 'open_dataset', 'side');
}
add_action('admin_menu', 'remove_open_datasets_meta');

function open_dataset_change_default_featured_image_labels($labels) {
  $labels->featured_image =  __('Open dataset Image');
  $labels->set_featured_image = __('Set open dataset image');
  $labels->remove_featured_image = __('Remove open dataset image');
  $labels->use_featured_image =  __('Use as open dataset image');
  return $labels;
}
add_filter('post_type_labels_open_dataset', 'open_dataset_change_default_featured_image_labels', 10, 1);

?>

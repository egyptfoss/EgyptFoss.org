<?php

$ef_service_categors_cats = array(
  'service_category'=>array("singular_name" => "Category","plural_name"=>"Categories"),
);
function custom_post_type_service() {  
  $labels = array(
    'name' => _x("Services", 'post type general name', 'your-plugin-textdomain'),
    'singular_name' => _x("Service", 'post type singular name', 'your-plugin-textdomain'),
    'add_new' => _x('Add '."Service", 'service', 'your-plugin-textdomain'),
    'add_new_item' => __('Add New '."Service", 'your-plugin-textdomain'),
    'edit_item' => __('Edit '."Service", 'your-plugin-textdomain'),
    'new_item' => __('New '."Service", 'your-plugin-textdomain'),
    'all_items' => __('All '."Services", 'your-plugin-textdomain'),
    'view_item' => __('View '."Service", 'your-plugin-textdomain'),
    'search_items' => __('Search '."Services", 'your-plugin-textdomain'),
    'not_found' => __('No '. "Services" .' found', 'your-plugin-textdomain'),
    'not_found_in_trash' => __('No '. "Services" .' found in the Trash', 'your-plugin-textdomain'),
    'parent_item_colon' => '',
    'menu_name' => 'Services Market'
  );
  
  // Set other options for Custom Post Type
  $args = array(
    'label' => __("service", 'twentythirteen'),
    'description' => __("service", 'twentythirteen'),
    'menu_icon' => "dashicons-store",
    'labels' => $labels,
    'supports' => array('title', 'editor', 'thumbnail'),
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
    'rewrite' => array('slug' => (defined('DOING_AJAX') && DOING_AJAX)?"marketplace/services":"(ar|en)/marketplace/services"),
  );
  
  // Registering service Custom Post Type
  register_post_type("service", $args);
  
  global $ef_service_categors_cats;
  foreach($ef_service_categors_cats as $key => $values)
  register_taxonomy(
    $key, "service", array(
    'query_var' => false,  
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
        'not_found'          => 'No '.$values['plural_name'].' found',
        'not_found_in_trash' => 'No '.$values['plural_name'].' found in Trash',
      ),
    )
  );
}

add_action('init', 'custom_post_type_service', 10);

function codex_service_updated_messages($messages) {
  $post = get_post();
  $post_type = get_post_type($post);
  $post_type_object = get_post_type_object($post_type);
  

  if ($post_type == "service") {
    $messages["service"] = array(
      0 => '', // Unused. Messages start at index 1.
      1 => __("Service".' updated.', 'your-plugin-textdomain'),
      2 => __('Custom field updated.', 'your-plugin-textdomain'),
      3 => __('Custom field deleted.', 'your-plugin-textdomain'),
      4 => __("Service".' updated.', 'your-plugin-textdomain'),
      /* translators: %s: date and time of the revision */
      5 => isset($_GET['revision']) ? sprintf(__("Service".' restored to revision from %s', 'your-plugin-textdomain'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
      6 => __("Service".' published.', 'your-plugin-textdomain'),
      7 => __("Service".' saved.', 'your-plugin-textdomain'),
      8 => __("Service".' submitted.', 'your-plugin-textdomain'),
      9 => sprintf(
        __("Service".' scheduled for: <strong>%1$s</strong>.', 'your-plugin-textdomain'),
        // translators: Publish box date format, see http://php.net/date
        date_i18n(__('M j, Y @ G:i', 'your-plugin-textdomain'), strtotime($post->post_date))
      ),
      10 => __("Service".' draft updated.', 'your-plugin-textdomain')
    );

    if ($post_type_object->publicly_queryable) {
      $permalink = get_permalink($post->ID);

      $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View '."Service", 'your-plugin-textdomain'));
      $messages[$post_type][1] .= $view_link;
      $messages[$post_type][6] .= $view_link;
      $messages[$post_type][9] .= $view_link;

      $preview_permalink = add_query_arg('preview', 'true', $permalink);
      $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview '."Service", 'your-plugin-textdomain'));
      $messages[$post_type][8] .= $preview_link;
      $messages[$post_type][10] .= $preview_link;
    }
  }

  return $messages;
}
add_filter( 'post_updated_messages', 'codex_servicer_updated_messages' );

function my_bulk_post_updated_messages_filter_service($bulk_messages, $bulk_counts) {
  
  $bulk_messages["service"] = array(
    'updated' => _n('%s '."Service".' updated.', '%s my_cpts updated.', $bulk_counts['updated']),
    'locked' => _n('%s '."Service".' not updated, somebody is editing it.', '%s '."Service".' not updated, somebody is editing them.', $bulk_counts['locked']),
    'deleted' => _n('%s '."Service".' permanently deleted.', '%s '."Service".' permanently deleted.', $bulk_counts['deleted']),
    'trashed' => _n('%s '."Service".' moved to the Trash.', '%s '."Service".' moved to the Trash.', $bulk_counts['trashed']),
    'untrashed' => _n('%s '."Service".' restored from the Trash.', '%s '."Service".' restored from the Trash.', $bulk_counts['untrashed']),
  );

  return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'my_bulk_post_updated_messages_filter_service', 10, 2);

// customizing publish button
function wpse_125800_custom_publish_box_service() {
  $postType = get_current_screen()->post_type;
  if (is_admin() && $postType == "service") {
    $style = '';
    $style .= '<style type="text/css">';
    $style .= "#edit-slug-box, #minor-publishing-actions, #visibility, .num-revisions, .curtime, div.row-actions>span[class='edit'] + span, .row-actions>span.view";
   // $style .= '{display: none; }';  // If this line is commented this will display the "save draft, preview" buttons. 
    $style .= '</style>';
    echo $style;
  }
}
add_action('admin_head', 'wpse_125800_custom_publish_box_service');

function remove_service_meta() {
  remove_meta_box('tagsdiv-service_category', 'service', 'side');
}
add_action('admin_menu', 'remove_service_meta');

function service_change_default_featured_image_labels($labels) {
  $labels->featured_image =  __('Service Image');
  $labels->set_featured_image = __('Set service image');
  $labels->remove_featured_image = __('Remove service image');
  $labels->use_featured_image =  __('Use as service image');
  return $labels;
}
add_filter('post_type_labels_service', 'service_change_default_featured_image_labels', 10, 1);

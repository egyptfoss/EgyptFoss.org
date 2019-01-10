<?php

/*
 * Creating a function to create our CPT
 */

function custom_post_type_product() {
  // Set UI labels for Custom Post Type
  $labels = array(
    'name' => _x('Products', 'post type general name', 'your-plugin-textdomain'),
    'singular_name' => _x('product', 'post type singular name', 'your-plugin-textdomain'),
    'add_new' => _x('Add New', 'product', 'your-plugin-textdomain'),
    'add_new_item' => __('Add New product', 'your-plugin-textdomain'),
    'edit_item' => __('Edit product', 'your-plugin-textdomain'),
    'new_item' => __('New product', 'your-plugin-textdomain'),
    'all_items' => __('All products', 'your-plugin-textdomain'),
    'view_item' => __('View product', 'your-plugin-textdomain'),
    'search_items' => __('Search products', 'your-plugin-textdomain'),
    'not_found' => __('No products found', 'your-plugin-textdomain'),
    'not_found_in_trash' => __('No products found in the Trash', 'your-plugin-textdomain'),
    'parent_item_colon' => '',
    'menu_name' => 'Products'
  );
// Set other options for Custom Post Type
  $args = array(
    'label' => __('Products', 'twentythirteen'),
    'description' => __('Products', 'twentythirteen'),
    'menu_icon' => "dashicons-products",
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
    'rewrite' => array('slug' => "products"),
  );
// Registering products Custom Post Type
  register_post_type('product', $args);
}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not
 * unnecessarily executed.
 */
add_action('init', 'custom_post_type_product', 10);

/**
 * products update messages.
 *
 * See /wp-admin/edit-form-advanced.php
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function codex_product_updated_messages($messages) {
  $post = get_post();
  $post_type = get_post_type($post);
  $post_type_object = get_post_type_object($post_type);

  if ($post_type == 'product') {
    $messages['product'] = array(
      0 => '', // Unused. Messages start at index 1.
      1 => __('product updated.', 'your-plugin-textdomain'),
      2 => __('Custom field updated.', 'your-plugin-textdomain'),
      3 => __('Custom field deleted.', 'your-plugin-textdomain'),
      4 => __('product updated.', 'your-plugin-textdomain'),
      /* translators: %s: date and time of the revision */
      5 => isset($_GET['revision']) ? sprintf(__('product restored to revision from %s', 'your-plugin-textdomain'), wp_post_revision_title((int) $_GET['revision'], false)) : false,
      6 => __('product published.', 'your-plugin-textdomain'),
      7 => __('product saved.', 'your-plugin-textdomain'),
      8 => __('product submitted.', 'your-plugin-textdomain'),
      9 => sprintf(
        __('product scheduled for: <strong>%1$s</strong>.', 'your-plugin-textdomain'),
        // translators: Publish box date format, see http://php.net/date
        date_i18n(__('M j, Y @ G:i', 'your-plugin-textdomain'), strtotime($post->post_date))
      ),
      10 => __('product draft updated.', 'your-plugin-textdomain')
    );

    if ($post_type_object->publicly_queryable) {
      $permalink = get_permalink($post->ID);

      $view_link = sprintf(' <a href="%s">%s</a>', esc_url($permalink), __('View product', 'your-plugin-textdomain'));
      $messages[$post_type][1] .= $view_link;
      $messages[$post_type][6] .= $view_link;
      $messages[$post_type][9] .= $view_link;

      $preview_permalink = add_query_arg('preview', 'true', $permalink);
      $preview_link = sprintf(' <a target="_blank" href="%s">%s</a>', esc_url($preview_permalink), __('Preview product', 'your-plugin-textdomain'));
      $messages[$post_type][8] .= $preview_link;
      $messages[$post_type][10] .= $preview_link;
    }
  }
  return $messages;
}
add_filter('post_updated_messages', 'codex_product_updated_messages');

function my_bulk_post_updated_messages_filter_product($bulk_messages, $bulk_counts) {
  $bulk_messages['product'] = array(
    'updated' => _n('%s product updated.', '%s my_cpts updated.', $bulk_counts['updated']),
    'locked' => _n('%s product not updated, somebody is editing it.', '%s products not updated, somebody is editing them.', $bulk_counts['locked']),
    'deleted' => _n('%s product permanently deleted.', '%s products permanently deleted.', $bulk_counts['deleted']),
    'trashed' => _n('%s product moved to the Trash.', '%s products moved to the Trash.', $bulk_counts['trashed']),
    'untrashed' => _n('%s product restored from the Trash.', '%s products restored from the Trash.', $bulk_counts['untrashed']),
  );

  return $bulk_messages;
}
add_filter('bulk_post_updated_messages', 'my_bulk_post_updated_messages_filter_product', 10, 2);

// customizing publish button
function wpse_125800_custom_publish_box_product() {
  $postType = get_current_screen()->post_type;
  if (is_admin() && $postType == "product") {
    $style = '';
    $style .= '<style type="text/css">';
    $style .= "#edit-slug-box, #minor-publishing-actions, #visibility, .num-revisions, .curtime, div.row-actions>span[class='edit'] + span, .row-actions>span.view";
   // $style .= '{display: none; }';  // If this line is commented this will display the "save draft, preview" buttons.
    $style .= '</style>';
    echo $style;
  }
}
add_action('admin_head', 'wpse_125800_custom_publish_box_product');

function remove_product_meta() {
  remove_meta_box('tagsdiv-type', 'product', 'side');
  // remove_meta_box( 'mm_custom_sidebar_meta_box', 'product', 'side' );
  // $ds_hide_custom_sidebar = "<style type=\"text/css\"> #mm_custom_sidebar_meta_box { display: none; }</style>";
  // print($ds_hide_custom_sidebar);
}
add_action('admin_menu', 'remove_product_meta');

/**
 * Adding product from backend task EGYFOSS-138 ..
 * Editor Eslam Diaa Jan17, 2016
 * Rename the meta box title.
 */
function replace_default_featured_image_meta_box() {
  $postType = get_current_screen()->post_type;
  if ($postType) {
    remove_meta_box('postimagediv', 'product', 'side');
    add_meta_box('postimagediv', __('Product Logo'), 'post_thumbnail_meta_box', 'product', 'side', 'high');
  }
}
add_action('admin_head', 'replace_default_featured_image_meta_box', 100);

function add_featured_galleries_to_ctp() {
  $postType = get_current_screen()->post_type;
  if ($postType) {
    add_meta_box('featuredgallerydiv', __('Product Screenshots', 'featured-gallery'), 'fg_display_metabox', 'product', 'side', 'high');
  }
}
add_filter('fg_post_types', 'add_featured_galleries_to_ctp');

/**
 * Renames the 'Set featured image' button in the Media Library dialog window.
 */
function product_change_default_featured_image_labels($labels) {
  $labels->featured_image =  __('Product Image');
  $labels->set_featured_image = __('Set product image');
  $labels->remove_featured_image = __('Remove product image');
  $labels->use_featured_image =  __('Use as product image');
  return $labels;
}
add_filter('post_type_labels_product', 'product_change_default_featured_image_labels', 10, 1);
?>

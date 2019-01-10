<?php

function custom_post_type_partner() {
    // Set UI labels for Custom Post Type
    $labels = array(
      'name' => _x('Partner', 'post type general name', 'egyptfoss'),
      'singular_name' => _x('partner', 'post type singular name', 'egyptfoss'),
      'add_new' => _x('Add Partner', 'Partner', 'egyptfoss'),
      'add_new_item' => __('Add New Partner', 'egyptfoss'),
      'edit_item' => __('Edit Partner', 'egyptfoss'),
      'new_item' => __('New Partner', 'egyptfoss'),
      'all_items' => __('All Partners', 'egyptfoss'),
      'view_item' => __('View Partners', 'egyptfoss'),
      'search_items' => __('Search Partners', 'egyptfoss'),
      'not_found' => __('No partners found', 'egyptfoss'),
      'not_found_in_trash' => __('No partners found in the Trash', 'egyptfoss'),
      'parent_item_colon' => '',
      'menu_name' => 'Partners'
    );

    // Set other options for Custom Post Type
    $args = array(
      'label' => __('Feedback', 'twentythirteen'),
      'description' => __('Feedback', 'twentythirteen'),
      'menu_icon' => "dashicons-megaphone",
      'labels' => $labels,
      // Features this CPT supports in Post Editor
      'supports' => array('title', 'thumbnail'),
      /* A hierarchical CPT is like Pages and can have
       * Parent and child items. A non-hierarchical CPT
       * is like Posts.
       */
      'hierarchical' => false,
      'public' => false,
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
      'rewrite' => array('slug' => (defined('DOING_AJAX') && DOING_AJAX)?"partner":"(ar|en)/partner"),
    );

    // Registering partner Custom Post Type
    register_post_type('partner', $args);
}

/* Hook into the 'init' action so that the function
 * Containing our post type registration is not
 * unnecessarily executed.
 */
add_action('init', 'custom_post_type_partner', 10);


function remove_yoast_metabox_partners(){
    remove_meta_box('wpseo_meta', 'partner', 'normal');
    remove_meta_box('heateor_sss_meta', 'partner', 'normal');
}
add_action( 'add_meta_boxes', 'remove_yoast_metabox_partners',11 );

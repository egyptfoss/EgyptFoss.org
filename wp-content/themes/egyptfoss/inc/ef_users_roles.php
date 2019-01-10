<?php
function set_default_user_role(){
  update_option( 'default_role', 'contributor' );
}
add_action('init', 'set_default_user_role');

function add_theme_caps() {
  $administrator = get_role( 'administrator' );
  $editor = get_role( 'editor' );
  $author = get_role( 'author' );
  $contributor = get_role( 'contributor' );
  $subscriber = get_role( 'subscriber' );
  // add_new_ef_posts
  $administrator->add_cap( 'add_new_ef_posts' );
  $editor->add_cap( 'add_new_ef_posts' );
  $author->add_cap( 'add_new_ef_posts' );
  $contributor->add_cap( 'add_new_ef_posts' );
  // perform_direct_ef_actions
  $administrator->add_cap( 'perform_direct_ef_actions' );
  $editor->add_cap( 'perform_direct_ef_actions' );
  $author->add_cap( 'perform_direct_ef_actions' );
  $contributor->add_cap( 'perform_direct_ef_actions' );
  
  //remove media button on init
  if(!is_admin())
  {
    remove_action( 'media_buttons', 'media_buttons' );
  }
}
add_action( 'init', 'add_theme_caps');

function upgradeContributorToAuthor( $post ) {
  $author_id = $post->post_author;
  $author_info = get_userdata($author_id);
  if(!empty($author_info->roles)) {
    if($author_info->roles[0] == 'contributor') {
      wp_update_user(array(
        'ID' => $author_info->ID,
        'role' => 'author'
      ));
    }
  }
}
add_action('pending_to_publish', 'upgradeContributorToAuthor', 10, 1);
<?php
define("ef_user_documents_per_page", 20);
add_action('bp_setup_nav', 'ef_add_documents_tab', 302);
function ef_add_documents_tab() {
  global $bp;
  bp_core_new_subnav_item( array(
      'name' => _x("Published Documents","definite","egyptfoss"),
      'slug' => 'documents',
      'parent_url' => $bp->displayed_user->domain . $bp->bp_nav['contributions']['slug'] . '/' ,
      'parent_slug' => $bp->bp_nav['contributions']['slug'],
      'position' => 10,
      'screen_function' => 'listing_user_documents'
    )
  );
}

function listing_user_documents(){
  add_action( 'bp_template_content', 'listing_user_documents_content' );
  bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function listing_user_documents_content() {
  bp_get_template_part( 'members/single/documents' );
}

function get_user_published_documents($user_id, $offest) {
  load_orm();
  $item = new CollaborationCenterItem();
  $limit = constant('ef_user_documents_per_page');
  return $item->userPublishedDocuments($user_id, $offest, $limit);
}

function get_user_total_published_documents($user_id) {
  load_orm();
  $item = new CollaborationCenterItem();
  return $item->userPublishedDocuments($user_id);
}

function ef_load_more_user_documents() {
  set_query_var('user_documents_offset', $_POST['offest']);
  get_template_part('template-parts/content', 'user_documents');
  die();
}
add_action('wp_ajax_ef_load_more_user_documents', 'ef_load_more_user_documents');
add_action('wp_ajax_nopriv_ef_load_more_user_documents', 'ef_load_more_user_documents');
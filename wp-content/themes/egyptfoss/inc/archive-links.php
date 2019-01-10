function prefix_add_metabox_menu_posttype_archive(){
  add_meta_box( 'prefix_metabox_menu_posttype_archive', __( 'Archives' ), 'prefix_metabox_menu_posttype_archive', 'nav-menus', 'side', 'default' );
}
add_action( 'admin_head-nav-menus.php', 'prefix_add_metabox_menu_posttype_archive' );

function prefix_metabox_menu_posttype_archive(){
  $post_types = get_post_types( array( 'show_in_nav_menus' => true, 'has_archive' => true ), 'object' );

  if( $post_types ){

    foreach( $post_types as $post_type ){

      $post_type->classes = array( $post_type->name );
      $post_type->type = $post_type->name;
      $post_type->object_id = $post_type->name;
      $post_type->title = $post_type->labels->name;
      $post_type->object = 'cpt_archive';

    }

    $walker = new Walker_Nav_Menu_Checklist( array() );?>
    <div id="cpt-archive" class="posttypediv">
      <div id="tabs-panel-cpt-archive" class="tabs-panel tabs-panel-active">
        <ul id="ctp-archive-checklist" class="categorychecklist form-no-clear"><?php
        echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $post_types ), 0, (object) array( 'walker' => $walker ) );?>
        </ul>
      </div>
    </div>
    <p class="button-controls">
      <span class="add-to-menu">
        <input type="submit"<?php disabled( $nav_menu_selected_id, 0 ); ?> class="button-secondary submit-add-to-menu" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-ctp-archive-menu-item" id="submit-cpt-archive" />
      </span>
    </p><?php

  }

}

function prefix_cpt_archive_menu_filter( $items, $menu, $args ){

  foreach( $items as &$item ){
    if( $item->object != 'cpt_archive' ) continue;
    $item->url = get_post_type_archive_link( $item->type );
    if( get_query_var( 'post_type' ) == $item->type ){
      $item->classes []= 'current-menu-item';
      $item->current = true;
    }
  }
  return $items;

}
add_filter( 'wp_get_nav_menu_items', 'prefix_cpt_archive_menu_filter', 10, 3 );
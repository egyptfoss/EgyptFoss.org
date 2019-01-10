<?php

add_action('bp_setup_nav', 'add_email_notifiacation_subnav_tab', 999);
function add_email_notifiacation_subnav_tab() {
  global $bp;
  bp_core_new_subnav_item(array(
    'name' => __('Notifications', 'egyptfoss'),
    'slug' => 'notifications-settings',
    'parent_url' => trailingslashit(bp_displayed_user_domain() . $bp->settings->slug),
    'parent_slug' => $bp->settings->slug,
				'position' => 10,
    'screen_function' => 'email_tab_show',
    'item_css_id' => $bp->settings->id,
    'user_has_access' => bp_is_my_profile(),
    
  ));
}
function email_tab_show(){
	add_action( 'bp_template_content', 'email_notif_screen' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function email_notif_screen() {
  bp_get_template_part('members/single/settings/email-notificaions');
}

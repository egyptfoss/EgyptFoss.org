<?php
define("ef_user_services_per_page", 20);

function profile_services_item() {
	global $bp;
	bp_core_new_nav_item(
		array(
			'name' => __('Services', 'egyptfoss'),
			'slug' => 'services',
			'position' => 30,
			'default_subnav_slug' => '/',
			'screen_function' => 'listing_services'
		)
	);
}
add_action('bp_setup_nav', 'profile_services_item', 302 );

function listing_services(){
	add_action( 'bp_template_content', 'listing_user_services_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function listing_user_services_content() {
	bp_get_template_part( 'members/single/services' );
}
<?php

function profile_badges_item() {
	global $bp;
	bp_core_new_nav_item(
		array(
			'name' => __('Badges', 'egyptfoss'),
			'slug' => 'badges',
			'position' => 30,
			'default_subnav_slug' => '/',
			'screen_function' => 'listing_badges'
		)
	);
}
add_action('bp_setup_nav', 'profile_badges_item', 302 );

function listing_badges(){
	add_action( 'bp_template_content', 'listing_user_badges_content' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function listing_user_badges_content() {
	bp_get_template_part( 'members/single/badges' );
}

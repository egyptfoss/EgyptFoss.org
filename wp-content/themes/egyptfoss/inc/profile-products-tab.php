<?php
/* Create Item in profile menu */
function profile_products_item() {
	global $bp;
	bp_core_new_nav_item(
	array(
	'name' => __('Contributions', 'egyptfoss'),
	'slug' => 'contributions',
	'position' => 20,
	'default_subnav_slug' => 'products',
	'screen_function' => 'pp_show'
	)
	);
}
add_action('bp_setup_nav', 'profile_products_item' );

/* Create 2 subs for products
 * the first is the one showing by default
 */

function profile_products_submenu() {
	global $bp;
	bp_core_new_subnav_item( array(
	'name' => __('Products', 'egyptfoss'),
	'slug' => 'products',
	'parent_url' => $bp->displayed_user->domain . $bp->bp_nav['contributions']['slug'] . '/' ,
	'parent_slug' => $bp->bp_nav['contributions']['slug'],
	'position' => 10,
	'screen_function' => 'pp_show' //the function is declared below
	)
	);
	
}
add_action('bp_setup_nav', 'profile_products_submenu' );

/* here we control our 1st sub item
 * first function is the screen_function
 * second function displays the content
*/
function pp_show(){
	add_action( 'bp_template_content', 'pp_show_products' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function pp_show_products() {
bp_get_template_part( 'members/single/products/products' );
}

/* here we control our 2nd sub item
 * first function is the screen_function
 * second function displays the content
 */
function show_pp_contrb() {
	add_action( 'bp_template_content', 'show_contrb_product' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
function show_contrb_product() {
bp_get_template_part( 'members/single/products/contributes' );
}

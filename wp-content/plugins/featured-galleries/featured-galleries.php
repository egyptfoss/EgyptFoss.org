<?php
/*
 * Plugin Name:       Featured Galleries
 * Plugin URI:        http://wordpress.org/plugins/featured-galleries/
 * Description:       WordPress ships with a Featured Image functionality. This adds a very similar functionality, but allows for full featured galleries with multiple images.
 * Version:           2.1.0
 * Author:            Andy Mercer
 * Author URI:        https://github.com/Kelderic/
 * Text Domain:       featured-galleries
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
*/


/***********************************************************************/
/*************************  DEFINE CONSTANTS  **************************/
/***********************************************************************/

define( 'FG_PLUGIN_VERSION', '2.1.0' );

define( 'FG_PLUGIN_FILE', __FILE__ );

if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {

	add_action('admin_notices', 'my_plugin_notice');

    function my_plugin_notice(){      

		echo '
			<div class="error below-h2">
				<p>
				 ' . sprintf( 'Featured Galleries requires PHP version 5.4 or greater. You are currently running version: %s. Please deactivate Featured Galleries or upgrade your PHP.', PHP_VERSION ) . '
				</p>
			</div>
		';
    }

} else {

	/***********************************************************************/
	/**********************  INCLUDE REQUIRED FILES  ***********************/
	/***********************************************************************/

	require_once( plugin_dir_path(FG_PLUGIN_FILE) . 'includes/controller.php' );

	require_once( plugin_dir_path(FG_PLUGIN_FILE) . 'includes/public-functions.php' );

	/***********************************************************************/
	/*****************************  INITIATE  ******************************/
	/***********************************************************************/

	new FG_Controller();

}
<?php
/*
Plugin Name: Database PHP Sessions
Version: 1.0
Description: Offload PHP's native sessions to your database for multi-server compatibility.
Author: EGYPTFOSS
Text Domain: egyptfoss-sessions
Domain Path: /languages
*/

class EGYPTFOSS_Sessions {

	private static $instance;

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new EGYPTFOSS_Sessions;
			self::$instance->load();
		}

	}

	/**
	 * Load the plugin
	 */
	private function load() {

		$this->define_constants();
		
		if ( EGYPTFOSS_SESSIONS_ENABLED ) {
			$this->setup_database();
			$this->initialize_session_override();
		}
	}

	/**
	 * Define our constants
	 */
	private function define_constants() {

		define( 'EGYPTFOSS_SESSIONS_VERSION', '1.0' );

		if ( ! defined( 'EGYPTFOSS_SESSIONS_ENABLED' ) ) {
			define( 'EGYPTFOSS_SESSIONS_ENABLED', 1 );
		}

	}


	/**
	 * Override the default sessions implementation with our own
	 *
	 * Largely adopted from Drupal 7's implementation
	 */
	private function initialize_session_override() {
    require_once dirname( __FILE__ ) . '/class-session.php';
    new SessionSaveHandler();
	}

	/**
	 * Set up the database
	 */
	private function setup_database() {
		global $wpdb, $table_prefix;

		$table_name = "{$table_prefix}sessions";
		$wpdb->egyptfoss_sessions = $table_name;
		$wpdb->tables[] = 'egyptfoss_sessions';

		if ( get_option( 'egyptfoss_session_version' ) ) {
			return;
		}

		$create_statement = "CREATE TABLE IF NOT EXISTS `{$table_name}` (
        id varchar(32) NOT NULL,
        access int(10) unsigned,
        data text,
        PRIMARY KEY (id)
		)";
		$wpdb->query( $create_statement );
		update_option( 'egyptfoss_session_version', EGYPTFOSS_SESSIONS_VERSION );

	}


	/**
	 * Force the plugin to be the first loaded
	 *
	 */
	static public function force_first_load()
	{

		$path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
		if ( $plugins = get_option( 'active_plugins' ) ) {
			if ( $key = array_search( $path, $plugins ) ) {
				array_splice( $plugins, $key, 1 );
				array_unshift( $plugins, $path );
				update_option( 'active_plugins', $plugins );
			}
		}

		return;
	}

}

/**
 * Release the kraken!
 */
function EGYPTFOSS_Sessions() {
	return EGYPTFOSS_Sessions::get_instance();
}

add_action( 'activated_plugin', 'EGYPTFOSS_Sessions::force_first_load');

EGYPTFOSS_Sessions();

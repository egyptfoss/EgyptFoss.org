<?php
/*
Plugin Name: EgyptFoss Badges
Version: 1.0
Description: EgyptFoss badges.
Author: EGYPTFOSS
Text Domain: efbadges
Domain Path: /lang
*/

class EGYPTFOSS_Badges {

	private static $instance;

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new EGYPTFOSS_Badges;
			self::$instance->load();
		}

	}

	/**
	 * Load the plugin
	 */
	private function load() {

		$this->define_constants();
		
		if ( EGYPTFOSS_BADGES_ENABLED ) {
			//$this->setup_database();
			$this->load_admin();
		}
	}

	/**
	 * Define our constants
	 */
	private function define_constants() {

		define( 'EGYPTFOSS_BADGES_VERSION', '1.0' );
    define( 'EFB_PLUGIN_PATH', dirname(__FILE__) );
    define( 'EFB_PLUGIN_URL', plugin_dir_url(__FILE__) );
    
		if ( ! defined( 'EGYPTFOSS_BADGES_ENABLED' ) ) {
			define( 'EGYPTFOSS_BADGES_ENABLED', 1 );
		}

	}
  
  /**
   * 
   */
	private function load_admin() {
    //require_once dirname(__FILE__) . '/inc/class-efb-admin.php';
    require_once dirname(__FILE__) . '/inc/class-efb-hooks.php';
    
    $models = glob(dirname(__FILE__) . '/models/*', GLOB_BRACE);
    foreach ($models as $model) {
      $file_name = basename($model);
      require_once dirname(__FILE__) . "/models/{$file_name}";
    }
    
    $adminFiles = glob(dirname(__FILE__) . '/inc/admin/*', GLOB_BRACE);
    foreach ($adminFiles as $adminFile) {
      $file_name = basename($adminFile);
      require_once dirname(__FILE__) . "/inc/admin/{$file_name}";
    }
    
    
    load_textdomain('efbadges', dirname(__FILE__) . '/lang/efbadges-' . get_locale() . '.mo');
  }

	/**
	 * Set up the database
	 */
	private function setup_database() {
		global $wpdb, $table_prefix;

		$table_name = "{$table_prefix}sessions";
		$wpdb->EGYPTFOSS_Badges = $table_name;
		$wpdb->tables[] = 'EGYPTFOSS_Badges';

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
		update_option( 'egyptfoss_session_version', EGYPTFOSS_BADGES_VERSION );

	}

}

/**
 * Release the kraken!
 */
function EGYPTFOSS_Badges() {
	return EGYPTFOSS_Badges::get_instance();
}

EGYPTFOSS_Badges();

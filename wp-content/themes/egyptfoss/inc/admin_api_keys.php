<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class API_Key_List extends WP_List_Table {
  
	/** Class constructor */
	public function __construct() {
		parent::__construct( [
			'singular' => __( 'API key', 'sp' ), //singular name of the listed records
			'plural'   => __( 'API keys', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}


	/**
	 * Retrieve keys data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function get_keys( $per_page = 5, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}api_keys";

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}
    else {
			$sql .= ' ORDER BY created_at DESC';
    }

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}


	/**
	 * Delete a key record.
	 *
	 * @param int $id key id
	 */
	public static function delete_key( $id ) {
		global $wpdb;

		$wpdb->delete(
			"{$wpdb->prefix}api_keys",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}
  
	/**
	 * Generate a new APIkey record.
	 *
	 * @param int $id key id
	 */
	public static function generate_key() {
		global $wpdb;
    
    $is_valid_key = false;
    $om = true;
    do {
      $key = substr( str_shuffle( str_repeat( $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil( 36 / strlen( $x ) ) ) ),1 ,36 );

      $key_record = $wpdb->get_var( $wpdb->prepare( 
        "
          SELECT id
          FROM {$wpdb->prefix}api_keys 
          WHERE api_key = %s
          LIMIT 1
        ", 
        $key
      ) );
          
      if( empty( $key_record ) ) {
        $is_valid_key = true;
      }
                     
    } while ( $is_valid_key === false );
    
		$wpdb->insert(
			"{$wpdb->prefix}api_keys",
			[ 'api_key' => $key, 'is_enabled' => 1, 'created_at' => date( 'Y-m-d H:i:s', time() ) ],
			[ '%s', '%d', '%s' ]
		);
    
    return $key;
	}
  
	/**
   * enable an APIkey.
   * 
   * @global type $wpdb
   * @param type $id
   * @param type $status
   */
	public static function enable_key( $id, $status ) {
		global $wpdb;
    
		$wpdb->update(
			"{$wpdb->prefix}api_keys",
			[ 'is_enabled' => $status ],
			[ 'id' => $id ],
			[ '%d' ],
			[ '%d' ]
		);
	}


	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}api_keys";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no keys data is available */
	public function no_items() {
		_e( 'No keys avaliable.', 'sp' );
	}


	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'api_key':
				return $item[ $column_name ];
			case 'created_at':
        
        // get created at value
        $m_time = $item[ $column_name ];
        
        // complete readable datetime
        $t_time = mysql2date( 'Y/m/d g:i:s a', $m_time );
        
        // Convert to Unix timestamp
        $time = mysql2date( 'G', $m_time );
        
        // get Diff
        $time_diff = time() - $time;
        
        if ( $time_diff >= 0 && $time_diff < DAY_IN_SECONDS ) {
          $h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
        } else {
          $h_time = mysql2date( __( 'Y/m/d' ), $m_time );
        }

        return '<abbr title="' . $t_time . '">' . $h_time . '</abbr>';
      case 'is_enabled':
        $class = 'hidden';
        
        if( $item[ $column_name ] == 1 ) {
          $class = 'visibility';
        }
        
        return '<span class="dashicons dashicons-'.$class.'"></span>';
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}

	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
		);
	}


	/**
	 * Method for api_key column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_api_key( $item ) {

		$delete_nonce = wp_create_nonce( 'ef_delete_api_key' );
		$enabled_nonce = wp_create_nonce( 'ef_enabled_api_key' );

		$title = '<strong>' . $item['api_key'] . '</strong>';

		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&key=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce ),
		];
    
    if( $item['is_enabled'] == 1 ) {
      $actions['disable'] = sprintf( '<a href="?page=%s&action=%s&key=%s&_wpnonce=%s">Disable</a>', esc_attr( $_REQUEST['page'] ), 'disable', absint( $item['id'] ), $enabled_nonce );
    }
    else {
      $actions['enable'] = sprintf( '<a href="?page=%s&action=%s&key=%s&_wpnonce=%s">Enable</a>', esc_attr( $_REQUEST['page'] ), 'enable', absint( $item['id'] ), $enabled_nonce );
    }
    
		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'          => '<input type="checkbox" />',
			'api_key'     => 'API key',
			'created_at'  => 'Created at',
			'is_enabled'  => 'Status',
		];

		return $columns;
	}


	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'created_at' => array( 'created_at', false )
		);

		return $sortable_columns;
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'Delete'
		];

		return $actions;
	}


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		/** Process bulk action */
		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'api_keys_per_page', 5 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );

		$this->items = self::get_keys( $per_page, $current_page );
	}

	public function process_bulk_action() {

		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'ef_delete_api_key' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				self::delete_key( absint( $_GET['key'] ) );
        
        wp_redirect( admin_url( '/admin.php?page=ef_list_api_keys&r=deleted' ) );
        die();
			}
		}

		//Detect when a bulk action is being triggered...
		if ( isset( $_GET['action'] ) && 'generate' === $_GET['action'] ) {

			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'ef_generate_api_key' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				$key = self::generate_key();
        
        wp_redirect( admin_url( '/admin.php?page=ef_list_api_keys&r=generated&key=' . $key ) );
        die();
			}
		}
    
		//Detect when a bulk action is being triggered...
		if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'enable', 'disable' ) ) ) {
      
      $enable = ( $_GET['action'] === 'enable' );
      
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'ef_enabled_api_key' ) ) {
				die( 'Go get a life script kiddies' );
			}
			else {
				$key = self::enable_key( absint( $_GET['key'] ), $enable );
        
        wp_redirect( admin_url( '/admin.php?page=ef_list_api_keys&r='. $_GET['action'] .'d' ) );
        die();
			}
		}

		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
      
			$delete_ids = esc_sql( $_POST['bulk-delete'] );

			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_key( $id );
			}
      
      wp_redirect( admin_url( '/admin.php?page=ef_list_api_keys&r=deleted' ) );
      die();
		}
	}
  
  public function prepare_message() {
    if( empty( $_GET['r'] )  
        || !in_array( $_GET['r'], array( 'deleted', 'enabled', 'disabled', 'generated' ) ) 
      ) {
      return;
    }
    ?>
    <div id="message" class="updated notice is-dismissible">
        <p>
            <?php
              $message = '';
              switch( $_GET['r'] ):
                case 'deleted':
                  $message = 'API key has been deleted successfully'; break;
                case 'enabled':
                  $message = 'API key has been enabled'; break;
                case 'disabled':
                  $message = 'API key has been disabled'; break;
                case 'generated':
                  $message = 'New API key has been generated ( '. $_GET['key'] .' )'; break;
              endswitch;

              echo $message;
            ?>
        </p>
        <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
    </div>
    <?php
  }

}

class Api_key_Page {

	// class instance
	static $instance;

	// key API_Key_List object
	public $keys_obj;

	// class constructor
	public function __construct() {
    //allow redirection, even if my theme starts to send output to the browser
    add_action('init', [ $this, 'do_output_buffer' ] );
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}

  public function do_output_buffer() {
      ob_start();
  }
  
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {

		$hook = add_menu_page(
			'API keys',
			'API keys',
			'manage_options',
			'ef_list_api_keys',
			[ $this, 'listing_page' ],
      'dashicons-admin-network',
      26
		);

		add_action( "load-$hook", [ $this, 'screen_option' ] );
	}


	/**
	 * Listing page
	 */
	public function listing_page() {
		?>
		<div class="wrap">
			<h2>
          API keys
          <?php printf( '<a href="?page=%s&action=%s&_wpnonce=%s" class="page-title-action">Generate new key</a>', esc_attr( $_REQUEST['page'] ), 'generate', wp_create_nonce( 'ef_generate_api_key' ) ); ?>
      </h2>
        
      <?php $this->keys_obj->prepare_message(); ?>
      
			<div id="apikeys_container">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							<form method="post">
								<?php
                  $this->keys_obj->prepare_items();
                  $this->keys_obj->display(); 
                ?>
							</form>
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
    <script>
      jQuery( document ).ready( function( $ ) {
        $( '#apikeys_container .row-actions .delete a' ).click( function() { 
            var r = confirm("Are you sure you want to take this action?");
            if (r == false) {
                return false;
            }
        });
      } );
    </script>
	<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Api keys',
			'default' => 5,
			'option'  => 'api_keys_per_page'
		];

		add_screen_option( $option, $args );

		$this->keys_obj = new API_Key_List();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

Api_key_Page::get_instance();
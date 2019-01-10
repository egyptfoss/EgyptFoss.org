<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class EFB_Admin_Actions extends WP_List_Table {
  
	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __('Points Upon Action', 'egyptfoss' ), //singular name of the listed records
			'plural'   => __( 'Points Upon Actions', 'egyptfoss' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );

	}
 
	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 * @param string $which
	 */
	protected function display_tablenav( $which ) {
		if ( 'top' === $which ) {
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		}
		?>
    <form action="" method="GET">
        <?php 
        $this->search_box( __( 'Search' ), 'example' ); 
        foreach ($_GET as $key => $value) {
            if( 's' !== $key ) // don't include the search query
                echo("<input type='hidden' name='$key' value='$value' />");
        }
        ?>
    </form>
	<div class="tablenav <?php echo esc_attr( $which ); ?>">

		<?php if ( $this->has_items() ): ?>
		<div class="alignleft actions bulkactions">
			<?php $this->bulk_actions( $which ); ?>
		</div>
		<?php endif;
		$this->extra_tablenav( $which );
		$this->pagination( $which );
?>

		<br class="clear" />
	</div>
<?php
  }

	/**
	 * Retrieve badges data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	public static function efb_get_actions( $per_page = 5, $page_number = 1 ) {

		global $wpdb;
    
    $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
    $do_search = ( $search ) ? $wpdb->prepare(" and name LIKE '%%%s%%' ", $search ) : '';
    
		$sql = "SELECT * FROM {$wpdb->prefix}efb_actions where parent_id is null and is_point_granted = 1";
    $sql .= $do_search;
    
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}
    else {
			$sql .= ' ORDER BY name DESC';
    }

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}efb_actions where parent_id is null and is_point_granted = 1";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no keys data is available */
	public function no_items() {
		_e( 'No actions avaliable.', 'sp' );
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
      case 'points_weight':

        return $item[ $column_name ];
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
  }

	/**
	 * Method for image column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	/*function column_img( $item ) {
    $img_src = $item['img'];
    
    if( $img_src ) {
      $img_src = '<img src="'.$img_src.'" title="'.$item['name'].'" width="50" height="50"/>';
    }
    
		return $img_src;
	}*/
  
	/**
	 * Method for title column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {
    
    $title = sprintf( '<a href="?page=%s&action=%s&action_id=%s"><strong>%s</strong></a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $item["post_type"] . " " . "points" );
    $actions = array( 'edit' => sprintf( '<a href="?page=%s&action=%s&action_id=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) ) );
    
		return $title . $this->row_actions( $actions );
	}


	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'            => '<input type="checkbox" />',
			'name'         => 'Title',
			'points_weight' => 'Points',
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
			'name'     => array( 'name', false ),
			'points_weight'  => array( 'points', false ),
		);

		return $sortable_columns;
	}
  
  /**
   * 
   * @global type $wpdb
   * @param type $badge_id
   * @return type
   */
  public static function get_action( $id ) {
      global $wpdb;
    
      $efb_admin_action = $wpdb->get_row( $wpdb->prepare(" SELECT * FROM {$wpdb->prefix}efb_actions WHERE `id` = %d and parent_id is null and is_point_granted = 1", $id ) );
      
      return $efb_admin_action;
  }
	
  /**
   * 
   * @global type $badge
   * @param type $badge_id
   * @return boolean
   */
	public function is_action_valid( $id ) {
      global $efb_admin_action;
      
      $efb_admin_action = self::get_action( $id );
      
      if( $efb_admin_action ) {
        return TRUE;
      }

      return FALSE;
	}
  
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function is_in_edit_mode() {
		if( 
        isset( $_REQUEST['action'] ) && 
        $_REQUEST['action'] === 'edit' &&
        isset( $_REQUEST['action_id'] ) &&
        $this->is_action_valid( $_REQUEST['action_id'] ) 
      ) {
      return TRUE;
    }
    
		return FALSE;
	}
  
  /**
   * 
   * @global type $errors
   * @global type $badge
   * @return type
   */
  public function process_update_action() {
    // if user submitted form      
    if( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
      return;
    }
    
    // verify direct access
    if( 
        !isset( $_REQUEST['_wpnonce'] ) ||
        !wp_verify_nonce( $_REQUEST['_wpnonce'],  'efb_update_action' ) 
      ) {
      die( 'Go get a life script kiddies' );
    }
    
    global $errors, $efb_admin_action, $show_message;
    
    $errors = $form_fields = array();
    
    // prepare form fields
    $fields = array(
      'efb-points_weight' => array( 'required' => TRUE, 'mapped_column' => 'points_weight' ),
     
    );
    
    // validate form fields
    foreach( $fields as $field => &$opts ) {

      if( $opts['required'] && ( empty( $_POST[ $field ] ) || trim( $_POST[ $field ] ) === '' ) ) {
        $errors[ $field ] = 'This field can\'t be empty.';
      }
      
      $form_fields[ $opts['mapped_column'] ] = $_POST[ $field ];
    }
    
    // validation test passed
    if( empty( $errors ) ) {
      $this->update_efb_action( $_REQUEST['action_id'], $form_fields );
    }
    
    // fill form with not updated attributes
    $form_fields['id'] = $_REQUEST['action_id'];
    $form_fields['name'] = $efb_admin_action->name;
    
    $efb_admin_action = (object) $form_fields;
    
    // show success/error message 
    $show_message = TRUE;
  }
  
  /**
   * 
   * 
   * @global type $wpdb
   * @param type $id
   * @param type $fields
   */
  public function update_efb_action( $id, $form_fields ) {
    global $wpdb;

      
     $wpdb->update(
			"{$wpdb->prefix}efb_actions",
			$form_fields,
			[ 'id' => $id ],
			[ '%s' ],
			[ '%d' ]
		);
      
  }


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();
    
		$per_page     = $this->get_items_per_page( 'efb_actions_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );
    
		$this->items = self::efb_get_actions( $per_page, $current_page );
	}
  
  /**
   * 
   * @global type $errors
   * @param type $field
   */
  public function the_field_error_message( $field ) {
    global $errors;
    
    if( isset( $errors[ $field ] ) ) {
      echo '<span class="description" style="color: red;">This field can\'t be empty.</span>';
    }
  }

  /**
   * 
   * @global type $badge
   */
  public function view_form() {
    global $efb_admin_action, $errors;
    
    ?>
    <div class="wrap" id="profile-page">
        <h1>Edit Action Points</h1>
        <form id="efb-admin-edit-badge" method="POST">
            <table class="form-table">
                <tbody>
                    <tr class="user-user-login-wrap">
                        <th>
                            <label for="efb_name">title</label>
                        </th>
                        <td>
                            <input type="text" name="efb_name" id="efb_name" value="<?php echo $efb_admin_action->post_type . " points"; ?>" disabled="disabled" class="regular-text"> 
                            <span class="description">title cannot be changed.</span>
                        </td>
                    </tr>
                    <tr>
                      <th>
                          <label for="efb-points_weight">points</label>
                      </th>
                      <td>
                          <input type="text" id="efb-points_weight" name="efb-points_weight" value="<?php echo $efb_admin_action->points_weight; ?>" class="regular-text ltr">
                          <?php $this->the_field_error_message( 'efb-points_weight' ); ?>
                      </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'efb_update_action' ); ?>">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="action_id" id="action_id" value="<?php echo $efb_admin_action->id; ?>">
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Update points">
            </p>
        </form>
    </div>
    <?php
  }
  
  /**
   * 
   * @return type
   */
  public function prepare_message() {
    global $errors, $show_message;
    
    if( !$show_message ) return;
    ?>
    <div id="message" class="updated <?php echo empty( $errors ) ? 'notice' : 'error'; ?> is-dismissible">
        <p>
            <?php
              if( empty( $errors ) ):
                echo 'Action points has been updated successfully.';
              else:
                echo '<strong>ERROR</strong> : Please check form errors.';
              endif;
            ?>
        </p>
        <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">Dismiss this notice.</span>
        </button>
    </div>
    <?php
  }

}

class EFB_Action_Page {

	// class instance
	static $instance;

	// key API_Key_List object
	public $efb_actions_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_gg_menu' ], 20 );
	}
  
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_gg_menu() {
    $hook = add_submenu_page('efb_badges','Points Upon Actions','Points','manage_options','efb_actions',[ $this, 'listing_page' ]);   
		add_action( "load-$hook", [ $this, 'screen_option' ] );
	}


	/**
	 * Listing page
	 */
	public function listing_page() {
		?>
		<div class="wrap">
			<h2>
          Points Upon Actions
      </h2>
        <?php
          $in_edit_mode = FALSE;
          if( $this->efb_actions_obj->is_in_edit_mode() ) {
            $in_edit_mode = TRUE;
            $this->efb_actions_obj->process_update_action();
            $this->efb_actions_obj->prepare_message(); 
          }
      ?>
      
			<div id="apikeys_container">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
            <?php if( $in_edit_mode ): ?>
                  <?php $this->efb_actions_obj->view_form(); ?>
            <?php else: ?>
                <div class="meta-box-sortables ui-sortable">
                  <form method="post">
                    <?php
                      $this->efb_actions_obj->prepare_items();
                      $this->efb_actions_obj->display(); 
                    ?>
                  </form>
                </div>
            <?php endif; ?>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}

	/**
	 * Screen options
	 */
	public function screen_option() {

		$option = 'per_page';
		$args   = [
			'label'   => 'Points Upon Actions',
			'default' => 5,
			'option'  => 'efb_actions_per_page'
		];

		add_screen_option( $option, $args );

		$this->efb_actions_obj = new EFB_Admin_Actions();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

EFB_Action_Page::get_instance();
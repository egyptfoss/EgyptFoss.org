<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class EFB_Admin_Badges extends WP_List_Table {
  
	/** Class constructor */
	public function __construct() {

		parent::__construct( [
			'singular' => __('Badge', 'egyptfoss' ), //singular name of the listed records
			'plural'   => __( 'Badges', 'egyptfoss' ), //plural name of the listed records
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
	public static function get_badges( $per_page = 5, $page_number = 1 ) {

		global $wpdb;
    
    $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
    $do_search = ( $search ) ? $wpdb->prepare(" WHERE title LIKE '%%%s%%' OR title_ar LIKE '%%%s%%' ", $search, $search ) : '';
    
		$sql = "SELECT *, COUNT( user_badges.badge_id ) AS users_count FROM {$wpdb->prefix}efb_badges";
		$sql .= " LEFT JOIN {$wpdb->prefix}efb_badges_users user_badges ON {$wpdb->prefix}efb_badges.id = user_badges.badge_id";
    $sql .= $do_search;
    $sql .= " GROUP BY {$wpdb->prefix}efb_badges.id";
    
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

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}efb_badges";

		return $wpdb->get_var( $sql );
	}


	/** Text displayed when no keys data is available */
	public function no_items() {
		_e( 'No badges avaliable.', 'sp' );
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
			case 'title':
      case 'title_ar':
      case 'min_threshold':
        return $item[ $column_name ];
      case 'users_count':
      {
        
        global $wpdb;
        
        $do_search = $wpdb->prepare(" WHERE badge_id = %d  ", $item['id'] );
    
        $sql = "SELECT users.* FROM {$wpdb->users} users";
        $sql .= " LEFT JOIN {$wpdb->prefix}efb_badges_users user_badges ON users.ID LIKE user_badges.user_id ";
        $sql .= " LEFT JOIN {$wpdb->prefix}efb_badges badges ON user_badges.badge_id = badges.id";
        $sql .= $do_search;
        $sql .= " ORDER BY user_badges.created_date";
        
        $users = $wpdb->get_results( $sql, 'ARRAY_A' );
        
          $return .= '<a href="#TB_inline?height=300&amp;width=400&amp;inlineId=thickbox-badge-'. $item['id'] .'" class="thickbox" title="'. $item['title'] .' Badge">'. $item[$column_name] .'</a>';
          $return .= '<div id="thickbox-badge-'. $item['id'] .'" style="display: none;">';

        if( count( $users ) ) {
          $return .= '<table class="wp-list-table striped widefat" style="margin-top: 15px;">
          <thead>
            <tr>
                <th><span>Profile Picture</span></th>	
                <th><span>Display Name</span></th>	
            </tr>
          </thead>
          <tbody>';
              foreach( $users as $user ) {
                $return .= '<tr>
                    <td>'. get_avatar($user['ID'], 30) .'</td>
                    <td>'. bp_core_get_userlink($user['ID']) .'</td>
                </tr>';
              }
          $return .= '</tbody>';

          $return .= '</table>';
        }
        else {
          $return .= '<p style="text-align: center;">No Users</p>';
        }
        
        $return .= '</div>';

        return $return;
      }
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
	function column_img( $item ) {
    $img_src = $item['img'];
    
    if( $img_src ) {
      $img_src = '<img src="'.$img_src.'" title="'.$item['title'].'" width="50" height="50"/>';
    }
    
		return $img_src;
	}
  
	/**
	 * Method for title column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_title( $item ) {
    
    $title = sprintf( '<a href="?page=%s&action=%s&badge=%s"><strong>%s</strong></a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $item['title'] );
    $actions = array( 'edit' => sprintf( '<a href="?page=%s&action=%s&badge=%s">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ) ) );
    
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
			'title'         => 'Title',
			'title_ar'      => 'Arabic Title',
			'min_threshold' => 'Threshold',
			'img'           => 'Image',
			'users_count'   => 'User awarded',
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
			'title'     => array( 'title', false ),
			'title_ar'  => array( 'title_ar', false ),
			'min_threshold'  => array( 'min_threshold', false ),
			'users_count'  => array( 'users_count', false )
		);

		return $sortable_columns;
	}
  
  /**
   * 
   * @global type $wpdb
   * @param type $badge_id
   * @return type
   */
  public static function get_badge( $badge_id ) {
      global $wpdb;
    
      $badge = $wpdb->get_row( $wpdb->prepare(" SELECT * FROM {$wpdb->prefix}efb_badges WHERE `id` = %d", $badge_id ) );
      
      return $badge;
  }
	
  /**
   * 
   * @global type $badge
   * @param type $badge_id
   * @return boolean
   */
	public function is_badge_valid( $badge_id ) {
      global $badge;
      
      $badge = self::get_badge( $badge_id );
      
      if( $badge ) {
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
        isset( $_REQUEST['badge'] ) &&
        $this->is_badge_valid( $_REQUEST['badge'] ) 
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
        !wp_verify_nonce( $_REQUEST['_wpnonce'],  'efb_update_badge' ) 
      ) {
      die( 'Go get a life script kiddies' );
    }
    
    global $errors, $badge, $show_message;
    
    $errors = $form_fields = array();
    
    // prepare form fields
    $fields = array(
      'efb-title' => array( 'required' => TRUE, 'mapped_column' => 'title' ),
      'efb-title-ar' => array( 'required' => TRUE, 'mapped_column' => 'title_ar' ),
      'efb-desc' => array( 'required' => TRUE, 'mapped_column' => 'description' ),
      'efb-desc-ar' => array( 'required' => TRUE, 'mapped_column' => 'description_ar' ),
      'efb-min-threshold'  => array( 'required' => TRUE, 'mapped_column' => 'min_threshold' ),
      'efb-img'  => array( 'required' => TRUE, 'mapped_column' => 'img' ),
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
      $this->update_badge( $_REQUEST['badge'], $form_fields );
    }
    
    // fill form with not updated attributes
    $form_fields['id'] = $_REQUEST['badge'];
    $form_fields['name'] = $badge->name;
    
    $badge = (object) $form_fields;
    
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
  public function update_badge( $id, $form_fields ) {
    global $wpdb;
    
    $old_badge = $this->get_badge( $id );
      
    $updated = $wpdb->update(
			"{$wpdb->prefix}efb_badges",
			$form_fields,
			[ 'id' => $id ],
			[ '%s', '%s', '%s', '%s', '%d', '%s' ],
			[ '%d' ]
		);
      
    if( $updated && $form_fields['min_threshold'] != $old_badge->min_threshold ) {
      $wpdb->insert(
        "{$wpdb->prefix}efb_badges_history",
        [ 
          'badge_id' => (int) $id,
          'threshold' => (int) $form_fields['min_threshold'],
          'created_at' => date( 'Y-m-d H:i:s', time() ) 
        ],
        [ '%d', '%d', '%s' ]
      );
    }
  }


	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();
    
		$per_page     = $this->get_items_per_page( 'efb_badges_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );
    
		$this->items = self::get_badges( $per_page, $current_page );
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
    global $badge, $errors;
    
    ?>
    <div class="wrap" id="profile-page">
        <h1>Edit Badge</h1>
        <form id="efb-admin-edit-badge" method="POST">
            <table class="form-table">
                <tbody>
                    <tr class="user-user-login-wrap">
                        <th>
                            <label for="efb_name">Name</label>
                        </th>
                        <td>
                            <input type="text" name="efb_name" id="efb_name" value="<?php echo $badge->name; ?>" disabled="disabled" class="regular-text"> 
                            <span class="description">Name cannot be changed.</span>
                        </td>
                    </tr>
                    <tr>
                      <th>
                          <label for="efb-title">Title </label>
                      </th>
                      <td>
                          <input type="text" id="efb-title" name="efb-title" value="<?php echo $badge->title; ?>" class="regular-text ltr">
                          <?php $this->the_field_error_message( 'efb-title' ); ?>
                      </td>
                    </tr>
                    <tr>
                      <th>
                          <label for="efb-title-ar">Arabic Title </label>
                      </th>
                      <td>
                          <input type="text" id="efb-title-ar" name="efb-title-ar" value="<?php echo $badge->title_ar; ?>" class="regular-text ltr">
                          <?php $this->the_field_error_message( 'efb-title-ar' ); ?>
                      </td>
                    </tr>
                    <tr>
                      <th>
                          <label for="efb-min-threshold">Minimum threshold </label>
                      </th>
                      <td>
                          <input type="text" id="efb-min-threshold" name="efb-min-threshold" value="<?php echo $badge->min_threshold; ?>" class="regular-text ltr">
                          <?php $this->the_field_error_message( 'efb-min-threshold' ); ?>
                      </td>
                    </tr>
                    <tr>
                      <th>
                          <label for="efb-img">Image </label>
                      </th>
                      <td>
                          <a href="#" class="button button-default efb-badge-logo-upload">Browse</a>
                          <img class="efb-badge-logo" src="<?php echo $badge->img; ?>" height="100" width="100" <?php echo empty( $badge->img )?'style="display: none"':''; ?>/>
                          <input type="hidden" id="efb-img" name="efb-img" size="60" value="<?php echo $badge->img; ?>" class="header_logo_url">
                          <?php $this->the_field_error_message( 'efb-img' ); ?>
                      </td>
                    </tr>
                    <tr>
                      <th>
                          <label for="efb-desc">Description </label>
                      </th>
                      <td>
                          <textarea id="efb-desc" name="efb-desc"><?php echo $badge->description; ?></textarea>
                          <?php $this->the_field_error_message( 'efb-desc' ); ?>
                      </td>
                    </tr>
                    <tr>
                      <th>
                          <label for="efb-desc-ar">Arabic Description </label>
                      </th>
                      <td>
                          <textarea id="efb-desc-ar" name="efb-desc-ar"><?php echo $badge->description_ar; ?></textarea>
                          <?php $this->the_field_error_message( 'efb-desc-ar' ); ?>
                      </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo wp_create_nonce( 'efb_update_badge' ); ?>">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="badge" id="badge" value="<?php echo $badge->id; ?>">
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Update Badge">
            </p>
        </form>
        <script>
            jQuery(document).ready(function($) {
                $('.efb-badge-logo-upload').click(function(e) {
                    e.preventDefault();

                    var custom_uploader = wp.media({
                        title: 'Custom Image',
                        button: {
                            text: 'Upload Image'
                        },
                        multiple: false  // Set this to true to allow multiple files to be selected
                    })
                    .on('select', function() {
                        var attachment = custom_uploader.state().get('selection').first().toJSON();
                        $('.efb-badge-logo').attr('src', attachment.url);
                        $('.efb-badge-logo').show();
                        $('#efb-img').val(attachment.url);
                    })
                    .open();
                });
            });
        </script>
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
                echo 'Badge has been updated successfully.';
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

class EFB_Badges_Page {

	// class instance
	static $instance;

	// key API_Key_List object
	public $badges_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ], 10 );
	}
  
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}

	public function plugin_menu() {

		$hook = add_menu_page(
			'Badges',
			'Badges',
			'manage_options',
			'efb_badges',
			[ $this, 'listing_page' ],
      'dashicons-shield',
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
          Badges
      </h2>
        <?php
          $in_edit_mode = FALSE;
          if( $this->badges_obj->is_in_edit_mode() ) {
            $in_edit_mode = TRUE;
            
            $this->badges_obj->process_update_action();
            $this->badges_obj->prepare_message(); 
          }
      ?>
      
			<div id="apikeys_container">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
            <?php if( $in_edit_mode ): ?>
                  <?php $this->badges_obj->view_form(); ?>
            <?php else: ?>
                <div class="meta-box-sortables ui-sortable">
                  <form method="post">
                    <?php
                      $this->badges_obj->prepare_items();
                      $this->badges_obj->display(); 
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
			'label'   => 'Badges',
			'default' => 5,
			'option'  => 'efb_badges_per_page'
		];

		add_screen_option( $option, $args );

		$this->badges_obj = new EFB_Admin_Badges();
	}


	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

EFB_Badges_Page::get_instance();
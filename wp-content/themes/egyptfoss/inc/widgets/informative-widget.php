<?php
/**
 * Register and print informative widget in admin dashboard
 */

/**
 * Register widget
 * 
 * @global array $wp_meta_boxes
 */
function register_informative_widget() {
 	global $wp_meta_boxes;

	wp_add_dashboard_widget(
		'informative_dashboard_widget',
		'Pending data',
		'print_informative_widget'
	);

 	$dashboard = $wp_meta_boxes['dashboard']['normal']['core'];

	$my_widget = array( 'informative_dashboard_widget' => $dashboard['informative_dashboard_widget'] );
 	unset( $dashboard['informative_dashboard_widget'] );

 	$sorted_dashboard = array_merge( $my_widget, $dashboard );
 	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
}
add_action( 'wp_dashboard_setup', 'register_informative_widget' );

/**
 * Display widget
 * 
 * @global type $wpdb
 */
function print_informative_widget() {
  global $wpdb;
  
  $info_sections = array(
    array( 'label' => 'News', 'post_type' => 'news', 'dashicon' => 'megaphone' ),
    array( 'label' => 'Events', 'post_type' => 'tribe_events', 'dashicon' => 'calendar' ),
    array( 'label' => 'Products', 'post_type' => 'product', 'dashicon' => 'products' ),
    array( 'label' => 'Data', 'post_type' => 'open_dataset', 'dashicon' => 'microphone' ),
    array( 'label' => 'Services Market', 'post_type' => 'service', 'dashicon' => 'store' ),
    array( 'label' => 'Request Center', 'post_type' => 'request_center', 'dashicon' => 'image-filter' ),
    array( 'label' => 'Success Stories', 'post_type' => 'success_story', 'dashicon' => 'megaphone' ),
    array( 'label' => 'Expert Thoughts', 'post_type' => 'expert_thought', 'dashicon' => 'lightbulb' ),
  );
  
  $sums = array();
  
  foreach( $info_sections as $section ) {
    $sums[] = "SUM(CASE WHEN posts.post_type = '{$section['post_type']}' THEN 1 ELSE 0 END) {$section['post_type']}";
  }
  
  $info_sections[] = array( 'label' => 'Feedbacks', 'post_type' => 'feedback', 'dashicon' => 'megaphone', 'url' => 'post_type=feedback&seen=unseen' );
  $info_sections[] = array( 'label' => 'Resources', 'post_type' => 'resources', 'dashicon' => 'format-aside', 'url' => 'post_type=open_dataset&pending_resources=pending' );
  
  $sql = "SELECT "
        . implode(', ', $sums)
        . " FROM $wpdb->posts posts"
        . " WHERE posts.post_status = 'pending'";
  
  $data = $wpdb->get_row( $sql );
  
  $current_user = get_current_user_id();
  
  $sql = "SELECT "
        . "COUNT(posts.ID)"
        . " FROM $wpdb->posts posts"
        . " JOIN $wpdb->postmeta post_meta ON post_meta.post_id = posts.ID"
        . " WHERE posts.post_type = 'open_dataset' AND ( post_meta.meta_key LIKE '%_resource_status%' AND post_meta.meta_value = 'pending' )";
  
  $resources_count = $wpdb->get_var( $sql );
  
  $data->resources = $resources_count;
  
  $sql = "SELECT "
        . "COUNT(posts.ID)"
        . " FROM $wpdb->posts posts"
        . " LEFT JOIN $wpdb->postmeta post_meta ON post_meta.post_id = posts.ID AND post_meta.meta_key = 'ef_feedback_{$current_user}_seen'"
        . " WHERE posts.post_type = 'feedback' AND (  post_meta.meta_value = 0 OR post_meta.post_id IS NULL )";
  
  $feedback_count = $wpdb->get_var( $sql );
  
  $data->feedback = $feedback_count;
  
	?>
  <style>
      #informative_dashboard_widget ul  {
          display: inline-block;
          margin-bottom: 0px;
      }
      #informative_dashboard_widget li {
          width: 50%;
          float: left;
          margin-bottom: 10px;
      }
  </style>
  <div class="inside">
    <div class="main">
      <ul>
        <?php 
          foreach( $info_sections as $section ) {
            $url = isset( $section['url'] )? $section['url'] : "post_status=pending&post_type=".$section['post_type'];
            the_info_item( 
              $section['label'], 
              $section['dashicon'], 
              $data->{$section['post_type']},
              "edit.php?" . $url
            ); 
          }
        ?>
      </ul>
    </div>
  </div>
	<?php
}

/**
 * Print informative item
 * 
 * @param type $label
 * @param type $dashicon
 * @param type $count
 * @param type $url
 */
function the_info_item( $label, $dashicon, $count, $url ) {
  ?>
  <li>
      <a href="<?php echo $url; ?>">
        <span class="dashicons dashicons-<?php echo $dashicon; ?>"></span> 
        <b><?php echo $label; ?></b> : 
        <?php if( $count > 0 ): ?>
          <?php echo $count; ?>
        <?php else: ?>
          <span class="dashicons dashicons-yes"></span>
        <?php endif; ?>
      </a>
  </li>
  <?php
}

add_action( 'current_screen', 'ef_update_feedback_user_seen' );

function ef_update_feedback_user_seen() {

    $current_screen = get_current_screen();
    
    if( $current_screen ->id === "feedback" && $_GET['action'] === 'edit' && !empty( $_GET['post'] ) ) {
        $current_user = get_current_user_id();
        
        update_post_meta( $_GET['post'] , "ef_feedback_{$current_user}_seen", 1 );
    }
}


//add new column to admin open dataset list
function ef_add_feedback_seen_column( $columns ) {
  $columns['seen'] = 'Seen';
  $customOrder = array('cb', 'title', 'section', 'seen', 'date', 'author');

  # return a new column array to wordpress.
  # order is the exactly like you set in $customOrder.
  foreach ($customOrder as $colname) {
    $new[$colname] = $columns[$colname]; 
  }
  
  return $new;
}
add_filter('manage_feedback_posts_columns', 'ef_add_feedback_seen_column', 999 );

function ef_manage_feedback_seen_column( $column, $post_id ) {
  $post = get_post($post_id);
  if($post->post_type == 'feedback') {
    switch ( $column ) {
      case 'seen':
        $current_user = get_current_user_id();
        $seen = get_post_meta( $post_id, "ef_feedback_{$current_user}_seen", true );
        $class = 'hidden';
        
        if( $seen == 1 ) {
          $class = 'visibility';
        }
        
        echo '<span class="dashicons dashicons-'.$class.'"></span>';
        break;
    }
  }
}
add_action('manage_posts_custom_column' , 'ef_manage_feedback_seen_column', 10, 2 );

function ef_add_feedback_seen_filter() {
  if (is_admin() && $_GET['post_type'] == 'feedback') {
      $seen = $_GET['seen'];
      if( empty( $seen ) )
      {
        $seen = '';
      }
      echo "<select name='seen' class='select2_admin_region'>";
      echo "<option value='' ".($seen == ''?'selected':'').">All Feedbacks</option>";
      echo '<option value="seen"'.($seen == 'seen'?'selected':'').'>Seen</option>'
      . '<option value="unseen"'.($seen == 'unseen'?'selected':'').'>Unseen</option>';
      echo '</select>';
  }
}

add_action('restrict_manage_posts', 'ef_add_feedback_seen_filter');
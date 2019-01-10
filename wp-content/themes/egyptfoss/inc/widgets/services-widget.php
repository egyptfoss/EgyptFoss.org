<?php
/**
 * Register and print services widget in admin dashboard
 */

/**
 * Register widget
 *
 * @global array $wp_meta_boxes
 */
function register_services_widget() {
 	global $wp_meta_boxes;

	wp_add_dashboard_widget(
		'servies_dashboard_widget',
		'Services',
		'print_services_widget'
	);
}
add_action( 'wp_dashboard_setup', 'register_services_widget' );

/**
 * Display widget
 *
 * @global type $wpdb
 */
function print_services_widget() {
  global $wpdb;
  $sql = "SELECT "
        . "COUNT(posts.ID)"
        . " FROM $wpdb->posts posts"
        . " WHERE posts.post_type = 'service' AND posts.post_status = 'publish'";

  $published_service_count = $wpdb->get_var( $sql );

  $sql = "SELECT "
        . "COUNT( DISTINCT posts.ID)"
        . " FROM $wpdb->posts posts"
        . " INNER JOIN {$wpdb->prefix}request_threads AS resp ON resp.request_id = posts.ID AND resp.responses_count > 0"
        . " WHERE posts.post_type = 'service' AND posts.post_status = 'publish'";

  $requested_service_count = $wpdb->get_var( $sql );
	?>
  <style>
      #service-widget-container {
        height: 50px;
        padding-top: 12px;
      }

      #service-widget-container li:nth-child(odd) {
        float: left;
        text-align: center;
        margin-left: 20px;
      }

      #service-widget-container li:nth-child(even) {
        float: right;
        text-align: center;
        margin-right: 20px;
      }

      #service-widget-container li:nth-child(odd) .services-label {
        text-align: left;
      }

      #service-widget-container li:nth-child(even) .services-label {
        text-align: left;
      }

      #service-widget-container .services-label {
        font-size: 13px;
        display: inline-block;
        line-height: 14px;
      }

      #service-widget-container .services-count {
        font-size: 33px;
        font-weight: bold;
        color: #474848;
      }

      #service-widget-container .services-label .first {
        display: block;
      }

      #service-widget-container .services-label {
        border-right: 2px solid #474848;
        padding-right: 7px;
      }

      #service-widget-container .dashicons, .dashicons-before:before {
        font-size: 26px;
        margin-right: 11px;
        color: #474848;
      }

  </style>
  <div class="inside">
    <div class="main" id="service-widget-container">
      <ul>
        <li class="dashicons-before dashicons-store">
          <span class="services-label">
            <span class="first">Published</span>
            <span class="second">Services</span>
          </span>
          <span class="services-count"><?php echo $published_service_count ?></span>
        </li>
        <li class="dashicons-before dashicons-portfolio">
          <span class="services-label">
            <span class="first">Requested</span>
            <span class="second">Services</span>
          </span>
          <span class="services-count"><?php echo $requested_service_count ?></span>
        </li>
      </ul>
    </div>
  </div>
	<?php
}

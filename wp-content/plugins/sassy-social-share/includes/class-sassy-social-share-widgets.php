<?php

/**
 * The file that defines classes for widgets
 *
 * Class definitions that include functions used for widgets.
 *
 * @since    1.0.0
 *
 */

/**
 * Standard Widget class.
 *
 * This is used to define functions for Standard Sharing Widget.
 *
 * @since    1.0.0
 */
class Sassy_Social_Share_Standard_Widget extends WP_Widget { 
	
	/**
	 * Options saved in database.
	 *
	 * @since    1.0.0
	 */
	private $options;

	/**
	 * Member to assign object of Sassy_Social_Share_Public Class.
	 *
	 * @since    1.0.0
	 */
	private $public_class_object;

	/**
	 * Assign plugin options to private member $options and define widget title, description etc.
	 *
	 * @since    1.0.0
	 */
	public function __construct() { 
		
		global $heateor_sss;

		$this->options = $heateor_sss->options;

		$this->public_class_object = new Sassy_Social_Share_Public( $heateor_sss->options, HEATEOR_SSS_VERSION );

		parent::__construct( 
			'Heateor_SSS_Sharing', // unique id 
			__( 'Sassy Social Share - Standard Widget' ), // Widget title 
			array( 'description' => __( 'Standard sharing widget. Let your website users share content on popular Social networks like Facebook, Twitter, Tumblr, Google+ and many more', 'sassy-social-share' ) )
		); 
	}  

	/**
	 * Render widget at front-end
	 *
	 * @since    1.0.0
	 */
	public function widget( $args, $instance ) { 
		// return if standard sharing is disabled
		if ( ! isset( $this->options['hor_enable'] ) ) {
			return;
		}
		extract( $args );
		if ( $instance['hide_for_logged_in'] == 1 && is_user_logged_in() ) return;
		
		global $post;
		$post_id = $post -> ID;
		if ( isset( $instance['target_url'] ) ) {
			if ( $instance['target_url'] == 'default' ) {
				$sharing_url = html_entity_decode( esc_url( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) );
				if ( is_home() ) {
					$sharing_url = home_url();
					$post_id = 0;
				} elseif ( ! is_singular() ) {
					$post_id = 0;
				} elseif ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] ) {
					$sharing_url = html_entity_decode( esc_url( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) );
				} elseif ( get_permalink( $post -> ID ) ) {
					$sharing_url = get_permalink( $post->ID );
				}
			} elseif ( $instance['target_url'] == 'homepage' ) {
				$sharing_url = home_url();
				$post_id = 0;
			} elseif ( $instance['target_url'] == 'custom' ) {
				$sharing_url = isset( $instance['target_url_custom'] ) ? trim( $instance['target_url_custom'] ) : get_permalink( $post->ID );
				$post_id = 0;
			}
		} else {
			$sharing_url = get_permalink( $post->ID );
		}
		$sharing_url = $this->public_class_object->apply_target_share_url_filter( $sharing_url, 'horizontal', ! is_singular() ? true : false );
		// share count transient ID
		$this->public_class_object->share_count_transient_id = $this->public_class_object->get_share_count_transient_id( $sharing_url );
		$cached_share_count = $this->public_class_object->get_cached_share_count( $this->public_class_object->share_count_transient_id );

		echo "<div class='heateor_sss_sharing_container heateor_sss_horizontal_sharing' " . ( $this->public_class_object->is_amp_page() ? "" : "heateor-sss-data-href='" . $sharing_url . "'" ) . ( ( $cached_share_count === false || $this->public_class_object->is_amp_page() ) ? "" : 'heateor-sss-no-counts="1"' ) .">";
		
		echo $before_widget;
		
		if ( ! empty( $instance['title'] ) ) { 
			$title = apply_filters( 'widget_title', $instance[ 'title' ] ); 
			echo $before_title . $title . $after_title;
		}

		if ( ! empty( $instance['before_widget_content'] ) ) { 
			echo '<div>' . $instance['before_widget_content'] . '</div>'; 
		}
		$short_url = $this->public_class_object->get_short_url( $sharing_url, $post_id );

		echo $this->public_class_object->prepare_sharing_html( $short_url ? $short_url : $sharing_url, 'horizontal', isset( $instance['show_counts'] ), isset( $instance['total_shares'] ), ! is_singular() ? true : false );

		if ( ! empty( $instance['after_widget_content'] ) ) { 
			echo '<div>' . $instance['after_widget_content'] . '</div>'; 
		}
		
		echo '</div>';
		if ( ( isset( $instance['show_counts'] ) || isset( $instance['total_shares'] ) ) && $cached_share_count === false ) {
			echo '<script>heateorSssLoadEvent(
		function() {
			// sharing counts
			heateorSssCallAjax(function() {
				heateorSssGetSharingCounts();
			});
		}
	);</script>';
		}
		echo $after_widget;
	} 

	/** 
	 * Everything which should happen when user edit widget at admin panel.
	 *
	 * @since    1.0.0
	 */
	public function update( $new_instance, $old_instance ) { 
		
		$instance = $old_instance; 
		$instance['title'] = strip_tags( $new_instance['title'] ); 
		$instance['show_counts'] = $new_instance['show_counts'];
		$instance['total_shares'] = $new_instance['total_shares']; 
		$instance['target_url'] = $new_instance['target_url'];
		$instance['target_url_custom'] = $new_instance['target_url_custom'];  
		$instance['before_widget_content'] = $new_instance['before_widget_content']; 
		$instance['after_widget_content'] = $new_instance['after_widget_content']; 
		$instance['hide_for_logged_in'] = $new_instance['hide_for_logged_in'];  

		return $instance; 

	}  

	/** 
	 * Widget options form at admin panel.
	 *
	 * @since    1.0.0
	 */
	public function form( $instance ) { 
		
		// default widget settings
		$defaults = array( 'title' => 'Share the joy', 'show_counts' => 0, 'total_shares' => 0, 'target_url' => 'default', 'target_url_custom' => '', 'before_widget_content' => '', 'after_widget_content' => '' );

		foreach ( $instance as $key => $value ) {
			if ( is_string( $value ) ) {
				$instance[ $key ] = esc_attr( $value );
			}
		}
		
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		?> 
		<script type="text/javascript">
			function heateorSssToggleHorSharingTargetUrl(val) {
				if (val == 'custom' ) {
					jQuery( '.heateorSssHorSharingTargetUrl' ).css( 'display', 'block' );
				} else {
					jQuery( '.heateorSssHorSharingTargetUrl' ).css( 'display', 'none' );
				}
			}
		</script>
		<p> 
			<p><strong>Note:</strong> <?php _e( 'Make sure "Standard Sharing Interface" is enabled in "Standard Interface" section at "Sassy Social Share" page.', 'sassy-social-share' ) ?></p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" /> <br/><br/>
			<label for="<?php echo $this->get_field_id( 'show_counts' ); ?>"><?php _e( 'Show individual share counts:', 'sassy-social-share' ); ?></label> 
			<input id="<?php echo $this->get_field_id( 'show_counts' ); ?>" name="<?php echo $this->get_field_name( 'show_counts' ); ?>" type="checkbox" value="1" <?php echo isset( $instance['show_counts'] ) && $instance['show_counts'] == 1 ? 'checked' : ''; ?> /><br/><br/>
			<label for="<?php echo $this->get_field_id( 'total_shares' ); ?>"><?php _e( 'Show total shares:', 'sassy-social-share' ); ?></label> 
			<input id="<?php echo $this->get_field_id( 'total_shares' ); ?>" name="<?php echo $this->get_field_name( 'total_shares' ); ?>" type="checkbox" value="1" <?php echo isset( $instance['total_shares'] ) && $instance['total_shares'] == 1 ? 'checked' : ''; ?> /><br/> <br/>
			<label for="<?php echo $this->get_field_id( 'target_url' ); ?>"><?php _e( 'Target Url:', 'sassy-social-share' ); ?></label> 
			<select style="width: 95%" onchange="heateorSssToggleHorSharingTargetUrl(this.value)" class="widefat" id="<?php echo $this->get_field_id( 'target_url' ); ?>" name="<?php echo $this->get_field_name( 'target_url' ); ?>">
				<option value="">--<?php _e( 'Select', 'sassy-social-share' ) ?>--</option>
				<option value="default" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'default' ? 'selected' : '' ; ?>>Url of the webpage where icons are located (default)</option>
				<option value="homepage" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'homepage' ? 'selected' : '' ; ?>>Url of the homepage of your website</option>
				<option value="custom" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'custom' ? 'selected' : '' ; ?>>Custom Url</option>
			</select>
			<input placeholder="Custom url" style="margin-top: 5px; <?php echo ! isset( $instance['target_url'] ) || $instance['target_url'] != 'custom' ? 'display: none' : '' ; ?>" class="widefat heateorSssHorSharingTargetUrl" id="<?php echo $this->get_field_id( 'target_url_custom' ); ?>" name="<?php echo $this->get_field_name( 'target_url_custom' ); ?>" type="text" value="<?php echo isset( $instance['target_url_custom'] ) ? $instance['target_url_custom'] : ''; ?>" /> 
			<label for="<?php echo $this->get_field_id( 'before_widget_content' ); ?>"><?php _e( 'Before widget content:', 'sassy-social-share' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'before_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'before_widget_content' ); ?>" type="text" value="<?php echo $instance['before_widget_content']; ?>" /> 
			<label for="<?php echo $this->get_field_id( 'after_widget_content' ); ?>"><?php _e( 'After widget content:', 'sassy-social-share' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'after_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'after_widget_content' ); ?>" type="text" value="<?php echo $instance['after_widget_content']; ?>" /> 
			<br /><br /><label for="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>"><?php _e( 'Hide for logged in users:', 'sassy-social-share' ); ?></label> 
			<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>" name="<?php echo $this->get_field_name( 'hide_for_logged_in' ); ?>" type="text" value="1" <?php if ( isset( $instance['hide_for_logged_in'] )  && $instance['hide_for_logged_in'] == 1 ) echo 'checked="checked"'; ?> /> 
		</p> 
	<?php 
    }

} 

/**
 * Floating Widget class
 *
 * This is used to define functions for Floating Sharing Widget.
 *
 * @since    1.0.0
 */
class Sassy_Social_Share_Floating_Widget extends WP_Widget { 
	
	/**
	 * Options saved in database.
	 *
	 * @since    1.0.0
	 */
	private $options;

	/**
	 * Member to assign object of Sassy_Social_Share_Public Class.
	 *
	 * @since    1.0.0
	 */
	private $public_class_object;

	/**
	 * Assign plugin options to private member $options and define widget title, description etc.
	 *
	 * @since    1.0.0
	 */
	public function __construct() { 
		
		global $heateor_sss;

		$this->options = $heateor_sss->options;

		$this->public_class_object = new Sassy_Social_Share_Public( $heateor_sss->options, HEATEOR_SSS_VERSION );

		parent::__construct( 
			'Heateor_SSS_Floating_Sharing', // unique id 
			'Sassy Social Share - Floating Widget', // widget title 
			// additional parameters 
			array(
				'description' => __( 'Floating sharing widget. Let your website users share content on popular Social networks like Facebook, Twitter, Tumblr, Google+ and many more', 'sassy-social-share' ) ) 
			); 
	}  

	/**
	 * Render widget at front-end
	 *
	 * @since    1.0.0
	 */
	public function widget( $args, $instance ) { 
		
		// return if vertical sharing is disabled
		if ( ! isset( $this->options['vertical_enable'] ) ) {
			return;
		}
		extract( $args );
		if ( $instance['hide_for_logged_in'] == 1 && is_user_logged_in() ) return;
		
		global $post;
		$post_id = $post -> ID;
		if ( isset( $instance['target_url'] ) ) {
			if ( $instance['target_url'] == 'default' ) {
				$sharing_url = html_entity_decode( esc_url( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) );
				if ( is_home() ) {
					$sharing_url = home_url();
					$post_id = 0;
				}  elseif ( ! is_singular() ) {
					$post_id = 0;
				} elseif ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] ) {
					$sharing_url = html_entity_decode( esc_url( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] ) );
				} elseif ( get_permalink( $post -> ID ) ) {
					$sharing_url = get_permalink( $post->ID );
				}
			} elseif ( $instance['target_url'] == 'homepage' ) {
				$sharing_url = home_url();
				$post_id = 0;
			} elseif ( $instance['target_url'] == 'custom' ) {
				$sharing_url = isset( $instance['target_url_custom'] ) ? trim( $instance['target_url_custom'] ) : get_permalink( $post->ID );
				$post_id = 0;
			}
		} else {
			$sharing_url = get_permalink( $post->ID );
		}
		$sharing_url = $this->public_class_object->apply_target_share_url_filter( $sharing_url, 'vertical', false );
		$ssOffset = 0;
		if ( isset( $instance['alignment'] ) && isset( $instance[$instance['alignment'] . '_offset'] ) ) {
			$ssOffset = $instance[$instance['alignment'] . '_offset'];
		}
		
		// share count transient ID
		$this->public_class_object->share_count_transient_id = $this->public_class_object->get_share_count_transient_id( $sharing_url );
		$cached_share_count = $this->public_class_object->get_cached_share_count( $this->public_class_object->share_count_transient_id );

		echo "<div class='heateor_sss_sharing_container heateor_sss_vertical_sharing" . ( isset( $this->options['hide_mobile_sharing'] ) ? ' heateor_sss_hide_sharing' : '' ) . ( isset( $this->options['bottom_mobile_sharing'] ) ? ' heateor_sss_bottom_sharing' : '' ) . "' ss-offset='" . $ssOffset . "' style='width:" . ( ( $this->options['vertical_sharing_size'] ? $this->options['vertical_sharing_size'] : 35) + 4) . "px;".( isset( $instance['alignment'] ) && $instance['alignment'] != '' && isset( $instance[$instance['alignment'].'_offset'] ) ? $instance['alignment'].': '. ( $instance[$instance['alignment'].'_offset'] == '' ? 0 : $instance[$instance['alignment'].'_offset'] ) .'px;' : '' ).( isset( $instance['top_offset'] ) ? 'top: '. ( $instance['top_offset'] == '' ? 0 : $instance['top_offset'] ) .'px;' : '' ) . ( isset( $instance['vertical_bg'] ) && $instance['vertical_bg'] != '' ? 'background-color: '.$instance['vertical_bg'] . ';' : '-webkit-box-shadow:none;box-shadow:none;' ) . "' " . ( $this->public_class_object->is_amp_page() ? "" : "heateor-sss-data-href='" . $sharing_url . "'" ) . ( ( $cached_share_count === false || $this->public_class_object->is_amp_page() ) ? "" : 'heateor-sss-no-counts="1"' ) .">";
		
		$short_url = $this->public_class_object->get_short_url( $sharing_url, $post_id );

		//echo $before_widget;
		
		echo $this->public_class_object->prepare_sharing_html( $short_url ? $short_url : $sharing_url, 'vertical', isset( $instance['show_counts'] ), isset( $instance['total_shares'] ) );

		echo '</div>';
		if ( ( isset( $instance['show_counts'] ) || isset( $instance['total_shares'] ) ) && $cached_share_count === false ) {
			echo '<script>heateorSssLoadEvent(
		function() {
			// sharing counts
			heateorSssCallAjax(function() {
				heateorSssGetSharingCounts();
			});
		}
	);</script>';
		}
		//echo $after_widget;
	}  

	/** 
	 * Everything which should happen when user edit widget at admin panel.
	 *
	 * @since    1.0.0
	 */
	public function update( $new_instance, $old_instance ) { 
		
		$instance = $old_instance; 
		$instance['target_url'] = $new_instance['target_url'];
		$instance['show_counts'] = $new_instance['show_counts']; 
		$instance['total_shares'] = $new_instance['total_shares']; 
		$instance['target_url_custom'] = $new_instance['target_url_custom'];
		$instance['alignment'] = $new_instance['alignment'];
		$instance['left_offset'] = $new_instance['left_offset'];
		$instance['right_offset'] = $new_instance['right_offset'];
		$instance['top_offset'] = $new_instance['top_offset'];
		$instance['vertical_bg'] = $new_instance['vertical_bg'];
		$instance['hide_for_logged_in'] = $new_instance['hide_for_logged_in'];  

		return $instance; 

	}  

	/** 
	 * Widget options form at admin panel.
	 *
	 * @since    1.0.0
	 */
	public function form( $instance ) { 
		
		/* Set up default widget settings. */ 
		$defaults = array( 'alignment' => 'left', 'show_counts' => 0, 'total_shares' => 0, 'left_offset' => '40', 'right_offset' => '0', 'target_url' => 'default', 'target_url_custom' => '', 'top_offset' => '100', 'vertical_bg' => '' );

		foreach ( $instance as $key => $value ) {
			if ( is_string( $value ) ) {
				$instance[ $key ] = esc_attr( $value );
			}
		}
		
		$instance = wp_parse_args( ( array ) $instance, $defaults ); 
		?> 
		<p> 
			<script>
			function heateorSssToggleSharingOffset(alignment) {
				if (alignment == 'left' ) {
					jQuery( '.heateorSssSharingLeftOffset' ).css( 'display', 'block' );
					jQuery( '.heateorSssSharingRightOffset' ).css( 'display', 'none' );
				} else {
					jQuery( '.heateorSssSharingLeftOffset' ).css( 'display', 'none' );
					jQuery( '.heateorSssSharingRightOffset' ).css( 'display', 'block' );
				}
			}
			function heateorSssToggleVerticalSharingTargetUrl(val) {
				if (val == 'custom' ) {
					jQuery( '.heateorSssVerticalSharingTargetUrl' ).css( 'display', 'block' );
				} else {
					jQuery( '.heateorSssVerticalSharingTargetUrl' ).css( 'display', 'none' );
				}
			}
			</script>
			<p><strong>Note:</strong> <?php _e( 'Make sure "Floating Interface" is enabled in "Floating Interface" section at "Sassy Social Share" page.', 'sassy-social-share' ) ?></p>
			<label for="<?php echo $this->get_field_id( 'show_counts' ); ?>"><?php _e( 'Show individual share counts:', 'sassy-social-share' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'show_counts' ); ?>" name="<?php echo $this->get_field_name( 'show_counts' ); ?>" type="checkbox" value="1" <?php echo isset( $instance['show_counts'] ) && $instance['show_counts'] == 1 ? 'checked' : ''; ?> /><br/><br/> 
			<label for="<?php echo $this->get_field_id( 'total_shares' ); ?>"><?php _e( 'Show total shares:', 'sassy-social-share' ); ?></label> 
			<input id="<?php echo $this->get_field_id( 'total_shares' ); ?>" name="<?php echo $this->get_field_name( 'total_shares' ); ?>" type="checkbox" value="1" <?php echo isset( $instance['total_shares'] ) && $instance['total_shares'] == 1 ? 'checked' : ''; ?> /><br/> <br/>
			<label for="<?php echo $this->get_field_id( 'target_url' ); ?>"><?php _e( 'Target Url:', 'sassy-social-share' ); ?></label> 
			<select style="width: 95%" onchange="heateorSssToggleVerticalSharingTargetUrl(this.value)" class="widefat" id="<?php echo $this->get_field_id( 'target_url' ); ?>" name="<?php echo $this->get_field_name( 'target_url' ); ?>">
				<option value="">--<?php _e( 'Select', 'sassy-social-share' ) ?>--</option>
				<option value="default" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'default' ? 'selected' : '' ; ?>>Url of the webpage where icons are located (default)</option>
				<option value="homepage" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'homepage' ? 'selected' : '' ; ?>>Url of the homepage of your website</option>
				<option value="custom" <?php echo isset( $instance['target_url'] ) && $instance['target_url'] == 'custom' ? 'selected' : '' ; ?>>Custom Url</option>
			</select>
			<input placeholder="Custom url" style="width:95%; margin-top: 5px; <?php echo ! isset( $instance['target_url'] ) || $instance['target_url'] != 'custom' ? 'display: none' : '' ; ?>" class="widefat heateorSssVerticalSharingTargetUrl" id="<?php echo $this->get_field_id( 'target_url_custom' ); ?>" name="<?php echo $this->get_field_name( 'target_url_custom' ); ?>" type="text" value="<?php echo isset( $instance['target_url_custom'] ) ? $instance['target_url_custom'] : ''; ?>" /> 
			<label for="<?php echo $this->get_field_id( 'alignment' ); ?>"><?php _e( 'Alignment', 'sassy-social-share' ); ?></label> 
			<select onchange="heateorSssToggleSharingOffset(this.value)" style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'alignment' ); ?>" name="<?php echo $this->get_field_name( 'alignment' ); ?>">
				<option value="left" <?php echo $instance['alignment'] == 'left' ? 'selected' : ''; ?>><?php _e( 'Left', 'sassy-social-share' ) ?></option>
				<option value="right" <?php echo $instance['alignment'] == 'right' ? 'selected' : ''; ?>><?php _e( 'Right', 'sassy-social-share' ) ?></option>
			</select>
			<div class="heateorSssSharingLeftOffset" <?php echo $instance['alignment'] == 'right' ? 'style="display: none"' : ''; ?>>
				<label for="<?php echo $this->get_field_id( 'left_offset' ); ?>"><?php _e( 'Left Offset', 'sassy-social-share' ); ?></label> 
				<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'left_offset' ); ?>" name="<?php echo $this->get_field_name( 'left_offset' ); ?>" type="text" value="<?php echo $instance['left_offset']; ?>" />px<br/>
			</div>
			<div class="heateorSssSharingRightOffset" <?php echo $instance['alignment'] == 'left' ? 'style="display: none"' : ''; ?>>
				<label for="<?php echo $this->get_field_id( 'right_offset' ); ?>"><?php _e( 'Right Offset', 'sassy-social-share' ); ?></label> 
				<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'right_offset' ); ?>" name="<?php echo $this->get_field_name( 'right_offset' ); ?>" type="text" value="<?php echo $instance['right_offset']; ?>" />px<br/>
			</div>
			<label for="<?php echo $this->get_field_id( 'top_offset' ); ?>"><?php _e( 'Top Offset', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'top_offset' ); ?>" name="<?php echo $this->get_field_name( 'top_offset' ); ?>" type="text" value="<?php echo $instance['top_offset']; ?>" />px<br/>
			
			<label for="<?php echo $this->get_field_id( 'vertical_bg' ); ?>"><?php _e( 'Background Color', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'vertical_bg' ); ?>" name="<?php echo $this->get_field_name( 'vertical_bg' ); ?>" type="text" value="<?php echo $instance['vertical_bg']; ?>" />
			
			<br /><br /><label for="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>"><?php _e( 'Hide for logged in users:', 'sassy-social-share' ); ?></label> 
			<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>" name="<?php echo $this->get_field_name( 'hide_for_logged_in' ); ?>" type="text" value="1" <?php if ( isset( $instance['hide_for_logged_in'] )  && $instance['hide_for_logged_in'] == 1 ) echo 'checked="checked"'; ?> /> 
		</p> 
	<?php 
    } 
}

/**
 * Follow Icons Widget class
 *
 * This is used to define functions for Follow Icons Widget
 *
 * @since    3.1.7
 */
class Sassy_Social_Share_Follow_Widget extends WP_Widget { 

	/**
	 * Assign plugin options to private member $options and define widget title, description etc.
	 *
	 * @since    3.1.7
	 */
	public function __construct() {

		parent::__construct( 
			'Heateor_SSS_Follow', // unique id 
			__( 'Sassy Social Share - Follow Icons' ), // Widget title 
			array( 'description' => __( 'These icons link to your Social Media accounts', 'sassy-social-share' ) )
		);

	}  

	/**
	 * Render widget at front-end
	 *
	 * @since    3.1.7
	 */
	public function widget( $args, $instance ) { 

		extract( $args );
		
		echo $before_widget;

		if ( ! empty( $instance['before_widget_content'] ) ) { 
			echo '<div>' . $instance['before_widget_content'] . '</div>'; 
		}
		
		echo '<div class="heateor_sss_follow_icons_container">';

		if ( ! empty( $instance['title'] ) ) { 
			$title = apply_filters( 'widget_title', $instance[ 'title' ] ); 
			echo $before_title . $title . $after_title;
		}

		echo $this->follow_icons( $instance );

		echo '<div style="clear:both"></div>';
		echo '</div>';

		if ( ! empty( $instance['after_widget_content'] ) ) { 
			echo '<div>' . $instance['after_widget_content'] . '</div>'; 
		}

		echo $after_widget;

	} 

	/**
	 * Render follow icons
	 *
	 * @since    3.1.7
	 */
	private function follow_icons( $instance ) {

		$html = '';
		$icon_style = 'width:'. $instance['size'] .'px;height:'. $instance['size'] .'px;'. ( $instance['icon_shape'] == 'round' ? 'border-radius:999px;' : '' );
		$html .= '<ul class="heateor_sss_follow_ul">';
		if ( $instance['facebook'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Facebook" title="Facebook" class="heateorSssSharing heateorSssFacebookBackground"><a target="_blank" href="'. $instance['facebook'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssFacebookSvg"></ss></a></i></li>';
		}
		if ( $instance['twitter'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Twitter" title="Twitter" class="heateorSssSharing heateorSssTwitterBackground"><a target="_blank" href="'. $instance['twitter'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssTwitterSvg"></ss></a></i></li>';
		}
		if ( $instance['instagram'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Instagram" title="Instagram" class="heateorSssSharing heateorSssInstagramBackground"><a target="_blank" href="'. $instance['instagram'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssInstagramSvg"></ss></a></i></li>';
		}
		if ( $instance['pinterest'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Pinterest" title="Pinterest" class="heateorSssSharing heateorSssPinterestBackground"><a target="_blank" href="'. $instance['pinterest'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssPinterestSvg"></ss></a></i></li>';
		}
		if ( $instance['behance'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Behance" title="Behance" class="heateorSssSharing heateorSssBehanceBackground"><a target="_blank" href="'. $instance['behance'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssBehanceSvg"></ss></a></i></li>';
		}
		if ( $instance['flickr'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Flickr" title="Flickr" class="heateorSssSharing heateorSssFlickrBackground"><a target="_blank" href="'. $instance['flickr'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssFlickrSvg"></ss></a></i></li>';
		}
		if ( $instance['foursquare'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Foursquare" title="Foursquare" class="heateorSssSharing heateorSssFoursquareBackground"><a target="_blank" href="'. $instance['foursquare'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssFoursquareSvg"></ss></a></i></li>';
		}
		if ( $instance['github'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Github" title="Github" class="heateorSssSharing heateorSssGithubBackground"><a target="_blank" href="'. $instance['github'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssGithubSvg"></ss></a></i></li>';
		}
		if ( $instance['google'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Google+" title="Google+" class="heateorSssSharing heateorSssGoogleplusBackground"><a target="_blank" href="'. $instance['google'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssGoogleplusSvg"></ss></a></i></li>';
		}
		if ( $instance['linkedin'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Linkedin" title="Linkedin" class="heateorSssSharing heateorSssLinkedinBackground"><a target="_blank" href="'. $instance['linkedin'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssLinkedinSvg"></ss></a></i></li>';
		}
		if ( $instance['linkedin_company'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Linkedin Company" title="Linkedin Company" class="heateorSssSharing heateorSssLinkedinBackground"><a target="_blank" href="'. $instance['linkedin_company'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssLinkedinSvg"></ss></a></i></li>';
		}
		if ( $instance['snapchat'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Snapchat" title="Snapchat" class="heateorSssSharing heateorSssSnapchatBackground"><a target="_blank" href="'. $instance['snapchat'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssSnapchatSvg"></ss></a></i></li>';
		}
		if ( $instance['tumblr'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Tumblr" title="Tumblr" class="heateorSssSharing heateorSssTumblrBackground"><a target="_blank" href="'. $instance['tumblr'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssTumblrSvg"></ss></a></i></li>';
		}
		if ( $instance['vimeo'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Vimeo" title="Vimeo" class="heateorSssSharing heateorSssVimeoBackground"><a target="_blank" href="'. $instance['vimeo'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssVimeoSvg"></ss></a></i></li>';
		}
		if ( $instance['youtube'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Youtube" title="Youtube" class="heateorSssSharing heateorSssYoutubeBackground"><a target="_blank" href="'. $instance['youtube'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssYoutubeSvg"></ss></a></i></li>';
		}
		if ( $instance['youtube_channel'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="Youtube Channel" title="Youtube Channel" class="heateorSssSharing heateorSssYoutubeBackground"><a target="_blank" href="'. $instance['youtube_channel'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssYoutubeSvg"></ss></a></i></li>';
		}
		if ( $instance['rss_feed'] ) {
			$html .= '<li class="heateorSssSharingRound"><i style="'. $icon_style .'" alt="RSS Feed" title="RSS Feed" class="heateorSssSharing heateorSssRSSBackground"><a target="_blank" href="'. $instance['rss_feed'] .'" rel="noopener"><ss style="display:block" class="heateorSssSharingSvg heateorSssRSSSvg"></ss></a></i></li>';
		}
		$html = apply_filters( 'heateor_sss_follow_icons', $html, $instance, $icon_style );
		$html .= '<ul>';

		return $html;

	}

	/**
	 * Validate the widget options
	 *
	 * @since    3.1.7
	 */ 
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['size'] = intval( $new_instance['size'] );
		$instance['icon_shape'] = $new_instance['icon_shape'];
		$instance['facebook'] = $new_instance['facebook'];
		$instance['twitter'] = $new_instance['twitter'];
		$instance['instagram'] = $new_instance['instagram'];
		$instance['pinterest'] = $new_instance['pinterest'];
		$instance['behance'] = $new_instance['behance'];
		$instance['flickr'] = $new_instance['flickr'];
		$instance['foursquare'] = $new_instance['foursquare'];
		$instance['github'] = $new_instance['github'];
		$instance['google'] = $new_instance['google'];
		$instance['linkedin'] = $new_instance['linkedin'];
		$instance['linkedin_company'] = $new_instance['linkedin_company'];
		$instance['snapchat'] = $new_instance['snapchat'];
		$instance['tumblr'] = $new_instance['tumblr'];
		$instance['vimeo'] = $new_instance['vimeo'];
		$instance['youtube'] = $new_instance['youtube'];
		$instance['youtube_channel'] = $new_instance['youtube_channel'];
		$instance['rss_feed'] = $new_instance['rss_feed'];
		$instance['before_widget_content'] = $new_instance['before_widget_content']; 
		$instance['after_widget_content'] = $new_instance['after_widget_content'];

		return $instance;

	}  

	/** 
	 * Widget options form
	 *
	 * @since    3.1.7
	 */
	public function form( $instance ) { 
		
		/* default widget settings. */ 
		$defaults = array( 'title' => '', 'size' => '32', 'icon_shape' => 'round', 'facebook' => '', 'twitter' => '', 'instagram' => '', 'pinterest' => '', 'behance' => '', 'flickr' => '', 'foursquare' => '', 'github' => '', 'google' => '', 'linkedin' => '', 'linkedin_company' => '', 'snapchat' => '', 'tumblr' => '', 'vimeo' => '', 'youtube' => '', 'youtube_channel' => '', 'rss_feed' => '', 'before_widget_content' => '', 'after_widget_content' => '' );

		foreach ( $instance as $key => $value ) {
			if ( is_string( $value ) ) {
				$instance[ $key ] = esc_attr( $value );
			}
		}
		
		$instance = wp_parse_args( ( array ) $instance, $defaults );
		?> 
		<p>
			<label for="<?php echo $this->get_field_id( 'before_widget_content' ); ?>"><?php _e( 'Before widget content:', 'sassy-social-share' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'before_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'before_widget_content' ); ?>" type="text" value="<?php echo $instance['before_widget_content']; ?>" /><br/><br/>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" /><br/><br/>
			<label for="<?php echo $this->get_field_id( 'size' ); ?>"><?php _e( 'Size of icons', 'sassy-social-share' ); ?></label> 
			<input style="width: 82%" class="widefat" id="<?php echo $this->get_field_id( 'size' ); ?>" name="<?php echo $this->get_field_name( 'size' ); ?>" type="text" value="<?php echo $instance['size']; ?>" />pixels<br/><br/>
			<label for="<?php echo $this->get_field_id( 'icon_shape' ); ?>"><?php _e( 'Icon Shape', 'sassy-social-share' ); ?></label> 
			<select style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'icon_shape' ); ?>" name="<?php echo $this->get_field_name( 'icon_shape' ); ?>">
				<option value="round" <?php echo ! isset( $instance['icon_shape'] ) || $instance['icon_shape'] == 'round' ? 'selected' : '' ; ?>><?php _e( 'Round', 'sassy-social-share' ); ?></option>
				<option value="square" <?php echo isset( $instance['icon_shape'] ) && $instance['icon_shape'] == 'square' ? 'selected' : '' ; ?>><?php _e( 'Square', 'sassy-social-share' ); ?></option>
			</select><br/><br/>
			<label for="<?php echo $this->get_field_id( 'facebook' ); ?>"><?php _e( 'Facebook ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" type="text" value="<?php echo $instance['facebook']; ?>" /><br/>
			<span>https://www.facebook.com/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'twitter' ); ?>"><?php _e( 'Twitter ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" type="text" value="<?php echo $instance['twitter']; ?>" /><br/>
			<span>https://twitter.com/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'instagram' ); ?>"><?php _e( 'Instagram ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'instagram' ); ?>" name="<?php echo $this->get_field_name( 'instagram' ); ?>" type="text" value="<?php echo $instance['instagram']; ?>" /><br/>
			<span>https://www.instagram.com/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'pinterest' ); ?>"><?php _e( 'Pinterest ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'pinterest' ); ?>" name="<?php echo $this->get_field_name( 'pinterest' ); ?>" type="text" value="<?php echo $instance['pinterest']; ?>" /><br/>
			<span>https://www.pinterest.com/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'behance' ); ?>"><?php _e( 'Behance ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'behance' ); ?>" name="<?php echo $this->get_field_name( 'behance' ); ?>" type="text" value="<?php echo $instance['behance']; ?>" /><br/>
			<span>https://www.behance.net/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'flickr' ); ?>"><?php _e( 'Flickr ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'flickr' ); ?>" name="<?php echo $this->get_field_name( 'flickr' ); ?>" type="text" value="<?php echo $instance['flickr']; ?>" /><br/>
			<span>https://www.flickr.com/photos/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'foursquare' ); ?>"><?php _e( 'Foursquare ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'foursquare' ); ?>" name="<?php echo $this->get_field_name( 'foursquare' ); ?>" type="text" value="<?php echo $instance['foursquare']; ?>" /><br/>
			<span>https://foursquare.com/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'github' ); ?>"><?php _e( 'Github ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'github' ); ?>" name="<?php echo $this->get_field_name( 'github' ); ?>" type="text" value="<?php echo $instance['github']; ?>" /><br/>
			<span>https://github.com/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'google' ); ?>"><?php _e( 'Google+ ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'google' ); ?>" name="<?php echo $this->get_field_name( 'google' ); ?>" type="text" value="<?php echo $instance['google']; ?>" /><br/>
			<span>https://plus.google.com/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'linkedin' ); ?>"><?php _e( 'LinkedIn ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'linkedin' ); ?>" name="<?php echo $this->get_field_name( 'linkedin' ); ?>" type="text" value="<?php echo $instance['linkedin']; ?>" /><br/>
			<span>https://www.linkedin.com/in/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'linkedin_company' ); ?>"><?php _e( 'LinkedIn Company ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'linkedin_company' ); ?>" name="<?php echo $this->get_field_name( 'linkedin_company' ); ?>" type="text" value="<?php echo $instance['linkedin_company']; ?>" /><br/>
			<span>https://www.linkedin.com/company/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'snapchat' ); ?>"><?php _e( 'Snapchat ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'snapchat' ); ?>" name="<?php echo $this->get_field_name( 'snapchat' ); ?>" type="text" value="<?php echo $instance['snapchat']; ?>" /><br/>
			<span>https://www.snapchat.com/add/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'tumblr' ); ?>"><?php _e( 'Tumblr ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'tumblr' ); ?>" name="<?php echo $this->get_field_name( 'tumblr' ); ?>" type="text" value="<?php echo $instance['tumblr']; ?>" /><br/>
			<span>https://ID.tumblr.com</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'vimeo' ); ?>"><?php _e( 'Vimeo ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'vimeo' ); ?>" name="<?php echo $this->get_field_name( 'vimeo' ); ?>" type="text" value="<?php echo $instance['vimeo']; ?>" /><br/>
			<span>https://vimeo.com/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'youtube' ); ?>"><?php _e( 'Youtube ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'youtube' ); ?>" name="<?php echo $this->get_field_name( 'youtube' ); ?>" type="text" value="<?php echo $instance['youtube']; ?>" /><br/>
			<span>https://www.youtube.com/user/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'youtube_channel' ); ?>"><?php _e( 'Youtube Channel ID:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'youtube_channel' ); ?>" name="<?php echo $this->get_field_name( 'youtube_channel' ); ?>" type="text" value="<?php echo $instance['youtube_channel']; ?>" /><br/>
			<span>https://www.youtube.com/channel/ID</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'rss_feed' ); ?>"><?php _e( 'RSS Feed URL:', 'sassy-social-share' ); ?></label> 
			<input style="width: 95%" class="widefat" id="<?php echo $this->get_field_id( 'rss_feed' ); ?>" name="<?php echo $this->get_field_name( 'rss_feed' ); ?>" type="text" value="<?php echo $instance['rss_feed']; ?>" /><br/>
			<span>http://www.example.com/feed/</span><br/><br/>
			<label for="<?php echo $this->get_field_id( 'after_widget_content' ); ?>"><?php _e( 'After widget content:', 'sassy-social-share' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'after_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'after_widget_content' ); ?>" type="text" value="<?php echo $instance['after_widget_content']; ?>" /> 
		</p>
	<?php 
	
    }
}
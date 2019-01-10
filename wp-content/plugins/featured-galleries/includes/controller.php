<?php

class FG_Controller {

	/***********************************************************************/
	/***************************  CONSTRUCTOR  *****************************/
	/***********************************************************************/

	function __construct() {

		add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );

		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );

		add_action( 'add_meta_boxes', [ $this, 'register_metabox' ] );

		add_action( 'save_post', [ $this, 'update_perm_metadata_via_save_post' ], 1, 2 );

		add_action( 'wp_ajax_fg_save_temp_metadata', [ $this, 'update_temp_metadata_via_ajax' ] );

	}

	/***********************************************************************/
	/***********************  BACK-END ADMIN SETUP  ************************/
	/***********************************************************************/

	public function load_textdomain() {

		// LOAD THE TEXT DOMAIN FOR INTERNATIONALIZATION

		load_plugin_textdomain( 'featured-gallery', false, dirname( plugin_basename( FG_PLUGIN_FILE ) ) . '/languages/' );

	}

	public function enqueue_admin_assets( $hook ) {

		// MAKE SURE WE ARE ON A POST ADD/EDIT SCREEN

		if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

			// LOAD THE LIST OF POST TYPES THAT FUNCTIONAL GALLERIES IS ATTACHED TO

			$post_types	= apply_filters( 'fg_post_types', [ 'post', 'page' ] );

			// LOAD THE CURRENT POST

			global $post;

			// MAKE SURE THE CURRENT POST TYPE IS ONE WHERE WE ARE LOADING FUNCTIONAL GALLERIES

			if ( in_array( $post->post_type, $post_types ) ) {

				// ENQUEUE WP MEDIA

				wp_enqueue_media([
					'post' => $post->ID
				]);

				// ENQUEUE OUR SCRIPT

				wp_enqueue_script( 'fg-script-admin', plugin_dir_url( FG_PLUGIN_FILE ) . 'assets/scripts/fg-admin.js', [], FG_PLUGIN_VERSION );

				// SENT THE SITE'S ADMIN AJAX URL TO OUR SCRIPT BY USING wp_localize_script. THIS WILL SET A GLOBAL JS
				// VARIABLE CALLED fgInfoFromPHP, WHICH IS AN OBJECT WITH A KEY OF 'wpAdminAjaxURL' WHICH WILL HAVE THE
				// CORRECT VALUE

				wp_localize_script( 'fg-script-admin', 'fgInfoFromPHP', [
					'wpAdminAjaxURL' => admin_url('admin-ajax.php'),
					'showDetailsSidebar' => apply_filters( 'fg_show_sidebar', false ),
					'useLegacySelection' => apply_filters( 'fg_use_legacy_selection', false )
				]);

				// ENQUEUE OUT STYLESHEETS

				wp_enqueue_style( 'fg-style-admin', plugin_dir_url( FG_PLUGIN_FILE ) . 'assets/stylesheets/fg-admin.css', [], FG_PLUGIN_VERSION );

			}

		}

	}

	public function register_metabox() {

		// LOAD THE VALUES THAT WE HAVE FILTERS FOR

		$post_types	= apply_filters( 'fg_post_types', [ 'post', 'page' ] );
		$context	= apply_filters( 'fg_context', 'side' );
		$priority	= apply_filters( 'fg_priority', 'default' );

		// LOOP THROUGH ALL SUPPORTED POST TYPES AND ADD THE FEATURED GALLERY METABOX TO EACH

		foreach ( $post_types as $post_type ) {

			add_meta_box( 'featuredgallerydiv', __( 'Featured Gallery', 'featured-gallery' ), [ $this, 'display_metabox' ], $post_type, $context, $priority );

		}

	}

	public function display_metabox() {

		global $post;

		// ATTEMPT TO LOAD EXISTING DATA

		$galleryIDs  = get_post_gallery_ids( $post->ID );

		// IF THERE IS DATA...

		if ( count($galleryIDs) > 0 ) {

			// LOOP THROUGH AND BUILD THE HTML PREVIEW

			$galleryHTML = '';

			foreach ( $galleryIDs as $ID ) {

				$galleryHTML .= '<li><button type="button"></button><img id="' . $ID . '" src="' . wp_get_attachment_image_src( $ID, 'thumbnail' )[0] . '"></li>';

			}

			// SET THE SELECT BUTTON'S TEXT

			$selectButtonText = __( 'Edit Selection', 'featured-gallery' );

			// DON'T SET CSS STYLES TO HIDE THE REMOVE ALL BUTTON

			$hideIfNoSelection = '';

		} else {

			// SET THE HTML PREVIEW TO EMPTY

			$galleryHTML = '';

			// SET THE SELECT BUTTON'S TEXT

			$selectButtonText = __( 'Select Images', 'featured-gallery' );

			// SET CSS STYLES TO HIDE THE REMOVE ALL BUTTON

			$hideIfNoSelection = ' style="display:none;"';

		} 

		// BUILD A COMMA DELIMITED STRING FROM THE GALLERY IDs, TO STORE AS POST META

		$galleryString = implode(',', $galleryIDs);

		// OVERWRITE THE TEMPORARY FEATURE GALLERY DATA WITH THE PERMANENT DATA. THIS IS A PRECAUTION IN CASE
		// SOMEONE PREVIOUSLY CLICKED "Preview Changes" AND THEN EXISTED WITHOUT SAVING. BASICALLY, THE TEMP
		// METADATA SHOULD ALWAYS REFLECT WHAT IS SHOWN IN THE PREVIEW.

		update_post_meta( $post->ID, 'fg_temp_metadata', $galleryString );

		// BUILD THE HTML FOR THE METABOX AND ECHO IT TO THE PAGE

		echo '

			<input type="hidden" name="fg_temp_noncedata" id="fg_temp_noncedata" value="' . wp_create_nonce( plugin_basename( FG_PLUGIN_FILE ).'_temp' ) . '" />
			<input type="hidden" name="fg_perm_noncedata" id="fg_perm_noncedata" value="' . wp_create_nonce( plugin_basename( FG_PLUGIN_FILE ).'_perm' ) . '" />

			<p class="post-attributes-label-wrapper post-attributes-label hide-if-js">Image IDs</p>
			<input type="text" class="hide-if-js" name="fg_perm_metadata" id="fg_perm_metadata" value="' . $galleryString . '" data-post_id="' . $post->ID . '" />
			<p class="howto hide-if-js">Enable Javascript to use drag and drop Media Manager. Alternatively, type in the IDs of the images that you want as part of the Featured Gallery in the above text box, separating with commas.</p>

			<ul id="fg-post-gallery" class="hide-if-no-js">' . $galleryHTML . '</ul>

			<button type="button" class="button hide-if-no-js" id="fg_removeall"' . $hideIfNoSelection . '>' . __( 'Remove All', 'featured-gallery' ) . '</button>
			<button type="button" class="button hide-if-no-js" id="fg_select">' . $selectButtonText . '</button>

			<div style="clear:both;"></div>

		';

	}

	function update_temp_metadata_via_ajax() {

		if ( ! array_key_exists( 'fg_post_id', $_POST ) ) {

			$response = [
				'success' => false,
				'response' => 'This query is missing required HTTP parameters.'
			];

		} else {

			$postID = $_POST['fg_post_id'];
			$post = get_post( $postID );

			$response = self::update_metadata( $postID, $post, 'temp' );

		}

		// RESPOND TO THE USER

		header( 'Content-type: application/json' );

		echo json_encode( $response );

		wp_die();

	}

	public function update_perm_metadata_via_save_post( $postID, $post ) {

		return self::update_metadata( $postID, $post, 'perm' );

	}

	function update_metadata( $postID, $post, $type ) {

		// BUILD KEYS

		$metadata_key = 'fg_'.$type.'_metadata';
		$nonce_key = 'fg_'.$type.'_noncedata';

		// CHECK TO MAKE SURE EVERYTHING IS KOSHER

		if ( ! array_key_exists( $nonce_key, $_POST ) || ! wp_verify_nonce( $_POST[$nonce_key], plugin_basename( FG_PLUGIN_FILE ).'_'.$type ) ) {
			return [
				'success' => false,
				'response' => 'There is an error with this request. It doesn\'t have a valid nonce.'
			];
		}

		if ( ! array_key_exists( $metadata_key, $_POST ) ) {
			return [
				'success' => false,
				'response' => 'There is an error with this request. This query is missing required HTTP parameters.'
			];
		}

		if ( ! current_user_can( 'edit_post', $postID ) ) {
			return [
				'success' => false,
				'response' => 'You don\'t appear to be logged in, something has gone wrong.'
			];
		}

		if ( $post->post_type == 'revision' ) {
			return [
				'success' => false,
				'response' => 'Something has gone wrong, because this appears to be a revision.'
			];
		}

		// LOAD METADATA VALUE FROM POST PARAMETERS

		$metadata_value = $_POST[$metadata_key];

		// UPDATE THE METADATA VALUE IN THE DATABASE, EITHER BE DELETING IT OR UPDATING IT.

		if ( $metadata_value === null ) {

			$success = delete_post_meta($postID, $metadata_key);

		} else {

			$success = update_post_meta($postID, $metadata_key, $metadata_value);

		}

		// RESPOND TO THE USER

		if ( $success ) {

			return [
				'success' => true,
				'response' => 'Metadata updated successfully.'
			];

		} else {

			return [
				'success' => false,
				'response' => 'There was a problem with the DB update.'
			];

		}

	}

}
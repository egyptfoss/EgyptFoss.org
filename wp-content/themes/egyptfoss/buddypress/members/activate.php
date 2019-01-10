<?php
/**
 * BuddyPress - Members Activate
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<div id="buddypress">

	<?php

	/**
	 * Fires before the display of the member activation page.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_activation_page' ); ?>

	<div class="page" id="activate-page">

		<?php

		/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
		//do_action( 'template_notices' ); ?>

		<?php

		/**
		 * Fires before the display of the member activation page content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_before_activate_content' ); ?>

		<?php if ( bp_account_was_activated() ) : ?>

			<?php if ( isset( $_GET['e'] ) ) : ?>
      <div aria-required="true" class="required">
        <div class="succes-message text-center">
            <img src="<?php echo get_template_directory_uri(); ?>/img/sent_icon.svg" alt=""> <br/>
            <h3> <?php _e( 'Your account was activated successfully! Your account details have been sent to you in a separate email.', 'buddypress' ); ?></h3>
        </div>
      </div>
			<?php else : ?>
      <div aria-required="true" class="required">
        <div class="succes-message text-center">
            <img src="<?php echo get_template_directory_uri(); ?>/img/sent_icon.svg" alt=""> <br/>
            <h3><?php printf( __( 'Your account was activated successfully! <br/> You can now <a href="%s">log in</a> with the username and password you provided when you signed up.', 'egyptfoss' ), wp_login_url( bp_get_root_domain() ) ); ?></h3>
        </div> 
      </div> 
			<?php endif; ?>

		<?php else : ?>

        <div class="succes-message text-center">
            <img src="<?php echo get_template_directory_uri(); ?>/img/fail_icon.svg" alt=""> <br/>
            <h3><?php _e( 'Invalid Activation Url.', 'buddypress' ); ?> </h3>
        </div>  

	<!--		<form action="" method="get" class="standard-form" id="activation-form">

				<label for="key"><?php _e( 'Activation Key:', 'buddypress' ); ?></label>
				<input type="text" name="key" id="key" value="" />

				<p class="submit">
					<input type="submit" name="submit" value="<?php esc_attr_e( 'Activate', 'buddypress' ); ?>" />
				</p>

			</form> -->

		<?php endif; ?>

		<?php

		/**
		 * Fires after the display of the member activation page content.
		 *
		 * @since 1.1.0
		 */
		do_action( 'bp_after_activate_content' ); ?>

	</div><!-- .page -->

	<?php

	/**
	 * Fires after the display of the member activation page.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_activation_page' ); ?>

</div><!-- #buddypress -->

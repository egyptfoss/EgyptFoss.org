<?php
/**
 * BuddyPress - Members Single Profile
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_before_member_settings_template' ); ?>
<header class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php _e("Account","egyptfoss"); ?></h2>
	</div>
</header>
<form action="<?php echo bp_displayed_user_domain() . bp_get_settings_slug() . '/general'; ?>" method="post" class="standard-form" id="settings-form">
<div class="row">
	<div class="col-md-12">
		<ul class="nav sub-tabs">
			<li class="active"><a id="change-pass" class="chng-pass"><?php _e( 'Change Your Password', 'egyptfoss' ); ?></a></li>
			<li><a id="change-email" class="chng-email"><?php _e( 'Change Your Email', 'egyptfoss' ); ?></a></li>
		</ul>
	</div>
</div>
<div class="panel panel-default change-panel">
<div class="panel-content">
	<?php
  global $wpdb;
  if (canDeleteLastSocialMedia(get_current_user_id(),$wpdb) ) : ?>
		<div class="current-password">
			<div class="row">
				<div class="col-md-12">
					<?php //if ( !is_super_admin() ) : ?>
						<label class="label" for="pwd"><?php _e( 'Current Password', 'buddypress' ); ?></label>
						<input type="password" name="pwd" id="pwd" size="16" value="" class="settings-input small form-control" autocomplete="off" <?php bp_form_field_attributes( 'password' ); ?>/>
					<?php //endif; ?>
				</div>
			</div>
		</div>
	<?php else: ?>
		<input type="hidden" name="pwd" id="pwd" size="16" value="social-login" />
	<?php endif; ?>

<div class="change-email hidden">
	<div class="row">
		<div class="col-md-12">
		<label class="label" for="email"><?php _e( 'Account Email', 'buddypress' ); ?></label>
		<input class="form-control" type="email" name="email" id="email" value="<?php echo bp_get_displayed_user_email(); ?>" class="settings-input" <?php bp_form_field_attributes( 'email' ); ?>/>
		<input class="hidden" id="saved_email" value="<?php echo bp_get_displayed_user_email(); ?>" />
		</div>
	</div>
</div>
<div class="change-password">
	<div class="row">
		<div class="col-md-6">
			<label for="pass1" class="label">
				<?php _e( 'New Password', 'buddypress' ); ?>
			</label>
				<input type="password" name="pass1" id="pass1" size="16" value="" class="form-control settings-input small password-entry" autocomplete="off" <?php bp_form_field_attributes( 'password' ); ?>/>
					<div id="pass-strength-result"></div>
		</div>
		<div class="col-md-6">
				<label for="pass2" class="bp-screen-reader-text"><?php _e( 'Confirm New Password', 'buddypress' ); ?></label>
				<label for="pass2" class="label">&nbsp;<?php _e( 'Confirm New Password', 'buddypress' ); ?>
				</label>
				<input type="password" name="pass2" id="pass2" size="16" value="" class="settings-input small password-entry-confirm form-control" autocomplete="off" <?php bp_form_field_attributes( 'password' ); ?>/>
		</div>
	</div>
</div>



	<?php

	/**
	 * Fires before the display of the submit button for user general settings saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_core_general_settings_before_submit' ); ?>
<div class="submit-data">
		<div class="submit clearfix">
		<input type="submit" name="submit" value="<?php esc_attr_e( 'Save', 'egyptfoss' ); ?>" id="submit" class="auto btn btn-primary rfloat" />
	</div>
</div>
</div>
	</div>

	<?php

	/**
	 * Fires after the display of the submit button for user general settings saving.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_core_general_settings_after_submit' ); ?>

	<?php wp_nonce_field( 'bp_settings_general' ); ?>

</form>

<?php

/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/settings/profile.php */
do_action( 'bp_after_member_settings_template' ); ?>

<?php
/**
 * BuddyPress - Members Register
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
include( ABSPATH . 'system_data.php' );
$lang = get_locale();
if($lang == 'ar') {
  global $ar_sub_types;
  $sub_types = $ar_sub_types;
} else {
  global $en_sub_types;
  $sub_types = $en_sub_types;
}
?>

<div id="registration-form" class="row">
	<div class="col-md-12">
	<div class="page" id="register-page">
	<div class="row">
		<div class="join-us text-center">
			<h2><?php _e( "Join Our Platform Today, It's FREE.", 'egyptfoss' ); ?></h2>
		</div>
	</div>
	<div class="row">
	<div class="col-md-8 signup-form-wrapper">
	    		<form action="" name="signup_form" id="signup_form" class="standard-form form" method="post" enctype="multipart/form-data">
      <?php
        $account_type = (isset($_POST['type']))?$_POST['type']:"";
        $sub_type = (isset($_POST['sub_type']))?$_POST['sub_type']:"";
      ?>
      <?php if ( 'registration-disabled' == bp_get_current_signup_step() ) : ?>
	      <?php do_action( 'template_notices' ); ?>
				<?php do_action( 'bp_before_registration_disabled' ); ?>
				<p><?php _e( 'User registration is currently not allowed.', 'egyptfoss' ); ?></p>
				<?php do_action( 'bp_after_registration_disabled' ); ?>
			<?php endif; // registration-disabled signup step ?>
			<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>
				<?php do_action( 'template_notices' ); ?>
				<?php do_action( 'bp_before_account_details_fields' ); ?>

				<div class="register-section" id="basic-details-section">
					<?php /***** Basic Account Details ******/ ?>
					<div class="form-group row">
						<div class="col-md-12" id="account_types">
							<label for="type" class="label"><?php _e( 'Account Type', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
						  <input type="radio" name="type" value="Individual" <?php echo (($account_type=='Individual' || $account_type=='')?'checked':''); ?>> <?php _e( 'Individual', 'egyptfoss' ); ?>
						  <input type="radio" name="type" value="Entity" <?php echo (($account_type=='Entity')?'checked':''); ?>> <?php _e( 'Entity', 'egyptfoss' ); ?><br>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-12">
							<label for="signup_username" class="label"><?php _e( 'Username', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
							<?php do_action( 'bp_signup_username_errors' ); ?>
							<input type="text" name="signup_username" id="signup_username" placeholder="<?php _e( 'Please enter Username (Username at least 4 characters)', 'egyptfoss'); ?>" class="form-control" value="<?php bp_signup_username_value(); ?>" <?php bp_form_field_attributes( 'username' ); ?> pattern="[أ-يa-zA-Z0-9._-]{1,}" />
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-12">
							<label class="label" for="signup_email"><?php _e( 'Email Address', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
							<?php do_action( 'bp_signup_email_errors' ); ?>
							<input type="email" class="form-control" name="signup_email" placeholder="<?php _e( 'Enter a valid email address', 'egyptfoss'); ?>" id="signup_email" value="<?php bp_signup_email_value(); ?>" <?php bp_form_field_attributes( 'email' ); ?> pattern="[أ-يa-z0-9._%+-]+@[أ-يa-z0-9.-]+\.[أ-يa-z]{2,63}$" />
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-6">
							<label class="label" for="signup_password"><?php _e( 'Choose a Password', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
							<input type="password" name="signup_password" placeholder="<?php _e( 'Please enter Password at least 8 characters', 'egyptfoss'); ?>" id="signup_password" value="" class="password-entry form-control" autocomplete="off" <?php bp_form_field_attributes( 'password' ); ?>/>
							<?php do_action( 'bp_signup_password_errors' ); ?>
							<div id="pass-strength-result"></div>
						</div>
						<div class="col-md-6">
							<label for="signup_password_confirm" class="label"><?php _e( 'Confirm Password ', 'egyptfoss' ); ?><?php _e( '(required)', 'egyptfoss' ); ?></label>
							<input type="password" name="signup_password_confirm" placeholder="<?php _e( 'Please confirm Password', 'egyptfoss'); ?>" id="signup_password_confirm" value="" class="password-entry-confirm form-control" autocomplete="off" <?php bp_form_field_attributes( 'password' ); ?>/>
							<?php do_action( 'bp_signup_password_confirm_errors' ); ?>
						</div>
					</div>

					<div class="form-group row">
						<div class="col-md-12">
							<label for="account_subtype" class="label"><?php _e( 'Account sub type', 'egyptfoss'); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
							<select name="sub_type" id="sub_type" class="registration_sub_type form-control">

								<option class="Individual Entity Event" value=""><?php _e( 'Select', 'egyptfoss' ); ?></option>
								<?php
		              $account_sub_type_labels = array();
		              foreach ($account_sub_types as $sub => $t) {
		                $account_sub_type_labels = array_merge($account_sub_type_labels,array($sub=>$sub_types[$sub]));
		              }
		              asort($account_sub_type_labels);
									foreach ($account_sub_type_labels as $sub => $label) {
		                if($sub_type == $sub)
		                    echo("<option class='".$account_sub_types[$sub]."' value='".$sub."' selected=\"selected\" >");
		                else
		                    echo("<option class='".$account_sub_types[$sub]."' value='".$sub."' >");
										echo($label);
										echo("</option>");
									}
								?>
							</select>
						</div>
					</div>

          <div class="form-group row" id="telephone_number_container" <?php echo $account_type=='Entity' ? '': 'style="display: none;"' ?>>
						<div class="col-md-12">
							<label class="label" for="signup_telephone_number"><?php _e( 'Telephone Number', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
							<?php do_action( 'bp_signup_telephone_number_errors' ); ?>
							<input type="text" class="form-control" name="signup_telephone_number" placeholder="<?php _e( 'Enter a valid telephone number', 'egyptfoss'); ?>" id="signup_telephone_number" value="<?php bp_signup_telephone_number_value(); ?>" <?php bp_form_field_attributes( 'telephone_number' ); ?> pattern="^(?=.*[0-9])[-+/0-9]+$" />
						</div>
					</div>

					<?php do_action( 'bp_account_details_fields' ); ?>
				</div><!-- #basic-details-section -->

				<?php do_action( 'bp_after_account_details_fields' ); ?>
				<?php /***** Extra Profile Details ******/ ?>

				<?php $enable_extended_profile_for_registration = 0; ?>
				<?php if ( $enable_extended_profile_for_registration && bp_is_active( 'xprofile' ) ) : ?>
				<?php do_action( 'bp_before_signup_profile_fields' ); ?>

				<div class="register-section" id="profile-details-section" style="display:none;">
					<h4><?php _e( 'Profile Details', 'egyptfoss' ); ?></h4>
					<?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
					<?php if ( bp_is_active( 'xprofile' ) ) : if ( bp_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => false ) ) ) : while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

						<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

							<div<?php bp_field_css_class( 'editfield' ); ?>>
								<?php
								$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
								$field_type->edit_field_html();
								do_action( 'bp_custom_profile_edit_fields_pre_visibility' );

								if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
								<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
									<?php
									printf(
									__( 'This field can be seen by: %s', 'egyptfoss' ),
									'<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
									);
									?>
									<a href="#" class="visibility-toggle-link"><?php _ex( 'Change', 'Change profile field visibility level', 'egyptfoss' ); ?></a>
								</p>

								<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
									<fieldset>
										<legend><?php _e( 'Who can see this field?', 'egyptfoss' ) ?></legend>
										<?php bp_profile_visibility_radio_buttons() ?>
									</fieldset>
									<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'egyptfoss' ) ?></a>
								</div>
								<?php else : ?>
								<p class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
									<?php
									printf(
									__( 'This field can be seen by: %s', 'egyptfoss' ),
									'<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
									);
									?>
								</p>
								<?php endif ?>

								<?php do_action( 'bp_custom_profile_edit_fields' ); ?>
								<p class="description"><?php bp_the_profile_field_description(); ?></p>
							</div>
						<?php endwhile; ?>
						<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

					<?php endwhile; endif; endif; ?>
					<?php do_action( 'bp_signup_profile_fields' ); ?>
				</div><!-- #profile-details-section -->

				<div class="form-group row">
					<div class="col-md-12">
						<h2><?php _e( 'Contact Information', 'egyptfoss' ); ?></h2>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-6">
					<label for="" class="label"><?php _e( 'Entity Email', 'egyptfoss' ); ?></label>
					<input type="text" class="form-control" placeholder="<?php _e( 'Entity Email', 'egyptfoss'); ?>">
					</div>
					<div class="col-md-6">
						<label for="" class="label"><?php _e( 'Phone', 'egyptfoss' ); ?></label>
					<input type="tel" class="form-control" placeholder="<?php _e( 'Entity Phone Number', 'egyptfoss'); ?>">
					</div>
				</div>
				<?php do_action( 'bp_after_signup_profile_fields' ); ?>

				<?php endif; ?>
				<?php if ( bp_get_blog_signup_allowed() ) : ?>
				<?php do_action( 'bp_before_blog_details_fields' ); ?>

				<?php /***** Blog Creation Details ******/ ?>
					<div class="register-section" id="blog-details-section">
						<h4><?php _e( 'Blog Details', 'egyptfoss' ); ?></h4>
						<p><label for="signup_with_blog"><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes, I\'d like to create a new site', 'egyptfoss' ); ?></label></p>
						<div id="blog-details"<?php if ( (int) bp_get_signup_with_blog_value() ) : ?>class="show"<?php endif; ?>>
							<label for="signup_blog_url"><?php _e( 'Blog URL', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
							<?php do_action( 'bp_signup_blog_url_errors' ); ?>

							<?php if ( is_subdomain_install() ) : ?>
								http:// <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" /> .<?php bp_signup_subdomain_base(); ?>
							<?php else : ?>
								<?php echo home_url( '/' ); ?> <input type="text" name="signup_blog_url" id="signup_blog_url" value="<?php bp_signup_blog_url_value(); ?>" />
							<?php endif; ?>

							<label for="signup_blog_title"><?php _e( 'Site Title', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
							<?php do_action( 'bp_signup_blog_title_errors' ); ?>
							<input type="text" name="signup_blog_title" id="signup_blog_title" value="<?php bp_signup_blog_title_value(); ?>" />

							<span class="label"><?php _e( 'I would like my site to appear in search engines, and in public listings around this network.', 'egyptfoss' ); ?></span>
							<?php do_action( 'bp_signup_blog_privacy_errors' ); ?>

							<label for="signup_blog_privacy_public"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_public" value="public"<?php if ( 'public' == bp_get_signup_blog_privacy_value() || !bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'Yes', 'egyptfoss' ); ?></label>
							<label for="signup_blog_privacy_private"><input type="radio" name="signup_blog_privacy" id="signup_blog_privacy_private" value="private"<?php if ( 'private' == bp_get_signup_blog_privacy_value() ) : ?> checked="checked"<?php endif; ?> /> <?php _e( 'No', 'egyptfoss' ); ?></label>

							<?php do_action( 'bp_blog_details_fields' ); ?>
						</div>
					</div><!-- #blog-details-section -->

					<?php do_action( 'bp_after_blog_details_fields' ); ?>
				<?php endif; ?>

				<?php do_action( 'bp_before_registration_submit_buttons' ); ?>
				<div class="form-group row">
					<div class="col-md-12">
						<label class="col-md-12 checkbox-inline form-group">
	              <input type="checkbox" id="terms" name="terms" value="checked"> <?php _e( 'I agree to', 'egyptfoss'); echo ' '; ?><a href="<?php echo home_url(pll_current_language()."/terms-of-services") ?>" target="_blank()"><?php _e( 'EgyptFOSS Terms of services', 'egyptfoss'); ?></a>
						</label>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">
						<input type="submit" name="signup_submit" id="signup_submit" class="btn btn-primary rfloat" value="<?php esc_attr_e( 'Complete Sign Up', 'egyptfoss' ); ?>" />
					</div>
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ); ?>
				<?php wp_nonce_field( 'bp_new_signup' ); ?>
			<?php endif; // request-details signup step ?>
			<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>
				<?php do_action( 'template_notices' ); ?>
				<?php do_action( 'bp_before_registration_confirmed' ); ?>
				<?php if ( bp_registration_needs_activation() ) : ?>
					<div class="alert alert-success"><i class="fa fa-check"></i> <?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'egyptfoss' ); ?></div>
				<?php else : ?>
					<div class="alert alert-success"><i class="fa fa-check"></i> <?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'egyptfoss' ); ?></div>
				<?php endif; ?>
				<?php do_action( 'bp_after_registration_confirmed' ); ?>
			<?php endif; // completed-confirmation signup step ?>

			<?php do_action( 'bp_custom_signup_steps' ); ?>
        <span>
          <?php _e( 'Have an account ?', 'egyptfoss' ); ?>
            <a href="<?php echo wp_login_url(); ?>"><?php _e( 'Login', 'egyptfoss' ); ?></a>
        </span>
		</form>
	</div>
	<div class="col-md-4">
	    <?php do_action( 'wordpress_social_login' ); ?>
	</div>
	</div>
	</div>
	</div>
	<?php do_action( 'bp_before_register_page' ); ?>
	<?php do_action( 'bp_after_register_page' ); ?>
</div><!-- #buddypress -->

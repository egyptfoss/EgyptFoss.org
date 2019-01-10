<?php
/**
 * BuddyPress - Members Single Profile Edit
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires after the display of member profile edit content.
 *
 * @since 1.1.0
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

do_action( 'bp_before_profile_edit_content' );
$xprofile_id = bp_displayed_user_id();
$user_data = get_registration_data($xprofile_id);
$user_data['display_name'] = get_user_by("ID", get_current_user_id())->display_name;
if ( bp_has_profile( 'profile_group_id=' . bp_get_current_profile_group_id() ) ) :
	while ( bp_profile_groups() ) : bp_the_profile_group(); ?>

<form action="<?php bp_the_profile_group_edit_form_action(); ?>" method="post" id="profile-edit-form" class="standard-form <?php bp_the_profile_group_slug(); ?>">

	<?php
		/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
		do_action( 'bp_before_profile_field_content' ); ?>
		<?php if ( bp_profile_has_multiple_groups() ) : ?>
			<ul class="button-nav">
				<?php bp_profile_group_tabs(); ?>
			</ul>
		<?php endif ;?>
		<div class="clear"></div>

		<?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>

			<div<?php bp_field_css_class( 'sr-only' ); ?>>

				<?php
				$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
				$field_type->edit_field_html();

				/**
				 * Fires before the display of visibility options for the field.
				 *
				 * @since 1.7.0
				 */
				do_action( 'bp_custom_profile_edit_fields_pre_visibility' );
				?>

				<?php if ( bp_current_user_can( 'bp_xprofile_change_field_visibility' ) ) : ?>
					<p class="field-visibility-settings-toggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
						<?php
						printf(
							__( 'This field can be seen by: %s', 'buddypress' ),
							'<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
						);
						?>
						<a href="#" class="visibility-toggle-link"><?php _e( 'Change', 'buddypress' ); ?></a>
					</p>

					<div class="field-visibility-settings" id="field-visibility-settings-<?php bp_the_profile_field_id() ?>">
						<fieldset>
							<legend><?php _e( 'Who can see this field?', 'buddypress' ) ?></legend>

							<?php bp_profile_visibility_radio_buttons() ?>

						</fieldset>
						<a class="field-visibility-settings-close" href="#"><?php _e( 'Close', 'buddypress' ) ?></a>
					</div>
				<?php else : ?>
					<div class="field-visibility-settings-notoggle" id="field-visibility-settings-toggle-<?php bp_the_profile_field_id() ?>">
						<?php
						printf(
							__( 'This field can be seen by: %s', 'buddypress' ),
							'<span class="current-visibility-level">' . bp_get_the_profile_field_visibility_level_label() . '</span>'
						);
						?>
					</div>
				<?php endif ?>

				<?php

				/**
				 * Fires after the visibility options for a field.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_custom_profile_edit_fields' ); ?>

				<p class="description"><?php bp_the_profile_field_description(); ?></p>
			</div>

		<?php endwhile; ?>

	<?php

	/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
	do_action( 'bp_after_profile_field_content' ); ?>
	<div class="row form-group">
		<div class="col-md-12">
			<labe class="label"><?php _e( 'Name', 'egyptfoss' ); ?></labe>
      <input type="text" name="display_name" rows="3" id="display_name" class="form-control" placeholder="<?php echo sprintf(__("Add %s here","egyptfoss"),__("Display name",'egyptfoss')); ?>" value="<?php echo (isset($user_data['display_name']) ? $user_data['display_name'] : ''); ?>">
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-12">
			<label for="account_subtype" class="label"><?php _e('Sub Type','egyptfoss');?></label>
			<?php $sub_type = ((isset($user_data['sub_type']) && !empty($user_data['sub_type'])) ? $user_data['sub_type'] : ''); ?>
			<select name="sub_type" id="sub_type" class="form-control">
				<option value=""><?php _e('Select','egyptfoss');?></option>
				<?php
					foreach ($account_sub_types as $sub => $t) {
						if(!isset($user_data['type']) || $t == $user_data['type']) {
							$selected = ($sub_type == $sub) ? 'selected' : '';
							echo("<option value='".$sub."' $selected >");
							echo($sub_types[$sub]);
							echo ("</option>");
						}
					}
				?>
			</select>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-12">
			<labe class="label"><?php echo _x( 'Description','register', 'egyptfoss' ); ?></labe>
      <textarea style="width:100%" type="text" name="functionality" rows="3" id="functionality" class="form-control" placeholder="<?php echo sprintf(__( 'Type %s here', 'egyptfoss'),_x( 'Description','register' ,'egyptfoss')); ?>"><?php echo (isset($user_data['functionality']) ? html_entity_decode( $user_data['functionality'] ): ''); ?></textarea>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-12">
		<label for="" class="label"><?php _e( 'Theme', 'egyptfoss' ); ?></label>
			<?php $theme = (isset($user_data['theme']) ? $user_data['theme'] : ''); ?>
			<select name="theme" id="theme" class="form-control">
				<option value=""><?php _e( 'Select', 'egyptfoss' ); ?></option>
				<?php
					$themes = get_terms( 'theme', array( 'hide_empty' => 0 ) );
					foreach ($themes as $i) {
						$selected = ($theme == $i->term_id) ? 'selected' : '';
						echo("<option value='".$i->term_id."' $selected >");
						_e("$i->name", "egyptfoss");
						echo ("</option>");
					}
				?>
			</select>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-md-12">
			<label for="" class="label"><?php _e( 'ICT Technologies', 'egyptfoss' ); ?></label>
			<?php $user_technologies = get_user_taxonomies($xprofile_id, 'technology'); ?>
      <select data-tags="true" name="ict_technology[]" id="ict_technology" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" class="technologies form-control L-validate_taxonomy" style="width:100%; visibility: hidden;" multiple="multiple">
				<optgroup>
					<?php
						$ict_technologies = get_terms( 'technology', array( 'hide_empty' => 0 ) );
						foreach ($ict_technologies as $t) {
							$selected = (in_array($t->name, $user_technologies)) ? 'selected' : '';
							echo("<option value='".$t->name."' $selected >");
							_e("$t->name", "egyptfoss");
							echo ("</option>");
						}
					?>
				</optgroup>
			</select>
			<span id="technology-error" class="error" style="display:none;"><?php _e('Invalid ict_technology.','egyptfoss') ?></span>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-12">
		<labe class="label"><?php _e( 'Address', 'egyptfoss' ); ?></labe>
    <input type="text" name="address" id="address" class="form-control" placeholder="<?php _e("Add Your address here","egyptfoss"); ?>" value="<?php echo (isset($user_data['address']) ? $user_data['address'] : ''); ?>">
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-12">
		<labe class="label"><?php _e( 'Phone', 'egyptfoss' ); ?></labe>
			<input type="text" name="phone" id="phone" class="form-control" placeholder="<?php _e("Add Your phone here","egyptfoss"); ?>" value="<?php echo (isset($user_data['phone']) ? $user_data['phone'] : ''); ?>">
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<h4><?php _e( 'Social Accounts', 'egyptfoss' ); ?></h4>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-12">
			<div class="input-group">
				<div class="input-group-addon"><i class="fa fa-facebook-square"></i></div>
				<input type="text" name="facebook_url" id="facebook_url" class="form-control"  placeholder="https://" value="<?php echo (isset($user_data['facebook_url']) ? $user_data['facebook_url'] : ''); ?>">
			</div>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-12">
			<div class="input-group">
				<div class="input-group-addon"><i class="fa fa-twitter"></i></div>
				<input type="text" name="twitter_url" id="twitter_url" class="form-control"  placeholder="https://" value="<?php echo (isset($user_data['twitter_url']) ? $user_data['twitter_url'] : ''); ?>">
			</div>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-12">
			<div class="input-group">
				<div class="input-group-addon"><i class="fa fa-linkedin-square"></i></div>
				<input type="text" name="linkedin_url" id="linkedin_url" class="form-control"  placeholder="https://" value="<?php echo (isset($user_data['linkedin_url']) ? $user_data['linkedin_url'] : ''); ?>">
			</div>
		</div>
	</div>
	<div class="row form-group">
		<div class="col-md-12">
			<div class="input-group">
				<div class="input-group-addon"><i class="fa fa-google-plus-square"></i></div>
				<input type="text" name="gplus_url" id="gplus_url" class="form-control"  placeholder="https://" value="<?php echo (isset($user_data['gplus_url']) ? $user_data['gplus_url'] : ''); ?>">
			</div>
		</div>
	</div>
	<div class="form-group row">
		<div class="col-md-12">
		<label for="" class="label"><?php _e( 'Interests', 'egyptfoss' ); ?></label>
			<?php $user_interests = get_user_taxonomies($xprofile_id, 'interest'); ?>
			<div style="position:relative;">
          <select data-tags="true" name="interest[]" id="interest" data-placeholder="<?php _e( 'Select', 'egyptfoss' ); ?>" class="technologies form-control L-validate_taxonomy" style="width:100%;visibility: hidden;" multiple="multiple">
				<optgroup>
					<?php
						$interests = get_terms( 'interest', array( 'hide_empty' => 0 ) );
						foreach ($interests as $s) {
							$selected = (in_array($s->name, $user_interests)) ? 'selected' : '';
							echo("<option value='".$s->name."' $selected >");
							_e("$s->name", "egyptfoss");
							echo ("</option>");
						}
					?>
				</optgroup>
			</select>
			<span id="interest-error" class="error" style="display:none;"><?php _e('Invalid interest.','egyptfoss') ?></span>
			</div>
		</div>
	</div>
	<?php if (isset($user_data['type']) && ($user_data['type'] == 'Entity')) { ?>
		<div class="row">
			<div class="col-md-12">
				<h4><?php _e( 'Entity Contact Person Data', 'egyptfoss' ); ?></h4>
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-12">
			<labe class="label"><?php _e( 'Contact Name', 'egyptfoss' ); ?></labe>
      <input type="text" name="contact_name" id="contact_name" class="form-control" placeholder="<?php _e("Add Your contact person name here","egyptfoss"); ?>" value="<?php echo (isset($user_data['contact_name']) ? $user_data['contact_name'] : ''); ?>">
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-12">
			<labe class="label"><?php _e( 'Contact Email', 'egyptfoss' ); ?></labe>
      <input type="text" name="contact_email" id="contact_email" class="form-control" placeholder="<?php _e("Add Your contact person email here","egyptfoss"); ?>" value="<?php echo (isset($user_data['contact_email']) ? $user_data['contact_email'] : ''); ?>">
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-12">
			<labe class="label"><?php _e( 'Contact Address', 'egyptfoss' ); ?></labe>
				<input type="text" name="contact_address" id="contact_address" class="form-control" placeholder="<?php _e("Add Your contact person address here","egyptfoss"); ?>" value="<?php echo (isset($user_data['contact_address']) ? $user_data['contact_address'] : ''); ?>">
			</div>
		</div>
		<div class="row form-group">
			<div class="col-md-12">
			<labe class="label"><?php _e( 'Contact Phone', 'egyptfoss' ); ?></labe>
				<input type="text" name="contact_phone" id="contact_phone" class="form-control" placeholder="<?php _e("Add Your contact person phone here","egyptfoss"); ?>" value="<?php echo (isset($user_data['contact_phone']) ? $user_data['contact_phone'] : ''); ?>">
			</div>
		</div>
	<?php } ?>
	<div class="row">
		<div class="col-md-12">
			<div class="submit rfloat">
				<input type="submit" name="profile-group-edit-submit" class="btn btn-primary" id="profile-group-edit-submit" value="<?php esc_attr_e( 'Save', 'egyptfoss' ); ?> " />
				<a href="<?php echo bp_loggedin_user_domain().'about'; ?>"><?php esc_attr_e( 'Cancel', 'egyptfoss' ); ?></a>
			</div>
		</div>
	</div>

	<input type="hidden" name="field_ids" id="field_ids" class="form-control" value="<?php bp_the_profile_field_ids(); ?>" />
	<?php wp_nonce_field( 'bp_xprofile_edit' ); ?>
</form>

<?php endwhile; endif; ?>

<?php

/**
 * Fires after the display of member profile edit content.
 *
 * @since 1.1.0
 */
do_action( 'bp_after_profile_edit_content' ); ?>

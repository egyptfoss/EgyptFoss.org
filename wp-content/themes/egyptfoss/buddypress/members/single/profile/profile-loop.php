<?php
/**
 * BuddyPress - Members Profile Loop
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
do_action( 'bp_before_profile_loop_content' ); ?>
<div class="list-unstyled" id="subnav" role="navigation">
	<ul class="my-profile-options rfloat">
		<?php //bp_get_options_nav(); ?>
			<?php if (  bp_is_my_profile() ):?>
		<li>	<a href="<?php bp_loggedinuser_link() ?>about/edit" class="btn btn-light"><i class="fa fa-pencil"></i> <?php _e('Edit','egyptfoss') ?></a></li>
		<?php endif;?>
		<li class="share-profile"><a class="btn btn-light"><i class="fa fa-share"></i> <?php _e('Share','egyptfoss') ?>
			<div class="share-box">
			<?php
echo do_shortcode('[Sassy_Social_Share]');
$xprofile_id = bp_displayed_user_id();
$user_data = get_registration_data($xprofile_id);
?>
		</div>
		</a>
		</li>
	</ul>
</div><!-- .item-list-tabs -->
<?php if ( bp_has_profile() ) : ?>
    <?php while ( bp_profile_groups() ) : bp_the_profile_group(); ?>
        <?php if ( bp_profile_group_has_fields() ) : ?>
            <?php
            /** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
            do_action( 'bp_before_profile_field_content' ); ?>

            <div class="bp-widget <?php bp_the_profile_group_slug(); ?>">
                <dl class="profile-fields">
                        <?php if ( bp_is_my_profile() ) { // -- hide address and phone if user show other profile -- // ?>
                               <dt class="data-view-section">
                               	<span class="section-name"><?php _e( 'Contact Info', 'egyptfoss' ); ?></span>
                               	</dt>
                                <?php
                                $user_data['display_name'] = get_user_by("ID", bp_displayed_user_id())->display_name;
                                if (!empty($user_data['display_name'])) { ?>
                                    <dt class="profile-label"><?php _e( 'Name', 'egyptfoss' ); ?></dt>
                                    <dd><?php echo (isset($user_data['display_name']) ? $user_data['display_name'] : ''); ?></dd>
                                <?php }
                                else{
                                    if ( bp_is_my_profile() ) { ?>
                                    <dt class="profile-label"><?php _e( 'Display name', 'egyptfoss' ); ?></dt>
                                    <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                    <?php } ?>
                                <?php }?>

                                <?php if (!empty($user_data['address'])) { ?>
                                    <dt class="profile-label"><?php _e( 'Address', 'egyptfoss' ); ?></dt>
                                    <dd><?php echo (isset($user_data['address']) ? $user_data['address'] : ''); ?></dd>
                                <?php }
                                else{
                                    if ( bp_is_my_profile() ) { ?>
                                    <dt class="profile-label"><?php _e( 'Address', 'egyptfoss' ); ?></dt>
                                    <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                    <?php } ?>
                                <?php }?>

                                <?php if (!empty($user_data['phone'])) { ?>
                                    <dt class="profile-label"><?php _e( 'Phone', 'egyptfoss' ); ?></dt>
                                    <dd><?php echo (isset($user_data['phone']) ? $user_data['phone'] : ''); ?></dd>
                                <?php }
                                else{
                                    if ( bp_is_my_profile() ) { ?>
                                    <dt class="profile-label"><?php _e( 'Phone', 'egyptfoss' ); ?></dt>
                                    <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                    <?php } ?>
                                <?php }?>
                            <?php } ?>

                    <dt class="data-view-section">
                        <span class="section-name"><?php _e( 'Main Info.', 'egyptfoss' ); ?></span>
                    </dt>
                    <?php while ( bp_profile_fields() ) : bp_the_profile_field(); ?>
                        <?php if ( bp_field_has_data() ) : ?>
                        <?php $user_technologies = get_user_taxonomies($xprofile_id, 'technology'); ?>
                        <?php $user_interests = get_user_taxonomies($xprofile_id, 'interest'); ?>

                            <?php if ( !bp_is_my_profile() && (empty($user_data['functionality'])) && (empty($user_data['theme'])) && (empty($user_data['$user_technologies'])) && empty($user_data['$user_interests']) ){ ?>
                                <dt class="empty-row"><?php _e('This info is not shared with you', 'egyptfoss'); ?></dt>
                            <?php } ?>

                            <?php if (!empty($user_data['functionality'])) { ?>
                                <dt class="profile-label"><?php echo _x( 'Description','register', 'egyptfoss' ); ?></dt>
                                <dd><?php echo (isset($user_data['functionality']) ? html_entity_decode( nl2br( $user_data['functionality'] ) ): ''); ?></dd>
                            <?php }
                            else{
                                if ( bp_is_my_profile() ) { ?>
                                <dt class="profile-label"><?php echo _x( 'Description','register', 'egyptfoss' ); ?></dt>
                                <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                <?php } ?>
                            <?php }?>

                            <?php if (!empty($user_data['theme'])) { ?>
                                <dt class="profile-label"><?php _e( 'Theme', 'egyptfoss' ); ?></dt>
                                <dd><?php
                                    $theme_id = (isset($user_data['theme']) ? $user_data['theme'] : '');
                                    $theme_row = get_term($theme_id, 'theme');
                                    if($theme_row)
                                    {
                                      if(pll_current_language() == "en")
                                      {
                                        echo $theme_row->name;
                                      }else
                                      {
                                        echo $theme_row->name_ar;
                                      }
                                    }
                                ?></dd>
                            <?php }
                            else{
                                if ( bp_is_my_profile() ) { ?>
                                <dt class="profile-label"><?php _e( 'Theme', 'egyptfoss' ); ?></dt>
                                <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                <?php } ?>
                            <?php }?>

                            <?php if (!empty($user_technologies)) { ?>
                                <dt class="profile-label"><?php echo __('Technologies' , 'egyptfoss' ); ?></dt>
                                <dd>
                                    <?php foreach ($user_technologies as $technology) {?>
                                    <span class="technology-tag">
                                        <?php _e("$technology", "egyptfoss");?>
                                    </span>
                                    <?php }?>
                                </dd>
                            <?php }
                            else{
                                if ( bp_is_my_profile() ) { ?>
                                <dt class="profile-label"><?php echo __('Technologies' , 'egyptfoss' ); ?></dt>
                                <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                <?php } ?>
                            <?php }?>

                            <?php if (!empty($user_interests)) { ?>
                                <dt class="profile-label"><?php _e( 'Interests', 'egyptfoss' ); ?></dt>
                                <dd>
                                    <?php
                                    foreach ($user_interests as $interest) {?>
                                        <span class="interest-tag">
                                          <?php  _e("$interest", "egyptfoss"); ?>
                                        </span>
                                     <?php }?>
                                </dd>
                            <?php }
                            else{
                                if ( bp_is_my_profile() ) { ?>
                                <dt class="profile-label"><?php _e( 'Interests', 'egyptfoss' ); ?></dt>
                                <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                <?php } ?>
                            <?php }?>

                            <dt class="data-view-section">
                                <span class="section-name"><?php _e( 'Links', 'egyptfoss' ); ?></span>
                            </dt>
                            <?php if ( !bp_is_my_profile() && (empty($user_data['facebook_url'])) && (empty($user_data['twitter_url'])) && (empty($user_data['linkedin_url'])) && empty($user_data['gplus_url']) ){ ?>
                                <dt class="empty-row"><?php _e('This info is not shared with you', 'egyptfoss'); ?></dt>
                            <?php } ?>

                            <?php if (!empty($user_data['facebook_url'])) { ?>
                                <dt class="profile-label"><?php _e( 'Facebook', 'egyptfoss' ); ?></dt>
                                <dd><a href="<?php echo (isset($user_data['facebook_url']) ? $user_data['facebook_url'] : ''); ?>" target="_blank" rel="nofollow"><?php echo (isset($user_data['facebook_url']) ? $user_data['facebook_url'] : ''); ?></a></dd>
                            <?php }
                            else{
                                if ( bp_is_my_profile() ) { ?>
                                <dt class="profile-label"><?php _e( 'Facebook', 'egyptfoss' ); ?></dt>
                                <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                <?php } ?>
                            <?php }?>

                            <?php if (!empty($user_data['twitter_url'])) { ?>
                                <dt class="profile-label"><?php _e( 'Twitter', 'egyptfoss' ); ?></dt>
                                <dd><a href="<?php echo (isset($user_data['twitter_url']) ? $user_data['twitter_url'] : ''); ?>" target="_blank" rel="nofollow"><?php echo (isset($user_data['twitter_url']) ? $user_data['twitter_url'] : ''); ?></a></dd>
                            <?php }
                            else{
                                if ( bp_is_my_profile() ) { ?>
                                <dt class="profile-label"><?php _e( 'Twitter', 'egyptfoss' ); ?></dt>
                                <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                <?php } ?>
                            <?php }?>

                            <?php if (!empty($user_data['linkedin_url'])) { ?>
                                <dt class="profile-label"><?php echo ucfirst((__( 'LinkedIn', 'egyptfoss' ))); ?></dt>
                                <dd><a href="<?php echo (isset($user_data['linkedin_url']) ? $user_data['linkedin_url'] : ''); ?>" target="_blank" rel="nofollow"><?php echo (isset($user_data['linkedin_url']) ? $user_data['linkedin_url'] : ''); ?></a></dd>
                            <?php }
                            else{
                                if ( bp_is_my_profile() ) { ?>
                                <dt class="profile-label"><?php echo ucfirst((__( 'LinkedIn', 'egyptfoss' ))); ?></dt>
                                <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                <?php } ?>
                            <?php }?>

                            <?php if (!empty($user_data['gplus_url'])) { ?>
                                <dt class="profile-label"><?php echo __( 'Google+', 'egyptfoss' ); ?></dt>
                                <dd><a href="<?php echo (isset($user_data['gplus_url']) ? $user_data['gplus_url'] : ''); ?>" target="_blank" rel="nofollow"><?php echo (isset($user_data['gplus_url']) ? $user_data['gplus_url'] : ''); ?></a></dd>
                            <?php }
                            else{
                                if ( bp_is_my_profile() ) { ?>
                                <dt class="profile-label"><?php echo __( 'Google+', 'egyptfoss' ); ?></dt>
                                <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                <?php } ?>
                            <?php }?>

                            <?php
                            // --------- If user is Entity --------- //
                            if (isset($user_data['type']) && ($user_data['type'] == 'Entity')) { ?>
                                <dt class="data-view-section">
                                    <span class="section-name"><?php _e( 'Entity Contact Info.', 'egyptfoss' ); ?></span>
                                </dt>
                                <?php if ( !bp_is_my_profile() && (empty($user_data['contact_name'])) && (empty($user_data['contact_email'])) && (empty($user_data['contact_address'])) && empty($user_data['contact_phone']) ){ ?>
                                <dt class="empty-row"><?php _e("This info is not shared with you", 'egyptfoss'); ?></dt>
                                <?php } ?>
                                <?php if (!empty($user_data['contact_name'])) { ?>
                                    <dt class="profile-label"><?php _e( 'Contact Name', 'egyptfoss' ); ?></dt>
                                    <dd><?php echo (isset($user_data['contact_name']) ? $user_data['contact_name'] : ''); ?></dd>
                                <?php }
                                else{
                                    if ( bp_is_my_profile() ) { ?>
                                    <dt class="profile-label"><?php _e( 'Contact Name', 'egyptfoss' ); ?></dt>
                                    <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                    <?php } ?>
                                <?php }?>

                                <?php if (!empty($user_data['contact_email'])) { ?>
                                    <dt class="profile-label"><?php _e( 'Contact Email', 'egyptfoss' ); ?></dt>
                                    <dd><?php echo (isset($user_data['contact_email']) ? $user_data['contact_email'] : ''); ?></dd>
                                <?php }
                                else{
                                    if ( bp_is_my_profile() ) { ?>
                                    <dt class="profile-label"><?php _e( 'Contact Email', 'egyptfoss' ); ?></dt>
                                    <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                    <?php } ?>
                                <?php }?>

                                <?php if (!empty($user_data['contact_address'])) { ?>
                                    <dt class="profile-label"><?php _e( 'Contact Address', 'egyptfoss' ); ?></dt>
                                    <dd><?php echo (isset($user_data['contact_address']) ? $user_data['contact_address'] : ''); ?></dd>
                                <?php }
                                else{
                                    if ( bp_is_my_profile() ) { ?>
                                    <dt class="profile-label"><?php _e( 'Contact Address', 'egyptfoss' ); ?></dt>
                                    <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                    <?php } ?>
                                <?php }?>

                                <?php if (!empty($user_data['contact_phone'])) { ?>
                                    <dt class="profile-label"><?php _e( 'Contact Phone', 'egyptfoss' ); ?></dt>
                                    <dd><?php echo (isset($user_data['contact_phone']) ? $user_data['contact_phone'] : ''); ?></dd>
                                <?php }
                                else{
                                    if ( bp_is_my_profile() ) { ?>
                                    <dt class="profile-label"><?php _e( 'Contact Phone', 'egyptfoss' ); ?></dt>
                                    <dd><?php _e('-', 'egyptfoss'); ?></dd>
                                    <?php } ?>
                                <?php }?>
                            <?php } ?>

                        <?php endif; ?>
                        <?php
                        /**
                         * Fires after the display of a field table row for profile data.
                         *
                         * @since 1.1.0
                         */
                        do_action( 'bp_profile_field_item' ); ?>
                    <?php endwhile; ?>
                </dl>
            </div>

            <?php
            /** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
            do_action( 'bp_after_profile_field_content' ); ?>
        <?php endif; ?>
    <?php endwhile; ?>
    <?php
    /** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
    do_action( 'bp_profile_field_buttons' ); ?>
<?php endif; ?>

<?php
/** This action is documented in bp-templates/bp-legacy/buddypress/members/single/profile/profile-wp.php */
do_action( 'bp_after_profile_loop_content' ); ?>

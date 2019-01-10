<?php
/**
 * BuddyPress - Members Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>
<?php
$xprofile_id = bp_displayed_user_id();
$user_data = get_registration_data($xprofile_id);
$lang = get_locale();
if($lang == 'ar') {
  global $ar_sub_types;
  $sub_types = $ar_sub_types;
} else {
  global $en_sub_types;
  $sub_types = $en_sub_types;
}
?>
<div id="buddypress">
	<div class="col-md-3 profile-sidebar">
		<div id="user-avatar">
			<?php if (  bp_is_my_profile() ) { ?>
				<a href="<?php bp_loggedinuser_link() ?>about/change-avatar">
					<span href="<?php bp_loggedinuser_link() ?>about/change-avatar" class="edit-profile-img"></span>
					<?php //bp_displayed_user_avatar( 'type=full' ); ?>
					<?php echo get_avatar( bp_displayed_user_id(''), 120 ); ?>
				</a>
			<?php } else {?>
				<a href="<?php bp_displayed_user_link(); ?>">
					<?php //bp_displayed_user_avatar( 'type=full' ); ?>
					<?php echo get_avatar( bp_displayed_user_id(), 120 ); ?>
				</a>
			<?php } ?>
      <?php  $is_expert = get_user_meta(bp_displayed_user_id(),"is_expert",true);
      if($is_expert){ ?>  
				<img src="<?php echo get_template_directory_uri();?>/img/expert-tick.svg" class="expert-sign" data-toggle="tooltip" title="<?php _e("Expert","egyptfoss") ?>">
      <?php } ?>
    </div>
		<div class="user-profile-name">
			<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
				<?php //$current_user = wp_get_current_user(); ?>
				<h3 class="user-nicename"><?php bp_displayed_user_fullname(); //echo $current_user->display_name; ?></h3>
			<?php endif; ?>
      <?php
      if (!empty($user_data['type'])) {
        if ($user_data['type'] == "Entity") {?>
          <span class="account-type-icon entity">
          	<i class="fa fa-building"></i>
          </span>
        <?php } else { ?>
           <span class="account-type-icon person">
          	<i class="fa fa-user"></i>
          </span>
        <?php }
      } ?>
      <?php if (!empty($user_data['sub_type'])) { ?>
      	<span><?php echo ((isset($user_data['sub_type']) && !empty($user_data['sub_type'])) ? $sub_types[$user_data['sub_type']] : ''); ?></span>
      <?php } ?>
		</div>
		<div class="text-center">
			<span class="activity"><?php bp_last_activity( bp_displayed_user_id() ); ?></span>
		</div>
      <?php $user = get_user_by('id',bp_displayed_user_id()); 
      if ( !in_array( 'administrator', (array) $user->roles ) ) {
      ?>
		<div class="user-badges">
          <?php
      if(class_exists('EFBBadgesUsers'))
      {
        $points = get_user_meta(bp_displayed_user_id(),"efb_points",true);
        $points = ($points)?$points:0;
        echo "<span class='user-points'>" . sprintf(_n("%s Point","%s Points",$points,"efbadges"),$points) . "</span><br/>"; 
        $badges = EFBBadgesUsers::getHighRankBadgesByUser(bp_displayed_user_id());
        foreach($badges as $badge)
        {
          $badge->img = EFBBadges::getBadgeSmallUrl($badge->img);
        ?>
      <span class="small-badge"><img data-toggle="tooltip" title="<?php echo $badge->getTitle(pll_current_language()) ?>" src="<?php echo $badge->img ?>" alt="<?php echo $badge->getTitle(pll_current_language()) ?>" width="32px" height="32px"></span>
        <?php
        }
      }
    ?>   
		</div>
      <?php } ?>
		<div id="item-nav">
			<div class="no-ajax panel-group" id="object-nav" role="navigation">
				<ul class="nav nav-pills nav-stacked profile-nav" id="profile-menu">
					<?php $lang = pll_current_language(); ?>
					<li class="<?php echo (ef_profile_menu_active_li('/'))?'active':''; ?>"><a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/activity/" ?>"><i class="fa fa-list-alt"></i><?php _e('Timeline','egyptfoss') ?></a></li>
					<li class="<?php echo (ef_profile_menu_active_li('/about/') || ef_profile_menu_active_li('/about/edit/group/1/'))?'active':''; ?>"><a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/about/" ?>"><i class="fa fa-user"></i> <?php _e('About','egyptfoss') ?></a></li>
					<?php $is_contribution_open = false;
						if(ef_profile_menu_active_li('/contributions/news/')
							|| ef_profile_menu_active_li('/contributions/')
							|| ef_profile_menu_active_li('/contributions/success-stories/')
							|| ef_profile_menu_active_li('/contributions/events/')
							|| ef_profile_menu_active_li('/contributions/open-datasets/')
							|| ef_profile_menu_active_li('/contributions/wiki/')
							|| ef_profile_menu_active_li('/contributions/request-center/')
							|| ef_profile_menu_active_li('/contributions/expert-thoughts/')
							|| ef_profile_menu_active_li('/contributions/documents/')) {
								$is_contribution_open = true;
						}
						//show or hide
						$show_contr = ef_show_contribution_li();
						if($show_contr['total_count'] > 0) { ?>
							<li class="panel">
								<a data-toggle="collapse" class="item <?php echo ($is_contribution_open)?'':'collapsed'; ?>" data-parent="#profile-menu" href="#contributions-list">
									<i class="fa fa-folder"></i>
									<?php _e('Contributions','egyptfoss') ?>
									<i class="fa expand-icon rfloat"></i>
								</a>
								<ul id="contributions-list" class="<?php echo ($is_contribution_open)?'collapse in':'collapse'; ?>">
									<?php if($show_contr['news'] > 0) { ?>
										<li class="<?php echo (ef_profile_menu_active_li('/contributions/news/'))?'active':''; ?>"><a id="news" href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/news/" ?>"><?php _e('News','egyptfoss') ?></a></li>
									<?php } ?>
									<?php if($show_contr['product'] > 0) { ?>
									  <li class="<?php echo (ef_profile_menu_active_li('/contributions/'))?'active':''; ?>"><a id="products" href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/" ?>"><?php _e('Products','egyptfoss') ?></a></li>
									<?php } ?>
									<?php if($show_contr['success_story'] > 0) { ?>
									  <li class="<?php echo (ef_profile_menu_active_li('/contributions/success-stories/'))?'active':''; ?>"><a id="success-stories" href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/success-stories/" ?>"><?php _e('Success Stories','egyptfoss') ?></a></li>
									<?php } ?>
									<?php if($show_contr['tribe_events'] > 0) { ?>
									  <li class="<?php echo (ef_profile_menu_active_li('/contributions/events/'))?'active':''; ?>"><a id="events" href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/events/" ?>"><?php _e('Events','egyptfoss') ?></a></li>
									<?php } ?>
									<?php if($show_contr['open_dataset'] > 0) { ?>
									  <li class="<?php echo (ef_profile_menu_active_li('/contributions/open-datasets/'))?'active':''; ?>"><a id="open-datasets" href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/open-datasets/" ?>"><?php echo _n("Open Dataset","Open Datasets",2,"egyptfoss"); ?></a></li>
									<?php } ?>
									<?php if($show_contr['wiki'] > 0) { ?>
									  <li class="<?php echo (ef_profile_menu_active_li('/contributions/wiki/'))?'active':''; ?>"><a id="foss-pedia" href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/wiki/" ?>"><?php _e('FOSSPedia','egyptfoss') ?></a></li>
									<?php } ?>
									<?php if($show_contr['request_center'] > 0) { ?>
										<li class="<?php echo (ef_profile_menu_active_li('/contributions/request-center/'))?'active':''; ?>"><a id="request-center" href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/request-center/" ?>"><?php _e('Request Center','egyptfoss') ?></a></li>
									<?php } ?>
									<?php if($show_contr['expert_thought'] > 0) { ?>
										<li class="<?php echo (ef_profile_menu_active_li('/contributions/expert-thoughts/'))?'active':''; ?>"><a id="expert-thoughts" href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/expert-thoughts/" ?>"><?php _e('Expert Thoughts','egyptfoss') ?></a></li>
									<?php } ?>
									<?php if($show_contr['documents'] > 0) { ?>
										<li class="<?php echo (ef_profile_menu_active_li('/contributions/documents/'))?'active':''; ?>"><a id="documents" href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/documents/" ?>"><?php echo _x("Published Documents","definite","egyptfoss"); ?></a></li>
									<?php } ?>
								</ul>
							</li>
	          <?php } else { ?>
	          	<li class="<?php echo (ef_profile_menu_active_li('/contributions/'))?'active':''; ?>">
	          		<a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/contributions/" ?>"><i class="fa fa-folder"></i>
	          			<?php _e('Contributions','egyptfoss') ?>
	          		</a>
	          	</li>
	          <?php } ?>
	          <?php $is_setting_open = false;
	          	if(ef_profile_menu_active_li('/settings/')
	              || ef_profile_menu_active_li('/settings/linking/')
	              || ef_profile_menu_active_li('/settings/notifications-settings/')) {
	              $is_setting_open = true;
	          } ?>
	          <li class="<?php echo (ef_profile_menu_active_li('/services/')) ? 'active' : ''; ?>">
	          	<a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/services/" ?>">
	          		<i class="fa fa-suitcase"></i>
	          		<?php _e('Services','egyptfoss') ?>
	          	</a>
	          </li>
            <?php if ( !in_array( 'administrator', (array) $user->roles ) ) { ?>
	          <li class="<?php echo (ef_profile_menu_active_li('/badges/')) ? 'active' : ''; ?>">
	          	<a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/badges/" ?>">
	          		<i class="fa fa-shield"></i>
	          		<?php _e('Badges','egyptfoss') ?>
	          	</a>
	          </li> 
            <?php } ?>
            <?php 
            if(get_current_user_id() == bp_displayed_user_id()) { ?>
	          <li class="<?php echo (ef_profile_menu_active_li('/quizzes/')) ? 'active' : ''; ?>">
	          	<a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/quizzes/" ?>">
	          		<i class="fa fa-question-circle"></i>
	          		<?php _e('Quizzes','egyptfoss') ?>
	          	</a>
	          </li>     
            <?php } ?>
	          <?php  if(get_current_user_id() == bp_displayed_user_id()) { ?>
							<li class="panel">
								<a data-toggle="collapse" class="item <?php echo ($is_setting_open)?'':'collapsed'; ?>" data-parent="#profile-menu" href="#settings-list">
							  	<i class="fa fa-cog"></i>
							  	<?php _e('Settings','egyptfoss') ?>
							  	<i class="fa expand-icon rfloat"></i>
							  </a>
							  <ul id="settings-list" class="<?php echo ($is_setting_open)?'collapse in':'collapse'; ?>">
									<li class="<?php echo (ef_profile_menu_active_li('/settings/'))?'active':''; ?>"><a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/settings/" ?>"><?php _e('Account','egyptfoss') ?></a></li>
									<li class="<?php echo (ef_profile_menu_active_li('/settings/notifications-settings/'))?'active':''; ?>"><a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/settings/notifications-settings/" ?>"><?php _e('Notifications','egyptfoss') ?></a></li>
									<li class="<?php echo (ef_profile_menu_active_li('/settings/linking/'))?'active':''; ?>"><a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id())."/settings/linking/" ?>"><?php _e('Linked Accounts','egyptfoss') ?></a></li>
								</ul>
							</li>
						<?php } ?>
					<?php //bp_get_displayed_user_nav(); ?>
					<?php
					/**
					 * Fires after the display of member options navigation.
					 *
					 * @since 1.2.4
					 */
					// do_action( 'bp_member_options_nav' ); ?>
				</ul>
			</div>
		</div><!-- #item-nav -->
	</div>
	<div class="col-md-9 profile-content">
		<div id="item-body">
			<?php	do_action( 'bp_before_member_home_content' ); ?>
			<?php do_action( 'template_notices' ); ?>
			<?php do_action( 'bp_before_member_body' );

			if ( bp_is_user_activity() || !bp_current_component() ) :
				bp_get_template_part( 'members/single/activity' );
			elseif ( bp_is_user_blogs() ) :
				bp_get_template_part( 'members/single/blogs'    );
			elseif ( bp_is_user_friends() ) :
				bp_get_template_part( 'members/single/friends'  );
			elseif ( bp_is_user_groups() ) :
				bp_get_template_part( 'members/single/groups'   );
			elseif ( bp_is_user_messages() ) :
				bp_get_template_part( 'members/single/messages' );
			elseif ( bp_is_user_profile() ) :
				bp_get_template_part( 'members/single/profile'  );
			elseif ( bp_is_user_forums() ) :
				bp_get_template_part( 'members/single/forums'   );
			elseif ( bp_is_user_notifications() ) :
				bp_get_template_part( 'members/single/notifications' );
			elseif ( bp_is_user_settings() ) :
				bp_get_template_part( 'members/single/settings' );
			// If nothing sticks, load a generic template
			else :
				bp_get_template_part( 'members/single/plugins'  );
			endif;

			do_action( 'bp_after_member_body' ); ?>
		</div><!-- #item-body -->

		<?php do_action( 'bp_after_member_home_content' ); ?>
	</div>

	<div id="item-header" role="complementary">
		<?php // If the cover image feature is enabled, use a specific header
		if ( bp_displayed_user_use_cover_image_header() ) :
			bp_get_template_part( 'members/single/cover-image-header' );
		else :
			bp_get_template_part( 'members/single/member-header' );
		endif;
		?>
	</div><!-- #item-header -->
</div><!-- #buddypress -->

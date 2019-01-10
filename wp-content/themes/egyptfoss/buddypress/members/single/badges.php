<?php
$lang = get_locale();
$AchievedIDs = "";
$badges = EFBBadgesUsers::getBadgesByUser(bp_displayed_user_id());

//validate user isn't admin
$user = get_user_by('id',bp_displayed_user_id()); 
?>
<div class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php _e("Badges","egyptfoss"); ?></h2>
	</div>
</div>
<?php 
if ( !in_array( 'administrator', (array) $user->roles ) ) { ?>
<div class="row">
    <div class="col-md-12">
      <?php if(sizeof($badges) > 0 ) { ?>
      <section class="featured-group">
          <div class="group-header">
              <h4><?php _e("Achieved","egyptfoss"); ?></h4>
          </div>
          <div class="group-content">
            <ul class="badges-grid clearfix">
              <?php foreach($badges as $badge) { 
                $AchievedIDs .= $badge->id.",";
                  //array_push($AchievedIDs, $badge->id);
              ?>
              <li class="badge-item taken">
                <div class="badge-icon">
                    <img src="<?php echo $badge->img ?>" alt="<?php echo ($lang == "ar")?$badge->title_ar:$badge->title; ?>" title="<?php echo ($lang == "ar")?$badge->title_ar:$badge->title; ?>">
                </div>
                <br>
                <h5><strong><?php echo ($lang == "ar")?$badge->title_ar:$badge->title; ?></strong></h5>
                <p><?php echo ($lang == "ar")?$badge->description_ar:$badge->description; ?></p>
              </li>
              <?php } ?>
          </ul>
          </div>
      </section>
      <?php } ?>
        
      <?php // Load not achieved Badges
        $notAchievedBadges = EFBBadgesUsers::getNotAchievedBadges(rtrim($AchievedIDs,","));
      ?>
        
      <?php if(sizeof($notAchievedBadges) > 0) { ?>
      <section class="featured-group">
          <div class="group-header">
              <h4><?php _e("Locked","egyptfoss"); ?></h4>
          </div>
          <div class="group-content">
        <ul class="badges-grid clearfix">
            <?php foreach($notAchievedBadges as $badge) { ?>
            <li class="badge-item">
                <div class="badge-icon">
                    <img src="<?php echo $badge->img ?>" alt="<?php echo ($lang == "ar")?$badge->title_ar:$badge->title; ?>" title="<?php echo ($lang == "ar")?$badge->title_ar:$badge->title; ?>">
                </div>
                <br>
              <h5><strong><?php echo ($lang == "ar")?$badge->title_ar:$badge->title; ?></strong></h5>
                <p><?php echo ($lang == "ar")?$badge->description_ar:$badge->description; ?></p>
            </li>
            <?php } ?>
        </ul>
          </div>
      </section>
      <?php } ?>
    </div>
</div>
<?php } else { ?>
      <div class="row">
             <div class="col-md-12">
               <div class="empty-state-msg">
                   <i class="fa fa-warning"></i>
                   <br>
                   <h4>
        <?php echo sprintf(__("You don't have permission to access this page or you have signed out.", "egyptfoss"), ''); ?>    
          <!--<a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id()).'/about/' ?>"> <?php echo bp_core_get_user_displayname(bp_displayed_user_id()); ?> </a>      -->
                   </h4>
               </div>
           </div>   
      </div>
<?php } ?>

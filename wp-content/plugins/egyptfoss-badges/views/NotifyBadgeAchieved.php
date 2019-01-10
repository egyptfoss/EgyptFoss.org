<?php
  $user_id = get_current_user_id();
  $userData = get_user_by("ID", $user_id);
?>
<div class="modal fade achievement-modal" id="achievement-modal" tabindex="-1" role="dialog" aria-labelledby="achievement" style="display: none;">
  <div class="modal-dialog" role="modal-dialog">
    <div class="modal-content achievement-modal-content">
     <audio  id="notification-sound">
         <source src="<?php echo constant('EFB_PLUGIN_URL'); ?>/sounds/01.ogg" type="audio/ogg">
  <source src="<?php echo constant('EFB_PLUGIN_URL'); ?>/sounds/01.mp3" type="audio/mpeg">
     </audio>
      <div class="modal-body text-center">
        <div class="row">
            <div class="col-md-12">
                <img src="<?php echo $badgeInfo->img ?>" class="achivement-badge" width="150px" alt="<?php echo $badgeInfo->getTitle(get_locale()) ?>">
                <h3><?php _e("Congratulations!","efbadges"); ?></h3>
                <p><?php echo sprintf(__("Congratulations! You have earned the <strong>%s</strong> badge.","efbadges"),$badgeInfo->getTitle(get_locale())); ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <a href="<?php echo home_url()."/members/".$userData->user_nicename."/badges/"; ?>" class="btn btn-primary"><?php _e("View all badges","efbadges"); ?></a>
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
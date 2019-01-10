<?php
/**
 * Egyptfoss- Email Notifications
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
$notificationsSettings = array(
  "profile_updates" => "Profile Updates Email Notifications",
  "products_updates" => "New Products Email Notifications",
  "events_updates" => "New Events Email Notifications",
  "news_updates" => "New News Email Notifications",
  "success_stories_updates" => "New Success Stories Email Notifications",
  "open_datasets_updates" => "New Open Datasets Email Notifications",
  "request_center_updates" => "New Requests Email Notifications",
  "collaboration_center_updates" => "New Spaces/Documents Email Notifications",
  "expert_thoughts_updates" => "Expert thoughts Email Notifications",
  "awarness_center_updates" => "Quizzes Email Notifications",
  "market_place_updates" => "New Services Email Notifications",
);
?>
<header class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php _e("Notifications","egyptfoss"); ?></h2>
	</div>
</header>
<div class="notifications-group">
    <ul class="list-social-networks">
        <?php foreach ($notificationsSettings as $notificationKey => $notificationText) : ?>
        <li class="clearfix">
            <div class="lfloat"><?php echo _e($notificationText,"egyptfoss");?></div>
            <div class="values-column rfloat">
                <label class="option-preview">
                    <?php $slectedFrequency = get_user_meta(get_current_user_id(), "notification_$notificationKey", true); ?>
                    <span id="<?php echo $notificationKey?>_value">
                        <?php echo $slectedFrequency ? _e($slectedFrequency,"egyptfoss") : _e('Never',"egyptfoss"); ?>
                    </span>
                    <a role="button" class="edit-field" id="<?php echo $notificationKey?>"><i class="fa fa-pencil"></i></a>
                </label>
                <div class="save-select hidden" id="email_updates_option">
                    <select class="form-control input-sm" id="<?php echo $notificationKey?>-notification-list">
                        <optgroup>
                            <option value="Never" <?php echo ($slectedFrequency == "Never") ? 'selected="selected"' : '' ?>>
                                <?php echo _e(Never,"egyptfoss"); ?>
                            </option>
                            <option value="Daily" <?php echo ($slectedFrequency == "Daily") ? 'selected="selected"' : '' ?>>
                                <?php echo _e(Daily,"egyptfoss"); ?>
                            </option>
                            <option value="Weekly" <?php echo ($slectedFrequency == "Weekly") ? 'selected="selected"' : '' ?>>
                                <?php echo _e(Weekly,"egyptfoss"); ?>
                            </option>
                            <option value="Monthly" <?php echo ($slectedFrequency == "Monthly") ? 'selected="selected"' : '' ?>>
                                <?php echo _e(Monthly,"egyptfoss"); ?>
                            </option>
                        </optgroup>
                    </select>
                    <button class="btn btn-primary btn-sm save-value" data-notification-type="<?php echo $notificationKey ?>" id="save_<?php echo $notificationKey ?>"><?php _e("Save","egyptfoss"); ?></button>
                </div>
            </div>
        </li>
        <?php endforeach;?>
    </ul>

</div>
<script>
    (function ($) {
        $(document).ready(function () {
            $('.save-value').click(function (e) {
                e.preventDefault();
                var frequency = $(this).parent().find('select').val();
                var frequencyText = $(this).parent().find('select option:selected').text();
                var data = {
                    action: 'ef_email_notifications',
                    notification: $(this).attr('data-notification-type'),
                    frequency: frequency
                };
                var clickedElement = $(this);
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    success: function (data) {
                        switch (data)
                        {
                            case "saved":
                                clickedElement.parent().addClass('hidden');
                                clickedElement.parent().parent().find(".option-preview").removeClass('hidden');
                                clickedElement.parent().parent().find(".option-preview span").html(frequencyText);
                                break;
                            case "not-saved":
                                break;
                        }
                    }
                });
            });
        });
    }(jQuery));
</script>
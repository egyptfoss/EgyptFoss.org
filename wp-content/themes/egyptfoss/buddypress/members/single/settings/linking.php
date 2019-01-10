<?php
/**
 * Egyptfoss- linking to social media page
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
?>
<?php
$messages = getMessageBySession('ef_wsl_login'); 
if(isset($messages['success']))
{
  echo "<div class='socialLinkingMessage alert alert-success'>". $messages['success'] ."</div>";
}else
{
  if(isset($messages['error']))
  {
    echo "<div class='socialLinkingMessage alert alert-error'>". $messages['error'] ."</div>";
  }
}
?>
<header class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php _e("Linked Accounts","egyptfoss"); ?></h2>
	</div>
</header>

	<ul class="list-social-networks">
<?php

$links = getSocialLinks();
foreach ($links as $provider => $url) {
  $profileID = isConnectedWithSocialMedia($provider);
  if($profileID){
  ?>

  		<li class="<?php echo $provider; ?>">
  			   	<?php echo  __($provider,"egyptfoss"); ?>
            <a id="profile-id-<?php echo $profileID ?>" data-provider="<?php echo $provider ?>" class="egyptfoss-deleteProfile btn btn-danger btn-sm" href="<?php echo $url; ?>"><i class="fa fa-unlink"></i> <?php _e("Unlink","egyptfoss"); ?></a>
  		</li>

  <?php
  } else {
  ?>
   <li class="<?php echo $provider; ?>">
   <?php echo  __($provider,"egyptfoss"); ?>
   	<a href="<?php echo $url; ?>" class="btn btn-light btn-sm"><i class="fa fa-link"></i> <?php _e("Link Account","egyptfoss"); ?></a>
   </li>
<?php
  }
}
?>
	</ul>
<script>
(function ($) {
  $(document).ready(function () {
    $(".socialLinkingMessage").fadeOut(4000);
    $(".egyptfoss-deleteProfile").click(function(e){
      e.preventDefault();
      $(".socialLinkingMessage").remove();
      id = this.id.replace('profile-id-','');
      var data = {
                action: 'ef_delete_user_profile',
                userProfileID: id,
      };
      dataProvider = $(this).attr('data-provider');
      jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
            switch(data)
            {
              case "deleted":
                $("#profile-id-"+id).unbind('click');
                $("#profile-id-"+id).html("<i class='fa fa-link'></i> "+ $.validator.messages.link_account);
                $("#profile-id-"+id).removeClass("egyptfoss-deleteProfile btn btn-danger btn-sm");
                $("#profile-id-"+id).addClass("btn btn-light btn-sm");
                $("#profile-id-"+id).removeAttr("id");
                successMessage = $.validator.messages.linking_successfull_message.replace("%s", $.validator.messages[dataProvider]);
                $(".list-social-networks").before("<div class='socialLinkingMessage alert alert-success'>"+ successMessage +"</div>");
                break;
              case "last-account":
                $(".list-social-networks").before("<div class='socialLinkingMessage alert alert-danger'>"+ $.validator.messages.linking_least_one_account +"</div>");
                break;
              case "not-deleted":
                $(".list-social-networks").before("<div class='socialLinkingMessage alert alert-danger'>"+ $.validator.messages.not_logged_in_user +"</div>");
                break;
              default:
                break;
            }
            $(".socialLinkingMessage").fadeOut(4000);
          }
      });
    });
  });
}(jQuery));
</script>

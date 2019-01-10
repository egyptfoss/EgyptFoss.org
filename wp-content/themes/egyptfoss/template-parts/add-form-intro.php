<?php
  $template_slug = get_page_template_slug();
  if($template_slug == "page-add-product.php") {
    $dismiss_class = 'welcome-product';
    $intro_message = 'You can suggest a product that enriches EgyptFOSS content.';
  } else if($template_slug == "template-manage-news.php") {
    $dismiss_class = 'welcome-news';
    $intro_message = 'You can suggest a news that enriches EgyptFOSS content.';
  } else if($template_slug == "page-add-event.php") {
    $dismiss_class = 'welcome-event';
    $intro_message = 'You can suggest an event that enriches EgyptFOSS content.';
  } else if($template_slug == "template-add-success-story.php") {
    $dismiss_class = 'welcome-story';
    $intro_message = 'You can suggest a success story that inspires and motivates EgyptFOSS members.';
  } else if($template_slug == "template-add-open-dataset.php") {
    $dismiss_class = 'welcome-dataset';
    $intro_message = 'You can suggest an open dataset that enriches EgyptFOSS content.';
  } else if($template_slug == "template-add-feedback.php") {
    $dismiss_class = 'welcome-feedback';
    $intro_message = 'Let us know what you think about EgyptFOSS and how to make it better.';
  } else if($template_slug == "template-add-request-center.php") {
    $dismiss_class = 'welcome-request';
    $intro_message = 'You can add a request and get responses from members on EgyptFOSS.‎';
  } else if($template_slug == "template-add-expert-thought.php") {
    $dismiss_class = 'welcome-expert';
    $intro_message = 'You can suggest a thought that inspires and motivates EgyptFOSS members.';
  } else if($template_slug == "MarketPlace/template-add-service.php") {
    $dismiss_class = 'welcome-service';
    $intro_message = 'Whether you are an individual or entity, offer your services in the Marketplace and receive requests from interested members.';
  }
  
  if (!isset($_COOKIE[$dismiss_class]) || $_COOKIE[$dismiss_class] != 'dismiss') {
?>
  <div class="well alert alert-dismissable text-center add-story-intro fade in">
    <div class="row">
      <button type="button" class="close dismiss-welcome" cname="<?php echo $dismiss_class; ?>" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="row">
      <div class="col-md-4">
        <div class="col-icon">
          <img src="<?php echo get_template_directory_uri(); ?>/img/add_story.png"  class="icon">
        </div>
        <p><?php _e($intro_message,"egyptfoss"); ?></p>
      </div>
      <div class="col-md-4">
        <div class="col-icon">
          <img src="<?php echo get_template_directory_uri(); ?>/img/review_story.png" class="icon">
        </div>
        <p><?php _e("Your content will be reviewed and published by the administrator. and you should receive an email after publishing your content.","egyptfoss") ?></p>
      </div>
      <div class="col-md-4">
        <div class="col-icon">
          <img src="<?php echo get_template_directory_uri(); ?>/img/interests_icon.png" height="60" class="icon">
        </div>
        <p><?php _e("Relating your content with interests helps others to make the best benefit from your content.","egyptfoss") ?></p>
      </div>
    </div>
    <div class="row">
      <a class="btn btn-primary dismiss-welcome" cname="<?php echo $dismiss_class; ?>" data-dismiss="alert"><?php _e("OK","egyptfoss") ?></a>
    </div>
  </div>

<?php } ?>
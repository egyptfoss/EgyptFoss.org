<?php
/**
 * Template Name: Listing Collaboration Items
 *
 * @package egyptfoss
 */
$space_id = 0;
$view = "";
$title = "";
$emptyMsg = "";
$items = array();
$spaceTitle = "";
$breadCrumb = "";
$collabCenter = new CollaborationCenterItem();
$pathDetails = explode("/", $_SERVER['REQUEST_URI']);
$isSharedView = array_search("shared", $pathDetails);
$isSpaceView = array_search("spaces", $pathDetails);
$isPublishedView = array_search("published", $pathDetails);
if ($isSharedView) {
  $space_id = efGetValueFromUrlByKey("spaces");
  if ($space_id) {
    $title = __("Shared with me", "egyptfoss");
    $emptyMsg = __("There are no documents or spaces shared with you !", "egyptfoss");
    $view = "shared";
    if ($collabCenter->isSharedItemByUser(get_current_user_id(), $space_id)) {
      $items = $collabCenter->getSpaceContentById($space_id);
    } else {
      wp_redirect(home_url('/?status=403'));
      exit;
    }
    $spaceTitle = $collabCenter->where("ID", "=", $space_id)->first()->title;
    $space = array("ID" => $space_id, "title" => $spaceTitle);
    $breadCrumb = CollaborationBreadCrumb($view, "List", $space);
  } else {
    $items = $collabCenter->getSharedItemsByUser(get_current_user_id())->get();
    $title = __("Shared with me", "egyptfoss");
    $emptyMsg = __("There are no documents or spaces shared with you !", "egyptfoss");
    $view = "shared";
  }
}
if (!$isSharedView && $isSpaceView) {
  $space_id = efGetValueFromUrlByKey("spaces");
  if ($space_id) {
    $items = $collabCenter->getSpaceContentByUserAndId($space_id,get_current_user_id());
    $spaceTitle = $collabCenter->where("ID","=",$space_id)->first()->title; 
    $title = __("My Spaces", "egyptfoss");
    $emptyMsg = __("No documents!", "egyptfoss");
    if (!$collabCenter->isMySpace(get_current_user_id(), $space_id)) {
      wp_redirect(home_url('/?status=403'));
      exit;
      $emptyMsg = __("There are no spaces found !", "egyptfoss");
      $title = __("My Spaces", "egyptfoss");
    }
    $view = "document";
    $space = array("ID"=>$space_id,"title"=>$spaceTitle); 
    $breadCrumb = CollaborationBreadCrumb($view, "List", $space);
  } else {
    $items = $collabCenter->getSpacesByUser(get_current_user_id());
    $title = __("My Spaces", "egyptfoss");
    $emptyMsg = __("You don't have spaces yet!", "egyptfoss");
    $view = "space";
  }
}
// in case of filteration in a url
$section = '';
if( isset( $_GET['section'] ) ) {
  $section = $_GET['section'];
}
if ($isPublishedView) {
  $items = $collabCenter->getPublishedDocuments( $section );
  $title = __("Published Documents", "egyptfoss");
  $emptyMsg = __("No published documents!", "egyptfoss");
  $view = "published";
} else {
  if (!$isSharedView && !$isSpaceView) {
    $items = $collabCenter->getPublishedDocuments( $section );
    $title = __("Published Documents", "egyptfoss");
    $emptyMsg = __("No published documents!", "egyptfoss");
    $view = "published";
  }
}
get_header();
load_orm();
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
       <?php echo $breadCrumb ?>
        <h1><?php echo $title ?></h1>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
 <div class="row ft-padding-top">
    <div class="col-md-12">
        <?php if (!isset($_COOKIE['welcome-collaboration-center']) || $_COOKIE['welcome-collaboration-center'] != 'dismiss') { ?>
        <div class="well alert alert-dismissable text-center add-story-intro fade in">
          <div class="row">
            <button type="button" class="close dismiss-welcome" cname="welcome-collaboration-center" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="row">
            <div class="col-md-12">
              <h1 class="color-primary"><?php _e("Welcome to the Collaboration Center", "egyptfoss") ?></h1>
                <p>
                   <?php _e("In the Collaboration Center you can create documents and share them with some members to contribute, then publish them to be be available for all members.", "egyptfoss") ?>
                </p>
            </div>
          </div>
          <div class="row">
            <a class="btn btn-primary dismiss-welcome" cname="welcome-collaboration-center" data-dismiss="alert"><?php _e("OK","egyptfoss") ?></a>
          </div>
        </div> 
        <?php } ?>
    </div>
 </div>
  <div class="row">
<?php
  include(locate_template('CollaborationCenter/template-parts/side-menu.php'));
if ($view == "published") {
  include(locate_template('CollaborationCenter/template-parts/content-list-published-items.php'));
} else { 
  include(locate_template('CollaborationCenter/template-parts/content-list-items.php'));
}
?>
  </div><!-- #primary -->
</div>
<!-- Modal -->
<div id="confirm-modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-ok"><?php echo __('Save', 'egyptfoss'); ?></button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Cancel', 'egyptfoss'); ?></button>
      </div>
    </div>
  </div>
</div>
<?php
get_footer();
?>

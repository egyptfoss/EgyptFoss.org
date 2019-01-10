<?php
/**
 * Template Name: View Revision of Document
 *
 * @package egyptfoss
 */
get_header();
load_orm();
$document_id = efGetValueFromUrlByKey("revisions");

$item = CollaborationCenterItemHistory::where(array( 'id'=> $document_id))->first();
$document = $item->document()->first();
$collabCenterItem = new CollaborationCenterItem();
$isShared = $collabCenterItem->isSharedItemByUser(get_current_user_id(), $document->ID);
$document_owner = $document->owner_id;
// not exist item, redirect to error page
if(empty( $item) || (get_current_user_id() != $document_owner && !$isShared)) {
    include( get_query_template( '404' ) );
    header( 'HTTP/1.0 404 Not Found' );
    exit; 
}
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
         <?php
         $space = $item->document()->first()->space()->first();
         $space = array("ID"=>$space->ID,"title"=>$space->title);
         $doc = array("ID"=>$document->ID,"title"=>$document->title);
         $viewMode = ($isShared)?"shared":"document";
         echo CollaborationBreadCrumb($viewMode,"Revision",$space,$doc); ?>
        <h1><?php echo __("Revision of","egyptfoss") . " [".mysql2date('d/m/Y',$item->created_date)."]: "  . $item->title ?></h1>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row" id="create-doc">
    <div class="edit-warning">
      <i class="fa fa-history"></i> <?php _e("Revision of","egyptfoss") ?> <a class="revision-date" href="#"><?php echo mysql2date('d/m/Y',$item->created_date) ?></a> <strong>[<?php _e(ucfirst($item->status),"egyptfoss") ?>]</strong> 
      <div class="revision-author">
        <small><i class="fa fa-user"></i> <?php if(bp_core_get_username($item->editor_id) != '') { ?>
                    <a href="<?php echo home_url()."/members/".bp_core_get_username($item->editor_id).'/about/' ?>"> 
                        <?php echo bp_core_get_user_displayname($item->editor_id); ?> 
                    </a>
                <?php } else { echo bp_core_get_user_displayname($item->editor_id); } ?></a></small>
      </div>
    </div>
<div class="content-area col-md-12">
  <div> 
    <br>     
    <span class="news-author documentOwner">
      <?php echo stripslashes( nl2br(html_entity_decode($item->content)) ); ?>
    </span>
  </div>
</div>
  </div><!-- #primary -->
</div>
<?php
get_footer();


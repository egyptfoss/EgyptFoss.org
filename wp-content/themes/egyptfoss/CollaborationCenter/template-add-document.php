<?php
/**
 * Template Name: Add Collaboration Documents
 *
 * @package egyptfoss
 */
load_orm();
$newCollabCenter = new CollaborationCenterItem();
$space_id = efGetValueFromUrlByKey("spaces");
if(!$space_id || ! $newCollabCenter->isMySpace(get_current_user_id(), $space_id))
{
  wp_redirect( home_url( '/?status=403' ) );
  exit;  
}

if (!empty($_POST['action']) && $_POST['action'] == "add_collaboration_center_document") {
  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'add_collaboration_center_document' ) ) {
    wp_redirect( home_url( '/?status=403' ) );
    exit;
  }
  ef_add_collaboration_center_document();
}

$roles = wp_get_current_user()->roles;

get_header();
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php 
        $space = CollaborationCenterItem::where("ID","=",$space_id)->first();
        $space = array("ID"=>$space->ID,"title"=>$space->title);
        echo CollaborationBreadCrumb("document", "Add",$space,array("title"=>$untitledDocLabel)); ?>
        <?php $untitledDocLabel = sprintf(__("Untitled %s", "egyptfoss"),_x("document","indefinite","egyptfoss")); ?>
        <input type="text" readonly class="doc-live-title" value="<?php echo $untitledDocLabel ?>">
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
  <div id="create-doc">
      <form id="add_collaboration_center_document" name="add_collaboration_center_document" method="POST" action="">  
      <div  class="spaces-listing col-md-12">
        <?php if( !in_array("author", $roles) && !in_array("administrator", $roles) ): ?>
          <div class="well alert alert-dismissable text-center add-story-intro fade in">
            <div class="row">
              <div class="col-md-12">
                  <p style="margin-bottom: 0px;">
                     <?php _e("Great you almost start to collaborate in EgyptFOSS, You can use this page to create your document, and it will be saved as a draft before you choose either to share it with another user in our community to review it and publish it, or to review it by yourself. Remember you need to be an author before you can publish your document. Thanks.", "egyptfoss") ?>
                  </p>
              </div>
            </div>
          </div>
        <?php endif; ?>
        <?php
      $messages = get_query_var("ef_collaboration_center_messages");
      if(isset($messages['errors']) && !empty($messages['errors'])){
      ?>
      <div class="alert alert-danger"><?php
      foreach($messages['errors'] as $error ) {
        echo "<i class='fa fa-warning'></i> " . $error . "<br/>";
      }
      ?>
      </div>  
      <?php 
      }
      if(isset($messages['success'])) { ?>
      <div class="alert alert-success"><?php
        foreach($messages['success'] as $success ) {
        echo "<i class='fa fa-check'></i> " . $success . "<br/>";
      } ?>
      </div>
      <?php }
      set_query_var("ef_collaboration_center_messages", array()); ?>
        <div class="form-group">
          <label for="" class="label"><?php echo sprintf(__("%s Title", "egyptfoss"),_x("Document","definite","egyptfoss")) ?></label>
          <input type="text" name="document_title" class="form-control doc-name" placeholder="<?php echo sprintf(__("Type %s Name", "egyptfoss"),_x("Document","definite","egyptfoss")) ?>">
        </div>
        <div class="editor-box">
          <?php
          $content = '';
          $editor_id = 'document_content';
          $settings = array(
            'wpautop' => true,
            'media_buttons' => true,
            'textarea_name' => $editor_id,
            'textarea_rows' => get_option('default_post_edit_rows', 10),
            'tabindex' => '',
            'editor_css' => '',
            'editor_class' => '',
            'teeny' => false,
            'dfw' => false,
            'tinymce' => true,
            'quicktags' => true
          );
          wp_editor($content, $editor_id, $settings = array());
          ?>
        </div>
        <input type="hidden" name="action" value="add_collaboration_center_document" />
        <input type="hidden" name="space_id" value="<?php echo $space_id ?>" />
        <?php wp_nonce_field( 'add_collaboration_center_document' ); ?>
        <div class="form-group clearfix">
          <input type="submit" class="btn btn-primary rfloat" value="<?php _e("Save", "egyptfoss") ?>"/>
        </div>
      </div>
      </form>  
    </div><!-- #primary -->
  </div>
</div>
<?php get_footer(); ?>

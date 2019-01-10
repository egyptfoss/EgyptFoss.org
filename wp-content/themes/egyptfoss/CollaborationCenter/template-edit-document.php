<?php
/**
 * Template Name: Edit Collaboration Document
 *
 * @package egyptfoss
 */

load_orm();

//$document_id = ef_get_id_from_url($_SERVER['REQUEST_URI'],"edit/");
$document_id = efGetValueFromUrlByKey("document");
if(!is_numeric($document_id))
{
  wp_redirect( home_url( '/?status=403' ) );
  exit;  
}

if (!empty($_POST['action']) && $_POST['action'] == "edit_collaboration_center_document" && isset($_POST['postid'])) {
  
  if($_POST['postid'] == -1 || !is_numeric($_POST['postid']))
  {
      wp_redirect( home_url( '/?status=403' ) );
      exit;
  }
  
  $nonce = $_REQUEST['_wpnonce'];
  if ( ! wp_verify_nonce( $nonce, 'edit_collaboration_center_document' ) ) {
    wp_redirect( home_url( '/?status=403' ) );
    exit;
  }
  
  ef_edit_collaboration_center_document($_POST['postid']);
  do_action('efb_init_alert_user');
}

if($document_id != -1)
{
  $item = new CollaborationCenterItem();
  $document = $item->getDocumentByID($document_id);
  if (!$document) {
      wp_redirect( home_url( '/?status=403' ) );
      exit;
  }
  
  //validate user has access to edit on it
  $user = get_current_user_id();
  $space_id = $document->item_ID;
  $collabCenterItem = new CollaborationCenterItem();
  $collabCenterUserPermission = new CollaborationCenterUserPermission();
  $isShared = $collabCenterItem->isSharedItemByUser($user, $document_id);
  if(!isset($document_id) || !$collabCenterItem->isMyDocument($user, $document_id))
  {
      //check if have permission
      if( ($collabCenterUserPermission->hasPermissionByItemID($user, $document_id) < 1
              && $collabCenterUserPermission->hasPermissionByItemID($user, $space_id) < 1) && !$isShared)
      {
        wp_redirect( home_url( '/?status=403' ) );
        exit;
      }
  }

  //validated document: retrieve its info
  $ef_document_owner = $document->owner_id;
  $ef_document_title = $document->title;
  $ef_document_content = htmlspecialchars_decode($document->content);
  $ef_document_status = $document->status;
  $ef_document_section = '';
  //load last document history status
  if($ef_document_status == "published")
  {
    $last_document_history = $document->documentHistory->last();
    $ef_document_section = $last_document_history->section;
  }
  
  //check if able to edit
  $edit_enabled = true;
  $alertMsg = '';
  if($ef_document_status != 'draft')
  {
    if($collabCenterItem->isSharedItemByUser($user, $document_id, true))
    {
      $edit_enabled = false;
      $alertMsg = __("Document is","egyptfoss")." ".__($ef_document_status,"egyptfoss")." ".__("and you can't make new changes","egyptfoss");
    } else {
      $ef_item_status = ef_return_status_by_permission($document_id, $space_id, $ef_document_owner);
      if(!array_key_exists($ef_document_status, $ef_item_status))
      {
        $edit_enabled = false;
      }

      //is editor
      if(sizeof($ef_item_status) == 1)
      {
        $alertMsg = __("Document is","egyptfoss")." ".__($ef_document_status,"egyptfoss")." ".__("and you can't make new changes","egyptfoss");
      }else if(sizeof($ef_item_status) == 2 && $ef_document_status == "published")
      {
        $alertMsg = __("Document is","egyptfoss")." ".__($ef_document_status,"egyptfoss")." ".__("and you can't make new changes","egyptfoss");
      }
    }
  }
}else
{
    wp_redirect( home_url( '/?status=403' ) );
    exit;
}

$roles = wp_get_current_user()->roles;

get_header();
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
       <?php echo CollaborationBreadCrumb($view, "Edit",$space,$doc) ?>
        <input type="text" readonly class="doc-live-title" value="<?php echo $document->title; ?>">
        <?php 
        $view = ($isShared)?"shared":"document";
        $doc = array("ID"=>$document_id,"title"=>$ef_document_title);
        $space = CollaborationCenterItem::where("ID","=",$space_id)->first();
        $space = array("ID"=>$space->ID,"title"=>$space->title); ?>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
  <div id="create-doc">
    <div class="row">
      <?php 
      // $doument_id, $isItemSharedWithUser & $document_owner used inside revisions.php
      $document_id = $document->ID;
      $document_owner = $document->owner_id;
      $isItemSharedWithUser = $isShared;
      include(locate_template('CollaborationCenter/template-parts/revisions.php'));
      ?>
      <?php if(!empty($alertMsg)) { ?>
      <div class="alert alert-warning">
          <?php echo $alertMsg; ?>
      </div>
      <?php } ?>
      <form id="edit_collaboration_center_document" name="edit_collaboration_center_document" method="POST" action="">  
          <input type="hidden" name="old_status" id="old_status" value="<?php echo $ef_document_status; ?>">
      <div  class="spaces-listing col-md-12">
        <?php if( !in_array("author", $roles) && !in_array("administrator", $roles) && $ef_document_status != 'published' ): ?>
          <div class="well alert alert-dismissable text-center add-story-intro fade in">
            <div class="row">
              <div class="col-md-12">
                  <p style="margin-bottom: 0px;">
                     <?php _e("Thanks for collaborating in EgyptFOSS, you need to get author badge before you can publish your document. It's so easy! you just need to suggest a news, product, dataset, service in our marketplace, or event and we will review and publish it as soon as possible. Thanks again for making a difference.", "egyptfoss") ?>
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
      
        //retrieve data selected
        $ef_document_title = $_POST['document_title'];
        $ef_document_content = $_POST['document_content'];
        $ef_document_status = $_POST['status'];
        $ef_document_section = $_POST['section'];
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
          <input <?php echo ($edit_enabled)?(''):('readonly') ?> type="text" value="<?php echo $ef_document_title; ?>" name="document_title" class="form-control doc-name" placeholder="<?php echo sprintf(__("Type %s Name", "egyptfoss"),_x("Document","definite","egyptfoss")) ?>">
        </div>
        <div class="editor-box">
          <?php
          //check if edited
          if(!$edit_enabled)
          {
            add_filter( 'tiny_mce_before_init', 'ef_tiny_mce_before_init', 10);
          }
          
          $editor_id = 'document_content';
          $decoded_content = stripslashes($ef_document_content);
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
          wp_editor($decoded_content, $editor_id, $settings = array());
          ?>
        </div>
        <?php 
          $ef_item_status = ef_return_status_by_permission($document_id, $space_id, $ef_document_owner);
          if(sizeof($ef_item_status) > 1 && $edit_enabled) {
        ?>
        <div class="form-group row">
        	<div class="col-md-12">                    
                <label for="status" class="label"><?php _e( 'Status', 'egyptfoss' ); ?> <?php _e( '(required)', 'egyptfoss' ); ?></label>
                <select class="form-control" id="status" name="status" style="width:100%; ">
                  <optgroup>
                    <option value="" <?php echo ((!isset($ef_document_status) || $ef_document_status == '')?"selected":""); ?> disabled><?php _e( 'Select', 'egyptfoss' ); ?></option>
                    <?php
                      foreach ($ef_item_status as $status) {
                          $key = array_search($status, $ef_collaboration_item_status);
                          if($ef_document_status == $key)
                          {
                              $selected = "selected";
                          } 
                          else 
                              $selected = "";                        
                        echo("<option value='".array_search($status, $ef_collaboration_item_status)."' $selected >");
                        _e("$status", "egyptfoss");
                        echo ("</option>");
                      } ?>
                  </optgroup>
                </select>
                <div id="type_validate"></div>
          </div>
        </div> 
        <div class="form-group row <?php echo ($ef_document_status != 'published')?'hide':''; ?>" id="section_div">
        	<div class="col-md-12">                    
                <label for="section" class="label"><?php _e( 'Related to', 'egyptfoss' ); ?> </label>
                <select class="form-control" id="section" name="section" style="width:100%; ">
                  <optgroup>
                    <option value="" <?php echo ((!isset($ef_document_section) || $ef_document_section == '')?"selected":""); ?>><?php _e( 'Select', 'egyptfoss' ); ?></option>
                    <?php
                      //$sections = ef_load_menu_items_by_language();
                      global $ef_sections;
                      foreach ($ef_sections as $key=>$section)
                      {
                        $ef_sections[$key] = __($section,"egyptfoss");
                      }
                      asort($ef_sections);
                      foreach ($ef_sections as $key => $section) {
                        if( $key == 'collaboration-center' ) continue;
                        //$key = $section->post_name;
                        if($ef_document_section == $key)
                        {
                            $selected = "selected";
                        } 
                        else {
                            $selected = ""; 
                        }

                        //echo("<option value='".$section->post_name."' $selected >");
                        echo("<option value='".$key."' $selected >");
                        //_e("$section->post_title", "egyptfoss");
                        echo $section;
                        echo ("</option>");
                      } ?>
                  </optgroup>
                </select>
                <div id="type_validate"></div>
          </div>
        </div> 
        <?php } ?>
        <input type="hidden" name="action" value="edit_collaboration_center_document" />
        <input type="hidden" name="postid" value="<?php echo $document_id; ?>" />
        <?php wp_nonce_field( 'edit_collaboration_center_document' ); ?>
        <?php if($edit_enabled) { ?>
        <div class="form-group clearfix">
          <input type="submit" class="btn btn-primary rfloat" value="<?php _e("Save", "egyptfoss") ?>"/>
        </div>
        <?php } ?>
      </div>
      </form>  
    </div><!-- #primary -->
  </div>
</div>

<!-- confirm model -->
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

<?php get_footer(); ?>

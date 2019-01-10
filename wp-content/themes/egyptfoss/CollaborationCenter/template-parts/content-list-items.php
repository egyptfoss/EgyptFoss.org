<div  class="spaces-listing col-md-9 content-area <?php echo $view ?>-items">
  <?php wp_nonce_field( 'validate_space_document' ); ?>
  <?php if ($spaceTitle != "") { ?>
    <h2><?php echo $spaceTitle ?></h2>
  <?php } ?>
  <?php
  $messages = getMessageBySession("ef_collaboration_center_messages");
  if (isset($messages['success'])) {
    do_action('efb_init_alert_user');
    ?>
    <div class="alert alert-success" id="listingItemsMessages"><?php
      foreach ($messages['success'] as $success) {
        echo "<i class='fa fa-check'></i> " . $success . "<br/>";
      }
      ?>
    </div>
    <?php
  }
  if ($view != "shared" && current_user_can('add_new_ef_posts')) {
    ?>
    <?php if ($view == "space") { ?>
      <div class="row">
        <div class="col-md-12">
          <a class="btn btn-primary rfloat" id="new_space"><i class="fa fa-plus"></i> <?php echo __("New Space", "egyptfoss"); ?></a>
        </div>
      </div>
    <?php } ?>
    <?php if ($view == "document") { ?>
      <div class="row">
        <div class="col-md-12">
          <a class="btn btn-primary rfloat" href="<?php echo get_current_lang_page_by_template('CollaborationCenter/template-add-document.php', false, null, array($space_id)) ?>"><i class="fa fa-plus"></i> <?php echo __("New Document", "egyptfoss"); ?></a>
        </div>
      </div>
    <?php } ?>
  <?php } ?>
  <div class="nano">
    <div class="nano-content" id="SpacesAndDocumentsDiv">
      <?php
      if (empty($items) || empty($items->first())) {
        ?>
        <div class="emptyItems">
          <div class="empty-state-msg">
            <i class="fa fa-folder-open"></i>
            <br>
            <span><?php echo $emptyMsg ?></span>
          </div>
        </div>
        <?php
      } else {
        $spacesShared = array();
        foreach ($items as $item) {
          if ($view == "shared") {
            if ($item->is_space) {
              array_push($spacesShared, $item->ID);
            } else {
              if (in_array($item->item_ID, $spacesShared)) {
                continue;
              }
            }
          }
          include(locate_template('CollaborationCenter/template-parts/item.php'));
        }
      }
      ?>
      <!-- Rename Modal --->
      <div class="modal fade" id="rename-space" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><?php _e("Rename", "egyptfoss"); ?></h4>
            </div>
            <div class="modal-body">
              <div class="row form-group">
                <div class="col-md-12">
                  <input class="form-control" type="text" name="name" value="" autofocus="true">
                </div>
              </div>
              <div class="row form-group text-right">
                <div class="col-md-12">
                  <button type="button" class="btn btn-light" name="button"><?php _e("Cancel", "egyptfoss"); ?></button>
                  <button type="button" class="btn btn-primary" name="button"><?php _e("Save", "egyptfoss"); ?></button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Rename Modal End --->		
     <?php include(locate_template('CollaborationCenter/template-parts/inviteModal.php')); ?>
    </div>
  </div>
</div>
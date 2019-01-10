<div class="doc-revisions rfloat">

  <?php if (get_current_user_id() == $document_owner || $isItemSharedWithUser) { ?>
    <a href="#" class="dropdown-toggle btn btn-light" id="revisions-list" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-history"></i> <?php _e("Revisions", "egyptfoss") ?> <i class="fa fa-angle-down"></i></a>
    <ul class="revisions-list" aria-labelledby="revisions-list">
      <?php
      $revisions = CollaborationCenterItemHistory::getItemRevisions($document_id);
      if (!empty($revisions->toArray())) {
        foreach ($revisions as $revision) {
          $link = get_current_lang_page_by_template('CollaborationCenter/template-single-revision.php', false, null, array( $revision->ID ));
          ?>
          <li class="clearfix">
            <a href="<?php echo $link ?>" target="_blank" class="revision-date"><?php echo mysql2date('d/m/Y - h:i A', $revision->created_date) ?></a> <strong>[<?php echo __(ucfirst($revision->status), "egyptfoss") ?>]</strong>
            <br>
            <span class="revision-author">
              <i class="fa fa-user"></i> <a href="<?php echo home_url() . "/members/" . bp_core_get_username($revision->editor_id) . '/about/' ?>"><?php echo bp_core_get_user_displayname($revision->editor_id); ?></a>
            </span>
          </li>
        <?php }
      } else {
        ?>
        <li class="clearfix"><?php _e("there are no revisions", "egyptfoss"); ?></li>
      <?php } ?>
    <?php } ?>
  </ul>
</div>
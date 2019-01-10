<?php 
  $displayedUserId = (bp_displayed_user_id()) ? bp_displayed_user_id() : $_POST['displayedUserID'];
  $offset = (int)get_query_var('user_documents_offset', 0);
  $items = get_user_published_documents($displayedUserId, $offset);
  foreach ($items as $item) {
  $space_id = $item->item_ID;
  $link = get_current_lang_page_by_template('CollaborationCenter/template-single-document.php', false, null, array( $item->ID ));
  ?>
  <div class="document-row published-row document" id="<?php echo "document_" . $item->ID ?>">
    <div class="icon">
      <i class="fa fa-file"></i>
    </div>
    <div class="name space_title">
      <a href="<?php echo $link ?>"><?php echo $item->title ?></a>
      <br>     
      <span class="news-author documentOwner">
        <span class="doc-creator">
          <?php _e("Created by","egyptfoss"); 
            ?>
          <a href="<?php echo home_url() . "/members/" . bp_core_get_username($item->owner_id) . "/about/" ?>">
            <?php echo bp_core_get_user_displayname($item->owner_id); ?>
          </a>
        </span>
      </span>
    </div>
    <div class="modified-date">
      <span class="post-date" title="<?php echo mysql2date('j F Y', $item->created_date) ?>"><i class="fa fa-clock-o"></i> <?php echo mysql2date('j F Y', $item->created_date) ?></span>
    </div>
  </div>
<?php } ?>

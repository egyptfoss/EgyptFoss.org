<div class="col-md-3 docs-sidebar">
  <ul class="side-nav-items">
    <li class="<?php if ($view == "published") { ?> active <?php } ?>">
      <a href="<?php echo get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "published"); ?>"><i class="fa fa-folder"></i> <?php _e("Published Documents", "egyptfoss") ?> </a>
    </li>
    <?php if (is_user_logged_in()) { ?>
      <li class="<?php if ($view == "document" || $view == "space") { ?> active <?php } ?>">
        <a href="<?php echo get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "spaces"); ?>"><i class="fa fa-folder"></i> <?php _e("My Spaces", "egyptfoss") ?> </a>
      </li>
      <?php if (current_user_can('add_new_ef_posts')) { ?>
        <li class="<?php if ($view == "shared") { ?> active <?php } ?>">
          <a href="<?php echo get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "shared"); ?>"><i class="fa fa-users"></i> <?php _e("Shared with me", "egyptfoss") ?> </a>
        </li>
      <?php } ?>
    <?php } ?>
  </ul>
</div>
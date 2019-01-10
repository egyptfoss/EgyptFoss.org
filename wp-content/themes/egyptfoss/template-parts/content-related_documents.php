<?php
load_orm();
$collabCenter = new CollaborationCenterItem();

// get published documents by section
$documents = $collabCenter->getPublishedDocuments( $section_slug, "", "", 4 );
?>
<div class="related-docs-container rfloat">
   <a href="javascript:;" class="open-related btn btn-outline active rfloat"><?php _e( 'Related Documents', 'egyptfoss' ); ?> <i class="fa fa-angle-down"></i></a>
    <div class="related-panel">
           <ul class="clearfix">
              <?php if( count( $documents ) ): ?>
                  <?php foreach( $documents as $document ): ?>
                   <li>
                       <div class="file-icon">
                           <i class="fa fa-file"></i>
                       </div>
                       <div class="file-name">
                           <?php
                            $link = get_current_lang_page_by_template('CollaborationCenter/template-single-document.php', false, null, array( $document->item_ID ));
                           ?>
                           <a href="<?php echo $link; ?>"><?php echo shorten_string_v2( $document->title, 70 ); ?></a>
                           <br>
                           <small class="post-date">
                              <i class="fa fa-clock-o"></i>
                              <?php echo mysql2date( 'j F Y', $document->created_date ); ?>
                              <br>
                              <i class="fa fa-user"></i> 
                              <a href="<?php echo home_url() . "/members/" . bp_core_get_username( $document->owner ) . "/about/" ?>" class="doc-author-link">
                                <?php echo bp_core_get_user_displayname( $document->owner ); ?>
                              </a>
                           </small>
                       </div>
                   </li>
                 <?php endforeach; ?> 
                 <li class="text-center"><a href="<?php echo get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "published") . "?section=$section_slug"; ?>" class="see-more-link"><?php _e( 'Show More', 'egyptfoss' ); ?></a></li>
             <?php else: ?>
                <li class="no-rdocuments"><?php _e( 'No published documents in this section', 'egyptfoss' ); ?> </li>
             <?php endif; ?>
          </ul>
    </div>
</div>
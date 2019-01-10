<?php

$link = "#";
$itemType = $item->is_space ? "space" : "document";
$item_id = ($item->ID)?$item->ID:$item->id;
$space_id = $item->item_ID;
if ($itemType == "space" && $view == "shared") {
  $link = get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "shared_space_content", array($item_id));
}
if ($view == "space" && $itemType == "space") {
  $link = get_current_lang_page_by_template('CollaborationCenter/template-listing-items.php', false, "space_content", array($item_id));
}else if($itemType == "document")
{
  $link = get_current_lang_page_by_template('CollaborationCenter/template-edit-document.php', false, null, array($space_id,$item_id));
}
?>
<div class="document-row <?php echo $itemType ?>" id="<?php echo $itemType."_".$item_id ?>">
  <div class="icon">
    <i class="fa <?php if ($itemType == "space") { ?> fa-folder <?php } else { ?> fa-file <?php } ?>"></i>
  </div>
  <div class="name space_title">
<a href="<?php echo $link ?>"><?php echo $item->title ?></a>
  <?php if ($itemType != "document" && $view != "shared") { ?>
    <a class="rename_space inline-rename" data-old-title ="<?php echo $item->title ?>" data-id ="<?php echo $item_id ?>" ><i class="fa fa-pencil"></i> <?php // _e("Rename", "egyptfoss"); ?></a>
      <?php
    } ?>
<?php if ($itemType == "document") {?> <strong>[<?php echo __(ucfirst($item->status),"egyptfoss"); ?>]</strong> <?php } ?>
    <br>
    <?php if ($itemType != "document") { ?>
    <small>
    	<?php echo count($item->documents()->get()) ." ". _nx('file','files',count($item->documents()->get()),'indefinite','egyptfoss') ?>
    </small>
      <?php } ?>
       <?php if($view == "shared" && $itemType == "document"){ ?>
      <span class="news-author documentOwner">
          <span class="doc-creator">
           <?php _e("Created by","egyptfoss") ?>
            <a href="<?php echo home_url() . "/members/" . bp_core_get_username($item->owner_id)."/about/" ?>">
    <?php echo bp_core_get_user_displayname($item->owner_id); ?>
            </a>
          </span>
        </span>
     <?php } ?>

  </div> 
  <div class="modified-date">
    <span class="post-date" title="<?php echo mysql2date('j F Y',$item->modified_date) ?>"><i class="fa fa-clock-o"></i> <?php echo mysql2date('j F Y',$item->modified_date) ?></span>
  </div>
  <div class="contributers">
      <?php $groupContributors = $item->getGroupContributors( get_locale() ); ?>
    <span class="contrib-counter" id="<?php echo "contributers_".$item_id ?>" <?php if( empty( $item->getNoOfContributers() ) ): ?>style="display: none;"<?php endif; ?>><i class="fa fa-user"></i> <?php echo $item->getNoOfContributers(); ?></span>
    <span class="group-share-icon" id="<?php echo "group_contributers_".$item_id ?>" <?php if ( empty( count( $groupContributors ) ) ): ?>style="display: none;"<?php endif; ?>>
        <a data-toggle="popover" data-target="#group-share" data-placement="bottom"  data-html="true" data-container="body" data-trigger="hover">
          <i class="fa fa-group"></i>
        </a>
        <div class="group-shared hidden">
            <!--popover-content-->
            <i class="fa fa-warning"></i> <?php _e( 'This Document is Shared with connections who care about', 'egyptfoss' ); ?>
            <strong class="contrib-strings">
                <?php 
                $test = 'Developer';
                    $gs = array();
                    foreach( $groupContributors as $g ) {
                      $trans = __( $g->name, 'egyptfoss' );
                      if( $locale == 'ar' && isset( $ar_sub_types[ $g->name ] ) ) {
                        $trans = $ar_sub_types[ $g->name ];
                      }
                      else if( $locale == 'en' && isset( $en_sub_types[ $g->name ] ) ) {
                        $trans = $en_sub_types[ $g->name ];
                      }
                      $gs[] = $trans;
                    }
                    echo implode( ', ', $gs );
                ?>
            </strong>
        </div>
    </span>
  </div>

  <div class="options">   
 <?php if(get_current_user_id() == $item->owner_id) { ?>
      <a href="#" class="remove_space rfloat" data-space="<?php echo ($itemType == "space")?1:0; ?>" data-id="<?php echo $item_id; ?>" data-title="<?php echo $item->title; ?>"><i class="fa fa-trash"></i> <?php _e("Remove", "egyptfoss") ?></a>
      <a href="#" class="invite-space-document rfloat" data-id="<?php echo $item_id; ?>" data-title="<?php echo $item->title; ?>" data-toggle="modal" data-target="#invite-space-document"><i class="fa fa-user-plus"></i> <?php _e("Share", "egyptfoss") ?></a>
    <?php } ?>
  </div> 
</div>

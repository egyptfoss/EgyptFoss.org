<?php
/**
 * Template Name: View Collaboration Document
 *
 * @package egyptfoss
 */
get_header();
load_orm();
$document_id = efGetValueFromUrlByKey("published");

// get latest revision for original document
$item = CollaborationCenterItemHistory::where( 
                array( 
                        'item_id'   =>  $document_id,
                        'status'    =>  'published'
                )
            )
            ->orderBy( 'created_date', 'DESC' )
            ->first();

// not exist item, redirect to error page
if( empty( $item ) ) {
    include( get_query_template( '404' ) );
    header( 'HTTP/1.0 404 Not Found' );
    exit; 
}
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <?php echo CollaborationBreadCrumb("published"); ?>
        <h1><?php echo $item->title ?></h1>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row" id="create-doc">
    <div class="single-news-meta single-doc-meta clearfix" >
         <div class="news-author lfloat ">
      <?php $document_owner = $item->document()->first()->owner_id; ?>
              <?php echo get_avatar( $document_owner, 32 ); ?>
                <span>
                    <?php if(bp_core_get_username($document_owner) != '') { ?>
                    <a href="<?php echo home_url()."/members/".bp_core_get_username($document_owner).'/about/' ?>"> 
                        <?php echo bp_core_get_user_displayname($document_owner); ?> 
                    </a>
                <?php } else { echo bp_core_get_user_displayname($document_owner); } ?>
                </span>
                 <span class="post-date" title="<?php echo mysql2date('j F Y',$item->created_date) ?>"><i class="fa fa-clock-o"></i> <?php echo mysql2date('j F Y',$item->created_date) ?></span>
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


<?php
get_header();
load_orm();
$limit = constant('ef_expert_thought_per_page');
$expertThought = new ExpertThought();
$thoughts = $expertThought->getPublishedThoughts();
$totalThoughts = count($thoughts);
$thoughts = $thoughts->take($limit);
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1>
          <?php echo _x( "Expert Thoughts", 'header', "egyptfoss" ); ?>
        </h1>
        <?php 
            // this variable is used in related documents templates
            $section_slug = 'expert-thought';
            include( locate_template( 'template-parts/content-related_documents.php' ) );
        ?>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row">
    <div  class="col-md-12 content-area">
      <div class="filter-btns">
        <?php if ( is_user_logged_in() ):
          $is_expert = get_user_meta( get_current_user_id(), "is_expert" ,true );
          if ( $is_expert ): ?>
              <a href="<?php echo get_current_lang_page_by_template( "template-add-expert-thought.php" ); ?>" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> <?php _e( "New Post", "egyptfoss" ); ?></a>
          <?php endif; ?>
        <?php endif; ?>
      </div>
      <div class="clear"></div>
      <?php if($totalThoughts != 0) { ?>
      <div class="single-column-content" id="expertThoughtList">
        <?php include(locate_template('template-parts/content-expert_cards.php')); ?> 
      </div>
      <div class=" pagination-row clearfix">
        <a href="javascript:void(0);" onclick="return false;" class="<?php if ($totalThoughts <= $limit) { ?> hidden <?php } ?> btn btn-load-more" id="load_more_thoughts" data-offset="<?php echo $limit ?>" data-count="<?php echo $totalThoughts ?>">
          <?php _e("Load more...", "egyptfoss"); ?>
        </a>
        <i class="fa fa-circle-o-notch fa-spin hidden ef-thoughts-list-spinner"></i>
      </div>
      <?php }else{ ?>
           <span><?php _e("There are no published Thoughts Yet.","egyptfoss"); ?></span>
      <?php } ?>
    </div>
  </div><!-- #primary -->
</div>
<?php get_footer(); ?>

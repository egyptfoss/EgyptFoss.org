<?php
/**
 * Template Name: Success Stories.
 *
 * @package egyptfoss
 */

$getParams = $_GET;
get_header(); ?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
          <h1 class="entry-title"><?php _e('Success Stories','egyptfoss') ?></h1>
          <?php 
            // this variable is used in related documents templates
            $section_slug = 'success-story';
            include( locate_template( 'template-parts/content-related_documents.php' ) );
          ?>
      </div>
    </div>
  </div>
</header>
<?php
    $list_success_stories = count_success_stories();
    
    $current_category = -1;
    
    //check query string
    if( !empty( $_GET['category'] ) ) {
        if( $_GET['category'] != "all" ) {
            //get id of selected category
            $current_category = ef_return_taxonomy_id_by_name(html_entity_decode($_GET['category']),'success_story_category');
            $count = ef_get_count_per_success_story_category( $list_success_stories, $current_category );
        }
    }
    
    if( !isset( $count ) ) {
      $count = count($list_success_stories);
    }
    
?>
<div class="container">
<div class="page-content">
    <div class="row">
            <div class="col-md-3 menu-left">
                    <?php get_template_part('template-parts/content', 'listing_success_story_left_menu'); ?>
                </div>
        <div id="primary" class="content-area col-md-9">
           <div class="row">
            <div class="col-md-12 add-new-bar">
                              <div class="filter-btns">
                  <?php if ( !is_user_logged_in() ) { ?>
                    <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addsuccessstory&redirect_to='.get_current_lang_page_by_template("template-add-success-story.php") ); ?>" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '.__("Success Story", "egyptfoss"); ?></a>
                  <?php } else if (current_user_can('add_new_ef_posts')) { ?>
                    <a href="<?php echo get_current_lang_page_by_template("template-add-success-story.php"); ?>" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '.__("Success Story", "egyptfoss"); ?></a>
                  <?php } else { ?>
                    <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                    <a href="javascript:void(0)" class="btn btn-primary disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '.__("Success Story", "egyptfoss"); ?></a>
                  <?php } ?>
                </div>
                      <div class="ef-results-meta" <?php echo !$count?'style="display:none;"':''; ?>>
              <?php
                printf( 
                    '%s <span class="ef-results-count">%s</span> '.
                    '%s <span class="ef-total-count">%s</span> %s '.
                    '<span class="ef-category" %s>'.
                    '%s <span class="ef-category-name">"%s"</span>'.
                    '</span>',
                    __( 'Showing', 'egyptfoss' ),
                    (constant("ef_success_story_per_page") > $count )?$count:constant("ef_success_story_per_page"),
                    __('of', 'egyptfoss'),
                    $count,
                    __('results', 'egyptfoss'),
                    ($current_category == -1)?'style="display:none;"':'',
                    __( 'From', 'egyptfoss' ),
                    ($current_category != -1)?html_entity_decode($_GET['category']):$current_category
                );
              ?>
            </div>
            </div>
          </div>
        <div class="row">
          <div class="clear"></div>
<div class="col-md-12">
  <?php
      set_query_var('ef_listing_success_stories_offset', $_POST['offset']);
     // if ($count > 0){
          ?>
      <div class="loading-overlay hidden">
          <div class="spinner">
              <div class="double-bounce1"></div>
              <div class="double-bounce2"></div>
          </div>
      </div>
      <div class="row" id="load_success_stories_by_ajax_container">
         <div class="col-md-12">
          <?php get_template_part('template-parts/content', 'listing_success_stories'); ?>
         </div>
        </div><?php
      ?><div class="pagination-row clearfix view-more">
          <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more" id="load_more_listing_success_stories" data-offset="<?php echo constant('ef_success_story_per_page'); ?>" data-count="<?php echo $count ; ?>">
          <?php _e("Load more...", "egyptfoss"); ?>
        </a>
        <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
      </div><?php
    //} else { ?>
    <!-- <div class="empty-state-msg">
             <i class="fa fa-3x fa-file-text"></i>
             <br>
             <p>
                  <?php _e("There are no Success Stories yet, ", "egyptfoss"); ?>
      <?php if ( !is_user_logged_in() ) {?>
        <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addsuccessstory&redirect_to='.get_current_lang_page_by_template("template-add-success-story.php") ); ?>"> <?php echo __("Suggest", "egyptfoss") .' '.__("Success Story", "egyptfoss"); ?></a>
      <?php } else if (current_user_can('add_new_ef_posts')) { ?>
        <a href="<?php echo get_current_lang_page_by_template('template-add-success-story.php') ?> "> <?php echo __("Suggest", "egyptfoss") .' '.__("Success Story", "egyptfoss"); ?></a>
      <?php } else { ?>
        <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
        <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><?php echo __("Suggest", "egyptfoss") .' '.__("Success Story", "egyptfoss"); ?></a>
      <?php } ?>
             </p>

     </div> -->
      <?php
   // }?>
</div>
      </div>
  </div><!-- #primary -->

  </div>
</div>
</div>

<?php get_footer();?>
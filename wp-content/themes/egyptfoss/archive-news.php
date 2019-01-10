<?php
/**
 * Template Name:Listing News.
 *
 * @package egyptfoss
 */
get_header(); ?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
          <h1 class="entry-title"><?php _e('News','egyptfoss') ?></h1>
          <?php 
            // this variable is used in related documents templates
            $section_slug = 'news';
            include( locate_template( 'template-parts/content-related_documents.php' ) );
          ?>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
  <div class="row">
    <div id="primary" class="col-md-12 news-listing">
      <div class="filter-btns">
        <?php if ( !is_user_logged_in() ) { ?>
          <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addnews&redirect_to='.get_current_lang_page_by_template("template-manage-news.php") ); ?>" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '._x("News", "up", "egyptfoss"); ?></a>
        <?php } else if (current_user_can('add_new_ef_posts')) { ?>
          <a href="<?php echo get_current_lang_page_by_template("template-manage-news.php"); ?>" class="btn btn-primary rfloat"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '._x("News", "up", "egyptfoss"); ?></a>
        <?php } else { ?>
          <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
          <a href="javascript:void(0)" class="btn btn-primary disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") .' '._x("News", "up", "egyptfoss"); ?></a>
        <?php } ?>
      </div>
      <div class="clear"></div>
      <ul class="news-grid">
      <?php
      set_query_var('ef_listing_news_offset', $_POST['offset']);
      $list_news = count_news();
      $count = count($list_news);
      if ($count > 0){
          ?><div class="row" id="load_news_by_ajax_container">
            <?php get_template_part('template-parts/content', 'listing_news'); ?>
          </div><?php
        ?><div class="pagination-row clearfix view-more">
            <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more" id="load_more_listing_news" data-offset="<?php echo constant('ef_news_per_page'); ?>" data-count=<?php echo $count ; ?>>
            <?php _e("Load more...", "egyptfoss"); ?>
          </a>
          <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
        </div><?php
      } else{
        _e("There are no News yet, ", "egyptfoss"); ?>
        <?php if ( !is_user_logged_in() ) { ?>
          <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addnews&redirect_to='.get_current_lang_page_by_template("template-manage-news.php") ); ?>"><?php echo __("Suggest", "egyptfoss") .' '._x("News", "up", "egyptfoss"); ?></a>
        <?php } else if (current_user_can('add_new_ef_posts')) { ?>
          <a href="<?php echo get_current_lang_page_by_template('template-manage-news.php') ?> "> <?php echo __("Suggest", "egyptfoss") .' '._x("News", "up", "egyptfoss"); ?></a>
        <?php } else { ?>
          <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
          <a href="javascript:void(0)" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><?php echo __("Suggest", "egyptfoss") .' '._x("News", "up", "egyptfoss"); ?></a>
        <?php } ?>
        <?php
      }?>
      </ul>
    </div><!-- #primary -->
  </div>
</div>

<?php get_footer();?>

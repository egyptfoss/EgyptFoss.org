<?php
/**
 * Template Name:Listing News.
 *
 * @package egyptfoss
 */
get_header(); 

//Load Quiz Categories
$categories = get_terms('quiz_categories', array('hide_empty' => 0)); 
if(pll_current_language() == "ar")
{
  $sorted_ind = array();
  foreach ($categories as $key => $row)
  {
    $sorted_ind[$key] = $row->name_ar;
  }
  array_multisort($sorted_ind, SORT_ASC, $categories);
}

$current_category = -1;
//check query string
if($_GET['category'] != null){
  if($_GET['category'] != "all")
  {
    //get id of selected category
    $current_category = ef_return_category_id_by_name($_GET['category'], "quiz_categories");
    set_query_var('ef_listing_awareness_center_category_id', $current_category);
  }
}

//Load quizzes count
$args = array(
    'lang' => pll_current_language(),
    'current_user' => get_current_user_id(),
    'category' => $current_category
);
$count = count(count_quizes($args));
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
          <h1 class="entry-title"><?php _e('Awareness Center','egyptfoss') ?></h1>
          <?php 
            // this variable is used in related documents templates
            $section_slug = 'quiz';
            include( locate_template( 'template-parts/content-related_documents.php' ) );
          ?>          
      </div>
    </div>
  </div>
</header><!-- .entry-header -->

<div class="container">
	<div class="row">
    <div class="col-md-3">
      <div class="side-menu">
        <ul class="categories-list industry-list">
            <li <?php echo ($current_category == -1)?'class="active"':''; ?>><a href="" onclick="return false;" data-slug="all" class="trigger_click" data-id="-1"><?php _e("All","egyptfoss"); ?></a> <!-- <span class="count"><?php echo $count; ?></span>--></li>
           <?php foreach($categories as $category) { 
              if($lang == "ar")
              {
                if($category->name_ar != '') {
                    $category->name = $category->name_ar;
                }
              }
            ?>   
           <li <?php echo ($current_category == $category->term_id)?'class="active"':''; ?>><a href="" onclick="return false;" data-slug="<?php echo rawurlencode($category->name); ?>" class="trigger_click" data-id="<?php echo $category->term_id; ?>"><?php echo $category->name; ?></a></li>
           <?php } ?>
        </ul>
      </div>
    </div>
  <div id="primary" class="content-area col-md-9">
    <div class="row filter-bar">
        <div class="col-md-5 rfloat">
            <div class="filter-btns">
                <?php if (!is_user_logged_in()) { ?>
                  <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addfeedback&redirect_to='.get_current_lang_page_by_template("template-add-feedback.php") ); ?>?section=quiz" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") . ' ' . _x("Quiz", "up", "egyptfoss"); ?></a>
                <?php } else if (current_user_can('add_new_ef_posts')) { ?>
                  <a href="<?php echo get_current_lang_page_by_template("template-add-feedback.php"); ?>?section=quiz" class="btn btn-primary"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") . ' ' . _x("Quiz", "up", "egyptfoss"); ?></a>
                <?php } else { ?>
                  <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                  <a href="javascript:void(0)" class="btn btn-primary disabled" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>"><i class="fa fa-plus"></i> <?php echo __("Suggest", "egyptfoss") . ' ' . _x("Quiz", "up", "egyptfoss"); ?></a>
                <?php } ?>
            </div>
        </div>
    </div>
  <?php if($count > 0) { ?>
    <div class="row">
      <div class="ef-results-meta" <?php echo !$count?'style="display:none;"':''; ?>>
          <?php
            printf( 
                '%s <span class="ef-results-count">%s</span> '.
                '%s <span class="ef-total-count">%s</span> %s '.
                '<span class="ef-category" %s>'.
                '%s <span class="ef-category-name">"%s"</span>'.
                '</span>',
                __( 'Showing', 'egyptfoss' ),
                (constant("ef_awareness_quiz_per_page") > $count )?$count:constant("ef_awareness_quiz_per_page"),
                __('of', 'egyptfoss'),
                $count,
                __('results', 'egyptfoss'),
                ($current_category == -1)?'style="display:none;"':'',
                __( 'From', 'egyptfoss' ),
                ($current_category != -1)?$_GET['category']:$current_category
            );
          ?>
      </div>
      <div class="col-md-12">
        <div class="surveys-list" id="load_awareness_center_by_ajax_container">
            <?php //if (is_user_logged_in() && !current_user_can('add_new_ef_posts')) { ?>
              <!--<div class="listing-overlay">
                  <div class="empty-state-msg">
                      <i class="fa fa-warning"></i>
                      <h4><?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?></h4>
                  </div>
              </div>-->
            <?php //} ?>
            <?php  get_template_part('template-parts/quizes/content', 'listing_awareness_center'); ?>
        </div>
      </div>
    </div>
    <div class="pagination-row clearfix view-more collapse">
        <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more" id="load_more_listing_awareness_center" data-offset="<?php echo constant('ef_awareness_quiz_per_page'); ?>" data-count=<?php echo $count ; ?>>
        <?php _e("Load more...", "egyptfoss"); ?>
      </a>
      <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
    </div>      
    <!--<div class="row text-center">
      <div class="col-md-12">
          <button type="button" class="btn btn-primary loadmore-btn" id="load" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Please wait...">Load more...</button>
      </div>
    </div>  -->    
  <?php } else { ?>
    <!-- Empty State - Remove class hidden to show -->
    <div class="row empty-state">
        <div class="empty-state-msg">
            <i class="fa fa-question-circle fa-4x"></i>
            <br>
            <h3><?php _e("There are no quizzes in this category","egyptfoss"); ?></h3>
        </div>
    </div>
    <!-- Empty State End -->
  <?php } ?>
  </div><!-- #primary -->
	</div>
</div>

<?php get_footer(); ?>

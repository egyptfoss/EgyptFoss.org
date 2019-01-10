<?php
/**
 * Template Name: Requests Center Listing.
 *
 * @package egyptfoss
 */

$getParams = $_GET;
get_header();
$current_type = -1;
  if($_GET['type'] != null){
    if($_GET['type'] != "all"){
      $current_type = ef_get_taxonomy_id_by_name($_GET['type'], 'request_center_type');
      set_query_var('ef_listing_request_type_id', $current_type);
  }
}

$current_target = -1;
if($_GET['target'] != null){
  if($_GET['target'] != "all"){
    $current_target = ef_get_taxonomy_id_by_name($_GET['target'], 'target_bussiness_relationship');
    set_query_var('ef_listing_request_target_id', $current_target);
  }
}
$current_theme = -1;
if($_GET['theme'] != null){
  if($_GET['theme'] != "all"){
    $current_theme = ef_get_taxonomy_id_by_name($_GET['theme'], 'theme');
    set_query_var('ef_listing_request_theme_id', $current_theme);
  }
}

wp_enqueue_script( 'listing_request_center-js', get_stylesheet_directory_uri() . '/js/listing_request_center.js', array('jquery'), '', true);
wp_localize_script('listing_request_center-js', 'ef_request_center', array("per_page" => constant("ef_request_center_per_page")));
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="entry-title"> <?php echo _e("Request Center", "egyptfoss"); ?> </h1>
        <?php 
            // this variable is used in related documents templates
            $section_slug = 'request-center';
            include( locate_template( 'template-parts/content-related_documents.php' ) );
        ?>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->
<?php
  $args = array(
    "post_status" => "publish",
    "post_type" => "request_center",
    "offset" => 0,
    "theme_id" => $current_theme,
    "type_id" => $current_type,
    "target_id" => $current_target
  );
  $list_request_center = count_request_center($args);
//  $count = count($list_request_center);
?>
<div class="container">
  <div class="row ft-padding-top">
    <div class="col-md-12">
      <?php if (!isset($_COOKIE['welcome-request-center']) || $_COOKIE['welcome-request-center'] != 'dismiss') { ?>
        <div class="well alert alert-dismissable text-center add-story-intro fade in">
          <div class="row">
            <button type="button" class="close dismiss-welcome" cname="welcome-request-center" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="row">
            <div class="col-md-12">
              <h1 class="color-primary"><?php _e("Welcome To Requests Center", "egyptfoss") ?></h1>
                <p>
                   <?php _e("In the Request Center you can submit various types of requests and, once approved, they will be published so that interested members can respond to them.", "egyptfoss") ?>
                </p>
            </div>
          </div>
          <div class="row">
            <a class="btn btn-primary dismiss-welcome" cname="welcome-request-center" data-dismiss="alert"><?php _e("OK","egyptfoss") ?></a>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-3">
      <div class="side-menu">
        <a href="#" class="open-list visible-xs"><h3><i class="fa fa-list-ul"></i> <?php _e('Browse By Request Type','egyptfoss') ?></h3></a>
        <ul class="type-list hidden-xs collapsable-list">
          <li <?php echo ($current_type == -1)?'class="active"':''; ?>>
            <a href="" onclick="return false;" data-slug="all" data-name="all" class="trigger_click" data-id="-1">
              <img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/listing-all.svg" width="22" alt="<?php _e('All','egyptfoss') ?>">
              <?php _e("All","egyptfoss"); ?>
            </a>
          </li>
          <?php $terms_data = get_terms('request_center_type', array('hide_empty' => 0)); 
            foreach ($terms_data as $term_data) {?>
              <li <?php echo ($current_type == $term_data->term_id)?'class="active"':''; ?>>
                <a href="" data-slug="<?php echo rawurlencode($term_data->slug); ?>" data-name="<?php echo rawurlencode($term_data->name); ?>" data-title="<?php echo $term_data->name; ?>" class="trigger_click" data-id="<?php echo $term_data->term_id; ?>">
                <img class="icon" src="<?php echo get_template_directory_uri(); ?>/img/<?php echo $term_data->slug; ?>_icon.svg" width="22" alt="<?php _e('Busines Releations','egyptfoss') ?>">
                  <?php echo $term_data->name; ?>
                </a>
              </li>
          <?php } ?>
<!--          <li>
            <a href="#"><img class="icon" src="<?php // echo get_template_directory_uri(); ?>/img/service_icon.svg" width="22" alt="<?php // _e('Service Request','egyptfoss') ?>">
                <?php // _e('Service Request','egyptfoss') ?></a>
          </li>
          <li>
            <a href="#"><img class="icon" src="<?php // echo get_template_directory_uri(); ?>/img/business_relation_icon.svg" width="22" alt="<?php // _e('Busines Releations','egyptfoss') ?>">
                <?php // _e('Busines Releations','egyptfoss') ?></a>
          </li>
          <li>
            <a href="#"><img class="icon" src="<?php // echo get_template_directory_uri(); ?>/img/support_icon.svg" width="22" alt="<?php // _e('Support Requests','egyptfoss') ?>">
                <?php // _e('Support Requests','egyptfoss') ?></a>
          </li>
          <li>
            <a href="#"><img class="icon" src="<?php // echo get_template_directory_uri(); ?>/img/product_icon.svg" width="22" alt="<?php // _e('Product Requests','egyptfoss') ?>">
                <?php // _e('Products Requests','egyptfoss') ?></a>
          </li>
          <li>
            <a href="#"><img class="icon" src="<?php // echo get_template_directory_uri(); ?>/img/resource_icon.svg" width="22" alt="<?php // _e('Resources Requests','egyptfoss') ?>">
                <?php // _e('Resources Requests','egyptfoss') ?></a>
          </li>
          <li>
            <a href="#"><img class="icon" src="<?php // echo get_template_directory_uri(); ?>/img/data_icon.svg" width="22" alt="<?php // _e('Datasets Requests','egyptfoss') ?>">
                <?php // _e('Datasets Requests','egyptfoss') ?></a>
          </li>-->
        </ul>
      </div>
    </div>

    <div id="primary" class="content-area col-md-9">
      <div class="row">
        <div class="col-md-12 ui-options-buttons">
          <?php if ( !is_user_logged_in() ) { ?>
            <a href="<?php echo home_url( pll_current_language().'/login/?redirected=addrequestcenter&redirect_to='.get_current_lang_page_by_template("template-add-request-center.php") ); ?>" class="btn btn-primary rfloat">
              <i class="fa fa-plus"></i> 
              <?php echo __("New Request", "egyptfoss"); ?>
            </a>
          <?php } else if (current_user_can('add_new_ef_posts')) { ?>
            <a href="<?php echo get_current_lang_page_by_template("template-add-request-center.php"); ?>" class="btn btn-primary rfloat">
              <i class="fa fa-plus"></i> 
              <?php echo __("New Request", "egyptfoss"); ?>
            </a>
          <?php } else { ?>
            <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
            <a href="javascript:void(0)" class="btn btn-primary disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">
             <i class="fa fa-plus"></i>
             <?php echo __("New Request", "egyptfoss"); ?>
            </a>
          <?php } ?>
               <div class="ef-results-meta" <?php echo !$list_request_center?'style="display:none;"':''; ?>>
            <?php
              printf( 
                  '%s <span class="ef-results-count">%s</span> '.
                  '%s <span class="ef-total-count">%s</span> %s '.
                  '<span class="ef-category" %s>'.
                  '%s <span class="ef-category-name">"%s"</span>'.
                  '</span>',
                  __( 'Showing', 'egyptfoss' ),
                  (constant("ef_request_center_per_page") > $list_request_center )?$list_request_center:constant("ef_request_center_per_page"),
                  __('of', 'egyptfoss'),
                  $list_request_center,
                  __('results', 'egyptfoss'),
                  ($current_type == -1)?'style="display:none;"':'',
                  __( 'From', 'egyptfoss' ),
                  ($current_type != -1)?$_GET['type']:$current_type
              );
            ?>
        </div>
        </div>
        <div class="col-md-12">
          <section class="filter-nav" id="filter-products">
            <div class="form-group row">
              <div class="col-md-5 type-filter">
                <select id="target" class="form-control technologies custom-select2 topFilters"
                        style="width:100%;" data-placeholder="<?php _e("Target Relationship", "egyptfoss"); ?> ">
                  <option hidden="hidden"></option>
                  <optgroup>   
                    <?php $terms_data = get_terms('target_bussiness_relationship', array('hide_empty' => 0)); 
                      foreach ($terms_data as $term_data) {?>
                        <option <?php echo ($current_target == $term_data->term_id)?'selected="selected"':''; ?>
                            data-slug="<?php echo $term_data->slug  ?>" value="<?php echo $term_data->term_id ?>">
                          <?php echo $term_data->name; ?>
                        </option>
                    <?php } ?>
                  </optgroup>
                </select>
              </div>
              <div class="col-md-5 type-filter">
                <select id="theme" class="form-control technologies custom-select2 topFilters"
                        style="width:100%;" data-placeholder=<?php _e("Theme","egyptfoss"); ?>>
                  <option hidden="hidden"></option>
                  <optgroup>   
                    <?php $terms_data = get_terms('theme', array('hide_empty' => 0)); 
                      foreach ($terms_data as $term_data) {?>
                        <option <?php echo ($current_theme == $term_data->term_id)?'selected="selected"':''; ?>
                            data-slug="<?php echo $term_data->slug  ?>" value="<?php echo $term_data->term_id ?>">
                          <?php echo $term_data->name; ?>
                        </option>
                    <?php } ?>
                  </optgroup>
                </select>
              </div>

              <div class="col-md-2 ui-custom-reset rfloat">
                <button class="btn btn-link reset-filters rfloat"><i class="fa fa-remove"></i> <?php _e('Reset', 'egyptfoss') ?> </button>
              </div>
            </div>
          </section>
        </div>
      </div>

      <div class="row">
        <?php set_query_var('ef_listing_datasets_offset', $_POST['offset']); ?>
        <div class="col-md-12"  id="load_requests_by_ajax_container">
          <?php get_template_part('template-parts/content', 'listing_request_center'); ?>
        </div> <!-- end of col-md-12 -->

        <div class="pagination-row clearfix view-more">
          <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more" 
            id="load_more_listing_requests" data-offset="<?php echo constant('ef_request_center_per_page'); ?>" data-count=<?php echo $list_request_center ; ?>>
            <?php _e("Load more...", "egyptfoss"); ?>
          </a>
          <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
        </div>
      </div> <!-- end of row -->
    </div><!-- #primary -->
  </div>
</div> <!-- end of container -->
<?php get_footer();?>

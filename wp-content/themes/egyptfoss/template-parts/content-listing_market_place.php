<?php
$category = '-1';
$technology = '-1';
$theme = '-1';
$type = '-1';
$subtype = '-1';
$months = ef_return_arabic_months();

if( get_query_var( 'ef_listing_service_category') ) {
  $category = get_query_var( 'ef_listing_service_category' );
}
if( get_query_var( 'ef_listing_service_technology_id' ) ) {
  $technology = get_query_var( 'ef_listing_service_technology_id' );
}
if( get_query_var( 'ef_listing_service_theme_id' ) ) {
  $theme = get_query_var( 'ef_listing_service_theme_id' );
}
if( get_query_var( 'ef_listing_service_type_id' ) ) {
  $type = get_query_var( 'ef_listing_service_type_id' );
}
if( get_query_var( 'ef_listing_service_subtype_id' ) ) {
  $subtype = get_query_var( 'ef_listing_service_subtype_id' );
}

$args = array(
    "post_status"   => "publish",
    "post_type"     => "service",
    "offset"        => 0,
    "category"      => $category,
    "technology_id" => $technology,
    "theme_id"      => $theme,
    "type"          => $type,
    "subtype"       => $subtype
);

$lang = pll_current_language();

if( isset( $services_count ) && $services_count == 0 ) {
  $services = NULL;
}
else {
  $services = ef_get_services( $args );
}

if ($services):
  foreach ( $services as $service ):
    $service_id = $service->ID;
    $user_id = $service->post_author;
    $meta = get_post_custom( $service_id );
    
    $s_user_meta = get_user_meta( $user_id, "registration_data", true );
    $user_meta = unserialize( $s_user_meta );
    $type = ($user_meta['type'] == 'Entity') ? 'Entity' : 'Individual';

    $reviewers_count = get_post_meta($service_id, 'reviewers_count', true);
    $reviewers_count = ($reviewers_count == NULL) ? 0 : $reviewers_count;
    $rate = get_post_meta($service_id, 'rate', true);
    $rate = ($rate == NULL) ? 0 : $rate;
    $terms = wp_get_post_terms( $service_id, 'service_category');
    $category_name = '';
    if(!empty($terms)) {
      $term = $terms[0];
      $category_name = ($lang == 'ar') ? $term->name_ar : $term->name;
    }
    $is_top_service = get_post_meta( $service_id, 'efb_is_top_service', true );
    ?>
    <div class="service-card">
      <div class="inner">
        <div class="service-cover">
          <?php if( $is_top_service ): ?>
            <img class="product-badge" src="<?php echo get_template_directory_uri(); ?>/img/featured-icon.png" alt="<?php _e("Top service", 'egyptfoss'); ?>" style="position:absolute;top:5px;left:5px;">
          <?php endif; ?>
          <?php if ( has_post_thumbnail( $service_id ) ): ?>
            <img class="icon" src="<?php echo get_the_post_thumbnail_url( $service->ID , 'medium-img' ); ?>">
          <?php else: ?>
            <img class="icon" src="<?php echo get_template_directory_uri() . '/img/empty_service_cover.png'; ?>">
          <?php endif; ?>
        </div>
        <div class="card-content">
          <h4><a href="<?php echo get_permalink_by_lang( $service_id, '/marketplace/services/' ); ?>"><?php echo get_the_title( $service_id ); ?></a></h4>
          <small><i class="fa fa-<?php  echo ( $type == 'Entity' )?'building-o':'user'; ?>"></i> <a class="service-offeredby" href="<?php echo bp_core_get_user_domain( $user_id ) . 'about'; ?>"><?php echo $service->display_name; ?></a></small>
          <br/><small><i class="fa fa-folder-open-o"></i> <small class="category_trim"> <?php echo $category_name; ?></small></small>
        </div>
      </div>
      <div class="card-footer clearfix">
        <div class="service-rating">
          <span class="provider-rating rating-readonly" data-rating="<?php echo $rate; ?>" title="<?php echo round($rate, 2); ?>">
          </span>
          <?php if( $reviewers_count ): ?>
            <span class="rating-count" title="<?php echo __('Rated by ','egyptfoss') .' '. $reviewers_count .' '. __(' customers','egyptfoss') ?>">(<?php echo $reviewers_count ?>)</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php
  endforeach;
else:
  ?>
  <div class="row empty-state">
      <div class="empty-state-msg">
          <img src="<?php echo get_template_directory_uri(); ?>/img/service_icon.svg" width="64" alt="No Services">
          <br>
          <?php 
          if( $category == -1 && $technology == -1 && $theme == -1 && $type == -1 &&  $subtype == -1 ):
            echo _e( "There are no services yet", "egyptfoss" ) . ', ';?>
            <?php if ( !is_user_logged_in() ): ?>
                <a href="<?php echo home_url( pll_current_language().'/login/?redirected=respondtoservice&redirect_to='.get_current_lang_page_by_template("MarketPlace/template-add-service.php") ); ?>">
            <?php elseif (current_user_can('add_new_ef_posts')): ?>
                <a href="<?php echo get_current_lang_page_by_template("MarketPlace/template-add-service.php"); ?>">
            <?php else: ?>
                <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
                <a href="javascript:void(0)" class="disabled" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">
            <?php endif; ?>
              <?php echo __("Add new Service", "egyptfoss"); ?>
            </a>
            <?php
          else:
            _e( "There are no services under this criteria, please try different filters.", "egyptfoss" );
          endif;
          ?>
      </div>
  </div>
  <?php
endif;
?>

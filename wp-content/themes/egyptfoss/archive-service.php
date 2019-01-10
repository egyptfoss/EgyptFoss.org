<?php
get_header();

global $account_types, $ar_sub_types, $en_sub_types, $locale;

$no_of_posts = constant("ef_services_per_page");
    
wp_enqueue_script( 'listing_market_place-js', get_stylesheet_directory_uri() . '/js/listing_market_place.js', array('jquery'), '', true);
wp_localize_script('listing_market_place-js', 'ef_market_place', array( "per_page" => $no_of_posts ) );

$current_category = -1;
if( !empty( $_GET[ 'category' ] ) ){
    if( $_GET[ 'category' ] != "all" ){
      $current_category = ef_get_taxonomy_id_by_name( html_entity_decode( urldecode( $_GET['category'] ) ), 'service_category' );
      set_query_var( 'ef_listing_service_category', $current_category );
  }
}

$current_technology = -1;
if( !empty( $_GET[ 'technology' ] ) ){
    $current_technology = ef_get_taxonomy_id_by_name( html_entity_decode( urldecode( $_GET['technology'] ) ), 'technology' );
    set_query_var( 'ef_listing_service_technology_id', $current_technology );
}

$current_theme = -1;
if( !empty( $_GET['theme'] ) ){
    $current_theme = ef_get_taxonomy_id_by_name( html_entity_decode(  urldecode( $_GET['theme'] ) ), 'theme' );
    set_query_var( 'ef_listing_service_theme_id', $current_theme );
}

$current_type = -1;
if( !empty( $_GET[ 'type' ] ) ) {
  $current_type = html_entity_decode( urldecode( $_GET[ 'type' ] ) );
  set_query_var( 'ef_listing_service_type_id', $current_type );
}

$current_sub_type = -1;
if( !empty( $_GET[ 'subtype' ] ) ){
  $current_sub_type = html_entity_decode( urldecode( $_GET[ 'subtype' ] ) );
  set_query_var('ef_listing_service_subtype_id', $current_sub_type);
} 

$sub_types = ($locale == 'ar') ? $ar_sub_types : $en_sub_types;
echo '<script>' . PHP_EOL;
echo 'var individuals_types = ', js_individuals_types($sub_types), ';' . PHP_EOL;
echo 'var entities_types = ', js_entities_types($sub_types), ';' . PHP_EOL;
echo '</script>';
?>
<header class="page-header">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <h1 class="entry-title"> <?php echo _e("Services Market", "egyptfoss"); ?> </h1>
        <?php 
          // this variable is used in related documents templates
          $section_slug = 'market-place';
          include( locate_template( 'template-parts/content-related_documents.php' ) );
        ?>
      </div>
    </div>
  </div>
</header><!-- .entry-header -->
<?php
  $args = array(
    "post_status"   => "publish",
    "post_type"     => "service",
    "offset"        => 0,
    "category"      => $current_category,
    "technology_id" => $current_technology,
    "theme_id"      => $current_theme,
    "type"          => $current_type,
    "subtype"       => $current_sub_type
  );

  $services_count = ef_get_services( $args, TRUE );
?>
<div class="container">
  <div class="row">
    <div class="col-md-3">
      <div class="side-menu">
        <ul class="categories-list">
          <li <?php echo ($current_category == -1)?'class="active"':''; ?>>
            <a href="" onclick="return false;" data-slug="all" data-name="all" class="trigger_click" data-id="-1">
              <?php _e("All","egyptfoss"); ?>
            </a>
          </li>
          <?php $terms_data = get_terms('service_category', array('hide_empty' => 0)); 
            foreach ($terms_data as $term_data) {?>
              <li <?php echo ($current_category == $term_data->term_id)?'class="active"':''; ?>>
                <a href="javascript:;" data-slug="<?php echo urlencode($term_data->slug); ?>" data-name="<?php echo urlencode($term_data->name); ?>" class="trigger_click" data-id="<?php echo $term_data->term_id; ?>">
                  <?php echo ( $locale == 'ar' && $term_data->name_ar )?$term_data->name_ar:$term_data->name; ?>
                </a>
              </li>
          <?php } ?>
        </ul>
      </div>
    </div>

    <div id="primary" class="content-area col-md-9">
      <div class="row filter-bar">
        <div class="col-md-12">
          <?php if ( !is_user_logged_in() ) { ?>
          <a href="<?php echo home_url( pll_current_language().'/login/?redirected=respondtoservice&redirect_to='.get_current_lang_page_by_template("MarketPlace/template-add-service.php") ); ?>" class="btn btn-primary rfloat">
          <?php } else if (current_user_can('add_new_ef_posts')) { ?>
          <a href="<?php echo get_current_lang_page_by_template("MarketPlace/template-add-service.php"); ?>" class="btn btn-primary rfloat">
          <?php } else { ?>
          <!-- Subscriber user should be able to view (Add New) button in Product, Event, News, Location list pages -->
          <a href="javascript:void(0)" class="btn btn-primary disabled rfloat" data-toggle="tooltip" data-placement="top" title="<?php _e("You are not authorized to perform this action. Please contact us for more information.", "egyptfoss"); ?>">
          <?php } ?>
            <i class="fa fa-plus"></i>
            <?php echo __("New Service", "egyptfoss"); ?>
          </a>
        </div>
        <div class="ef-results-meta" <?php echo !$services_count?'style="display:none;"':''; ?>>
            <?php
              printf( 
                  '%s <span class="ef-results-count">%s</span> '.
                  '%s <span class="ef-total-count">%s</span> %s '.
                  '<span class="ef-category" %s>'.
                  '%s <span class="ef-category-name">"%s"</span>'.
                  '</span>',
                  __( 'Showing', 'egyptfoss' ),
                  (constant("ef_services_per_page") > $services_count )?$services_count:constant("ef_services_per_page"),
                  __('of', 'egyptfoss'),
                  $services_count,
                  __('results', 'egyptfoss'),
                  ($current_category == -1)?'style="display:none;"':'',
                  __( 'From', 'egyptfoss' ),
                  ($current_category != -1)?$_GET['category']:$current_theme
              );
            ?>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <section class="filter-nav" id="filter-products">
            <div class="row form-group">
              <div class="col-md-2">
                <select id="service-type" class="form-control custom-select2 topFilters"
                        style="width:100%;" data-placeholder="<?php _e("Type", "egyptfoss"); ?> ">
                  <option hidden="hidden"></option>
                  <optgroup>
                    <?php foreach ( $account_types as $type ):
                            printf( 
                              '<option value="%s"%s>%s</option>' ,
                              urlencode( $type ),
                              ( $current_type == $type )?'selected="selected"':'',
                              __( $type, 'egyptfoss' )
                            );
                        endforeach; ?>
                  </optgroup>
                </select>
              </div>
              <div class="col-md-2">
                <select id="service-subtype" class="form-control custom-select2 topFilters"
                        style="width:100%;" data-placeholder="<?php _e( "Sub Type", "egyptfoss" ); ?>">
                  <option hidden="hidden"></option>
                  <optgroup>
                    <?php if( $current_type != -1 ):
                        foreach ( $account_sub_types as $key => $type ):
                            if( $type == $current_type ):
                              printf( 
                                '<option value="%s"%s>%s</option>' ,
                                urlencode( $key ),
                                ( $current_sub_type == $key )?'selected="selected"':'',
                                ( $locale == 'ar' )?$ar_sub_types[ $key ]:$en_sub_types[ $key ]
                              );
                            endif;
                        endforeach; 
                      endif; ?>
                  </optgroup>
                </select>
              </div>
              <div class="col-md-2">
                <select id="service-technology" class="form-control custom-select2 topFilters"
                        style="width:100%;" data-placeholder=<?php _e("Technologies","egyptfoss"); ?>>
                  <option hidden="hidden"></option>
                  <optgroup>   
                    <?php 
                      $technologies = get_terms( 'technology', array( 'hide_empty' => false )  );
                      foreach ( $technologies as $technology ):
                          printf( 
                            '<option value="%s"%s>%s</option>' ,
                            urlencode( $technology->name ),
                            ( $current_technology == $technology->term_id )?'selected="selected"':'',
                            $technology->name
                          );
                      endforeach; 
                    ?>
                  </optgroup>
                </select>
              </div>
              <div class="col-md-2">
                <select id="service-theme" class="form-control custom-select2 topFilters"
                        style="width:100%;" data-placeholder=<?php _e("Theme","egyptfoss"); ?>>
                  <option hidden="hidden"></option>
                  <optgroup>   
                    <?php 
                      $themes = get_terms( 'theme', array( 'hide_empty' => false ) );
                      foreach ( $themes as $theme ):
                          printf( 
                            '<option value="%s"%s>%s</option>' ,
                            urlencode( $theme->name ),
                            ( $current_theme == $theme->term_id )?'selected="selected"':'',
                            ( $locale == 'ar' && $theme->name_ar )?$theme->name_ar:$theme->name
                          );
                      endforeach; 
                    ?>
                  </optgroup>
                </select>
              </div>
              <button class="btn btn-link reset-filters rfloat"><i class="fa fa-remove"></i> <?php _e( 'Reset', 'egyptfoss' ); ?> </button>
            </div>
          </section>
        </div>
      </div>

      <div class="row">
        <div class="services-grid">
        <?php set_query_var( 'ef_listing_services_offset', empty($_POST['offset']) ? 0 : $_POST['offset'] ); ?>
            <div class="loading-overlay hidden">
                <div class="spinner">
                    <div class="double-bounce1"></div>
                    <div class="double-bounce2"></div>
                </div>
            </div>
            <div  id="load_services_by_ajax_container">
              <?php get_template_part('template-parts/content', 'listing_market_place'); ?>
            </div> <!-- end of col-md-12 -->

            <div class="pagination-row clearfix view-more">
              <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more" 
                id="load_more_listing_services" data-offset="<?php echo constant('ef_services_per_page'); ?>" data-count=<?php echo $services_count ; ?>>
                <?php _e("Load more...", "egyptfoss"); ?>
              </a>
              <i class="fa fa-circle-o-notch fa-spin hidden ef-service-list-spinner"></i>
            </div>
        </div> <!-- end of row -->
      </div>
    </div><!-- #primary -->
  </div>
</div> <!-- end of container -->
<?php get_footer();?>

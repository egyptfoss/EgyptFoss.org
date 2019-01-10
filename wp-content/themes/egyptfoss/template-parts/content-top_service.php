<?php
  // get top services
  $top_services = Badge::efb_get_top_services();
  
  // hide all top service slider section if services count less than 4 
  if( count( $top_services ) < 4 ) {
    return;
  }
  
  // get current language
  $lang = pll_current_language();
?>

<div class="row text-center mt60_px">
    <div class="col-md-12">
        <h2 class="section-title-services">
            <?php 
              if( Badge::$is_top ):
                _e('Top Services', 'egyptfoss');
              else:
                _e('Latest Services', 'egyptfoss');
              endif;
            ?>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="mp-carousel">
            <?php 
              foreach( $top_services as $service ): 
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
              ?>
                <div class="service-card" style="width:100%;">
                    <div class="inner">
                        <div class="service-cover">
                            <?php if ( has_post_thumbnail( $service_id ) ): ?>
                              <img class="icon" src="<?php echo get_the_post_thumbnail_url( $service_id, 'medium-img' ); ?>" >
                            <?php else: ?>
                              <img class="icon" src="<?php echo get_template_directory_uri() . '/img/empty_service_cover.png'; ?>">
                            <?php endif; ?>
                        </div>
                        <div class="card-content">
                            <h4>
                                <a href="<?php echo get_permalink_by_lang( $service_id, '/marketplace/services/' ); ?>">
                                  <?php echo get_the_title( $service_id ); ?>
                                </a>
                            </h4>
                            <small>
                                <i class="fa fa-<?php  echo ( $type == 'Entity' )?'building-o':'user'; ?>"></i> 
                                  <?php _e( 'Offered by', 'egyptfoss' ); ?> 
                                <a  class="service-offeredby" href="<?php echo bp_core_get_user_domain( $user_id ) . 'about'; ?>">
                                  <?php echo $service->display_name; ?>
                                </a>
                            </small>
                            <br/>
                            <small>
                                <i class=" fa fa-folder-open-o"></i> 
                                 <small class="category_trim"> 
                                   <?php echo $category_name; ?>
                                 </small>
                            </small>
                        </div>
                        <div class="card-footer clearfix">
                          <div class="service-rating">
                              <span class="provider-rating rating-readonly" data-rating="<?php echo $rate; ?>"  title="<?php echo round($rate, 2); ?>"></span>
                              <?php if( $reviewers_count ): ?>
                                <span class="rating-count" title="<?php echo __( 'Rated by ', 'egyptfoss' ) . $reviewers_count . __( ' customers', 'egyptfoss' ) ?>">
                                    (<?php echo $reviewers_count ?>)
                                </span>
                              <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
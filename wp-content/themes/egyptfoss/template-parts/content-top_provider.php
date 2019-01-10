<?php
  global $sub_types;
  
  // get top providers
  $top_providers = Badge::efb_get_top_providers();
  
  // hide all top providers slider section if providers count less than 4 
  if( count( $top_providers ) < 4 ) {
    return;
  }
?>
<div class="row text-center mt60_px">
    <div class="col-md-12">
      <h2 class="section-title-services">
        <?php
          if( Badge::$is_top ):
            _e('Top Providers', 'egyptfoss');
          else:
            _e('Latest Providers', 'egyptfoss');
          endif;
        ?>
      </h2>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="mp-carousel">
            <?php
              foreach( $top_providers as $provider ): 

                  $user_data = get_registration_data( $provider->ID );

                  $sub_type = '';

                  if ( !empty( $user_data['sub_type'] ) ) {
                    $sub_type = $sub_types[ $user_data['sub_type'] ];
                  }
                  ?>

                  <div class="provider-card service-card" style="width:100%">
                      <div class="inner">
                          <div class="card-content text-center">
                               <div class="avatar">
                                   <?php echo get_avatar( $provider->ID, 120 ); ?>
                               </div>
                               <h4>
                                 <a href="<?php echo bp_core_get_user_domain( $provider->ID ) . 'about'; ?>">
                                     <?php echo $provider->display_name; ?>
                                 </a>
                              </h4>
                              <small><?php echo $sub_type; ?></small>   
                          </div>
                          <?php 
                            $rates_sum = Review::where( 'provider_id', '=', $provider->ID )->sum( 'rate' );
                            $reviewers_count = $provider->reviewers_count;
                            $average_rate = 0;
                            
                            if( $reviewers_count ) {
                              $average_rate = (float)$rates_sum / (float)$reviewers_count;
                            }
                          ?>
                          <div class="card-footer clearfix">
                            <span class="service-rating">
                              <span class="provider-rating rating-readonly" data-rating="<?php echo $average_rate; ?>" title="<?php echo round($average_rate, 2); ?>">
                              </span>
                              <?php if( $reviewers_count ): ?>
                                <span class="rating-count" title="<?php echo __( 'Rated by ', 'egyptfoss' ) . $reviewers_count . __( ' customers', 'egyptfoss' ) ?>">
                                    (<?php echo $reviewers_count ?>)
                                </span>
                              <?php endif; ?>
                            </span>
                            <a href="<?php echo bp_core_get_user_domain( $provider->ID ) . __( 'services', 'egyptfoss' ); ?>" class="rfloat small-link">
                                <i></i>
                                <?php echo sprintf( _n( "%s Service", "%s Services", $provider->services_count, "egyptfoss" ), $provider->services_count ); ?>
                            </a>
                          </div>
                      </div>
                  </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
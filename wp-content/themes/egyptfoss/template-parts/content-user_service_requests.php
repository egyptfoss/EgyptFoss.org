<?php

$displayedUserId = bp_displayed_user_id();

if( empty( $displayedUserId ) && isset( $_POST[ 'displayedUserID' ] ) ) {
  $displayedUserId = $_POST[ 'displayedUserID' ];
}

$args = array(
  "post_status" => '',
  "post_type"   => "service",
  "no_of_posts" => constant("ef_user_service_per_page"),
  "offset"      => get_query_var( 'user_service_offset' ) ? get_query_var('user_service_offset') : 0,
  "author"      => $displayedUserId
);

if( get_current_user_id() != $displayedUserId ) {
  $args[ 'post_status' ] = "publish";
}

$results = get_user_request_center_requests( $args );

foreach ( $results as $post ): setup_postdata( $post ); ?>
  <div class="service-panel clearfix">
    <div class="service-cover" style="vertical-align: middle;">
        <?php if ( has_post_thumbnail() ): ?>
          <!-- <img class="icon" src="<?php the_post_thumbnail_url(); ?>"> -->
        <?php echo get_the_post_thumbnail( get_the_ID(), array(100,100) ); ?>
        <?php else: ?>
          <img class="icon" src="<?php echo get_template_directory_uri() . '/img/empty_service_cover.png'; ?>">
        <?php endif; ?>
    </div>
    <div class="service-panel-content">
      <h3><a href="<?php echo get_permalink_by_lang( get_the_ID(), '/marketplace/services/' ); ?>"><?php the_title(); ?></a>
          <?php
            $reviewers_count = get_post_meta(get_the_ID(), 'reviewers_count', true);
            $reviewers_count = ($reviewers_count == NULL) ? 0 : $reviewers_count;
            $rate = get_post_meta(get_the_ID(), 'rate', true);
            $rate = ($rate == NULL) ? 0 : $rate;
          ?>
          <div class="service-rating rfloat">
            <span class="provider-rating rating-readonly" data-rating="<?php echo $rate; ?>" title="<?php echo round($rate, 2); ?>">
            </span>
            <?php if( $reviewers_count > 0 ): ?>
              <span class="rating-count" title="<?php echo __('Rated by ','egyptfoss') .' '. $reviewers_count .' '. __(' customers','egyptfoss') ?>">(<?php echo $reviewers_count ?>)</span>
            <?php endif; ?>
          </div>
      </h3>
      <div class="service-meta">
          <strong><?php _e( 'Category', 'egyptfoss' ); ?>:</strong>
          <?php $category_id = get_post_meta( get_the_ID(), 'service_category', TRUE ); 
          $category_term = get_term( $category_id, 'service_category' );
          ?>
          <span class="technology-tag"><?php 
            if( pll_current_language() == "ar")
            {
                if($category_term->name_ar != ''){
                    echo $category_term->name_ar;
                }else {
                  echo $category_term->name;
                }
            }else {
              echo $category_term->name;
            }
          ?>  </span>
      </div>
      <?php if (  get_post_status() == 'pending' ): ?>
        <span class="pending-approval">
          <i class="fa fa-history"></i>
          <?php _e( 'Pending Approval', 'egyptfoss' ) ?>
        </span>
      <?php elseif( get_post_status() == 'archive' ): ?>
        <span class="archived-label">
          <i class="fa fa-archive"></i>
          <?php _e( 'Archived', 'egyptfoss' ) ?>
        </span>
      <?php endif; ?> 
      </div>
  </div>
<?php endforeach;

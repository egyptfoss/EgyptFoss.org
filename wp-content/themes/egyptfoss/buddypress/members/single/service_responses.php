<div class="row">
  <div class="col-md-12">
     <?php 
      $args = array(
        "post_status" => '',
        "post_type"   =>"service",
        "no_of_posts" => constant("ef_user_service_per_page"),
        "offset"      => get_query_var('user_service_responses_offset') ? get_query_var( 'user_service_responses_offset' ) : 0,
        "author"      => bp_displayed_user_id()
      );
    if( get_current_user_id() != bp_displayed_user_id() ) {
      $args['post_status'] = "publish";
    }     
    $user_services_count = count( get_user_request_center_responses_count( $args ) );
    if( $user_services_count > 0 ): ?>
      <div class="row" id="load_responses_by_ajax_container">
          <?php get_template_part( 'template-parts/content', 'user_service_responses' ); ?>
      </div>
      <?php if( constant( "ef_user_service_per_page" ) < $user_services_count ): ?>  
          <div class="pagination-row clearfix view-more">
              <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more hidden" id="load_more_responses" data-offest="<?php echo constant( "ef_user_service_per_page" ); ?>" data-count=<?php echo $user_services_count; ?>>
              <?php _e( "Load more...", "egyptfoss" ); ?>
            </a>
            <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
          </div>
      <?php endif; ?> 
    <?php else: ?>
          <div class="row">
      <div class="col-md-12">
          <div class="empty-state-msg">
               <img src="<?php echo get_template_directory_uri(); ?>/img/service_icon.svg" width="64" alt="No Services">
               <br>
               <h4><?php _e( "You don't have services' requests", "egyptfoss" ); ?></h4>
           </div>
      </div>
   </div>
    <?php endif; ?>
  </div>
</div>

<script>
  (function ($) {
    
  $(document).ready(function () {
    offest = parseInt($("#load_more_responses").attr("data-offest"));
      $productCount = $("#load_more_responses").attr("data-count");
      if(offest < $productCount)
      {
        $("#load_more_responses").removeClass("hidden");
      }

    $("#load_more_responses").click(function (e) {
      e.preventDefault();
      offest = parseInt($("#load_more_responses").attr("data-offest"));
      $productCount = $("#load_more_responses").attr("data-count");
      $("#load_more_responses").addClass("hidden");
      $(".ef-product-list-spinner").removeClass("hidden");

      get_user_responses_by_ajax();
    });

    function refreshCountAndOffest()
    {
      offest = parseInt($("#load_more_responses").attr("data-offest"));
      dataset_count = parseInt($("#load_more_responses").attr("data-count"));
      newOffest = parseInt(offest) + <?php echo constant( "ef_user_service_per_page" ); ?>;
      if (dataset_count <= newOffest)
      {
        $("#load_more_responses").addClass("hidden");
      } else
      {
        $("#load_more_responses").removeClass("hidden");
      }
      $("#load_more_responses").attr("data-offest", newOffest);
    }
    function get_user_responses_by_ajax()
    {
       offest = parseInt($("#load_more_responses").attr("data-offest"));
      var data = {
        action: 'ef_load_more_user_service_responses',
        offest: offest,
        displayedUserID: <?php echo bp_displayed_user_id(); ?>,
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        async: false,
        success: function (data) { 
            $(".ef-product-list-spinner").addClass("hidden");
            refreshCountAndOffest(0);
            //$scrollHeight = $("#load_product_by_ajax_container div.ef_product_set:last")[0].scrollHeight;
            $offest = parseInt($("#load_more_responses").attr("data-offest")) - <?php echo constant( "ef_user_service_per_page" ); ?>;
            $('body,html').animate({ scrollTop: (102* $offest) + 200 }, 1000);
            $("#load_responses_by_ajax_container").append(data);
        }
      });
    }
  });
}(jQuery));
</script>  
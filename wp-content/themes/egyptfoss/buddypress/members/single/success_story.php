<header class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php _e("Success Stories","egyptfoss"); ?></h2>
	</div>
</header>
<div class="row">
  <div class="col-md-12">
     <?php 
     $args = array("post_status" => '',
              "post_type"=>"success_story",
              "no_of_posts" => constant("ef_user_success_story_per_page"),
              "offset" => (get_query_var('user_success_story_offset') ? get_query_var('user_success_story_offset') : 0),
              "author" => bp_displayed_user_id());
     if(get_current_user_id() != bp_displayed_user_id())
     {
       $args['post_status'] = "publish";
     }
     $user_success_story_count = count(get_user_success_stories_count($args));
     if($user_success_story_count > 0)
     {
     ?> 
    <div class="row" id="load_success_story_by_ajax_container">
      <?php get_template_part('template-parts/content', 'user_success_stories'); ?>
    </div>
    <?php if(constant("ef_user_success_story_per_page") < $user_success_story_count){ ?>  
    <div class="pagination-row clearfix view-more">
        <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more hidden" id="load_more_success_story" data-offest="<?php echo constant("ef_user_success_story_per_page") ?>" data-count=<?php echo $user_success_story_count; ?>>
        <?php _e("Load more...", "egyptfoss"); ?>
      </a>
      <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
    </div>
    <?php } 
     }else
     {?>
         <div class="row">
                 <div class="col-md-12">
               <div class="empty-state-msg">
                   <i class="fa fa-warning"></i>
                   <br>
                   <h4>
                    <?php echo sprintf(__("There are no success stories added by %s", "egyptfoss"), ''); ?>    
             <a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id()).'/about/' ?>"> <?php echo bp_core_get_user_displayname(bp_displayed_user_id()); ?> </a>   
                   </h4>
               </div>
           </div>  
          </div>
    <?php }
    ?>
      
  </div>
</div>

<script>
  (function ($) {
    
  $(document).ready(function () {
    offest = parseInt($("#load_more_success_story").attr("data-offest"));
      $productCount = $("#load_more_success_story").attr("data-count");
      if(offest < $productCount)
      {
        $("#load_more_success_story").removeClass("hidden");
      }
      //$("#load_more_success_story").addClass("hidden");
    $("#load_more_success_story").click(function (e) {
      e.preventDefault();
      offest = parseInt($("#load_more_success_story").attr("data-offest"));
      $productCount = $("#load_more_success_story").attr("data-count");
      $("#load_more_success_story").addClass("hidden");
      $(".ef-product-list-spinner").removeClass("hidden");

      get_user_success_story_by_ajax();
    });

    function refreshCountAndOffest()
    {
      offest = parseInt($("#load_more_success_story").attr("data-offest"));
      success_count = parseInt($("#load_more_success_story").attr("data-count"));
      newOffest = parseInt(offest) + <?php echo constant("ef_user_success_story_per_page") ?>;
      if (success_count <= newOffest)
      {
        $("#load_more_success_story").addClass("hidden");
      } else
      {
        $("#load_more_success_story").removeClass("hidden");
      }
      $("#load_more_success_story").attr("data-offest", newOffest);
    }
    function get_user_success_story_by_ajax()
    {
       offest = parseInt($("#load_more_success_story").attr("data-offest"));
      var data = {
        action: 'ef_load_more_user_success_story',
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
            $offest = parseInt($("#load_more_success_story").attr("data-offest")) - <?php echo constant("ef_user_success_story_per_page") ?>;
            $('body,html').animate({ scrollTop: (102* $offest) + 200 }, 1000);
            $("#load_success_story_by_ajax_container").append(data);
        }
      });
    }
  });




}(jQuery));
</script>  
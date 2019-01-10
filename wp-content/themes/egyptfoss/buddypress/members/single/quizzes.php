<header class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php _e("Quizzes","egyptfoss"); ?></h2>
	</div>
</header>
<div class="row">
	<div class="col-md-12">
      <?php if(get_current_user_id() == bp_displayed_user_id()) { ?>
      <div class="surveys-list profile-quizzes">
      <?php 
        $user_quizzes_taken = count(get_user_quizzes_taken_count($args));
        if($user_quizzes_taken > 0){
        ?> 
        <div class="row" id="load_quizzes_by_ajax_container">
          <?php get_template_part('template-parts/content', 'user_quizzes'); ?>
        </div>
        <?php if(constant("ef_user_quizzes_per_page") < $user_quizzes_taken){ ?>  
        <div class="pagination-row clearfix view-more">
            <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more hidden" id="load_more_user_quizzes" data-offest="<?php echo constant("ef_user_quizzes_per_page") ?>" data-count=<?php echo $user_quizzes_taken; ?>>
            <?php _e("Load more...", "egyptfoss"); ?>
          </a>
          <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
        </div>
        <?php } 
        } else {?>
          <div class="row">
            <div class="col-md-12">
                <div class="empty-state-msg">
                    <i class="fa fa-warning"></i>
                    <br>
                    <h4>
           <?php echo sprintf(__("There are no quizzes taken by %s", "egyptfoss"), ''); ?>    
              <a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id()).'/about/' ?>"> <?php echo bp_core_get_user_displayname(bp_displayed_user_id()); ?> </a>     
                    </h4>
                </div>
            </div>   
        </div>
        <?php }
        ?>
      </div>
      <?php } else { ?>
      <div class="row">
             <div class="col-md-12">
               <div class="empty-state-msg">
                   <i class="fa fa-warning"></i>
                   <br>
                   <h4>
        <?php echo sprintf(__("You don't have permission to access this page or you have signed out.", "egyptfoss"), ''); ?>    
          <!--<a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id()).'/about/' ?>"> <?php echo bp_core_get_user_displayname(bp_displayed_user_id()); ?> </a>      -->
                   </h4>
               </div>
           </div>   
      </div>
      <?php } ?>
	</div>
</div>
<script>
  (function ($) {
  $(document).ready(function () {
    offest = parseInt($("#load_more_user_quizzes").attr("data-offest"));
      $productCount = $("#load_more_user_quizzes").attr("data-count");
      if(offest < $productCount)
      {
        $("#load_more_user_quizzes").removeClass("hidden");
      }
      //$("#load_more_user_quizzes").addClass("hidden");
    $("#load_more_user_quizzes").click(function (e) {
      e.preventDefault();
      offest = parseInt($("#load_more_user_quizzes").attr("data-offest"));
      $productCount = $("#load_more_user_quizzes").attr("data-count");
      $("#load_more_user_quizzes").addClass("hidden");
      $(".ef-product-list-spinner").removeClass("hidden");

      get_user_quizzes_by_ajax();
    });

    function refreshCountAndOffest()
    {
      offest = parseInt($("#load_more_user_quizzes").attr("data-offest"));
      dataset_count = parseInt($("#load_more_user_quizzes").attr("data-count"));
      newOffest = parseInt(offest) + <?php echo constant("ef_user_quizzes_per_page") ?>;
      if (dataset_count <= newOffest)
      {
        $("#load_more_user_quizzes").addClass("hidden");
      } else
      {
        $("#load_more_user_quizzes").removeClass("hidden");
      }
      $("#load_more_user_quizzes").attr("data-offest", newOffest);
    }
    function get_user_quizzes_by_ajax()
    {
       offest = parseInt($("#load_more_user_quizzes").attr("data-offest"));
      var data = {
        action: 'ef_load_more_user_quizzes_taken',
        offest: offest
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        async: false,
        success: function (data) { 
            $(".ef-product-list-spinner").addClass("hidden");
            refreshCountAndOffest(0);
            $offest = parseInt($("#load_more_user_quizzes").attr("data-offest")) - <?php echo constant("ef_user_quizzes_per_page") ?>;
            //$('body,html').animate({scrollTop: $('.quiz-title:last').offset().top}, 1000);
            $("#load_quizzes_by_ajax_container").append(data);
        }
      });
    }
  });
}(jQuery));
</script>  
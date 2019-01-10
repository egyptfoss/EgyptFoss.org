<header class="row">
	<div class="col-md-12">
		<h2 class="profile-page-title"><?php echo _x("Published Documents","definite","egyptfoss"); ?></h2>
	</div>
</header>
<div class="row">
  <div class="col-md-12">
    <?php 
    $items = get_user_published_documents(bp_displayed_user_id(), 0);
    $user_documents_count = count(get_user_total_published_documents(bp_displayed_user_id()));
    if($user_documents_count > 0) { ?>
      <div class="row" id="load_documents_by_ajax_container">
        <?php get_template_part('template-parts/content', 'user_documents'); ?>
      </div>
      <?php if(constant("ef_user_documents_per_page") < $user_documents_count){ ?>  
        <div class="pagination-row clearfix view-more">
          <a href="javascript:void(0);" onclick="return false;" class="btn btn-load-more hidden" id="load_more_documents" data-offest="<?php echo constant("ef_user_documents_per_page") ?>" data-count=<?php echo $user_documents_count; ?>>
            <?php _e("Load more...", "egyptfoss"); ?>
          </a>
          <i class="fa fa-circle-o-notch fa-spin hidden ef-product-list-spinner"></i>
        </div>
      <?php } ?>
    <?php } else { ?>
      <div class="row">
        <div class="col-md-12">
          <div class="empty-state-msg">
            <i class="fa fa-newspaper-o"></i><br>
            <h4>
              <?php echo sprintf(__("There are no published documents edited by %s", "egyptfoss"), ''); ?>
              <a href="<?php echo home_url()."/members/".bp_core_get_username(bp_displayed_user_id()).'/about/' ?>"> <?php echo bp_core_get_user_displayname(bp_displayed_user_id()); ?> </a>    
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
      offest = parseInt($("#load_more_documents").attr("data-offest"));
      documentCount = $("#load_more_documents").attr("data-count");
      if(offest < documentCount) {
        $("#load_more_documents").removeClass("hidden");
      }
      $("#load_more_documents").click(function (e) {
        e.preventDefault();
        offest = parseInt($("#load_more_documents").attr("data-offest"));
        documentCount = $("#load_more_documents").attr("data-count");
        $("#load_more_documents").addClass("hidden");
        $(".ef-product-list-spinner").removeClass("hidden");
        get_user_published_documents_by_ajax();
      });

      function refreshCountAndOffest() {
        offest = parseInt($("#load_more_documents").attr("data-offest"));
        documents_count = parseInt($("#load_more_documents").attr("data-count"));
        newOffest = parseInt(offest) + <?php echo constant("ef_user_documents_per_page") ?>;
        if (documents_count <= newOffest) {
          $("#load_more_documents").addClass("hidden");
        } else {
          $("#load_more_documents").removeClass("hidden");
        }
        $("#load_more_documents").attr("data-offest", newOffest);
      }

      function get_user_published_documents_by_ajax() {
        offest = parseInt($("#load_more_documents").attr("data-offest"));
        var data = {
          action: 'ef_load_more_user_documents',
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
            $offest = parseInt($("#load_more_documents").attr("data-offest")) - <?php echo constant("ef_user_documents_per_page") ?>;
            $('body,html').animate({ scrollTop: (102* $offest) + 200 }, 1000);
            $("#load_documents_by_ajax_container").append(data);
          }
        });
      }
    });
  }(jQuery));
</script>

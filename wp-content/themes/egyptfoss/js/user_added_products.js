(function ($) {
  $(document).ready(function () {
    offest = parseInt($("#load_more_added_products_by_user").attr("data-offest"));
    $productCount = $("#load_more_added_products_by_user").attr("data-count");
    if (offest < $productCount){
      $(".view-more").removeClass("hidden");
    }

    $("#load_more_added_products_by_user").click(function (e) {
        e.preventDefault();
        offest = parseInt($("#load_more_added_products_by_user").attr("data-offest"));
        $productCount = $("#load_more_added_products_by_user").attr("data-count");
        get_added_products_by_user_ajax();
    });

    function get_added_products_by_user_ajax(){
      var data = {
        action: 'ef_load_more_added_products_by_user',
        offest: offest,
        displayed_user_id: profile.bp_user_id,
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
            offest = parseInt($("#load_more_added_products_by_user").attr("data-offest"));
            newOffest = parseInt(offest) + 20;
            $("#load_more_added_products_by_user").attr("data-offest", newOffest);
            if (newOffest >= $productCount){
              $(".view-more").addClass("hidden");
            }
            $("#load_user_added_product_by_ajax_container").append(data);            
        }
      });
    }
  });
}(jQuery));
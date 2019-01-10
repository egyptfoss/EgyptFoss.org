(function ($) {
  $(document).ready(function () {
    offest = parseInt($("#load_more_contributed_products").attr("data-offest"));
    $productCount = parseInt($("#load_more_contributed_products").attr("data-count"));
    if (offest < $productCount){
      $(".view-more-contributed").removeClass("hidden");
    }
    
    $("#load_more_contributed_products").click(function (e) {
        e.preventDefault();
        offest = parseInt($("#load_more_contributed_products").attr("data-offest"));
        $productCount = $("#load_more_contributed_products").attr("data-count");

        get_contributed_products_by_user_ajax();
    });

    function get_contributed_products_by_user_ajax(){
      var data = {
        action: 'ef_load_more_contributed_products_by_user',
        offest: offest,
        displayed_user_id: profile.bp_user_id,
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
            offest = parseInt($("#load_more_contributed_products").attr("data-offest"));
            newOffest = parseInt(offest) + 20;
            $("#load_more_contributed_products").attr("data-offest", newOffest);
            if (newOffest >= $productCount){
              $(".view-more-contributed").addClass("hidden");
            }
            $("#load_user_contributed_product_by_ajax_container").append(data);            
        }
      });
    }
  });
}(jQuery));
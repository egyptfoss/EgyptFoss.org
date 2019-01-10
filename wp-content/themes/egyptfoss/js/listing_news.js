(function ($) {
  $(document).ready(function () {
    offset = parseInt($("#load_more_listing_news").attr("data-offset"));
    $count = $("#load_more_listing_news").attr("data-count");
        
    if (offset >= $count){
      $(".view-more").hide();
    }
    
    $("#load_more_listing_news").click(function (e) {
        e.preventDefault();
        offset = parseInt($("#load_more_listing_news").attr("data-offset"));
        $count = $("#load_more_listing_news").attr("data-count");

        get_listing_news_by_ajax();
    });

    function get_listing_news_by_ajax(){
      var data = {
        action: 'ef_load_more_listing_news',
        offset: offset
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
            offset = parseInt($("#load_more_listing_news").attr("data-offset"));
            newOffset = parseInt(offset) + parseInt(ef_news.per_page);
            // alert(newOffset);
            $("#load_more_listing_news").attr("data-offset", newOffset);
            if (newOffset >= $count){
              $(".view-more").hide();
            }
            $("#load_news_by_ajax_container").append(data);            
        }
      });
    }
  });
}(jQuery));
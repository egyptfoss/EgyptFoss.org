(function ($) {
  $(document).ready(function () {
    offset = parseInt($("#load_more_listing_awareness_center").attr("data-offset"));
    $count = $("#load_more_listing_awareness_center").attr("data-count");
    category_id = -1;
    is_new_category = false;
    if (offset >= $count){
      $(".view-more").hide();
    }else {
      $(".view-more").show();  
    }
    
    $("#load_more_listing_awareness_center").click(function (e) {
        e.preventDefault();
        offset = parseInt($("#load_more_listing_awareness_center").attr("data-offset"));
        $count = $("#load_more_listing_awareness_center").attr("data-count");
        is_new_category = false;
        get_listing_awareness_center_by_ajax();
    });
    
    $(document).on("click",".trigger_click",function (e) {        
        e.preventDefault();
        offset = 0;
        $count = $("#load_more_listing_awareness_center").attr("data-count");
        //set category id
        category_id = $(this).attr("data-id");
        is_new_category = true;
        
        //set active menu item
        $(document).find(".categories-list li").removeClass("active");
        //add class active to selected item
        $(document).find("a[data-id='"+$(this).attr("data-id")+"']").parent().addClass("active");

        get_listing_awareness_center_by_ajax();
        updateUrl($(this));    
    });

    function updateUrl(item)
    {
        var myURL = window.location.href.split('?')[0]+"?category="+item.attr("data-slug");
        window.history.pushState({path: myURL}, '', myURL);
    }

    function checkPaginationDisplay(data)
    {
        if (newOffset >= $count){
          newOffset = $count;
          $(".view-more").hide();
        }else 
        {
          $(".view-more").show();  
        }
        if(!is_new_category)
        {
            $("#load_awareness_center_by_ajax_container").append(data);            
        } else {
            $("#load_awareness_center_by_ajax_container").html(data);            
        }
        
        if( $count == 0 ) {
            $( '.ef-results-meta' ).hide();
        }
        else {
            $( '.ef-results-meta' ).show();
        }

        $( '.ef-results-count' ).html( newOffset );
        $( '.ef-total-count' ).html( $count );

        if( category_id != -1 ) {
            $( '.ef-category-name' ).html( '"' + $( ".categories-list .active a" ).html() + '"' );
            $( '.ef-category' ).show();
        }
        else {
            $( '.ef-category-name' ).html( '' );
            $( '.ef-category' ).hide();
        }

        $(".loading-overlay").addClass('hidden');
    }

    function get_listing_awareness_center_by_ajax(){
        $(".loading-overlay").removeClass('hidden');
        var data = {
          action: 'ef_load_more_listing_awareness_center',
          offset: offset,
          category: category_id
        };
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            success: function (data) 
            {
                offset = parseInt($("#load_more_listing_awareness_center").attr("data-offset"));
                if(!is_new_category){
                    newOffset = parseInt(offset) + parseInt(ef_listing_awareness_center.per_page);
                } 
                else {
                    newOffset = parseInt(ef_listing_awareness_center.per_page);
                }
                $("#load_more_listing_awareness_center").attr("data-offset", newOffset);
                if(is_new_category)
                {
                    //Load new count by category
                    var data_count = {
                        action: 'ef_load_more_listing_count_awareness_center',
                        offset: offset,
                        category: category_id
                    };
                    jQuery.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: data_count,
                        success: function (count) 
                        {
                            //data represents total count of data
                            $count = count;
                            $("#load_more_listing_awareness_center").attr("data-count", count);
                            checkPaginationDisplay(data);
                        }
                    });
                }else
                {
                    checkPaginationDisplay(data);
                }
            }
        });
    }
  });
}(jQuery));
(function ($) {
  $(document).ready(function () {
    offset = parseInt($("#load_more_listing_success_stories").attr("data-offset"));
    $count = $("#load_more_listing_success_stories").attr("data-count");
    category_id = -1;
    if (offset >= $count){
      $(".view-more").hide();
    }
    
    $("#load_more_listing_success_stories").click(function (e) {
        e.preventDefault();
        offset = parseInt($("#load_more_listing_success_stories").attr("data-offset"));
        $count = $("#load_more_listing_success_stories").attr("data-count");
        category_id = $( ".categories-list li.active a" ).attr( 'data-id' );
        get_listing_success_story_by_ajax();
    });
    
    $(document).on("click",".trigger_click",function (e) {
        e.preventDefault();
        offset = 0;
        $count = $("#load_more_listing_success_stories").attr("data-count");
        get_left_menu_success_story_by_ajax($(this));
        updateUrl($(this));
    });

    function updateUrl(item)
    {
        var myURL = window.location.href.split('?')[0]+"?category="+item.attr("data-slug");
        window.history.pushState({path: myURL}, '', myURL);
    }

    function get_listing_success_story_by_ajax(){
      var data = {
        action: 'ef_load_more_listing_success_story',
        offset: offset,
        category: category_id
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
            offset = parseInt($("#load_more_listing_success_stories").attr("data-offset"));
            newOffset = parseInt(offset) + parseInt(ef_success_story.per_page);
            $("#load_more_listing_success_stories").attr("data-offset", newOffset);
            if (newOffset >= $count){
              newOffset = $count;
              $(".view-more").hide();
            }
            $("#load_success_stories_by_ajax_container").append(data);
            
            $( '.ef-results-count' ).html( newOffset );
            
            //update line numbers
            if ($(".story-content p").exists()) {
                $('.story-content p').trunk8({
                        lines:4,
                        tooltip: false
                });
            }
        }
      });
    }
    
    function get_left_menu_success_story_by_ajax(selected_item){
      var data = {
        action: 'ef_left_menu_listing_success_story'
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
            $(".menu-left").html(data);   
            
            //remove class active for all other li
            $(document).find(".categories-list li").removeClass("active");
            //add class active to selected item
            $(document).find("a[data-id='"+selected_item.attr("data-id")+"']").parent().addClass("active");
            get_listing_success_story_by_category_ajax(selected_item.attr("data-id"), selected_item.parent().find(".count").text());  
        }
      });
    }
    
    function get_listing_success_story_by_category_ajax(cat_id, total_count){
        $(".loading-overlay").removeClass('hidden');
        category_id = cat_id;
      var data = {
        action: 'ef_load_change_category_success_story',
        offset: offset,
        category: cat_id
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
            newOffset = parseInt(ef_success_story.per_page);
            newCount = total_count;
            $("#load_more_listing_success_stories").attr("data-offset", newOffset);
            $("#load_more_listing_success_stories").attr("data-count", newCount);
            $count = newCount;
            if (newOffset >= $count){
              newOffset = $count;
              $(".view-more").hide();
            }else
            {
              $(".view-more").show();  
            }
            $(".loading-overlay").addClass('hidden');
            $("#load_success_stories_by_ajax_container").html(data); 
            
            if( newCount == 0 ) {
              $( '.ef-results-meta' ).hide();
            }
            else {
              $( '.ef-results-meta' ).show();
            }
            
            $( '.ef-results-count' ).html( newOffset );
            $( '.ef-total-count' ).html( newCount );
            
            if( category_id != -1 ) {
                $( '.ef-category-name' ).html( '"' + $( ".categories-list .active a" ).html() + '"' );
                $( '.ef-category' ).show();
            }
            else {
                $( '.ef-category-name' ).html( '' );
                $( '.ef-category' ).hide();
            }
            
            //update line numbers
            if ($(".story-content p").exists()) {
                $('.story-content p').trunk8({
                        lines:4,
                        tooltip: false
                });
            }
        }
      });
    }
  });
}(jQuery));
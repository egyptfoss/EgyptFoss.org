(function ($) {
  $(document).ready(function () {
    offset = parseInt($("#load_more_listing_search").attr("data-offset"));
    $count = $("#load_more_listing_search").attr("data-count");
        
    if (offset >= $count){
      $(".view-more").hide();
    }else {
      $(".view-more").show();  
    }
    
    //Load sidebar
    get_sideBar_info();
    
    $("#load_more_listing_search").click(function (e) {
        e.preventDefault();
        offset = parseInt($("#load_more_listing_search").attr("data-offset"));
        $count = $("#load_more_listing_search").attr("data-count");

        get_listing_search_by_ajax();
    });

    function get_listing_search_by_ajax(){
      var data = {
        action: 'ef_load_more_listing_search',
        offset: offset
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
            offset = parseInt($("#load_more_listing_search").attr("data-offset"));
            newOffset = parseInt(offset) + parseInt(ef_search.per_page);
            
            //update showing number
            if(newOffset >= $count)
                jQuery("#span_numb").text($count);
            else
                jQuery("#span_numb").text(newOffset);
            
            $("#load_more_listing_search").attr("data-offset", newOffset);
            if (newOffset >= $count){
              $(".view-more").hide();
            }
            $("#load_search_by_ajax_container").last().append(data);            
        }
      });
    }
    
    function get_sideBar_info(){
      var data = {
        action: 'ef_load_sidebar',
        entity: ef_search.entity
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
           data = JSON.parse(data);
           if(data.error)
           {
               $(".search-sidebar").hide();
           }
                      
           if(data.title !== undefined && data.title.value !== undefined && data.title.value != ''
             && data.description !== undefined && data.description.value !== undefined && data.description.value != '')
           {
                $("#title").html(data.title.value);
                $("#sidebar-thmbnail").attr("src", data.thumb.value);
                $("#sidebar-wikipedia").attr("href", data.wikiLink.value);
                $("#sidebar-description").html(data.description.value);
                
                var relatedItems = '';
                var length = data.seeAlso.length;
                if(length > 5)
                    length = 5;
                for (i = 0; i < length; i++) { 
                    var linkParts = data.seeAlso[i].value.split("/");
                    var item_title = '';
                    if( linkParts.length ) {
                        item_title = linkParts[ linkParts.length - 1 ].split('_').join(' ');
                    }
                    else {
                        item_title = data.seeAlso[i].value;
                    }
                    relatedItems += "<li><a target=\"_blank\" href=\""+data.seeAlso[i].value+"\">"+item_title+"</a></li>"
                }
                $("#sidebar-related-items").html(relatedItems);
                if(relatedItems != '')
                    $("#sidebar-related-items-head").show();
                
                $( '#primary' ).removeClass( 'col-md-9' ).addClass( 'col-md-6' );
                
                $(".search-sidebar").show();
           }    
        }
      });
    }
  });
}(jQuery));
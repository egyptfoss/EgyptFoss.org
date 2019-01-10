(function ($) {
  $(document).ready(function () {
    offset = parseInt($("#load_more_listing_requests").attr("data-offset"));
    $count = $("#load_more_listing_requests").attr("data-count");
    type_id = -1;
    if (offset >= $count){
      $(".view-more").hide();
    }
    
    //update line numbers
    if ($(".card-summary p").exists()) {
        $('.card-summary p').trunk8({
                lines:3,
                tooltip: false
        });
    }
    //update select2 add remove button for each filter
    $(".custom-select2").each(function(){
      $(this).select2({
        placeholder: $.validator.messages[$(this).attr("data-taxonomy")],
        allowClear: true,
        language: {
          noResults: function () {
            return jQuery.validator.messages.select2_no_results;
          }
        }
      });
    });
    
    $(".topFilters").change(function (e) {
        e.preventDefault();
        var type_id = jQuery(document).find(".type-list .active").find('a').attr('data-id');
        get_listing_request_center_by_filters_ajax(type_id);
    });
    
    $(".topFiltersInList").live('click', function(e){
        e.preventDefault();
        var type_id = jQuery(document).find(".type-list .active").find('a').attr('data-id');
        
        //add option selected
        var id = $('.topFilters option[value="'+$(this).attr("data-id")+'"]').val();
        $(".custom-select2").each(function(){
            if($(this).find("option[value='"+id+"']").length > 0){
                $(this).val(id).trigger("change");
            }
        });
        
        get_listing_request_center_by_filters_ajax(type_id);
    });
    
    $("#load_more_listing_requests").click(function (e) {
        e.preventDefault();
        offset = parseInt($("#load_more_listing_requests").attr("data-offset"));
        $count = $("#load_more_listing_requests").attr("data-count");

        get_listing_request_center_by_ajax();
    });
    
    $(document).on("click",".trigger_click",function (e) {
        e.preventDefault();
        offset = 0;
        $count = $("#load_more_listing_requests").attr("data-count");
        
        //remove class active for all other li
        $(document).find(".type-list li").removeClass("active");
        //add class active to selected item
        $(document).find("a[data-id='"+$(this).attr("data-id")+"']").parent().addClass("active");

        get_listing_request_center_by_filters_ajax($(this).attr("data-id"));  
        
        if ($(".card-summary p").exists()) {
            $('.card-summary p').trunk8({
                    lines:3,
                    tooltip: false
            });
        }
        
        updateUrl($(this));
    });

    $(".reset-filters").click(function (e) {
      $(".custom-select2").val(null).trigger("change");
    });

    function updateUrl(item){
        if(item == '' || item == null)
            item = jQuery(document).find(".type-list .active").find('a');

        //check theme
        var request_theme = '';
        if(jQuery("#theme").select2("data")[0] !== undefined){
            theme = jQuery("#theme").select2("data")[0].text;
            request_theme = theme.trim();
            if(request_theme !== '')
                request_theme = '&theme='+request_theme;
        }
        
        //check target
        var request_target = '';
        if(jQuery("#target").select2("data")[0] !== undefined){
            target = jQuery("#target").select2("data")[0].text;
            request_target = target.trim();
            if(request_target !== '')
                request_target = '&target='+request_target;
        }
        
        var myURL = window.location.href.split('?')[0]+"?type="+item.attr("data-name")+request_theme+request_target;
        window.history.pushState({path: myURL}, '', myURL);
    }

    function get_listing_request_center_by_ajax(){
        //load top filters
        var theme = jQuery("#theme").val();
        var request_theme = theme.trim();
        var target = jQuery("#target").val(); 
        var request_target = target.trim();
        var data = {
            action: 'ef_load_more_listing_request_center',
            offset: offset,
            type: type_id,
            request_theme: request_theme,
            request_target: request_target
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
              offset = parseInt($("#load_more_listing_requests").attr("data-offset"));
              newOffset = parseInt(offset) + parseInt(ef_request_center.per_page);
              $("#load_more_listing_requests").attr("data-offset", newOffset);
              if (newOffset >= $count){
                newOffset = $count;
                $(".view-more").hide();
              }
              $("#load_requests_by_ajax_container").append(data);   
              
              $( '.ef-results-count' ).html( newOffset );
          }
        });
    }
    
    function get_listing_request_center_by_filters_ajax(type_id){
        $(".loading-overlay").removeClass('hidden');
        type_id = type_id;
        //load top filters
        var theme = jQuery("#theme").val();
        var request_theme = theme.trim();
        var target = jQuery("#target").val();
        var request_target = target.trim();
        var data = {
          action: 'ef_load_change_type_request_center',
          offset: offset,
          type: type_id,
          request_theme: request_theme,
          request_target: request_target
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
              $(".loading-overlay").addClass('hidden');
              $("#load_requests_by_ajax_container").html(data); 
              get_filteration_count(type_id);
              updateUrl();
          }
        });
    }
    
    //get new count
    function get_filteration_count(type_id){
        //load top filters
        var request_theme = jQuery("#theme").val();
        var request_target = jQuery("#target").val();
        var data = {
            action: 'ef_count_request_center_ajax',
            offset: offset,
            type: type_id,
            request_theme: request_theme,
            request_target: request_target
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
            newOffset = parseInt(ef_request_center.per_page);
            newCount = data;
            $("#load_more_listing_requests").attr("data-offset", newOffset);
            $("#load_more_listing_requests").attr("data-count", newCount);
            $count = newCount;
            if (newOffset >= $count){
              newOffset = $count;
              $(".view-more").hide();
            }else{
              $(".view-more").show();  
            }
            
            if( newCount == 0 ) {
              $( '.ef-results-meta' ).hide();
            }
            else {
              $( '.ef-results-meta' ).show();
            }
            
            $( '.ef-results-count' ).html( newOffset );
            $( '.ef-total-count' ).html( newCount );
            
            if( type_id != -1 ) {
                $( '.ef-category-name' ).html( '"' + $( "ul.type-list  li.active a" ).attr('data-title') + '"' );
                $( '.ef-category' ).show();
            }
            else {
                $( '.ef-category-name' ).html( '' );
                $( '.ef-category' ).hide();
            }
            
            //update line numbers
            if ($(".card-summary p").exists()) {
                $('.card-summary p').trunk8({
                        lines:3,
                        tooltip: false
                });
            }
          }
        });
    }
  });
}(jQuery));
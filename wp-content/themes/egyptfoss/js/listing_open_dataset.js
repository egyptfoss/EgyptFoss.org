(function ($) {
  $(document).ready(function () {
    offset = parseInt($("#load_more_listing_datasets").attr("data-offset"));
    $count = $("#load_more_listing_datasets").attr("data-count");
    theme_id = -1;
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
        var theme_id = jQuery(document).find(".categories-list .active").find('a').attr('data-id');
        get_listing_open_dataset_by_filters_ajax(theme_id);
    });
    
    $(".topFiltersInList").live('click', function(e){
        e.preventDefault();
        
        var theme_id = jQuery(document).find(".categories-list .active").find('a').attr('data-id');
        
        //add option selected
        var id = $('.topFilters option[value="'+$(this).attr("data-id")+'"]').val();
        $(".custom-select2").each(function(){
            if($(this).find("option[value='"+id+"']").length > 0)
            {
                $(this).val(id).trigger("change");
            }
        });
        
        get_listing_open_dataset_by_filters_ajax(theme_id);
    });
    
    $("#load_more_listing_datasets").click(function (e) {
        e.preventDefault();
        offset = parseInt($("#load_more_listing_datasets").attr("data-offset"));
        $count = $("#load_more_listing_datasets").attr("data-count");

        get_listing_open_dataset_by_ajax();
    });
    
    $(document).on("click",".trigger_click",function (e) {
        e.preventDefault();
        offset = 0;
        $count = $("#load_more_listing_datasets").attr("data-count");
        
        //remove class active for all other li
        $(document).find(".categories-list li").removeClass("active");
        //add class active to selected item
        $(document).find("a[data-id='"+$(this).attr("data-id")+"']").parent().addClass("active");

        get_listing_open_dataset_by_filters_ajax($(this).attr("data-id"));  
        
        updateUrl($(this));
        
        if ($(".card-summary p").exists()) {
            $('.card-summary p').trunk8({
                    lines:3,
                    tooltip: false
            });
        }
    });

    $(".reset-filters").click(function (e) {
      $(".custom-select2").val(null).trigger("change");
      //updateUrl();
    });

    function updateUrl(item)
    {
        if(item == '' || item == null)
            item = jQuery(document).find(".categories-list .active").find('a');
        //check type
        var dataset_type = '';
        if(jQuery("#type").select2("data")[0] !== undefined)
        {
            dataset_type = jQuery("#type").select2("data")[0].text;
            if(dataset_type !== '')
                dataset_type = '&type='+dataset_type;
        }
        
        //check license
        var dataset_license = '';
        if(jQuery("#license").select2("data")[0] !== undefined)
        {
            dataset_license = jQuery("#license").select2("data")[0].text;
            if(dataset_license !== '')
                dataset_license = '&license='+dataset_license;
        }
        
        //check formats
        var dataset_formats = '';
        if(jQuery("#format").select2("data")[0] !== undefined)
        {
            dataset_formats = jQuery("#format").select2("data")[0].text;
            if(dataset_formats !== '')
                dataset_formats = '&format='+dataset_formats;
        }
        
        //check publisher
        var dataset_publisher = '';
        if(jQuery("#publisher").select2("data")[0] !== undefined)
        {
            dataset_publisher = jQuery("#publisher").select2("data")[0].text;
            if(dataset_publisher !== '')
                dataset_publisher = '&publisher='+dataset_publisher;
        }
        
        var myURL = window.location.href.split('?')[0]+"?theme="+item.attr("data-slug")+dataset_type+dataset_license+dataset_formats+dataset_publisher;
        window.history.pushState({path: myURL}, '', myURL);
    }

    function get_listing_open_dataset_by_ajax(){
        //load top filters
        var dataset_type = jQuery("#type").val();
        var dataset_license = jQuery("#license").val(); 
        var dataset_formats = jQuery("#format").val();
        var data = {
            action: 'ef_load_more_listing_open_dataset',
            offset: offset,
            theme: theme_id,
            dataset_type: dataset_type,
            dataset_license: dataset_license,
            dataset_formats: dataset_formats
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
              offset = parseInt($("#load_more_listing_datasets").attr("data-offset"));
              newOffset = parseInt(offset) + parseInt(ef_open_dataset.per_page);
              $("#load_more_listing_datasets").attr("data-offset", newOffset);
              if (newOffset >= $count){
                  newOffset = $count;
                $(".view-more").hide();
              }
              $("#load_datasets_by_ajax_container").append(data); 
              
              $( '.ef-results-count' ).html( newOffset );
          }
        });
    }
    
    function get_listing_open_dataset_by_filters_ajax(them_id){
        $(".loading-overlay").removeClass('hidden');
        theme_id = them_id;
        //load top filters
        var dataset_type = jQuery("#type").val();
        var dataset_license = jQuery("#license").val();
        var dataset_formats = jQuery("#format").val();
        var dataset_publisher = jQuery("#publisher").val();
        
        var data = {
          action: 'ef_load_change_theme_open_dataset',
          offset: offset,
          theme: them_id,
          dataset_type: dataset_type,
          dataset_license: dataset_license,
          dataset_formats: dataset_formats,
          dataset_publisher: dataset_publisher
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
                $(".loading-overlay").addClass('hidden');
                $(".view-more").hide();
                get_filteration_count(theme_id);
                $("#load_datasets_by_ajax_container").html(data); 
                //update line numbers
                if ($(".card-summary p").exists()) {
                    $('.card-summary p').trunk8({
                            lines:3,
                            tooltip: false
                    });
                }                
                updateUrl();
          }
        });
      }
    
    //get new count
    function get_filteration_count(theme_id)
    {
        //load top filters
        var dataset_type = jQuery("#type").val();
        var dataset_license = jQuery("#license").val();
        var dataset_formats = jQuery("#format").val();
        var dataset_publisher = jQuery("#publisher").val();
        
        var data = {
            action: 'ef_count_open_dataset_ajax',
            offset: offset,
            theme: theme_id,
            dataset_type: dataset_type,
            dataset_license: dataset_license,
            dataset_formats: dataset_formats,
            dataset_publisher: dataset_publisher
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
            newOffset = parseInt(ef_open_dataset.per_page);
            newCount = data;
            $("#load_more_listing_datasets").attr("data-offset", newOffset);
            $("#load_more_listing_datasets").attr("data-count", newCount);
            $count = newCount;
            if (newOffset >= $count){
                newOffset = $count;
              $(".view-more").hide();
            }else
            {
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
            
            if( theme_id != -1 ) {
                $( '.ef-category-name' ).html( '"' + $( ".categories-list .active a" ).html() + '"' );
                $( '.ef-category' ).show();
            }
            else {
                $( '.ef-category-name' ).html( '' );
                $( '.ef-category' ).hide();
            }        
          }
        });
    }
  });
}(jQuery));
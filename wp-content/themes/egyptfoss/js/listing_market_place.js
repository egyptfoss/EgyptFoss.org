(function ($) {
  $(document).ready(function () {
    offset = parseInt($("#load_more_listing_services").attr("data-offset"));
    $count = $("#load_more_listing_services").attr("data-count");
    category_id = $(document).find(".categories-list .active").find('a').attr('data-id');
    
    if (offset >= $count){
      $(".view-more").hide();
    }
    
    //update select2 add remove button for each filter
    $(".custom-select2").each(function(){
      $(this).select2({
        allowClear: true,
        language: {
          noResults: function () {
            return jQuery.validator.messages.select2_no_results;
          }
        }
      });
    });
    
    $(".service-card .card-content h4").trunk8({ lines: 2, tooltip: false });
    $(".category_trim").trunk8({ lines: 1, tooltip: false });
    $(".service-offeredby").trunk8({lines: 2, tooltip: false});
    
    $(".topFilters").change(function (e) {
        e.preventDefault();
        
        get_listing_services_by_filters_ajax();
    });
    
    $("#load_more_listing_services").click(function (e) {
        e.preventDefault();
        offset = parseInt($("#load_more_listing_services").attr("data-offset"));
        $count = $("#load_more_listing_services").attr("data-count");
        $( '.ef-service-list-spinner' ).removeClass( 'hidden' );
        get_listing_service_by_ajax();
    });
    
    $(document).on("click",".trigger_click",function (e) {
        e.preventDefault();
        offset = 0;
        $count = $("#load_more_listing_services").attr("data-count");
        
        //remove class active for all other li
        $(document).find(".categories-list li").removeClass("active");
        //add class active to selected item
        $(document).find("a[data-id='"+$(this).attr("data-id")+"']").parent().addClass("active");
        category_id = $(this).attr("data-id");
        
        get_listing_services_by_filters_ajax();  
        
        updateUrl($(this));
    });

    $(".reset-filters").click(function (e) {
      $(".custom-select2").val(null).trigger("change");
    });
    
    $("#service-type").on('change', function(){
        var type = $(this).val();
        
        if (type == 'Individual') {
          sub_types = individuals_types;
        } else if (type == 'Entity') {
          sub_types = entities_types;
        }
        else {
          sub_types = [];
        }
        populateSubtypes(sub_types);
  });
  
    function populateSubtypes(sub_types) {
        $('#service-subtype optgroup option').remove();
        for (var key in sub_types) {
          $('#service-subtype optgroup').append('<option value="'+key+'">'+sub_types[key]+'</option>');
        }
        $('#service-subtype').select2({
          allowClear: true,
          language: {
            noResults: function () {
              return jQuery.validator.messages.select2_no_results;
            }
          }
        });
        $('#service-subtype').select2("val", "");
    }

    function updateUrl(item){
        if(item == '' || item == null)
            item = jQuery(document).find(".categories-list .active").find('a');

        //check technology
        var service_technology = '';
        if(jQuery( "#service-technology" ).select2( "data" )[0] !== undefined){
            technology = jQuery( "#service-technology" ).select2( "data" )[0].id;
            service_technology = technology.trim();
            if( service_technology !== '' )
                service_technology = '&technology=' + service_technology;
        }
        
        //check theme
        var service_theme = '';
        if(jQuery( "#service-theme" ).select2( "data" )[0] !== undefined){
            theme = jQuery( "#service-theme" ).select2( "data" )[0].id;
            service_theme = theme.trim();
            if( service_theme !== '' )
                service_theme = '&theme=' + service_theme;
        }
        
        //check type
        var service_type = '';
        if(jQuery( "#service-type" ).select2( "data" )[0] !== undefined){
            type = jQuery( "#service-type" ).select2( "data" )[0].id;
            service_type = type.trim();
            if( service_type !== '' )
                service_type = '&type=' + service_type;
        }
        
        //check subtype
        var service_subtype = '';
        if(jQuery( "#service-subtype" ).select2( "data" )[0] !== undefined){
            subtype = jQuery( "#service-subtype" ).select2( "data" )[0].id;
            service_subtype = subtype.trim();
            if( service_subtype !== '' )
                service_subtype = '&subtype=' + service_subtype;
        }
        
        var myURL = window.location.href.split('?')[0]
                    + "?category=" + item.attr("data-name") 
                    + service_technology + service_theme + service_type + service_subtype;
            
        window.history.pushState({path: myURL}, '', myURL);
    }

    function get_listing_service_by_ajax(){
        //load top filters
        var technology = jQuery("#service-technology").val();
        var service_technology = technology.trim();
        var theme = jQuery("#service-theme").val();
        var service_theme = theme.trim();
        var type = jQuery("#service-type").val();
        var service_type = type.trim();
        var subtype = jQuery("#service-subtype").val();
        var service_subtype = subtype?subtype.trim():subtype;
        
        var data = {
            action: 'ef_load_more_listing_service',
            offset: offset,
            category: category_id,
            service_technology: service_technology,
            service_theme  : service_theme,
            service_type      : service_type,
            service_subtype   : service_subtype
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
              offset = parseInt($("#load_more_listing_services").attr("data-offset"));
              newOffset = parseInt(offset) + parseInt(ef_market_place.per_page);
              $("#load_more_listing_services").attr("data-offset", newOffset);
              if (newOffset >= $count){
                  newOffset = $count;
                $(".view-more").hide();
              }
              $("#load_services_by_ajax_container").append(data);     
              $( '.ef-service-list-spinner' ).addClass( 'hidden' );
              $(".service-card .card-content h4").trunk8({ lines: 2, tooltip: false });
              $(".category_trim").trunk8({lines: 1,  tooltip: false });
              $(".service-offeredby").trunk8({lines: 2, tooltip: false});
              $(".rating-readonly").starRating({
		starSize: 25,
                emptyColor: 'lightgray',
                hoverColor: '#74b977',
                activeColor: '#4caf50',
                strokeWidth: 0,
                useGradient: false,
                readOnly: true,
            });
            
            $( '.ef-results-count' ).html( newOffset );
          }
        });
    }
    
    function get_listing_services_by_filters_ajax(){
        $(".loading-overlay").removeClass('hidden');
        
        //load top filters
        var technology = jQuery("#service-technology").val();
        var service_technology = technology.trim();
        var theme = jQuery("#service-theme").val();
        var service_theme = theme.trim();
        var type = jQuery("#service-type").val();
        var service_type = type.trim();
        var subtype = jQuery("#service-subtype").val();
        var service_subtype = subtype?subtype.trim():subtype;
        
        var data = {
          action: 'ef_load_more_listing_service',
          offset: 0,
          category: category_id,
          service_technology: service_technology,
          service_theme  : service_theme,
          service_type      : service_type,
          service_subtype   : service_subtype
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
            $(".loading-overlay").addClass('hidden');
            $("#load_services_by_ajax_container").html(data); 
            $(".service-card .card-content h4").trunk8({ lines: 2, tooltip: false });
            $(".category_trim").trunk8({ lines: 1,  tooltip: false });
            $(".service-offeredby").trunk8({lines: 2, tooltip: false});
            $(".rating-readonly").starRating({
		starSize: 25,
                emptyColor: 'lightgray',
                hoverColor: '#74b977',
                activeColor: '#4caf50',
                strokeWidth: 0,
                useGradient: false,
                readOnly: true,
            });
            get_filteration_count();
            updateUrl();
          }
        });
    }
    
    //get new count
    function get_filteration_count(){
        //load top filters
        var technology = jQuery("#service-technology").val();
        var service_technology = technology.trim();
        var theme = jQuery("#service-theme").val();
        var service_theme = theme.trim();
        var type = jQuery("#service-type").val();
        var service_type = type.trim();
        var subtype = jQuery("#service-subtype").val();
        var service_subtype = subtype?subtype.trim():subtype;
        
        var data = {
            action: 'ef_count_service_ajax',
            offset: offset,
            category: category_id,
            service_technology: service_technology,
            service_theme  : service_theme,
            service_type      : service_type,
            service_subtype   : service_subtype
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
            newOffset = parseInt(ef_market_place.per_page);
            newCount = data;
            $("#load_more_listing_services").attr("data-offset", newOffset);
            $("#load_more_listing_services").attr("data-count", newCount);
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
            
            if( category_id != -1 ) {
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
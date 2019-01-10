(function ($) {
  $(document).ready(function () {
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
    is_filter = 0;
    if (window.location.href.indexOf("?") != -1)
    {
      if (window.location.href.indexOf("?all") != -1)
      {
        is_filter = 0;
      } else
      {
        $("#filter-products").collapse();
        is_filter = 1;
      }
    }
    refreshCountAndOffest(is_filter);

    $("#load_more_product").click(function (e) {
      e.preventDefault();
      offest = parseInt($("#load_more_product").attr("data-offest"));
      $productCount = $("#load_more_product").attr("data-count");
      $("#load_more_product").addClass("hidden");
      $(".ef-product-list-spinner").removeClass("hidden");

      get_filtered_products_by_ajax(1);
    });

    function refreshCountAndOffest(is_filter)
    {
      offest = parseInt($("#load_more_product").attr("data-offest"));
      $newCount = $(".ef_product_set").last().attr("data-filtered-product-count");
      $("#filtered_product_count").html($newCount);
      $("#load_more_product").attr("data-count", $newCount);
      newOffest = parseInt(offest) + parseInt(ef_products.per_page);
      checkEmptyResults($newCount, is_filter);
      if(is_filter)
      {
        newOffest = parseInt(ef_products.per_page);
      }
      if ($newCount <= newOffest)
      {
        newOffest = $newCount;
        $("#load_more_product").addClass("hidden");
      } else
      {
        $("#load_more_product").removeClass("hidden");
      }
      $("#load_more_product").attr("data-offest", newOffest);
      
      if( $newCount == 0 ) {
        $( '.ef-results-meta' ).hide();
      }
      else {
        $( '.ef-results-meta' ).show();
      }

      $( '.ef-results-count' ).html( newOffest );
      $( '.ef-total-count' ).html( $newCount );
      
      var category_id = $( ".categories-list .active a" ).attr( 'data-id' );
      if( category_id != '' && category_id != 'top-ten' ) {
          $( '.ef-category-name' ).html( '"' + $( ".categories-list .active a" ).attr('data-name') + '"' );
          $( '.ef-category' ).show();
      }
      else {
          $( '.ef-category-name' ).html( '' );
          $( '.ef-category' ).hide();
      }
    }
    function get_filtered_products_by_ajax(isLoadMore)
    {
      term_ids = [];
      browseProductsBy = "";
      count = 0;
       $(".topFilters").each(function (index) {
         if($(this).val() != "undefined" && $(this).val() != "")
         {
           term_ids.push($(this).val());
         }
      });
      
      $(".leftFilter[selected]").each(function (index) {
          if($(this).attr("data-id") != "" && $(this).attr("data-id") != "featured")
          {
            term_ids.push($(this).attr("data-id"));
            browseProductsBy = $(this).attr("data-id");
          }else
          {
            if($(this).attr("data-id") == "featured")
            {
              browseProductsBy = "featured";
            }
          }
      });
      
      
      if (isLoadMore == 1)
      {
        offest = parseInt($("#load_more_product").attr("data-offest"));
      } else
      {
        offest = 0;
        $(".loading-overlay").removeClass('hidden');
      }

      var data = {
        action: 'ef_load_more_filtered_products',
        term_ids: term_ids,
        offest: offest,
        newUrl: updateUrl(),
        browseProductsBy: browseProductsBy
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        async: false,
        success: function (data) {
          if (isLoadMore == 1) {
            $(".ef-product-list-spinner").addClass("hidden");
            refreshCountAndOffest(0);
            //$scrollHeight = $("#load_product_by_ajax_container div.ef_product_set:last")[0].scrollHeight;
            $offest = parseInt($("#load_more_product").attr("data-offest")) - parseInt(ef_products.per_page);
            $('body,html').animate({ scrollTop: (102* $offest) + 200 }, 1000);
            $("#load_product_by_ajax_container").append(data);
          } else {
            $("#load_product_by_ajax_container").html(data);
            $(window).scrollTop(0);
            $newCount = $(".ef_product_set").last().attr("data-filtered-product-count");
            $("#filtered_product_count").html($newCount);
            $("#load_more_product").attr("data-count", $newCount);
            refreshCountAndOffest(1);
            $(".loading-overlay").addClass('hidden');
          }
        }
      });
    }
    $(".topFilters").change(function (e) {
      e.preventDefault();
       $(".browseFilter[selected]").each(function (index) {
            $(this).removeAttr('selected');
            $(this).parent().removeClass('active');
      });
      
      get_filtered_products_by_ajax(0);
    });
    
    $(".leftFilter").click(function (e) {
      if($(this).attr("data-id") == "top-ten")
      {
         e.preventDefault();
         $(".industry-list li a").removeAttr('selected');
         $(".industry-list li").removeClass('active');
         $(this).parent().addClass("active");
         $(this).attr("selected", "true");
         $(".normalListingProduct").addClass("hidden");
         $(".topTenListingProduct").removeClass("hidden");
         $(".loading-overlay_top_ten").removeClass('hidden');
         get_top_ten_products_by_ajax(0,[],0);

      }else
      {
        e.preventDefault();
      $(".industry-list li a").removeAttr('selected');
      $(".industry-list li").removeClass('active');
      
      if($(this).attr("data-id") == "top-ten")
      {
        $(".normalListingProduct").addClass("hidden");
        $(".topTenListingProduct").removeClass("hidden");
      }else
      {
        $(".normalListingProduct").removeClass("hidden");
        $(".topTenListingProduct").addClass("hidden");
      }
      
      if ($(this).attr("data-id") != "")
      {
        $(this).attr("selected", "true");
      } else
      {
        $(this).parent().addClass("active");
        $(this).attr("selected", "true");
      }
      
      $(".leftFilter[selected]").each(function (index) {
        $(this).parent().addClass("active");
      });

      get_filtered_products_by_ajax(0);  
    }
    });
    
    $(".reset-filters").click(function (e) {
      $(".custom-select2").val(null).trigger("change");
      updateUrl();
    });

    function updateUrl()
    {
      pageurl = "";
      $(".topFilters").each(function (index) {
        if (index == 0 && pageurl == "")
        {
          pageurl += "?";
        }
        if($(this).find('option:selected').attr('value') != "")
        {
          pageurl += $(this).attr("data-taxonomy") + "=" + $(this).find('option:selected').attr('data-slug');
          pageurl += "&";
        }
      });


      $(".leftFilter[selected]").each(function (index) {
        if (pageurl == "")
        {
          pageurl += "?";
        }
        if($(this).attr("data-id") != "" && $(this).attr("data-id") != "featured" && $(this).attr("data-id") != "top-ten")
        {
            pageurl += "industry=" + $(this).attr("data-slug");
            pageurl += "&";
        }else
        {
          if($(this).attr("data-id") == "featured" || $(this).attr("data-id") == "top-ten")
          {
            pageurl += "industry=" + $(this).attr("data-id");
            pageurl += "&";
          }
        }
      });
      
      
      pageurl = pageurl.slice(0, -1);
      if (pageurl != window.location) {
        window.history.pushState({path: pageurl}, '', pageurl);
      }
      //alert();
      lang = (ef_products.current_lang == "ar")?"en":"ar"; 
      $(".pll-lang-url").attr("href",ef_products.site_url+"/"+lang+"/products/"+pageurl);
      
      return pageurl;
    }
    function checkEmptyResults(count, is_filter)
    {
      if (count > 0)
      {
        
        $(".noProductsFound").addClass("hidden");
        $(".noResultsFound").addClass("hidden");
        $(".noFeaturedFound").addClass("hidden");
      } else
      {
        $(".noProductsFound").addClass("hidden");
        $(".noResultsFound").addClass("hidden");
        $(".noFeaturedFound").addClass("hidden");
        
        $(".leftFilter[selected]").each(function (index) {
          if($(this).attr("data-id") == "featured")
          {
            $(".noFeaturedFound").removeClass("hidden");
          }else
          {
            $(".noProductsFound").removeClass("hidden");
          }
        });
        //hasProducts = $(".noProductsFound").hasClass("hidden");
       /* if (is_filter == 1)
        {
          if(hasProducts)
          {
            $(".noResultsFound").removeClass("hidden");
          }
        } else
        {
          $(".noProductsFound").removeClass("hidden");
        }*/
      }
    }
    
    function get_top_ten_products_by_ajax(offset,term_ids,isLoadMore)
    {
      var data = {
        action: 'ef_load_more_top_ten_products',
        offset: offset,
        random_term_ids: term_ids
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        async: false,
        success: function (data) {
          if (isLoadMore == 0)
          {
            $("#load_more_top_ten_product_container").html(data);
            $("#load_more_top_ten_product").attr("data-random",$("#load_more_top_ten_product_container input[name='random_term_ids']:last").val());
            $(".loading-overlay_top_ten").addClass('hidden');
            $(".topTenListingProduct").removeClass("hidden");
            if ($('body').hasClass('rtl')) {
              $slidedir = true
            } else {
              $slidedir = false
            }
            $(".default-carousel").owlCarousel({
              items: 5,
              margin: 10,
              stagePadding: 40,
              nav: true,
              slideBy: 5,
              rtl: $slidedir,
              navText: false,
              smartSpeed: 60,
              dots: false,
              navContainerClass: 'owl-buttons',
              responsive: {
                // breakpoint from 0 up
                0: {
                  items: 1,
                  nav: false,
                  dotsEach: 1,
                  dots: true,
                  slideBy: 1
                },
                // breakpoint from 480 up
                480: {
                  items: 2,
                  nav: false,
                  dotsEach: 2,
                  dots: true,
                  slideBy: 2
                },
                // breakpoint from 768 up
                768: {
                  items: 5,
                  nav: true,
                }
              }
            });
            updateUrl();
            
          } else
          {
            $(".ef-product-list-spinner").addClass("hidden");
            $('body,html').animate({scrollTop: $('.featured-group:last').offset().top}, 1000);
            $("#load_more_top_ten_product_container").append(data);
            if ($('body').hasClass('rtl')) {
              $slidedir = true
            } else {
              $slidedir = false
            }
            $(".default-carousel").owlCarousel({
              items: 5,
              margin: 10,
              stagePadding: 40,
              nav: true,
              slideBy: 5,
              rtl: $slidedir,
              navText: false,
              smartSpeed: 60,
              dots: false,
              navContainerClass: 'owl-buttons',
              responsive: {
                // breakpoint from 0 up
                0: {
                  items: 1,
                  nav: false,
                  dotsEach: 1,
                  dots: true,
                  slideBy: 1
                },
                // breakpoint from 480 up
                480: {
                  items: 2,
                  nav: false,
                  dotsEach: 2,
                  dots: true,
                  slideBy: 2
                },
                // breakpoint from 768 up
                768: {
                  items: 5,
                  nav: true,
                }
              }
            });
            $("#load_more_top_ten_product").removeClass("hidden");
            $(".ef-top-ten-product-list-spinner").addClass("hidden");
          }
        }
      });
    }
    $("#load_more_top_ten_product").click(function (e) {
      e.preventDefault();
      offset = parseInt($("#load_more_top_ten_product").attr("data-offset"));
      count = parseInt($("#load_more_top_ten_product").attr("data-count"));
      term_ids = ($("#load_more_top_ten_product").attr("data-random"));
      $("#load_more_top_ten_product").addClass("hidden");
      $(".ef-top-ten-product-list-spinner").removeClass("hidden");

      get_top_ten_products_by_ajax(offset,term_ids,1);
      newOffset = offset + 5
      $("#load_more_top_ten_product").attr("data-offset",newOffset);
      if(parseInt(newOffset) >= parseInt(count))
      {
        $("#load_more_top_ten_product").addClass("hidden");
      }
    });
     offset = $("#load_more_top_ten_product").attr("data-offset");
     count = $("#load_more_top_ten_product").attr("data-count");
     
      if(parseInt(offset) < parseInt(count))
      {
        $("#load_more_top_ten_product").removeClass("hidden");
      }
  });
    
    


}(jQuery));
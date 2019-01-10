(function ($) {
  $(document).ready(function () {
    $('#admin-top-ten-products-list').multiSelect({
      selectableHeader: "<div class='lou_custom_header'>All products</div><input type='text' class='lou_search-input' autocomplete='off' placeholder='search'>",
      selectionHeader: "<div class='lou_custom_header'>Top 10 products</div><input type='text' class='lou_search-input' autocomplete='off' placeholder='search'>",
      selectableFooter: "",
      selectionFooter: "",
      afterInit: function (ms) {
        var that = this,
          $selectableSearch = that.$selectableUl.prev(),
          $selectionSearch = that.$selectionUl.prev(),
          selectableSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selectable:not(.ms-selected)',
          selectionSearchString = '#' + that.$container.attr('id') + ' .ms-elem-selection.ms-selected';

        that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
          .on('keydown', function (e) {
            if (e.which === 40) {
              that.$selectableUl.focus();
              return false;
            }
          });

        that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
          .on('keydown', function (e) {
            if (e.which == 40) {
              that.$selectionUl.focus();
              return false;
            }
          });   
      },
      afterSelect: function () {
        this.qs1.cache();
        this.qs2.cache();
      },
      afterDeselect: function () {
        this.qs1.cache();
        this.qs2.cache();
      }
    });
    toggleSelectionPanel(0);
    $("#industry").select2({ width: '300px ' });
     oldList = $(".ms-selection li.ms-selected").length;
     chosenIndustry = $("#industry").val();
    $("#industry").change(function (e) {
      if (parseInt($(".ms-selection li.ms-selected").length) !== parseInt(oldList))
      {
        if (!confirm("data you have entered may not be saved, are you sure you want to leave ?")) {
          $("#industry").val(chosenIndustry).trigger('change.select2');
          return false;
        }
      }
      results = ef_admin_load_products($(this).val());
      chosenIndustry = $(this).val();
    });
    
    $('#admin-top-ten-products-list').change(function(e){
      toggleSelectionPanel(1);
    });
    
    $('#save_top_ten_form').submit(function(e){
      if(parseInt($(".ms-selection li.ms-selected").length) !== 10)
      {
        alert("please choose 10 products ");
        return false;
      }
    });

    function ef_admin_load_products(term_id) {
      var results = 0;
      var data = {
        action: 'ef_admin_load_products',
        term_id: term_id,
        async: false,
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
          $("#admin-top-ten-products-list").html(data);
          $('#admin-top-ten-products-list').multiSelect('refresh');
          toggleSelectionPanel(0);
          oldList = $(".ms-selection li.ms-selected").length;
        }
      });
    }
    
    function toggleSelectionPanel(onchange)
    {
      if($('#admin-top-ten-products-list option:selected').length >= 10)
       {  
         $('.ms-elem-selectable').addClass("disabled");
         if(onchange == 1)
         {
            alert("10 products selected");
          }
       }else
       {
        $('.ms-elem-selectable').removeClass("disabled");
       }
    }
  });
}(jQuery));
jQuery(document).ready(function ($) {
  $('#expert_thought_form').validate({
    onfocusout: function (element) {
      $(element).valid();
    },
    focusInvalid: false,
    invalidHandler: function(form, validator) {
      if (!validator.numberOfInvalids())
        return;
      $('html, body').animate({
        scrollTop: $(validator.errorList[0].element).offset().top - 100
      }, 500);
    },
    rules: {
      expert_thought_title: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/,
        minlength: 10,
        maxlength: 100,
      },
      expert_thought_description: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      }
    },
    errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate"));
        },
    messages: {
      expert_thought_title: {
        required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern),
        minlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_minlength),
        maxlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_maxlength)
      },
      expert_thought_description: {
        required: ($.validator.messages.ef_content + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_content + " " + $.validator.messages.ef_pattern)
      }
    }
  });
  
  if (ef_expert.crud == "list") {
    $("#load_more_thoughts").click(function (e) {
      e.preventDefault();
      offest = parseInt($("#load_more_thoughts").attr("data-offset"));
      $productCount = $("#load_more_thoughts").attr("data-count");
      $("#load_more_thoughts").addClass("hidden");
      $(".ef-thoughts-list-spinner").removeClass("hidden");
      var data = {
        action: 'ef_load_more_thoughts',
        offset: offest,
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        async: false,
        success: function (data) {
          refreshOffest();
          $(".ef-thoughts-list-spinner").addClass("hidden");
          $offest = parseInt($("#load_more_thoughts").attr("data-offset")) - parseInt(ef_expert.per_page);
          //$('body,html').animate({scrollTop:20}, 1000);
          $("#expertThoughtList").append(data);
        }
      });
    });
    
    function refreshOffest()
    {
      offest = parseInt($("#load_more_thoughts").attr("data-offset"));
      dataCount = $("#load_more_thoughts").attr("data-count");
      newOffest = parseInt(offest) + parseInt(ef_expert.per_page);   
      $("#load_more_thoughts").attr("data-offset", newOffest);
      if (dataCount <= newOffest)
      {
        $("#load_more_thoughts").addClass("hidden");
      } else
      {
        $("#load_more_thoughts").removeClass("hidden");
      }
      $("#load_more_thoughts").attr("data-offsst", newOffest);
    }
  }
});
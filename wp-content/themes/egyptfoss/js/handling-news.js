jQuery(document).ready(function ($) {
  $('#add_news').validate({
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
      news_title: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/,
        minlength: 10,
        maxlength: 100,
      },
      news_subtitle: {
        pattern: /[أ-يa-zA-Z]+/
      },
      news_description: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },
      news_category: {
        required: true
      },      
      news_image: {
          required: true
      }
    },
    errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate"));
        },
    messages: {
      news_title: {
        required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern),
        minlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_minlength),
        maxlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_maxlength),
      },
      news_subtitle: {
        required: ($.validator.messages.ef_subtitle + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_subtitle + " " + $.validator.messages.ef_pattern),
      },
      news_description: {
        required: ($.validator.messages.ef_description + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_description + " " + $.validator.messages.ef_pattern),
      },
      news_category: {
        required: ($.validator.messages.ef_category + " " + $.validator.messages.ef_required),  
      },
      news_image: {
          required: ($.validator.messages.ef_image + " " + $.validator.messages.ef_required),
      }
    },
  });

});
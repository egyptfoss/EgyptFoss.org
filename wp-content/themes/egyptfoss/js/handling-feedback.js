jQuery(document).ready(function ($) {
  $('#add_feedback').validate({
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
      feedback_title: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/,
        minlength: 10,
        maxlength: 100,
      },
      feedback_description: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      }
    },
    errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate"));
    },
    messages: {
      feedback_title: {
        required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern),
        minlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_minlength),
        maxlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_maxlength)
      },
      feedback_description: {
        required: ($.validator.messages.ef_content + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_content + " " + $.validator.messages.ef_pattern)
      }
    }
  });
});
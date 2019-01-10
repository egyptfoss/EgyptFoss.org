jQuery(document).ready(function ($) {
  $('#add_success_story').validate({
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
      success_story_title: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/,
        minlength: 10,
        maxlength: 100,
      },
      success_story_description: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },
      post_category: {
        required: true
      },      
      success_story_image: {
          required: true
      }
    },
    errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            error.appendTo($("#" + name + "_validate"));
        },
    messages: {
      success_story_title: {
        required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern),
        minlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_minlength),
        maxlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_maxlength)
      },
      success_story_description: {
        required: ($.validator.messages.ef_content + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_content + " " + $.validator.messages.ef_pattern)
      },
      post_category: {
        required: ($.validator.messages.ef_category + " " + $.validator.messages.ef_required),  
      },
      success_story_image: {
          required: ($.validator.messages.ef_image + " " + $.validator.messages.ef_required)
      }
    }
  });
  $('#add_success_story').on('submit', function(){
    if ( $('#add_success_story').valid() ) {
      document.getElementById('submit_success_story').disabled = true;
    }
  });
});
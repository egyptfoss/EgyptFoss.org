jQuery(document).ready(function ($) {
  $('#edit_product').validate({
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
      product_title: {
        required: true,
        minlength: 2,
        maxlength: 100,
        pattern: /[أ-يa-zA-Z]+/
      },
      description: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },
      developer: {
        pattern: /[أ-يa-zA-Z]+/
      },
      functionality: {
        pattern: /[أ-يa-zA-Z]+/
      },
      post_industry: {
        required: true,
      },
      usage_hints: {
        pattern: /[أ-يa-zA-Z]+/
      },
      references: {
        pattern: /[أ-يa-zA-Z]+/
      },
      link_to_source: {
        pattern: /^(https?):\/\/[^ ]+\.[^ ]+$/
      },
    },
    messages: {
      product_title: {
        required: $.validator.messages.addproduct_title_required,
        minlength: jQuery.validator.format("Enter at least {0} characters"),
        maxlength: jQuery.validator.format("Enter no more than {0} characters"),
        pattern: $.validator.messages.addproduct_title_pattern
      },
      description: {
        required: $.validator.messages.addproduct_description_required,
        pattern: $.validator.messages.addproduct_description_pattern
      },
      developer: $.validator.messages.addproduct_developer,
      functionality: $.validator.messages.profile_functionality,
      post_industry: $.validator.messages.addproduct_industry,
      usage_hints: $.validator.messages.addproduct_usage,
      references: $.validator.messages.addproduct_references,
      link_to_source: $.validator.messages.profile_gplus_url
    },
  });

  var $form = $('#edit_product'),
    origForm = $form.serialize();
  $('#edit_product').on('submit', function () {
    if ($form.serialize() == origForm)
    {
      input = $("<input>")
        .attr("type", "hidden")
        .attr("name", "form_changed").val("false");

    } else
    {
      input = $("<input>")
        .attr("type", "hidden")
        .attr("name", "form_changed").val("true");
    }
    $('#edit_product').append($(input));
  });

});
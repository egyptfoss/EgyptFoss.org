jQuery(document).ready(function($) {

  function taxonomy_validations(selectElement) {
    var valid = true,
        taxonomy_valid = true,
        re = new RegExp("[أ-يa-zA-Z]+");
    taxonomy_values = selectElement.select2('data');
    for (t = 0; t < taxonomy_values.length; t++) {
      taxonomy = taxonomy_values[t];
      if (!re.test(taxonomy.text)) {
//        selectElement.closest('.form-group').addClass('has-error');
        selectElement.siblings('.error').show();
        taxonomy_valid = valid = false;
      }
    }
    if(taxonomy_valid) {
//      selectElement.closest('.form-group').removeClass('has-error');
      selectElement.siblings('.error').hide();
    }
    return valid;
  }

  function validate_venue(selectElement) {
    var valid = true,
        new_form_name = ($("input#venue_name").exists()) ? $("input#venue_name").val() : "",
        new_form_address = ($("input#venue_address").exists()) ? $("input#venue_address").val() : "",
        exists = ((new_form_name != "") && ($("#venue option[value='"+new_form_name+"']").length > 0));
    if( (selectElement.val() == "") && (new_form_name == "") && (new_form_address == "") ){
      valid = false;
//      selectElement.siblings('.label').css('color','#c00202');
      selectElement.siblings('#venue-error').show();
      selectElement.siblings('#venue-duplicate').hide();
    } else if (exists) {
      valid = false;
//      selectElement.siblings('.label').css('color','#c00202');
      selectElement.siblings('#venue-error').hide();
      selectElement.siblings('#venue-duplicate').show();
    } else {
//      selectElement.siblings('.label').css('color','#333');
      selectElement.siblings('#venue-error').hide();
      selectElement.siblings('#venue-duplicate').hide();
    }
    return valid;
  }

  function validate_organizer(selectElement) {
    var valid = true,
        new_form_name = ($("input#organizer_name").exists()) ? $("input#organizer_name").val() : "",
        exists = ((new_form_name != "") && ($("#organizer option[value='"+new_form_name+"']").length > 0));
    if( (selectElement.val() == "") && (new_form_name == "") ){
      valid = false;
      //selectElement.siblings('.label').css('color','#c00202');
      selectElement.siblings('#organizer-error').show();
      selectElement.siblings('#organizer-duplicate').hide();
    } else if (exists) {
//      selectElement.siblings('.label').css('color','#c00202');
      selectElement.siblings('#organizer-error').hide();
      selectElement.siblings('#organizer-duplicate').show();
    } else {
      //selectElement.siblings('.label').css('color','#333');
      selectElement.siblings('#organizer-error').hide();
      selectElement.siblings('#organizer-duplicate').hide();
    }
    return valid;
  }

  $("#venue").change(function () {
    $("#add-venue").show();
    $("#cancel-venue").hide();
    if ($(this).val() == '') {
      $("#new-venue-link").show();
    } else {
      $("#venue_name").val('');
      $("#venue_address").val('');
      $("#venue_country").val('');
      $("#venue_city").val('');
      $("#venue_phone").val('');
      venue_element = $("select#venue");
      valid_venue = validate_venue(venue_element);
      $("#new-venue").collapse('hide');
      $("#new-venue-link").hide();
    }
  });

  $("#organizer").change(function () {
    $("#add-organizer").show();
    $("#cancel-organizer").hide();
    if ($(this).val() == '') {
      $("#new-organizer-link").show();
    } else {
      $("#organizer_name").val('');
      $("#organizer_email").val('');
      $("#organizer_phone").val('');
      organizer_element = $("select#organizer");
      valid_organizer = validate_organizer(organizer_element);
      $("#new-organizer").collapse('hide');
      $("#new-organizer-link").hide();
    }
  });

  $("#add_event, #edit_event").on('submit', function(e){
    venue_element = $("select#venue");
    organizer_element = $("select#organizer");
    valid_venue = validate_venue(venue_element);
    valid_organizer = validate_organizer(organizer_element);
    if( !valid_venue || !valid_organizer ) {
      e.preventDefault();
      return false;
    }
    taxonomies_fields = ['technology', 'platform', 'interest'];
    for (i = 0; i < taxonomies_fields.length; i++) {
      taxonomy_field = taxonomies_fields[i];
      select_element = $("#"+taxonomy_field);
      if( !taxonomy_validations(select_element) ) {
        e.preventDefault();
        return false;
      }
    }
  });

  $(".venue-group input").on('blur', function(e) {
    venue_element = $("select#venue");
    valid_venue = validate_venue(venue_element);
  });

  $(".organizer-group input").on('blur', function(e) {
    organizer_element = $("select#organizer");
    valid_organizer = validate_organizer(organizer_element);
  });

  $('#add_event').validate({
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
      event_title: {
        required: true,
        minlength: 2,
        maxlength: 100,
        pattern: /[أ-يa-zA-Z]+/
      },
      description:{
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },
      event_type: {
        required: true,
      },
      start_datetime: {
        required: true,
        after_now: true
      },
      end_datetime: {
        required: true,
        after_date: "#start_datetime"
      },
      venue_name: {
        required_if_new: "#venue",
        pattern: /[أ-يa-zA-Z]+/
      },
      venue_address: {
        required_if_new: "#venue",
        pattern: /[أ-يa-zA-Z]+/
      },
      venue_city: {
        pattern: /[أ-يa-zA-Z]+/
      },
      venue_country: {
        pattern: /[أ-يa-zA-Z]+/
      },
      venue_phone: {
        phone_number: true
      },
      organizer_name: {
        required_if_new: "#organizer",
        pattern: /[أ-يa-zA-Z]+/
      },
      organizer_phone: {
        phone_number: true
      },
      organizer_email: {
        email: true
      },
      website: {
        url: true
      },
      audience: {
        pattern: /[أ-يa-zA-Z]+/
      },
      objectives: {
        pattern: /[أ-يa-zA-Z]+/
      },
      prerequisites: {
        pattern: /[أ-يa-zA-Z]+/
      },
      functionality: {
        pattern: /[أ-يa-zA-Z]+/
      },
    },

    messages: {
      event_title: {
        required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
        minlength: $.validator.messages.general_minlength_2,
        maxlength: $.validator.messages.signup_username_maxlength,
        pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern)
      },
      description:{
        required: ($.validator.messages.ef_description + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_description + " " + $.validator.messages.ef_pattern)
      },
      event_type: $.validator.messages.event_type,
      start_datetime: {
        required: $.validator.messages.start_datetime,
        after_now: $.validator.messages.after_now
      },
      end_datetime: {
        required: $.validator.messages.end_datetime,
        after_date: $.validator.messages.after_date
      },
      venue_name: {
        required_if_new: $.validator.messages.required_field,
        pattern: $.validator.messages.venue_name,
      },
      venue_address: {
        required_if_new: $.validator.messages.required_field,
        pattern: $.validator.messages.venue_address,
      },
      venue_city: $.validator.messages.venue_city,
      venue_country: $.validator.messages.venue_country,
      organizer_name: {
        required_if_new: $.validator.messages.required_field,
        pattern: $.validator.messages.organizer_name,
      },
      organizer_email: $.validator.messages.organizer_email,
      audience: $.validator.messages.audience,
      objectives: $.validator.messages.objectives,
      prerequisites: $.validator.messages.prerequisites,
      functionality: $.validator.messages.functionality,
    },
    // Fire bootstrap errors
    highlight: function(element) {
      if (element.type !== 'search') {
//        $(element).closest('.form-group').addClass('has-error');
      }
    },
    unhighlight: function(element) {
      if (element.type !== 'search') {
//        $(element).closest('.form-group').removeClass('has-error');
      }
    },
    errorElement: 'span',
    errorClass: 'error',
    errorPlacement: function(error, element) {
      if(element.parent('.form-group').length) {
        error.insertAfter(element.parent());
      } else {
        error.insertAfter(element);
      }
    }
  });

  $('#edit_event').validate({
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
      event_title: {
        required: true,
        minlength: 2,
        maxlength: 100,
        pattern: /[أ-يa-zA-Z]+/
      },
      description:{
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },
      event_type: {
        required: true,
      },
      start_datetime: {
        required: true,
        after_now: true
      },
      end_datetime: {
        required: true,
        after_date: "#start_datetime"
      },
      venue_name: {
        required_if_new: "#venue",
        pattern: /[أ-يa-zA-Z]+/
      },
      organizer_name: {
        required_if_new: "#organizer",
        pattern: /[أ-يa-zA-Z]+/
      },
      website: {
        url: true
      },
      audience: {
        pattern: /[أ-يa-zA-Z]+/
      },
      objectives: {
        pattern: /[أ-يa-zA-Z]+/
      },
      prerequisites: {
        pattern: /[أ-يa-zA-Z]+/
      },
      functionality: {
        pattern: /[أ-يa-zA-Z]+/
      },
    },

    messages: {
      event_title: {
        required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
        minlength: $.validator.messages.general_minlength_2,
        maxlength: $.validator.messages.signup_username_maxlength,
        pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern)
      },
      description:{
        required: ($.validator.messages.ef_description + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_description + " " + $.validator.messages.ef_pattern)
      },
      event_type: $.validator.messages.event_type,
      start_datetime: {
        required: $.validator.messages.start_datetime,
        after_now: $.validator.messages.after_now
      },
      end_datetime: {
        required: $.validator.messages.end_datetime,
        after_date: $.validator.messages.after_date
      },
      venue_name: {
        required_if_new: $.validator.messages.required_field,
        pattern: $.validator.messages.venue_name,
      },
      organizer_name: {
        required_if_new: $.validator.messages.required_field,
        pattern: $.validator.messages.organizer_name,
      },
      audience: $.validator.messages.audience,
      objectives: $.validator.messages.objectives,
      prerequisites: $.validator.messages.prerequisites,
      functionality: $.validator.messages.functionality,
    },
    // Fire bootstrap errors
    highlight: function(element) {
      if (element.type !== 'search') {
//        $(element).closest('.form-group').addClass('has-error');
      }
    },
    unhighlight: function(element) {
      if (element.type !== 'search') {
//        $(element).closest('.form-group').removeClass('has-error');
      }
    },
    errorElement: 'span',
    errorClass: 'error',
    errorPlacement: function(error, element) {
      if(element.parent('.form-group').length) {
        error.insertAfter(element.parent());
      } else {
        error.insertAfter(element);
      }
    }
  });
});
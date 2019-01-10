jQuery(document).ready(function ($) {
  if (ef_service.is_admin == 1) {
    $(".select2_admin_region").select2({width: '140px'});
  } else {
    function taxonomy_validations(selectElement,tax) {
      var valid = true,
          taxonomy_valid = true,
          re = new RegExp("[أ-يa-zA-Z]+");
      taxonomy_values = selectElement.select2('data');
      for (t = 0; t < taxonomy_values.length; t++) {
        taxonomy = taxonomy_values[t];
        if (!re.test(taxonomy.text)) {
          $("#"+tax+"_error").show("");
          $("#"+tax+"_error").html($.validator.messages["ef_"+tax] + " " + $.validator.messages.ef_pattern);
          
          taxonomy_valid = valid = false;
        }
      }
      if(taxonomy_valid) {
        $("#"+tax+"_error").hide("");
      }
      return valid;
    }

    var forms_validate = ["add_service", "edit_service"];
    for(var i = 0; i < forms_validate.length; i++) {
      $("#"+forms_validate[i]).on('submit', function(e){
        taxonomies_fields = ['technology', 'interest'];
        for (i = 0; i < taxonomies_fields.length; i++) {
          taxonomy_field = taxonomies_fields[i];
          select_element = $("#"+taxonomy_field);
          if( !taxonomy_validations(select_element,taxonomies_fields[i]) ) {
            e.preventDefault();
            return false;
          }
        }
      });
    }

    $("#interest").on('change', function(e){
      taxonomies_fields = ['interest'];
      for (i = 0; i < taxonomies_fields.length; i++) {
        taxonomy_field = taxonomies_fields[i];
        select_element = $("#"+taxonomy_field);
        if( !taxonomy_validations(select_element,taxonomies_fields[i]) ) {
          e.preventDefault();
          return false;
        }
      }
    });

    $("#technology").on('change', function(e){
      taxonomies_fields = ['technology'];
      for (i = 0; i < taxonomies_fields.length; i++) {
        taxonomy_field = taxonomies_fields[i];
        select_element = $("#"+taxonomy_field);
        if( !taxonomy_validations(select_element,taxonomies_fields[i]) ) {
          e.preventDefault();
          return false;
        }
      }
    });

    for(var i = 0; i < forms_validate.length; i++) {
      $('#'+forms_validate[i]).validate({
        onfocusout: function (element) {
          $(element).valid();
        },
        focusInvalid: false,
        invalidHandler: function (form, validator) {
          if (!validator.numberOfInvalids())
            return;
          $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top - 100
          }, 500);
        },
        rules: {
          service_title: {
            required: true,
            minlength: 10,
            maxlength: 100,
            pattern: /[أ-يa-zA-Z]+/
          },
          service_category: {
            required: true,
          },
          service_image: {
            required: (true && i == 0)
          },
          service_description: {
            required: true,
            pattern: /[أ-يa-zA-Z]+/
          },
          service_constraints: {
            pattern: /[أ-يa-zA-Z]+/
          },
          service_conditions: {
            pattern: /[أ-يa-zA-Z]+/
          },
        },
        errorPlacement: function (error, element) {
          if(element.attr("name") == "service_image") {
            var id = $(element).attr("id");
            error.appendTo($("#" + id + "_validate"));
          } else {
            error.insertAfter(element);
          }
        },
        messages: {
          service_title: {
            required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
            pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern),
            minlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_minlength),
            maxlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_maxlength)
          },
          service_image: {
            required: ($.validator.messages.ef_image + " " + $.validator.messages.ef_required),
          },
          service_description: {
            required: ($.validator.messages.ef_description + " " + $.validator.messages.ef_required),
            pattern: ($.validator.messages.ef_description + " " + $.validator.messages.ef_pattern),
          },
          service_category: $.validator.messages.ef_category + " " + $.validator.messages.ef_required,
          service_constraints: $.validator.messages.ef_constraints + " " + $.validator.messages.ef_pattern_fem,
          service_conditions: $.validator.messages.ef_conditions + " " + $.validator.messages.ef_pattern_fem,
        },
      });
    }
  }
});
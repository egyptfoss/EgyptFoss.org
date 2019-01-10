jQuery(document).ready(function ($) {
  if (ef_request.is_admin == 1)
  {
    $(".select2_admin_region").select2({width: '140px'});
  } else
  {
    if ($(".deadline-date-picker").exists()) {
      $('.deadline-date-picker').datetimepicker({
        
        format: 'YYYY-MM-DD',
        icons: {
          date: 'fa fa-calendar-o',
          up: 'fa fa-chevron-up',
          down: 'fa fa-chevron-down',
          previous: 'fa fa-chevron-left',
          next: 'fa fa-chevron-right',
          clear: 'fa fa-remove',
          close: 'fa fa-remove'
        }
      });
    }
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
   var forms_validate = ["add_request_center","edit_request_center"];
   for(var i = 0; i < forms_validate.length; i++)
   {
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
    for(var i = 0; i < forms_validate.length; i++)
    {
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
        request_center_title: {
          required: true,
          pattern: /[أ-يa-zA-Z]+/
        },
        request_center_type: {
          required: true,
        },
        target_bussiness_relationship: {
          required: true,
        },
        request_center_description: {
          required: true,
          pattern: /[أ-يa-zA-Z]+/
        },
        request_center_constraints: {
          pattern: /[أ-يa-zA-Z]+/
        },
        request_center_requirements: {
          pattern: /[أ-يa-zA-Z]+/
        },
      },
      messages: {
        request_center_title: {
          required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
          pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern),
        },
        request_center_description: {
          required: ($.validator.messages.ef_description + " " + $.validator.messages.ef_required),
          pattern: ($.validator.messages.ef_description + " " + $.validator.messages.ef_pattern),
        },
        target_bussiness_relationship: $.validator.messages.ef_target_bussiness_rel + " " + $.validator.messages.ef_required,
        request_center_type: $.validator.messages.ef_type + " " + $.validator.messages.ef_required,
        request_center_constraints: $.validator.messages.ef_constraints + " " + $.validator.messages.ef_pattern_fem,
        request_center_requirements: $.validator.messages.ef_requirements + " " + $.validator.messages.ef_pattern_fem,
      },
    });
    }
  }
});
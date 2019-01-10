jQuery(document).ready(function($) {
	var validator = $('#profile-edit-form').validate({
		ignore: 'input[type=hidden]',
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
			display_name: {
				required: true,
				contain_at_least_one_letter: true
			},
			sub_type: {
				required: true,
			},
			functionality: {
				pattern: /[أ-يa-zA-Z]+/
			},
			address: {
			pattern: /[أ-يa-zA-Z]+/
			},
			phone: {
				pattern: /^[0-9 \/+\(\)-]*[0-9][0-9 \/+\(\)-]*$/
			},
			facebook_url: {
				pattern: /^(https?):\/\/[^ ]+\.[^ ]+$/
			},
			twitter_url: {
				pattern: /^(https?):\/\/[^ ]+\.[^ ]+$/
			},
			linkedin_url: {
				pattern: /^(https?):\/\/[^ ]+\.[^ ]+$/
			},
			gplus_url: {
				pattern: /^(https?):\/\/[^ ]+\.[^ ]+$/
			},
			contact_name: {
				pattern: /[أ-يa-zA-Z]+/
			},
			contact_email: {
				email: true
			},
			contact_address: {
				pattern: /[أ-يa-zA-Z]+/
			},
			contact_phone: {
				required: true,
				pattern: /^[0-9 \/+\(\)-]*[0-9][0-9 \/+\(\)-]*$/
			},
		},
		messages: {
			display_name: {
				required: $.validator.messages.ef_required,
				contain_at_least_one_letter: $.validator.messages.ef_pattern,
			},
			sub_type: $.validator.messages.account_sub_type,
			functionality: $.validator.messages.profile_functionality,
			address: $.validator.messages.profile_address,
			phone: $.validator.messages.profile_phone,
			facebook_url: $.validator.messages.profile_facebook_url,
			twitter_url: $.validator.messages.profile_twitter_url,
			linkedin_url: $.validator.messages.profile_linkedin_url,
			gplus_url: $.validator.messages.profile_gplus_url,
			contact_name: $.validator.messages.profile_contact_name,
			contact_email: $.validator.messages.profile_contact_email,
			contact_address: $.validator.messages.profile_contact_address,
			contact_phone: $.validator.messages.profile_contact_phone,
		},
		// Fire bootstrap errors
		highlight: function(element) {
			if (element.type !== 'search') {
				$(element).closest('.form-group').addClass('has-error');
			}
		},
		unhighlight: function(element) {
			if (element.type !== 'search') {
				$(element).closest('.form-group').removeClass('has-error');
			}
		},
		errorElement: 'span',
		errorClass: 'error',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});
	function taxonomy_validations(selectElement) {
		var valid = true,
				taxonomy_valid = true,
				re = new RegExp("[أ-يa-zA-Z]+");
		taxonomy_values = selectElement.select2('data');
		for (t = 0; t < taxonomy_values.length; t++) {
			taxonomy = taxonomy_values[t];
			if (!re.test(taxonomy.text)) {
				selectElement.closest('.form-group').addClass('has-error');
				selectElement.siblings('.error').show();
				taxonomy_valid = valid = false;
			}
		}
		if(taxonomy_valid) {
			selectElement.closest('.form-group').removeClass('has-error');
			selectElement.siblings('.error').hide();
		}
		return valid;
	}
	$("select.L-validate_taxonomy").on("select2:close", function (e) {
		taxonomy_validations($(this));
	});
	$("#profile-edit-form").on('submit', function(e){
		taxonomies_fields = ['ict_technology', 'interest'];
    for (i = 0; i < taxonomies_fields.length; i++) {
      taxonomy_field = taxonomies_fields[i];
      select_element = $("#"+taxonomy_field);
			if( !taxonomy_validations(select_element) ) {
				e.preventDefault();
				return false;
			}
    }
	});
	$("#signup_form").on('submit', function(e){
		taxonomies_fields = ['ict_technology'];
    for (i = 0; i < taxonomies_fields.length; i++) {
      taxonomy_field = taxonomies_fields[i];
      select_element = $("#"+taxonomy_field);
			if( !taxonomy_validations(select_element) ) {
				e.preventDefault();
				return false;
			}
    }
	});
	var validator = $('#settings-form').validate({
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
			pwd: {
				required: true,
				minlength: 8
			},
			email: {
				required: true,
				email: true
			},
			pass1: {
				required: true,
				minlength: 8
			},
			pass2: {
				required: true
			},
		},
		messages: {
			pwd: $.validator.messages.settings_current_password,
			email: $.validator.messages.settings_email,
			pass1: {
				required: $.validator.messages.settings_new_pass_required,
				minlength: $.validator.messages.settings_new_pass_min_length,
			},
			pass2: $.validator.messages.settings_pass_confirm,
		},
		// Fire bootstrap errors
		highlight: function(element) {
			$(element).closest('.form-group').addClass('has-error');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		errorElement: 'span',
		errorClass: 'error',
		errorPlacement: function(error, element) {
			if(element.parent('.input-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});
	$("#change-pass").on('click', function (e) {
		$("input#email").val($("input#saved_email").val());
	});
});

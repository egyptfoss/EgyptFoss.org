jQuery(document).ready(function($) {
	function datetime_now() {
		var d = new Date,
		dformat = [d.getFullYear(), ("0" + (d.getMonth()+1)).slice(-2), ("0" + (d.getDate())).slice(-2)].join('-')+' '+
							[d.getHours(), d.getMinutes(), d.getSeconds()].join(':');
		return dformat;
	}
	$.validator.addMethod("no_arabic", function (value) {
		if (!value || 0 === value.length)
			return true;
		return !(/[أ-ي]+/i.test(value));
	}, $.validator.messages.no_arabic );
	$.validator.addMethod("contain_at_least_one_en_letter", function (value) {
		if (!value || 0 === value.length)
			return true;
		return /[a-zA-Z]+/i.test(value);
	}, $.validator.messages.contain_at_least_one_en_letter );
	$.validator.addMethod("contain_at_least_one_letter", function (value) {
		if (!value || 0 === value.length)
			return true;
		return /[أ-يa-zA-Z]+/i.test(value);
	}, $.validator.messages.contain_at_least_one_letter );
	$.validator.addMethod("phone_number", function (value) {
		if (!value || 0 === value.length)
			return true;
		return /^[0-9 \/+\(\)-]*[0-9][0-9 \/+\(\)-]*$/i.test(value);
	}, $.validator.messages.phone_number );
	$.validator.addMethod("url", function (value) {
		if (!value || 0 === value.length)
			return true;
		return /^(https?):\/\/[^ ]+\.[^ ]+$/i.test(value);
	}, $.validator.messages.url );
	$.validator.addMethod("required_if_new", function (value, element, param) {
		var otherElement = $(param);
		other_exist = (otherElement.val() != "");
		value_exist = (otherElement.val() == "") && (value != "");
		return (other_exist || value_exist);
	}, $.validator.messages.required_field);
	$.validator.addMethod("after_now", function (value) {
		now = datetime_now();
		return (value > now);
	}, $.validator.messages.after_now);
	$.validator.addMethod("after_date", function (value, element, param) {
		var start = $(param);
		start_datetime = start.val();
		return (value > start_datetime);
	}, $.validator.messages.after_date);
  $.validator.addMethod("noSpace", function(value, element) {
  return value.indexOf(" ") < 0 && value != "";
}, $.validator.messages.username_no_space);
});

jQuery(document).ready(function($) {
  $('#commentform').validate({
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
        comment: {
            required: true,
            pattern: /[أ-يa-zA-Z0-9._-]+/
        }
    },
    messages: {
        comment: {
            required: $.validator.messages.add_comment_required,
            pattern: $.validator.messages.add_comment_pattern,
        }
    }
  });
	$('#signup_form').validate({
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
			signup_username: {
				required: true,
				minlength: 4,
				maxlength: 20,
				no_arabic: true,
				contain_at_least_one_en_letter: true,
				pattern: /[أ-يa-zA-Z0-9._-]+/,
        noSpace: true
			},
			signup_email: {
				required: true,
				email: true,
        pattern: /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
			},
			signup_telephone_number: {
				required: true,
        pattern: /^(?=.*[0-9])[-+/0-9]+$/i
			},
			signup_password: {
				required: true,
				minlength: 8
			},
			signup_password_confirm: {
				required: true,
				minlength: 8,
				equalTo: "#signup_password"
			},
			type: {
				required: true
			},
      sub_type: {
        required: true
      },
			terms: "required"
		},
		messages: {
			signup_username: {
				required: $.validator.messages.signup_username_required,
				minlength: $.validator.messages.signup_username_minlength,
				maxlength: $.validator.messages.signup_username_maxlength,
				pattern: $.validator.messages.signup_pattern,
			},
			signup_email: $.validator.messages.signup_email,
			signup_telephone_number: $.validator.messages.signup_telephone_number,
			signup_password: $.validator.messages.signup_password,
			signup_password_confirm: $.validator.messages.signup_password_confirm,
			type: $.validator.messages.account_type,
      sub_type: $.validator.messages.account_sub_type,
			terms: $.validator.messages.terms,
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
			if(element.parent('.form-group').length) {
				error.insertAfter(element.parent());
			} else {
				error.insertAfter(element);
			}
		}
	});
	$('#add_product').validate({
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
			description:{
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
		  usage_hints:{
		    pattern: /[أ-يa-zA-Z]+/
		  },
		  references: {
		    pattern: /[أ-يa-zA-Z]+/
		  },
		  link_to_source: {
		  	pattern: /^(https?):\/\/[^ ]+\.[^ ]+$/
		  },

		},
    errorPlacement: function(error, element) {
        if (element.attr("name") == "product_logo" )
            error.insertAfter("#product_image_error");
        else
            error.insertAfter(element);
      },

		messages: {
		  product_title: {
		    required: $.validator.messages.addproduct_title_required,
		    minlength: $.validator.messages.general_minlength_2,
		    maxlength: $.validator.messages.signup_username_maxlength,
		    pattern: $.validator.messages.addproduct_title_pattern
		  },
		  description:{
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
	var classTypeSelected = $('input[name=type]:checked').val();
	if ($(".registration_sub_type").exists()) {
		var allSubTypes = $('.registration_sub_type option');
		var currentAccountSubTypeSelected = $(".registration_sub_type")[0].selectedIndex;
		$('.registration_sub_type option:not(".'+ classTypeSelected +'")').remove();
	}
	$('input:radio[name=type]').change(function () {
		$('.registration_sub_type option').remove(); //remove all options
		var classN = $('input[name=type]:checked').val();
		var opts = allSubTypes.filter('.' + classN);
		var opts_Individual = allSubTypes.filter('.Individual').length - 1;
		var classTypeSelected = $('input[name=type]:checked').val();
		if(classTypeSelected === "Individual") {
			$('#telephone_number_container').hide();
			$('#telephone_number_container input').val('');
			opts_Individual = 0;
		}
		else if (classTypeSelected === "Entity") {
			$('#telephone_number_container').show();
		}
		$.each(opts, function (i, j) {
			$(j).appendTo('.registration_sub_type'); //append those options back
		});
    if(classTypeSelected == classN)
        $('.registration_sub_type option').eq(currentAccountSubTypeSelected - opts_Individual).prop('selected', true);
    else
        $('.registration_sub_type option').eq(0).prop('selected', true);
	});
});

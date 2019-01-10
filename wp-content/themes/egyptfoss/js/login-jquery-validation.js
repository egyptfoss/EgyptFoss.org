jQuery(document).ready(function($) {
	$('#loginform').validate({
    onfocusout: function (element) {
    	$(element).valid();
    },
		rules: {
			log: {
				required: true,
				minlength: 4,
				maxlength: 80,
			},
			pwd: {
				required: true
			}
		},
		messages: {
			log: {
				required: $.validator.messages.login_username_required,
				minlength: $.validator.messages.login_username_minlength,
				maxlength: $.validator.messages.login_username_maxlength
			},
			pwd: $.validator.messages.login_password
		}
	});
});

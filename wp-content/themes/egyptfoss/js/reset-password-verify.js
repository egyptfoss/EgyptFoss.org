( function( $ ){
	function check_pass_strength() {
		var pass1 = $( '.egyptfoss-pass1' ).val(),
		    pass2 = $( '.egyptfoss-pass2' ).val(),
		    strength;

		// Reset classes and result text
		$( '#pass-strength-result' ).removeClass( 'short bad good strong' );
		if ( ! pass1 ) {
			$( '#pass-strength-result' ).html( "" );
			return;
		}

		strength = wp.passwordStrength.meter( pass1, wp.passwordStrength.userInputBlacklist(), pass2 );

		switch ( strength ) {
			case 2:
				$( '#pass-strength-result' ).addClass( 'bad' ).html( pwsL10n.bad );
				break;
			case 3:
				$( '#pass-strength-result' ).addClass( 'good' ).html( pwsL10n.good );
				break;
			case 4:
				$( '#pass-strength-result' ).addClass( 'strong' ).html( pwsL10n.strong );
				break;
			case 5:
				$( '#pass-strength-result' ).addClass( 'short' ).html( pwsL10n.mismatch );
				break;
			default:
				$( '#pass-strength-result' ).addClass( 'short' ).html( pwsL10n['short'] );
				break;
		}
	}

	// Bind check_pass_strength to keyup events in the password fields
	$( document ).ready( function() {
		$( '.egyptfoss-pass1' ).val( '' ).keyup( check_pass_strength );
		$( '.egyptfoss-pass2' ).val( '' ).keyup( check_pass_strength );
		$('#resetpassform').validate({
			onfocusout: function (element) {
				$(element).valid();
			},
		rules: {
			"egyptfoss-pass1": {
				required: true,
				minlength: 8
			},
			"egyptfoss-pass2": {
				required: true,
	                        minlength: 8,
	                        equalTo: "#egyptfoss-pass1"
			},
		},
		messages: {
			"egyptfoss-pass1": $.validator.messages.signup_password,
                        "egyptfoss-pass2": $.validator.messages.signup_password_confirm,
		}
            });
	});

} )( jQuery );

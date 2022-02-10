jQuery(document).ready(function(){ 
	// Focus
	if ( jQuery( '#password_current' ).length ) {
		jQuery( '#password_current' ).focus();
	} else if ( jQuery( '#pass1' ).length ) {
		jQuery( '#pass1' ).focus();
	}
	
	function pmpro_check_password_strength( pass_field ) {
		var pass1 = jQuery( pass_field ).val();		
		var indicator = jQuery( '#pass-strength-result' );		
		
		var strength;		
		if ( pass1 != '' ) {
			// Call the disallowed list method corresponding to appropriate WP version.
			const disallowedList = ( 'function' == typeof wp.passwordStrength.userInputDisallowedList )
				? wp.passwordStrength.userInputDisallowedList()
				: wp.passwordStrength.userInputBlacklist();

			strength = wp.passwordStrength.meter( pass1, disallowedList, pass1 );

		} else {
			strength = -1;
		}

		var submitbutton;
		if ( jQuery( '#resetpass-button' ).length ) {
			submitbutton = jQuery( '#resetpass-button' );
		} else {
			submitbutton = jQuery( '#change-password input.pmpro_btn-submit' );
		}

		indicator.removeClass( 'empty bad good strong short' );

		switch ( strength ) {
			case -1:
				indicator.addClass( 'empty' ).html( '&nbsp;' );
				if ( pmpro.allow_weak_passwords === '' ) {
					submitbutton.prop( 'disabled', true );
				}
				break;
			case 2:
				indicator.addClass( 'bad' ).html( pwsL10n.bad );
				if ( pmpro.allow_weak_passwords === '' ) {
					submitbutton.prop( 'disabled', true );
				}
				break;
			case 3:
				indicator.addClass( 'good' ).html( pwsL10n.good );
				submitbutton.prop( 'disabled', false );
				break;
			case 4:
				indicator.addClass( 'strong' ).html( pwsL10n.strong );
				submitbutton.prop( 'disabled', false );
				break;
			case 5:
				indicator.addClass( 'short' ).html( pwsL10n.mismatch );
				submitbutton.prop( 'disabled', false );
				break;
			default:
				indicator.addClass( 'short' ).html( pwsL10n['short'] );
				if ( pmpro.allow_weak_passwords === '' ) {
					submitbutton.prop( 'disabled', true );
				}
		}
	}
	
	// Set up Strong Password script.
	if ( jQuery( '#pass1' ) ) {
		pmpro_check_password_strength( jQuery( '#pass1' ) );
		jQuery( '#pass1' ).bind( 'keyup paste', function() {
			pmpro_check_password_strength( jQuery( '#pass1' ) );
		});
	}
});

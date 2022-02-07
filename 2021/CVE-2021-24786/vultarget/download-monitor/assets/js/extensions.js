jQuery( function ( $ ) {

	$.each( $( '.extension_license a' ), function ( k, v ) {
		$( v ).click( function () {
			var wrap = $( v ).closest( '.extension_license' );

			var ex_ac = (
				'inactive' == $( wrap ).find( '#status' ).val()
			) ? 'activate' : 'deactivate';

			$(wrap).find('.dlm_license_error').remove();

			$.post( ajaxurl, {
				action: 'dlm_extension',
				nonce: $( '#dlm-ajax-nonce' ).val(),
				product_id: $( wrap ).find( '#product_id' ).val(),
				key: $( wrap ).find( '#key' ).val(),
				email: $( wrap ).find( '#email' ).val(),
				extension_action: ex_ac
			}, function ( response ) {
				if ( response.result == 'failed' ) {
					$( wrap ).prepend( $( "<div>" ).addClass( "dlm_license_error" ).html( response.message ) );
				} else {
					if ( 'activate' == ex_ac ) {
						$( wrap ).find( '.license-status' ).addClass( 'active' ).html( 'ACTIVE' );
						$( wrap ).find( '.button' ).html( 'Deactivate' );
						$( wrap ).find( '#status' ).val( 'active' );
						$( wrap ).find( '#key' ).attr( 'disabled', true );
						$( wrap ).find( '#email' ).attr( 'disabled', true );
					} else {
						$( wrap ).find( '.license-status' ).removeClass( 'active' ).html( 'INACTIVE' );
						$( wrap ).find( '.button' ).html( 'Activate' );
						$( wrap ).find( '#status' ).val( 'inactive' );
						$( wrap ).find( '#key' ).attr( 'disabled', false );
						$( wrap ).find( '#email' ).attr( 'disabled', false );
					}
				}
			} );

		} );
	} );
} );
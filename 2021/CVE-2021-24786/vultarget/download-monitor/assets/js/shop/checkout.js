jQuery( function ( $ ) {

	$( '#dlm-form-checkout' ).submit( function ( e ) {

		var form = $( this );

		dlmShopResetErrorFields( form );
		dlmShopRemoveErrors( form );

		dlmShopShowLoading( form );

		var customer = {
			first_name: form.find( '#dlm_first_name' ).val(),
			last_name: form.find( '#dlm_last_name' ).val(),
			company: form.find( '#dlm_company' ).val(),
			email: form.find( '#dlm_email' ).val(),
			address_1: form.find( '#dlm_address_1' ).val(),
			postcode: form.find( '#dlm_postcode' ).val(),
			city: form.find( '#dlm_city' ).val(),
			country: form.find( '#dlm_country' ).val(),
		};

		var data = {
			payment_gateway: $( 'input[name=dlm_gateway]:checked', $( this ) ).val(),
			customer: customer
		};

		if ( typeof form.data( 'order_id' ) !== "undefined" ) {
			data.order_id = form.data( 'order_id' );
		}

		if ( typeof form.data( 'order_hash' ) !== "undefined" ) {
			data.order_hash = form.data( 'order_hash' );
		}

		// check if required data is set
		var errorFields = [];
		var success = true;
		for ( var i = 0; i < dlm_strings.required_fields.length; i ++ ) {

			if ( customer[dlm_strings.required_fields[i]] === "" ) {
				success = false;
				errorFields.push( dlm_strings.required_fields[i] );
			}
		}

		if ( success === false ) {
			dlmShopMarkErrorFields( form, errorFields );

			dlmShopDisplayError( form, dlm_strings.error_message_required_fields );

			dlmShopHideLoading( form );
			return false;
		}

		$.post( dlm_strings.ajax_url_place_order, data, function ( response ) {
			if ( response.success === true && typeof response.redirect !== 'undefined' ) {
				window.location.replace( response.redirect );
				return false;
			} else if ( response.success === false && response.error !== '' ) {
				dlmShopDisplayError( form, response.error );
			}
			dlmShopHideLoading( form );
		} );

		return false;
	} );

	function dlmShopMarkErrorFields( form, fields ) {
		for ( var i = 0; i < fields.length; i ++ ) {
			$( form ).find( '#dlm_' + fields[i] ).addClass( 'dlm-checkout-field-error' );
		}
	}

	function dlmShopResetErrorFields( form ) {
		$( form ).find( '.dlm-checkout-field-error' ).removeClass( 'dlm-checkout-field-error' );
	}

	function dlmShopDisplayError( form, errorMessage ) {
		var errorContainer = $( '<div>' ).addClass( "dlm-checkout-error" );
		errorContainer.append( $( '<img>' ).attr( 'src', dlm_strings.icon_error ).attr( 'alt', 'Checkout error' ).addClass( 'dlm-checkout-error-icon' ) );
		errorContainer.append( $( '<p>' ).html( errorMessage ) );
		form.prepend( errorContainer );
	}

	function dlmShopRemoveErrors( form ) {
		form.find( '.dlm-checkout-error' ).remove();
	}

	function dlmShopShowLoading( form ) {
		$( form ).find( '#dlm_checkout_submit' ).attr( 'disabled', true );

		var overlayBg = $( '<div>' ).addClass( 'dlm-checkout-overlay-bg' );

		var overlay = $( '<div>' ).addClass( 'dlm-checkout-overlay' );
		overlay.append( $( '<h2>' ).html( dlm_strings.overlay_title ) );
		overlay.append( $( '<span>' ).html( dlm_strings.overlay_body ) );
		overlay.append( $( '<img>' ).attr( 'src', dlm_strings.overlay_img_src ) );

		$( 'body' ).append( overlayBg );
		$( 'body' ).append( overlay );

		overlayBg.fadeIn( 300, function () {
			overlay.css( 'display', 'block' ).css( 'top', '47%' );
			overlay.animate( {
				"top": "+=3%"
			}, 300 );
		} );
	}

	function dlmShopHideLoading( form ) {

		var overlay = $( '.dlm-checkout-overlay:first' );
		var overlayBg = $( '.dlm-checkout-overlay-bg:first' );

		overlay.fadeOut( 300, function () {
			overlay.remove();
		} );

		overlayBg.fadeOut( 300, function () {
			overlayBg.remove();
			$( form ).find( '#dlm_checkout_submit' ).attr( 'disabled', false );
		} );
	}
} );



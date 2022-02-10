var tribe_tickets_tpp_admin = {
	l10n: window.tribe_tickets_tpp_admin_strings || false
};

(function( $, my, strings ) {
	'use strict';

	my.checkmarkValidationMap = function() {
		return {
			'email': function( email ) {
				var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
				return re.test( email );
			},
			'radio': function( val ) {
				return ['yes', '1', 1, true, 'true', 'on', 'complete', 'completed'].includes( val.toLowerCase() );
			},
		}
	};

	my.castStatusToBool = function( status ) {
		var radio = my.checkmarkValidationMap()['radio'];
		return radio( status );
	};

	my.castBoolToStatus = function( val ) {
		return val === true ? 'complete' : 'incomplete';
	};


	my.updatePayPalIpnStatus = function( ) {
		var $find = $( '#paypal-ipn-config-status' );
		var $dependsOn = $( '.ipn-required' );

		if ( ! $dependsOn ) {
			return;
		}

		var newStatus = _.reduce( $dependsOn, function( currentStatusBool, el ) {
			return currentStatusBool && ! $( el ).hasClass( 'no-checkmark' );
		}, true );
		var newStatusSlug = my.castBoolToStatus( newStatus );
		$find.text( my.l10n[ newStatusSlug ] ).attr( 'data-status', newStatusSlug );
	};

	my.isOkInput = function( input ) {
		var $parent = $( input ).closest( '.checkmark' );

		if ( ! $parent ) {
			return;
		}

		var ok = false;
		var map = my.checkmarkValidationMap();

		if ( $parent.hasClass( 'tribe-field-email' ) ) {
			ok = map['email']( input.value );
		} else if ( $parent.hasClass( 'tribe-field-radio' ) ) {
			var value = $( input ).closest( '.tribe-field-wrap' ).find( 'input:checked' ).val();
			ok = map['radio']( value );
		} else {
			ok = true
		}

		return ok;
	};

	my.toggleCheckmark = function() {
		var ok = my.isOkInput( this );
		var $parent = $(this).closest('.checkmark');

		if ( ok ) {
			$parent.removeClass( 'no-checkmark' )
		} else {
			$parent.addClass( 'no-checkmark' );
		}

		my.updatePayPalIpnStatus( );
	};

	my.setupValidationOnPanel = function( event, data ) {
		if ( ! ( data.panel && data.panel instanceof jQuery ) ) {
			return;
		}

		var $panel = data.panel;

		var paypalIsDefaultProvider = $panel.data( 'default-provider' ) === 'Tribe__Tickets__Commerce__PayPal__Main';
		var isNew = ! $( '#ticket_id' ).val();

		if ( paypalIsDefaultProvider && isNew ) {
			$( '#ticket_price, #ticket_sale_price' )
				.prop( 'data-required', true )
				.attr( 'data-validation-is-greater-than', '0' )
		}

		$panel.find( '.tribe-validation' ).validation();
	};

	my.init = function() {
		$( '.checkmark input' ).each( function() {
			$( this ).on( 'change', my.toggleCheckmark )
			         .each( my.toggleCheckmark );
		} );

		$( '#event_tickets' ).on( 'after_panel_swap.tickets', my.setupValidationOnPanel );
	};

	$( function() {
		if ( ! my.l10n ) {
			return;
		}
		my.init();
	} );
})( jQuery, tribe_tickets_tpp_admin , tribe_tickets_tpp_admin_strings );
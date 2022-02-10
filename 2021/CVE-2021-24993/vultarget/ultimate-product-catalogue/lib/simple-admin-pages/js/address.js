/**
 * Javascript functions for Address component
 *
 * @package Simple Admin Pages
 */

jQuery(document).ready(function ($) {

	/**
	 * Set coordinate that have been received
	 */
	function sap_address_set_coords( control, lat, lon ) {
		control.find( '.sap-coords-result' ).remove();
		control.find( 'input.lat' ).val( lat );
		control.find( 'input.lon' ).val( lon );

		if ( lat == '' && lon == '' ) {
			control.find( '.sap-map-coords' ).text( lat + sap_address.strings['no-setting'] + lon ).attr( 'style', '' );
		} else {
			control.find( '.sap-map-coords' ).text( lat + sap_address.strings['sep-lat-lon'] + lon ).attr( 'style', '' );
		}

		var url = 'https://maps.google.com/maps?q=' + lat + ',' + lon;
		if ( control.find( '.sap-view-coords' ).length ) {
			control.find( '.sap-view-coords' ).attr( 'href', url );
		} else {
			control.find( '.sap-map-coords-wrapper' ).append( '<a class="sap-view-coords" href="' + url + '" target="_blank">' + sap_address.strings.view + '</a>' );
		}
	}

	/**
	 * Retrieve coordinates
	 */
	$('.sap-get-coords').click( function(e) {

		e.stopPropagation();
		e.preventDefault();

		var control = $(this).parent().parent();
		var address = control.find( 'textarea' ).val();
		var params = {
			sensor: false,
			address: address,
		};
		if ( sap_address.api_key_selector ) {
			var $input = $( sap_address.api_key_selector );
			if ( $input.length && $input.val() ) {
				params.key = $input.val();
			}
		} else if ( sap_address.api_key ) {
			params.key = sap_address.api_key;
		}

		// Reset messages
		control.find( '.sap-coords-result' ).remove();
		control.find( '.error' ).remove();
		control.find( '.sap-view-coords' ).remove();
		control.find( '.sap-map-coords' ).text( sap_address.strings.retrieving ).attr( 'style', 'opacity: 0.3' );

		// Call Google Maps geocoding API
		// See: https://developers.google.com/maps/documentation/geocoding/
		var req = $.get(
			'https://maps.googleapis.com/maps/api/geocode/json',
			params,
			function( data ) {

				if ( data.status == 'OK' ) {
					if ( data.results.length == 1 ) {
						sap_address_set_coords( control, data.results[0].geometry.location.lat, data.results[0].geometry.location.lng );

					} else {
						for ( var key in data.results ) {
							control.append( '<p class="sap-coords-result">' + data.results[key].formatted_address + ' <span class="dashicons dashicons-arrow-right"></span> <a href="#" data-lat="' + data.results[key].geometry.location.lat + '" data-lon="' + data.results[key].geometry.location.lng + '">Set</a></p>' );
						}
						control.find( '.sap-map-coords' ).text( sap_address.strings.select ).attr( 'style', '' );

						control.find( '.sap-coords-result a' ).click( function() {
							sap_address_set_coords( control, $(this).data( 'lat' ), $(this).data( 'lon' ) );
						});

					}

				} else {
					sap_address_set_coords( control, control.find( 'input.lat' ).val(), control.find( 'input.lon' ).val() );

					if ( data.status == 'UNKNOWN_ERROR' ) {
						control.find( '.sap-coords-action-wrapper' ).prepend( '<div class="error">' + sap_address.strings.result_error + '</div>' );
					} else if ( data.status == 'INVALID_REQUEST' ) {
						control.find( '.sap-coords-action-wrapper' ).prepend( '<div class="error">' + sap_address.strings.result_invalid + '</div>' );
					} else if ( data.status == 'INVALID_REQUEST' ) {
						control.find( '.sap-coords-action-wrapper' ).prepend( '<div class="error">' + sap_address.strings.result_error + '</div>' );
					} else if ( data.status == 'REQUEST_DENIED' ) {
						control.find( '.sap-coords-action-wrapper' ).prepend( '<div class="error">' + sap_address.strings.result_denied + '</div>' );
					} else if ( data.status == 'OVER_QUERY_LIMIT' ) {
						control.find( '.sap-coords-action-wrapper' ).prepend( '<div class="error">' + sap_address.strings.result_limit + '</div>' );
					} else if ( data.status == 'ZERO_RESULTS' ) {
						control.find( '.sap-coords-action-wrapper' ).prepend( '<div class="error">' + sap_address.strings.result_empty + '</div>' );
					}
				}
			}
		)
	});

	/**
	 * Remove coordinates from settings
	 */
	$('.sap-remove-coords').click( function(e) {

		e.stopPropagation();
		e.preventDefault();

		var control = $(this).parent().parent();
		sap_address_set_coords( control, '', '' );
	});

});

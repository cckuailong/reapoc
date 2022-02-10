/* global google */
/**
 * Front-end JavaScript for Business Profile maps
 *
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     0.0.1
 */

var bpfwp_map = bpfwp_map || {};

/**
 * Set up a map using the Google Maps API and data attributes added to `.bp-map`
 * elements on a given page.
 *
 * @uses  Google Maps API (https://developers.google.com/maps/web/)
 * @since 1.1.0
 */
function bpInitializeMap() {
	'use strict';

	bpfwp_map.maps = [];
	bpfwp_map.info_windows = [];

	jQuery( '.bp-map' ).each( function() {
		var id = jQuery( this ).attr( 'id' );
		var data = jQuery( this ).data();

		data.addressURI = encodeURIComponent( data.address.replace( /(<([^>]+)>)/ig, ', ' ) );

		// Google Maps API v3
		if ( 'undefined' !== typeof data.lat ) {
			data.addressURI              = encodeURIComponent( data.address.replace( /(<([^>]+)>)/ig, ', ' ) );
			bpfwp_map.map_options        = bpfwp_map.map_options || {};
			bpfwp_map.map_options.center = new google.maps.LatLng( data.lat, data.lon );
			if ( typeof bpfwp_map.map_options.zoom === 'undefined' ) {
				bpfwp_map.map_options.zoom = bpfwp_map.map_options.zoom || 15;
			}
			bpfwp_map.maps[ id ] = new google.maps.Map( document.getElementById( id ), bpfwp_map.map_options );

			var content = '<div class="bp-map-info-window">' + '<p><strong>' + data.name + '</strong></p>' + '<p>' + data.address.replace( /(?:\r\n|\r|\n)/g, '<br>' ) + '</p>';

			if ( 'undefined' !== typeof data.phone ) {
				content += '<p>' + data.phone + '</p>';
			}

			content += '<p><a target="_blank" href="//maps.google.com/maps?saddr=current+location&daddr=' + data.addressURI + '">' + bpfwp_map.strings.getDirections + '</a></p>' + '</div>';

			bpfwp_map.info_windows[ id ] = new google.maps.InfoWindow({
				position: bpfwp_map.map_options.center,
				content: content
			});
			bpfwp_map.info_windows[ id ].open( bpfwp_map.maps[ id ] );

			// Trigger an intiailized event on this dom element for third-party code
			jQuery( this ).trigger( 'bpfwp.map_initialized', [ id, bpfwp_map.maps[id], bpfwp_map.info_windows[id] ] );

		// Google Maps iframe embed (fallback if no lat/lon data available)
		} else if ( '' !== data.address ) {
			var bpMapIframe = document.createElement( 'iframe' );

			bpMapIframe.frameBorder = 0;
			bpMapIframe.style.width = '100%';
			bpMapIframe.style.height = '100%';

			if ( '' !== data.name ) {
				data.address = data.name + ',' + data.address;
			}

			bpMapIframe.src = '//maps.google.com/maps?output=embed&q=' + encodeURIComponent( data.address );
			bpMapIframe.src = '//maps.google.com/maps?output=embed&q=' + data.addressURI;

			jQuery( this ).html( bpMapIframe );

			// Trigger an intiailized event on this dom element for third-party code
			jQuery( this ).trigger( 'bpfwp.map_initialized_in_iframe', [ jQuery( this ) ] );
		}
	});
}

/**
 * Backwards-compatable alias function.
 *
 * @since 1.1.0
 */
function bp_initialize_map() {
	bpInitializeMap();
}

jQuery( document ).ready( function() {
	'use strict';

	// Allow developers to override the maps api loading and initializing.
	if ( ! bpfwp_map.autoload_google_maps ) {
		return;
	}
	// Load Google Maps API and initialize maps.
	if ( 'undefined' === typeof google || 'undefined' === typeof google.maps ) {
		var bpMapScript = document.createElement( 'script' );
		bpMapScript.type = 'text/javascript';
		bpMapScript.src = '//maps.googleapis.com/maps/api/js?v=3.exp&callback=bp_initialize_map';

		if ( 'undefined' !== typeof bpfwp_map.google_maps_api_key ) {
			bpMapScript.src += '&key=' + bpfwp_map.google_maps_api_key;
		}

		document.body.appendChild( bpMapScript );
	} else {
		// If the API is already loaded (eg - by a third-party theme or plugin),
		// just initialize the map.
		bp_initialize_map();
	}
});

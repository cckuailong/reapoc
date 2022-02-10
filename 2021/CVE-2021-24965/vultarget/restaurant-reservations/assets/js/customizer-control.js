/* global wp, jQuery */

/**
 * Initialize the customizer controls
 *
 * @since 0.1
 */
(function( api, $ ) {

	api.etfrtb = api.etfrtb || {};

	/**
	 * Update a URI query string parameter
	 *
	 * @see http://stackoverflow.com/a/6021027
	 * @param string uri The original URI
	 * @param string key The parameter key to add/update
	 * @param string value The value to insert
	 * @since 0.1
	 */
	api.etfrtb.updateQueryStringParam = function( uri, key, value ) {
		var re = new RegExp( "([?&])" + key + "=.*?(&|$)", "i" ),
			separator = uri.indexOf( '?' ) !== -1 ? "&" : "?";

		key = encodeURIComponent( key );
		value = encodeURIComponent( value );

		if ( uri.match( re ) ) {
			return uri.replace( re, '$1' + key + "=" + value + '$2' );
		} else {
			return uri + separator + key + "=" + value;
		}
	};

	api.bind( 'ready', function() {
		api.section.each( function( section ) {
			section.expanded.bind( function( expanded ) {
				if ( expanded && section.id !== 'etfrtb_style' ) {
					api.etfrtb.load_email( section.id );
				}
			} );
		} );
	} );

	api.etfrtb.load_email = function( email ) {
		var email_type = email.replace( 'etfrtb-content-', '' ),
			url = api.etfrtb.updateQueryStringParam( api.previewer.previewUrl.get(), 'etfrtb_designer_email', email_type );

		api.previewer.previewUrl.set( url );
	};

}( wp.customize, jQuery ) );

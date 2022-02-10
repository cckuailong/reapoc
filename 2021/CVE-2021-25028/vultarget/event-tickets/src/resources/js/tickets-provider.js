/* global tribe, jQuery, tecTicketsSettings, console */
/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 5.1.9
 *
 * @type   {Object}
 */
tribe.tickets = tribe.tickets || {};

/**
 * Initializes in a Strict env the code that manages the plugin tickets commerce.
 *
 * @since 5.1.9
 *
 * @param  {Object} $   jQuery
 * @param  {Object} obj tribe.tickets.commerce
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';

	/**
	 * Ticket Commerce debug settings.
	 *
	 * @since 5.2.0
	 */
	obj.settings = tecTicketsSettings;

	obj.debug = {
		log: ( ...args ) => {
			if ( ! obj.settings.debug ) {
				return;
			}

			console.log( args );
		},
		error: ( ...args ) => {
			if ( ! obj.settings.debug ) {
				return;
			}

			console.error( args );
		},
		info: ( ...args ) => {
			if ( ! obj.settings.debug ) {
				return;
			}

			console.info( args );
		},
		trace: ( ...args ) => {
			if ( ! obj.settings.debug ) {
				return;
			}

			console.trace( args );
		},
	};

} )( jQuery, tribe.tickets );

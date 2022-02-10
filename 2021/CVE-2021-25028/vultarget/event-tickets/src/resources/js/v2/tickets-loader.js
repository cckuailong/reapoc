/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 5.0.3
 *
 * @type   {PlainObject}
 */
tribe.tickets = tribe.tickets || {};

/**
 * Configures ET Loader Object in the Global Tribe variable
 *
 * @since 5.0.3
 *
 * @type   {PlainObject}
 */
tribe.tickets.loader = {};

/**
 * Initializes in a Strict env the code that manages the plugin "loader".
 *
 * @since 5.0.3
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.tickets.loader
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';

	/**
	 * Selectors used for configuration and setup.
	 *
	 * @since 5.0.3
	 *
	 * @type {PlainObject}
	 */
	obj.selectors = {
		loader: '.tribe-common-c-loader',
		hiddenElement: '.tribe-common-a11y-hidden',
	};

	/**
	 * Show loader for the container.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 *
	 * @return {void}
	 */
	obj.show = function( $container ) {
		const $loader = $container.find( obj.selectors.loader );

		if ( $loader.length ) {
			$loader.removeClass( obj.selectors.hiddenElement.className() );
		}
	};

	/**
	 * Hide loader for the container.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of the container.
	 *
	 * @return {void}
	 */
	obj.hide = function( $container ) {
		const $loader = $container.find( obj.selectors.loader );

		if ( $loader.length ) {
			$loader.addClass( obj.selectors.hiddenElement.className() );
		}
	};
} )( jQuery, tribe.tickets.loader );

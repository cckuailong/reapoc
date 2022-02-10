/* global tribe, jQuery, tecTicketsCommerceGatewayPayPalSignup, ajaxurl */
/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 5.2.0
 *
 * @type   {Object}
 */
tribe.tickets = tribe.tickets || {};

/**
 * Path to this script in the global tribe Object.
 *
 * @since 5.2.0
 *
 * @type   {Object}
 */
tribe.tickets.commerce = tribe.tickets.commerce || {};

/**
 * Path to this script in the global tribe Object.
 *
 * @since 5.2.0
 *
 * @type   {Object}
 */
tribe.tickets.commerce.gateway = tribe.tickets.commerce.gateway || {};

/**
 * Path to this script in the global tribe Object.
 *
 * @since 5.2.0
 *
 * @type   {Object}
 */
tribe.tickets.commerce.gateway.paypal = tribe.tickets.commerce.gateway.paypal || {};

/**
 * This script Object for public usage of the methods.
 *
 * @since 5.2.0
 *
 * @type   {Object}
 */
tribe.tickets.commerce.gateway.paypal.signup = {};

/**
 * Initializes in a Strict env the code that manages the checkout for PayPal.
 *
 * @since 5.2.0
 *
 * @param  {Object} $   jQuery
 * @param  {Object} obj tribe.tickets.commerce.gateway.paypal.checkout
 *
 * @return {void}
 */
( function ( $, obj ) {
	'use strict';
	const $document = $( document );

	/**
	 * PayPal Signup nonce.
	 *
	 * @since 5.2.0
	 *
	 * @type {string}
	 */
	obj.onboardNonce = tecTicketsCommerceGatewayPayPalSignup.onboardNonce;

	/**
	 * PayPal Refresh Connect URL nonce.
	 *
	 * @since 5.2.0
	 *
	 * @type {string}
	 */
	obj.refreshConnectNonce = tecTicketsCommerceGatewayPayPalSignup.refreshConnectNonce;

	/**
	 * PayPal Signup handling endpoint.
	 *
	 * @since 5.2.0
	 *
	 * @type {string}
	 */
	obj.onboardingEndpointUrl = tecTicketsCommerceGatewayPayPalSignup.onboardingEndpointUrl;

	/**
	 * PayPal Signup Selectors.
	 *
	 * @since 5.2.0
	 *
	 * @type {Object}
	 */
	obj.selectors = {
		button: '.tec-tickets__admin-settings-tickets-commerce-paypal-connect-button-link',
		countryField: '[name="tec-tickets-commerce-gateway-paypal-merchant-country"]',
	};

	/**
	 * Handles the singup onboarding of customers to PayPal.
	 *
	 * @since 5.2.0
	 *
	 * @param {string} authCode PayPal data passed to this method.
	 * @param {string} sharedId jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.onboardedCallback = ( authCode, sharedId ) => {
		fetch( obj.onboardingEndpointUrl, {
			method: 'POST',
			headers: {
				'content-type': 'application/json',
			},
			body: JSON.stringify( {
				auth_code: authCode,
				shared_id: sharedId,
				nonce: obj.onboardNonce,
			} ),
		} );
	};

	/**
	 * When the country field changes we need to refresh the link.
	 *
	 * @since 5.2.0
	 *
	 * @param event {Event}
	 *
	 * @return {void}
	 */
	obj.onCountryChange = function ( event ) {
		const $field = $( this );
		const $button = $( obj.selectors.button );
		$button.addClass( 'disabled' );

		fetch(
			ajaxurl + '?action=tec_tickets_commerce_gateway_paypal_refresh_connect_url&nonce=' + obj.refreshConnectNonce + '&country_code=' + $field.val(),
			{
				method: 'GET',
				headers: {
					'content-type': 'application/json',
				}
			}
		) // eslint-disable-line max-len
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( res ) {
				// Handle success.
				if ( true === res.success ) {
					$button.prop( 'href', res.data.new_url );
				}

				$button.removeClass( 'disabled' );
			} );
	};

	/**
	 * Setup the triggers for Ticket Commerce loader view.
	 *
	 * @since 5.2.0
	 *
	 * @return {void}
	 */
	obj.setup = () => {
		// Hide loader when Paypal buttons are added.
		$( obj.selectors.countryField ).on( 'change', obj.onCountryChange );
	};

	/**
	 * Handles the initialization of the tickets commerce events when Document is ready.
	 *
	 * @since 5.2.0
	 *
	 * @return {void}
	 */
	obj.ready = () => {
		obj.setup();
	};

	$( obj.ready );

} )( jQuery, tribe.tickets.commerce.gateway.paypal.signup );

/**
 * Do not remove this, since PayPal codebase doesn't support a direct reference to how our objects are structured.
 *
 * @type {tribe.tickets.commerce.gateway.paypal.signup.onboardedCallback}
 *
 * @since 5.2.0
 */
tecTicketsCommerceGatewayPayPalSignupCallback = tribe.tickets.commerce.gateway.paypal.signup.onboardedCallback;

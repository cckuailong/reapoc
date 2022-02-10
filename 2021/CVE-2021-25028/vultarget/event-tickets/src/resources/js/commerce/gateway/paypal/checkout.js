/* global tribe, jQuery, paypal, ajaxurl */
/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 5.1.9
 *
 * @type   {Object}
 */
tribe.tickets = tribe.tickets || {};

/**
 * Path to this script in the global tribe Object.
 *
 * @since 5.1.9
 *
 * @type   {Object}
 */
tribe.tickets.commerce = tribe.tickets.commerce || {};

/**
 * Path to this script in the global tribe Object.
 *
 * @since 5.1.9
 *
 * @type   {Object}
 */
tribe.tickets.commerce.gateway = tribe.tickets.commerce.gateway || {};

/**
 * Path to this script in the global tribe Object.
 *
 * @since 5.1.9
 *
 * @type   {Object}
 */
tribe.tickets.commerce.gateway.paypal = tribe.tickets.commerce.gateway.paypal || {};

/**
 * This script Object for public usage of the methods.
 *
 * @since 5.1.9
 *
 * @type   {Object}
 */
tribe.tickets.commerce.gateway.paypal.checkout = {};

/**
 * Initializes in a Strict env the code that manages the checkout for PayPal.
 *
 * @since 5.1.9
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
	 * PayPal Order handling endpoint.
	 *
	 * @since 5.1.9
	 *
	 * @type {string}
	 */
	obj.orderEndpointUrl = tecTicketsCommerceGatewayPayPalCheckout.orderEndpoint;

	/**
	 * PayPal advanced Payment settings.
	 *
	 * @since 5.2.0
	 *
	 * @type {string}
	 */
	obj.advancedPayments = tecTicketsCommerceGatewayPayPalCheckout.advancedPayments;

	/**
	 * Set of timeout IDs so we can clear when the process of purchasing starts.
	 *
	 * @since 5.1.9
	 *
	 * @type {Array}
	 */
	obj.timeouts = [];

	/**
	 * PayPal Checkout Selectors.
	 *
	 * @since 5.1.9
	 *
	 * @type {Object}
	 */
	obj.selectors = {
		checkoutScript: '.tec-tc-gateway-paypal-checkout-script',
		activePayment: '.tec-tc-gateway-paypal-payment-active',
		buttons: '#tec-tc-gateway-paypal-checkout-buttons',
		advancedPayments: {
			container: '.tribe-tickets__commerce-checkout-paypal-advanced-payments-container',
			form: '.tribe-tickets__commerce-checkout-paypal-advanced-payments-form',
			cardField: '#tec-tc-card-number',
			cvvField: '#tec-tc-cvv',
			nameField: '#tec-tc-card-holder-name',
			expirationField: '#tec-tc-expiration-date',
		},
	};

	/**
	 * Handles the creation of the orders via PayPal.
	 *
	 * @since 5.1.9
	 *
	 * @param {Object} data PayPal data passed to this method.
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.handleCancel = function ( data, $container ) {
		tribe.tickets.debug.log( 'handleCancel', arguments );
		$container.removeClass( obj.selectors.activePayment.className() );
		obj.triggerCancelOrder( $container, data.orderID, null, null );
	};

	/**
	 * Handles the creation of the orders via PayPal.
	 *
	 * @since 5.1.9
	 *
	 * @param {Object} error PayPal data passed to this method.
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.handleGenericError = function ( error, $container ) {
		tribe.tickets.debug.log( 'handleGenericError', arguments );
		$container.removeClass( obj.selectors.activePayment.className() );

		obj.showNotice( $container, error.title, error.content );
	};

	/**
	 * Handles the click when one of the buttons were clicked.
	 *
	 * @since 5.1.9
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.handleClick = function ( $container ) {
		tribe.tickets.debug.log( 'handleClick', arguments );
		$container.addClass( obj.selectors.activePayment.className() );
		obj.hideNotice( $container );
	};

	/**
	 * Handles the creation of the orders via PayPal.
	 *
	 * @since 5.1.9
	 *
	 * @param {Object} data PayPal data passed to this method.
	 * @param {Object} actions PayPal actions available on order creation.
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.handleCreateOrder = function ( data, actions, $container ) {
		tribe.tickets.debug.log( 'handleCreateOrder', arguments );
		return fetch(
			obj.orderEndpointUrl,
			{
				method: 'POST',
				headers: {
					'X-WP-Nonce': $container.find( tribe.tickets.commerce.selectors.nonce ).val(),
				}
			}
		)
			.then( response => response.json() )
			.then( data => {
				tribe.tickets.debug.log( data );
				if ( data.success ) {
					return obj.handleCreateOrderSuccess( $container, data );
				} else {
					return obj.handleCreateOrderFail( $container, data );
				}
			} )
			.catch( () => {
				obj.handleCreateOrderError( $container );
			} );
	};

	/**
	 * When a successful request is completed to our Create Order endpoint.
	 *
	 * @since 5.1.9
	 * @since 5.2.0 $container Param added.
	 *
	 * @param {jQuery} $container To which container this handling is for.
	 * @param {Object} data Data returning from our endpoint.
	 *
	 * @return {string}
	 */
	obj.handleCreateOrderSuccess = function ( $container, data ) {
		tribe.tickets.debug.log( 'handleCreateOrderSuccess', arguments );
		return data.id;
	};

	/**
	 * When a failed request is completed to our Create Order endpoint.
	 *
	 * @since 5.1.9
	 * @since 5.2.0 $container Param added.
	 *
	 * @param {jQuery} $container To which container this handling is for.
	 * @param {Object} data Data returning from our endpoint.
	 *
	 * @return {void}
	 */
	obj.handleCreateOrderFail = function ( $container, data ) {
		tribe.tickets.debug.log( 'handleCreateOrderFail', arguments );
		obj.showNotice( $container, data.title, data.content );
	};

	/**
	 * When a error happens on the fetch request to our Create Order endpoint.
	 *
	 * @since 5.1.9
	 * @since 5.2.0 $container Param added.
	 *
	 * @param {jQuery} $container To which container this handling is for.
	 * @param {Object} error Which error the fetch() threw on requesting our endpoints.
	 *
	 * @return {void}
	 */
	obj.handleCreateOrderError = function ( $container, error ) {
		tribe.tickets.debug.log( 'handleCreateOrderError', arguments );
	};

	/**
	 * Handles the Approval of the orders via PayPal.
	 *
	 * @since 5.1.9
	 *
	 * @param {Object} data PayPal data passed to this method.
	 * @param {Object} actions PayPal actions available on approve.
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.handleApprove = function ( data, actions, $container ) {
		tribe.tickets.debug.log( 'handleApprove', arguments );
		/**
		 * @todo On approval we receive a bit more than just the orderID on the data object
		 *       we should be passing those to the BE.
		 */

		const body = {
			'payer_id': data.payerID ?? '',
		};

		return fetch(
			obj.orderEndpointUrl + '/' + data.orderID,
			{
				method: 'POST',
				headers: {
					'X-WP-Nonce': $container.find( tribe.tickets.commerce.selectors.nonce ).val(),
					'Content-Type': 'application/json',
				},
				body: JSON.stringify( body ),
			}
		)
			.then( response => response.json() )
			.then( data => {
				if ( data.success ) {
					return obj.handleApproveSuccess( data, actions, $container );
				} else {
					return obj.handleApproveFail( data, actions, $container );
				}
			} )
			.catch( obj.handleApproveError );
	};

	/**
	 * When a successful request is completed to our Approval endpoint.
	 *
	 * @since 5.1.9
	 *
	 * @param {Object} data Data returning from our endpoint.
	 *
	 * @return {void}
	 */
	obj.handleApproveSuccess = ( data, actions, $container ) => {
		tribe.tickets.debug.log( 'handleApproveSuccess', data, actions );
		// If the Token has expired we refresh the browser.
		window.location.replace( data.redirect_url );
	};

	/**
	 * When a failed request is completed to our Approval endpoint.
	 *
	 * @since 5.1.9
	 *
	 * @param {Object} data Data returning from our endpoint.
	 *
	 * @return {void}
	 */
	obj.handleApproveFail = ( data, actions, $container ) => {
		tribe.tickets.debug.log( 'handleApproveFail', data, actions );

		if ( 'UNPROCESSABLE_ENTITY' === data.data.name ) {
			if ( 'INSTRUMENT_DECLINED' === data.data.details[ 0 ].issue ) {
				obj.showNotice( $container, '', data.data.details[ 0 ].description );
				// Recoverable state, per:
				// return actions.restart();
				// https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
			} else {
				obj.showNotice( $container, '', data.message );
			}
		}

		tribe.tickets.loader.hide( $container );
	};

	/**
	 * When a error happens on the fetch request to our Approval endpoint.
	 *
	 * @since 5.1.9
	 *
	 * @param {Object} error Which error the fetch() threw on requesting our endpoints.
	 *
	 * @return {void}
	 */
	obj.handleApproveError = function ( error ) {
		tribe.tickets.debug.log( 'handleApproveError', arguments );
	};

	/**
	 * Fetches the configuration object for the PayPal buttons.
	 *
	 * @since 5.1.9
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.getButtonConfig = function ( $container ) {
		let configs = {
			style: {
				layout: 'vertical',
				shape: 'rect',
				label: 'paypal'
			},
			createOrder: ( data, actions ) => {
				return obj.handleCreateOrder( data, actions, $container );
			},
			onApprove: ( data, actions ) => {
				return obj.handleApprove( data, actions, $container );
			},
			onCancel: ( data ) => {
				return obj.handleCancel( data, $container );
			},
			onError: ( data ) => {
				return obj.handleGenericError( data, $container );
			},
			onClick: () => {
				return obj.handleClick( $container );
			}
		};

		return configs;
	};

	/**
	 * Triggers an AJAX request to handle the failing of an order.
	 *
	 * @since 5.2.0
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 * @param {string} orderId PayPal Order ID.
	 * @param {string} status To which status in Tickets Commerce we should move this order to.
	 * @param {string} reason What is the reason this order is failing.
	 *
	 * @return {void}
	 */
	obj.triggerCancelOrder = ( $container, orderId, status, reason ) => {
		const data = {
			failed_status: status,
			failed_reason: reason,
		};

		$document.trigger( tribe.tickets.commerce.customEvents.showLoader );

		return fetch(
			obj.orderEndpointUrl + '/' + orderId,
			{
				method: 'DELETE',
				headers: {
					'X-WP-Nonce': $container.find( tribe.tickets.commerce.selectors.nonce ).val(),
				},
				body: JSON.stringify( data )
			}
		)
			.then( response => response.json() )
			.then( data => {
				$document.trigger( tribe.tickets.commerce.customEvents.hideLoader );
				tribe.tickets.debug.log( data );
				if ( data.success ) {
					return obj.handleCancelOrderSuccess( $container, data );
				} else {
					return obj.handleCancelOrderFail( $container, data );
				}
			} )
			.catch( () => {
				obj.handleCancelOrderError( $container );
			} );
	};

	/**
	 * If the failing of an order AJAX request returns an error we need to be able to catch it.
	 *
	 * @since 5.2.0
	 *
	 * @return {void}
	 */
	obj.handleCancelOrderSuccess = ( $container, data ) => {
		tribe.tickets.debug.log( 'handleCancelOrderSuccess', arguments );
		obj.showNotice( $container, data.title, '' );
	};

	/**
	 * If the failing of an order AJAX request returns an error we need to be able to catch it.
	 *
	 * @since 5.2.0
	 *
	 * @return {void}
	 */
	obj.handleCancelOrderFail = ( $container, data ) => {
		tribe.tickets.debug.log( 'handleCancelOrderFail', arguments );
		obj.showNotice( $container, data.title, '' );
	};
	/**
	 * If the failing of an order AJAX request returns an error we need to be able to catch it.
	 *
	 * @since 5.2.0
	 *
	 * @return {void}
	 */
	obj.handleCancelOrderError = ( $container ) => {
		tribe.tickets.debug.log( 'handleCancelOrderError', arguments );
		obj.showNotice( $container );
	};

	/**
	 * Redirect the user back to the checkout page when the Token is expired so it gets refreshed properly.
	 *
	 * @since 5.1.9
	 *
	 * @param {jQuery} $container jQuery Object.
	 */
	obj.timeoutRedirect = ( $container ) => {
		// Prevent redirecting when a payment is engaged.
		if ( $container.is( obj.selectors.activePayment.className() ) ) {
			return;
		}

		// When this Token has expired we just refresh the browser.
		window.location.replace( window.location.href );
	};

	/**
	 * Setup the Buttons for PayPal Checkout.
	 *
	 * @since 5.1.9
	 *
	 * @param  {Event}   event      event object for 'afterSetup.tecTicketsCommerce' event
	 * @param  {jQuery}  $container jQuery object of checkout container.
	 *
	 * @return {void}
	 */
	obj.setupButtons = function ( event, $container ) {
		paypal.Buttons( obj.getButtonConfig( $container ) ).render( obj.selectors.buttons );

		const $checkoutScript = $container.find( obj.selectors.checkoutScript );

		if ( $checkoutScript.length && $checkoutScript.is( '[data-client-token-expires-in]' ) ) {
			const timeout = parseInt( $checkoutScript.data( 'clientTokenExpiresIn' ), 10 ) * 1000;
			obj.timeouts.push( setTimeout( obj.timeoutRedirect, timeout, $container ) );
		}
	};

	/**
	 * Handle actions when checkout buttons are loaded.
	 *
	 * @since 5.1.10
	 */
	obj.buttonsLoaded = function () {
		$document.trigger( tribe.tickets.commerce.customEvents.hideLoader );
		$( tribe.tickets.commerce.selectors.checkoutContainer ).off( 'DOMNodeInserted', obj.selectors.buttons, obj.buttonsLoaded );
	};

	/**
	 * Shows the notice for the checkout container for PayPal.
	 *
	 * @since 5.2.0
	 *
	 * @param {jQuery} $container Parent container of notice element.
	 * @param {string} title Notice Title.
	 * @param {string} content Notice message content.
	 */
	obj.showNotice = ( $container, title, content ) => {
		if ( ! $container || ! $container.length ) {
			$container = $( tribe.tickets.commerce.selectors.checkoutContainer );
		}
		const notice = tribe.tickets.commerce.notice;
		const $item = $container.find( notice.selectors.item );
		notice.populate( $item, title, content );
		notice.show( $item );
	};

	/**
	 * Hides the notice for the checkout container for PayPal.
	 *
	 * @since 5.2.0
	 *
	 * @param {jQuery} $container Parent container of notice element.
	 */
	obj.hideNotice = ( $container ) => {
		if ( ! $container.length ) {
			$container = $( tribe.tickets.commerce.selectors.checkoutContainer );
		}

		const notice = tribe.tickets.commerce.notice;
		const $item = $container.find( notice.selectors.item );
		notice.hide( $item );
	};

	/**
	 * Setup the triggers for Ticket Commerce loader view.
	 *
	 * @since 5.1.10
	 *
	 * @return {void}
	 */
	obj.setupLoader = function () {
		$document.trigger( tribe.tickets.commerce.customEvents.showLoader );

		// Hide loader when Paypal buttons are added.
		$( tribe.tickets.commerce.selectors.checkoutContainer ).on( 'DOMNodeInserted', obj.selectors.buttons, obj.buttonsLoaded );
	};

	/**
	 * Bind script loader to trigger script dependent methods.
	 *
	 * @since 5.1.10
	 */
	obj.bindScriptLoader = function () {

		const $script = $( obj.selectors.checkoutScript );

		if ( ! $script.length ) {
			$document.trigger( tribe.tickets.commerce.customEvents.hideLoader );
			obj.showNotice();
			return;
		}

		/**
		 * If PayPal is loaded already then setup PayPal buttons.
		 */
		if ( typeof paypal !== 'undefined' ) {
			obj.setupButtons( {}, $( tribe.tickets.commerce.selectors.checkoutContainer ) );
			obj.setupAdvancedPayments( {}, $( tribe.tickets.commerce.selectors.checkoutContainer ) );
			return;
		}

		/**
		 * Setup PayPal buttons when everything is loaded.
		 */
		window.onload = ( event ) => {
			if ( typeof paypal === 'undefined' ) {
				obj.showNotice();
				$document.trigger( tribe.tickets.commerce.customEvents.hideLoader );
				return;
			}
			obj.setupButtons( event, $( tribe.tickets.commerce.selectors.checkoutContainer ) );
		};
	};

	/**
	 * Configures the Advanced Payments to the checkout page.
	 *
	 * @since 5.2.0
	 *
	 * @param {Event|Object} event
	 * @param {jQuery} $container
	 */
	obj.setupAdvancedPayments = ( event, $container ) => {
		// If this returns false or the card fields aren't visible, see Step #1.
		if ( ! paypal.HostedFields.isEligible() ) {
			// Hides card fields if the merchant isn't eligible
			$container.find( obj.selectors.advancedPayments.form ).hide();

			return;
		}

		/**
		 * See references on how to use:
		 * https://developer.paypal.com/docs/business/javascript-sdk/javascript-sdk-reference/#paypalhostedfields
		 */
		paypal.HostedFields.render( {
			createOrder: ( data, actions ) => {
				return obj.handleCreateOrder( data, actions, $container );
			},

			styles: {
				'.invalid': {
					'color': '#DA394D'
				},
				'input::placeholder': {
					color: '#999999'
				}
			},

			fields: {
				number: {
					selector: obj.selectors.advancedPayments.cardField,
					placeholder: obj.advancedPayments.fieldPlaceholders.number,
				},
				cvv: {
					selector: obj.selectors.advancedPayments.cvvField,
					placeholder: obj.advancedPayments.fieldPlaceholders.cvv,
				},
				expirationDate: {
					selector: obj.selectors.advancedPayments.expirationField,
					placeholder: obj.advancedPayments.fieldPlaceholders.expirationDate,
				}
			}
		} ).then( ( cardFields ) => {
			return obj.handleHostedFields( cardFields, $container );
		} );
	};

	/**
	 * Handles the Hosted Fields from PayPal.
	 *
	 * @since 5.2.0
	 *
	 * @param {Object} cardFields
	 * @param {jQuery} $container
	 */
	obj.handleHostedFields = ( cardFields, $container ) => {
		$container.find( obj.selectors.advancedPayments.form ).on( 'submit', ( event ) => {
			return obj.onHostedSubmit( event, cardFields, $container );
		} );
	};

	/**
	 * Fetches the configuration for the any extra fields that need to be passed to PayPal, if we implement address later
	 * to how we handle Hosted fields, we use this.
	 *
	 * @since 5.2.0
	 *
	 * @param {jQuery} $container
	 *
	 * @return {Object}
	 */
	obj.getExtraCardFields = ( $container ) => {
		return {
			// Cardholder's first and last name
			cardholderName: $container.find( obj.selectors.advancedPayments.nameField ).val(),
		};
	};

	/**
	 * When the Hosted Fields form is submitted we need to trigger some actions on PayPal, so we use this method for that.
	 *
	 * @since 5.2.0
	 *
	 * @param {Event} event
	 * @param {Object} cardFields
	 * @param {jQuery} $container
	 */
	obj.onHostedSubmit = ( event, cardFields, $container ) => {
		event.preventDefault();

		tribe.tickets.loader.show( $container );

		cardFields.submit( obj.getExtraCardFields( $container ) ).then( ( data, actions ) => {
			obj.handleHostedApprove( data, actions, $container );
		} ).catch( ( error ) => {
			obj.handleHostedCaptureError( error, $container );
		} );
	};

	/**
	 * When submitting the Hosted Fields there might be an error due to some problem in configuration so we make sure
	 * we handle that.
	 *
	 * @since 5.2.0
	 *
	 * @param {Object} error
	 * @param {jQuery} $container
	 */
	obj.handleHostedCaptureError = ( error, $container ) => {
		tribe.tickets.debug.log( 'handleHostedCaptureError', error );
		tribe.tickets.loader.hide( $container );

		let errorTitle = '';
		let errorContent = '';

		if ( [ 'INVALID_REQUEST', 'UNPROCESSABLE_ENTITY' ].includes( error.name ) ) {
			errorContent = error.message;
		}

		if ( 'VALIDATION_ERROR' === error.name ) {
			errorContent = $( '<div>' );

			if ( Array.isArray( error.details ) ) {
				error.details.map( ( item ) => {
					const $item = $( '<p>' ).text( item.description );
					errorContent.append( $item );
				} );
			}
		}

		// For now show no error, but eventually generic error needs to be done here.
		if ( '' === errorContent && '' === errorTitle ) {
			return;
		}

		obj.showNotice( $container, errorTitle, errorContent );
	};

	/**
	 * Handles the Approval of the orders via PayPal.
	 *
	 * @since 5.2.0
	 *
	 * @param {Object} data PayPal data passed to this method.
	 * @param {Object} actions PayPal actions available on approve.
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.handleHostedApprove = function ( data, actions, $container ) {
		tribe.tickets.debug.log( 'handleHostedApprove', arguments );

		const body = {
			'advanced_payment': true,
		};

		return fetch(
			obj.orderEndpointUrl + '/' + data.orderId,
			{
				method: 'POST',
				headers: {
					'X-WP-Nonce': $container.find( tribe.tickets.commerce.selectors.nonce ).val(),
					'Content-Type': 'application/json',
				},
				body: JSON.stringify( body ),
			}
		)
			.then( response => response.json() )
			.then( data => {
				tribe.tickets.debug.log( data );
				if ( data.success ) {
					return obj.handleHostedApproveSuccess( data, actions, $container );
				} else {
					return obj.handleHostedApproveFail( data, actions, $container );
				}
			} )
			.catch( ( error ) => {
				obj.handleHostedApproveError( error, $container );
			} );
	};

	/**
	 * When a successful request is completed to our Approval endpoint.
	 *
	 * @since 5.2.0
	 *
	 * @param {Object} data Data returning from our endpoint.
	 *
	 * @return {void}
	 */
	obj.handleHostedApproveSuccess = function ( data, actions, $container ) {
		tribe.tickets.debug.log( 'handleHostedApproveSuccess', arguments );
		tribe.tickets.loader.hide( $container );
		// When this Token has expired we just refresh the browser.
		window.location.replace( data.redirect_url );
	};

	/**
	 * When a failed request is completed to our Approval endpoint.
	 *
	 * @since 5.2.0
	 *
	 * @param {Object} data Data returning from our endpoint.
	 *
	 * @return {void}
	 */
	obj.handleHostedApproveFail = ( data, actions, $container ) => {
		tribe.tickets.debug.log( 'handleHostedApproveFail', data, actions, $container );
		if ( 'UNPROCESSABLE_ENTITY' === data.data.name ) {
			if ( 'INSTRUMENT_DECLINED' === data.data.details[ 0 ].issue ) {
				obj.showNotice( $container, '', data.data.details[ 0 ].description );
				// Recoverable stawte, per:
				// return actions.restart();
				// https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
			} else {
				obj.showNotice( $container, '', data.message );
			}
		}

		tribe.tickets.loader.hide( $container );
	};

	/**
	 * When a error happens on the fetch request to our Approval endpoint.
	 *
	 * @since 5.2.0
	 *
	 * @param {Object} error Which error the fetch() threw on requesting our endpoints.
	 *
	 * @return {void}
	 */
	obj.handleHostedApproveError = ( error, $container, ...rest ) => {
		tribe.tickets.loader.hide( $container );
		tribe.tickets.debug.log( 'handleHostedApproveError', error, rest );
	};

	/**
	 * Handles the initialization of the tickets commerce events when Document is ready.
	 *
	 * @since 5.1.9
	 *
	 * @return {void}
	 */
	obj.ready = function () {
		obj.setupLoader();
		obj.bindScriptLoader();
	};

	$( obj.ready );

} )( jQuery, tribe.tickets.commerce.gateway.paypal.checkout );

/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 5.1.6
 *
 * @type {Object}
 */
tribe.tickets = tribe.tickets || {};
tribe.tickets.admin = tribe.tickets.admin || {};
tribe.dialogs = tribe.dialogs || {};
tribe.dialogs.events = tribe.dialogs.events || {};

/**
 * Configures admin commerce settings Object in the Global Tribe variable
 *
 * @since 5.1.6
 *
 * @type {Object}
 */
tribe.tickets.admin.commerceSettings = {};

/**
 * Initializes in a Strict env the code that manages the Tickets Commerce settings page.
 *
 * @since 5.1.6
 *
 * @param  {Object} $   jQuery
 * @param  {Object} _   Underscore.js
 * @param  {Object} obj tribe.tickets.admin.commerceSettings
 *
 * @return {void}
 */
( function( $, _, obj ) {
	'use strict';
	const $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 5.1.6
	 *
	 * @type {PlainObject}
	 */
	// @todo Replace ID/class names.
	obj.selectors = {
		connectButton: '#js-give-paypal-on-boarding-handler',
		connectButtonWrap: '.connect-button-wrap',
		connectionSettingContainer: '#give-paypal-commerce-account-manager-field-wrap .connection-setting', // eslint-disable-line max-len
		container: '#tribe-field-tickets-commerce-paypal-commerce-configure',
		countrySelect: '#tickets-commerce-paypal-commerce-account-country-select',
		errorMessageTemplate: '.paypal-message-template',
		disconnectionSettingContainer: '#give-paypal-commerce-account-manager-field-wrap .disconnection-setting', // eslint-disable-line max-len
		disconnectPayPalAccountButton: '#js-give-paypal-disconnect-paypal-account',
		troubleNotice: '#give-paypal-onboarding-trouble-notice',
	};

	obj.observePayPalModal = function() {
		obj.paypalErrorQuickHelp = $( obj.selectors.troubleNotice );

		const paypalModalObserver = new MutationObserver( function( mutationsRecord ) {
			mutationsRecord.forEach( function( record ) {
				record.removedNodes.forEach( function( node ) {
					if ( 'PPMiniWin' !== node.getAttribute( 'id' ) ) {
						return;
					}

					obj.paypalErrorQuickHelp[0] &&
						obj.paypalErrorQuickHelp.removeClass( 'tribe-common-a11y-hidden' );
				} );
			} );
		} );

		paypalModalObserver.observe( document.querySelector( 'body' ), {
			attributes: true,
			childList: true,
		} );
	}

	obj.maybeShowPCINotice = function() {

		if ( ! window.location.search.match( /tc-status=paypal-signup-complete/i ) ) {
			return;
		}

		tribe.dialogs.dialogs.forEach( function( dialog ) {
			if ( 'paypal-connected-modal-id' === dialog.id ) {
				dialog.a11yInstance.show();
			}

			dialog.a11yInstance.node.querySelectorAll( '[data-js="a11y-close-button"]' )
				.forEach( function( closeButton ) {
					$( closeButton ).on( 'click', function() {
						dialog.a11yInstance.hide();
					} );
			} );
		} );

	};

	obj.setupPartnerLink = function( partnerLink ) {
		const payPalLink = document.querySelector( '[data-paypal-button]' );

		payPalLink.href = partnerLink + '&displayMode=minibrowser';
		payPalLink.click();

		// This object will check if a class added to body or not.
		// If class added that means modal opened.
		// If class removed that means modal closed.
		obj.observePayPalModal();
	};

	/**
	 * Performs an AJAX request to get the partner URL.
	 *
	 * @since 5.1.6
	 *
	 * @param {String} countryCode The country code.
	 *
	 * @return {void}
	 */
	obj.requestPartnerUrl = function( countryCode ) {
		// @todo Add AJAX handler for this.
		fetch( ajaxurl + '?action=tribe_tickets_paypal_commerce_get_partner_url&country_code=' + countryCode ) // eslint-disable-line max-len
			.then( function( response ) {
				return response.json();
			} )
			.then( function( res ) {
				// Handle success.
				if ( true === res.success ) {
					obj.setupPartnerLink( res.data.partnerLink );
				}

				obj.buttonState.enable();
			} )
			.then( function() {
				// Handle the error notice.
				// @todo Add AJAX handler for this.
				fetch( ajaxurl + '?action=tribe_tickets_paypal_commerce_onboarding_trouble_notice' )
					.then( function( response ) {
						return response.json();
					} )
					.then( function( res ) {
						if ( true !== res.success ) {
							return;
						}

						function createElementFromHTML( htmlString ) {
							const div = document.createElement( 'div' );
							div.innerHTML = htmlString.trim();
							return div.firstChild;
						}

						const buttonContainer = document.querySelector( obj.selectors.connectButtonWrap );
						obj.paypalErrorQuickHelp[0] && obj.paypalErrorQuickHelp.remove();
						buttonContainer.append( createElementFromHTML( res.data ) );
					} );
			} );
	};

	obj.removeErrors = function() {
		const errorsContainer = document.querySelector( obj.selectors.errorMessageTemplate );

		if ( errorsContainer ) {
			errorsContainer.parentElement.remove();
		}
	};

	obj.buttonState = {
		enable: function() {
			obj.onBoardingButton.attr( 'disabled', false );
			obj.onBoardingButton.text( obj.onBoardingButton.data( 'initial-label' ) );
		},
		disable: function() {
			// Preserve initial label.
			if ( ! obj.onBoardingButton.data( 'initial-label' ) ) {
				obj.onBoardingButton.data( 'initial-label', obj.onBoardingButton.text().trim() );
			}

			obj.onBoardingButton.attr( 'disabled', true );

			// @todo Replace the i18n text here.
			obj.onBoardingButton.text( 'Processing text here' );
		},
	};

	obj.handleConnectClick = function( evt ) {
		evt.preventDefault();
		obj.removeErrors();

		const countryCode = $( obj.selectors.countrySelect ).val();

		obj.buttonState.disable();

		obj.paypalErrorQuickHelp = $( obj.selectors.troubleNotice );

		// Hide paypal quick help message.
		obj.paypalErrorQuickHelp[0] && obj.paypalErrorQuickHelp.addClass( 'tribe-common-a11y-hidden' );

		obj.requestPartnerUrl( countryCode );
	};

	obj.handleDisconnectClick = function( evt ) {
		evt.preventDefault();
		obj.removeErrors();

		// @todo Show a confirmation modal here.
		//title: givePayPalCommerce.translations.confirmPaypalAccountDisconnection
		//desc: givePayPalCommerce.translations.disconnectPayPalAccount

		// On modal confirmation, disconnect the account.
		$( obj.selectors.connectionSettingContainer ).removeClass( 'tribe-common-a11y-hidden' );
		$( obj.selectors.disconnectionSettingContainer ).addClass( 'tribe-common-a11y-hidden' );

		fetch( ajaxurl + '?action=tribe_tickets_paypal_commerce_disconnect_account' );
	};

	obj.onBoardCallback = function( authCode, sharedId ) {
		const query = '&authCode=' + authCode + '&sharedId=' + sharedId;

		fetch( ajaxurl + '?action=tribe_tickets_paypal_commerce_user_on_boarded' + query )
			.then( function ( res ) {
				return res.json()
			} )
			.then( function ( res ) {
				if ( true !== res.success ) {
					// @todo Improve the error messaging here.
					alert( 'Something went wrong while we were connecting your account, please try again.' );
					return;
				}

				// Remove PayPal quick help container.
				obj.paypalErrorQuickHelp = $( obj.selectors.troubleNotice );
				obj.paypalErrorQuickHelp[0] && obj.paypalErrorQuickHelp.remove();
			} );
	}

	/**
	 * Handles the initialization of the gateway settings when Document is ready.
	 *
	 * @since 5.1.6
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		obj.onBoardingButton = $( obj.selectors.connectButton );
		obj.disconnectButton = $( obj.selectors.disconnectPayPalAccountButton );

		if ( obj.onBoardingButton[0] ) {
			obj.onBoardingButton.on( 'click', obj.handleConnectClick );
		}

		if ( obj.disconnectButton[0] ) {
			obj.disconnectButton.on( 'click', obj.handleDisconnectClick );
		}

		obj.maybeShowPCINotice();
	};

	// Configure on document ready.
	$document.ready( obj.ready );
} )( jQuery, window.underscore || window._, tribe.tickets.admin.commerceSettings );

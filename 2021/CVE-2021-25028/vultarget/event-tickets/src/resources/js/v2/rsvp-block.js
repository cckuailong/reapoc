/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 5.0.0
 *
 * @type {Object}
 */
tribe.tickets = tribe.tickets || {};
tribe.tickets.rsvp = tribe.tickets.rsvp || {};

/**
 * Configures RSVP block Object in the Global Tribe variable
 *
 * @since 5.0.0
 *
 * @type {Object}
 */
tribe.tickets.rsvp.block = {};

/**
 * Initializes in a Strict env the code that manages the RSVP block.
 *
 * @since 5.0.0
 *
 * @param  {Object} $   jQuery
 * @param  {Object} obj tribe.tickets.rsvp.block
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	const $document = $( document );

	/**
	 * Selectors used for configuration and setup
	 *
	 * @since 5.0.0
	 *
	 * @type {Object}
	 */
	obj.selectors = {
		container: '.tribe-tickets__rsvp-wrapper',
		rsvpForm: 'form[name~="tribe-tickets-rsvp-form"]',
		goingButton: '.tribe-tickets__rsvp-actions-button-going',
		notGoingButton: '.tribe-tickets__rsvp-actions-button-not-going',
		cancelButton: '.tribe-tickets__rsvp-form-button--cancel',
		errorMessage: '.tribe-tickets__form-message--error',
		hiddenElement: '.tribe-common-a11y-hidden',
		displayToggle: '.tribe-tickets__rsvp-actions-success-going-toggle-input',
	};

	/**
	 * Binds events for the going button.
	 *
	 * @since 5.0.0
	 *
	 * @param {jQuery} $container jQuery object of the RSVP container.
	 *
	 * @return {void}
	 */
	obj.bindGoing = function( $container ) {
		let data  = {};
		const rsvpId = $container.data( 'rsvp-id' );
		const $goingButton = $container.find( obj.selectors.goingButton );

		$goingButton.each( function( index, button ) {
			$( button ).on( 'click', function() {
				data = {
					action: 'tribe_tickets_rsvp_handle',
					ticket_id: rsvpId,
					step: 'going',
				};

				tribe.tickets.rsvp.manager.request( data, $container );
			} );
		} );
	};

	/**
	 * Binds events for the not going button.
	 *
	 * @since 5.0.0
	 *
	 * @param {jQuery} $container jQuery object of the RSVP container.
	 *
	 * @return {void}
	 */
	obj.bindNotGoing = function( $container ) {
		let data  = {};
		const rsvpId = $container.data( 'rsvp-id' );
		const $notGoingButton = $container.find( obj.selectors.notGoingButton );

		$notGoingButton.each( function( index, button ) {
			$( button ).on( 'click', function() {
				data = {
					action: 'tribe_tickets_rsvp_handle',
					ticket_id: rsvpId,
					step: 'not-going',
				};

				tribe.tickets.rsvp.manager.request( data, $container );
			} );
		} );
	};

	/**
	 * Binds events for the cancel button.
	 *
	 * @since 5.0.0
	 *
	 * @param {jQuery} $container jQuery object of the RSVP container.
	 *
	 * @return {void}
	 */
	obj.bindCancel = function( $container ) {
		let data  = {};
		const rsvpId = $container.data( 'rsvp-id' );
		const $cancelButton = $container.find( obj.selectors.cancelButton );

		$cancelButton.each( function( index, button ) {
			$( button ).on( 'click', function() {

				if ( ! confirm( TribeRsvp.cancelText ) ) {
					return;
				}

				data = {
					action: 'tribe_tickets_rsvp_handle',
					ticket_id: rsvpId,
					step: null,
				};

				tribe.tickets.rsvp.manager.request( data, $container );
			} );
		} );
	};

	/**
	 * Handle the RSVP toggle for listing in public attendee list.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event} event Input event
	 */
	obj.handleDisplayToggle = function( event ) {
		event.preventDefault();

		const $input = $( event.target );
		const rsvpId = $input.data( 'rsvp-id' );
		const checked = $input.prop( 'checked' );
		const attendeeIds = $input.data( 'attendee-ids' );
		const nonce = $input.data( 'opt-in-nonce' );
		const $container = event.data.container;

		const data = {
			action: 'tribe_tickets_rsvp_handle',
			ticket_id: rsvpId,
			step: 'opt-in',
			opt_in: checked,
			opt_in_nonce: nonce,
			attendee_ids: attendeeIds,
		};

		tribe.tickets.rsvp.manager.request( data, $container );
	};

	/**
	 * Handle the RSVP form submission
	 *
	 * @since 5.0.0
	 *
	 * @param {event} e submission event
	 */
	obj.handleSubmission = function( e ) {
		e.preventDefault();

		const $form = $( this );
		const $container = $form.closest( obj.selectors.container );
		const rsvpId = $form.data( 'rsvp-id' );
		const params = $form.serializeArray();

		var data = {
			action: 'tribe_tickets_rsvp_handle',
			ticket_id: rsvpId,
			step: 'success',
		};

		$( params ).each( function( index, object ) {
			data[ object.name ] = object.value;
		} );

		tribe.tickets.rsvp.manager.request( data, $container );
	};

	/**
	 * Binds events for the RSVP form.
	 *
	 * @since 5.0.0
	 *
	 * @param {jQuery} $container jQuery object of the RSVP container.
	 *
	 * @return {void}
	 */
	obj.bindForm = function( $container ) {
		const $rsvpForm = $container.find( obj.selectors.rsvpForm );

		$rsvpForm.each( function( index, form ) {
			$( form ).on( 'submit', obj.handleSubmission );
		} );
	};

	/**
	 * Binds events for the display in public attendee toggle.
	 *
	 * @since 5.0.0
	 *
	 * @param {jQuery} $container jQuery object of the RSVP container.
	 *
	 * @return {void}
	 */
	obj.bindDisplayToggle = function( $container ) {
		const $displayToggle = $container.find( obj.selectors.displayToggle );

		$displayToggle.on(
			'input',
			{ container: $container },
			obj.handleDisplayToggle
		);
	};

	/**
	 * Unbinds events.
	 *
	 * @since 5.0.0
	 *
	 * @param  {Event}       event    event object for 'beforeAjaxSuccess.tribeTicketsRsvp' event
	 * @param  {jqXHR}       jqXHR    Request object
	 * @param  {Object}      settings Settings that this request was made with
	 *
	 * @return {void}
	 */
	obj.unbindEvents = function( event, jqXHR, settings ) { // eslint-disable-line no-unused-vars
		const $container = event.data.container;
		const $goingButton = $container.find( obj.selectors.goingButton );
		const $notGoingButton = $container.find( obj.selectors.notGoingButton );
		const $cancelButton = $container.find( obj.selectors.cancelButton );
		const $rsvpForm = $container.find( obj.selectors.rsvpForm );
		const $displayToggle = $container.find( obj.selectors.displayToggle );

		$goingButton.off();
		$notGoingButton.off();
		$cancelButton.off();
		$rsvpForm.off();
		$displayToggle.off();
	};

	/**
	 * Binds events for container.
	 *
	 * @since 5.0.0
	 *
	 * @param {jQuery}  $container jQuery object of object of the RSVP container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {

		obj.bindGoing( $container );
		obj.bindNotGoing( $container );
		obj.bindCancel( $container );
		obj.bindForm( $container );
		obj.bindDisplayToggle( $container );

		$container.on(
			'beforeAjaxSuccess.tribeTicketsRsvp',
			{ container: $container },
			obj.unbindEvents
		);
	};

	/**
	 * Initialize RSVP events.
	 *
	 * @since 5.0.0
	 *
	 * @param {Event}   event      event object for 'afterSetup.tribeTicketsRsvp' event
	 * @param {int}     index      jQuery.each index param from 'afterSetup.tribeTicketsRsvp' event.
	 * @param {jQuery}  $container jQuery object of view container.
	 *
	 * @return {void}
	 */
	obj.init = function( event, index, $container ) {
		obj.bindEvents( $container );
	};

	/**
	 * Handles the initialization of the RSVP block events when Document is ready.
	 *
	 * @since 5.0.0
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		$document.on(
			'afterSetup.tribeTicketsRsvp',
			tribe.tickets.rsvp.manager.selectors.container,
			obj.init
		);
	};

	// Configure on document ready.
	$( obj.ready );
} )( jQuery, tribe.tickets.rsvp.block );

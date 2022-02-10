/**
 * Makes sure we have all the required levels on the Tribe Object
 *
 * @since 5.0.3
 *
 * @type {PlainObject}
 */
tribe.tickets = tribe.tickets || {};

/**
 * Configures Tickets Block Object in the Global Tribe variable
 *
 * @since 5.0.3
 *
 * @type {PlainObject}
 */
tribe.tickets.block = {
	num_attendees: 0,
	event: {},
};

/**
 * Initializes in a Strict env the code that manages the Tickets Block
 *
 * @since 5.0.3
 *
 * @param  {PlainObject} $   jQuery
 * @param  {PlainObject} obj tribe.tickets.block
 *
 * @return {void}
 */
( function( $, obj ) {
	'use strict';
	const $document = $( document );

	/**
	 * Ticket Block Selectors.
	 *
	 * @since 5.0.3
	 */
	obj.selectors = {
		container: '.tribe-tickets__tickets-wrapper',
		form: '.tribe-tickets__tickets-form',
		blockFooter: '.tribe-tickets__tickets-footer',
		blockFooterActive: 'tribe-tickets__tickets-footer--active',
		blockFooterAmount: '.tribe-amount',
		blockFooterQuantity: '.tribe-tickets__tickets-footer-quantity-number',
		blockSubmit: '#tribe-tickets__tickets-submit',
		item: '.tribe-tickets__tickets-item',
		itemDescription: '.tribe-tickets__tickets-item-details-content',
		itemDescriptionButtonMore: '.tribe-tickets__tickets-item-details-summary-button--more',
		itemDescriptionButtonLess: '.tribe-tickets__tickets-item-details-summary-button--less',
		itemExtraAvailable: '.tribe-tickets__tickets-item-extra-available',
		itemExtraAvailableQuantity: '.tribe-tickets__tickets-item-extra-available-quantity',
		itemOptOut: '.tribe-tickets-attendees-list-optout--wrapper',
		itemOptOutInput: '#tribe-tickets-attendees-list-optout-',
		itemPrice: '.tribe-tickets__tickets-sale-price .tribe-amount',
		itemQuantity: '.tribe-tickets__tickets-item-quantity',
		itemQuantityInput: '.tribe-tickets__tickets-item-quantity-number-input',
		itemQuantityAdd: '.tribe-tickets__tickets-item-quantity-add',
		itemQuantityRemove: '.tribe-tickets__tickets-item-quantity-remove',
		submit: '.tribe-tickets__tickets-buy',
		hiddenElement: '.tribe-common-a11y-hidden',
	};

	/**
	 * Commerce Provider "lookup table".
	 *
	 * @since 5.0.3
	 */
	obj.commerceSelector = {
		edd: 'Tribe__Tickets_Plus__Commerce__EDD__Main',
		rsvp: 'Tribe__Tickets__RSVP',
		tpp: 'Tribe__Tickets__Commerce__PayPal__Main',
		Tribe__Tickets__Commerce__PayPal__Main: 'tribe-commerce',
		Tribe__Tickets__RSVP: 'rsvp',
		Tribe__Tickets_Plus__Commerce__EDD__Main: 'edd',
		Tribe__Tickets_Plus__Commerce__WooCommerce__Main: 'woo',
		tribe_eddticket: 'Tribe__Tickets_Plus__Commerce__EDD__Main',
		tribe_tpp_attendees: 'Tribe__Tickets__Commerce__PayPal__Main',
		tribe_wooticket: 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main',
		woo: 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main',
	};

	/**
	 * Make DOM updates for the AJAX response.
	 *
	 * @since 5.0.3
	 *
	 * @param {array} tickets Array of tickets to iterate over.
	 *
	 * @return {void}
	 */
	obj.updateAvailability = function( tickets ) {
		Object.keys( tickets ).forEach( function( ticketId ) {
			const available = tickets[ ticketId ].available;
			const maxPurchase = tickets[ ticketId ].max_purchase;
			const $ticketEl = $( obj.selectors.item + '[data-ticket-id="' + ticketId + '"]' );

			if ( 0 === available ) { // Ticket is out of stock.
				const unavailableHtml = tickets[ ticketId ].unavailable_html;
				// Set the availability data attribute to false.
				$ticketEl.prop( 'available', false );

				// Remove classes for in-stock and purchasable.
				$ticketEl.removeClass( 'instock' );
				$ticketEl.removeClass( 'purchasable' );

				// Update HTML elements with the "Out of Stock" messages.
				$ticketEl.find( obj.selectors.itemQuantity ).html( unavailableHtml );
				$ticketEl.find( obj.selectors.itemExtraAvailable ).html( '' );
			}

			if ( 1 < available ) { // Ticket in stock, we may want to update values.
				$ticketEl.find( obj.selectors.itemQuantityInput ).attr( { max: maxPurchase } );
				$ticketEl.find( obj.selectors.itemExtraAvailableQuantity ).html( available );
			}
		} );
	};

	/**
	 * Update all the footer info.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $form The form we're updating.
	 *
	 * @return {void}
	 */
	obj.updateFooter = function( $form ) {
		const $footer = $form.find( obj.selectors.blockFooter );

		obj.updateFooterCount( $form );
		obj.updateFooterAmount( $form );

		$footer.addClass( obj.selectors.blockFooterActive.className() );
	};

	/**
	 * Adjust the footer count for +/-.
	 *
	 * @since 5.0.3
	 *
	 * @param {object} $form The form we're updating.
	 */
	obj.updateFooterCount = function( $form ) {
		const $field = $form
			.find( obj.selectors.blockFooter + ' ' + obj.selectors.blockFooterQuantity );
		const $quantities = $form
			.find( obj.selectors.item + ' ' + obj.selectors.itemQuantityInput );
		let footerCount = 0;

		$quantities.each( function() {
			const $input = $( this );

			// Only check on elements that are visible, to work with cart removals.
			if ( ! $input.is( ':visible' ) ) {
				return;
			}

			let newQuantity = parseInt( $input.val(), 10 );
			newQuantity = isNaN( newQuantity ) ? 0 : newQuantity;
			footerCount += newQuantity;
		} );

		const disabled = 0 >= footerCount ? true : false;
		tribe.tickets.utils.disable( $form.find( obj.selectors.submit ), disabled );

		if ( 0 > footerCount ) {
			return;
		}

		$field.text( footerCount );
	};

	/**
	 * Get tickets block provider.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $form The form we want to retrieve the provider from.
	 *
	 * @return {string} The provider.
	 */
	obj.getTicketsBlockProvider = function( $form ) {
		return $form.data( 'provider' );
	};

	/**
	 * Adjust the footer total/amount for +/-.
	 *
	 * @since 5.0.3
	 *
	 * @param {object} $form The form we're updating.
	 */
	obj.updateFooterAmount = function( $form ) {
		const $field = $form.find( obj.selectors.blockFooter + ' ' + obj.selectors.blockFooterAmount );
		const $quantities = $form.find( obj.selectors.item + ' ' + obj.selectors.itemQuantityInput );
		const provider = obj.getTicketsBlockProvider( $form );
		let footerAmount = 0;

		$quantities.each( function() {
			const $input = $( this );

			// Only check on elements that are visible, to work with cart removals.
			if ( ! $input.is( ':visible' ) ) {
				return;
			}

			let quantity = parseInt( $input.val(), 10 );
			quantity = isNaN( quantity ) ? 0 : quantity;
			const $ticketItem = $input.closest( obj.selectors.item );
			const ticketPrice = obj.getPrice( $ticketItem, provider );
			const cost = ticketPrice * quantity;
			footerAmount += cost;
		} );

		if ( 0 > footerAmount ) {
			return;
		}

		$field.text( tribe.tickets.utils.numberFormat( footerAmount, provider ) );
	};

	/**
	 * Update form totals.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $form The jQuery form object to update totals.
	 *
	 * @return {void}
	 */
	obj.updateFormTotals = function( $form ) {
		$document.trigger( 'beforeUpdateFormTotals.tribeTicketsBlock', [ $form ] );

		obj.updateFooter( $form );

		$document.trigger( 'afterUpdateFormTotals.tribeTicketsBlock', [ $form ] );
	};

	/**
	 * Get the tickets IDs.
	 *
	 * @since 5.0.3
	 *
	 * @returns {array} Array of tickets IDs.
	 */
	obj.getTickets = function() {
		const $tickets = $( obj.selectors.item ).map(
			function() {
				return $( this ).data( 'ticket-id' );
			}
		).get();

		return $tickets;
	};

	/**
	 * Maybe display the Opt Out.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $ticket The ticket item element.
	 * @param {number} newQuantity The new ticket quantity.
	 *
	 * @return {void}
	 */
	obj.maybeShowOptOut = function( $ticket, newQuantity ) {
		const hasOptOut = $ticket.has( obj.selectors.itemOptOut ).length;

		if ( hasOptOut ) {
			const $item = $ticket.closest( obj.selectors.item );
			if ( 0 < newQuantity ) {
				$item.addClass( 'show-optout' );
			} else {
				$item.removeClass( 'show-optout' );
			}
		}
	};

	/**
	 * Step up the input according to the button that was clicked.
	 * Handles IE/Edge.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $input The input field.
	 * @param {number} originalValue The field's original value.
	 */
	obj.stepUp = function( $input, originalValue ) {
		// We use 0 here as a shorthand for no maximum.
		const max = $input.attr( 'max' ) ? Number( $input.attr( 'max' ) ) : -1;
		const step = $input.attr( 'step' ) ? Number( $input.attr( 'step' ) ) : 1;
		let newValue = ( -1 === max || max >= originalValue + step ) ? originalValue + step : max;
		const $parent = $input.closest( obj.selectors.item );

		if ( 'true' === $parent.attr( 'data-has-shared-cap' ) ) {
			const $form = $parent.closest( 'form' );
			newValue = obj.checkSharedCapacity( $form, newValue );
		}

		if ( 0 === newValue ) {
			return;
		}

		if ( 0 > newValue ) {
			$input[ 0 ].value = originalValue + newValue;
			return;
		}

		if ( 'function' === typeof $input[ 0 ].stepUp ) {
			try {
				// Bail if we're already in the max, safari has issues with stepUp() here.
				if ( max < ( originalValue + step ) ) {
					return;
				}
				$input[ 0 ].stepUp();
			} catch ( ex ) {
				$input.val( newValue );
			}
		} else {
			$input.val( newValue );
		}
	};

	/**
	 * Step down the input according to the button that was clicked.
	 * Handles IE/Edge.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $input The input field.
	 * @param {number} originalValue The field's original value.
	 */
	obj.stepDown = function( $input, originalValue ) {
		const min = $input.attr( 'min' ) ? Number( $input.attr( 'min' ) ) : 0;
		const step = $input.attr( 'step' ) ? Number( $input.attr( 'step' ) ) : 1;
		const decrease = ( min <= originalValue - step && 0 < originalValue - step )
			? originalValue - step
			: min;

		if ( 'function' === typeof $input[ 0 ].stepDown ) {
			try {
				$input[ 0 ].stepDown();
			} catch ( ex ) {
				$input[ 0 ].value = decrease;
			}
		} else {
			$input[ 0 ].value = decrease;
		}
	};

	/**
	 * Check tickets availability.
	 *
	 * @since 5.0.3
	 */
	obj.checkAvailability = function() {
		// We're checking availability for all the tickets at once.
		const params = {
			action: 'ticket_availability_check',
			tickets: obj.getTickets(),
		};

		$.post(
			TribeTicketOptions.ajaxurl,
			params,
			function( response ) {
				const success = response.success;

				// Bail if we don't get a successful response.
				if ( ! success ) {
					return;
				}

				// Get the tickets response with availability.
				const tickets = response.data.tickets;

				// Make DOM updates.
				obj.updateAvailability( tickets );
			}
		);

		// Repeat every 60 ( filterable via tribe_tickets_availability_check_interval ) seconds.
		if ( 0 < TribeTicketOptions.availability_check_interval ) {
			setTimeout( obj.checkAvailability, TribeTicketOptions.availability_check_interval );
		}
	};

	/**
	 * Check if we're updating the qty of a shared cap ticket and
	 * limits it to the shared cap minus any tickets in cart.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $form jQuery object that is the form we are checking.
	 * @param {number} qty The quantity we desire.
	 *
	 * @returns {integer} The quantity, limited by existing shared cap tickets.
	 */
	obj.checkSharedCapacity = function( $form, qty ) {
		let sharedCap = [];
		let currentLoad = [];
		const $sharedTickets = $form
			.find( obj.selectors.item )
			.filter( '[data-has-shared-cap="true"]' );
		const $sharedCapTickets = $sharedTickets.find( obj.selectors.itemQuantityInput );

		if ( ! $sharedTickets.length ) {
			return qty;
		}

		$sharedTickets.each(
			function() {
				sharedCap.push( parseInt( $( this ).attr( 'data-available-count' ), 10 ) );
			}
		);

		$sharedCapTickets.each(
			function() {
				currentLoad.push( parseInt( $( this ).val(), 10 ) );
			}
		);

		// IE doesn't allow spread operator.
		// @todo: check that we're no longer supporting some IE versions.
		sharedCap = Math.max.apply( this, sharedCap );

		currentLoad = currentLoad.reduce(
			function( a, b ) {
				return a + b;
			},
			0
		);

		const currentAvailable = sharedCap - currentLoad;

		return Math.min( currentAvailable, qty );
	};

	/**
	 * Get the Quantity.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $cartItem The cart item to update.
	 *
	 * @returns {number} The item quantity.
	 */
	obj.getQty = function( $cartItem ) {
		const qty = parseInt( $cartItem.find( obj.selectors.itemQuantityInput ).val(), 10 );

		return isNaN( qty ) ? 0 : qty;
	};

	/**
	 * Get the Price.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $item The jQuery object of the ticket item to update.
	 *
	 * @returns {number} The item price.
	 */
	obj.getPrice = function( $item ) {
		return tribe.tickets.utils.getPrice( $item, obj.tribe_tickets_provider );
	};

	/**
	 * Get ticket data to send to cart.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $form jQuery object of the form container.
	 *
	 * @returns {array} Tickets array of objects.
	 */
	obj.getTicketsForCart = function( $form ) {
		const $ticketsForm = $form || $document;
		const tickets = [];
		const $ticketRows = $ticketsForm.find( obj.selectors.item );

		$ticketRows.each(
			function() {
				const $row = $( this );

				if ( ! $row.is( ':visible' ) ) {
					return;
				}
				const ticketId = $row.data( 'ticketId' );
				const qty = $row.find( obj.selectors.itemQuantityInput ).val();
				const $optoutInput = $row.find( '[name="attendee[optout]"]' );
				let optout = $optoutInput.val();

				if ( $optoutInput.is( ':checkbox' ) ) {
					optout = $optoutInput.prop( 'checked' ) ? 1 : 0;
				}

				const data = {};
				data.ticket_id = ticketId;
				data.quantity = qty;
				data.optout = optout;

				tickets.push( data );
			}
		);

		return tickets;
	};

	/**
	 * Unbinds events for add/remove ticket.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.unbindTicketsAddRemove = function( $container ) {
		const $addRemove = $container
			.find( obj.selectors.itemQuantityAdd + ', ' + obj.selectors.itemQuantityRemove );

		$addRemove.off();
	};

	/**
	 * Binds events for add/remove ticket.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.bindTicketsAddRemove = function( $container ) {
		const $addRemove = $container
			.find( obj.selectors.itemQuantityAdd + ', ' + obj.selectors.itemQuantityRemove );

		$addRemove.unbind( 'click' ).on(
			'click',
			function( e ) {
				e.preventDefault();
				const $input = $( this ).parent().find( 'input[type="number"]' );

				$document.trigger( 'beforeTicketsAddRemove.tribeTicketsBlock', [ $input ] );

				if ( $input.is( ':disabled' ) ) {
					return false;
				}

				const originalValue = Number( $input[ 0 ].value );

				// Step up or Step down the input according to the button that was clicked.
				// Handles IE/Edge.
				// @todo: check if we still want to support this.
				if ( $( this ).hasClass( obj.selectors.itemQuantityAdd.className() ) ) {
					obj.stepUp( $input, originalValue );
				} else {
					obj.stepDown( $input, originalValue );
				}

				obj.updateFooter( $input.closest( 'form' ) );

				// Trigger the on Change for the input ( if it has changed ) as it's not handled via stepUp() || stepDown().
				if ( originalValue !== $input[ 0 ].value ) {
					$input.trigger( 'change' );
				}

				$document.trigger( 'afterTicketsAddRemove.tribeTicketsBlock', [ $input ] );
			}
		);
	};

	/**
	 * Unbinds events for the quantity input.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.unbindTicketsQuantityInput = function( $container ) {
		const $quantityInput = $container.find( obj.selectors.itemQuantityInput );

		$quantityInput.off();
	};

	/**
	 * Binds events for the quantity input.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.bindTicketsQuantityInput = function( $container ) {
		const $quantityInput = $container.find( obj.selectors.itemQuantityInput );

		// Handle Enter/Return on the quantity input from the main tickets form.
		$quantityInput.on(
			'keypress',
			function( e ) {
				if ( e.keyCode === 13 ) {
					e.preventDefault();
					e.stopPropagation();
					return;
				}
			}
		);

		/**
		 * Handle the Ticket form(s).
		 *
		 * @since 5.0.3
		 */
		$quantityInput.on(
			'change keyup',
			function( e ) {
				const $this = $( e.target );

				$document.trigger( 'beforeTicketsQuantityChange.tribeTicketsBlock', [ $this ] );

				const $ticket = $this.closest( obj.selectors.item );
				const $form = $this.closest( 'form' );
				const max = $this.attr( 'max' );
				let maxQty = 0;
				let newQuantity = parseInt( $this.val(), 10 );
				newQuantity = isNaN( newQuantity ) ? 0 : newQuantity;

				if ( max < newQuantity ) {
					newQuantity = max;
					$this.val( max );
				}

				if ( 'true' === $ticket.attr( 'data-has-shared-cap' ) ) {
					maxQty = obj.checkSharedCapacity( $form, newQuantity );
				}

				if ( 0 > maxQty ) {
					newQuantity += maxQty;
					$this.val( newQuantity );
				}

				e.preventDefault();
				obj.maybeShowOptOut( $ticket, newQuantity );
				obj.updateFooter( $form );
				obj.updateFormTotals( $form );

				$document.trigger( 'afterTicketsQuantityChange.tribeTicketsBlock', [ $this ] );
			}
		);
	};

	/**
	 * Toggle the ticket item description visibility.
	 *
	 * @since 5.0.3
	 *
	 * @param {event} event The event.
	 *
	 * @return {void}
	 */
	obj.itemDescriptionToggle = function( event ) {
		if ( 'keyup' === event.type && 13 !== event.keyCode ) {
			return;
		}

		const trigger = event.target;

		if ( ! trigger ) {
			return;
		}

		const $trigger = $( trigger );

		if (
			! $trigger.hasClass( obj.selectors.itemDescriptionButtonMore.className() ) &&
			! $trigger.hasClass( obj.selectors.itemDescriptionButtonLess.className() )
		) {
			return;
		}

		const $parent = $trigger.closest( obj.selectors.item );
		const $target = $( '#' + $trigger.attr( 'aria-controls' ) );

		if ( ! $target.length || ! $parent.length ) {
			return;
		}

		// Let our CSS handle the hide/show. Also allows us to make it responsive.
		const onOff = ! $parent.hasClass( 'tribe__details--open' );
		$parent.toggleClass( 'tribe__details--open', onOff );
		$target.toggleClass( 'tribe__details--open', onOff );
		$target.toggleClass( obj.selectors.hiddenElement.className() );
	};

	/**
	 * Binds the description toggle.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.bindDescriptionToggle = function( $container ) {
		const $descriptionToggleButtons = $container.find(
			obj.selectors.itemDescriptionButtonMore + ', ' + obj.selectors.itemDescriptionButtonLess
		);

		// Add keyboard support for enter key.
		$descriptionToggleButtons.on(
			'keyup',
			obj.itemDescriptionToggle
		);

		$descriptionToggleButtons.on(
			'click',
			obj.itemDescriptionToggle
		);
	};

	/**
	 * Unbinds the description toggle.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.unbindDescriptionToggle = function( $container ) {
		const $descriptionToggleButtons = $container.find(
			obj.selectors.itemDescriptionButtonMore + ', ' + obj.selectors.itemDescriptionButtonLess
		);

		$descriptionToggleButtons.off();
	};

	/**
	 * Submit the tickets block form.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $form jQuery object of the form.
	 *
	 * @return {void}
	 */
	obj.ticketsSubmit = function( $form ) {
		const postId = $form.data( 'post-id' );
		const ticketProvider = $form.data( 'provider' );

		// Show the loader.
		tribe.tickets.loader.show( $form );

		// Save meta and cart.
		const params = {
			tribe_tickets_provider: obj.commerceSelector[ ticketProvider ],
			tribe_tickets_tickets: obj.getTicketsForCart( $form ),
			tribe_tickets_meta: {},
			tribe_tickets_post_id: postId,
		};

		$form.find( '#tribe_tickets_block_ar_data' ).val( JSON.stringify( params ) );

		$document.trigger( 'beforeTicketsSubmit.tribeTicketsBlock', [ $form, params ] );

		$form.submit();

		$document.trigger( 'afterTicketsSubmit.tribeTicketsBlock', [ $form, params ] );
	};

	/**
	 * Binds events the classic "Submit" (non-modal)
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.bindTicketsSubmit = function( $container ) {
		const $submitButton = $container.find( obj.selectors.submit );

		$submitButton.on(
			'click',
			function( e ) {
				e.preventDefault();
				const hasModal = !! $( this ).data( 'content' );

				if ( hasModal ) {
					return;
				}

				const $form = $container.find( obj.selectors.form );

				obj.ticketsSubmit( $form );
			}
		);
	};

	/**
	 * Binds events for container.
	 *
	 * @since 5.0.3
	 *
	 * @param {jQuery} $container jQuery object of object of the tickets container.
	 *
	 * @return {void}
	 */
	obj.bindEvents = function( $container ) {
		$document.trigger( 'beforeSetup.tribeTicketsBlock', [ $container ] );

		// Disable the submit button.
		tribe.tickets.utils.disable( $container.find( obj.selectors.submit ), true );

		// Bind container based events.
		obj.bindTicketsAddRemove( $container );
		obj.bindTicketsQuantityInput( $container );
		obj.bindTicketsSubmit( $container );
		obj.bindDescriptionToggle( $container );

		$document.trigger( 'afterSetup.tribeTicketsBlock', [ $container ] );
	};

	/**
	 * Handles the initialization of the tickets block events when Document is ready.
	 *
	 * @since 5.0.3
	 *
	 * @return {void}
	 */
	obj.ready = function() {
		if ( 0 < TribeTicketOptions.availability_check_interval ) {
			obj.checkAvailability();
		}

		const $ticketsBlock = $document.find( obj.selectors.container );
		// Bind events for each tickets block.
		$ticketsBlock.each( function( index, block ) {
			obj.bindEvents( $( block ) );
		} );
	};

	window.addEventListener( 'pageshow', function( event ) {
		if (
			event.persisted ||
			(
				typeof window.performance != 'undefined' &&
				window.performance.navigation.type === 2
			)
		) {
			obj.ready();
		}
	} );

	// Configure on document ready.
	$( obj.ready );
} )( jQuery, tribe.tickets.block );

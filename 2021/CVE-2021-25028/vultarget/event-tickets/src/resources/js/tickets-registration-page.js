// For compatibility purposes we add this
if ( 'undefined' === typeof window.tribe ) {
	window.tribe = {};
}

if ( 'undefined' === typeof window.tribe.tickets ) {
	window.tribe.tickets = {};
}

window.tribe.tickets.registration = {};

( function( $, obj ) {
	/* Variables */

	obj.document = $( document );

	obj.hasChanges = {};

	obj.selector = {
		footerQuantity: '.tribe-tickets__footer__quantity__number',
		footerAmount: '.tribe-tickets__footer__total .tribe-amount',
		checkout: '.tribe-tickets__registration__checkout',
		checkoutButton: '.tribe-tickets__item__registration__submit',
		container: '.tribe-tickets__registration',
		eventContainer: '.tribe-tickets__registration__event',
		field: {
			text: '.tribe-tickets__item__attendee__field__text',
			checkbox: '.tribe-tickets__item__attendee__field__checkbox',
			select: '.tribe-tickets__item__attendee__field__select',
			radio: '.tribe-tickets__item__attendee__field__radio',
		},
		fields: '.tribe-tickets__item__attendee__fields',
		fieldsError: '.tribe-tickets__item__attendee__fields__error',
		fieldsErrorAjax: '.tribe-tickets__item__attendee__fields__error--ajax',
		fieldsErrorRequired: '.tribe-tickets__item__attendee__fields__error--required',
		fieldsSuccess: '.tribe-tickets__item__attendee__fields__success',
		form: '#tribe-tickets__registration__form',
		item: '.tribe-tickets__item',
		itemPrice: '.tribe-amount',
		itemQuantity: '.tribe-ticket-quantity',
		loader: '.tribe-common-c-loader',
		metaField: '.ticket-meta',
		metaItem: '.tribe-ticket',
		metaForm: '.tribe-tickets__registration__content',
		miniCart: '#tribe-tickets__mini-cart',
		status: '.tribe-tickets__registration__status',
		toggler: '.tribe-tickets__registration__toggle__handler',
		horizontal_datepicker: {
			container: '.tribe_horizontal_datepicker__container',
			select: '.tribe_horizontal_datepicker__container select',
			day: '.tribe_horizontal_datepicker__day',
			month: '.tribe_horizontal_datepicker__month',
			year: '.tribe_horizontal_datepicker__year',
			value: '.tribe_horizontal_datepicker__value',
		},
	};

	const $tribeRegistration = $( obj.selector.container );

	// Bail if there are no tickets on the current event/page/post
	if ( ! $( obj.selector.eventContainer ).length ) {
		return;
	}

	/*
	 * Commerce Provider Selectors.
	 *
	 * @since 4.11.0
	 *
	 */
	obj.commerceSelector = {
		edd: 'Tribe__Tickets_Plus__Commerce__EDD__Main',
		rsvp: 'Tribe__Tickets__RSVP',
		tpp: 'Tribe__Tickets__Commerce__PayPal__Main',
		Tribe__Tickets__Commerce__PayPal__Main: 'tribe-commerce',
		Tribe__Tickets__RSVP: 'rsvp',
		Tribe__Tickets_Plus__Commerce__EDD__Main: 'edd',
		Tribe__Tickets_Plus__Commerce__WooCommerce__Main: 'woo',
		tribe_eddticket: 'edd',
		tribe_tpp_attendees: 'tpp',
		tribe_wooticket: 'woo',
		woo: 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main',
	};

	// Get the current provider & ID.
	obj.provider = $tribeRegistration.data( 'provider' );
	obj.providerId = obj.commerceSelector[ obj.provider ];

	/* Data Formatting / API Handling */

	/**
	 * Get and format the meta to save.
	 *
	 * @since 4.11.0
	 *
	 * @return {object} Meta data object.
	 */
	obj.getMetaForSave = function() {
		const $metaForm     = $( obj.selector.metaForm );
		const $ticketRows = $metaForm.find( obj.selector.metaItem );
		const meta    = [];
		const tempMeta    = [];
		$ticketRows.each(
			function() {
				const data      = {};
				const $row      = $( this );
				const ticketId = $row.data( 'ticketId' );

				const $fields = $row.find( obj.selector.metaField );

				// Skip tickets with no meta fields
				if ( ! $fields.length ) {
					return;
				}

				if ( ! tempMeta[ ticketId ] ) {
					tempMeta[ ticketId ] = {};
					tempMeta[ ticketId ].ticket_Id = ticketId;
					tempMeta[ ticketId ].items = [];
				}

				$fields.each(
					function() {
						const $field  = $( this );
						let value   = $field.val();
						const isRadio = $field.is( ':radio' );
						let name    = $field.attr( 'name' );

						// Grab everything after the last bracket `[`.
						name = name.split( '[' );
						name = name.pop().replace( ']', '' );

						// Skip unchecked radio/checkboxes.
						if ( isRadio || $field.is( ':checkbox' ) ) {
							if ( ! $field.prop( 'checked' ) ) {
								// If empty radio field, if field already has a value, skip setting it as empty.
								if ( isRadio && '' !== data[ name ] ) {
									return;
								}

								value = '';
							}
						}

						data[ name ] = value;
					}
				);

				tempMeta[ ticketId ].items.push( data );
			}
		);

		Object.keys( tempMeta ).forEach( function( index ) {
			const newArr = {
				ticket_id: index,
				items: tempMeta[ index ].items,
			};
			meta.push( newArr );
		} );

		return meta;
	};

	/**
	 * Get ticket data to send to cart.
	 *
	 * @since 4.11.0
	 *
	 * @return {object} Tickets data object.
	 */
	obj.getTicketsForSave = function() {
		const tickets   = [];
		let $cartForm = $( obj.selector.miniCart );

		// Handle non-modal instances
		if ( ! $cartForm.length ) {
			$cartForm = $( obj.selector.container );
		}

		const $ticketRows = $cartForm.find( obj.selector.item );

		$ticketRows.each(
			function() {
				const $row        = $( this );
				const ticketId    = $row.data( 'ticketId' );
				const qty          = $row.find( obj.selector.itemQuantity ).text();

				const data          = {};
				data.ticket_id = ticketId;
				data.quantity = qty;

				tickets.push( data );
			}
		);

		return tickets;
	};

	/* Prefill Functions */

	/**
	 * Init the form prefills ( cart and AR forms ).
	 *
	 * @since 4.11.0
	 */
	obj.initFormPrefills = function() {
		$.ajax( {
			type: 'GET',
			data: {
				provider: obj.providerId,
				post_id: obj.postId,
			},
			dataType: 'json',
			url: obj.getRestEndpoint(),
			success: function( data ) {
				if ( data.tickets ) {
					obj.prefillCartForm( $( obj.selector.miniCart ), data.tickets );
				}

				if ( data.meta ) {
					obj.appendARFields( data );
					obj.prefillMetaForm( data );

					window.dispatchEvent( new Event( 'tribe_et_after_form_prefills' ) );
				}
			},
			complete: function() {
				obj.loaderHide();
			},
		} );
	};

	/**
	 * Appends AR fields on page load.
	 *
	 * @since 4.11.0
	 *
	 * @param {object} data The ticket meta we are using to add "blocks".
	 */
	obj.appendARFields = function( data ) {
		const tickets      = data.tickets;
		let nonMetaCount = 0;
		let metaCount    = 0;

		$.each( tickets, function( index, ticket ) {
			const ticketTemplate    = window.wp.template( 'tribe-registration--' + ticket.ticket_id );
			const $ticketContainer  = $tribeRegistration.find(
				'.tribe-tickets__item__attendee__fields__container[data-ticket-id="' + ticket.ticket_id + '"]' // eslint-disable-line max-len
			);
			const counter           = 1;

			if ( ! $ticketContainer.length ) {
				nonMetaCount += ticket.quantity;
			} else {
				metaCount += ticket.quantity;
			}

			$ticketContainer.addClass( 'tribe-tickets--has-tickets' );

			for ( let i = counter; i <= ticket.quantity; ++i ) {
				const datum = { attendee_id: i };
				try {
					$ticketContainer.append( ticketTemplate( datum ) );
				} catch ( error ) {
					// template doesn't exist - the ticket has no meta.
				}
			}
		} );

		obj.maybeShowNonMetaNotice( nonMetaCount, metaCount );
	};

	obj.maybeShowNonMetaNotice = function( nonMetaCount, metaCount ) {
		const $notice = $( '.tribe-tickets__notice--non-ar' );
		if ( 0 < nonMetaCount && 0 < metaCount ) {
			$( '#tribe-tickets__non-ar-count' ).text( nonMetaCount );
			$notice.removeClass( 'tribe-common-a11y-hidden' );
		} else {
			$notice.addClass( 'tribe-common-a11y-hidden' );
		}
	};

	/**
	 * Prefills the AR fields from supplied data.
	 *
	 * @since 4.11.0
	 *
	 * @param {object} data Data to fill the form in with.
	 * @param {number} len Starting pointer for partial fill-ins.
	 */
	obj.prefillMetaForm = function( data, len ) {
		let length = len;
		if ( undefined === data || 0 >= data.length ) {
			return;
		}

		if ( undefined === length ) {
			length = 0;
		}

		const $form = $tribeRegistration;
		const $containers = $form.find( '.tribe-tickets__item__attendee__fields__container' );
		let meta = data.meta;

		if ( 0 < length ) {
			meta = meta.splice( 0, length - 1 );
		}

		$.each( meta, function( metaIndex, ticket ) {
			const $currentContainers = $containers
				.filter( '[data-ticket-id="' + ticket.ticket_id + '"]' );

			if ( ! $currentContainers.length ) {
				return;
			}

			let current = 0;
			$.each( ticket.items, function( ticketIndex, datum ) {
				if ( 'object' !== typeof datum ) {
					return;
				}

				const $ticketContainers = $currentContainers.find( '.tribe-ticket' );
				$.each( datum, function( index, value ) {
					const $field = $ticketContainers.eq( current ).find( '[name*="' + index + '"]' );
					if ( ! $field.is( ':radio' ) && ! $field.is( ':checkbox' ) ) {
						$field.val( value );
					} else {
						$field.each( function() {
							const $item = $( this );
							if ( value === $item.val() ) {
								$item.prop( 'checked', true );
							}
						} );
					}
				} );

				current++;
			} );
		} );
	};

	/**
	 * Update all the footer info.
	 *
	 * @since 4.11.0
	 */
	obj.updateFooter = function() {
		obj.updateFooterCount();
		obj.updateFooterAmount();
	};

	/**
	 * Adjust the footer count for +/-.
	 *
	 * @since 4.11.0
	 */
	obj.updateFooterCount = function() {
		const $form       = $( obj.selector.miniCart );
		const $field      = $form.find( obj.selector.footerQuantity );
		let footerCount = 0;
		const $qtys       = $form.find( obj.selector.itemQuantity );

		$qtys.each( function() {
			let newQuantity = parseInt( $( this ).text(), 10 );
			newQuantity = isNaN( newQuantity ) ? 0 : newQuantity;
			footerCount += newQuantity;
		} );

		if ( 0 > footerCount ) {
			return;
		}

		$field.text( footerCount );
	};

	/**
	 * Adjust the footer total/amount for +/-.
	 *
	 * @since 4.11.0
	 */
	obj.updateFooterAmount = function() {
		const $form        = $( obj.selector.miniCart );
		const $field       = $form.find( obj.selector.footerAmount );
		let footerAmount = 0;
		const $qtys        = $form.find( obj.selector.itemQuantity );

		$qtys.each( function() {
			const $qty = $( this );
			const $price = $qty.closest( obj.selector.item ).find( obj.selector.itemPrice ).first( 0 );
			let quantity = parseInt( $qty.text(), 10 );
			quantity = isNaN( quantity ) ? 0 : quantity;
			const cost = obj.cleanNumber( $price.text() ) * quantity;
			footerAmount += cost;
		} );

		if ( 0 > footerAmount ) {
			return;
		}

		$field.text( obj.numberFormat( footerAmount ) );
	};

	/**
	 * Prefill the Mini-Cart.
	 *
	 * @since 4.11.0
	 *
	 * @param {object} $form The mini-cart form.
	 * @param {object} tickets THe ticket data.
	 */
	obj.prefillCartForm = function( $form, tickets ) {
		$.each( tickets, function( index, value ) {
			const $item = $form.find( '[data-ticket-id="' + value.ticket_id + '"]' );

			if ( $item ) {
				const pricePer = $item.find( '.tribe-tickets__sale_price .tribe-amount' ).text();
				$item.find( '.tribe-ticket-quantity' ).html( value.quantity );
				let price = value.quantity * obj.cleanNumber( pricePer );
				price = obj.numberFormat( price );
				$item.find( '.tribe-tickets__item__total .tribe-amount' ).html( price );
			}
		} );

		obj.updateFooter();
	};

	/* Validation */

	/**
	 * Validates the entire meta form.
	 * Adds errors to the top of the modal.
	 *
	 * @since 4.11.0
	 *
	 * @param {object} $form jQuery object that is the form we are validating.
	 *
	 * @return {boolean} If the form validates.
	 */
	obj.validateForm = function( $form ) {
		const $containers     = $form.find( obj.selector.metaItem );
		let formValid       = true;
		let invalidTickets  = 0;

		$containers.each(
			function() {
				const $container     = $( this );
				const validContainer = obj.validateBlock( $container );

				if ( ! validContainer ) {
					invalidTickets++;
					formValid = false;
				}
			}
		);

		return [ formValid, invalidTickets ];
	};

	/**
	 * Validates and adds/removes error classes from a ticket meta block.
	 *
	 * @since 4.11.0
	 *
	 * @param {object} $container jQuery object that is the block we are validating.
	 *
	 * @return {boolean} True if all fields validate, false otherwise.
	 */
	obj.validateBlock = function( $container ) {
		const $fields = $container.find( obj.selector.metaField );
		let validBlock = true;
		$fields.each(
			function() {
				const $field = $( this );
				const isValidfield = obj.validateField( $field[ 0 ] );

				if ( ! isValidfield ) {
					validBlock = false;
				}
			}
		);

		if ( validBlock ) {
			$container.removeClass( 'tribe-ticket-item__has-error' );
		} else {
			$container.addClass( 'tribe-ticket-item__has-error' );
		}

		return validBlock;
	};

	/**
	 * Validate Checkbox/Radio group.
	 * We operate under the assumption that you must check _at least_ one,
	 * but not necessarily all. Also that the checkboxes are all required.
	 *
	 * @since 4.11.0
	 *
	 * @param {object} $group The jQuery object for the checkbox group.
	 *
	 * @return {boolean} If the input group is valid.
	 */
	obj.validateCheckboxRadioGroup = function( $group ) {
		const $checkboxes   = $group.find( obj.selector.metaField );
		let checkboxValid = false;
		let required      = true;

		$checkboxes.each(
			function() {
				const $this = $( this );
				if ( $this.is( ':checked' ) ) {
					checkboxValid = true;
				}

				if ( ! $this.prop( 'required' ) ) {
					required = false;
				}
			}
		);

		const valid = ! required || checkboxValid;

		return valid;
	};

	/**
	 * Adds/removes error classes from a single field.
	 *
	 * @since 4.11.0
	 *
	 * @param {object} input DOM Object that is the field we are validating.
	 *
	 * @return {boolean} If the field is valid.
	 */
	obj.validateField = function( input ) {
		const $input       = $( input );
		let isValidfield = input.checkValidity();

		if ( ! isValidfield ) {
			// Got to be careful of required checkbox/radio groups...
			if ( $input.is( ':checkbox' ) || $input.is( ':radio' ) ) {
				const $group = $input.closest( '.tribe-common-form-control-checkbox-radio-group' );

				if ( $group.length ) {
					isValidfield = obj.validateCheckboxRadioGroup( $group );
				}
			} else {
				isValidfield = false;
			}
		}

		// Validation for Tribe Horizontal Date Picker
		if ( $input.hasClass( obj.selector.horizontal_datepicker.value.replace( /^\./, '' ) ) ) {
			const wrapper = $input.closest( obj.selector.horizontal_datepicker.container );
			const day = wrapper.find( obj.selector.horizontal_datepicker.day ); // eslint-disable-line es5/no-es6-methods,max-len
			const month = wrapper.find( obj.selector.horizontal_datepicker.month ); // eslint-disable-line es5/no-es6-methods,max-len
			const year = wrapper.find( obj.selector.horizontal_datepicker.year ); // eslint-disable-line es5/no-es6-methods,max-len

			[ day, month, year ].forEach( function( el ) {
				// Check if given value is a positive number, even if it's a string
				if ( isNaN( parseInt( el.val() ) ) || parseInt( el.val() ) <= 0 ) {
					el.addClass( 'ticket-meta__has-error' );

					isValidfield = false;
				} else {
					el.removeClass( 'ticket-meta__has-error' );
				}
			} );
		}

		if ( ! isValidfield ) {
			$input.addClass( 'ticket-meta__has-error' );
		} else {
			$input.removeClass( 'ticket-meta__has-error' );
		}

		return isValidfield;
	};

	/* DOM Manipulation */

	/**
	 * Adds focus effect to ticket block.
	 *
	 * @since 4.11.0
	 *
	 * @param {string} input The triggering input selector string.
	 */
	obj.focusTicketBlock = function( input ) {
		$( input ).closest( obj.selector.metaItem ).addClass( 'tribe-ticket-item__has-focus' );
	};

	/**
	 * Remove focus effect from ticket block.
	 *
	 * @since 4.11.0
	 *
	 * @param {string} input The triggering input selector string.
	 */
	obj.unfocusTicketBlock = function( input ) {
		$( input ).closest( obj.selector.metaItem ).removeClass( 'tribe-ticket-item__has-focus' );
	};

	/**
	 * Show the loader/spinner.
	 *
	 * @since 4.11.0
	 */
	obj.loaderShow = function() {
		$( obj.selector.loader ).removeClass( 'tribe-common-a11y-hidden' );
	};

	/**
	 * Hide the loader/spinner.
	 *
	 * @since 4.11.0
	 */
	obj.loaderHide = function() {
		$( obj.selector.loader ).addClass( 'tribe-common-a11y-hidden' );
	};

	/* Utility */

	/**
	 * Get the REST endpoint
	 *
	 * @since 4.11.0
	 *
	 * @returns {string} The endpoint URL.
	 */
	obj.getRestEndpoint = function() {
		const url = TribeCartEndpoint.url;
		return url;
	};

	/**
	 * Get the Currency Formatting for a Provider.
	 *
	 * @since 4.11.0
	 *
	 * @returns {string} The currency format.
	 */
	obj.getCurrencyFormatting = function() {
		const currency = JSON.parse( TribeCurrency.formatting );
		const format   = currency[ obj.commerceSelector[ obj.providerId ] ];
		return format;
	};

	/**
	 * Removes separator characters and converts decimal character to '.'
	 * So they play nice with other functions.
	 *
	 * @since 4.11.0
	 *
	 * @param {string|number} num The number to clean.
	 * @returns {string} The cleaned number.
	 */
	obj.cleanNumber = function( num ) {
		let number   = num;
		const format = obj.getCurrencyFormatting();
		// we run into issue when the two symbols are the same -
		// which appears to happen by default with some providers.
		const same = format.thousands_sep === format.decimal_point;

		if ( ! same ) {
			number = number.split( format.thousands_sep ).join( '' );
			number = number.split( format.decimal_point ).join( '.' );
		} else {
			const decPlace = number.length - ( format.number_of_decimals + 1 );
			number = number.substr( 0, decPlace ) + '_' + number.substr( decPlace + 1 );
			number = number.split( format.thousands_sep ).join( '' );
			number = number.split( '_' ).join( '.' );
		}

		return number;
	};

	/**
	 * Format the number according to provider settings.
	 * Based off coding fron https://stackoverflow.com/a/2901136.
	 *
	 * @since 4.11.0
	 *
	 * @param {string|number} number The number to format.
	 *
	 * @returns {string} The formatted number.
	 */
	obj.numberFormat = function( number ) {
		const format = obj.getCurrencyFormatting();

		if ( ! format ) {
			return false;
		}

		const decimals      = format.number_of_decimals;
		const decPoint     = format.decimal_point;
		const thousandsSep = format.thousands_sep;
		const n             = ! isFinite( +number ) ? 0 : +number;
		const prec          = ! isFinite( +decimals ) ? 0 : Math.abs( decimals );
		const sep           = ( 'undefined' === typeof thousandsSep ) ? ',' : thousandsSep;
		const dec           = ( 'undefined' === typeof decPoint ) ? '.' : decPoint;
		const toFixedFix    = function( num, precision ) {
			// Fix for IE parseFloat(0.55).toFixed(0) = 0;
			const k = Math.pow( 10, precision );

			return Math.round( num * k ) / k;
		};

		const s = ( prec ? toFixedFix( n, prec ) : Math.round( n ) ).toString().split( dec );

		if ( s[ 0 ].length > 3 ) {
			s[ 0 ] = s[ 0 ].replace( /\B(?=(?:\d{3} )+(?!\d))/g, sep );
		}

		if ( ( s[ 1 ] || '' ).length < prec ) {
			s[ 1 ] = s[ 1 ] || '';
			s[ 1 ] += new Array( prec - s[ 1 ].length + 1 ).join( '0' );
		}

		return s.join( dec );
	};

	/* Event Handlers */

	/**
	 * Adds focus effect to ticket block.
	 *
	 * @since 4.11.0
	 *
	 */
	obj.document.on(
		'focus',
		'.tribe-ticket .ticket-meta',
		function( e ) {
			const input      = e.target;
			obj.focusTicketBlock( input );
		}
	);

	/**
	 * handles input blur.
	 *
	 * @since 4.11.0
	 *
	 */
	obj.document.on(
		'blur',
		'.tribe-ticket .ticket-meta',
		function( e ) {
			const input      = e.target;
			obj.unfocusTicketBlock( input );
		}
	);

	/**
	 * Handle AR submission.
	 *
	 * @since 4.11.0
	 */
	obj.document.on(
		'click',
		obj.selector.checkoutButton,
		function( e ) {
			e.preventDefault();
			const $metaForm    = $( obj.selector.metaForm );
			const $errorNotice = $( '.tribe-tickets__notice--error' );
			const isValidForm  = obj.validateForm( $metaForm );

			if ( ! isValidForm[ 0 ] ) {
				$( [ document.documentElement, document.body ] ).animate(
					{ scrollTop: $( '.tribe-tickets__registration' ).offset().top },
					'slow'
				);

				$( '.tribe-tickets__notice--error__count' ).text( isValidForm[ 1 ] );
				$errorNotice.show();

				return false;
			}

			$errorNotice.hide();

			obj.loaderShow();

			// save meta and cart
			const params = {
				tribe_tickets_provider: obj.commerceSelector[ obj.tribe_ticket_provider ],
				tribe_tickets_tickets: obj.getTicketsForSave(),
				tribe_tickets_meta: obj.getMetaForSave(),
				tribe_tickets_post_id: obj.postId,
			};

			$( '#tribe_tickets_ar_data' ).val( JSON.stringify( params ) );

			// Submit the form.
			$( obj.selector.form ).submit();
		}
	);

	/**
	 * Init the tickets registration script
	 *
	 * @since 4.9
	 */
	obj.init = function() {
		obj.loaderShow();
		obj.initFormPrefills();
	};

	$( obj.init );
} )( jQuery, window.tribe.tickets.registration );

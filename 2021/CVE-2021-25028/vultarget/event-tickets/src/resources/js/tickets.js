/* global tribe_event_tickets_plus, tribe, jQuery, _, tribe_l10n_datatables,
 tribe_ticket_datepicker_format, TribeTickets, tribe_timepickers  */

// For compatibility purposes we add this
if ( 'undefined' === typeof tribe.tickets ) {
	tribe.tickets = {};
}

if ( 'undefined' === typeof ajaxurl ) {
	ajaxurl = TribeTickets.ajaxurl;
}

tribe.tickets.editor = {};

var ticketHeaderImage = window.ticketHeaderImage || {};

(function( window, $, _, obj ) {
	'use strict';

	// base elements
	var $body = $( 'html, body' );
	var $document = $( document );
	var $tribe_tickets = $( document.getElementById( 'tribetickets' ) );

	// Bail if we don't have what we need
	if ( 0 === $tribe_tickets.length ) {
		return;
	}

	/**
	 * Replacement for jQuery $.isNumeric that was deprecated on version 5.7 of WP.
	 *
	 * @param {string|int} number
	 *
	 * @return {boolean} If the passed variable is numeric.
	 */
	const isNumeric = function( number ) {
		return ! isNaN( parseFloat( number ) ) && isFinite( number );
	};

	var $tickets_container = $( document.getElementById( 'event_tickets' ) );
	var $post_id = $( document.getElementById( 'post_ID' ) );
	var $publish = $( document.getElementById( 'publish' ) );
	var $metaboxBlocker = $tribe_tickets.find( '.tribe-tickets-editor-blocker' );
	var $spinner = $tribe_tickets.find( '.spinner' );

	// panels
	var $base_panel = $( document.getElementById( 'tribe_panel_base' ) );
	var $edit_panel = $( document.getElementById( 'tribe_panel_edit' ) );
	var $settings_panel = $( document.getElementById( 'tribe_panel_settings' ) );

	// Datepicker and Timepicker variables
	var datepickerFormats = [
		'yy-mm-dd',
		'm/d/yy',
		'mm/dd/yy',
		'd/m/yy',
		'dd/mm/yy',
		'm-d-yy',
		'mm-dd-yy',
		'd-m-yy',
		'dd-mm-yy',
		'yy.mm.dd',
		'mm.dd.yy',
		'dd.mm.yy',
	];
	var dateFormat = datepickerFormats[0];

	var changeEventCapacity = function( event, eventCapacity ) {
		if ( 'undefined' === typeof eventCapacity ) {
			var $element = $( this );
			eventCapacity = $element.val();
		}

		if ( undefined === eventCapacity ) {
			return;
		}

		// Make sure we don't have NaN
		if ( ! eventCapacity ) {
			eventCapacity = 0;
		}

		eventCapacity = parseInt( eventCapacity, 10 );
		var $maxCapacity = $( '.tribe-ticket-capacity-max' );
		var $capacityValue = $maxCapacity.find( '.tribe-ticket-capacity-value' );
		var $capacity = $( '.tribe-ticket-field-capacity[name="tribe-ticket[capacity]"]' );

		// may as well set this here just in case
		$capacity.attr( 'placeholder', eventCapacity );

		if ( ! eventCapacity ) {
			eventCapacity = 0;
		} else {
			$capacity.attr( 'max', eventCapacity );
		}

		$capacityValue.text( eventCapacity );
	};

	/**
	 * Sets the ticket edit form provider to the currently selected default ticketing provider.
	 * Defaults to RSVP if something fails
	 *
	 * @since 4.6
	 *
	 * @param boolean force selection to RSVP
	 * @return void
	 */
	function set_default_provider_radio( force_rsvp ) {
		if ( 'undefined' === typeof force_rsvp ) {
			force_rsvp = true;
		}
		var $checkedProvider = $tribe_tickets.find( '.tribe-ticket-editor-field-default_provider' );

		if ( $checkedProvider.is( ':radio' ) ) {
			$checkedProvider = $checkedProvider.filter( ':checked' );
		}

		var provider_id;

		if ( force_rsvp ) {
			provider_id = 'Tribe__Tickets__RSVP_radio';
		} else {
			// Allows default to WooCommerce
			provider_id = 'Tribe__Tickets_Plus__Commerce__WooCommerce__Main_radio';
		}

		if ( ! force_rsvp && $checkedProvider.length > 0 ) {
			provider_id = $checkedProvider.val() + '_radio';
		}

		$( document.getElementById( provider_id ) ).prop( 'checked', true ).trigger( 'change' );
	}

	/**
	 * If the user attempts to nav away without saving global stock setting
	 * changes then try to bring this to their attention!
	 */
	obj.beforeUnload = function( event ) {
		var returnValue = false;

		// If we are not on the base panel we alert the user about leaving
		// NOTE: This custom message will only work for Chrome < 51, Opera < 38, Firefox < 44, and Safari < 9.1
		if ( 'true' === $base_panel.attr( 'aria-hidden' ) ) {
			returnValue = tribe_global_stock_admin_ui.nav_away_msg;
		}

		event.returnValue = returnValue;

		// We can't trigger a confirm() dialog from within this action but returning
		// a string should achieve effectively the same result
		return returnValue;
	};

	ticketHeaderImage = {
		// Call this from the upload button to initiate the upload frame.
		uploader: function() {
			var frame = wp.media( {
				title: HeaderImageData.title,
				multiple: false,
				library: { type: 'image' },
				button: { text: HeaderImageData.button },
			} );

			// Handle results from media manager.
			frame.on( 'close', function() {
				var attachments = frame.state().get( 'selection' ).toJSON();
				if ( attachments.length ) {
					ticketHeaderImage.render( attachments[0] );
				}
			} );

			frame.open();
			return false;
		},
		// Output Image preview and populate widget form.
		render: function( attachment ) {
			$( document.getElementById( 'tribe_ticket_header_preview' ) )
				.html( ticketHeaderImage.imgHTML( attachment ) );
			$( document.getElementById( 'tribe_ticket_header_image_id' ) ).val( attachment.id );
			$( document.getElementById( 'tribe_ticket_header_remove' ) ).show();
			$( document.getElementById( 'tribe_tickets_image_preview_filename' ) )
				.show().find( '.filename' ).text( attachment.filename );
		},
		// Render html for the image.
		imgHTML: function( attachment ) {
			var img_html = '<img src="' + attachment.url + '" ';
			img_html += 'width="' + attachment.width + '" ';
			img_html += 'height="' + attachment.height + '" ';
			img_html += '/>';
			return img_html;
		},
	};

	obj.panels = {
		list: '#tribe_panel_base',
		ticket: '#tribe_panel_edit',
		settings: '#tribe_panel_settings',
	};

	/**
	 * Switch from one panel to another
	 * @param  event  e      triggering event
	 * @param  object ($base_panel) $panel jQuery object containing the panel we want to switch to
	 * @return void
	 */
	obj.swapPanel = function( panel ) {
		var $panel;

		if ( panel instanceof jQuery ) {
			$panel = panel;
		} else if ( 'undefined' !== typeof obj.panels[ panel ] ) {
			$panel = $( obj.panels[ panel ] );
		} else {
			$panel = $base_panel;
		}

		var $eventTickets = $( '#event_tickets' );

		// trigger an event before swapping the panel
		$eventTickets.trigger( 'before_panel_swap.tickets', { panel: $panel } );

		// First, hide them all!
		$tribe_tickets.find( '.ticket_panel' ).each( function() {
			$( this ).attr( 'aria-hidden', 'true' );
		} );

		// then show the one we want
		$panel.attr( 'aria-hidden', 'false' );

		if ( ! $panel.is( $base_panel ) ) {
			$( window ).on( 'beforeunload.tribe', obj.beforeUnload );
		} else {
			$( window ).off( 'beforeunload.tribe' );

			//trigger dependencies for messages on load of base panel
			$document.trigger( 'tribe.dependencies-run' );
		}

		// trigger an event after swapping the panel
		$eventTickets.trigger( 'after_panel_swap.tickets', { panel: $panel } );
	};

	obj.fetchPanels = function( data, swapTo ) {
		if ( 'undefined' === typeof data ) {
			data = [];
		}

		var params = {
			action: 'tribe-ticket-panels',
			notice: false,
			post_id: $post_id.val(),
			nonce: TribeTickets.add_ticket_nonce,
			data: data,
			is_admin: $( 'body' ).hasClass( 'wp-admin' )
		};

		$.post(
			ajaxurl,
			params,
			function( response ) {
				if ( ! response.success ) {
					return;
				}

				obj.refreshPanels( response.data, swapTo );
			},
			'json'
		);
	};

	obj.startWatchingMoveLinkIn = function() {
		$tickets_container.find( '.tribe-ticket-move-link' ).one( 'click', function() {
			// give ThickBox some time to load, in ms
			window.setTimeout( obj.listentToThickboxEvents, 250 );
		} );
	};

	obj.listentToThickboxEvents = function() {
		/**
		 * ThickBox id from its source code.
		 *
		 * @see /wp-includes/js/thickbox/thickbox.js
		 */
		var $tbWindow = $( '#TB_window' );

		if ( $tbWindow.length === 0 ) {
			return;
		}

		// refetch the panels when the ThickBox closes and swap to the ticket list
		$tbWindow.one( 'tb_unload', function() {
			obj.fetchPanels( null, 'list' );
		} );
	};

	obj.refreshPanels = function( panels, swapTo ) {
		// After this point is safe to assume we have a valid set of panels
		$base_panel = $( panels.list );
		$edit_panel = $( panels.ticket );
		$settings_panel = $( panels.settings );

		// Rplace the old ones
		$tribe_tickets.find( obj.panels.list ).replaceWith( $base_panel );
		$tribe_tickets.find( obj.panels.ticket ).replaceWith( $edit_panel );
		$tribe_tickets.find( obj.panels.settings ).replaceWith( $settings_panel );

		// Makes sure the Panels are Ready for interaction
		obj.setupPanels();

		// At the end always swap panels (deafults to base/list)
		obj.swapPanel( swapTo );

		// Trigger dependency.
		$( '.tribe-dependency' ).trigger( 'verify.dependency' );
	};

	obj.setupPanels = function() {
		window.MTAccordion( {
			target: '.accordion', // ID (or class) of accordion container
		} );

		// date elements
		var $event_pickers = $( document.getElementById( 'tribe-event-datepickers' ) );
		var $ticket_start_date = $( document.getElementById( 'ticket_start_date' ) );
		var $ticket_end_date = $( document.getElementById( 'ticket_end_date' ) );
		var $ticket_start_time = $( document.getElementById( 'ticket_start_time' ) );
		var $ticket_end_time = $( document.getElementById( 'ticket_end_time' ) );
		var startofweek = 0;

		/**
		 * There might be cases when Tickets is used in isolation where TEC is not installed
		 * for those cases tribe_datepicker_opts is undefined as is a variable defined by TEC. One of
		 * the most important part of this variable is the dateFormat value, in this case we created
		 * a new global variable so any other element that dependes on it has access to this value
		 */
		if ( typeof tribe_datepicker_opts === 'undefined' ) {
			var $dateFormat = $( '[data-datepicker_format]' );
			var formatAttr = $dateFormat.length ? $dateFormat.attr( 'data-datepicker_format' ) : '';
			var format = parseInt( formatAttr, 10 );
			if ( ! isNaN( format ) ) {
				window.tribe_datepicker_opts = {
					dateFormat: datepickerFormats[ format ],
				};
			}
		}

		var datepicker_opts = window['tribe_datepicker_opts'] || {};

		if ( $event_pickers.length ) {
			startofweek = $event_pickers.data( 'startofweek' );
		}

		if ( 'undefined' !== typeof tribe_ticket_datepicker_format ) {
			var indexDatepickerFormat = isNumeric( tribe_ticket_datepicker_format.datepicker_format_index ) ? tribe_ticket_datepicker_format.datepicker_format_index : 0;
			dateFormat = datepickerFormats[ indexDatepickerFormat ];
		} else if ( datepicker_opts && datepicker_opts.dateFormat ) {
			// if datepicker_opts exists and has a valid dateFormat use it if tribe_ticket_datepicker_format is not defined
			dateFormat = datepicker_opts.dateFormat;
		}

		var datepickerOpts = {
			dateFormat: dateFormat,
			showAnim: 'fadeIn',
			changeMonth: true,
			changeYear: true,
			numberOfMonths: 3,
			showButtonPanel: false,
			onChange: function() {
			},
			beforeShow: function( element, object ) {
				object.input.data( 'prevDate', object.input.datepicker( 'getDate' ) );

				// Capture the datepicker div here; it's dynamically generated so best to grab here instead of elsewhere.
				var $dpDiv = $( object.dpDiv );

				// "Namespace" our CSS a bit so that our custom jquery-ui-datepicker styles don't interfere with other plugins'/themes'.
				$dpDiv.addClass( 'tribe-ui-datepicker' );

				// @todo Look into making this also compatible with ACF
				// $event_details.trigger( 'tribe.ui-datepicker-div-beforeshow', [ object ] );

				$dpDiv.attrchange( {
					trackValues: true,
					callback: function( attr ) {
						// This is a non-ideal, but very reliable way to look for the closing of the ui-datepicker box,
						// since onClose method is often included by other plugins, including Events Calender PRO.
						if (
							attr.newValue.indexOf( 'display: none' ) >= 0 ||
							attr.newValue.indexOf( 'display:none' ) >= 0
						) {
							$dpDiv.removeClass( 'tribe-ui-datepicker' );

							// @todo Look into making this also compatible with ACF
							// $event_details.trigger( 'tribe.ui-datepicker-div-closed', [ object ] );
						}
					}
				} );
			},
			onSelect: function( dateText, inst ) {
				var the_date = $.datepicker.parseDate( dateFormat, dateText );

				if ( inst.id === 'ticket_start_date' ) {
					$ticket_end_date.datepicker( 'option', 'minDate', the_date );
				} else {
					$ticket_start_date.datepicker( 'option', 'maxDate', the_date );
				}
			}
		};

		$.extend( datepickerOpts, tribe_l10n_datatables.datepicker );

		var $timepickers = $tribe_tickets.find( '.tribe-timepicker:not(.ui-timepicker-input)' );
		tribe_timepickers.setup_timepickers( $timepickers );

		$ticket_start_date
			.datepicker( datepickerOpts )
			.datepicker( 'option', 'defaultDate', $( document.getElementById( 'EventStartDate' ) ).val() )
			.on( 'keyup', function( e ) {
				if ( e.keyCode === 8 || e.keyCode === 46 ) {
					$.datepicker._clearDate( this );
				}
			} );

		$ticket_end_date
			.datepicker( datepickerOpts )
			.datepicker( 'option', 'defaultDate', $( document.getElementById( 'EventEndDate' ) ).val() )
			.on( 'keyup', function( e ) {
				if ( e.keyCode === 8 || e.keyCode === 46 ) {
					$.datepicker._clearDate( this );
				}
			} );

		if ( $( document.getElementById( 'tribe_ticket_header_preview' ) ).find( 'img' ).length ) {
			$( document.getElementById( 'tribe_ticket_header_remove' ) ).show();

			var $tiximg = $( document.getElementById( 'tribe_ticket_header_preview' ) ).find( 'img' );
			$tiximg.removeAttr( 'width' ).removeAttr( 'height' );

			if ( $tribe_tickets.width() < $tiximg.width() ) {
				$tiximg.css( 'width', '95%' );
			}
		}

		// When we have Meta fields for Attendees
		if (
			'undefined' !== typeof tribe_event_tickets_plus
			&& $.isPlainObject( tribe_event_tickets_plus )
			&& $.isPlainObject( tribe_event_tickets_plus.meta )
			&& $.isPlainObject( tribe_event_tickets_plus.meta.admin )
			&& 'function' === typeof tribe_event_tickets_plus.meta.admin.init_ticket_fields
		) {
			tribe_event_tickets_plus.meta.admin.init_ticket_fields();
		}

		// Setup Drag and Drop
		if (
			tribe.tickets.table
			&& 0 !== $base_panel.find( '.tribe-tickets-editor-table-tickets-body tr' ).length
		) {
			tribe.tickets.table.toggle_sortable();
		}

		$tribe_tickets.find( tribe.validation.selectors.item ).validation();

		// Make sure we display the correct Fields and things
		$tribe_tickets.find( '.tribe-dependent' ).dependency();
		$tribe_tickets.find( '.tribe-dependency' ).trigger( 'verify.dependency' );
	};

	$document.ajaxSend( function( event, jqxhr, settings ) {
		if ( 'string' !== $.type( settings.data ) ) {
			return;
		}

		if ( - 1 === settings.data.indexOf( 'action=tribe-ticket' ) ) {
			return;
		}

		$tribe_tickets.trigger( 'spin.tribe', 'start' );
	} );

	$document.ajaxComplete( function( event, jqxhr, settings ) {
		if ( 'string' !== $.type( settings.data ) ) {
			return;
		}

		if ( - 1 === settings.data.indexOf( 'action=tribe-ticket' ) ) {
			return;
		}

		$tribe_tickets.trigger( 'spin.tribe', 'stop' );
	} );

	/* Add some trigger actions */
	$document.on( {
		/**
		 * Makes a Visual Spinning thingy appear on the Tickets metabox.
		 * Also prevents user Action on the metabox elements.
		 *
		 * @param  {jQuery.event} event  The jQuery event
		 * @param  {string} action You can use `start` or `stop`
		 * @return {void}
		 */
		'spin.tribe': function( event, action ) {
			if ( 'undefined' === typeof action || $.inArray( action, ['start', 'stop'] ) ) {
				action = 'stop';
			}

			if ( 'stop' === action ) {
				$metaboxBlocker.hide();
				$spinner.removeClass( 'is-active' );
			} else {
				$metaboxBlocker.show();
				$spinner.addClass( 'is-active' );
			}
		},
	} );

	/**
	 * When Hitting the Publish button we remove our beforeunload
	 */
	$publish.on( 'click', function( event ) {
		$( window ).off( 'beforeunload.tribe' );
	} );

	/* "Settings" button action */
	$document.on( 'click', '#settings_form_toggle', function( event ) {
		// Prevent Form Submit on button click
		event.preventDefault();

		// Fetches as fresh set of panels
		obj.fetchPanels( null, 'settings' );

		// Make it safe that it wont submit
		return false;
	} );

	/**
	 * Cancel buttons, which refresh and swap to list
	 */
	$document.on( 'click', '#tribe_settings_form_cancel, #ticket_form_cancel', function( event ) {
		// Prevent Form Submit on button click
		event.preventDefault();

		// Fetches as fresh set of panels
		obj.fetchPanels( null, 'list' );

		// Make it safe that it wont submit
		return false;
	} );

	/* "Save Settings" button action */
	$document.on( 'click', '#tribe_settings_form_save', function( event ) {
		// Prevent Form Submit on button click
		event.preventDefault();

		// Fetches form data from this panel
		var formData = $settings_panel.find( 'input,textarea' ).serialize();

		// Save and Refresh the Panels
		obj.fetchPanels( formData, 'list' );

		// Make it safe that it wont submit
		return false;
	} );

	/* "Add ticket" button action */
	$document.on( 'click', '.ticket_form_toggle', function( event ) {

		// Prevent Form Submit on button click
		event.preventDefault();

		// Where we clicked
		var $button = $( this );

		set_default_provider_radio( 'rsvp_form_toggle' === $button.attr( 'id' ) );

		// Triggers Dependency
		$edit_panel.find( '.tribe-dependency' ).trigger( 'verify.dependency' );

		obj.swapPanel( 'ticket' );

		// Make it safe that it wont submit
		return false;
	} );

	/* "Edit Ticket" link action */
	$document.on( 'click', '.ticket_edit_button', function( event ) {
		// Prevent Form Submit on button click
		event.preventDefault();

		// Where we clicked
		var $button = $( this );

		// Prep the Params for the Request
		var params = {
			action: 'tribe-ticket-edit',
			post_id: $post_id.val(),
			ticket_id: $button.data( 'ticketId' ),
			nonce: TribeTickets.edit_ticket_nonce,
			is_admin: $( 'body' ).hasClass( 'wp-admin' )
		};

		$.post(
			ajaxurl,
			params,
			function( response ) {
				if ( ! response.success ) {
					return;
				}

				obj.refreshPanels( response.data, 'ticket' );
				obj.startWatchingMoveLinkIn( '#event_tickets' )

				$tribe_tickets.trigger( 'edit-ticket.tribe', event );
			},
			'json'
		);

		// Make it safe that it wont submit
		return false;
	} );

	/* "Save Ticket" button action */
	$document.on( 'click.tribe', '[name="ticket_form_save"]', function( e ) {
		var $form = $( document.getElementById( 'ticket_form_table' ) );

		// Makes sure we have validation
		$form.trigger( 'validation.tribe' );

		// Prevent anything from happening when there are errors
		if ( tribe.validation.hasErrors( $form ) ) {
			return;
		}

		$tribe_tickets.trigger( 'pre-save-ticket.tribe', e );

		var $orders = $base_panel.find( '.tribe-ticket-field-order' );
		var params = {
			action: 'tribe-ticket-add',
			data: $edit_panel.find( 'input,textarea,select' ).serialize().replace( /\'/g, '%27' ).replace( /\:/g, '%3A' ),
			post_id: $post_id.val(),
			nonce: TribeTickets.add_ticket_nonce,
			menu_order: $orders.length,
			is_admin: $( 'body' ).hasClass( 'wp-admin' )
		};

		$.post(
			ajaxurl,
			params,
			function( response ) {
				if ( ! response.success ) {
					return;
				}

				obj.refreshPanels( response.data );
			},
			'json'
		);
	} );

	/* "Delete Ticket" link action */
	$document.on( 'click', '.ticket_delete', function( event ) {
		if ( ! confirm( tribe_ticket_notices.confirm_alert ) ) {
			return false;
		}

		event.preventDefault();

		$tribe_tickets.trigger( 'delete-ticket.tribe', event );

		var deleted_ticket_id = $( this ).attr( 'attr-ticket-id' );

		var params = {
			action: 'tribe-ticket-delete',
			post_id: $post_id.val(),
			ticket_id: deleted_ticket_id,
			nonce: TribeTickets.remove_ticket_nonce,
			is_admin: $( 'body' ).hasClass( 'wp-admin' )
		};

		$.post(
			ajaxurl,
			params,
			function( response ) {
				if ( ! response.success ) {
					return;
				}

				obj.refreshPanels( response.data );
			},
			'json'
		);
	} );

	/* Change global stock type if we've put a value in global_stock_cap */
	$document.on( 'change', '.tribe-ticket-field-capacity', function( e ) {
		var $this = $( this );
		var $globalField = $this.parents( '.input_block' ).eq( 0 ).find( '.tribe-ticket-field-mode' );

		// Bail if we have any value on Stock Cap
		if ( ! $this.val() ) {
			return;
		}

		$globalField.val( 'capped' );
	} );

	$document.on( 'keyup', '#ticket_price', function( e ) {
		e.preventDefault();

		var decimal_point = price_format.decimal;
		var regex = new RegExp( '[^\-0-9\%\\' + decimal_point + ']+', 'gi' );
		var value = $( this ).val();
		var newvalue = value.replace( regex, '' );

		// @todo add info message or tooltip to let people know we are removing the comma or period
		if ( value !== newvalue ) {
			$( this ).val( newvalue );
		}
	} );

	$document.on( 'click', '#tribe_ticket_header_image, #tribe_ticket_header_preview', function( e ) {
		e.preventDefault();
		ticketHeaderImage.uploader( '', '' );
	} );

	$document.on( 'focus', '#settings_global_capacity_edit', function() {
		var $capacity = $( this );
		var nonSharedCapacity = 0;
		var $capacities = $( '.tribe-tickets-editor-capacity-table' ).find( '[data-capacity]' );

		$capacities.each( function() {
			var $item = $( this );
			nonSharedCapacity = nonSharedCapacity + parseInt( $item.data( 'capacity' ), 10 );
		} );

		$capacity.data( 'nonSharedCapacity', nonSharedCapacity );
	} );

	/* Handle saving changes to capacity from Settings form */
	$document.on( 'blur change', '#settings_global_capacity_edit', function() {
		var $totalRow = $( '.tribe-tickets-editor-table-row-capacity-total' );
		var totalCapacity = parseInt( $totalRow.data( 'totalCapacity' ), 10 );

		// We just bail if we are dealing with any unlimited
		if ( - 1 === totalCapacity ) {
			return;
		}

		var $capacity = $( this );
		var $total = $totalRow.find( '.tribe-tickets-editor-total-capacity' );
		var capacity = parseInt( $capacity.val(), 10 );
		var nonSharedCapacity = $capacity.data( 'nonSharedCapacity' );

		// Prevent Fails with empty stuff
		if ( '' === capacity || 0 > capacity || _.isNaN( capacity ) ) {
			capacity = 0;
		}

		var total = nonSharedCapacity + capacity;

		$total.text( total );
	} );

	/* Handle editing global capacity from the settings panel */
	$document.on( 'click', '#global_capacity_edit_button', function( e ) {
		e.preventDefault();
		$( document.getElementById( 'settings_global_capacity_edit' ) ).prop( 'disabled', false ).focus();
	} );

	/* Track changes to the global stock level on the ticket edit form. */
	$document.on( 'blur', '[name="tribe-ticket[event_capacity]"]', changeEventCapacity );

	/**
	 * Track changes to Capacity to avoid going over the max
	 */
	$document.on( 'change', '[name="tribe-ticket[capacity]"]', function( event ) {
		var $field = $( this );
		var max = parseInt( $field.attr( 'max' ), 10 );
		var value = parseInt( $field.val(), 10 );

		if ( max && max < value ) {
			$field.val( max );
		}
	} );

	/* Remove header image action */
	$document.on( 'click', '#tribe_ticket_header_remove', function( e ) {
		e.preventDefault();
		$( document.getElementById( 'tribe_ticket_header_preview' ) ).html( '' );
		$( document.getElementById( 'tribe_ticket_header_remove' ) ).hide();
		$( document.getElementById( 'tribe_tickets_image_preview_filename' ) ).hide().find( '.filename' ).text( '' );
		$( document.getElementById( 'tribe_ticket_header_image_id' ) ).val( '' );
	} );

	$( obj.setupPanels );
} )( window, jQuery, _, tribe.tickets.editor );

/* global jQuery, AttendeesPointer, Attendees */
var tribe_event_tickets_attendees = tribe_event_tickets_attendees || {};

( function( $, obj ) {

	function init() {
		if ( typeof AttendeesPointer !== 'undefined' && AttendeesPointer.length ) {
			options = $.extend( AttendeesPointer.options, {
				close: function() {
					$.post( Attendees.ajaxurl, {
						pointer: AttendeesPointer.pointer_id,
						action : 'dismiss-wp-pointer',
					} );
				},
				open: function( event, widget ) {
					widget.pointer
						.css({
							top: parseInt( widget.pointer.css( 'top' ).replace( 'px', '' ), 10 ) + 5
						})
						.find( '.wp-pointer-arrow' ).css( {
							right: '50px',
							left: 'auto',
						} );

					widget.element.on( {
						click: function() {
							widget.element.pointer( 'close' );
						}
					} );
				}
			} );

			var $pointer = $( AttendeesPointer.target ).pointer( options ).pointer( 'open' ).pointer( 'widget' );
		}

		$( 'input.print' ).on( 'click', function() {
			$( window ).trigger( 'attendees-report-before-print.tribe-tickets' );

			var $table = $( 'table.wp-list-table.attendees' ),
				$visible_columns = $table.find( 'thead th:visible' ).length,
				$header_and_data = $table.find( 'th,td' ),
				hidden_in_print = 3;

			// make the visible columns stretch to fill the available width
			$header_and_data.css( {'width': 100 / ($visible_columns - hidden_in_print) + '%'} );

			window.print();

			// reset the columns width
			$header_and_data.css( {'width': ''} );

			$( window ).trigger( 'attendees-report-after-print.tribe-tickets' );
		} );

		$( '.tribe-attendees-email' ).on({
			'submit': function( event ) {
				$( '.tribe-attendees-email' ).hide();
				$( document.getElementById( 'tribe-loading' ) ).show();
			}
		});

		$( 'span.trash a' ).on( 'click', function ( e ) {
			return confirm( Attendees.confirmation );
		});

		$( '.event-tickets__attendees-admin-form' ).on( 'submit', function ( e ) {

			// If not the delete action, return.
			if ( 'delete_attendee' !== $( '#bulk-action-selector-top' ).val() ) {
				return;
			}

			// If no attendee was selected, bail out.
			if ( ! $( this ).serialize().includes( '&attendee' ) )  {
				return;
			}

			return confirm( Attendees.bulk_confirmation );
		} );

		$( '.tickets_checkin' ).on( 'click', function( e ) {

			var obj = jQuery( this );
			obj.prop( 'disabled', true );

			var params = {
				action  : 'tribe-ticket-checkin',
				provider: obj.attr( 'data-provider' ),
				attendee_id: obj.attr( 'data-attendee-id' ),
				nonce   : Attendees.checkin_nonce
			};

			// add event_ID information if available
			if ( obj.attr( 'data-event-id' ) ) {
				params.event_ID = obj.attr( 'data-event-id' );
			}

			$.post(
				Attendees.ajaxurl,
				params,
				function( response ) {
					if ( response.success ) {
						obj.closest( 'tr' ).addClass( 'tickets_checked' );

						$( '#total_checkedin' ).text( parseInt( $( '#total_checkedin' ).text() ) + 1 );
					}

					obj.prop( 'disabled', false );
				},
				'json'
			);

			e.preventDefault();
		} );

		$( '.tickets_uncheckin' ).on( 'click', function( e ) {

			var obj = jQuery( this );
			obj.prop( 'disabled', true );

			var params = {
				action  : 'tribe-ticket-uncheckin',
				provider: obj.attr( 'data-provider' ),
				attendee_id: obj.attr( 'data-attendee-id' ),
				nonce   : Attendees.uncheckin_nonce
			};

			// add event_ID information if available
			if ( obj.attr( 'data-event-id' ) ) {
				params.event_ID = obj.attr( 'data-event-id' );
			}

			$.post(
				Attendees.ajaxurl,
				params,
				function( response ) {
					if ( response.success ) {
						obj.closest( 'tr' ).removeClass( 'tickets_checked' );
						$( '#total_checkedin' ).text( parseInt( $( '#total_checkedin' ).text() ) - 1 );
					}

					obj.prop( 'disabled', false );
				},
				'json'
			);

			e.preventDefault();
		} );

		/**
		 * Handle "move" requests for individual rows.
		 */
		$( 'table.wp-list-table' ).on( 'click', '.row-actions .move-ticket', function( event ) {
			var ticket_id = $( this ).parents( 'tr' ).find( 'input[name="attendee[]"]' ).val().match( /^[0-9]+/ );

			if ( ticket_id ) {
				create_move_ticket_modal( ticket_id );
			}

			event.stopPropagation();
			return false;
		} );

		/**
		 * Handle "move" bulk action requests.
		 */
		$( '#doaction, #doaction2' ).on( 'click', function( event ) {
			var bulk_action_selector;

			// Which doaction button was selected (top or bottom)?
			switch ( $( event.currentTarget ).attr( 'id' ) ) {
				case 'doaction':  bulk_action_selector = 'action';  break;
				case 'doaction2': bulk_action_selector = 'action2'; break;
			}

			// If a bulk action wasn't selected, we're not interested
			if ( 'undefined' === typeof bulk_action_selector ) {
				return;
			}

			// If the selected bulk action was not 'move', we're not interested
			if ( 'move' !== $( 'select[name="' + bulk_action_selector + '"]' ).val() ) {
				return;
			}

			var $checked_tickets = jQuery( 'input[name="attendee[]"]:checked' );

			if ( ! $checked_tickets.length ) {
				// No tickets/attendees selected? Nothing we can do
				alert( Attendees.cannot_move );
			} else {
				// Add the list of selected ticket IDs to the move modal URL and trigger its appearance
				var ticket_list = [];

				$checked_tickets.each( function() {
					var ticket_id = $( this ).val().match( /^[0-9]+/ );
					if ( ticket_id ) {
						ticket_list.push( ticket_id.toString() );
					}
				} );

				create_move_ticket_modal( ticket_list );
			}

			event.stopPropagation();
			return false;
		} );

		/**
		 * Triggers the creation of the move tickets dialog, passing the
		 * provided ticket IDs across in the process.
		 *
		 * @param ticket_ids
		 */
		function create_move_ticket_modal( ticket_ids ) {
			if ( ! $.isArray( ticket_ids ) ) {
				ticket_ids = [ ticket_ids ];
			}

			var target_width = parseInt( $( window ).width() * 0.7, 10 );
			target_width = target_width > 800 ? 800 : target_width;

			var target_height = parseInt( $( window ).height() * 0.9, 10 );
			target_height = target_height > 800 ? 800 : target_height;

			var params = '&ticket_ids=' + ticket_ids.join( '|' )
				+ '&width=' + target_width + '&height=' + target_height;

			/* We need to add our list of ticket IDs and other params *before* the "TB_*"
			 * param otherwise they will be discarded by Thickbox.
			 *
			 * We pass the list in a pipe separated format rather than a regular [] array
			 * style, again due to Thickbox oddities which would otherwise discard all but
			 * the first value.
			 */
			var request_url = Attendees.move_url.replace( '&TB_', params + '&TB_' )
			tb_show( null, request_url, false );
		}

		/**
		 * Handle ticket history show/hide requests.
		 */
		( function() {
			var $show_links = $( '.ticket-history' );
			var $hide_links = $( '.hide-ticket-history' );

			// Hide the hide history links until they are needed
			$hide_links.hide();

			$show_links.on( 'click', function( event ) {
				var $this      = $( this );
				var $hide_link = $this.siblings( '.hide-ticket-history' );
				var ticket_id  = parseInt( $this.data( 'ticket-id' ), 10 );
				var check      = $this.data( 'check' );

				if ( ! ticket_id || ! check ) {
					return;
				}

				var $existing_row = $( document.getElementById( 'ticket-history-' + ticket_id ) );

				// Reuse the existing history row, if it exists
				if ( $existing_row.length ) {
					$existing_row.show();
					$this.hide();
					$hide_link.show();
				}
				// Otherwise we'll need to load the data and create the history row
				else {
					load_history_row();
				}

				function load_history_row() {
					var request = {
						'action':    'get_ticket_history',
						'check':     check,
						'ticket_id': ticket_id
					};

					$.post( Attendees.ajaxurl, request, function( response ) {
						if ( 'undefined' === typeof response.data || 'string' !== typeof response.data.html ) {
							return;
						}

						var $parent_row  = $this.parents( 'tr' );
						var num_cols     = obj.count_columns( $parent_row );
						var $history_row = $( '<tr id="ticket-history-' + ticket_id + '"> <td colspan="' + num_cols + '">' + response.data.html + '</td></tr>' );

						$history_row.hide().insertAfter( $parent_row ).slideDown();
						$this.hide();
						$hide_link.show();
					} );
				}

				event.stopPropagation();
				return false;
			} )

			$hide_links.on( 'click', function( event ) {
				var $this      = $( this );
				var $show_link = $this.siblings( '.ticket-history' );
				var ticket_id  = parseInt( $show_link.data( 'ticket-id' ), 10 );

				$( document.getElementById( 'ticket-history-' + ticket_id ) ).hide();
				$show_link.show();
				$this.hide();
			} );
		} )();
	}

	/**
	 * Given a jQuery object representing a single row, returns a count of the
	 * total number of columns (accounting for colspans).
	 *
	 * @param $row
	 */
	obj.count_columns = function( $row ) {
		var count = 0;
		var $cells = $row.find( 'td, th' );

		if ( ! $cells.length ) {
			return 0;
		}

		$cells.each( function() {
			var colspan = parseInt( $( this ).attr( 'colspan' ), 10 );
			colspan = ( colspan > 1 ) ? colspan - 1 : 0;
			count += 1 + colspan;
		} );

		return count;
	};

	/**
	 * Given a list of ticket IDs, removes the matching rows from the attendee table.
	 *
	 * @param ticket_ids
	 */
	obj.remove_tickets = function( ticket_ids ) {
		$( '#the-list' ).find( 'tr' ).each( function() {
			var $this = $( this );
			var ticket_ref = $this.find( '.check-column' ).find( 'input' ).val();

			if ( 'string' !== typeof ticket_ref ) {
				return;
			}

			var match_id = ticket_ref.match( /^[0-9]+/ );

			// If we couldn't extract the numeric portion of the row ticket ID, or if that ID
			// isn't in the provided list, then skip ahead
			if ( ! match_id.length || -1 === ticket_ids.indexOf( parseInt( match_id[ 0 ], 10 ) ) ) {
				return;
			}

			// Otherwise, let's remove this row
			$this.remove();
		} );
	};

	function tribe_is_email( emailAddress ) {
		var pattern = new RegExp( /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i );
		return pattern.test( emailAddress );
	}

	function tribe_validate_email() {
		$( '#email_errors' ).removeClass( 'ui-state-error' ).addClass( 'ui-state-highlight' ).text( Attendees.sending );
		var $address = $( '#email_to_address' ).val();
		var $user = $( '#email_to_user' ).val();
		var $email = false;

		if ( $user > - 1 ) {
			$email = $user;
		}

		if ( $address.trim() !== '' && tribe_is_email( $address ) ) {
			$email = $address;
		}

		if ( ! $email ) {
			$( '#email_errors' ).removeClass( 'ui-state-highlight' ).addClass( 'ui-state-error' ).text( Attendees.required );
		}

		return $email;
	}

	function tribe_array_filter( arr ) {

		var retObj = {},
			k;

		for ( k in arr ) {
			if ( arr[k] ) {
				retObj[k] = arr[k];
			}
		}

		return retObj;
	}

	$( init );

} )( jQuery, tribe_event_tickets_attendees );

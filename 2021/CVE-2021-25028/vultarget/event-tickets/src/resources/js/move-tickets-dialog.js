/**
 * Handles the move tickets/move ticket type dialogs.
 *
 * @var object tribe_move_tickets
 * @var object tribe_move_tickets_data
 */

var tribe_move_tickets = tribe_move_tickets || {};

( function( $, obj ) {
	var $main,
	    $processing,
	    $errors,
	    $current_stage,
	    current_stage_name,
	    $stages,
	    $back,
	    $next,
	    last_direction,
	    skip_choose_event,
	    target_post_id,
	    target_ticket_type_id;

	function init() {
		$main       = $( '#main' );
		$errors     = $( '.error' );
		$processing = $( '#processing' );
		$back       = $( '#back' );
		$next       = $( '#next' );

		$main.trigger( 'move-tickets-dialog-pre-init.tribe' );

		$back.add( $next ).hide();
		$processing.hide();

		if ( ! $errors.length ) {
			$next.show();
			setup_stages();
		}

		$main.trigger( 'move-tickets-dialog-post-init.tribe' );
	}

	/**
	 * Can be used to obtain the jQuery object representing the dialog's
	 * #main element (useful for triggering/listening for dialog events).
	 *
	 * @returns object
	 */
	obj.get_main_element = function() {
		return $main;
	};

	/**
	 * Hides the specified button element, but only if stage matches the
	 * ID of the currently activated stage.
	 *
	 * @type function
	 *
	 * @param $btn
	 * @param stage
	 */
	obj.hide_btn = hide_btn;

	/**
	 * Disables the specified button element, but only if stage matches the
	 * ID of the currently activated stage.
	 *
	 * @type function
	 *
	 * @param $btn
	 * @param stage
	 */
	obj.disable_btn = disable_btn;

	/**
	 * Enables the specified button element, but only if stage matches the
	 * ID of the currently activated stage.
	 *
	 * @type function
	 *
	 * @param $btn
	 * @param stage
	 */
	obj.enable_btn = enable_btn;

	/**
	 * Activates the specified stage element.
	 *
	 * @type function
	 *
	 * @param $stage_element
	 */
	obj.activate_stage = activate_stage;

	/**
	 * Show and hide the initial set of stages as required.
	 */
	function setup_stages() {
		// The "choose_event" stage is always required, regardless of mode
		choose_event_stage();

		// We only need the "move_where" and "choose_ticket_type" stages in the "move_tickets" mode
		if ( 'move_tickets' === tribe_move_tickets_data.mode ) {
			move_where_stage();
			choose_ticket_type_stage();
		}
		// In other cases the above two stages can be removed altogether
		else {
			$( '#move-where' ).remove();
			$( '#choose-ticket-type' ).remove();
		}

		// Complete setup
		$stages = $( '.stage' );
		forward_back_handler();
		final_stage_handler();
		activate_stage( $stages.first() );
	}

	function activate_stage( $stage_element ) {
		$current_stage = $stage_element;
		current_stage_name = $current_stage.attr( 'id' );

		$stages.hide();
		$current_stage.show();

		$main.trigger( 'pre-activate-stage.tribe' );
		$main.trigger( 'activate-' + current_stage_name + '.tribe' );
	}

	/**
	 * Setup the "move where" stage, where the user decides if they are targeting
	 * the same or a different post.
	 */
	function move_where_stage() {
		var this_stage = 'move-where';
		var $selectors = $( 'input[name="move-where"]' );

		// This is the first stage - remove the back button as it's useless here
		$main.on( 'activate-move-where.tribe', function() {
			hide_btn( $back, this_stage );
			check_selection();
		} );

		// So long as at least one option has been selected, the user is free to advance
		function check_selection() {
			var $selected = $selectors.filter( ':checked' );

			if ( ! $selected.length ) {
				disable_btn( $next, this_stage );
				return;
			}

			if ( 'this-post' === $selected.val() ) {
				set_target_post( tribe_move_tickets_data.src_post_id );
				skip_choose_event = true;
			} else {
				target_post_id = false;
				skip_choose_event = false;
			}

			enable_btn( $next, this_stage );
		}

		$selectors.change( check_selection );
	}

	/**
	 * Setup the "choose event" stage, where the user selects the target event.
	 */
	function choose_event_stage() {
		var $choose_event = $( '#choose-event' ),
		    $post_choices = $choose_event.find( '.select-single-container' ),
		    $post_type = $( '#post-type' ),
		    $search_terms = $( '#search-terms' ),
		    update_delay,
		    this_stage = 'choose-event',
		    populating = false;

		$main.on( 'activate-choose-event.tribe', function() {
			disable_btn( $next, this_stage );

			// If a destination post has already been selected, advance a stage
			if ( skip_choose_event && last_direction === 'next' ) {
				activate_stage( $current_stage.next() );
			} // Otherwise, if we're moving backwards, move right back the start
			else if ( skip_choose_event ) {
				activate_stage( $stages.first() );
			}

			process_selection();
		} );

		/**
		 * Obtain a list of supported post types and drop them into the
		 * post type selector.
		 */
		function populate_post_types() {
			// Placeholder text to indicate the list is still being loaded
			$post_type.html( '<option value="">' + tribe_move_tickets_data.loading_msg + '</option>' );

			var request = {
				'action': 'move_tickets_get_post_types',
				'check': tribe_move_tickets_data.check
			};

			$.post( ajaxurl, request, function( response ) {
				if ( 'undefined' === typeof response.data || 'object' !== typeof response.data.posts ) {
					return;
				}

				// Wipe the existing content before repopulating
				$post_type.html( '' );

				for ( var key in response.data.posts ) {
					$post_type.append( '<option value="' + key + '">' + response.data.posts[ key ] + '</option>' );
				}
			} );
		}

		/**
		 * Populate the list of possible post choices based on the selected
		 * post type and search keywords, if set.
		 */
		function populate_post_choices() {
			// Don't bombard the server with queries if an update is already in progress
			if ( populating ) {
				return;
			}

			populating = true;
			$post_choices.css( 'opacity', 0.6 );
			disable_btn( $next, this_stage );

			var request = {
				'action': 'move_tickets_get_post_choices',
				'check': tribe_move_tickets_data.check,
				'post_type': $post_type.val(),
				'search_terms': $search_terms.val(),
				'ignore': tribe_move_tickets_data.src_post_id
			};

			$.post( ajaxurl, request, function( response ) {
				if ( 'undefined' === typeof response.data || 'object' !== typeof response.data.posts ) {
					return;
				}

				// Clear the existing list
				$post_choices.html( '' );
				var total_posts = 0;

				for ( var key in response.data.posts ) {
					var post_id = parseInt( key, 10 );
					var title = response.data.posts[ key ];
					total_posts++;

					$post_choices.append(
						'<label> <input type="radio" value="' + post_id + '" name="post-choice">' + title + '</label>'
					);
				}

				if ( ! total_posts ) {
					$post_choices.append(
						'<label>' + tribe_move_tickets_data.no_posts_found + '</label>'
					);
				}

				populating = false;
				$post_choices.css( 'opacity', 1 );
			} );
		}

		function process_selection() {
			var $selected_input = $post_choices.find( 'input:checked' );

			// Clear anything that was already highlighted
			$post_choices.find( '.selected' ).removeClass( 'selected' );

			if ( $selected_input.length ) {
				set_target_post( $selected_input.val() );
				$selected_input.parent( 'label' ).addClass( 'selected' );
				enable_btn( $next, this_stage );
			}
		}

		// Initial setup
		populate_post_types();
		populate_post_choices();

		// Wait just a moment or two after the user stops typing before refreshing the list
		$search_terms.keyup( function() {
			clearTimeout( update_delay );
			update_delay = setTimeout( populate_post_choices, 200 );
		} );

		// If the post type choice is changed, refresh the list immediately
		$post_type.change( populate_post_choices );

		// Once an option is selected, highlight and enable the next button
		$post_choices.click( function() {
			// Disallow selection when the list is being (re-)populated
			if ( populating ) {
				return;
			}

			process_selection();
		} );
	}

	/**
	 * Setup and manage the "choose ticket type" stage; but only if a
	 * destination post has been selected.
	 */
	function choose_ticket_type_stage() {
		var this_stage = 'choose-ticket-type',
		    $choose_type = $( '#choose-ticket-type' ),
		    $type_choices = $choose_type.find( '.select-single-container' );

		$main.on( 'activate-choose-ticket-type.tribe', function() {
			disable_btn( $next, this_stage );

			// If a destination post has not been selected, move back to the start
			if ( ! target_post_id ) {
				activate_stage( $stages.first() );
			}
		} );

		/**
		 * Populate the list of possible ticket types based on what's available within
		 * the destination post.
		 */
		$main.on( 'destination-post-selected.tribe', function() {
			$type_choices.css( 'opacity', 0.6 );
			disable_btn( $next, this_stage );

			var request = {
				'action': 'move_tickets_get_ticket_types',
				'check': tribe_move_tickets_data.check,
				'post_id': target_post_id,
				'provider': tribe_move_tickets_data.provider,
				'ticket_ids': tribe_move_tickets_data.ticket_ids
			};

			$.post( ajaxurl, request, function( response ) {
				if ( 'object' !== typeof response.data.posts ) {
					return;
				}

				// Clear the existing list
				$type_choices.html( '' );
				var types_count = 0;

				for ( var key in response.data.posts ) {
					var post_id = parseInt( key, 10 );
					var title = response.data.posts[ key ];
					types_count++;

					$type_choices.append(
						'<label> <input type="radio" value="' + post_id + '" name="post-choice">' + title + '</label>'
					);
				}

				if ( ! types_count ) {
					$type_choices.append(
						'<label>' + tribe_move_tickets_data.no_ticket_types_found + '</label>'
					);
				}

				$type_choices.css( 'opacity', 1 );
			} );
		} );

		function process_selection() {
			var $selected_input = $type_choices.find( 'input:checked' );

			// Clear anything that was already highlighted
			$type_choices.find( '.selected' ).removeClass( 'selected' );

			if ( $selected_input.length ) {
				target_ticket_type_id = parseInt( $selected_input.val() );
				$selected_input.parent( 'label' ).addClass( 'selected' );
				enable_btn( $next, this_stage );
			}
		}

		$type_choices.click( process_selection );
	}

	/**
	 * Facilitate moving forward/backwards a stage.
	 */
	function forward_back_handler() {
		// Normal/final stage next button text
		var normal_text = $next.html();
		var final_text = $next.data( 'final-text' );
		final_text = final_text ? final_text : normal_text;

		// Support changing the next button so it has a different label at the final stage
		$main.on( 'pre-activate-stage.tribe', function() {
			$next.html( is_final_stage() ? final_text : normal_text );
		} );

		$back.add( $next ).click( function() {
			switch ( $( this ).attr( 'id' ) ) {
				case 'back': move( 'prev' ); break;
				case 'next': move( 'next' ); break
			}
		} );

		function move( direction ) {
			last_direction = direction;
			var $stage     = $current_stage[ direction ]( '.stage' );

			if ( $stage.length ) {
				activate_stage( $stage );
			} else if ( is_final_stage() ) {
				$main.trigger( 'move-tickets-final-stage.tribe')
			}
		}
	}

	/**
	 * Handles the final stage and presents the user with a success message
	 * or further options as needed.
	 */
	function final_stage_handler() {
		// Setup the final stage callback
		$main.on( 'move-tickets-final-stage.tribe', function() {
			switch ( tribe_move_tickets_data.mode ) {
				case 'move_tickets': move_tickets(); break;
				case 'ticket_type_only': move_ticket_type(); break;
			}
		} );

		/**
		 * Handles a move of one or more tickets within the same post or to
		 * a different post.
		 */
		function move_tickets() {
			if ( ! target_post_id || ! target_ticket_type_id ) {
				return;
			}

			var request = {
				'action':         'move_tickets',
				'src_post_id':    tribe_move_tickets_data.src_post_id,
				'target_post_id': target_post_id,
				'check':          tribe_move_tickets_data.check,
				'ticket_ids':     tribe_move_tickets_data.ticket_ids,
				'target_type_id': target_ticket_type_id
			};

			$stages.hide();
			$back.hide();
			$next.hide();
			$processing.show();

			$.post( ajaxurl, request, function( data ) {
				on_response( data )
			} ).fail( on_failure );
		}

		/**
		 * Handles a move of a ticket type to a different post.
		 */
		function move_ticket_type() {
			if ( ! target_post_id ) {
				return;
			}

			var request = {
				'action':         'move_ticket_type',
				'src_post_id':    tribe_move_tickets_data.src_post_id,
				'ticket_type_id': tribe_move_tickets_data.ticket_type_id,
				'target_post_id': target_post_id,
				'check':          tribe_move_tickets_data.check,
			};

			$stages.hide();
			$back.hide();
			$next.hide();
			$processing.show();

			$.post( ajaxurl, request, function( data ) {
				on_response( data )
			} ).fail( on_failure );
		}

		function on_response( response ) {
			if ( 'undefined' === typeof response.data || 'string' !== typeof response.data.message ) {
				on_failure();
				return;
			}

			$processing.html( response.data.message );

			// Respect top window redirects if set
			if ( 'string' === typeof response.data.redirect_top ) {
				var delay = ( 'number' === typeof response.data.redirect_top_delay )
					? response.data.redirect_top_delay
					: 2000;

				setTimeout( function () {
					top.location = response.data.redirect_top;
				}, delay );
			}

			// If specified, try to remove the ticket type entry from the top window
			if ( 'number' === typeof response.data.remove_ticket_type ) {
				top.jQuery( 'table.ticket_list' )
					.find( 'tr[data-ticket-type-id="' + response.data.remove_ticket_type + '"]' )
					.remove();
			}

			top.jQuery( '#ticket_form_cancel' ).trigger( 'click' );

			// Remove the specified tickets from the attendee list
			if ( $.isArray( response.data.remove_tickets ) ) {
				top.tribe_event_tickets_attendees.remove_tickets( response.data.remove_tickets );
			}
		}

		function on_failure() {
			$processing.html( tribe_move_tickets_data.unexpected_failure );
		}
	}

	function set_target_post( id ) {
		id = parseInt( id, 10 );

		// Only update if the ID is a positive integer and if the value has changed
		if ( id > 0 && target_post_id !== id ) {
			target_post_id = id;
			$main.trigger( 'destination-post-selected.tribe' );
		}
	}

	function enable_btn( $btn, stage ) {
		if ( stage === current_stage_name ) {
			$btn.removeProp( 'disabled' ).show().removeClass( 'disabled' );

			// For the next button, on the final stage, let's make it really prominent
			if ( 'next' === $btn.attr( 'id' ) && is_final_stage() ) {
				$btn.addClass( 'button-primary' );
			}
		}
	}

	function disable_btn( $btn, stage ) {
		if ( stage === current_stage_name ) {
			$btn.prop( 'disabled', true ).addClass( 'disabled' ).removeClass( 'button-primary' );
		}
	}

	/**
	 * Hides the specified button element, but only if stage matches the
	 * ID of the currently activated stage.
	 *
	 * @param $btn
	 * @param stage
	 */
	function hide_btn( $btn, stage ) {
		if ( stage === current_stage_name ) {
			$btn.hide();
		}
	}

	function is_final_stage() {
		return ! $current_stage.next( '.stage' ).length;
	}

	$( init );
}( jQuery, tribe_move_tickets ) );
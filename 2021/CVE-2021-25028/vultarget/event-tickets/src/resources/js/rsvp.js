var tribe_tickets_rsvp = {
	num_attendees: 0,
	event: {},
};

(function( $, my ) {
	'use strict';

	my.init = function() {
		my.$rsvp = $( '.tribe-events-tickets-rsvp' );
		my.attendee_template = $( document.getElementById( 'tribe-tickets-rsvp-tmpl' ) ).html();

		my.$rsvp.on( 'change input keyup', '.tribe-tickets-quantity', my.event.quantity_changed );

		my.$rsvp.closest( '.cart' )
			.on( 'submit', my.event.handle_submission );

		$( '.tribe-rsvp-list' ).on( 'click', '.attendee-meta-row .toggle', function() {
			$( this )
				.toggleClass( 'on' )
				.siblings( '.attendee-meta-details' )
				.slideToggle();
		} );
	};

	my.quantity_changed = function( $quantity ) {
		const $rsvp = $quantity.closest( '.tribe-events-tickets-rsvp' );
		const $rsvpQtys = $rsvp.find( '.tribe-tickets-quantity' );
		let rsvpQty = 0;
		$rsvpQtys.each( function() {
			rsvpQty = rsvpQty + parseInt( $( this ).val(), 10 );
		} );

		if ( 0 === rsvpQty ) {
			$rsvp.removeClass( 'tribe-tickets-has-rsvp' );
		} else {
			$rsvp.addClass( 'tribe-tickets-has-rsvp' );
		}
	};

	my.validate_rsvp_info = function( $form ) {
		const $qty = $form.find( 'input.tribe-tickets-quantity' );
		const $name = $form.find( 'input#tribe-tickets-full-name' );
		const $email = $form.find( 'input#tribe-tickets-email' );
		let rsvpQty = 0;

		$qty.each( function() {
			rsvpQty = rsvpQty + parseInt( $( this ).val(), 10 );
		} );

		return (
			$name.val().trim().length &&
			$email.val().trim().length &&
			rsvpQty
		);
	};

	my.validate_meta = function( $form ) {
		const hasTicketsPlus = !! window.tribe_event_tickets_plus;
		let isMetaValid = true;

		if ( hasTicketsPlus ) {
			isMetaValid = window.tribe_event_tickets_plus.meta.validate_meta( $form );
		}

		return isMetaValid;
	};

	my.event.quantity_changed = function() {
		my.quantity_changed( $( this ) );
	};

	my.event.handle_submission = function( e ) {
		const $form = $( this ).closest( 'form' );

		const $rsvpMessages = $form.find(
			'.tribe-rsvp-messages, ' +
			'.tribe-rsvp-message-confirmation-error',
		);

		const $etpMetaMessages = $form.find( '.tribe-event-tickets-meta-required-message' );

		const isRsvpInfoValid = my.validate_rsvp_info( $form );
		const isAttendeeMetaValid = my.validate_meta( $form );

		// Show/Hide message about missing RSVP details (name, email, going/not) and/or missing ETP fields (if applicable).
		if ( ! isRsvpInfoValid || ! isAttendeeMetaValid ) {
			isRsvpInfoValid
				? $rsvpMessages.hide()
				: $rsvpMessages.show();

			if ( isAttendeeMetaValid ) {
				$etpMetaMessages.hide();
				$form.removeClass( 'tribe-event-tickets-plus-meta-missing-required' );
			} else {
				$form.addClass( 'tribe-event-tickets-plus-meta-missing-required' );
				$etpMetaMessages.show();
			}

			$( 'html, body' ).animate( {
				scrollTop: $form.offset().top - 100,
			}, 300 );

			return false;
		}

		return true;
	};

	$( my.init );
})( jQuery, tribe_tickets_rsvp );

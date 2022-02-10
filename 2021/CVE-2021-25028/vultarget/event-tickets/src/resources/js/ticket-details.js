var tribe_ticket_details = tribe_ticket_details || {};

( function( $, obj ) {
	'use strict';
	var $document = $( document );

	obj.init = function( detailsElems ) { // eslint-disable-line no-unused-vars
		obj.event_listeners();
	}

	obj.selectors = [
		'.tribe-tickets__item__details__summary--more',
		'.tribe-tickets__item__details__summary--less',
	];

	obj.event_listeners = function() {
		// Add keyboard support for enter key.
		$document.on( 'keyup', obj.selectors, function( event ) {
			// Toggle open like click does.
			if ( 13 === event.keyCode ) {
				obj.toggle_open( event.target );
			}
		} );

		$document.on( 'click', obj.selectors, function( event ) {
			obj.toggle_open( event.target );
		} );
	}

	obj.toggle_open = function( trigger ) {
		if( ! trigger ) {
			return;
		}
		var $trigger = $( trigger );

		if (
			! $trigger.hasClass( 'tribe-tickets__item__details__summary--more' ) &&
			! $trigger.hasClass( 'tribe-tickets__item__details__summary--less' )
		) {
			return;
		}


		var $parent = $trigger.closest( '.tribe-tickets__item__details__summary' );
		var $target = $( '#' + $trigger.attr( 'aria-controls' ) );

		if ( ! $target || ! $parent ) {
			return;
		}

		event.preventDefault();
		// Let our CSS handle the hide/show. Also allows us to make it responsive.
		var onOff = ! $parent.hasClass( 'tribe__details--open' );
		$parent.toggleClass( 'tribe__details--open', onOff );
		$target.toggleClass( 'tribe__details--open', onOff );
	}

	$(
		function() {
			var detailsElems = document.querySelectorAll( '.tribe-tickets__item__details__summary' );

			// details element not present
			if ( ! detailsElems.length ) {
				return;
			}

			obj.init( detailsElems );
		}
	);

} )( jQuery, tribe_ticket_details );

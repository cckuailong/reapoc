(function ($) {
	'use strict';
	$(function () {
		$('.calendar-event').children().not('.event-title,.screen-reader-text').hide();

		$(document).on('click', '.calendar-event .event-title a',
			function (e) {
				e.preventDefault();
				var current_date = $(this).parents( '.vevent' ).children();

				$(this).closest( '.mc-main' ).toggleClass( 'grid-open' );
				$(this).parents( '.vevent' ).children().not('.event-title').toggle().attr('tabindex', '-1');
				$(this).parents( '.vevent' ).trigger( 'focus' );

				var focusable = current_date.find( 'a, object, :input, iframe, [tabindex]' );
				var lastFocus  = focusable.last();
				var firstFocus = focusable.first();
				lastFocus.attr( 'data-action', 'shiftback' );

				$('.calendar-event').children().not('.event-title,.screen-reader-text').not( current_date ).hide();
			});

		$(document).on('click', '.calendar-event .close',
			function (e) {
				e.preventDefault();
				$(this).closest( '.mc-main' ).removeClass( 'grid-open' );
				$(this).closest('.vevent').find('.event-title a').trigger( 'focus' );
				$(this).closest('div.details').toggle();
			});

		$(document).on( 'keydown', function(e) {
			var keycode   = ( e.keyCode ? e.keyCode : e.which );
			if ( keycode == 27 ) {
				$( '.mc-main ').removeClass( 'grid-open' );
				$( '.calendar-event div.details' ).hide();
			}
		});

		$(document).on( 'keydown', '.details a, .details object, .details :input, .details iframe, .details [tabindex]',
			function(e) {
				var keycode   = ( e.keyCode ? e.keyCode : e.which );
				var action = $( ':focus' ).attr( 'data-action' );
				if ( ( !e.shiftKey && keycode == 9 ) && action == 'shiftback' ) {
					e.preventDefault();
					$( '.mc-toggle.close' ).trigger( 'focus' );
				}
				if ( ( e.shiftKey && keycode == 9 ) && action == 'shiftforward' ) {
					e.preventDefault();
					$( '[data-action=shiftback]' ).trigger( 'focus' );
				}
			});
	});
}(jQuery));
(function ($) {
	'use strict';
	$(function () {
		$( ".mini .has-events" ).children().not( ".trigger, .mc-date, .event-date" ).hide();
		$( document ).on( "click", ".mini .has-events .trigger", function (e) {
			e.preventDefault();
			var current_date = $(this).parent().children();
			current_date.not(".trigger").toggle().attr( "tabindex", "-1" ).trigger( 'focus' );
			$( '.mini .has-events' ).children( '.trigger' ).removeClass( 'active-toggle' );
			$( '.mini .has-events' ).children().not( '.trigger, .mc-date, .event-date' ).not( current_date ).hide();
			$( this ).addClass( 'active-toggle' );
		} );
		$( document ).on( "click", ".calendar-events .close", function (e) {
			e.preventDefault();
			$(this).closest( '.mini .has-events' ).children( '.trigger' ).removeClass( 'active-toggle' );
			$(this).closest( 'div.calendar-events' ).toggle();
		} );
	});
}(jQuery));
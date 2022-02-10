(function ($) {
	'use strict';
	$(function () {
		$('li.mc-events').children().not('.event-date').hide();
		$('li.current-day').children().show();
		$(document).on( 'click', '.event-date button',
			function (e) {
				e.preventDefault();
				var vevent = $( this ).closest( '.mc-events' ).find( '.vevent:first' );
				$( this ).closest( '.mc-events' ).find( '.vevent' ).toggle();
				vevent.attr('tabindex', '-1').trigger( 'focus' );
				var visible = $(this).closest( '.mc-events' ).find('.vevent').is(':visible');
				if ( visible ) {
					$(this).attr('aria-expanded', 'true');
				} else {
					$(this).attr('aria-expanded', 'false');
				}
			});
	});
}(jQuery));
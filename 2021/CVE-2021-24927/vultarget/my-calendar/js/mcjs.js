(function ($) {
	'use strict';
	$(function () { 
		$( '.mc-main' ).removeClass( 'mcjs' ); 
	});

	$('.mc-main a[target=_blank]').append( ' <span class="dashicons dashicons-external" aria-hidden="true"></span><span class="screen-reader-text"> ' + my_calendar.newWindow + '</span>' );
}(jQuery));
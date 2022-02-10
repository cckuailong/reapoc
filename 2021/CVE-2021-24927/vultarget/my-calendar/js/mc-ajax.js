(function ($) {
	'use strict';
	$(function () {
		mc_display_usertime();
		$(document).on('click', ".my-calendar-header a.mcajax, .my-calendar-footer a.mcajax", function (e) {
			e.preventDefault();
			var calendar = $( this ).closest( '.mc-main' );
			var ref      = calendar.attr('id');
			var link     = $(this).attr('href');
			let url;
			try {
				url = new URL(link);
				url.searchParams.delete('embed');

				window.history.pushState({}, '', url );
			} catch(_) {
				url = false;
			}

			var height   = calendar.height();
			$('#' + ref).html('<div class=\"mc-loading\"></div><div class=\"loading\" style=\"height:' + height + 'px\"><span class="screen-reader-text">Loading...</span></div>');
			$( '#' + ref ).load(link + ' #' + ref + ' > *', function ( response, status, xhr ) {

				if ( status == 'error' ) {
					$( '#' + ref ).html( xhr.status + " " + xhr.statusText );
				}
				// functions to execute when new view loads.
				// List view.
				if ( typeof( mclist ) !== "undefined" && mclist.list == 'true' ) {
					$('li.mc-events').children().not('.event-date').hide();
					$('li.current-day').children().show();
				}
				// Grid view.
				if ( typeof( mcgrid ) !== "undefined" && mcgrid.grid == 'true' ) {
					$('.calendar-event').children().not('.event-title').hide();
				}
				// Mini view.
				if  ( typeof( mcmini ) !== "undefined" && mcmini.mini == 'true' ) {
					$('.mini .has-events').children().not('.trigger, .mc-date, .event-date').hide();
				}
				// All views.
				$( '#' + ref ).attr('tabindex', '-1').trigger( 'focus' );
				mc_display_usertime();
				// Your Custom ajax load changes if needed.
			});
		});
	});

	function mc_display_usertime() {
		var utime = $( '.mc-user-time' );
		utime.each(function() {
			var time  = $( this ).text();
			var label = $( this ).attr( 'data-label' );
			var utime = '<span class="mc-local-time-time">' + new Date( time ).toLocaleTimeString().replace( ':00 ', ' ' ) + '</span>';
			var udate = '<span class="mc-local-time-date">' + new Date( time ).toLocaleDateString() + '</span>';
			$( this ).html( '<span class="mc-local-time-label">' + label + '</span>' + ' ' + udate + '<span class="sep">, </span>' + utime ).attr( 'data-time', time );
		});
	}
}(jQuery));
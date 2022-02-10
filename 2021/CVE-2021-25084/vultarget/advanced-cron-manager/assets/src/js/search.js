( function( $ ) {

	var search_input_delay = 400,
		timer;

	$( '#search' ).bind( 'input', function() {
		window.clearTimeout( timer );
		timer = window.setTimeout( function() {
			wp.hooks.doAction( 'advanced-cron-manager.events.search.triggered', $( '#search' ).val() );
		}, search_input_delay );
	} );

	// filter the events list
	wp.hooks.addAction( 'advanced-cron-manager.events.search.triggered', 'bracketspace/acm/events-search-triggered', function( search_word ) {

		$( '#events .events .single-event.row' ).each( function() {

			var $row = $( this );
			var event_name = $row.find( '.columns .event .event-name' ).text();

			if ( event_name.toLowerCase().indexOf( search_word.toLowerCase() ) == -1 ) {
				$row.hide();
			} else {
				$row.show();
			}

		} );

	} );

	// clear search input while using filters
	wp.hooks.addAction( 'advanced-cron-manager.events.filter.schedule', 'bracketspace/acm/events-filter-schedule', function() {
		$( '#search' ).val( '' );
	} );

	// preserve search value.
	wp.hooks.addAction(
		'advanced-cron-manager.events.search.triggered',
		'bracketspace/acm',
		function( value ) {
			var url_params = new URLSearchParams( window.location.search );

			if ( value !== "") {
				url_params.set( 'event-search', value );
			} else {
				url_params.delete( 'event-search' );
			}

			var url = "?" + url_params.toString();
			window.history.pushState( { 'event-search': value }, '', url );
		}
	);

	// apply search to events table by last search value.
	function events_table_preserved_search () {
		var url_params   = new URLSearchParams( window.location.search );
		var search_param = url_params.get( 'event-search' );

		if ( search_param !== null && search_param !== "") {
			$( '#search' ).val( search_param ).trigger( 'input' );
		}
	}

	// apply preserved sorting when window is reloaded.
	$( window ).ready( events_table_preserved_search );

	wp.hooks.addAction( 'advanced-cron-manager.event.search', 'bracketspace/acm', events_table_preserved_search );

} )( jQuery );

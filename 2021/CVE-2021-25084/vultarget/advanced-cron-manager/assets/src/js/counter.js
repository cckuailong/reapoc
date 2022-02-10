( function( $ ) {

	function count_events() {
		var number_of_events = $( '#events .events .single-event.row:visible' ).length;
		$( '#events .tablenav .tablenav-pages .displaying-num' ).text( number_of_events + ' ' + advanced_cron_manager.i18n.events );
	}

	wp.hooks.addAction( 'advanced-cron-manager.events.filter.schedule', 'bracketspace/acm/events-filter-schedule', count_events, 100 );
	wp.hooks.addAction( 'advanced-cron-manager.events.search.triggered', 'bracketspace/acm/events-search-triggered', count_events, 100 );

} )( jQuery );

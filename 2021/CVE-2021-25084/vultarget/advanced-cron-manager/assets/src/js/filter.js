( function( $ ) {

	$( '.tools_page_advanced-cron-manager' ).on( 'change', '#events .tablenav .schedules-filter', function() {
		wp.hooks.doAction( 'advanced-cron-manager.events.filter.schedule', $( this ).val() );
	} );

	// filter the events list with schedule
	wp.hooks.addAction( 'advanced-cron-manager.events.filter.schedule', 'bracketspace/acm/events-filter-schedule', function( schedule ) {

		$( '#events .events .single-event.row' ).each( function() {

			var $row = $( this );
			var event_schedule = $row.data( 'schedule' );

			if ( event_schedule == schedule || schedule == '' ) {
				$row.show();
			} else {
				$row.hide();
			}

		} );

	} );

	// clear filters while using search
	wp.hooks.addAction( 'advanced-cron-manager.events.search.triggered', 'bracketspace/acm/events-search-triggered', function() {
		$( '#events .tablenav .schedules-filter' ).val( '' );
	} );

} )( jQuery );

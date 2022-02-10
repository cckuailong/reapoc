
(function ($) {

	// ///////////
	// Sorting  //
	// ///////////

	$( '.tools_page_advanced-cron-manager' ).on(
		'click',
		'#events .header .is-sortable',
		function (event) {

			event.preventDefault();

			var event_rows_block = $( '.event-rows-block' );
			var event_rows       = event_rows_block.children();
			var column_name      = $( this ).data( 'name' );
			var column_headers   = $( '#events .header' ).find( "[data-name='" + column_name + "']" );

			$.each(
				column_headers,
				function ( index, item ) {
					assign_order_class( item );
				}
			);

			preserve_sorting( $( this ) );

			event_rows.sort( get_comparator( column_name, get_order_direction( $( this ) ) ) );
			event_rows_block.html( event_rows );
		}
	);

	function get_comparator ( column_name, order ) {
		switch ( column_name ) {
			case 'event':
				return compare_by_event_name;
			case 'schedule':
				return compare_by_event_schedule;
			case 'next-execution':
				return compare_by_event_execution;
			default:
				return;
		}

		function compare_by_event_name(row1, row2) {
			row1 = $( row1 ).find( '.event-name' ).text().toLowerCase();
			row2 = $( row2 ).find( '.event-name' ).text().toLowerCase();
			return row1.localeCompare( row2 ) * order;
		}

		function compare_by_event_schedule(row1, row2) {
			row1 = parseInt( $( row1 ).find( '.schedule' ).data( 'interval' ) );
			row2 = parseInt( $( row2 ).find( '.schedule' ).data( 'interval' ) );
			return row1 === row2 ? 0 : ( row1 > row2 ? 1 : -1 ) * order;
		}

		function compare_by_event_execution(row1, row2) {
			row1 = parseInt( $( row1 ).find( '.next-execution' ).data( 'time' ) );
			row2 = parseInt( $( row2 ).find( '.next-execution' ).data( 'time' ) );
			return row1 === row2 ? 0 : ( row1 > row2 ? 1 : -1 ) * order;
		}
	}

	function assign_order_class( column_header ) {
		if ( $( column_header ).is( '.asc' ) || $( column_header ).is( '.desc' ) ) {
			$( column_header ).toggleClass( 'asc desc' );
		} else {
			$( column_header ).addClass( 'asc' );
		}
		$( column_header ).siblings().removeClass( 'asc desc' );
	}

	function preserve_sorting( column_header ) {
			var url_params = new URLSearchParams( window.location.search );
			var sort       = column_header.data( 'name' );
			var order      = column_header.is( '.asc' ) ? 'asc' : 'desc';

			url_params.set( 'sort', sort );
			url_params.set( 'order',  order );
			var url = "?" + url_params.toString();

			window.history.pushState( { 'sort': sort, 'order': order }, '', url );
	}

	function get_order_direction( column_header ) {
		if ( column_header.is( '.asc' ) ) {
			return 1;
		} else if ( column_header.is( '.desc' ) ) {
			return -1;
		}
		return 0;
	}


	// sort events table by last selected sorting.
	function events_table_preserved_sort() {
		var column_name = get_param_from_url( 'sort' );
		var order_class = get_param_from_url( 'order' );

		if ( column_name && order_class ) {
			order_class = order_class === 'desc' ? 'asc' : 'desc';
			$( '.columns' ).find( "[data-name='" + column_name + "']" )
				.removeClass( 'asc desc' )
				.addClass( order_class )
				.first()
				.trigger( 'click' );
		}

		function get_param_from_url( key ) {
			var url_params = new URLSearchParams( window.location.search );
			return url_params.get( key );
		}
	}

	// apply preserved sorting when window is reloaded.
	$( window ).ready( events_table_preserved_sort );

	wp.hooks.addAction( 'advanced-cron-manager.event.sort', 'bracketspace/acm', events_table_preserved_sort );

})( jQuery );

( function( $ ) {

	////////////
	// Action //
	////////////

	$( '.tools_page_advanced-cron-manager' ).on( 'click', '.tablenav .action', function( event ) {

		event.preventDefault();

		var $apply_button = $( this );
		var $select_input = $( this ).prev( 'select' );
		var action        = $select_input.val();

		if ( action != '-1' ) {

			$apply_button.attr( 'disabled', true );

			get_all_checkboxes( true ).each( function() {

				var $checkbox      = $( this );
				var $action_button = $checkbox.parents( '.single-event.row' ).first().find( 'a.' + action + '-event' );

				if ( $action_button ) {
					$action_button.trigger( 'click' );
				}

				$checkbox.attr( 'checked', false );

			} );

			$apply_button.attr( 'disabled', false );
			$select_input.val( '-1' );

		}

	} );

	////////////////
	// Checkboxes //
	////////////////

	var $cb_all    = $( '.single-event.header .select-all' ),
		cb_checked = [];

	function get_all_checkboxes( checked ) {

		checked = typeof checked !== 'undefined' ? checked : false;

		if ( checked ) {
			var appendix = ':checked';
		} else {
			var appendix = '';
		}

		return $( '#events .events .single-event.row:visible .cb input:checkbox' + appendix );

	}

	function clear_all_checkboxes() {
		get_all_checkboxes().prop( 'checked', false );
		$cb_all.prop( 'checked', false );
	}

	// change all rows if parent checkboxes has been changed
	$cb_all.on( 'change', function() {
		get_all_checkboxes().prop( 'checked', this.checked );
		$cb_all.prop( 'checked', this.checked );
	});

	// check if parent checkboxes should be changed when changing row checkboxes
	get_all_checkboxes().on( 'change', function() {
		$cb_all.prop( 'checked', ( get_all_checkboxes( true ).length == get_all_checkboxes().length ) );
	} );

	// clear all checkboxes on search
	wp.hooks.addAction( 'advanced-cron-manager.events.search.triggered', 'bracketspace/acm/events-search-triggered', clear_all_checkboxes );

	// clear all checkboxes on filter
	wp.hooks.addAction( 'advanced-cron-manager.events.filter.schedule', 'bracketspace/acm/events-filter-schedule', clear_all_checkboxes );

} )( jQuery );

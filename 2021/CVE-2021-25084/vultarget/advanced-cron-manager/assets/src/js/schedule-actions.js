( function( $ ) {

	///////////////////
	// form requests //
	///////////////////

	$( '.tools_page_advanced-cron-manager' ).on( 'click', '.add-schedule', function( event ) {

		event.preventDefault();
		wp.hooks.doAction( 'advanced-cron-manager.schedule.add', $(this) );

	} );

	$( '.tools_page_advanced-cron-manager' ).on( 'click', '#schedules .single-schedule .actions .edit-schedule', function( event ) {

		event.preventDefault();
		wp.hooks.doAction( 'advanced-cron-manager.schedule.edit', $(this).parents( '.single-schedule' ).first() );

	} );

	/////////////////////
	// Form processing //
	/////////////////////

	$( '.slidebar' ).on( 'submit', '.schedule-add', function( event ) {

		event.preventDefault();
		wp.hooks.doAction( 'advanced-cron-manager.schedule.add.process', $(this) );

	} );

	$( '.slidebar' ).on( 'submit', '.schedule-edit', function( event ) {

		event.preventDefault();
		wp.hooks.doAction( 'advanced-cron-manager.schedule.edit.process', $(this) );

	} );

	$( '.tools_page_advanced-cron-manager' ).on( 'click', '#schedules .single-schedule .actions .remove-schedule', function( event ) {

		event.preventDefault();
		wp.hooks.doAction( 'advanced-cron-manager.schedule.remove.process', $(this).parents( '.single-schedule' ).first() );

	} );

	/////////////
	// Actions //
	/////////////

	// add schedule
	wp.hooks.addAction( 'advanced-cron-manager.schedule.add', 'bracketspace/acm/schedule-add', function( $button ) {

		advanced_cron_manager.slidebar.open();
		advanced_cron_manager.slidebar.wait();

		var data = {
	        'action': 'acm/schedule/add/form',
	        'nonce' : $button.data( 'nonce' )
	    };

	    $.post( ajaxurl, data, function( response ) {
	        advanced_cron_manager.slidebar.fulfill( response.data );
	    } );

	} );

	wp.hooks.addAction( 'advanced-cron-manager.schedule.add.process', 'bracketspace/acm/schedule-add-process', function( $form ) {

		advanced_cron_manager.slidebar.form_process_start();

		var data = {
	        'action': 'acm/schedule/insert',
	        'nonce' : $form.find( '#nonce' ).val(),
	        'data'  : $form.serialize()
	    };

	    $.post( ajaxurl, data, function( response ) {

	    	advanced_cron_manager.ajax_messages( response );

	        if ( response.success == true ) {
	        	wp.hooks.doAction( 'advanced-cron-manager.schedule.added', $form.find( '#schedule-slug' ).val() );
	        } else {
	        	advanced_cron_manager.slidebar.form_process_stop();
	        }

	    } );

	} );

	// edit schedule
	wp.hooks.addAction( 'advanced-cron-manager.schedule.edit', 'bracketspace/acm/schedule-edit', function( $row ) {

		event.preventDefault();

		var $button = $row.find( '.actions .edit-schedule' );
		var schedule_name = $button.data( 'schedule' );

		advanced_cron_manager.slidebar.open();
		advanced_cron_manager.slidebar.wait();

		var data = {
	        'action'  : 'acm/schedule/edit/form',
	        'nonce'   : $button.data( 'nonce' ),
	        'schedule': schedule_name
	    };

	    $.post( ajaxurl, data, function( response ) {
	        advanced_cron_manager.slidebar.fulfill( response.data );
	    } );

	} );

	wp.hooks.addAction( 'advanced-cron-manager.schedule.edit.process', 'bracketspace/acm/schedule-edit-process', function( $form ) {

		advanced_cron_manager.slidebar.form_process_start();

		var data = {
	        'action': 'acm/schedule/edit',
	        'nonce' : $form.find( '#nonce' ).val(),
	        'data'  : $form.serialize()
	    };

	    $.post( ajaxurl, data, function( response ) {

	    	advanced_cron_manager.ajax_messages( response );

	        if ( response.success == true ) {
	        	wp.hooks.doAction( 'advanced-cron-manager.schedule.edited', $form.find( '#schedule-slug' ).val() );
	        } else {
	        	advanced_cron_manager.slidebar.form_process_stop();
	        }

	    } );

	} );

	// remove schedule
	wp.hooks.addAction( 'advanced-cron-manager.schedule.remove.process', 'bracketspace/acm/schedule-remove-process', function( $row ) {

		var $button = $row.find( '.actions .remove-schedule' );

		$button.addClass( 'working' );

		var schedule_name = $button.data( 'schedule' );

	    var data = {
	        'action'  : 'acm/schedule/remove',
	        'nonce'   : $button.data( 'nonce' ),
	        'schedule': schedule_name
	    };

	    $.post( ajaxurl, data, function( response ) {

	        advanced_cron_manager.ajax_messages( response );

	        if ( response.success == true ) {
	        	$row.slideUp();
	        	wp.hooks.doAction( 'advanced-cron-manager.schedule.removed', schedule_name );
	        } else {
	        	$button.removeClass( 'working' );
	        }

	    } );

	} );

	// refresh table and close slidebar
	var schedules_table_rerender = function() {

		$( '#schedules' ).addClass( 'loading' );

	    $.post( ajaxurl, { 'action': 'acm/rerender/schedules' }, function( response ) {
	    	$( '#schedules' ).replaceWith( response.data );
	    	advanced_cron_manager.slidebar.form_process_stop();
			advanced_cron_manager.slidebar.close();
	    } );

	};

	wp.hooks.addAction( 'advanced-cron-manager.schedule.added', 'bracketspace/acm/schedule-added', schedules_table_rerender );
	wp.hooks.addAction( 'advanced-cron-manager.schedule.edited', 'bracketspace/acm/schedule-edited', schedules_table_rerender );

	/////////////
	// Helpers //
	/////////////

	// add schedule form helpers
	$( '.slidebar' ).on( 'blur', '#schedule-name', function() {
		var name = $(this).val();
		if ( $( '.slidebar #schedule-slug' ).val() == '' ) {
			var slug = name.trim().toLowerCase().replace( / /g, '_' );
			$( '.slidebar #schedule-slug' ).val( slug );
		}
	} );

	$( '.slidebar' ).on( 'change', 'table .spinbox', function() {

		var seconds = 0;

		$( '.slidebar table .spinbox' ).each( function() {

			var $input = $( this );

			if ( $input.hasClass( 'days' ) ) {
				var multiplier = 86400;
			} else if ( $input.hasClass( 'hours' ) ) {
				var multiplier = 3600;
			} else if ( $input.hasClass( 'minutes' ) ) {
				var multiplier = 60;
			} else {
				var multiplier = 1;
			}

			seconds = seconds + ( $input.val() * multiplier );

		} );

		$( '.slidebar .total-seconds span' ).text( seconds );
		$( '.slidebar .interval-input' ).val( seconds );

	} );

} )( jQuery );

( function( $ ) {

	$( '.tools_page_advanced-cron-manager' ).on( 'click', '#events .details .tabs a', function( event ) {

		event.preventDefault();

		var target = $( this ).data( 'section' );

		var $details = $( this ).parents( '.details' ).first();

		$details.find( '.tabs li.active' ).removeClass( 'active' );
		$( this ).parent().addClass( 'active' );

		$details.find( '.content.active' ).removeClass( 'active' );
		$details.find( '.content.' + target ).addClass( 'active' );

		wp.hooks.doAction( 'advanced-cron-manager.event.details.tabs.changed', target );

	} );

} )( jQuery );

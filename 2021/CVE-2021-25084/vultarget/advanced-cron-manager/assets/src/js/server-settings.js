( function( $ ) {

	/////////////////////
	// Form processing //
	/////////////////////

	$( '#server-settings-form' ).on( 'submit', function( event ) {

		event.preventDefault();
		wp.hooks.doAction( 'advanced-cron-manager.server.settings.action', $(this) );

	} );

	/////////////
	// Actions //
	/////////////

	wp.hooks.addAction( 'advanced-cron-manager.server.settings.action', 'bracketspace/acm/server-settings-action', function( $form ) {

		var $button = $form.find( '.button-secondary' ).first();

		var data = {
			'action' : 'acm/server/settings/save',
			'data'   : $form.serialize(),
			'nonce'  : $button.data( 'nonce' )
	    };

	    var button_label = $button.val();

	    $button.val( advanced_cron_manager.i18n.saving );
	    $button.attr( 'disabled', true );

	    $.post( ajaxurl, data, function( response ) {

	        advanced_cron_manager.ajax_messages( response );

	        $button.val( button_label );
		    $button.attr( 'disabled', false );

	    } );

	} );

	/////////////
	// Helpers //
	/////////////

	$( '#server-settings-form' ).on( 'change', '.master-setting input', function() {

		if ( this.checked ) {
			$( this ).parent().nextAll( '.dependants' ).show();
		} else {
			$( this ).parent().nextAll( '.dependants' ).hide();
		}

	} );

} )( jQuery );

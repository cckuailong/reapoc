jQuery( function($) {

	/**
	 * Handle the Uninstall Plugin button
	 *
	 */
	$(document).on( 'click', '#wpbs-uninstaller-button', function(e) {

		if( ! $('#wpbs-uninstaller-confirmation').is(':visible') ) {

			e.preventDefault();

			$('#wpbs-uninstaller-confirmation').fadeIn( 300 );
			$(this).attr( 'disabled', true );

			return false;

		} else {

			if( ! confirm( 'Are you sure you wish to remove all WP Booking System related data?' ) )
				return false;

		}

	});

	/**
	 * Track the value of the confirmation field to match the word REMOVE
	 * before letting the user click the Uninstall button
	 *
	 */
	$(document).on( 'keyup', function() {

		if( ! $('#wpbs-uninstaller-confirmation').is(':visible') )
			return false;

		if( $('#wpbs-uninstaller-confirmation-field').val() == 'REMOVE' )
			$('#wpbs-uninstaller-button').attr( 'disabled', false );
		else
			$('#wpbs-uninstaller-button').attr( 'disabled', true );

	});

});
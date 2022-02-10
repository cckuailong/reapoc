jQuery(function($){

	$(document).ready(function(){

		$( 'select#mycred-email-instance' ).change(function(e){

			var selectedevent = $(this).find( ':selected' );
			console.log( selectedevent.val() );
			if ( selectedevent.val() == 'custom' ) {

				$( '#reference-selection' ).show();

			}
			else {

				$( '#reference-selection' ).hide();

			}

		});

		$( 'select#mycred-email-reference' ).change(function(e){

			var selectedevent = $(this).find( ':selected' );
			if ( selectedevent.val() == 'mycred_custom' ) {

				$( '#custom-reference-selection' ).show();
				$( '#mycred-email-custom-ref' ).focus();

			}
			else {

				$( '#custom-reference-selection' ).hide();
				$( '#mycred-email-custom-ref' ).blur();

			}

		});

	});

});
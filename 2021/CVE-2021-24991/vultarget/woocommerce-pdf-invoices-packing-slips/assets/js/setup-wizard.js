jQuery( function( $ ) {

	$( '.tab' ).on( 'click', function() {
		$( this ).closest('.extra-field').find('.tab').removeClass( 'active' );
		$( this ).addClass( 'active' );
		var $language = $( this ).attr('id');
		$( this ).siblings('.extra-field-input').hide();
		$('.' + $language ).show();
	});

	// Show Preview of logo
	$('#file-upload').on( 'change',  function(event) {
		if ( event.target.files[0] ) {
			var tmppath = URL.createObjectURL(event.target.files[0]);
			$( '#logo-preview' ).find( "img" ).attr( 'src',tmppath );
		}
	});

});

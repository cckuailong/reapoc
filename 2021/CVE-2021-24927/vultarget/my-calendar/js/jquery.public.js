(function ($) {
    $(function () {
		// Delete single instances of recurring events.
		$( '.mc_response' ).hide();
        $('button.delete_occurrence').on( 'click', function () {
            var value = $(this).attr( 'data-value' );
            var data = {
                'action': mc_data.action,
                'occur_id': value,
                'security': mc_data.security
            };
            $.post( ajaxurl, data, function (response) {
				if ( response.success == 1 ) {
					$( "button[data-value='"+value+"']" ).parent( 'li' ).hide();
				}
                $('.mc_response').text( response.response ).show( 300 );
            }, "json" );
        });
}(jQuery));
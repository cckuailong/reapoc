jQuery( function ( $ ) {

	$( '.dlm-notice.is-dismissible' ).on( 'click', '.notice-dismiss', function ( event ) {
		//$( '#dlm-ajax-nonce' ).val()
		var notice_el = $( this ).closest( '.dlm-notice' );

		var notice = notice_el.attr( 'id' );
		var notice_nonce = notice_el.attr( 'data-nonce' );
		$.post(
			ajaxurl,
			{
				action: 'dlm_dismiss_notice',
				nonce: notice_nonce,
				notice: notice
			},
			function ( response ) {
			}
		)
	} );

} );
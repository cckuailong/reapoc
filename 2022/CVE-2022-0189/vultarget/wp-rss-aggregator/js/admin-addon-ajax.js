jQuery( document ).ready( function($) {

	$('.ajax-close-addon-notice').click( function() {
		var addon = $(this).attr( 'data-addon' ),
			notice = $(this).attr( 'data-notice' ),
			element = $(this).parent().parent();
		if ( addon !== false && addon !== undefined && notice !== false && notice !== undefined ) {
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'wprss_dismiss_addon_notice',
					addon: addon,
					notice: notice,
                    nonce: wprss_admin_addon_ajax.nonce,
				},
				success: function( data, status, jqXHR) {
					if ( data === 'true' ) {
						element.slideUp( 'fast', function(){
							element.remove();
						});
					}
				}
			});
			$(this).text( wprss_admin_addon_ajax.please_wait );
		}
	});

});

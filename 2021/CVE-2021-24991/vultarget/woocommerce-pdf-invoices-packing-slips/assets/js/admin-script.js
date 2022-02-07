jQuery( function( $ ) {
	$('.edit-next-number').on('click', function( event ) {
		// enable input & show save button
		$( this ).hide();
		$( this ).siblings( 'input' ).prop('disabled', false);
		$( this ).siblings( '.save-next-number.button').show();
	});

	$('.save-next-number').on('click', function( event ) {
		$input = $( this ).siblings( 'input' );
		$input.addClass('ajax-waiting');
		var data = {
			security:      $input.data('nonce'),
			action:        "wpo_wcpdf_set_next_number",
			store:         $input.data('store'),
			number:        $input.val(), 
		};

		xhr = $.ajax({
			type:		'POST',
			url:		wpo_wcpdf_admin.ajaxurl,
			data:		data,
			success:	function( response ) {
				$input.removeClass('ajax-waiting');
				$input.siblings( '.edit-next-number' ).show();
				$input.prop('disabled', 'disabled');
				$input.siblings( '.save-next-number.button').hide();
			}
		});
	});

	$("[name='wpo_wcpdf_documents_settings_invoice[display_number]']").on('change', function (event) {
		if ($(this).val() == 'order_number') {
			$(this).closest('td').find('.description').slideDown();
		} else {
			$(this).closest('td').find('.description').hide();
		}
	}).trigger('change');
});
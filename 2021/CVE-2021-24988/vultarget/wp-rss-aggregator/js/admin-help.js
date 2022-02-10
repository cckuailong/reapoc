jQuery( document ).ready( function($) {

	showError = function(error) {
		console.log('Error: ', error);
		$('#support-error').html(error);
		$('#support-error').css('color', 'red');
	};


	$('#send-message-btn').click(function() {
		var formElements = $("form :input"),
			button = $(this),
			origButtonLabel = button.attr('value'),
			params = {
				action: 'wprss_ajax_send_premium_support'
			};

		$(this).attr('value', wprss_admin_help.sending);

		formElements.each(function(id) {
			var input = $(this);

			if (input.attr('type') === 'checkbox') {
				params[input.attr('name')] = input.is(':checked');
			} else {
				params[input.attr('name')] = input.val();
			}
		});

		$.ajax({
			url: ajaxurl,
			dataType: 'json',
			data: params
		}).then(function(response, textStatus, jqXHR) {
			button.attr('value', origButtonLabel);

			// There was an error.
			if (response.error !== undefined) {
				// If the backend failed to send the message, replace the customer's message
				// with the one we appended the log and sys info to.
				if (response.message !== undefined) {
					$('[name="support-message"]').val(response.message);
				}

				showError(response.error);
			} else {
				$('#support-error').html(wprss_admin_help['sent-ok']);
				$('#support-error').css('color', 'green');

				formElements.parents('form').get(0).reset();
			}

			return response;
		}, function(error) {
			button.attr('value', origButtonLabel);

			showError(error);
		});
	});

});
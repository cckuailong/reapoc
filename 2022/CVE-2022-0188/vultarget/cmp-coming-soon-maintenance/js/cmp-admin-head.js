jQuery(document).ready(function ($) {
	// toggle cmp activation via menu bar ajax
	jQuery('#cmp-toggle-adminbar').on('click', function (e) {
		e.preventDefault();
		var security = jQuery(this).data('security');
		var data = {
			action: 'cmp_toggle_activation',
			security: security,
			payload: 'toggle_cmp_status',
		};

		$.post(cmp_ajax.ajax_url, data, function (response) {
			if (response == 'success') {
				jQuery('#cmp-toggle-adminbar').toggleClass('status-1');
				jQuery('.cmp-status input[type=radio]').prop('disabled', function (_, val) {
					return !val;
				});
				jQuery('#cmp-status').prop('checked', function (_, val) {
					return !val;
				});
			} else {
				console.log(response);
			}
		});
	});
});

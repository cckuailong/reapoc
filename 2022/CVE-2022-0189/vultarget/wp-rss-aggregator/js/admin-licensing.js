jQuery( document ).ready( function($) {
	var licenseManager = window.wprss.licenseManager;

	manage_license = function() {
		var button = $(this),
			activating = button.hasClass('button-activate-license'),
			button_orig_label = button.attr('value'),
			addon = button.attr('name').split('_', 3)[1], // Name has form "wprss_ftp_license_deactivate"; grab the "ftp" part.
			license = $('#wprss-' + addon + '-license-key').val(),
			nonce = $('#wprss_' + addon + '_license_nonce').val(),
			promise;

		button.attr('disabled', true);
		button.attr('value', activating ? wprss_admin_licensing.activating : wprss_admin_licensing.deactivating);

		if (activating) {
			promise = licenseManager.activateLicense(addon, license, nonce);
		} else {
			promise = licenseManager.deactivateLicense(addon, license, nonce);
		}

		promise.then(function( response ) {
			var td = button.parent(),
				i;

			// Inject the new HTML we got to update the UI and hook up the onClick handler.
			if (response.html !== undefined) {
				td.empty();
				td.append(response.html);
				td.children('.button-activate-license').click(manage_license);
				td.children('.button-deactivate-license').click(manage_license);
			}

			if (response.licensedAddons) {
				for (i = 0; i < response.licensedAddons.length; i++) {
					$('#wprss-license-notice-' + response.licensedAddons[i]).remove();
				}
				$('.wprss-license-notice.updated').remove();
			}

			// There was an error.
			if (response.error !== undefined) {
				console.log('There was an error: ' + response.error);
			}
		},
		function ( error ) {
			console.log('Error: ', error);
			button.attr('disabled', false);
			button.attr('value', button_orig_label);
		});

	};

    handle_license_keypress = function(event) {
        if (event.keyCode !== 13) {
            return;
        }
        var row = $(this).closest('tr'),
            nextRow = row.next(),
            btn = nextRow.find('.button-process-license');

        btn.click();

        event.preventDefault();
        event.stopPropagation();

        return false;
    };

    on_form_submit = function() {
        // Disable submission
        return false;
    };

	// This .js is only enqueued on our settings page, so just check the tab we're on.
	if ( document.location.href.search('tab=licenses_settings') > 0 ) {
		$('.button-activate-license').click(manage_license);
		$('.button-deactivate-license').click(manage_license);
		$('.submit').remove();
        // Handle form submission
        $('form').submit(on_form_submit);
        // Handle keypress on license fields
        $('.wprss-license-input').bind('keypress', handle_license_keypress);
	}

});

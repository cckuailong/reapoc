jQuery(document).ready(function($) {
	$('.deactivate a').each(function(i, ele) {
		if ($(ele).attr('href').indexOf('ip2location-country-blocker') > -1) {
			$('#ip2location-country-blocker-feedback-modal').find('a').attr('href', $(ele).attr('href'));

			$(ele).on('click', function(e) {
				e.preventDefault();

				if (!$('#ip2location-country-blocker-feedback-modal').length) {
					window.location.href = $(ele).attr('href');
					return;
				}

				$('#ip2location-country-blocker-feedback-response').html('');
				$('#ip2location-country-blocker-feedback-modal').css('display', 'block');
			});

			$('#ip2location-country-blocker-feedback-modal .ip2location-close').on('click', function() {
				$('#ip2location-country-blocker-feedback-modal').css('display', 'none');
			});

			$('input[name="ip2location-country-blocker-feedback"]').on('change', function(e) {
				if($(this).val() == 4) {
					$('#ip2location-country-blocker-feedback-other').show();
				} else {
					$('#ip2location-country-blocker-feedback-other').hide();
				}
			});

			$('#ip2location-country-blocker-submit-feedback-button').on('click', function(e) {
				e.preventDefault();

				$('#ip2location-country-blocker-feedback-response').html('');

				if (!$('input[name="ip2location-country-blocker-feedback"]:checked').length) {
					$('#ip2location-country-blocker-feedback-response').html('<div style="color:#cc0033;font-weight:800">Please select your feedback.</div>');
				} else {
					$(this).val('Loading...');
					$.post(ajaxurl, {
						action: 'ip2location_country_blocker_submit_feedback',
						feedback: $('input[name="ip2location-country-blocker-feedback"]:checked').val(),
						others: $('#ip2location-country-blocker-feedback-other').val(),
					}, function(response) {
						window.location = $(ele).attr('href');
					}).always(function() {
						window.location = $(ele).attr('href');
					});
				}
			});
		}
	});
});
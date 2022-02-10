jQuery(document).ready(function($) {
	jQuery('.bpfwp-main-dashboard-review-ask').css('display', 'block');

  jQuery(document).on('click', '.bpfwp-main-dashboard-review-ask .notice-dismiss', function(event) {

  	var params = {
			ask_review_time: '7',
			nonce: bpfwp_review_ask.nonce,
			action: 'bpfwp_hide_review_ask'
		};

		var data = jQuery.param( params );
    
    jQuery.post(ajaxurl, data, function() {});
  });

	jQuery('.bpfwp-review-ask-yes').on('click', function() {

		jQuery('.bpfwp-review-ask-feedback-text').removeClass('bpfwp-hidden');
		jQuery('.bpfwp-review-ask-starting-text').addClass('bpfwp-hidden');

		jQuery('.bpfwp-review-ask-no-thanks').removeClass('bpfwp-hidden');
		jQuery('.bpfwp-review-ask-review').removeClass('bpfwp-hidden');

		jQuery('.bpfwp-review-ask-not-really').addClass('bpfwp-hidden');
		jQuery('.bpfwp-review-ask-yes').addClass('bpfwp-hidden');

		var params = {
			ask_review_time: '7',
			nonce: bpfwp_review_ask.nonce,
			action: 'bpfwp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.bpfwp-review-ask-not-really').on('click', function() {

		jQuery('.bpfwp-review-ask-review-text').removeClass('bpfwp-hidden');
		jQuery('.bpfwp-review-ask-starting-text').addClass('bpfwp-hidden');

		jQuery('.bpfwp-review-ask-feedback-form').removeClass('bpfwp-hidden');
		jQuery('.bpfwp-review-ask-actions').addClass('bpfwp-hidden');

		var params = {
			ask_review_time: '1000',
			nonce: bpfwp_review_ask.nonce,
			action: 'bpfwp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.bpfwp-review-ask-no-thanks').on('click', function() {

		var params = {
			ask_review_time: '1000',
			nonce: bpfwp_review_ask.nonce,
			action: 'bpfwp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function() {});

    jQuery('.bpfwp-main-dashboard-review-ask').css('display', 'none');
	});

	jQuery('.bpfwp-review-ask-review').on('click', function() {

		jQuery('.bpfwp-review-ask-feedback-text').addClass('bpfwp-hidden');
		jQuery('.bpfwp-review-ask-thank-you-text').removeClass('bpfwp-hidden');

		var params = {
			ask_review_time: '1000',
			nonce: bpfwp_review_ask.nonce,
			action: 'bpfwp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.bpfwp-review-ask-send-feedback').on('click', function() {

		var feedback = jQuery('.bpfwp-review-ask-feedback-explanation textarea').val();
		var email_address = jQuery('.bpfwp-review-ask-feedback-explanation input[name="feedback_email_address"]').val();
		
    var params = {
			feedback: feedback,
			email_address: email_address,
			nonce: bpfwp_review_ask.nonce,
			action: 'bpfwp_send_feedback'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function() {});

    var params = {
			ask_review_time: '1000',
			nonce: bpfwp_review_ask.nonce,
			action: 'bpfwp_hide_review_ask'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function() {});

    jQuery('.bpfwp-review-ask-feedback-form').addClass('bpfwp-hidden');
    jQuery('.bpfwp-review-ask-review-text').addClass('bpfwp-hidden');
    jQuery('.bpfwp-review-ask-thank-you-text').removeClass('bpfwp-hidden');
	});
});
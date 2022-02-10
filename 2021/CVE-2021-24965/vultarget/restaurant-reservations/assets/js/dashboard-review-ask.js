jQuery(document).ready(function($) {
	jQuery('.rtb-main-dashboard-review-ask').css('display', 'block');

	jQuery(document).on('click', '.rtb-main-dashboard-review-ask .notice-dismiss', function(event) {
		var data = rtb_hide_review_ask_params( 7 );
		jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.rtb-review-ask-yes').on('click', function() {
		jQuery('.rtb-review-ask-feedback-text').removeClass('rtb-hidden');
		jQuery('.rtb-review-ask-starting-text').addClass('rtb-hidden');

		jQuery('.rtb-review-ask-no-thanks').removeClass('rtb-hidden');
		jQuery('.rtb-review-ask-review').removeClass('rtb-hidden');

		jQuery('.rtb-review-ask-not-really').addClass('rtb-hidden');
		jQuery('.rtb-review-ask-yes').addClass('rtb-hidden');

		var data = rtb_hide_review_ask_params( 7 );
		jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.rtb-review-ask-not-really').on('click', function() {
		jQuery('.rtb-review-ask-review-text').removeClass('rtb-hidden');
		jQuery('.rtb-review-ask-starting-text').addClass('rtb-hidden');

		jQuery('.rtb-review-ask-feedback-form').removeClass('rtb-hidden');
		jQuery('.rtb-review-ask-actions').addClass('rtb-hidden');

		var data = rtb_hide_review_ask_params( 1000 );
		jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.rtb-review-ask-no-thanks').on('click', function() {
		var data = rtb_hide_review_ask_params( 1000 );
		jQuery.post(ajaxurl, data, function() {});

		jQuery('.rtb-main-dashboard-review-ask').css('display', 'none');
	});

	jQuery('.rtb-review-ask-review').on('click', function() {
		jQuery('.rtb-review-ask-feedback-text').addClass('rtb-hidden');
		jQuery('.rtb-review-ask-thank-you-text').removeClass('rtb-hidden');

		var data = rtb_hide_review_ask_params( 1000 );
		jQuery.post(ajaxurl, data, function() {});
	});

	jQuery('.rtb-review-ask-send-feedback').on('click', function() {
		var feedback = jQuery('.rtb-review-ask-feedback-explanation textarea').val();
		var email_address = jQuery('.rtb-review-ask-feedback-explanation input[name="feedback_email_address"]').val();

		var params = {};

		params.action = 'rtb-send-feedback';
		params.nonce = rtb_review_ask.nonce;
		params.feedback = feedback;
		params.email_address = email_address;

		var data = jQuery.param( params );
		jQuery.post(ajaxurl, data, function() {});

		var data = rtb_hide_review_ask_params( 1000 );
		jQuery.post(ajaxurl, data, function() {});

		jQuery('.rtb-review-ask-feedback-form').addClass('rtb-hidden');
		jQuery('.rtb-review-ask-review-text').addClass('rtb-hidden');
		jQuery('.rtb-review-ask-thank-you-text').removeClass('rtb-hidden');
	});

	function rtb_hide_review_ask_params(ask_review_time = 7) {
		var params = {};

		params.action = 'rtb-hide-review-ask';
		params.nonce = rtb_review_ask.nonce;
		params.ask_review_time = ask_review_time;

		return jQuery.param( params );
	}
});
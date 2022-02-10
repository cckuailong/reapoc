jQuery( document ).ready( function( $ ) {
	jQuery( '.ewd-ufaq-main-dashboard-review-ask' ).css( 'display', 'block' );

  jQuery(document).on( 'click', '.ewd-ufaq-main-dashboard-review-ask .notice-dismiss', function( event ) {
    var data = 'ask_review_time=7&action=ewd_ufaq_hide_review_ask';
    jQuery.post( ajaxurl, data, function() {} );
  });

	jQuery( '.ewd-ufaq-review-ask-yes' ).on( 'click', function() {
		jQuery( '.ewd-ufaq-review-ask-feedback-text' ).removeClass( 'ewd-ufaq-hidden' );
		jQuery( '.ewd-ufaq-review-ask-starting-text' ).addClass( 'ewd-ufaq-hidden' );

		jQuery( '.ewd-ufaq-review-ask-no-thanks' ).removeClass( 'ewd-ufaq-hidden' );
		jQuery( '.ewd-ufaq-review-ask-review' ).removeClass( 'ewd-ufaq-hidden' );

		jQuery( '.ewd-ufaq-review-ask-not-really' ).addClass( 'ewd-ufaq-hidden' );
		jQuery( '.ewd-ufaq-review-ask-yes' ).addClass( 'ewd-ufaq-hidden' );

		var data = 'ask_review_time=7&action=ewd_ufaq_hide_review_ask';
        jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-ufaq-review-ask-not-really' ).on( 'click', function() {
		jQuery( '.ewd-ufaq-review-ask-review-text' ).removeClass( 'ewd-ufaq-hidden' );
		jQuery( '.ewd-ufaq-review-ask-starting-text' ).addClass( 'ewd-ufaq-hidden' );

		jQuery( '.ewd-ufaq-review-ask-feedback-form' ).removeClass( 'ewd-ufaq-hidden' );
		jQuery( '.ewd-ufaq-review-ask-actions' ).addClass( 'ewd-ufaq-hidden' );

		var data = 'ask_review_time=1000&action=ewd_ufaq_hide_review_ask';
        jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-ufaq-review-ask-no-thanks' ).on( 'click', function() {
		var data = 'ask_review_time=1000&action=ewd_ufaq_hide_review_ask';
        jQuery.post( ajaxurl, data, function() {} );

        jQuery( '.ewd-ufaq-main-dashboard-review-ask' ).css( 'display', 'none' );
	});

	jQuery( '.ewd-ufaq-review-ask-review' ).on( 'click', function() {
		jQuery( '.ewd-ufaq-review-ask-feedback-text' ).addClass( 'ewd-ufaq-hidden' );
		jQuery( '.ewd-ufaq-review-ask-thank-you-text' ).removeClass( 'ewd-ufaq-hidden' );

		var data = 'ask_review_time=1000&action=ewd_ufaq_hide_review_ask';
        jQuery.post( ajaxurl, data, function() {} );
	});

	jQuery( '.ewd-ufaq-review-ask-send-feedback' ).on( 'click', function() {
		var feedback = jQuery( '.ewd-ufaq-review-ask-feedback-explanation textarea' ).val();
		var email_address = jQuery( '.ewd-ufaq-review-ask-feedback-explanation input[name="feedback_email_address"]' ).val();
		var data = 'feedback=' + feedback + '&email_address=' + email_address + '&action=ewd_ufaq_send_feedback';
        jQuery.post( ajaxurl, data, function() {} );

        var data = 'ask_review_time=1000&action=ewd_ufaq_hide_review_ask';
        jQuery.post( ajaxurl, data, function() {} );

        jQuery( '.ewd-ufaq-review-ask-feedback-form' ).addClass( 'ewd-ufaq-hidden' );
        jQuery( '.ewd-ufaq-review-ask-review-text' ).addClass( 'ewd-ufaq-hidden' );
        jQuery( '.ewd-ufaq-review-ask-thank-you-text' ).removeClass( 'ewd-ufaq-hidden' );
	});
});
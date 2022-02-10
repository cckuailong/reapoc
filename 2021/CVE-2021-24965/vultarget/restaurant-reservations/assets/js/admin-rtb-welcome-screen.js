jQuery(document).ready(function() {
	jQuery('.rtb-welcome-screen-box h2').on('click', function() {
		var section = jQuery(this).parent().data('screen');
		rtb_toggle_section(section);
	});

	jQuery('.rtb-welcome-screen-next-button').on('click', function() {
		var section = jQuery(this).data('nextaction');
		rtb_toggle_section(section);
	});

	jQuery('.rtb-welcome-screen-previous-button').on('click', function() {
		var section = jQuery(this).data('previousaction');
		rtb_toggle_section(section);
	});

	jQuery('.rtb-welcome-screen-add-reservations-page-button').on('click', function() {
		var reservations_page_title = jQuery('.rtb-welcome-screen-add-reservations-page-name input').val();

		var params = {};

		params.action = 'rtb-welcome-add-menu-page';
		params.nonce = rtb_getting_started.nonce;
		params.reservations_page_title = reservations_page_title;

		var data = jQuery.param( params );
		jQuery.post(ajaxurl, data, function(response) {});

		var section = jQuery(this).data('nextaction');
		rtb_toggle_section(section);
	});

	jQuery('.rtb-welcome-screen-save-schedule-open-button').on('click', function() {

		var schedule_open = [];
 
 		jQuery('.sap-scheduler-rule').each(function() {
			var weekdays ={};

			jQuery(this).find('.sap-scheduler-weekdays input[type="checkbox"]').each(function() { 
				if ( jQuery(this).is(':checked') ) { weekdays[jQuery(this).data('day')] = "1" ; }
			}); 

			var start = jQuery(this).find('.sap-scheduler-time-input .start input').first().val();
			var end = jQuery(this).find('.sap-scheduler-time-input .end input').first().val();

			schedule_open.push({'weekdays': weekdays, 'time': {'start': start, 'end': end }});
		}); 

		var params = {};

		params.action = 'rtb-welcome-set-schedule';
		params.nonce = rtb_getting_started.nonce;
		params.schedule_open = schedule_open;

		var data = jQuery.param( params );
		jQuery.post(ajaxurl, data, function(response) {

			jQuery( '.rtb-welcome-screen-save-schedule-open-button' ).after( '<div class="rtb-save-message"><div class="rtb-save-message-inside">Schedule has been saved.</div></div>' );
			jQuery( '.rtb-save-message' ).delay( 2000 ).fadeOut( 400, function() { jQuery( '.rtb-save-message' ).remove(); } );
		});
	});

	jQuery('.rtb-welcome-screen-save-options-button').on('click', function() {
		var party_size_min = jQuery('select[name="min-party-size"]').val();
		var party_size = jQuery('select[name="party-size"]').val();
		var early_bookings = jQuery('select[name="early-bookings"]').val();
		var late_bookings = jQuery('select[name="late-bookings"]').val();
		var time_interval = jQuery('select[name="time-interval"]').val();

		var params = {};

		params.action = 'rtb-welcome-set-options';
		params.nonce  = rtb_getting_started.nonce;
		params.party_size_min = party_size_min;
		params.party_size     = party_size;
		params.early_bookings = early_bookings;
		params.late_bookings  = late_bookings;
		params.time_interval  = time_interval;

		var data = jQuery.param( params );
		jQuery.post(ajaxurl, data, function(response) {

			jQuery( '.rtb-welcome-screen-save-options-button' ).after( '<div class="rtb-save-message"><div class="rtb-save-message-inside">Options have been saved.</div></div>' );
			jQuery( '.rtb-save-message' ).delay( 2000 ).fadeOut( 400, function() { jQuery( '.rtb-save-message' ).remove(); } );
		});
	});
});

function rtb_toggle_section(page) {
	jQuery('.rtb-welcome-screen-box').removeClass('rtb-welcome-screen-open');
	jQuery('.rtb-welcome-screen-' + page).addClass('rtb-welcome-screen-open');
}
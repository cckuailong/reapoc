jQuery(document).ready(function() {
	jQuery('.bpfwp-welcome-screen-box h2').on('click', function() {
		var section = jQuery(this).parent().data('screen');
		bpfwp_toggle_section(section);
	});

	jQuery('.bpfwp-welcome-screen-next-button').on('click', function() {
		var section = jQuery(this).data('nextaction');
		bpfwp_toggle_section(section);
	});

	jQuery('.bpfwp-welcome-screen-previous-button').on('click', function() {
		var section = jQuery(this).data('previousaction');
		bpfwp_toggle_section(section);
	});

	jQuery('.bpfwp-welcome-screen-add-contact-page-button').on('click', function() {

		var contact_page_title = jQuery('.bpfwp-welcome-screen-add-contact-page-name input').val();

		var params = {
			contact_page_title: contact_page_title,
			nonce: bpfwp_getting_started.nonce,
			action: 'bpfwp_welcome_add_contact_page'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function(response) {});

		bpfwp_toggle_section('set_contact_info');
	});

	jQuery('.bpfwp-welcome-screen-set-contact-information-button').on('click', function() {

		jQuery('.bpfwp-welcome-screen-show-created-sections').show();

		var schema_type = jQuery('select[name="bpfwp-schema-type"]').val();
		var name = jQuery('input[name="bpfwp-contact-name"]').val();
		var address = jQuery('textarea[name="bpfwp-contact-address"]').val();
		var phone = jQuery('input[name="bpfwp-contact-phone"]').val();
		var email = jQuery('input[name="bpfwp-contact-email"]').val();

		jQuery('input[name="bpfwp-contact-name"]').val('');
		jQuery('textarea[name="bpfwp-contact-address"]').val('');
		jQuery('input[name="bpfwp-contact-phone"]').val('');
		jQuery('input[name="bpfwp-contact-email"]').val('');

		var params = {
			schema_type: schema_type,
			name: name,
			address: address,
			phone: phone,
			email: email,
			nonce: bpfwp_getting_started.nonce,
			action: 'bpfwp_welcome_set_contact_information'
		};

		var data = jQuery.param( params );

		jQuery.post(ajaxurl, data, function(response) {});

		bpfwp_toggle_section('set_hours');
	});

	jQuery('.bpfwp-welcome-screen-set-hours-button').on('click', function() {
		
		var form_data = jQuery('.bpfwp-welcome-screen-form').serialize(); 

		var data = form_data + '&nonce=' + bpfwp_getting_started.nonce + '&action=bpfwp_welcome_set_opening_hours';

		jQuery.post(ajaxurl, data, function(response) {});

		bpfwp_toggle_section('create_schema');
	});

});

function bpfwp_toggle_section(page) {
	jQuery('.bpfwp-welcome-screen-box').removeClass('bpfwp-welcome-screen-open');
	jQuery('.bpfwp-welcome-screen-' + page).addClass('bpfwp-welcome-screen-open');
}
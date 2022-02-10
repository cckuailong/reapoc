jQuery(document).ready(function($) {

 	jQuery("#deactivate_license").click(function(){

		$('.notice').replaceWith("<div class='notice notice-info is-dismissible'><p>Your license has been deactivated.</p></div>");
		$('#license_email').val('');
		$('#license_key').val('');

		jQuery.ajax({
                	method: "POST",
                       	url: ajaxurl,
                       	data: { 'action': 'woosea_deactivate_license' }
                })
                .done(function( data ) {
                	data = JSON.parse( data );	
		})
                .fail(function( data ) {
                	console.log('Failed AJAX Call :( /// Return Data: ' + data);
                });
	});

 	jQuery("#checklicense").click(function(){

		var temp = location.host.split('.').reverse();
		var root_domain = $(location).attr('hostname');
		var license_email = $('#license-email').val();
		var license_key = $('#license-key').val();

		jQuery.ajax({
    			url: 'https://www.adtribes.io/check/license.php?key=' + license_key + '&email=' + license_email + '&domain=' + root_domain + '&version=3.1.5',
			jsonp: 'callback',
    			dataType: 'jsonp',
			type: 'GET',
			success: function( licenseData ) {
	
				var license_valid = licenseData.valid;
				if (license_valid == "true"){
					$('.notice').replaceWith("<div class='notice notice-success is-dismissible'><p>Thank you for registering your Elite product, your license has been activated. Please do not hesitate to contact us whenever you have questions (support@adtribes.io).</p></div>");
				} else {
					$('.notice').replaceWith("<div class='notice notice-error is-dismissible'><p>Sorry, this does not seem to be a valid or active license key and email. Please feel free to contact us at support@adtribes.io whenever you have questions with regards to your license.</p></div>");
				}

				var license_created = licenseData.created;
				var message = licenseData.message;
				var message_type = licenseData.message_type;
				var license_email = licenseData.license_email;
				var license_key = licenseData.license_key;			
				var notice = licenseData.notice;			
				
				jQuery.ajax({
                        		method: "POST",
                        		url: ajaxurl,
                        		data: { 'action': 'woosea_register_license', 'notice': notice, 'message_type': message_type, 'license_email': license_email, 'license_key': license_key, 'license_valid': license_valid, 'license_created': license_created, 'message': message }
                		})
                		.done(function( data ) {
                        		data = JSON.parse( data );	
				})
                		.fail(function( data ) {
                        		console.log('Failed AJAX Call :( /// Return Data: ' + data);
                		});
				console.log( licenseData );
			}		
		})
	});

	// On succes this call will return yes/no in jsonp for the domain name check. It will also return the license key. This key needs to correlate with the one user entered.
});

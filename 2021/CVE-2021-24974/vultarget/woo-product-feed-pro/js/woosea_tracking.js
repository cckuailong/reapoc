jQuery(document).ready(function($) {
	var path = $(location).attr('href');
	var currentTime = new Date();

	if (path.toLowerCase().indexOf("order-received") >= 0){
        	if (window.localStorage) {

			// Check if concerns in-session conversion
			var adTribesID = localStorage.getItem("adTribesID");
			var utm_source = localStorage.getItem("utm_source");
			var utm_campaign = localStorage.getItem("utm_campaign");
			var utm_medium = localStorage.getItem("utm_medium");
			var utm_term = localStorage.getItem("utm_term");

       			jQuery.ajax({
              			method: "POST",
              			url: ajaxurl,
              			data: { 'action': 'woosea_track_conversion', 'utm_source': utm_source, 'utm_campaign': utm_campaign, 'utm_medium': utm_medium, 'utm_term': utm_term, 'adTribesID': adTribesID }
              		})
			.done(function( data ) {
				data = JSON.parse( data );

				if (data.conversion_saved == "yes"){
					// Conversion has been saved so we can clear our local storage
					localStorage.removeItem("adTribesID");
					localStorage.removeItem("utm_source");
					localStorage.removeItem("utm_campaign");
					localStorage.removeItem("utm_medium");
					localStorage.removeItem("utm_term");
				}
			})
			.fail(function( data ) {
        			console.log('Failed sending conversion data via AJAX Call :( /// Return Data: ' + data);
        		});
		}
	} else {
		if (path.toLowerCase().indexOf("adtribesid") >= 0){
        		if (window.localStorage) {
        			// First make sure older localstorage settings are empty   
				localStorage.removeItem("adTribesID");
				localStorage.removeItem("utm_source");
				localStorage.removeItem("utm_campaign");
				localStorage.removeItem("utm_medium");
				localStorage.removeItem("utm_term");

				var splitted_path = path.split('?');
				var parameter_parts = splitted_path[1].split('&');

				for(i=0;i<parameter_parts.length;i++){
					// Save UTM's in local storage
					if (parameter_parts[i].toLowerCase().indexOf("utm_") >= 0){
						var utm_details = parameter_parts[i].split('=');
						var utm_key = utm_details[0];
						var utm_value = utm_details[1];	
						var clean_value = utm_value.replace("%20", " ");
	
						localStorage.setItem(utm_key, clean_value);
					} else if (parameter_parts[i].toLowerCase().indexOf("adtribesid") >= 0){
						var adtribes_details = parameter_parts[i].split('=');
						var adtribes_key = adtribes_details[0];
						var adtribes_value = adtribes_details[1];	
						var clean_adtribes_value = adtribes_value.replace("%20", " ");

						// Save AdTribesID in local storage
						localStorage.setItem(adtribes_key, clean_adtribes_value);

						// Save AdTribesID in cookie too
			       			jQuery.ajax({
              						method: "POST",
              						url: ajaxurl,
              						data: { 'action': 'woosea_set_cookie', 'adTribesID': clean_adtribes_value }
              					})
						.done(function( data ) {
							data = JSON.parse( data );
						})
						.fail(function( data ) {
        						console.log('Failed setting cookies via AJAX Call :( /// Return Data: ' + data);
        					});
					}
				}
        		}
		}
	}
});

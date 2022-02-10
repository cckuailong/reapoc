(function( $ ) { 'use strict';
	/* https://autocomplete.trevoreyre.com/#/javascript-component?id=getresultvalue */
	new Autocomplete( '#mc-locations-autocomplete', {
		search: input => {
			const url = mclocations.ajaxurl;
			return new Promise( resolve => {
				if (input.length < 3) {
					return resolve([])
				}

				var data = new FormData();
				data.append( 'action', mclocations.action );
				data.append( 'security', mclocations.security );
				data.append( 'data', input );
				const response = fetch(url, {
					method: 'POST',
					credentials: 'same-origin',
					body: data
				}).then(response => response.json())
				.then(data => {
					resolve(data.response)
				})
			})
		},
		onSubmit: result => {
			var location_field = document.getElementById( 'mc_event_location_value' );

			location_field.value = result.location_id;
			$( location_field ).trigger( 'change' );
		},
		getResultValue: result => result.location_label
	});

}(jQuery));
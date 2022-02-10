jQuery(function($) {
	$(document).ready(function() {
	    $('.mycred_bank_id_select2').select2({
			minimumInputLength: 1,
			placeholder: "Select a user",
	    	ajax: {
    			url: ajaxurl,
    			dataType: 'json',
    			delay: 1000,
    			data: function (params) {
      				return {
        				search: params.term, 
        				page: params.page || 1,
        				action: 'get_bank_accounts'
      				};
    			},
	    		processResults: function( data ) {
	    			console.log(data);
					var options = [];
					if ( data.users ) {

						$.each( data.users, function( index, user ) {
							options.push( { id: user.ID, text: '#'+user.ID+' - '+user.display_name+' ('+user.user_email+')' } );
						});
	
					}
					return {
						results: options, 
						pagination: {
                            'more': data.more
                        }
					};
				},
				cache: true
			}
	    });
	});
} );
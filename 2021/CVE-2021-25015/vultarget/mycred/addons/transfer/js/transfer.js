/**
 * myCRED Transfer jQuery
 * Handles transfer requests and autocomplete of recipient search.
 *
 * @requires jQuery
 * @requires jQuery UI
 * @requires jQuery Autocomplete
 * @since 0.1
 * @version 1.4
 */
jQuery(function($){
	// Transfer function
	var transfer_creds = function( submitted_form, label ) {
		$.ajax({
			type : "POST",
			data : {
				action    : 'mycred-transfer-creds',
				form      : submitted_form,
				token     : myCRED.token
			},
			dataType : "JSON",
			url : myCRED.ajaxurl,
			// Before we start
			beforeSend : function() {
				// Prevent users from clicking multiple times
				$( '.mycred-click' ).val( myCRED.working );
				$( '.mycred-click' ).attr( 'disabled', 'disabled' );
			},
			// On Successful Communication
			success    : function( data ) {
				$( '.mycred-click' ).val( label );
				$( '.mycred-click' ).removeAttr( 'disabled' );

				// Error
				if ( myCRED[ data ] !== undefined )
					alert( myCRED[ data ] );

				// Completed
				else if ( data == 'ok' ) {
					alert( myCRED.completed );

					// If reload is set
					if ( myCRED.reload == '1' )
						location.reload();
				}

				// WP Nonce no longer valid / we have been logged out
				else if ( data == '-1' || data == 0 )
					location.reload();

				// All else
				else {
					$('.mycred-click').attr( 'value', data );
					if ( myCRED.reload == '1' )
						location.reload();
				}
			}
		});
	};
	
	// Autocomplete
	// @api http://api.jqueryui.com/autocomplete/
	var cache = {};
	$( 'input.mycred-autofill' ).autocomplete({
		minLength: 2,
		source: function( request, response ) {
			var term = request.term;
			if ( term in cache ) {
				response( cache[ term ] );
				return;
			}
			
			var send = {
				action : "mycred-autocomplete",
				token  : myCRED.atoken,
				string : request
			};
			$.getJSON( myCRED.ajaxurl, send, function( data, status, xhr ) {
				cache[ term ] = data;
				// Debug - uncomment to use
				//console.log( data );
				//console.log( status );
				response( data );
			});
		},
		messages: {
			noResults: '',
			results: function() {}
		},
		appendTo : 'div.transfer-to'
	});
	
	// Attempt Transfer
	$( '.mycred-click' ).click(function(){

		// The form
		var the_form = $(this).parent().parent().parent();

		// To:
		var receipient = $(this).parent().prev().children( 'div' ).children( 'input' ).val();

		// Amount:
		var creds = $(this).prev().children( 'input[name=mycred-transfer-amount]' ).val();

		// If elements are not emepty attempt transfer
		if ( receipient != '' && creds != '' ) {
			transfer_creds( the_form.serialize(), $(this).val() );
		}

	});
});
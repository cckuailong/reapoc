/**
 * myCRED Transfer jQuery
 * Handles transfer requests and autocomplete of recipient search.
 *
 * @requires jQuery
 * @requires jQuery UI
 * @requires jQuery Autocomplete
 * @since 0.1
 * @version 1.5.2
 */
(function($) {

	var mycred_transfer_cache  = {};

	// Autocomplete
	// @api http://api.jqueryui.com/autocomplete/
	var mycred_transfer_autofill = $( 'input.mycred-autofill' ).autocomplete({

		minLength : 2,
		source    : function( request, response ) {

			var term = request.term;
			if ( term in mycred_transfer_cache ) {
				response( mycred_transfer_cache[ term ] );
				return;
			}
			
			var send = {
				action : "mycred-autocomplete",
				token  : myCREDTransfer.token,
				string : request
			};

			$.getJSON( myCREDTransfer.ajaxurl, send, function( data, status, xhr ) {
				mycred_transfer_cache[ term ] = data;
				response( data );
			});

		},
		messages: {
			noResults : '',
			results   : function() {}
		},
		position: { my : "right top", at: "right bottom" }

	});

	$( 'input.mycred-autofill' ).click(function(){

		if ( myCREDTransfer.autofill == 'none' ) return false;

		var formfieldid = $(this).data( 'form' );
		mycred_transfer_autofill.autocomplete( "option", "appendTo", '#mycred-transfer-form-' + formfieldid + ' .select-recipient-wrapper' );
		console.log( formfieldid );

	});

	// Transfer form submissions
	// @since 1.6.3
	$( 'html body' ).on( 'submit', 'form.mycred-transfer-form', function(e){

		console.log( 'new transfer' );

		var transferform = $(this);
		var formrefid    = transferform.data( 'ref' );
		var formid       = '#mycred-transfer-form-' + formrefid;
		var submitbutton = $( formid + ' button.mycred-submit-transfer' );
		var buttonlabel  = submitbutton.val();

		e.preventDefault();

		$.ajax({
			type       : "POST",
			data       : {
				action			: 'mycred-new-transfer',
				form			: transferform.serialize()
			},
			dataType   : "JSON",
			url        : myCREDTransfer.ajaxurl,
			beforeSend : function() {

				$( formid + ' input.form-control' ).each(function(index){
					$(this).attr( 'disabled', 'disabled' );
				});

				submitbutton.attr( 'disabled', 'disabled' );
				submitbutton.val( myCREDTransfer.working );

			},
			success    : function( response ) {
				console.log( response );

				$( formid + ' input.form-control' ).each(function(index){
					$(this).removeAttr( 'disabled' );
				});

				submitbutton.removeAttr( 'disabled', 'disabled' );
				submitbutton.val( buttonlabel );

				if ( response.success !== undefined ) {

					if ( response.success ) {

						// Allow customizations to present custom success messages
						if ( response.data.message !== undefined && response.data.message != '' )
							alert( response.data.message );
						else
							alert( myCREDTransfer.completed );

						if ( $( response.data.css ) !== undefined )
							$( response.data.css ).empty().html( response.data.balance );

						// Reset form
						$( formid + ' input.form-control' ).each(function(index){
							$(this).val( '' );
						});

						$( formid + ' select' ).each(function(index){
							var selecteditem = $(this).find( ':selected' );
							if ( selecteditem !== undefined )
								selecteditem.removeAttr( 'selected' );
						});

						// If we require reload after submission, do so now
						if ( myCREDTransfer.reload == '1' ) location.reload();

					}

					else if ( myCREDTransfer[ response.data ] !== undefined )
						alert( myCREDTransfer[ response.data ] );

				}

			}

		});

		return false;

	});

})( jQuery );
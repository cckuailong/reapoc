/**
 * buyCRED Checkout
 * @since 1.8
 * @version 1.0
 */
jQuery(function($){

	var buyCREDcheckout = $( '#buycred-checkout-wrapper' );
	var buyCREDform     = $( '#buycred-checkout-wrapper #buycred-checkout-form' );
	var buyCREDcancel   = $( '#cancel-checkout-wrapper' );
	var activeForm;

	var buycred_send_call = function( formdata ) {

		$.ajax({
			type     : "POST",
			data     : formdata,
			dataType : "JSON",
			url      : buyCRED.ajaxurl,
			success  : function( response ) {

				console.log(response);

				if ( typeof response.validationFail === "undefined" ) {
					buyCREDform.slideUp(function(){
						buyCREDform.empty().append( response ).slideDown();
					});
				} 
				else {
					buyCREDform.slideUp(function(){
						buyCREDform.empty().append( '<div class="padded error">' + response.errors[0] + '</div>' ).slideDown();
					});
					buyCREDcancel.addClass( 'on' );
				}

			}
		});

	};

	$(document).ready(function(){

		// Forms rendered by mycred_buy
		$( 'body' ).on( 'click', '.mycred-buy-link', function(e){

			if ( buyCRED.checkout == 'popup' ) {

				e.preventDefault();

				buyCREDcancel.removeClass( 'on' );
				buyCREDcheckout.addClass( 'open' );

				activeForm             = $(this);

				var targeturl          = $(this).attr( 'href' );
				var formdata           = JSON.parse( '{"' + decodeURI( targeturl ).replace(/"/g, '\\"').replace(/&/g, '","').replace(/=/g,'":"') + '"}' );
				formdata['ajax']       = 1;
				formdata['mycred_buy'] = $(this).data( 'gateway' );
console.log( formdata );
				buycred_send_call( formdata );

			}

		});

		// Forms rendered by mycred_buy_form
		$( 'body' ).on( 'submit', '.myCRED-buy-form', function(e){

			if ( buyCRED.checkout == 'popup' ) {

				e.preventDefault();

				buyCREDcancel.removeClass( 'on' );
				buyCREDcheckout.addClass( 'open' );

				activeForm   = $(this);

				var formdata = { ajax : 1 };
				var fields   = $(this).find( 'input' );
				var selects  = $(this).find( 'select' );

				fields.each(function(index, item){
					var element = $(this);
					if ( element.attr( 'name' ) !== undefined ) {
						formdata[ element.attr( 'name' ) ] = element.val();
					}
				});

				selects.each(function(index, item){
					var element = $(this);
					var option  = element.find( ':selected' );
					if ( option.val() !== undefined ) {
						formdata[ element.attr( 'name' ) ] = option.val();
					}
				});

				buycred_send_call( formdata );

			}

		});

		$( '#buycred-checkout-wrapper' ).on( 'click', '.checkout-footer button', function(e){

			var buttontype  = $(this).data( 'act' );
			var buttonvalue = $(this).data( 'value' );

			if ( buttontype == 'redirect' ) {

				$(this).attr( 'disabled', 'disabled' ).html( buyCRED.redirecting );
				
				if ( $(this).hasClass('bitpay') ) {
					window.location = buttonvalue;
				}
				else
				{
					buyCREDform.attr( 'action', buttonvalue );
					buyCREDform.submit();
				}

			}

			else if ( buttontype == 'toggle' ) {

				var toggleelement = $( '#' + buttonvalue );
				toggleelement.prev().slideUp(function(){
					toggleelement.slideDown();
				});

				$(this).parent().slideUp();
				buyCREDcancel.addClass( 'on' );

			}

			$( '#buycred-checkout-wrapper .cancel a' ).slideUp();

		});

		$( '#buycred-checkout-page' ).on( 'click', '.checkout-footer button', function(){

			var pageform    = $( '#buycred-checkout-page form' );
			var buttontype  = $(this).data( 'act' );
			var buttonvalue = $(this).data( 'value' );

			if ( buttontype == 'redirect' ) {

				if ( $(this).hasClass('bitpay') ) { 
					window.location = buttonvalue;
				}
				else {
					pageform.attr( 'action', buttonvalue );
					pageform.submit();
				}

				$(this).attr( 'disabled', 'disabled' ).html( buyCRED.redirecting );

			}

			else if ( buttontype == 'toggle' ) {

				var toggleelement = $( '#' + buttonvalue );
				toggleelement.prev().slideUp(function(){
					toggleelement.slideDown();
				});

				$(this).parent().slideUp();

			}

		});

		buyCREDcancel.on( 'click', function(){

			// Reset the form that was originally submitted
			var formfields = activeForm.find( 'input.form-control' );
			formfields.each(function(){
				$(this).val( '' );
			});

			$(this).removeClass( 'on' );
			buyCREDcheckout.removeClass( 'open' );
			buyCREDcancel.removeClass( 'open' );
			buyCREDform.attr( 'action', '' ).empty().append( '<div class="loading-indicator"></div>' );

		});

		$( '#buycred-checkout-wrapper' ).on( 'click', '.cancel a', function(e){

			$( '#buycred-checkout-wrapper form .checkout-footer' ).slideUp();

		});

		$( document ).on( 'change', '.mycred-change-pointtypes', function(){
			
			var value = $(this).find('option:selected').text();
			var label = $('.mycred-point-type').html(value);

		});

	});

});
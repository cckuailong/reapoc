/**
 * myCRED
 * Handle mycred_send shortcode buttons.
 * @since 0.1
 * @version 1.3
 */
jQuery(function($) {

	$( 'button.mycred-send-points-button' ).click(function(){

		var button        = $(this);
		var originallabel = button.text();

		$.ajax({
			type : "POST",
			data : {
				action    : 'mycred-send-points',
				amount    : button.data( 'amount' ),
				recipient : button.data( 'to' ),
				log       : button.data( 'log' ),
				reference : button.data( 'ref' ),
				type      : button.data( 'type' ),
				token     : myCREDsend.token
			},
			dataType   : "JSON",
			url        : myCREDsend.ajaxurl,
			beforeSend : function() {

				button.attr( 'disabled', 'disabled' ).text( myCREDsend.working );

			},
			success    : function( data ) {

				if ( data == 'done' ) {

					button.text( myCREDsend.done );
					setTimeout( function(){
						button.removeAttr( 'disabled' ).text( originallabel );
					}, 2000 );

				}

				else if ( data == 'zero' ) {

					button.text( myCREDsend.done );
					setTimeout( function(){

						$( 'button.mycred-send-points-button' ).each(function(){
							$(this).attr( 'disabled', 'disabled' ).hide();
						});

					}, 2000 );

				}
				else {

					button.text( myCREDsend.error );
					setTimeout( function(){
						button.removeAttr( 'disabled' ).text( originallabel );
					}, 2000 );

				}

			}
		});

	});

});
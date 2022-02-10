/**
 * myCRED Sell Content
 * @since 1.1
 * @version 1.0
 */
jQuery(function($) {
	var mycred_buy_content = function( button, label ) {
		wrapper = button.parents( 'div.mycred-content-forsale' );
		$.ajax({
			type : "POST",
			data : {
				action    : 'mycred-buy-content',
				postid    : button.attr( 'data-id' ),
				token     : myCREDsell.token
			},
			dataType : "HTML",
			url : myCREDsell.ajaxurl,
			// Before we start
			beforeSend : function() {
				button.attr( 'value', myCREDsell.working );
				button.attr( 'disabled', 'disabled' );
				wrapper.slideUp();
			},
			// On Successful Communication
			success    : function( data ) {
				wrapper.empty();
				wrapper.append( data );
				wrapper.slideDown();
			},
			// Error (sent to console)
			error      : function( jqXHR, textStatus, errorThrown ) {
				button.attr( 'value', 'Upps!' );
				button.removeAttr( 'disabled' );
				wrapper.slideDown();
				// Debug - uncomment to use
				console.log( jqXHR );
				console.log( textStatus );
				console.log( errorThrown );
			}
		});
	};
	
	$('.mycred-sell-this-button').click(function(){
		mycred_buy_content( $(this), $(this).attr( 'value' ) );
	});
});
/**
 * myCRED Points for Link Clicks jQuery Scripts
 * @contributors Kevin Reeves
 * @since 0.1
 * @version 1.7.1
 */
jQuery(function($) {

	$( '.mycred-points-link' ).click(function(){

		var mycredlink      = $(this);
		var linkdestination = mycredlink.attr( 'href' );
		var target          = mycredlink.attr( 'target' );
		if ( typeof target === 'undefined' ) {
			target = 'self';
		}

		$.ajax({
			type     : "POST",
			data     : {
				action : 'mycred-click-points',
				url    : linkdestination,
				token  : myCREDlink.token,
				etitle : mycredlink.text(),
				ctype  : mycredlink.attr( 'data-type' ),
				key    : mycredlink.attr( 'data-token' )
			},
			dataType : "JSON",
			url      : myCREDlink.ajaxurl,
			success  : function( response ) {
				console.log( response );
				if ( target == 'self' || target == '_self' )
					window.location.href = linkdestination;
			}
		});

		if ( target == 'self' || target == '_self' ) return false;

	});

});
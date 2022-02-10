/**
 * Accordion
 * @since 0.1
 * @since 2.3 Added open in new tab
 * @version 1.1
 */
jQuery(function($) {

	var active_box = false;
	if ( typeof myCRED !== 'undefined' ) {
		if ( myCRED.active != '-1' )
			active_box = parseInt( myCRED.active, 10 );
	}

	$( "#accordion" ).accordion({ collapsible: true, header: "h4", heightStyle: "content", active: active_box });

	$( document ).on( 'click', '.buycred-cashcred-more-tab-btn', function(){
		var $url = $( this ).data( 'url' );
		window.open( $url, '_blank');
	} ) 
});


/**
 * myCRED Rank Tweaks
 * @since 1.6
 * @version 1.0.1
 */
(function($) {

	// When the page has loaded, append the point type to the "Add New" button url
	// This will make sure the new rank is created for the correct point type.
	$( document ).ready(function() {

		var newurl = $( 'a.add-new-h2' ).attr( 'href' );
		newurl = newurl + '&ctype=' + myCRED_Ranks.rank_ctype;
		$( 'a.add-new-h2' ).attr( 'href', newurl );

	});

})( jQuery );
/**
 * Javascript functions for Opening Hours component
 *
 * @package Simple Admin Pages
 */

jQuery(document).ready(function ($) {

	/**
	 * Opening Hours
	 ***************/

	/**
	 * Update the name of each day when the select option is changed
	 */
	$( '.sap-opening-hours-day' ).change( function() {
		$( $(this).data( 'target' ) ).val( $(this).children( 'option:selected' ).data( 'name' ) );
	});

});

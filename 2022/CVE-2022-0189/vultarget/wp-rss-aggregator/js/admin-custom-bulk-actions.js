/**
 * Adds and manages custom bulk actions for the Feed Sources page.
 * 
 * @since 2.5
 */
(function($, wprss_admin_bulk){
	
	$(document).ready( function(){
		var bulk_actions_select = $( 'select#bulk-action-selector-top, select#bulk-action-selector-bottom' );
		var bulk_actions_trash = bulk_actions_select.find( "option[value='trash']" );

		bulk_actions_select.find( 'option[value="edit"]' ).remove();
		$( '<option>' ).attr( 'value', 'activate' ).text( wprss_admin_bulk.activate ).insertBefore( bulk_actions_trash );
		$( '<option>' ).attr( 'value', 'pause' ).text( wprss_admin_bulk.pause ).insertBefore( bulk_actions_trash );
	});


})(jQuery, wprss_admin_bulk);

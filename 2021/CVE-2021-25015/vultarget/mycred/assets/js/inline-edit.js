/**
 * myCRED Inline Edit
 * @since 1.2
 * @version 1.2
 */
jQuery(function($) {

	var username = '';
	var user_id = '';
	var current = '';
	var current_el = '';

	/**
	 * Setup Points Editor Modal
	 */
	$(document).ready( function() {
		$('#edit-mycred-balance').dialog({
			dialogClass : 'mycred-update-balance',
			draggable   : true,
			autoOpen    : false,
			title       : myCREDedit.title,
			closeText   : myCREDedit.close,
			modal       : true,
			width       : 500,
			height      : 'auto',
			resizable   : false,
			show        : {
				effect     : 'slide',
				direction  : 'up',
				duration   : 250
			},
			hide        : {
				effect     : 'slide',
				direction  : 'up',
				duration   : 250
			}
		});
	});

	/**
	 * Edit Points Trigger
	 */
	$( '.mycred-open-points-editor' ).click( function() {
		
		$( '#edit-mycred-balance' ).dialog( 'open' );
		
		$( '#edit-mycred-balance #mycred-username' ).empty().text( $(this).attr( 'data-username' ) );
		
		$( '#edit-mycred-balance #mycred-userid' ).empty().text( $(this).attr( 'data-userid' ) );
		
		$( '#edit-mycred-balance #mycred-current' ).empty().text( $(this).attr( 'data-current' ) );
		
		$( '#mycred-update-users-balance-type' ).val( $(this).attr( 'data-type' ) );
	});

	/**
	 * Update Balance AJAX Caller
	 */
	$( '#mycred-update-users-balance-submit' ).click( function() {
		var button = $(this);
		var label = button.val();
		var current_el = $( '#edit-mycred-balance #mycred-current' );
		var user_id = $( '#edit-mycred-balance #mycred-userid' ).text();
		var amount_el = $( 'input#mycred-update-users-balance-amount' );
		var entry_el = $( 'input#mycred-update-users-balance-entry' );
		var type_el = $( '#mycred-update-users-balance-type' );
		
		$.ajax({
			type       : "POST",
			data       : {
				action    : 'mycred-inline-edit-users-balance',
				token     : $( 'input#mycred-update-users-balance-token' ).val(),
				user      : user_id,
				amount    : amount_el.val(),
				entry     : entry_el.val(),
				type      : type_el.val()
			},
			dataType   : "JSON",
			url        : myCREDedit.ajaxurl,
			// Before we start
			beforeSend : function() {
				current_el.removeClass( 'done' );
				entry_el.removeClass( 'error' );
				amount_el.removeClass( 'error' );
				
				button.attr( 'value', myCREDedit.working );
				button.attr( 'disabled', 'disabled' );
			},
			// On Successful Communication
			success    : function( response ) {
				// Debug
				console.log( response );
				
				if ( response.success ) {
					current_el.addClass( 'done' );
					current_el.text( response.data );
					amount_el.val( '' );
					entry_el.val( '' );
					$( 'div#mycred-user-' + user_id + '-balance-' + type_el.val() + ' span' ).empty().html( response.data );
				}
				else {
					if ( response.data.error == 'ERROR_1' ) {
						$( '#edit-mycred-balance' ).dialog( 'destroy' );
					}
					else if ( response.data.error == 'ERROR_2' ) {
						alert( response.data.message );
						amount_el.val( '' );
						entry_el.val( '' );
					}
					else  {
						entry_el.addClass( 'error' );
						entry_el.attr( 'title', response.data.message );
					}
				}
				
				button.attr( 'value', label );
				button.removeAttr( 'disabled' );
			},
			// Error (sent to console)
			error      : function( jqXHR, textStatus, errorThrown ) {
				// Debug
				//console.log( jqXHR + ':' + textStatus + ':' + errorThrown );
				
				button.attr( 'value', label );
				button.removeAttr( 'disabled' );
			}
		});
	});
});
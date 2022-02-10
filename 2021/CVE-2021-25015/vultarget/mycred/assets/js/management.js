/**
 * myCRED Management Scripts
 * @since 1.3
 * @version 1.2
 */
jQuery(function($) {

	/**
	 * Empty Log AJAX Caller
	 */
	var mycred_action_empty_log = function( button ) {
		var label = button.val();
		$.ajax({
			type       : "POST",
			data       : {
				action    : 'mycred-action-empty-log',
				token     : myCREDmanage.token,
				type      : button.attr( 'data-type' )
			},
			dataType   : "JSON",
			url        : myCREDmanage.ajaxurl,
			beforeSend : function() {
				button.attr( 'value', myCREDmanage.working );
				button.attr( 'disabled', 'disabled' );
			},
			success    : function( response ) {
				if ( response.success ) {
					$( 'input#mycred-manage-table-rows' ).val( response.data );
					button.val( myCREDmanage.done );
					button.removeClass( 'button-primary' );
				}
				else {
					button.val( label );
					button.removeAttr( 'disabled' );
					alert( response.data );
				}
			}
		});
	};

	/**
	 * Empty Log Trigger
	 */
	$( 'input#mycred-manage-action-empty-log' ).click(function(){
		// Confirm action
		if ( confirm( myCREDmanage.confirm_log ) )
			mycred_action_empty_log( $(this) );
	});

	/**
	 * Reset Balance AJAX Caller
	 */
	var mycred_action_reset_balance = function( button ) {
		var label = button.val();
		$.ajax({
			type       : "POST",
			data       : {
				action    : 'mycred-action-reset-accounts',
				token     : myCREDmanage.token,
				type      : button.attr( 'data-type' )
			},
			dataType   : "JSON",
			url        : myCREDmanage.ajaxurl,
			beforeSend : function() {
				button.attr( 'value', myCREDmanage.working );
				button.attr( 'disabled', 'disabled' );
			},
			success    : function( response ) {
				if ( response.success ) {
					button.val( response.data );
					button.removeClass( 'button-primary' );
				}
				else {
					button.val( label );
					button.removeAttr( 'disabled' );
					alert( response.data );
				}
			}
		});
	};

	/**
	 * Reset Balance Trigger
	 */
	$( 'input#mycred-manage-action-reset-accounts' ).click(function(){
		// Confirm action
		if ( confirm( myCREDmanage.confirm_reset ) )
			mycred_action_reset_balance( $(this) );
	});

	/**
	 * Export Balances Modal
	 */
	$('#export-points').dialog({
		dialogClass : 'mycred-export-points',
		draggable   : false,
		autoOpen    : false,
		closeText   : myCREDmanage.export_close,
		title       : myCREDmanage.export_title,
		modal       : true,
		width       : 500,
		resizable   : false,
		show        : { effect: 'slide', direction: 'up', duration: 250 },
		hide        : { effect: 'slide', direction: 'up', duration: 250 }
	});

	/**
	 * Export balances Modal Trigger
	 */
	$( '#mycred-export-users-points' ).click( function() {
		$( '#export-points' ).dialog( 'open' );
	});

	/**
	 * Export Balances AJAX Caller
	 */
	var mycred_action_export_balances = function( button ) {
		var label = button.val();
		$.ajax({
			type       : "POST",
			data       : {
				action    : 'mycred-action-export-balances',
				token     : myCREDmanage.token,
				identify  : $( '#mycred-export-identify-by' ).val(),
				log_temp  : $( '#mycred-export-log-template' ).val(),
				type      : button.attr( 'data-type' )
			},
			dataType   : "JSON",
			url        : myCREDmanage.ajaxurl,
			beforeSend : function() {
				button.attr( 'value', myCREDmanage.working );
				button.attr( 'disabled', 'disabled' );
			},
			success    : function( response ) {
				// Debug
				//console.log( response );

				if ( response.success ) {
					setTimeout(function(){
						window.location.href = response.data;
						button.val( myCREDmanage.done );
					}, 2000 );
					setTimeout(function(){
						button.removeAttr( 'disabled' );
						button.val( label );
					}, 4000 );
				}
				else {
					button.val( label );
					button.before( response.data );
				}
			}
		});
	};

	/**
	 * Balance Export Trigger
	 */
	$( '#mycred-run-exporter' ).click(function(){
		mycred_action_export_balances( $(this) );
	});

	/**
	 * Generate Key AJAX Caller
	 */
	var mycred_generate_key = function() {
		$.ajax({
			type     : "POST",
			data     : {
				action  : 'mycred-action-generate-key',
				token   : myCREDmanage.token
			},
			dataType : "JSON",
			url      : myCREDmanage.ajaxurl,
			success  : function( response ) {
				$( '#myCRED-remote-key' ).val( response.data );
				$( '#mycred-length-counter' ).text( response.data.length );
			}
		});
	}

	/**
	 * Generate Key Trigger
	 */
	$( '#mycred-generate-api-key' ).click(function(){
		mycred_generate_key();
	});

	/**
	 * Key Length Indicator
	 */
	$( '#myCRED-remote-key' ).change(function(){
		$( '#mycred-length-counter' ).text( $(this).val().length );
	});

	/**
	 * Key Length Indicator
	 */
	$( '#myCRED-remote-key' ).keyup(function(){
		$( '#mycred-length-counter' ).text( $(this).val().length );
	});
});
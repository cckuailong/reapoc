/**
 * myCRED Management Scripts
 * @since 1.3
 * @version 1.1.1
 */
jQuery(function($){
	
	var mycred_action_delete_ranks = function( button, pointtype ) {
		var label = button.val();
		$.ajax({
			type : "POST",
			data : {
				action : 'mycred-action-delete-ranks',
				token  : myCRED_Ranks.token,
				ctype  : pointtype
			},
			dataType : "JSON",
			url : myCRED_Ranks.ajaxurl,
			beforeSend : function() {
				button.attr( 'value', myCRED_Ranks.working );
				button.attr( 'disabled', 'disabled' );
			},
			success : function( data ) {
				console.log( data );
				
				if ( data.status == 'OK' ) {
					$( 'input#mycred-ranks-no-of-ranks' ).val( data.rows );
					button.val( myCREDmanage.done );
					button.removeClass( 'button-primary' );
				}
				else {
					button.val( label );
					button.removeAttr( 'disabled' );
				}
			},
			error   : function( jqXHR, textStatus, errorThrown ) {
				// Debug
				console.log( textStatus + ':' + errorThrown );
				button.attr( 'value', label );
				button.removeAttr( 'disabled' );
			}
		});
	};
	
	$( 'input#mycred-manage-action-reset-ranks' ).click(function(){
		// Confirm action
		if ( confirm( myCRED_Ranks.confirm_del ) ) {
			mycred_action_delete_ranks( $(this), $(this).data( 'type' ) );
		}
	});
	
	var mycred_action_assign_ranks = function( button, pointtype ) {
		var label = button.val();
		$.ajax({
			type : "POST",
			data : {
				action : 'mycred-action-assign-ranks',
				token  : myCRED_Ranks.token,
				ctype  : pointtype
			},
			dataType : "JSON",
			url : myCRED_Ranks.ajaxurl,
			beforeSend : function() {
				button.attr( 'value', myCRED_Ranks.working );
				button.attr( 'disabled', 'disabled' );
			},
			success : function( data ) {
				console.log( data );
				
				if ( data.status == 'OK' ) {
					button.val( myCREDmanage.done );
				}
				else {
					button.val( label );
					button.removeAttr( 'disabled' );
				}
			},
			error   : function( jqXHR, textStatus, errorThrown ) {
				// Debug
				console.log( textStatus + ':' + errorThrown );
				button.attr( 'value', label );
				button.removeAttr( 'disabled' );
			}
		});
	};
	
	$( 'input#mycred-manage-action-assign-ranks' ).click(function(){
		// Confirm action
		if ( confirm( myCRED_Ranks.confirm_assign ) ) {
			mycred_action_assign_ranks( $(this), $(this).data( 'type' ) );
		}
	});

});
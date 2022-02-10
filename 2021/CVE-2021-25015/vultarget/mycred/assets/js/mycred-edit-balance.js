/**
 * myCRED Editor
 * Handles the product type editor in the WordPress admin area on the "Users" page.
 * @since 1.0
 * @version 1.3
 */
jQuery(function($) {

	var myCREDtype           = myCREDedit.defaulttype;
	var myCREDuser           = 0;

	var myCREDEditorModal    = $( '#edit-mycred-balance' );
	var myCREDEditorLedger   = $( '#mycred-users-mini-ledger' );
	var myCREDEditorResults  = $( '#mycred-editor-results' );

	var myCREDIDtoShow       = $( '#mycred-userid-to-show' );
	var myCREDUsernametoShow = $( '#mycred-username-to-show' );
	var myCREDCBalancetoShow = $( '#mycred-current-to-show' );
	var myCREDTBalancetoShow = $( '#mycred-total-to-show' );

	var myCREDAmount         = $( 'input#mycred-editor-amount' );
	var myCREDReference      = $( 'select#mycred-editor-reference' );
	var myCREDCustomRefWrap  = $( '#mycred-custom-reference-wrapper' );
	var myCREDCustomRef      = $( 'input#mycred-editor-custom-reference' );
	var myCREDLogEntry       = $( 'input#mycred-editor-entry' );

	var wWidth     = $(window).width();
	var dWidth     = wWidth * 0.75;

	/**
	 * Reset Editor
	 */
	function mycred_reset_editor() {

		var currentreference = myCREDReference.find( ':selected' );
		if ( currentreference !== undefined && currentreference.val() != myCREDedit.ref ) {
			currentreference.removeAttr( 'selected' );
		}

		myCREDAmount.val( '' );
		myCREDCustomRef.val( '' );
		myCREDLogEntry.val( '' );

		$( 'select#mycred-editor-reference option[value="' + myCREDedit.ref + '"]' ).attr( 'selected', 'selected' );
		myCREDCustomRefWrap.hide();

		myCREDuser = 0;

	}

	/**
	 * Animate Balance
	 */
	function mycred_animate_balance( elementtoanimate, finalAmount, decimals ) {

		var currentbalance = elementtoanimate.text();

		// Float
		if ( decimals > 0 ) {

			currentbalance = parseFloat( currentbalance );
			finalAmount    = parseFloat( finalAmount );

			var decimal_factor = decimals === 0 ? 1 : Math.pow( 10, decimals );

			elementtoanimate.prop( 'number', currentbalance ).numerator({
				toValue    : finalAmount,
				fromValue  : currentbalance,
				rounding   : decimals,
				duration   : 2000
			});

		}
		// Int
		else {

			currentbalance = parseInt( currentbalance );
			finalAmount    = parseInt( finalAmount );

			elementtoanimate.prop( 'number', currentbalance ).numerator({
				toValue    : finalAmount,
				fromValue  : currentbalance,
				duration   : 2000
			});

		}

	}

	$(document).ready( function() {

		if ( dWidth < 250 )
			dWidth = wWidth;

		if ( dWidth > 960 )
			dWidth = 960;

		/**
		 * Setup Editor Window
		 */
		myCREDEditorModal.dialog({
			dialogClass : 'mycred-update-balance',
			draggable   : true,
			autoOpen    : false,
			title       : myCREDedit.title,
			closeText   : myCREDedit.close,
			modal       : true,
			width       : dWidth,
			height      : 'auto',
			resizable   : false,
			position    : { my: "center", at: "top+25%", of: window },
			show        : {
				effect     : 'fadeIn',
				duration   : 250
			},
			hide        : {
				effect     : 'fadeOut',
				duration   : 250
			}
		});

		/**
		 * Toggle Editor
		 */
		$( '.mycred-open-points-editor' ).click( function(e) {

			e.preventDefault();

			myCREDtype = $(this).data( 'type' );
			myCREDuser = $(this).data( 'userid' );

			if ( myCREDEditorLedger.hasClass( 'shown' ) )
				myCREDEditorLedger.slideUp().removeClass( 'shown' );

			myCREDEditorResults.empty();

			// Setup the information we show about the user
			myCREDIDtoShow.empty().text( myCREDuser );
			myCREDUsernametoShow.empty().text( $(this).data( 'username' ) );
			myCREDCBalancetoShow.empty().text( $(this).data( 'current' ) );
			myCREDTBalancetoShow.empty().text( $(this).data( 'total' ) );

			$( 'input#mycred-edit-balance-of-user' ).val( myCREDuser );
			$( 'input#mycred-edit-balance-of-type' ).val( myCREDtype );

			// Setup amount placeholder
			myCREDAmount.attr( 'placeholder', $(this).data( 'zero' ) );

			console.log( 'Editing ' + $(this).data( 'username' ) + ' s balance' );

			// Show editor
			myCREDEditorModal.dialog( 'open' );

		});

		/**
		 * Toggle custom reference field
		 */
		myCREDReference.change(function() {

			var selectedreference = $(this).find( ':selected' );
			if ( selectedreference === undefined ) return false;

			if ( selectedreference.val() == 'mycred_custom' )
				myCREDCustomRefWrap.slideDown();

			else {
				myCREDCustomRefWrap.slideUp();
				myCREDCustomRef.val( '' );
			}

		});

		/**
		 * Toggle mini ledger
		 */
		$( 'button#load-users-mycred-history' ).click(function() {

			if ( myCREDEditorLedger.hasClass( 'shown' ) ) {
				myCREDEditorLedger.slideUp(function(){
					$( '#mycred-users-mini-ledger .border' ).empty().html( myCREDedit.loading );
				}).removeClass( 'shown' );
				
			}

			else {

				$( '#mycred-users-mini-ledger .border' ).empty().html( myCREDedit.loading );
				myCREDEditorLedger.slideDown().addClass( 'shown' );
				$(this).attr( 'disabled', 'disabled' );

				$.ajax({
					type       : 'POST',
					data       : {
						action    : 'mycred-admin-recent-activity',
						token     : myCREDedit.ledgertoken,
						userid    : myCREDuser,
						type      : myCREDtype
					},
					dataType   : 'HTML',
					url        : myCREDedit.ajaxurl,
					success    : function( response ) {

						$( '#mycred-users-mini-ledger .border #mycred-processing' ).slideUp(function(){
							$( '#mycred-users-mini-ledger .border' ).empty().html( response ).slideDown();
							$( 'button#load-users-mycred-history' ).removeAttr( 'disabled' );
						});

					}
				});

			}

		});

		/**
		 * Editor Submit
		 */
		$( 'form#mycred-editor-form' ).submit( function(e) {

			e.preventDefault();

			$.ajax({
				type       : 'POST',
				data       : {
					action    : 'mycred-admin-editor',
					token     : myCREDedit.token,
					form      : $(this).serialize()
				},
				dataType   : 'JSON',
				url        : myCREDedit.ajaxurl,
				beforeSend : function() {

					// Disable all fields in the form to prevent edits while we submit the form
					$( 'form#mycred-editor-form input' ).attr( 'readonly', 'readonly' );
					$( 'form#mycred-editor-form select' ).attr( 'readonly', 'readonly' );

					if ( myCREDEditorLedger.hasClass( 'shown' ) )
						$( 'button#load-users-mycred-history' ).click();

					// Disable submit button and show that we are working
					$( '#mycred-editor-submit' ).val( myCREDedit.working ).attr( 'disabled', 'disabled' );
					myCREDEditorResults.empty();
					$( '#mycred-editor-indicator' ).addClass( 'is-active' );

				},
				success    : function( response ) {

					$( '#mycred-editor-indicator' ).removeClass( 'is-active' );

					// Security token has expired or something is blocking access to the ajax handler
					if ( response.success === undefined ) {
						myCREDEditorModal.dialog( 'destroy' );
						location.reload();
					}

					console.log( response );

					// Remove form restrictions
					$( 'form#mycred-editor-form input' ).removeAttr( 'readonly' );
					$( 'form#mycred-editor-form select' ).removeAttr( 'readonly' );

					// All went well, clear the form
					if ( response.success ) {

						mycred_animate_balance( myCREDCBalancetoShow, response.data.current, response.data.decimals );
						mycred_animate_balance( myCREDTBalancetoShow, response.data.total, response.data.decimals );

						$( '#mycred-user-' + myCREDuser + '-balance-' + myCREDtype + ' span' ).empty().text( response.data.current );
						$( '#mycred-user-' + myCREDuser + '-balance-total-' + myCREDtype + ' small span' ).empty().text( response.data.current );

						mycred_reset_editor();

					}

					// Update results
					myCREDEditorResults.html( response.data.results );

					// Reset submit button
					$( '#mycred-editor-submit' ).val( response.data.label ).removeAttr( 'disabled', 'disabled' );

				}

			});

			return false;

		});

	});

});
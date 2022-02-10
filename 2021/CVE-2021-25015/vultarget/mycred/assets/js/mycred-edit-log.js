/**
 * myCRED Edit Log Scripts
 * These scripts are used to edit or delete entries
 * in the myCRED Log.
 * @since 1.4
 * @version 1.2.5
 */
jQuery(function($) {

	var wWidth     = $(window).width();
	var dWidth     = wWidth * 0.75;

	var myCREDRowId     = 0;
	var myCREDRow       = '';
	var myCREDReference = '';

	var myCREDEditorModal     = $( '#edit-mycred-log-entry' );
	var myCREDEditorResults   = $( '#mycred-editor-results' );
	var myCREDAvailableTags   = $( '#available-template-tags' );

	var myCREDUsertoShow      = $( '#mycred-user-to-show' );
	var myCREDDatetoShow      = $( '#mycred-date-to-show' );
	var myCREDAmounttoShow    = $( '#mycred-creds-to-show' );
	var myCREDReferencetoShow = $( '#mycred-referece-to-show' );
	var myCREDOldEntrytoShow  = $( '#mycred-old-entry-to-show' );
	var myCREDNewEntrytoShow  = $( '#mycred-new-entry-to-show' );

	/**
	 * Reset Editor
	 */
	function mycred_reset_editor() {

		myCREDEditorResults.empty();
		myCREDUsertoShow.empty();
		myCREDDatetoShow.empty();
		myCREDAmounttoShow.val( '' );
		myCREDReferencetoShow.empty();
		myCREDOldEntrytoShow.empty();
		myCREDNewEntrytoShow.val( '' );
		myCREDAvailableTags.empty();

		$.each( myCREDLog.references, function( index ){

			var optiontoinsert = '<option value=\"' + index + '\"';
			if ( myCREDReference == index ) optiontoinsert += ' selected=\"selected\"';
			optiontoinsert += '>' + myCREDLog.references[ index ] + '<\/option>';

			myCREDReferencetoShow.append( optiontoinsert );

		});

		$( 'button#mycred-delete-entry-in-editor' ).attr( 'data', myCREDRowId );

	}

	/**
	 * Animate Row Deletion
	 */
	function mycred_animate_row_deletion( rowtoanimate ) {

		var rowtodelete = $( '#entry-' + rowtoanimate );
		if ( rowtodelete === undefined ) return;

		rowtodelete.addClass( 'deleted-row' ).fadeOut( 2000, function(){
			rowtodelete.remove();
		});

	}

	/**
	 * Animate Row Update
	 */
	function mycred_animate_row_update( newrow ) {

		var affectedrow = $( '#entry-' + myCREDRowId );

		affectedrow.addClass( 'updated-row' ).fadeOut(function(){
			affectedrow.empty().append( newrow ).fadeIn( 2000, function(){
				affectedrow.removeClass( 'updated-row' );
			});
		});

	}

	/**
	 * Update Log Entry
	 */
	function mycred_update_entry( submission ) {

		var submitbutton = $( '#mycred-editor-submit' );
		var submitlabel  = submitbutton.val();

		$.ajax({
			type       : "POST",
			data       : {
				action    : 'mycred-update-log-entry',
				token     : myCREDLog.tokens.update,
				screen    : myCREDLog.screen,
				page      : myCREDLog.page,
				rowid     : myCREDRowId,
				ctype     : myCREDLog.ctype,
				form      : submission
			},
			dataType   : "JSON",
			url        : myCREDLog.ajaxurl,
			beforeSend : function() {

				myCREDAmounttoShow.attr( 'readonly', 'readonly' );
				myCREDReferencetoShow.attr( 'readonly', 'readonly' );
				myCREDNewEntrytoShow.attr( 'readonly', 'readonly' );

				// Prep results box
				myCREDEditorResults.empty();
				$( '#mycred-editor-indicator' ).addClass( 'is-active' );

				// Indicate that we are doing something
				submitbutton.empty().text( myCREDLog.working );

			},
			success    : function( response ) {

				// Remove indicator
				$( '#mycred-editor-indicator' ).removeClass( 'is-active' );

				// Most likelly the wpnonce has expired (screen open too long)
				if ( response.success === undefined ) {
					myCREDEditorModal.dialog( 'destroy' );
					location.reload();
				}

				// Ok, something was done
				else {

					myCREDAmounttoShow.removeAttr( 'readonly' );
					myCREDReferencetoShow.removeAttr( 'readonly' );
					myCREDNewEntrytoShow.removeAttr( 'readonly' );

					submitbutton.empty().text( submitlabel );

					myCREDEditorResults.text( response.data.message );

					if ( response.success === true ) {
						mycred_animate_row_update( response.data.results );
					}

				}

			}
		});

	}

	/**
	 * Delete Log Entry
	 */
	function mycred_delete_entry( entryid ) {

		var ismodalopen  = myCREDEditorModal.dialog( "isOpen" );
		var deletebutton = $( '#mycred-delete-entry-in-editor' );
		var deletelabel  = deletebutton.text();

		$.ajax({
			type       : "POST",
			data       : {
				action    : 'mycred-delete-log-entry',
				token     : myCREDLog.tokens.delete,
				ctype     : myCREDLog.ctype,
				row       : entryid
			},
			dataType   : "JSON",
			url        : myCREDLog.ajaxurl,
			beforeSend : function() {

				if ( ismodalopen === true ) {

					// Make sure we can not make adjustments while we wait fo the AJAX handler to get back to us
					myCREDAmounttoShow.attr( 'readonly', 'readonly' );
					myCREDReferencetoShow.attr( 'readonly', 'readonly' );
					myCREDNewEntrytoShow.attr( 'readonly', 'readonly' );

					// Prep results box
					myCREDEditorResults.empty();
					$( '#mycred-editor-indicator' ).addClass( 'is-active' );

					// Indicate that we are doing something
					deletebutton.empty().text( myCREDLog.working );

				}

			},
			success    : function( response ) {

				// Remove indicator
				$( '#mycred-editor-indicator' ).removeClass( 'is-active' );

				// Most likelly the wpnonce has expired (screen open too long)
				if ( response.success === undefined ) {
					myCREDEditorModal.dialog( 'destroy' );
					location.reload();
				}

				// Ok, something was done
				else {

					// Act based on where we clicked to delete - In modal
					if ( ismodalopen === true ) {

						myCREDEditorResults.text( response.data );

						// Request failed for some reason, restore form usability
						if ( response.success !== true ) {

							myCREDAmounttoShow.removeAttr( 'readonly' );
							myCREDReferencetoShow.removeAttr( 'readonly' );
							myCREDNewEntrytoShow.removeAttr( 'readonly' );

							deletebutton.empty().text( deletelabel );

						}

						// All good. Close Dialog
						else {

							// Reset row id
							myCREDRowId = 0;

							// Restore button label for next opening
							deletebutton.empty().text( deletelabel );

							// Close dialog window
							myCREDEditorModal.dialog( 'close' );

						}

					}

					// In table
					else {

						if ( response.success !== true )
							alert( response.data );

					}

					// No matter which button we pressed, animate the row removal if successfull
					if ( response.success === true )
						mycred_animate_row_deletion( entryid );

				}

			}
		});

	}

	/**
	 * Once Ready
	 */
	$(document).ready( function() {

		// Adjust modal width based on device width
		if ( dWidth < 250 )
			dWidth = wWidth;

		if ( dWidth > 960 )
			dWidth = 960;

		/**
		 * Setup Editor Window
		 */
		myCREDEditorModal.dialog({
			dialogClass : 'mycred-edit-logentry',
			draggable   : true,
			autoOpen    : false,
			title       : myCREDLog.title,
			closeText   : myCREDLog.close,
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
		$( 'tbody#the-list' ).on( 'click', '.mycred-open-log-entry-editor', function(e) {

			e.preventDefault();

			myCREDRowId     = $(this).data( 'id' );
			myCREDReference = $(this).data( 'ref' );
			myCREDRow       = '#entry-' + myCREDRowId;

			mycred_reset_editor();

			myCREDUsertoShow.append( $( myCREDRow + ' td.column-username strong' ).text() );
			myCREDDatetoShow.append( $( myCREDRow + ' td.column-time time' ).text() );
			myCREDOldEntrytoShow.append( $( myCREDRow + ' td.column-entry' ).text() );
			myCREDNewEntrytoShow.val( $( myCREDRow + ' td.column-entry' ).data( 'raw' ) );

			var amounttoshow = $( myCREDRow + ' td.column-creds' ).data( 'raw' );
			myCREDAmounttoShow.val( amounttoshow ).attr( 'placeholder', amounttoshow );

			// Show editor
			myCREDEditorModal.dialog( 'open' );

		});

		/**
		 * Trigger Log Deletion
		 */
		$( 'tbody#the-list' ).on( 'click', '.mycred-delete-row', function(){

			// Require user to confirm deletion (if used)
			if ( myCREDLog.messages.delete_row != '' && ! confirm( myCREDLog.messages.delete ) )
				return false;

			var deletebutton = $(this);
			var rowtodelete  = deletebutton.data( 'id' );

			if ( rowtodelete === undefined || rowtodelete == '' )
				rowtodelete = myCREDRowId;
			else
				myCREDRowId = rowtodelete;

			mycred_delete_entry( rowtodelete );

		});
		$( '#mycred-delete-entry-in-editor' ).on( 'click', function(e){

			e.preventDefault();

			// Require user to confirm deletion (if used)
			if ( myCREDLog.messages.delete_row != '' && ! confirm( myCREDLog.messages.delete ) )
				return false;

			var deletebutton = $(this);
			var rowtodelete  = deletebutton.data( 'id' );

			if ( rowtodelete === undefined || rowtodelete == '' )
				rowtodelete = myCREDRowId;
			else
				myCREDRowId = rowtodelete;

			mycred_delete_entry( rowtodelete );

		});

		/**
		 * Submit New Log Entry
		 */
		$( '#edit-mycred-log-entry' ).on( 'submit', 'form#mycred-editor-form', function(e){

			e.preventDefault();

			mycred_update_entry( $(this).serialize() );

		});

	});

	// Checkbox select in table
	// @see http://stackoverflow.com/questions/19164816/jquery-select-all-checkboxes-in-table
	$( '#myCRED-wrap form table thead .check-column input' ).click(function(e){
		var table= $(e.target).closest('table');
		$('.check-column input',table).prop( 'checked',this.checked );
	});

	/**
	 * Click To Toggle Script
	 */
	$( '.click-to-toggle' ).click(function(){

		var target = $(this).attr( 'data-toggle' );
		$( '#' + target ).toggle();

	});

});
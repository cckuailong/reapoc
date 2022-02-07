jQuery( function( $ ) {

	$( '#doaction, #doaction2' ).on( 'click', function( e ) {
		let actionselected = $(this).attr("id").substr(2);
		let action         = $('select[name="' + actionselected + '"]').val();

		if ( $.inArray(action, wpo_wcpdf_ajax.bulk_actions) !== -1 ) {
			e.preventDefault();
			let template = action;
			let checked  = [];

			$('tbody th.check-column input[type="checkbox"]:checked').each(
				function() {
					checked.push($(this).val());
				}
			);
			
			if (!checked.length) {
				alert('You have to select order(s) first!');
				return;
			}
			
			let order_ids = checked.join('x');

			if (wpo_wcpdf_ajax.ajaxurl.indexOf("?") != -1) {
				url = wpo_wcpdf_ajax.ajaxurl+'&action=generate_wpo_wcpdf&document_type='+template+'&order_ids='+order_ids+'&bulk&_wpnonce='+wpo_wcpdf_ajax.nonce;
			} else {
				url = wpo_wcpdf_ajax.ajaxurl+'?action=generate_wpo_wcpdf&document_type='+template+'&order_ids='+order_ids+'&bulk&_wpnonce='+wpo_wcpdf_ajax.nonce;
			}

			window.open(url,'_blank');
		}
	} );

	$( '#wpo_wcpdf-data-input-box' ).insertAfter('#woocommerce-order-data');
	
	// enable invoice number edit if user initiated
	$( '#wpo_wcpdf-data-input-box' ).on( 'click', '.wpo-wcpdf-set-date-number, .wpo-wcpdf-edit-date-number, .wpo-wcpdf-edit-document-notes', function() {
		let $form = $(this).closest('.wcpdf-data-fields-section');
		if ( $form.length == 0 ) { // no section, take overall wrapper
			$form = $(this).closest('.wcpdf-data-fields');
		}

		let edit = $(this).data( 'edit' );

		// check visibility
		toggle_edit_mode( $form, edit );
	} );

	// cancel edit
	$( '#wpo_wcpdf-data-input-box' ).on( 'click', '.wpo-wcpdf-cancel', function() {
		let $form = $(this).closest('.wcpdf-data-fields');
		toggle_edit_mode( $form );
	} );

	// save, regenerate and delete document
	$( '#wpo_wcpdf-data-input-box' ).on( 'click', '.wpo-wcpdf-save-document, .wpo-wcpdf-regenerate-document, .wpo-wcpdf-delete-document', function( e ) {
		e.preventDefault();

		let $form      = $(this).closest('.wcpdf-data-fields');
		let action     = $(this).data('action');
		let nonce      = $(this).data('nonce');
		let data       = $form.data();
		let serialized = $form.find(":input:visible:not(:disabled)").serialize();

		// regenerate specific
		if( action == 'regenerate' ) {
			if ( window.confirm( wpo_wcpdf_ajax.confirm_regenerate ) === false ) {
				return; // having second thoughts
			}
	
			$form.find('.wpo-wcpdf-regenerate-document').addClass('wcpdf-regenerate-spin');

		// delete specific
		} else if( action == 'delete' ) {
			if ( window.confirm( wpo_wcpdf_ajax.confirm_delete ) === false ) {
				return; // having second thoughts
			}

			// hide regenerate button
			$form.find('.wpo-wcpdf-regenerate-document').hide();
		}

		// block ui
		$form.block( {
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		} );

		// request
		$.ajax( {
			url:                            wpo_wcpdf_ajax.ajaxurl,
			data: {
				action:                     'wpo_wcpdf_'+action+'_document',
				security:                   nonce,
				form_data:                  serialized,
				order_id:                   data.order_id,
				document_type:              data.document,
				action_type:                action,
				wpcdf_document_data_notice: action+'d',
			},
			type:               'POST',
			context:            $form,
			success: function( response ) {
				toggle_edit_mode( $form );

				// update document DOM data
				$form.closest('#wpo_wcpdf-data-input-box').load( document.URL + ' #wpo_wcpdf-data-input-box .postbox-header, #wpo_wcpdf-data-input-box .inside', function() {
					let notice_type;
					if( response.success ) {
						notice_type = 'success';
					} else {
						notice_type = 'error';
					}
					$(this).find( ".wcpdf-data-fields[data-document='" + data.document +"'][data-order_id='" + data.order_id +"']" ).before( '<div class="notice notice-'+notice_type+' inline" style="margin:0 10px 10px 10px;"><p>'+response.data.message+'</p></div>' );
				});

				if( action == 'regenerate' ) {
					$form.find('.wpo-wcpdf-regenerate-document').removeClass('wcpdf-regenerate-spin');
					toggle_edit_mode( $form );
				}

				// unblock ui
				$form.unblock();
			}
		} );
		
	} );

	function toggle_edit_mode( $form, mode = null ) {
		// check visibility
		if( $form.find(".read-only").is(":visible") ) {
			if( mode == 'notes' ) {
				$form.find('.editable-notes :input').attr('disabled', false);
			} else {
				$form.find(".editable").show();
				$form.find(':input').attr('disabled', false);
			}

			$form.find(".read-only").hide();
			$form.find(".editable-notes").show();
			$form.closest('.wcpdf-data-fields').find('.wpo-wcpdf-document-buttons').show();
		} else {
			$form.find(".read-only").show();
			$form.find(".editable").hide();
			$form.find(".editable-notes").hide();
			$form.find(':input').attr('disabled', true);
			$form.closest('.wcpdf-data-fields').find('.wpo-wcpdf-document-buttons').hide();
		}
	}

} );
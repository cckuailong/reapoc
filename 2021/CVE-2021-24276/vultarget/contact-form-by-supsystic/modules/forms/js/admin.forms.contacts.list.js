jQuery(document).ready(function(){
	var tblId = 'cfsFormContactsTbl';

	var colNames = [ toeLangCfs('ID') ]
	,	colModel = [ {name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '50', align: 'center'} ]
	,	contactFieldsToHtml = {};
	if(typeof(cfsFormFields) !== 'undefined' && cfsFormFields) {
		for(var key in cfsFormFields) {
			colNames.push( cfsFormFields[ key ].label );
			colModel.push( {name: 'user_field_'+ key, index: 'field_'+ key, searchoptions: {sopt: ['eq']}, align: 'center', sortable: false} );
			contactFieldsToHtml[ key ] = cfsFormFields[ key ].html;
		}
	}
	jQuery('#'+ tblId).jqGrid({ 
		url: cfsTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames: colNames 
	,	colModel: colModel
	,	postData: {
			search: {
				text_like: jQuery('#'+ tblId+ 'SearchTxt').val()
			,	form_id: jQuery('#'+ tblId+ 'SearchFidSelect').val()
			}
		,	contact_fields_to_html: contactFieldsToHtml
		}
	,	rowNum:10
	,	rowList:[10, 20, 30, 1000]
	,	pager: '#'+ tblId+ 'Nav'
	,	sortname: 'id'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangCfs('Current Form Contacts')
	,	height: '100%' 
	,	emptyrecords: toeLangCfs('You have no Form Contactss for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e, event) {
			var tblId = jQuery(this).attr('id')
			,	selectedRowIds = jQuery('#'+ tblId).jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#'+ tblId).getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#cfsFormContactsRemoveGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_'+ tblId).prop('indeterminate', false);
					jQuery('#cb_'+ tblId).attr('checked', 'checked');
				} else {
					jQuery('#cb_'+ tblId).prop('indeterminate', true);
				}
			} else {
				jQuery('#cfsFormContactsRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_'+ tblId).prop('indeterminate', false);
				jQuery('#cb_'+ tblId).removeAttr('checked');
			}
			cfsCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			cfsCheckUpdate('#cb_'+ tblId);
			if(selectedRowIds 
				&& selectedRowIds.length == 1 
				&& jQuery(event.target).attr('aria-describedby') != tblId+ '_cb'
			) {	// It was simple click on the row, not on checkbox
				var contactId = parseInt( jQuery('#'+ tblId).jqGrid ('getCell', selectedRowIds[ 0 ], 'id') );
				if( contactId ) {
					cfsFormContactPrev( contactId );
				}
			}
		}
	,	gridComplete: function(a, b, c) {
			var tblId = jQuery(this).attr('id');
			jQuery('#cfsFormContactsRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_'+ tblId).prop('indeterminate', false);
			jQuery('#cb_'+ tblId).removeAttr('checked');
			// Custom checkbox manipulation
			cfsInitCustomCheckRadio('#'+ jQuery(this).attr('id') );
			cfsCheckUpdate('#cb_'+ jQuery(this).attr('id'));
			// Preview contact
			jQuery('#'+ tblId).find('.cfsFormContactPrevLnk').click(function(){
				cfsFormContactPrev( jQuery(this).attr('href'), this );
				return false;
			});
		}
	,	loadComplete: function() {
			var tblId = jQuery(this).attr('id');
			if (this.p.reccount === 0) {
				jQuery(this).hide();
				jQuery('#'+ tblId+ 'EmptyMsg').show();
			} else {
				jQuery(this).show();
				jQuery('#'+ tblId+ 'EmptyMsg').hide();
			}
		}
	});
	jQuery('#'+ tblId+ 'NavShell').append( jQuery('#'+ tblId+ 'Nav') );
	jQuery('#'+ tblId+ 'Nav').find('.ui-pg-selbox').insertAfter( jQuery('#'+ tblId+ 'Nav').find('.ui-paging-info') );
	jQuery('#'+ tblId+ 'Nav').find('.ui-pg-table td:first').remove();
	// Make navigation tabs to be with our additional buttons - in one row
	jQuery('#'+ tblId+ 'Nav_center').prepend( jQuery('#'+ tblId+ 'NavBtnsShell') ).css({
		'width': '80%'
	,	'white-space': 'normal'
	,	'padding-top': '8px'
	});
	jQuery('#'+ tblId+ 'SearchTxt').keyup(function(){
		var searchVal = jQuery.trim( jQuery(this).val() );
		if(searchVal && searchVal != '') {
			cfsGridDoListSearch({
				text_like: searchVal
			}, tblId);
		}
	});
	jQuery('#'+ tblId+ 'SearchFidSelect').change(function(){
		var fid = jQuery(this).val();
		cfsGridDoListSearch({
			form_id: fid
		}, tblId);
	});
	// Fallback for case if library was not loaded
	if(!jQuery.fn.chosen) {
		jQuery.fn.chosen = function() {
			
		};
	}
	jQuery('.chosen').chosen({
		disable_search_threshold: 10
	});
	
	jQuery('#'+ tblId+ 'EmptyMsg').insertAfter(jQuery('#'+ tblId+ '').parent());
	jQuery('#'+ tblId+ '').jqGrid('navGrid', '#'+ tblId+ 'Nav', {edit: false, add: false, del: false});
	jQuery('#cb_'+ tblId+ '').change(function(){
		jQuery(this).attr('checked') 
			? jQuery('#cfsFormContactsRemoveGroupBtn').removeAttr('disabled')
			: jQuery('#cfsFormContactsRemoveGroupBtn').attr('disabled', 'disabled');
	});
	jQuery('#cfsFormContactsRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#cfsFormContactsTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#cfsFormContactsTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		var confirmMsg = listIds.length > 1
			? toeLangCfs('Are you sur want to remove '+ listIds.length+ ' Form Contactss?')
			: toeLangCfs('Are you sure want to remove 1 Form Contact?')
		if(confirm(confirmMsg)) {
			jQuery.sendFormCfs({
				btn: this
			,	data: {mod: 'forms', action: 'removeContactsGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#cfsFormContactsTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	cfsInitCustomCheckRadio('#'+ tblId+ '_cb');
});
function cfsFormContactPrev( id, lnk ) {
	jQuery.sendFormCfs({
		btn: lnk
	,	data: {mod: 'forms', action: 'getContactDetails', id: id}
	,	onSuccess: function(res) {
			if(!res.error) {
				var $dlg = _cfsGetContactDetailsWnd()
				,	$shell = $dlg.find('#cfsContactDetailsShell')
				,	$rowEx = $dlg.find('#cfsFormContactFieldRowEx');
				$shell.html('');
				if( res.data.form_fields ) {
					for(var i = 0; i < res.data.form_fields.length; i++) {
						var fieldName = res.data.form_fields[ i ].name;

						if(typeof(res.data.contact.fields[ fieldName ]) !== 'undefined') {
							var $newRow = $rowEx.clone().removeAttr('id').appendTo( $shell )
							,	fieldLabel = res.data.form_fields[ i ].label 
									? res.data.form_fields[ i ].label
									: res.data.form_fields[ i ].placeholder
							,	fieldVal = res.data.contact.fields[ fieldName ];
							if(typeof(fieldVal) !== 'string' && fieldVal.join) {
								fieldVal = fieldVal.join(', ');
							}
							console.log(fieldVal, typeof(fieldVal));
							$newRow.find('.cfsFieldLabel').html( fieldLabel );
							$newRow.find('.cfsFieldValue').html( fieldVal );
						}
					}
					var standardFields = [
						{label: toeLangCfs('IP'), val: res.data.contact.ip}
					,	{label: toeLangCfs('From URL'), val: res.data.contact.url}
					,	{label: toeLangCfs('Date'), val: res.data.contact.date_created}
					,	{label: toeLangCfs('Form'), val: res.data.form_label}
					];
					for(var i = 0; i < standardFields.length; i++) {
						var $newRow = $rowEx.clone().removeAttr('id').appendTo( $shell );
						$newRow.find('.cfsFieldLabel').html( standardFields[ i ].label );
						$newRow.find('.cfsFieldValue').html( standardFields[ i ].val );
					}
				} else {
					$shell.html( toeLangCfs('Form is broken - it have no fields some how, please check it\'s settings') );
				}
				$dlg.dialog('open');
			}
		}
	});
}
function _cfsGetContactDetailsWnd() {
	if(jQuery('#cfsFormContactViewWnd').hasClass('ui-dialog-content')) {
		return jQuery('#cfsFormContactViewWnd');
	}
	return jQuery('#cfsFormContactViewWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 540
	,	height: 200
	,	open: function() {
			
		}
	});
}

jQuery(document).ready(function(){
	var tblId = 'umsMapsTbl';
	jQuery('#'+ tblId).jqGrid({
		url: umsTblDataUrl
	,	datatype: 'json'
	,	autowidth: true
	,	shrinkToFit: true
	,	colNames:[toeLangUms('ID'), toeLangUms('Title'), toeLangUms('Create Date'), toeLangUms('Markers'), toeLangUms('Actions')]
	,	colModel:[
			{name: 'id', index: 'id', searchoptions: {sopt: ['eq']}, width: '50', align: 'center'}
		,	{name: 'title', index: 'title', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'create_date', index: 'create_date', searchoptions: {sopt: ['eq']}, align: 'center'}
		,	{name: 'markers', index: 'markers', searchoptions: {sopt: ['eq']}, align: 'center', sortable: false}
		,	{name: 'actions', index: 'actions', searchoptions: {sopt: ['eq']}, align: 'center', sortable: false}
		]
	,	postData: {
			search: {
				text_like: jQuery('#'+ tblId+ 'SearchTxt').val()
			}
		}
	,	rowNum: 10
	,	rowList: [10, 20, 30, 1000]
	,	pager: '#'+ tblId+ 'Nav'
	,	sortname: 'id'
	,	viewrecords: true
	,	sortorder: 'desc'
	,	jsonReader: { repeatitems : false, id: '0' }
	,	caption: toeLangUms('Current Map')
	,	height: '100%'
	,	emptyrecords: toeLangUms('You have no Map for now.')
	,	multiselect: true
	,	onSelectRow: function(rowid, e) {
			var tblId = jQuery(this).attr('id')
			,	selectedRowIds = jQuery('#'+ tblId).jqGrid ('getGridParam', 'selarrrow')
			,	totalRows = jQuery('#'+ tblId).getGridParam('reccount')
			,	totalRowsSelected = selectedRowIds.length;
			if(totalRowsSelected) {
				jQuery('#umsMapsRemoveGroupBtn').removeAttr('disabled');
				jQuery('#umsMapsCloneGroupBtn').removeAttr('disabled');
				if(totalRowsSelected == totalRows) {
					jQuery('#cb_'+ tblId).prop('indeterminate', false);
					jQuery('#cb_'+ tblId).attr('checked', 'checked');
				} else {
					jQuery('#cb_'+ tblId).prop('indeterminate', true);
				}
			} else {
				jQuery('#umsMapsRemoveGroupBtn').attr('disabled', 'disabled');
				jQuery('#umsMapsCloneGroupBtn').attr('disabled', 'disabled');
				jQuery('#cb_'+ tblId).prop('indeterminate', false);
				jQuery('#cb_'+ tblId).removeAttr('checked');
			}
			umsCheckUpdate(jQuery(this).find('tr:eq('+rowid+')').find('input[type=checkbox].cbox'));
			umsCheckUpdate('#cb_'+ tblId);
		}
	,	gridComplete: function(a, b, c) {
			var tblId = jQuery(this).attr('id');
			jQuery('#umsMapsRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#umsMapsCloneGroupBtn').attr('disabled', 'disabled');
			jQuery('#cb_'+ tblId).prop('indeterminate', false);
			jQuery('#cb_'+ tblId).removeAttr('checked');
			/*if(jQuery('#'+ tblId).jqGrid('getGridParam', 'records'))	// If we have at least one row - allow to clear whole list
				jQuery('#umsMapsClearBtn').removeAttr('disabled');
			else
				jQuery('#umsMapsClearBtn').attr('disabled', 'disabled');*/
			// Custom checkbox manipulation
			umsInitCustomCheckRadio('#'+ jQuery(this).attr('id') );
			umsCheckUpdate('#cb_'+ jQuery(this).attr('id'));
			tooltipsterize( jQuery('#'+ tblId) );
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
			umsGridDoListSearch({
				text_like: searchVal
			}, tblId);
		}
	});

	jQuery('#'+ tblId+ 'EmptyMsg').insertAfter(jQuery('#'+ tblId+ '').parent());
	jQuery('#'+ tblId+ '').jqGrid('navGrid', '#'+ tblId+ 'Nav', {edit: false, add: false, del: false});
	jQuery('#cb_'+ tblId+ '').change(function(){
		if(jQuery(this).attr('checked')) {
			jQuery('#umsMapsRemoveGroupBtn').removeAttr('disabled');
			jQuery('#umsMapsCloneGroupBtn').removeAttr('disabled');
		} else {
			jQuery('#umsMapsRemoveGroupBtn').attr('disabled', 'disabled');
			jQuery('#umsMapsCloneGroupBtn').attr('disabled', 'disabled');
		}
	});
	jQuery('#umsMapsRemoveGroupBtn').click(function(){
		var selectedRowIds = jQuery('#umsMapsTbl').jqGrid ('getGridParam', 'selarrrow')
		,	listIds = [];
		for(var i in selectedRowIds) {
			var rowData = jQuery('#umsMapsTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		var mapLabel = '';
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = umsGetGridColDataById(listIds[0], 'title', 'umsMapsTbl');
			mapLabel = labelCellData ? jQuery(labelCellData).text() : labelCellData;
		}
		var confirmMsg = listIds.length > 1
			? toeLangUms('Are you sur want to remove '+ listIds.length+ ' Maps?')
			: toeLangUms('Are you sure want to remove "'+ mapLabel+ '" Map?');
		if(confirm(confirmMsg)) {
			jQuery.sendFormUms({
				btn: this
			,	data: {mod: 'maps', action: 'removeGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#umsMapsTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	jQuery('#umsMapsCloneGroupBtn').click(function(){
		var selectedRowIds = jQuery('#umsMapsTbl').jqGrid ('getGridParam', 'selarrrow')
		,	mapLabel = ''
		,	listIds = [];

		for(var i in selectedRowIds) {
			var rowData = jQuery('#umsMapsTbl').jqGrid('getRowData', selectedRowIds[ i ]);
			listIds.push( rowData.id );
		}
		if(listIds.length == 1) {	// In table label cell there can be some additional links
			var labelCellData = umsGetGridColDataById(listIds[0], 'title', 'umsMapsTbl');
			mapLabel = labelCellData;
			mapLabel = mapLabel.replace(/<\/?[^>]+(>|$)/g, "");
			mapLabel = mapLabel.replace(/&nbsp;/g, "");
		}
		var confirmMsg = listIds.length > 1
			? toeLangUms('Are you sur want to clone '+ listIds.length+ ' Maps?')
			: toeLangUms('Are you sure want to clone "'+ mapLabel + '" Map?');
		if(confirm(confirmMsg)) {
			jQuery.sendFormUms({
				btn: this
			,	data: {mod: 'maps', action: 'cloneMapGroup', listIds: listIds}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#umsMapsTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});
	/*jQuery('#umsMapsClearBtn').click(function(){
		if(confirm(toeLangUms('Clear whole maps list?'))) {
			jQuery.sendFormUms({
				btn: this
			,	data: {mod: 'maps', action: 'clear'}
			,	onSuccess: function(res) {
					if(!res.error) {
						jQuery('#umsMapsTbl').trigger( 'reloadGrid' );
					}
				}
			});
		}
		return false;
	});*/

	umsInitCustomCheckRadio('#'+ tblId+ '_cb');
});
function umsRemoveMapFromTblClick(mapId){
	if(!confirm(toeLangUms('Remove Map?'))) {
		return false;
	}
	if(mapId == ''){
		return false;
	}
	var msgEl = jQuery('#umsRemoveElemLoader__'+ mapId);

	jQuery.sendFormUms({
		msgElID: msgEl
	,	data: {action: 'remove', mod: 'maps', id: mapId}
	,	onSuccess: function(res) {
			if(!res.error){
				jQuery('#umsMapsTbl').trigger( 'reloadGrid' );
				setTimeout(function(){
					msgEl.hide('500', function(){
						jQuery(this).parents('tr:first').remove();
					});
				}, 500);
			}
		}
	});
}

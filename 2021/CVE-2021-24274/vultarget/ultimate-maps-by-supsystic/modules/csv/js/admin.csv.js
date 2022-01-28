var umsCsvImportData = {};
jQuery(document).ready(function(){
	jQuery('#umsCsvExportMapsBtn').click(function(){
		var delimiter = jQuery('#umsCsvExportDelimiter').val() || ';';
		toeRedirect(createAjaxLinkUms({
			page: 'csv'
		,	action: 'exportMaps'
		,	withMarkers: 1
		,	delimiter: delimiter
		,	onSubmit: umsCsvImportOnSubmit
		,	onComplete: umsCsvImportOnComplete
		}));
		return false;
	});
	jQuery('#umsCsvExportMarkersBtn').click(function(){
		var delimiter = jQuery('#umsCsvExportDelimiter').val() || ';';
		toeRedirect(createAjaxLinkUms({
			page: 'csv'
		,	action: 'exportMarkers'
		,	delimiter: delimiter
		,	onSubmit: umsCsvImportOnSubmit
		,	onComplete: umsCsvImportOnComplete
		}));
		return false;
	});
	jQuery('#umsCsvSaveBtn').click(function () {
		jQuery('#umsCsvForm').submit();
		return false;
	});
	jQuery('#umsCsvForm').submit(function () {
		jQuery(this).sendFormUms({
			btn: '#umsCsvSaveBtn'
		});
		return false;
	});

	/*jQuery('#umsCsvImportMarkersBtn').on('click', function () {
		jQuery('input[name="csv_import_file"]').click();
	});*/
});
function umsCsvImportOnSubmit() {
	var msg = jQuery('#umsCsvImportMsg');

	msg.showLoaderUms();
	msg.removeClass('toeErrorMsg');
	msg.removeClass('toeSuccessMsg');

	// Add CSV options to request data
	umsCsvImportData['delimiter'] = jQuery('#umsCsvExportDelimiter').val() || ';';
	//umsCsvImportData['overwrite_same_names'] = jQuery('#umsCsvOverwriteSameNames').attr('checked') ? 1 : 0;
}
function umsCsvImportOnComplete(file, res) {
	toeProcessAjaxResponseUms(res, 'umsCsvImportMsg');
}
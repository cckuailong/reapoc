var g_umsMarkerGroupFormChanged = false;

window.onbeforeunload = function(){
	// If there are at lease one unsaved form - show message for confirnation for page leave
	if(_umsIsMarkerGroupFormChanged()) {
		return 'You have unsaved changes in Marker Category form. Are you sure want to leave this page?';
	}
};
jQuery(document).ready(function() {
	function umsGetCurrentId() {
		return parseInt( jQuery('#umsMgrForm input[name="marker_group[id]"]').val() );
	}
	// Map saving form
	jQuery('#umsMgrForm').submit(function () {
		var currentId = umsGetCurrentId()
		,	firstTime = currentId ? false : true;

		jQuery(this).sendFormUms({
			btn: '#umsMgrSaveBtn'
		,	onSuccess: function (res) {
				if (!res.error) {
					_umsUnchangeMarkerGroupForm();
					if(firstTime) {
						if(res.data.edit_url) {
							window.location = res.data.edit_url;
						}
					}
				}
			}
		});
		return false;
	});
	jQuery('#umsMgrSaveBtn').click(function () {
		jQuery('#umsMgrForm').submit();
		return false;
	});
	jQuery('#umsMgrForm').find('input').change(function(){
		_umsChangeMarkerGroupForm();
	});
	jQuery('#umsUploadMarkerGroupClastererIconBtn').click(function(e){
		var custom_uploader;
		e.preventDefault();
		//If the uploader object has already been created, reopen the dialog
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}
		//Extend the wp.media object
		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Choose Image'
		,	button: {
				text: 'Choose Image'
			}
		,	multiple: false
		});
		//When a file is selected, grab the URL and set it as the text field's value
		custom_uploader.on('select', function(){
			var attachment = custom_uploader.state().get('selection').first().toJSON()
				,	iconPrevImg = jQuery('#umsMarkerGroupClastererIconPrevImg')
				,	width  = 53
				,	height = 'auto';

			iconPrevImg.attr('src', attachment.url);
			width = document.getElementById('umsMarkerGroupClastererIconPrevImg').naturalWidth;
			height = document.getElementById('umsMarkerGroupClastererIconPrevImg').naturalHeight;
			umsUpdateMarkerGroupClusterIcon(attachment.url, width, height);
		});
		//Open the uploader dialog
		custom_uploader.open();
	});
	jQuery('#umsDefaultMarkerGroupClastererIconBtn').click(function(e) {
		e.preventDefault();
		var defIconUrl = UMS_DATA.modPath + 'maps/img/m1.png';
		jQuery('#umsMarkerGroupClastererIconPrevImg').attr('src', defIconUrl);
		umsUpdateMarkerGroupClusterIcon(defIconUrl, 53, 52);
	});
});
// Marker Group form check change actions
function _umsIsMarkerGroupFormChanged() {
	return g_umsMarkerGroupFormChanged;
}
function _umsChangeMarkerGroupForm() {
	g_umsMarkerGroupFormChanged = true;
}
function _umsUnchangeMarkerGroupForm() {
	g_umsMarkerGroupFormChanged = false;
}
function umsUpdateMarkerGroupClusterIcon(url, width, height) {
	jQuery('input[name="marker_group[claster_icon]"]').val(url);
	jQuery('input[name="marker_group[claster_icon_width]"]').val(width);
	jQuery('input[name="marker_group[claster_icon_height]"]').val(height);
}
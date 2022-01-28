var	g_umsCurrentEditMarker = null
,	g_umsMarkerFormChanged = false
,	g_umsTinyMceMarkerEditorUpdateBinded = false
,   g_umsGrid = jQuery('#umsMarkersListGrid')
,	g_umsGridData = null;
jQuery(window).on('load',function(){
	// Build initial markers list
	g_umsMapLoadObserver.trigger(umsGetMapsEngine(umsMainMap), function() {
    g_umsGrid.jqGrid({
		url: umsMarkersTblDataUrl
	,	mtype: 'GET'
	,	datatype: 'json'
	,	colNames:[toeLangUms('ID'), toeLangUms('Icon'), toeLangUms('Title'), toeLangUms('Coords'), toeLangUms('Actions')]
	,	colModel: [
			{ name: 'id', index: 'id', key: true, sortable: true, width: '90', align: 'center' }
		,	{ name: 'icon_img', index: 'icon_img', sortable: false, width: '70', align: 'center' }
		,	{ name: 'title', index: 'title', sortable: true, align: 'center' }
		,	{ name: 'coords', index: 'coords', sortable: false, width: '90', align: 'center' }
		,	{ name: 'actions', index: 'actions', sortable: false, width: '100', align: 'center' }
	]
	,	width: jQuery('#umsMapRightStickyBar').width()
	,	height: 200
	//,	autowidth: true
	,	shrinkToFit: false
	,	sortname: 'sort_order'
	,	rowNum: 1000000000000
	,	viewrecords: true
	,	emptyrecords: toeLangUms('You have no markers for now.')
	,	loadComplete: function(res) {
			if(g_umsGridData === null)
				g_umsGridData = res.rows;
			umsRefreshMapMarkersList(res.rows);
			if(res.rows.length) {
				//g_umsMap.applyZoomTypeAdmin();	// Apply zoom type fit_bounds after all markers load in admin area
			}
			_umsResizeRightSidebar(jQuery('#umsMarkersListGrid'));
			jQuery('#umsMarkersSearchInput').show();
		}
	}).jqGrid('sortableRows', {
		update: function (e, ui) {
			var markersList = jQuery('#umsMarkersListGrid').jqGrid('getDataIDs');
			jQuery.sendFormUms({
				data: { mod: 'maps', action: 'resortMarkers', markers_list: markersList }
			,	onSuccess: function(res) {
					if(!res.error) {
						var sortOrder = jQuery('#umsMarkersListGrid').jqGrid('getGridParam', 'sortorder');

						jQuery('#umsMarkersListGrid').jqGrid('setGridParam', {
							sortname: 'sort_order',
							sortorder: sortOrder
						});
						jQuery('#umsMarkersListGrid').trigger('reloadGrid');
					}
				}
			});
		}
	});

    // Search by markers name functionality
    jQuery('#umsMarkersSearchInput').on('keyup', function (e) {
        var result = []
			, value = e.target.value
			, valueLength = value.length;

			if(valueLength === 0) {
				//console.log('Value length == 0');
				result = g_umsGridData;
			} else {
				result = g_umsGridData.filter(function (item) {
					var result = item.title.substr(0, valueLength) == value;
					return result;
				});
			}

		g_umsGrid.jqGrid('clearGridData');
		g_umsGrid.jqGrid('setGridParam', { datatype: 'local', data: result });
		g_umsGrid.trigger('reloadGrid');
    });
	// Markers form functionality
	jQuery('#umsAddNewMarkerBtn').click(function(){
		var currentEditId = parseInt( jQuery('#umsMarkerForm input[name="marker_opts[id]"]').val() );
		if(!currentEditId) {	// This was new marker
			var title = jQuery.trim( jQuery('#umsMarkerForm input[name="marker_opts[title]"]').val() );
			if(title && title != '') {	// Save it if there was some required changes
				jQuery('#umsMarkerForm').data('only-save', 1).submit();
			} else {
				var currentMarker = umsGetCurrentMarker();
				if(currentMarker) {
					currentMarker.removeFromMap();
				}
			}
		}
		umsOpenMarkerForm();
		// Add new marker - right after click on "Add new"
		_umsCreateNewMapMarker();
		return false;
	});
	jQuery('#umsSaveMarkerBtn').click(function(){
		jQuery('#umsMarkerForm').submit();
		return false;
	});
	jQuery('#umsMarkerDeleteBtn').click(function(){
		var markerTitle = jQuery('#umsMarkerForm [name="marker_opts[title]"]').val();
		if(markerTitle && markerTitle != '') {
			markerTitle = '"'+ markerTitle+ '"';
		} else {
			markerTitle = 'current';
		}
		if(confirm('Remove '+ markerTitle+ ' marker?')) {
			var currentMarkerIdInForm = g_umsCurrentEditMarker ? g_umsCurrentEditMarker.getId() : 0;
			var removeFinalClb = function() {
				if(currentMarkerIdInForm) {
					g_umsMap.removeMarker( currentMarkerIdInForm );
					jQuery('#umsMarkersListGrid').trigger('reloadGrid');
				}
				if(g_umsCurrentEditMarker) {
					g_umsCurrentEditMarker.removeFromMap();
				}
				umsResetMarkerForm();
			};
			if(currentMarkerIdInForm) {
				jQuery.sendFormUms({
					btn: this
					,	data: {action: 'removeMarker', mod: 'marker', id: currentMarkerIdInForm}
					,	onSuccess: function(res) {
						if(!res.error) {
							removeFinalClb();
						}
					}
				});
			} else {
				removeFinalClb();
			}
		}
		return false;
	});
	// Marker saving
	jQuery('#umsMarkerForm').submit(function(){
		var currentMapId = umsGetCurrentId()
		,	currentMarkerMapId = parseInt( jQuery('#umsMarkerForm input[name="marker_opts[map_id]"]').val() )
		,	coordX = jQuery('#umsMarkerForm input[name="marker_opts[coord_x]"]').val()
		,	coordY = jQuery('#umsMarkerForm input[name="marker_opts[coord_y]"]').val()
		,	onlySave = parseInt(jQuery(this).data('only-save'));

		if(currentMapId && !currentMarkerMapId) {
			jQuery('#umsMarkerForm input[name="marker_opts[map_id]"]').val( currentMapId );
		}
		jQuery('#umsMarkerForm input[name="marker_opts[description]"]').val( umsGetTxtEditorVal('markerDescription') );
		if(coordX == '' && coordY == '') {
			_umsCreateNewMapMarker();
		}
		if(onlySave) {
			jQuery(this).data('only-save', 0);
		}
		jQuery(this).sendFormUms({
			btn: jQuery('#umsSaveMarkerBtn')
		,	onSuccess: function(res) {
				if(!res.error) {
					if(!onlySave) {
						if(!res.data.update) {
							jQuery('#umsMarkerForm input[name="marker_opts[id]"]').val( res.data.marker.id );
							var marker = umsGetCurrentMarker();
							if(marker) {
								marker.setId(res.data.marker.id);
							}
						}
					}

					if(!currentMarkerMapId) {
						g_umsMapMarkersIdsAdded.push( res.data.marker.id );
					}
					if(!onlySave) {
						jQuery('#umsMarkersListGrid').trigger('reloadGrid');
					}
					_umsUnchangeMarkerForm();
					jQuery(document).trigger('umsAfterMarkerSave', umsGetCurrentMarker());

                    jQuery('#umsMapSaveBtn').click();
				}
			}
		});
		return false;
	});
	// Init window to choose marker
	umsInitIconsWnd();
	// Set base icon img
	umsSetIconImg();
	// Bind change marker description - with it's description in map preview
	setTimeout(function(){
		umsBindMarkerTinyMceUpdate();
		if(!g_umsTinyMceMarkerEditorUpdateBinded) {
			jQuery('#markerDescription-tmce.wp-switch-editor.switch-tmce').click(function(){
				setTimeout(umsBindMarkerTinyMceUpdate, 500);
			});
		}
	}, 500);
	jQuery('#markerDescription').keyup(function(){
		var marker = umsGetCurrentMarker();
		if(!marker) {
			_umsCreateNewMapMarker();
			marker = umsGetCurrentMarker();
		}
		if(marker) {
			marker.setDescription( umsGetTxtEditorVal('markerDescription') );
			marker.showInfoWnd();
            showLeafInfoWindow(marker);
		}
	});
	jQuery('#umsMarkerForm [name="marker_opts[title]"]').keyup(function(){
		var marker = umsGetCurrentMarker();
		if(!marker) {
			_umsCreateNewMapMarker();
			marker = umsGetCurrentMarker();
		}
		if(marker) {
			marker.setTitle( jQuery(this).val() );
			marker.showInfoWnd();
            showLeafInfoWindow(marker);
		}
	});
	var interval = setInterval(function(){
		if (g_umsMap) {
			clearInterval(interval);
			g_umsMap.geocodeSearchAutocomplete('#umsMarkerForm [name="marker_opts[address]"]', {
				msgEl: ''
				,	onSelect: function(item, event, ui) {
					if(item) {
						jQuery('#umsMarkerForm [name="marker_opts[coord_x]"]').val(item.lat);
						jQuery('#umsMarkerForm [name="marker_opts[coord_y]"]').val(item.lng).trigger('change');
					}
				}
			});
		}
	},100);

	jQuery('#umsMarkerForm').find('input[name="marker_opts[coord_x]"],input[name="marker_opts[coord_y]"]').change(function(){
		var newX = jQuery('#umsMarkerForm [name="marker_opts[coord_x]"]').val()
		,	newY = jQuery('#umsMarkerForm [name="marker_opts[coord_y]"]').val();
		var marker = umsGetCurrentMarker();
		if(marker) {
			marker.setPosition(newX, newY);
		} else {	// If there are no marker on map - set it and re-position it right into new position
			_umsCreateNewMapMarker({coord_x: newX, coord_y: newY});
		}
	});
	jQuery('#umsMarkerForm').find('input,textarea,select').change(function(){
		_umsChangeMarkerForm();
	});
	// Make old markers table - sortable
	/*jQuery('#umsMarkerList').sortable({
		revert: true
	,	items: '.umsMapMarkerRow'
	,	placeholder: 'ui-sortable-placeholder'
	,	update: function(event, ui) {
			var mapId = umsGetCurrentId();
			var msgEl = jQuery('#umsMarkersSortMsg').length ? jQuery('#umsMarkersSortMsg') : jQuery('<div id="umsMarkersSortMsg" />')
	 	,	markersList = [];
			jQuery('#umsMarkerList').find('.umsMapMarkerRow:not(#markerRowTemplate)').each(function(){
				markersList.push( jQuery(this).data('id') );
			});
			ui.item.find('.egm-marker-icon').append( msgEl );
			jQuery.sendFormUms({
				msgElID: 'umsMarkersSortMsg'
			,	data: {mod: 'maps', action: 'resortMarkers', markers_list: markersList, map_id: mapId}
			,	onSuccess: function(res) {	}
			});
		}
	});*/
	});
});
function umsSetCurrentMarker(marker) {
	g_umsCurrentEditMarker = marker;
}
function umsGetCurrentMarker() {
	return g_umsCurrentEditMarker;
}
// Markers form check change actions
function _umsIsMarkerFormChanged() {
	return g_umsMarkerFormChanged;
}
function _umsChangeMarkerForm() {
	g_umsMarkerFormChanged = true;
}
function _umsUnchangeMarkerForm() {
	g_umsMarkerFormChanged = false;
}
function umsOpenMarkerForm() {
	umsShowMarkerForm();
	umsResetMarkerForm();
}
function umsShowMarkerForm() {
	var markerFormIsVisible = jQuery('#umsMarkerForm').is(':visible');
	if(!markerFormIsVisible) {
		jQuery('#umsMapPropertiesTabs').wpTabs('activate', '#umsMarkerTab');
	}
}
function umsHideMarkerForm() {
	var markerFormIsVisible = jQuery('#umsMarkerForm').is(':visible');
	if(markerFormIsVisible) {
		jQuery('#umsSaveMarkerBtn').hide( g_umsAnimationSpeed );
		jQuery('#umsAddNewMarkerBtn').animate({
			width: '100%'
		}, g_umsAnimationSpeed);
		jQuery('#umsMarkerForm').slideUp( g_umsAnimationSpeed );
	}
}
function umsResetMarkerForm() {
	jQuery('#umsMarkerForm')[0].reset();
	jQuery('#umsMarkerForm input[name="marker_opts[id]"]').val('');
	jQuery('#umsMarkerForm input[name="marker_opts[icon]"]').val( 1 );

	jQuery('#umsMarkerForm input[name="marker_opts[params][show_description]"]').prop('checked', false);
	umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][show_description]"]') );

	jQuery('#umsMarkerForm input[name="marker_opts[params][marker_link]"]').prop('checked', false);
	umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][marker_link]"]') );

	jQuery('#umsMarkerForm input[name="marker_opts[params][marker_link_new_wnd]"]').prop('checked', false);
	umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][marker_link_new_wnd]"]') );

	jQuery('#umsMarkerForm input[name="marker_opts[params][description_mouse_hover]"]').prop('checked', false);
	umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][description_mouse_hover]"]') );

	if(jQuery('#umsMarkerForm input[name="marker_opts[params][description_mouse_hover]"]').prop('checked') === false)
		umsHideCloseDescriptionCheckbox();
	jQuery('#umsMarkerForm input[name="marker_opts[params][description_mouse_leave]"]').prop('checked', false);
	jQuery('#umsMarkerForm input[name="marker_opts[params][clasterer_exclude]"]').prop('checked', false);

	umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][description_mouse_leave]"]') );
	umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][clasterer_exclude]"]') );

	jQuery('#umsMarkerForm input[name="marker_opts[params][marker_list_def_img]"]').prop('checked', false);
	umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][marker_list_def_img]"]') );
	jQuery('#umsMarkerForm input[name="marker_opts[params][marker_list_def_img]"]').trigger('change');

	umsSetIconImg();
	umsAddLinkOptions();
	jQuery(document).trigger('umsAfterResetMarkerForm');
}
function _umsCreateNewMapMarker(params) {
	params = params || {};
	var iconSize = umsGetIconSize();
	var newMarkerData = {
		icon: umsGetIconPath()
	,	icon_data: iconSize
	,	draggable: true
	,	dragend: _umsMarkerDragEndClb
	};
	var lat = 0
	,	lng = 0;
	if(params.coord_x && params.coord_y) {
		newMarkerData.coord_x = lat = parseFloat( params.coord_x );
		newMarkerData.coord_y = lng = parseFloat( params.coord_y );
	} else {
		var mapCenter = g_umsMap.getCenter();
		newMarkerData.position = mapCenter;
		lat = mapCenter.lat;
		lng = mapCenter.lng;
	}
	umsSetCurrentMarker( g_umsMap.addMarker( newMarkerData ) );
	jQuery('#umsMarkerForm [name="marker_opts[coord_x]"]').val( lat );
	jQuery('#umsMarkerForm [name="marker_opts[coord_y]"]').val( lng );
	jQuery('#umsMarkerForm [name="marker_opts[marker_group_id][]"]').val('').trigger("chosen:updated");
}
function umsMarkerEditBtnClick(btn){
	var markerId = jQuery(btn).data('marker_id');
	umsOpenMarkerEdit( markerId );
}
function umsMarkerDelBtnClick(btn){
	var markerId = jQuery(btn).data('marker_id')
	,	markerRow = jQuery(btn).parents('tr:first');
	umsRemoveMarkerFromMapTblClick(markerId, {row: markerRow});
}
function umsOpenMarkerEdit(id) {
	umsOpenMarkerForm();
	var marker = g_umsMap.getMarkerById( id );
	if(marker) {
		var markerParams = marker.getRawMarkerParams();
		jQuery('#umsMarkerForm input[name="marker_opts[title]"]').val( markerParams.title );
		umsSetTxtEditorVal('markerDescription', markerParams.description);

		jQuery('#umsMarkerForm input[name="marker_opts[icon]"]').val( markerParams.icon_data.id );
		jQuery('#umsMarkerForm input[name="marker_opts[address]"]').val( markerParams.address );

		jQuery('#umsMarkerForm input[name="marker_opts[coord_x]"]').val( markerParams.coord_x );
		jQuery('#umsMarkerForm input[name="marker_opts[coord_y]"]').val( markerParams.coord_y );

		jQuery('#umsMarkerForm input[name="marker_opts[id]"]').val( markerParams.id );
		var markerGroupsIds = markerParams.marker_group_ids;
		jQuery('#umsMarkerForm select[name="marker_opts[marker_group_id][]"] option:selected').prop("selected", false);
		for(var i = 0; i < markerGroupsIds.length; i++ ){
			jQuery('#umsMarkerForm select[name="marker_opts[marker_group_id][]"] option[value="'+markerGroupsIds[i]+'"]').prop("selected", true);
		}
		jQuery('#marker_opts_marker_group_id').trigger("chosen:updated");

		if(markerParams.period_from) {
			jQuery('#umsMarkerForm input[name="marker_opts[period_date_from]"]').val(markerParams.period_from);
		}
		if(markerParams.period_to) {
			jQuery('#umsMarkerForm input[name="marker_opts[period_date_to]"]').val(markerParams.period_to);
		}

		if(parseInt(markerParams.params.show_description)){
			jQuery('#umsMarkerForm input[name="marker_opts[params][show_description]"]').prop('checked', true);
			umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][show_description]"]') );
		}
		if(parseInt(markerParams.params.marker_link)){
			jQuery('#umsMarkerForm input[name="marker_opts[params][marker_link]"]').prop('checked', true);
			umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][marker_link]"]') );
			jQuery('#umsMarkerForm input[name="marker_opts[params][marker_link_src]"]').val( markerParams.params.marker_link_src );
			if(parseInt(markerParams.params.marker_link_new_wnd)){
				jQuery('#umsMarkerForm input[name="marker_opts[params][marker_link_new_wnd]"]').prop('checked', true);
				umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][marker_link_new_wnd]"]') );
			}
		}
		if(parseInt(markerParams.params.description_mouse_hover)) {
			jQuery('#umsMarkerForm input[name="marker_opts[params][description_mouse_hover]"]').prop('checked', true);
			umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][description_mouse_hover]"]') );
			umsShowCloseDescriptionCheckbox();
		} else {
			umsHideCloseDescriptionCheckbox();
		}
		if(parseInt(markerParams.params.description_mouse_leave)) {
			jQuery('#umsMarkerForm input[name="marker_opts[params][description_mouse_leave]"]').prop('checked', true);
			umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][description_mouse_leave]"]') );
		}
		if(parseInt(markerParams.params.clasterer_exclude)) {
			jQuery('#umsMarkerForm input[name="marker_opts[params][clasterer_exclude]"]').prop('checked', true);
			umsCheckUpdate( jQuery('#umsMarkerForm input[name="marker_opts[params][clasterer_exclude]"]') );
		}
		if(parseInt(markerParams.params.marker_list_def_img)) {
			var markerListDefImg = jQuery('#umsMarkerForm input[name="marker_opts[params][marker_list_def_img]"]')
			,	markerListDefImgUrl = jQuery('#umsMarkerForm input[name="marker_opts[params][marker_list_def_img_url]"]');

			markerListDefImgUrl.val(markerParams.params.marker_list_def_img_url);
			markerListDefImg.prop('checked', true);
			umsCheckUpdate( markerListDefImg );
			markerListDefImg.trigger('change');
		}
		umsAddLinkOptions();
		umsSetIconImg();
		umsSetCurrentMarker( marker );
		marker.showInfoWnd();
        g_umsMap.setCenter(markerParams.coord_x, markerParams.coord_y);
		jQuery(document).trigger('umsMarkerFormEdit', marker);
        showLeafInfoWindow(marker);
	}
}
function showLeafInfoWindow(marker){
    if ((marker._map._engine == 'leaflet' || marker._map._engine == 'mapbox') && !marker._infoWindow.isOpen()) {
        marker._markerObj.fireEvent('click');
    }
}
function umsRemoveMarkerFromMapTblClick(markerId, params) {
	params = params || {};
	var markerTitle = params.row ? params.row.find('td[aria-describedby="umsMarkersListGrid_title"]').text() : ''
	,	btn = params.row ? params.row : params.btn;
	if(!confirm('Remove "'+ markerTitle+ '" marker?')) {
		return false;
	}
	if(markerId == ''){
		return false;
	}
	jQuery.sendFormUms({
		btn: btn
		,	data: {action: 'removeMarker', mod: 'marker', id: markerId}
		,	onSuccess: function(res) {
			if(!res.error){
				g_umsMap.removeMarker( markerId );
				jQuery('#umsMarkersListGrid').trigger('reloadGrid');
				var currentEditMarkerId = parseInt( jQuery('#umsMarkerForm input[name="marker_opts[id]"]').val() );
				if(currentEditMarkerId && currentEditMarkerId == markerId) {
					umsResetMarkerForm();
					//umsHideMarkerForm();
				}
			}
		}
	});
}
function _umsMarkerDragEndClb() {
	var currentMarkerIdInForm = g_umsCurrentEditMarker ? g_umsCurrentEditMarker.getId() : 0
		,	draggedId =  this.getId()
		,	self = this;
	if((currentMarkerIdInForm && currentMarkerIdInForm == draggedId) || (!currentMarkerIdInForm && !draggedId)) {
		jQuery('#umsMarkerForm input[name="marker_opts[coord_x]"]').val( this.lat() );
		jQuery('#umsMarkerForm input[name="marker_opts[coord_y]"]').val( this.lng() );
	}
	if(draggedId) {	// Just save it in database
		jQuery.sendFormUms({
			data: {mod: 'marker', action: 'updatePos', id: draggedId, lat: this.lat(), lng: this.lng()}
		,	onSuccess: function(res) {
				if(!res.error) {
					jQuery('#umsMarkersListGrid').trigger('reloadGrid');
				}
			}
		});
	}
}
function drawNewIcon(icon){
	if(typeof(icon.data) == undefined){
		return;
	}
	jQuery('#umsMarkerForm input[name="marker_opts[icon]"]').val(icon.id);
	var newIcon = '<li class="previewIcon" data-id="'+ icon.id+ '" data-width="'+ icon.width+ '" data-height="'+ icon.height+ '" title="'+ icon.title+ '"><img src="'+ icon.url+ '"><i class="fa fa-times" aria-hidden="true"></i></li>';
	jQuery('ul.iconsList').append(newIcon);
	umsSetIconImg();
}
function umsSetIconImg() {
	var id = parseInt( jQuery('#umsMarkerForm input[name="marker_opts[icon]"]').val() );
	jQuery('#umsMarkerIconPrevImg').attr('src', jQuery('.previewIcon[data-id="'+ id+ '"] img').attr('src'));
}
function umsGetIconPath() {
	return jQuery('#umsMarkerIconPrevImg').attr('src');
}
function umsGetIconSize() {
	var id = parseInt( jQuery('#umsMarkerForm input[name="marker_opts[icon]"]').val() );
	var $icon = jQuery('#umsIconsWnd .previewIcon[data-id="'+ id+ '"]');
	return {width: $icon.data('width'), height: $icon.data('height')};
}
function umsGetDialogClasses() {
	return {
		markerIcon: 'umsMarkerIconWnd'
	,	curUserPosIcon: 'umsCurUserPosIconWnd'
	};
}
function umsInitMarkerIconsDialogWnd() {
	if(jQuery('#umsIconsWnd').hasClass('ui-dialog-content')) {
		return jQuery('#umsIconsWnd');
	}
	var dialodClasses =  umsGetDialogClasses();

	return jQuery('#umsIconsWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: 540
	,	height: 600
	,	beforeClose: function(e, ui) {
			for(var i in dialodClasses) {
				if(jQuery(this).hasClass(dialodClasses[i]))
					jQuery(this).removeClass(dialodClasses[i]);
			}
		}
	});
}
function umsInitIconsWnd() {
	var $container = umsInitMarkerIconsDialogWnd()
	,	dialodClasses =  umsGetDialogClasses();

	jQuery('#umsMarkerIconBtn').click(function(){
		$container.addClass(dialodClasses.markerIcon);
		$container.dialog('open');
		return false;
	});
	jQuery('#umsIconsWnd').on('click', '.previewIcon img', function(e){
		if($container.hasClass(dialodClasses.markerIcon)) {
			var $parentContainer = jQuery(this).parent()
			,	newId = $parentContainer.data('id')
			,	width = $parentContainer.data('width')
			,	height = $parentContainer.data('height');
			jQuery('#umsMarkerForm input[name="marker_opts[icon]"]').val( newId );
			umsSetIconImg();
			var marker = umsGetCurrentMarker();
			if(!marker) {
				_umsCreateNewMapMarker();
				marker = umsGetCurrentMarker();
			}
			if(marker) {
				marker.setIconSize(width, height);
				marker.setIcon( umsGetIconPath() );
			}
			$container.dialog('close');
		}
		return false;
	});

	jQuery('#umsIconsWnd').on('click', '.previewIcon i', function(e){
		e.preventDefault();
		var icon = jQuery(this)
		,   iconWrapper = icon.closest('.previewIcon')
		,   iconId = iconWrapper.attr('data-id');
      if(confirm('Remove icon with ID: '+ iconId+ '? If you delete this icon, all markers that use it will no longer be able to use this image.')) {
   		jQuery.sendFormUms({
   			data: {action: 'remove', mod: 'icons', id: iconId}
   		,	onSuccess: function(res) {
   				iconWrapper.remove();
   			}
   		});
      }
	});
	/*
	 * wp media upload
	 *
	 */
	jQuery('#umsUploadIconBtn').click(function(e){
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
			,	respElem = jQuery('.umsUplRes')
			,	sendData = {
					page: 'icons'
				,	action: 'saveNewIcon'
				,	icon: {
						url: attachment.url
					}
				};
			if(typeof(attachment.title) !== 'undefined'){
				sendData.icon.title = attachment.title;
			}
			if(typeof(attachment.description) !== 'undefined'){
				sendData.icon.description = attachment.description;
			}
			sendData.icon.width = attachment.width;
			sendData.icon.height = attachment.height;
			jQuery.sendFormUms({
				msgElID: respElem
			,	data: sendData
			,	onSuccess: function(res){
					if(!res.error) {
						drawNewIcon(res.data);
					} else {
						respElem.html(data.error.join(','));
					}
				}
			});
		});
		//Open the uploader dialog
		custom_uploader.open();
	});
}
function umsAddLinkOptions() {
	var markerLink = jQuery('#marker_link').prop('checked');
	if (markerLink) {
		jQuery('#link_options').css('display', 'inline');
	} else {
		jQuery('#link_options').css('display', 'none');
	}
}
function umsRefreshMapMarkers(map, markers) {
	//g_umsMapLoadObserver.trigger(umsGetMapsEngine(), function() {

		map.clearMarkers();
		markers = _umsPrepareMarkersListAdmin( markers );
		for(var i in markers) {
			var newMarker = map.addMarker( markers[i] );
			newMarker.setTitle( markers[i].title, true );
			newMarker.setDescription( markers[i].description );
		}
		map.markersRefresh();
	//});
}
function _umsPrepareMarkersListAdmin(markers) {
	return _umsPrepareMarkersList(markers, {
		dragend: _umsMarkerDragEndClb
	});
}
// New markers list version method
function umsRefreshMapMarkersList(markersList) {
	umsRefreshMapMarkers(g_umsMap, markersList);
	var currentFormMarker = parseInt( jQuery('#umsMarkerForm input[name="marker_opts[id]"]').val() );
	if(currentFormMarker) {
		var editMapMarker = g_umsMap.getMarkerById(currentFormMarker);
		if(editMapMarker) {
			umsSetCurrentMarker( editMapMarker );
			editMapMarker.showInfoWnd();
		}
	}
}
function umsBindMarkerTinyMceUpdate() {
	if(!g_umsTinyMceMarkerEditorUpdateBinded && typeof(tinyMCE) !== 'undefined' && tinyMCE.editors) {
		if(tinyMCE.editors.markerDescription) {
			tinyMCE.editors.markerDescription.onKeyUp.add(function(){
				var marker = umsGetCurrentMarker();

				if(!marker) {
					_umsCreateNewMapMarker();
					marker = umsGetCurrentMarker();
				}
				if(marker) {
					marker.setDescription( umsGetTxtEditorVal('markerDescription') );
					marker.showInfoWnd();
				}
			});
			g_umsTinyMceMarkerEditorUpdateBinded = true;
		}
	}
}
// Old markers list version method
/*function umsRefreshMapMarkersList(fromServer, justTable) {
	var shell = jQuery('#umsMarkerList');
	var buildListClb = function(markersList) {
		if(umsMainMap)
			umsMainMap.markers = markersList;
		if(!justTable) {
			umsRefreshMapMarkers(g_umsMap, markersList);
			var currentFormMarker = parseInt( jQuery('#umsMarkerForm input[name="marker_opts[id]"]').val() );
			if(currentFormMarker) {
				var editMapMarker = g_umsMap.getMarkerById(currentFormMarker);
				if(editMapMarker) {
					umsSetCurrentMarker( editMapMarker );
					editMapMarker.showInfoWnd();
				}
			}
		}
		//g_umsMap.setMarkersParams( markersList );
		shell.find('.umsMapMarkerRow:not(#markerRowTemplate)').remove();
		if(markersList && markersList.length) {
			for(var i = 0; i < markersList.length; i++) {
				var newRow = jQuery('#markerRowTemplate').clone();
				newRow.find('.egm-marker-icon img').attr('src', markersList[i].icon_data.path);
				newRow.find('.egm-marker-title').html(markersList[i].title);
				newRow.find('.egm-marker-latlng').html(parseFloat(markersList[i].coord_x).toFixed(2)+ '"N '+ parseFloat(markersList[i].coord_y).toFixed(2)+ '"E');
				newRow.data('id', markersList[i].id);
				newRow.find('.egm-marker-edit').click(function(){
					var markerRow = jQuery(this).parents('.umsMapMarkerRow:first');
					umsOpenMarkerEdit( markerRow.data('id') );
					return false;
				});
				newRow.find('.egm-marker-remove').click(function(){
					var markerRow = jQuery(this).parents('.umsMapMarkerRow:first');
					umsRemoveMarkerFromMapTblClick(markerRow.data('id'), {row: markerRow});
					return false;
				});
				newRow.removeAttr('id').show();
				shell.append( newRow );
			}
		}
		_umsResizeRightSidebar();
	};
	if(fromServer) {
		shell.find('.egm-marker').css('opacity', '0.5');
		shell.addClass('supsystic-inline-loader');
		var currentMapId = umsGetCurrentId();
		jQuery.sendFormUms({
			data: {mod: 'marker', action: 'getMapMarkers', map_id: (currentMapId ? currentMapId : 0), 'added_marker_ids': g_umsMapMarkersIdsAdded}
			,	onSuccess: function(res) {
				if(!res.error) {
					shell.find('.egm-marker').css('opacity', '1');
					shell.removeClass('supsystic-inline-loader');
					buildListClb( res.data.markers );
				}
			}
		});
	} else {
		if(umsMainMap)
			buildListClb( umsMainMap.markers );
	}
}*/

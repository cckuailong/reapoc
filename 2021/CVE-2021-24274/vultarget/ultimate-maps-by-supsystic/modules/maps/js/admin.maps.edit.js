var g_umsMap = null
,	g_umsMapMarkersIdsAdded = []	// Markers, added for map
,	g_umsMapShapesIdsAdded = []	// Shapes, added for map
,	g_umsEditMap = false	// Adding or editing map
,	g_umsMapFormChanged = false
,	g_umsMarkerTitleColorTimeoutSet = false
,	g_umsMarkerTitleColorLast = ''
,	g_umsMarkerBgColorTimeoutSet = false
,	g_umsMapAuthorizationFailWnd = false
,	g_umsIsNeedTriggerZoomTypeAdmin = false;
window.onbeforeunload = function(){
	// TODO: Uncomment after main development will be ready
	// If there are at lease one unsaved form - show message for confirnation for page leave
	/*if(_umsIsMapFormChanged()) {
		return 'You have unsaved changes in Map form. Are you sure want to leave this page?';
	}
	if(_umsIsMarkerFormChanged()) {
		return 'You have unsaved changes in Marker form. Are you sure want to leave this page?';
	}
	if(UMS_DATA.isPro && _umsIsShapeFormChanged()) {
		return 'You have unsaved changes in Figure form. Are you sure want to leave this page?';
	}
	if(UMS_DATA.isPro && _umsIsHeatmapFormChanged()) {
		return 'You have unsaved changes in Heatmap form. Are you sure want to leave this page?';
	}*/
};
// Right sidebar height re-calc
jQuery(window).bind('resize', _umsResizeRightSidebar);
jQuery(window).bind('orientationchange', _umsResizeRightSidebar);

jQuery(document).ready(function(){
	var propTabs = jQuery('#umsMapPropertiesTabs')
	,	$contactFormsListWnd = jQuery('#umsInsertToContactFormWnd')
	,	contactFormBtn = jQuery('#umsInsertToContactForm')
	,	mapMainBtns = jQuery('#umsMapMainBtns')
	,	markerMainBtns = jQuery('#umsMarkerMainBtns')
	,	shapeMainBtns = jQuery('#umsShapeMainBtns')
	,	heatmapMainBtns = jQuery('#umsHeatmapMainBtns')
	,	markerList = jQuery('#umsMarkerList')
	,	shapeList = jQuery('#umsShapeList')
	,	rightStickyBar = jQuery('#umsMapRightStickyBar');

	propTabs.wpTabs({
		change: function(selector) {
			switch(selector) {
				case '#umsMarkerTab':
					if(!UMS_DATA.isPro) {
						rightStickyBar.show();
					}
					mapMainBtns.hide();
					markerMainBtns.show();
					shapeMainBtns.hide();
					heatmapMainBtns.hide();
					markerList.show();
					shapeList.hide();
					break;
				case '#umsShapeTab':
					if(UMS_DATA.isPro) {
						mapMainBtns.hide();
						markerMainBtns.hide();
						shapeMainBtns.show();
						heatmapMainBtns.hide();
						markerList.hide();
						shapeList.show();
					} else {
						rightStickyBar.hide();
					}
					break;
				case '#umsHeatmapTab':
					if(!UMS_DATA.isPro) {
						rightStickyBar.hide();
					}
					mapMainBtns.hide();
					markerMainBtns.hide();
					shapeMainBtns.hide();
					heatmapMainBtns.show();
					markerList.hide();
					shapeList.hide();
					break;
				case '#umsMapTab': default:
					if(!UMS_DATA.isPro) {
						rightStickyBar.show();
					}
					mapMainBtns.show();
					markerMainBtns.hide();
					shapeMainBtns.hide();
					heatmapMainBtns.hide();
					markerList.show();
					shapeList.hide();
					break;
			}
		}
	});
	propTabs.show();
	// TODO: Contact form and membership integration - in next versions
	/*$contactFormsListWnd.dialog({
		modal:    true
	,	autoOpen: false
	,	width: 540
	,	height: 'auto'
	,	buttons:  {
			Cancel: function() {
				$contactFormsListWnd.dialog('close');
			}
		,	Select: function() {
				var formSelect = $contactFormsListWnd.find('select[name="contact_form"]');

				if(formSelect.length && typeof(umsContactFormEditUrl) != 'undefined') {
					var id = formSelect.val();

					window.open(umsContactFormEditUrl + '&id=' + id + '&map_id=' + g_umsMap.getId() + '#cfsFormFields', '_blank');
					$contactFormsListWnd.dialog('close');
				}
			}
		}
	,	open: function() {
			if(!$contactFormsListWnd.find('select[name="contact_form"]').length) {
				$contactFormsListWnd.next('.ui-dialog-buttonpane').find('button:last-child').hide();
			}
		}
	});
	contactFormBtn.click(function(){
		$contactFormsListWnd.dialog('open');
		return false;
	});

	jQuery('#membershipPropEnable').on('ifChanged', function() {
		if(jQuery('#membershipPropEnable:checked').length) {
			jQuery('#membershipHiddenEnable').val('1');
		} else {
			jQuery('#membershipHiddenEnable').val('0');
		}

	});*/

	// Preview map definition
	umsMainMap = typeof(umsMainMap) === 'undefined' ? null : umsMainMap;
	var previewMapParams = {}
	,	additionalData = {};

	if(umsMainMap) {
		previewMapParams = umsMainMap;
		additionalData.markerGroups = typeof(umsMainMap.marker_groups) != 'undefined' ? umsMainMap.marker_groups : [];
		g_umsEditMap = true;
	}
	previewMapParams.view_id = jQuery('#umsViewId').val();
	/*if(previewMapParams.enable_custom_map_controls == 1) {
		umsAddCustomControlsOptions();
	}*/
	g_umsMapLoadObserver.trigger(umsGetMapsEngine(previewMapParams), function() {
		g_umsMap = umsMapLoader.initMap('#umsMapPreview', previewMapParams, additionalData);
		if(!g_umsEditMap) {
			//jQuery('#umsMapForm input[name="map_opts[map_center][coord_x]"]').val(g_umsMap.getCenter().lat());
			//jQuery('#umsMapForm input[name="map_opts[map_center][coord_y]"]').val(g_umsMap.getCenter().lng());
		}
		/*if(umsMainMap && umsMainMap.markers) {
			umsRefreshMapMarkers(g_umsMap, umsMainMap.markers);
		}*/
		// Map saving form
		jQuery('#umsMapForm').submit(function(){
			var currentId = umsGetCurrentId()
			,	firstTime = currentId ? false : true
			,	$currMapForm = jQuery(this)
			/*,	appendDataVal = {
					add_marker_ids: g_umsMapMarkersIdsAdded, add_shape_ids: g_umsMapShapesIdsAdded
				}*/;
			/*if(jQuery('[name="engine_from_req"]').length) {
				console.log(jQuery('[name="engine_from_req"]').val());
				//appendData.engine_from_req = jQuery('[name="engine_from_req"]').val();
			}*/
			$currMapForm.sendFormUms({
				btn: '#umsMapSaveBtn'
			,	appendData: {
					add_marker_ids: g_umsMapMarkersIdsAdded, add_shape_ids: g_umsMapShapesIdsAdded
				}
			,	onSuccess: function(res) {
					if(!res.error) {
						if(res.data.map_id) {
							jQuery('#umsMapForm input[name="map_opts[id]"]').val( res.data.map_id );

							// Update Markers table link
							var mrParams = URLToArray(umsMarkersTblDataUrl)
							,	newMarkersTblUrl = umsMarkersTblDataUrl.substring(0, umsMarkersTblDataUrl.indexOf('?') + 1);

							mrParams['map_id'] = res.data.map_id;
							mrParams = ArrayToURL(mrParams);
							newMarkersTblUrl += mrParams;
							jQuery("#umsMarkersListGrid").jqGrid('setGridParam', { url: newMarkersTblUrl });

							// Update Shapes table link
							/*if(UMS_DATA.isPro) {
								var shParams = URLToArray(umsShapesTblDataUrl)
								,	newShapesTblUrl = umsShapesTblDataUrl.substring(0, umsShapesTblDataUrl.indexOf('?') + 1);

								shParams['map_id'] = res.data.map_id;
								shParams = ArrayToURL(shParams);
								newShapesTblUrl += shParams;
								jQuery("#umsShapesListGrid").jqGrid('setGridParam', { url: newShapesTblUrl });
							}*/
						}
						if(firstTime) {
							// Do reload here
							umsCheckShortcode();
							if (res.data.edit_url) {
								toeRedirect(res.data.edit_url);
								return;
								/*setBrowserUrl( res.data.edit_url );
								jQuery('.supsystic-main-navigation-list li').removeClass('active');
								jQuery('.supsystic-main-navigation-list li[data-tab-key="maps"]').addClass('active');*/
							}
							/*g_umsMapMarkersIdsAdded = [];
							g_umsMapShapesIdsAdded = [];
							umsMainMap = res.data.map;*/
							// #227
							// window.location.reload();
						}
						if(_umsIsMarkerFormChanged() && jQuery('#umsMarkerForm input[name="marker_opts[title]"]').val() != '') {
							jQuery('#umsMarkerForm').submit();
						}
						// Maybe here should be the saving of shape and heatmap forms
						_umsUnchangeMapForm();
						$currMapForm.trigger('umsSaved');
					}
				}
			});
			return false;
		});
		jQuery('#umsCopyTextCodeExamples').change(function(){
			umsCheckShortcode();
		});
		jQuery('#umsMapSaveBtn').click(function(){
			jQuery('#umsMapForm').submit();
			return false;
		});
		jQuery('#umsMapDeleteBtn').click(function(){
			var mapId = parseInt( jQuery('#umsMapForm input[name="map_opts[id]"]').val() );
			if(mapId) {
				if(confirm(toeLangUms('Are you sure want to delete current map?'))) {
					jQuery.sendFormUms({
						btn: this
					,	data: {mod: 'maps', action: 'remove', id: mapId}
					,	onSuccess: function(res) {
							if(!res.error) {
								toeRedirect(umsMapsListUrl);
							}
						}
					});
				}
			}
			return false;
		});
		// Check - should we show shortcode block or not
		umsCheckShortcode();
		// Extended options block
		jQuery('#umsExtendOptsBtn').click(function(){
			jQuery('#umsExtendOptsBtnShell').slideUp( g_umsAnimationSpeed );
			jQuery('#umsExtendOptsShell').slideDown( g_umsAnimationSpeed );
			return false;
		});
		// Map type control style
		jQuery('#umsMapForm select[name="map_opts[navigation_bar_mode]"]').change(function(){
			var newType = jQuery(this).val();
			g_umsMap.setNavigationBarMode(newType);
		});
		// Map zoom control style
		jQuery('#umsMapForm select[name="map_opts[zoom_control]"]').change(function(e){
			var newType = jQuery(this).val();
			g_umsMap.enbZoom(newType === 'none' ? false : true);
		});
		// Map pan view control
		jQuery('#umsMapForm input[name="map_opts[pan_control]"]').change(function(){
			// Remember - that this is not actually checkbox, we detect hidden field value here, @see htmlUms::checkboxHiddenVal()
			g_umsMap.enbDraggable(jQuery(this).val());
		});
		// Map overview control style
		jQuery('#umsMapForm select[name="map_opts[overview_control]"]').change(function(){
			var newType = jQuery(this).val();
			if(newType !== 'none') {
				g_umsMap.set('overviewMapControlOptions', {
					opened: newType === 'opened' ? true : false
				}).set('overviewMapControl', true);
			} else {
				g_umsMap.set('overviewMapControl', false);
			}
		});
		// Is map draggable
		jQuery('#umsMapForm select[name="map_opts[draggable]"]').change(function(){
			// Remember - that this is not actually checkbox, we detect hidden field value here, @see htmlUms::checkboxHiddenVal()
			g_umsMap.enbDraggable(jQuery(this).val());
		});
		// Enable Double Click to zoom
		// TODO: Make it changable - there are problem that it's need to add / remove event listener to do this in bing - umsBingMap.prototype._afterInit
		/*jQuery('#umsMapForm input[name="map_opts[dbl_click_zoom]"]').change(function(){
			// Remember - that this is not actually checkbox, we detect hidden field value here, @see htmlUms::checkboxHiddenVal()
			g_umsMap.enbWheelZoom(parseInt(jQuery(this).val()));
		});*/
		// Mouse zoom enabling
		jQuery('#umsMapForm input[name="map_opts[mouse_wheel_zoom]"]').change(function(){
			// Remember - that this is not actually checkbox, we detect hidden field value here, @see htmlUms::checkboxHiddenVal()
			g_umsMap.enbWheelZoom(parseInt(jQuery(this).val()));
		});
		// Map center
		g_umsMap.geocodeSearchAutocomplete('#umsMapForm [name="map_opts[map_center][address]"]', {
			msgEl: ''
		,	onSelect: function(item, event, ui) {
				if(item) {
					jQuery('#umsMapForm input[name="map_opts[map_center][coord_x]"]').val(item.lat);
					jQuery('#umsMapForm input[name="map_opts[map_center][coord_y]"]').val(item.lng);
					g_umsMap.setCenter(item.lat, item.lng);
				}
			}
		});
		jQuery('#umsMapForm [name="map_opts[map_center][coord_x]"], #umsMapForm [name="map_opts[map_center][coord_y]"]').on('change', function() {
			var lat = jQuery.trim(jQuery('#umsMapForm [name="map_opts[map_center][coord_x]"]').val())
			,	lng = jQuery.trim(jQuery('#umsMapForm [name="map_opts[map_center][coord_y]"]').val());
			g_umsMap.setCenter(lat, lng);
		});
		// TODO: Add drag here
		g_umsMap.addEventListener('dragend', function(){
			var center = g_umsMap.getCenter();
			jQuery('#umsMapForm input[name="map_opts[map_center][coord_x]"]').val(center.lat);
			jQuery('#umsMapForm input[name="map_opts[map_center][coord_y]"]').val(center.lng);
			var coord = '['+center.lat+','+center.lng+']';
			var address = getLocationByCoordinates(coord);
		});
		// Map zoom
		/*jQuery('#umsMapForm [name="map_opts[zoom_type]"]').change(function(){
			var value = jQuery(this).val()
			,	zoomLevelOpt = jQuery('#umsMapForm #zoom_type_options .zoom_level');

			switch(value) {
				case 'zoom_level':
					zoomLevelOpt.show( g_umsAnimationSpeed );
					break;
				case 'fit_bounds':
					zoomLevelOpt.hide( g_umsAnimationSpeed );
					break;
				default:
					break;
			}
			g_umsMap.setParam('zoom_type', value);
			if(g_umsIsNeedTriggerZoomTypeAdmin) {
				g_umsMap.applyZoomTypeAdmin();
			}
		}).trigger('change');*/
		//g_umsIsNeedTriggerZoomTypeAdmin = true;	// To prevent trigger applyZoomTypeAdmin by .trigger('change')
		jQuery('#umsMapForm [name="map_opts[zoom]"]').change(function(){
			g_umsMap.setZoom(jQuery(this).val());
		});
		g_umsMap.addEventListener('zoom_changed', function(){
			jQuery('#umsMapForm [name="map_opts[zoom]"]').val(parseInt(g_umsMap.getZoom()));
		});
		// Map type
		jQuery('#umsMapForm select[name="map_opts[map_type]"]').change(function(){
			var newType = jQuery(this).val();
			g_umsMap.setMapType(newType);
            umsSwitchHidePoi();
		});
        jQuery('#umsMapForm select[name="map_opts[map_type]"]').trigger('change');
		// Map stylization
		jQuery('#umsMapForm select[name="map_opts[map_stylization]"]').change(function(){
			var newType = jQuery(this).val();

			// Common styles go first
			// TODO: Make it work - at least for Bing
			console.log(umsAllStylizationsList[ newType ]);
			/*if(newType !== 'none' && typeof(umsAllStylizationsList[ newType ]) !== 'undefined') {
				g_umsMap.set('styles', umsAllStylizationsList[ newType ]);
			} else {
				g_umsMap.set('styles', false);
			}*/
		});
		// Map Clasterization
		// TODO: Make it work at least for Bing
		jQuery('#umsMapForm select[name="map_opts[marker_clasterer]"]').change(function(){
			var newType = jQuery(this).val();
			if(newType !== 'none' && newType) {
				g_umsMap.enableClasterization( newType );
				umsSwitchClustererSubOpts(newType);
			} else {
				g_umsMap.disableClasterization();
				umsSwitchClustererSubOpts('none');
			}
		});
		jQuery('#umsMapForm [name="map_opts[marker_clasterer_grid_size]"]').change(function(){
			g_umsMap.setClusterSize(jQuery(this).val());
		});
		umsSwitchClustererSubOpts(jQuery('#umsMapForm select[name="map_opts[marker_clasterer]"]').val());
		jQuery('#umsUploadClastererIconBtn').click(function(e){
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
				,	iconPrevImg = jQuery('#umsMarkerClastererIconPrevImg')
				,	width  = 53
				,	height = 'auto';

				iconPrevImg.attr('src', attachment.url);
				width = document.getElementById('umsMarkerClastererIconPrevImg').naturalWidth;
				height = document.getElementById('umsMarkerClastererIconPrevImg').naturalHeight;
				umsUpdateClusterIcon(attachment.url, width, height);
			});
			//Open the uploader dialog
			custom_uploader.open();
		});
		jQuery('#umsDefaultClastererIconBtn').click(function(e) {
			e.preventDefault();
			var defIconUrl = UMS_DATA.modPath + 'maps/img/m1.png';
			jQuery('#umsMarkerClastererIconPrevImg').attr('src', defIconUrl);
			umsUpdateClusterIcon(defIconUrl, 53, 52);
		});
		jQuery('#umsDefaultClastererGridSizeBtn').click(function(e) {
			e.preventDefault();
			jQuery('#umsMarkerClastererSubOpts').find('#umsMarkerClastererGridSize').val('60');
		});

		// Map KML layers
		jQuery('#umsAddNewShapeBtn').click(function(e){
			if(UMS_DATA.isPro == '') {
				e.stopPropagation();
				var $proOptWnd = umsGetMainPromoPopup();
				$proOptWnd.dialog('open');
				return false;
			}
		});
		jQuery('#umsKmlAddFileRowBtn').click(function(e){
			if(UMS_DATA.isPro == '') {
				e.stopPropagation();
				var $proOptWnd = umsGetMainPromoPopup();
				$proOptWnd.dialog('open');
				return false;
			}
		});
		jQuery('#umsCurUserPosIconBtn').click(function(e){
			if(UMS_DATA.isPro == '') {
				e.stopPropagation();
				var $proOptWnd = umsGetMainPromoPopup();
				$proOptWnd.dialog('open');
				return false;
			}
		});
		jQuery('#umsUploadCurUserPosIconBtn').click(function(e){
			if(UMS_DATA.isPro == '') {
				e.stopPropagation();
				var $proOptWnd = umsGetMainPromoPopup();
				$proOptWnd.dialog('open');
				return false;
			}
		});
		// Map Marker Info Window width and height units
		/*jQuery('#umsMapForm select[name="map_opts[marker_infownd_type]"]').change(function(){
			umsToggleSubOptionsByDataParam(jQuery(this).val());
		});
		umsToggleSubOptionsByDataParam(g_umsMap.getParam('marker_infownd_type'));*/
		jQuery('#umsMapForm input[name="map_opts[marker_infownd_width_units]"]').change(function(){
			var infoWndWidthInput = jQuery('#umsMapForm input[name="map_opts[marker_infownd_width]"]')
			,	infoWndWidthLabel = jQuery('#umsMapForm').find('[for="map_opts_marker_infownd_width_units"]');

			if(jQuery(this).val() == 'px' && jQuery(this).val()) {
				infoWndWidthLabel.css('top', '7px');
				infoWndWidthInput.show();
			} else {
				infoWndWidthLabel.css('top', '0px');
				infoWndWidthInput.hide();
			}
			g_umsMap.setMarkerInfoWndWidthUnits(jQuery(this).val());
		});
		jQuery('#umsMapForm input[name="map_opts[marker_infownd_width]"]').change(function(){
			g_umsMap.setMarkerInfoWndWidth(jQuery(this).val());
		});
		jQuery('#umsMapForm input[name="map_opts[marker_infownd_height_units]"]').change(function(){
			var infoWndHeightInput = jQuery('#umsMapForm input[name="map_opts[marker_infownd_height]"]')
			,	infoWndHeightLabel = jQuery('#umsMapForm').find('[for="map_opts_marker_infownd_height_units"]');

			if(jQuery(this).val() == 'px' && jQuery(this).val()) {
				infoWndHeightLabel.css('top', '7px');
				infoWndHeightInput.show();
			} else {
				infoWndHeightLabel.css('top', '0px');
				infoWndHeightInput.hide();
			}
			g_umsMap.setMarkerInfoWndHeightUnits(jQuery(this).val());
		});
		jQuery('#umsMapForm input[name="map_opts[marker_infownd_height]"]').change(function(){
			g_umsMap.setMarkerInfoWndHeight(jQuery(this).val());
		});
		jQuery('#umsMapForm input[name="map_opts[marker_title_size]"]').change(function(){
			g_umsMap.setMarkerInfoWndTitleSize(jQuery(this).val());
		});
		jQuery('#umsMapForm input[name="map_opts[marker_desc_size]"]').change(function(){
			g_umsMap.setMarkerInfoWndDescSize(jQuery(this).val());
		});
		var updateZoomLevels = function(minZoom, maxZoom) {
			jQuery('#umsZoomLelvelsError').hide();
			minZoom = parseInt(minZoom);
			maxZoom = parseInt(maxZoom);
			if(minZoom >= maxZoom) {
				jQuery('#umsZoomLelvelsError').slideDown(g_umsAnimationSpeed);
				return;
			}
			g_umsMap.setParam('zoom_min', minZoom);
			g_umsMap.setParam('zoom_max', maxZoom);
			g_umsMap._setMinZoomLevel();
			g_umsMap._setMaxZoomLevel();
		};
		jQuery('#umsMapForm select[name="map_opts[zoom_min]"]').change(function(){
			updateZoomLevels(jQuery(this).val(), jQuery('#umsMapForm select[name="map_opts[zoom_max]"]').val());
		});
		jQuery('#umsMapForm select[name="map_opts[zoom_max]"]').change(function(){
			updateZoomLevels(jQuery('#umsMapForm select[name="map_opts[zoom_min]"]').val(), jQuery(this).val());
		});
		jQuery('#umsMapForm input[name="map_opts[adapt_map_to_screen_height]"]').change(function(){
			var $parrentCol = jQuery(this).parents('.sup-col:first');
			if(parseInt(jQuery(this).val())) {
				jQuery('.umsMainHeightOpts').hide(g_umsAnimationSpeed);
				$parrentCol.removeClass('sup-w-25').addClass('sup-w-100');
			} else {
				jQuery('.umsMainHeightOpts').show(g_umsAnimationSpeed);
				$parrentCol.removeClass('sup-w-100').addClass('sup-w-25');
			}
		});
		jQuery('#umsMapForm input[name="map_opts[adapt_map_to_screen_height]"]').trigger('change');
		// Map Markers List selection
		umsInitMapMarkersListWnd();
		// Ask before leave page without saving
		jQuery('#umsMapForm').find('input,select,textarea').change(function(){
			_umsChangeMapForm();
		});
		//Show 'Close description by mouse hover' checkbox only if 'Show description by mouse hover' if checked
		jQuery('#umsMarkerTab input[name="marker_opts[params][description_mouse_hover]"]').change(function(){
			if(jQuery(this).prop('checked') === true)
				umsShowCloseDescriptionCheckbox();
			else
				umsHideCloseDescriptionCheckbox();
		});
	});

	jQuery('.supsystic-panel .tooltipstered').removeAttr('title');
	umsInitEngineChange();
});
function umsSwitchHidePoi(){
    if(UMS_DATA.isPro) {
        typeof g_umsMap.setHidePoi === 'function' && g_umsMap.setHidePoi(jQuery('#umsMapForm input[name="map_opts[hide_poi]"]').val());
    }
}
function umsShowCloseDescriptionCheckbox() {
	jQuery('#marker_opts_description_mouse_leave').show();
}
function umsHideCloseDescriptionCheckbox() {
	//uncheck if checked.
	jQuery('#umsMarkerTab input[name="marker_opts[params][description_mouse_leave]"]').prop('checked', false).iCheck('update');
	//hide the element
	jQuery('#marker_opts_description_mouse_leave').hide();
}
function umsCheckShortcode() {
	var currentId = umsGetCurrentId();
	if(currentId) {
		var codeType = jQuery('#umsCopyTextCodeExamples').val();
		jQuery('.umsMapShortCodeShell').val(codeType == 'shortcode' ? '['+ umsMapShortcode+ ' id="' + currentId+ '"]' : '<?php echo do_shortcode(\'['+ umsMapShortcode+ ' id="'+ currentId+ '"]\')?>');
		if(UMS_DATA.isPro) {
			jQuery('.umsMapMarkerFormCodeShell').val('['+ umsMapShortcode + '_marker_form map_id="'+ currentId+ '"]');
			jQuery('.umsPlacesToolbarCodeShell').val('['+ umsMapShortcode + '_places_toolbar map_id="'+ currentId+ '"]');
		}
		umsResetCopyTextCodeFields('#shortcodeCode');
	}
}
function umsGetCurrentId() {
	return parseInt( jQuery('#umsMapForm input[name="map_opts[id]"]').val() );
}
function umsUpdateClusterIcon(url, width, height) {
	jQuery('input[name="map_opts[marker_clasterer_icon]"]').val(url);
	jQuery('input[name="map_opts[marker_clasterer_icon_width]"]').val(width);
	jQuery('input[name="map_opts[marker_clasterer_icon_height]"]').val(height);
	g_umsMap
		.setParam('marker_clasterer_icon', url)
		.setParam('marker_clasterer_icon_width', width)
		.setParam('marker_clasterer_icon_height', height)
		.enableClasterization(g_umsMap.getParam('marker_clasterer'));
}
function umsInitMapMarkersListWnd() {
	var wndWidth = jQuery(window).width()
	,	wndHeight = jQuery(window).height()
	,	normWidth = 740
	,	normHeight = 540
	,	popupWidth = wndWidth > normWidth ? normWidth : wndWidth - 20
	,	popupHeight = wndHeight < normHeight ? normHeight : wndHeight - 70;

	jQuery('#umsMarkersListWnd').find('.umsMmlElement').css('max-width', popupWidth - 20);

	var $markersListWnd = jQuery('#umsMarkersListWnd').dialog({
		modal:    true
	,	autoOpen: false
	,	width: popupWidth
	,	height: popupHeight
	,	open: function() {
			jQuery('.ui-widget-overlay').bind('click', function() {
				$markersListWnd.dialog('close');
			});
		}
	});
	jQuery('#umsMapMarkersListBtn').click(function(){
		$markersListWnd.dialog('open');
		return false;
	});
	if(!UMS_DATA.isPro) {
		jQuery('.umsMmlElement').click(function(){
			var url = jQuery(this).find('.umsMmlApplyBtn').attr('href');
			window.open( url );
			return false;
		});
	}
}
// Map form check change actions
function _umsIsMapFormChanged() {
	return g_umsMapFormChanged;
}
function _umsChangeMapForm() {
	g_umsMapFormChanged = true;
}
function _umsUnchangeMapForm() {
	g_umsMapFormChanged = false;
}
function _umsResizeRightSidebar(container) {
	jQuery(window).trigger('scroll');

	var listContainers = container && container instanceof jQuery? container : jQuery('#umsMarkersListGrid, #umsShapeListGrid')
	,	rightBar = jQuery('#umsMapRightStickyBar')
	,	wnd = jQuery(window)
	,	wndWd = wnd.width()
	,	wndHt = wnd.height()
	,	rightBarWd,	newHeight;

	rightBar.width( jQuery('.supsistic-half-side-box').width());
	rightBarWd = rightBar.outerWidth();
	newHeight = 400;

	if(wndWd > 991) {
		newHeight = wndHt
		- jQuery('#wpadminbar').outerHeight()
		- jQuery('#umsMapPreview').outerHeight()
		- jQuery('.umsControlBtns:first').outerHeight()
		- jQuery('.ui-jqgrid-htable:first').outerHeight();
	}
	listContainers.each(function() {
		var self = jQuery(this);

		if(self.attr('id') == 'umsMarkersListGrid') {
			newHeight = newHeight - jQuery('#umsMarkersSearchInput').outerHeight();
		}
		newHeight = newHeight > 250 ? newHeight : 250;

		self.jqGrid('setGridWidth', rightBarWd);
		self.jqGrid('setGridHeight', newHeight);
	});
}
function umsAddCustomControlsOptions() {
	var customMapControls = jQuery('#map_optsenable_custom_map_controls_check').prop('checked');

	if (customMapControls) {
		jQuery('#custom_controls_options').show(300);
        //#229
        jQuery('select[name="map_opts[zoom_control]"]').closest('tr').hide(300);
        jQuery('[name="map_opts[custom_controls_position]"]').trigger('change');
	} else {
		jQuery('#custom_controls_options').hide(300);
		//#229
        jQuery('select[name="map_opts[zoom_control]"]').closest('tr').show(300);
	}
}
function umsSwitchClustererSubOpts(clusterType) {
	if (clusterType == 'none') {
		jQuery('#umsMarkerClastererSubOpts').hide( g_umsAnimationSpeed );
	} else {
		jQuery('#umsMarkerClastererSubOpts').show( g_umsAnimationSpeed );
	}
}
function umsToggleSubOptionsByDataParam(value) {
	var subOpts = jQuery('#umsMarkerInfoWndTypeSubOpts .umsSubOpt');

	subOpts.filter('[data-type]').hide();
	subOpts.filter('[data-type="' + value + '"]').show();
}
function umsWpColorpickerUpdateTitlesColor(color) {
	g_umsMarkerTitleColorTimeoutSet = false;
	g_umsMap.setMarkerInfoWndTitleColor(g_umsMarkerTitleColorLast);
}
function wpColorPicker_map_optsmarker_title_color_change(event, ui) {
	g_umsMarkerTitleColorLast = ui.color.toString();
	if(!g_umsMarkerTitleColorTimeoutSet) {
		setTimeout(function(){
			umsWpColorpickerUpdateTitlesColor();
		}, 500);
		g_umsMarkerTitleColorTimeoutSet = true;
	}
}
function wpColorPicker_map_optscustom_controls_bg_color_change(event, ui) {
	if(!UMS_DATA.isPro) {
		jQuery('#umsMapForm [name="map_opts[custom_controls_bg_color]"]').trigger('change');
	}
}
function wpColorPicker_map_optscustom_controls_txt_color_change(event, ui) {
	if(!UMS_DATA.isPro) {
		jQuery('#umsMapForm [name="map_opts[custom_controls_txt_color]"]').trigger('change');
	}
}
var g_umsMarkerBgColorLast = '';
function wpColorPicker_map_optsmarker_infownd_bg_color_change(event, ui) {
	g_umsMarkerBgColorLast = ui.color.toString();
	if(!g_umsMarkerBgColorTimeoutSet) {
		setTimeout(function(){
			//Set param anyway for info window preview, opened before new marker will be saved
			changeInfoWndBgColor();
		}, 500);
		g_umsMarkerBgColorTimeoutSet = true;
	}
}
function changeInfoWndBgColor() {
	g_umsMarkerBgColorTimeoutSet = false;
	g_umsMap.setMarkerInfoWndBgColor(g_umsMarkerBgColorLast);
}
// Common function for map PRO tabs
function umsUnshiftButtons(btns) {
	for(var i in btns) {
		if(jQuery('#' + i).hasClass(btns[i]))
			jQuery('#' + i).trigger('click');
	}
}
function getLocationByCoordinates(coord) {
	jQuery.ajax({
		url: 'https://nominatim.openstreetmap.org/?q='+coord+'&format=json'
	,	dataType: 'text'
	,	success: function(data) {
			if(data) {
				var r = JSON.parse(data);
				if(r) {
					if(r && r.length > 0) {
						jQuery("[name='map_opts[map_center][address]']").val(r[0]['display_name']);
					}
				}
			}
		}
	});
}
// Change engine is separate functionality
function umsInitEngineChange() {
	var $engineSel = jQuery('#umsEngineSel')
	,	baseEngine = $engineSel.val()
	,	$engineChangeDlg = jQuery('#umsEngineChangeWnd').dialog({
			modal:    true
		,	autoOpen: false
		,	width: 315
		,	buttons:
			[
				{
					text: 'Agree - Change It'
				,	click: function(e) {
						var mapId = parseInt( jQuery('#umsMapForm input[name="map_opts[id]"]').val() );
						jQuery.sendFormUms({
							data: {mod: 'maps', action: 'changeEngine', id: mapId, 'engine': $engineSel.val()}
						,	msgElID: 'umsEngineChangeMsg'
						,	btn: jQuery(e.currentTarget)
						,	onSuccess: function(res) {
								if(!res.error) {
									toeReload(res.data && res.data.new_engine_url ? res.data.new_engine_url : false);
								}
							}
						});
					}
				,	id: 'umsAgreeChangeEngineBtn'
				},
				{
					text: 'Don\'t do this'
				,	click: function() {
						$engineSel.val(baseEngine);
						$engineChangeDlg.dialog('close');
					}
				}
			]
		});
	// Append save icon and make buttons "all-in-one-line"
	jQuery('#umsAgreeChangeEngineBtn').prepend('<i class="fa fa-floppy-o" style=""></i>');
	$engineChangeDlg.parents('.ui-dialog:first').find('.ui-button-text').css('display', 'inline-block');
	$engineSel.change(function(){
		var newEngine = jQuery(this).val();
		if(baseEngine !== newEngine) {
			$engineChangeDlg.dialog('open');
		}
	});
}

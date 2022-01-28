var g_umsAllMaps = [];
function umsGetMembershipGmeViewId(map, oldViewId) {
	var newViewId = oldViewId;
	if(map && map.getParam && map.getParam('membershipEnable') == '1') {
		// prepare view id
		if(map._elementId && map._elementId.id && map._elementId.id.substr) {
			var viewIdKey = 'ultimate_maps_'
			,	newIdPos = map._elementId.id.substr(viewIdKey.length);
			if(newIdPos) {
				newViewId = newIdPos;
			}
		}
	}
	return newViewId;
}
jQuery(document).ready(function(){
	var mapsInitClb = function() {
		if(typeof(umsAllMapsInfo) !== 'undefined' && umsAllMapsInfo && umsAllMapsInfo.length) {
			for(var i = 0; i < umsAllMapsInfo.length; i++) {
				if(jQuery('#'+ umsAllMapsInfo[i].view_html_id).length) {
					umsInitMapOnPage( umsAllMapsInfo[i] );
				}
			}
			jQuery(document).trigger('umsAmiVarInited');
		}
	};
	/*if(typeof(google) === 'undefined'
		&& typeof(umsLoadGoogleLib) !== 'undefined'	// Maybe it's just a static maps here - can do it without google lib
	) {
		umsLoadGoogleLib();
		setTimeout(mapsInitClb, 1000);
	} else {
		mapsInitClb();
	}*/
	// TODO: Make it more correct - attach by batches or something like this
	setTimeout(function(){
		mapsInitClb();
	}, 500);
	//g_umsMapLoadObserver.trigger(umsGetMapsEngine(), mapsInitClb);
});
function umsInitMapOnPage(mapData) {
	g_umsMapLoadObserver.trigger(umsGetMapsEngine(mapData), function() {
		var additionalData = {
			markerGroups: typeof(mapData.marker_groups) != 'undefined' ? mapData.marker_groups : []
		}
		,	newMap = null
		,	mapMarkersIds = []
		,	markerIdToShow = umsIsMarkerToShow()
		,	infoWndToShow = umsIsInfoWndToShow();

		/*if(mapData && mapData.view_html_mbs_id) {
			// for membership Activity ajax load
			newMap = new umsGoogleMap(mapData.view_html_mbs_id, mapData.params, additionalData);
			newMap.setParam('view_html_mbs_id', mapData.view_html_mbs_id);
			newMap.refreshWithCenter(mapData.params.center.lat(), mapData.params.center.lng(), mapData.params.zoom);
		} else {
			newMap = new umsGoogleMap('#'+ mapData.view_html_id, mapData.params, additionalData);
		}*/

		// for membership Google Maps "Get original"
		/*if(mapData.mbs_presets == 1) {
			newMap.setParam('mbs_presets', 1);
		}*/
		newMap = umsMapLoader.initMap('#'+ mapData.view_html_id, mapData, additionalData);

		if(mapData.markers && mapData.markers.length) {
			mapData.markers = _umsPrepareMarkersList( mapData.markers );

			for(var i in mapData.markers) {
				mapMarkersIds.push(mapData.markers[i].id);
			}
			if(toeInArray(markerIdToShow, mapMarkersIds) == -1) {
				markerIdToShow = false;
			}
			if(toeInArray(infoWndToShow, mapMarkersIds) == -1) {
				infoWndToShow = false;
			}
			for(var j = 0; j < mapData.markers.length; j++) {
				if(markerIdToShow && mapData.markers[j].id != markerIdToShow) continue;
				if(infoWndToShow) {
					mapData.markers[j].params.show_description = mapData.markers[j].id == infoWndToShow ? '1' : '0';
				}
				var newMarker = newMap.addMarker( mapData.markers[j] );
				// We will set this only when marker info window need to be loaded
				newMarker.setTitle( mapData.markers[j].title, true );
				newMarker.setDescription( mapData.markers[j].description );
			}
			newMap.markersRefresh();
			newMap.checkMarkersParams(newMap.getAllMarkers(), markerIdToShow);
		}
		if(mapData.shapes && mapData.shapes.length) {
			mapData.shapes = _umsPrepareShapesList( mapData.shapes );
			for(var z = 0; z < mapData.shapes.length; z++) {
				var newShape = newMap.addShape( mapData.shapes[z] );
			}
		}
		if(mapData.heatmap) {
			mapData.heatmap = _umsPrepareHeatmapList( mapData.heatmap );
			newMap.addHeatmap( mapData.heatmap );
		}
		g_umsAllMaps.push( newMap );
	});

}
function umsGetMapInfoById(id) {
	if(typeof(umsAllMapsInfo) !== 'undefined' && umsAllMapsInfo && umsAllMapsInfo.length) {
		id = parseInt(id);
		for(var i = 0; i < umsAllMapsInfo.length; i++) {
			if(umsAllMapsInfo[i].id == id) {
				return umsAllMapsInfo[i];
			}
		}
	}
	return false;
}
function umsGetMapInfoByViewId(viewId) {
	if(typeof(umsAllMapsInfo) !== 'undefined' && umsAllMapsInfo && umsAllMapsInfo.length) {
		for(var i = 0; i < umsAllMapsInfo.length; i++) {
			if(umsAllMapsInfo[i].view_id == viewId) {
				return umsAllMapsInfo[i];
			}
		}
	}
	return false;
}
function umsGetAllMaps() {
	return g_umsAllMaps;
}
function umsGetMapById(id) {
	var allMaps = umsGetAllMaps();
	for(var i = 0; i < allMaps.length; i++) {
		if(allMaps[i].getId() == id) {
			return allMaps[i];
		}
	}
	return false;
}
function umsGetMapByViewId(viewId) {
	var allMaps = umsGetAllMaps();
	for(var i = 0; i < allMaps.length; i++) {
		var currViewId = allMaps[i].getViewId();
		if(window.umsGetMembershipGmeViewId) {
			currViewId = umsGetMembershipGmeViewId(allMaps[i], currViewId);
		}
		if(currViewId == viewId) {
			return allMaps[i];
		}
	}
	return false;
}
function umsIsMarkerToShow() {
	var markerHash = 'umsMarker'
	,	hashParams = toeGetHashParams();
	if(hashParams) {
		for(var i in hashParams) {
			if(!hashParams[i] || typeof(hashParams[i]) !== 'string') continue;
			var pair = hashParams[i].split('=');
			if(pair[0] == markerHash)
				return parseInt(pair[1]);
		}
	}
	return false;
}
function umsIsInfoWndToShow() {
	var markerHash = 'umsInfoWnd'
	,	hashParams = toeGetHashParams();
	if(hashParams) {
		for(var i in hashParams) {
			if(!hashParams[i] || typeof(hashParams[i]) !== 'string') continue;
			var pair = hashParams[i].split('=');
			if(pair[0] == markerHash)
				return parseInt(pair[1]);
		}
	}
	return false;
}

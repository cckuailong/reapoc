function umsBaseMap(elementId, mapData, engine, additionalParams) {
	mapData = mapData ? mapData : {};
	additionalParams = additionalParams ? additionalParams : {};
	var defaults = {};
	this._engine = engine;
	this._fullEngine = additionalParams.fullEngine ? additionalParams.fullEngine : this._engine;
	/*var defaults = {
		center: new google.maps.LatLng(40.69847032728747, -73.9514422416687)
	,	zoom: 8
	//,	mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	if(params.map_center && params.map_center.coord_x && params.map_center.coord_y) {
		params.center = new google.maps.LatLng(params.map_center.coord_x, params.map_center.coord_y);
	}
	if(params.zoom) {
		params.zoom = parseInt(params.zoom);
	}
	if(!UMS_DATA.isAdmin && params.zoom_type == 'zoom_level' && params.zoom_mobile && jQuery(document).width() < 768) {
		params.zoom = parseInt(params.zoom_mobile);
	}*/
	/*if (typeof(elementId) === 'string') {
		elementId = jQuery(elementId)[0];
	}*/
	this._$markerInfoWndStyle = null;
	this._elementId = elementId;
	this._clearElementId = str_replace(this._elementId, '#', '');
	this._element = typeof(elementId) === 'string' ? jQuery(elementId)[0] : elementId;
	this._mapParams = jQuery.extend({}, defaults, mapData.params);
	this._mapObj = null;
	this._markers = [];
	this._shapes = [];
	this._heatmap = [];
	this._kmlLayers = [];
	this._clasterer = null;
	this._clastererEnabled = false;
	this._clastererMarkersGroupsStyles = [];
	//this._eventListeners = {};
	//this._layers = {};
	//this.mapMarkersGroups = additionalData.markerGroups ? additionalData.markerGroups : [];
	this._initComplete = false;
	this.init();
}
umsBaseMap.prototype.getEngine = function() {
	return this._engine;
};
umsBaseMap.prototype.init = function() {
	this._beforeInit();
	this._createMapObj();
	this._refreshMarkerInfoWndStyle();
	this._afterInit();
	this._initComplete = true;
};
umsBaseMap.prototype._createMapObj = function() {
	// Map object creation for inheritance should be here
};
umsBaseMap.prototype._beforeInit = function() {
	jQuery(document).trigger('umsBeforeMapInit', this);
};
umsBaseMap.prototype.getParams = function(){
	return this._mapParams;
};
umsBaseMap.prototype.getParam = function(key){
	return this._mapParams[ key ];
};
umsBaseMap.prototype.setParam = function(key, value){
	this._mapParams[ key ] = value;
	return this;
};
umsBaseMap.prototype.resizeMapByHeight = function() {
	if(!UMS_DATA.isAdmin && parseInt(this.getParam('adapt_map_to_screen_height')) && this.getRawMapInstance().map_display_mode != 'popup') {
		var self = this;

		function resizeHeight() {
			var viewId = self.getParam('view_id')
			,	mapContainer = jQuery('#umsMapDetailsContainer_' + viewId)
			,	mapContainerOffset = mapContainer.length ? mapContainer.offset() : false
			,	windowHeight = jQuery(window).height();

			if(mapContainerOffset) {
				jQuery('#umsMapDetailsContainer_' + viewId + ', #' + self.getParam('view_html_id')).each(function () {
					var height = mapContainerOffset.top < windowHeight ? windowHeight - mapContainerOffset.top : windowHeight;
					jQuery(this).height(height);
				});
				//self.refresh();
			}
		}
		resizeHeight();
		jQuery(window).bind('resize', resizeHeight);
		jQuery(window).bind('orientationchange', resizeHeight);
	}
};
umsBaseMap.prototype._afterInit = function() {
	if(typeof(this._mapParams.marker_clasterer) !== 'undefined' && this._mapParams.marker_clasterer) {
		this.enableClasterization(this._mapParams.marker_clasterer);
	}
	this.resizeMapByHeight(); 
	jQuery(document).trigger('umsAfterMapInit', this);
};
umsBaseMap.prototype.addMarker = function(params) {
	var newMarker = this._createMarkerObj(params);
	this._markers.push( newMarker );
	return newMarker;
};
umsBaseMap.prototype._createMarkerObj = function(params) {
	// Marker object creation should be here
	return umsMapLoader.initMarker(this, params)
	//this._methodRedefineNotice('umsBaseMap.prototype.setCenter');
	//return null;
};
umsBaseMap.prototype.getMarkerById = function(id) {
	if(this._markers && this._markers.length) {
		id = parseInt(id);
		for(var i in this._markers) {
			if(this._markers[i].getId && this._markers[i].getId() === id)
				return this._markers[ i ];
		}
	}
	return false;
};
umsBaseMap.prototype.removeMarker = function(id) {
	var marker = this.getMarkerById( id );
	if(marker) {
		marker.removeFromMap();
	}
};
umsBaseMap.prototype.getAllMarkers = function() {
	return this._markers;
};
umsBaseMap.prototype.addEventListener = function(event, callback) {
	/*zoom_changed, dragend*/
	this._methodRedefineNotice('umsBaseMap.prototype.addEventListener');
};
umsBaseMap.prototype.setCenter = function (lat, lng) {
	this._methodRedefineNotice('umsBaseMap.prototype.setCenter');
};
umsBaseMap.prototype.getCenter = function () {
	this._methodRedefineNotice('umsBaseMap.prototype.getCenter');
};
umsBaseMap.prototype.setZoom = function (zoomLevel) {
	this._methodRedefineNotice('umsBaseMap.prototype.setZoom');
};
umsBaseMap.prototype.getZoom = function () {
	this._methodRedefineNotice('umsBaseMap.prototype.getZoom');
};
umsBaseMap.prototype.setMapType = function (mapType) {
	this._methodRedefineNotice('umsBaseMap.prototype.setMapType');
};
/**
 * Should trigger after added or modified markers
 */
umsBaseMap.prototype.markersRefresh = function() {
	var clasterer = this.getClasterer();

	if(this._clastererEnabled && clasterer) {
		this._refreshCluster();
		/*clasterer.clearMarkers();
		clasterer.addMarkers( this.getAllRawMarkers() );*/
	}
	jQuery(document).trigger('umsAfterMarkersRefresh', this);
};
umsBaseMap.prototype._refreshCluster = function() {
	this._methodRedefineNotice('umsBaseMap.prototype._refreshCluster');
};
umsBaseMap.prototype.getAllRawMarkers = function() {
	var res = [];
	if(this._markers && this._markers.length) {
		for(var i = 0; i < this._markers.length; i++) {
			res.push( this._markers[i].getRawMarkerInstance() );
		}
	}
	return res;
};

umsBaseMap.prototype._methodRedefineNotice = function(methodName) {
	console.log('['+ methodName+ '] should be re-defined!');
};
umsBaseMap.prototype.clearMarkers = function() {
	if(this._markers && this._markers.length) {
		for(var i = 0; i < this._markers.length; i++) {
			this._markers[i].removeFromMap();
		}
		this._markers = [];
	}
};
umsBaseMap.prototype.clearMarkersByParam = function(param) {
	if(this._markers && this._markers.length) {
		for(var i = 0; i < this._markers.length; i++) {
			if(this._markers[i].getMarkerParam(param)) {
				this._markers[i].removeFromMap();
				this._markers.splice(i, 1);
				this.clearMarkersByParam(param);
				break;
			}
		}
	}
};
umsBaseMap.prototype._runAfterInitComplete = function(clb) {
	// TODO: think and write
};
umsBaseMap.prototype.setNavigationBarMode = function(mode) {
	this._methodRedefineNotice('umsBaseMap.prototype.setNavigationBarMode');
};
umsBaseMap.prototype.getNavigationBarMode = function() {
	this._methodRedefineNotice('umsBaseMap.prototype.getNavigationBarMode');
};
umsBaseMap.prototype.enbZoom = function(mode) {
	this._methodRedefineNotice('umsBaseMap.prototype.enbZoom');
};
umsBaseMap.prototype.enbDraggable = function(mode) {
	this._methodRedefineNotice('umsBaseMap.prototype.enbDraggable');
};
umsBaseMap.prototype.enbWheelZoom = function(mode) {
	this._methodRedefineNotice('umsBaseMap.prototype.enbWheelZoom');
};
umsBaseMap.prototype.geocodeQuery = function(search, clb) {
	this._methodRedefineNotice('umsBaseMap.prototype.geocodeQuery');
};

umsBaseMap.prototype.getClasterer = function() {
	if(this._clasterer) {
		return this._clasterer;
	}
	return false;
};
umsBaseMap.prototype.setClasterer = function(clasterer) {
	this._clasterer = clasterer;
};

umsBaseMap.prototype.geocodeSearchAutocomplete = function($el, params) {
	params = params || {};
	var mapSelf = this;
	$el = typeof($el) === 'string' ? jQuery($el) : $el;
	$el.keyup(function(event){
		// Ignore tab, enter, caps, end, home, arrows
		if(toeInArrayUms(event.keyCode, [9, 13, 20, 35, 36, 37, 38, 39, 40])) return;

		var searchData = jQuery.trim(jQuery(this).val());

		if(searchData && searchData != '') {
			if(typeof(params.msgEl) === 'string') {
				params.msgEl = jQuery(params.msgEl);
			}
			params.msgEl.showLoaderUms();
			var self = this;

			jQuery(this).autocomplete({
				source: function(request, response) {
					var autocomleateData = typeof(params.additionalData) != 'undefined'
						? umsAutocomleateData(params.additionalData, request.term)
						: []

					mapSelf.geocodeQuery(searchData, function(results) {
						params.msgEl.html('');
						for(var i = 0; i < results.length; i++) {
							autocomleateData.push({
								label: results[i].formatted_address
							,	lat: results[i].lat
							,	lng: results[i].lng
							});
						}
						response(autocomleateData);
					}, function(event) {
						if(autocomleateData && autocomleateData.length > 0) {
							response(autocomleateData);
						} else {
							//var notFoundMsg = toeLangUms('Google can\'t find requested address coordinates, please try to modify search criterias.');
							var notFoundMsg = toeLangUms('Nothing was found');

							if(jQuery(self).parent().find('.ui-helper-hidden-accessible').length) {
								jQuery(self).parent().find('.ui-helper-hidden-accessible').html(notFoundMsg);
							} else {
								params.msgEl.html(notFoundMsg);
							}
						}
					});
				}
			,	select: function(event, ui) {
					if(params.onSelect) {
						params.onSelect(ui.item, event, ui);
					}
					jQuery(self).removeClass('ui-autocomplete-loading');
				}
			});

			// Force imidiate search right after creation
			jQuery(this).autocomplete('search');
		}
	});
	function umsAutocomleateData(data, needle) {
		var autocomleateData = [];

		for(var i = 0; i < data.length; i++) {
			for(var j = 0; j < data[i].length; j++) {
				if(data[i][j]) {
					var label = data[i][j].label.toString().toLowerCase()
					,	desc = data[i][j].marker_desc != 'undefined' ? data[i][j].marker_desc : ''
					,	term = needle.toLowerCase();

					if(label.indexOf(term) !== -1 || (desc && desc.indexOf(term) !== -1)) {
						autocomleateData.push(data[i][j]);
					}
				}
			}
		}
		return autocomleateData;
	}
};
umsBaseMap.prototype.getRawMapInstance = function() {
	return this._mapObj;
};
umsBaseMap.prototype.checkMarkersParams = function(markers, needToShow) {
	if(markers && markers.length) {
		for (var i = 0; i < markers.length; i++) {
			var markerParams = markers[i].getMarkerParam('params')
			,	showDescription = parseInt(markerParams.show_description);
			if(showDescription || needToShow) {
				markers[i].showInfoWnd( true, showDescription, true );
			}
		}
	}
};
umsBaseMap.prototype.setMarkerInfoWndWidthUnits = function(type) {
	this.setParam('marker_infownd_width_units', type);
	this._refreshMarkerInfoWndStyle();
};
umsBaseMap.prototype.setMarkerInfoWndWidth = function(width) {
	this.setParam('marker_infownd_width', width);
	this._refreshMarkerInfoWndStyle();
};
umsBaseMap.prototype.setMarkerInfoWndHeightUnits = function(type) {
	this.setParam('marker_infownd_height_units', type);
	this._refreshMarkerInfoWndStyle();
};
umsBaseMap.prototype.setMarkerInfoWndHeight = function(height) {
	this.setParam('marker_infownd_height', height);
	this._refreshMarkerInfoWndStyle();
};

umsBaseMap.prototype.setMarkerInfoWndTitleColor = function(color) {
	this.setParam('marker_title_color', color);
	this._refreshMarkerInfoWndStyle();
};
umsBaseMap.prototype.setMarkerInfoWndBgColor = function(color) {
	this.setParam('marker_infownd_bg_color', color);
	this._refreshMarkerInfoWndStyle();
};
umsBaseMap.prototype.setMarkerInfoWndTitleSize = function(size) {
	this.setParam('marker_title_size', size);
	this._refreshMarkerInfoWndStyle();
};
umsBaseMap.prototype.setMarkerInfoWndDescSize = function(size) {
	this.setParam('marker_desc_size', size);
	this._refreshMarkerInfoWndStyle();
};
umsBaseMap.prototype._refreshMarkerInfoWndStyle = function() {
	var styleHtml = this._generateMarkerInfoWndStyle();
	if(!styleHtml) return;
	if(!this._$markerInfoWndStyle) {
		this._$markerInfoWndStyle = jQuery('<style id="umsMarkerInfoWndStyle_'+ this._clearElementId+ '">').appendTo('body');
	}
	this._$markerInfoWndStyle.html( styleHtml );
};
umsBaseMap.prototype._generateMarkerInfoWndStyle = function() {
	//this._methodRedefineNotice('umsBaseMap.prototype._generateMarkerInfoWndStyle');
	// If map require generate style - it should re-define this
	return false;
};
umsBaseMap.prototype._getCreateCoords = function() {
	return this._mapParams.map_center
			&& this._mapParams.map_center.coord_x && this._mapParams.map_center.coord_x !== ''
			&& this._mapParams.map_center.coord_y && this._mapParams.map_center.coord_y !== ''
		? this._mapParams.map_center
		: {coord_x: 51.50632, coord_y: -0.12714};
};
umsBaseMap.prototype.enableClasterization = function(clasterType, needTrigger) {
	this._clastererEnabled = true;
};
umsBaseMap.prototype.disableClasterization = function() {
	this._clastererEnabled = false;
};
umsBaseMap.prototype.setClusterSize = function(size) {
	this._methodRedefineNotice('umsBaseMap.prototype.geocodeQuery');
};
umsBaseMap.prototype.getViewId = function() {
	return this._mapParams.view_id;
};
umsBaseMap.prototype.getViewHtmlId = function() {
	return this._mapParams.view_html_id;
};
umsBaseMap.prototype.getId = function() {
	return this._mapParams.id;
};
umsBaseMap.prototype.closeAllInfoWnd = function() {
	if(this._markers && this._markers.length) {
		for (var i = 0; i < this._markers.length; i++) {
			this._markers[i].hideInfoWnd();
		}
	}
};
umsBaseMap.prototype.clearShapes = function() {
	if(this._shapes && this._shapes.length) {
		for(var i = 0; i < this._shapes.length; i++) {
			this._shapes[i].setMap( null );
		}
		this._shapes = [];
	}
};
umsBaseMap.prototype.addShape = function(params) {
	var newShape = umsShapeLoader.initShape(this, params);
	this._shapes.push( newShape );
	return newShape;
};
umsBaseMap.prototype.addHeatmap = function(params) {
	//var heatmap = new umsGoogleHeatmap(this, params);
	// TODO: Add here heatmap object generation
	this._heatmap.push( heatmap );
	return heatmap;
};

umsBaseMap.prototype.getShapeById = function(id) {
	if(this._shapes && this._shapes.length) {
		for(var i in this._shapes) {
			if(this._shapes[ i ].getId() == id)
				return this._shapes[ i ];
		}
	}
	return false;
};
umsBaseMap.prototype.getHeatmap = function() {
	if(this._heatmap && this._heatmap.length) {
		// There is only one heatmap layer on the map
		return this._heatmap[0];
	}
	return false;
};

umsBaseMap.prototype.removeShape = function(id) {
	var shape = this.getShapeById( id );
	if(shape) {
		shape.removeFromMap();
	}
};

umsBaseMap.prototype.getAllShapes = function() {
	return this._shapes;
};
umsBaseMap.prototype.addKmlLayer = function(layer) {
	this._kmlLayers.push(layer);
};
umsBaseMap.prototype.removeKmlLayerByUrl = function(url) {
	if(this._kmlLayers.length) {
		for(var i = 0; i < this._kmlLayers.length; i++) {
			if(this._kmlLayers[ i ].getUrl() == url) {
				this._kmlLayers[ i ].removeFromMap();
				this._kmlLayers.splice(i, 1);
				break;
			}
		}
	}
};
umsBaseMap.prototype._setMinZoomLevel = function() {
	var curZoom = this.getZoom();
	var minZoom = parseInt(this._mapParams.zoom_min) ? parseInt(this._mapParams.zoom_min) : null;
	//this.getRawMapInstance().setOptions({minZoom: minZoom});
	if(curZoom < minZoom)
		this.setZoom(minZoom);
};
umsBaseMap.prototype._setMaxZoomLevel = function() {
	var maxZoom = parseInt(this._mapParams.zoom_max) ? parseInt(this._mapParams.zoom_max) : null;
	//this.getRawMapInstance().setOptions({maxZoom: maxZoom});
	if(this.getRawMapInstance().zoom > maxZoom)
		this.setZoom(maxZoom);
};
/*umsBaseMap.prototype._fixZoomLevel = function() {
	var eventHandle = this._getEventListenerHandle('zoom_changed', 'zoomChanged');
	if(!eventHandle) {
		eventHandle = google.maps.event.addListener(this.getRawMapInstance(), 'zoom_changed', jQuery.proxy(function(){
			var minZoom = parseInt(this.getParam('zoom_min'))
			,	maxZoom = parseInt(this.getParam('zoom_max'));
			if (this.getZoom() < minZoom) {
				this.setZoom(minZoom);
				if(GMP_DATA.isAdmin && this._getEventListenerHandle('idle', 'enableClasterization'))
					google.maps.event.trigger(this.getRawMapInstance(), 'idle');
			}
			if (this.getZoom() > maxZoom) {
				this.setZoom(maxZoom);
				if(GMP_DATA.isAdmin && this._getEventListenerHandle('idle', 'enableClasterization'))
					google.maps.event.trigger(this.getRawMapInstance(), 'idle');
			}
		}, this));
		this._addEventListenerHandle('zoom_changed', 'zoomChanged', eventHandle);
	}
};*/
var umsMapLoader = {
	initMap: function(elementId, mapData, additionalParams) {
		var engine = umsGetMapsEngine(mapData);
		additionalParams = additionalParams ? additionalParams : {};
		additionalParams.fullEngine = umsGetMapsFullEngine(mapData);
		return new window['ums'+ toeStrFirstUp(engine)+ 'Map'](elementId, mapData, engine, additionalParams);
	}
,	initMarker: function(map, markerParams) {
		return new window['ums'+ toeStrFirstUp(map.getEngine())+ 'Marker'](map, markerParams);
	}
};
var g_umsMapLoadObserver = {
	_loaded: {}
,	setLoaded: function(engine) {
		this._loaded[ engine ] = true;
	}
,	isLoaded: function(engine) {
		return this._loaded[ engine ];
	}
,	trigger: function(engine, clb) {
		if(this.isLoaded(engine)) {
			clb();
		} else {
			var self = this;
			setTimeout(function(){
				self.trigger(engine, clb);
			}, 500);
		}
	}
};
function umsGetMapsEngine(map) {
	var fullEngine = umsGetMapsFullEngine(map);
	if(fullEngine.indexOf('l-') === 0) {
		return 'leaflet';
	}
	return fullEngine;
}
function umsGetMapsFullEngine(map) {
	// This can be object inherited from umsBaseMap
	if(map && typeof(map.getEngine) === 'function')  {
		return map.getEngine();
	}
	// Or just map set from database
	if(map && map.engine && map.engine !== '') {
		return map.engine;
	}
	return UMS_DATA.engine;
}
function gmpAutocomleateData(data, needle) {
    var autocomleateData = [];

    for(var i = 0; i < data.length; i++) {
        for(var j = 0; j < data[i].length; j++) {
            if(data[i][j]) {
                var label = data[i][j].label.toString().toLowerCase()
                    ,	desc = data[i][j].marker_desc != 'undefined' ? data[i][j].marker_desc : ''
                    ,	term = needle.toLowerCase();

                if(label.indexOf(term) !== -1 || (desc && desc.indexOf(term) !== -1)) {
                    autocomleateData.push(data[i][j]);
                }
            }
        }
    }
    return autocomleateData;
}
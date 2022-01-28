function umsLeafletMap(elementId, mapData, engine) {
	this._zoomControl = null;
	this._mainLayer = null;
	this._mapTypes = null;
	this._providers = null;
	this._scaleControl = null;
	this._provider = false;
	this._providerData = null;
	umsLeafletMap.superclass.constructor.apply(this, arguments);
}
extendUms(umsLeafletMap, umsBaseMap);
umsLeafletMap.prototype._beforeInit = function() {
	this._mapParams.zoom = this._mapParams.zoom ? parseInt(this._mapParams.zoom) : 10;

	if(typeof(this._mapParams.zoom_min) !== 'undefined') {
		this._mapParams.minZoom = parseInt(this._mapParams.zoom_min);
	}
	if(typeof(this._mapParams.zoom_max) !== 'undefined') {
		this._mapParams.maxZoom = parseInt(this._mapParams.zoom_max);
	}
	if(typeof(this._mapParams.mouse_wheel_zoom) !== 'undefined') {
		this._mapParams.scrollWheelZoom = parseInt(this._mapParams.mouse_wheel_zoom);
	}
	if(typeof(this._mapParams.draggable) !== 'undefined') {
		var isMobile = ( navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i) ) ? true : false;
		switch (this._mapParams.draggable) {
			case 'enable':
				this._mapParams.dragging = 1;
				break;
			case 'disable':
				this._mapParams.dragging = 0;
				break;
			case 'desktop':
				if (!isMobile) {
					this._mapParams.dragging = 1;
				} else {
					this._mapParams.dragging  = 0;
				}
				break;
			case 'mobile':
				if (isMobile) {
					this._mapParams.dragging = 1;
				} else {
					this._mapParams.dragging = 0;
				}
				break;
		}
	}
	if(typeof(this._mapParams.dbl_click_zoom) !== 'undefined') {
		this._mapParams.doubleClickZoom = parseInt(this._mapParams.dbl_click_zoom);
	}
	// We will do this after map will be init - to have zoom control object in our class
	this._mapParams.zoomControl = false;
	umsLeafletMap.superclass._beforeInit.apply(this, arguments);
};
umsLeafletMap.prototype._createMapObj = function() {
	var centerCoords = this._getCreateCoords()
	,	params = jQuery.extend(this._mapParams, {
			center: [centerCoords.coord_x, centerCoords.coord_y]
		});
	this._mapObj = L.map(this._clearElementId, params);
	this._provider = this._getProviderEngine();
	this._mainLayer = this._createMainLayer();
};
umsLeafletMap.prototype._createMainLayer = function(provider) {
	var typeUrl = '';

	this._providerData = this._getProviderData( this._provider );
	if(this._providerData) {
		typeUrl = this._providerData.url;
	} else if(typeof(this._mapParams.map_type) !== 'undefined' && this._mapTypeExists(this._mapParams.map_type)) {
		typeUrl = this._providerData && this._providerData.typeToUrl
			? this._providerData.typeToUrl(this._mapParams.map_type)
			: this._mapParams.map_type;
	} else {
		typeUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
	}
	var props = {
		attribution: this._getAttributions(typeUrl)
	};
	if(this._providerData) {
		if(this._providerData.props) {
			props = jQuery.extend(props, this._providerData.props);
		}
		if(this._providerData.prepareProps) {
			props = this._providerData.prepareProps(props);
		}
	}
	return L.tileLayer(typeUrl, props).addTo(this._mapObj);
};
umsLeafletMap.prototype._getProviders = function() {
	if(!this._providers) {
		var self = this;
		this._providers = {
			'mapbox': {
			   url:  'https://api.mapbox.com/styles/v1/mapbox/{id}/tiles/256/{z}/{x}/{y}?access_token={accessToken}'
			,	props: {id: 'streets-v11', accessToken: UMS_DATA.mapboxKey}
			/*,	types: [
					'mapbox.streets', 'mapbox.outdoors',
					'mapbox.light','mapbox.dark','mapbox.satellite',
					'mapbox.satellite-streets','mapbox.navigation-preview-day',
					'mapbox.navigation-preview-night','mapbox.navigation-guidance-day','mapbox.navigation-guidance-night',
					'mapbox.terrain-rgb'
				]*/
			,	typeToUrl: function(type) {
					return str_replace(this.url, '{id}', type);
				}
			,	prepareProps: function(props) {
					if(typeof(self._mapParams.map_type) !== 'undefined' && self._mapTypeExists(self._mapParams.map_type)) {
						props.id = self._mapParams.map_type;
					}
					return props;
				}
			}
		,	'thunderforest': {
				url: 'https://tile.thunderforest.com/{id}/{z}/{x}/{y}.png?apikey={apikey}'
			,	props: {id: 'cycle', apikey: UMS_DATA.thunderforestKey}
			//,	attr: '&copy; <a href="http://www.thunderforest.com/">Thunderforest</a>, &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
			/*,	types: [
					'cycle', 'transport', 'landscape', 'outdoors', 'transport-dark', 'spinal-map',
					'pioneer', 'mobile-atlas', 'neighbourhood'
				]*/
			,	typeToUrl: function(type) {
					return str_replace(this.url, '{id}', type);
				}
			,	prepareProps: function(props) {
					if(typeof(self._mapParams.map_type) !== 'undefined' && self._mapTypeExists(self._mapParams.map_type)) {
						props.id = self._mapParams.map_type;
					}
					return props;
				}
			}
		};
	}
	return this._providers;
};
umsLeafletMap.prototype._getProviderData = function(provider) {
	this._getProviders();
	return this._providers[ provider ] ? this._providers[ provider ] : false;
};
umsLeafletMap.prototype._getProviderEngine = function() {
	if(this._fullEngine.indexOf('l-') === 0) {
		// like "l-mapbox" for example
		return this._fullEngine.split('-')[1];
	}
	return this._fullEngine;
};
umsLeafletMap.prototype._afterInit = function() {
	if(typeof(this._mapParams.zoom_control) !== 'undefined'
		&& this._mapParams.zoom_control !== 'none'
	) {
		this.enbZoom(true);
	}
	umsLeafletMap.superclass._afterInit.apply(this, arguments);
};
umsLeafletMap.prototype.addEventListener = function(event, clb) {
	if(event === 'zoom_changed') {
		event = 'zoomend';
	}
	this._mapObj.addEventListener(event, clb);
};
umsLeafletMap.prototype.enbZoom = function(mode) {
	var self = this;
	var removeCtrl = function(ctrl) {
		switch(ctrl) {
			case 'zoom':
				if(self._zoomControl) {
					self._zoomControl.remove();
					self._zoomControl = null;
				}
				break;
			case 'scale':
				if(self._scaleControl) {
					self._scaleControl.remove();
					self._scaleControl = null;
				}
				break;
		}
	};
	if(mode) {
		var mode = typeof(this._mapParams.navigation_bar_mode) !== 'undefined'
			? this._mapParams.navigation_bar_mode
			: 'full';
		if(toeInArrayUms(mode, ['full', 'zoom_only']) && !this._zoomControl) {
			this._zoomControl = new L.control.zoom();
			this._mapObj.addControl(this._zoomControl);
			if(mode !== 'full') {
				removeCtrl('scale');
			}
		}
		if(toeInArrayUms(mode, ['full', 'scale_only']) && !this._scaleControl) {
			this._scaleControl = new L.control.scale();
			this._mapObj.addControl(this._scaleControl);
			if(mode !== 'full') {
				removeCtrl('zoom');
			}
		}
	} else {
		removeCtrl('zoom');
		removeCtrl('scale');
	}
};
umsLeafletMap.prototype.enbDraggable = function(mode) {
	var isMobile = ( navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i) ) ? true : false;
	switch (mode) {
		case 'enable':
			this._mapObj.dragging.enable();
			break;
		case 'disable':
			this._mapObj.dragging.disable();
			break;
		case 'desktop':
			if (!isMobile) {
				this._mapObj.dragging.enable();
			} else {
				this._mapObj.dragging.disable();
			}
			break;
		case 'mobile':
			if (isMobile) {
				this._mapObj.dragging.enable();
			} else {
				this._mapObj.dragging.disable();
			}
			break;
	}
};
umsLeafletMap.prototype.setCenter = function (lat, lng) {
	this._mapObj.panTo([lat, lng]);
};
umsLeafletMap.prototype.getCenter = function () {
	var center = this._mapObj.getCenter();
	return {lat: center.lat, lng: center.lng};
};
umsLeafletMap.prototype.setZoom = function (zoomLevel) {
	this._mapObj.setZoom(zoomLevel);
};
umsLeafletMap.prototype.getZoom = function () {
	return this._mapObj.getZoom();
};
umsLeafletMap.prototype.setNavigationBarMode = function(mode) {
	if(typeof(this._mapParams.zoom_control) !== 'undefined'
		&& this._mapParams.zoom_control !== 'none'
	) {
		this._mapParams.navigation_bar_mode = mode;
		this.enbZoom(false);
		this.enbZoom(true);
	}
};
umsLeafletMap.prototype.enbWheelZoom = function(mode) {
	mode
		? this._mapObj.scrollWheelZoom.enable()
		: this._mapObj.scrollWheelZoom.disable();
};
umsLeafletMap.prototype.getNavigationBarMode = function() {
	this._methodRedefineNotice('umsLeafletMap.prototype.getNavigationBarMode');
};
umsLeafletMap.prototype.setMapType = function (mapType) {
	if(this._providerData && this._providerData.typeToUrl) {
		this._mainLayer.setUrl(this._providerData.typeToUrl(mapType));
	} else {
		this._mainLayer.setUrl(mapType);
	}
};
umsLeafletMap.prototype.geocodeQuery = function(search, clb, errorClb) {
	// Leaflet don't have it's own geocode. So use nominatim service. maybe we need to push it as a default fallback?
	jQuery.ajax({
		url: 'https://nominatim.openstreetmap.org/?q='+ search+ '&format=json'
	,	dataType: 'text'
	,	success: function(data) {
			if(clb) {
				if(data) {
					var r = JSON.parse(data);
					if(r) {
						var formattedResult = [];
						if(r && r.length > 0) {
							for(var i = 0; i < r.length; i++) {
								// All addresses in all geocoders should have same - our - format results
								formattedResult.push({
									formatted_address: r[ i ].display_name
								,	lat: r[ i ].lat
								,	lng: r[ i ].lon
								});
							}
							clb(formattedResult);
						}
						return;
					}
				}
				errorClb(data);
				return;
			} else
				console.log('So, what should I do next, ha?');
		}
	});
};
umsLeafletMap.prototype._generateMarkerInfoWndStyle = function() {
	var res = [];
	// Width

	res.push(this._elementId+ ' .marker-cluster {background-color: '+ this.getParam('marker_clasterer_border_color') + '; opacity:1;}');
	res.push(this._elementId+ ' .marker-cluster {color: '+ this.getParam('marker_clasterer_text_color') + ';}');
	res.push(this._elementId+ ' .marker-cluster div {background-color: '+ this.getParam('marker_clasterer_background_color') + '; opacity:1;}');


	if(this.getParam('marker_infownd_width_units') === 'px') {
		res.push(this._elementId+ ' .leaflet-popup-content {width: '+ this.getParam('marker_infownd_width')+ 'px;}');
	}
	// Height
	if(this.getParam('marker_infownd_height_units') === 'px') {
		res.push(this._elementId+ ' .leaflet-popup-content {height: '+ this.getParam('marker_infownd_height')+ 'px;}');
	}
	// Title style
	res.push(this._elementId+ ' .leaflet-popup-content .umsMarkerTitle {color: '+ this.getParam('marker_title_color')
			+ '; font-size: '+ this.getParam('marker_title_size')+ 'px}');
	// Description
	res.push(this._elementId+ ' .leaflet-popup-content .umsMarkerDesc, '+ this._elementId+ ' .leaflet-popup-content .umsMarkerDesc * {font-size: '+ this.getParam('marker_desc_size')+ 'px}');
	// Background
	res.push(this._elementId+ ' .leaflet-popup .leaflet-popup-content-wrapper, '+ this._elementId+ ' .leaflet-popup .leaflet-popup-tip {background-color: '+ this.getParam('marker_infownd_bg_color')+ ';}');
	return res.join('');
};
umsLeafletMap.prototype._mapTypeExists = function(typeUrl) {
	this._getMapTypes();
	if(typeof(this._mapTypes[ typeUrl ]) !== 'undefined')
		return true;
	/*if(this._providerData && this._providerData.types && this._providerData.types.indexOf(typeUrl) !== -1)
		return true;*/
	return false;
};
umsLeafletMap.prototype._getMapTypes = function() {
	if(!this._mapTypes) {
		var attr = '';
		this._mapTypes = {};
		var typesVarName = 'umsLeaFletTypes__'+ (this._provider ? this._provider : this._fullEngine);
		if(this._providerData) {
			this._mapTypes[ this._providerData.url ] = {
				// In first element there should be attr
				'attr': window[ typesVarName ][ Object.keys(window[ typesVarName ])[0] ].attr
			};
		}
		for(var typeName in window[ typesVarName ]) {
			if(typeof(window[ typesVarName ][ typeName ]) === 'object') {
				attr = window[ typesVarName ][ typeName ]['attr'];
			}
			this._mapTypes[ typeName ] = {
				'attr': attr
			};
		}
	}
	return this._mapTypes;
};
umsLeafletMap.prototype._getAttributions = function(typeUrl) {
	this._getMapTypes();
	return this._mapTypes[ typeUrl ].attr;
};
umsLeafletMap.prototype.enableClasterization = function(clasterType, needTrigger) {
	switch(clasterType) {
		case 'MarkerClusterer':	// Support only this one for now
			this._clearCluster();
			this._createCluster();
			this._addMarkersToCluster();
			this._mapObj.addLayer(this._clasterer);
			break;
	}
	this._clastererEnabled = true;
};
umsLeafletMap.prototype._addMarkersToCluster = function() {
	if(this._markers.length > 0) {
		for(var i = 0; i < this._markers.length; i++) {
			this._clasterer.addLayer(this._markers[ i ].getRawMarkerInstance());
			this._markers[i].removeFromMap();
		}
	}
};
umsLeafletMap.prototype.disableClasterization = function() {
	this._clearCluster();
	if(this._markers.length > 0) {
		for(var i = 0; i < this._markers.length; i++) {
			this._markers[i].addToMap();
		}
	}
	this._clastererEnabled = false;
};
umsLeafletMap.prototype._clearCluster = function() {
	if(this._clasterer) {
		this._clasterer.remove();
		this._clasterer = null;
	}
};
umsLeafletMap.prototype._createCluster = function() {
	if(!this._clasterer) {
		var clasterGridSize = parseInt(this.getParam('marker_clasterer_grid_size'));
		if(!clasterGridSize)
			clasterGridSize = 60;
		var opts = {
			maxClusterRadius: clasterGridSize,
		};
		this._clasterer = new L.MarkerClusterGroup(opts);
	}
};
umsLeafletMap.prototype._refreshCluster = function() {
	this._clearCluster();
	this._createCluster();
	this._addMarkersToCluster();
	this._mapObj.addLayer(this._clasterer);
};
umsLeafletMap.prototype.setClusterSize = function(size) {
	this.setParam('marker_clasterer_grid_size', size);
	this._refreshCluster();
};
umsLeafletMap.prototype._setMinZoomLevel = function() {
	umsLeafletMap.superclass._setMinZoomLevel.apply(this, arguments);
	var minZoom = parseInt(this._mapParams.zoom_min) ? parseInt(this._mapParams.zoom_min) : null;
	this._mapObj.setMinZoom(minZoom);
};
umsLeafletMap.prototype._setMaxZoomLevel = function() {
	umsLeafletMap.superclass._setMaxZoomLevel.apply(this, arguments);
	var maxZoom = parseInt(this._mapParams.zoom_max) ? parseInt(this._mapParams.zoom_max) : null;
	this._mapObj.setMaxZoom(maxZoom);
};
function umsLeafletMapsLoadComplete() {
	g_umsMapLoadObserver.setLoaded('leaflet')
}
umsLeafletMapsLoadComplete();

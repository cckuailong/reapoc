function umsMapboxMap(elementId, mapData, engine) {
	this._navigationControl = null;

	umsMapboxMap.superclass.constructor.apply(this, arguments);
}
extendUms(umsMapboxMap, umsBaseMap);
/*umsMapboxMap.prototype.init = function() {
	if(!g_umsMapLoadObserver.isLoaded(this._engine)) {
		var self = this;
		setTimeout(function(){
			self.init();
		}, 500);
		return;
	}
	umsMapboxMap.superclass.init.apply(this, arguments);
};*/
umsMapboxMap.prototype._beforeInit = function() {
	this._mapParams.zoom = this._mapParams.zoom ? parseInt(this._mapParams.zoom) : 10;

	if(typeof(this._mapParams.zoom_min) !== 'undefined') {
		this._mapParams.minZoom = parseInt(this._mapParams.zoom_min);
	}
	if(typeof(this._mapParams.zoom_max) !== 'undefined') {
		this._mapParams.maxZoom = parseInt(this._mapParams.zoom_max);
	}
	if(typeof(this._mapParams.mouse_wheel_zoom) !== 'undefined') {
		this._mapParams.scrollZoom = parseInt(this._mapParams.mouse_wheel_zoom);
	}
	if(typeof(this._mapParams.draggable) !== 'undefined') {
		this._mapParams.dragPan = parseInt(this._mapParams.draggable);
	}
	if(typeof(this._mapParams.dbl_click_zoom) !== 'undefined') {
		this._mapParams.doubleClickZoom = parseInt(this._mapParams.dbl_click_zoom);
	}
	if(typeof(this._mapParams.map_type) !== 'undefined') {
		this._mapParams.style = this._mapParams.map_type;
	}
	umsMapboxMap.superclass._beforeInit.apply(this, arguments);
};
umsMapboxMap.prototype._createMapObj = function() {
	//console.log(this._element);
	var centerCoords = this._getCreateCoords()
	,	params = jQuery.extend(this._mapParams, {
			center: [centerCoords.coord_y, centerCoords.coord_x]
		,	container: this._element.getAttribute('id')
		//,	style: 'mapbox://styles/mapbox/streets-v9'	// TODO: Look at this - this is Map Type?
		});
	this._mapObj = new L.mapbox.map( params );
};
umsMapboxMap.prototype._afterInit = function() {
	if(typeof(this._mapParams.zoom_control) !== 'undefined'
		&& this._mapParams.zoom_control !== 'none'
	) {
		this.enbZoom(true);
	}
	if(typeof(this._mapParams.zoom) !== 'undefined') {
		this._mapObj.setZoom(this._mapParams.zoom);
	}
	umsMapboxMap.superclass._afterInit.apply(this, arguments);
};
umsMapboxMap.prototype.addEventListener = function(event, clb) {
	if(event === 'zoom_changed') {
		event = 'zoomend';
	}
	this._mapObj.on(event, clb);
};
umsMapboxMap.prototype.enbZoom = function(mode) {
	if(mode) {
		var opts = {showCompass: true, showZoom: true};
		if(typeof(this._mapParams.navigation_bar_mode) !== 'undefined') {
			switch(this._mapParams.navigation_bar_mode) {
				case 'zoom_only':
					opts.showCompass = false;
					break;
				case 'compass_only':
					opts.showZoom = false;
					break;
			}
		}
		this._navigationControl = new L.mapbox.NavigationControl(opts);
		this._mapObj.addControl(this._navigationControl);
	} else if(this._navigationControl) {
		this._mapParams.zoom_control = 'none';
		this._mapObj.removeControl(this._navigationControl);
	}
};
umsMapboxMap.prototype.enbDraggable = function(mode) {
	var isMobile = ( navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i) ) ? true : false;
	switch (mode) {
		case 'enable':
			this._mapObj.dragPan.enable();
			break;
		case 'disable':
			this._mapObj.dragPan.disable();
			break;
		case 'desktop':
			if (!isMobile) {
				this._mapObj.dragPan.enable();
			} else {
				this._mapObj.dragPan.disable();
			}
			break;
		case 'mobile':
			if (isMobile) {
				this._mapObj.dragPan.enable();
			} else {
				this._mapObj.dragPan.disable();
			}
			break;
	}
};
umsMapboxMap.prototype.setCenter = function (lat, lng) {
	this._mapObj.setCenter([lng, lat]);
};
umsMapboxMap.prototype.getCenter = function () {
	var center = this._mapObj.getCenter();
	return {lat: center.lat, lng: center.lng};
};
umsMapboxMap.prototype.setZoom = function (zoomLevel) {
	this._mapObj.setZoom(zoomLevel);
};
umsMapboxMap.prototype.getZoom = function () {
	return this._mapObj.getZoom();
};
umsMapboxMap.prototype.setNavigationBarMode = function(mode) {
	if(typeof(this._mapParams.zoom_control) !== 'undefined'
		&& this._mapParams.zoom_control !== 'none'
	) {
		this._mapParams.navigation_bar_mode = mode;
		this.enbZoom(false);
		this.enbZoom(true);
	}
};
umsMapboxMap.prototype.enbWheelZoom = function(mode) {
	mode
		? this._mapObj.scrollZoom.enable()
		: this._mapObj.scrollZoom.disable();
};
umsMapboxMap.prototype.getNavigationBarMode = function() {
	this._methodRedefineNotice('umsMapboxMap.prototype.getNavigationBarMode');
};
umsMapboxMap.prototype.setMapType = function (mapType) {
	this._mapObj.setStyle(mapType, {
		diff: false
	});
};
umsMapboxMap.prototype.geocodeQuery = function(search, clb, errorClb) {
	jQuery.ajax({
		url: 'https://api.mapbox.com/geocoding/v5/mapbox.places/'+ search+ '.json?access_token='+ UMS_DATA.mapboxKey
	,	dataType: 'text'
	,	success: function (data) {
			if(clb) {
				if(data) {
					var r = JSON.parse(data);
					if(r) {
						var formattedResult = [];
						if(r.features && r.features.length > 0) {
							for(var i = 0; i < r.features.length; i++) {
								// All addresses in all geocoders should have same - our - format results
								formattedResult.push({
									formatted_address: r.features[ i ].place_name
								,	lat: r.features[ i ].center[ 1 ]
								,	lng: r.features[ i ].center[ 0 ]
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
umsMapboxMap.prototype._generateMarkerInfoWndStyle = function() {
	var res = [];

	res.push(this._elementId+ ' .marker-cluster {background-color: '+ this.getParam('marker_clasterer_border_color') + '; opacity:1;}');
	res.push(this._elementId+ ' .marker-cluster {color: '+ this.getParam('marker_clasterer_text_color') + ';}');
	res.push(this._elementId+ ' .marker-cluster div {background-color: '+ this.getParam('marker_clasterer_background_color') + '; opacity:1;}');

	if(this.getParam('marker_infownd_width_units') === 'px') {
		res.push(this._elementId+ ' .mapboxgl-popup {width: '+ this.getParam('marker_infownd_width')+ 'px;}');
	}
	if(this.getParam('marker_infownd_height_units') === 'px') {
		res.push(this._elementId+ ' .mapboxgl-popup {height: '+ this.getParam('marker_infownd_height')+ 'px;}');
	}
	res.push(this._elementId+ ' .mapboxgl-popup .umsMarkerTitle {color: '+ this.getParam('marker_title_color')
			+ '; font-size: '+ this.getParam('marker_title_size')+ 'px}');
	res.push(this._elementId+ ' .mapboxgl-popup .umsMarkerDesc, '+ this._elementId+ ' .mapboxgl-popup .umsMarkerDesc * {font-size: '+ this.getParam('marker_desc_size')+ 'px}');
	res.push(this._elementId+ ' .mapboxgl-popup .mapboxgl-popup-content {background-color: '+ this.getParam('marker_infownd_bg_color')+ ';}');
	return res.join('');
};

umsMapboxMap.prototype.enableClasterization = function(clasterType, needTrigger) {
	//console.log('Enable!!!', clasterType);
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
umsMapboxMap.prototype._addMarkersToCluster = function() {
	if(this._markers.length > 0) {
		for(var i = 0; i < this._markers.length; i++) {
			this._clasterer.addLayer(this._markers[ i ].getRawMarkerInstance());
			this._markers[i].removeFromMap();
		}
	}
};
umsMapboxMap.prototype.disableClasterization = function() {
	this._clearCluster();
	if(this._markers.length > 0) {
		for(var i = 0; i < this._markers.length; i++) {
			this._markers[i].addToMap();
		}
	}
	this._clastererEnabled = false;
};
umsMapboxMap.prototype._clearCluster = function() {
	if(this._clasterer) {
		this._clasterer.remove();
		this._clasterer = null;
	}
};
umsMapboxMap.prototype._createCluster = function() {
	if(!this._clasterer) {
		var clasterGridSize = parseInt(this.getParam('marker_clasterer_grid_size'));
		if(!clasterGridSize)
			clasterGridSize = 60;
		var opts = {
			maxClusterRadius: clasterGridSize
		};
		this._clasterer = new L.MarkerClusterGroup(opts);
	}
};
umsMapboxMap.prototype._refreshCluster = function() {
	this._clearCluster();
	this._createCluster();
	this._addMarkersToCluster();
	this._mapObj.addLayer(this._clasterer);
};
umsMapboxMap.prototype.setClusterSize = function(size) {
	this.setParam('marker_clasterer_grid_size', size);
	this._refreshCluster();
};

function umsMapboxMapsLoadComplete() {
	L.mapbox.accessToken = UMS_DATA.mapboxKey;
	g_umsMapLoadObserver.setLoaded('mapbox')
}
// It's load sync
umsMapboxMapsLoadComplete();

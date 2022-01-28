function umsBingMap(elementId, mapData, engine) {
	this._lastZoom = 0;
	this._geocodeSearchManager = null;
	umsBingMap.superclass.constructor.apply(this, arguments);
}
extendUms(umsBingMap, umsBaseMap);
umsBingMap.prototype.init = function() {
	if(!g_umsMapLoadObserver.isLoaded(this._engine)) {
		var self = this;
		setTimeout(function(){
			self.init();
		}, 500);
		return;
	}
	umsBingMap.superclass.init.apply(this, arguments);
};
umsBingMap.prototype._beforeInit = function() {
	//if(typeof(this._mapParams.zoom_min) !== 'undefined') {
		this._mapParams.zoom = this._mapParams.zoom ? parseInt(this._mapParams.zoom) : 10;
	//}
	if(typeof(this._mapParams.zoom_min) !== 'undefined') {
		this._mapParams.minZoom = parseInt(this._mapParams.zoom_min);
	}
	if(typeof(this._mapParams.zoom_max) !== 'undefined') {
		this._mapParams.maxZoom = parseInt(this._mapParams.zoom_max);
	}
	if(typeof(this._mapParams.navigation_bar_mode) !== 'undefined'
		&& Microsoft.Maps.NavigationBarMode[this._mapParams.navigation_bar_mode]
	) {
		this._mapParams.navigationBarMode = Microsoft.Maps.NavigationBarMode[this._mapParams.navigation_bar_mode];
	}
	if(typeof(this._mapParams.map_type) !== 'undefined'
		&& Microsoft.Maps.MapTypeId[this._mapParams.map_type]
	) {
		this._mapParams.mapTypeId = Microsoft.Maps.MapTypeId[this._mapParams.map_type];
	}
	if(typeof(this._mapParams.draggable) !== 'undefined') {
		var isMobile = ( navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i) ) ? true : false;
		switch (this._mapParams.draggable) {
			case 'enable':
				this._mapParams.disablePanning = false;
			break;
			case 'disable':
				this._mapParams.disablePanning = true;
			break;
			case 'desktop':
				if (!isMobile) {
					this._mapParams.disablePanning = false;
				} else {
					this._mapParams.disablePanning  = true;
				}
			break;
			case 'mobile':
				if (isMobile) {
					this._mapParams.disablePanning = false;
				} else {
					this._mapParams.disablePanning = true;
				}
			break;
		}
	}
	umsBingMap.superclass._beforeInit.apply(this, arguments);
};
umsBingMap.prototype._createMapObj = function() {
	var centerCoords = this._getCreateCoords()
	,	params = jQuery.extend({
			center: new Microsoft.Maps.Location(centerCoords.coord_x, centerCoords.coord_y)
		//,	zoom: this._mapParams.zoom ? parseInt(this._mapParams.zoom) : 10
		//,	navigationBarMode: Microsoft.Maps.NavigationBarMode.compact
		//,	mapTypeId: Microsoft.Maps.MapTypeId.aerial
		,	showZoomButtons: this._mapParams.zoom_control === 'none' ? false : true
		//,	disablePanning: parseInt(this._mapParams.draggable) ? true : false
		,	disableScrollWheelZoom: typeof(this._mapParams.mouse_wheel_zoom) !== 'undefined'
				&& !parseInt(this._mapParams.mouse_wheel_zoom) ? true : false
	}, this._mapParams);

	this._mapObj = new Microsoft.Maps.Map(this._element, params);
	this._lastZoom = this.getZoom();
};
umsBingMap.prototype._afterInit = function() {
	if(typeof(this._mapParams.dbl_click_zoom) !== 'undefined'
		&& !parseInt(this._mapParams.dbl_click_zoom)
	) {
		Microsoft.Maps.Events.addHandler(this._mapObj, 'dblclick', function(e){
			e.handled = true;
		});
	}
	if(typeof(this._mapParams.zoom) !== 'undefined') {
		this.setZoom(this._mapParams.zoom);
	}
    if(typeof(this._mapParams.hide_poi) !== 'undefined') {
        this.setHidePoi(parseInt(this._mapParams.hide_poi));
    }
	umsBingMap.superclass._afterInit.apply(this, arguments);
};

umsBingMap.prototype.addEventListener = function(event, clb) {
	var self = this;
	if(!this._initComplete) {
		setTimeout(function(){
			self.addEventListener(event, clb);
		}, 500);
		return;
	}
	if(event == 'zoom_changed') {
		Microsoft.Maps.Events.addHandler(this._mapObj, 'viewchangeend', function(){
			self._checkZoomChangedClb(clb);
		});
	}
	if(event == 'dragend') {
		Microsoft.Maps.Events.addHandler(this._mapObj, 'viewchangeend', function(){
			self._checkDragChangeClb(clb);
		});
	}
	if(event == 'click') {
		Microsoft.Maps.Events.addHandler(this._mapObj, 'click', clb);
	}
};
umsBingMap.prototype._checkZoomChangedClb = function(clb) {
	if(this._lastZoom !== this.getZoom()) {
		this._lastZoom = this.getZoom();
		clb();
	}
};
umsBingMap.prototype._checkDragChangeClb = function(clb) {
	// Don't change - if coords changed for now
	clb();
};
umsBingMap.prototype.enbZoom = function(mode) {
	this._mapObj.setOptions({
		showZoomButtons: mode
	});
};
umsBingMap.prototype.enbDraggable = function(mode) {
	var isMobile = ( navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i) ) ? true : false;
	switch (mode) {
		case 'enable':
			this._mapObj.setOptions({
				disablePanning: false
			});
		break;
		case 'disable':
			this._mapObj.setOptions({
				disablePanning: true
			});
		break;
		case 'desktop':
			if (!isMobile) {
				this._mapObj.setOptions({
					disablePanning: false
				});
			} else {
				this._mapObj.setOptions({
					disablePanning: true
				});
			}
		break;
		case 'mobile':
			if (isMobile) {
				this._mapObj.setOptions({
					disablePanning: false
				});
			} else {
				this._mapObj.setOptions({
					disablePanning: true
				});
			}
		break;
	}

};
umsBingMap.prototype.setCenter = function (lat, lng) {
	this._mapObj.setView({
        center: new Microsoft.Maps.Location(lat, lng)
    });
};
umsBingMap.prototype.getCenter = function () {
	var center = this._mapObj.getCenter();
	return {lat: center.latitude, lng: center.longitude};
};
umsBingMap.prototype.setZoom = function (zoomLevel) {
	this._mapObj.setView({
        zoom: parseInt(zoomLevel)
    });
};
umsBingMap.prototype.getZoom = function () {
	return this._mapObj.getZoom();
};
umsBingMap.prototype.setNavigationBarMode = function(mode) {
	// This is not workig - just because Bing don't want to allow change this dynamic
	this._mapObj.setOptions({
		navigationBarMode: Microsoft.Maps.NavigationBarMode[ mode ]
	});
};
umsBingMap.prototype.enbWheelZoom = function(mode) {
	this._mapObj.setOptions({
		navigationBarMode: !mode
	});
};
umsBingMap.prototype.getNavigationBarMode = function() {
	//this._methodRedefineNotice('umsBaseMap.prototype.getNavigationBarMode');
};
umsBingMap.prototype.geocodeQuery = function(search, clb, errorClb) {
	if (!this._geocodeSearchManager) {
		var self = this;
		//Create an instance of the search manager and call the geocodeQuery function again.
		Microsoft.Maps.loadModule('Microsoft.Maps.Search', function () {
			self._geocodeSearchManager = new Microsoft.Maps.Search.SearchManager(self._mapObj);
			self.geocodeQuery(search, clb, errorClb);
		});
	} else {
		var searchRequest = {
			where: search
		,	callback: function (r) {
				if(clb) {
					var formattedResult = [];
					if(r && r.results && r.results.length > 0) {
						for(var i = 0; i < r.results.length; i++) {
							// All addresses in all geocoders should have same - our - format results
							//console.log(r.results.name);
							formattedResult.push({
								formatted_address: r.results[ i ].name
							,	lat: r.results[ i ].location.latitude
							,	lng: r.results[ i ].location.longitude
							});
						}
					}
					clb(formattedResult);
				} else
					console.log('So, what should I do next, ha?');
			}
		,	errorCallback: function (e) {
				if(errorClb) {
					errorClb(e);
				}
			}
		};
		//Make the geocode request.
		this._geocodeSearchManager.geocode(searchRequest);
	}
};
umsBingMap.prototype.setMapType = function (mapType) {
	this._mapObj.setView({
        mapTypeId: Microsoft.Maps.MapTypeId[ mapType ]
    });
};
umsBingMap.prototype.setHidePoi = function (hidePoi) {
	if (hidePoi) {
        this._mapObj.setView({
            labelOverlay: Microsoft.Maps.LabelOverlay.hidden
        });
    } else {
        this._mapObj.setView({
            labelOverlay: Microsoft.Maps.LabelOverlay.visible
        });
	}
};
umsBingMap.prototype.enableClasterization = function(clasterType, needTrigger) {
	switch(clasterType) {
		case 'MarkerClusterer':	// Support only this one for now
			this._clearCluster();
			var self = this;
			var enbClusterClb = function() {
				self._createCluster();
				self._addMarkersToCluster();
			};
			if(typeof(Microsoft.Maps.ClusterLayer) === 'undefined') {
				Microsoft.Maps.loadModule('Microsoft.Maps.Clustering', enbClusterClb);
			} else {
				enbClusterClb();
			}
			break;
	}
	this._clastererEnabled = true;
};
umsBingMap.prototype._addMarkersToCluster = function() {
	if(this._markers.length > 0) {
		this._mapObj.entities.clear();
		this._clasterer.setPushpins(this.getAllRawMarkers());
	}
};
umsBingMap.prototype.disableClasterization = function() {
	this._clearCluster();
	if(this._markers.length > 0) {
		for(var i = 0; i < this._markers.length; i++) {
			this._markers[i].addToMap();
		}
	}
	this._clastererEnabled = false;
};
umsBingMap.prototype._clearCluster = function() {
	if(this._clasterer) {
		this._clasterer.clear();
	}
};
umsBingMap.prototype._createCluster = function() {
	if(!this._clasterer) {
		var clasterGridSize = parseInt(this.getParam('marker_clasterer_grid_size'))
		,	self = this
		,	zoomIntoClusterClb = function(e) {
				if (e.target.containedPushpins) {
					var locs = [];
					for (var i = 0, len = e.target.containedPushpins.length; i < len; i++) {
						//Get the location of each pushpin.
						locs.push(e.target.containedPushpins[i].getLocation());
					}

					//Create a bounding box for the pushpins.
					var bounds = Microsoft.Maps.LocationRect.fromLocations(locs);

					//Zoom into the bounding box of the cluster.
					//Add a padding to compensate for the pixel area of the pushpins.
					self._mapObj.setView({ bounds: bounds, padding: 100 });
				}
		},	opts = {
				clusteredPinCallback: function(clusterPin) {
					Microsoft.Maps.Events.addHandler(self._clasterer, 'click', zoomIntoClusterClb);
					clusterPin.setOptions({ color: self.getParam('marker_clasterer_background_color') });
				}
			};
		if(clasterGridSize) {
			opts.gridSize = clasterGridSize;
		}
		this._clasterer = new Microsoft.Maps.ClusterLayer([], opts);
		this._mapObj.layers.insert( this._clasterer );
	}
};
umsBingMap.prototype._refreshCluster = function() {
	this._clearCluster();
	this._createCluster();
	this._addMarkersToCluster();
};
umsBingMap.prototype.setClusterSize = function(size) {
	this.setParam('marker_clasterer_grid_size', size);
	if(this._clasterer && parseInt(size)) {
		this._clasterer.setOptions({
			gridSize: parseInt(size)
		});
	}
	//this._refreshCluster();
};
// TODO: Make it work as all other - with indexes like Microsoft like it
umsBingMap.prototype.clearMarkers = function() {
	if(this._markers && this._markers.length) {
		this._mapObj.entities.clear();
		this._markers = [];
	}
};
umsBingMap.prototype._recalcMarkersEntryId = function() {
	// TODO: Make it work when markers is removed from collection
};
umsBingMap.prototype._generateMarkerInfoWndStyle = function() {
	var res = [];
	// Width
	if(this.getParam('marker_infownd_width_units') === 'px') {
		res.push(this._elementId+ ' .Infobox {width: '+ this.getParam('marker_infownd_width')+ 'px !important;}');
	} else {
		res.push(this._elementId+ ' .Infobox {max-width: 200px !important;}');
	}
	// Height
	if(this.getParam('marker_infownd_height_units') === 'px') {
		res.push(this._elementId+ ' .Infobox {height: '+ this.getParam('marker_infownd_height')+ 'px !important;}');
	} else {
		res.push(this._elementId+ ' .Infobox {height: 200px;}');
	}
	// Title style
	res.push(this._elementId+ ' .infobox-title {color: '+ this.getParam('marker_title_color')
			+ '; font-size: '+ this.getParam('marker_title_size')+ 'px}');
	// Description
	res.push(this._elementId+ ' .infobox-info, '+ this._elementId+ ' .infobox-info * {font-size: '+ this.getParam('marker_desc_size')+ 'px}');
	// Background tip
	res.push(this._elementId+ ' .MicrosoftMap .Infobox .infobox-stalk {background-image: none;'
		+ 'width: 0;'
		+ 'height: 0;'
		+ 'border-style: solid;'
		+ 'border-width: 16px 7px 0 7px;'
		+ 'border-color: '+ this.getParam('marker_infownd_bg_color')+ ' transparent transparent transparent;}');
	// Background
	res.push(this._elementId+ ' .Infobox, '+ this._elementId+ ' .Infobox .leaflet-popup-tip {background-color: '+ this.getParam('marker_infownd_bg_color')+ ';}');
	return res.join('');
};
umsBingMap.prototype._setMinZoomLevel = function() {
	umsBingMap.superclass._setMinZoomLevel.apply(this, arguments);
	var minZoom = parseInt(this._mapParams.zoom_min) ? parseInt(this._mapParams.zoom_min) : null;
	this._mapObj.setOptions({
		minZoom: minZoom
	});
};
umsBingMap.prototype._setMaxZoomLevel = function() {
	umsBingMap.superclass._setMaxZoomLevel.apply(this, arguments);
	var maxZoom = parseInt(this._mapParams.zoom_max) ? parseInt(this._mapParams.zoom_max) : null;
	this._mapObj.setOptions({
		maxZoom: maxZoom
	});
};
function umsBingMapsLoadComplete() {
	g_umsMapLoadObserver.setLoaded('bing')
}

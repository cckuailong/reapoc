function umsBingMarker(map, params) {
	this._mapEntryId = 0;
	umsBingMarker.superclass.constructor.apply(this, arguments);
}
extendUms(umsBingMarker, umsBaseMarker);
umsBingMarker.prototype._createMarkerObj = function() {
	var pos = this._markerParams.position && typeof(this._markerParams.coord_x) === 'undefined'
		? new Microsoft.Maps.Location(this._markerParams.position.lat, this._markerParams.position.lng)
		: new Microsoft.Maps.Location(this._markerParams.coord_x, this._markerParams.coord_y);
	var params = {
		draggable: this._markerParams.draggable ? true : false
	};
	if(this._markerParams.icon) {
		params.icon = this._markerParams.icon;
	}
	if(this._markerParams.icon_data) {
		this._markerParams.icon_data.width = this._markerParams.icon_data.width ? parseInt(this._markerParams.icon_data.width) : false;
		this._markerParams.icon_data.height = this._markerParams.icon_data.height ? parseInt(this._markerParams.icon_data.height) : false;
		if(this._markerParams.icon_data.width && this._markerParams.icon_data.height) {
			params.anchor = new Microsoft.Maps.Point(this._markerParams.icon_data.width / 2, this._markerParams.icon_data.height);
			//console.log(params.anchor);
		}
	}
	var pushpin = new Microsoft.Maps.Pushpin(pos, params);
	this._mapEntryId = this._map.getRawMapInstance().entities.getLength();
	this._map.getRawMapInstance().entities.push(pushpin);
	return pushpin;
};
umsBingMarker.prototype._setAnchor = function(iconWidth, iconHeight) {
	// TODO: Check this, maybe it should apply on icon change?
	//console.log(iconWidth, iconHeight);
	/*this._markerObj.setOptions({
		anchor: new Microsoft.Maps.Point(0, -iconHeight)
	});*/
	//console.log('_setAnchor', iconWidth, iconHeight);
};
/*umsBingMarker.prototype.setTitle = function(title, noRefresh) {
	umsBingMarker.superclass.setTitle.apply(this, arguments);
};
umsBingMarker.prototype.setDescription = function (description, noRefresh) {
	umsBingMarker.superclass.setTitle.apply(this, arguments);
};*/
umsBingMarker.prototype.setVisible = function(state) {
    this._markerObj.setOptions({visible: state})
    if (state) {
        this._markerObj.opacity = 1;
	} else {
        this._markerObj.opacity = 0;
	}
};
umsBingMarker.prototype.getPosition = function() {
	var loc = this._markerObj.getLocation();
	return {lat: loc.latitude, lng: loc.longitude}
};
umsBingMarker.prototype.setPosition = function(lat, lng) {
	this._markerObj.setLocation(new Microsoft.Maps.Location(lat, lng));
};
umsBingMarker.prototype.lat = function() {
	var loc = this._markerObj.getLocation();
	return loc.latitude;
};
umsBingMarker.prototype.lng = function(lng) {
	var loc = this._markerObj.getLocation();
	return loc.longitude;
};
umsBingMarker.prototype.removeFromMap = function() {
	this._map.getRawMapInstance().entities.removeAt(this._mapEntryId);
};
umsBingMarker.prototype.addToMap = function() {
	this._mapEntryId = this._map.getRawMapInstance().entities.getLength();
	this._map.getRawMapInstance().entities.push(this._markerObj);
};
umsBingMarker.prototype._setInfoWndContent = function() {
	if(!this._infoWindow) {
		var props = {
			visible: false
		,	autoAlignment: true
		,	offset: new Microsoft.Maps.Point(0, -24)
		//,	maxHeight: 1000
		//,	maxWidth: 1000
		};
		if(this._map.getParam('marker_infownd_width_units') === 'px') {
			var widht = parseInt(this._map.getParam('marker_infownd_width'));
			if(widht) {
				props.maxWidth = widht;
			}
		}
		else {
			props.maxWidth = 200;
		}
		if(this._map.getParam('marker_infownd_height_units') === 'px') {
			var height = parseInt(this._map.getParam('marker_infownd_height'));
			if(height) {
				props.maxHeight = height;
			}
		}
		else {
			props.maxHeight = 200;
		}
		if(this._markerParams.icon_data && this._markerParams.icon_data.height) {
			props.offset = new Microsoft.Maps.Point(0, parseInt(this._markerParams.icon_data.height));
		}

		this._infoWindow = new Microsoft.Maps.Infobox(this._markerObj.getLocation(), props);

		this._infoWindow.setMap(this._map.getRawMapInstance());

		var selfMarker = this;
		Microsoft.Maps.Events.addHandler(this._infoWindow, 'infoboxChanged', function(e){
			selfMarker._infoWndOpened = e.target.getVisible();
		});
	}
	var description = this._markerParams.description ? this._markerParams.description.replace(/\n/g, '<br/>') : false
	,	title = this._markerParams.title ? this._markerParams.title : false;

	this._infoWindow.setOptions({
		title: title
	,	description: description
	,	location: this._markerObj.getLocation()
	});
	this._markerParams.infoBox = this._infoWindow;
};
umsBingMarker.prototype._closeInfoBoxes = function() {
	var allMapMArkers = this._map.getAllMarkers();
	if (allMapMArkers && allMapMArkers.length > 1) {
		for(var i = 0; i < allMapMArkers.length; i++) {
			if (allMapMArkers[i]._markerParams.infoBox) {
				allMapMArkers[i]._markerParams.infoBox.setOptions({
					visible:false,
				});
			}
		}
	}
	var allMapShapes = this._map.getAllShapes();
	if(allMapShapes && allMapShapes.length > 1) {
		for(var i = 0; i < allMapShapes.length; i++) {
			if (allMapShapes[i]._shapeParams.infoBox) {
				allMapShapes[i]._shapeParams.infoBox.setOptions({
					visible:false,
				});
			}
		}
	}
}
umsBingMarker.prototype._getInfoWndLocation = function() {
	var pos = this._markerObj.getLocation();
};
umsBingMarker.prototype._updateInfoWndContent = function() {
	this._setInfoWndContent();
};
umsBingMarker.prototype.addEventListener = function(event, callback) {
	Microsoft.Maps.Events.addHandler(this._markerObj, event, callback);
};
umsBingMarker.prototype._openInfoWnd = function() {
	this._closeInfoBoxes();
	this._infoWindow.setOptions({
		visible: true
	,	location: this._markerObj.getLocation()	// Update it for now each time - to make sure position is correct
	});
};
umsBingMarker.prototype.directOpenInfoWnd = function() {
	var allMapMArkers = this._map.getAllMarkers();
	if(allMapMArkers && allMapMArkers.length > 1) {
		for(var i = 0; i < allMapMArkers.length; i++) {
			allMapMArkers[i].hideInfoWnd();
		}
	}
	this._openInfoWnd();
};
umsBingMarker.prototype._closeInfoWnd = function() {
	this._infoWindow.setOptions({
		visible: false
	});
};
umsBingMarker.prototype.getIcon = (function() {
	return this._markerObj.getIcon();
});
umsBingMarker.prototype.setIcon = function(iconPath) {
	this._markerObj.setOptions({
		icon: iconPath
	});
};

function umsLeafletMarker(map, params) {
	//this._$markerEl = null;
	this._iconObj = null;

	umsLeafletMarker.superclass.constructor.apply(this, arguments);
	//this._mapEntryId = 0;
}
extendUms(umsLeafletMarker, umsBaseMarker);
/*umsLeafletMarker.prototype.init = function() {
	this._ignoreBaseInfoWndBind = true;
	umsLeafletMarker.superclass.init.apply(this, arguments);
};*/
umsLeafletMarker.prototype._createMarkerObj = function() {
	var pos = this._markerParams.position && typeof(this._markerParams.coord_x) === 'undefined'
		? [this._markerParams.position.lat, this._markerParams.position.lng]
		: [this._markerParams.coord_x, this._markerParams.coord_y];

	var opts = {
		draggable: this._markerParams.draggable
	};
	if(this._markerParams.icon) {
		this._createIcon(this._markerParams.icon);
		opts.icon = this._iconObj;
	}
	var marker = L.marker(pos, opts)
		.addTo(this._map.getRawMapInstance());

	return marker;
};
umsLeafletMarker.prototype.addToMap = function() {
	this._markerObj.addTo(this._map.getRawMapInstance());
};
umsLeafletMarker.prototype._setAnchor = function(iconWidth, iconHeight) {
	//console.log(iconWidth, iconHeight);
	/*this._markerObj.setOptions({
		anchor: new Microsoft.Maps.Point(0, -iconHeight)
	});*/
	//console.log('_setAnchor', iconWidth, iconHeight);
};
/*umsLeafletMarker.prototype.setTitle = function(title, noRefresh) {
	umsLeafletMarker.superclass.setTitle.apply(this, arguments);
};
umsLeafletMarker.prototype.setDescription = function (description, noRefresh) {
	umsLeafletMarker.superclass.setTitle.apply(this, arguments);
};*/
umsLeafletMarker.prototype.setVisible = function(state) {
	if (state) {
        this._markerObj.setOpacity(1);
        this._markerObj.opacity = 1;
	} else {
        this._markerObj.setOpacity(0);
        this._markerObj.opacity = 0;
        this._markerObj.closePopup();
	}
};
umsLeafletMarker.prototype.getPosition = function() {
	var loc = this._markerObj.getLatLng();
	return {lat: loc.lat, lng: loc.lng}
};
umsLeafletMarker.prototype.setPosition = function(lat, lng) {
	this._markerObj.setLatLng([lat, lng]);
};
umsLeafletMarker.prototype.lat = function() {
	var loc = this._markerObj.getLatLng();
	return loc.lat;
};
umsLeafletMarker.prototype.lng = function() {
	var loc = this._markerObj.getLatLng();
	return loc.lng;
};
umsLeafletMarker.prototype.removeFromMap = function() {
	this._markerObj.removeFrom(this._map.getRawMapInstance());
};
umsLeafletMarker.prototype._setInfoWndContent = function(withOpenPopup) {
	if(!this._infoWindow) {
		var offsetH = 0;
		if(this._markerParams
			&& this._markerParams.icon_data
			&& this._markerParams.icon_data.width
			&& this._markerParams.icon_data.height
		) {
			offsetH = -1 * parseInt(this._markerParams.icon_data.height);
			//opts.iconSize = [parseInt(this._markerParams.icon_data.width), parseInt(this._markerParams.icon_data.height)];
		}
		this._infoWindow = new L.popup({
			// TODO: Make this offset - to depends from marker icon size
			offset: L.point(0, offsetH)
		});
		this._markerObj.bindPopup(this._infoWindow) // add popups
	}
	var description = this._markerParams.description ? this._markerParams.description.replace(/\n/g, '<br/>') : false
	,	title = this._markerParams.title ? this._markerParams.title : false
	,	content = (title ? '<h3 class="umsMarkerTitle">' + title + '</h3>' : '')
			+ (description ? '<div class="umsMarkerDesc">' + description + '</div>' : '');

	this._infoWindow.setContent(content);
	this._infoWindow.update();
	if(withOpenPopup) {
		this._infoWndOpened = true;
		this._markerObj.openPopup();
	}
};
/*umsLeafletMarker.prototype.refreshInfoWnd = function() {

};*/
umsLeafletMarker.prototype._updateInfoWndContent = function(withOpenPopup) {
	this._setInfoWndContent(withOpenPopup);
	this._infoWndWasInited = true;
};
umsLeafletMarker.prototype.addEventListener = function(event, callback) {
	this._markerObj.on(event, callback);
};
umsLeafletMarker.prototype._openInfoWnd = function(withOpenPopup) {
	this._setInfoWndContent(withOpenPopup);
};
umsLeafletMarker.prototype.directOpenInfoWnd = function() {
	if(this._infoWindow) {
		this._markerObj.openPopup();
	} else
		this._setInfoWndContent();
};
umsLeafletMarker.prototype._closeInfoWnd = function() {
	if(this._infoWindow.isPopupOpen()) {
		this._markerObj.closePopup();
	}
};
umsLeafletMarker.prototype.getIcon = (function() {
	return this._markerObj.getIcon();
});
umsLeafletMarker.prototype.setIcon = function(iconPath) {
	this._createIcon(iconPath);
	this._markerObj.setIcon(this._iconObj);
};
umsLeafletMarker.prototype._createIcon = function(iconPath) {
	var opts = {
		iconUrl: iconPath
	};
	if(this._markerParams.icon_data) {
		this._markerParams.icon_data.width = this._markerParams.icon_data.width ? parseInt(this._markerParams.icon_data.width) : false;
		this._markerParams.icon_data.height = this._markerParams.icon_data.height ? parseInt(this._markerParams.icon_data.height) : false;
		if(this._markerParams.icon_data.width && this._markerParams.icon_data.height) {
			opts.iconSize = [this._markerParams.icon_data.width, this._markerParams.icon_data.height];
			opts.iconAnchor = [this._markerParams.icon_data.width / 2, this._markerParams.icon_data.height];
		}
	}
	this._iconObj = L.icon(opts);
};
umsLeafletMarker.prototype.showInfoWnd = function( forceUpdateInfoWnd, forceShow, withOpenPopup ) {
	if(this._infoWindow) {
		this._infoWndOpened = this._infoWindow.isOpen();
	}
	umsLeafletMarker.superclass.showInfoWnd.apply(this, arguments);
};

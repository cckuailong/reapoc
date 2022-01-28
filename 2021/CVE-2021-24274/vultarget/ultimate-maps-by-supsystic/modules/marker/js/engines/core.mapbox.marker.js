function umsMapboxMarker(map, params) {
	this._$markerEl = null;
	umsMapboxMarker.superclass.constructor.apply(this, arguments);
	//this._mapEntryId = 0;
}
extendUms(umsMapboxMarker, umsBaseMarker);
umsMapboxMarker.prototype._createMarkerObj = function() {
	var pos = this._markerParams.position && typeof(this._markerParams.coord_x) === 'undefined'
		? [this._markerParams.position.lng, this._markerParams.position.lat]
		: [this._markerParams.coord_y, this._markerParams.coord_x];

	this._$markerEl = jQuery('<div class="umsMapboxMarker" />');
	this.setIcon(this._markerParams.icon);

	var marker = new mapboxgl.Marker(this._$markerEl.get(0), {
		draggable: this._markerParams.draggable
	}).setLngLat(pos)
		.addTo(this._map.getRawMapInstance());

	return marker;
};
umsMapboxMarker.prototype._setAnchor = function(iconWidth, iconHeight) {
	/*this._markerObj.setOptions({
		anchor: new Microsoft.Maps.Point(0, -iconHeight)
	});*/
	//console.log('_setAnchor', iconWidth, iconHeight);
};
/*umsMapboxMarker.prototype.setTitle = function(title, noRefresh) {
	umsMapboxMarker.superclass.setTitle.apply(this, arguments);
};
umsMapboxMarker.prototype.setDescription = function (description, noRefresh) {
	umsMapboxMarker.superclass.setTitle.apply(this, arguments);
};*/
umsMapboxMarker.prototype.setVisible = function(state) {
    if (state) {
        this._markerObj.setOpacity(1);
        this._markerObj.opacity = 1;
    } else {
        this._markerObj.setOpacity(0);
        this._markerObj.opacity = 0;
        this._markerObj.closePopup();
    }
};
umsMapboxMarker.prototype.getPosition = function() {
	var loc = this._markerObj.getLngLat();
	return {lat: loc.lat, lng: loc.lng}
};
umsMapboxMarker.prototype.setPosition = function(lat, lng) {
	this._markerObj.setLngLat([lng, lat]);
};
umsMapboxMarker.prototype.lat = function() {
	var loc = this._markerObj.getLngLat();
	return loc.lat;
};
umsMapboxMarker.prototype.lng = function(lng) {
	var loc = this._markerObj.getLngLat();
	return loc.lng;
};
umsMapboxMarker.prototype.removeFromMap = function() {
	this._markerObj.remove();
};
umsMapboxMarker.prototype._setInfoWndContent = function() {
	if(!this._infoWindow) {
		this._infoWindow = new mapboxgl.Popup({ offset: 25 });
		this._markerObj.setPopup(this._infoWindow) // add popups
	}
	///this.refreshInfoWnd();
	var description = this._markerParams.description ? this._markerParams.description.replace(/\n/g, '<br/>') : false
	,	title = this._markerParams.title ? this._markerParams.title : false;
	this._infoWindow.setHTML('<h3 class="umsMarkerTitle">' + title + '</h3><div class="umsMarkerDesc">' + description + '</div>');
};
/*umsMapboxMarker.prototype.refreshInfoWnd = function() {

};*/
umsMapboxMarker.prototype._updateInfoWndContent = function() {
	this._setInfoWndContent();
};
umsMapboxMarker.prototype.addEventListener = function(event, callback) {
	if(event === 'click') {
		var self = this;
		this._map.addEventListener('click', function(e){
			if(e.originalEvent.originalTarget === self._$markerEl.get(0)) {
				callback();
			}
		});
	} else {
		this._markerObj.on(event, callback);
	}
};
umsMapboxMarker.prototype._openInfoWnd = function() {
	this._setInfoWndContent();
	if(!this._infoWindow.isOpen()) {
		this._markerObj.togglePopup();
	}
};
umsMapboxMarker.prototype._closeInfoWnd = function() {
	this._setInfoWndContent();
	if(this._infoWindow.isOpen()) {
		this._markerObj.togglePopup();
	}
};
umsMapboxMarker.prototype.getIcon = (function() {
	return this._markerObj.getIcon();
});
umsMapboxMarker.prototype.setIcon = function(iconPath) {
	this._$markerEl.css({
		'background-image': 'url("'+ iconPath+ '")'
	});
	if(this._markerParams.icon_data) {
		if(this._markerParams.icon_data.width) {
			this._$markerEl.css('width', this._markerParams.icon_data.width);
		}
		if(this._markerParams.icon_data.height) {
			this._$markerEl.css('height', this._markerParams.icon_data.height);
		}
	}
};

// Markers
function umsBaseMarker(map, params) {
	this._map = map;
	this._markerObj = null;
	var defaults = {
		// Empty for now
	};
	/*if(!params.position && params.coord_x && params.coord_y) {
		params.position = new google.maps.LatLng(params.coord_x, params.coord_y);
	}*/
	this._markerParams = jQuery.extend({}, defaults, params);
	this._markerParams.map = this._map.getRawMapInstance();
	//this._id = params.id ? params.id : 0;
	this._infoWindow = null;
	this._infoWndOpened = false;
	this._infoWndWasInited = false;
	this._infoWndDirectionsBtn = false;
	this._infoWndPrintBtn = false;
	this._mapDragScroll = {
		scrollwheel: null
	};
	this._ignoreBaseInfoWndBind = false;
	this.init();
}
umsBaseMarker.prototype.infoWndOpened = function() {
	return this._infoWndOpened;
};
umsBaseMarker.prototype._createMarkerObj = function() {
	// Marker object creation for inheritance - here
	this._methodRedefineNotice('umsBaseMarker.prototype._createMarkerObj');
};
umsBaseMarker.prototype.init = function() {
	this._markerObj = this._createMarkerObj();
	var openInfoWndEvent = 'click'
	,	closeInfoWndEvent = ''
	,	openLinkEvent = 'click';
	/*var markerParamsForCreate = this._markerParams
	,	openInfoWndEvent = 'click'
	,	closeInfoWndEvent = ''
	,	openLinkEvent = 'click';

	if(parseInt(this._map._mapParams.hide_marker_tooltip)) {
		this._markerParams.marker_title = this._markerParams.title;
		delete markerParamsForCreate.title;
	}
	this._markerObj = new google.maps.Marker( markerParamsForCreate );

	this._markerObj.addListener('domready', jQuery.proxy(function(){
		changeInfoWndBgColor(this._map);
	}, this));*/
	if(this._markerParams.dragend) {
		this.addEventListener('dragend', jQuery.proxy(this._markerParams.dragend, this));
		//this._markerObj.addListener('dragend', jQuery.proxy(this._markerParams.dragend, this));
	}
	if(this._markerParams.click) {
		this.addEventListener('click', jQuery.proxy(this._markerParams.click, this));
	}
	if ( !navigator.userAgent.match(/iPad|iPhone|iPod|Android|BlackBerry|webOS/i) ) {
		if ( parseInt(this._map._mapParams.marker_hover) ) {
			this.addEventListener('mouseover', jQuery.proxy(function() {
            if (typeof(this._markerObj.opacity) == 'undefined' || this._markerObj.opacity == 1) {
				this.showInfoWnd();
				jQuery(document).trigger('umsAfterMarkerClick', this);
            }
			}, this));
		}
	}
	// console.log( this._markerParams );
	if(this._markerParams.params && !(window.ontouchstart === null || navigator.msMaxTouchPoints)) {
		if(parseInt(this._markerParams.params.description_mouse_hover)) {
			openInfoWndEvent = 'mouseover';
			if(parseInt(this._markerParams.params.description_mouse_leave)) {
				closeInfoWndEvent = 'mouseout';
			}
		}
	}
	if(!this._ignoreBaseInfoWndBind) {
		this.addEventListener(openInfoWndEvent, jQuery.proxy(function () {
			if(this._markerParams.params
				&& !parseInt(this._markerParams.params.description_mouse_hover)
				&& parseInt(this._markerParams.params.marker_link)
			) {
				return;
			} else {
				this.showInfoWnd();
			}
			jQuery(document).trigger('umsAfterMarkerClick', this);
		}, this));
	}
	if(closeInfoWndEvent) {
		// Don't exactly know - what to do with this code
		/*this.addEventListener(closeInfoWndEvent, jQuery.proxy(function () {
			var self = this
			,	infoWndDiv = jQuery('.gm-style-iw').parent()
			,	timeout = 300;

			infoWndDiv.on('mouseover', function () {
				// Mouse is on infowindow content
				infoWndDiv.addClass('hovering');
			});
			infoWndDiv.on('mouseleave', function () {
				// Hide infowindow after mouse have left infowindow content
				setTimeout(function() {
					self.hideInfoWnd();
				}, timeout);
			});
			setTimeout(function() {
				// Hide infowindow if mouse is not on infowindow content
				if(!infoWndDiv.hasClass('hovering')) {
					self.hideInfoWnd();
				}
			}, timeout);
		}, this));*/
	}
	if(this._markerParams.params && parseInt(this._markerParams.params.marker_link)) {
		this.addEventListener(openLinkEvent, jQuery.proxy(function () {
			var url_string = window.location.href,
				url = new URL(url_string),
				tab = url.searchParams.get("tab");
			if (tab !== 'maps_edit') {
				var isLink = /http/gi
				,	markerLink = !this._markerParams.params.marker_link_src.match(isLink)
						? 'http://' + this._markerParams.params.marker_link_src
						: this._markerParams.params.marker_link_src;

				if(parseInt(this._markerParams.params.marker_link_new_wnd)) {
					window.open(markerLink,	'_blank');
				} else {
					location.href = markerLink;
				}
			}
		}, this));
	}
};
umsBaseMarker.prototype.addEventListener = function(event, callback) {
	this._methodRedefineNotice('umsBaseMarker.prototype.addEventListener');
};
umsBaseMarker.prototype.showInfoWnd = function( forceUpdateInfoWnd, forceShow, withOpenPopup ) {
	/*var allShapes = this._map.getAllShapes();
	if(allShapes && allShapes.length) {
		for(var i = 0; i < allShapes.length; i++) {
			if(allShapes[i]._infoWndOpened) allShapes[i].hideInfoWnd();
		}
	}*/
	if(!this._infoWndWasInited || forceUpdateInfoWnd) {
		this._updateInfoWndContent(true);
		this._infoWndWasInited = true;
	}
	if(this._infoWindow && !this._infoWndOpened) {
		var allMapMArkers = this._map.getAllMarkers();
		if(allMapMArkers && allMapMArkers.length > 1 && !forceShow) {
			for(var i = 0; i < allMapMArkers.length; i++) {
				allMapMArkers[i].hideInfoWnd();
			}
		}
		this._openInfoWnd( withOpenPopup );
	}
	this._infoWndOpened = true;
	if ( !navigator.userAgent.match(/iPad|iPhone|iPod|Android|BlackBerry|webOS/i) ) {
		if ( parseInt(this._map._mapParams.marker_hover) ) {
			this.directOpenInfoWnd();
		}
	}
};
umsBaseMarker.prototype._openInfoWnd = function( withOpenPopup ) {
	this._methodRedefineNotice('umsBaseMarker.prototype._openInfoWnd');
};
umsBaseMarker.prototype.directOpenInfoWnd = function() {
	this._methodRedefineNotice('umsBaseMarker.prototype.directOpenInfoWnd');
};
umsBaseMarker.prototype._closeInfoWnd = function() {
	this._methodRedefineNotice('umsBaseMarker.prototype._closeInfoWnd');
};
umsBaseMarker.prototype.hideInfoWnd = function() {
	if(this._infoWindow && this._infoWndOpened) {
		this._closeInfoWnd();
		this._infoWndOpened = false;

		//var mapObj = this._map.getRawMapInstance();
		//mapObj.setOptions( {scrollwheel: this._mapDragScroll.scrollwheel} );

		//jQuery(document).trigger('umsAfterHideInfoWnd', this);
	}
};
umsBaseMarker.prototype.getRawMarkerInstance = function() {
	return this._markerObj;
};
umsBaseMarker.prototype.getRawMarkerParams = function() {
	return this._markerParams;
};
umsBaseMarker.prototype.getIcon = (function() {
	//return this._markerObj.getIcon();
	this._methodRedefineNotice('umsBaseMarker.prototype.getIcon');
});
umsBaseMarker.prototype.setIcon = function(iconPath) {
	//this._markerObj.setIcon( iconPath );
	this._methodRedefineNotice('umsBaseMarker.prototype.setIcon');
};
umsBaseMarker.prototype.setIconSize = function(width, height) {
	if(typeof(this._markerParams.icon_data) === 'undefined') {
		this._markerParams.icon_data = {};
	}
	this._markerParams.icon_data.width = width;
	this._markerParams.icon_data.height = height;
};
umsBaseMarker.prototype.setTitle = function(title, noRefresh) {
	this._markerParams.title = title;
	if(!noRefresh)
		this._updateInfoWndContent();
};
umsBaseMarker.prototype.getTitle = function() {
	return this._markerParams.title;
};
umsBaseMarker.prototype.getPosition = function() {
	//return this._markerObj.getPosition();
	this._methodRedefineNotice('umsBaseMarker.prototype.getPosition');
};
umsBaseMarker.prototype.setPosition = function(lat, lng) {
	//this._markerObj.setPosition( new google.maps.LatLng(lat, lng) );
	this._methodRedefineNotice('umsBaseMarker.prototype.setPosition');
};
umsBaseMarker.prototype.lat = function() {
	//return this.getPosition().lat();
	this._methodRedefineNotice('umsBaseMarker.prototype.lat');
};
umsBaseMarker.prototype.lng = function(lng) {
	//return this.getPosition().lng();
	this._methodRedefineNotice('umsBaseMarker.prototype.lng');
};
umsBaseMarker.prototype.setId = function(id) {
	this._markerParams.id = id;
};
umsBaseMarker.prototype.getId = function() {
	return parseInt(this._markerParams.id);
};
umsBaseMarker.prototype.setDescription = function (description, noRefresh) {
	this._markerParams.description = description;
	if(!noRefresh)
		this._updateInfoWndContent();
	if(this._markerParams.params && parseInt(this._markerParams.params.show_description)) {
		this.showInfoWnd(false, true);
	}
};
umsBaseMarker.prototype.getDescription = function () {
	return this._markerParams.description;
};
umsBaseMarker.prototype._setTitleColor = function(titleDiv) {
	var titleColor = this._map.getParam('marker_title_color');

	if(titleColor && titleColor != '') {
		titleDiv.css({
			'color': titleColor
		});
	}
	return titleDiv;
};
umsBaseMarker.prototype._setTitleSize = function(titleDiv) {
	var titleSize = this._map.getParam('marker_title_size')
	,	titleSizeUnits = this._map.getParam('marker_title_size_units');

	if(titleSize && titleSizeUnits && titleSize != '') {
		titleDiv.css({
			'font-size': titleSize + titleSizeUnits
		,	'line-height': (+titleSize + 5) + titleSizeUnits
		});
	}
	return titleDiv;
};
umsBaseMarker.prototype._setDescSize = function(descDiv) {
	var descSize = this._map.getParam('marker_desc_size')
	,	descSizeUnits = this._map.getParam('marker_desc_size_units');

	if(descSize && descSizeUnits && descSize != '') {
		descDiv.css({
			'font-size': descSize + descSizeUnits
		,	'line-height': parseInt(descSize) + 5 + descSizeUnits
		});
	}
	return descDiv;
};
umsBaseMarker.prototype._updateInfoWndContent = function() {
	this._methodRedefineNotice('umsBaseMarker.prototype._updateInfoWndContent');
};
/**
 * Just mark it as closed
 */
umsBaseMarker.prototype._setInfoWndClosed = function() {
	this._infoWndOpened = false;
	jQuery(document).trigger('umsAfterHideInfoWnd', this);
};
umsBaseMarker.prototype._setInfoWndContent = function(withoutShowTrigger) {
	this._methodRedefineNotice('umsBaseMarker.prototype._setInfoWndContent');
};
umsBaseMarker.prototype.removeFromMap = function() {
	this._methodRedefineNotice('umsBaseMarker.prototype.removeFromMap');
};
umsBaseMarker.prototype.setMarkerParams = function(params) {
	this._markerParams = params;
	return this;
};
umsBaseMarker.prototype.setMarkerParam = function(key, value) {
	this._markerParams[ key ] = value;
	return this;
};
umsBaseMarker.prototype.getMarkerParam = function(key) {
	return this._markerParams[ key ];
};
umsBaseMarker.prototype.setMap = function( map ) {
	this._methodRedefineNotice('umsBaseMarker.prototype.setMap');
};
umsBaseMarker.prototype.getMap = function() {
	return this._map;
};
umsBaseMarker.prototype.setVisible = function(state) {
	this.getRawMarkerInstance().setVisible(state);
};
umsBaseMarker.prototype.getVisible = function(state) {
	this.getRawMarkerInstance().getVisible(state);
};
/*umsBaseMarker.prototype.removeFromMap = function() {
	this._methodRedefineNotice('umsBaseMarker.prototype.removeFromMap');
};*/
umsBaseMarker.prototype._methodRedefineNotice = function(methodName) {
	console.log('['+ methodName+ '] should be re-defined!');
};
/*umsBaseMarker.prototype.refreshInfoWnd = function() {
	this._methodRedefineNotice('umsBaseMarker.prototype.setMap');
};*/

// Common functions
function _umsPrepareMarkersList(markers, params) {
	params = params || {};
	if(markers) {
		for(var i = 0; i < markers.length; i++) {
			markers[i].coord_x = parseFloat( markers[i].coord_x );
			markers[i].coord_y = parseFloat( markers[i].coord_y );
			markers[i].icon = markers[i].icon_data.path;
			if(params.dragend) {
				markers[i].draggable = true;
				markers[i].dragend = params.dragend;
			}
		}
	}
	return markers;
}

/*var umsMarkerLoader = {
	init: function(map, markerParams) {
		return new window['ums'+ toeStrFirstUp(UMS_DATA.engine)+ 'Marker'](map, markerParams);
	}
};*/

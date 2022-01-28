/*
 * jQuery.fullscreen library v0.4.0
 * Copyright (c) 2013 Vladimir Zhuravlev
 *
 * @license https://github.com/private-face/jquery.fullscreen/blob/master/LICENSE
 *
 * Date: Wed Dec 11 22:45:17 ICT 2013
 **/
;(function($) {

function defined(a) {
	return typeof a !== 'undefined';
}

function extend(child, parent, prototype) {
    var F = function() {};
    F.prototype = parent.prototype;
    child.prototype = new F();
    child.prototype.constructor = child;
	parent.prototype.constructor = parent;
    child._super = parent.prototype;
    if (prototype) {
        $.extend(child.prototype, prototype);
    }
}

var SUBST = [
    ['', ''],               /* spec*/
    ['exit', 'cancel'],     /* firefox & old webkits expect cancelFullScreen instead of exitFullscreen*/
    ['screen', 'Screen']    /* firefox expects FullScreen instead of Fullscreen*/
];

var VENDOR_PREFIXES = ['', 'o', 'ms', 'moz', 'webkit', 'webkitCurrent'];

function native(obj, name) {
    var prefixed;

    if (typeof obj === 'string') {
        name = obj;
        obj = document;
    }

    for (var i = 0; i < SUBST.length; ++i) {
        name = name.replace(SUBST[i][0], SUBST[i][1]);
        for (var j = 0; j < VENDOR_PREFIXES.length; ++j) {
            prefixed = VENDOR_PREFIXES[j];
            prefixed += j === 0 ? name : name.charAt(0).toUpperCase() + name.substr(1);
            if (defined(obj[prefixed])) {
                return obj[prefixed];
            }
        }
    }

    return void 0;
}var ua = navigator.userAgent;
var fsEnabled = native('fullscreenEnabled');
var IS_ANDROID_CHROME = ua.indexOf('Android') !== -1 && ua.indexOf('Chrome') !== -1; 
var IS_NATIVELY_SUPPORTED = 
		!IS_ANDROID_CHROME &&
		 defined(native('fullscreenElement')) && 
		(!defined(fsEnabled) || fsEnabled === true);

var version = $.fn.jquery.split('.');
var JQ_LT_17 = (parseInt(version[0]) < 2 && parseInt(version[1]) < 7);

var FullScreenAbstract = function() {
	this.__options = null;
	this._fullScreenElement = null;
	this.__savedStyles = {};
};

FullScreenAbstract.prototype = {
	_DEFAULT_OPTIONS: {
		styles: {
			'boxSizing': 'border-box',
			'MozBoxSizing': 'border-box',
			'WebkitBoxSizing': 'border-box'
		},
		toggleClass: null
	},
	__documentOverflow: '',
	__htmlOverflow: '',
	_preventDocumentScroll: function() {
		this.__documentOverflow = $('body')[0].style.overflow;
		this.__htmlOverflow = $('html')[0].style.overflow;
		/* $('body, html').css('overflow', 'hidden');*/
	},
	_allowDocumentScroll: function() {
		/* $('body')[0].style.overflow = this.__documentOverflow;*/
		/* $('html')[0].style.overflow = this.__htmlOverflow; */
	},
	_fullScreenChange: function() {
		if (!this.isFullScreen()) {
			this._allowDocumentScroll();
			this._revertStyles();
			this._triggerEvents();
			this._fullScreenElement = null;
		} else {
			this._preventDocumentScroll();
			this._triggerEvents();
		}
	},
	_fullScreenError: function(e) {
		this._revertStyles();
		this._fullScreenElement = null;
		if (e) {
			$(document).trigger('fscreenerror', [e]);
		}
	},
	_triggerEvents: function() {
		$(this._fullScreenElement).trigger(this.isFullScreen() ? 'fscreenopen' : 'fscreenclose');
		$(document).trigger('fscreenchange', [this.isFullScreen(), this._fullScreenElement]);
	},
	_saveAndApplyStyles: function() {
		var $elem = $(this._fullScreenElement);
		this.__savedStyles = {};
		for (var property in this.__options.styles) {
			/* save */
			this.__savedStyles[property] = this._fullScreenElement.style[property];
			/* apply */
			this._fullScreenElement.style[property] = this.__options.styles[property];
		}
		if (this.__options.toggleClass) {
			$elem.addClass(this.__options.toggleClass);
		}
	},
	_revertStyles: function() {
		var $elem = $(this._fullScreenElement);
		for (var property in this.__options.styles) {
			this._fullScreenElement.style[property] = this.__savedStyles[property];
		}
		if (this.__options.toggleClass) {
			$elem.removeClass(this.__options.toggleClass);
		}
	},
	open: function(elem, options) {
		/* do nothing if request is for already fullscreened element */
		if (elem === this._fullScreenElement) {
			return;
		}
		/* exit active fullscreen before opening another one */
		if (this.isFullScreen()) {
			this.exit();
		}
		/* save fullscreened element */
		this._fullScreenElement = elem;
		/* apply options, if any */
		this.__options = $.extend(true, {}, this._DEFAULT_OPTIONS, options);
		/* save current element styles and apply new */
		this._saveAndApplyStyles();
	},
	exit: null,
	isFullScreen: null,
	isNativelySupported: function() {
		return IS_NATIVELY_SUPPORTED;
	}
};
var FullScreenNative = function() {
	FullScreenNative._super.constructor.apply(this, arguments);
	this.exit = $.proxy(native('exitFullscreen'), document);
	this._DEFAULT_OPTIONS = $.extend(true, {}, this._DEFAULT_OPTIONS, {
		'styles': {
			'width': '100%',
			'height': '100%'
		}
	});
	$(document)
		.bind(this._prefixedString('fullscreenchange') + ' MSFullscreenChange', $.proxy(this._fullScreenChange, this))
		.bind(this._prefixedString('fullscreenerror') + ' MSFullscreenError', $.proxy(this._fullScreenError, this));
};

extend(FullScreenNative, FullScreenAbstract, {
	VENDOR_PREFIXES: ['', 'o', 'moz', 'webkit'],
	_prefixedString: function(str) {
		return $.map(this.VENDOR_PREFIXES, function(s) {
			return s + str;
		}).join(' ');
	},
	open: function(elem, options) {
		FullScreenNative._super.open.apply(this, arguments);
		var requestFS = native(elem, 'requestFullscreen');
		requestFS.call(elem);
	},
	exit: $.noop,
	isFullScreen: function() {
		return native('fullscreenElement') !== null;
	},
	element: function() {
		return native('fullscreenElement');
	}
});
var FullScreenFallback = function() {
	FullScreenFallback._super.constructor.apply(this, arguments);
	this._DEFAULT_OPTIONS = $.extend({}, this._DEFAULT_OPTIONS, {
		'styles': {
			'position': 'fixed',
			'zIndex': '2147483647',
			'left': 0,
			'top': 0,
			'bottom': 0,
			'right': 0
		}
	});
	this.__delegateKeydownHandler();
};

extend(FullScreenFallback, FullScreenAbstract, {
	__isFullScreen: false,
	__delegateKeydownHandler: function() {
		var $doc = $(document);
		$doc.delegate('*', 'keydown.fullscreen', $.proxy(this.__keydownHandler, this));
		var data = JQ_LT_17 ? $doc.data('events') : $._data(document).events;
		var events = data['keydown'];
		if (!JQ_LT_17) {
			events.splice(0, 0, events.splice(events.delegateCount - 1, 1)[0]);
		} else {
			data.live.unshift(data.live.pop());
		}
	},
	__keydownHandler: function(e) {
		if (this.isFullScreen() && e.which === 27) {
			this.exit();
			return false;
		}
		return true;
	},
	_revertStyles: function() {
		FullScreenFallback._super._revertStyles.apply(this, arguments);
		/* force redraw (fixes bug in IE7 with content dissapearing) */
		this._fullScreenElement.offsetHeight;
	},
	open: function(elem) {
		FullScreenFallback._super.open.apply(this, arguments);
		this.__isFullScreen = true;
		this._fullScreenChange();
	},
	exit: function() {
		this.__isFullScreen = false;
		this._fullScreenChange();
	},
	isFullScreen: function() {
		return this.__isFullScreen;
	},
	element: function() {
		return this.__isFullScreen ? this._fullScreenElement : null;
	}
});$.fullscreen = IS_NATIVELY_SUPPORTED 
				? new FullScreenNative() 
				: new FullScreenFallback();

$.fn.fullscreen = function(options) {
	var elem = this[0];

	options = $.extend({
		toggleClass: null,
		/* overflow: 'hidden'*/
	}, options);
	options.styles = {
		/* overflow: options.overflow */
	};
	/* delete options.overflow; */

	if (elem) {
		$.fullscreen.open(elem, options);
	}

	return this;
};
})(jQuery);

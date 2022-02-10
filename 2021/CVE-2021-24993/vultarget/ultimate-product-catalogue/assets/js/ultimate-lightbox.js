/* Simple JavaScript Inheritance
* By John Resig http://ejohn.org/
* MIT Licensed.
*/
// Inspired by base2 and Prototype
(function(){
var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;

// The base Class implementation (does nothing)
this.Class = function(){};

// Create a new Class that inherits from this class
Class.extend = function(prop) {
var _super = this.prototype;

// Instantiate a base class (but only create the instance,
// don’t run the init constructor)
initializing = true;
var prototype = new this();
initializing = false;

// Copy the properties over onto the new prototype
for (var name in prop) {
// Check if we’re overwriting an existing function
prototype[name] = typeof prop[name] == "function" &&
typeof _super[name] == "function" && fnTest.test(prop[name]) ?
(function(name, fn){
return function() {
var tmp = this._super;

// Add a new ._super() method that is the same method
// but on the super-class
this._super = _super[name];

// The method only need to be bound temporarily, so we
// remove it when we’re done executing
var ret = fn.apply(this, arguments);
this._super = tmp;

return ret;
};
})(name, prop[name]) :
prop[name];
}

// The dummy class constructor
function Class() {
// All construction is actually done in the init method
if ( !initializing && this.init )
this.init.apply(this, arguments);
}

// Populate our constructed prototype object
Class.prototype = prototype;

// Enforce the constructor to be what we expect
Class.prototype.constructor = Class;

// And make this class extendable
Class.extend = arguments.callee;

return Class;
};
})();

var lightbox;

var defaults = {

    transition_class: 'ewd-ulb-horizontal-slide',

    speed: 600,
    height: '100%',
    width: '100%',

    closable: true,
    loop: true,
    keyboard_controls: true,

    show_thumbnails: 'bottom',

    hideElements: ['description', 'thumbnails'],

    autoplay: false,
    autoplayInterval: 4000,

    zoomLevel: 0,
    fullsize: false,

	ulb_arrow: 'a',
    ulb_icon_set: 'a',

    controls: {'top_right_controls': ['exit'],
    			'top_left_controls': ['autoplay', 'zoom', 'zoom_out', 'download', 'fullscreen'],
    			'bottom_right_controls': ['slide_counter'],
    			'bottom_left_controls': []}
};

var EWD_ULB_LightboxSlide = Class.extend({
	init: function(element, length) {
		this.element = element; 
		this.source = jQuery(element).data("ulbsource");
		this.title = jQuery(element).data("ulbtitle");
		this.description = jQuery(element).data("ulbdescription");
		this.gallery = jQuery(element).data("ulbGallery");

		if (this.source) {
			var youtube = this.source.match(/\/\/(?:www\.)?youtu(?:\.be|be\.com)\/(?:watch\?v=|embed\/)?([a-z0-9\-\_\%]+)/i);
			if (youtube) {
				this.video = "youtube";
				this.source = this.source.replace("watch?v=", "v/");
			}
			else {
				this.video = false;
			}
		}
		else {
			this.video = false;
		}

		if (jQuery(element).data("ulbheight")) {this.height = jQuery(element).data("ulbheight");}
		else {this.height = 315;}
		if (jQuery(element).data("ulbwidth")) {this.width = jQuery(element).data("ulbwidth");}
		else {this.width = 560;}
	}
});

var UltimateLightbox = Class.extend({
	init: function(options) {
		this.settings = jQuery.extend({}, defaults, options);

		this.displaying = false;

		var elements = [];
		jQuery('.ewd-ulb-lightbox, .ewd-ulb-lightbox-noclick-image').each(function(index, value) {
			var Slide = new EWD_ULB_LightboxSlide(this, elements.length);
			jQuery(this).data('slideIndex', elements.length);
			elements.push(Slide);
		});
		this.elements = elements;

		this.currentSlide = 0;

		this.maxSlide = this.elements.length - 1;

		this.setMobileClasses();
	},

	setMobileClasses: function() {
		if (jQuery.inArray('description', this.settings.hideElements) !== -1) {this.settings.descriptionClass = 'ewd-ulb-mobile-hide';}
		else {this.settings.descriptionClass = '';}
		if (jQuery.inArray('title', this.settings.hideElements) !== -1) {this.settings.titleClass = 'ewd-ulb-mobile-hide';}
		else {this.settings.titleClass = '';}
		if (jQuery.inArray('thumbnails', this.settings.hideElements) !== -1) {this.settings.thumbnailsClass = 'ewd-ulb-mobile-hide';}
		else {this.settings.thumbnailsClass = '';}

		if (this.settings.descriptionClass == 'ewd-ulb-mobile-hide' && this.settings.titleClass == 'ewd-ulb-mobile-hide') {this.settings.overlayClass = 'ewd-ulb-mobile-hide';}
		else {this.settings.overlayClass = '';}
	},

	setCurrentSlide: function(targetElement) {
		jQuery(this.elements).each(function(index, element) {
			if (element.source == jQuery(targetElement).data('ulbsource')) {lightbox.currentSlide = index;}
		})
	},	

	toggle: function() {
		if (this.displaying) {this.closeLightbox();}
		else {this.openLightbox();}
	},

	closeLightbox: function() {
		this.displaying = false;
		jQuery('.ewd-ulb-background').css('display', 'none');
		jQuery('.ewd-ulb-lightbox-container').css('display', 'none');
		this.switchSlide();
		jQuery('.ewd-ulb-active-slide').removeClass('ewd-ulb-active-slide');
		jQuery('.ewd-ulb-active-thumbnail').removeClass('ewd-ulb-active-thumbnail');
	},

	openLightbox: function () {
		this.displaying = true;
		jQuery('.ewd-ulb-background').css('display', 'inline');
		jQuery('.ewd-ulb-lightbox-container').css('display', 'inline');
		if (this.settings.autoplay) {this.startAutoplay();}
		this.switchSlide();
		this.selectThumbnails();
		lightbox.noZoom();
	},

	nextSlide: function() {
		var oldSlide = this.currentSlide;
		do {
			if (this.currentSlide == this.maxSlide) {this.currentSlide = 0;}
			else {this.currentSlide = this.currentSlide + 1;}
		}
		while (this.elements[oldSlide].gallery != this.elements[this.currentSlide].gallery);
		jQuery('.ewd-ulb-slide').addClass('ewd-ulb-transition-next');
		this.switchSlide(oldSlide);
	},

	previousSlide: function() {
		var oldSlide = this.currentSlide;
		do {
			if (this.currentSlide == 0) {this.currentSlide = this.maxSlide;}
			else {this.currentSlide = this.currentSlide - 1;}
		}
		while (this.elements[oldSlide].gallery != this.elements[this.currentSlide].gallery);
		jQuery('.ewd-ulb-slide').addClass('ewd-ulb-transition-previous');
		this.switchSlide(oldSlide);
	},

	goToSlide: function(slideIndex) {
		var oldSlide = this.currentSlide;
		this.currentSlide = slideIndex;
		jQuery('.ewd-ulb-slide').addClass('ewd-ulb-transition-next');
		this.switchSlide(oldSlide);
	},

	switchSlide: function(oldSlide) {
		if (typeof oldSlide !== "undefined") {
			jQuery(".ewd-ulb-slide-thumbnail[data-slideindex='" + oldSlide + "']").removeClass('ewd-ulb-active-thumbnail');
			jQuery(".ewd-ulb-slide[data-slideindex='" + oldSlide + "']").removeClass('ewd-ulb-image-displaying')
			jQuery(".ewd-ulb-slide[data-slideindex='" + oldSlide + "']").addClass('ewd-ulb-old-active-slide');
			setTimeout(function(){
				jQuery(".ewd-ulb-slide[data-slideindex='" + oldSlide + "']").removeClass('ewd-ulb-active-slide ewd-ulb-old-active-slide');
			}, 500);
		}
		jQuery(".ewd-ulb-slide-thumbnail[data-slideindex='" + this.currentSlide + "']").addClass('ewd-ulb-active-thumbnail');
		jQuery(".ewd-ulb-slide[data-slideindex='" + this.currentSlide + "']").addClass('ewd-ulb-active-slide ewd-ulb-image-displaying');
		var slideNumber = this.currentSlide + 1;
		jQuery(".ewd-ulb-current-count-indicator").each(function() {jQuery(this).html(slideNumber);});

    	setTimeout(function(){
			  jQuery('.ewd-ulb-slide').removeClass('ewd-ulb-transition-next ewd-ulb-transition-previous');
    	}, 500);

		lightbox.resizeOverlay();
		lightbox.setDownloadLinks();
	},

	selectThumbnails: function() {
		var gallery = this.getCurrentGallery();

		jQuery('.ewd-ulb-slide-thumbnail').addClass('ewd-ulb-thumbnail-hidden');
		jQuery('.ewd-ulb-slide-thumbnail[data-ulbGallery="' + gallery + '"]').removeClass('ewd-ulb-thumbnail-hidden');
	},

	getCurrentGallery: function() {
		var gallery = "";
		var currentSlide = this.currentSlide;
		jQuery(this.elements).each(function(index, element) {
			if (index == currentSlide) {gallery = element.gallery;}
		});

		return gallery;
	},

	resizeOverlay: function() {
		var imgWidth = jQuery(".ewd-ulb-active-slide.ewd-ulb-image-displaying img").first().width(); console.log("Image width: " + imgWidth);
		var containerWidth = jQuery(".ewd-ulb-active-slide.ewd-ulb-image-displaying").first().width(); console.log("Container width: " + containerWidth);
		var marginWidth = (containerWidth - imgWidth) / 2;
		jQuery(".ewd-ulb-slide-overlay").css('width', jQuery(".ewd-ulb-active-slide.ewd-ulb-image-displaying img").first().width());
		jQuery(".ewd-ulb-slide-overlay").css('margin', '0px ' + marginWidth + 'px');
	},

	toggleAutoplay: function() {
		if (this.settings.autoplay) {this.stopAutoplay();}
		else {this.startAutoplay();}
	},

	startAutoplay: function() {
		if (this.interval) {clearInterval(this.interval);}
		this.settings.autoplay = true;
		this.interval = setInterval(function() {
			lightbox.nextSlide();
		}, this.settings.autoplayInterval);
	},

	stopAutoplay: function() {
		this.settings.autoplay = false;
		clearInterval(this.interval);
	},

	ZoomOut: function() {
		if (this.settings.zoomLevel == 2) {this.zoomOne();}
		else if (this.settings.zoomLevel == 1) {this.noZoom();}

		this.removeFullSize();
	},

	toggleZoom: function() {
		if (this.settings.zoomLevel == 0) {this.zoomOne();}
		else if (this.settings.zoomLevel == 1) {this.zoomTwo();}
		else {this.noZoom();}

		this.removeFullSize();
	},

	zoomOne: function() {
		this.settings.zoomLevel = 1;
		jQuery('.ewd-ulb-slide').addClass('ewd-ulb-zoom-one');
		jQuery('.ewd-ulb-slide').removeClass('ewd-ulb-zoom-two');
	},

	zoomTwo: function() {
		this.settings.zoomLevel = 2;
		jQuery('.ewd-ulb-slide').removeClass('ewd-ulb-zoom-one');
		jQuery('.ewd-ulb-slide').addClass('ewd-ulb-zoom-two');
	},

	noZoom: function() {
		this.settings.zoomLevel = 0;
		jQuery('.ewd-ulb-slide').removeClass('ewd-ulb-zoom-one');
		jQuery('.ewd-ulb-slide').removeClass('ewd-ulb-zoom-two');
	},

	fullscreen: function() {
		if (!document.fullscreenElement && !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {
	    	jQuery('ewd-ulb-fullscreen').addClass('ewd-ulb-regular_screen');
	    	if (document.documentElement.requestFullscreen) {
	    		document.documentElement.requestFullscreen();
	    	} else if (document.documentElement.msRequestFullscreen) {
	    		document.documentElement.msRequestFullscreen();
	    	} else if (document.documentElement.mozRequestFullScreen) {
	      		document.documentElement.mozRequestFullScreen();
	    	} else if (document.documentElement.webkitRequestFullscreen) {
	      		document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
	    	}
	  	} else {
	    	jQuery('ewd-ulb-fullscreen').removeClass('ewd-ulb-regular_screen');
	    	if (document.exitFullscreen) {
	      		document.exitFullscreen();
	    	} else if (document.msExitFullscreen) {
	      		document.msExitFullscreen();
	    	} else if (document.mozCancelFullScreen) {
	      		document.mozCancelFullScreen();
	    	} else if (document.webkitExitFullscreen) {
	      		document.webkitExitFullscreen();
	    	}
	  	}
	},

	toggleFullSize: function() {
		this.noZoom();
		if (this.settings.fullsize) {this.removeFullSize();}
		else {this.goFullSize();}
	},

	goFullSize: function() {
		jQuery('.ewd-ulb-slide').addClass('ewd-ulb-fullsize-image');
		this.settings.fullsize = true;
	},

	removeFullSize: function() {
		jQuery('.ewd-ulb-slide').removeClass('ewd-ulb-fullsize-image');
		this.settings.fullsize = false;
	},

	setDownloadLinks: function() {
		jQuery('.ewd-ulb-download-link').attr('href', jQuery('.ewd-ulb-active-slide div img').attr('src'));
	},
});

jQuery(document).ready(function() {
	if (typeof ewd_ulb_php_data == "undefined") {ewd_ulb_php_data = [];}
	lightbox = new UltimateLightbox(ewd_ulb_php_data);

	jQuery('.ewd-ulb-lightbox, .ewd-ulb-open-lightbox').on('click', function(event) {
		if (jQuery(event.currentTarget).hasClass('ewd-ulb-lightbox')) {lightbox.currentSlide = jQuery(event.currentTarget).data('slideIndex');}
		else {lightbox.setCurrentSlide(event.currentTarget);}
		lightbox.toggle();
		event.preventDefault();
	});

	if (jQuery('.ewd-ulb-lightbox, .ewd-ulb-lightbox-noclick-image').length) {
		EWD_ULB_Add_Lightbox_HTML(lightbox);
		EWD_ULB_Add_Lightbox_Custom_CSS();

		jQuery('.ewd-ulb-slide-container').on('click.background', function(event) {
			if (event.toElement.nodeName == "IMG" || ewd_ulb_php_data.background_close == "false") {return;}
			lightbox.toggle();
		});

		jQuery('.ewd-ulb-slide-control-next').on('click.next', function() {
			lightbox.noZoom();
			lightbox.stopAutoplay();
			lightbox.nextSlide();
		});
		jQuery('.ewd-ulb-slide-control-previous').on('click.prev', function() {
			lightbox.noZoom();
			lightbox.stopAutoplay();
			lightbox.previousSlide();
		});

		jQuery('.ewd-ulb-slide-thumbnail').on('click.thumbnail', function() {
			var slideIndex = jQuery(this).data("slideindex");
			lightbox.noZoom();
			lightbox.stopAutoplay();
			lightbox.goToSlide(slideIndex);
		});

		jQuery(window).on('resize', function() {
			lightbox.resizeOverlay()
		});

		jQuery('.ewd-ulb-exit').on('click.exit', function() {
			lightbox.toggle();
		});
		jQuery('.ewd-ulb-autoplay').on('click.autoplay', function() {
			lightbox.toggleAutoplay();
		});
		jQuery('.ewd-ulb-zoom').on('click.zoom', function() {
			lightbox.toggleZoom();
		});
		jQuery('.ewd-ulb-zoom_out').on('click.zoom_out', function() {
			lightbox.ZoomOut();
		});
		jQuery('.ewd-ulb-fullsize').on('click.fullsize', function() {
			lightbox.toggleFullSize();
		});
		jQuery('.ewd-ulb-fullscreen').on('click.zoom_out', function() {
			lightbox.fullscreen();
		});
		jQuery('.ewd-ulb-download').wrap('<a class="ewd-ulb-download-link" href="empty.png" download></a>');

		if (lightbox.settings.keyboard_controls) {
			jQuery(document).on('keyup', function(e) {
				if (e.which == 27) {lightbox.closeLightbox();}
				if (e.which == 37) {
					lightbox.noZoom();
					lightbox.stopAutoplay();
					lightbox.previousSlide();
				}
				if (e.which == 39) {
					lightbox.noZoom();
					lightbox.stopAutoplay();
					lightbox.nextSlide();
				}
			});
		}
	}
});

function EWD_ULB_Add_Lightbox_HTML(lightbox) {
	var Top_Toolbar_HTML = '<div class="ewd-ulb-top-toolbar">';
	Top_Toolbar_HTML += '<div class="ewd-ulb-left-top-toolbar">' + EWD_ULB_Add_Controls(lightbox.settings.controls.top_left_controls, lightbox.maxSlide) + '</div>';
	Top_Toolbar_HTML += '<div class="ewd-ulb-right-top-toolbar">' + EWD_ULB_Add_Controls(lightbox.settings.controls.top_right_controls, lightbox.maxSlide) + '</div>';
	Top_Toolbar_HTML += '</div>';

	var Top_Thumbnail_Bar_HTML = '<div class="ewd-ulb-top-thumbnail-bar ' + lightbox.settings.thumbnailsClass + '">';
	Top_Thumbnail_Bar_HTML += '<div class="ewd-thumbnail-scroll-button ewd-thumbnail-scroll-button-left">a</div>';
 	Top_Thumbnail_Bar_HTML += '<div class="ewd-thumbnail-scroll-button ewd-thumbnail-scroll-button-right">b</div>';
	Top_Thumbnail_Bar_HTML += '<div class="ewd-ulb-top-thumbnails">';
	if (lightbox.settings.show_thumbnails == "top") {Top_Thumbnail_Bar_HTML += EWD_ULB_Thumbnails_HTML(lightbox.elements);}
	Top_Thumbnail_Bar_HTML += '</div>';
	Top_Thumbnail_Bar_HTML += '</div>';

	var Slide_Area_HTML = '<div class="ewd-ulb-slide-area">';
	Slide_Area_HTML += '<div class="ewd-ulb-slide-control ewd-ulb-slide-control-previous ewd-ulb-arrow">' + lightbox.settings.ulb_arrow + '</div>';
	Slide_Area_HTML += '<div class="ewd-ulb-slide-container">';
	jQuery(lightbox.elements).each(function(index, value){
		Slide_Area_HTML += '<div class="ewd-ulb-slide ' + lightbox.settings.transition_class + '" data-slideindex="' + index + '">';
		Slide_Area_HTML += '<div class="ewd-ulb-slide-img">';
		if (this.video == "youtube") {
			Slide_Area_HTML += '<iframe width="' + this.width + '" height="' + this.height + '" src="' + this.source + '" frameborder="0" allowfullscreen></iframe>'
		}
		else {
			Slide_Area_HTML += '<img src="' + this.source + '" />';
		}
		Slide_Area_HTML += '</div>';
		if ((this.title != undefined && this.title != "") || (this.description != undefined && this.description != "")) {
			Slide_Area_HTML += '<div class="ewd-ulb-slide-overlay ' + lightbox.settings.overlayClass + '">';
			if (this.title != undefined && this.title != "") {Slide_Area_HTML += '<div class="ewd-ulb-slide-title ' + lightbox.settings.titleClass + '">' + this.title + '</div>';}
			if (this.description != undefined && this.description != "") {Slide_Area_HTML += '<div class="ewd-ulb-slide-description ' + lightbox.settings.descriptionClass + '">' + this.description + '</div>';}
			Slide_Area_HTML += '</div>';
		}
		Slide_Area_HTML += '</div>';
	});
	Slide_Area_HTML += '</div>';
	Slide_Area_HTML += '<div class="ewd-ulb-slide-control ewd-ulb-slide-control-next ewd-ulb-arrow">' + String.fromCharCode(lightbox.settings.ulb_arrow.charCodeAt(lightbox.settings.ulb_arrow.length-1)+1) + '</div>';
	Slide_Area_HTML += '</div>';

	var Botton_Thumbnail_Bar_HTML = '<div class="ewd-ulb-bottom-thumbnail-bar ' + lightbox.settings.thumbnailsClass + '">';
 	Botton_Thumbnail_Bar_HTML += '<div class="ewd-thumbnail-scroll-button ewd-thumbnail-scroll-button-left">a</div>';
 	Botton_Thumbnail_Bar_HTML += '<div class="ewd-thumbnail-scroll-button ewd-thumbnail-scroll-button-right">b</div>';
	Botton_Thumbnail_Bar_HTML += '<div class="ewd-ulb-bottom-thumbnails"><div class="ewd-ulb-thumbnails-inside">';
	if (lightbox.settings.show_thumbnails == "bottom") {Botton_Thumbnail_Bar_HTML += EWD_ULB_Thumbnails_HTML(lightbox.elements);}
	Botton_Thumbnail_Bar_HTML += '</div></div>';
	Botton_Thumbnail_Bar_HTML += '</div>';

	var Bottom_Toolbar_HTML = '<div class="ewd-ulb-bottom-toolbar">';
	Bottom_Toolbar_HTML += '<div class="ewd-ulb-left-bottom-toolbar">' + EWD_ULB_Add_Controls(lightbox.settings.controls.bottom_left_controls, lightbox.maxSlide) + '</div>';
	Bottom_Toolbar_HTML += '<div class="ewd-ulb-right-bottom-toolbar">' + EWD_ULB_Add_Controls(lightbox.settings.controls.bottom_right_controls, lightbox.maxSlide) + '</div>';
	Bottom_Toolbar_HTML += '</div>';

	var HTML = '<div class="ewd-ulb-background" style="display:none;width:' + lightbox.settings.width + ';height:' + lightbox.settings.height + '"></div>';
	HTML += '<div class="ewd-ulb-lightbox-container ewd-ulb-thumbnails-' + lightbox.settings.show_thumbnails + '" style="display:none;">';
	HTML += Top_Toolbar_HTML;
	HTML += Top_Thumbnail_Bar_HTML;
	HTML += Slide_Area_HTML;
	HTML += Botton_Thumbnail_Bar_HTML;
	HTML += Bottom_Toolbar_HTML;
	HTML += "</div>";

	jQuery('body').append(HTML);
}

function EWD_ULB_Thumbnails_HTML(slides) {
	var Slide_HTML = '';
	jQuery(slides).each(function(index, value) {
		Slide_HTML += '<div class="ewd-ulb-slide-thumbnail" data-slideindex="' + index + '" data-ulbGallery="' + this.gallery + '">';
		if (this.video == "youtube") {Slide_HTML += '<img src="http://img.youtube.com/vi/' + /[^/]*$/.exec(this.source)[0] + '/default.jpg" />';}
		else {Slide_HTML += '<img src="' + this.source + '" />';}
		Slide_HTML += '</div>';
	});

	return Slide_HTML;
}

function EWD_ULB_Add_Controls(controls, maxSlide) {
	var Controls_HTML = '';
	jQuery(controls).each(function() {
		if (this == 'slide_counter') {Controls_HTML += '<div class="ewd-ulb-control ewd-ulb-slide-counter"><span class="ewd-ulb-current-count-indicator">1</span>/<span class="ewd-ulb-max-count-indicator">' +  (maxSlide + 1) + '</span></div>';}
		else {Controls_HTML += '<div class="ewd-ulb-control ewd-ulb-' + this + '">' + lightbox.settings.ulb_icon_set + '</div>';}
	});

	return Controls_HTML;
}

function EWD_ULB_Add_Lightbox_Custom_CSS() {
	var data = '&action=ulb_get_custom_css';
    jQuery.post(ajaxurl, data, function(response) {
    	jQuery('body').append(response);
    });
}


/****************************************
THUMBNAIL SCROLLING
****************************************/
jQuery(document).ready(function($){
  $('.ewd-ulb-bottom-thumbnail-bar').each(function(){
    var thisThumbBar = $(this);
    var numberOfThumbs = thisThumbBar.find('.ewd-ulb-slide-thumbnail').length;
    var widthOfThumbsInside = numberOfThumbs * 144;
    thisThumbBar.find('.ewd-ulb-thumbnails-inside').css('width', widthOfThumbsInside+'px');
    thisThumbBar.find('.ewd-thumbnail-scroll-button-right').click(function(){
      var widthOfThumbs = thisThumbBar.find('.ewd-ulb-bottom-thumbnails').width();
      if(widthOfThumbs >= widthOfThumbsInside){
        var rightClickStop = 0;
      }
      else{
        var rightClickStop = widthOfThumbs - 576;
      }
      var thumbsInsideLeft = thisThumbBar.find('.ewd-ulb-thumbnails-inside').position().left;
      if(thumbsInsideLeft != rightClickStop){
        thisThumbBar.find('.ewd-ulb-thumbnails-inside').css('left', '-=144');
      }
    });
    thisThumbBar.find('.ewd-thumbnail-scroll-button-left').click(function(){
      var leftClickStop = 0;
      var thumbsInsideLeft = thisThumbBar.find('.ewd-ulb-thumbnails-inside').position().left;
      if(thumbsInsideLeft != leftClickStop){
        thisThumbBar.find('.ewd-ulb-thumbnails-inside').css('left', '+=144');
      }
    });
  });
  $('.ewd-ulb-top-thumbnail-bar').each(function(){
    var thisThumbBar = $(this);
    var numberOfThumbs = thisThumbBar.find('.ewd-ulb-slide-thumbnail').length;
    var widthOfThumbsInside = numberOfThumbs * 144;
    thisThumbBar.find('.ewd-ulb-thumbnails-inside').css('width', widthOfThumbsInside+'px');
    thisThumbBar.find('.ewd-thumbnail-scroll-button-right').click(function(){
      var widthOfThumbs = thisThumbBar.find('.ewd-ulb-top-thumbnails').width();
      if(widthOfThumbs >= widthOfThumbsInside){
        var rightClickStop = 0;
      }
      else{
        var rightClickStop = widthOfThumbs - 576;
      }
      var thumbsInsideLeft = thisThumbBar.find('.ewd-ulb-thumbnails-inside').position().left;
      if(thumbsInsideLeft != rightClickStop){
        thisThumbBar.find('.ewd-ulb-thumbnails-inside').css('left', '-=144');
      }
    });
    thisThumbBar.find('.ewd-thumbnail-scroll-button-left').click(function(){
      var leftClickStop = 0;
      var thumbsInsideLeft = thisThumbBar.find('.ewd-ulb-thumbnails-inside').position().left;
      if(thumbsInsideLeft != leftClickStop){
        thisThumbBar.find('.ewd-ulb-thumbnails-inside').css('left', '+=144');
      }
    });
  });
});

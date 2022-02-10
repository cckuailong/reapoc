(function ($) {
    "use strict";
    
    var getElementSettings = function( $element ) {
		var elementSettings = {},
			modelCID 		= $element.data( 'model-cid' );

		if ( isEditMode && modelCID ) {
			var settings 		= elementorFrontend.config.elements.data[ modelCID ],
				settingsKeys 	= elementorFrontend.config.elements.keys[ settings.attributes.widgetType || settings.attributes.elType ];

			jQuery.each( settings.getActiveControls(), function( controlKey ) {
				if ( -1 !== settingsKeys.indexOf( controlKey ) ) {
					elementSettings[ controlKey ] = settings.attributes[ controlKey ];
				}
			} );
		} else {
			elementSettings = $element.data('settings') || {};
		}

		return elementSettings;
	};

    var isEditMode		= false;
    
    var ppSwiperSliderinit = function (carousel, carouselWrap, elementSettings, sliderOptions) {
		if ( 'undefined' === typeof Swiper ) {
			var asyncSwiper = elementorFrontend.utils.swiper;

			new asyncSwiper( carousel, sliderOptions ).then( function( newSwiperInstance ) {
				var mySwiper = newSwiperInstance;
				ppSwiperSliderAfterinit( carousel, carouselWrap, elementSettings, mySwiper );
			} );
		} else {
			var mySwiper = new Swiper(carousel, sliderOptions);
			ppSwiperSliderAfterinit( carousel, carouselWrap, elementSettings, mySwiper );
		}
    };

	var ppSwiperSliderAfterinit = function (carousel, carouselWrap, elementSettings, mySwiper) {
		if ( 'yes' === elementSettings.pause_on_hover ) {
			carousel.on( 'mouseover', function() {
				mySwiper.autoplay.stop();
			});

			carousel.on( 'mouseout', function() {
				mySwiper.autoplay.start();
			});
		}

		if ( isEditMode ) {
			carouselWrap.resize( function() {
				mySwiper.update();
			});
		}

		ppWidgetUpdate( mySwiper, '.pp-swiper-slider', 'swiper' );
    };
	
    var ppSwiperSliderHandler = function ($scope, $) {
		var elementSettings = getElementSettings( $scope ),
			carouselWrap    = $scope.find('.swiper-container-wrap'),
            carousel        = $scope.find('.pp-swiper-slider'),
            sliderOptions   = JSON.parse( carousel.attr('data-slider-settings') );

		ppSwiperSliderinit(carousel, carouselWrap, elementSettings, sliderOptions);
	};
    
    var ppWidgetUpdate = function (slider, selector, type) {
		if( 'undefined' === typeof type ){
			type = 'swiper';
		}

		var $triggers = [
			'ppe-tabs-switched',
			'ppe-toggle-switched',
			'ppe-accordion-switched',
			'ppe-popup-opened',
		];

		$triggers.forEach(function(trigger) {
			if ( 'undefined' !== typeof trigger ) {
				$(document).on(trigger, function(e, wrap) {
					if ( trigger == 'ppe-popup-opened' ) {
						wrap = $('.pp-modal-popup-' + wrap);
					}
					if ( wrap.find( selector ).length > 0 ) {
						setTimeout(function() {
							if ( 'slick' === type ) {
								slider.slick( 'setPosition' );
							} else if ( 'swiper' === type ) {
								slider.update();
							} else if ( 'gallery' === type ) {
								var $gallery = wrap.find('.pp-image-gallery').eq(0);
								$gallery.isotope( 'layout' );
							}
						}, 100);
					}
				});
			}
		});
	};
    
    var ImageHotspotHandler = function ($scope, $) {
		var id              = $scope.data('id'),
			elementSettings = getElementSettings( $scope ),
			ppclass         = 'pp-tooltip' + ' pp-tooltip-' + id,
        	ttArrow         = elementSettings.tooltip_arrow,
        	ttAlwaysOpen    = elementSettings.tooltip_always_open,
			ttTrigger       = elementSettings.tooltip_trigger,
			ttDistance      = elementSettings.distance.size,
			animation       = elementSettings.tooltip_animation,
			minWidth        = elementSettings.tooltip_width.size,
			tooltipSize     = elementSettings.tooltip_size,
			tooltipZindex   = elementSettings.tooltip_zindex;

		if ( '' !== tooltipSize && undefined !== tooltipSize ) {
			ppclass += ' pp-tooltip-size-' + tooltipSize;
		}

		$('.pp-hot-spot-wrap[data-tooltip]').each(function () {
			var ttPosition   = $(this).data('tooltip-position');

			$( this ).pptooltipster({
				trigger : ttTrigger,
				animation : animation,
	        	minWidth: minWidth,
				ppclass : ppclass,
				side : ttPosition,
	        	arrow : ( 'yes' === ttArrow ),
	        	distance : ttDistance,
	        	interactive : true,
	        	positionTracker : true,
	        	zIndex : tooltipZindex,
			});

			if ( ttAlwaysOpen === 'yes' ) {
				$(this).pptooltipster();
				$(this).pptooltipster('show');
			}
		});
    };
    
    var ImageComparisonHandler = function ($scope, $) {
        var image_comparison_elem       = $scope.find('.pp-image-comparison').eq(0),
            settings                    = image_comparison_elem.data('settings');
        
        image_comparison_elem.twentytwenty({
            default_offset_pct:         settings.visible_ratio,
            orientation:                settings.orientation,
            before_label:               settings.before_label,
            after_label:                settings.after_label,
            move_slider_on_hover:       settings.slider_on_hover,
            move_with_handle_only:      settings.slider_with_handle,
            click_to_move:              settings.slider_with_click,
            no_overlay:                 settings.no_overlay
        });
    };
    
    var CounterHandler = function ($scope, $) {
        var counterElem   = $scope.find('.pp-counter').eq(0),
            target        = counterElem.data('target'),
            separator     = $scope.find('.pp-counter-number').data('separator'),
			separatorChar = $scope.find('.pp-counter-number').data('separator-char'),
			format        = ( separatorChar !== '' ) ? '(' + separatorChar + 'ddd).dd' : '(,ddd).dd';

		var counter = function () {
			$(target).each(function () {
				var to     = $(this).data('to'),
					speed  = $(this).data('speed'),
					od     = new Odometer({
						el:       this,
						value:    0,
						duration: speed,
						format:   (separator === 'yes') ? format : ''
					});
				od.render();
				setInterval(function () {
					od.update(to);
				});
			})
		}

		if ( 'undefined' !== typeof elementorFrontend.waypoint ) {
			elementorFrontend.waypoint(
				counterElem,
				counter,
				{ offset: '80%', triggerOnce: true }
			);
		}
	};
	
	var IbEqualHeight = function($scope, $) {
		var maxHeight = 0;
		$scope.find('.swiper-slide').each( function() {
			if($(this).height() > maxHeight){
				maxHeight = $(this).height();
			}
		});
		$scope.find('.pp-info-box-content-wrap').css('min-height',maxHeight);
	};
    
    var InfoBoxCarouselHandler = function ($scope, $) {
		var elementSettings = getElementSettings( $scope ),
			carouselWrap    = $scope.find('.swiper-container-wrap'),
            carousel        = $scope.find('.pp-info-box-carousel'),
            sliderOptions   = JSON.parse( carousel.attr('data-slider-settings') ),
            equalHeight	    = elementSettings.equal_height_boxes;

		ppSwiperSliderinit(carousel, carouselWrap, elementSettings, sliderOptions);
		
		if ( equalHeight === 'yes' ) {
			IbEqualHeight($scope, $);
			$(window).resize(IbEqualHeight($scope, $));
		}
    };
    
    var InstaFeedPopupHandler = function ($scope, $) {
        var widgetId		= $scope.data('id'),
			elementSettings = getElementSettings( $scope ),
            layout          = elementSettings.feed_layout;

		if ( layout === 'carousel' ) {
			var carouselWrap  = $scope.find('.swiper-container-wrap'),
				carousel      = $scope.find('.swiper-container').eq(0),
				sliderOptions = JSON.parse( carousel.attr('data-slider-settings') );

			ppSwiperSliderinit(carousel, carouselWrap, elementSettings, sliderOptions);
		} else if (layout === 'masonry') {
			var grid = $('#pp-instafeed-' + widgetId).imagesLoaded( function() {
				grid.masonry({
					itemSelector: '.pp-feed-item',
					percentPosition: true
				});
			});
		}
    };
    
    var ImageScrollHandler = function($scope, $) {
        var scrollElement    = $scope.find(".pp-image-scroll-container"),
            scrollOverlay    = scrollElement.find(".pp-image-scroll-overlay"),
            scrollVertical   = scrollElement.find(".pp-image-scroll-vertical"),
			elementSettings  = getElementSettings( $scope ),
            imageScroll      = scrollElement.find('.pp-image-scroll-image img'),
            direction        = elementSettings.direction_type,
            reverse			 = elementSettings.reverse,
            trigger			 = elementSettings.trigger_type,
            transformOffset  = null;
        
        function startTransform() {
            imageScroll.css("transform", (direction == "vertical" ? "translateY" : "translateX") + "( -" +  transformOffset + "px)");
        }
        
        function endTransform() {
            imageScroll.css("transform", (direction == 'vertical' ? "translateY" : "translateX") + "(0px)");
        }
        
        function setTransform() {
            if( direction == "vertical" ) {
                transformOffset = imageScroll.height() - scrollElement.height();
            } else {
                transformOffset = imageScroll.width() - scrollElement.width();
            }
        }
        
        if( trigger == "scroll" ) {
            scrollElement.addClass("pp-container-scroll");
            if ( direction == "vertical" ) {
                scrollVertical.addClass("pp-image-scroll-ver");
            } else {
                scrollElement.imagesLoaded(function() {
                  scrollOverlay.css( { "width": imageScroll.width(), "height": imageScroll.height() } );
                });
            }
        } else {
            if ( reverse === 'yes' ) {
                scrollElement.imagesLoaded(function() {
                    scrollElement.addClass("pp-container-scroll-instant");
                    setTransform();
                    startTransform();
                });
            }
            if ( direction == "vertical" ) {
                scrollVertical.removeClass("pp-image-scroll-ver");
            }
            scrollElement.mouseenter(function() {
                scrollElement.removeClass("pp-container-scroll-instant");
                setTransform();
                reverse === 'yes' ? endTransform() : startTransform();
            });

            scrollElement.mouseleave(function() {
                reverse === 'yes' ? startTransform() : endTransform();
            });
        }
    };
    
    var AdvancedAccordionHandler = function ($scope, $) {
    	var accordionTitle  = $scope.find('.pp-accordion-tab-title'),
            elementSettings = getElementSettings( $scope ),
        	accordionType   = elementSettings.accordion_type,
        	accordionSpeed  = elementSettings.toggle_speed;
	
        // Open default actived tab
        accordionTitle.each(function(){
            if ( $(this).hasClass('pp-accordion-tab-active-default') ) {
                $(this).addClass('pp-accordion-tab-show pp-accordion-tab-active');
                $(this).next().slideDown(accordionSpeed);
            }
        });

        // Remove multiple click event for nested accordion
        accordionTitle.unbind('click');

        accordionTitle.click(function(e) {
            e.preventDefault();

            var $this = $(this),
				$item = $this.parent();
			
			$(document).trigger('ppe-accordion-switched', [ $item ]);

            if ( accordionType === 'accordion' ) {
                if ( $this.hasClass('pp-accordion-tab-show') ) {
                    $this.closest('.pp-accordion-item').removeClass('pp-accordion-item-active');
                    $this.removeClass('pp-accordion-tab-show pp-accordion-tab-active');
                    $this.next().slideUp(accordionSpeed);
                } else {
                    $this.closest('.pp-advanced-accordion').find('.pp-accordion-item').removeClass('pp-accordion-item-active');
                    $this.closest('.pp-advanced-accordion').find('.pp-accordion-tab-title').removeClass('pp-accordion-tab-show pp-accordion-tab-active');
                    $this.closest('.pp-advanced-accordion').find('.pp-accordion-tab-title').removeClass('pp-accordion-tab-active-default');
                    $this.closest('.pp-advanced-accordion').find('.pp-accordion-tab-content').slideUp(accordionSpeed);
                    $this.toggleClass('pp-accordion-tab-show pp-accordion-tab-active');
                    $this.closest('.pp-accordion-item').toggleClass('pp-accordion-item-active');
                    $this.next().slideToggle(accordionSpeed);
                }
            } else {
                // For acccordion type 'toggle'
                if ( $this.hasClass('pp-accordion-tab-show') ) {
                    $this.removeClass('pp-accordion-tab-show pp-accordion-tab-active');
                    $this.next().slideUp(accordionSpeed);
                } else {
                    $this.addClass('pp-accordion-tab-show pp-accordion-tab-active');
                    $this.next().slideDown(accordionSpeed);
                }
			}
        });
    };

	var PPButtonHandler = function ( $scope ) {
		var id = $scope.data('id'),
			ppclass = 'pp-tooltip' + ' pp-tooltip-' + id,
			ttipPosition = $scope.find('.pp-button[data-tooltip]').data('tooltip-position'),
			elementorBreakpoints = elementorFrontend.config.breakpoints;

		// tablet
		if ( window.innerWidth <= elementorBreakpoints.lg && window.innerWidth >= elementorBreakpoints.md ) {
			ttipPosition = $scope.find('.pp-button[data-tooltip]').data('tooltip-position-tablet');
		}
		// mobile
		if ( window.innerWidth < elementorBreakpoints.md ) {
			ttipPosition = $scope.find('.pp-button[data-tooltip]').data('tooltip-position-mobile');
		}

		$scope.find('.pp-button[data-tooltip]').pptooltipster({
			trigger : 'hover',
			animation : 'fade',
			ppclass : ppclass,
			side : ttipPosition,
			interactive : true,
			positionTracker : true,
		});
	};

	var TwitterTimelineHandler = function ($scope, $) {
		$(document).ready(function () {
			if ('undefined' !== twttr) {
				twttr.widgets.load();
			}
		});
	};
    
    var ImageAccordionHandler = function ($scope, $) {
		var $image_accordion            = $scope.find('.pp-image-accordion').eq(0),
            elementSettings             = getElementSettings( $scope ),
            $action                     = elementSettings.accordion_action,
		    $id                         = $image_accordion.attr( 'id' ),
		    $item                       = $('#'+ $id +' .pp-image-accordion-item');
		   
		if ( 'on-hover' === $action ) {
            $item.hover(
                function ImageAccordionHover() {
                    $item.css('flex', '1');
                    $item.removeClass('pp-image-accordion-active');
                    $(this).addClass('pp-image-accordion-active');
                    $item.find('.pp-image-accordion-content-wrap').removeClass('pp-image-accordion-content-active');
                    $(this).find('.pp-image-accordion-content-wrap').addClass('pp-image-accordion-content-active');
                    $(this).css('flex', '3');
                },
                function() {
                    $item.css('flex', '1');
                    $item.find('.pp-image-accordion-content-wrap').removeClass('pp-image-accordion-content-active');
                    $item.removeClass('pp-image-accordion-active');
                }
            );
        }
		else if ( 'on-click' === $action ) {
            $item.click( function(e) {
                e.stopPropagation(); // when you click the button, it stops the page from seeing it as clicking the body too
                $item.css('flex', '1');
				$item.removeClass('pp-image-accordion-active');
                $(this).addClass('pp-image-accordion-active');
				$item.find('.pp-image-accordion-content-wrap').removeClass('pp-image-accordion-content-active');
				$(this).find('.pp-image-accordion-content-wrap').addClass('pp-image-accordion-content-active');
                $(this).css('flex', '3');
            });

            $('#'+ $id).click( function(e) {
                e.stopPropagation(); // when you click within the content area, it stops the page from seeing it as clicking the body too
            });

            $('body').click( function() {
                $item.css('flex', '1');
				$item.find('.pp-image-accordion-content-wrap').removeClass('pp-image-accordion-content-active');
				$item.removeClass('pp-image-accordion-active');
            });
		}
    };

	var GFormsHandler = function( $scope, $ ) {
		if ( 'undefined' == typeof $scope )
			return;

		$scope.find('select:not([multiple])').each(function() {
			var	gf_select_field = $( this );
			if( gf_select_field.next().hasClass('chosen-container') ) {
				gf_select_field.next().wrap( "<span class='pp-gf-select-custom'></span>" );
			} else {
				gf_select_field.wrap( "<span class='pp-gf-select-custom'></span>" );
			}
		});
	};

	var PricingTableHandler = function( $scope, $ ) {
		var id                   = $scope.data('id'),
			toolTopElm           = $scope.find('.pp-pricing-table-tooptip[data-tooltip]'),
			elementSettings      = getElementSettings( $scope ),
			ppclass              = 'pp-tooltip' + ' pp-tooltip-' + id,
        	ttArrow              = elementSettings.tooltip_arrow,
			ttTrigger            = elementSettings.tooltip_trigger,
			animation            = elementSettings.tooltip_animation,
			tooltipSize          = elementSettings.tooltip_size,
			tooltipZindex        = elementSettings.tooltip_zindex,
			elementorBreakpoints = elementorFrontend.config.breakpoints;

		if ( '' !== tooltipSize && undefined !== tooltipSize ) {
			ppclass += ' pp-tooltip-size-' + tooltipSize;
		}

		toolTopElm.each(function () {
            var ttPosition   = $(this).data('tooltip-position'),
				minWidth     = $(this).data('tooltip-width'),
				ttDistance   = $(this).data('tooltip-distance');

            // tablet
            if ( window.innerWidth <= elementorBreakpoints.lg && window.innerWidth >= elementorBreakpoints.md ) {
                ttPosition = $scope.find('.pp-pricing-table-tooptip[data-tooltip]').data('tooltip-position-tablet');
            }

            // mobile
            if ( window.innerWidth < elementorBreakpoints.md ) {
                ttPosition = $scope.find('.pp-pricing-table-tooptip[data-tooltip]').data('tooltip-position-mobile');
            }

			$( this ).pptooltipster({
				trigger : ttTrigger,
				animation : animation,
	        	minWidth: minWidth,
				ppclass : ppclass,
				side : ttPosition,
	        	arrow : ( 'yes' === ttArrow ),
	        	distance : ttDistance,
	        	interactive : true,
	        	positionTracker : true,
	        	zIndex : tooltipZindex,
			});
        });
	};
    
	var ContentReveal = function ($scope, $) {
		var elementSettings 	= getElementSettings($scope),
			contentWrapper      = $scope.find('.pp-content-reveal-content-wrapper'),
			$content 			= $scope.find('.pp-content-reveal-content'),
			$saparator 			= $scope.find('.pp-content-reveal-saparator'),
			$button				= $scope.find('.pp-content-reveal-button-inner'),
			contentOuterHeight 	= $content.outerHeight(),
			contentVisibility   = contentWrapper.data('visibility'),
			contentHeightCustom = contentWrapper.data('content-height'),
			speedUnreveal       = contentWrapper.data('speed') * 1000,
			contentHeightLines  = contentWrapper.data('lines'),
			contentLineHeight   = $scope.find('.pp-content-reveal-content p').css('line-height'),
			contentPaddingTop 	= $content.css('padding-top');

        if ( contentVisibility == 'lines' ) {
            if ( contentHeightLines == '0' ) {
                var contentWrapperHeight = contentWrapper.outerHeight();
            } else {
                var contentWrapperHeight = (parseInt(contentLineHeight, 10) * contentHeightLines) + parseInt(contentPaddingTop, 10);
                contentWrapper.css( 'height', (contentWrapperHeight + 'px') );
            }
        } else {
            contentWrapper.css( 'height', (contentHeightCustom + 'px') );
            contentWrapperHeight = contentHeightCustom;
        }

		$button.on('click', function () {
			$saparator.slideToggle(speedUnreveal);
			$(this).toggleClass('pp-content-revealed');
			$(this).find('.pp-content-reveal-button-open').slideToggle(speedUnreveal);
			$(this).find('.pp-content-reveal-button-closed').slideToggle(speedUnreveal);
			if ( $button.hasClass('pp-content-revealed') ) {
				contentWrapper.animate({ height: ( contentOuterHeight + 'px') }, speedUnreveal);
			} else {
				contentWrapper.animate({ height: ( contentWrapperHeight + 'px') }, speedUnreveal);
			}
		});
    };

	var WrapperLinkHandler = function( $scope ) {
		if ( $scope.data( 'pp-wrapper-link' ) ) {
			var wrapperLink = $scope.data('pp-wrapper-link'),
				id          = $scope.data('id'),
				url         = wrapperLink.url,
				isExternal  = wrapperLink.is_external ? '_blank' : '_self',
				rel         = wrapperLink.nofollow ? 'nofollow' : '',
				anchorTag   = document.createElement('a');

			$scope.on('click.onPPWrapperLink', function() {
				anchorTag.id            = 'pp-wrapper-link-' + id;
				anchorTag.href          = url;
				anchorTag.target        = isExternal;
				anchorTag.rel           = rel;
				anchorTag.style.display = 'none';

				document.body.appendChild(anchorTag);

				var anchorObj = document.getElementById(anchorTag.id);
				anchorObj.click();

				var timeout = setTimeout(function() {
					document.body.removeChild(anchorObj);
					clearTimeout(timeout);
				});
			});
		}
	};
    
    $(window).on('elementor/frontend/init', function () {
        if ( elementorFrontend.isEditMode() ) {
			isEditMode = true;
		}
        
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-image-hotspots.default', ImageHotspotHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-image-comparison.default', ImageComparisonHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-counter.default', CounterHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-logo-carousel.default', ppSwiperSliderHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-info-box-carousel.default', InfoBoxCarouselHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-instafeed.default', InstaFeedPopupHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-team-member-carousel.default', ppSwiperSliderHandler);
        elementorFrontend.hooks.addAction('frontend/element_ready/pp-scroll-image.default', ImageScrollHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-advanced-accordion.default', AdvancedAccordionHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-content-ticker.default', ppSwiperSliderHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-buttons.default', PPButtonHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-twitter-timeline.default', TwitterTimelineHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-twitter-tweet.default', TwitterTimelineHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-image-accordion.default', ImageAccordionHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-gravity-forms.default', GFormsHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-pricing-table.default', PricingTableHandler);
		elementorFrontend.hooks.addAction('frontend/element_ready/pp-content-reveal.default', ContentReveal);

		elementorFrontend.hooks.addAction( 'frontend/element_ready/global', WrapperLinkHandler );
		
		if (isEditMode) {
			parent.document.addEventListener("mousedown", function(e) {
				var widgets = parent.document.querySelectorAll(".elementor-element--promotion");

				if (widgets.length > 0) {
					for (var i = 0; i < widgets.length; i++) {
						if (widgets[i].contains(e.target)) {
							var dialog = parent.document.querySelector("#elementor-element--promotion__dialog");
							var icon = widgets[i].querySelector(".icon > i");

							if (icon.classList.toString().indexOf("ppicon") >= 0) {
								dialog.querySelector(".dialog-buttons-action").style.display = "none";

								if (dialog.querySelector(".pp-dialog-buttons-action") === null) {
									var button = document.createElement("a");
									var buttonText = document.createTextNode("Upgrade to PowerPack Pro");

									button.setAttribute("href", "https://powerpackelements.com/upgrade/?utm_medium=pp-elements-lite&utm_source=pp-editor-icons&utm_campaign=pp-pro-upgrade");
									button.setAttribute("target", "_blank");
									button.classList.add(
										"dialog-button",
										"dialog-action",
										"dialog-buttons-action",
										"elementor-button",
										"elementor-button-success",
										"pp-dialog-buttons-action"
									);
									button.appendChild(buttonText);

									dialog.querySelector(".dialog-buttons-action").insertAdjacentHTML("afterend", button.outerHTML);
								} else {
									dialog.querySelector(".pp-dialog-buttons-action").style.display = "";
								}
							} else {
								dialog.querySelector(".dialog-buttons-action").style.display = "";

								if (dialog.querySelector(".pp-dialog-buttons-action") !== null) {
									dialog.querySelector(".pp-dialog-buttons-action").style.display = "none";
								}
							}

							// stop loop
							break;
						}
					}
				}
			});
		}
    });
    
}(jQuery));
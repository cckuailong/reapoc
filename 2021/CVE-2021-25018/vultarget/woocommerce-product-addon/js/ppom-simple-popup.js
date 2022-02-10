/*
 * jQuery Simple Popup Window Plugin 1.0
 */

'use strict';

(function($) {

	// Tooltip Init
	var tooltip_position = ppom_tooltip_vars.ppom_tooltip_position;
	var tooltip_trigger = ppom_tooltip_vars.ppom_tooltip_trigger;
	var tooltip_animation = ppom_tooltip_vars.ppom_tooltip_animation;
	var tooltip_maxwidth = ppom_tooltip_vars.ppom_tooltip_maxwidth;
	var tooltip_borderclr = ppom_tooltip_vars.ppom_tooltip_borderclr;
	var tooltip_bgclr = ppom_tooltip_vars.ppom_tooltip_bgclr;
	var tooltip_txtclr = ppom_tooltip_vars.ppom_tooltip_txtclr;
	var tooltip_interactive = (ppom_tooltip_vars.ppom_tooltip_interactive == 'yes') ? true : false;

	const tooltip_options = {
		contentAsHTML: true,
		animation: tooltip_animation,
		theme: 'ppom_tooltipster-punk',
		interactive: tooltip_interactive,
		trigger: 'custom',
		position: tooltip_position,
		maxWidth: tooltip_maxwidth,
		tooltipBorderColor: tooltip_borderclr,
		tooltipBGColor: tooltip_bgclr,
		tooltipContentColor: tooltip_txtclr
	};

	if (tooltip_trigger != 'yes') {

		tooltip_options.triggerClose = {
			mouseleave: true,
			originClick: true,
			tap: true,
		};

		tooltip_options.triggerOpen = {
			mouseenter: true,
			tap: true,
		};
	}
	else {

		tooltip_options.triggerClose = {
			click: true,
			tap: true,
		};

		tooltip_options.triggerOpen = {
			click: true,
			tap: true,
		};
	}

	$('[data-ppom-tooltip~=ppom_tooltip]').ppom_tooltipster(tooltip_options);

	// Plugin name and prefix 
	var pluginName = 'megapopup';
	var prefix = 'ppom-popup';

	$(document).on('click', '[data-model-id]', function(e) {
		e.preventDefault();
		var popup_id = $(this).attr('data-model-id');

		$('#' + popup_id).megapopup($(this).data());
	});

	// Init Plugin
	$.fn[pluginName] = function(options) {


		var defaults = {
			backgroundclickevent: true,
			popupcloseclass: prefix + '-close-js',
			bodycontroller: prefix + '-open'
		};

		//Extend popup options
		var options = $.extend({}, defaults, options);

		return this.each(function() {

			// Global Variables
			var modal = $(this),
				modalBG = $('.' + prefix + '-bg-controler');

			// Popup background show
			if (modalBG.length == 0) {
				modalBG = $('<div class="' + prefix + '-bg-controler" />').appendTo('body');
			}

			// open popup
			modal.bind(prefix + ':open', function() {

				$('body').addClass(options.bodycontroller);
				modal.css({ 'display': 'block', });
				modalBG.fadeIn();
				modal.animate({
					"top": '0px',
					"opacity": 1
				}, 0);

			});

			// close popup
			modal.bind(prefix + ':close', function() {

				$('body').removeClass(options.bodycontroller);
				modalBG.fadeOut();
				modal.animate({
					"top": '0px',
					"opacity": 0
				}, 0, function() {
					modal.css({ 'display': 'none' });
				});
			});

			//Open Modal Immediately
			modal.trigger(prefix + ':open');

			// close popup listner
			var closeButton = $('.' + options.popupcloseclass).bind('click.modalEvent', function(e) {
				modal.trigger(prefix + ':close');
				e.preventDefault();
			});

			// disable backgroundclickevent close
			if (options.backgroundclickevent) {
				modalBG.css({ "cursor": "pointer" })
				modalBG.bind('click.modalEvent', function() {
					modal.trigger(prefix + ':close')
				});
			}

		});
	}

})(jQuery);

(function(window, $, undefined){
	'use strict';

	var plugin = {},
		$pluginSelect = $('#pluginSelect'),
		getQueryVariable = function(variable) {
			var query = window.location.search.substring(1),
				vars = query.split('&'),
				pair, i;

			for (i = vars.length; i--; ) {
				pair = vars[i].split('=');
				if(pair[0] === variable){
					return pair[1].replace(/^\s*|\s*$/, '');
				}
			}
			return(false);
		},
		type = getQueryVariable('type');

	// plugin for mobile use (bigger and value/color display on top)
	plugin.mobile = {
		customBG: '#222',
		margin: '4px -2px 0',
		doRender: 'div div',
		preventFocus: true,
		animationSpeed: 0,

		// demo on how to make plugins... mobile support plugin
		buildCallback: function($elm) {
			$elm.prepend('<div class="cp-disp"></div>');
			$('.color').on('click', function(e) {
				e.preventDefault && e.preventDefault();
			});
		},
		cssAddon: // could also be in a css file instead
			'.cp-disp{padding:10px; margin-bottom:6px; font-size:19px; height:20px; line-height:20px}' +
			'.cp-xy-slider{width:200px; height:200px;}' +
			'.cp-xy-cursor{width:16px; height:16px; border-width:2px; margin:-8px}' +
			'.cp-z-slider{height:200px; width:40px;}' +
			'.cp-z-cursor{border-width:8px; margin-top:-8px;}' +
			'.cp-alpha{height:40px;}' +
			'.cp-alpha-cursor{border-width: 8px; margin-left:-8px;}',

		renderCallback: function($elm, toggled) {
			var colors = this.color.colors,
				rgb = colors.RND.rgb;

			$('.cp-disp').css({
				backgroundColor: '#' + colors.HEX,
				color: colors.RGBLuminance > 0.22 ? '#222' : '#ddd'
			}).text('rgba(' + rgb.r + ', ' + rgb.g + ', ' + rgb.b +
				', ' + (Math.round(colors.alpha * 100) / 100) + ')');
		}
	};

	// shows input fields for rgb and hsv; changeable
	plugin.input = {
		customBG: '#222',
		margin: '4px -2px 0',
		doRender: 'div div',

		buildCallback: function($elm) {
			var colorInstance = this.color,
				colorPicker = this;

			$elm.prepend('<div class="cp-panel">' +
				'R <input type="text" class="cp-r" /><br>' +
				'G <input type="text" class="cp-g" /><br>' +
				'B <input type="text" class="cp-b" /><hr>' +
				'H <input type="text" class="cp-h" /><br>' +
				'S <input type="text" class="cp-s" /><br>' +
				'B <input type="text" class="cp-v" /><hr>' +
				'<input type="text" class="cp-HEX" />' +
			'</div>').on('change', 'input', function(e) {
				var value = this.value,
					className = this.className,
					type = className.split('-')[1],
					color = {};

				color[type] = value;
				colorInstance.setColor(type === 'HEX' ? value : color,
					type === 'HEX' ? 'HEX' : /(?:r|g|b)/.test(type) ? 'rgb' : 'hsv');
				colorPicker.render();
				this.blur();
			});
		},

		cssAddon: // could also be in a css file instead
			'.cp-color-picker{box-sizing:border-box; width:226px;}' +
			'.cp-color-picker .cp-panel {line-height: 21px; float:right;' +
				'padding:0 1px 0 8px; margin-top:-1px; overflow:visible}' +
			'.cp-xy-slider:active {cursor:none;}' +
			'.cp-panel, .cp-panel input {color:#bbb; font-family:monospace,' +
				'"Courier New",Courier,mono; font-size:12px; font-weight:bold;}' +
			'.cp-panel input {width:28px; height:12px; padding:2px 3px 1px;' +
				'text-align:right; line-height:12px; background:transparent;' +
				'border:1px solid; border-color:#222 #666 #666 #222;}' +
			'.cp-panel hr {margin:0 -2px 2px; height:1px; border:0;' +
				'background:#666; border-top:1px solid #222;}' +
			'.cp-panel .cp-HEX {width:44px; position:absolute; margin:1px -3px 0 -2px;}' +
			'.cp-alpha {width:155px;}',

		renderCallback: function($elm, toggled) {
			var colors = this.color.colors.RND,
				modes = {
					r: colors.rgb.r, g: colors.rgb.g, b: colors.rgb.b,
					h: colors.hsv.h, s: colors.hsv.s, v: colors.hsv.v,
					HEX: this.color.colors.HEX
				};

			$('input', '.cp-panel').each(function() {
				this.value = modes[this.className.substr(3)];
			});
		}
	};

	// shows memory fields
	plugin.memory = {
		customBG: '#222',
		margin: '4px -2px 0',
		doRender: 'div div',
		opacity: false,

		buildCallback: function($elm) {
			var colorInstance = this.color,
				colorPicker = this,
				random = function(n) {
					return Math.round(Math.random() * (n || 255));
				};

			$elm.append('<div class="cp-memory">' +
				'<div></div><div></div><div></div><div></div>' +
				'<div></div><div></div><div></div><div class="cp-store">S</div>').
			on('click', '.cp-memory div', function(e) {
				var $this = $(this);

				if (this.className) {
					$this.parent().prepend($this.prev()).children().eq(0).
						css('background-color', '#' + colorInstance.colors.HEX);
				} else {
					colorInstance.setColor($this.css('background-color'));
					colorPicker.render();
				}
			}).find('.cp-memory div').each(function() {
				!this.className && $(this).css({background:
					'rgb(' + random() + ', ' + random() + ', ' + random() + ')'
				});
			});
		},

		cssAddon: // could also be in a css file instead
			'.cp-memory {margin-bottom:6px; clear:both;}' +
			'.cp-xy-slider:active {cursor:none;}' +
			'.cp-memory div {float:left; width:17px; height:17px; margin-right:2px;' +
				'background:rgba(0,0,0,1); text-align:center; line-height:17px;}' +
			'.cp-memory .cp-store {width:21px; margin:0; background:none; font-weight:bold;' +
				'box-sizing:border-box; border: 1px solid; border-color: #666 #222 #222 #666;}'
	};

	// the default; no extra stuff
	plugin.desktop = {
		customBG: '#222',
		margin: '4px -2px 0',
		doRender: 'div div',
		cssAddon:'.cp-xy-slider:active {cursor:none;}'
	};

	// the default without alpha chanel though
	plugin.noalpha = {
		customBG: '#222',
		margin: '4px -2px 0',
		doRender: 'div div',
		cssAddon:'.cp-xy-slider:active {cursor:none;}',
		opacity: false
	};

	// the default but smaller
	plugin.small = {
		customBG: '#222',
		margin: '4px -2px 0',
		doRender: 'div div',
		cssAddon: // could also be in a css file instead
			'.cp-color-picker{padding:5px 5px 0;}' +
			'.cp-xy-slider{width:100px; height:100px; margin-bottom:3px;}' +
			'.cp-z-slider{height:100px; margin-left:4px;}' +
			'.cp-alpha{margin:4px 0;}' +
			'.cp-xy-slider:active {cursor:none;}'
	};

	// better IE support
	plugin.IE8 = {
		customBG: '#222',
		margin: '4px -2px 0',
		doRender: 'div div',
		// this demo adds divs with gradiends,... you could simply load an image as well
		buildCallback: function($elm) {
			var that = this;

			$elm.find('.cp-z-slider').prepend(
				'<div class="cp-r-rb"></div>' +
				'<div class="cp-rb-b"></div>' +
				'<div class="cp-b-bg"></div>' +
				'<div class="cp-bg-g"></div>' +
				'<div class="cp-g-gr"></div>' +
				'<div class="cp-gr-r"></div>'
			);
		},
		cssAddon: // could also be in a css file instead
			'.cp-r-rb{filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#ff0000\', endColorstr=\'#ff00ff\',GradientType=0 ); height:21px; width: 100%;}' +
			'.cp-rb-b{filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#FF00FF\', endColorstr=\'#0000FF\',GradientType=0 ); height:21px; width: 100%;}' +
			'.cp-b-bg{filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#0000FF\', endColorstr=\'#00FFFF\',GradientType=0 ); height:22px; width: 100%;}' +
			'.cp-bg-g{filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#00FFFF\', endColorstr=\'#00FF00\',GradientType=0 ); height:21px; width: 100%;}' +
			'.cp-g-gr{filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#00FF00\', endColorstr=\'#FFFF00\',GradientType=0 ); height:21px; width: 100%;}' +
			'.cp-gr-r{filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#FFFF00\', endColorstr=\'#FF0000\',GradientType=0 ); height:22px; width: 100%;}' +
			'.cp-z-cursor{top:0;height:0;}' +
			'.cp-xy-slider:active {cursor:none;}',

		renderCallback: function($elm, toggled) {
			// to avoid selection
			window.event && (window.event.returnValue = false);
		}
	};


	// the default skinned and white
	plugin.skinned = {
		customBG: '#222',
		margin: '5px -2px 0',
		doRender: 'div div',
		cssAddon: // could also be in a css file instead
			'.cp-color-picker{border:1px solid #999; padding:10px 10px 0;' +
				'background:#eee; overflow:visible; border-radius:3px;}' +
			'.cp-color-picker:after{content:""; display:block; ' +
				'position:absolute; top:-15px; left:12px; border:8px solid #eee;' +
				'border-color: transparent transparent #eee}' +
			// simulate border...
			'.cp-color-picker:before{content:""; display:block; ' +
				'position:absolute; top:-16px; left:12px; border:8px solid #eee;' +
				'border-color: transparent transparent #999}' +
			'.cp-xy-slider:active {cursor:none;}' +
			'.cp-xy-slider{border:1px solid #999; margin-bottom:10px;}' +
			'.cp-xy-cursor{width:12px; height:12px; margin:-6px}' +
			'.cp-z-slider{margin-left:10px; border:1px solid #999;}' +
			'.cp-z-cursor{border-width:5px; margin-top:-5px;}' +
			'.cp-color-picker .cp-alpha{margin:10px 0 0; height:6px; border-radius:6px;' +
				'overflow:visible; border:1px solid #999; box-sizing:border-box;' +
				'background: linear-gradient(to right, rgba(238,238,238,1) 0%,rgba(238,238,238,0) 100%);}' +
			'.cp-color-picker .cp-alpha{margin:10px 0}' +
			'.cp-alpha-cursor{background: #eee; border-radius: 100%;' +
				'width:14px; height:14px; margin:-5px -7px; border:1px solid #666!important;' +
				'box-shadow:inset -2px -4px 3px #ccc}'
	};

	// the default skinned and white
	plugin.skinned_sa = $.extend({}, plugin.skinned, {
		renderCallback: function($elm, toggled) {
			if (typeof toggled === 'boolean') {
				$('.cp-alpha', this.$UI).css('display', $elm.hasClass('no-alpha') ? 'none' : '')
			}
		}});

	// skinned similar to dev tools color picker (but then way smaller and faster...)
	plugin.dev_skinned = {
		customBG: '#222',
		margin: '5px 0 0',
		doRender: 'div div',
		colorNames: {
			'191970': 'midnightblue',
			'696969': 'dimgrey',
			'708090': 'slategrey',
			'778899': 'lightslategrey',
			'800000': 'maroon',
			'800080': 'purple',
			'808000': 'olive',
			'808080': 'grey',
			'F0F8FF': 'aliceblue',
			'FAEBD7': 'antiquewhite',
			'00FFFF': 'cyan',
			'7FFFD4': 'aquamarine',
			'F0FFFF': 'azure',
			'F5F5DC': 'beige',
			'FFE4C4': 'bisque',
			'000000': 'black',
			'FFEBCD': 'blanchedalmond',
			'0000FF': 'blue',
			'8A2BE2': 'blueviolet',
			'A52A2A': 'brown',
			'DEB887': 'burlywood',
			'5F9EA0': 'cadetblue',
			'7FFF00': 'chartreuse',
			'D2691E': 'chocolate',
			'FF7F50': 'coral',
			'6495ED': 'cornflowerblue',
			'FFF8DC': 'cornsilk',
			'DC143C': 'crimson',
			'00008B': 'darkblue',
			'008B8B': 'darkcyan',
			'B8860B': 'darkgoldenrod',
			'A9A9A9': 'darkgrey',
			'006400': 'darkgreen',
			'BDB76B': 'darkkhaki',
			'8B008B': 'darkmagenta',
			'556B2F': 'darkolivegreen',
			'FF8C00': 'darkorange',
			'9932CC': 'darkorchid',
			'8B0000': 'darkred',
			'E9967A': 'darksalmon',
			'8FBC8F': 'darkseagreen',
			'483D8B': 'darkslateblue',
			'2F4F4F': 'darkslategrey',
			'00CED1': 'darkturquoise',
			'9400D3': 'darkviolet',
			'FF1493': 'deeppink',
			'00BFFF': 'deepskyblue',
			'1E90FF': 'dodgerblue',
			'B22222': 'firebrick',
			'FFFAF0': 'floralwhite',
			'228B22': 'forestgreen',
			'FF00FF': 'magenta',
			'DCDCDC': 'gainsboro',
			'F8F8FF': 'ghostwhite',
			'FFD700': 'gold',
			'DAA520': 'goldenrod',
			'008000': 'green',
			'ADFF2F': 'greenyellow',
			'F0FFF0': 'honeydew',
			'FF69B4': 'hotpink',
			'CD5C5C': 'indianred',
			'4B0082': 'indigo',
			'FFFFF0': 'ivory',
			'F0E68C': 'khaki',
			'E6E6FA': 'lavender',
			'FFF0F5': 'lavenderblush',
			'7CFC00': 'lawngreen',
			'FFFACD': 'lemonchiffon',
			'ADD8E6': 'lightblue',
			'F08080': 'lightcoral',
			'E0FFFF': 'lightcyan',
			'FAFAD2': 'lightgoldenrodyellow',
			'D3D3D3': 'lightgrey',
			'90EE90': 'lightgreen',
			'FFB6C1': 'lightpink',
			'FFA07A': 'lightsalmon',
			'20B2AA': 'lightseagreen',
			'87CEFA': 'lightskyblue',
			'B0C4DE': 'lightsteelblue',
			'FFFFE0': 'lightyellow',
			'00FF00': 'lime',
			'32CD32': 'limegreen',
			'FAF0E6': 'linen',
			'66CDAA': 'mediumaquamarine',
			'0000CD': 'mediumblue',
			'BA55D3': 'mediumorchid',
			'9370DB': 'mediumpurple',
			'3CB371': 'mediumseagreen',
			'7B68EE': 'mediumslateblue',
			'00FA9A': 'mediumspringgreen',
			'48D1CC': 'mediumturquoise',
			'C71585': 'mediumvioletred',
			'F5FFFA': 'mintcream',
			'FFE4E1': 'mistyrose',
			'FFE4B5': 'moccasin',
			'FFDEAD': 'navajowhite',
			'000080': 'navy',
			'FDF5E6': 'oldlace',
			'6B8E23': 'olivedrab',
			'FFA500': 'orange',
			'FF4500': 'orangered',
			'DA70D6': 'orchid',
			'EEE8AA': 'palegoldenrod',
			'98FB98': 'palegreen',
			'AFEEEE': 'paleturquoise',
			'DB7093': 'palevioletred',
			'FFEFD5': 'papayawhip',
			'FFDAB9': 'peachpuff',
			'CD853F': 'peru',
			'FFC0CB': 'pink',
			'DDA0DD': 'plum',
			'B0E0E6': 'powderblue',
			'FF0000': 'red',
			'BC8F8F': 'rosybrown',
			'4169E1': 'royalblue',
			'8B4513': 'saddlebrown',
			'FA8072': 'salmon',
			'F4A460': 'sandybrown',
			'2E8B57': 'seagreen',
			'FFF5EE': 'seashell',
			'A0522D': 'sienna',
			'C0C0C0': 'silver',
			'87CEEB': 'skyblue',
			'6A5ACD': 'slateblue',
			'FFFAFA': 'snow',
			'00FF7F': 'springgreen',
			'4682B4': 'steelblue',
			'D2B48C': 'tan',
			'008080': 'teal',
			'D8BFD8': 'thistle',
			'FF6347': 'tomato',
			'40E0D0': 'turquoise',
			'EE82EE': 'violet',
			'F5DEB3': 'wheat',
			'FFFFFF': 'white',
			'F5F5F5': 'whitesmoke',
			'FFFF00': 'yellow',
			'9ACD32': 'yellowgreen'
		},

		buildCallback: function($elm) {
			var that = this;

			$elm.append('<div class="cp-patch"><div></div></div><div class="cp-disp"></div>');
			$('.trigger').parent().on('click', '.trigger', function(e) {
				if (e.target === this && $(this).hasClass('active')) {
					e.cancelBubble = true;
					e.stopPropagation && e.stopPropagation();
					that.toggle();
				}
			});
			// if input type="color"
			$('.color').on('click', function(e){
				e.preventDefault && e.preventDefault();
			});
		},

		cssAddon: // could also be in a css file instead
			'.cp-patch{float:left; margin:9px 0 0;' +
				'height:24px; width: 24px; border:1px solid #aaa;}' +
			'.cp-patch{background-image: url(\'data:image/gif;base64,R0lGODlhDAAMAIABAMzMzP///yH5BAEAAAEALAAAAAAMAAwAAAIWhB+ph5ps3IMyQFBvzVRq3zmfGC5QAQA7\');}' +
			'.cp-patch div{height:24px; width: 24px;}' +
			'.cp-disp{padding:4px 0 4px 4px; margin-top:10px; font-size:12px;' +
				'height:16px; line-height:16px; color:#333;}' +
			'.cp-color-picker{border:1px solid #999; padding:8px; box-shadow:5px 5px 16px rgba(0,0,0,0.4);' +
				'background:#eee; overflow:visible; border-radius:3px;}' +
			'.cp-color-picker:after{content:""; display:block; ' +
				'position:absolute; top:-8px; left:8px; border:8px solid #eee; border-width: 0px 8px 8px;' +
				'border-color: transparent transparent #eee}' +
			// simulate border...
			'.cp-color-picker:before{content:""; display:block; ' +
				'position:absolute; top:-9px; left:8px; border:8px solid #eee; border-width: 0px 8px 8px;' +
				'border-color: transparent transparent #999}' +
			'.cp-xy-slider{border:1px solid #aaa; margin-bottom:10px; width:150px; height:150px;}' +
			'.cp-xy-slider:active {cursor:none;}' +
			'.cp-xy-cursor{width:12px; height:12px; margin:-6px}' +
			'.cp-z-slider{margin-left:8px; border:1px solid #aaa; height:150px; width:24px;}' +
			'.cp-z-cursor{border-width:5px; margin-top:-5px;}' +
			'.cp-color-picker .cp-alpha{width:152px; margin:10px 0 0; height:6px; border-radius:6px;' +
				'overflow:visible; border:1px solid #aaa; box-sizing:border-box;' +
				'background: linear-gradient(to right, rgba(238,238,238,1) 0%,rgba(238,238,238,0) 100%);}' +
			'.cp-alpha-cursor{background: #eee; border-radius: 100%;' +
				'width:14px; height:14px; margin:-5px -7px; border:1px solid #999!important;' +
				'box-shadow:inset -2px -4px 3px #ccc}' +
			'.cp-alpha:after{position:relative; content:"α"; color:#666; font-size:16px;' +
				'font-family:monospace; position:absolute; right:-26px; top:-8px}',

		renderCallback: function($elm, toggled) {
			var colors = this.color.colors,
				rgb = colors.RND.rgb;

			$('.cp-patch div').css({'background-color': $elm[0].style.backgroundColor});
			$('.cp-disp').text(this.color.options.colorNames[colors.HEX] || $elm.val());
			if (toggled === true) {
				// here you can recalculate position after showing the color picker
				// in case it doesn't fit into view.
				$('.trigger').removeClass('active');
				$elm.closest('.trigger').addClass('active');
			} else if (toggled === false) {
				$elm.closest('.trigger').removeClass('active');
			}
		}
	};

	plugin.dev_skinned_hh = $.extend({}, plugin.dev_skinned,
		{cssAddon: plugin.dev_skinned.cssAddon +
			'.cp-xy-slider{height:100px}' +
			'.cp-z-slider{height:100px}' +
			'.cp-xy-slider, .cp-patch, .cp-z-slider{box-shadow: inset 1px 1px 8px rgba(0,0,0,.15),' +
				' inset -1px -1px 6px rgba(0,0,0,.15);'});


	$pluginSelect.val(type || 'desktop').
	on('change', function(e) {
		window.location = './?type=' + this.value + '#demo'
	});


	var isMobile = /webOS|iPhone|iPad|iPod|BlackBerry|Windows Phone/i.
			test(window.navigator.userAgent);

	type === 'mobile' && !isMobile && $('#qr').show();
	isMobile && $('.div-toggles').hide();

	window.myColorPicker = $('.color').colorPicker(
		plugin[type] || plugin.desktop
	);
	$('.trigger').colorPicker();
//	$('pre').colorPicker({doRender: false});
})(window, jQuery);
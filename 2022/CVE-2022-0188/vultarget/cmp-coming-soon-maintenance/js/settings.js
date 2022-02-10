jQuery(document).ready(function ($) {
	var tab = document.location.hash.substring(1);
	var action = jQuery('#csoptions').attr('action');
	var settings = jQuery('#csoptions fieldset:not(.skip-preview-validation *)').serialize();

	// ini custom css textarea to codeEditor
	if (wp.codeEditor && jQuery('#niteoCS_custom_css').length) {
		wp.codeEditor.initialize('niteoCS_custom_css');
	}

	// function to tab navigation
	navtab = function (tab) {
		jQuery('.cmp-coming-soon-maintenance .nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
		jQuery('.nav-tab-wrapper .' + tab).addClass('nav-tab-active');

		jQuery('.cmp-coming-soon-maintenance .table-wrapper.' + tab).css('display', 'block');
		jQuery('.cmp-coming-soon-maintenance .table-wrapper-css.' + tab).css('display', 'block');
		jQuery('.cmp-coming-soon-maintenance .comingsoon.' + tab).css('display', 'block');

		jQuery('.cmp-coming-soon-maintenance .table-wrapper:not(.' + tab + ')').css('display', 'none');
		jQuery('.cmp-coming-soon-maintenance .table-wrapper-css:not(.' + tab).css('display', 'none');
		jQuery('.cmp-coming-soon-maintenance .comingsoon:not(.' + tab + ')').css('display', 'none');

		if (tab == 'install') {
			jQuery('.cmp-coming-soon-maintenance .submit').css('display', 'none');
			jQuery('.cmp-coming-soon-maintenance #csoptions').attr('action', action);
		} else {
			jQuery('.cmp-coming-soon-maintenance .submit').css('display', 'block');
			// change form action to display current tab after save
			jQuery('.cmp-coming-soon-maintenance #csoptions').attr('action', action + '#' + tab);
		}
	};

	if (tab != '') {
		navtab(tab);
	} else {
		jQuery('.cmp-coming-soon-maintenance .table-wrapper-css').css('display', 'none');
	}

	window.onhashchange = function () {
		tab = document.location.hash.substring(1);
		navtab(tab);
	};

	jQuery('.cmp-coming-soon-maintenance .nav-tab:not(.theme-preview):not(.advanced)').click(
		function (e) {
			e.preventDefault();
			tab = jQuery(this).data('tab');
			document.location.hash = tab;
		}
	);

	// update range inputs on change
	update_range('.cmp-coming-soon-maintenance .blur-range', 'span', 'html');
	update_range('.cmp-coming-soon-maintenance .background-overlay-opacity', 'span', 'html');
	update_range('#logo_size_slider', '#niteoCS_logo_size', 'val');
	update_range('#niteoCS_logo_size', '#logo_size_slider', 'val');

	// create media upload buttons
	// selector, multipe, type, element to render
	media_upload_button('logo', false, 'image', 'img');
	media_upload_button('favicon', false, 'image', 'img');
	media_upload_button('seo_img', false, 'image', 'img');
	media_upload_button('images', true, 'image', 'img');
	media_upload_button('mobile-images', true, 'image', 'img');
	media_upload_button('pattern', false, 'image', 'background');
	media_upload_button('video-thumb', false, 'image', 'img');
	media_upload_button('video-local', false, 'video', 'video');
	media_upload_button('subs-img', false, 'image', 'img');
	media_upload_button('subs-img-popup', false, 'image', 'img');
	media_upload_button('avatar', false, 'image', 'img');
	media_upload_button('gallery', true, 'image', 'img');

	// show / hide settings
	toggle_settings('analytics');
	toggle_settings('contact-form');
	toggle_settings('subscribe');
	toggle_settings('background-effect');
	toggle_settings('special-effect');
	toggle_settings('cmp-logo');
	toggle_settings('background-type');
	toggle_settings('counter');
	toggle_settings('progress-bar');
	toggle_settings('overlay-text');
	toggle_settings('cmp-logo-size');
	toggle_settings('recaptcha-toggle');
	toggle_settings('login-icon');
	toggle_settings('custom-mobile-imgs');
	toggle_settings('lang-switcher');
	toggle_settings('subscribe-popup');
	toggle_settings('inpage-subscribe');
	toggle_settings('tags');

	toggle_select('counter-action');
	toggle_select('subscribe-method');
	toggle_select('background-overlay');
	toggle_select('banner-video-source');
	toggle_select('progress-bar-type');

	cmp_repeat_fields('tags');

	// change all selects to select2
	jQuery(
		'.cmp-coming-soon-maintenance select:not(.headings-google-font):not(.content-google-font )'
	).select2({
		width: '100%',
		minimumResultsForSearch: -1,
		placeholder: 'Click to select..',
	});

	jQuery('.cmp-coming-soon-maintenance #cmp-status').click(function () {
		jQuery('.cmp-coming-soon-maintenance .cmp-status input[type=radio]').prop(
			'disabled',
			function (_, val) {
				return !val;
			}
		);
		jQuery('#cmp-toggle-adminbar').toggleClass('status-1');
		jQuery('.cmp-status-pages').fadeToggle();
	});

	jQuery('.cmp-status-pages input[type=radio]').change(function () {
		jQuery('.cmp-status-pages input[type=radio]').parent().removeClass('active');
		jQuery(this).parent().addClass('active');
	});

	cmp_status_inputs();

	function cmp_status_inputs() {
		// Make clickable status radio buttons
		jQuery('.cmp-coming-soon-maintenance .cmp-status.switch:not(.disabled)').click(function () {
			if (jQuery('.cmp-coming-soon-maintenance #cmp-status').prop('checked') == false) {
				return;
			}
			var $children = jQuery(this).children('input');
			$children.prop('checked', true);
			jQuery('.cmp-coming-soon-maintenance .cmp-status.switch').removeClass('active');
			jQuery(this).addClass('active');

			$children.trigger('change');

			if ($children.val() == '3') {
				jQuery('.cmp-coming-soon-maintenance .redirect-inputs').fadeIn('fast');
			} else {
				jQuery('.cmp-coming-soon-maintenance .redirect-inputs').fadeOut('fast');
			}
		});
	}

	// expandable tabs
	jQuery('.cmp-coming-soon-maintenance .table-wrapper h3').click(function () {
		jQuery(this).parent().toggleClass('closed');
	});

	// test unsplash image
	jQuery('.cmp-coming-soon-maintenance #test-unsplash').click(function (e) {
		e.preventDefault();

		var media_wrapper = jQuery('.cmp-coming-soon-maintenance #unsplash-media'),
			unsplash_feed = jQuery(
				'.cmp-coming-soon-maintenance .unsplash_banner select[name^="unsplash_feed"] option:selected'
			).val(),
			unsp_url = '',
			feat = '',
			custom_str = '',
			security = jQuery(this).data('security');

		// return if not specific unsplash photo selected - throttling due too much requests
		if (unsplash_feed != 0) return;

		switch (unsplash_feed) {
			// specific photo
			case '0':
				unsp_url = jQuery('.cmp-coming-soon-maintenance #niteoCS-unsplash-0').val();
				break;

			default:
				break;
		}

		if (unsplash_feed == 3 || unsp_url != '' || custom_str != '') {
			var params = {
				feed: unsplash_feed,
				url: unsp_url,
				feat: feat,
				custom_str: custom_str,
			};

			jQuery(this).prop('disabled', true);
			jQuery(this).html('<i class="fas fa-cog fa-spin fa-1x fa-fw"></i><span> loading..</span>');
			// media_wrapper.html('');

			var data = {
				action: 'niteo_unsplash',
				security: security,
				params: params,
			};

			jQuery.post(ajaxurl, data, function (response) {
				var unsplash = JSON.parse(response);

				jQuery('#unsplash_img').remove();

				var loadingTimeout = setTimeout(function () {
					jQuery('#test-unsplash').prop('disabled', false);
					jQuery('#test-unsplash').text('Display Unsplash Photo');
					jQuery('#unsplash-media').html(
						'<p>It seems <a href="https://status.unsplash.com/" target="_blank">Unsplash API</a> is not responding. Please try again later.</p>'
					);
				}, 5000);

				if (unsplash.response == '200') {
					var unsplash = jQuery.parseJSON(unsplash.body);

					if (unsplash[0]) {
						var img =
							unsplash[0]['urls']['raw'] +
							'?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&fit=max&w=900';
						var author = unsplash[0]['user']['name'];
						var author_url = unsplash[0]['user']['links']['html'];
						var img_url = unsplash[0]['links']['html'];
						var img_id = unsplash[0]['id'];
					} else {
						var img =
							unsplash['urls']['raw'] +
							'?ixlib=rb-0.3.5&q=80&fm=jpg&crop=entropy&cs=tinysrgb&fit=max&w=900';
						var author = unsplash['user']['name'];
						var author_url = unsplash['user']['links']['html'];
						var img_url = unsplash['links']['html'];
						var img_id = unsplash['id'];
					}

					jQuery('<img />', { src: img, id: 'unsplash_img' }).one('load', function () {
						//Set something to run when it finishes loading
						jQuery(this).appendTo(media_wrapper);
						jQuery(this).fadeIn();
						jQuery('#test-unsplash').prop('disabled', false);
						jQuery('#test-unsplash').text('Display Unsplash Photo');
						jQuery('.unsplash-id').html(
							'<a href="' +
								img_url +
								'" target="_blank">Photo</a> (ID: ' +
								img_id +
								') by <a href="' +
								author_url +
								'" target="_blank">' +
								author +
								'</a> / <a href="https://unsplash.com/" target="_blank">Unsplash</a>'
						);
						jQuery('.blur-range').trigger('input');
						clearTimeout(loadingTimeout);
					});
				} else {
					jQuery('.cmp-coming-soon-maintenance #test-unsplash').prop('disabled', false);
					jQuery('.cmp-coming-soon-maintenance #test-unsplash').text('Display Unsplash Photo');
					jQuery('.cmp-coming-soon-maintenance #unsplash-media').html(
						'<p>Error ' +
							unsplash.response +
							': <span style="text-transform:lowercase;">' +
							JSON.parse(unsplash.body).errors +
							'</span></p>'
					);
					clearTimeout(loadingTimeout);
				}
			});
		} else {
			jQuery('.cmp-coming-soon-maintenance #unsplash_img').remove();
		}
	});

	videoPreview = function () {
		// return of video background is not selected
		if (jQuery('.cmp-coming-soon-maintenance .background-type:checked').val() != '5') {
			return;
		}

		var source = jQuery('.cmp-coming-soon-maintenance .banner-video-source').val();

		if (source == 'youtube') {
			var youtubeURL = jQuery('.cmp-coming-soon-maintenance #niteoCS-youtube-url').val();

			// get YT thumbnail and append it to wrapper
			if (youtubeURL != '') {
				var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
				var ytID = youtubeURL.match(regExp);

				if (ytID && ytID[7].length == 11) {
					jQuery('.cmp-coming-soon-maintenance .video-yt-wrapper .video-yt-thumb-wrapper').html('');
					var ytThumb = 'https://img.youtube.com/vi/' + ytID[7] + '/maxresdefault.jpg';
					jQuery('.cmp-coming-soon-maintenance .video-yt-wrapper .video-yt-thumb-wrapper').append(
						'<img src="' + ytThumb + '" alt=""/>'
					);
					jQuery('.cmp-coming-soon-maintenance .blur-range').trigger('input');
				} else {
					jQuery('.cmp-coming-soon-maintenance .video-yt-wrapper .video-yt-thumb-wrapper').html('');
				}
			}
		}

		// append local video
		if (source == 'local') {
			var videoURL = jQuery('#niteoCS-video-local-id').data('url');

			if (videoURL != '') {
				jQuery('.cmp-coming-soon-maintenance .video-local-wrapper').html(
					'<video width="600" height="400" controls><source src="' +
						videoURL +
						'" type="video/mp4">Your browser does not support the video tag.</video>'
				);
			}
		}
	};

	// display video on load
	videoPreview();

	// display videos on background change to video type
	jQuery('.cmp-coming-soon-maintenance .background-type').on('change', function () {
		videoPreview();
	});

	// display YT video while inserting YT URL
	jQuery('.cmp-coming-soon-maintenance #niteoCS-youtube-url').on('keyup', function () {
		videoPreview();
	});

	jQuery('.cmp-coming-soon-maintenance .banner-video-source').on('change', function () {
		videoPreview();
	});

	// display selected unsplash feed
	var unsplashfeed = jQuery('.unsplash_banner select[name^="unsplash_feed"] option:selected').val();
	jQuery('.unsplash-feed-' + unsplashfeed).css('display', 'block');

	jQuery('.unsplash_banner select[name^="unsplash_feed"]').on('change', function () {
		unsplashfeed = jQuery('.unsplash_banner select[name^="unsplash_feed"] option:selected').val();
		jQuery('.unsplash-feed').css('display', 'none');
		jQuery('.unsplash-feed-' + unsplashfeed).css('display', 'block');
		jQuery('#test-unsplash').trigger('click');
	});

	// load unsplash upon load if unsplash is selected
	if (jQuery('#csoptions .niteoCS_banner:checked').val() == 1) {
		jQuery('#test-unsplash').trigger('click');
	}

	// preview gradient on select change
	jQuery('.cmp-coming-soon-maintenance select.background-gradient')
		.on('change', function () {
			var gradient = jQuery(
				'.cmp-coming-soon-maintenance select.background-gradient option:selected'
			).val();

			if (gradient == 'custom') {
				jQuery('.cmp-coming-soon-maintenance .custom-gradient').css('display', 'block');
				jQuery('.cmp-coming-soon-maintenance .gradient-preview').css({
					background:
						'-moz-linear-gradient(-45deg, ' +
						jQuery('#niteoCS_gradient_one').val() +
						' 0%, ' +
						jQuery('#niteoCS_gradient_two').val() +
						' 100%)',
					background:
						'-webkit-linear-gradient(-45deg, ' +
						jQuery('#niteoCS_gradient_one').val() +
						' 0%, ' +
						jQuery('#niteoCS_gradient_two').val() +
						' 100%)',
					background:
						'linear-gradient(135deg, ' +
						jQuery('#niteoCS_gradient_one').val() +
						' 0%, ' +
						jQuery('#niteoCS_gradient_two').val() +
						' 100%)',
				});
			} else {
				colors = gradient.split(':');
				jQuery('.cmp-coming-soon-maintenance .custom-gradient').css('display', 'none');
				jQuery('.cmp-coming-soon-maintenance .gradient-preview').css({
					background: '-moz-linear-gradient(-45deg, ' + colors[0] + ' 0%, ' + colors[1] + ' 100%)',
					background:
						'-webkit-linear-gradient(-45deg, ' + colors[0] + ' 0%, ' + colors[1] + ' 100%)',
					background: 'linear-gradient(135deg, ' + colors[0] + ' 0%, ' + colors[1] + ' 100%)',
				});
			}
		})
		.trigger('change');

	// banner background colorpicker
	jQuery('.cmp-coming-soon-maintenance #niteoCS_banner_color').wpColorPicker({
		change: function (event, ui) {
			jQuery('.color-preview').css('background-color', ui.color.toString());
		},
	});

	// banner gradient background colorpicker one
	jQuery('.cmp-coming-soon-maintenance #niteoCS_gradient_one').wpColorPicker({
		change: function (event, ui) {
			jQuery('.gradient-preview').css({
				background:
					'-moz-linear-gradient(-45deg, ' +
					ui.color.toString() +
					' 0%, ' +
					jQuery('#niteoCS_gradient_two').val() +
					' 100%)',
				background:
					'-webkit-linear-gradient(-45deg, ' +
					ui.color.toString() +
					' 0%, ' +
					jQuery('#niteoCS_gradient_two').val() +
					' 100%)',
				background:
					'linear-gradient(135deg, ' +
					ui.color.toString() +
					' 0%, ' +
					jQuery('#niteoCS_gradient_two').val() +
					' 100%)',
			});
		},
	});

	// banner gradient background colorpicker two
	jQuery('.cmp-coming-soon-maintenance #niteoCS_gradient_two').wpColorPicker({
		change: function (event, ui) {
			jQuery('.gradient-preview').css({
				background:
					'-moz-linear-gradient(-45deg, ' +
					jQuery('#niteoCS_gradient_one').val() +
					' 0%, ' +
					ui.color.toString() +
					' 100%)',
				background:
					'-webkit-linear-gradient(-45deg, ' +
					jQuery('#niteoCS_gradient_one').val() +
					' 0%, ' +
					ui.color.toString() +
					' 100%)',
				background:
					'linear-gradient(135deg, ' +
					jQuery('#niteoCS_gradient_one').val() +
					' 0%, ' +
					ui.color.toString() +
					' 100%)',
			});
		},
	});

	// OVERLAY COLOR
	jQuery('.cmp-coming-soon-maintenance #niteoCS_overlay_color').wpColorPicker({
		change: function (event, ui) {
			jQuery('.thumbnail-overlay').css('background', ui.color.toString());
		},
	});

	// get overlay color and apply it to Background thumbnails
	jQuery('.cmp-coming-soon-maintenance .thumbnail-overlay').css(
		'background',
		jQuery('#niteoCS_overlay_color').val()
	);

	// OVERLAY GRADIENT
	jQuery('.cmp-coming-soon-maintenance select.overlay-gradient').on('change', function () {
		var overlay_gradient = jQuery('select.overlay-gradient option:selected').val();

		if (overlay_gradient == 'custom') {
			jQuery('.cmp-coming-soon-maintenance .custom-overlay-gradient').css('display', 'block');

			var gradient_one = jQuery('#niteoCS_overlay_gradient_one').val();
			var gradient_two = jQuery('#niteoCS_overlay_gradient_two').val();

			jQuery('.cmp-coming-soon-maintenance .thumbnail-overlay').css({
				background:
					'-moz-linear-gradient(-45deg, ' + gradient_one + ' 0%, ' + gradient_two + ' 100%)',
				background:
					'-webkit-linear-gradient(-45deg, ' + gradient_one + ' 0%, ' + gradient_two + ' 100%)',
				background: 'linear-gradient(135deg, ' + gradient_one + ' 0%, ' + gradient_two + ' 100%)',
			});
		} else {
			colors = overlay_gradient.split(':');
			jQuery('.cmp-coming-soon-maintenance .custom-overlay-gradient').css('display', 'none');
			jQuery('.cmp-coming-soon-maintenance .thumbnail-overlay').css({
				background: '-moz-linear-gradient(-45deg, ' + colors[0] + ' 0%, ' + colors[1] + ' 100%)',
				background: '-webkit-linear-gradient(-45deg, ' + colors[0] + ' 0%, ' + colors[1] + ' 100%)',
				background: 'linear-gradient(135deg, ' + colors[0] + ' 0%, ' + colors[1] + ' 100%)',
			});
		}
	});

	// Overlay gradient colorpicker one
	jQuery('#niteoCS_overlay_gradient_one').wpColorPicker({
		change: function (event, ui) {
			jQuery('.thumbnail-overlay').css({
				background:
					'-moz-linear-gradient(-45deg, ' +
					ui.color.toString() +
					' 0%, ' +
					jQuery('#niteoCS_overlay_gradient_two').val() +
					' 100%)',
				background:
					'-webkit-linear-gradient(-45deg, ' +
					ui.color.toString() +
					' 0%, ' +
					jQuery('#niteoCS_overlay_gradient_two').val() +
					' 100%)',
				background:
					'linear-gradient(135deg, ' +
					ui.color.toString() +
					' 0%, ' +
					jQuery('#niteoCS_overlay_gradient_two').val() +
					' 100%)',
			});
		},
	});

	// Overlay gradient colorpicker two
	jQuery('#niteoCS_overlay_gradient_two').wpColorPicker({
		change: function (event, ui) {
			jQuery('.thumbnail-overlay').css({
				background:
					'-moz-linear-gradient(-45deg, ' +
					jQuery('#niteoCS_overlay_gradient_one').val() +
					' 0%, ' +
					ui.color.toString() +
					' 100%)',
				background:
					'-webkit-linear-gradient(-45deg, ' +
					jQuery('#niteoCS_overlay_gradient_one').val() +
					' 0%, ' +
					ui.color.toString() +
					' 100%)',
				background:
					'linear-gradient(135deg, ' +
					jQuery('#niteoCS_overlay_gradient_one').val() +
					' 0%, ' +
					ui.color.toString() +
					' 100%)',
			});
		},
	});

	// OVERLAY OPACITY
	jQuery('.background-overlay-opacity')
		.on('input', function () {
			var value = jQuery(this).val();
			jQuery('.thumbnail-overlay').css('opacity', value);
		})
		.trigger('input');

	// OVERLAY SELECTION
	gradientIni(jQuery('.cmp-coming-soon-maintenance .background-overlay').val());

	jQuery('.cmp-coming-soon-maintenance .background-overlay').on('change', function () {
		gradientIni(jQuery(this).val());
	});

	function gradientIni(gradient_type) {
		switch (gradient_type) {
			case 'solid-color':
				jQuery('.thumbnail-overlay').css('background', jQuery('#niteoCS_overlay_color').val());
				break;

			case 'gradient':
				jQuery('.cmp-coming-soon-maintenance select.overlay-gradient').trigger('change');
				break;

			case 'disabled':
				jQuery('.cmp-coming-soon-maintenance .thumbnail-overlay').css('background', 'none');
				break;

			default:
				break;
		}
	}

	// BLUR PREVIEW
	jQuery('.cmp-coming-soon-maintenance .blur-range')
		.on('input', function () {
			var value = jQuery(this).val();
			jQuery('.cmp-coming-soon-maintenance .background-thumb-wrapper img:not(.no-blur)').css(
				'filter',
				'blur(' + value + 'px)'
			);
		})
		.trigger('input');

	// banner pattern on change image preview
	jQuery('.cmp-coming-soon-maintenance select[name^="niteoCS_banner_pattern"]').on(
		'change',
		function () {
			var pattern = jQuery(
				'.cmp-coming-soon-maintenance select[name^="niteoCS_banner_pattern"] option:selected'
			).val();

			if (pattern != 'custom') {
				var pattern_url = jQuery(this).data('url');
				jQuery('.cmp-coming-soon-maintenance #add-pattern').css('display', 'none');
				jQuery('.cmp-coming-soon-maintenance .pattern-wrapper').css(
					'background-image',
					"url('" + pattern_url + pattern + ".png')"
				);
			} else {
				var pattern_url = jQuery(
					'.cmp-coming-soon-maintenance #niteoCS_banner_pattern_custom'
				).val();
				jQuery('.cmp-coming-soon-maintenance #add-pattern').css('display', 'block');
				jQuery('.cmp-coming-soon-maintenance .pattern-wrapper').css(
					'background-image',
					"url('" + pattern_url + "')"
				);
			}
		}
	);

	// preview animation
	jQuery('.cmp-coming-soon-maintenance .heading-animation').on('change', function () {
		heading_anim = jQuery('.cmp-coming-soon-maintenance .heading-animation option:selected').val();
		jQuery('.cmp-coming-soon-maintenance #heading-example')
			.removeClass()
			.addClass('animated ' + heading_anim);
	});

	jQuery('.cmp-coming-soon-maintenance .content-animation').on('change', function () {
		heading_anim = jQuery('.cmp-coming-soon-maintenance .content-animation option:selected').val();
		jQuery('.cmp-coming-soon-maintenance #content-example')
			.removeClass()
			.addClass('animated ' + heading_anim);
	});

	// ----------------------- sortable social list -----------------------
	// function to update social list
	var update_social = function (name, key, val) {
		var socialmedia = jQuery('.cmp-coming-soon-maintenance #niteoCS_socialmedia').attr('value');
		socialmedia = jQuery.parseJSON(socialmedia);

		jQuery.each(socialmedia, function (i, ele) {
			if (ele['name'] == name) {
				ele[key] = val;
			}
		});

		jQuery('.cmp-coming-soon-maintenance #niteoCS_socialmedia').attr(
			'value',
			JSON.stringify(socialmedia)
		);
	};

	// sortable UI - disabled on Mobile phones - input elements where not clickable...
	if (!/Mobi/.test(navigator.userAgent)) {
		var $sortableList = jQuery('.cmp-coming-soon-maintenance .social-inputs');

		var sortEventHandler = function (event, ui) {
			var inputs = $sortableList.find('input[type="text"]');

			var order = ui.item.index();

			inputs.each(function (i, ele) {
				var name = jQuery(ele).data('name');
				update_social(name, 'order', i);
			});
		};

		$sortableList.sortable({
			stop: sortEventHandler,
		});

		$sortableList.on('sortchange', sortEventHandler);
	}

	// social checkbox to enable/disable input
	(function ($) {
		jQuery.fn.toggleDisabled = function () {
			return this.each(function () {
				var $this = jQuery(this);
				var active;
				var name = $this.data('name');
				if ($this.attr('disabled')) {
					$this.prop('disabled', false);
					active = '1';
				} else {
					$this.prop('disabled', true);
					active = '0';
				}
				update_social(name, 'active', active);
			});
		};
	})(jQuery);

	jQuery('.cmp-coming-soon-maintenance .social-inputs input[type="text"]').focusout(function () {
		var name = jQuery(this).data('name');
		var socialurl = jQuery(this).val();
		update_social(name, 'url', socialurl);
	});

	jQuery('.cmp-coming-soon-maintenance .social-inputs input[type="checkbox"]').click(function (e) {
		var $this = jQuery(this).siblings('input[type="text"]');
		$this.toggleDisabled();
	});

	// social icons active/inactive
	jQuery('.cmp-coming-soon-maintenance .social-media i').click(function () {
		var name = jQuery(this).data('name');
		jQuery(this).toggleClass('active');
		jQuery('.cmp-coming-soon-maintenance .social-inputs li.' + name).toggleClass('active');
		jQuery('.cmp-coming-soon-maintenance .social-inputs li.' + name + ' input').trigger('change');

		if (jQuery(this).hasClass('active')) {
			update_social(name, 'hidden', '0');
		} else {
			update_social(name, 'hidden', '1');
		}
		// hide/show input labels
		if (jQuery('.cmp-coming-soon-maintenance .social-media i.active').length) {
			jQuery('.social-inputs .label').css('display', 'block');
		} else {
			jQuery('.cmp-coming-soon-maintenance .social-inputs .label').css('display', 'none');
		}
	});

	// hide/show input labels
	if (jQuery('.cmp-coming-soon-maintenance .social-media i.active').length) {
		jQuery('.cmp-coming-soon-maintenance .social-inputs .label').css('display', 'block');
	}

	// theme update via admin notice
	jQuery('.cmp.update-theme').click(function (e) {
		e.preventDefault();
		var $this = jQuery(this),
			$parent = $this.parents('.notice'),
			security = $this.data('security'),
			slug = $this.data('slug'),
			themeName = $this.data('name'),
			remoteUrl = jQuery(this).data('remote_url');
		var update = {
			name: slug,
			tmp_name: '',
			url: remoteUrl + '?action=download&slug=' + slug,
		};

		var data = {
			action: 'cmp_theme_update_install',
			security: security,
			file: update,
		};

		$parent
			.find('.message')
			.html(
				'<i class="fas fa-cog fa-spin fa-1x fa-fw"></i><span class="sr-only">Updating heme...</span><span> working hard on updating Theme...</span>'
			);

		jQuery.post(ajaxurl, data, function (response) {
			response = response.trim();

			if (response == 'success') {
				setTimeout(function () {
					$parent.removeClass('notice-warning').addClass('notice-success');
					$parent
						.find('.message')
						.html(
							'<span> ' +
								themeName +
								' CMP theme was updated sucessfully! You can enjoy latest features now :) </span><i class="far fa-smile" aria-hidden="true"></i>'
						);
				}, 1500);
			} else {
				response = response.slice(0, -1);
				var error = jQuery('p', jQuery(response)).text();
				$parent.removeClass('notice-warning').addClass('notice-error');
				$parent
					.find('.message')
					.html('<i class="far fa-frown" aria-hidden="true"></i><span> ' + error + '</span>');
			}
		});
	});

	// theme update via theme button
	jQuery('.cmp-coming-soon-maintenance .theme-update.button').one('click', function (e) {
		e.preventDefault();
		var $this = jQuery(this),
			$wrapper = $this.closest('.theme-wrapper'),
			security = $wrapper.data('security'),
			slug = $wrapper.data('slug'),
			remoteUrl = $wrapper.data('remote_url');

		var update = {
			name: slug,
			tmp_name: '',
			url: remoteUrl + '?action=download&slug=' + slug,
		};

		var data = {
			action: 'cmp_theme_update_install',
			security: security,
			file: update,
		};

		$this.html('<i class="fas fa-cog fa-spin fa-1x fa-fw"></i><span>Updating..</span>');

		jQuery.post(ajaxurl, data, function (response) {
			if (response == 'success') {
				setTimeout(function () {
					$this.html('<i class="far fa-smile" aria-hidden="true"></i><span>Updated!</span>');
					setTimeout(function () {
						$this.fadeOut();
					}, 1500);
				}, 1500);
			} else {
				response = response.slice(0, -1);
				$this.html('<i class="far fa-frown" aria-hidden="true"></i><span>Update Failed!</span>');
			}
		});
	});

	// display theme details overlay
	jQuery('.cmp-coming-soon-maintenance .theme-details').click(function (e) {
		e.preventDefault();
		var $this = jQuery(this),
			$wrapper = $this.closest('.theme-wrapper'),
			slug = $wrapper.data('slug'),
			version = $wrapper.data('version'),
			type = $wrapper.data('type'),
			purchased = $wrapper.data('purchased'),
			i = 0;

		var data = {
			action: 'niteo_themeinfo',
			security: jQuery('.theme-wrapper').data('security'),
			theme_slug: jQuery(this).parents('.theme-wrapper').data('slug'),
		};

		jQuery.post(ajaxurl, data, function (response) {
			var buyButton = '';
			var versionInfo = '';
			var noticeHtml = '';
			// parse JSON data to array
			response = jQuery.parseJSON(response);

			if (response.result == 'true') {
				// overflow body hidden
				jQuery('body').addClass('modal-open');

				// if installed display version info
				if (purchased == '1') {
					versionInfo = '<span class="theme-version">Installed version: ' + version + '</span>';
				}

				// if premium and not installed, display buy button
				if (purchased != '1' && type == 'premium') {
					var buyURL = $wrapper.find('.cmp-purchase-theme').attr('href');
					var buyButton =
						'<button type="button" class="theme-purchase button hide"><a href="' +
						buyURL +
						'" target="_blank"><i class="fas fa-cart-arrow-down" aria-hidden="true"></i>Get Theme</a></button>';
				}

				// get screenshots
				var screenshots = response.screenshots;
				var arrows = '';
				// if we have more screenshots, generate navigation arrows
				if (Object.keys(screenshots).length > 1) {
					arrows =
						'<div class="screenshots-nav"><div class="left"><i class="fas fa-chevron-left" aria-hidden="true"></i></div><div class="right"><i class="fas fa-chevron-right" aria-hidden="true"></i></div></div>';
				}

				// generate html to append to theme-overlay
				var html = jQuery(
					[
						'<div class="theme-backdrop">',
						'	<div class="theme-wrap">',
						'		<div class="theme-header">',
						'			<button class="close dashicons dashicons-no"><span class="screen-reader-text">Close details dialog</span></button>',
						'		</div>',
						'		<div class="theme-about">',
						'			<div class="theme-screenshots">',
						'				<div class="screenshot" style="background-image:url(\'' +
							screenshots['0'] +
							'\')">' +
							arrows +
							'</div>',
						'			</div>',
						'			<div class="theme-info">',
						'				<h2 class="theme-name">' + response['name'] + versionInfo + '</h2>',
						'				<p class="theme-author">By <a href="' +
							response['author_homepage'] +
							'" target="_blank">' +
							response['author'] +
							'</a></p>',
						noticeHtml,
						buyButton,
						'				<div class="theme-description">' + response['description'] + '</div>',
						'			</div>',
						'		</div>',
						'		<div class="theme-actions">',
						'			<a href="https://niteothemes.com/cmp-coming-soon-maintenance/?theme=' +
							slug +
							'&utm_source=cmp&utm_medium=referral&utm_campaign=' +
							slug +
							'" class="button cmp-preview" target="_blank" aria-label="Preview ' +
							response['name'] +
							'">Live Preview</a>',
						'		</div>',
						'	</div>',
						'</div>',
					].join('\n')
				);

				// append html to overlay
				jQuery('.theme-overlay.cmp').append(html);

				// attach close button handler
				jQuery('.theme-overlay.cmp .close').click(function (e) {
					e.preventDefault();
					// overflow body hidden
					jQuery('body').removeClass('modal-open');
					jQuery('.theme-overlay.cmp .theme-backdrop').fadeOut('fast');
				});

				// attach arrows navigation handler
				jQuery('.screenshots-nav .right').click(function () {
					i++;

					if (i == Object.keys(screenshots).length) {
						i = 0;
					}

					if (i in screenshots) {
						jQuery('.screenshot').css('background-image', "url('" + screenshots[i] + "')");
					}
				});

				// attach arrows navigation handler
				jQuery('.screenshots-nav .left').click(function () {
					i--;

					if (i < 0) {
						i = Object.keys(screenshots).length - 1;
					}

					if (i in screenshots) {
						jQuery('.screenshot').css('background-image', "url('" + screenshots[i] + "')");
					}
				});
			}
		});
	});

	function media_upload_button(name, multiple, type, element) {
		// define var
		var $container = jQuery('.' + name + '-wrapper');
		var $add_button = jQuery('#add-' + name);
		var $delete_button = jQuery('#delete-' + name);
		var image;
		var imgID = '';
		var title = name.replace('-', ' ');
		title = title[0].toUpperCase() + title.slice(1);

		if (jQuery('#niteoCS-' + name + '-id').val() != '') {
			// Display Delete button
			$delete_button.css('display', 'block');
		}

		$add_button.click(function (e) {
			e.preventDefault();
			// If the media frame already exists, reopen it.
			if (media_uploader) {
				media_uploader.open();
				return;
			}

			var media_uploader = wp
				.media({
					title: 'Select ' + title,
					button: {
						text: 'Insert ' + title,
					},
					multiple: multiple, // Set this to true to allow multiple files to be selected
					library: {
						type: [type],
					},
				})
				.on('select', function () {
					// Get media attachment details from the frame state
					var attachment = media_uploader.state().get('selection').toJSON();

					if (attachment.length > 0) {
						$container.find('img').remove();

						// remove and add gallery count class
						$container.attr('class', name + '-wrapper custom-gallery');
						$container.addClass('gallery-' + attachment.length);

						// get images ID, append thumbnail and store IDs in hidden input
						jQuery(attachment).each(function (i) {
							if (attachment[i].sizes && attachment[i].sizes.large) {
								image = attachment[i].sizes.large.url;
							} else {
								image = attachment[i].url;
							}

							// add image ID and url to comma-separated variable
							var comma = i === 0 ? '' : ',';
							imgID += comma + attachment[i].id;

							// Send the attachment URL to our custom image input field.
							switch (element) {
								case 'video':
									$container.append(
										'<video width="600" height="400" controls><source src="' +
											image +
											'" type="video/mp4">Your browser does not support the video tag.</video>'
									);
									break;

								case 'background':
									$container.css('background-image', "url('" + image + "')");
									break;

								case 'img':
								default:
									// if gallery - more than one image, it must be a graphic background, attach the first image to big coontainer
									if (
										attachment.length > 1 &&
										i == 0 &&
										jQuery('.' + name + '-wrapper .big-thumb').length
									) {
										jQuery('.' + name + '-wrapper .big-thumb').append(
											'<img src="' + image + '" alt=""/>'
										);

										// if gallery - more than one image, it must be a graphic background, attach all others images to normal wrapper
									} else if (attachment.length > 1 && i !== 0) {
										$container.append('<img src="' + image + '" alt="" class="no-blur"/>');

										// if single image, and it is a background image, attach it to big container as well
									} else if ($container.find('.big-thumb').length) {
										jQuery('.' + name + '-wrapper .big-thumb').append(
											'<img src="' + image + '" alt=""/>'
										);

										// all others images
									} else {
										$container.append('<img src="' + image + '" alt=""/>');
									}

									jQuery('.blur-range').trigger('input');
									break;
							}
						});

						// Display Delete  button
						$delete_button.css('display', 'block');
					}
					// update hidden input with media id and trigger change
					jQuery('#niteoCS-' + name + '-id')
						.val(imgID)
						.trigger('change');
				})
				.open();
		});

		$delete_button.click(function (e) {
			jQuery(this).css('display', 'none');
			$container.find('img').remove();
			jQuery('#niteoCS-' + name + '-id').val('');
			jQuery('#niteoCS-' + name + '-id').trigger('change');
		});
	}

	// Retrieve Mailchimp lists
	jQuery('.cmp-coming-soon-maintenance #connect-mailchimp').click(function (e) {
		e.preventDefault();

		var apikey = jQuery(
				'.cmp-coming-soon-maintenance input[name="niteoCS_mailchimp_apikey"]'
			).val(),
			security = jQuery(this).data('security'),
			button = jQuery(this);

		if (apikey != '') {
			var params = { apikey: apikey, security: security };

			jQuery(this).prop('disabled', true);

			jQuery(this).html(
				'<i class="fas fa-cog fa-spin fa-1x fa-fw"></i><span> retrieving lists..</span>'
			);

			var data = {
				action: 'cmp_mailchimp_list_ajax',
				security: security,
				params: params,
			};

			jQuery
				.post(ajaxurl, data, function (response) {
					var lists = JSON.parse(response);

					if (lists.response == 200) {
						jQuery('#niteoCS_mailchimp_list').empty().prop('disabled', false);
						jQuery.each(lists.lists, function (i, val) {
							jQuery('#niteoCS_mailchimp_list').append(
								'<option value="' + val.id + '">' + val.name + '</option>'
							);
						});
					} else {
						jQuery('#niteoCS_mailchimp_list')
							.empty()
							.prop('disabled', true)
							.html('<option value="error">' + lists.message + '</option>')
							.trigger('change');
					}

					button.html('Retrieve Lists');
					button.prop('disabled', false);
				})
				.fail(function () {
					button.html('Retrieve Lists');
					button.prop('disabled', false);
				});
		}
	});

	function toggle_settings(classname) {
		// Logo type inputs
		jQuery('.cmp-coming-soon-maintenance .' + classname).change(function () {
			var value = jQuery('.cmp-coming-soon-maintenance .' + classname + ':checked').val();
			value = jQuery.isNumeric(value) ? 'x' + value : value;
			value = value === undefined ? 'off' : value;

			jQuery('.cmp-coming-soon-maintenance .' + classname + '-switch.' + value).css(
				'display',
				'block'
			);
			jQuery('.cmp-coming-soon-maintenance .' + classname + '-switch:not(.' + value + ')').css(
				'display',
				'none'
			);
		});

		jQuery('.cmp-coming-soon-maintenance .' + classname)
			.first()
			.trigger('change');
	}

	function toggle_select(classname) {
		jQuery('.cmp-coming-soon-maintenance .' + classname).change(function () {
			var value = jQuery('.' + classname).val();
			value = jQuery.isNumeric(value) ? 'x' + value : value;

			jQuery('.cmp-coming-soon-maintenance .' + classname + '.' + value).css('display', 'block');
			jQuery('.cmp-coming-soon-maintenance .' + classname + ':not(.' + value + ')').css(
				'display',
				'none'
			);
		});

		jQuery('.' + classname)
			.first()
			.trigger('change');
	}

	function update_range(selector, target, method) {
		jQuery(selector).on('input', function () {
			var value = jQuery(this).val();
			// change label value
			if (method === 'html') {
				jQuery(this).parent().find(target).html(value);
			}

			if (method === 'val') {
				jQuery(this).parent().find(target).val(value);
			}
		});
	}

	function cmp_repeat_fields(field_id) {
		if (!jQuery('#wrapper-' + field_id).length) {
			return;
		}
		jQuery('#add-' + field_id).click(function (e) {
			e.preventDefault();
			var $wrapper = jQuery('#wrapper-' + field_id);
			var $target = jQuery('#wrapper-' + field_id + ' .target-repeater-fields');
			var $fields = $wrapper.find('.source-repeater-fields').children().clone();
			$($fields[0]).val('');
			$($target).append($fields);
		});

		cmp_delete_field(field_id);
	}

	function cmp_delete_field(field_id) {
		jQuery('#wrapper-' + field_id + ' .target-repeater-fields').on(
			'click',
			'.delete-' + field_id,
			function (e) {
				e.preventDefault();
				$(this).prev().remove();
				$(this).remove();
			}
		);

		jQuery('#wrapper-' + field_id + ' .source-repeater-fields').on(
			'click',
			'.delete-' + field_id,
			function (e) {
				e.preventDefault();
				$(this).prev().val('');
			}
		);
	}

	// delete overlay color from premium themes after update 2.8
	jQuery('.cmp-coming-soon-maintenance .table-wrapper.theme-setup h4').each(function () {
		if (jQuery(this).html() == 'Overlay Color') {
			jQuery(this).parents('tr').remove();
			return false;
		}
	});

	// warn users about unsaved changes for preview
	jQuery('.nav-tab-wrapper').on('click', '.theme-preview', function (e) {
		if (jQuery('#csoptions fieldset:not(.skip-preview-validation *)').serialize() != settings) {
			if (
				!confirm(
					'You have made changes that will not be visible in the preview until you save them. Please save changes first.\nContinue anyway?'
				)
			) {
				e.preventDefault();
				return false;
			}
		}

		return true;
	});
});

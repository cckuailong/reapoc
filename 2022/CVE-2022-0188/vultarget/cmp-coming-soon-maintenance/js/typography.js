jQuery(document).ready(function ($) {

	headingVariant = jQuery('.cmp-coming-soon-maintenance .headings-google-font-variant').val();
	contentVariant = jQuery('.cmp-coming-soon-maintenance .content-google-font-variant').val();

	fontVariant = function (variant) {
		switch (variant) {
			case '100':
				return 'Thin 100';
				break;
			case '100italic':
				return 'Thin 100 Italic';
				break;
			case '200':
				return 'Extra-light 200';
				break;
			case '200italic':
				return 'Extra-light 200 Italic';
				break;
			case '300':
				return 'Light 300';
				break;
			case '300italic':
				return 'Light 300 Italic';
				break;
			case '400':
			case 'regular':
				return 'Regular 400';
				break;
			case '400italic':
			case 'italic':
				return 'Regular 400 Italic';
				break;
			case '500':
				return 'Medium 500';
				break;
			case '500italic':
				return 'Meidum 500 Italic';
				break;
			case '600':
				return 'Semi-Bold 600';
				break;
			case '600italic':
				return 'Semi-Bold 600 Italic';
				break;
			case '700':
				return 'Bold 700';
				break;
			case '700italic':
				return 'Bold 700 Italic';
				break;
			case '800':
				return 'Extra-Bold 800';
				break;
			case '800italic':
				return 'Extra-Bold Italic';
				break;
			case '900':
				return 'Black 900';
				break;
			case '900italic':
				return 'Black 900 Italic';
				break;
			case 'Not Applicable':
				return 'Not Applicable';
				break;
			default:
				break;
		}
	}

	loadCustomFont = function (fontFace, type) {

		newFontFace = Array.isArray(fontFace) ? fontFace[0] : fontFace;

		for (let index = 0; index < newFontFace['urls'].length; index++) {
			if (newFontFace['urls'][index].includes('woff')) {
				var url = newFontFace['urls'][index];
			}
		}

		var junction_font = new FontFace(newFontFace['id'], 'url(' + url + ')');
		var preview = document.getElementById(type + '-example');
		var logo = document.getElementById('niteoCS-text-logo');

		junction_font.load().then(function (loaded_face) {
			document.fonts.add(loaded_face);
			preview.style.fontFamily = '"' + loaded_face['family'] + '"';
			type === 'heading' ? logo.style.fontFamily = '"' + loaded_face['family'] + '"' : null;

		}).catch(function (error) {
			console.log('Cannot load custom font: ' + error)
		});
	}

	var heading_font = fonts.google.filter(function (element) {
		return element.id === jQuery('.cmp-coming-soon-maintenance .headings-google-font option:selected').val();
	});

	var content_font = fonts.google.filter(function (element) {
		return element.id === jQuery('.cmp-coming-soon-maintenance .content-google-font option:selected').val();
	});

	if (heading_font.length) {
		var heading_font_variant = jQuery.map(heading_font[0].variants, function (obj) {
			return { id: obj, text: fontVariant(obj) };
		});
	}

	if (content_font.length) {
		var content_font_variant = jQuery.map(content_font[0].variants, function (obj) {
			return { id: obj, text: fontVariant(obj) };
		});
	}

	// ini select2 
	$HeadingFont = jQuery('.cmp-coming-soon-maintenance .headings-google-font').select2({
		data: fonts.google,
		width: '100%',
		// templateResult: formatFont
	});

	// ini select2 
	$contentFont = jQuery('.cmp-coming-soon-maintenance .content-google-font').select2({
		data: fonts.google,
		width: '100%',
	});

	// ini select2 
	$HeadingFontVariant = jQuery('.cmp-coming-soon-maintenance .headings-google-font-variant').select2({
		data: heading_font_variant,
		width: '100%',
	})

	// ini select2 
	$contentFontVariant = jQuery('.cmp-coming-soon-maintenance .content-google-font-variant').select2({
		data: content_font_variant,
		width: '100%',
	})

	if (heading_font.length) {

		if (heading_font[0]['variants'][0] == 'Not Applicable') {
			loadCustomFont(heading_font, 'heading');
		} else {

			// change fonts families upon a load 
			WebFont.load({
				google: {
					families: [
						heading_font[0]['id'] + ':' + heading_font[0]['variants'].join(',')
					],
					text: 'Hello, I am your Headings font!' + jQuery('#niteoCS-text-logo').val()
				},
			});

			if (jQuery.isNumeric(headingVariant)) {
				jQuery('#heading-example').css('font-weight', headingVariant).css('font-style', 'normal');

			} else if (headingVariant == 'regular') {
				jQuery('#heading-example').css('font-weight', '400').css('font-style', 'normal');

			} else if (headingVariant == 'italic') {
				jQuery('#heading-example').css('font-style', 'italic').css('font-weight', '400');

			} else {
				fontweight = parseInt(headingVariant, 10);
				jQuery('#heading-example').css('font-weight', fontweight).css('font-style', 'italic');
			}

			jQuery('#heading-example, #niteoCS-text-logo').css('font-family', heading_font[0]['id']);
		}
	}

	if (content_font.length) {

		if (content_font[0]['variants'][0] == 'Not Applicable') {
			loadCustomFont(content_font, 'content');

		} else {
			// change fonts families upon a load 
			WebFont.load({
				google: {
					families: [
						content_font[0]['id'] + ':' + content_font[0]['variants'].join(',')
					],
					text: 'And this is a long paragraph. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
				}
			});

			if (jQuery.isNumeric(contentVariant)) {
				jQuery('#content-example').css('font-weight', contentVariant).css('font-style', 'normal');

			} else if (contentVariant == 'regular') {
				jQuery('#content-example').css('font-weight', '400').css('font-style', 'normal');

			} else if (contentVariant == 'italic') {
				jQuery('#content-example').css('font-style', 'italic').css('font-weight', '400');

			} else {
				fontweight = parseInt(contentVariant, 10);
				jQuery('#content-example').css('font-weight', fontweight).css('font-style', 'italic');
			}

			jQuery('#content-example').css('font-family', content_font[0]['id']);
		}
	}


	$('body').on('mouseenter', '.select2-results__option.select2-results__option--highlighted', function (e) {
		var data = $(this).data().data;
		var type = data.element.parentNode.dataset.type;

		if (type !== 'content' && type !== 'heading') {
			return
		}

		var text = type === 'heading' ? 'Hello, I am your Headings font!' : 'And this is a long paragraph. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

		if (data.variants[0] === 'Not Applicable') {
			loadCustomFont(data, type);

		} else {
			WebFont.load({
				google: {
					families: [
						[data.text] + ':100,200,300,regular,600,700,900,100italic,200italic,300italic,400italic,600italic,700italic,900italic'
					],
					text: text

				},
				active: function () {
					jQuery('#' + type + '-example, #niteoCS-text-logo').css('font-family', data.text);
				},
			});
		}


	});

	// change preview fonts on select2 selection
	$HeadingFont.on('select2:select', function (e) {
		// get current variant value
		var selected = $HeadingFontVariant.select2('data');

		var heading_font_variant = jQuery.map(e.params.data.variants, function (obj) {
			return { id: obj, text: fontVariant(obj) };
		});

		// empty select variant
		$HeadingFontVariant.empty();
		// populate select with new variants
		$HeadingFontVariant.select2({
			data: heading_font_variant
		});

		// set same variant as before selection if variant is in array, else set regular
		if (selected[0].id) {
			if (jQuery.inArray(selected[0].id, e.params.data.variants) == '-1') {
				jQuery('#heading-example, #niteoCS-text-logo').css('font-weight', '400').css('font-style', 'normal');
			} else {
				$HeadingFontVariant.val(selected[0].id).trigger('change.select2');
			}
		}

	});


	$HeadingFontVariant.on('select2:select', function (e) {

		headingVariant = e.params.data.id;

		if (jQuery.isNumeric(headingVariant)) {
			jQuery('#heading-example, #niteoCS-text-logo').css('font-weight', headingVariant).css('font-style', 'normal');

		} else if (headingVariant == 'regular') {
			jQuery('#heading-example, #niteoCS-text-logo').css('font-weight', '400').css('font-style', 'normal');

		} else if (headingVariant == 'italic') {
			jQuery('#heading-example, #niteoCS-text-logo').css('font-style', 'italic').css('font-weight', '400');

		} else {
			fontweight = parseInt(headingVariant, 10);
			jQuery('#heading-example, #niteoCS-text-logo').css('font-weight', fontweight).css('font-style', 'italic');
		}

	});


	// // change content preview fonts on font select
	$contentFont.on('select2:select', function (e) {
		// get current variant value
		var selected = $contentFontVariant.select2('data');

		var content_font_variant = jQuery.map(e.params.data.variants, function (obj) {
			return { id: obj, text: fontVariant(obj) };
		});

		// empty select variant
		$contentFontVariant.empty();
		// populate select with new variants
		$contentFontVariant.select2({
			data: content_font_variant
		});

		// set same variant as before selection if variant is in array, else set regular
		if (selected[0].id) {

			if (jQuery.inArray(selected[0].id, e.params.data.variants) == '-1') {
				jQuery('#content-example').css('font-weight', '400').css('font-style', 'normal');
			} else {
				$contentFontVariant.val(selected[0].id).trigger('change.select2');
			}
		}

	});

	$contentFontVariant.on('select2:select', function (e) {

		contentVariant = e.params.data.id;

		if (jQuery.isNumeric(contentVariant)) {
			jQuery('#content-example').css('font-weight', contentVariant).css('font-style', 'normal');

		} else if (contentVariant == 'regular') {
			jQuery('#content-example').css('font-weight', '400').css('font-style', 'normal');

		} else if (contentVariant == 'italic') {
			jQuery('#content-example').css('font-style', 'italic').css('font-weight', '400');

		} else {
			fontweight = parseInt(contentVariant, 10);
			jQuery('#content-example').css('font-weight', fontweight).css('font-style', 'italic');
		}

	});

	jQuery('.cmp-coming-soon-maintenance .font-selector input[type=range]').on('input', function () {
		var type = jQuery(this).data('type');
		var css = jQuery(this).data('css');
		var value = jQuery(this).val();

		// change label value
		jQuery(this).parent().find('span').html(value);

		// add px if css requires it
		value = (css == 'line-height') ? value : value + 'px';

		// change example css
		if (type == 'heading') {
			jQuery('#heading-example').css(css, value);

		} else {
			jQuery('#content-example').css(css, value);
		}
	});



	// Upload custom font
	jQuery('#cmp-install-font').click(function (e) {
		e.preventDefault();

		var file_frame;

		$(document.body).on('click', '#cmp-install-font', function (event) {
			event.preventDefault();

			var security = $(this).data('security');

			var payload = {
				action: 'upload_font',
				files: []
			}

			var data = {
				action: 'cmp_ajax_upload_font',
				security: security,
				payload: JSON.stringify(payload)
			};

			// If the media frame already exists, reopen it.
			if (file_frame) {
				file_frame.open();
				return;
			}

			// Create a new media frame
			file_frame = wp.media.frames.file_frame = wp.media({
				multiple: true  // Set to true to allow multiple files to be selected
			});

			// When a file is selected in the media frame...
			file_frame.on('select', function () {
				// Get selected attachment
				var attachment = file_frame.state().get('selection').toJSON();

				if (attachment.length) {
					payload.action = 'upload_font';
					// create unique array of font names
					var titles = [];

					jQuery(attachment).each(function (i, value) {
						if (titles.indexOf(value.title) < 0) {
							titles.push(value.title);
						}
					});

					// loop trough Font titles
					jQuery(titles).each(function (i, title) {

						var urls = [];
						var ids = [];
						// get all urls of same font title
						jQuery(attachment).each(function (i, value) {
							if (value.title === title) {
								urls.push(value.url);
								ids.push(value.id);
							}
						});

						// create payload object
						payload.files[i] = {
							id: title,
							text: title,
							urls: urls,
							ids: ids,
							variants: ['Not Applicable']
						}
					});

					data.payload = JSON.stringify(payload);

					$.post(ajaxurl, data, function (response) {
						for (var i = 0; i < payload.files.length; ++i) {
							fonts.google.unshift(payload.files[i])
						}

						$HeadingFont.select2({
							data: fonts.google
						});

						$contentFont.select2({
							data: fonts.google
						});

						alert('Following fonts have been added to Fonts Family selections: ' + titles.join(', '));
					});

				}

			});

			file_frame.open();

		});

	});

});


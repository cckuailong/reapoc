jQuery(document).ready(function ($) {
	var tab = document.location.hash.substring(1);
	var action = jQuery('#csoptions').attr('action');
	var importing = false;

	// function to tab navigation
	navtab = function (tab) {
		jQuery('.cmp-coming-soon-maintenance .nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');

		jQuery('.nav-tab-wrapper .nav-tab[data-tab="' + tab + '"]').addClass('nav-tab-active');

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

	jQuery('.cmp-coming-soon-maintenance .nav-tab').click(function (e) {
		e.preventDefault();
		tab = jQuery(this).data('tab');
		document.location.hash = tab;
	});

	toggle_settings('page-whitelist');
	toggle_settings('cmp-bypass');
	toggle_settings('cmp-topbar-icon');
	toggle_settings('countdown-toggle');
	toggle_settings('mode-change-toggle');
	toggle_settings('subscribe-toggle');
	toggle_settings('cmp-wpautop');
	toggle_settings('cmp-cookienotice');
	cmp_repeat_fields('head_scripts');
	cmp_repeat_fields('footer_scripts');

	// upload json file button
	jQuery('#cmp-import-json').on('change', function (e) {
		// change label
		jQuery('.import-json-label').attr('data-default', e.target.files[0].name);

		var reader = new FileReader();
		reader.onload = function (e) {
			jQuery('#cmp-import-input').val(e.target.result);
		};

		reader.readAsText(e.target.files[0]);

		// enable import button
		$('#cmp-import-settings').attr('disabled', false);
		jQuery('.import-json-label').removeClass('import-fail');

		importing = false;
	});

	// export button ajax call
	jQuery('#cmp-export-json').click(function (e) {
		e.preventDefault();

		var data = {
			action: 'cmp_ajax_export_settings',
			security: jQuery(this).data('security'),
		};

		jQuery.post(ajaxurl, data, function (response) {
			// if (response) {
			jQuery('<iframe />')
				.attr(
					'src',
					ajaxurl +
						'?action=cmp_ajax_export_settings&security=' +
						jQuery('#cmp-export-json').data('security')
				)
				.appendTo('body')
				.hide();
			// }
		});
	});

	// import button ajax call
	jQuery('#cmp-import-settings').click(function (e) {
		e.preventDefault();

		if (importing === true) {
			return false;
		}

		var json = jQuery('#cmp-import-input').val();
		var $label = jQuery('.import-json-label');

		// remove settings keys with media if no media import
		if (!jQuery('#cmp-import-media').is(':checked')) {
			var settings = new Array('niteoCS_banner_id', 'niteoCS_logo_id', 'niteoCS_seo_img_id');

			json = jQuery.parseJSON(json);

			json.map(function (value, index) {
				for (var key in value) {
					if (settings.indexOf(key) > -1) {
						json.splice(index, 1);
					}
				}
			});

			json = JSON.stringify(json);
		}

		var data = {
			action: 'cmp_ajax_import_settings',
			security: jQuery(this).data('security'),
			json: json,
		};

		importing = true;

		var ajaxTime = new Date().getTime();

		// change label
		$label.html('<i class="fa fa-cog fa-spin" aria-hidden="true"></i> importing..');
		$label.attr('data-default', '');

		jQuery.post(ajaxurl, data, function (response) {
			if (response) {
				var totalTime = new Date().getTime() - ajaxTime;
				var result = jQuery.parseJSON(response);

				if (totalTime > 2000) {
					if (result.result == 'success') {
						$label.addClass('import-success');
					} else {
						$label.addClass('import-fail');
					}

					$label.html('');
					$label.attr('data-default', result.message);
				} else {
					setTimeout(function () {
						if (result.result == 'success') {
							$label.addClass('import-success');
						} else {
							$label.addClass('import-fail');
						}

						$label.html('');
						$label.attr('data-default', result.message);
					}, 2000 - totalTime);
				}
			}
		});
	});

	function toggle_settings(classname) {
		jQuery('.' + classname).change(function () {
			var value = jQuery('.' + classname + ':checked').val();
			value = jQuery.isNumeric(value) ? 'x' + value : value;
			value = value === undefined ? 'x' + 0 : value;

			jQuery('.' + classname + '-switch.' + value).css('display', 'block');
			jQuery('.' + classname + '-switch:not(.' + value + ')').css('display', 'none');
		});

		jQuery('.' + classname)
			.first()
			.trigger('change');
	}

	jQuery('.cmp-whitelist-select, .cmp-blacklist, .cmp-user_roles').select2({
		width: 'calc(100% - 1em)',
		placeholder: 'Click to select..',
	});

	jQuery('select[name="niteoCS_topbar_version"]').select2({
		width: 'calc(100% - 1em)',
		minimumResultsForSearch: -1,
	});

	function copyTextToClipboard(text) {
		var textArea = document.createElement('textarea');
		textArea.style.position = 'fixed';
		textArea.style.top = 0;
		textArea.style.left = 0;
		textArea.style.width = '2em';
		textArea.style.height = '2em';
		textArea.style.padding = 0;
		textArea.style.border = 'none';
		textArea.style.outline = 'none';
		textArea.style.boxShadow = 'none';
		textArea.style.background = 'transparent';
		textArea.value = text;

		document.body.appendChild(textArea);
		textArea.focus();
		textArea.select();

		try {
			var successful = document.execCommand('copy');
			var msg = successful ? 'successful' : 'unsuccessful';
			console.log('Copying text command was ' + msg);
		} catch (err) {
			console.log('Oops, unable to copy');
		}

		document.body.removeChild(textArea);
	}

	jQuery('#copy-bypass').click(function (e) {
		e.preventDefault();
		copyTextToClipboard(jQuery('#bypass-code').html());
	});

	function cmp_repeat_fields(field_id) {
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
});

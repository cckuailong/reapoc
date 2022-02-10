jQuery(document).ready(function ($) {
	var regex = /^((?!0)(?!.*\.$)((1?\d?\d|25[0-5]|2[0-4]\d|\*)(\.|$)){4})|(([0-9a-f]|:){1,4}(:([0-9a-f]{0,4})*){1,7})$/;

	$('#frontend_ip_blacklist').tagsInput({
		defaultText: '',
		delimiter: ';',
		width: '90%',
		pattern: regex,
		onChange: function (obj, tag) {
			if ($('#frontend_ip_whitelist').tagExist(tag)) {
				$('#frontend_ip_blacklist').removeTag(tag);
			}
		}
	});

	$('#frontend_ip_whitelist').tagsInput({
		defaultText: '',
		delimiter: ';',
		width: '90%',
		pattern: regex,
		onChange: function (obj, tag) {
			if ($('#frontend_ip_blacklist').tagExist(tag)) {
				$('#frontend_ip_whitelist').removeTag(tag);
			}
		}
	});

	refresh_frontend_settings();

	$('.chosen').chosen({
		width: '95%'
	});

	$('#enable_frontend,input[name=frontend_option]').on('change', function () {
		refresh_frontend_settings();
	});

	function refresh_frontend_settings() {
		if ($('#enable_frontend').length == 0) {
			return;
		}

		if ($('#enable_frontend').is(':checked')) {
			$('.input-field,.tagsinput input,.disabled').prop('disabled', false);

			if ($('input[name=frontend_option]:checked').val() != '2') {
				$('#frontend_error_page').prop('disabled', true);
			}

			if ($('input[name=frontend_option]:checked').val() != '3') {
				$('#frontend_redirect_url').prop('disabled', true);
			}

			if ($('#support_proxy').val() == '0') {
				$('#frontend_block_proxy, #frontend_block_proxy_type').prop('disabled', true);
			}

			toggleTagsInput(true);
		} else {
			$('.input-field,.tagsinput input,.disabled').prop('disabled', true);
			toggleTagsInput(false);
		}

		$('.chosen').trigger('chosen:updated');
	}

	function toggleTagsInput(state) {
		if (!state) {
			$.each($('.tagsinput'), function (i, obj) {
				var $div = $('<div class="tagsinput-disabled" style="display:block;position:absolute;z-index:99999;opacity:0.1;background:#808080";top:' + $(obj).offset().top + ';left:' + $(obj).offset().left + '" />').css({
					width: $(obj).outerWidth() + 'px',
					height: $(obj).outerHeight() + 'px'
				});

				$(obj).parent().prepend($div);
			});
		} else {
			$('.tagsinput-disabled').remove();
		}
	}
});
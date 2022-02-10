jQuery(document).ready(function($){
	var regex = /^((?!0)(?!.*\.$)((1?\d?\d|25[0-5]|2[0-4]\d|\*)(\.|$)){4})|(([0-9a-f]|:){1,4}(:([0-9a-f]{0,4})*){1,7})$/;

	$('#backend_ip_blacklist').tagsInput({
		defaultText: '',
		delimiter: ';',
		width: '90%',
		pattern: regex,
		onChange: function(obj, tag){
			if($('#backend_ip_whitelist').tagExist(tag)){
				$('#backend_ip_blacklist').removeTag(tag);
			}
		}
	});

	$('#backend_ip_whitelist').tagsInput({
		defaultText: '',
		delimiter: ';',
		width: '90%',
		pattern: regex,
		onChange: function(obj, tag){
			if($('#backend_ip_blacklist').tagExist(tag)){
				$('#backend_ip_whitelist').removeTag(tag);
			}
		}
	});

	refresh_backend_settings();

	$('.chosen').chosen({
		width: '95%'
	});

	$('#enable_backend,input[name=backend_option]').on('change', function(){
		refresh_backend_settings();
	});

	$('#form_backend_settings').on('submit', function(e){
		if($('#enable_backend').is(':checked')){
			if($('#bypass_code').val().length == 0){
				if(($.inArray($('#my_country_code').val(), $('#backend_ban_list').val()) >= 0 && $('input[name=backend_block_mode]:checked').val() == 1) || ($.inArray($('#my_country_code').val(), $('#backend_ban_list').val()) < 0 && $('input[name=backend_block_mode]:checked').val() == 2)){
					alert("==========\n WARNING \n==========\n\nYou are about to block your own country, " + $('#my_country_name').val() + ".\nThis can locked yourself and prevent you from login to admin area.\n\nPlease set a bypass code to avoid this.");
					$('#bypass_code').focus();
					e.preventDefault();
				}
			}
		}
	});


	function refresh_backend_settings(){
		if($('#enable_backend').length == 0)
			return;

		if($('#enable_backend').is(':checked')){
			$('.input-field,.tagsinput input').prop('disabled', false);

			if($('input[name=backend_option]:checked').val() != '2'){
				$('#backend_error_page').prop('disabled', true);
			}

			if($('input[name=backend_option]:checked').val() != '3'){
				$('#backend_redirect_url').prop('disabled', true);
			}

			$('.disabled').prop('disabled', true);

			if ($('#support_proxy').val() == '0') {
				$('#backend_block_proxy, #backend_block_proxy_type').prop('disabled', true);
			}

			toggleTagsInput(true);
		}
		else{
			$('.input-field').prop('disabled', true);
			toggleTagsInput(false);
		}

		$('.chosen').trigger('chosen:updated');
	}

	function toggleTagsInput(state){
		if(!state){
			$.each($('.tagsinput'), function(i, obj){
				var $div = $('<div class="tagsinput-disabled" style="display:block;position:absolute;z-index:99999;opacity:0.1;background:#808080";top:' + $(obj).offset().top + ';left:' + $(obj).offset().left + '" />').css({
					width: $(obj).outerWidth() + 'px',
					height: $(obj).outerHeight() + 'px'
				});

				$(obj).parent().prepend($div);
			});
		}
		else{
			$('.tagsinput-disabled').remove();
		}
	}
});
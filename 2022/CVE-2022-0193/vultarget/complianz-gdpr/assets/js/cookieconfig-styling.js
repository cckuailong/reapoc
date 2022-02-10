jQuery(document).ready(function ($) {
    var ccName;
	var ccCatsOpenedByUser = false;
	var ccCookieWallShownBefore = false;
    $('.cmplz-color-picker').wpColorPicker({
        change:
            function (event, ui) {
                var container_id = $(event.target).data('hidden-input');
                if (container_id === 'cmplz_text_color'){
                	ccCheckboxes = '';
				}
                $('#' + container_id).val(ui.color.toString());
				cmplz_apply_style();
				cmplz_cookie_warning();
            }
        }
    );

	var ccConsentType = $('select[name=cmplz_type]').val();
    var settingConsentType = ccConsentType;
    if (ccConsentType === 'optinstats') settingConsentType = 'optin';

    $(document).on('change', 'select[name=cmplz_type]', function () {
        ccConsentType = $('select[name=cmplz_type]').val();
        settingConsentType = ccConsentType;
        if (ccConsentType === 'optinstats') settingConsentType = 'optin';
        cmplz_cookie_warning();
    });

    $(document).on('keyup', 'input[name=cmplz_header]', function () {
        $(".cc-header").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_dismiss]', function () {
    	if (settingConsentType !== 'optout') {
			$(".cc-dismiss").html($(this).val());
		}
    });
    $(document).on('keyup', 'input[name=cmplz_accept]', function () {
        $(".cc-allow").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_accept_optinstats]', function () {
        $(".cc-allow").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_accept_informational]', function () {
        $(".cc-allow").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_revoke]', function () {
        $(".cc-revoke").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_readmore_privacy]', function () {
        $(".cc-link.privacy-policy").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_view_preferences]', function () {
		$(".cc-show-settings").html($(this).val());
	});
	$(document).on('keyup', 'input[name=cmplz_accept_all]', function () {
		$(".cc-accept-all").html($(this).val());
	});
    $(document).on('keyup', 'input[name=cmplz_save_preferences]', function () {
        $(".cc-save-settings").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_category_functional]', function () {
        $(".cmplz_functional").closest('.cmplz-categories-wrap').find(".cc-category").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_category_stats]', function () {
        //keep up to date with other tabs
        $('input[name=cmplz_category_stats]').val($(this).val());
		$(".cmplz_statistics").closest('.cmplz-categories-wrap').find(".cc-category").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_category_prefs]', function () {
        //keep up to date with other tabs
        $('input[name=cmplz_category_prefs]').val($(this).val());
		$(".cmplz_preferences").closest('.cmplz-categories-wrap').find(".cc-category").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_category_all]', function () {
        //keep up to date with other tabs
        $('input[name=cmplz_category_all]').val($(this).val());
		$(".cmplz_marketing").closest('.cmplz-categories-wrap').find(".cc-category").html($(this).val());
    });
    $(document).on('keyup', 'input[name=cmplz_readmore_optin]', function () {
        $(".cc-link.cookie-policy").html($(this).val());
    });
	$(document).on('keyup', 'input[name=cmplz_readmore_impressum]', function () {
		$(".cc-link.impressum").html($(this).val());
	});
    $(document).on('keyup', 'input[name=cmplz_readmore_optinstats]', function () {
        $(".cc-link.cookie-policy").html($(this).val());
    });

	$(document).on('keyup', 'input[name=cmplz_banner_width]', function () {
		cmplz_apply_style();
	});
	$(document).on('change', 'input[name=cmplz_banner_width]', function () {
		cmplz_apply_style();
	});

    $(document).on('keyup', 'input[name=cmplz_readmore_optout]', function () {
        $(".cc-link.cookie-policy").html($(this).val());
    });

	$(document).on('keyup', 'input[name=cmplz_readmore_optout_dnsmpi]', function () {
		$(".cc-link.cookie-policy").html($(this).val());
	});


    $(document).on('keyup', 'textarea[name=cmplz_tagmanager_categories]', function () {
		var ccCheckboxBase = '<div class="cmplz-categories-wrap" style="display: block;">'+$(".cmplz_functional").closest(".cmplz-categories-wrap").html()+'</div>';
		var ccTagManagerCategories = $('textarea[name=cmplz_tagmanager_categories]').val();
		var ccCategoryAll = $('input[name=cmplz_category_all]').val();
		var ccCategoryFunctional = $('input[name=cmplz_category_functional]').val();


		ccCheckboxes = ccCheckboxBase.replace('disabled', 'disabled checked');
		var ccCheckboxBase = ccCheckboxBase.replace('disabled', 'data-1');
		var tmCatsKV = ccTagManagerCategories.split(",");
		tmCatsKV.forEach(function(category, i) {
			if (category.length > 0)
			{
				var tmp = ccCheckboxBase.replace(/cmplz_functional/g, 'cmplz_' + i);
				tmp = tmp.replace(ccCategoryFunctional, category);
				ccCheckboxes += tmp;
			}
		});
		tmp = ccCheckboxBase.replace(/cmplz_functional/g, 'cmplz_marketing');
		ccCheckboxes += tmp.replace(ccCategoryFunctional, ccCategoryAll);

		cmplz_cookie_warning_render();
    });

    setTimeout(function () {
		if (typeof tinymce !== 'undefined' ) {
			for (var i = 0; i < tinymce.editors.length; i++) {
				tinymce.editors[i].on('NodeChange keyup', function (ed, e) {
					var content;
					var link = $(".cc-message").find('a').html();
					var editor_id = 'cmplz_message_' + settingConsentType;
					var textarea_id = 'cmplz_message';
					if (typeof editor_id == 'undefined') editor_id = wpActiveEditor;
					if (typeof textarea_id == 'undefined') textarea_id = editor_id;

					if (jQuery('#wp-' + editor_id + '-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id)) {
						content = tinyMCE.get(editor_id).getContent();
					} else {
						content = jQuery('#' + textarea_id).val();
					}
					content = content.replace(/<[\/]{0,1}(p)[^><]*>/ig, "");
					$(".cc-message").html(content + '<a href="#" class="cc-link cookie-policy">' + link + '</a>');
					// Update HTML view textarea (that is the one used to send the data to server).
				});
			}
		}

    }, 1500);

    $(document).on('change', 'select[name=cmplz_static]', function () {
        cmplz_cookie_warning();
    });

    $(document).on('change', 'select[name=cmplz_position]', function () {
        cmplz_apply_style();
        cmplz_cookie_warning();
    });

    $(document).on('change', 'select[name=cmplz_theme]', function () {
        cmplz_cookie_warning();
    });

	$(document).on('change', 'select[name=cmplz_color_scheme]', function () {
		var selected_scheme = $('select[name=cmplz_color_scheme]').val();
		var colors = color_schemes[selected_scheme];
		for (var key in colors) {
			if (colors.hasOwnProperty(key)) {
				var field = $('input[name=cmplz_'+key+']');
				field.val(colors[key]);
				field.closest('.cmplz-field').find('.wp-color-result').css({"background-color": colors[key]});

				//also try select
				var select = $('select[name=cmplz_'+key+']');
				select.val(colors[key]);
			}
		}
		cmplz_apply_style();
		cmplz_cookie_warning();
	});

	$(document).on('change', 'select[name=cmplz_checkbox_style]', function () {
		ccCheckboxes = '';
		cmplz_cookie_warning();
	});

    $(document).on('keyup', '#cmplz_custom_csseditor', function () {
        cmplz_apply_style();
    });

    $(document).on('change', 'input[name=cmplz_use_custom_cookie_css]', function () {
        cmplz_apply_style();
    });

    $(document).on('change', 'input[name=cmplz_hide_preview]', function () {
        cmplz_cookie_warning();
    });

    $(document).on('change', 'input[name=cmplz_use_box_shadow]', function () {
        cmplz_cookie_warning();
    });

    //cmplz_apply_style();
    function cmplz_apply_style(){
		$("#cmplz-cookie-inline-css").remove();
        var checked = $('input[name=cmplz_use_custom_cookie_css]').is(':checked');
        var ccSliderBackgroundColor = $('input[name="cmplz_colorpalette_toggles[background]"]').val();
        var ccSliderBulletColor = $('input[name="cmplz_colorpalette_toggles[bullet]"]').val();
        var ccSliderBackgroundColorInactive = $('input[name="cmplz_colorpalette_toggles[inactive]"]').val();

		var ccAcceptAllTextColor = $('input[name="cmplz_colorpalette_button_accept[text]"]').val();
		var ccAcceptAllBackgroundcolor = $('input[name="cmplz_colorpalette_button_accept[background]"]').val();
		var ccAcceptAllHovercolor = getHoverColour(ccAcceptAllBackgroundcolor);
		var ccAcceptAllBorderColor = $('input[name="cmplz_colorpalette_button_accept[border]"]').val();

		var ccFunctionalTextColor = $('input[name="cmplz_colorpalette_button_deny[text]"]').val();
		var ccFuntionalBackgroundcolor = $('input[name="cmplz_colorpalette_button_deny[background]"]').val();
		var ccFuntionalHovercolor = getHoverColour(ccFuntionalBackgroundcolor);
		var ccFuntionalBorderColor = $('input[name="cmplz_colorpalette_button_deny[border]"]').val();

        var ccSettingsTextColor = $('input[name="cmplz_colorpalette_button_settings[text]"]').val();
        var ccSettingsBackgroundcolor = $('input[name="cmplz_colorpalette_button_settings[background]"]').val();
        var ccSettingsHovercolor = getHoverColour(ccSettingsBackgroundcolor);
        var ccSettingsBorderColor = $('input[name="cmplz_colorpalette_button_settings[border]"]').val();

		var ccBannerWidth = $('input[name=cmplz_banner_width]').val();
		var css = '';
		//we use importants here, not because it's needed in the front-end, but because it's needed for the dynamic changes in the wysiwyg editor
		css += '.cc-compliance .cc-btn.cc-accept-all{color:' + ccAcceptAllTextColor + '!important;background-color:' + ccAcceptAllBackgroundcolor + ';border-color:' + ccAcceptAllBorderColor + '!important}';
		css += '.cc-compliance .cc-btn.cc-accept-all:hover{background-color:' + ccAcceptAllHovercolor + '!important}';

        css += '.cc-compliance .cc-btn.cc-allow{color:' + ccAcceptAllTextColor + '!important;background-color:' + ccAcceptAllBackgroundcolor + ';border-color:' + ccAcceptAllBorderColor + '!important}';
        css += '.cc-compliance .cc-btn.cc-allow:hover{background-color:' + ccAcceptAllHovercolor + '!important}';

		css += '.cc-compliance .cc-btn.cc-dismiss{color:' + ccFunctionalTextColor + '!important;background-color:' + ccFuntionalBackgroundcolor + '!important;border-color:' + ccFuntionalBorderColor + '!important}';
        css += '.cc-compliance .cc-btn.cc-dismiss:hover{background-color:' + ccFuntionalHovercolor + '!important}';

        css += '.cc-compliance .cc-btn.cc-show-settings{color:' + ccSettingsTextColor + '!important;background-color:' + ccSettingsBackgroundcolor + '!important;border-color:' + ccSettingsBorderColor + '!important}';
        css += '.cc-compliance .cc-btn.cc-show-settings:hover{background-color:' + ccSettingsHovercolor + '!important}';

        css += '.cc-compliance .cc-btn.cc-save-settings{color:' + ccSettingsTextColor + '!important;background-color:' + ccSettingsBackgroundcolor + '!important;border-color:' + ccSettingsBorderColor + '!important}';
        css += '.cc-compliance .cc-btn.cc-save-settings:hover{background-color:' + ccSettingsHovercolor + '!important}';

		css += ".cmplz-slider-checkbox input:checked + .cmplz-slider {background-color: "+ccSliderBackgroundColor+"!important}";
		css += '.cmplz-slider-checkbox .cmplz-slider {background-color: '+ccSliderBackgroundColorInactive+'!important;}';
		css += ".cmplz-slider-checkbox input:focus + .cmplz-slider {}";
		css += ".cmplz-slider-checkbox .cmplz-slider:before {background-color: "+ccSliderBulletColor+"!important;}.cmplz-slider-checkbox .cmplz-slider-na:before {color:"+ccSliderBulletColor+"!important;}";
		if ( ccBannerWidth !== 468 ) {
			css += "#cc-window.cc-floating {max-width:" + ccBannerWidth + "px;}";
		}
		if (checked){
			css +=$('textarea[name="cmplz_custom_css"]').val();
        }
		$('<style id="cmplz-cookie-inline-css">')
			.prop("type", "text/css")
			.html(css).appendTo("head");
    }

    $(document).on('change', 'select[name=cmplz_use_categories]', function () {
        cmplz_cookie_warning();
    });

    $(document).on('change', 'select[name=cmplz_use_categories_optinstats]', function () {
        cmplz_cookie_warning();
    });

    $(document).on('change', 'select[name=cmplz_animation]', function () {
        cmplz_apply_style();
        cmplz_cookie_warning();
    });

    $(document).on('change', 'input[name=cmplz_hide_revoke]', function () {
        cmplz_cookie_warning();
    });

    $(document).on('change', 'input[name=cmplz_soft_cookiewall]', function () {
        cmplz_cookie_warning();
    });

    $(document).on('change', 'input[name=cmplz_use_tagmanager_categories]', function () {
        cmplz_cookie_warning();
    });

    $(document).on('change', 'input[type=number].cmplz-border-radius', function () {
        cmplz_cookie_warning();
    });
	$(document).on('keyup', 'input[type=number].cmplz-border-radius', function () {
		cmplz_cookie_warning();
	});
	$(document).on('change', 'input[type=number].cmplz-border-width', function () {
		cmplz_cookie_warning();
	});

    $(document).on('keyup', 'input[type=number].cmplz-border-width', function () {
        cmplz_cookie_warning();
    });

    /**
     * if both EU and UK are active, we might have some double input fields. Remove all double fields from the form to
     * prevent settings not being saved
     */

    $(document).on('submit', '#CMPLZ_COOKIEBANNER',function(e){
        var inputs = $('#CMPLZ_COOKIEBANNER :input');
        if ($('#optin').length && $('#optinstats').length){
            inputs.each(function(){
                var name = $(this).attr("name");
                if (typeof name !== 'undefined' && name.indexOf('cmplz_')!==-1){
					if ($(this).closest('.field-group').is(":hidden")){
                        $(this).remove();
                    }
                }
            });
        }
    });

    var ccCheckboxes = '';
    cmplz_apply_style();
    cmplz_cookie_warning();
    function cmplz_cookie_warning(){
    	if (ccCheckboxes.length === 0) {
			$.ajax({
				type: 'GET',
				url: complianz_admin.url,
				dataType: 'json',
				data: ({
					id: $('input[name=cmplz_banner_id]').val(),
					checkbox_style: $('select[name=cmplz_checkbox_style]').val(),
					consenttype: ccConsentType,
					action: 'cmplz_get_dynamic_categories_ajax'
				}),
				success: function (response) {
					ccCheckboxes = response;
					cmplz_cookie_warning_render();
				}
			});
		} else {
			cmplz_cookie_warning_render();
		}
	}

    function cmplz_cookie_warning_render() {
        if (ccConsentType === 'tcf' || $('input[name=cmplz_hide_preview]').is(':checked')) {
            $('#cc-window').hide();
            return;
        }

        var ccDismiss;
		complianz.tcf_regions = ['eu', 'uk', 'ca'];
        if (ccName) {
            ccName.fadeOut();
            ccName.destroy();
        }

		var event = new CustomEvent('wp_consent_type_defined');
		document.dispatchEvent(event);

        var ccCategories = 'no';

        if (ccConsentType === 'optin') {
			ccCategories = $('select[name=cmplz_use_categories]').val();
		} else {
			ccCategories = $('select[name=cmplz_use_categories_optinstats]').val();
		}

        if (settingConsentType === 'optin'){
            ccDismiss = $('input[name=cmplz_dismiss]').val();
        } else {
            ccDismiss = $('input[name=cmplz_accept_informational]').val();
		}

        var ccHideRevoke = $('input[name=cmplz_hide_revoke]').is(':checked');
        if (ccHideRevoke) {
            ccHideRevoke = 'cc-hidden';
        } else {
            ccHideRevoke = '';
        }

        var ccHeader =  $('input[name=cmplz_header]').val();
        var ccMessage = $('textarea[name=cmplz_message_'+settingConsentType + ']').val();
		var ccAllow = $('input[name=cmplz_accept]').val();
		if ($('input[name=cmplz_readmore_'+settingConsentType + '_dnsmpi]').length){
			var ccLink = $('input[name=cmplz_readmore_'+settingConsentType + '_dnsmpi]').val();
		} else {
			var ccLink = $('input[name=cmplz_readmore_'+settingConsentType + ']').val();
		}
        var ccStatic = false;

		var ccBorderColor = $('input[name="cmplz_colorpalette_background[border]"]').val();
        var ccBorderRadiusType = $('input[name="cmplz_colorpalette_border_radius[type]"]').val() == 'px' ? 'px' : '%';
        var ccBorderRadius =
            $('input[name="cmplz_colorpalette_border_radius[top]"]').val() + ccBorderRadiusType + ' ' +
            $('input[name="cmplz_colorpalette_border_radius[right]"]').val() + ccBorderRadiusType + ' ' +
            $('input[name="cmplz_colorpalette_border_radius[bottom]"]').val() + ccBorderRadiusType + ' ' +
            $('input[name="cmplz_colorpalette_border_radius[left]"]').val() + ccBorderRadiusType;

        var ccBorderWidth =
            $('input[name="cmplz_border_width[top]"]').val() + 'px ' +
            $('input[name="cmplz_border_width[right]"]').val() + 'px ' +
            $('input[name="cmplz_border_width[bottom]"]').val() + 'px ' +
            $('input[name="cmplz_border_width[left]"]').val() + 'px';

        var ccPosition = $('select[name=cmplz_position]').val();
        var ccAnimation = $('select[name=cmplz_animation]').val();

        var ccType = 'opt-in';
        var ccPrivacyLink = '';

        if (ccConsentType==='optout') {
            ccType = 'opt-out';
            ccCategories = 'no';
            if ($('input[name=cmplz_readmore_privacy]').length)
                ccPrivacyLink = '<span class="cc-divider">&nbsp;-&nbsp;</span><a class="cc-link privacy-policy" href="#">' + $('input[name=cmplz_readmore_privacy]').val() + '</a>';
        }

		if ( ccConsentType === 'optin' && $('input[name=cmplz_impressum_required]').val() == 1 ){
				ccPrivacyLink = '<span class="cc-divider">&nbsp;-&nbsp;</span><a class="cc-link impressum" href="#">' + $('input[name=cmplz_readmore_impressum]').val() + '</a>';
		}

        var ccTheme = $('select[name=cmplz_theme]').val();
        var ccLayout = 'basic';
        var ccPopupTextColor = $('input[name="cmplz_colorpalette_text[color]"]').val();
        var ccPopupLinkColor = $('input[name="cmplz_colorpalette_text[hyperlink]"]').val();
        var ccButtonBackgroundColor = $('input[name="cmplz_colorpalette_button_accept[background]"]').val();
        var ccButtonTextColor = $('input[name="cmplz_colorpalette_button_accept[text]"]').val();
        var ccSavePreferences = $('input[name=cmplz_save_preferences]').val();
        var ccViewPreferences = $('input[name=cmplz_view_preferences]').val();
        var ccAcceptAll = $('input[name=cmplz_accept_all]').val();
        var ccRevokeText = $('input[name=cmplz_revoke]').val();
		var ccUseTagManagerCategories = $('textarea[name=cmplz_tagmanager_categories]').length;
        var ccBorderButtonsRadiusType = $('input[name="cmplz_buttons_border_radius[type]"]').val() == 'px' ? 'px' : '%';
        var ccBorderButtonsRadius =
            $('input[name="cmplz_buttons_border_radius[top]"]').val() + ccBorderButtonsRadiusType + ' ' +
            $('input[name="cmplz_buttons_border_radius[right]"]').val() + ccBorderButtonsRadiusType + ' ' +
            $('input[name="cmplz_buttons_border_radius[bottom]"]').val() + ccBorderButtonsRadiusType + ' ' +
            $('input[name="cmplz_buttons_border_radius[left]"]').val() + ccBorderButtonsRadiusType;

        var ccBoxShadow = $('input[name=cmplz_use_box_shadow]').is(':checked') ? '0 0 10px rgba(0, 0, 0, .4)' : '';

        var ccCategoryFunctional = '';
        var ccCategoryAll = '';
        if (ccPosition==='bottom-left'){
            $('.cmplz-cookiebanner-save-button').css({"float": "right"});
        } else {
            $('.cmplz-cookiebanner-save-button').css({"float": "left"});
        }

        //small hack to prevent the admin menu on top of the banner in case of push down
		if (ccPosition === 'static') {
			$('#adminmenuback').css({"position": 'absolute'});
		}

        if ( ccCategories !== 'no' ) {
            ccCategoryFunctional = $('input[name=cmplz_category_functional]').val();
            ccCategoryAll = $('input[name=cmplz_category_all]').val();
            ccType = 'categories';
            ccLayout = 'categories-layout';

			var ccHasStatsCategory;
			if (!ccUseTagManagerCategories) {
				if (ccConsentType === 'optin') {
					ccHasStatsCategory = $('input[name=cmplz_cookie_warning_required_stats]').val();
				} else if (ccConsentType === 'optinstats') {
					ccHasStatsCategory = true;
				}
			}

			if (!ccHasStatsCategory){
				ccCheckboxes =  cmplzRemoveStatisticsCategory(ccCheckboxes, ccCategories, ccConsentType);
			}
		}

        if (ccPosition === 'static') {
            ccStatic = true;
            ccPosition = 'top';
        }

        if (ccTheme === 'edgeless') {
             ccBorderColor = false;
        }

		var save_button = '<a href="#" role="button" class="cc-btn cc-save-settings">{{save_preferences}}</a>';
         if (ccCategories === 'hidden' ) {
			save_button = '<a href="#" role="button" class="cc-btn cc-dismiss">{{dismiss}}</a><a role="button" href="#" class="cc-btn cc-save cc-show-settings">{{settings}}</a>';
		 }
		if (ccCategories === 'visible' || ccCategories === 'hidden' ) {
			save_button = '<a href="#" role="button" class="cc-btn cc-accept-all">{{accept_all}}</a>'+save_button;
		}

		var dismiss_button = '<a href="#" role="button" class="cc-btn cc-dismiss">{{dismiss}}</a>';
		var allow_button = '<a href="#" role="button" class="cc-btn cc-save cc-allow">{{allow}}</a>';
		if (settingConsentType === 'optout' ) {
			dismiss_button ='<a href="#" role="button" class="cc-btn cc-allow">{{dismiss}}</a>';
		}
        var ccStatus;
        window.cookieconsent.initialise({
            cookie: {
                name: 'complianz_config',
                expiryDays: 1
            },
            "revokeBtn": '<button type="button" class="cc-revoke ' + ccHideRevoke + ' {{classes}}">' + ccRevokeText + '</button>',
            "palette": {
                "popup": {
                    "background": $('input[name="cmplz_colorpalette_background[color]"]').val(),
                    "text": ccPopupTextColor,
                    "link": ccPopupLinkColor,
                    "border": ccBorderColor,
                    "borderwidth": ccBorderWidth,
                    "borderradius": ccBorderRadius,
                    "boxshadow": ccBoxShadow,
                },
                "button": {
                    "background": ccButtonBackgroundColor,
                    "text": ccButtonTextColor,
                    "border": ccBorderColor,
                    "borderradius": ccBorderButtonsRadius,
                }
            },
            "layout": ccLayout,
            "layouts": {
                'categories-layout': '{{messagelink}}{{categories-checkboxes}}{{compliance}}',
				'compliance': '{{messagelink}}{{compliance}}',
			},
			"compliance": {
				'categories': '<div class="cc-compliance cc-highlight">{{save}}</div>',
			},
            "elements": {
                "categories-checkboxes": ccCheckboxes,
				"save": save_button,
				"allow": allow_button,
				"dismiss": dismiss_button,
				"messagelink": '<div class="cc-header">{{header}}</div><div id="cookieconsent:desc" class="cc-message">{{message}} <a class="cc-link cookie-policy" href="{{href}}" target="_blank">{{link}}</a>' + ccPrivacyLink + '</div>',
            },
            "type": ccType,
            "theme": ccTheme,
            "static": ccStatic,
            "position": ccPosition,
            "animation": ccAnimation,
            "content": {
                "save_preferences" : ccSavePreferences,
                "header": ccHeader,
                "message": ccMessage,
                "dismiss": ccDismiss,
                "deny": '',
                "allow": ccAllow,
                "link": ccLink,
                "href": '#',
                "categoryfunctional": ccCategoryFunctional,
                "categoryall": ccCategoryAll,
				 "accept_all": ccAcceptAll,
				 "settings": ccViewPreferences,
            },
            onInitialise: function (status) {
                ccStatus = status;
            }
        }, function (popup) {
            ccName = popup;
            var ccSoftCookieWall = $('input[name=cmplz_soft_cookiewall]').is(':checked');
            if (ccSoftCookieWall && !ccCookieWallShownBefore){
                //disabled, because it prevents editing options
                $(".cc-window").wrap("<div class='cmplz-soft-cookiewall preview'></div>" );
                //remove after 2 seconds, otherwise you can't save anything
				setTimeout(function () {
					$('.cmplz-soft-cookiewall').removeClass('cmplz-soft-cookiewall');
					ccCookieWallShownBefore = true;
				}, 3000);
            }
			$('.cc-window').addClass('cmplz-categories-'+ccCategories);

			if (ccCategories === 'hidden' && !ccCatsOpenedByUser) {
				$('.cmplz-categories-wrap').hide();
			}

			var event = new CustomEvent('cmplzCookieWarningLoaded', { detail: complianz.region });
			document.dispatchEvent(event);

			ccName.open();
			//check for TCF integration
			//we need to reload if it exists
			if ($('#cmplz-tcf-js').length) {
				var src = $('#cmplz-tcf-js').attr('src');
				$.getScript(src);
			}
		});

        $(document).on('click', '.cc-save-settings', function(){
        	if ($('#cmplz_marketing').is(":checked")) {
                ccName.setStatus(cookieconsent.status.allow);
            } else {
                ccName.setStatus(cookieconsent.status.dismiss);
            }
			var catsContainer = $('.cmplz-categories-wrap');
			catsContainer.hide();
			var showSettingsBtn = $(".cc-save-settings");
			showSettingsBtn.addClass('cc-show-settings');
			showSettingsBtn.html($('input[name=cmplz_view_preferences]').val());
			showSettingsBtn.removeClass('cc-save-settings');

            $('.cc-window').addClass('cmplz-dismiss');
            ccName.close();
            $('.cc-revoke').fadeIn();
        });

		$(document).on('click', '.cc-dismiss', function(){
			$('.cc-window').addClass('cmplz-dismiss');
			ccName.close();
			$('.cc-revoke').fadeIn();
		});

		$(document).on('click', '.cc-accept-all', function (event) {
			$('.cc-window').addClass('cmplz-dismiss');
			ccName.close();
			$('.cc-revoke').fadeIn();
		});

		$(document).on('click', '.cc-revoke', function (event) {
			$('.cc-window').removeClass('cmplz-dismiss');
		});

		$(document).on('click', '.cc-show-settings', function(e){
			var catsContainer = $('.cmplz-categories-wrap');
			if (!catsContainer.is(":visible")){
				catsContainer.fadeIn(1600);
				var showSettingsBtn = $(".cc-show-settings");
				showSettingsBtn.html($('input[name=cmplz_save_preferences]').val());
				showSettingsBtn.removeClass('cc-show-settings');
				showSettingsBtn.addClass('cc-save-settings');
			}
			return false;
		});

        if (ccPosition === 'top') {
            $('.cc-window').css({'top': '30px'});
        }

        //make it float over the wp menu
        if (ccPosition === 'static') {
            $('.cc-grower').css('z-index', 10);
            $('.cc-grower').css('position', 'relative');
        }
    }



	/**
	 * For UK, cats are always needed. If this user is EU, and does not need consent for stats, we can remove the stats category
	 * @param categories
	 * @param category_type
	 * @param consent_type
	 * @returns {*}
	 */

	function cmplzRemoveStatisticsCategory(categories, category_type, consent_type) {
		if (category_type !== 'no' && consent_type === 'optin') {
			return categories.replace(/(.*)(<div class="cmplz-categories-wrap"><label.*?>.*?<input.*?class=".*?cmplz_statistics.*?<\/label><\/div>)(.*)/g, function (a, b, c, d) {
				return b + d;
			});

		}
		return categories;
	}


	function getHoverColour(hex) {
		if (typeof hex === 'undefined' ) return hex;

		if (hex[0] === '#') {
			hex = hex.substr(1);
		}
		if (hex.length === 3) {
			hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
		}

		// for black buttons
		if (hex === '000000') {
			return '#222';
		}
		var num = parseInt(hex, 16),
			amt = 38,
			R = (num >> 16) + amt,
			B = (num >> 8 & 0x00FF) + amt,
			G = (num & 0x0000FF) + amt;
		var newColour = (0x1000000 + (R<255?R<1?0:R:255)*0x10000 + (B<255?B<1?0:B:255)*0x100 + (G<255?G<1?0:G:255)).toString(16).slice(1);
		return '#'+newColour;
	}

    $(document).on('click', '.cmplz-border-input-type-pixel', function() {
        var hidden_field = $(this).closest('.cmplz-border-input-type-wrap');
        hidden_field.find('.cmplz-border-input-type').val('px');
        hidden_field.find('.cmplz-border-input-type-percent').addClass('cmplz-grey');
        $(this).removeClass('cmplz-grey');
        cmplz_cookie_warning();
    });

    $(document).on('click', '.cmplz-border-input-type-percent', function() {
        var hidden_field = $(this).closest('.cmplz-border-input-type-wrap');
        hidden_field.find('.cmplz-border-input-type').val('%');
        hidden_field.find('.cmplz-border-input-type-pixel').addClass('cmplz-grey');
        $(this).removeClass('cmplz-grey');
        cmplz_cookie_warning();
    });

    $(document).on('click', '.cmplz-step-header', function() {
        $('.cmplz-step-header').removeClass('active');
        $(this).addClass('active');
    });

});

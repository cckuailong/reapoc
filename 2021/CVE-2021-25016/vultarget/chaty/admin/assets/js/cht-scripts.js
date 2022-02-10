/**
 * easyModal.js v1.3.2
 * A minimal jQuery modal that works with your CSS.
 * Author: Flavius Matis - http://flaviusmatis.github.com/
 * URL: https://github.com/flaviusmatis/easyModal.js
 *
 * Copyright 2012, Flavius Matis
 * Released under the MIT license.
 * http://flaviusmatis.github.com/license.html
 */

/* jslint browser: true */
/* global jQuery */

jQuery( function($) {
    'use strict';
    var chatyError;
    var forceSubmit = false;
    var whatsappStatus = false;
    var phoneStatus = false;
    var fbStatus = false;
    var smsStatus = false;
    var viberStatus = false;
    var phoneNumberStatus = false;
    function checkForDevices() {
        $(".chaty-popup").hide();
        if($("#cht-form .js-chanel-desktop").length == 0 || $("#cht-form .js-chanel-mobile").length == 0) {
            $("#no-device-popup").show();
            return false;
        } else if($("#cht-form .js-chanel-desktop:checked").length == 0 && $("#cht-form .js-chanel-mobile:checked").length == 0) {
            $("#device-popup").show();
            return false;
        } else {
            var inputError = 0;
            $("#channels-selected-list > li:not(#chaty-social-close)").find(".channels__input").each(function(){
                if(jQuery.trim($(this).val()) == "") {
                    inputError++;
                }
            });
            if(inputError == $("#channels-selected-list > li:not(#chaty-social-close)").find(".channels__input").length) {
                if(!$("#chaty-social-Contact_Us").length) {
                    $("#no-device-value").show();
                    return false;
                }
            }
        }
        return checkForTriggers();
    }

    function checkForTriggers() {
        $(".chaty-popup").hide();
        if(!$("#trigger_on_time").is(":checked") && !$("#chaty_trigger_on_exit").is(":checked") && !$("#chaty_trigger_on_scroll").is(":checked")) {
            $("#trigger-popup").show();
            return false;
        }
        return checkForStatus();
    }

    function checkForStatus() {
        $(".chaty-popup").hide();
        if(!$(".cht_active").is(":checked")) {
            $("#status-popup").show();
            return false;
        }
        forceSubmit = true;
        $("#cht-form").trigger("submit");
        return true;
    }
    function checkPreSettings() {
        if(!whatsappStatus) {
            whatsappStatus = true;
            var phoneNumberReg = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
            if ($("#cht-form #Whatsapp").length && $("#cht-form #Whatsapp").val() != "") {
                var InputVal = jQuery.trim($("#cht-form #Whatsapp").val());
                chatyError = check_for_number_chaty(InputVal, "Whatsapp");
                if(chatyError) {
                    $("#custom-message-popup .chaty-popup-header").text("Whatsapp number is not valid");
                    $("#custom-message-popup .chaty-popup-body").text("Seems like the WhatsApp number you're trying to enter isn't in the right syntax. Would you like to publish it anyway?");
                    $("#custom-message-popup").show();
                    return false;
                }
            }
        } else if(!phoneStatus) {
            phoneStatus = true;
            if ($("#cht-form #Phone").length && $("#cht-form #Phone").val() != "") {
                var InputVal = jQuery.trim($("#cht-form #Phone").val());
                chatyError = check_for_number_chaty(InputVal, "Phone");
                if(chatyError) {
                    $("#custom-message-popup .chaty-popup-header").text("Phone number is not valid");
                    $("#custom-message-popup .chaty-popup-body").text("Seems like the phone number you're trying to enter isn't in the right syntax. Would you like to publish it anyway?");
                    $("#custom-message-popup").show();
                    return false;
                }
            }
        } else if(!fbStatus) {
            fbStatus = true;
            if ($("#cht-form #Facebook_Messenger").length && $("#cht-form #Facebook_Messenger").val() != "") {
                var faceBookMeReg = /(?:http:\/\/)?m\.me\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*([\w\-]*)/;
                var faceBookReg = /(?:http:\/\/)?facebook\.com\/(?:(?:\w)*#!\/)?(?:pages\/)?(?:[\w\-]*\/)*([\w\-]*)/;
                var InputVal = jQuery.trim($("#Facebook_Messenger").val());
                $("#cht-form #Facebook_Messenger").val(InputVal);
                if (!faceBookReg.test(InputVal) && !faceBookMeReg.test(InputVal)) {
                    $("#custom-message-popup .chaty-popup-header").text("Facebook page's URL is not valid");
                    $("#custom-message-popup .chaty-popup-body").text("Please make sure your Facebook page's URL looks like, <br/>https://m.me/YOURPAGE");
                    $("#custom-message-popup").show();
                    return false;
                }
            }
        } else if(!smsStatus) {
            smsStatus = true;
            if ($("#cht-form #SMS").length && $("#cht-form #SMS").val() != "") {
                var InputVal = jQuery.trim($("#cht-form #SMS").val());
                chatyError = check_for_number_chaty(InputVal, "SMS");
                if(chatyError) {
                    $("#custom-message-popup .chaty-popup-header").text("SMS number is not valid");
                    $("#custom-message-popup .chaty-popup-body").text("Seems like the SMS number you're trying to enter isn't in the right syntax. Would you like to publish it anyway?");
                    $("#custom-message-popup").show();
                    return false;
                }
            }
        } else if(!viberStatus) {
            viberStatus = true;
            if ($("#cht-form #Viber").length && $("#cht-form #Viber").val() != "") {
                var InputVal = jQuery.trim($("#cht-form #Viber").val());
                chatyError = check_for_number_chaty(InputVal, "Viber");
                if(chatyError) {
                    $("#custom-message-popup .chaty-popup-header").text("Viber number is not valid");
                    $("#custom-message-popup .chaty-popup-body").text("Seems like the Viber number you're trying to enter isn't in the right syntax. Would you like to publish it anyway?");
                    $("#custom-message-popup").show();
                    return false;
                }
            }
        } else if(!phoneNumberStatus) {
            phoneNumberStatus = true;
            if($("#channels-selected-list .phone-number").length) {
                $("#channels-selected-list .phone-number").each(function(){
                    if(jQuery.trim($(this).val()) != '') {
                        var inputLen = (jQuery.trim($(this).val())).length;
                        if(inputLen > 13) {
                            $("#custom-message-popup .chaty-popup-header").text($(this).data("label")+" number is not valid");
                            $("#custom-message-popup .chaty-popup-body").text("Seems like the "+$(this).data("label")+" number you're trying to enter isn't valid. Would you like to publish it anyway?");
                            $("#custom-message-popup").show();
                            return false;
                        }
                    }
                });
            }
        }
        return checkForDevices();
    }
    $(document).ready(function () {

        jQuery(document).on("click", "#update-chaty-traffic-source-rule", function(e){
            jQuery(".traffic-options-box").addClass("active");
            jQuery("#chaty_traffic_source").val("yes");
        });
        jQuery(document).on("click", "#remove-traffic-rules", function(e){
            jQuery(".traffic-options-box").removeClass("active");
            jQuery("#chaty_traffic_source").val("no");
        });
        jQuery(document).on("click", ".remove-traffic-option", function(e){
            jQuery(this).closest(".custom-traffic-rule").remove();
        });

        $(document).on("click", ".chaty-switch-toggle", function(){
            setTimeout(function(){
                $(".chaty-field-setting").each(function(){
                    if($(this).is(":checked")) {
                        $(this).closest(".field-setting-col").find(".field-settings").addClass("active");
                    } else {
                        $(this).closest(".field-setting-col").find(".field-settings").removeClass("active");
                    }
                });
            },100);
        });

        $(document).on("change", ".chaty-close_form_after-setting", function(){
            setTimeout(function(){
                $(".chaty-close_form_after-setting").each(function(){
                    if($(this).is(":checked")) {
                        $(this).closest(".form-field-setting-col").find(".close_form_after-settings").addClass("active");
                    } else {
                        $(this).closest(".form-field-setting-col").find(".close_form_after-settings").removeClass("active");
                    }
                });
            },100);
        });

        if($("#channel_input_Whatsapp").length) {
            cht_settings.channel_settings['Whatsapp'] = document.querySelector("#channel_input_Whatsapp");
            window.intlTelInput(cht_settings.channel_settings['Whatsapp'], {
                dropdownContainer: document.body,
                formatOnDisplay: true,
                hiddenInput: "full_number",
                initialCountry: "auto",
                nationalMode: false,
                utilsScript: cht_settings.plugin_url+"admin/assets/js/utils.js",
            });
        }

        if($(".custom-channel-Whatsapp:not(#channel_input_Whatsapp)").length) {
            $(".custom-channel-Whatsapp:not(#channel_input_Whatsapp)").each(function(){
                var dataSlag = $(this).closest("li.chaty-channel").data("id");
                if(dataSlag != undefined) {
                    if($("#channel_input_"+dataSlag).length) {
                        cht_settings.channel_settings[dataSlag] = document.querySelector("#channel_input_"+dataSlag);
                        window.intlTelInput(cht_settings.channel_settings[dataSlag], {
                            dropdownContainer: document.body,
                            formatOnDisplay: true,
                            hiddenInput: "full_number",
                            initialCountry: "auto",
                            nationalMode: false,
                            utilsScript: cht_settings.plugin_url + "admin/assets/js/utils.js",
                        });
                    }
                }
            });
        }

        $(document).on("change", ".chaty-redirect-setting", function(){
            setTimeout(function(){
                $(".chaty-redirect-setting").each(function(){
                    if($(this).is(":checked")) {
                        $(this).closest(".form-field-setting-col").find(".redirect_action-settings").addClass("active");
                    } else {
                        $(this).closest(".form-field-setting-col").find(".redirect_action-settings").removeClass("active");
                    }
                });
            },100);
        });

        $(document).on("click", ".email-setting", function(){
            setTimeout(function(){
                $(".email-setting-field").each(function(){
                    if($(this).is(":checked")) {
                        $(this).closest(".form-field-setting-col").find(".email-settings").addClass("active");
                    } else {
                        $(this).closest(".form-field-setting-col").find(".email-settings").removeClass("active");
                    }
                });
            },100);
        });
        $(document).on("click", ".chaty-embedded-window", function(){
            setTimeout(function(){
                $(".embedded_window-checkbox").each(function(){
                    if($(this).is(":checked")) {
                        $(this).closest("li.chaty-channel").find(".whatsapp-welcome-message").addClass("active");
                    } else {
                        $(this).closest("li.chaty-channel").find(".whatsapp-welcome-message").removeClass("active");
                    }
                });
            },300);
        });

        jQuery("input[name='switchPreview']:checked").trigger("change");

        $(document).on("change", "#cht-form input", function(){
            whatsappStatus = false;
            phoneStatus = false;
            fbStatus = false;
            smsStatus = false;
            viberStatus = false;
            phoneNumberStatus = false;
        });
        $("#cht-form").on("submit", function () {
            if(forceSubmit) {
                return true;
            }
            set_social_channel_order();
            var errorCount = 0;
            if ($("#chaty-page-options .cht-required").length) {
                $("#chaty-page-options .cht-required").each(function () {
                    if (jQuery.trim($(this).val()) == "") {
                        $(this).addClass("cht-input-error");
                        errorCount++;
                    }
                });
            }
            if ($(".chaty-data-and-time-rules .cht-required").length) {
                $(".chaty-data-and-time-rules .cht-required").each(function () {
                    if (jQuery.trim($(this).val()) == "") {
                        $(this).addClass("cht-input-error");
                        errorCount++;
                    }
                });
            }
            if(errorCount == 0) {
                return checkPreSettings();
            } else {
                $(".cht-input-error:first").focus();
                return false;
            }
        });
        $(".close-chaty-popup-btn").on("click", function(e){
            e.stopPropagation();
            $(".chaty-popup").hide();
            if($(this).hasClass("channel-setting-btn")) {
                $("body, html").animate({
                    scrollTop: $("#channel-list").offset().top - 125
                }, 250);
            } else if($(this).hasClass("select-trigger-btn")) {
                $("body, html").animate({
                    scrollTop: $("#trigger-setting").offset().top - 50
                }, 250);
            } else if($(this).hasClass("change-status-btn")) {
                $("body, html").animate({
                    scrollTop: $("#launch-section").offset().top - 100
                }, 250);
            }
        });
        $(".chaty-popup-inner").on("click", function(e){
            e.stopPropagation();
        });
        $(".chaty-popup-outer").on("click", function(e){
            $(".chaty-popup").hide();
        });
        $(".check-for-numbers").on("click", function(){
            checkPreSettings();
        });
        $(".check-for-device").on("click", function(){
            checkForDevices();
        });
        $(".check-for-triggers").on("click", function(){
            checkForTriggers();
        });
        $(".check-for-status").on("click", function(){
            checkForStatus();
        });
        $(".change-status-and-save").on("click", function(){
            $(".cht_active").prop("checked", true);
            forceSubmit = true;
            $(".chaty-popup").hide();
            $("#cht-form").trigger("submit");
        });
        $(".status-and-save").on("click", function(){
            $(".cht_active").prop("checked", false);
            forceSubmit = true;
            $(".chaty-popup").hide();
            $("#cht-form").trigger("submit");
        });
        $(document).on("click", ".preview-section-chaty", function(e){
            e.stopPropagation();
        });
        $(document).on("click", ".preview-section-overlay", function(){
            $(".preview-help-btn").removeClass("active");
            $(".preview-section-chaty").removeClass("active");
            $(".preview-section-overlay").removeClass("active");
        });
        $(document).on("click", ".preview-help-btn", function(e){
            e.preventDefault();
            if($(this).hasClass("active")) {
                $(this).removeClass("active");
                $(".preview-section-chaty").removeClass("active");
                $(".preview-section-overlay").removeClass("active");
            } else {
                $(this).addClass("active");
                $(".preview-section-chaty").addClass("active");
                $(".preview-section-overlay").addClass("active");
            }
            return false;
        });
        jQuery(document).on("click", "#create-date-rule", function(e){
            jQuery("#date-schedule").addClass("active");
            jQuery("#cht_date_rules").val("yes");
        });
        jQuery(document).on("click", "#remove-date-rule", function(e){
            jQuery("#date-schedule").removeClass("active");
            jQuery("#cht_date_rules").val("no");
        });
    });
});

function check_for_number_chaty(phoneNumber, validationFor) {
    if (phoneNumber != "") {
        if (phoneNumber[0] == "+") {
            phoneNumber = phoneNumber.substr(1, phoneNumber.length)
        }
        if (validationFor == "Phone") {
            if (phoneNumber[0] == "*") {
                phoneNumber = phoneNumber.substr(1, phoneNumber.length)
            }
        }
        if (isNaN(phoneNumber)) {
            return true;
        }
    }
    return false;
}

(function ($) {
    var closeAction = 0;

    jQuery(window).on('popstate', function(event) {
        window.onbeforeunload = null;
        if(window.history && window.history.pushState) {
            window.history.back();
        }
    });

    jQuery(document).ready(function () {
        if(!jQuery(".chaty-table").length) {
            jQuery('body input, body .icon, body textarea, body .btn-cancel:not(.close-btn-set) ').on("click", function (event) {
                window.onbeforeunload = function (e) {
                    e = e || window.event;
                    e.preventDefault = true;
                    e.cancelBubble = true;
                    e.returnValue = 'Your beautiful goodbye message';
                };
            });
        }

        jQuery(document).on('submit', 'form', function (event) {
            window.onbeforeunload = null;
        });

        jQuery(document).on('change', '.channel-select-input', function (event) {
            var selChannel = $(this).closest("li").attr("data-id");
            jQuery.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    social: jQuery(this).val(),
                    channel: selChannel,
                    action: 'get_chaty_settings'
                },
                success: function (response) {
                    if(response.status == 1) {
                        if(response.data.slug == "Whatsapp") {
                            if($("#channel_input_"+response.channel).length) {
                                cht_settings.channel_settings[response.channel] = document.querySelector("#channel_input_"+response.channel);
                                window.intlTelInput(cht_settings.channel_settings[response.channel], {
                                    dropdownContainer: document.body,
                                    formatOnDisplay: true,
                                    hiddenInput: "full_number",
                                    initialCountry: "auto",
                                    nationalMode: false,
                                    utilsScript: cht_settings.plugin_url + "admin/assets/js/utils.js",
                                });
                            }
                        } else {
                            $("#chaty-social-"+response.channel+" .channels__input-box").html("<input type='text' class='channels__input' name='cht_social_"+response.channel+"[value]' id='channel_input_"+response.channel+"' />");
                        }
                        jQuery(".custom-icon-"+response.channel+" svg").html(response.data.svg);
                        jQuery("#chaty-social-"+response.channel).attr("data-channel", response.data.slug);
                        jQuery("#chaty-social-"+response.channel).find(".sp-preview-inner").css("background-color", response.data.color);
                        jQuery("#chaty-social-"+response.channel).find(".chaty-color-field").val(response.data.color);
                        jQuery("#chaty-social-"+response.channel).find(".channels__input").attr("placeholder", response.data.placeholder);
                        jQuery("#chaty-social-"+response.channel).find(".channel-example").text(response.data.example);
                        jQuery("#chaty-social-"+response.channel).find(".chaty-title").val(response.data.title);
                        jQuery("#chaty-social-"+response.channel).find(".icon").attr("data-title", response.data.title);
                        jQuery("#chaty-social-"+response.channel).find(".chaty-color-field").trigger("change");
                        jQuery(".help-section").html("");
                        if(response.data.help_link != "") {
                            jQuery(".help-section").html('<div class="viber-help"><a target="_blank" href="'+response.data.help_link+'">'+response.data.help_title+'</a></div>');
                        } else if(response.data.help_text != "") {
                            jQuery(".help-section").html('<div class="viber-help"><span class="help-text">'+response.data.help_text+'</span><span class="help-title">'+response.data.help_title+'</span></div>');
                        }
                    }
                }
            })
        });

        jQuery(document).on("click", "#chaty_icons_view", function(e){
            jQuery(".page-body .chaty-widget").removeClass("vertical").removeClass("horizontal");
            jQuery(".page-body .chaty-widget").addClass(jQuery(this).val());
        });

        jQuery('.upg').on("click", function (event) {
            jQuery('.valid_domain_input').val(jQuery('.valid_domain_input').val().replace(' ', ''));
            if (!/^(http(s)?:\/\/)?(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/.test(jQuery('.valid_domain_input').val())) {
                event.preventDefault();
                jQuery('.valid_domain').fadeIn().css({
                    display: 'block'
                });
            }
        });
        jQuery('.del_token').on("click", function (event) {
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'del_token',
                    nonce_code: cht_nonce_ajax.cht_nonce
                },
                success: function (bool) {
                    location.reload();
                },
                error: function (xhr, status, error) {

                }
            });
        });

        jQuery(document).on("blur", "#channels-selected-list > li:not(#chaty-social-close) .channels__input", function(){
            if(jQuery(this).hasClass("border-red") && jQuery(this).val() != "") {
                jQuery(this).removeClass("border-red");
            }
        });

        var count_click = 1000000003;
        jQuery('.show_up').on("click", function () {
            count_click += 10;
            jQuery('#upgrade-modal').css({
                'z-index': count_click,
                display: 'block',
                'margin-left': '-258px'
            });
        });

        (function colorPicker() {
            jQuery('.color-picker-btn, .color-picker-btn-close, .color-picker-custom button').on('click', function (e) {
                e.preventDefault();

                jQuery('.color-picker-box').toggle();
                jQuery('.color-picker-btn').toggle();
            });

            jQuery('.color-picker-radio input').on("change", function () {
                var $this = jQuery(this);
                jQuery('.color-picker-custom input[name="cht_custom_color"]').val('');
                jQuery('.color-picker-custom .circle').html('?').css({
                    'background-color': '#fff'
                });
                if ($this.prop('checked')) {
                    jQuery('.color-picker-radio input').prop('checked', false);
                    $this.prop('checked', true);
                    var color = $this.val();
                    var title = $this.prop('title');
                } else {
                    color = jQuery('.color-picker-custom input').val();
                    title = 'Custom';
                }

                if(color != "") {
                    var hashExists = color.indexOf("#");
                    if (hashExists == -1) {
                        color = "#" + color;
                    }
                }
                jQuery('.color-picker-btn .circle').css({backgroundColor: color});
                jQuery('.color-picker-btn .text').text(title);
                jQuery('#chaty-social-close ellipse').attr("fill", color);
            });

            jQuery('.color-picker-custom input').on("change", function () {
                jQuery('.color-picker-radio input').prop('checked', false);

                var $this = jQuery(this);

                var color = $this.val();

                if(color != "") {
                    var hashExists = color.indexOf("#");
                    if (hashExists == -1) {
                        color = "#" + color;
                    }
                }
                jQuery('.color-picker-btn .circle').css({backgroundColor: color});
                jQuery('.color-picker-btn .text').text('Custom');
                jQuery('#chaty-social-close ellipse').attr("fill", color);
            });
        }());

        (function customSelect() {
            jQuery('[name="cht_position"]').on("change", function () {
                if (jQuery('#positionCustom').prop('checked')) {
                    jQuery('#positionPro').show();
                } else {
                    jQuery('#positionPro').hide();
                }
            });
        }());


        /**
         * add Token
         */

        var AddTokenBtn = jQuery('.update_token');

        AddTokenBtn.on('click', function (e) {
            e.preventDefault();
            var token = jQuery('input[name="cht_token"]').val();

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'add_token',
                    nonce_code: cht_nonce_ajax.cht_nonce,
                    token: token
                },
                beforeSend: function (xhr) {

                },
                success: function (bool) {
                    if (bool) {
                        alert('Your pro plan is activated');
                        location.reload();
                    } else {
                        alert('You`ve entered a wrong token');
                    }
                },
                error: function (xhr, status, error) {

                }
            });
        });
        jQuery('textarea[name=cht_cta]').on("keyup", function (event) {
            jQuery('.tooltiptext span').html(jQuery(this).val());
            if (jQuery(this).val().length == 0) {
                jQuery('.cta').hide(200);
                jQuery('.tooltiptext span').hide(200);
            } else {
                jQuery('.cta').show(300);
                jQuery('.tooltiptext span').show(200);
                var temp = jQuery(".tooltiptext span").html();
                count = (temp.match(/\n/g) || []).length;
                if(count > 0) {
                    jQuery('.tooltiptext').addClass("has-multiline").removeClass("line-1").removeClass("line-2").removeClass("line-3").addClass("line-"+count);

                } else {
                    jQuery('.tooltiptext').removeClass("has-multiline");
                }
            }
        });
        jQuery('textarea[name=cht_cta]').trigger("keyup");
    });
}(jQuery));

(function ($) {
    jQuery(document).ready(function () {
        (function preview() {
            (function previewColor() {
                jQuery('.color-picker-radio input').on("change", function () {
                    var $this = jQuery(this);

                    if ($this.prop('checked')) {
                        var color = $this.val();
                    } else {
                        color = jQuery('.color-picker-custom input').val();
                    }
                    detectIcon();
                });

                jQuery('.color-picker-custom input').on("change", function () {
                    var $this = jQuery(this);

                    var color = $this.val();

                    detectIcon();
                });

                jQuery(document).on("change", "#chaty_default_state", function(){

                    detectIcon();
                });

                jQuery('#cht_close_button, #trigger_on_time, #chaty_trigger_on_scroll').on("click", function () {
                    detectIcon();
                });
            }());

            (function previewTooltip() {
                var $widgetTooltip = jQuery('#widgetTooltip');
                var $icon = jQuery('.preview .page .icon');

                function tooltipToggle() {
                    if(jQuery('[name=cht_cta]').length) {
                        if (jQuery('[name=cht_cta]').val().length >= 1) {
                            $icon.removeClass('no-tooltip');
                        } else {
                            $icon.addClass('no-tooltip');
                        }
                    }
                }

                tooltipToggle();

                $widgetTooltip.on("change", function () {
                    tooltipToggle();
                });
            }());

            function previewPosition() {
                var $inputPosBot = jQuery('#positionBottom');
                var $inputPosSide = jQuery('#positionSide');
                var $chatyWidget = jQuery('.preview .page .chaty-widget');
                var customSpace = '7px';

                var value = jQuery('[name="cht_position"]:checked').val();

                if (value === 'right') {
                    $chatyWidget.css({right: customSpace, left: 'auto', bottom: '7px'});
                } else if (value === 'left') {
                    $chatyWidget.css({left: customSpace, right: 'auto', bottom: '7px'});
                } else if (value === 'custom') {
                    if ($inputPosBot.val()) {
                        var positionBottom = $inputPosBot.val() + 'px';
                    } else {
                        positionBottom = customSpace;
                    }

                    if ($inputPosSide.val()) {
                        var positionSide = $inputPosSide.val() + 'px';
                    } else {
                        positionSide = customSpace;
                    }

                    $inputPosBot.on("change", function () {
                        positionBottom = jQuery('#positionBottom').val() + 'px';

                        $chatyWidget.css({bottom: positionBottom});
                    });

                    $inputPosSide.on("change", function () {
                        var valueCustom = jQuery('[name="positionSide"]:checked').val();
                        positionSide = jQuery(this).val() + 'px';

                        if (valueCustom === 'right') {
                            jQuery('.page-body .chaty-widget ').removeClass('chaty-widget-icons-left');
                            jQuery('.page-body .chaty-widget ').addClass('chaty-widget-icons-right');
                            $chatyWidget.css({right: positionSide, left: 'auto'});
                        } else if (valueCustom === 'left') {
                            jQuery('.page-body .chaty-widget ').removeClass('chaty-widget-icons-right');
                            jQuery('.page-body .chaty-widget ').addClass('chaty-widget-icons-left');
                            $chatyWidget.css({left: positionSide, right: 'auto'});
                        }
                    });

                    jQuery('[name="positionSide"]').on("change", function () {
                        var valueCustom = jQuery('[name="positionSide"]:checked').val();

                        if (valueCustom === 'right') {
                            jQuery('.page-body .chaty-widget ').removeClass('chaty-widget-icons-left');
                            jQuery('.page-body .chaty-widget ').addClass('chaty-widget-icons-right');
                            $chatyWidget.css({right: positionSide, left: 'auto'});
                        } else if (valueCustom === 'left') {
                            jQuery('.page-body .chaty-widget ').removeClass('chaty-widget-icons-right');
                            jQuery('.page-body .chaty-widget ').addClass('chaty-widget-icons-left');
                            $chatyWidget.css({left: positionSide, right: 'auto'});
                        }
                    });
                }
            }

            previewPosition();


            jQuery('input[name="cht_position"]').on("change", function () {
                var valueCustom = jQuery('[name="cht_position"]:checked').val();

                if (valueCustom === 'right') {
                    jQuery('.page-body .chaty-widget ').removeClass('chaty-widget-icons-left');
                    jQuery('.page-body .chaty-widget ').addClass('chaty-widget-icons-right');
                } else if (valueCustom === 'left') {
                    jQuery('.page-body .chaty-widget ').removeClass('chaty-widget-icons-right');
                    jQuery('.page-body .chaty-widget ').addClass('chaty-widget-icons-left');
                }
                previewPosition();
            });

        }());
        jQuery('.popover').hide();
        two_soc();

        var socialIcon = jQuery('.channels-icons > .icon-sm');


        var socialInputsContainer = jQuery('.social-inputs');

        var click = 0;
        jQuery('input[name=cht_custom_color]').on("keyup", function (event) {
            var color = jQuery(this).val();
            jQuery('.circle').html('');
            if(color != "") {
                var hashExists = color.indexOf("#");
                if(hashExists == -1) {
                    color = "#"+color;
                }
                jQuery('.color-picker-custom .circle').css({
                    'background-color': color
                });
            }
            if (jQuery(this).val().length < 1) {
                jQuery('.color-picker-custom .circle').html('?');
            }
        });
        socialIcon.on('click', function () {
            ++click;
            two_soc();

            var $this = jQuery(this);

            var social = $this.data('social');

            var socialItem = socialInputsContainer.find('.social-form-group');

            if ($this.hasClass('active')) {
                var del = ',' + jQuery(this).attr('data-social');

                var newlocaldata = jQuery('.add_slug').val();
                newlocaldata = newlocaldata.replace(del, '');
                jQuery('.add_slug').val(newlocaldata);
                newlocaldata = newlocaldata.replace(del, '');
                jQuery('.add_slug').val(newlocaldata);
                newlocaldata = newlocaldata.replace(del, '');
                jQuery('.add_slug').val(newlocaldata);
                newlocaldata = newlocaldata.replace(del, '');


                jQuery('.add_slug').val(newlocaldata);

                $this.toggleClass('active');
                return;
            }
            socialIcon.addClass('disabled');
            icon = jQuery(this).data('social');

            if (jQuery('.add_slug').val().indexOf(icon) == '1' && jQuery('.add_slug').val() != '') {
                var del = ',' + icon;
                var newlocaldata = jQuery('.add_slug').val();

                newlocaldata = newlocaldata.replace(del, '');
                jQuery('.add_slug').val(newlocaldata);
                newlocaldata = newlocaldata.replace(del, '');
                jQuery('.add_slug').val(newlocaldata);
                newlocaldata = newlocaldata.replace(del, '');
                jQuery('.add_slug').val(newlocaldata);
            } else {
                jQuery('.add_slug').val(jQuery('.add_slug').val() + ',' + jQuery(this).attr('data-social'));
            }


            /*  if(jQuery('section').is("#pro")){

             }else if(click >='3'){
             // alert(click);
             jQuery('.popover').show().effect( "shake", {times:3}, 600 );
             click = jQuery('.channels-selected__item.free').length;
             return;


             } */

            if (!jQuery('section').is('#pro') && jQuery('.channels-icons > .icon.active').length >= 2) {
                socialIcon.removeClass('disabled');
                jQuery('.popover').show();
                jQuery('.popover .upgrade-link').addClass("active");
                setTimeout(function(){
                    jQuery('.popover .upgrade-link').removeClass("active");
                }, 1000);
                return;
            }

            $this.toggleClass('active');


            if (jQuery('section').is('#pro')) {
                var token = 'pro';
            } else {
                var token = 'free';
            }


            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: {
                    action: 'choose_social',
                    social: social,
                    nonce_code: cht_nonce_ajax.cht_nonce,
                    version: token,
                    widget_index: jQuery("#widget_index").val()
                },
                beforeSend: function (xhr) {

                },
                success: function (data) {
                    var item = jQuery(data);
                    var itemName = item.find('.icon').data('title');
                    var itemChannel = item.data('channel');

                    if (!jQuery('.channels-selected div[data-social="' + itemName + '"]').length) {
                        jQuery('#chaty-social-close').before(item);
                        if(jQuery("#chaty-social-"+social+" .chaty-whatsapp-setting-textarea").length) {
                            editorId = jQuery("#chaty-social-"+social+" .chaty-whatsapp-setting-textarea").attr("id");
                            tinymce.execCommand( 'mceAddEditor', true, editorId);
                        }
                    }

                    socialIcon.removeClass('disabled');
                    $("#iconWidget").show();
                    detectIcon();
                    two_soc();
                    set_social_channel_order();
                    check_for_chaty_close_button();

                    jQuery('.chaty-color-field').spectrum({
                        chooseText: "Submit",
                        preferredFormat: "hex",
                        showInput: true,
                        cancelText: "Cancel",
                        showAlpha: true,
                        move: function (color) {
                            jQuery(this).val(color.toRgbString());
                            chaty_set_bg_color();
                            jQuery("input[name='switchPreview']:checked").trigger("change");
                        },
                        change: function (color) {
                            jQuery(this).val(color.toRgbString());
                            chaty_set_bg_color();
                            jQuery("input[name='switchPreview']:checked").trigger("change");
                        }
                    });
                    check_for_chaty_close_button();

                    if(social == "Whatsapp") {
                        if($("#channel_input_Whatsapp").length) {
                            cht_settings.channel_settings['Whatsapp'] = document.querySelector("#channel_input_Whatsapp");
                            window.intlTelInput(cht_settings.channel_settings['Whatsapp'], {
                                dropdownContainer: document.body,
                                formatOnDisplay: true,
                                hiddenInput: "full_number",
                                initialCountry: "auto",
                                nationalMode: false,
                                utilsScript: cht_settings.plugin_url + "admin/assets/js/utils.js",
                            });
                        }
                    }

                    if(jQuery(".custom-channel-Whatsapp").length) {
                        jQuery(".custom-channel-Whatsapp").each(function(){
                            if(!jQuery(this).closest(".iti__flag-container").length) {
                                var dataChannel = jQuery(this).closest("li.chaty-channel").data("id");
                                if(jQuery("#channel_input_"+dataChannel).length) {
                                    cht_settings.channel_settings[dataChannel] = document.querySelector("#channel_input_" + dataChannel);
                                    window.intlTelInput(cht_settings.channel_settings[dataChannel], {
                                        dropdownContainer: document.body,
                                        formatOnDisplay: true,
                                        hiddenInput: "full_number",
                                        initialCountry: "auto",
                                        nationalMode: false,
                                        utilsScript: cht_settings.plugin_url + "admin/assets/js/utils.js",
                                    });
                                }
                            }
                        });
                    }

                },
                error: function (xhr, status, error) {

                }
            });

            two_soc();
        });

        /**
         * Cancel Btn
         *
         */
        var cancelBtn = jQuery('body');

        cancelBtn.on('click', '.icon, .btn-cancel:not(.close-btn-set)', function (e) {

            if (jQuery(this).hasClass("close-btn-set")) {
                return;
            }

            e.preventDefault();

            if (jQuery(this).hasClass('icon') && jQuery(this).hasClass('active')) {
                return;
            }

            icon = jQuery(this).data('social');
            if (jQuery(this).hasClass('btn-cancel')) {
                jQuery('.icon.active[data-social^="' + icon + '"]').removeClass('active');

                var del = ',' + icon;
                var newlocaldata = jQuery('.add_slug').val();
                newlocaldata = newlocaldata.replace(del, '');

                jQuery('.add_slug').val(newlocaldata);
            }
            var del_item = jQuery('#chaty-social-' + icon);
            del_item.remove();

            var item = jQuery(this).parent('.channels-selected__item');


            var social = jQuery(this).data('social');

            // $.ajax({
            //     type: 'POST',
            //     dataType: 'json',
            //     url: ajaxurl,
            //     data: {
            //         action: 'remove_social',
            //         nonce_code: cht_nonce_ajax.cht_nonce,
            //         social: social,
            //         widget_index: jQuery("#widget_index").val()
            //     },
            //     beforeSend: function (xhr) {
            //
            //     },
            //     success: function (bool) {
            //         if (bool) {
            //             item.closest("li").remove();
            //             del_item.remove();
            //
            //
            //             jQuery('.icon-sm').each(function () {
            //                 if (jQuery(this).data('social') === social) {
            //                     // jQuery(this).removeClass('active');
            //                 }
            //             });
            //             set_social_channel_order();
            //         }
            //         check_for_chaty_close_button();
            //     },
            //     error: function (xhr, status, error) {
            //
            //     }
            // });
            detectIcon();
            two_soc();
            set_social_channel_order();
            check_for_chaty_close_button();
        });

        function two_soc() {
            if (jQuery('section').is('#pro')) {
                return;
            }

            if (jQuery('.channels-selected__item').length <= 1) {
                jQuery('.channels-selected__item').hide();
                jQuery('.popover').hide();
            } else if (jQuery('.channels-selected__item').length >= 2) {
                jQuery('.channels-selected__item').show();
            }
        }

        jQuery('.btn-help').on("click", function (event) {
            window.open(
                'https://premio.io/help/chaty/',
                '_blank' // <- This is what makes it open in a new window.
            );
        });


        var freeCustomInput = jQuery('.free-custom-radio, .free-custom-checkbox');

        freeCustomInput.on('click', function (e) {
            e.preventDefault();
        });
        var chatyCta = jQuery('[name=cht_cta]');
        var toolTip = jQuery('.preview .tooltip-show');

        chatyCta.on("keyup", function () {
            var $icon = jQuery('.preview .page .icon');
            if (chatyCta.val().length >= 1) {
                $icon.removeClass('no-tooltip');
            } else {
                $icon.addClass('no-tooltip');
            }
            toolTip.attr('data-title', chatyCta.val());
        });


        var baseIcon = '<svg version="1.1" id="ch" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.7 54 54" style="enable-background:new -496 507.7 54 54;" xml:space="preserve">\n' +
                '                            <style type="text/css">.st0 {fill: #A886CD;}  .st1 {fill: #FFFFFF;}\n' +
                '                        </style><g><circle class="st0" cx="-469" cy="534.7" r="27"/></g><path class="st1" d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z"/>\n' +
                '                                                    <path class="st0" d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0\n' +
                '                            C-478.6,530.8-478.2,530.5-477.7,530.5z"/>\n' +
                '                                                    <path class="st0" d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0\n' +
                '                            C-478.6,533.9-478.2,533.5-477.7,533.5z"/>\n' +
                '                        </svg>',
            defaultIcon = '<svg version="1.1" id="ch" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.7 54 54" style="enable-background:new -496 507.7 54 54;" xml:space="preserve">\n' +
                '                            <style type="text/css">.st0 {fill: #A886CD;}  .st1 {fill: #FFFFFF;}\n' +
                '                        </style><g><circle class="st0" cx="-469" cy="534.7" r="27"/></g><path class="st1" d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z"/>\n' +
                '                                                    <path class="st0" d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0\n' +
                '                            C-478.6,530.8-478.2,530.5-477.7,530.5z"/>\n' +
                '                                                    <path class="st0" d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0\n' +
                '                            C-478.6,533.9-478.2,533.5-477.7,533.5z"/>\n' +
                '                        </svg>',
            iconBlock = document.getElementById('iconWidget'),
            desktopIcon,
            mobileIcon,
            colorFill = jQuery('.color-picker-radio input:checked').val();

        jQuery('#testUpload').on('change', function () {
            if (this.value.length > 0) {
                document.querySelector('.js-upload').disabled = false;
            } else {
                document.querySelector('.js-upload').disabled = true;
                document.getElementById('uploadInput').checked = false;
            }
        });

        jQuery(document).on("keyup", "textarea.test_textarea", function(){
            detectIcon();
        });

        jQuery('.js-switch-preview').on('change', function () {
            if (getPreviewDesktop()) {
                jQuery(this).closest(".preview").removeClass('mobiel-view');
            } else {
                jQuery(this).closest(".preview").addClass('mobiel-view');
            }
            detectIcon();
        });

        jQuery(document).on("change","input[name='cht_pending_messages']", function(){
            if(jQuery("#cht_pending_messages").is(":checked")) {
                jQuery(".pending-message-items").addClass("active");
            } else {
                jQuery(".pending-message-items").removeClass("active");
            }
            detectIcon();
        });

        jQuery(document).on("change","#cht_number_of_messages", function(){
            detectIcon();
        });

        jQuery(document).on("keyup","#cht_number_of_messages", function(){
            detectIcon();
        });

        jQuery(document).on("blur","#cht_number_of_messages", function(){
            detectIcon();
        });

        function detectIcon() {
            var desktop,
                mobile,
                colorSelf = false;
            jQuery(".single-channel-setting").addClass("active");
            jQuery("#iconWidget").removeClass("img-p-active");

            if (getPreviewDesktop()) {
                if (jQuery('.js-chanel-desktop:checked').length === 0) {
                    desktop = false;
                    jQuery(".page-body .chaty-widget").hide();
                } else {
                    jQuery(".page-body .chaty-widget").show();
                }
                if (jQuery('.js-chanel-desktop:checked').length === 1) {
                    desktop = jQuery('.js-chanel-desktop:checked').closest("li").find(".icon.icon-md").html();
                    if (jQuery('.js-chanel-desktop:checked').closest(".channels-selected__item").hasClass("img-active")) {
                        jQuery("#iconWidget").addClass("img-p-active");
                    }
                }
                if (jQuery('.js-chanel-desktop:checked').length > 1) {
                    desktop = defaultIcon;
                    colorSelf = true;
                }
            } else {
                if (jQuery('.js-chanel-mobile:checked').length === 0) {
                    mobile = false;
                    jQuery(".page-body .chaty-widget").hide();
                } else {
                    jQuery(".page-body .chaty-widget").show();
                }
                if (jQuery('.js-chanel-mobile:checked').length === 1) {
                    mobile = jQuery('.js-chanel-mobile:checked').closest("li").find(".icon.icon-md").html();
                    if (jQuery('.js-chanel-mobile:checked').closest(".channels-selected__item").hasClass("img-active")) {
                        jQuery("#iconWidget").addClass("img-p-active");
                    }
                }
                if (jQuery('.js-chanel-mobile:checked').length > 1) {
                    mobile = defaultIcon;
                    colorSelf = true;
                }
            }

            desktopIcon = desktop;
            mobileIcon = mobile;

            if (getPreviewDesktop()) {
                setIcon(desktopIcon, colorSelf)
            } else {
                setIcon(mobileIcon, colorSelf)
            }

            $("#iconWidget .pop-number").remove();
            if(jQuery("#cht_pending_messages").is(":checked")) {
                var noOfVal = jQuery("#cht_number_of_messages").val();
                if(noOfVal != "" && noOfVal > 0) {
                    $("#iconWidget").append("<span class='pop-number'>"+noOfVal+"</span>");
                    $("#iconWidget .pop-number").css("color", jQuery("#cht_number_color").val());
                    $("#iconWidget .pop-number").css("background", jQuery("#cht_number_bg_color").val());
                }
            }

            var eClass = ".js-chanel-mobile";
            if (getPreviewDesktop()) {
                var eClass = ".js-chanel-desktop";
            }

            if(jQuery("#chaty_default_state").val() == "open" && jQuery(eClass+':checked').length > 1) {
                $("#iconWidget .pop-number").remove();
            }

            $("#cta-box span").css("color", $("#cht_cta_text_color").val());
            $("#cta-box span").css("background", $("#cht_cta_bg_color").val());
            $("#custom-css").html("<style>.preview .page .chaty-widget .icon:before {border-color: transparent "+$('#cht_cta_bg_color').val()+" transparent transparent } .preview .page .chaty-widget[style*='left: auto;'] .icon:before {border-color: transparent transparent transparent "+$('#cht_cta_bg_color').val()+"}</style>");

            jQuery(".single-channel-setting").addClass("active");
            if (jQuery("#channel-list .icon.active").length === 1) {
                jQuery(".single-channel-setting").removeClass("active");

                if(jQuery("#chaty_default_state").val() == "open") {
                    jQuery(".hide-show-button").removeClass("active");
                }
            }
        }

        function stickyelement_iconformat(icon) {
            var originalOption = icon.element;
            return jQuery('<span><i class="' + icon.text + '"></i> ' + icon.text + '</span>');
        }

        function setIcon(icon, colorSelf) {
            if(!jQuery("#iconWidget").length) {
                return;
            }
            if (icon) {
                //jQuery('.preview .page .chaty-widget').show();
                iconBlock.innerHTML = icon;
            } else {
                //jQuery('.preview .page .chaty-widget').hide();
                iconBlock.innerHTML = '';
            }
            if (colorSelf) {
                var color = jQuery('.color-picker-custom input').val() ? jQuery('.color-picker-custom input').val() : jQuery('.color-picker-radio input:checked').val();
                if(color != "") {
                    var hashExists = color.indexOf("#");
                    if (hashExists == -1) {
                        color = "#" + color;
                    }
                }
                jQuery('.preview .page #iconWidget svg circle').css({fill: color});
                jQuery('.preview .page .chaty-close-icon ellipse').css({fill: color});
                jQuery('#chaty-social-close ellipse').attr("fill", color);
            }

            thisVal = jQuery("#chaty_default_state").val();
            if(thisVal == "open") {
                jQuery(".hide-show-button").addClass("active");
            } else {
                jQuery(".hide-show-button").removeClass("active");
            }

            jQuery(".chaty-widget").removeClass("active").removeClass("hover").removeClass("click").removeClass("hide_arrow");
            if(thisVal == "open") {
                jQuery(".chaty-widget").addClass("active").addClass("click");
                if(thisVal == "open" && jQuery(eClass+':checked').length > 1) {
                    jQuery(".chaty-widget").addClass("hide_arrow");
                }
            } else if(thisVal == "hover") {
                jQuery(".chaty-widget").addClass("hover");
            } else {
                jQuery(".chaty-widget").addClass("click");
            }

            jQuery(".chaty-channels").html("");
            var eClass = ".js-chanel-mobile";
            if (getPreviewDesktop()) {
                var eClass = ".js-chanel-desktop";
            }

            if(thisVal == "open" && jQuery(eClass+':checked').length > 1) {
                jQuery("#chaty_attention_effect").val("");
                jQuery("#chaty_attention_effect, .test_textarea").attr("disabled", true);
                jQuery("#chaty_attention_effect option:first-child").text("Doesn't apply for the open state");
                if(jQuery(".test_textarea").val() != "Doesn't apply for the open state") {
                    jQuery(".test_textarea").attr("data-value", jQuery(".test_textarea").val());
                }
                jQuery(".test_textarea").val("Doesn't apply for the open state");
                jQuery("#cht_number_of_messages").attr("disabled", true);
                jQuery("#cht_pending_messages").attr("disabled", true);
                jQuery(".disable-message").addClass("label-tooltip").addClass("icon");
                jQuery("#cht_pending_messages").attr("checked", false);
                jQuery(".pending-message-items").removeClass("active");
                jQuery(".cta-action-radio input").attr("disabled", true);
            } else {
                jQuery("#chaty_attention_effect, .test_textarea").attr("disabled", false);
                jQuery("#chaty_attention_effect option:first-child").text("None");
                jQuery(".test_textarea").attr("placeholder","");
                if(jQuery(".test_textarea").val() == "Doesn't apply for the open state") {
                    jQuery(".test_textarea").val(jQuery(".test_textarea").attr("data-value"));
                }
                jQuery("#cht_number_of_messages").attr("disabled", false);
                jQuery("#cht_pending_messages").attr("disabled", false);
                jQuery(".disable-message").removeClass("label-tooltip").removeClass("icon");
                jQuery(".cta-action-radio input").attr("disabled", false);
            }
            jQuery(".page-body .chaty-widget").removeClass("vertical").removeClass("horizontal");

            if (jQuery(eClass+':checked').length > 1) {
                jQuery(eClass+':checked').each(function(){
                    var socialIcon = jQuery(this).closest("li").find(".icon").html();
                    var socialIcon = jQuery(this).closest("li").find(".icon").html();
                    var socialIconText = jQuery(this).closest("li").find(".chaty-title").val();
                    var eClass = jQuery(this).closest(".channels-selected__item").hasClass("img-active")?"img-active":"";
                    if(socialIconText != "") {
                        socialIconText = "<span class='social-tooltip'>"+socialIconText+"</span>";
                    }
                    jQuery(".chaty-channels").append("<div class='social-item-box "+eClass+"'><span class='tooltip-icon'>"+socialIcon+"</span>"+socialIconText+"</div>");
                });

                if(jQuery("#chaty_default_state").val() == "open" && jQuery("#cht_close_button").is(":checked")) {
                    jQuery("#iconWidget").css("display", "block");
                    jQuery(".chaty-widget .tooltiptext").css("display","block");
                    jQuery(".chaty-widget").removeClass("hide-arrow");
                } else if(jQuery("#chaty_default_state").val() != "open") {
                    jQuery("#iconWidget").css("display", "block");
                    jQuery(".chaty-widget .tooltiptext").css("display","block");
                    jQuery(".chaty-widget").removeClass("hide-arrow");
                } else if(jQuery("#chaty_default_state").val() == "open") {
                    jQuery("#iconWidget").hide();
                    jQuery(".chaty-widget .tooltiptext").hide();
                    jQuery(".chaty-widget").addClass("hide-arrow");
                }
                jQuery(".chaty-widget").removeClass("has-single");
                jQuery(".page-body .chaty-widget").addClass(jQuery("#chaty_icons_view").val());

            } else if (jQuery(eClass+':checked').length == 1) {
                if(jQuery("#chaty_default_state").val() == "open" && !jQuery("#cht_close_button").is(":checked")) {
                    jQuery("#iconWidget").css("display","block");
                    jQuery(".chaty-widget .tooltiptext").css("display","block");
                    jQuery(".chaty-widget").removeClass("hide-arrow");
                } else if(jQuery("#chaty_default_state").val() != "open") {
                    jQuery("#iconWidget").css("display","block");
                    jQuery(".chaty-widget .tooltiptext").css("display","block");
                    jQuery(".chaty-widget").removeClass("hide-arrow");
                }
                jQuery(".chaty-widget").addClass("has-single");
                jQuery(".chaty-widget").removeClass("hide-arrow");
            } else if (jQuery(eClass+':checked').length == 0) {
                jQuery("#iconWidget").hide();
                jQuery(".chaty-widget .tooltiptext").hide();
                jQuery(".chaty-widget").addClass("hide-arrow");
                jQuery(".chaty-widget").removeClass("has-single");
            }
            jQuery(".chaty-channels .remove-icon-img").remove();

            if(jQuery("#trigger_on_time").is(":checked")) {
                jQuery("#chaty_trigger_time").attr("readonly", false);
            } else {
                jQuery("#chaty_trigger_time").attr("readonly", true);
            }

            if(jQuery("#chaty_trigger_on_scroll").is(":checked")) {
                jQuery("#chaty_trigger_on_page_scroll").attr("readonly", false);
            } else {
                jQuery("#chaty_trigger_on_page_scroll").attr("readonly", true);
            }


            if(jQuery(".chaty-widget .tooltiptext").text() == "") {
                jQuery(".chaty-widget .tooltiptext").hide();
            } else {
                if(jQuery("#chaty_default_state").val() == "open" && jQuery(eClass+':checked').length > 1) {
                    jQuery(".chaty-widget .tooltiptext").hide();
                } else {
                    jQuery(".chaty-widget .tooltiptext").css("display", "block");
                }
            }
        }

        function getPreviewDesktop() {
            return jQuery('#previewDesktop').is(':checked') ? true : false;
        }

        function changeWidgetIcon() {

            jQuery(document).on('change', '.js-chanel-icon', function () {
                detectIcon();
            });

            jQuery(document).on('change', '.js-widget-i', function (ev) {
                if (ev.target.classList.contains('js-upload')) {
                    defaultIcon = jQuery('.file-preview-image').last().parent().html();
                } else {
                    defaultIcon = jQuery('i[data-type=' + jQuery(".js-widget-i:checked").val() + ']').html()
                }
                detectIcon();
            });
        }

        changeWidgetIcon();

        if (jQuery(".js-widget-i:checked").attr("data-type") !== 'chat-image') {
            defaultIcon = jQuery('i[data-type=' + jQuery(".js-widget-i:checked").attr("data-type") + ']').html();
            detectIcon();
        };
    });

    /*jQuery(window).on("load", function(){
        if(jQuery("#setting-error-settings_updated").length) {
            jQuery(".toast-message").show();
            jQuery(".toast-message").addClass("active");

            setTimeout(function(){
                jQuery(".toast-message").removeClass("active");
            }, 5000);
        }
    });*/

    jQuery(document).ready(function () {

        if(jQuery(".toast-message").length) {
            jQuery(".toast-message").show();
            jQuery(".toast-message").addClass("active");

            setTimeout(function(){
                jQuery(".toast-message").removeClass("active");
            }, 5000);
        }

        jQuery(document).on("click", ".toast-close-btn a", function(e){
            e.preventDefault();
            jQuery(".toast-message").removeClass("active");
        });

        jQuery(document).on("click", ".chaty-popup-box button, #chaty-intro-popup", function(e){
            e.stopPropagation();
            var nonceVal = jQuery("#chaty_update_popup_status").val();
            $("#chaty-intro-popup").remove();
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'update_popup_status',
                    nonce: nonceVal
                },
                beforeSend: function (xhr) {

                },
                success: function (res) {

                },
                error: function (xhr, status, error) {

                }
            });
        });

        jQuery(document).on("click", ".chaty-popup-box", function(e){
            e.stopPropagation();
        });

        jQuery(document).on("click", ".remove-chaty-options", function (e) {
            e.preventDefault();
            e.stopPropagation();
            if(confirm("Are you sure you want to delete this widget?")) {
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        action: 'remove_chaty_widget',
                        nonce_code: cht_nonce_ajax.cht_nonce,
                        widget_index: jQuery("#widget_index").val()
                    },
                    beforeSend: function (xhr) {

                    },
                    success: function (res) {
                        window.location = res;
                    },
                    error: function (xhr, status, error) {

                    }
                });
            }
        })

        /* Date: 2019-07-26 */
        var location_href = window.location.href;
        if (window.location.href.indexOf('page=chaty-app&widget=') > -1) {
            jQuery('#toplevel_page_chaty-app .wp-submenu.wp-submenu-wrap li').each(function () {
                var element_href = jQuery(this).find('a').attr('href');
                if (typeof element_href !== 'undefined') {
                    jQuery(this).removeClass('current');
                    if (window.location.href.indexOf(element_href) > -1 && element_href.indexOf('&widget=') > -1) {
                        jQuery(this).addClass('current');
                    }
                }
            });
        }
    });
}(jQuery));

jQuery(window).on("resize", function () {
    check_for_preview_pos();
});
jQuery(window).on("scroll", function () {
    check_for_preview_pos();
});
jQuery(document).ready(function () {
    check_for_preview_pos();
});

function check_for_preview_pos() {
    if(jQuery(".chaty-setting-form").length) {
        if(jQuery(window).width() > 1179) {
            var topPos = parseInt(jQuery(".chaty-setting-form").offset().top);
            jQuery(".btn-save-sticky, .chaty-sticky-buttons").css("top", (topPos+58));
            jQuery(".preview").css("top", (topPos+18));
            jQuery(".btn-help").css("top", (topPos+58+145));
            jQuery("a.remove-chaty-widget-sticky").css("top", (topPos+58+145+119));
        } else {
            jQuery(".btn-save-sticky, .chaty-sticky-buttons").attr("style", "");
            jQuery(".preview").attr("style", "");
            jQuery(".btn-help").attr("style", "");
            jQuery("a.remove-chaty-widget-sticky").attr("style", "");
        }
    }

    if (jQuery("#scroll-to-item").length && jQuery("#admin-preview").length) {
        if(jQuery("body").hasClass("has-premio-box")) {
            topPos = jQuery("#scroll-to-item").offset().top - jQuery(window).scrollTop() - 625;
        } else {
            topPos = jQuery("#scroll-to-item").offset().top - jQuery(window).scrollTop() - 485;
        }

        if (topPos < 0) {
            topPos = Math.abs(topPos);
            jQuery("#admin-preview").css("margin-top", ((-1) * topPos) + "px");
        } else {
            jQuery("#admin-preview").css("margin-top", "0");
        }
    }

    if(jQuery(window).height() <= 1180) {
        var totalHeight = 285;
        if(jQuery(window).width() <= 600) {
            totalHeight = 310;
        }
        jQuery(".chaty-sticky-buttons").css("top", ((jQuery(window).height()/2)-(totalHeight/2))+"px");
    }

    if(jQuery(".html-tooltip:not(.no-position)").length) {
        jQuery(".html-tooltip:not(.no-position)").each(function(){
            if(jQuery(this).offset().top - jQuery(window).scrollTop() > 540) {
                jQuery(this).addClass("top").removeClass("side").removeClass("bottom");
                jQuery(this).find(".tooltip-text").attr("style","");
                jQuery(this).find(".tooltip-text").removeClass("hide-arrow");
            } else if(jQuery(window).height() - (jQuery(this).offset().top - jQuery(window).scrollTop()) > 460) {
                jQuery(this).addClass("bottom").removeClass("top").removeClass("side");
                jQuery(this).find(".tooltip-text").attr("style","");
                jQuery(this).find(".tooltip-text").removeClass("hide-arrow");
            } else {
                jQuery(this).addClass("side").removeClass("top").removeClass("bottom");
                if(jQuery(this).find(".tooltip-text").length) {
                    jQuery(this).find(".tooltip-text").attr("style","");
                    jQuery(this).find(".tooltip-text").removeClass("hide-arrow");

                    if(jQuery(this).find(".tooltip-text").offset().top - jQuery(window).scrollTop() - 50 < 0) {
                        jQuery(this).find(".tooltip-text").css("margin-top", Math.abs(jQuery(this).find(".tooltip-text").offset().top - jQuery(window).scrollTop() - 50)+"px");
                        jQuery(this).find(".tooltip-text").addClass("hide-arrow");
                    } else {
                        jQuery(this).find(".tooltip-text").attr("style","");
                        if((jQuery(this).find(".tooltip-text").offset().top + parseInt(jQuery(this).find(".tooltip-text").outerHeight()) - jQuery(window).scrollTop() - jQuery(window).height()) > 0) {
                            jQuery(this).find(".tooltip-text").css("margin-top", ((-1)*Math.abs(jQuery(this).find(".tooltip-text").offset().top + parseInt(jQuery(this).find(".tooltip-text").outerHeight()) - jQuery(window).scrollTop() - jQuery(window).height()) - 10)+"px");
                            jQuery(this).find(".tooltip-text").addClass("hide-arrow");
                        }
                    }
                }
            }
        });
    }
}

var totalPageOptions = 0;
var pageOptionContent = "";
var totalDateAndTimeOptions = 0;
var dateAndTimeOptionContent = "";
jQuery(document).ready(function () {
    var isChatyInMobile = false; //initiate as false
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {
        isChatyInMobile = true;
    }
    totalPageOptions = parseInt(jQuery(".chaty-page-option").length);
    pageOptionContent = jQuery(".chaty-page-options-html").html();
    jQuery(".chaty-page-options-html").remove();
    totalDateAndTimeOptions = parseInt(jQuery(".chaty-date-time-option").length);
    dateAndTimeOptionContent = jQuery(".chaty-date-and-time-options-html").html();
    jQuery(".chaty-date-and-time-options-html").remove();

    jQuery("#create-rule").on("click", function () {
        // appendHtml = pageOptionContent.replace(/__count__/g, totalPageOptions, pageOptionContent);
        // totalPageOptions++;
        // jQuery(".chaty-page-options").append(appendHtml);
        // jQuery(".chaty-page-options .chaty-page-option").removeClass("last");
        // jQuery(".chaty-page-options .chaty-page-option:last").addClass("last");
        //
        // if (jQuery("#is_pro_plugin").val() == "0") {
        //     jQuery(".chaty-page-options").find("input").attr("name", "");
        //     jQuery(".chaty-page-options").find("select").attr("name", "");
        //     jQuery(".chaty-page-options").find("input").removeClass("cht-required");
        //     jQuery(".chaty-page-options").find("select").removeClass("cht-required");
        //     jQuery(this).remove();
        // }
        jQuery(".page-options").toggle();
    });

    jQuery("#create-data-and-time-rule").on("click", function () {
        jQuery(".chaty-data-and-time-rules").toggle();
    });

    jQuery(document).on("change", "#chaty_attention_effect", function(){
        var currentClass = jQuery(this).attr("data-effect");
        if(currentClass != "") {
            jQuery("#iconWidget").removeClass("chaty-animation-"+currentClass);
        }
        jQuery("#iconWidget").removeClass("start-now");
        jQuery("#iconWidget").addClass("chaty-animation-"+jQuery(this).val()).addClass("start-now");
        jQuery(this).attr("data-effect", jQuery(this).val());
    });

    setInterval(function(){
        var currentClass = jQuery("#chaty_attention_effect").attr("data-effect");
        if(currentClass != "") {
            jQuery("#iconWidget").removeClass("chaty-animation-"+currentClass);
            jQuery("#iconWidget").removeClass("start-now");
            setTimeout(function(){
                jQuery("#iconWidget").addClass("chaty-animation-"+jQuery("#chaty_attention_effect").val()).addClass("start-now");
            }, 1000);
        } else {
            jQuery("#chaty_attention_effect").attr("data-effect", jQuery("#chaty_attention_effect").val());
        }
    }, 5000);

    jQuery(document).on("click", ".remove-chaty", function () {
        jQuery(".page-options").toggle();
    });

    jQuery(document).on("click", ".remove-page-option", function () {
        jQuery(".chaty-data-and-time-rules ").toggle();
    });

    jQuery("#image-upload-content .custom-control-label").on("click", function (e) {
        e.stopPropagation();
        jQuery(this).closest(".custom-control").find("input[type=radio]").attr("checked", true);
        jQuery('.js-widget-i').trigger("change");
        return false;
    });

    jQuery('.chaty-color-field').spectrum({
        chooseText: "Submit",
        preferredFormat: "hex",
        cancelText: "Cancel",
        showInput: true,
        showAlpha: true,
        move: function (color) {
            jQuery(this).val(color.toRgbString());
            jQuery("#cta-box span").css("color", jQuery("#cht_cta_text_color").val());
            jQuery("#cta-box span").css("background", jQuery("#cht_cta_bg_color").val());
            jQuery("#custom-css").html("<style>.preview .page .chaty-widget .icon:before {border-color: transparent "+jQuery('#cht_cta_bg_color').val()+" transparent transparent } .preview .page .chaty-widget[style*='left: auto;'] .icon:before {border-color: transparent transparent transparent "+jQuery('#cht_cta_bg_color').val()+"}</style>");
            chaty_set_bg_color();
            jQuery("input[name='switchPreview']:checked").trigger("change");
        },
        change: function (color) {
            jQuery(this).val(color.toRgbString());
            chaty_set_bg_color();
            jQuery("#cta-box span").css("color", jQuery("#cht_cta_text_color").val());
            jQuery("#cta-box span").css("background", jQuery("#cht_cta_bg_color").val());
            jQuery("#custom-css").html("<style>.preview .page .chaty-widget .icon:before {border-color: transparent "+jQuery('#cht_cta_bg_color').val()+" transparent transparent } .preview .page .chaty-widget[style*='left: auto;'] .icon:before {border-color: transparent transparent transparent "+jQuery('#cht_cta_bg_color').val()+"}</style>");
            jQuery("input[name='switchPreview']:checked").trigger("change");
        }
    });
    jQuery(".chaty-color-field").on("change", function () {
        chaty_set_bg_color();
        change_custom_preview();
    });

    jQuery(".remove-chaty-img").on("click", function (e) {
        e.stopPropagation();
    });

    if(!isChatyInMobile) {
        jQuery("#channels-selected-list").sortable({
            placeholder: "ui-chaty-state-hl",
            items: "li:not(#chaty-social-close)",
            stop: function () {
                set_wp_editor();
            },
            update: function (event, ui) {
                set_social_channel_order();
                change_custom_preview();
                set_wp_editor();
            }
        });
    }

    jQuery(document).ready(function(){
        set_wp_editor();
    });

    function set_wp_editor() {
        if(jQuery(".chaty-whatsapp-setting-textarea").length) {
            jQuery(".chaty-whatsapp-setting-textarea").each(function(){
                if(jQuery("#cht_social_embedded_message_"+jQuery(this).data("id")+"_ifr").length) {
                    tinymce.get(jQuery(this).attr("id")).remove();
                }
                tinymce.execCommand( 'mceAddEditor', true, jQuery(this).attr("id"));
            });
        }
    }

    jQuery(".close-button-img img, .close-button-img .image-upload").on("click", function () {
        var image = wp.media({
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false,
            library: {
                type: 'image',
            }
        }).open()
        .on('select', function (e) {
            var uploaded_image = image.state().get('selection').first();
            imageData = uploaded_image.toJSON();
            jQuery('.close-button-img').addClass("active");
            jQuery('.close-button-img input').val(imageData.id);
            jQuery('.close-button-img img').attr("src", imageData.url);
            change_custom_preview();
        });
    });

    jQuery(".remove-close-img").on("click", function () {
        default_image = jQuery("#default_image").val();
        jQuery('.close-button-img').removeClass("active");
        jQuery('.close-button-img input').val("");
        jQuery('.close-button-img img').attr("src", default_image);
        change_custom_preview();
    });

    jQuery(document).on("click", ".chaty-widget.click", function(e){
        e.preventDefault();
        // jQuery(".chaty-channels").toggle();
        jQuery(".chaty-widget").toggleClass("active");
    });

    jQuery(document).on('change', '.url-options.cht-required', function (ev) {
        thisVal = jQuery(this).val();
        siteURL = jQuery("#chaty_site_url").val();
        newURL = siteURL;
        if (thisVal == "page_has_url") {
            newURL = siteURL;
        } else if (thisVal == "page_contains") {
            newURL = siteURL + "%s%";
        } else if (thisVal == "page_start_with") {
            newURL = siteURL + "s%";
        } else if (thisVal == "page_end_with") {
            newURL = siteURL + "%s";
        }
        jQuery(this).closest(".url-content").find(".chaty-url").text(newURL);
    });

    check_for_chaty_close_button();
    chaty_set_bg_color();
    change_custom_preview();

    jQuery(".chaty-settings.cls-btn a, .close-btn-set").on("click", function (e) {
        e.preventDefault();
        jQuery(".cls-btn-settings, .close-btn-set").toggleClass("active");
    });

    /*Default Values*/
    if (jQuery("input[name='cht_position']:checked").length == 0) {
        jQuery("#right-position").attr("checked", true);
        jQuery("input[name='cht_position']:checked").trigger("change");
    }
    if (jQuery("input[name='widget_icon']:checked").length == 0) {
        jQuery("input[name='widget_icon']:first").attr("checked", true);
        jQuery("input[name='widget_icon']:checked").trigger("change");
    }

    /*font family Privew*/
    jQuery('.form-fonts').on( 'change', function() {
        var font_val = jQuery(this).val();
        jQuery('.chaty-google-font').remove();
        if (font_val != "") {
            if(jQuery('.form-fonts option:selected').data("group") != "Default") {
                //jQuery('head').append('<link href="https://fonts.googleapis.com/css?family=' + font_val + ':400,600,700" rel="stylesheet" type="text/css" class="chaty-google-font">');
            }
            jQuery('.preview-section-chaty #admin-preview .page-body').css('font-family', font_val);
        } else {
            jQuery('.preview-section-chaty #admin-preview .page-body').attr("style","");
        }
    });
});

jQuery(window).on("load", function() {
    check_for_chaty_close_button();
    chaty_set_bg_color();
    jQuery(".chaty-page-options .chaty-page-option").removeClass("last");
    jQuery(".chaty-page-options .chaty-page-option:last").addClass("last");

    jQuery('.url-options.cht-required').each(function () {
        jQuery(this).trigger("change");
    });
    var font_val = jQuery('.form-fonts').val();
    jQuery('.chaty-google-font').remove();
    if (font_val != "") {
        if(jQuery('.form-fonts option:selected').data("group") != "Default") {
            //jQuery('head').append('<link href="https://fonts.googleapis.com/css?family=' + font_val + ':400,600,700" rel="stylesheet" type="text/css" class="chaty-google-font">');
        }
        jQuery('.preview-section-chaty #admin-preview .page-body').css('font-family', font_val);
    }
    // jQuery("#chaty_default_state").trigger("change");
});

var selectedsocialSlug = "";

function upload_chaty_image(socialSlug) {
    selectedsocialSlug = socialSlug;
    var image = wp.media({
        title: 'Upload Image',
        // mutiple: true if you want to upload multiple files at once
        multiple: false,
        library: {
            type: 'image',
        }
    }).open()
        .on('select', function (e) {
            var uploaded_image = image.state().get('selection').first();
            imageData = uploaded_image.toJSON();
            jQuery('#cht_social_image_' + selectedsocialSlug).val(imageData.id);
            jQuery('.custom-image-' + selectedsocialSlug + " img").attr("src", imageData.url);
            jQuery("#chaty-social-" + selectedsocialSlug + " .channels-selected__item").addClass("img-active");
            change_custom_preview();
        });
}

function toggle_chaty_setting(socId) {
    jQuery("#chaty-social-" + socId).find(".chaty-advance-settings").toggle();
    jQuery("#chaty-social-" + socId).find(".chaty-advance-settings").toggleClass('active');
    if(socId == "Contact_Us") {
        if(jQuery("#Contact_Us-close-btn").length) {
            var nonce = jQuery("#Contact_Us-close-btn").data("nonce");
            if (!jQuery("#Contact_Us-close-btn").hasClass("active")) {
                jQuery("#Contact_Us-close-btn").addClass("active")
                jQuery.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: {
                        "nonce": nonce,
                        "action": 'update_channel_setting'
                    },
                    success: function (response) {

                    }
                });
            }
        }
    }
    if(jQuery("#chaty-social-" + socId+ " .chaty-advance-settings.active").length) {
        jQuery("body,html").animate({
            scrollTop: jQuery("#chaty-social-" + socId+ " .chaty-advance-settings.active").offset().top - 50
        }, 500);
    }
    change_custom_preview();
}

function chaty_set_bg_color() {
    jQuery(".chaty-color-field:not(.button-color)").each(function () {
        if (jQuery(this).val() != "" && jQuery(this).val() != "#ffffff") {
            if (jQuery(this).closest("li").data("id") != "Linkedin" || (jQuery(this).closest("li").data("id") == "Linkedin" && jQuery(this).val() != "#ffffff")) {
                defaultColor = jQuery(this).val();
                jQuery(this).closest(".channels-selected__item").find(".color-element").css("fill", defaultColor);
                jQuery(this).closest(".channels-selected__item").find(".custom-chaty-image").css("background", defaultColor);
                jQuery(this).closest(".channels-selected__item").find(".facustom-icon").css("background", defaultColor);
            }
        }
    });
    change_custom_preview();
}

function upload_qr_code() {
    var image = wp.media({
        title: 'Upload QR Image',
        multiple: false,
        library: {
            type: 'image',
        }
    }).open()
        .on('select', function (e) {
            var uploaded_image = image.state().get('selection').first();
            imageData = uploaded_image.toJSON();
            jQuery('#upload_qr_code_val').val(imageData.id);
            jQuery('#upload_qr_code img').attr("src", imageData.url);
            jQuery(".remove-qr-code").addClass("active");
            change_custom_preview();
        });
}

function remove_qr_code() {
    jQuery(".remove-qr-code").removeClass("active");
    jQuery('#upload_qr_code_val').val("");
    default_image = jQuery("#default_image").val();
    jQuery('#upload_qr_code img').attr("src", default_image);
    change_custom_preview();
}

function remove_chaty_image(socId) {
    default_image = jQuery("#default_image").val();
    jQuery("#chaty-social-" + socId + " .channels-selected__item").removeClass("img-active");
    jQuery('#cht_social_image_' + socId).val("");
    jQuery('#cht_social_image_src_' + socId).attr("src", default_image);
    change_custom_preview();
}

var baseIcon = '<svg version="1.1" id="ch" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.7 54 54" style="enable-background:new -496 507.7 54 54;" xml:space="preserve">\n' +
        '                            <style type="text/css">.st0 {fill: #A886CD;}  .st1 {fill: #FFFFFF;}\n' +
        '                        </style><g><circle class="st0" cx="-469" cy="534.7" r="27"/></g><path class="st1" d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z"/>\n' +
        '                                                    <path class="st0" d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0\n' +
        '                            C-478.6,530.8-478.2,530.5-477.7,530.5z"/>\n' +
        '                                                    <path class="st0" d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0\n' +
        '                            C-478.6,533.9-478.2,533.5-477.7,533.5z"/>\n' +
        '                        </svg>',
    defaultIcon = '<svg version="1.1" id="ch" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="-496 507.7 54 54" style="enable-background:new -496 507.7 54 54;" xml:space="preserve">\n' +
        '                            <style type="text/css">.st0 {fill: #A886CD;}  .st1 {fill: #FFFFFF;}\n' +
        '                        </style><g><circle class="st0" cx="-469" cy="534.7" r="27"/></g><path class="st1" d="M-459.9,523.7h-20.3c-1.9,0-3.4,1.5-3.4,3.4v15.3c0,1.9,1.5,3.4,3.4,3.4h11.4l5.9,4.9c0.2,0.2,0.3,0.2,0.5,0.2 h0.3c0.3-0.2,0.5-0.5,0.5-0.8v-4.2h1.7c1.9,0,3.4-1.5,3.4-3.4v-15.3C-456.5,525.2-458,523.7-459.9,523.7z"/>\n' +
        '                                                    <path class="st0" d="M-477.7,530.5h11.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-11.9c-0.5,0-0.8-0.4-0.8-0.8l0,0\n' +
        '                            C-478.6,530.8-478.2,530.5-477.7,530.5z"/>\n' +
        '                                                    <path class="st0" d="M-477.7,533.5h7.9c0.5,0,0.8,0.4,0.8,0.8l0,0c0,0.5-0.4,0.8-0.8,0.8h-7.9c-0.5,0-0.8-0.4-0.8-0.8l0,0\n' +
        '                            C-478.6,533.9-478.2,533.5-477.7,533.5z"/>\n' +
        '                        </svg>'
var iconBlock = document.getElementById('iconWidget');

function set_social_channel_order() {
    socialString = [];
    jQuery("#channels-selected-list li").each(function () {
        socialString.push(jQuery(this).attr("data-id"));
    });
    socialString = socialString.join(",");
    jQuery("#cht_numb_slug").val(socialString);
    check_for_chaty_close_button();
}

function check_for_chaty_close_button() {
    if (jQuery("#channels-selected-list > li:not(.chaty-cls-setting)").length >= 2) {
        jQuery("#chaty-social-close").show();
    } else {
        jQuery("#chaty-social-close").hide();
    }
    change_custom_preview();
    var srtString = "";
    jQuery("#channels-selected-list > li").each(function(){
        if(jQuery(this).attr("data-id") != "undefined" && jQuery(this).attr("data-id") != "") {
            srtString += jQuery(this).attr("data-id")+",";
        }
        srtString = srtString.trimRight(",")
    });
    jQuery(".add_slug").val(srtString);
}

function change_custom_preview() {
    var desktop,
        mobile,
        colorSelf = false;
    jQuery("#iconWidget").removeClass("img-p-active");
    if (getChtPreviewDesktop()) {
        if (jQuery('.js-chanel-desktop:checked').length === 0) {
            desktop = false;
        }
        if (jQuery('.js-chanel-desktop:checked').length === 1) {
            desktop = jQuery('.js-chanel-desktop:checked').closest("li").find(".icon.icon-md").html();
            if (jQuery('.js-chanel-desktop:checked').closest(".channels-selected__item").hasClass("img-active")) {
                jQuery("#iconWidget").addClass("img-p-active");
            }
        }
        if (jQuery('.js-chanel-desktop:checked').length > 1) {
            desktop = defaultIcon;
            colorSelf = true;
        }
    } else {
        if (jQuery('.js-chanel-mobile:checked').length === 0) {
            mobile = false;
        }
        if (jQuery('.js-chanel-mobile:checked').length === 1) {
            mobile = jQuery('.js-chanel-mobile:checked').closest("li").find(".icon.icon-md").html();
            if (jQuery('.js-chanel-mobile:checked').closest(".channels-selected__item").hasClass("img-active")) {
                jQuery("#iconWidget").addClass("img-p-active");
            }
        }
        if (jQuery('.js-chanel-mobile:checked').length > 1) {
            mobile = defaultIcon;
            colorSelf = true;
        }
    }


    desktopIcon = desktop;
    mobileIcon = mobile;

    if (getChtPreviewDesktop()) {
        setChtIcon(desktopIcon, colorSelf)
    } else {
        setChtIcon(mobileIcon, colorSelf)
    }

    if(jQuery("#channels-selected-list > li.chaty-channel").length <= 1) {
        jQuery(".o-channel, .font-section").removeClass("active");
    } else {
        jQuery(".o-channel, .font-section").addClass("active");
    }
}

function getChtPreviewDesktop() {
    return jQuery('#previewDesktop').is(':checked') ? true : false;
}

function setChtIcon(icon, colorSelf) {
    if(!jQuery("iconWidget").length) {
        return;
    }
    iconBlock = document.getElementById('iconWidget');
    if (icon) {
        //jQuery('.preview .page .chaty-widget').show();
        iconBlock.innerHTML = icon;
    } else {
        //jQuery('.preview .page .chaty-widget').hide();
        iconBlock.innerHTML = '';
    }
    if (colorSelf) {
        var color = jQuery('.color-picker-custom input').val() ? jQuery('.color-picker-custom input').val() : jQuery('.color-picker-radio input:checked').val();
        jQuery('.preview .page #iconWidget svg circle').css({fill: color});
        jQuery('.preview .page .chaty-close-icon ellipse').css({fill: color});
        jQuery('#chaty-social-close ellipse').attr("fill", color);
    }
    jQuery('.js-widget-i:checked').trigger("change");
}
;
var wpdiscuzLoadRichEditor = parseInt(wpdiscuzAjaxObj.loadRichEditor);
if (wpdiscuzLoadRichEditor) {
    var wpDiscuzEditor = new WpdEditor();
}
function wpdMessagesOnInit(message, type) {
    wpdiscuzAjaxObj.setCommentMessage(message, type);
    setTimeout(function () {
        location.href = location.href.substring(0, location.href.indexOf('wpdiscuzUrlAnchor') - 1);
    }, 3000);
}
/**
 * @param {string/array} message(s) - message(s) to show
 * @param {string/array} type(s) - message(s) type(s). acceptable values error/success/warning
 * @param {string/array} delay(s) - timeout(s) before message(s) disappears
 */
wpdiscuzAjaxObj.setCommentMessage = function (message, type, delay) {
    var className = 'wpdiscuz-message-error';
    if (Object.prototype.toString.call(message) === '[object Array]') {
        for (var i in message) {
            if (Object.prototype.toString.call(type) === '[object Array]') {
                if (type[i] === 'success') {
                    className = 'wpdiscuz-message-success';
                } else if (type[i] === 'warning') {
                    className = 'wpdiscuz-message-warning';
                }
            } else {
                if (type === 'success') {
                    className = 'wpdiscuz-message-success';
                } else if (type === 'warning') {
                    className = 'wpdiscuz-message-warning';
                }
            }
            jQuery('<div/>')
                    .addClass(className)
                    .html(message[i])
                    .prependTo('#wpdiscuz-comment-message')
                    .delay(Object.prototype.toString.call(delay) === '[object Array]' ? delay[i] : delay ? delay : 4000)
                    .fadeOut(1000, function () {
                        jQuery(this).remove();
                    });
        }
    } else {
        if (type === 'success') {
            className = 'wpdiscuz-message-success';
        } else if (type === 'warning') {
            className = 'wpdiscuz-message-warning';
        }
        jQuery('<div/>')
                .addClass(className)
                .html(message)
                .prependTo('#wpdiscuz-comment-message')
                .delay(delay ? delay : 4000)
                .fadeOut(1000, function () {
                    jQuery(this).remove();
                });
    }
};
jQuery(document).ready(function ($) {
    /* global wpdiscuzAjaxObj */
    /* global Cookies  */
    /* global grecaptcha */
    /* global Quill */
    $('body').addClass('wpdiscuz_' + wpdiscuzAjaxObj.version);
    var isUserLoggedIn = wpdiscuzAjaxObj.is_user_logged_in;
    var isShowCaptchaForGuests = wpdiscuzAjaxObj.wc_captcha_show_for_guest == 1 && !isUserLoggedIn;
    var isShowCaptchaForMembers = wpdiscuzAjaxObj.wc_captcha_show_for_members == 1 && isUserLoggedIn;
    var wpdiscuzRecaptchaVersion = wpdiscuzAjaxObj.wpDiscuzReCaptchaVersion;
    var commentListLoadType = parseInt(wpdiscuzAjaxObj.commentListLoadType);
    var wpdiscuzPostId = parseInt(wpdiscuzAjaxObj.wc_post_id);
    var commentListUpdateType = parseInt(wpdiscuzAjaxObj.commentListUpdateType);
    var commentListUpdateTimer = parseInt(wpdiscuzAjaxObj.commentListUpdateTimer) * 1000;
    var enableGuestsLiveUpdate = parseInt(wpdiscuzAjaxObj.liveUpdateGuests);
    var loadLastCommentId = wpdiscuzAjaxObj.loadLastCommentId;
    var bubbleLastCommentId = loadLastCommentId;
    var firstLoadWithAjax = parseInt(wpdiscuzAjaxObj.firstLoadWithAjax);
    if (Cookies.get('wpdiscuz_comments_sorting')) {
        Cookies.remove('wpdiscuz_comments_sorting', {path: ''});
    }
    if (Cookies.get('wordpress_last_visit')) {
        Cookies.remove('wordpress_last_visit', {path: ''});
    }
    if (Cookies.get('wpdiscuz_last_visit')) {
        Cookies.remove('wpdiscuz_last_visit', {path: ''});
    }
    var storeCommenterData = wpdiscuzAjaxObj.storeCommenterData;
    var wpdiscuzUploader = parseInt(wpdiscuzAjaxObj.wmuEnabled);
    var isCookiesEnabled = wpdiscuzAjaxObj.isCookiesEnabled;
    var wpdCookiesConsent = true;
    var wpdiscuzCookiehash = wpdiscuzAjaxObj.cookiehash;
    var isLoadOnlyParentComments = parseInt(wpdiscuzAjaxObj.isLoadOnlyParentComments);
    var enableDropAnimation = parseInt(wpdiscuzAjaxObj.enableDropAnimation) ? 500 : 0;
    var isNativeAjaxEnabled = parseInt(wpdiscuzAjaxObj.isNativeAjaxEnabled);
    var bubbleEnabled = parseInt(wpdiscuzAjaxObj.enableBubble);
    var bubbleLiveUpdate = parseInt(wpdiscuzAjaxObj.bubbleLiveUpdate);
    var bubbleHintTimeout = parseInt(wpdiscuzAjaxObj.bubbleHintTimeout);
    var bubbleHintHideTimeout = parseInt(wpdiscuzAjaxObj.bubbleHintHideTimeout) ? parseInt(wpdiscuzAjaxObj.bubbleHintHideTimeout) : 5;
    var bubbleShowNewCommentMessage = parseInt(wpdiscuzAjaxObj.bubbleShowNewCommentMessage);
    var bubbleLocation = wpdiscuzAjaxObj.bubbleLocation;
    var inlineFeedbackAttractionType = wpdiscuzAjaxObj.inlineFeedbackAttractionType;
    var wpdiscuzAgreementFields = [];
    var reCaptchaWidgets = [];
    var bubbleNewCommentIds = [];
    var addingComment = false;
    var wpdiscuzLoadCount = 1;
    loginButtonsClone();
    if (wpdiscuzLoadRichEditor && $('#wpd-editor-0_0').length) {
        wpDiscuzEditor.createEditor('#wpd-editor-0_0');
    }

    $(document).delegate('#wpdcom .ql-editor, #wpdcom .wc_comment', 'focus', function () {
        $('.wpd-form-foot', $(this).parents('.wpd_comm_form')).slideDown(enableDropAnimation);
    });
    $(document).delegate('#wpdcom textarea', 'focus', function () {
        if (!$(this).next('.autogrow-textarea-mirror').length) {
            $(this).autoGrow();
        }
    });

    if (!isUserLoggedIn) {
        var commentAuthorCookies = {
            comment_author: Cookies.get('comment_author_' + wpdiscuzCookiehash),
            comment_author_email: Cookies.get('comment_author_email_' + wpdiscuzCookiehash),
            comment_author_url: Cookies.get('comment_author_url_' + wpdiscuzCookiehash)
        };
        setCookieInForm(commentAuthorCookies);
    }

    $('.wpd-vote-down.wpd-dislike-hidden').remove();
    $('.wpd-toolbar-hidden').prev('[id^=wpd-editor-]').css('border-bottom', "1px solid #dddddd");

    $(document).delegate('#wpd-editor-source-code-wrapper-bg', 'click', function () {
        $(this).hide();
        $('#wpd-editor-source-code-wrapper').hide();
        $('#wpd-editor-uid').val('');
        $('#wpd-editor-source-code').val('');
    });

    if (wpdiscuzLoadRichEditor) {
        $(document).delegate('#wpd-insert-source-code', 'click', function () {
            var editor = wpDiscuzEditor.createEditor('#' + $('#wpd-editor-uid').val());
            editor.deleteText(0, editor.getLength(), Quill.sources.USER);
            var html = $('#wpd-editor-source-code').val();
            if (html.length) {
                editor.clipboard.dangerouslyPasteHTML(0, html, Quill.sources.USER);
            }
            editor.update();
            $('#wpd-editor-source-code-wrapper-bg').hide();
            $('#wpd-editor-source-code-wrapper').hide();
            $('#wpd-editor-uid').val('');
            $('#wpd-editor-source-code').val('');
        });
    }

    $(document).delegate('.wpd-reply-button', 'click', function () {
        var uniqueID = getUniqueID($(this), 0);
        if ($(this).hasClass('wpdiscuz-clonned')) {
            if (wpdiscuzLoadRichEditor) {
                setTimeout(function () {
                    wpDiscuzEditor.createEditor('#wpd-editor-' + uniqueID).focus();
                }, enableDropAnimation);
            } else {
                setTimeout(function () {
                    $('#wc-textarea-' + uniqueID).trigger('focus');
                }, enableDropAnimation);
            }
            $('#wpd-secondary-form-wrapper-' + uniqueID).slideToggle(enableDropAnimation);
        } else {
            cloneSecondaryForm($(this));
        }
        generateReCaptcha(uniqueID);
    });

    $(document).delegate('#wpdcom .wpd-comment-link [data-comment-url]', 'click', function () {
        var val = $(this).data('comment-url');
        var el = $('<input/>');
        el.appendTo('body').css({'position': 'absolute', 'top': '-10000000px'}).val(val);
        el.select();
        document.execCommand('copy');
        el.remove();
        wpdiscuzAjaxObj.setCommentMessage(val + '<br/>' + wpdiscuzAjaxObj.wc_copied_to_clipboard, 'success', 5000);
    });

    $(document).delegate('.wpdiscuz-nofollow,.wc_captcha_refresh_img,.wpd-load-more-submit', 'click', function (e) {
        e.preventDefault();
    });

    $(document).delegate('.wpd-toggle.wpd_not_clicked', 'click', function () {
        var btn = $(this);
        btn.removeClass('wpd_not_clicked');
        var uniqueID = getUniqueID($(this), 0);
        var toggle = $(this);
        var icon = $('.fas', toggle);
        if (!toggle.parents('.wpd-comment:not(.wpd-reply)').children('.wpd-reply').length && isLoadOnlyParentComments) {
            wpdiscuzShowReplies(uniqueID, btn);
        } else {
            $('#wpd-comm-' + uniqueID + '> .wpd-reply').slideToggle(700, function () {
                if ($(this).is(':hidden')) {
                    icon.removeClass('fa-chevron-up');
                    icon.addClass('fa-chevron-down');
                    toggle.attr('wpd-tooltip', wpdiscuzAjaxObj.wc_show_replies_text);
                } else {
                    icon.removeClass('fa-chevron-down');
                    icon.addClass('fa-chevron-up');
                    toggle.attr('wpd-tooltip', wpdiscuzAjaxObj.wc_hide_replies_text);
                }
                btn.addClass('wpd_not_clicked');
            });
        }
    });

    $(document).delegate('.wpd-new-loaded-comment', 'mouseenter', function () {
        $(this).removeClass('wpd-new-loaded-comment');
    });

    $(document).delegate('.wpd-sbs-toggle', 'click', function () {
        $('.wpdiscuz-subscribe-bar').slideToggle(enableDropAnimation);
    });
//============================== reCAPTCHA ============================== //
    if (parseInt(wpdiscuzAjaxObj.wpDiscuzIsShowOnSubscribeForm) && !isUserLoggedIn && wpdiscuzAjaxObj.wpDiscuzReCaptchaSK) {
        if (wpdiscuzRecaptchaVersion === '2.0') {
            setTimeout(function () {
                try {
                    grecaptcha.render('wpdiscuz-recaptcha-subscribe-form', {
                        'sitekey': wpdiscuzAjaxObj.wpDiscuzReCaptchaSK,
                        'theme': wpdiscuzAjaxObj.wpDiscuzReCaptchaTheme,
                        'callback': function (response) {
                            $('#wpdiscuz-recaptcha-field-subscribe-form').val('key');
                        },
                        'expired-callback': function () {
                            $('#wpdiscuz-recaptcha-field-subscribe-form').val("");
                        }
                    });
                } catch (e) {
                    console.log(e);
                    wpdiscuzAjaxObj.setCommentMessage('reCaptcha Error: ' + e.message, 'error');
                }
            }, 1000);

            $(document).delegate('#wpdiscuz-subscribe-form', 'submit', function (e) {
                if (!$('#wpdiscuz-recaptcha-field-subscribe-form').val()) {
                    $('.wpdiscuz-recaptcha', $(this)).css('border', '1px solid red');
                    e.preventDefault();
                } else {
                    $('.wpdiscuz-recaptcha', $(this)).css('border', 'none');
                }
            });
        } else if (wpdiscuzRecaptchaVersion === '3.0') {
            $(document).delegate('#wpdiscuz_subscription_button', 'click', function (e) {
                var subscriptionForm = $(this).parents('#wpdiscuz-subscribe-form');
                e.preventDefault();
                try {
                    grecaptcha.ready(function () {
                        grecaptcha.execute(wpdiscuzAjaxObj.wpDiscuzReCaptchaSK, {action: 'wpdiscuz/wpdAddSubscription'})
                                .then(function (token) {
                                    console.log(5555);
                                    document.getElementById('wpdiscuz-recaptcha-field-subscribe-form').value = token;
                                    subscriptionForm.submit();
                                }, function (reason) {
                                    wpdiscuzAjaxObj.setCommentMessage('reCaptcha Error', 'error');
                                    console.log(reason);
                                });
                    });
                } catch (e) {
                    console.log(e);
                    wpdiscuzAjaxObj.setCommentMessage('reCaptcha Error: ' + e.message, 'error');
                }
            });
        }
    }

    function generateReCaptcha(uniqueId) {
        if ((isShowCaptchaForGuests || isShowCaptchaForMembers) && wpdiscuzRecaptchaVersion === '2.0') {
            var commentId = getCommentID(uniqueId);
            setTimeout(function () {
                if (!reCaptchaWidgets[commentId]) {
                    try {
                        reCaptchaWidgets[commentId] = grecaptcha.render('wpdiscuz-recaptcha-' + uniqueId, {
                            'sitekey': wpdiscuzAjaxObj.wpDiscuzReCaptchaSK,
                            'theme': wpdiscuzAjaxObj.wpDiscuzReCaptchaTheme,
                            'callback': function (response) {
                                $('#wpdiscuz-recaptcha-field-' + uniqueId).val('key');
                            },
                            'expired-callback': function () {
                                $('#wpdiscuz-recaptcha-field-' + uniqueId).val("");
                            }
                        });
                    } catch (e) {
                        console.log(e);
                        wpdiscuzAjaxObj.setCommentMessage('reCaptcha Error: ' + e.message, 'error');
                    }
                }
            }, 1000);
        }
    }

    function resetReCaptcha(uniqueId) {
        if ((isShowCaptchaForGuests || isShowCaptchaForMembers) && wpdiscuzRecaptchaVersion === '2.0') {
            var commentId = getCommentID(uniqueId);
            grecaptcha.reset(reCaptchaWidgets[commentId]);
        }
    }

    function wpdReCaptchaValidate(form) {
        var wpdGoogleRecaptchaValid = true;
        if (wpdiscuzRecaptchaVersion === '2.0' && $('input[name=wc_captcha]', form).length && !$('input[name=wc_captcha]', form).val().length) {
            wpdGoogleRecaptchaValid = false;
            $('.wpdiscuz-recaptcha', form).css('border', '1px solid red');
        } else if (wpdiscuzRecaptchaVersion === '2.0' && $('input[name=wc_captcha]', form).length) {
            $('.wpdiscuz-recaptcha', form).css('border', 'none');
        }
        return wpdGoogleRecaptchaValid;
    }
    function wpdReCaptchaValidateOnSubscribeForm(form) {
        var wpdGoogleRecaptchaValid = true;
        if (wpdiscuzRecaptchaVersion === '2.0' && $('input[name=wpdiscuz_recaptcha_subscribe_form]', form).length && !$('input[name=wpdiscuz_recaptcha_subscribe_form]', form).val().length) {
            wpdGoogleRecaptchaValid = false;
            $('.wpdiscuz-recaptcha', form).css('border', '1px solid red');
        } else if (wpdiscuzRecaptchaVersion === '2.0' && $('input[name=wpdiscuz_recaptcha_subscribe_form]', form).length) {
            $('.wpdiscuz-recaptcha', form).css('border', 'none');
        }
        return wpdGoogleRecaptchaValid;
    }
    if ((isShowCaptchaForGuests || isShowCaptchaForMembers) && wpdiscuzRecaptchaVersion === '2.0') {
        var ww = $(window).width();
        var wpcomm = $('#wpdcom').width();
        if (wpcomm >= 1100) {
            $("#wpdcom .wpd_main_comm_form .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform-origin": "right 0", "-webkit-transform-origin": "right 0", "transform": "scale(0.9)", "-webkit-transform": "scale(0.9)"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform-origin": "right 0", "-webkit-transform-origin": "right 0", "transform": "scale(0.9)", "-webkit-transform": "scale(0.9)"});
            $("#wpdcom .wpd_main_comm_form .wpd-form-col-left").css({"width": "65%"});
            $("#wpdcom .wpd_main_comm_form .wpd-form-col-right").css({"width": "35%"});
        }
        if (wpcomm >= 940 && wpcomm < 1100) {
            $("#wpdcom .wpd_main_comm_form .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform-origin": "right 0", "-webkit-transform-origin": "right 0", "transform": "scale(0.9)", "-webkit-transform": "scale(0.9)"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform-origin": "right 0", "-webkit-transform-origin": "right 0", "transform": "scale(0.9)", "-webkit-transform": "scale(0.9)"});
            $("#wpdcom .wpd_main_comm_form .wpd-form-col-left").css({"width": "60%"});
            $("#wpdcom .wpd_main_comm_form .wpd-form-col-right").css({"width": "40%"});
        }
        if (wpcomm >= 810 && wpcomm < 940) {
            $("#wpdcom .wpd_main_comm_form .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform": "scale(0.9)", "-webkit-transform": "scale(0.9)"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform": "scale(0.8)", "-webkit-transform": "scale(0.8)"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-left").css({"width": "40%"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-right").css({"width": "60%"});
        }
        if (wpcomm >= 730 && wpcomm < 810) {
            $("#wpdcom .wpd_main_comm_form .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform": "scale(0.9)", "-webkit-transform": "scale(0.9)"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform-origin": "right 0", "-webkit-transform-origin": "right 0", "transform": "scale(0.8)", "-webkit-transform": "scale(0.8)"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-left").css({"width": "45%"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-right").css({"width": "55%"});
        }
        if (wpcomm >= 610 && wpcomm < 730) {
            $("#wpdcom .wpd_main_comm_form .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform": "scale(0.85)", "-webkit-transform": "scale(0.85)"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform": "scale(0.8)", "-webkit-transform": "scale(0.8)"});
            $("#wpdcom .wpd_main_comm_form .wpd-form-col-left").css({"width": "43%"});
            $("#wpdcom .wpd_main_comm_form .wpd-form-col-right").css({"width": "55%"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-left").css({"width": "30%"});
            $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-right").css({"width": "70%"});
        }
        if (ww > 650) {
            if (wpcomm >= 510 && wpcomm < 610) {
                $("#wpdcom .wpd_main_comm_form .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform-origin": "center 0", "-webkit-transform-origin": "center 0", "transform": "scale(0.77)", "-webkit-transform": "scale(0.77)"});
                $("#wpdcom .wpd-secondary-form-wrapper .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform-origin": "right 0", "-webkit-transform-origin": "right 0", "transform": "scale(0.77)", "-webkit-transform": "scale(0.77)"});
                $("#wpdcom .wpd_main_comm_form .wpd-form-col-left").css({"width": "35%"});
                $("#wpdcom .wpd_main_comm_form .wpd-form-col-right").css({"width": "63%"});
                $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-left").css({"width": "30%", "position": "relative", "right": "-60px"});
                $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-right").css({"width": "70%"});
                $("#wpdcom .wpd-secondary-form-wrapper .wc-form-footer").css({"margin-left": "0px"});
            }
            if (wpcomm >= 470 && wpcomm < 510) {
                $("#wpdcom .wpd_main_comm_form .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform-origin": "center 0", "-webkit-transform-origin": "center 0", "transform": "scale(0.77)", "-webkit-transform": "scale(0.77)"});
                $("#wpdcom .wpd-secondary-form-wrapper .wpd-field-captcha .wpdiscuz-recaptcha").css({"transform-origin": "right 0", "-webkit-transform-origin": "right 0", "transform": "scale(0.77)", "-webkit-transform": "scale(0.77)"});
                $("#wpdcom .wpd_main_comm_form .wpd-form-col-left").css({"width": "40%"});
                $("#wpdcom .wpd_main_comm_form .wpd-form-col-right").css({"width": "60%"});
                $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-left").css({"float": "none", "width": "100%", "display": "block"});
                $("#wpdcom .wpd-secondary-form-wrapper .wpd-form-col-right").css({"float": "none", "width": "100%", "display": "block"});
                $("#wpdcom .wpd_main_comm_form .wc-form-footer").css({"margin-left": "0px"});
                $("#wpdcom .wpd-secondary-form-wrapper .wc-form-footer").css({"margin-left": "0px"});
            }
            if (wpcomm < 470) {
                $("#wpdcom .wpd-secondary-form-wrapper .wpd-field-captcha .wpdiscuz-recaptcha").css({"margin": "0px auto", "transform-origin": "center 0", "-webkit-transform-origin": "center 0"});
                $("#wpdcom .wpd-form-col-left").css({"float": "none", "width": "100%", "display": "block"});
                $("#wpdcom .wpd-form-col-right").css({"float": "none", "width": "100%", "display": "block"});
                $("#wpdcom .wpd-secondary-form-wrapper .wc-form-footer").css({"margin-left": "0px"});
                $("#wpdcom .wpd-secondary-form-wrapper .wc_notification_checkboxes").css({"text-align": "center"});
                $("#wpdcom .wpd-secondary-form-wrapper .wc-field-submit").css({"text-align": "center"});
            }
        }
    }

//============================== /reCAPTCHA ============================== //
//============================== ADD COMMENT FUNCTION ============================== // 

    $(document).delegate('.wc_comm_submit.wpd_not_clicked', 'click', function () {
        var currentSubmitBtn = $(this);
        var depth = 1;
        var wcForm = $(this).parents('form');
        if (!wcForm.hasClass('wpd_main_comm_form')) {
            depth = getCommentDepth($(this).parents('.wpd-comment'));
        }

        wpdValidateFieldRequired(wcForm, '#wpd-editor-' + $('.wpdiscuz_unique_id', wcForm).val());
        wcForm.submit(function (e) {
            e.preventDefault();
        });
        if ($('.wc_comment', wcForm).val().trim() === '') {
            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj.wc_msg_required_fields, 'error');
            return;
        }
        if (wcForm[0].checkValidity() && wpdReCaptchaValidate(wcForm)) {
            addingComment = true;
            addAgreementInCookie(wcForm);
            $(currentSubmitBtn).removeClass('wpd_not_clicked');
            var data = new FormData();
            data.append('action', 'wpdAddComment');
            var inputs = $(":input", wcForm);
            inputs.each(function () {
                if (this.name != '' && this.type != 'checkbox' && this.type != 'radio') {
                    data.append(this.name + '', $(this).val().trim());
                }
                if (this.type == 'checkbox' || this.type == 'radio') {
                    if ($(this).is(':checked')) {
                        data.append(this.name + '', $(this).val());
                    }
                }
            });

            data.append('wpd_comment_depth', depth);

            if (wpdiscuzAjaxObj.wpdiscuz_zs) {
                data.append('wpdiscuz_zs', wpdiscuzAjaxObj.wpdiscuz_zs);
            }

            if ($('.wpd-cookies-checkbox', wcForm).length && !$('.wpd-cookies-checkbox', wcForm).prop("checked")) {
                wpdCookiesConsent = false;
            }
            $('#wpdiscuz-loading-bar').show();
            if (wpdiscuzAjaxObj.wpDiscuzReCaptchaSK && wpdiscuzRecaptchaVersion === '3.0' && ((wpdiscuzAjaxObj.wc_captcha_show_for_guest == 1 && !wpdiscuzAjaxObj.is_user_logged_in) || (wpdiscuzAjaxObj.wc_captcha_show_for_members == 1 && wpdiscuzAjaxObj.is_user_logged_in))) {
                try {
                    grecaptcha.ready(function () {
                        grecaptcha.execute(wpdiscuzAjaxObj.wpDiscuzReCaptchaSK, {action: 'wpdiscuz/addComment'})
                                .then(function (token) {
                                    data.append('g-recaptcha-response', token);
                                    wpdiscuzSendComment(wcForm, data, currentSubmitBtn);
                                }, function (reason) {
                                    wpdiscuzAjaxObj.setCommentMessage('reCaptcha Error', 'error');
                                    console.log(reason);
                                });
                    });
                } catch (e) {
                    console.log(e);
                    wpdiscuzAjaxObj.setCommentMessage('reCaptcha Error: ' + e.message, 'error');
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                }
            } else {
                wpdiscuzSendComment(wcForm, data, currentSubmitBtn);
            }

        }
        resetReCaptcha($('.wpdiscuz_unique_id', wcForm).val());
        wpdiscuzReset();
    });

    function wpdiscuzSendComment(wcForm, data, currentSubmitBtn) {
        getAjaxObj(isNativeAjaxEnabled || wpdiscuzUploader, false, data)
                .done(function (r) {
                    $(currentSubmitBtn).addClass('wpd_not_clicked');
                    if (typeof r === 'object') {
                        if (r.success) {
                            if (wpdiscuzAjaxObj.commentFormView === "collapsed") {
                                $('.wpd-form-foot', wcForm).slideUp(enableDropAnimation);
                            }
                            $('.wpd-thread-info').html(r.data.wc_all_comments_count_new_html);
                            r.data.wc_all_comments_count_new = parseInt(r.data.wc_all_comments_count_new);
                            $('#wpd-bubble-all-comments-count').text(r.data.wc_all_comments_count_new);
                            if (r.data.wc_all_comments_count_new) {
                                $('#wpd-bubble-all-comments-count').show();
                            } else {
                                $('#wpd-bubble-all-comments-count').hide();
                            }
                            var animateDelay = enableDropAnimation;
                            if (r.data.is_main) {
                                addCommentsAfterSticky(r.data.message);
                            } else {
                                animateDelay = enableDropAnimation + 700;
                                $('#wpd-secondary-form-wrapper-' + r.data.uniqueid).slideToggle(700);
                                if (r.data.is_in_same_container == 1) {
                                    $('#wpd-secondary-form-wrapper-' + r.data.uniqueid).after(r.data.message);
                                } else {
                                    $('#wpd-comm-' + r.data.uniqueid).after(r.data.message);
                                }
                            }
                            notifySubscribers(r);
                            wpdiscuzRedirect(r);
                            if (isCookiesEnabled && wpdCookiesConsent) {
                                addCookie(r.data);
                            } else if (!wpdCookiesConsent) {
                                $('.wpd-cookies-checkbox').removeAttr('checked');
                            }
                            if (wpdiscuzLoadRichEditor) {
                                wpDiscuzEditor.createEditor('#wpd-editor-' + $('.wpdiscuz_unique_id', wcForm).val()).setContents([{insert: '\n'}]);
                            }
                            wcForm.get(0).reset();
                            setCookieInForm(r.data);
                            $('.wmu-preview-wrap', wcForm).remove();
                            deleteAgreementFields();
                            if (parseInt(wpdiscuzAjaxObj.scrollToComment)) {
                                setTimeout(function () {
                                    $('html, body').animate({
                                        scrollTop: $('#comment-' + r.data.new_comment_id).offset().top - 32
                                    }, 1000);
                                }, animateDelay);
                            }
                            runCallbacks(r, wcForm);
                        } else if (r.data) {
                            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data], 'error');
                            runCallbacks(r, wcForm);
                        }
                    } else {
                        wpdiscuzAjaxObj.setCommentMessage(r, 'error');
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                    addingComment = false;
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $(currentSubmitBtn).addClass('wpd_not_clicked');
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    }

    function notifySubscribers(r) {
        if (!r.data.held_moderate) {
            var data = new FormData();
            data.append('action', 'wpdCheckNotificationType');
            data.append('comment_id', r.data.new_comment_id);
            data.append('email', r.data.comment_author_email);
            data.append('isParent', r.data.is_main);
            getAjaxObj(isNativeAjaxEnabled, true, data);
        }
    }

    function wpdiscuzRedirect(r) {
        if (r.data.redirect > 0 && r.data.new_comment_id) {
            var data = new FormData();
            data.append('action', 'wpdRedirect');
            data.append('commentId', r.data.new_comment_id);
            getAjaxObj(isNativeAjaxEnabled, true, data)
                    .done(function (r) {
                        if (typeof r === 'object') {
                            if (r.success) {
                                setTimeout(function () {
                                    location.href = r.data;
                                }, 2000);
                            }
                        }
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                    });
        }
    }

    function setCookieInForm(obj) {
        $('.wpd_comm_form .wc_name').val(obj.comment_author);
        if (obj.comment_author_email && obj.comment_author_email.indexOf('@example.com') < 0) {
            $('.wpd_comm_form .wc_email').val(obj.comment_author_email);
        }
        if (obj.comment_author_url) {
            $('.wpd_comm_form .wc_website').val(obj.comment_author_url);
        }
    }

    function addCookie(obj) {
        var email = obj.comment_author_email;
        var name = obj.comment_author;
        var weburl = obj.comment_author_url;
        if (storeCommenterData == null) {
            Cookies.set('comment_author_email_' + wpdiscuzCookiehash, email);
            Cookies.set('comment_author_' + wpdiscuzCookiehash, name);
            if (weburl.length) {
                Cookies.set('comment_author_url_' + wpdiscuzCookiehash, weburl);
            }
        } else {
            storeCommenterData = parseInt(storeCommenterData);
            Cookies.set('comment_author_email_' + wpdiscuzCookiehash, email, {expires: storeCommenterData, path: '/'});
            Cookies.set('comment_author_' + wpdiscuzCookiehash, name, {expires: storeCommenterData, path: '/'});
            if (weburl.length) {
                Cookies.set('comment_author_url_' + wpdiscuzCookiehash, weburl, {expires: storeCommenterData, path: '/'});
            }
        }
        if ($('.wpd-cookies-checkbox').length) {
            $('.wpd-cookies-checkbox').attr('checked', 'checked');
        }
    }
//============================== /ADD COMMENT FUNCTION ============================== // 
//============================== EDIT COMMENT FUNCTION ============================== // 
    var wcCommentTextBeforeEditing;

    $(document).delegate('.wpd_editable_comment', 'click', function () {
        if (wcCommentTextBeforeEditing && $('.wpdiscuz-edit-form-wrap').length) {
            wpdCancelOrSave(getUniqueID($('.wpdiscuz-edit-form-wrap'), 0), wcCommentTextBeforeEditing);
        }
        var uniqueID = getUniqueID($(this), 0);
        var commentID = getCommentID(uniqueID);
        var data = new FormData();
        data.append('action', 'wpdEditComment');
        data.append('commentId', commentID);
        wcCommentTextBeforeEditing = $('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-text').get(0);
        getAjaxObj(isNativeAjaxEnabled, true, data)
                .done(function (r) {
                    if (typeof r === 'object') {
                        if (r.success) {
                            $('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-right .wpd-comment-text').replaceWith(r.data.html);
                            if (wpdiscuzLoadRichEditor) {
                                let currentEditor = wpDiscuzEditor.createEditor('#wpd-editor-edit_' + uniqueID);
                                currentEditor.clipboard.dangerouslyPasteHTML(0, r.data.content);
                                currentEditor.update();
                                $('.wpd-toolbar-hidden').prev('[id^=wpd-editor-]').css('border-bottom', "1px solid #dddddd");
                            } else {
                                $('#wc-textarea-edit_' + uniqueID).val(r.data.content);
                            }
                            $('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-right .wpd_editable_comment').hide();
                            $('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-last-edited').hide();
                        } else {
                            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data], 'error');
                        }
                    } else {
                        console.log(r);
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    });

    $(document).delegate('.wc_save_edited_comment', 'click', function () {
        var uniqueID = getUniqueID($(this));
        var commentID = getCommentID(uniqueID);
        var editCommentForm = $('#wpd-comm-' + uniqueID + ' #wpdiscuz-edit-form');
        wpdValidateFieldRequired(editCommentForm, '#wpd-editor-edit_' + uniqueID);
        editCommentForm.submit(function (e) {
            e.preventDefault();
        });

        if (editCommentForm[0].checkValidity()) {
            var data = new FormData();
            data.append('action', 'wpdSaveEditedComment');
            data.append('commentId', commentID);
            var inputs = $(":input", editCommentForm);
            inputs.each(function () {
                if (this.name !== '' && this.type !== 'checkbox' && this.type !== 'radio') {
                    data.append(this.name + '', $(this).val());
                }
                if (this.type === 'checkbox' || this.type === 'radio') {
                    if ($(this).is(':checked')) {
                        data.append(this.name + '', $(this).val());
                    }
                }
            });

            getAjaxObj(isNativeAjaxEnabled, true, data)
                    .done(function (r) {
                        if (typeof r === 'object') {
                            if (r.success) {
                                wpdCancelOrSave(uniqueID, r.data.message);
                                if (r.data.lastEdited) {
                                    $('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-last-edited').remove();
                                    $(r.data.lastEdited).insertAfter('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-right .wpd-comment-text');
                                }
                                if (r.data.twitterShareLink) {
                                    $('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-share .wpd-tooltip-content .wc_tw').attr('href', r.data.twitterShareLink);
                                }
                                if (r.data.whatsappShareLink) {
                                    $('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-share .wpd-tooltip-content .wc_whatsapp').attr('href', r.data.whatsappShareLink);
                                }
                                if (wpdiscuzLoadRichEditor) {
                                    wpDiscuzEditor.removeEditor('#wpd-editor-edit_' + uniqueID);
                                }
                            } else {
                                wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data], 'error');
                            }
                            runCallbacks(r, commentID);
                        } else {
                            console.log(r);
                        }
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    });
        }
    });

    $(document).delegate('.wc_cancel_edit', 'click', function () {
        var uniqueID = getUniqueID($(this));
        wpdCancelOrSave(uniqueID, wcCommentTextBeforeEditing);
        if (wpdiscuzLoadRichEditor) {
            wpDiscuzEditor.removeEditor('#wpd-editor-edit_' + uniqueID);
        }
    });

    function wpdCancelOrSave(uniqueID, content) {
        $('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-right .wpd_editable_comment').show();
        $('#wpd-comm-' + uniqueID + ' .wpdiscuz-edit-form-wrap').replaceWith(content);
        $('#wpd-comm-' + uniqueID + ' > .wpd-comment-wrap .wpd-comment-last-edited').show();
    }

//============================== /EDIT COMMENT FUNCTION ============================== // 
//============================== LOAD MORE ============================== // 
    if (!wpdiscuzAjaxObj.wordpressIsPaginate && firstLoadWithAjax) {
        wpdiscuzLoadCount = 0;
        if (firstLoadWithAjax == 1) {
            setTimeout(function () {
                wpdiscuzLoadComments(true);
            }, 500);
        } else {
            $(document).delegate('.wpd-load-comments', 'click', function () {
                $(this).parent('.wpd-load-more-submit-wrap').remove();
                wpdiscuzLoadComments(true);
            });
        }
    }

    $(document).delegate('.wpd-load-more-submit', 'click', function () {
        var loadButton = $(this);
        var loaded = 'wpd-loaded';
        var loading = 'wpd-loading';
        if (loadButton.hasClass(loaded)) {
            wpdiscuzLoadComments(false, loadButton, loaded, loading);
        }
    });

    var isRun = false;
    if (commentListLoadType === 2 && !wpdiscuzAjaxObj.wordpressIsPaginate) {
        $('.wpd-load-more-submit').parents('.wpdiscuz-comment-pagination').hide();
        wpdiscuzScrollEvents();
        $(window).scroll(function () {
            wpdiscuzScrollEvents();
        });
    }

    function wpdiscuzScrollEvents() {
        var wpdiscuzHasMoreComments = $('#wpdiscuzHasMoreComments').val();
        var scrollHeight = $(document).height();
        var scrollPosition = $(window).height() + $(window).scrollTop();
        if (scrollHeight && scrollPosition) {
            var scrollPercent = scrollPosition * 100 / scrollHeight;
            if (scrollPercent >= 80 && isRun === false && wpdiscuzHasMoreComments == 1) {
                isRun = true;
                wpdiscuzLoadComments(false, $('.wpd-load-more-submit'));
            }
        }
    }

    function wpdiscuzLoadComments(isFirstLoad, loadButton, loaded, loading) {
        if (loadButton) {
            loadButton.toggleClass(loaded);
            loadButton.toggleClass(loading);
        }
        var data = new FormData();
        data.append('action', 'wpdLoadMoreComments');
        var sorting = $('.wpdiscuz-sort-button-active').attr('data-sorting');
        if (sorting) {
            data.append('sorting', sorting);
        }
        data.append('offset', wpdiscuzLoadCount);
        data.append('lastParentId', getLastParentID());
        data.append('isFirstLoad', isFirstLoad ? 1 : 0);
        var filterType = $('.wpdf-active').attr('data-filter-type');
        data.append('wpdType', filterType ? filterType : '');
        getAjaxObj(isNativeAjaxEnabled, isFirstLoad && firstLoadWithAjax == 1 ? false : true, data)
                .done(function (r) {
                    if (typeof r === 'object') {
                        if (r.success) {
                            wpdiscuzLoadCount++;
                            if (isFirstLoad) {
                                $('.wpd-comment').remove();
                            }
                            $('.wpdiscuz_single').remove();
                            $('.wpdiscuz-comment-pagination').before(r.data.comment_list);
                            setLoadMoreVisibility(r, isFirstLoad && commentListLoadType !== 2);
                            isRun = false;
                            loadLastCommentId = r.data.loadLastCommentId;
                            runCallbacks(r);
                            if (isFirstLoad) {
                                getSingleComment(false);
                            }
                        }
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                    $('.wpd-load-more-submit').blur();
                    if (loadButton) {
                        loadButton.toggleClass(loaded);
                        loadButton.toggleClass(loading);
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                    $('.wpd-load-more-submit').blur();
                    if (loadButton) {
                        loadButton.toggleClass(loaded);
                        loadButton.toggleClass(loading);
                    }
                });
    }

    function setLoadMoreVisibility(r, showPagination) {
        if (r.data.is_show_load_more == false) {
            $('#wpdiscuzHasMoreComments').val(0);
            $('.wpd-load-more-submit').parents('.wpdiscuz-comment-pagination').hide();
        } else {
            setLastParentID(r.data.last_parent_id);
            $('#wpdiscuzHasMoreComments').val(1);
            if (showPagination) {
                $('.wpd-load-more-submit').parents('.wpdiscuz-comment-pagination').show();
            }
        }

        runCallbacks(r);
    }
    wpdiscuzAjaxObj.setLoadMoreVisibility = setLoadMoreVisibility;
//============================== /LOAD MORE ============================== // 
//============================== VOTE  ============================== // 
    $(document).delegate('.wpd-vote-up.wpd_not_clicked, .wpd-vote-down.wpd_not_clicked', 'click', function () {
        var currentVoteBtn = $(this);
        $(currentVoteBtn).removeClass('wpd_not_clicked');
        var uniqueId = getUniqueID(currentVoteBtn);
        var commentID = getCommentID(uniqueId);
        var voteType;
        if ($(this).hasClass('wpd-vote-up')) {
            voteType = 1;
        } else {
            voteType = -1;
        }

        var data = new FormData();
        data.append('action', 'wpdVoteOnComment');
        data.append('commentId', commentID);
        data.append('voteType', voteType);
        getAjaxObj(isNativeAjaxEnabled, true, data)
                .done(function (r) {
                    $(currentVoteBtn).addClass('wpd_not_clicked');
                    if (typeof r === 'object') {
                        if (r.success) {
                            if (r.data.buttonsStyle === 'total') {
                                var voteCountDiv = $('.wpd-comment-footer .wpd-vote-result', $('#comment-' + commentID));
                                var votes = r.data.votes;
                                voteCountDiv.text(votes);
                                voteCountDiv.removeClass('wpd-up wpd-down');
                                if (votes > 0) {
                                    voteCountDiv.addClass('wpd-up');
                                }
                                if (votes < 0) {
                                    voteCountDiv.addClass('wpd-down');
                                }
                            } else {
                                var likeCountDiv = $('.wpd-comment-footer .wpd-vote-result-like', $('#comment-' + commentID));
                                var dislikeCountDiv = $('.wpd-comment-footer .wpd-vote-result-dislike', $('#comment-' + commentID));
                                likeCountDiv.text(r.data.likeCount);
                                dislikeCountDiv.text(r.data.dislikeCount);
                                parseInt(r.data.likeCount) > 0 ? likeCountDiv.addClass('wpd-up') : likeCountDiv.removeClass('wpd-up');
                                parseInt(r.data.dislikeCount) < 0 ? dislikeCountDiv.addClass('wpd-down') : dislikeCountDiv.removeClass('wpd-down');
                            }
                            var voteUpDiv = $('.wpd-comment-footer .wpd-vote-up', $('#comment-' + commentID));
                            var voteDownDiv = $('.wpd-comment-footer .wpd-vote-down', $('#comment-' + commentID));
                            voteUpDiv.removeClass('wpd-up');
                            voteDownDiv.removeClass('wpd-down');
                            if (r.data.curUserReaction > 0) {
                                voteUpDiv.addClass('wpd-up');
                            } else if (r.data.curUserReaction < 0) {
                                voteDownDiv.addClass('wpd-down');
                            }
                        } else if (r.data) {
                            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data], 'error');
                        }
                        runCallbacks(r, commentID, voteType);
                    } else {
                        console.log(r);
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $(currentVoteBtn).addClass('wpd_not_clicked');
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    });
//============================== /VOTE ============================== //
//============================== SORTING ============================== //
    $(document).delegate('body', 'click', function (e) {
        var children = $('.wpdiscuz-sort-buttons');
        if ($(e.target).hasClass('wpdf-sorting') || $(e.target).parent().hasClass('wpdf-sorting')) {
            children.css({display: children.is(':visible') ? 'none' : 'flex'});
        } else {
            children.hide();
        }
    });
    $(document).delegate('.wpdiscuz-sort-button:not(.wpdiscuz-sort-button-active)', 'click', function () {
        var clickedBtn = $(this);
        var sorting = $(this).attr("data-sorting");
        if (sorting) {
            $('.wpdiscuz-sort-button.wpdiscuz-sort-button-active').removeClass('wpdiscuz-sort-button-active').appendTo('.wpdiscuz-sort-buttons');
            clickedBtn.addClass('wpdiscuz-sort-button-active').prependTo('.wpdf-sorting');
            var data = new FormData();
            data.append('action', 'wpdSorting');
            data.append('sorting', sorting);
            var filterType = $('.wpdf-active').attr('data-filter-type');
            data.append('wpdType', filterType ? filterType : '');
            getAjaxObj(isNativeAjaxEnabled, true, data)
                    .done(function (r) {
                        if (typeof r === 'object') {
                            if (r.success) {
                                $('#wpdcom .wpd-comment').remove();
                                $('#wpdcom .wpd-thread-list').prepend(r.data.message);
                                setLoadMoreVisibility(r, false);
                                wpdiscuzLoadCount = 1;
                            }
                        }
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    });
        }
    });
//============================== /SORTING ============================== // 
//============================== SINGLE COMMENT ============================== // 
    function getSingleComment(showReply) {
        var loc = location.href;
        var matches = loc.match(/#comment\-(\d+)/);
        if (matches !== null) {
            var commentId = matches[1];
            if (!$('#comment-' + commentId).length) {
                var data = new FormData();
                data.append('action', 'wpdGetSingleComment');
                data.append('commentId', commentId);
                getAjaxObj(isNativeAjaxEnabled, true, data)
                        .done(function (r) {
                            if (typeof r === 'object') {
                                if (r.success) {
                                    var scrollToSelector = '#comment-' + commentId;
                                    if ($('#comment-' + r.data.parentCommentID).length) {
                                        $('#comment-' + r.data.parentCommentID).parents('[id^=wpd-comm-' + r.data.parentCommentID + ']').replaceWith(r.data.message);
                                    } else {
                                        $('.wpd-thread-list').prepend(r.data.message);
                                    }

                                    runCallbacks(r);

                                    $('html, body').animate({
                                        scrollTop: $(scrollToSelector).offset().top - 32
                                    }, 1000);
                                    if (showReply) {
                                        showReplyForm(commentId);
                                    }
                                }
                            }
                            $('#wpdiscuz-loading-bar').fadeOut(250);
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                            $('#wpdiscuz-loading-bar').fadeOut(250);
                        });
            } else {
                setTimeout(function () {
                    $('html, body').animate({
                        scrollTop: $('#comment-' + commentId).parents('[id^=wpd-comm-]').offset().top - 32
                    }, 1000);
                    if (showReply) {
                        showReplyForm(commentId);
                    }
                }, 500);
            }
        }
    }
    window.onhashchange = function () {
        getSingleComment(false);
    };
    if (firstLoadWithAjax != 1) {
        getSingleComment(false);
    }
    function showReplyForm(commentId) {
        setTimeout(function () {
            if (!$('#comment-' + commentId).siblings('.wpd-secondary-form-wrapper').is(':visible')) {
                $('#comment-' + commentId).find('.wpd-reply-button').trigger('click');
            }
        }, 1100);
    }
//============================== /SINGLE COMMENT ============================== //
//============================== LIVE UPDATE ============================== // 
    function liveUpdate() {
        var data = new FormData();
        data.append('action', 'wpdUpdateAutomatically');
        data.append('loadLastCommentId', loadLastCommentId);
        data.append('visibleCommentIds', getVisibleCommentIds());
        getAjaxObj(isNativeAjaxEnabled, false, data)
                .done(function (r) {
                    if (!addingComment) {
                        if (typeof r === 'object') {
                            if (r.success) {
                                liveUpdateImmediately(r);
                                $('.wpd-thread-info').html(r.data.wc_all_comments_count_new_html);
                                r.data.wc_all_comments_count_new = parseInt(r.data.wc_all_comments_count_new);
                                $('#wpd-bubble-all-comments-count').text(r.data.wc_all_comments_count_new);
                                if (r.data.wc_all_comments_count_new) {
                                    $('#wpd-bubble-all-comments-count').show();
                                } else {
                                    $('#wpd-bubble-all-comments-count').hide();
                                }
                                loadLastCommentId = r.data.loadLastCommentId;
                            }
                        }
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                });
    }

    function liveUpdateImmediately(r) {
        if (r.data.message !== undefined) {
            var commentObject;
            var message = r.data.message;
            for (var i = 0; i < message.length; i++) {
                commentObject = message[i];
                addCommentToTree(commentObject.comment_parent, commentObject.comment_html);
            }
        }
    }
//============================== /LIVE UPDATE ============================== // 
//============================== READ MORE ============================== // 
    $(document).delegate('.wpdiscuz-readmore', 'click', function () {
        var uniqueId = getUniqueID($(this));
        var commentId = getCommentID(uniqueId);
        var data = new FormData();
        data.append('action', 'wpdReadMore');
        data.append('commentId', commentId);
        getAjaxObj(isNativeAjaxEnabled, true, data)
                .done(function (r) {
                    if (typeof r === 'object') {
                        if (r.success) {
                            $('#comment-' + commentId + ' .wpd-comment-text').replaceWith(' ' + r.data.message);
                            $('#wpdiscuz-readmore-' + uniqueId).remove();
                        } else {
                            console.log(r.data);
                        }
                        runCallbacks(r);
                    } else {
                        console.log(r);
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    });
//============================== /READ MORE ============================== // 
//============================== FUNCTIONS ============================== //

    function cloneSecondaryForm(field) {
        var uniqueId = getUniqueID(field, 0);
        $('#wpdiscuz_form_anchor-' + uniqueId).before(replaceUniqueId(uniqueId));
        var secondaryFormWrapper = $('#wpd-secondary-form-wrapper-' + uniqueId);
        if (!isUserLoggedIn) {
            var commentAuthorCookies = {
                comment_author: Cookies.get('comment_author_' + wpdiscuzCookiehash),
                comment_author_email: Cookies.get('comment_author_email_' + wpdiscuzCookiehash),
                comment_author_url: Cookies.get('comment_author_url_' + wpdiscuzCookiehash)
            };
            setCookieInForm(commentAuthorCookies);
        }
        if (wpdiscuzLoadRichEditor) {
            setTimeout(function () {
                wpDiscuzEditor.createEditor('#wpd-editor-' + uniqueId).focus();
            }, enableDropAnimation);
        } else {
            setTimeout(function () {
                $('#wc-textarea-' + uniqueId).trigger('focus');
            }, enableDropAnimation);
        }
        secondaryFormWrapper.slideToggle(enableDropAnimation, function () {
            field.addClass('wpdiscuz-clonned');
        });
    }

    function replaceUniqueId(uniqueId) {
        var secondaryForm = $('#wpdiscuz_hidden_secondary_form').html();
        return secondaryForm.replace(/wpdiscuzuniqueid/g, uniqueId);
    }

    function getUniqueID(field, isMain) {
        var fieldID = '';
        if (isMain) {
            fieldID = field.parents('.wpd-main-form-wrapper').attr('id');
        } else {
            fieldID = field.parents('.wpd-comment').attr('id');
        }
        var uniqueID = fieldID.substring(fieldID.lastIndexOf('-') + 1);
        return uniqueID;
    }

    function getCommentID(uniqueID) {
        return uniqueID.substring(0, uniqueID.indexOf('_'));
    }

    function getLastParentID() {
        return $('.wpd-load-more-submit').attr("data-lastparentid");
    }

    function setLastParentID(lastParentID) {
        $('.wpd-load-more-submit').attr("data-lastparentid", lastParentID);
        if (commentListLoadType !== 2) {
            $('.wpdiscuz-comment-pagination').show();
        }
    }

    function getCommentDepth(field) {
        var fieldClasses = field.attr('class');
        var classesArray = fieldClasses.split(' ');
        var depth = '';
        $.each(classesArray, function (index, value) {
            if ('wpd_comment_level' === getParentDepth(value, false)) {
                depth = getParentDepth(value, true);
            }
        });
        return parseInt(depth) + 1;
    }

    function getParentDepth(depthValue, isNumberPart) {
        var depth = '';
        if (isNumberPart) {
            depth = depthValue.substring(depthValue.indexOf('-') + 1);
        } else {
            depth = depthValue.substring(0, depthValue.indexOf('-'));
        }
        return depth;
    }

    function addCommentToTree(parentId, comment) {
        if (parentId == 0) {
            addCommentsAfterSticky(comment);
        } else {
            var parentUniqueId = getUniqueID($('#comment-' + parentId), 0);
            $('#wpdiscuz_form_anchor-' + parentUniqueId).after(comment);
        }
    }

    function getVisibleCommentIds() {
        var uniqueId;
        var commentId;
        var visibleCommentIds = '';
        $('.wpd-comment-right').each(function () {
            uniqueId = getUniqueID($(this), 0);
            commentId = getCommentID(uniqueId);
            visibleCommentIds += commentId + ',';
        });
        return visibleCommentIds;
    }

    function loginButtonsClone() {
        if ($('.wc_social_plugin_wrapper .wp-social-login-provider-list').length) {
            $('.wc_social_plugin_wrapper .wp-social-login-provider-list').clone().prependTo('#wpdiscuz_hidden_secondary_form > .wpd-form-wrapper >  .wpd-secondary-forms-social-content');
        } else if ($('.wc_social_plugin_wrapper .the_champ_login_container').length) {
            $('.wc_social_plugin_wrapper .the_champ_login_container').clone().prependTo('#wpdiscuz_hidden_secondary_form > .wpd-form-wrapper >  .wpd-secondary-forms-social-content');
        } else if ($('.wc_social_plugin_wrapper .social_connect_form').length) {
            $('.wc_social_plugin_wrapper .social_connect_form').clone().prependTo('#wpdiscuz_hidden_secondary_form > .wpd-form-wrapper >  .wpd-secondary-forms-social-content');
        } else if ($('.wc_social_plugin_wrapper .oneall_social_login_providers').length) {
            $('.wc_social_plugin_wrapper .oneall_social_login .oneall_social_login_providers').clone().prependTo('#wpdiscuz_hidden_secondary_form > .wpd-form-wrapper >  .wpd-secondary-forms-social-content');
        }
    }

    function wpdiscuzReset() {
        $('.wpdiscuz_reset').val("");
    }

    function wpdValidateFieldRequired(form, editorId) {
        var fieldsGroup = form.find('.wpd-required-group');
        if (wpdiscuzLoadRichEditor) {
            form.find('.wc_comment').val($(editorId + '>.ql-editor').html());
        }
        wpdSanitizeCommentText(form);
        $.each(fieldsGroup, function () {
            $('input', this).removeAttr('required');
            var checkedFields = $('input:checked', this);
            if (checkedFields.length === 0) {
                $('input', $(this)).attr('required', 'required');
            } else {
                $('.wpd-field-invalid', this).remove();
            }
        });
    }

    function wpdSanitizeCommentText(form) {
        var textarea = form.find('.wc_comment');
        var commentText = textarea.val().trim();
        var replacedText = commentText.replace(/<p><br><\/p>/g, "\n").replace(/<p>(.*?)<\/p>/g, "$1\n");
        replacedText = replacedText.replace(/<img src=["|']https\:\/\/s\.w\.org\/images\/core\/emoji\/([^"|']+)["|'](.*?)alt=["|']([^"|']+)["|'](.*?)[^>]*>/g, " $3 ");
        replacedText = replacedText.replace(/<img[^>]+alt=["|']([^"|']+)["|'][^>]+src=["|']https\:\/\/s\.w\.org\/images\/core\/emoji\/([^"|']+)["|'][^>]?>/g, " $1 ");
        replacedText = replacedText.replace(/<img\s+([^>]*)class=["|']wpdem\-sticker["|'](.*?)alt=["|']([^"|']+)["|'](.*?)[^>]*>/g, " $3 ");
        replacedText = replacedText.replace(/<img\s+([^>]*)src=["|']([^"|']+)["|'](.*?)[^>]*>/g, " $2 ");
        textarea.val(replacedText);
    }

    $(document).delegate('.wpd-required-group', 'change', function () {
        if ($('input:checked', this).length !== 0) {
            $('input', $(this)).removeAttr('required');
        } else {
            $('input', $(this)).attr('required', 'required');
        }
    });

    /* SPOILER */
    $(document).delegate('.wpdiscuz-spoiler', 'click', function () {
        $(this).next().slideToggle();
        if ($(this).hasClass('wpdiscuz-spoiler-closed')) {
            $(this).parents('.wpdiscuz-spoiler-wrap').find('.fa-plus').removeClass('fa-plus').addClass('fa-minus');
        } else {
            $(this).parents('.wpdiscuz-spoiler-wrap').find('.fa-minus').removeClass('fa-minus').addClass('fa-plus');
        }
        $(this).toggleClass('wpdiscuz-spoiler-closed');
    });

    function wpdiscuzShowReplies(uniqueId, btn) {
        var commentId = getCommentID(uniqueId);
        var data = new FormData();
        data.append('action', 'wpdShowReplies');
        data.append('commentId', commentId);
        getAjaxObj(isNativeAjaxEnabled, true, data)
                .done(function (r) {
                    btn.addClass('wpd_not_clicked');
                    if (typeof r === 'object') {
                        if (r.success) {
                            $('#wpd-comm-' + uniqueId).replaceWith(r.data.comment_list);
                            $('#wpd-comm-' + uniqueId + ' .wpd-toggle .fas').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                            $('#wpd-comm-' + uniqueId + ' .wpd-toggle').attr('wpd-tooltip', wpdiscuzAjaxObj.wc_hide_replies_text);
                            $('#wpd-comm-' + uniqueId + ' .wpd-toggle .wpd-view-replies').remove();

                            runCallbacks(r);
                        }
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    btn.addClass('wpd_not_clicked');
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    }

    $(document).delegate('.wpd-tools i', 'click', function () {
        var sibling = $(this).siblings('.wpd-tools-actions');
        sibling.css({display: sibling.is(':visible') ? 'none' : 'flex'});
    });
    $(document).delegate('.wpd-comment-right', 'mouseleave', function () {
        $(this).find('.wpd-tools-actions').hide();
    });

    $(document).delegate('.wpd_stick_btn', 'click', function () {
        var uniqueId = getUniqueID($(this), 0);
        var commentId = getCommentID(uniqueId);
        var data = new FormData();
        data.append('action', 'wpdStickComment');
        data.append('commentId', commentId);
        getAjaxObj(isNativeAjaxEnabled, true, data)
                .done(function (r) {
                    if (typeof r === 'object') {
                        if (r.success) {
                            location.reload(true);
                        }
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    });

    $(document).delegate('.wpd_close_btn', 'click', function () {
        var uniqueId = getUniqueID($(this), 0);
        var commentId = getCommentID(uniqueId);
        var data = new FormData();
        data.append('action', 'wpdCloseThread');
        data.append('commentId', commentId);
        getAjaxObj(isNativeAjaxEnabled, true, data)
                .done(function (r) {
                    if (typeof r === 'object') {
                        if (r.success) {
                            location.reload(true);
                        }
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    });

    $(document).delegate('.wpd-filter.wpd_not_clicked[data-filter-type]', 'click', function () {
        var btn = $(this);
        var type = btn.attr('data-filter-type');
        wpdiscuzAjaxObj.resetActiveFilters('.wpdf-' + type);
        btn.removeClass('wpd_not_clicked');
        $('.fas', btn).addClass('fa-pulse fa-spinner');
        var data = new FormData();
        data.append('action', 'wpdLoadMoreComments');
        var sorting = $('.wpdiscuz-sort-button-active').attr('data-sorting');
        if (sorting) {
            data.append('sorting', sorting);
        }
        data.append('lastParentId', 0);
        data.append('offset', 0);
        wpdiscuzLoadCount = 1;
        data.append('wpdType', btn.hasClass('wpdf-active') ? '' : type);
        data.append('isFirstLoad', 1);
        if ($(this).hasClass('wpdf-inline')) {
            if ($(this).hasClass('wpdf-active')) {
                $('.wpd-comment-info-bar').hide();
            } else {
                $('.wpd-comment-info-bar').css('display', 'flex');
            }
        } else {
            $('.wpd-comment-info-bar').hide();
        }
        getAjaxObj(isNativeAjaxEnabled, false, data)
                .done(function (r) {
                    btn.addClass('wpd_not_clicked');
                    $('.fas', btn).removeClass('fa-pulse fa-spinner');
                    if (typeof r === 'object') {
                        if (r.success) {
                            btn.toggleClass('wpdf-active');
                            $('.wpd-load-comments').remove();
                            $('.wpd-comment').remove();
                            $('.wpd-thread-list').prepend(r.data.comment_list);
                            setLoadMoreVisibility(r);
                            loadLastCommentId = r.data.loadLastCommentId;
                            $('.wpd-load-more-submit').blur();
                            runCallbacks(r);
                        }
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    });

    $(document).delegate('.wpdf-reacted.wpd_not_clicked', 'click', function () {
        var btn = $(this);
        btn.removeClass('wpd_not_clicked');
        $('.fas', btn).addClass('fa-pulse fa-spinner');
        var data = new FormData();
        data.append('action', 'wpdMostReactedComment');
        getAjaxObj(isNativeAjaxEnabled, false, data)
                .done(function (r) {
                    btn.addClass('wpd_not_clicked');
                    $('.fas', btn).removeClass('fa-pulse fa-spinner');
                    if (typeof r === 'object') {
                        if (r.success) {
                            if ($('#comment-' + r.data.parentCommentID).length) {
                                $('#comment-' + r.data.parentCommentID).parents('[id^=wpd-comm-' + r.data.parentCommentID + ']').replaceWith(r.data.message);
                            } else if (!$('#comment-' + r.data.commentId).length) {
                                $('.wpd-thread-list').prepend(r.data.message);
                            }

                            runCallbacks(r);
                            $('html, body').animate({
                                scrollTop: $('#comment-' + r.data.commentId).offset().top - 32
                            }, 1000);
                        }
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('.fas', btn).removeClass('fa-pulse fa-spinner');
                });
    });

    $(document).delegate('.wpdf-hottest.wpd_not_clicked', 'click', function () {
        var btn = $(this);
        btn.removeClass('wpd_not_clicked');
        $('.fas', btn).addClass('fa-pulse fa-spinner');
        var data = new FormData();
        data.append('action', 'wpdHottestThread');
        getAjaxObj(isNativeAjaxEnabled, false, data)
                .done(function (r) {
                    btn.addClass('wpd_not_clicked');
                    $('.fas', btn).removeClass('fa-pulse fa-spinner');
                    if (typeof r === 'object') {
                        if (r.success) {
                            if ($('#comment-' + r.data.commentId).length) {
                                $('#comment-' + r.data.commentId).parents('[id^=wpd-comm-' + r.data.commentId + ']').replaceWith(r.data.message);
                            } else {
                                $('.wpd-thread-list').prepend(r.data.message);
                            }

                            runCallbacks(r);

                            $('html, body').animate({
                                scrollTop: $('#comment-' + r.data.commentId).offset().top - 32
                            }, 1000);
                        }
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('.fas', btn).removeClass('fa-pulse fa-spinner');
                });
    });

    $(document).delegate('.wpd-filter-view-all', 'click', function () {
        $('.wpdf-inline.wpdf-active.wpd_not_clicked').trigger('click');
    });


    function addAgreementInCookie(wcForm) {
        $('.wpd-agreement-checkbox', wcForm).each(function () {
            if ($(this).hasClass('wpd_agreement_hide') && isCookiesEnabled && $(this).prop('checked')) {
                Cookies.set($(this).attr('name') + '_' + wpdiscuzCookiehash, 1, {expires: 30, path: '/'});
                $('input[name=' + $(this).attr('name') + ']').each(function () {
                    wpdiscuzAgreementFields.push($(this));
                });
            }
        });
    }

    function deleteAgreementFields() {
        if (wpdiscuzAgreementFields.length) {
            wpdiscuzAgreementFields.forEach(function (item) {
                item.parents('.wpd-field-checkbox').remove();
            });
            wpdiscuzAgreementFields = [];
        }
    }

    $(document).delegate('.wpd-follow-link.wpd_not_clicked', 'click', function () {
        var btn = $(this);
        btn.removeClass('wpd_not_clicked');
        $('.fas', btn).addClass('fa-pulse fa-spinner');
        var uniqueId = getUniqueID(btn, 0);
        var commentId = getCommentID(uniqueId);
        var data = new FormData();
        data.append('action', 'wpdFollowUser');
        data.append('commentId', commentId);
        getAjaxObj(isNativeAjaxEnabled, true, data)
                .done(function (r) {
                    btn.addClass('wpd_not_clicked');
                    if (typeof r === 'object') {
                        if (r.success) {
                            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data.code], 'success');
                            btn.removeClass('wpd-follow-active');
                            if (r.data.followTip) {
                                btn.attr('wpd-tooltip', r.data.followTip)
                            }
                            if (r.data.followClass) {
                                btn.addClass(r.data.followClass);
                            }
                        } else {
                            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data], 'error');
                        }
                    } else {
                        console.log(r);
                    }
                    $('.fas', btn).removeClass('fa-pulse fa-spinner');
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('.fas', btn).removeClass('fa-pulse fa-spinner');
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    });

    function addCommentsAfterSticky(comment) {
        if ($('.wpd-sticky-comment').last()[0]) {
            $(comment).insertAfter($('.wpd-sticky-comment').last()[0]);
        } else {
            $('.wpd-thread-list').prepend(comment);
        }
    }

    function showHideNotificationType(current) {
        if (current) {
            if (!current.prop('required')) {
                if (current.val()) {
                    current.parents('form').find('[name=wpdiscuz_notification_type]').parent().css('display', 'inline-block');
                } else {
                    current.parents('form').find('[name=wpdiscuz_notification_type]').parent().css('display', 'none');
                }
            }
        } else {
            $.each($('.wc_email'), function (i, val) {
                var obj = $(val);
                if (!obj.prop('required')) {
                    if (obj.val()) {
                        obj.parents('form').find('[name=wpdiscuz_notification_type]').parent().css('display', 'inline-block');
                    } else {
                        obj.parents('form').find('[name=wpdiscuz_notification_type]').parent().css('display', 'none');
                    }
                }
            });
        }
    }
    showHideNotificationType();

    $(document).delegate('.wc_email', 'keyup', function () {
        showHideNotificationType($(this));
    });

//========================= BUBBLE =====================//
    if (bubbleEnabled && $('#wpdcom').length) {
        $('#wpd-bubble-wrapper').hover(function () {
            $(this).addClass('wpd-bubble-hover');
        }, function () {
            $(this).removeClass('wpd-bubble-hover');
        });
        if (bubbleHintTimeout && !Cookies.get(wpdiscuzAjaxObj.cookieHideBubbleHint)) {
            setTimeout(function () {
                $('#wpd-bubble-wrapper').addClass('wpd-bubble-hover');
                Cookies.set(wpdiscuzAjaxObj.cookieHideBubbleHint, '1', {expires: 7, path: '/'});
                setTimeout(function () {
                    $('#wpd-bubble-wrapper').removeClass('wpd-bubble-hover');
                }, bubbleHintHideTimeout * 1000);
            }, bubbleHintTimeout * 1000);
        }
        if ('content_left' === bubbleLocation) {
            if ($('.entry-content').length) {
                var left = Math.min($('.entry-content').offset().left, $('#wpdcom').offset().left) - 120;
                var bubbleLeft = left > 25 ? left : 25;
                $('#wpd-bubble-wrapper').css({left: bubbleLeft + 'px'});
                $('#wpd-bubble-wrapper').addClass('wpd-left-content');
            } else if ($('.post-entry').length) {
                var left = Math.min($('.post-entry').offset().left, $('#wpdcom').offset().left) - 120;
                var bubbleLeft = left > 25 ? left : 25;
                $('#wpd-bubble-wrapper').css({left: bubbleLeft + 'px'});
                $('#wpd-bubble-wrapper').addClass('wpd-left-content');
            } else if ($('.container').length) {
                var left = Math.min($('.container').offset().left, $('#wpdcom').offset().left) - 120;
                var bubbleLeft = left > 25 ? left : 25;
                $('#wpd-bubble-wrapper').css({left: bubbleLeft + 'px'});
                $('#wpd-bubble-wrapper').addClass('wpd-left-content');
            } else {
                $('#wpd-bubble-wrapper').css({left: '25px'});
                $('#wpd-bubble-wrapper').addClass('wpd-left-corner');
            }
        } else if ('left_corner' === bubbleLocation) {
            $('#wpd-bubble-wrapper').css({left: '25px'});
            $('#wpd-bubble-wrapper').addClass('wpd-left-corner');
        } else if ('right_corner' === bubbleLocation) {
            $('#wpd-bubble-wrapper').css({right: '25px'});
            $('#wpd-bubble-wrapper').addClass('wpd-right-corner');
        }

        $('#wpd-bubble-wrapper').show();

        $('#wpd-bubble-add-message-close').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            $('#wpd-bubble-wrapper').removeClass('wpd-bubble-hover');
        });

        $('#wpd-bubble').click(function () {
            $('html, body').animate({
                scrollTop: $('#wpdcom').offset().top - 60
            }, 1000, function () {
                $('#wpd-bubble-wrapper').removeClass('wpd-bubble-hover');
                if (wpdiscuzLoadRichEditor) {
                    if ($('#wpd-editor-0_0').length) {
                        wpDiscuzEditor.createEditor('#wpd-editor-0_0').focus();
                    }
                } else if ($('#wc-textarea-0_0').length) {
                    $('#wc-textarea-0_0').focus();
                }
            });
        });

        $('#wpd-bubble-comment-close').click(function (e) {
            e.preventDefault();
            $('#wpd-bubble-notification-message').hide();
            $('#wpd-bubble-wrapper').removeClass('wpd-new-comment-added');
        });

        $('#wpd-bubble-comment-reply-link a').click(function () {
            var href = $(this).attr('href');
            setTimeout(function () {
                $('#wpd-bubble-notification-message').hide();
                $('#wpd-bubble-wrapper').removeClass('wpd-new-comment-added');
                getSingleComment(true);
                var commentId = href.match(/#comment\-(\d+)/);
                bubbleNewCommentIds = bubbleNewCommentIds.filter(function (val) {
                    return val != commentId[1];
                });
                $('#wpd-bubble-count .wpd-new-comments-count').text(bubbleNewCommentIds.length);
                if (bubbleNewCommentIds.length == 0) {
                    $('#wpd-bubble-count').removeClass('wpd-new-comments');
                }
            }, 100);
        });

        $('#wpd-bubble-count').click(function () {
            if (bubbleNewCommentIds.length) {
                var data = new FormData();
                data.append('action', 'wpdBubbleUpdate');
                data.append('newCommentIds', bubbleNewCommentIds.join());
                getAjaxObj(isNativeAjaxEnabled, true, data)
                        .done(function (r) {
                            if (typeof r === 'object') {
                                if (r.success) {
                                    r.data.message = r.data.message.filter(function (comment) {
                                        if (!$('#comment-' + comment.comment_id).length) {
                                            return comment;
                                        }
                                    });
                                    liveUpdateImmediately(r);
                                    $('#wpd-bubble-count').removeClass('wpd-new-comments');
                                    $('#wpd-bubble-count .wpd-new-comments-count').text('0');
                                    bubbleNewCommentIds = [];
                                    $('html, body').animate({
                                        scrollTop: $($('.wpd-new-loaded-comment')[0]).offset().top - 60
                                    }, 1000);
                                    runCallbacks(r);
                                }
                            }
                            $('#wpdiscuz-loading-bar').fadeOut(250);
                        })
                        .fail(function (jqXHR, textStatus, errorThrown) {
                            console.log(errorThrown);
                        });
            }
        });

    }
    function bubbleAjax() {
        $.ajax({
            type: 'GET',
            url: wpdiscuzAjaxObj.bubbleUpdateUrl,
            data: {
                postId: wpdiscuzPostId,
                lastId: bubbleLastCommentId,
                visibleCommentIds: getVisibleCommentIds(),
            }
        }).done(function (r) {
            if (!addingComment) {
                if (typeof r === 'object') {
                    if (r.ids.length) {
                        if (commentListUpdateType) {
                            liveUpdate();
                        }
                        r.ids = r.ids.filter(function (id) {
                            if (!$('#comment-' + id).length) {
                                return id;
                            }
                        });
                        var timeout = 5000;
                        bubbleLastCommentId = parseInt(r.ids[r.ids.length - 1]);
                        bubbleNewCommentIds = bubbleNewCommentIds.concat(r.ids);
                        if (bubbleShowNewCommentMessage && r.commentText) {
                            $('#wpd-bubble-author-avatar').html(r.avatar);
                            $('#wpd-bubble-author-name').html(r.authorName);
                            $('#wpd-bubble-comment-date span').html(r.commentDate);
                            $('#wpd-bubble-comment-text').html(r.commentText);
                            $('#wpd-bubble-comment-reply-link a').attr('href', r.commentLink);
                            $('#wpd-bubble-notification-message').show();
                            timeout = 10000;
                        }
                        var count = parseInt($('.wpd-new-comments-count').text());
                        count += r.ids.length;
                        $('#wpd-bubble-wrapper').removeClass('wpd-new-comment-added');
                        $('#wpd-bubble-wrapper').addClass('wpd-new-comment-added');
                        setTimeout(function () {
                            $('#wpd-bubble-notification-message').hide();
                            $('#wpd-bubble-wrapper').removeClass('wpd-new-comment-added');
                        }, timeout);
                        $('.wpd-new-comments-count').text(count);
                        $('#wpd-bubble-count').addClass('wpd-new-comments');
                        r.all_comments_count = parseInt(r.all_comments_count);
                        $('#wpd-bubble-all-comments-count').text(r.all_comments_count);
                        if (r.all_comments_count) {
                            $('#wpd-bubble-all-comments-count').show();
                        } else {
                            $('#wpd-bubble-all-comments-count').hide();
                        }
                        $('.wpd-thread-info').html(r.all_comments_count_html);
                    }
                } else {
                    console.log(r);
                }
            }
            setTimeout(bubbleAjax, commentListUpdateTimer);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
            setTimeout(bubbleAjax, commentListUpdateTimer);
        });
    }
    if (((bubbleEnabled && bubbleLiveUpdate) || commentListUpdateType) && (isUserLoggedIn || (!isUserLoggedIn && enableGuestsLiveUpdate))) {
        setTimeout(bubbleAjax, commentListUpdateTimer);
    }
//========================= /BUBBLE =====================//
//========================= INLINE COMMENTS =====================//
    if ($('.wpd-inline-form-wrapper').length) {
        var data = new FormData();
        data.append('action', 'wpdGetInlineCommentForm');
        getAjaxObj(isNativeAjaxEnabled, false, data)
                .done(function (r) {
                    if (typeof r === 'object') {
                        if (r.success) {
                            $('.wpd-inline-form-wrapper').append(r.data);
                            $.each($('[name=_wpd_inline_nonce]'), function () {
                                var id = $(this).attr('id');
                                var parentId = $(this).parents('.wpd-inline-shortcode').attr('id');
                                $(this).attr('id', id + '-' + parentId.substring(parentId.lastIndexOf('-') + 1));
                            });
                            $('.wpd-inline-opened').addClass('wpd-active');
                            $('.wpd-inline-opened').find('.wpd-inline-form-wrapper').show();
                            $('.wpd-inline-opened').find('.wpd-inline-icon').addClass('wpd-open');
                            $('.wpd-inline-opened').find('.wpd-inline-icon').removeClass('wpd-ignored');
                            fixInlineFormsPosition();
                        } else {
                            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data], 'error');
                        }
                    } else {
                        console.log(r);
                    }
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                });
    }
    $(document).delegate('body', 'click', function (e) {
        if ($(e.target).hasClass('wpd-inline-form-close') || $(e.target).parents('.wpd-inline-form-close').length) {
            e.preventDefault();
            $(e.target).parents('.wpd-inline-form-wrapper').hide();
            $(e.target).parents('.wpd-inline-shortcode').removeClass('wpd-active');
            $(e.target).parents('.wpd-inline-form-wrapper').siblings('.wpd-inline-icon').removeClass('wpd-open');
        } else if (!$(e.target).hasClass('wpd-inline-form-wrapper') && !$(e.target).parents('.wpd-inline-form-wrapper').length) {
            hideInlineForms();
            var currentEl = '';
            if ($(e.target).hasClass('wpd-inline-icon')) {
                currentEl = $(e.target);
            } else if ($(e.target).parents('.wpd-inline-icon').length) {
                currentEl = $(e.target).parents('.wpd-inline-icon');
            }
            if (currentEl.length) {
                currentEl.parents('.wpd-inline-shortcode').addClass('wpd-active');
                currentEl.siblings('.wpd-inline-form-wrapper').show();
                currentEl.addClass('wpd-open');
                currentEl.removeClass('wpd-ignored');
                fixInlineFormsPosition(currentEl.siblings('.wpd-inline-form-wrapper'));
            }
        }
        if ((!$(e.target).hasClass('wpd-last-inline-comments-wrapper') && !$(e.target).parents('.wpd-last-inline-comments-wrapper').length) || ($(e.target).parents('.wpd-last-inline-comments-wrapper').length && $(e.target).hasClass('wpd-load-inline-comment'))) {
            $('.wpd-last-inline-comments-wrapper').remove();
        }
    });
    $(document).delegate('.wpd-inline-submit.wpd_not_clicked', 'click', function (e) {
        e.preventDefault();
        var clickedButton = $(this);
        var form = $(this).parents('.wpd_inline_comm_form');
        if (form[0].checkValidity()) {
            $(this).removeClass('wpd_not_clicked');
            var data = new FormData();
            data.append('action', 'wpdAddInlineComment');
            data.append('inline_form_id', getInlineFormId(form));
            $.each($('input, textarea', form), function (i, val) {
                if (this.type === 'checkbox') {
                    if ($(this).is(':checked')) {
                        data.append($(val).attr('name'), $(val).val());
                    }
                } else {
                    data.append($(val).attr('name'), $(val).val());
                }
            });
            getAjaxObj(isNativeAjaxEnabled, true, data)
                    .done(function (r) {
                        clickedButton.addClass('wpd_not_clicked');
                        if (typeof r === 'object') {
                            if (r.success) {
                                form[0].reset();
                                hideInlineForms();
                                var newCount = parseInt(r.data.newCount);
                                var countEl = clickedButton.parents('.wpd-inline-icon-wrapper').find('.wpd-inline-icon-count');
                                countEl.text(newCount);
                                if (newCount) {
                                    countEl.addClass('wpd-has-comments');
                                } else {
                                    countEl.removeClass('wpd-has-comments');
                                }
                                $('.wpd-thread-info').html(r.data.allCommentsCountNewHtml);
                                r.data.allCommentsCountNew = parseInt(r.data.allCommentsCountNew);
                                $('#wpd-bubble-all-comments-count').text(r.data.allCommentsCountNew);
                                if (r.data.allCommentsCountNew) {
                                    $('#wpd-bubble-all-comments-count').show();
                                } else {
                                    $('#wpd-bubble-all-comments-count').hide();
                                }
                                if (r.data.message) {
                                    addCommentsAfterSticky(r.data.message);
                                }
                                wpdiscuzAjaxObj.setCommentMessage(r.data.notification, 'success');
                            } else if (r.data) {
                                wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data], 'error');
                            }
                        } else {
                            wpdiscuzAjaxObj.setCommentMessage(r, 'error');
                        }
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    });
        }
    });
    $(document).delegate('.wpd-form', 'keydown', function (e) {
        if (e.ctrlKey && e.keyCode == 13) {
            $(this).find('.wc_comm_submit').trigger('click');
        }
    });
    $(document).delegate('#wpdiscuz-edit-form', 'keydown', function (e) {
        if (e.ctrlKey && e.keyCode == 13) {
            $(this).find('.wc_save_edited_comment').trigger('click');
        }
    });
    $(document).delegate('.wpd-inline-comment-content', 'keydown', function (e) {
        if (e.ctrlKey && e.keyCode == 13) {
            $(this).parents('.wpd_inline_comm_form').find('.wpd-inline-submit.wpd_not_clicked').trigger('click');
        }
    });
    $(document).delegate('.wpd-inline-icon-count.wpd-has-comments', 'click', function () {
        var clickedButton = $(this);
        var data = new FormData();
        data.append('action', 'wpdGetLastInlineComments');
        data.append('inline_form_id', getInlineFormId(clickedButton));
        getAjaxObj(isNativeAjaxEnabled, true, data)
                .done(function (r) {
                    if (typeof r === 'object') {
                        if (r.success) {
                            $(r.data).insertAfter(clickedButton);
                        } else {
                            wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data], 'error');
                        }
                    } else {
                        console.log(r);
                    }
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                })
                .fail(function (jqXHR, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $('#wpdiscuz-loading-bar').fadeOut(250);
                });
    });
    $(document).delegate('.wpd-view-all-inline-comments', 'click', function (e) {
        e.preventDefault();
        $(this).parents('.wpd-last-inline-comments-wrapper').remove();
        if (!$('.wpdf-inline').hasClass('wpdf-active')) {
            $('.wpdf-inline').trigger('click');
        }
        $('html, body').animate({
            scrollTop: $('.wpdf-inline').offset().top - 32
        }, 1000);
    });
    $(document).delegate('.wpd-feedback-content-link', 'click', function (e) {
        e.preventDefault();
        var feedbackId = $(this).data('feedback-content-id');
        $('html, body').animate({
            scrollTop: $('#wpd-inline-' + feedbackId).offset().top - 38
        }, 1000, function () {
            $('#wpd-inline-' + feedbackId).addClass('wpd-active');
        });
    });
    if (inlineFeedbackAttractionType === 'scroll_open' || inlineFeedbackAttractionType === 'blink') {
        inlineAttraction();
        $(window).scroll(inlineAttraction);
    }
    function getInlineFormId(el) {
        if ($(el).hasClass('wpd-inline-shortcode')) {
            var elId = $(el).attr('id');
        } else {
            var elId = $(el).parents('.wpd-inline-shortcode').attr('id');
        }
        return elId.substring(elId.lastIndexOf('-') + 1);
    }
    function hideInlineForms() {
        $('.wpd-inline-form-wrapper').hide();
        $('.wpd-inline-shortcode').removeClass('wpd-active');
        $('.wpd-inline-icon').removeClass('wpd-open');
    }
    function inlineAttraction() {
        $.each($('.wpd-inline-shortcode:not(.wpd-inline-opened) .wpd-inline-icon'), function () {
            var el = $(this);
            var diff = el.offset().top - window.pageYOffset;
            if (diff > 0 && diff < 300) {
                if (inlineFeedbackAttractionType === 'blink') {
                    el.addClass('wpd-blink');
                    setTimeout(function () {
                        el.removeClass('wpd-blink');
                    }, 3000);
                } else {
                    el.parents('.wpd-inline-shortcode').addClass('wpd-active');
                    el.siblings('.wpd-inline-form-wrapper').show();
                    el.addClass('wpd-open');
                    fixInlineFormsPosition(el.siblings('.wpd-inline-form-wrapper'));
                }
            }
        });
    }
    function fixInlineFormsPosition(form) {
        if (form) {
            if (form.offset().left <= 10) {
                form.css('left', Math.ceil(parseInt(form.css('left')) - form.offset().left + 10));
                var beforeLeft = Math.ceil(form.siblings('.wpd-inline-icon.wpd-open').offset().left - form.offset().left + 2);
                if (beforeLeft < 3) {
                    beforeLeft = 3;
                }
                document.styleSheets[0].addRule('#' + form.parents('.wpd-inline-shortcode').attr('id') + ' .wpd-inline-form-wrapper::before', 'left: ' + beforeLeft + 'px;');
            } else if (form.offset().left + form.width() > document.body.clientWidth - 10) {
                form.css('left', Math.ceil(parseInt(form.css('left')) + (document.body.clientWidth - (form.offset().left + form.width())) - 10));
                var beforeLeft = Math.ceil(form.siblings('.wpd-inline-icon.wpd-open').offset().left - form.offset().left + 2);
                if (beforeLeft > form.width() - 3) {
                    beforeLeft = form.width() - 3;
                }
                document.styleSheets[0].addRule('#' + form.parents('.wpd-inline-shortcode').attr('id') + ' .wpd-inline-form-wrapper::before', 'left: ' + beforeLeft + 'px;');
            }
        } else {
            $.each($('.wpd-inline-form-wrapper:visible'), function () {
                if ($(this).offset().left <= 10) {
                    $(this).css('left', Math.ceil(parseInt($(this).css('left')) - $(this).offset().left + 10));
                    var beforeLeft = Math.ceil($(this).siblings('.wpd-inline-icon.wpd-open').offset().left - $(this).offset().left + 2);
                    if (beforeLeft < 3) {
                        beforeLeft = 3;
                    }
                    document.styleSheets[0].addRule('#' + $(this).parents('.wpd-inline-shortcode').attr('id') + ' .wpd-inline-form-wrapper::before', 'left: ' + beforeLeft + 'px;');
                } else if ($(this).offset().left + $(this).width() > document.body.clientWidth - 10) {
                    $(this).css('left', Math.ceil(parseInt($(this).css('left')) + (document.body.clientWidth - ($(this).offset().left + $(this).width())) - 10));
                    var beforeLeft = Math.ceil($(this).siblings('.wpd-inline-icon.wpd-open').offset().left - $(this).offset().left + 2);
                    if (beforeLeft > $(this).width() - 3) {
                        beforeLeft = $(this).width() - 3;
                    }
                    document.styleSheets[0].addRule('#' + $(this).parents('.wpd-inline-shortcode').attr('id') + ' .wpd-inline-form-wrapper::before', 'left: ' + beforeLeft + 'px;');
                }
            });
        }
    }
//========================= /INLINE COMMENTS =====================//
//========================= POST RATING =====================//
    $(document).delegate('#wpd-post-rating.wpd-not-rated .wpd-rate-starts svg', 'click', function () {
        var data = new FormData();
        var rating = $(this).index();
        if (rating >= 0 && rating < 5) {
            data.append('action', 'wpdUserRate');
            data.append('rating', rating + 1);
            getAjaxObj(isNativeAjaxEnabled, true, data)
                    .done(function (r) {
                        if (typeof r === 'object') {
                            if (r.success) {
                                location.reload(true);
                            } else {
                                wpdiscuzAjaxObj.setCommentMessage(wpdiscuzAjaxObj[r.data], 'error');
                            }
                        } else {
                            console.log(r);
                        }
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    });
        }
    });
//========================= /POST RATING =====================//

    $('#wpdiscuz-subscribe-form').submit(function (e) {
        e.preventDefault();
        var wcForm = $(this);
        if (wcForm[0].checkValidity() && wpdReCaptchaValidateOnSubscribeForm(wcForm)) {
            var data = new FormData();
            data.append('action', 'wpdAddSubscription');
            var elements = $("*", wcForm);
            elements.each(function () {
                if (this.name != '' && this.type != 'checkbox' && this.type != 'radio') {
                    data.append(this.name + '', $(this).val());
                }
                if (this.type == 'checkbox' || this.type == 'radio') {
                    if ($(this).is(':checked')) {
                        data.append(this.name + '', $(this).val());
                    }
                }
            });
            getAjaxObj(isNativeAjaxEnabled, true, data)
                    .done(function (r) {
                        if (typeof r === 'object') {
                            if (r.success) {
                                wpdiscuzAjaxObj.setCommentMessage(r.data, 'success');
                                setTimeout(function () {
                                    location.reload(true);
                                }, 3000);
                            } else {
                                wpdiscuzAjaxObj.setCommentMessage(r.data, 'error');
                            }
                        } else {
                            wpdiscuzAjaxObj.setCommentMessage(r, 'error');
                        }
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                        addingComment = false;
                    })
                    .fail(function (jqXHR, textStatus, errorThrown) {
                        console.log(errorThrown);
                        $('#wpdiscuz-loading-bar').fadeOut(250);
                    });
        }
    });
    $('.wpd-unsubscribe').click(function (e) {
        e.preventDefault();
        var data = new FormData();
        data.append('action', 'wpdUnsubscribe');
        data.append('sid', $(this).data('sid'));
        data.append('skey', $(this).data('skey'));
        getAjaxObj(isNativeAjaxEnabled, true, data).done(function (r) {
            if (typeof r === 'object') {
                if (r.success) {
                    wpdiscuzAjaxObj.setCommentMessage(r.data, 'success');
                    setTimeout(function () {
                        location.reload(true);
                    }, 3000);
                } else {
                    wpdiscuzAjaxObj.setCommentMessage(r.data, 'error');
                }
            } else {
                console.log(r);
            }
            $('#wpdiscuz-loading-bar').fadeOut(250);
            addingComment = false;
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
            $('#wpdiscuz-loading-bar').fadeOut(250);
        });
    });

    wpdiscuzAjaxObj.resetActiveFilters = function (currentItemSelector) {
        $('.wpd-filter.wpdf-active' + (currentItemSelector ? ':not(' + currentItemSelector + ')' : '')).removeClass('wpdf-active');
    };

    function runCallbacks(r, commentID, voteType, wcForm) {
        if (r.data.callbackFunctions) {
            $.each(r.data.callbackFunctions, function (i) {
                if (typeof wpdiscuzAjaxObj[r.data.callbackFunctions[i]] === "function") {
                    wpdiscuzAjaxObj[r.data.callbackFunctions[i]](r, commentID, voteType, wcForm);
                } else {
                    console.log(r.data.callbackFunctions[i] + " is not a function");
                }
            });
        }
    }

    /**
     * @param {int/bool} isNative - use native or custom ajax
     * @param {bool} isShowTopLoading - show loading bar
     * @param {object} data - data for ajax request
     * @returns {jqXHR}
     */
    function getAjaxObj(isNative, isShowTopLoading, data) {
        if (isShowTopLoading) {
            $('#wpdiscuz-loading-bar').show();
        }
        data.append('postId', wpdiscuzPostId);
        var action = data.get('action');
        if (wpdiscuzAjaxObj.dataFilterCallbacks && wpdiscuzAjaxObj.dataFilterCallbacks[action]) {
            $.each(wpdiscuzAjaxObj.dataFilterCallbacks[action], function (i) {
                if (typeof wpdiscuzAjaxObj[wpdiscuzAjaxObj.dataFilterCallbacks[action][i]] === "function") {
                    data = wpdiscuzAjaxObj[wpdiscuzAjaxObj.dataFilterCallbacks[action][i]](data, isNative, isShowTopLoading);
                }
            });
        }
        var url = isNative ? wpdiscuzAjaxObj.url : wpdiscuzAjaxObj.customAjaxUrl;
        return $.ajax({
            type: 'POST',
            url: url,
            data: data,
            contentType: false,
            processData: false
        });
    }
    wpdiscuzAjaxObj.getAjaxObj = getAjaxObj;

});
//========================= reCAPTCHA =====================//
var onloadCallback = function () {
    if (document.getElementById('wpdiscuz-recaptcha-0_0') && wpdiscuzAjaxObj.wpDiscuzReCaptchaVersion === '2.0' && ((wpdiscuzAjaxObj.wc_captcha_show_for_guest == 1 && !wpdiscuzAjaxObj.is_user_logged_in) || (wpdiscuzAjaxObj.wc_captcha_show_for_members == 1 && wpdiscuzAjaxObj.is_user_logged_in))) {
        try {
            grecaptcha.render('wpdiscuz-recaptcha-0_0', {
                'sitekey': wpdiscuzAjaxObj.wpDiscuzReCaptchaSK,
                'theme': wpdiscuzAjaxObj.wpDiscuzReCaptchaTheme,
                'callback': function (response) {
                    jQuery('#wpdiscuz-recaptcha-field-0_0').val('key');
                },
                'expired-callback': function () {
                    jQuery('#wpdiscuz-recaptcha-field-0_0').val("");
                }
            });
        } catch (e) {
            console.log(e);
            wpdiscuzAjaxObj.setCommentMessage('reCaptcha Error: ' + e.message, 'error');
        }
    }
};
//========================= /reCAPTCHA =====================//
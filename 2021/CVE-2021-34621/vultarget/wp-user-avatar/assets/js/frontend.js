(function ($, window, undefined) {

    function Frontend() {

        var _this = this;

        this.init = function () {

            window.ppFormRecaptchaLoadCallback = this.recaptcha_processing;

            $('.pp-del-profile-avatar').click(this.delete_avatar);
            $('.pp-del-cover-image').click(this.delete_profile_image_cover);

            $(document).on('click', '.has-password-visibility-icon .pp-form-material-icons', this.toggle_password_visibility);

            // used by the WooCommerce module for toggling wc overridden checkout login form
            $(document.body).on('click', 'a.showlogin', function () {
                $(".pp_wc_login").slideToggle();
            });

            $(window).on('load resize', function () {
                _this.defaultUserProfileResponsive();
            });

            // only enable if pp_disable_ajax_form filter is false.
            if (pp_ajax_form.disable_ajax_form === 'true') return;

            $(document).on('submit', 'form[data-pp-form-submit="login"]', this.ajax_login);
            $(document).on('submit', 'form[data-pp-form-submit="signup"]', this.ajax_registration);
            $(document).on('submit', 'form[data-pp-form-submit="passwordreset"]', this.ajax_password_reset);
            $(document).on('submit', 'form[data-pp-form-submit="editprofile"]', this.ajax_edit_profile);
        };

        this.recaptcha_processing = function () {
            $('.pp-g-recaptcha').each(function (index, el) {
                var $site_key = $(el).attr('data-sitekey');
                var $form = $(this).parents('.pp-form-container').find('form');

                if ($(el).attr('data-type') === 'v3') {

                    $form.find('input.pp-submit-form').on('click', function (e) {
                        e.preventDefault();
                        _this._add_processing_label($form);
                        grecaptcha.ready(function () {
                            grecaptcha.execute($site_key, {action: 'form'}).then(function (token) {
                                $form.find('[name="g-recaptcha-response"]').remove();

                                $form.append($('<input>', {
                                    type: 'hidden',
                                    value: token,
                                    name: 'g-recaptcha-response'
                                }));

                                $form.submit();
                            });
                        });
                    });
                } else {
                    var widgetId1 = grecaptcha.render(el, {
                        'sitekey': $site_key,
                        'theme': $(el).attr('data-theme'),
                        'size': $(el).attr('data-size')
                    });

                    $form.on('pp_form_submitted', function () {
                        grecaptcha.reset(widgetId1)
                    });
                }
            });
        };

        this.toggle_password_visibility = function (e) {
            e.preventDefault();
            var input = $(this).parents('.pp-form-field-input-textarea-wrap').find('.pp-form-field');

            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                $(this).text('visibility_off')
            } else {
                input.attr('type', 'password');
                $(this).text('visibility')
            }
        };

        this.ajax_edit_profile = function (e) {

            if (typeof window.FormData === 'undefined' || !window.FormData) return;

            e.preventDefault();

            var $editprofile_form = $('form[data-pp-form-submit="editprofile"]');

            var melange_id = _this.get_melange_id($editprofile_form);

            var formData = new FormData(this);
            formData.append("action", "pp_ajax_editprofile");
            formData.append("nonce", pp_ajax_form.nonce);
            formData.append("melange_id", melange_id);

            // remove any prior edit profile error.
            $('.profilepress-edit-profile-status').remove();
            $('.profilepress-edit-profile-success').remove();

            // remove any prior status message. Fixes removal of message with custom class.
            if ("" !== window.edit_profile_msg_class) {
                $('.' + window.edit_profile_msg_class).remove();
            }

            _this._add_processing_label($editprofile_form);

            $.post({
                url: pp_ajax_form.ajaxurl,
                data: formData,
                cache: false,
                contentType: false,
                enctype: 'multipart/form-data',
                processData: false,
                dataType: 'json',
                success: function (response) {
                    $editprofile_form.trigger('pp_form_submitted');
                    $editprofile_form.trigger('pp_form_edit_profile_success', [$editprofile_form]);
                    if ("avatar_url" in response && response.avatar_url !== '') {
                        $("img[data-del='avatar'], img.pp-user-avatar").attr('src', response.avatar_url);
                        // remove the picture upload path text.
                        $('input[name=eup_avatar]', $editprofile_form).val('');
                    }

                    if ("cover_image_url" in response && response.cover_image_url !== '') {
                        $("img[data-del='cover-image'], img.pp-user-cover-image").attr('src', response.cover_image_url);
                        // remove the picture upload path text.
                        $('input[name=eup_cover_image]', $editprofile_form).val('');
                        $('.profilepress-myaccount-has-cover-image', $editprofile_form).show();
                        $('.profilepress-myaccount-cover-image-empty', $editprofile_form).hide();
                    }

                    if ('message' in response) {
                        // save the response error message class for early removal in next request.
                        window.edit_profile_msg_class = $(response.message).attr('class');

                        $editprofile_form.before(response.message);
                    }

                    if ('redirect' in response) {
                        $editprofile_form.trigger('pp_edit_profile_success_before_redirect');
                        window.location.assign(response.redirect)
                    }

                    _this._remove_processing_label($editprofile_form);
                }
            }, 'json');
        };

        this.ajax_password_reset = function (e) {
            e.preventDefault();

            var $passwordreset_form = $(this),
                melange_id = _this.get_melange_id($passwordreset_form),
                is_tab_widget = $passwordreset_form.find('input[name="is-pp-tab-widget"]').val() === 'true',
                ajaxData = {
                    action: 'pp_ajax_passwordreset',
                    // if this is melange, we need it ID thus "&melange_id".
                    data: $(this).serialize() + '&melange_id=' + melange_id
                };

            _this._remove_status_notice();

            $passwordreset_form.parents('.pp-tab-widget-form').prev('.pp-tab-status').remove();

            _this._add_processing_label($passwordreset_form);

            $.post(pp_ajax_form.ajaxurl, ajaxData, function (response) {

                $passwordreset_form.trigger('pp_form_submitted');
                // remove the processing label and do nothing if 0 is returned which perhaps means the user is
                // already logged in.
                if (typeof response !== 'object') {
                    return _this._remove_processing_label($passwordreset_form);
                }

                if ('message' in response) {
                    $passwordreset_form.trigger('pp_password_reset_status');
                    if (is_tab_widget) {
                        // tab widget has its own class for status notice/message which is pp-tab-status thus the replacement.
                        var notice = response.message.replace('profilepress-reset-status', 'pp-tab-status');
                        $passwordreset_form.parents('.pp-tab-widget-form').before(notice);
                    } else if ($passwordreset_form.parents('.lucidContainer').length > 0) {
                        $passwordreset_form.parents('.lucidContainer').before(response.message)
                    } else {
                        $passwordreset_form.before(response.message);
                    }

                    if ('status' in response && response.status === true) {
                        $passwordreset_form.hide();
                    }

                    $('input[name="user_login"]', $passwordreset_form).val('');
                }

                _this._remove_processing_label($passwordreset_form);

            }, 'json');
        };

        this.ajax_registration = function (e) {

            if (typeof window.FormData === 'undefined' || !window.FormData) return;

            e.preventDefault();

            var $signup_form = $(this),
                melange_id = _this.get_melange_id($signup_form),
                formData = new FormData(this),
                is_tab_widget = $signup_form.find('input[name="is-pp-tab-widget"]').val() === 'true';

            formData.append("action", "pp_ajax_signup");
            formData.append("melange_id", melange_id);

            _this._remove_status_notice();

            $signup_form.parents('.pp-tab-widget-form').prev('.pp-tab-status').remove();

            _this._add_processing_label($signup_form);

            $.post({
                url: pp_ajax_form.ajaxurl,
                data: formData,
                cache: false,
                contentType: false,
                enctype: 'multipart/form-data',
                processData: false,
                dataType: 'json',
                success: function (response) {
                    $signup_form.trigger('pp_form_submitted');
                    // remove the processing label and do nothing if 0 is returned which perhaps means the user is
                    // already logged in.
                    if (typeof response !== 'object') {
                        return _this._remove_processing_label($signup_form);
                    }

                    if ('message' in response) {
                        // backward compat. To be removed in future
                        $signup_form.trigger('pp_registration_error', [response]);

                        $signup_form.trigger('pp_registration_ajax_response', [response]);

                        if (is_tab_widget) {
                            // tab widget has its own class for status notice/message which is pp-tab-status thus the replacement.
                            var notice = response.message.replace('profilepress-reg-status', 'pp-tab-status');
                            $signup_form.parents('.pp-tab-widget-form').before(notice);
                        }
                        // if lucid tab widget
                        else if ($signup_form.parents('.lucidContainer').length > 0) {
                            $signup_form.parents('.lucidContainer').before(response.message)
                        } else {
                            $signup_form.before(response.message);
                        }
                    } else if ('redirect' in response) {
                        $signup_form.trigger('pp_registration_success', [response]);
                        window.location.assign(response.redirect)
                    }
                    _this._remove_processing_label($signup_form);
                }
            });
        };

        this.ajax_login = function (e) {
            e.preventDefault();

            var $login_form = $(this),
                ajaxData = {action: 'pp_ajax_login', data: $(this).serialize()},
                is_tab_widget = $login_form.find('input[name="is-pp-tab-widget"]').val() === 'true';

            _this._remove_status_notice();

            _this._add_processing_label($login_form);

            $.post(pp_ajax_form.ajaxurl, ajaxData, function (response) {
                $login_form.trigger('pp_form_submitted');
                // remove the processing label and do nothing if 0 is returned which perhaps means the user is
                // already logged in.
                // we are checking for null because response can be null hence we want the processing label removed.
                if (response === null || typeof response !== 'object') {
                    return _this._remove_processing_label($login_form);
                }

                if ('success' in response && response.success === true && 'redirect' in response) {
                    $login_form.trigger('pp_login_form_success');
                    window.location.assign(response.redirect)
                } else {
                    $login_form.trigger('pp_login_form_error');

                    if (is_tab_widget) {
                        // tab widget has its own class for status notice/message which is pp-tab-status thus the replacement.
                        var notice = response.message.replace('profilepress-login-status', 'pp-tab-status');
                        $login_form.parents('.pp-tab-widget-form').before(notice);
                    }
                    // if lucid tab widget
                    else if ($login_form.parents('.lucidContainer').length > 0) {
                        $login_form.parents('.lucidContainer').before(response.message)
                    } else {
                        $login_form.before(response.message);
                    }
                }

                _this._remove_processing_label($login_form);

            }, 'json');
        };

        this.delete_avatar = function (e) {

            e.preventDefault();

            var button_text = $(this).text(),
                this_obj = $(this);

            e.preventDefault();
            if (confirm(pp_ajax_form.confirm_delete)) {

                if (this_obj.is('button')) {
                    this_obj.text(pp_ajax_form.deleting_text);
                }

                $.post(pp_ajax_form.ajaxurl, {
                    action: 'pp_del_avatar',
                    nonce: pp_ajax_form.nonce
                }).done(function (data) {
                    if ('error' in data && data.error === 'nonce_failed') {
                        this_obj.text(button_text);
                        alert(pp_ajax_form.deleting_error);
                    } else if ('success' in data) {
                        $("img[data-del='avatar']").attr('src', data.default);
                        this_obj.remove();
                    }
                });

            }
        };

        this.delete_profile_image_cover = function (e) {

            e.preventDefault();

            var button_text = $(this).text(), this_obj = $(this);

            e.preventDefault();

            if (confirm(pp_ajax_form.confirm_delete)) {

                if (this_obj.is('button')) {
                    this_obj.text(pp_ajax_form.deleting_text);
                }

                $.post(pp_ajax_form.ajaxurl, {
                    action: 'pp_del_cover_image',
                    nonce: pp_ajax_form.nonce
                }).done(function (data) {
                    if ('error' in data && data.error === 'nonce_failed') {
                        this_obj.text(button_text);
                        alert(pp_ajax_form.deleting_error);
                    }

                    if ('success' in data) {
                        if (data.default !== '') {
                            $("img[data-del='cover-image']").attr('src', data.default);
                            this_obj.parent().find('.profilepress-myaccount-has-cover-image').show();
                            this_obj.parent().find('.profilepress-myaccount-cover-image-empty').hide();
                        } else {
                            this_obj.parent().find('.profilepress-myaccount-has-cover-image').hide();
                            this_obj.parent().find('.profilepress-myaccount-cover-image-empty').show();
                        }

                        this_obj.remove();
                    }
                });

            }
        };

        this.get_melange_id = function ($scope) {
            var melange_id = $('input.pp_melange_id', $scope).val();
            return melange_id === undefined ? '' : melange_id;
        };

        this._add_processing_label = function (obj) {
            var submit_btn = obj.find('input[data-pp-submit-label]');
            submit_btn.attr({
                'value': submit_btn.data('pp-processing-label'),
                'disabled': 'disabled',
            }).css("opacity", ".4");
        };

        this._remove_processing_label = function (obj) {
            var submit_btn = obj.find('input[data-pp-submit-label]');
            submit_btn.attr('value', submit_btn.data('pp-submit-label'));
            submit_btn.attr({
                'value': submit_btn.data('pp-submit-label'),
                // set to null to remove. See https://api.jquery.com/attr/#attr-attributeName-value
                'disabled': null,
            }).css("opacity", "");
        };

        this._remove_status_notice = function () {
            $('.profilepress-login-status,.pp-tab-status,.profilepress-edit-profile-success,.profilepress-edit-profile-status,.pp-reset-success,.profilepress-reset-status,.profilepress-reg-status').remove();
        };

        this.defaultUserProfileResponsive = function () {

            $('.ppress-default-profile, .pp-member-directory').each(function () {

                var obj = $(this),
                    element_width = obj.width();

                if (element_width <= 340) {

                    obj.removeClass('ppressui340');
                    obj.removeClass('ppressui500');
                    obj.removeClass('ppressui800');
                    obj.removeClass('ppressui960');

                    obj.addClass('ppressui340');

                } else if (element_width <= 500) {

                    obj.removeClass('ppressui340');
                    obj.removeClass('ppressui500');
                    obj.removeClass('ppressui800');
                    obj.removeClass('ppressui960');

                    obj.addClass('ppressui500');

                } else if (element_width <= 800) {

                    obj.removeClass('ppressui340');
                    obj.removeClass('ppressui500');
                    obj.removeClass('ppressui800');
                    obj.removeClass('ppressui960');

                    obj.addClass('ppressui800');

                } else if (element_width <= 960) {

                    obj.removeClass('ppressui340');
                    obj.removeClass('ppressui500');
                    obj.removeClass('ppressui800');
                    obj.removeClass('ppressui960');

                    obj.addClass('ppressui960');

                } else if (element_width > 960) {

                    obj.removeClass('ppressui340');
                    obj.removeClass('ppressui500');
                    obj.removeClass('ppressui800');
                    obj.removeClass('ppressui960');
                }

                obj.css('opacity', 1);
            });

            $('.ppress-default-profile-cover, .ppress-default-profile-cover-e').each(function () {

                var elem = $(this),
                    calcHeight = Math.round(elem.width() / elem.data('ratio')) + 'px';

                elem.height(calcHeight);
                elem.find('.ppress-dpf-cover-add').height(calcHeight);
            });
        };
    }

    (new Frontend()).init();

})(jQuery, window, undefined);
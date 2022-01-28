var wpcf7_redirect;

(function ($) {
    function Wpcf7_redirect() {
        this.init = function () {
            this.wpcf7_redirect_mailsent_handler();
        };

        this.wpcf7_redirect_mailsent_handler = function () {

            document.addEventListener('wpcf7mailsent', function (event) {

                var form = $(event.target);

                $(document.body).trigger('wpcf7r-mailsent', [event]);

                if (typeof event.detail.apiResponse != 'undefined' && event.detail.apiResponse) {
                    var apiResponse = event.detail.apiResponse;
                    var actionDelay = 0;

                    //handle api response
                    if (typeof apiResponse.api_url_request != 'undefined' && apiResponse.api_url_request) {
                        wpcf7_redirect.handle_api_action(apiResponse.api_url_request);
                    }

                    //handle api response
                    if (typeof apiResponse.api_json_xml_request != 'undefined' && apiResponse.api_json_xml_request) {
                        wpcf7_redirect.handle_api_action(apiResponse.api_json_xml_request);
                    }

                    //handle fire javascript action
                    if (typeof apiResponse.FireScript != 'undefined' && apiResponse.FireScript) {
                        actionDelay = typeof apiResponse.FireScript.delay_redirect != 'undefined' ? apiResponse.FireScript.delay_redirect : actionDelay;
                        window.setTimeout(function () {
                            wpcf7_redirect.handle_javascript_action(apiResponse.FireScript);
                        }, actionDelay);
                    }

                    //catch and handle popup action
                    if (typeof apiResponse.popup != 'undefined' && apiResponse.popup) {
                        wpcf7_redirect.handle_popups(apiResponse.popup);
                    }

                    //catch redirect to paypal
                    if (typeof apiResponse.redirect_to_paypal != 'undefined' && apiResponse.redirect_to_paypal) {
                        actionDelay = typeof apiResponse.redirect_to_paypal.delay_redirect != 'undefined' ? apiResponse.redirect_to_paypal.delay_redirect : actionDelay;
                        window.setTimeout(function () {
                            wpcf7_redirect.handle_redirect_action(apiResponse.redirect_to_paypal);
                        }, actionDelay);
                    }

                    //catch redirect action
                    if (typeof apiResponse.redirect != 'undefined' && apiResponse.redirect) {
                        actionDelay = typeof apiResponse.redirect.delay_redirect != 'undefined' ? apiResponse.redirect.delay_redirect : actionDelay;
                        window.setTimeout(function () {
                            wpcf7_redirect.handle_redirect_action(apiResponse.redirect);
                        }, actionDelay);
                    }
                }
            }, false);

            document.addEventListener('wpcf7invalid', function (event) {

                var form = $(event.target);

                $(document.body).trigger('wpcf7r-invalid', [event]);

                if (typeof event.detail.apiResponse != 'undefined' && event.detail.apiResponse) {
                    response = event.detail.apiResponse;
                    if (response.invalidFields) {
                        //support for multistep by ninja
                        wpcf7_redirect.ninja_multistep_mov_to_invalid_tab(event, response);
                    }
                }
            });
        };

        this.handle_popups = function (popups) {

            $(document.body).trigger('wpcf7r-before-open-popup', [event]);

            $.each(popups, function (k, popup) {

                var $new_elem = $(popup['popup-template']);

                $(document.body).append($new_elem);
                $(document.body).addClass(popup['template-name']);

                window.setTimeout(function () {
                    $(document.body).addClass('modal-popup-open');
                    $new_elem.addClass('is-open');
                }, 1000);

                $new_elem.find('.close-button').on('click', function () {

                    $new_elem.removeClass('is-open').addClass('fade');

                    $(document.body).removeClass('modal-popup-open');

                    window.setTimeout(function () {
                        $('.wpcf7r-modal').remove();
                        $(document.body).trigger('wpcf7r-popup-removed', [$new_elem]);
                    }, 4000);
                });

                $(document.body).trigger('wpcf7r-popup-appended', [$new_elem]);
            });

        }

        this.handle_api_action = function (send_to_api_result, request) {

            $.each(send_to_api_result, function (k, v) {
                if (!v.result_javascript) {
                    return;
                }
                response = typeof v.api_response != 'undefined' ? v.api_response : '';
                request = typeof v.request != 'undefined' ? v.request : '';
                eval(v.result_javascript);
            });
        };

        this.ninja_multistep_mov_to_invalid_tab = function (event, response) {

            if ($('.fieldset-cf7mls-wrapper').length) {
                var form = $(event.target);
                var first_invalid_field = response.invalidFields[0];
                var parent_step = $(first_invalid_field.into).parents('fieldset');

                form.find('.fieldset-cf7mls').removeClass('cf7mls_current_fs');
                parent_step.addClass('cf7mls_current_fs').removeClass('cf7mls_back_fs');
                if (form.find('.cf7mls_progress_bar').length) {
                    form.find('.cf7mls_progress_bar li').eq(form.find("fieldset.fieldset-cf7mls").index(previous_fs)).addClass("current");
                    form.find('.cf7mls_progress_bar li').eq(form.find("fieldset.fieldset-cf7mls").index(current_fs)).removeClass("active current");
                }
            }
        }

        this.handle_redirect_action = function (redirect) {

            $(document.body).trigger('wpcf7r-handle_redirect_action', [redirect]);

            $.each(redirect, function (k, v) {
                var delay = typeof v.delay != 'undefined' && v.delay ? v.delay : '';

                delay = delay * 1000;

                window.setTimeout(function (v) {
                    var redirect_url = typeof v.redirect_url != 'undefined' && v.redirect_url ? v.redirect_url : '';
                    var type = typeof v.type != 'undefined' && v.type ? v.type : '';

                    if (typeof v.form != 'undefined' && v.form) {
                        $('body').append(v.form);
                        $('#cf7r-result-form').submit();
                    } else {

                        if (redirect_url && type == 'redirect') {
                            window.location = redirect_url;
                        } else if (redirect_url && type == 'new_tab') {
                            window.open(redirect_url);
                        }
                    }
                }, delay, v);

            });

        };

        this.handle_javascript_action = function (scripts) {

            $(document.body).trigger('wpcf7r-handle_javascript_action', [scripts]);

            $.each(scripts, function (k, script) {
                eval(script); //not using user input
            });

        };

        this.htmlspecialchars_decode = function (string) {

            var map = {
                '&amp;': '&',
                '&#038;': "&",
                '&lt;': '<',
                '&gt;': '>',
                '&quot;': '"',
                '&#039;': "'",
                '&#8217;': "’",
                '&#8216;': "‘",
                '&#8211;': "–",
                '&#8212;': "—",
                '&#8230;': "…",
                '&#8221;': '”'
            };

            return string.replace(/\&[\w\d\#]{2,5}\;/g, function (m) { return map[m]; });
        };

        this.init();
    }
    
    wpcf7_redirect = new Wpcf7_redirect();
})(jQuery);
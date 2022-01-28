(function ($) {
    var fb = {};

    fb.uiBlock = function () {
        $('#post-body-content').block({
            message: '<span class="spinner" style="visibility:visible;float:none;"></span>',
            css: {border: '0', backgroundColor: 'transparent'},
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.9
            }
        });
    };

    fb.uiUnBlock = function () {
        $('#post-body-content').unblock();
    };

    fb.fetchThemes = function () {
        $(document).on('click', '.pp-hald-first.ppbd-active', function (e) {
            e.preventDefault();
            $('.pp-half-meta-inner').removeClass('pp-builder-select-active');
            $(this).find('.pp-half-meta-inner').addClass('pp-builder-select-active');
            fb.uiBlock();
            var builder_type = $(this).data('builder-type');
            $('.pp-main-ajax-body').html('');

            $.post(
                ajaxurl, {
                    action: "pp_get_forms_by_builder_type",
                    data: builder_type
                }, function (response) {
                    $('.pp-main-ajax-body').html(response);
                    fb.uiUnBlock();
                    $('html, body').animate({
                        scrollTop: $(".pp-main-ajax-body .pp-form-new-list").offset().top
                    }, 1500);

                    new jBox('Tooltip', {
                        attach: '.pp-melange-jbox',
                        maxWidth: 200,
                        theme: 'TooltipDark'
                    });
                }
            );
        });
    };

    fb.filterFormByType = function () {
        $(document).on('click', '.pp-select-form-type', function (e) {
            e.preventDefault();
            $('.pp-select-form-type').removeClass('pp-type-active');
            $(this).addClass('pp-type-active');
            $('.pp-form-new-list .pp-dash-spinner').css('visibility', 'visible');

            var ajaxData = {
                action: 'pp_form_type_selection',
                nonce: $('#pp_plugin_nonce').val(),
                'form-type': $(this).attr('data-form-type').trim(),
                'builder-type': $(this).attr('data-builder-type').trim()
            };

            $.post(ajaxurl, ajaxData, function (response) {
                    if (typeof response === 'string') {
                        $('.pp-optin-themes').replaceWith(response);
                    }
                    $('.pp-form-new-list .pp-dash-spinner').css('visibility', 'hidden');
                }
            );
        });
    };

    fb.createShortcodeForm = function () {
        $(document).on('click', '.pp-optin-theme.ppress-allow-activate', function (e) {
            e.preventDefault();
            var form_title_obj = $('#pp-add-form-title');
            // remove input field error on change.
            form_title_obj.change(function () {
                form_title_obj.removeClass('pp-input-error');
            });

            if (!form_title_obj.val()) {
                form_title_obj.addClass('pp-input-error');
            } else {
                form_title_obj.removeClass('pp-input-error');
                $(".pp-error").remove();
                $('.pp-form-new-list .pp-dash-spinner').css('visibility', 'visible');

                var theme_class = typeof $(this).attr('data-theme-class') !== 'undefined' ? $(this).attr('data-theme-class').trim() : '';
                var theme_type = typeof $(this).attr('data-theme-type') !== 'undefined' ? $(this).attr('data-theme-type').trim() : '';

                var ajaxData = {
                    action: 'pp_create_form',
                    nonce: $('#pp_plugin_nonce').val(),
                    title: form_title_obj.val().trim(),
                    theme_class: theme_class,
                    theme_type: theme_type,
                    builder_type: $(this).attr('data-builder-type').trim()
                };

                $.post(ajaxurl, ajaxData, function (response) {
                        if (response.success && response.data.redirect) {
                            window.location.assign(response.data.redirect);
                        } else {
                            var error_msg = response.data ? response.data : '';
                            form_title_obj.after('<span class="pp-error">' + error_msg + '</span>');
                            $('.pp-form-new-list .pp-dash-spinner').css('visibility', 'hidden');
                        }
                    }, 'json'
                );
            }
        });
    };

    fb.fetchThemes();
    fb.createShortcodeForm();
    fb.filterFormByType();

})(jQuery);
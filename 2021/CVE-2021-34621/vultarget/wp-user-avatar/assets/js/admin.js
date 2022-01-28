(function ($) {

    /** @todo add nonce to all ajax requests */

    var sb = {
        ajax_flag: false,
        ajax_queue: []
    };

    sb.process_ajax_queue = function () {
        if (sb.ajax_queue.length === 0) return;
        var req = sb.ajax_queue.pop();
        sb.send_ajax(req.iframe_id, req.structure_codemirror_editor, req.css_codemirror_editor);
    };

    var currentRequest = null;

    sb.send_ajax = function (iframe_id, structure_codemirror_editor, css_codemirror_editor) {
        if (sb.ajax_flag === true) {
            sb.ajax_queue.push({
                iframe_id: iframe_id,
                structure_codemirror_editor: structure_codemirror_editor,
                css_codemirror_editor: css_codemirror_editor
            });

            return;
        }

        sb.ajax_flag = true;

        var builder_structure = '<div class="pp-password-reset-handler-wrap">' + structure_codemirror_editor.getValue() + '</div>';

        currentRequest = $.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                builder_structure: builder_structure,
                builder_css: css_codemirror_editor.getValue(),
                action: 'pp-builder-preview',
                _wpnonce: ppress_admin_globals.nonce
            },
            beforeSend: function () {
                if (currentRequest != null) {
                    currentRequest.abort();
                }
            },
        })
            .done(function (response) {
                sb.ajax_flag = false;
                var doc = document.getElementById(iframe_id).contentWindow.document;
                doc.open();
                doc.write(response);
                doc.close();

            }).always(function () {
                sb.process_ajax_queue();
            });
    };

    sb.codeMirrorInit = function (id, mode, lineNumbers) {
        lineNumbers = typeof lineNumbers !== 'undefined' ? lineNumbers : true;
        return CodeMirror.fromTextArea(id, {
            lineNumbers: lineNumbers,
            mode: mode
        });
    };

    sb.preview_request = function (builder_structure_id, builder_css_id, iframe_id) {

        iframe_id = iframe_id || 'indexIframe';

        if (document.getElementById(builder_css_id) === null) return;

        var structure_codemirror_editor = window.cmSettingsInstances[builder_structure_id] = sb.codeMirrorInit(document.getElementById(builder_structure_id), 'htmlmixed');
        var css_codemirror_editor = window.cmSettingsInstances[builder_css_id] = sb.codeMirrorInit(document.getElementById(builder_css_id), 'css');

        // detect if a change event is fired in codemirror editor.
        structure_codemirror_editor.on('change', _.debounce(function () {
            sb.send_ajax(iframe_id, structure_codemirror_editor, css_codemirror_editor);
            window.onbeforeunload = function () {
                return 'The changes you made will be lost if you navigate away from this page.';
            };
        }, 1500));

        // detect if a change event is fired in codemirror editor.
        css_codemirror_editor.on('change', function () {
            sb.send_ajax(iframe_id, structure_codemirror_editor, css_codemirror_editor);
            window.onbeforeunload = function () {
                return 'The changes you made will be lost if you navigate away from this page.';
            };
        });

        $('input[type="submit"]').click(function () {
            window.onbeforeunload = function (e) {
                e = null;
            };
        });

        $(window).load(function () {
            sb.send_ajax(iframe_id, structure_codemirror_editor, css_codemirror_editor);
        });


        if ($('#pp_password_handler_structure').length === 0) return;
        var password_handler_structure_codemirror_editor = window.cmSettingsInstances['pp_password_handler_structure'] = sb.codeMirrorInit(document.getElementById('pp_password_handler_structure'), 'htmlmixed');
        password_handler_structure_codemirror_editor.on('change', function () {
            sb.send_ajax('handlerIframe', password_handler_structure_codemirror_editor, css_codemirror_editor);
        });

        $(window).load(function () {
            sb.send_ajax('handlerIframe', password_handler_structure_codemirror_editor, css_codemirror_editor);
        });
    };

    sb.login = function () {
        sb.preview_request('pp_login_structure', 'pp_login_css');
    };

    sb.melange = function () {
        sb.preview_request('pp_melange_structure', 'pp_melange_css');
    };

    sb.password_reset = function () {
        sb.preview_request('pp_password_structure', 'pp_password_css');
    };

    sb.registration = function () {
        sb.preview_request('pp_registration_structure', 'pp_registration_css');
    };

    sb.frontend_profile = function () {
        sb.preview_request('pp_fe_profile_structure', 'pp_fe_profile_css');
    };

    sb.edit_profile = function () {
        sb.preview_request('pp_edit_profile_structure', 'pp_edit_profile_css');
    };

    var admin_sidebar_tab_settings = function () {

        var open_tab = function (tab_selector, control_view) {
            if ($(tab_selector).length === 0) return;

            $('.pp-settings-wrap .nav-tab-wrapper a').removeClass('nav-tab-active');
            $(tab_selector).addClass('nav-tab-active').blur();
            var clicked_group = $(tab_selector).attr('href');
            if (typeof (localStorage) !== 'undefined') {
                localStorage.setItem(option_name + "_active-tab", $(tab_selector).attr('href'));
            }
            $('.pp-group-wrapper').hide();
            $(clicked_group).fadeIn();

            if (typeof control_view !== 'undefined') {
                $('html, body').animate({
                    // we are removing 20 to accomodate admin bar which cut into view.
                    scrollTop: $("#" + control_view).offset().top - 20
                }, 2000);
            }

            // reset/remove hash from url
            window.location.hash = '';

            $.each(window.cmSettingsInstances, function (index, value) {
                value.refresh();
            });
        };

        var open_active_or_first_tab = function () {
            var active_tab = '';
            if (typeof (localStorage) !== 'undefined') {
                active_tab = localStorage.getItem(option_name + "_active-tab");
            }

            if (active_tab !== '' && $(active_tab).length) {
                active_tab += '-tab';
            } else {
                active_tab = $('.pp-settings-wrap .nav-tab-wrapper a:first')
            }

            open_tab(active_tab);
        };

        $('.pp-group-wrapper').hide();
        var option_name = $('div.pp-settings-wrap').data('option-name');

        $('.pp-settings-wrap .nav-tab-wrapper a').click(function (e) {
            e.preventDefault();
            open_tab(this);
        });

        var hash_event_triggered = false;

        $(window).on('hashchange', function () {
            if (hash_event_triggered === true) return;

            // in #registration_page?login_page, registration_page is the tab id and \
            // login_page the control/settings tr id.
            var hash = this.location.hash, tab_id_len, tab_id, cache;
            if (hash.length === 0) open_active_or_first_tab();

            if ((tab_id_len = hash.indexOf('?')) !== -1) {
                tab_id = hash.slice(0, tab_id_len);
                control_tr_id = hash.slice(tab_id_len + 1);

                if ((cache = $('a' + tab_id + '-tab')).length !== 0) {
                    open_tab(cache, control_tr_id);
                }
            } else {
                open_tab(hash + '-tab')
            }

            hash_event_triggered = true;

        });

        $(window).trigger('hashchange');
    };

    var custom_fields_sortable = function () {
        // profile fields sortable
        $("table.custom_profile_fields tbody").sortable({
            cursor: "move",
            containment: "table",
            handle: ".custom-field-anchor",
            start: function (event, ui) {
                ui.item.toggleClass("alternate");
            },
            stop: function (event, ui) {
                ui.item.toggleClass("alternate");
            },
            update: function (event, ui) {
                var data = $(this).sortable('toArray');
                $.post(
                    ajaxurl, {
                        action: "pp_profile_fields_sortable",
                        data: data
                    }
                );

                // regenerate the table tr ids after each DOM update
                $('table.custom_profile_fields tbody tr').each(function (index) {
                    $(this).attr('id', ++index);
                });
            }
        });
        // profile fields sortable
        $("table#pp_contact_info tbody").sortable({
            cursor: "move",
            containment: "table",
            handle: ".custom-field-anchor",
            start: function (event, ui) {
                ui.item.toggleClass("alternate");
            },
            stop: function (event, ui) {
                ui.item.toggleClass("alternate");
            },
            update: function (event, ui) {
                var data = $(this).sortable('toArray');
                $.post(
                    ajaxurl, {
                        action: "pp_contact_info_sortable",
                        data: data
                    }
                );
            }
        });

        // add IDs to table "tr" tags in profile fields wp-list-table
        // to be used by jQuery sortable.
        $('table.custom_profile_fields tbody tr').each(function (index) {
            $(this).attr('id', ++index);
        });
    };

    var custom_field_toggling = function () {
        jQuery(function ($) {
            var cpf_type = $('#cpf_type');

            var flag = true;

            if (cpf_type.length > 0) {

                cpf_type.change(function () {
                    var cache = $('#cpf-multi-select'),
                        cache2 = $('#pp-custom-field-date-format-row'),
                        cache3 = $('#pp-custom-field-options-row'),
                        selected_value = this.value;

                    cache.hide();
                    if (selected_value === 'select') {
                        cache.show();
                    }

                    cache2.hide();
                    if (selected_value === 'date') {
                        cache2.show();
                    }

                    cache3.hide();
                    // contextual display of options field
                    if ($.inArray(selected_value, ['select', 'radio', 'checkbox', 'file']) !== -1) {
                        if (flag === false) {
                            cache3.find('input').val('');
                        }
                        cache3.show();
                    }
                });

                cpf_type.change();

                flag = false;
            }

        });
    };

    var email_settings_field_init = function () {
        // initialize codemirror on email field in settings
        $(window).load(function () {
            $('.pp-email-editor-textarea').each(function () {
                window.cmSettingsInstances[this.id] = sb.codeMirrorInit(this, 'htmlmixed', false);
            });

            $('.pp-codemirror-editor-textarea').each(function () {
                window.cmSettingsInstances[this.id] = sb.codeMirrorInit(this, 'html', true);
            });
        });

        $('.pp-email-editor-tablinks').click(function (e) {
            e.preventDefault();
            $(this).blur();
            var parent = $(this).parents('.ppress-email-editor-wrap');
            $('.pp-email-editor-tablinks', parent).removeClass('eactive');
            var key = parent.find('.pp-email-editor-textarea').attr('id');

            $('.pp-email-editor-tabcontent', parent).hide();

            $(this).addClass('eactive');

            if ($(this).hasClass('ecode')) {
                parent.find('.pp-email-editor-tabcontent.ecode').show();
            } else {
                $('#' + key + '_preview_tabcontent').html(window.cmSettingsInstances[key].getValue());
                parent.find('.pp-email-editor-tabcontent.epreview').show();
            }
        });

        $('.pp-email-editor-tablinks.ecode').click();

        $('#email_content_type').on('change', function () {
            if (this.value === 'text/plain') {
                $('.pp-email-editor-tablinks').hide();

                $('.pp-email-editor-tabcontent').hide();
                $('.pp-email-editor-tabcontent.ecode').show();
            } else {
                $('.pp-email-editor-tablinks').show();
                $('.pp-email-editor-tablinks.ecode').click();
            }
        });
    };

    var titleshit = function () {
        $('#title').each(function () {
            var input = $(this), prompt = $('#' + this.id + '-prompt-text');

            if ('' === this.value) {
                prompt.removeClass('screen-reader-text');
            }

            prompt.click(function () {
                $(this).addClass('screen-reader-text');
                input.focus();
            });

            input.blur(function () {
                if ('' === this.value) {
                    prompt.removeClass('screen-reader-text');
                }
            });

            input.focus(function () {
                prompt.addClass('screen-reader-text');
            });
        });
    };

    var shortcodeBuilderUI = function () {
        $('.ppSCB-tab-box .nav-tab').click(function (e) {
            e.preventDefault();
            $('.ppSCB-tab-box .nav-tab').removeClass('nav-tab-active');
            var href = $(this).attr('href');
            $('.ppSCB-tab-content').hide();
            $(this).addClass('nav-tab-active');
            $(href).show();

            // if tab is switched in settings, refresh codemirror to redraw the layout
            // see https://github.com/codemirror/CodeMirror/issues/61
            $.each(window.cmSettingsInstances, function (index, value) {
                value.refresh();
            });

        }).eq(0).click();

        $('.ppSCB-preview-h-left').click(function (e) {
            e.preventDefault();
            $('.ppSCB-preview-h-left, .ppSCB-preview-h-right').removeClass('ppSCB-preview-active');
            $('.ppSCB-sidebar.password-reset iframe').hide();
            $(this).addClass('ppSCB-preview-active');
            $('#indexIframe').show();
        }).click();

        $('.ppSCB-preview-h-right').click(function (e) {
            e.preventDefault();
            $('.ppSCB-preview-h-left, .ppSCB-preview-h-right').removeClass('ppSCB-preview-active');
            $('.ppSCB-sidebar.password-reset iframe').hide();
            $(this).addClass('ppSCB-preview-active');
            $('#handlerIframe').show();
        });
    };

    $(function () {

        if (typeof window.cmSettingsInstances === 'undefined') {
            window.cmSettingsInstances = {};
        }
        admin_sidebar_tab_settings();
        custom_fields_sortable();
        custom_field_toggling();
        email_settings_field_init();
        titleshit();
        shortcodeBuilderUI();

        sb.login();
        sb.melange();
        sb.password_reset();
        sb.registration();
        sb.frontend_profile();
        sb.edit_profile();

        // confirm before deleting form
        $('.pp-form-listing.pp-forms .pp-form-delete, .pp-builder-action-btn-block .pp-form-delete, .pp-confirm-delete').click(function (e) {
            e.preventDefault();
            if (confirm(pp_form_builder.confirm_delete)) {
                window.location.href = $(this).attr('href');
            }
        });

        // Access settings tab contextual display
        $('#global_site_access').change(function () {
            var val = this.value;

            $('#global_site_access_redirect_page_row, #global_site_access_exclude_pages_row, #global_site_access_allow_homepage_row')
                .toggle('login' === val);
        }).change();

        $('.wp-csa-select2').select2();
    });
})(jQuery);
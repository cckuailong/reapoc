'use strict';
jQuery(document).ready(function ($) {
    console.log(vi_wot_admin_settings)
    let type_carrier,
        shipping_country_carrier;
    /**/
    $('.woo-orders-tracking-setting-service-carrier-type').on('change',function () {
        let $api=$('.woo-orders-tracking-tracking-service-api');
        if($(this).val()==='cainiao'){
            $api.addClass('woo-orders-tracking-hidden');
        }else{
            $api.removeClass('woo-orders-tracking-hidden');
        }
    });
    wotv_list_shipping_carriers();
    type_carrier = $('#woo-orders-tracking-setting-shipping-carriers-filter-type').val();
    shipping_country_carrier = $('#woo-orders-tracking-setting-shipping-carriers-filter-country').val();
    $('.vi-ui.vi-ui-main.tabular.menu .item').vi_tab({
        history: true,
        historyType: 'hash'
    });
    $('.vi-ui.vi-ui-shipment.menu .item').vi_tab();
    /*Setup tab*/
    let tabs,
        tabEvent = false,
        initialTab = 'shipping_carriers',
        navSelector = '.vi-ui.vi-ui-main.menu',
        panelSelector = '.vi-ui.vi-ui-main.tab',
        navSelectorSecond = '.vi-ui.vi-ui-shipment.menu',
        panelFilter = function () {
            $(panelSelector + ' a').filter(function () {
                return $(navSelector + ' a[title=' + $(this).attr('title') + ']').size() != 0;
            });
        };
    // Initializes plugin features
    $.address.strict(false).wrap(true);

    if ($.address.value() == '') {
        $.address.history(false).value(initialTab).history(true);
    }
    // Address handler
    $.address.init(function (event) {

        // Adds the ID in a lazy manner to prevent scrolling
        $(panelSelector).attr('id', initialTab);

        panelFilter();

        // Tabs setup
        tabs = $('.vi-ui.vi-ui-main.menu')
            .vi_tab({
                history: true,
                historyType: 'hash'
            });

        // Enables the plugin for all the tabs
        $(navSelector + ' a').click(function (event) {
            if ($(this).attr('data-tab') === 'design') {
                window.open($(this).attr('data-href'), '_blank');
            }
            tabEvent = true;

            tabEvent = false;
            return true;
        });
        $(navSelectorSecond + ' a').click(function (event) {
            $(navSelectorSecond + ' a').removeClass('header');
            $(this).addClass('header');
            return true;
        });

    });

    $('.vi-ui.dropdown').dropdown();
    $('.vi-ui.checkbox').checkbox();
    $('.woo-orders-tracking-setting-shipping-carriers-filter-country').select2();

    add_keyboard_event();

    function add_keyboard_event() {
        $(document).on('keydown', function (e) {
            if (!$('.add-new-shipping-carrier-html-container').hasClass('woo-orders-tracking-hidden')) {
                if (e.keyCode == 13) {
                    $('.add-new-shipping-carrier-html-btn-save').click();
                } else if (e.keyCode == 27) {
                    $('.add-new-shipping-carrier-html-btn-cancel').click();
                }
            } else if (!$('.edit-shipping-carrier-html-container').hasClass('woo-orders-tracking-hidden')) {
                if (e.keyCode == 13) {
                    $('.edit-shipping-carrier-html-btn-save').click();
                } else if (e.keyCode == 27) {
                    $('.edit-shipping-carrier-html-btn-cancel').click();
                }
            }
        });
    }

    $('.add-new-shipping-carrier-html-content-body-country, .edit-shipping-carrier-html-content-body-country').select2({
        placeholder: 'Please fill shipping country name',
        theme: 'add-new-shipping-carrier-select2'
    });
    $('.woo-orders-tracking-setting-service-carrier-api-key-' + $('#woo-orders-tracking-setting-service-carrier-type').val()).removeClass('woo-orders-tracking-hidden');
    $(document).on('change', '#woo-orders-tracking-setting-service-carrier-type', function () {
        $('.woo-orders-tracking-setting-service-carrier-api-key').addClass('woo-orders-tracking-hidden');
        $('.woo-orders-tracking-setting-service-carrier-api-key-' + $('#woo-orders-tracking-setting-service-carrier-type').val()).removeClass('woo-orders-tracking-hidden');
    });

    $(document).on('change', '#woo-orders-tracking-setting-shipping-carriers-filter-type', function () {
        type_carrier = $(this).val();
        let class_type = '';
        if (type_carrier === 'custom') {
            class_type = '.custom-shipping-carrier';
        }
        let class_shipping_country = '';

        if (shipping_country_carrier !== 'all_country') {
            class_shipping_country = '.shipping-country-' + shipping_country_carrier;
        }
        let search_key = $('.woo-orders-tracking-setting-shipping-carriers-filter-search').val().toLowerCase();
        if (search_key) {
            viWotSearch(class_type, class_shipping_country, search_key);
        } else {
            switch (type_carrier) {
                case 'all':
                    $('.woo-orders-tracking-setting-shipping-carriers-wrap' + class_shipping_country).removeClass('woo-orders-tracking-hidden');
                    break;
                case 'custom':
                    $('.woo-orders-tracking-setting-shipping-carriers-wrap').addClass('woo-orders-tracking-hidden');
                    $('.woo-orders-tracking-setting-shipping-carriers-wrap.custom-shipping-carrier' + class_shipping_country).removeClass('woo-orders-tracking-hidden');
                    break;
            }
        }
    });
    $(document).on('change', '#woo-orders-tracking-setting-shipping-carriers-filter-country', function () {
        shipping_country_carrier = $(this).val();
        let class_type = '',
            class_shipping_country = '';

        if (type_carrier === 'custom') {
            class_type = '.custom-shipping-carrier';
        }
        if (shipping_country_carrier !== 'all_country') {
            class_shipping_country = '.shipping-country-' + shipping_country_carrier;
        }
        let search_key = $('.woo-orders-tracking-setting-shipping-carriers-filter-search').val().toLowerCase();
        if (search_key) {
            viWotSearch(class_type, class_shipping_country, search_key);
        } else {
            $('.woo-orders-tracking-setting-shipping-carriers-wrap').addClass('woo-orders-tracking-hidden');
            $('.woo-orders-tracking-setting-shipping-carriers-wrap' + class_type + class_shipping_country).removeClass('woo-orders-tracking-hidden');
        }
    });

    $(document).on('click', '.woo-orders-tracking-setting-shipping-carriers-add-new-carrier', function () {
        wot_disable_scroll();
        $('.add-new-shipping-carrier-html-container').removeClass('woo-orders-tracking-hidden');
    });
    $(document).on('keyup', '.woo-orders-tracking-setting-shipping-carriers-filter-search', function () {
        let search_key = $(this).val().toLowerCase(),
            class_type = '',
            class_shipping_country = '';

        if (type_carrier === 'custom') {
            class_type = '.custom-shipping-carrier';
        }
        if (shipping_country_carrier !== 'all_country') {
            class_shipping_country = '.shipping-country-' + shipping_country_carrier;
        }
        if (search_key) {
            viWotSearch(class_type, class_shipping_country, search_key);
        } else {
            $('.woo-orders-tracking-setting-shipping-carriers-wrap' + class_type + class_shipping_country).removeClass('woo-orders-tracking-hidden');
        }
    });

    function viWotSearch(class_type, class_shipping_country, search_key) {
        $('.woo-orders-tracking-setting-shipping-carriers-wrap').addClass('woo-orders-tracking-hidden');
        $('.woo-orders-tracking-setting-shipping-carriers-wrap' + class_type + class_shipping_country).each(function () {
            let shipping_carrier_name = $(this).attr('data-carrier_name').toLowerCase(),
                pattern = new RegExp(search_key);
            if (pattern.exec(shipping_carrier_name)) {
                $(this).removeClass('woo-orders-tracking-hidden');
            }
        });
    }

    $(document).on('change', '.woo-orders-tracking-setting-shipping-carrier-action-default', function () {
        let overlay = $('.woo-orders-tracking-setting-shipping-carriers-overlay');
        let div = $(this).closest('.woo-orders-tracking-setting-shipping-carriers-wrap');
        let data = {
            action: 'wotv_admin_choose_default_shipping_carrier',
            action_nonce: $('#_vi_wot_setting_nonce').val(),
            carrier_slug: $(this).val(),
        };
        $.ajax({
            url: vi_wot_admin_settings.ajax_url,
            type: 'post',
            data: data,
            beforeSend: function () {
                overlay.removeClass('woo-orders-tracking-hidden');
            },
            success: function (response) {
                if (response) {
                    console.log(response);
                }
            },
            error: function (err) {
                console.log(err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
            },
            complete: function () {
                overlay.addClass('woo-orders-tracking-hidden');
            }
        });
    });
    $(document).on('mouseenter', '.woo-orders-tracking-setting-shipping-carriers-wrap', function () {
        $(this).addClass('custom-shipping-carrier-show-action');
        $(this).find('.woo-orders-tracking-setting-custom-shipping-carrier-action').removeClass('woo-orders-tracking-hidden');
    });
    $(document).on('mouseleave', '.woo-orders-tracking-setting-shipping-carriers-wrap', function () {
        $(this).removeClass('custom-shipping-carrier-show-action');
        $(this).find('.woo-orders-tracking-setting-custom-shipping-carrier-action').addClass('woo-orders-tracking-hidden');
    });
    $(document).on('click', '.woo-orders-tracking-overlay , .add-new-shipping-carrier-html-content-close, .add-new-shipping-carrier-html-btn-cancel ,.edit-shipping-carrier-html-content-close, .edit-shipping-carrier-html-btn-cancel', function () {
        if ($(this).closest('.woo-orders-tracking-footer-container').hasClass('add-new-shipping-carrier-html-container')) {
            $('#woo-orders-tracking-setting-shipping-carriers-filter-type').val('all').change();
        }
        $('.woo-orders-tracking-footer-container').addClass('woo-orders-tracking-hidden');
        wot_enable_scroll();
    });
    $(document).on('click', '.add-new-shipping-carrier-html-btn-save', function () {
        if (!$('#add-new-shipping-carrier-html-content-body-carrier-name').val() || !$('.add-new-shipping-carrier-html-content-body-country').val() || !$('#add-new-shipping-carrier-html-content-body-carrier-url').val()) {
            alert(vi_wot_admin_settings.add_new_error_empty_field);
            return false;
        }
        let data = {
            action: 'wotv_admin_add_new_shipping_carrier',
            action_nonce: $('#_vi_wot_setting_nonce').val(),
            carrier_name: $('#add-new-shipping-carrier-html-content-body-carrier-name').val(),
            shipping_country: $('#add-new-shipping-carrier-html-content-body-country').val(),
            tracking_url: $('#add-new-shipping-carrier-html-content-body-carrier-url').val(),
            digital_delivery : $('.add-new-shipping-carrier-is-digital-delivery').prop('checked') ? 1 : 0
        };
        $.ajax({
            url: vi_wot_admin_settings.ajax_url,
            type: 'post',
            data: data,
            beforeSend: function () {
                // console.log(data);
                $('.add-new-shipping-carrier-html-btn-save').addClass('loading');
            },
            success: function (response) {
                if (response) {
                    // console.log(response);
                    $('.woo-orders-tracking-setting-shipping-carriers-list-wrap').prepend(wotv_html_shipping_carrier(response.carrier));
                    $('.woo-orders-tracking-setting-shipping-carriers-list-wrap .vi-ui.checkbox').checkbox();
                }
            },
            error: function (err) {
                console.log(err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
            },
            complete: function () {
                $('.add-new-shipping-carrier-html-btn-save').removeClass('loading');
                $('#add-new-shipping-carrier-html-content-body-carrier-name,#add-new-shipping-carrier-html-content-body-country, #add-new-shipping-carrier-html-content-body-carrier-url').val(null);
                $('.woo-orders-tracking-overlay ').click();
            }
        });
    });
    $(document).on('click', '.add-new-shipping-carrier-is-digital-delivery', function () {
        let $button = $(this);
        let $container = $button.closest('.add-new-shipping-carrier-html-content-body');
        let $error = $container.find('.wotv-error-tracking-url');
        if ($button.prop('checked')) {
            $error.addClass('woo-orders-tracking-hidden')
        } else {
            let carrier_url = $('#add-new-shipping-carrier-html-content-body-carrier-url').val();
            if (carrier_url.indexOf('{tracking_number}') === -1) {
                $error.removeClass('woo-orders-tracking-hidden');
            } else {
                $error.addClass('woo-orders-tracking-hidden');
            }
        }
    });
    $(document).on('click', '.edit-shipping-carrier-is-digital-delivery', function () {
        let $button = $(this);
        let $container = $button.closest('.edit-shipping-carrier-html-content-body');
        let $error = $container.find('.wotv-error-tracking-url');
        if ($button.prop('checked')) {
            $error.addClass('woo-orders-tracking-hidden')
        } else {
            let carrier_url = $('#edit-shipping-carrier-html-content-body-carrier-url').val();
            if (carrier_url.indexOf('{tracking_number}') === -1) {
                $error.removeClass('woo-orders-tracking-hidden');
            } else {
                $error.addClass('woo-orders-tracking-hidden');
            }
        }
    });
    $('#edit-shipping-carrier-html-content-body-carrier-url').keyup(function () {
        let carrier_url = $(this).val();
        let $digital_delivery = $('.edit-shipping-carrier-is-digital-delivery');
        if (!$digital_delivery.prop('checked')) {
            if (carrier_url.indexOf('{tracking_number}') === -1) {
                $(this).parent().find('.wotv-error-tracking-url').removeClass('woo-orders-tracking-hidden');
            } else {
                $(this).parent().find('.wotv-error-tracking-url').addClass('woo-orders-tracking-hidden');
            }
        }
    });
    $('#add-new-shipping-carrier-html-content-body-carrier-url').keyup(function () {
        let carrier_url = $(this).val();
        let $digital_delivery = $('.add-new-shipping-carrier-is-digital-delivery');
        if (!$digital_delivery.prop('checked')) {
            if (carrier_url.indexOf('{tracking_number}') === -1) {
                $(this).parent().find('.wotv-error-tracking-url').removeClass('woo-orders-tracking-hidden');
            } else {
                $(this).parent().find('.wotv-error-tracking-url').addClass('woo-orders-tracking-hidden');
            }
        }
    });


    $(document).on('click', '.woo-orders-tracking-setting-custom-shipping-carrier-action-edit', function () {
        wot_disable_scroll();
        $('.edit-shipping-carrier-html-container').removeClass('woo-orders-tracking-hidden');
        let shipping_carrier_data = $(this).data(),
            carrier_slug = shipping_carrier_data['carrier_slug'],
            carrier_name = shipping_carrier_data['carrier_name'],
            shipping_country = shipping_carrier_data['shipping_country'],
            carrier_url = shipping_carrier_data['carrier_url'],
            digital_delivery = shipping_carrier_data['digital_delivery'];
        $('#edit-shipping-carrier-html-content-body-carrier-name').val(carrier_name);
        $('#edit-shipping-carrier-html-content-body-country').val(shipping_country).change();
        $('#edit-shipping-carrier-html-content-body-carrier-url').val(carrier_url);
        $('.edit-shipping-carrier-is-digital-delivery').prop('checked', digital_delivery == 1);
        $('.edit-shipping-carrier-html-btn-save').attr({
            'data-carrier_slug': carrier_slug,
            'data-carrier_name': carrier_name,
            'data-shipping_country': shipping_country,
            'data-carrier_url': carrier_url,
            'data-digital_delivery': digital_delivery,
        });
        $(this).closest('.woo-orders-tracking-setting-shipping-carriers-wrap').addClass('woo-orders-tracking-setting-shipping-carriers-wrap-editing');
        if (carrier_url.indexOf('{tracking_number}') === -1&&digital_delivery!=1) {
            $('.edit-shipping-carrier-html-container .wotv-error-tracking-url').removeClass('woo-orders-tracking-hidden');
        } else {
            $('.edit-shipping-carrier-html-container .wotv-error-tracking-url').addClass('woo-orders-tracking-hidden');
        }
    });
    $(document).on('click', '.edit-shipping-carrier-html-btn-save', function () {
        let shipping_carrier_data = $(this).data(),
            carrier_name = $('#edit-shipping-carrier-html-content-body-carrier-name').val(),
            shipping_country = $('#edit-shipping-carrier-html-content-body-country').val(),
            carrier_url = $('#edit-shipping-carrier-html-content-body-carrier-url').val(),
            digital_delivery = $('.edit-shipping-carrier-is-digital-delivery').prop('checked') ? 1 : 0;
        if (!carrier_name || !shipping_country || !carrier_url) {
            alert(vi_wot_admin_settings.add_new_error_empty_field);
            return false;
        }
        if (carrier_name === shipping_carrier_data['carrier_name'] && carrier_url === shipping_carrier_data['carrier_url'] && shipping_country === shipping_carrier_data['shipping_country'] && digital_delivery == shipping_carrier_data['digital_delivery']) {
            $('.woo-orders-tracking-footer-container').addClass('woo-orders-tracking-hidden');
            wot_enable_scroll();
            return false;
        }
        let div = $('.woo-orders-tracking-setting-shipping-carriers-wrap-editing'),
            data = {
                action: 'wotv_admin_edit_shipping_carrier',
                action_nonce: $('#_vi_wot_setting_nonce').val(),
                carrier_slug: shipping_carrier_data['carrier_slug'],
                carrier_name: carrier_name,
                shipping_country: shipping_country,
                tracking_url: carrier_url,
                digital_delivery: digital_delivery,
            };
        $.ajax({
            url: vi_wot_admin_settings.ajax_url,
            type: 'post',
            data: data,
            beforeSend: function () {
                // console.log(data);
                $('.edit-shipping-carrier-html-btn-save').addClass('loading');
            },
            success: function (response) {
                if (response.status === 'success') {
                    // console.log(response);
                    div.data('carrier_name', response.carrier_name);
                    div.find('.woo-orders-tracking-setting-custom-shipping-carrier-action-edit').data('carrier_name', response.carrier_name).data('shipping_country', response.shipping_country).data('carrier_url', response.tracking_url).data('digital_delivery', response.digital_delivery);
                    div.find('.woo-orders-tracking-setting-shipping-carrier-name a').html('<a href="' + response.tracking_url + '" target="_blank">' + response.carrier_name + '</a>');
                    shipping_carrier_data['carrier_name'] = response.carrier_name;
                    shipping_carrier_data['carrier_url'] = response.tracking_url;
                    shipping_carrier_data['shipping_country'] = response.shipping_country;
                } else {
                    console.log(response);
                }
            },
            error: function (err) {
                console.log(err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
            },
            complete: function () {
                $('.edit-shipping-carrier-html-btn-save').removeClass('loading');
                $('.woo-orders-tracking-footer-container').addClass('woo-orders-tracking-hidden');
                wot_enable_scroll();
            }
        });
    });
    $(document).on('click', '.woo-orders-tracking-setting-custom-shipping-carrier-action-copy', function () {
        $('.woo-orders-tracking-copy-carrier-successful').remove();
        let $temp = $('<input>');
        $('body').append($temp);
        $temp.val($(this).data('carrier_slug')).select();
        document.execCommand('copy');
        $temp.remove();
        let $result_icon = $('<span class="woo-orders-tracking-copy-carrier-successful dashicons dashicons-yes" title="Copied slug to clipboard"></span>');
        let $container = $(this).closest('.woo-orders-tracking-setting-shipping-carriers-wrap');
        let $carrier_slug_container = $container.find('.woo-orders-tracking-setting-shipping-carrier-slug');
        $carrier_slug_container.append($result_icon);
    });
    $(document).on('click', '.woo-orders-tracking-setting-custom-shipping-carrier-action-delete', function () {
        if (confirm(vi_wot_admin_settings.confirm_delete_carrier_custom)) {
            let overlay = $('.woo-orders-tracking-setting-shipping-carriers-overlay');
            let div = $(this).closest('.woo-orders-tracking-setting-shipping-carriers-wrap'),
                shipping_carrier_data = $(this).data(),
                data = {
                    action: 'wotv_admin_delete_shipping_carrier',
                    action_nonce: $('#_vi_wot_setting_nonce').val(),
                    carrier_slug: shipping_carrier_data['carrier_slug'],
                };
            $.ajax({
                url: vi_wot_admin_settings.ajax_url,
                type: 'post',
                data: data,
                beforeSend: function () {
                    overlay.removeClass('woo-orders-tracking-hidden');
                },
                success: function (response) {
                    if (response.status === 'success') {
                        div.remove();
                    } else {
                        console.log(response);
                    }
                },
                error: function (err) {
                    console.log(err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
                },
                complete: function () {
                    overlay.addClass('woo-orders-tracking-hidden');
                }
            });
        }
    });


    $(document).on('click', '.woo-orders-tracking-preview-emails-button', function () {
        $(this).html('Please wait...');
        let data = {
            action: 'wot_preview_emails',
            heading: $('#woo-orders-tracking-setting-email-heading').val(),
            content: tinyMCE.get('wot-email-content') ? tinyMCE.get('wot-email-content').getContent() : $('#coupon_email_content').val(),
        };

        $.ajax({
            url: vi_wot_admin_settings.ajax_url,
            type: 'GET',
            dataType: 'JSON',
            data: data,
            success: function (response) {
                $('.woo-orders-tracking-preview-emails-button').html('Preview emails');
                if (response) {
                    // console.log(response);
                    $('.preview-emails-html').html(response.html);
                    wot_disable_scroll();
                    $('.preview-emails-html-container').removeClass('woo-orders-tracking-hidden');
                }
            },
            error: function (err) {
                $('.woo-orders-tracking-preview-emails-button').html('Preview emails');
                console.log(err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
            }
        });
    });

    $('input#woo-orders-tracking-setting-paypal-sandbox-enable').each(function () {
        if ($(this).prop('checked')) {
            $(this).closest('.wot-paypal-app-content').find('.woo-orders-tracking-setting-paypal-live-wrap').removeClass('woo-orders-tracking-setting-paypal-live-wrap-show').addClass('woo-orders-tracking-hidden');
            $(this).closest('.wot-paypal-app-content').find('.woo-orders-tracking-setting-paypal-sandbox-wrap').removeClass('woo-orders-tracking-hidden');
        } else {
            $(this).closest('.wot-paypal-app-content').find('.woo-orders-tracking-setting-paypal-live-wrap').removeClass('woo-orders-tracking-hidden').addClass('woo-orders-tracking-setting-paypal-live-wrap-show');
            $(this).closest('.wot-paypal-app-content').find('.woo-orders-tracking-setting-paypal-sandbox-wrap').addClass('woo-orders-tracking-hidden');
        }
        $(this).change(function () {
            if ($(this).prop('checked')) {
                $(this).parent().parent().find('.woo-orders-tracking-setting-paypal-sandbox-enable').val('1');
                $(this).closest('.wot-paypal-app-content').find('.woo-orders-tracking-setting-paypal-live-wrap').removeClass('woo-orders-tracking-setting-paypal-live-wrap-show').addClass('woo-orders-tracking-hidden');
                $(this).closest('.wot-paypal-app-content').find('.woo-orders-tracking-setting-paypal-sandbox-wrap').removeClass('woo-orders-tracking-hidden');
            } else {
                $(this).parent().parent().find('.woo-orders-tracking-setting-paypal-sandbox-enable').val('');
                $(this).closest('.wot-paypal-app-content').find('.woo-orders-tracking-setting-paypal-live-wrap').removeClass('woo-orders-tracking-hidden').addClass('woo-orders-tracking-setting-paypal-live-wrap-show');
                $(this).closest('.wot-paypal-app-content').find('.woo-orders-tracking-setting-paypal-sandbox-wrap').addClass('woo-orders-tracking-hidden');
            }
        });
    });


    $('.wot-paypal-app-content-action-test-api').click(function () {
        let data, div, parent, btnt_test;
        btnt_test = $(this);
        div = btnt_test.closest('.wot-paypal-app-content');
        parent = btnt_test.closest('td');
        parent.find('.woo-orders-tracking-setting-paypal-btn-check-api-text').html('');
        div.find('input[type ="text"]').removeAttr('style');
        if (div.find('#woo-orders-tracking-setting-paypal-sandbox-enable').prop('checked')) {
            if (!div.find('.woo-orders-tracking-setting-paypal-client-id-sandbox').val()) {
                div.find('.woo-orders-tracking-setting-paypal-client-id-sandbox').css('border-color', 'red');
                return false;
            }
            if (!div.find('.woo-orders-tracking-setting-paypal-secret-sandbox').val()) {
                div.find('.woo-orders-tracking-setting-paypal-secret-sandbox').css('border-color', 'red');
                return false;
            }
            data = {
                action: 'wot_test_connection_paypal',
                client_id: div.find('.woo-orders-tracking-setting-paypal-client-id-sandbox').val(),
                secret: div.find('.woo-orders-tracking-setting-paypal-secret-sandbox').val(),
                sandbox: 'yes'
            };
        } else {
            if (!div.find('.woo-orders-tracking-setting-paypal-client-id-live').val()) {
                div.find('.woo-orders-tracking-setting-paypal-client-id-live').css('border-color', 'red');
                return false;
            }
            if (!div.find('.woo-orders-tracking-setting-paypal-secret-live').val()) {
                div.find('.woo-orders-tracking-setting-paypal-secret-live').css('border-color', 'red');
                return false;
            }
            data = {
                action: 'wot_test_connection_paypal',
                client_id: div.find('.woo-orders-tracking-setting-paypal-client-id-live').val(),
                secret: div.find('.woo-orders-tracking-setting-paypal-secret-live').val(),
                sandbox: 'no'
            };
        }
        $.ajax({
            url: vi_wot_admin_settings.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            beforeSend: function () {
                btnt_test.addClass('loading');
                console.log(data);
            },
            success: function (response) {
                console.log(response);
                parent.find('.woo-orders-tracking-setting-paypal-btn-check-api-text').html(response.message);
                div.find('input[type ="text"]').removeAttr('style');
            },
            error: function (err) {
                console.log(err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
            },
            complete: function () {
                btnt_test.removeClass('loading');
            }
        });
    });

    $('#woo-orders-tracking-setting-paypal-guide').click(function () {
        $('.woo-orders-tracking-setting-paypal-guide').click();
    });

    function wot_enable_scroll() {
        let scrollTop = parseInt($('html').css('top'));
        $('html').removeClass('wot-noscroll');
        $('html,body').scrollTop(-scrollTop);
    }

    function wot_disable_scroll() {
        if ($(document).height() > $(window).height()) {
            let scrollTop = ($('html').scrollTop()) ? $('html').scrollTop() : $('body').scrollTop(); // Works for Chrome, Firefox, IE...
            $('html').addClass('wot-noscroll').css('top', -scrollTop);
        }
    }


    function wotv_html_shipping_carrier(data) {
        let html = '';
        let checked = 'checked="checked"',
            class_type = 'define-shipping-carrier',
            class_shipping_country = 'shipping-country-' + data.country,
            custom_carrier = '';

        if (data.type && data.type === 'custom') {
            custom_carrier = 'yes';
            class_type = 'custom-shipping-carrier';
        }

        html += '<div class="woo-orders-tracking-setting-shipping-carriers-wrap ' + class_shipping_country + ' ' + class_type + '" data-country="' + data.country + '"  data-carrier_name="' + data.name + '"  data-custom_carrier="' + custom_carrier + '">';

        html += '<div class="woo-orders-tracking-setting-shipping-carrier-name">';
        html += '<a href="' + data.url + '" target="_blank">' + data.name + '</a>';
        html += '<div class="woo-orders-tracking-setting-custom-shipping-carrier-action woo-orders-tracking-hidden">';
        html += '<i class="copy outline icon woo-orders-tracking-setting-custom-shipping-carrier-action-copy green" data-carrier_slug="' + data.slug + '" title="Copy carrier slug"></i>';
        if (custom_carrier === 'yes') {
            let digital_delivery = 0;
            if (data.hasOwnProperty('digital_delivery') && data.digital_delivery==1) {
                digital_delivery = 1;
            }
            html += '<i class="edit outline icon woo-orders-tracking-setting-custom-shipping-carrier-action-edit blue"  data-carrier_slug="' + data.slug + '"  data-carrier_name="' + data.name + '" data-shipping_country="' + data.country + '" data-carrier_url="' + data.url + '" data-digital_delivery="' + digital_delivery + '"></i>';
            html += '<i class="trash alternate outline icon woo-orders-tracking-setting-custom-shipping-carrier-action-delete red" data-carrier_slug="' + data.slug + '"></i>';
        }
        html += '</div>';
        html += '</div>';
        html += '<div class="woo-orders-tracking-setting-shipping-carrier-slug"><input class="woo-orders-tracking-setting-shipping-carrier-slug-input" type="text" value="' + data.slug + '" readonly></div>';


        html += '<div class="woo-orders-tracking-setting-shipping-carrier-action">';

        html += '<div class="woo-orders-tracking-setting-shipping-carrier-action-default-wrap">';

        html += '<div class="vi-ui toggle checkbox"><input name="woo_order_tracking_default_carrier" type="radio" class="woo-orders-tracking-setting-shipping-carrier-action-default" id="woo-orders-tracking-setting-shipping-carrier-action-default-' + data.slug + '" value="' + data.slug + '"';
        if (data.slug === vi_wot_admin_settings.shipping_carrier_default) {
            html += checked;
        }
        html += '/>';
        html += '<label for="woo-orders-tracking-setting-shipping-carrier-action-default-' + data.slug + '"><span>' + vi_wot_admin_settings.select_default_carrier_text + '</span></label></div>';
        html += '</div>';

        html += '</div>';


        html += '</div>';
        return html;
    }

    function wotv_list_shipping_carriers() {
        let shipping_carriers_define_list,
            custom_carriers_list,
            carrier;
        shipping_carriers_define_list = $.parseJSON(vi_wot_admin_settings.shipping_carriers_define_list);
        custom_carriers_list = $.parseJSON(vi_wot_admin_settings.custom_carriers_list);
        carrier = shipping_carriers_define_list.concat(custom_carriers_list);
        let html = '';
        let default_carrier = '';
        carrier = wot_sort_carriers(carrier);
        for (let i = 0; i < carrier.length; i++) {
            if (carrier[i]['slug'] === vi_wot_admin_settings.shipping_carrier_default) {
                default_carrier = wotv_html_shipping_carrier(carrier[i]);
            } else {
                html += wotv_html_shipping_carrier(carrier[i]);
            }
        }
        html = default_carrier + html;
        $('.woo-orders-tracking-setting-shipping-carriers-list-wrap').html(html);
    }

    $('body').on('change', '.woo-orders-tracking-string-replace-sensitive', function () {
        let $container = $(this).closest('.woo-orders-tracking-string-replace-sensitive-container');
        let $sensitive_value = $container.find('.woo-orders-tracking-string-replace-sensitive-value');
        let sensitive_value = $(this).prop('checked') ? 1 : '';
        $sensitive_value.val(sensitive_value);
    });
    $('body').on('click', '.delete-string-replace-rule', function () {
        if (confirm(vi_wot_admin_settings.confirm_delete_string_replace)) {
            $(this).closest('.clone-source').remove();
        }
    });
    /*Search page*/
    $('.search-page').select2({
        allowClear: true,
        closeOnSelect: true,
        placeholder: 'Please fill in your page title',
        ajax: {
            url: "admin-ajax.php?action=woo_orders_tracking_search_page",
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1
    });

});

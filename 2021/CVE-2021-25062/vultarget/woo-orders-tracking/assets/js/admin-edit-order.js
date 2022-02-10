'use strict';
jQuery(document).ready(function ($) {
    let custom_carriers_list = $.parseJSON(vi_wot_edit_order.custom_carriers_list);
    let global_tracking_number = '';
    wotv_get_shipping_carriers_html();
    $('.woo-orders-tracking-edit-tracking-other-carrier-country').select2({
        placeholder: "Please fill in your shipping country name",
        theme: "wotv-select2-custom-country"
    });
    add_keyboard_event();

    function add_keyboard_event() {
        $(document).on('keydown', function (e) {
            if (!$('.woo-orders-tracking-edit-tracking-container').hasClass('woo-orders-tracking-hidden')) {
                if (e.keyCode == 13) {
                    $('.woo-orders-tracking-edit-tracking-button-save').click();
                } else if (e.keyCode == 27) {
                    $('.woo-orders-tracking-edit-tracking-button-cancel').click();
                }
            }
        });
    }

    /*Button add tracking number to paypal*/
    $(document).on('click', '.woo-orders-tracking-paypal-active', function () {
        let $button = $(this);
        let $paypal_image = $button.find('.woo-orders-tracking-item-tracking-button-add-to-paypal');

        $.ajax({
            url: vi_wot_edit_order.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'vi_woo_orders_tracking_add_tracking_to_paypal',
                item_id: $button.data('item_id'),
                order_id: $button.data('order_id'),
                action_nonce: $('#_vi_wot_item_nonce').val(),
            },
            beforeSend: function () {

                $button.removeClass('woo-orders-tracking-paypal-active').addClass('woo-orders-tracking-paypal-inactive');
                $paypal_image.attr('src', vi_wot_edit_order.loading_image);
            },
            success: function (response) {
                if (response.status === 'success') {
                    if (response.paypal_added_trackings) {
                        $('.woo-orders-tracking-item-tracking-paypal-added-tracking-numbers-values').val(response.paypal_added_trackings);
                    }
                    if (response.paypal_button_title) {
                        $paypal_image.attr('title', response.paypal_button_title);
                    }
                    villatheme_admin_show_message(response.message, response.status, response.message_content, false, 5000);
                } else {
                    $button.removeClass('woo-orders-tracking-paypal-inactive').addClass('woo-orders-tracking-paypal-active');
                    villatheme_admin_show_message(response.message, response.status, response.message_content);
                }
            },
            error: function (err) {
                villatheme_admin_show_message('Error', 'error', err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
                $button.removeClass('woo-orders-tracking-paypal-inactive').addClass('woo-orders-tracking-paypal-active');
            },
            complete: function () {
                $paypal_image.attr('src', vi_wot_edit_order.paypal_image);
            }
        });
    });
    $(document).on('click', '.woo-orders-tracking-button-edit', function () {
        $(this).addClass('woo-orders-tracking-button-editing');
        $('.woo-orders-tracking-edit-tracking-button-save').addClass('woo-orders-tracking-edit-tracking-save-only-one-item');
        vi_wotg_edit_tracking_show();
        let data = $(this).data();
        $('#woo-orders-tracking-edit-tracking-number').val(data['tracking_code']);
        global_tracking_number = data['tracking_code'];
        if (data['tracking_url']) {
            $('.woo-orders-tracking-edit-tracking-carrier').val('shipping-carriers').change();
            if (data['carrier_id']) {
                $('.woo-orders-tracking-edit-tracking-shipping-carrier').val(data['carrier_id']).change();
            } else {
                if (vi_wot_edit_order.shipping_carrier_default && data['tracking_url'].indexOf(data['tracking_code']) !== -1) {
                    let pattern = vi_wot_edit_order.shipping_carrier_default_url_check,
                        pattern_url_check = data['tracking_url'].split(data['tracking_code'], 1)[0];
                    pattern = pattern.split('{tracking_number}', 1)[0];
                    if (pattern === pattern_url_check) {
                        $('.woo-orders-tracking-edit-tracking-shipping-carrier').val(vi_wot_edit_order.shipping_carrier_default).change();
                    } else {
                        $('.woo-orders-tracking-edit-tracking-carrier').val('other').change();
                    }
                } else {
                    $('.woo-orders-tracking-edit-tracking-carrier').val('other').change();
                    $('.woo-orders-tracking-edit-tracking-other-carrier-name').val(data['carrier_name']).change();
                }
            }
            if ($('.woo-orders-tracking-edit-tracking-carrier').val() === 'other') {
                $('#woo-orders-tracking-edit-tracking-other-carrier-url').val(data['tracking_url'].replace(data['tracking_code'], '{tracking_number}'));
            }
        } else {
            if (data['tracking_code']) {
                $('.woo-orders-tracking-edit-tracking-carrier').val('shipping-carriers').change();
                if (data['carrier_id']) {
                    $('.woo-orders-tracking-edit-tracking-shipping-carrier').val(data['carrier_id']).change();
                }
            } else {
                $('.woo-orders-tracking-edit-tracking-carrier').val('shipping-carriers').change();
                if (vi_wot_edit_order.shipping_carrier_default) {
                    $('.woo-orders-tracking-edit-tracking-shipping-carrier').val(vi_wot_edit_order.shipping_carrier_default).change();
                }
            }
        }
        $('.woo-orders-tracking-edit-tracking-save-only-one-item').attr({
            'data-order_id': data['order_id'],
            'data-item_id': data['item_id'],
            'data-item_name': data['item_name']
        });
    });
    $('#woo-orders-tracking-edit-tracking-other-carrier-url').keyup(function () {
        let carrier_url = $(this).val();
        if (carrier_url.indexOf('{tracking_number}') === -1) {
            $(this).parent().find('.wotv-error-tracking-url').removeClass('woo-orders-tracking-hidden');
        } else {
            $(this).parent().find('.wotv-error-tracking-url').addClass('woo-orders-tracking-hidden');
        }
    });
    $(document).on('click', '.woo-orders-tracking-button-edit-all-tracking-number', function () {
        if ($('.woo-orders-tracking-button-edit').length === 1) {
            $('.woo-orders-tracking-button-edit').click();
        } else {
            $('.woo-orders-tracking-edit-tracking-button-save').addClass('woo-orders-tracking-edit-tracking-save-all-item');
            vi_wotg_edit_tracking_show();

            $('.woo-orders-tracking-edit-tracking-carrier').val('shipping-carriers').change();
            $('.woo-orders-tracking-edit-tracking-shipping-carrier').val(vi_wot_edit_order.shipping_carrier_default).change();
            let data = $(this).data();
            $('.woo-orders-tracking-edit-tracking-save-all-item').attr({'data-order_id': data['order_id']});
        }
    });

    $(document).on('click', '.woo-orders-tracking-overlay, .woo-orders-tracking-edit-tracking-close, .woo-orders-tracking-edit-tracking-button-cancel ', function () {
        vi_wotg_edit_tracking_hide();
    });
    $(document).on('change', '.woo-orders-tracking-edit-tracking-number', function () {
        global_tracking_number = $(this).val();
    });
    $(document).on('change', '.woo-orders-tracking-edit-tracking-shipping-carrier', function () {
        let $tracking_number = $('.woo-orders-tracking-edit-tracking-number');
        let selected_carrier = vi_wotg_get_custom_carrier_by_slug($(this).val());
        if (selected_carrier && selected_carrier.hasOwnProperty('digital_delivery') && selected_carrier.digital_delivery == 1) {
            $tracking_number.val('');
            $tracking_number.prop('disabled', true);
        } else {
            $tracking_number.val(global_tracking_number);
            $tracking_number.prop('disabled', false);
        }
    });
    $(document).on('change', '.woo-orders-tracking-edit-tracking-carrier', function () {
        let $tracking_number = $('.woo-orders-tracking-edit-tracking-number');
        switch ($(this).val()) {
            case 'other':
                $('.woo-orders-tracking-edit-tracking-content-body-row-shipping-carrier').addClass('woo-orders-tracking-hidden');
                $('.woo-orders-tracking-edit-tracking-content-body-row-service-carrier').addClass('woo-orders-tracking-hidden');
                $('.woo-orders-tracking-edit-tracking-content-body-row-other-carrier').removeClass('woo-orders-tracking-hidden');
                $tracking_number.val(global_tracking_number);
                $tracking_number.prop('disabled', false);
                break;
            case 'shipping-carriers':
                $('.woo-orders-tracking-edit-tracking-content-body-row-other-carrier').addClass('woo-orders-tracking-hidden');
                $('.woo-orders-tracking-edit-tracking-content-body-row-service-carrier').addClass('woo-orders-tracking-hidden');
                $('.woo-orders-tracking-edit-tracking-content-body-row-shipping-carrier').removeClass('woo-orders-tracking-hidden');
                let selected_carrier = vi_wotg_get_custom_carrier_by_slug($('.woo-orders-tracking-edit-tracking-shipping-carrier').val());
                if (selected_carrier && selected_carrier.hasOwnProperty('digital_delivery') && selected_carrier.digital_delivery == 1) {
                    $tracking_number.val('');
                    $tracking_number.prop('disabled', true);
                } else {
                    $tracking_number.val(global_tracking_number);
                    $tracking_number.prop('disabled', false);
                }
                break;
            default:
                $(this).val('other').change();
        }
    });

    $(document).on('click', '.woo-orders-tracking-edit-tracking-save-all-item', function () {
        let carrier_type = $('.woo-orders-tracking-edit-tracking-carrier').val(),
            tracking_code = $('#woo-orders-tracking-edit-tracking-number').val(),
            order_data = $(this).data();
        $('.woo-orders-tracking-edit-tracking-content-body-row-error').addClass('woo-orders-tracking-hidden');
        switch (carrier_type) {
            case 'other':
                let carrier_name = $('#woo-orders-tracking-edit-tracking-other-carrier-name').val(),
                    shipping_country = $('.woo-orders-tracking-edit-tracking-other-carrier-country').val(),
                    tracking_url = $('#woo-orders-tracking-edit-tracking-other-carrier-url').val();
                if (!tracking_code || !tracking_url || !carrier_name || !shipping_country) {
                    $('.woo-orders-tracking-edit-tracking-content-body-row-error').removeClass('woo-orders-tracking-hidden');
                    $('.woo-orders-tracking-edit-tracking-content-body-row-error p').html(vi_wot_edit_order.error_empty_field);
                    return false;
                }
                let data_new_carrier = {
                    action: 'wotv_save_track_info_all_item',
                    action_nonce: $('#_vi_wot_item_nonce').val(),
                    tracking_code: tracking_code,
                    change_order_status: $('#woo-orders-tracking-order_status').val(),
                    send_mail: $('#woo-orders-tracking-edit-tracking-send-email').prop('checked') ? 'yes' : 'no',
                    add_to_paypal: $('#woo-orders-tracking-edit-tracking-add-to-paypal').prop('checked') ? 'yes' : 'no',
                    transID: $('#woo-orders-tracking-edit-tracking-add-to-paypal').val(),
                    paypal_method: $('#woo-orders-tracking-edit-tracking-add-to-paypal-method').val(),
                    carrier_id: '',
                    carrier_name: carrier_name,
                    shipping_country: shipping_country,
                    tracking_url: tracking_url,
                    add_new_carrier: 1,
                    order_id: order_data['order_id'],
                };
                wotv_save_track_info_all_item(data_new_carrier);
                break;
            case 'shipping-carriers':
                let shipping_carrier_id = $('#woo-orders-tracking-edit-tracking-shipping-carrier').val();
                if (!shipping_carrier_id) {
                    $('.woo-orders-tracking-edit-tracking-content-body-row-error').removeClass('woo-orders-tracking-hidden');
                    $('.woo-orders-tracking-edit-tracking-content-body-row-error p').html(vi_wot_edit_order.error_empty_field);
                    return false;
                } else if (!tracking_code) {
                    let found_carrier = vi_wotg_get_custom_carrier_by_slug(shipping_carrier_id);
                    let digital_delivery = 0;
                    if (found_carrier && found_carrier.hasOwnProperty('digital_delivery')) {
                        digital_delivery = found_carrier.digital_delivery;
                    }
                    if (digital_delivery != 1) {
                        $('.woo-orders-tracking-edit-tracking-content-body-row-error').removeClass('woo-orders-tracking-hidden');
                        $('.woo-orders-tracking-edit-tracking-content-body-row-error p').html(vi_wot_edit_order.error_empty_field);
                        return false;
                    }
                }
                let shipping_data = {
                    action: 'wotv_save_track_info_all_item',
                    action_nonce: $('#_vi_wot_item_nonce').val(),
                    carrier_id: shipping_carrier_id,
                    carrier_name: $('#woo-orders-tracking-edit-tracking-shipping-carrier option[value="' + shipping_carrier_id + '"').text(),
                    tracking_code: tracking_code,
                    change_order_status: $('#woo-orders-tracking-order_status').val(),
                    send_mail: $('#woo-orders-tracking-edit-tracking-send-email').prop('checked') ? 'yes' : 'no',
                    add_to_paypal: $('#woo-orders-tracking-edit-tracking-add-to-paypal').prop('checked') ? 'yes' : 'no',
                    transID: $('#woo-orders-tracking-edit-tracking-add-to-paypal').val(),
                    paypal_method: $('#woo-orders-tracking-edit-tracking-add-to-paypal-method').val(),
                    order_id: order_data['order_id'],
                };
                wotv_save_track_info_all_item(shipping_data);
                break;
        }
    });
    $(document).on('click', '.woo-orders-tracking-edit-tracking-save-only-one-item', function () {
        let carrier_type = $('.woo-orders-tracking-edit-tracking-carrier').val(),
            editing = $('.woo-orders-tracking-button-editing'),
            tracking_code = $('#woo-orders-tracking-edit-tracking-number').val(),
            item_data = {
                'order_id': $(this).attr('data-order_id'),
                'item_id': $(this).attr('data-item_id'),
                'item_name': $(this).attr('data-item_name'),
            };
        let shipping_carrier_id = $('#woo-orders-tracking-edit-tracking-shipping-carrier').val();
        $('.woo-orders-tracking-edit-tracking-content-body-row-error').addClass('woo-orders-tracking-hidden');
        switch (carrier_type) {
            case 'other':
                let carrier_name = $('#woo-orders-tracking-edit-tracking-other-carrier-name').val(),
                    shipping_country = $('.woo-orders-tracking-edit-tracking-other-carrier-country').val(),
                    tracking_url = $('#woo-orders-tracking-edit-tracking-other-carrier-url').val();
                if (!tracking_code || !tracking_url || !carrier_name || !shipping_country) {
                    $('.woo-orders-tracking-edit-tracking-content-body-row-error').removeClass('woo-orders-tracking-hidden');
                    $('.woo-orders-tracking-edit-tracking-content-body-row-error p').html(vi_wot_edit_order.error_empty_field);
                    return false;
                }
                let data_new_carrier = {
                    action: 'wotv_save_track_info_item',
                    action_nonce: $('#_vi_wot_item_nonce').val(),
                    tracking_code: tracking_code,
                    change_order_status: $('#woo-orders-tracking-order_status').val(),
                    send_mail: $('#woo-orders-tracking-edit-tracking-send-email').prop('checked') ? 'yes' : 'no',
                    add_to_paypal: $('#woo-orders-tracking-edit-tracking-add-to-paypal').prop('checked') ? 'yes' : 'no',
                    transID: $('#woo-orders-tracking-edit-tracking-add-to-paypal').val(),
                    paypal_method: $('#woo-orders-tracking-edit-tracking-add-to-paypal-method').val(),
                    order_id: item_data['order_id'],
                    item_id: item_data['item_id'],
                    item_name: item_data['item_name'],
                    carrier_id: '',
                    carrier_name: carrier_name,
                    shipping_country: shipping_country,
                    tracking_url: tracking_url,
                    add_new_carrier: 1,
                };
                wotv_save_track_info_item(data_new_carrier, editing);
                break;
            case 'shipping-carriers':
                if (!shipping_carrier_id) {
                    $('.woo-orders-tracking-edit-tracking-content-body-row-error').removeClass('woo-orders-tracking-hidden');
                    $('.woo-orders-tracking-edit-tracking-content-body-row-error p').html(vi_wot_edit_order.error_empty_field);
                    return false;
                } else if (!tracking_code) {
                    let found_carrier = vi_wotg_get_custom_carrier_by_slug(shipping_carrier_id);
                    let digital_delivery = 0;
                    if (found_carrier && found_carrier.hasOwnProperty('digital_delivery')) {
                        digital_delivery = found_carrier.digital_delivery;
                    }
                    if (digital_delivery != 1) {
                        $('.woo-orders-tracking-edit-tracking-content-body-row-error').removeClass('woo-orders-tracking-hidden');
                        $('.woo-orders-tracking-edit-tracking-content-body-row-error p').html(vi_wot_edit_order.error_empty_field);
                        return false;
                    }
                }

                let shipping_data = {
                    action: 'wotv_save_track_info_item',
                    action_nonce: $('#_vi_wot_item_nonce').val(),
                    carrier_id: shipping_carrier_id,
                    carrier_name: $('#woo-orders-tracking-edit-tracking-shipping-carrier option[value="' + shipping_carrier_id + '"').text(),
                    tracking_code: tracking_code,
                    change_order_status: $('#woo-orders-tracking-order_status').val(),
                    send_mail: $('#woo-orders-tracking-edit-tracking-send-email').prop('checked') ? 'yes' : 'no',
                    add_to_paypal: $('#woo-orders-tracking-edit-tracking-add-to-paypal').prop('checked') ? 'yes' : 'no',
                    transID: $('#woo-orders-tracking-edit-tracking-add-to-paypal').val(),
                    paypal_method: $('#woo-orders-tracking-edit-tracking-add-to-paypal-method').val(),
                    order_id: item_data['order_id'],
                    item_id: item_data['item_id'],
                    item_name: item_data['item_name'],
                };
                wotv_save_track_info_item(shipping_data, editing);
                break;
        }

        $(this).removeAttr('data-order_id  data-item_id data-item_name');
    });

    function wotv_save_track_info_all_item(data) {
        let editing = $('.woo-orders-tracking-button-edit');
        $.ajax({
            url: vi_wot_edit_order.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            beforeSend: function () {
                $('.woo-orders-tracking-saving-overlay').removeClass('woo-orders-tracking-hidden');

            },
            success: function (response) {
                if (response.hasOwnProperty('change_order_status') && response.change_order_status) {
                    $('body').find('#order_status').val(response.change_order_status).change();
                }
                if (response.status === 'error') {
                    villatheme_admin_show_message(response.message, response.status);
                } else {
                    villatheme_admin_show_message(response.message, response.status, '', false, 5000);
                    /*Update data*/
                    editing.data('tracking_code', response.tracking_code);
                    editing.data('tracking_url', response.carrier_url);
                    editing.data('carrier_id', response.carrier_id);
                    editing.data('carrier_name', response.carrier_name);
                    editing.data('digital_delivery', response.digital_delivery);
                    editing.closest('.woo-orders-tracking-container').find('.woo-orders-tracking-item-tracking-code-value').html('<a target="_blank" href="' + response.tracking_url_show + '">' + response.tracking_code + '</a>').attr('title', response.carrier_name);
                    let $shipping_carrier_select = $('#woo-orders-tracking-edit-tracking-shipping-carrier');
                    if (data.hasOwnProperty('add_new_carrier') && response.carrier_id) {
                        let option = {
                            id: response.carrier_id,
                            text: response.carrier_name
                        };

                        let newOption = new Option(option.text, option.id, false, false);
                        $shipping_carrier_select.append(newOption).trigger('change');
                    }

                    if (response.paypal_status === 'error') {
                        villatheme_admin_show_message('Can not add tracking to PayPal', 'error', response.paypal_message);
                    }
                    let $button_add_pay_pal_container = $('.woo-orders-tracking-item-tracking-button-add-to-paypal-container');
                    if (response.paypal_button_class === 'inactive') {
                        $button_add_pay_pal_container.removeClass('woo-orders-tracking-paypal-active').addClass('woo-orders-tracking-paypal-inactive').attr('title', response.paypal_button_title);
                        $button_add_pay_pal_container.find('.woo-orders-tracking-item-tracking-button-add-to-paypal').attr('title', response.paypal_button_title);
                    } else if (response.paypal_button_class === 'active') {
                        $button_add_pay_pal_container.removeClass('woo-orders-tracking-paypal-inactive').addClass('woo-orders-tracking-paypal-active');
                        $button_add_pay_pal_container.find('.woo-orders-tracking-item-tracking-button-add-to-paypal').attr('title', response.paypal_button_title);
                    }
                    if (response.paypal_added_trackings) {
                        $('.woo-orders-tracking-item-tracking-paypal-added-tracking-numbers-values').val(response.paypal_added_trackings);
                    }
                }
            },
            error: function (err) {
                villatheme_admin_show_message('Error', 'error', err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
            },
            complete: function () {
                $('.woo-orders-tracking-saving-overlay').addClass('woo-orders-tracking-hidden');
                vi_wotg_edit_tracking_hide();
            }
        });
    }

    function wotv_save_track_info_item(data, editing) {
        let $container = editing.closest('.woo-orders-tracking-container');
        $.ajax({
            url: vi_wot_edit_order.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: data,
            beforeSend: function () {
                $('.woo-orders-tracking-saving-overlay').removeClass('woo-orders-tracking-hidden');
            },
            success: function (response) {
                if (response.hasOwnProperty('change_order_status') && response.change_order_status) {
                    $('body').find('#order_status').val(response.change_order_status).change();
                }
                if (response.tracking_service_status === 'error') {
                    villatheme_admin_show_message(response.tracking_service, response.tracking_service_status, response.tracking_service_message);
                }
                if (response.status === 'error') {
                    villatheme_admin_show_message(response.message, response.status);
                } else {
                    /*Update data*/
                    villatheme_admin_show_message(response.message, response.status, '', false, 5000);
                    editing.data('tracking_code', response.tracking_code);
                    editing.data('tracking_url', response.carrier_url);
                    editing.data('carrier_id', response.carrier_id);
                    editing.data('carrier_name', response.carrier_name);
                    editing.data('digital_delivery', response.digital_delivery);
                    editing.closest('.woo-orders-tracking-container').find('.woo-orders-tracking-item-tracking-code-value').html('<a target="_blank" href="' + response.tracking_url_show + '">' + response.tracking_code + '</a>').attr('title', response.carrier_name);
                    let $shipping_carrier_select = $('#woo-orders-tracking-edit-tracking-shipping-carrier');
                    if (data.hasOwnProperty('add_new_carrier') && response.carrier_id) {
                        let option = {
                            id: response.carrier_id,
                            text: response.carrier_name
                        };

                        let newOption = new Option(option.text, option.id, false, false);
                        $shipping_carrier_select.append(newOption).trigger('change');
                    }

                    if (response.paypal_status === 'error') {
                        villatheme_admin_show_message('Can not add tracking to PayPal', 'error', response.paypal_message);
                    }
                    let $button_add_pay_pal_container = $container.find('.woo-orders-tracking-item-tracking-button-add-to-paypal-container');
                    if (response.paypal_button_class === 'inactive') {
                        $button_add_pay_pal_container.removeClass('woo-orders-tracking-paypal-active').addClass('woo-orders-tracking-paypal-inactive').attr('title', response.paypal_button_title);
                        $button_add_pay_pal_container.find('.woo-orders-tracking-item-tracking-button-add-to-paypal').attr('title', response.paypal_button_title);
                    } else if (response.paypal_button_class === 'active') {
                        $button_add_pay_pal_container.removeClass('woo-orders-tracking-paypal-inactive').addClass('woo-orders-tracking-paypal-active');
                        $button_add_pay_pal_container.find('.woo-orders-tracking-item-tracking-button-add-to-paypal').attr('title', response.paypal_button_title);
                    }
                    if (response.paypal_added_trackings) {
                        $('.woo-orders-tracking-item-tracking-paypal-added-tracking-numbers-values').val(response.paypal_added_trackings);
                    }
                }
            },
            error: function (err) {
                villatheme_admin_show_message('Error', 'error', err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
            },
            complete: function () {
                $('.woo-orders-tracking-saving-overlay').addClass('woo-orders-tracking-hidden');
                vi_wotg_edit_tracking_hide();
            }
        });
    }

    function vi_wotg_enable_scroll() {
        let scrollTop = parseInt($('html').css('top'));
        $('html').removeClass('vi_wotg-noscroll');
        $('html,body').scrollTop(-scrollTop);
    }

    function vi_wotg_disable_scroll() {
        if ($(document).height() > $(window).height()) {
            let scrollTop = ($('html').scrollTop()) ? $('html').scrollTop() : $('body').scrollTop(); // Works for Chrome, Firefox, IE...
            $('html').addClass('vi_wotg-noscroll').css('top', -scrollTop);
        }
    }

    function vi_wotg_edit_tracking_hide() {
        $('.woo-orders-tracking-edit-tracking-carrier').val('shipping-carriers').change();
        $('.woo-orders-tracking-button-edit').removeClass('woo-orders-tracking-button-editing');
        $('.woo-orders-tracking-edit-tracking-button-save').removeAttr('class').attr('class', ' button button-primary woo-orders-tracking-edit-tracking-button-save');
        $('.woo-orders-tracking-edit-tracking-container').addClass('woo-orders-tracking-hidden');
        vi_wotg_enable_scroll();
    }

    function vi_wotg_edit_tracking_show() {
        $('.woo-orders-tracking-edit-tracking-container').removeClass('woo-orders-tracking-hidden');

        vi_wotg_disable_scroll();
    }

    function vi_wotg_get_custom_carrier_by_slug(carrier_slug) {
        let found_carrier = {};
        for (let i = 0; i < custom_carriers_list.length; i++) {
            if (custom_carriers_list[i].slug === carrier_slug) {
                found_carrier = custom_carriers_list[i];
                break;
            }
        }
        return found_carrier;
    }

    function wotv_get_shipping_carriers_html() {
        let shipping_carriers_define_list,
            carriers,
            html = '',
            shipping_carrier_default = vi_wot_edit_order.shipping_carrier_default;
        shipping_carriers_define_list = $.parseJSON(vi_wot_edit_order.shipping_carriers_define_list);
        carriers = shipping_carriers_define_list.concat(custom_carriers_list);
        if (carriers.length === 0) {
            return false;
        }
        carriers = wot_sort_carriers(carriers);
        for (let i = 0; i < carriers.length; i++) {
            html += '<option value="' + carriers[i].slug + '">' + carriers[i].name + '</option>';
        }

        $('.woo-orders-tracking-edit-tracking-shipping-carrier').html(html);
        $('.woo-orders-tracking-edit-tracking-shipping-carrier').val(shipping_carrier_default).change();

        $('.woo-orders-tracking-edit-tracking-shipping-carrier').select2({
            placeholder: "Please fill in your shipping carrier name",
            theme: "wotv-select2-custom-carrier",
            dropdownParent: $('.woo-orders-tracking-edit-tracking-container')
        });
    }
});
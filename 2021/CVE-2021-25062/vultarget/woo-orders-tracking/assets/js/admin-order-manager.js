jQuery(document).ready(function ($) {
    $(document).on('click', '.woo-orders-tracking-tracking-service-copy', function () {
        let $temp = $('<input>');
        $('body').append($temp);
        let $container = $(this).closest('.woo-orders-tracking-tracking-number-container');
        let tracking_number = $container.data('tracking_number');
        $temp.val(tracking_number).select();
        document.execCommand('copy');
        $temp.remove();
        villatheme_admin_show_message(vi_wot_admin_order_manager.message_copy, 'success', tracking_number, false, 2000);
    });
    $(document).on('click', '.woo-orders-tracking-tracking-number-column-container', function (e) {
        e.stopPropagation();
    });
    $(document).on('click', '.woo-orders-tracking-paypal-active', function () {
        let $button = $(this);
        let $paypal_image = $button.find('.woo-orders-tracking-item-tracking-button-add-to-paypal');
        let $result_icon = $('<span class="woo-orders-tracking-paypal-result dashicons"></span>');
        $.ajax({
            url: vi_wot_admin_order_manager.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'vi_woo_orders_tracking_add_tracking_to_paypal',
                item_id: $button.data('item_id'),
                order_id: $button.data('order_id'),
                action_nonce: $('#_vi_wot_item_nonce').val(),
            },
            beforeSend: function () {
                $button.find('.woo-orders-tracking-paypal-result').remove();
                $button.removeClass('woo-orders-tracking-paypal-active').addClass('woo-orders-tracking-paypal-inactive');
                $paypal_image.attr('src', vi_wot_admin_order_manager.loading_image);
            },
            success: function (response) {
                if (response.status === 'success') {
                    $result_icon.addClass('dashicons-yes-alt').addClass('woo-orders-tracking-paypal-result-success');
                    $button.append($result_icon);
                    if (response.paypal_button_title) {
                        $paypal_image.attr('title', response.paypal_button_title);
                    }
                } else {
                    $result_icon.addClass('dashicons-no-alt').addClass('woo-orders-tracking-paypal-result-error');
                    $button.removeClass('woo-orders-tracking-paypal-inactive').addClass('woo-orders-tracking-paypal-active').append($result_icon);
                    $button.append($result_icon);
                }
                $result_icon.attr('title', response.message).fadeOut(10000);
            },
            error: function (err) {
                console.log(err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
                $result_icon.addClass('dashicons-no-alt').addClass('woo-orders-tracking-paypal-result-error');
                $button.removeClass('woo-orders-tracking-paypal-inactive').addClass('woo-orders-tracking-paypal-active').append($result_icon);
                $button.append($result_icon);
            },
            complete: function () {
                $paypal_image.attr('src', vi_wot_admin_order_manager.paypal_image)
            }
        });
    });
    $(document).on('click', '.woo-orders-tracking-order-tracking-info-overlay', function (e) {
        e.stopPropagation();
    });
    $(document).on('click', '.woo-orders-tracking-order-tracking-info-icon', function (e) {
        e.stopPropagation();
        let data = $(this).data();
        let $icon = $('.woo-orders-tracking-order-tracking-info-wrap-' + data['order_id']);
        if ($icon.hasClass('woo-orders-tracking-order-tracking-info-hidden')) {
            $(this).addClass('woo-orders-tracking-order-tracking-info-open');
            $icon.removeClass('woo-orders-tracking-order-tracking-info-hidden');
        } else {
            $(this).removeClass('woo-orders-tracking-order-tracking-info-open');
            $icon.addClass('woo-orders-tracking-order-tracking-info-hidden');
        }
    });

    $(document).on('click', '.woo-orders-tracking-order-tracking-info-wrap', function (e) {
        e.stopPropagation();
    });
    $(document).on('click', '.woo-orders-tracking-order-tracking-info-refresh', function (e) {
        e.stopPropagation();
        let data = $(this).data();
        $('.woo-orders-tracking-order-tracking-info-overlay-' + data['tracking_number']).removeClass('woo-orders-tracking-order-tracking-info-hidden');
        let data_ajax = {
            action: 'vi_wot_refresh_track_info',
            order_id: data['order_id'],
            tracking_number: data['tracking_number'],
            carrier_name: data['carrier_name'],
            carrier_id: data['carrier_id'],
        };
        $.ajax({
            url: vi_wot_admin_order_manager.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: data_ajax,
            success: function (response) {
                $('.woo-orders-tracking-order-tracking-info-overlay-' + data['tracking_number']).addClass('woo-orders-tracking-order-tracking-info-hidden');
                let $status = $('.woo-orders-tracking-order-tracking-info-status-' + data['tracking_number']).find('span');
                if (response.status === 'success') {
                    $status.text(response.shipment_status).attr('title', response.shipment_last_event);
                    villatheme_admin_show_message(response.message, response.status, '', false, 5000);
                } else {
                    let message = response.message;
                    if (response.hasOwnProperty('data')) {
                        let data = response.data;
                        if (data.hasOwnProperty('track_info')) {
                            let track_info = data.track_info;
                            if (track_info.hasOwnProperty('data')) {
                                message = track_info.data;
                            }
                        }
                    }
                    $status.text('Error').attr('title', message);
                    villatheme_admin_show_message(response.message, response.status);
                }
            },
            error: function (err) {
                villatheme_admin_show_message('Error', 'error', err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
            },
        });
    });
});

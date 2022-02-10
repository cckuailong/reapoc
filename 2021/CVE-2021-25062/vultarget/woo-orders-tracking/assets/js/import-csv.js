'use strict';
jQuery(document).ready(function ($) {
    console.log(woo_orders_tracking_import_params);
    $('.vi-ui.dropdown').dropdown({placeholder: 'Do not map'});
    if (woo_orders_tracking_import_params.step === 'mapping') {
        let required_fields = woo_orders_tracking_import_params.required_fields;
        $('input[name="woo_orders_tracking_import"]').on('click', function (e) {
            let empty_required_fields = [];
            for (let field in required_fields) {
                if (required_fields.hasOwnProperty(field) && !$('#woo-orders-tracking-' + field).val()) {
                    empty_required_fields.push(required_fields[field]);
                }
            }
            if (empty_required_fields.length > 0) {
                if (empty_required_fields.length === 1) {
                    alert(empty_required_fields[0] + ' is required to map')
                } else {
                    alert('These fields are required to map: ' + empty_required_fields.join(', '));
                }
                e.preventDefault();
                return false;
            }
        })
    }

    let $progress = $('.woo-orders-tracking-import-progress');
    let $progress_paypal = $('.woo-orders-tracking-paypal-progress');
    let $progress_ppec_paypal = $('.woo-orders-tracking-ppec_paypal-progress');
    let $progress_send_email = $('.woo-orders-tracking-send-email-progress');
    let step = 'check';
    let total = 0;
    let paypal_total = 0;
    let ppec_paypal_total = 0;
    let paypal_processed = 0;
    let ppec_paypal_processed = 0;
    let ftell = 0;
    let start = parseInt(woo_orders_tracking_import_params.custom_start) - 1;
    if (start < 1) {
        start = 1;
    }
    let orders_per_request = parseInt(woo_orders_tracking_import_params.orders_per_request);
    let email_enable = woo_orders_tracking_import_params.email_enable;
    let order_status = woo_orders_tracking_import_params.order_status;
    let paypal_enable = woo_orders_tracking_import_params.paypal_enable;
    let vi_wot_index = woo_orders_tracking_import_params.vi_wot_index;
    let changed_orders = '';
    let paypal = '';
    let ppec_paypal = '';
    let $import_icon = $('.woo-orders-tracking-import-icon');
    if (woo_orders_tracking_import_params.step === 'import') {
        $progress.progress('set percent', 0);
        $import_icon.addClass('woo-orders-tracking-updating');
        $.ajax({
            url: woo_orders_tracking_import_params.url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'woo_orders_tracking_import',
                nonce: woo_orders_tracking_import_params.nonce,
                file_url: woo_orders_tracking_import_params.file_url,
                vi_wot_index: vi_wot_index,
                orders_per_request: orders_per_request,
                email_enable: email_enable,
                order_status: order_status,
                paypal_enable: paypal_enable,
                step: step,
                start: start,
            },
            success: function (response) {
                console.log(response);
                if (response.status === 'success') {
                    total = parseInt(response.total);
                    step = 'import';
                    vi_wot_import();
                } else {
                    $progress.progress('set error');
                    $import_icon.removeClass('woo-orders-tracking-updating');
                    if (response.hasOwnProperty('message')) {
                        $progress.progress('set label', 'Error: ' + response.message);
                    }
                }
            },
            error: function (err) {
                $progress.progress('set error');
                $progress.progress('set label', err.statusText);
                $import_icon.removeClass('woo-orders-tracking-updating');
            },
        });
    }

    function vi_wot_import() {
        $.ajax({
            url: woo_orders_tracking_import_params.url,
            type: 'POST',
            dataType: 'JSON',
            data: {
                action: 'woo_orders_tracking_import',
                nonce: woo_orders_tracking_import_params.nonce,
                file_url: woo_orders_tracking_import_params.file_url,
                orders_per_request: orders_per_request,
                email_enable: email_enable,
                order_status: order_status,
                paypal_enable: paypal_enable,
                vi_wot_index: vi_wot_index,
                step: step,
                ftell: ftell,
                start: start,
                total: total,
                changed_orders: changed_orders,
                paypal: paypal,
                paypal_total: paypal_total,
                paypal_processed: paypal_processed,
                ppec_paypal: ppec_paypal,
                ppec_paypal_total: ppec_paypal_total,
                ppec_paypal_processed: ppec_paypal_processed,
            },
            success: function (response) {
                console.log(response);
                let percent = response.percent;
                switch (response.status) {
                    case 'success':
                        ftell = response.ftell;
                        start = response.start;
                        changed_orders = response.changed_orders;
                        paypal = response.paypal;
                        paypal_total = response.paypal_total;
                        ppec_paypal = response.ppec_paypal;
                        ppec_paypal_total = response.ppec_paypal_total;
                        $progress.progress('set percent', percent);
                        vi_wot_import();
                        break;
                    case 'paypal':
                        changed_orders = response.changed_orders;
                        $progress.progress('complete');
                        step = 'paypal';
                        paypal = response.paypal;
                        paypal_total = response.paypal_total;
                        paypal_processed = response.paypal_processed;
                        $progress_paypal.progress('set percent', percent);
                        $progress_paypal.fadeIn(300);
                        vi_wot_import();
                        break;
                    case 'ppec_paypal':
                        changed_orders = response.changed_orders;
                        $progress.progress('complete');
                        $progress_paypal.progress('complete');
                        step = 'ppec_paypal';
                        ppec_paypal = response.ppec_paypal;
                        ppec_paypal_total = response.ppec_paypal_total;
                        ppec_paypal_processed = response.ppec_paypal_processed;
                        $progress_ppec_paypal.progress('set percent', percent);
                        $progress_ppec_paypal.fadeIn(300);
                        vi_wot_import();
                        break;
                    case 'send_email':
                        changed_orders = response.changed_orders;
                        $progress.progress('complete');
                        $progress_paypal.progress('complete');
                        $progress_ppec_paypal.progress('complete');
                        step = 'send_email';
                        $progress_send_email.fadeIn(300);
                        vi_wot_import();
                        break;
                    case 'finish':
                        $import_icon.removeClass('woo-orders-tracking-updating');
                        $progress.progress('complete');
                        $progress_paypal.progress('complete');
                        $progress_ppec_paypal.progress('complete');
                        $progress_send_email.progress('complete');
                        let message = 'Import completed.';
                        alert(message);
                        break;
                    case 'error':
                        $progress.progress('set error');
                        $progress.progress('set label', response.message);
                        break;
                    default:
                }
            },
            error: function (err) {
                $import_icon.removeClass('woo-orders-tracking-updating');
                console.log(err);
                $progress.progress('set error');
                $progress.progress('set label', 'Error');
            },
        });
    }
});

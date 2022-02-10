jQuery(document).ready(function ($) {
    let service_carrier_type = vi_wot_customize_params.service_carrier_type;
    let delivered_icons = vi_wot_customize_params.delivered_icons;
    let pickup_icons = vi_wot_customize_params.pickup_icons;
    let transit_icons = vi_wot_customize_params.transit_icons;

    function addPreviewControl(name, element, style, suffix = '', type = '') {
        if (type) {
            type = '[' + type + ']';
        }
        wp.customize('woo_orders_tracking_settings' + type + '[' + name + ']', function (value) {
            value.bind(function (newval) {
                $('#vi-wot-orders-tracking-customize-preview-' + name.replace(/_/g, '-')).html(element + '{' + style + ':' + newval + suffix + ' ; }');
            })
        })
    }

    wp.customize('woo_orders_tracking_settings[timeline_track_info_template]', function (value) {
        value.bind(function (newval) {
            switch (newval) {
                case '1':
                    $('#vi-wot-orders-tracking-customize-preview-show-timeline-template').html('.woo-orders-tracking-preview-shortcode-template-two{\n' +
                        '                display: none !important;\n' +
                        '            }\n' +
                        '            .woo-orders-tracking-preview-shortcode-template-one{\n' +
                        '                display: block;\n' +
                        '            }');
                    break;
                case '2':
                    $('#vi-wot-orders-tracking-customize-preview-show-timeline-template').html('.woo-orders-tracking-preview-shortcode-template-two{\n' +
                        '                display: block;\n' +
                        '            }\n' +
                        '            .woo-orders-tracking-preview-shortcode-template-one{\n' +
                        '                display: none !important;\n' +
                        '            }');
                    break;
            }
        });
    });
    wp.customize('woo_orders_tracking_settings[timeline_track_info_sort_event]', function (value) {
        value.bind(function (newval) {
            switch (newval) {
                case 'most_recent_to_oldest':
                    $('.woo-orders-tracking-most-recent-to-oldest').removeClass('woo-orders-tracking-shortcode-hidden');
                    $('.woo-orders-tracking-oldest-to-most-recent').addClass('woo-orders-tracking-shortcode-hidden');
                    break;
                case 'oldest_to_most_recent':
                    $('.woo-orders-tracking-oldest-to-most-recent').removeClass('woo-orders-tracking-shortcode-hidden');
                    $('.woo-orders-tracking-most-recent-to-oldest').addClass('woo-orders-tracking-shortcode-hidden');
                    break;
            }
        });
    });
    wp.customize('woo_orders_tracking_settings[timeline_track_info_date_format]', function (value) {
        value.bind(function (newval) {
            let time_format = wp.customize('woo_orders_tracking_settings[timeline_track_info_time_format]').get();
            $.ajax({
                type: 'POST',
                url: vi_wot_customize_params.ajax_url,
                data: {
                    action: 'vi_wot_customize_params_date_time_format',
                    format: newval + ' ' + time_format
                },
                beforeSend: function () {
                    // console.log( newval+' '+time_format)  ;
                },
                success: function (response) {
                    // console.log(response);
                    if (response.status && response.status === 'success') {
                        $('.woo-orders-tracking-shortcode-timeline-event-time').html(response.html);
                        $('.woo-orders-tracking-shortcode-timeline-event-content-date').html(response.html);
                    }

                },
                error: function (err) {
                    console.log(err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
                }
            });
        });
    });
    wp.customize('woo_orders_tracking_settings[timeline_track_info_time_format]', function (value) {
        value.bind(function (newval) {
            let date_format = wp.customize('woo_orders_tracking_settings[timeline_track_info_date_format]').get();
            $.ajax({
                type: 'POST',
                url: vi_wot_customize_params.ajax_url,
                data: {
                    action: 'vi_wot_customize_params_date_time_format',
                    format: date_format + ' ' + newval
                },
                success: function (response) {
                    if (response.status && response.status === 'success') {
                        $('.woo-orders-tracking-shortcode-timeline-event-time').html(response.html);
                        $('.woo-orders-tracking-shortcode-timeline-event-content-date').html(response.html);
                    }

                },
                error: function (err) {
                    console.log(err.responseText.replace(/<\/?[^>]+(>|$)/g, ""));
                }
            });
        });
    });
    wp.customize('woo_orders_tracking_settings[timeline_track_info_title]', function (value) {
        value.bind(function (newval) {
            newval = newval.replace(/{carrier_name}/g, 'Carrier Name');
            newval = newval.replace(/{tracking_number}/g, 'CUSTOMIZE_PREVIEW');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-title').html(newval.replace(/_/g, '-'));
        });
    });
    addPreviewControl('timeline_track_info_title_alignment', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-title', 'text-align', '');
    addPreviewControl('timeline_track_info_title_color', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-title', 'color', '');
    addPreviewControl('timeline_track_info_title_font_size', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-title', 'font-size', 'px');


    addPreviewControl('timeline_track_info_status_color', '.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap', 'color', '');

    wp.customize('woo_orders_tracking_settings[timeline_track_info_status_background_delivered]', function (value) {
        value.bind(function (newval) {
            $('#vi-wot-orders-tracking-customize-preview-timeline-track-info-status-background-delivered').html('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-delivered{background-color:' + newval + ' ; }');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap').addClass('woo-orders-tracking-shortcode-hidden');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-delivered').removeClass('woo-orders-tracking-shortcode-hidden');
        });
    });
    wp.customize('woo_orders_tracking_settings[timeline_track_info_status_background_pickup]', function (value) {
        value.bind(function (newval) {
            $('#vi-wot-orders-tracking-customize-preview-timeline-track-info-status-background-pickup').html('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-pickup{background-color:' + newval + ' ; }');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap').addClass('woo-orders-tracking-shortcode-hidden');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-pickup').removeClass('woo-orders-tracking-shortcode-hidden');
        });
    });
    wp.customize('woo_orders_tracking_settings[timeline_track_info_status_background_transit]', function (value) {
        value.bind(function (newval) {
            $('#vi-wot-orders-tracking-customize-preview-timeline-track-info-status-background-transit').html('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-transit{background-color:' + newval + ' ; }');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap').addClass('woo-orders-tracking-shortcode-hidden');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-transit').removeClass('woo-orders-tracking-shortcode-hidden');
        });
    });
    wp.customize('woo_orders_tracking_settings[timeline_track_info_status_background_pending]', function (value) {
        value.bind(function (newval) {
            $('#vi-wot-orders-tracking-customize-preview-timeline-track-info-status-background-pending').html('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-pending{background-color:' + newval + ' ; }');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap').addClass('woo-orders-tracking-shortcode-hidden');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-pending').removeClass('woo-orders-tracking-shortcode-hidden');
        });
    });
    wp.customize('woo_orders_tracking_settings[timeline_track_info_status_background_alert]', function (value) {
        value.bind(function (newval) {
            $('#vi-wot-orders-tracking-customize-preview-timeline-track-info-status-background-alert').html('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-alert{background-color:' + newval + ' ; }');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap').addClass('woo-orders-tracking-shortcode-hidden');
            $('.woo-orders-tracking-shortcode-timeline-wrap .woo-orders-tracking-shortcode-timeline-status-wrap.woo-orders-tracking-shortcode-timeline-status-alert').removeClass('woo-orders-tracking-shortcode-hidden');
        });
    });

    addPreviewControl('icon_delivered_color',
        '.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one ' +
        '.woo-orders-tracking-shortcode-timeline-events-wrap ' +
        '.woo-orders-tracking-shortcode-timeline-event ' +
        '.woo-orders-tracking-shortcode-timeline-icon-delivered i:before',
        'color', '', 'timeline_track_info_template_one');
    wp.customize('woo_orders_tracking_settings[timeline_track_info_template_one][icon_delivered]', function (value) {
        value.bind(function (newval) {
            if (delivered_icons.hasOwnProperty(newval)) {
                $('.woo-orders-tracking-shortcode-timeline-wrap-template-one .woo-orders-tracking-shortcode-timeline-events-wrap .woo-orders-tracking-shortcode-timeline-event .woo-orders-tracking-shortcode-timeline-icon-delivered').html(`<i class="${delivered_icons[newval]}"></i>`)
            }
        });
    });


    addPreviewControl('icon_pickup_color',
        '.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one ' +
        '.woo-orders-tracking-shortcode-timeline-events-wrap ' +
        '.woo-orders-tracking-shortcode-timeline-event ' +
        '.woo-orders-tracking-shortcode-timeline-icon-pickup i:before',
        'color', '', 'timeline_track_info_template_one');
    addPreviewControl('icon_pickup_background',
        '.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one ' +
        '.woo-orders-tracking-shortcode-timeline-events-wrap ' +
        '.woo-orders-tracking-shortcode-timeline-event ' +
        '.woo-orders-tracking-shortcode-timeline-icon-pickup ',
        'background-color', '', 'timeline_track_info_template_one');

    wp.customize('woo_orders_tracking_settings[timeline_track_info_template_one][icon_pickup]', function (value) {
        value.bind(function (newval) {
            if (pickup_icons.hasOwnProperty(newval)) {
                $('.woo-orders-tracking-shortcode-timeline-wrap-template-one .woo-orders-tracking-shortcode-timeline-events-wrap .woo-orders-tracking-shortcode-timeline-event .woo-orders-tracking-shortcode-timeline-icon-pickup').html(`<i class="${pickup_icons[newval]}"></i>`)
            }
        });
    });

    addPreviewControl('icon_transit_color',
        '.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one ' +
        '.woo-orders-tracking-shortcode-timeline-events-wrap ' +
        '.woo-orders-tracking-shortcode-timeline-event ' +
        '.woo-orders-tracking-shortcode-timeline-icon-transit i:before',
        'color', '', 'timeline_track_info_template_one');
    addPreviewControl('icon_transit_background',
        '.woo-orders-tracking-shortcode-timeline-wrap.woo-orders-tracking-shortcode-timeline-wrap-template-one ' +
        '.woo-orders-tracking-shortcode-timeline-events-wrap ' +
        '.woo-orders-tracking-shortcode-timeline-event ' +
        '.woo-orders-tracking-shortcode-timeline-icon-transit',
        'background-color', '', 'timeline_track_info_template_one');

    wp.customize('woo_orders_tracking_settings[timeline_track_info_template_one][icon_transit]', function (value) {
        value.bind(function (newval) {
            if (transit_icons.hasOwnProperty(newval)) {
                $('.woo-orders-tracking-shortcode-timeline-wrap-template-one .woo-orders-tracking-shortcode-timeline-events-wrap .woo-orders-tracking-shortcode-timeline-event .woo-orders-tracking-shortcode-timeline-icon-transit').html(`<i class="${transit_icons[newval]}"></i>`)
            }
        });
    });
    wp.customize('woo_orders_tracking_settings[custom_css]', function (value) {
        value.bind(function (newval) {
            $('#vi-wot-orders-tracking-customize-preview-custom-css').html(newval);
        });
    });


    wp.customize.preview.bind('active', function () {
        wp.customize.preview.bind('vi_wot_orders_tracking_design_template_one', function () {
            $('#vi-wot-orders-tracking-customize-preview-show-timeline').html('.woo-orders-tracking-shortcode-timeline-wrap{ display: block ; }');
            $('#vi-wot-orders-tracking-customize-preview-show-timeline-template').html('.woo-orders-tracking-preview-shortcode-template-two{\n' +
                '                display: none !important;\n' +
                '            }\n' +
                '            .woo-orders-tracking-preview-shortcode-template-one{\n' +
                '                display: block;\n' +
                '            }');

        });
        wp.customize.preview.bind('vi_wot_orders_tracking_design_general', function () {
            let template = wp.customize('woo_orders_tracking_settings[timeline_track_info_template]').get();
            switch (template) {
                case '1':
                    $('#vi-wot-orders-tracking-customize-preview-show-timeline-template').html('.woo-orders-tracking-preview-shortcode-template-two{\n' +
                        '                display: none !important;\n' +
                        '            }\n' +
                        '            .woo-orders-tracking-preview-shortcode-template-one{\n' +
                        '                display: block;\n' +
                        '            }');
                    break;
                case '2':
                    $('#vi-wot-orders-tracking-customize-preview-show-timeline-template').html('.woo-orders-tracking-preview-shortcode-template-two{\n' +
                        '                display: block;\n' +
                        '            }\n' +
                        '            .woo-orders-tracking-preview-shortcode-template-one{\n' +
                        '                display: none !important;\n' +
                        '            }');
                    break;
            }
        });
    });
});
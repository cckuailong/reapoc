/** Set AjaxQ Option */
wps_js.ajax_queue = {
    key: 'wp-statistics',
    time: 400 // millisecond
};

/**
 * Base AjaxQ function For All request
 *
 * @param url
 * @param params
 * @param callback
 * @param error_callback
 * @param type
 * @param internal
 */
wps_js.ajaxQ = function (url, params, callback, error_callback, type = 'GET', internal = true) {

    // Check Url
    if (url === false || url === "metabox") {
        url = wps_js.global.meta_box_api;
    }

    // prepare Ajax Parameter
    let ajaxQ = {
        url: url,
        type: type,
        dataType: "json",
        crossDomain: true,
        cache: false,
        data: params,
        success: function (data) {

            // Check Meta Box URL
            if (url === wps_js.global.meta_box_api && internal === true) {

                // Check is NO Data Meta Box
                if (data['no_data']) {

                    jQuery(wps_js.meta_box_inner(params.name)).empty().html(wps_js.no_meta_box_data());
                } else {

                    // Show Meta Box
                    jQuery(wps_js.meta_box_inner(params.name)).empty().html(wps_js[callback]['view'](data));

                    // Check After Load Hook
                    if (wps_js[callback]['meta_box_init']) {
                        setTimeout(function () {
                            wps_js[callback]['meta_box_init'](data);
                        }, 150);
                    }
                }
            } else {

                // If Not Meta Box Ajax
                wps_js[callback](data);
            }
        },
        error: function (xhr, status, error) {

            // Check Meta Box Error
            if (url === wps_js.global.meta_box_api && internal === true) {
                jQuery(wps_js.meta_box_inner(params.name)).empty().html(wps_js[error_callback](xhr.responseText));
            } else {

                // Global Call Back Error
                wps_js[error_callback](xhr.responseText)
            }
        }
    };

    // Check WordPress REST-API Nonce [https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/  ]
    if (url === wps_js.global.meta_box_api) {
        ajaxQ.beforeSend = function (xhr) {
            xhr.setRequestHeader('X-WP-Nonce', wps_js.global.rest_api_nonce);
            xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
        };
    }

    // Send Request and Get Response
    jQuery.ajaxq(wps_js.ajax_queue.key, ajaxQ);
};
if (wps_js.isset(wps_js.global, 'request_params', 'page') && wps_js.global.request_params.page === "platform") {

    // Check Params
    let params = {};

    // Check Extra Parameter [Days ago or Between ..]
    ['from', 'to'].forEach((key) => {
        if (wps_js.isset(wps_js.global, 'request_params', key)) {
            params[key] = wps_js.global.request_params[key];
        }
    });

    // Set Equal Height
    ['platforms-table', 'platforms'].forEach((key) => {
        jQuery("#" + wps_js.getMetaBoxKey(key) + " .inside").css('height', '430px');
    });

    // Set Loading Table-List
    jQuery("#wp-statistics-platforms-table-widget .inside").html(wps_js.placeholder());
    jQuery(".wps-ph-picture").attr("style", "height: 310px;");

    // Run Browsers Meta Box
    wps_js.run_meta_box('platforms', params, false);
}
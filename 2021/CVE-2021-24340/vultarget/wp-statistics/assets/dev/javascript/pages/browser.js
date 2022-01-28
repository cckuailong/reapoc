if (wps_js.isset(wps_js.global, 'request_params', 'page') && wps_js.global.request_params.page === "browser") {

    // Check Pagination
    let params = {};

    // Check Extra Parameter [Days ago or Between ..]
    ['from', 'to'].forEach((key) => {
        if (wps_js.isset(wps_js.global, 'request_params', key)) {
            params[key] = wps_js.global.request_params[key];
        }
    });

    // Set Equal Height
    ['browsers-table', 'browsers'].forEach((key) => {
        jQuery("#" + wps_js.getMetaBoxKey(key) + " .inside").css('height', '430px');
    });

    // Set Loading Table-List
    jQuery("#wp-statistics-browsers-table-widget .inside").html(wps_js.placeholder());
    jQuery(".wps-ph-picture").attr("style", "height: 310px;");

    // Run Browsers Meta Box
    wps_js.run_meta_box('browsers', params, false);
}
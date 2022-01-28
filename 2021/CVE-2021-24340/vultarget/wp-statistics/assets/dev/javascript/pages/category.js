if (wps_js.isset(wps_js.global, 'request_params', 'page') && wps_js.global.request_params.page === "categories") {

    // Create Params
    let params = {'ago': 30, 'type': 'category', 'ID': 0};

    // Check Extra Parameter [Days ago or Between ..]
    ['from', 'to', 'ID'].forEach((key) => {
        if (wps_js.isset(wps_js.global, 'request_params', key)) {
            params[key] = wps_js.global.request_params[key];
        }
    });

    // Set PlaceHolder For Total
    jQuery("span[id^='number-total-']").html(wps_js.rectangle_placeholder('wps-text-placeholder'));

    // Run Meta Box
    wps_js.run_meta_box('pages-chart', params, false);
}
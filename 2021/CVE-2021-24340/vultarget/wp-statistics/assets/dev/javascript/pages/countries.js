if (wps_js.isset(wps_js.global, 'request_params', 'page') && wps_js.global.request_params.page === "countries") {

    // Check Params
    let params = {'limit': 0};

    // Check Extra Parameter [Days ago or Between ..]
    ['from', 'to'].forEach((key) => {
        if (wps_js.isset(wps_js.global, 'request_params', key)) {
            params[key] = wps_js.global.request_params[key];
        }
    });

    // Run Pages list MetaBox
    wps_js.run_meta_box('countries', params, false);
}
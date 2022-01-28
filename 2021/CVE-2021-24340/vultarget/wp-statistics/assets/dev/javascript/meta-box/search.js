wps_js.search_meta_box = {

    placeholder: function () {
        return wps_js.rectangle_placeholder();
    },

    view: function (args = []) {

        // Check Hit Chart size in Different Page
        let height = wps_js.is_active('overview_page') ? 110 : 210;
        if (wps_js.isset(wps_js.global, 'request_params', 'page') && wps_js.global.request_params.page === "searches") {
            height = 80;
        }

        // Create Html
        let html = '';

        // Check Show Button Group
        if (wps_js.is_active('overview_page')) {
            html += wps_js.btn_group_chart('search', args);
            setTimeout(function () {
                wps_js.date_picker();
            }, 1000);
        }

        // Add Chart
        html += '<canvas id="' + wps_js.chart_id('search') + '" height="' + height + '"></canvas>';

        // show Data
        return html;
    },

    meta_box_init: function (args = []) {

        // Prepare Chart Data
        let datasets = [];
        let i = 0;
        Object.keys(args['search-engine']).forEach(function (key) {
            let search_engine_name = args['search-engine'][key]['name'];
            let color = wps_js.random_color(i);
            datasets.push({
                label: search_engine_name,
                data: args['stat'][search_engine_name],
                backgroundColor: 'rgba(' + color[0] + ',' + color[1] + ',' + color[2] + ',' + '0.3)',
                borderColor: 'rgba(' + color[0] + ',' + color[1] + ',' + color[2] + ',' + '1)',
                borderWidth: 1,
                fill: true
            });
            i++;
        });

        // Set Total
        if (args['total']['active'] === 1) {
            datasets.push({
                label: wps_js._('total'),
                data: args['total']['stat'],
                backgroundColor: 'rgba(' + args['total']['color'] + ', 0.2)',
                borderColor: 'rgba(' + args['total']['color'] + ', 1)',
                borderWidth: 1,
                fill: true
            });
        }
        wps_js.line_chart(wps_js.chart_id('search'), args['title'], args['date'], datasets);
    },

};
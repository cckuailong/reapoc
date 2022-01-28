wps_js.hits_meta_box = {

    placeholder: function () {
        return wps_js.rectangle_placeholder();
    },

    view: function (args = []) {

        // Check Hit Chart size in Different Page
        let height = wps_js.is_active('overview_page') ? 110 : 210;
        if (wps_js.isset(wps_js.global, 'request_params', 'page') && wps_js.global.request_params.page === "hits") {
            height = 80;
        }

        // Create Html
        let html = '';

        // Check Show Button Group
        if (wps_js.is_active('overview_page')) {
            html += wps_js.btn_group_chart('hits', args);
            setTimeout(function(){ wps_js.date_picker(); }, 1000);
        }

        // Add Chart
        html += '<canvas id="' + wps_js.chart_id('hits') + '" height="' + height + '"></canvas>';

        // show Data
        return html;
    },

    meta_box_init: function (args = []) {

        // Show chart
        this.hits_chart(wps_js.chart_id('hits'), args);

        // Set Total For Hits Page
        if (wps_js.isset(wps_js.global, 'request_params', 'page') && wps_js.global.request_params.page === "hits") {
            ["visits", "visitors"].forEach(function (key) {
                let tag = "span[id^='number-total-chart-" + key + "']";
                if (wps_js.exist_tag(tag)) {
                    jQuery(tag).html(args.total[key]);
                }
            });
        }
    },

    hits_chart: function (tag_id, args = []) {

        // Check Hit-chart for Quick State
        let params = args;
        if ('hits-chart' in args) {
            params = args['hits-chart'];
        }

        // Prepare Chart Data
        let datasets = [];
        if (wps_js.is_active('visitors')) {
            datasets.push({
                label: wps_js._('visitors'),
                data: params['visitors'],
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                fill: true
            });
        }
        if (wps_js.is_active('visits')) {
            datasets.push({
                label: wps_js._('visits'),
                data: params['visits'],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                fill: true
            });
        }
        wps_js.line_chart(tag_id, params['title'], params['date'], datasets);
    }
};
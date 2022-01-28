wps_js.exclusions_meta_box = {

    placeholder: function () {
        return wps_js.rectangle_placeholder();
    },

    view: function (args = []) {

        // Check Chart size in Different Page
        let height = wps_js.is_active('overview_page') ? 110 : 210;
        if (wps_js.isset(wps_js.global, 'request_params', 'page') && wps_js.global.request_params.page === "exclusions") {
            height = 80;
        }

        // Create Html
        let html = '';

        // Add Chart
        html += '<canvas id="' + wps_js.chart_id('exclusions') + '" height="' + height + '"></canvas>';

        // show Data
        return html;
    },

    meta_box_init: function (args = []) {

        // Show chart
        this.show_chart(wps_js.chart_id('exclusions'), args);

        // Set Total For Hits Page
        if (wps_js.isset(wps_js.global, 'request_params', 'page') && wps_js.global.request_params.page === "exclusions") {
            let tag = "span[id='number-total-chart-exclusions']";
            if (wps_js.exist_tag(tag)) {
                let sum = wps_js.sum(Object.values(args.total));
                jQuery(tag).html(wps_js.number_format(sum));
            }
        }
    },

    show_chart: function (tag_id, args = []) {

        // Prepare Chart Data
        let html = '';
        let datasets = [];
        let i = 0;
        Object.keys(args['exclusions']).forEach(function (key) {
            // Check Has Item
            let sum = wps_js.sum(Object.values(args['value'][key]));
            if (sum > 0) {

                // Push To Chart
                let item_name = args['exclusions'][key];
                let color = wps_js.random_color(i);
                datasets.push({
                    label: item_name,
                    data: args['value'][key],
                    backgroundColor: 'rgba(' + color[0] + ',' + color[1] + ',' + color[2] + ',' + '0.3)',
                    borderColor: 'rgba(' + color[0] + ',' + color[1] + ',' + color[2] + ',' + '1)',
                    borderWidth: 1,
                    fill: true
                });

                // Push to Table List
                html += `<tr><th>${item_name}</th> <th class="th-center"><span style="color: #9a9494 !important;">${wps_js.number_format(sum)}</span></th></tr>`;
                i++;
            }
        });

        if (wps_js.exist_tag("table[data-table=exclusions]")) {
            jQuery(html).insertAfter("table[data-table=exclusions] tr:first");
        }
        wps_js.line_chart(tag_id, args['title'], args['date'], datasets);
    }
};
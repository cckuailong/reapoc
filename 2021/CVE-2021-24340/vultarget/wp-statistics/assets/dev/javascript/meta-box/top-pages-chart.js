wps_js.top_pages_chart_meta_box = {

    placeholder: function () {
        return wps_js.rectangle_placeholder();
    },

    view: function (args = []) {

        // Create Html
        let html = '';

        // Check Show Button Group
        //html += wps_js.btn_group_chart('top-pages-chart', args);
        setTimeout(function () {
            wps_js.date_picker();
        }, 1000);

        // Add Chart
        html += '<canvas id="' + wps_js.chart_id('top-pages-chart') + '" height="80"></canvas>';

        // show Data
        return html;
    },

    meta_box_init: function (args = []) {
        this.top_pages_chart(wps_js.chart_id('top-pages-chart'), args);
    },

    top_pages_chart: function (tag_id, args = []) {

        // Prepare Chart Data
        let datasets = [];
        let i = 0;
        Object.keys(args['stat']).forEach(function (key) {
            let color = wps_js.random_color(i);
            datasets.push({
                label: key,
                data: args['stat'][key],
                backgroundColor: 'rgba(' + color[0] + ',' + color[1] + ',' + color[2] + ',' + '0.3)',
                borderColor: 'rgba(' + color[0] + ',' + color[1] + ',' + color[2] + ',' + '1)',
                borderWidth: 1,
                fill: true
            });
            i++;
        });

        wps_js.line_chart(tag_id, args['title'], args['date'], datasets);
    }
};
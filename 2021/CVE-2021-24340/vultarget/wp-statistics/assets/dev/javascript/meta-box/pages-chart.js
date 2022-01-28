wps_js.pages_chart_meta_box = {

    placeholder: function () {
        return wps_js.rectangle_placeholder();
    },

    view: function (args = []) {
        return '<canvas id="' + wps_js.chart_id('pages-chart') + '" height="80"></canvas>';
    },

    meta_box_init: function (args = []) {

        // Show chart
        this.show_chart(wps_js.chart_id('pages-chart'), args);

        // Set Total For Hits Page
        if(wps_js.exist_tag("span[id=number-total-visits]")) {
            jQuery("span[id=number-total-visits]").html(args.total);
        }
        if(wps_js.exist_tag("span[id=number-total-chart-visits]")) {
            jQuery("span[id=number-total-chart-visits]").html(args.total_dates);
        }
    },

    show_chart: function (tag_id, args = []) {
        wps_js.line_chart(tag_id, args['title'], args['date'], [{
            label: wps_js._('visits'),
            data: args['stat'],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
            fill: true
        }]);
    }
};
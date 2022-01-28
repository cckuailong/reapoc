wps_js.quickstats_meta_box = {

    view: function (args = []) {
        let t = '';
        t += `<table width="100%" class="widefat table-stats wps-summary-stats"><tbody>`;

        //Summary Statistics
        t += wps_js.summary_meta_box.summary_statistics(args);

        t += `</tbody></table>`;
        t += `<br><hr width="80%"/><br>`;

        // Show Chart JS
        t += `<canvas id="` + wps_js.chart_id('quickstats') + `" height="210"></canvas>`;
        return t;
    },

    meta_box_init: function (args = []) {
        wps_js.hits_meta_box.hits_chart(wps_js.chart_id('quickstats'), args);
    }
};
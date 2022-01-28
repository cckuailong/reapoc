wps_js.browsers_meta_box = {

    placeholder: function () {
        return wps_js.circle_placeholder();
    },

    view: function (args = []) {

        // Create Html
        let html = '';

        // Check Show Button Group
        if (wps_js.is_active('overview_page')) {
            html += wps_js.btn_group_chart('browsers', args);
            setTimeout(function () {
                wps_js.date_picker();
            }, 1000);
        }

        // Add Chart
        html += '<canvas id="' + wps_js.chart_id('browsers') + '" height="220"></canvas>';

        // show Data
        return html;
    },

    meta_box_init: function (args = []) {

        // Get Background Color
        let backgroundColor = [];
        let color;
        for (let i = 0; i <= 10; i++) {
            color = wps_js.random_color(i);
            backgroundColor.push('rgba(' + color[0] + ',' + color[1] + ',' + color[2] + ',' + '0.4)');
        }

        // Prepare Data
        let data = [{
            label: wps_js._('browsers'),
            data: args['browsers_value'],
            backgroundColor: backgroundColor
        }];

        // Show Chart
        wps_js.pie_chart(wps_js.chart_id('browsers'), args['browsers_name'], data);

        // Check Table information
        if (wps_js.exist_tag('#' + wps_js.getMetaBoxKey('browsers-table'))) {

            // Reset All Height
            ['browsers-table', 'browsers'].forEach((key) => {
                jQuery("#" + wps_js.getMetaBoxKey(key) + " .inside").removeAttr("style");
            });

            // Show Table information
            let tbl = `<div class="title-center">${args.title}</div>
                    <table width="100%" class="widefat table-stats">
                        <tr>
                            <td class="wps-text-danger">${wps_js._('browser')}</td>
                            <td class="wps-text-danger">${wps_js._('visitor_count')}</td>
                            <td class="wps-text-danger">${wps_js._('percentage')}</td>
                        </tr>`;

            for (let i = 0; i < args.browsers_name.length; i++) {
                tbl += `
                 <tr>
                        <td>${args.browsers_name[i]}</td>
                        <td>${(parseInt(args.browsers_value[i]) > 0 ? `${args.info.agent[i] !== "other" ? `<a href="` + args.info.visitor_page + `&agent=` + args.info.agent[i] + `&from=` + args.from + `&to=` + args.to + `" target="_blank">` : ``} ${wps_js.number_format(args.browsers_value[i])} ${(args.info.agent[i] !== "other") ? `</a>` : ``}` : args.browsers_value[i])}</td>
                        <td>${wps_js.number_format((args.browsers_value[i] / args.total) * 100)}%</td>
                 </tr>
                `;
            }

            // Set Total
            tbl += ` <tr><td>${wps_js._('total')}</td><td>${wps_js.number_format(args.total)}</td><td></td></tr>`;
            tbl += `</table>`;
            jQuery("#" + wps_js.getMetaBoxKey('browsers-table') + " .inside").html(tbl);

            // Set Equal Height
            wps_js.set_equal_height('.postBox-table .inside', '.postBox-chart .inside');

            // Add Extra Browser List Version
            let html = '';
            for (let i = 0; i < args.browsers_name.length; i++) {
                if (parseInt(args.browsers_value[i]) > 0 && args.info.agent[i]) {
                    html += `<div class="wps-title-group"><img src="${args.info.logo[i]}" alt="${args.browsers_name[i]}" style="vertical-align: -3px;"> ${args.browsers_name[i]}</div><div class="wp-clearfix"></div>`;
                    html += wps_js.Create_Half_PostBox('postBox-chart-' + args.info.agent[i], 'browser-' + args.info.agent[i] + '-chart');
                    html += wps_js.Create_Half_PostBox('postBox-table-' + args.info.agent[i], 'browser-' + args.info.agent[i] + '-table');
                    html += `<div class="wp-clearfix"></div>`;
                }
            }

            // Set Html in Page
            jQuery(html).insertAfter("#browsers-table");

            // Load function to Get Meta Box
            for (let i = 0; i < args.browsers_name.length; i++) {
                if (parseInt(args.browsers_value[i]) > 0 && args.info.agent[i]) {
                    this.run_custom_browser(args.info.agent[i]);
                }
            }
        }
    },

    run_custom_browser: function (agent) {

        // Show Placeholder
        ['browser-' + agent + '-chart', 'browser-' + agent + '-table'].forEach((key) => {
            jQuery("#" + key + " .inside").css('height', '430px');
        });
        jQuery("#browser-" + agent + "-table .inside").html(wps_js.placeholder());
        jQuery("#browser-" + agent + "-chart .inside").html(wps_js.circle_placeholder());
        jQuery(".wps-ph-picture").attr("style", "height: 310px;");

        //Prepare Params
        let params = {'name': 'browsers', 'browser': agent};
        ['from', 'to'].forEach((key) => {
            if (wps_js.isset(wps_js.global, 'request_params', key)) {
                params[key] = wps_js.global.request_params[key];
            }
        });

        // Send Request
        wps_js.ajaxQ(wps_js.global.meta_box_api, params, 'show_custom_agent', 'error_custom_agent', 'GET', false);
    }
};

/**
 * Show Custom Browser Report
 *
 * @param args
 */
wps_js.show_custom_agent = function (args) {

    // Get Browser Key
    var BrowserKey = args.info.agent[0];

    // Set Canvas Chart
    jQuery('#browser-' + BrowserKey + '-chart .inside').html(`<canvas id="` + wps_js.chart_id('browser-' + BrowserKey) + `" height="220"></canvas>`);

    // After Second Run Chart JS
    setTimeout(function () {

        // Get Background Color
        let backgroundColor = [];
        let color;
        for (let i = 0; i <= 10; i++) {
            color = wps_js.random_color(i);
            backgroundColor.push('rgba(' + color[0] + ',' + color[1] + ',' + color[2] + ',' + '0.4)');
        }

        // Prepare Data
        let data = [{
            label: wps_js._('browsers'),
            data: args['browsers_value'],
            backgroundColor: backgroundColor
        }];

        // Show Chart
        wps_js.pie_chart(wps_js.chart_id('browser-' + BrowserKey), args['browsers_name'], data);

        // Reset All Height
        ['browser-' + BrowserKey + '-chart', 'browser-' + BrowserKey + '-table'].forEach((key) => {
            jQuery("#" + key + " .inside").removeAttr("style");
        });

        // Show Table information
        let tbl = `<div class="title-center">${args.title}</div>
                    <table width="100%" class="widefat table-stats">
                        <tr>
                            <td class="wps-text-danger">${wps_js._('version_list')}</td>
                            <td class="wps-text-danger">${wps_js._('visitor_count')}</td>
                            <td class="wps-text-danger">${wps_js._('percentage')}</td>
                        </tr>`;

        for (let i = 0; i < args.browsers_name.length; i++) {
            tbl += `
                 <tr>
                    <td>${args.browsers_name[i]}</td>
                    <td>${parseInt(args.browsers_value[i]) > 0 ? wps_js.number_format(args.browsers_value[i]) : args.browsers_value[i]}</td>
                    <td>${wps_js.number_format((args.browsers_value[i] / args.total) * 100)}%</td>
                </tr>
                `;
        }

        // Set Total
        tbl += ` <tr><td>${wps_js._('total')}</td><td>${wps_js.number_format(args.total)}</td><td></td></tr>`;
        tbl += `</table>`;
        let tbl_inside = "#browser-" + BrowserKey + "-table .inside";
        jQuery(tbl_inside).html(tbl);

        // Set Equal Height
        wps_js.set_equal_height(tbl_inside, "#browser-" + BrowserKey + "-chart .inside");
    }, 500);
};

wps_js.error_custom_agent = function (data) {
    // Do Stuff
};
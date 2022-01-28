wps_js.summary_meta_box = {

    summary_statistics: function (args = []) {
        let t = '';

        // Show Visitor Online
        if (args['user_online']) {
            t = `<tr>
                    <th>${wps_js._('online_users')}:</th>
                    <th colspan="2" id="th-colspan"><span><a href="${args['user_online']['link']}">${args['user_online']['value']}</a></span></th>
                </tr>`;
        }

        // Show Visitors and Visits
        if (wps_js.is_active('visitors') || wps_js.is_active('visits')) {
            t += `<tr><th width="60%"></th>`;
            ["visitors", "visits"].forEach(function (key) {
                t += `<th class="th-center">` + (wps_js.is_active(key) ? wps_js._(key) : ``) + `</th>`;
            });
            t += `</tr>`;

            // Show Statistics in Days
            let summary_item = ["today", "yesterday", "week", "month", "year", "total"];
            for (let i = 0; i < summary_item.length; i++) {
                t += `<tr><th>${wps_js._(summary_item[i])}: </th>`;
                ["visitors", "visits"].forEach(function (key) {
                    t += `<th class="th-center">` + (wps_js.is_active(key) ? `<a href="${args[key][summary_item[i]]['link']}"><span>${args[key][summary_item[i]]['value']}</span></a>` : ``) + `</th>`;
                });
                t += `</tr>`;
            }

        }

        return t;
    },

    view: function (args = []) {
        let t = '';
        t += `<table width="100%" class="widefat table-stats wps-summary-stats"><tbody>`;

        // Summary Statistics
        t += this.summary_statistics(args);

        // Show Search Engine
        if (wps_js.is_active('visitors')) {
            t += `<tr><th colspan="3"><br><hr></th></tr>`;
            t += `<tr>
                <th colspan="3" style="text-align: center;">${wps_js.meta_box_lang('summary', 'search_engine')}</th>
            </tr>
            <tr>
                <th width="60%"></th>
                <th class="th-center">${wps_js._('today')}</th>
                <th class="th-center">${wps_js._('yesterday')}</th>
            </tr>`;

            Object.keys(args['search-engine']).forEach(function (key) {
                t += `<tr>
                    <th>
                        <img src="${args['search-engine'][key]['logo']}" alt="${args['search-engine'][key]['name']}" class="wps-engine-logo"> ${args['search-engine'][key]['name']}:
                    </th>
                    <th class="th-center"><span>${args['search-engine'][key]['today']}</span></th>
                    <th class="th-center"><span>${args['search-engine'][key]['yesterday']}</span></th>
                </tr>`;
            });

            t += `<tr>
                <th>${wps_js._('daily_total')}:</th>
                <td id="th-colspan" class="th-center"><span>${args['search-engine-total']['today']}</span></td>
                <td id="th-colspan" class="th-center"><span>${args['search-engine-total']['yesterday']}</span></td>
            </tr>
            <tr>
                <th>${wps_js._('total')}:</th>
                <th colspan="2" id="th-colspan"><span>${args['search-engine-total']['total']}</span></th>
            </tr>
            `;
        }

        // Show TimeZone
        t += `
         <tr><th colspan="3"><br><hr></th></tr>
            <tr>
                <th colspan="3" style="text-align: center;">${wps_js.meta_box_lang('summary', 'current_time_date')}<span id="time_zone"><a href="${args['timezone']['option-link']}"> ${wps_js.meta_box_lang('summary', 'adjustment')}</a></span></th>
            </tr>
            <tr>
                <th colspan="3">${wps_js._('date')}: <code dir="ltr">${args['timezone']['date']}</code></th>
            </tr>
            <tr>
               <th colspan="3">${wps_js._('time')}: <code dir="ltr">${args['timezone']['time']}</code></th>
            </tr>
        `;

        t += `</tbody></table>`;
        return t;
    }

};
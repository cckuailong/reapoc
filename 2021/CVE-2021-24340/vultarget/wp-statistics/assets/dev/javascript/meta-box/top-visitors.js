wps_js.top_visitors_meta_box = {

    view: function (args = []) {
        let t = '';
        t += `<div class="wp-statistics-responsive-table">`;
        t += `<table width="100%" class="widefat table-stats wps-report-table"><tbody>
        <tr>
            <td>${wps_js._('rank')}</td>
            <td>${wps_js._('hits')}</td>
            ` + (wps_js.is_active('geo_ip') ? `<td>${wps_js._('flag')}</td><td>${wps_js._('country')}</td>` : ``) + `
            ` + (wps_js.is_active('geo_city') ? `<td>${wps_js._('city')}</td>` : ``) + `
            <td>${wps_js._('ip')}</td>
            <td>${wps_js._('agent')}</td>
            <td>${wps_js._('platform')}</td>
            <td>${wps_js._('version')}</td>
        </tr>`;

        let i = 1;
        args.forEach(function (value) {
            t += `<tr>
            <td>${i}</td>
            <td>${value['hits']}</td>
            ` + (wps_js.is_active('geo_ip') ? `<td><img src='${value['country']['flag']}' alt='${value['country']['name']}' title='${value['country']['name']}' class='log-tools'/></td><td>${value['country']['name']}</td>` : ``) + `
            ` + (wps_js.is_active('geo_city') ? `<td>${value['city']}</td>` : ``) + `
            <td>` + (value['hash_ip'] ? value['hash_ip'] : `<a href='${value['ip']['link']}'>${value['ip']['value']}</a>`) + `</td>
            <td>${value['agent']}</td>
            <td>${value['platform']}</td>
            <td>${value['version']}</td>
			</tr>`;
            i++;
        });

        t += `</tbody></table>`;
        t += `</div>`;
        return t;
    }

};
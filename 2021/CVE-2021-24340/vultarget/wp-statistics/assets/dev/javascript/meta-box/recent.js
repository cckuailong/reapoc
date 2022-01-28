wps_js.recent_meta_box = {

    view: function (args = []) {
        let t = '';
        t += `<div class="wp-statistics-responsive-table">`;
        t += `<table width="100%" class="widefat table-stats wps-report-table"><tbody>
        <tr>
            <td>${wps_js._('browser')}</td>
            ` + (wps_js.is_active('geo_ip') ? `<td>${wps_js._('country')}</td>` : ``) + `
            ` + (wps_js.is_active('geo_city') ? `<td>${wps_js._('city')}</td>` : ``) + `
            <td>${wps_js._('date')}</td>
            <td>${wps_js._('ip')}</td>
            <td>${wps_js._('referrer')}</td>
        </tr>`;

        args.forEach(function (value) {
            t += `<tr>
            <td style="text-align: left"><a href="${value['browser']['link']}" title="${value['browser']['name']}"><img src="${value['browser']['logo']}" alt="${value['browser']['name']}" class='log-tools' title='${value['browser']['name']}'/></a></td>
            ` + (wps_js.is_active('geo_ip') ? `<td style="text-align: left"><img src='${value['country']['flag']}' alt='${value['country']['name']}' title='${value['country']['name']}' class='log-tools'/></td>` : ``) + `
            ` + (wps_js.is_active('geo_city') ? `<td>${value['city']}</td>` : ``) + `
            <td style="text-align: left">${value['date']}</td>
            <td style="text-align: left">` + (value['hash_ip'] ? value['hash_ip'] : `<a href='${value['ip']['link']}'>${value['ip']['value']}</a>`) + `</td>
            <td style="text-align: left">${value['referred']}</td>
			</tr>`;
        });

        t += `</tbody></table>`;
        t += `</div>`;
        return t;
    }

};
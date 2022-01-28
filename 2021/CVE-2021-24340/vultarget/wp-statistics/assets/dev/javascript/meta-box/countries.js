wps_js.countries_meta_box = {

    view: function (args = []) {
        let t = '';
        t += `<table width="100%" class="widefat table-stats wps-report-table"><tbody>
        <tr>
            <td width="10%" style='text-align: left'>${wps_js._('rank')}</td>
            <td width="10%" style='text-align: left'>${wps_js._('flag')}</td>
            <td width="40%" style='text-align: left'>${wps_js._('country')}</td>
            <td width="40%" style='text-align: left'>${wps_js._('visitor_count')}</td>
        </tr>`;

        let i = 1;
        args.forEach(function (value) {
            t += `<tr>
			<td style='text-align: left;'>${i}</td>
			<td style='text-align: left;'><img src="${value['flag']}" title="${value['name']}" alt="${value['name']}"/></td>
			<td style='text-align: left;'>${value['name']}</td>
			<td style='text-align: left;'><a href="${value['link']}" title="${value['name']}" target="_blank">${wps_js.number_format(value['number'])}</a></td>
			</tr>`;
            i++;
        });

        t += `</tbody></table>`;
        return t;
    }

};
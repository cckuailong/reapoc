wps_js.useronline_meta_box = {

    view: function (args = []) {
        let t = '';
        t += `<table class="widefat table-stats wps-report-table wps-table-fixed">
        <tr>
        ` + (wps_js.is_active('geo_ip') ? `<td style='text-align: left;'>${wps_js._('country')}</td>` : ``) + `
            <td style='text-align: left;'>${wps_js._('ip')}</td>
            <td width="35%" style='text-align: left;'>${wps_js._('page')}</td>
            <td style='text-align: left;'>${wps_js._('referrer')}</td>
        </tr>`;

        args.forEach(function (value) {
            t += `<tr>
            ` + (wps_js.is_active('geo_ip') ? `<td style="text-align: left"><img src='${value['country']['flag']}' alt='${value['country']['name']}' title='${value['country']['name']}' class='log-tools'/></td>` : ``) + `
            <td style='text-align: left !important'>` + (value['hash_ip'] ? value['hash_ip'] : value['ip']['value']) + `</td>
            <td style='text-align: left !important;'><span class="wps-text-wrap">` + (value['page']['link'].length > 2 ? `<a href="${value['page']['link']}" title="${value['page']['title']}" target="_blank" class="wps-text-danger">` : ``) + value['page']['title'] + (value['page']['link'].length > 2 ? `</a>` : ``) + `</span></td>
            <td style="text-align: left !important">${value['referred']}</td>
			</tr>`;
        });

        t += `</table>`;
        return t;
    }

};
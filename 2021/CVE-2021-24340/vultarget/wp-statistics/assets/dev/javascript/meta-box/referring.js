wps_js.referring_meta_box = {

    view: function (args = []) {
        let t = '';
        t += `<table width="100%" class="widefat table-stats wps-report-table"><tbody>
        <tr>
            <td width="80%">${wps_js._('address')}</td>
            <td width="20%">${wps_js.meta_box_lang('referring', 'references')}</td>
        </tr>`;

        args.forEach(function (value) {
            t += `<tr>
			<td>` + wps_js.site_icon(value['domain']) + ` <a href='//${value['domain']}' title='${value['title']}' target="_blank">${value['domain']}</a>
            <span class='wps-cursor-default wps-referring-widget-ip'` + (value['country'].length > 2 ? ` title="${value['country']}"` : ``) + `>${value['ip']}</span></td>
			<td class="wps-middle-vertical"><a href="${value['page_link']}">${value['number']}</a></td>
			</tr>`;
        });

        t += `</tbody></table>`;
        return t;
    }

};
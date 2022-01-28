wps_js.pages_meta_box = {

    view: function (args = []) {
        let t = '';
        t += `<table width="100%" class="widefat table-stats wps-report-table"><tbody>
        <tr>
            <td width='10%'>${wps_js._('id')}</td>
            <td width='40%'>${wps_js._('title')}</td>
            <td width='40%'>${wps_js._('link')}</td>
            <td width='10%'>${wps_js._('visits')}</td>
        </tr>`;

        let i = 1;
        args.forEach(function (value) {
            t += `<tr>
			<td style='text-align: left;'>${i}</td>
			<td style='text-align: left;'><span title='${value['title']}' class='wps-cursor-default wps-text-wrap'>${value['title']}</span></td>
			<td style='text-align: left;'><a href="${value['link']}" title="${value['title']}" target="_blank">${value['str_url']}</a></td>
		    <td style="text-align: left"><a href="${value['hits_page']}" class="wps-text-danger">${value['number']}</a></td>
			</tr>`;
            i++;
        });

        t += `</tbody></table>`;
        return t;
    }

};
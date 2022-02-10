// Don't wait for DOM ready.
var now = new Date();
jQuery.post(
        emeclock.translate_ajax_url, {
		action: 'eme_client_clock',
		client_unixtime: Math.round(now.getTime() / 1000), // make seconds
		client_seconds: now.getSeconds(),
		client_minutes: now.getMinutes(),
		client_hours: now.getHours(),
		client_wday: now.getDay(),
		client_mday: now.getDate(),
		client_month: now.getMonth()+1, // make 1-12
		client_fullyear: now.getFullYear()},
        function(ret) {
                if (ret == '1') {
			// we refresh if the cookie is actually there
			// people can refuse the cookie ...
			if (document.cookie.indexOf('eme_client_time') != -1) {
				top.location.href = self.location.href;
			}
                }
        }
);

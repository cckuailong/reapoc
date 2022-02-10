window['gtag_enable_tcf_support'] = {enable_tcf_support};
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', {
	'security_storage': "granted",
	'functionality_storage': "granted",
	'personalization_storage': "denied",
	'analytics_storage': 'denied',
	'ad_storage': "denied",
});

document.addEventListener("cmplzFireCategories", function (e) {
	var consentedCategory = e.detail.category;
	if ( consentedCategory === 'marketing' ) {
		gtag('consent', 'update', {
			'ad_storage': 'granted',
			'analytics_storage': 'granted',
			'personalization_storage': 'granted'
		});
	} else if ( consentedCategory === 'statistics' ) {
		gtag('consent', 'update', {
			'analytics_storage': 'granted',
			'personalization_storage': 'granted',
		});
	} else if ( consentedCategory === 'preferences' ) {
		gtag('consent', 'update', {
			'personalization_storage': 'granted'
		});
	} else {
		gtag('consent', 'update', {
			'security_storage': "granted",
			'functionality_storage': "granted",
			'personalization_storage': "denied",
			'analytics_storage': 'denied',
			'ad_storage': "denied",
		});
	}
});

document.addEventListener("cmplzCookieWarningLoaded", function (e) {
	gtag('js', new Date());
	gtag('config', '{G_code}', {
		cookie_flags:'secure;samesite=none',
	{anonymize_ip}
	});
});

document.addEventListener("cmplzRevoke", function (e) {
	gtag('consent', 'update', {
		'security_storage': "granted",
		'functionality_storage': "granted",
		'personalization_storage': "denied",
		'analytics_storage': 'denied',
		'ad_storage': "denied",
	});
});

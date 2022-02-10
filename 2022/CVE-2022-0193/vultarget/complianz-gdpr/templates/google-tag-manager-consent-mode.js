window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('consent', 'default', {
	'security_storage': "granted",
	'functionality_storage': "granted",
	'personalization_storage': "denied",
	'analytics_storage': 'denied',
	'ad_storage': "denied",
});

dataLayer.push({
	'event': 'default_consent'
});

document.addEventListener("cmplzFireTMCategories", function (e) {
	var consentedCategory = e.detail;
	if ( consentedCategory === '0') {
		gtag('consent', 'update', {
			'analytics_storage': 'granted'
		});
	} else {
		gtag('consent', 'update', {
			'analytics_storage': 'denied'
		});
	}
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
	(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','{GTM_code}');
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

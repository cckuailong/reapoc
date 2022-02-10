window['gtag_enable_tcf_support'] = {enable_tcf_support};
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', '{G_code}', {
	cookie_flags:'secure;samesite=none',
	{anonymize_ip}
});

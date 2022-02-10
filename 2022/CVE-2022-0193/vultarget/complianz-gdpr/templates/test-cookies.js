/**
 * Script to test site for cookies. Never inserted for visitors, only for admin.
 */
jQuery(document).ready(function ($) {
	//create an element we can click on to accept cookies
	$('body').append('<div id="cmplz_test_cookies_div" class="cmplz-accept-cookies"></div>');
	/**
	 * Force all cookies to be accepted, starting with complianz
	 */
	$(document).on("cmplzCookieWarningLoaded", cmplzForceAcceptCookies);
	function cmplzForceAcceptCookies(consentData) {
		$('.cmplz-accept-cookies').click();
	}

    var cookies = get_cookies_array();
    var lstorage = get_localstorage_array();

    $.post(
        '{admin_url}',
        {
            action: 'store_detected_cookies',
            cookies: cookies,
            lstorage: lstorage,
            token: '{token}',
            complianz_id: '{id}'
        }
    );

    function get_localstorage_array() {
        var lstorage = {};
        for (i = 0; i < localStorage.length; i++) {

            lstorage[localStorage.key(i)] = localStorage.key(i);
        }
        for (i = 0; i < sessionStorage.length; i++) {
            lstorage[sessionStorage.key(i)] = sessionStorage.key(i);
        }


        return lstorage;
    }

    function get_cookies_array() {
        var cookies = {};
        if (document.cookie && document.cookie != '') {
            var split = document.cookie.split(';');
            for (var i = 0; i < split.length; i++) {
                var name_value = split[i].split("=");
                name_value[0] = name_value[0].replace(/^ /, '');
                cookies[decodeURIComponent(name_value[0])] = decodeURIComponent(name_value[1]);
            }
        }

        return cookies;

    }
});

function cmplz_function_exists(function_name) {
    if (typeof function_name == 'string') {
        return (typeof window[function_name] == 'function');
    } else {
        return (function_name instanceof Function);
    }
}

function deleteAllCookies() {
    document.cookie.split(";").forEach(function (c) {
        document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");
    });
}

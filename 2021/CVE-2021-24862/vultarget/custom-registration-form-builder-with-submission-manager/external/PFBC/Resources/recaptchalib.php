<?php
/*
 * This is a PHP library that handles calling reCAPTCHA.
 *    - Documentation and latest version
 *          http://recaptcha.net/plugins/php/
 *    - Get a reCAPTCHA API Key
 *          https://www.google.com/recaptcha/admin/create
 *    - Discussion group
 *          http://groups.google.com/group/recaptcha
 *
 * Copyright (c) 2007 reCAPTCHA -- http://recaptcha.net
 * AUTHORS:
 *   Mike Crawford
 *   Ben Maurer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * The reCAPTCHA server URL's
 */
define("RM_RECAPTCHA_API_SERVER", "http://www.google.com/recaptcha/api");
define("RM_RECAPTCHA_API_SECURE_SERVER", "https://www.google.com/recaptcha/api");
define("RM_RECAPTCHA_VERIFY_SERVER", "https://www.google.com");

/**
 * Submits an HTTP request to a reCAPTCHA server
 * @param string $host
 * @param string $path
 * @param array $data
 */

function _rm_recaptcha_http_get($host, $path, $data, $port = 80) {

    $verifyResponse = wp_remote_get($host.$path.'?secret='.$data['secret'].'&response='.$data['response']);
    if (!is_wp_error($verifyResponse)) {
        $response = json_decode($verifyResponse['body']);
    }
    else{
        $response = new stdClass();
        $response->success = "0";
    }
    return $response;

}



/**
 * Gets the challenge HTML (javascript and non-javascript version).
 * This is called from the browser, and the resulting reCAPTCHA HTML widget
 * is embedded within the HTML form it was called from.
 * @param string $pubkey A public key for reCAPTCHA
 * @param string $error The error given by reCAPTCHA (optional, default is null)
 * @param boolean $use_ssl Should the request be made over ssl? (optional, default is false)

 * @return string - The HTML to be embedded in the user's form.
 */
function rm_recaptcha_get_html($pubkey, $version, $error = null, $use_ssl = false)
{
	if ($pubkey == null || $pubkey == '') {
		die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
	}
	
	if ($use_ssl) {
                $server = RM_RECAPTCHA_API_SECURE_SERVER;
        } else {
                $server = RM_RECAPTCHA_API_SERVER;
        }

        $errorpart = "";
        if ($error) {
           $errorpart = "&amp;error=" . $error;
        }
        if($version==3)
            return '<input type="hidden" class="g-recaptcha-response" id="g-recaptcha-response" name="g-recaptcha-response"><script type=text/javascript>if(typeof rm_captcha_site_key === "undefined")rm_captcha_site_key="'.$pubkey.'";</script></pre>';
        
        return '<pre class="rm-pre-wrapper-for-script-tags"><script type=text/javascript>if(typeof rm_captcha_site_key === "undefined")rm_captcha_site_key="'.$pubkey.'";</script></pre><div style="overflow:hidden" class="g-recaptcha" data-sitekey="'.$pubkey.'"></div>';

}




/**
 * A RM_ReCaptchaResponse is returned from rm_recaptcha_check_answer()
 */
class RM_ReCaptchaResponse {
        var $is_valid;
        var $error;
}


/**
  * Calls an HTTP GET function to verify if the user's guess was correct
  * @param string $privkey
  * @param string $remoteip
  * @param string $challenge
  * @param string $response
  * @param array $extra_params an array of extra variables to post to the server
  * @return RM_ReCaptchaResponse
  */
function rm_recaptcha_check_answer ($privkey, $remoteip, $response, $extra_params = array())
{
	if ($privkey == null || $privkey == '') {
		die ("To use reCAPTCHA you must get an API key from <a href='https://www.google.com/recaptcha/admin/create'>https://www.google.com/recaptcha/admin/create</a>");
	}

	if ($remoteip == null || $remoteip == '') {
		die ("For security reasons, you must pass the remote ip to reCAPTCHA");
	}

	
	
        //discard spam submissions
        if ($response == null || strlen($response) == 0) {
                $recaptcha_response = new RM_ReCaptchaResponse();
                $recaptcha_response->is_valid = false;
                $recaptcha_response->error = 'incorrect-captcha-sol';
                return $recaptcha_response;
        }
        $response = _rm_recaptcha_http_get (RM_RECAPTCHA_VERIFY_SERVER, "/recaptcha/api/siteverify",
                                          array (
                                                 'secret' => $privkey,
                                                 'remoteip' => $remoteip,
                                                 'response' => $response
                                                 ) + $extra_params
                                          );
        $recaptcha_response = new RM_ReCaptchaResponse();

        if ($response->success=="1") {
                $recaptcha_response->is_valid = true;
        }
        else {
                $recaptcha_response->is_valid = false;
                $recaptcha_response->error = __('Captcha Error', 'custom-registration-form-builder-with-submission-manager');
        }
        return $recaptcha_response;

}


?>

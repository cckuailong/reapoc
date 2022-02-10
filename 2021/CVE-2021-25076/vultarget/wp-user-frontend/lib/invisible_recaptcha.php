<?php

/**
* Name:  Google Invisible reCAPTCHA
*
* Author: Geordy James
*         @geordyjames
*
* Location: https://github.com/geordyjames/google-Invisible-reCAPTCHA

* Created:  13.03.2017

* Created by Geordy James to make a easy version of google Invisible reCAPTCHA PHP Library
*
* Description:  This is an unofficial version of google Invisible reCAPTCHA PHP Library
*
*/

class Invisible_Recaptcha{

	private static $_signupUrl = "https://www.google.com/recaptcha/admin";

	public function __construct( $site_key, $secret_key ){
		
		if ( $secret_key == null || $secret_key == "" ) {
            die("To use reCAPTCHA you must get an API key from <a href='"
                . self::$_signupUrl . "'>" . self::$_signupUrl . "</a>");
        }
        $this->config = array(
			'client-key' => $site_key,
			'secret-key' => $secret_key
		);
    }

    
	public function verifyResponse($recaptcha){

		$remoteIp = $this->getIPAddress();

		// Discard empty solution submissions
		if (empty($recaptcha)) {
			return array(
				'success' => false,
				'error-codes' => 'missing-input',
			);
		}

		$getResponse = $this->getHTTP(
			array(
				'secret' => $this->config['secret-key'],
				'remoteip' => $remoteIp,
				'response' => $recaptcha,
			)
		);

		// get reCAPTCHA server response
		$responses = json_decode($getResponse, true);

		if (isset($responses['success']) and $responses['success'] == true) {
			$status = true;
		} else {
			$status = false;
			$error = (isset($responses['error-codes'])) ? $responses['error-codes']
				: 'invalid-input-response';
		}

		return array(
			'success' => $status,
			'error-codes' => (isset($error)) ? $error : null,
		);
	}


	private function getIPAddress(){
		if (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
		$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
		 $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else
		{
		  $ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}

	private function getHTTP($data){

		$url = 'https://www.google.com/recaptcha/api/siteverify?'.http_build_query($data);
		$response = file_get_contents($url);

		return $response;
	}
}


<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// paypal post
add_action('admin_post_add_wpedon_button_ipn', 'wpplugin_wpedon_button_ipn');
add_action('admin_post_nopriv_add_wpedon_button_ipn', 'wpplugin_wpedon_button_ipn');

function wpplugin_wpedon_button_ipn() {

	$options = get_option('wpedon_settingsoptions');
	foreach ($options as $k => $v ) { $value[$k] = $v; }
	
	if ($value['mode'] == "1") {
		define("USE_SANDBOX", 1);
	} else {
		define("USE_SANDBOX", 0);
	}

	$raw_post_data = file_get_contents('php://input');
	$raw_post_array = explode('&', $raw_post_data);
	$myPost = array();
	foreach ($raw_post_array as $keyval) {
		$keyval = explode ('=', $keyval);
		if (count($keyval) == 2)
			$myPost[$keyval[0]] = urldecode($keyval[1]);
	}

	$req = 'cmd=_notify-validate';
	if(function_exists('get_magic_quotes_gpc')) {
		$get_magic_quotes_exists = true;
	}
	foreach ($myPost as $key => $value) {
		if($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
			$value = urlencode(stripslashes($value));
		} else {
			$value = urlencode($value);
		}
		$req .= "&$key=$value";
	}

	if(USE_SANDBOX == true) {
		$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
	} else {
		$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
	}

	$ch = curl_init($paypal_url);
	if ($ch == FALSE) {
		return FALSE;
	}

	curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

	if(WP_DEBUG === true) {
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
	}

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

	$res = curl_exec($ch);
	if (curl_errno($ch) != 0)
		{
		if(WP_DEBUG === true) {
			error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, WP_DEBUG_LOG);
		}
		curl_close($ch);
		exit;

	} else {
			if(WP_DEBUG === true) {
				error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, WP_DEBUG_LOG);
				error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, WP_DEBUG_LOG);
			}
			curl_close($ch);
	}

	$tokens = 						explode("\r\n\r\n", trim($res));
	$res = 							trim(end($tokens));

	if (strcmp ($res, "VERIFIED") == 0) {

		// assign posted variables to local variables
		$txn_id = 					sanitize_text_field($_POST['txn_id']);
		$custom = 					sanitize_text_field($_POST['custom']);
		
		// lookup post author to save ipn as based on author of button
		$post_id_data = 		get_post($custom); 
		$post_id_author = 		$post_id_data->post_author;
		
		// save responce to db
		
		// make sure txt id isset, if payment is recurring paypal will post successful ipn separately and that should not be logged
		if (!empty($txn_id)) {
			
			// assign posted variables to local variables
			$item_name = 			sanitize_text_field($_POST['item_name']);
			$item_number = 			intval($_POST['item_number']);
				if (!$item_number) { $item_number = "";	}
			$payment_status = 		sanitize_text_field($_POST['payment_status']);
			$payment_amount = 		sanitize_text_field($_POST['mc_gross']);
			$payment_currency = 	sanitize_text_field($_POST['mc_currency']);
			$payer_email = 			sanitize_email($_POST['payer_email']);
			$purchased_quantity = 	sanitize_text_field($_POST['quantity']);
			$fee = 					sanitize_text_field($_POST['mc_fee']);
			$payment_cycle = 		sanitize_text_field($_POST['payment_cycle']);
			
			$ipn_post = array(
				'post_title'    => $item_name,
				'post_status'   => 'publish',
				'post_author'   => $post_id_author,
				'post_type'     => 'wpplugin_don_order'
			);
			
			// left here as a debugging tool
			//$payment_cycle = file_get_contents("php://input");
			
			$post_id = wp_insert_post($ipn_post);
			update_post_meta($post_id, 'wpedon_button_item_number', $item_number);
			update_post_meta($post_id, 'wpedon_button_payment_status', $payment_status);
			update_post_meta($post_id, 'wpedon_button_payment_amount', $payment_amount);
			update_post_meta($post_id, 'wpedon_button_payment_currency', $payment_currency);
			update_post_meta($post_id, 'wpedon_button_txn_id', $txn_id);
			update_post_meta($post_id, 'wpedon_button_payer_email', $payer_email);
			update_post_meta($post_id, 'wpedon_button_payment_cycle', $payment_cycle);
			
		}
		
		if(WP_DEBUG === true) {
			error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, WP_DEBUG_LOG);
		}
	} else if (strcmp ($res, "INVALID") == 0) {
		// log for manual investigation
		if(WP_DEBUG === true) {
			error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, WP_DEBUG_LOG);
		}
		
	}

}
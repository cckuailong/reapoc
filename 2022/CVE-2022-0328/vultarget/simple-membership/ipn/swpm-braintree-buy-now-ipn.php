<?php

include(SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm_handle_subsc_ipn.php');

class SwpmBraintreeBuyNowIpnHandler {

    public function __construct() {

        $this->handle_braintree_ipn();
    }

    public function handle_braintree_ipn() {
        SwpmLog::log_simple_debug("Braintree Buy Now IPN received. Processing request...", true);
        //SwpmLog::log_simple_debug(print_r($_REQUEST, true), true);//Useful for debugging purpose
        //Include the Braintree library.
        require_once(SIMPLE_WP_MEMBERSHIP_PATH . 'lib/braintree/lib/autoload.php');

        //Read and sanitize the request parameters.
        $button_id = filter_input(INPUT_POST, 'item_number', FILTER_SANITIZE_NUMBER_INT);
        $button_title = filter_input(INPUT_POST, 'item_name', FILTER_SANITIZE_STRING);
        $payment_amount = filter_input(INPUT_POST, 'item_price', FILTER_SANITIZE_STRING);

        //Retrieve the CPT for this button
        $button_cpt = get_post($button_id);
        if (!$button_cpt) {
            //Fatal error. Could not find this payment button post object.
            SwpmLog::log_simple_debug("Fatal Error! Failed to retrieve the payment button post object for the given button ID: " . $button_id, false);
            wp_die("Fatal Error! Payment button (ID: " . $button_id . ") does not exist. This request will fail.");
        }

        $membership_level_id = get_post_meta($button_id, 'membership_level_id', true);

        //Validate and verify some of the main values.
        $true_payment_amount = get_post_meta($button_id, 'payment_amount', true);
        $true_payment_amount = apply_filters('swpm_payment_amount_filter',$true_payment_amount,$button_id);
        if ($payment_amount != $true_payment_amount) {
            //Fatal error. Payment amount may have been tampered with.
            $error_msg = 'Fatal Error! Received payment amount (' . $payment_amount . ') does not match with the original amount (' . $true_payment_amount . ')';
            SwpmLog::log_simple_debug($error_msg, false);
            wp_die($error_msg);
        }

        //Validation passed. Go ahead with the charge.
        //Sandbox and other settings
        $settings = SwpmSettings::get_instance();
        $sandbox_enabled = $settings->get_value('enable-sandbox-testing');
        if ($sandbox_enabled) {
            SwpmLog::log_simple_debug("Sandbox payment mode is enabled. Using sandbox enviroment.", true);
            $braintree_env = "sandbox"; //Use sandbox environment
        } else {
            $braintree_env = "production"; //Use production environment
        }

        //Set Braintree library environment and keys
        try {
            Braintree_Configuration::environment($braintree_env);
            Braintree_Configuration::merchantId(get_post_meta($button_id, 'braintree_merchant_acc_id', true));
            Braintree_Configuration::publicKey(get_post_meta($button_id, 'braintree_public_key', true));
            Braintree_Configuration::privateKey(get_post_meta($button_id, 'braintree_private_key', true));

            $braintree_merc_acc_name = get_post_meta($button_id, 'braintree_merchant_acc_name', true);


            // Create the charge on Braintree's servers - this will charge the user's card

            $nonce = filter_input(INPUT_POST, 'payment_method_nonce', FILTER_SANITIZE_STRING);

            $result = Braintree_Transaction::sale([
                        'amount' => $payment_amount,
                        'paymentMethodNonce' => $nonce,
                        'channel' => 'TipsandTricks_SP',
                        'options' => [
                            'submitForSettlement' => True
                        ],
                        'merchantAccountId' => $braintree_merc_acc_name,
            ]);
        } catch (Exception $e) {
            SwpmLog::log_simple_debug("Braintree library error occurred: " . get_class($e) . ", button ID: " . $button_id, false);
            wp_die('Braintree library error occurred: ' . get_class($e));
        }

        if (!$result->success) {
            SwpmLog::log_simple_debug("Braintree transaction error occurred: " . $result->transaction->status . ", button ID: " . $button_id, false);
            wp_die("Braintree transaction error occurred: " . $result->transaction->status);
        } else {

            //Everything went ahead smoothly with the charge.
            SwpmLog::log_simple_debug("Braintree Buy Now charge successful.", true);

            //Grab the transaction ID.
            $txn_id = $result->transaction->id; //$charge->balance_transaction;

            $custom = filter_input(INPUT_POST, 'custom', FILTER_SANITIZE_STRING);
            $custom_var = SwpmTransactions::parse_custom_var($custom);
            $swpm_id = isset($custom_var['swpm_id']) ? $custom_var['swpm_id'] : '';

            //Create the $ipn_data array.
            $ipn_data = array();
            $ipn_data['mc_gross'] = $payment_amount;
            $ipn_data['first_name'] = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
            $ipn_data['last_name'] = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
            $ipn_data['payer_email'] = filter_input(INPUT_POST, 'member_email', FILTER_SANITIZE_EMAIL);
            $ipn_data['membership_level'] = $membership_level_id;
            $ipn_data['txn_id'] = $txn_id;
            $ipn_data['subscr_id'] = $txn_id;
            $ipn_data['swpm_id'] = $swpm_id;
            $ipn_data['ip'] = $custom_var['user_ip'];
            $ipn_data['custom'] = $custom;
            $ipn_data['gateway'] = 'braintree';
            $ipn_data['status'] = 'completed';

            $ipn_data['address_street'] = '';
            $ipn_data['address_city'] = '';
            $ipn_data['address_state'] = '';
            $ipn_data['address_zipcode'] = '';
            $ipn_data['country'] = '';

            //Handle the membership signup related tasks.
            swpm_handle_subsc_signup_stand_alone($ipn_data, $membership_level_id, $txn_id, $swpm_id);

            //Save the transaction record
            SwpmTransactions::save_txn_record($ipn_data);
            SwpmLog::log_simple_debug('Transaction data saved.', true);

            //Trigger the stripe IPN processed action hook (so other plugins can can listen for this event).
            do_action('swpm_braintree_ipn_processed', $ipn_data);

            do_action('swpm_payment_ipn_processed', $ipn_data);

            //Redirect the user to the return URL (or to the homepage if a return URL is not specified for this payment button).
            $return_url = get_post_meta($button_id, 'return_url', true);
            if (empty($return_url)) {
                $return_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
            }
            SwpmLog::log_simple_debug("Redirecting customer to: " . $return_url, true);
            SwpmLog::log_simple_debug("End of Braintree Buy Now IPN processing.", true, true);
            SwpmMiscUtils::redirect_to_url($return_url);
        }
    }

}

$swpm_braintree_buy_ipn = new SwpmBraintreeBuyNowIpnHandler();

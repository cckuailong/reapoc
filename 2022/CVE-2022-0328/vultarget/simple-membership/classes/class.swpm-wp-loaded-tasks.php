<?php

class SwpmWpLoadedTasks {

	public function __construct() {

	}

	/*
	 * This is triggered after all plugins, themes and WP has loaded.
	 * It is triggered after init, plugins_loaded etc.
	 */
	public function do_wp_loaded_tasks() {
		$this->synchronise_swpm_logout_for_wp_users();

		//IPN listener
		$this->swpm_ipn_listener();

		//Cancel subscirption action listener
		$cancel_sub_action = filter_input( INPUT_POST, 'swpm_do_cancel_sub', FILTER_SANITIZE_NUMBER_INT );

		if ( ! empty( $cancel_sub_action ) ) {
			$this->do_cancel_sub();
		}

	}

	/*
	 * Logs out the user from the swpm session if they are logged out of the WP user session
	 */
	public function synchronise_swpm_logout_for_wp_users() {
		if ( ! is_user_logged_in() ) {
			/* WP user is logged out. So logout the SWPM user (if applicable) */
			if ( SwpmMemberUtils::is_member_logged_in() ) {

				//Check if force WP user login sync is enabled or not
				$force_wp_user_sync = SwpmSettings::get_instance()->get_value( 'force-wp-user-sync' );
				if ( empty( $force_wp_user_sync ) ) {
					return '';
				}
				/* Force WP user login sync is enabled. */
				/* SWPM user is logged in the system. Log him out. */
				SwpmLog::log_auth_debug( 'synchronise_swpm_logout_for_wp_users() - Force wp user login sync is enabled. ', true );
				SwpmLog::log_auth_debug( 'WP user session is logged out for this user. So logging out of the swpm session also.', true );
				wp_logout();
			}
		}
	}

	/* Payment Gateway IPN listener */

	public function swpm_ipn_listener() {

		//Listen and handle PayPal IPN
		$swpm_process_ipn = filter_input( INPUT_GET, 'swpm_process_ipn' );
		if ( $swpm_process_ipn == '1' ) {
			include SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm_handle_pp_ipn.php';
			exit;
		}

		//Listen and handle Stripe Buy Now IPN
		$swpm_process_stripe_buy_now = filter_input( INPUT_GET, 'swpm_process_stripe_buy_now' );
		if ( $swpm_process_stripe_buy_now == '1' ) {
			include SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm-stripe-buy-now-ipn.php';
			exit;
		}

		//Listen and handle Stripe SCA Buy Now IPN
		$swpm_process_stripe_sca_buy_now = filter_input( INPUT_GET, 'swpm_process_stripe_sca_buy_now' );
		if ( $swpm_process_stripe_sca_buy_now == '1' ) {
			include SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm-stripe-sca-buy-now-ipn.php';
			exit;
		}

		//Listen and handle Stripe Subscription IPN
		$swpm_process_stripe_subscription = filter_input( INPUT_GET, 'swpm_process_stripe_subscription' );
		if ( $swpm_process_stripe_subscription == '1' ) {
			include SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm-stripe-subscription-ipn.php';
			exit;
		}

		//Listen and handle Stripe SCA Subscription IPN
		$swpm_process_stripe_sca_subscription = filter_input( INPUT_GET, 'swpm_process_stripe_sca_subscription' );
		$hook                                 = filter_input( INPUT_GET, 'hook', FILTER_SANITIZE_NUMBER_INT );
		if ( $swpm_process_stripe_sca_subscription == '1' ) {
						//$hook == 1 means it is a background post via webshooks. Otherwise it is direct post to the script after payment (at the time of payment).
			if ( $hook ) {
				include SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm-stripe-subscription-ipn.php';
			} else {
				include SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm-stripe-sca-subscription-ipn.php';
			}
			exit;
		}

		//Listen and handle Braintree Buy Now IPN
		$swpm_process_braintree_buy_now = filter_input( INPUT_GET, 'swpm_process_braintree_buy_now' );
		if ( $swpm_process_braintree_buy_now == '1' ) {
			include SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm-braintree-buy-now-ipn.php';
			exit;
		}

		if ( wp_doing_ajax() ) {
			//Listen and handle smart paypal checkout IPN
			include SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm-smart-checkout-ipn.php';
			add_action( 'wp_ajax_swpm_process_pp_smart_checkout', 'swpm_pp_smart_checkout_ajax_hanlder' );
			add_action( 'wp_ajax_nopriv_swpm_process_pp_smart_checkout', 'swpm_pp_smart_checkout_ajax_hanlder' );

			//Listed and handle Stripe SCA checkout session create requests
			require_once SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm-stripe-sca-buy-now-ipn.php';
		}
	}

	private function do_cancel_sub() {

		function msg( $msg, $is_error = true ) {
			echo $msg;
			echo '<br><br>';
			echo SwpmUtils::_( 'You will be redirected to the previous page in a few seconds. If not, please <a href="">click here</a>.' );
			echo '<script>function toPrevPage(){window.location = window.location.href;}setTimeout(toPrevPage,5000);</script>';
			if ( ! $is_error ) {
				wp_die( '', SwpmUtils::_( 'Success!' ), array( 'response' => 200 ) );
			}
			wp_die();
		}

		$token = filter_input( INPUT_POST, 'swpm_cancel_sub_token', FILTER_SANITIZE_STRING );
		if ( empty( $token ) ) {
			//no token
			msg( SwpmUtils::_( 'No token provided.' ) );
		}

		//check nonce
		$nonce = filter_input( INPUT_POST, 'swpm_cancel_sub_nonce', FILTER_SANITIZE_STRING );
		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, $token ) ) {
			//nonce check failed
			msg( SwpmUtils::_( 'Nonce check failed.' ) );
		}

		if ( ! SwpmMemberUtils::is_member_logged_in() ) {
			//member not logged in
			msg( SwpmUtils::_( 'You are not logged in.' ) );
		}

		$member_id = SwpmMemberUtils::get_logged_in_members_id();

		$subs = new SWPM_Member_Subscriptions( $member_id );

		$sub = $subs->find_by_token( $token );

		if ( empty( $sub ) ) {
			//no subscription found
			return false;
		}

		$res = $subs->cancel( $sub['sub_id'] );

		if ( $res !== true ) {
			msg( $res );
		}

		msg( SwpmUtils::_( 'Subscription has been cancelled.' ), false );

	}

}

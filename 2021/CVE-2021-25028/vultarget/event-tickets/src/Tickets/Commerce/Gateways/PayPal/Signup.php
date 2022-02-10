<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal;

use TEC\Tickets\Commerce\Gateways\PayPal\Location\Country;
use TEC\Tickets\Commerce\Gateways\PayPal\REST\On_Boarding_Endpoint;
use TEC\Tickets\Commerce\Settings;
use Tribe__Utils__Array as Arr;

/**
 * Class Signup
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */
class Signup {

	/**
	 * Holds the transient key used to store hash passed to PayPal.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $signup_hash_meta_key = 'tec_tc_paypal_signup_hash';

	/**
	 * Holds the transient key used to link PayPal to this site.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	public static $signup_data_meta_key = 'tec_tc_paypal_signup_data';

	/**
	 * Stores the instance of the template engine that we will use for rendering the page.
	 *
	 * @since 5.1.9
	 *
	 * @var \Tribe__Template
	 */
	protected $template;

	/**
	 * Gets the template instance used to setup the rendering of the page.
	 *
	 * @since 5.1.9
	 *
	 * @return \Tribe__Template
	 */
	public function get_template() {
		if ( empty( $this->template ) ) {
			$this->template = new \Tribe__Template();
			$this->template->set_template_origin( \Tribe__Tickets__Main::instance() );
			$this->template->set_template_folder( 'src/admin-views/settings/tickets-commerce/paypal' );
			$this->template->set_template_context_extract( true );
		}

		return $this->template;
	}

	/**
	 * Gets the saved hash for a given user, empty when non-existent.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_transient_hash() {
		return get_transient( static::$signup_hash_meta_key );
	}

	/**
	 * Gets the saved hash for a given user, empty when non-existent.
	 *
	 * @since 5.1.9
	 *
	 * @param string $value Hash for signup.
	 *
	 * @return bool
	 */
	public function update_transient_hash( $value ) {
		return set_transient( static::$signup_hash_meta_key, $value, DAY_IN_SECONDS );
	}

	/**
	 * Delete Hash transient from the DB.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function delete_transient_hash() {
		return delete_transient( static::$signup_hash_meta_key );
	}

	/**
	 * Gets the saved hash for a given user, empty when non-existent.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function get_transient_data() {
		return get_transient( static::$signup_data_meta_key );
	}

	/**
	 * Saves the URL in a transient for later use.
	 *
	 * @since 5.1.9
	 *
	 * @param string $value URL for signup.
	 *
	 * @return bool
	 */
	public function update_transient_data( $value ) {
		return set_transient( static::$signup_data_meta_key, $value, DAY_IN_SECONDS );
	}

	/**
	 * Delete url transient from the DB.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function delete_transient_data() {
		return delete_transient( static::$signup_data_meta_key );
	}

	/**
	 * Generate a Unique Hash for signup. It will always be 20 characters long.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function generate_unique_signup_hash() {
		$nonce_key  = defined( 'NONCE_KEY' ) ? NONCE_KEY : uniqid( '', true );
		$nonce_salt = defined( 'NONCE_SALT' ) ? NONCE_SALT : uniqid( '', true );

		$unique = uniqid( '', true );

		$keys = [ $nonce_key, $nonce_salt, $unique ];
		$keys = array_map( 'md5', $keys );

		return substr( str_shuffle( implode( '-', $keys ) ), 0, 45 );
	}

	/**
	 * Generates a Tracking it for this website.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function generate_unique_tracking_id() {
		$id = wp_generate_password( 6, false, false );;
		$url_frags = wp_parse_url( home_url() );
		$url       = Arr::get( $url_frags, 'host' ) . Arr::get( $url_frags, 'path' );
		$url       = add_query_arg( [
			'v' => Gateway::VERSION . '-' . $id,
		], $url );

		/**
		 * Tracking ID sent to PayPal.
		 *
		 * @since 5.1.9
		 *
		 * @param string $url Which ID we are using normally a URL, cannot be longer than 127 chars.
		 */
		$url = apply_filters( 'tec_tickets_commerce_gateway_paypal_tracking_id', $url );

		// Always limit it to 127 chars.
		return substr( (string) $url, 0, 127 );
	}

	/**
	 * Request the signup link that redirects the seller to PayPal.
	 *
	 * @since 5.1.9
	 *
	 * @param string $country Which country code we are generating the URL for.
	 * @param bool   $force   It prevents the system from using the cached version of the URL.
	 *
	 * @return string|false
	 */
	public function generate_url( $country, $force = false ) {
		// Fetch the cached value for this user.
		$signup = $this->get_transient_data();
		if (
			false === $force
			&& $signup_url = Arr::get( $signup, [ 'links', 1, 'href' ] )
		) {
			return $signup_url;
		}

		$hash = $this->generate_unique_signup_hash();
		$this->update_transient_hash( $hash );

		$signup = tribe( WhoDat::class )->get_seller_signup_data( $hash, $country );

		if ( ! $signup_url = Arr::get( $signup, [ 'links', 1, 'href' ] ) ) {
			return false;
		}

		$this->update_transient_data( $signup );

		return $signup_url;
	}

	/**
	 * From the Transient data store we get the referral data link.
	 *
	 * @since 5.1.9
	 *
	 * @return false|string
	 */
	public function get_referral_data_link() {
		$links = $this->get_transient_data();
		if ( empty( $links ) ) {
			return false;
		}

		return Arr::get( $links, [ 'links', 0, 'href' ], false );
	}

	/**
	 * Refresh the Connect link when someone changes the country.
	 *
	 * @since 5.2.0
	 *
	 * @return void
	 */
	public function ajax_refresh_connect_url() {
		$data  = [];
		$nonce = tribe_get_request_var( 'nonce' );

		if ( ! wp_verify_nonce( $nonce, 'tec-tickets-commerce-gateway-paypal-refresh-connect-url' ) ) {
			wp_send_json_error( new \WP_Error( 'tec-tickets-commerce-paypal-nonce-problem' ) );
			return;
		}

		$country = tribe_get_request_var( 'country_code', 'US' );

		// Save to the DB.
		tribe( Country::class )->save_setting( $country );

		$new_url = $this->generate_url( $country, true );
		if ( empty( $new_url ) ) {
			wp_send_json_error( new \WP_Error( 'tec-tickets-commerce-paypal-refresh-connect-url-error' ) );
			return;
		}

		// Append the minibrowser query arg.
		$data['new_url'] = $new_url . '&displayMode=minibrowser';

		wp_send_json_success( $data );
		return;
	}

	/**
	 * Gets the content for the template used for the sign up link that paypal creates.
	 *
	 * @since 5.1.9
	 *
	 * @return false|string
	 */
	public function get_link_html() {
		$country       = tribe( Country::class )->get_setting();
		$template_vars = [
			'url'          => $this->generate_url( $country ),
			'country_code' => $country,
		];

		return $this->get_template()->template( 'signup-link', $template_vars, false );
	}


	/**
	 * Validate seller on Boarding status data.
	 *
	 * @since 5.2.0
	 *
	 * @return string[]
	 */
	public function get_errors_from_on_boarded_data() {
		$error_messages = [];
		$merchant = tribe( Merchant::class );
		$saved_merchant_id = $merchant->get_merchant_id_in_paypal();
		if ( ! $saved_merchant_id ) {
			return [];
		}

		$seller_status = tribe( WhoDat::class )->get_seller_status( $saved_merchant_id );

		if ( array_diff( [ 'payments_receivable', 'primary_email_confirmed' ], array_keys( $seller_status ) ) ) {
			$error_messages[] = esc_html__( 'There was a problem with the status check for your account. Please try disconnecting and connecting again. If the problem persists, please contact support.', 'event-tickets' );

			// Return here since the rest of the validations will definitely fail
			return $error_messages;
		}

		$payments_receivable = tribe_is_truthy( Arr::get( $seller_status, 'payments_receivable' ) );
		$primary_email_confirmed = tribe_is_truthy( Arr::get( $seller_status, 'primary_email_confirmed' ) );

		if ( ! $payments_receivable ) {
			$error_messages[] = esc_html__( 'Set up an account to receive payment from PayPal', 'event-tickets' );
		}

		if ( ! $primary_email_confirmed ) {
			$error_messages[] = esc_html__( 'Confirm your primary email address', 'event-tickets' );
		}

		$merchant->set_account_is_ready( $payments_receivable && $primary_email_confirmed );
		$merchant->save();

		if ( ! $merchant->get_supports_custom_payments() ) {
			return ! empty( $error_messages ) ? $error_messages : [];
		}

		if ( array_diff( [ 'products', 'capabilities' ], array_keys( $seller_status ) ) ) {
			$error_messages[] = esc_html__( 'Your account was expected to be able to accept custom payments, but is not. Please make sure your
				account country matches the country setting. If the problem persists, please contact PayPal.', 'event-tickets' );

			// Return here since the rest of the validations will definitely fail
			return $error_messages;
		}

		// Grab the PPCP_CUSTOM product from the status data
		$custom_product = current(
			array_filter(
				$seller_status['products'],
				static function ( $product ) {
					return 'PPCP_CUSTOM' === $product['name'];
				}
			)
		);

		$has_custom_payments = 'SUBSCRIBED' === Arr::get( $custom_product, 'vetting_status' );
		if ( ! $has_custom_payments ) {
			$error_messages[] = esc_html__( 'Reach out to PayPal to enable PPCP_CUSTOM for your account', 'event-tickets' );
		}

		// Loop through the capabilities and see if any are not active
		$invalid_capabilities = [];
		foreach ( $seller_status['capabilities'] as $capability ) {
			if ( $capability['status'] !== 'ACTIVE' ) {
				$invalid_capabilities[] = $capability['name'];
			}
		}

		if ( ! empty( $invalid_capabilities ) ) {
			$error_messages[] = esc_html__( 'Reach out to PayPal to resolve the following capabilities:', 'event-tickets' ) . ' ' . implode( ', ', $invalid_capabilities );
		}

		// If there were errors then redirect the user with notices
		return $error_messages;
	}

	/**
	 * When toggling between PayPal test mode and live mode, we need to re-generate
	 * the transients to make sure the proper URLs are used.
	 *
	 * @since 5.2.0
	 *
	 * @param array $options the list of plugin options set for saving
	 *
	 * @return array
	 */
	public function maybe_delete_transient_data( $options ) {

		if ( ! isset( $options[ Settings::$option_sandbox ] ) ) {
			return $options;
		}

		if ( tec_tickets_commerce_is_sandbox_mode() === $options[ Settings::$option_sandbox ] ) {
			return $options;
		}

		$this->delete_transient_data();

		return $options;
	}
}

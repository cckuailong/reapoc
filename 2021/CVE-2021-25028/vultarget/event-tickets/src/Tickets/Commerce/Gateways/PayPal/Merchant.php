<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal;

use Tribe__Utils__Array as Arr;
use Tribe__Date_Utils as Dates;
use TEC\Tickets\Commerce\Traits\Has_Mode;

/**
 * Class Merchant.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */
class Merchant {
	use Has_Mode;

	/**
	 * All account Props we use for the merchant
	 *
	 * @since 5.1.9
	 *
	 * @var string[]
	 */
	protected $account_props = [
		'signup_hash',
		'merchant_id',
		'merchant_id_in_paypal',
		'client_id',
		'client_secret',
		'account_is_ready',
		'account_is_connected',
		'supports_custom_payments',
		'active_custom_payments',
		'account_country',
		'access_token',
	];

	/**
	 * Determines if the data needs to be saved to the Database
	 *
	 * @since 5.1.9
	 *
	 * @var boolean
	 */
	protected $needs_save = false;

	/**
	 * PayPal merchant Id  (email address).
	 *
	 * @since 5.1.9
	 *
	 * @var null|string
	 */
	protected $merchant_id;

	/**
	 * A Hash used during signup that should be associated with the merchant.
	 *
	 * @since 5.1.9
	 *
	 * @var null|string
	 */
	protected $signup_hash;

	/**
	 * PayPal merchant id.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $merchant_id_in_paypal;

	/**
	 * Client id.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $client_id;

	/**
	 * Client Secret.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $client_secret;

	/**
	 * Client token.
	 *
	 * @since 5.1.9
	 *
	 * @var array
	 */
	protected $client_token;

	/**
	 * How long till the Client token expires
	 *
	 * @since 5.1.9
	 *
	 * @var int
	 */
	protected $client_token_expires_in;

	/**
	 * Access token.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $access_token;

	/**
	 * Whether or not the connected account is ready to process payments.
	 *
	 * @since 5.1.9
	 *
	 * @var bool
	 */
	protected $account_is_ready = false;

	/**
	 * Whether or not an account is connected.
	 *
	 * @since 5.2.0
	 *
	 * @var bool
	 */
	protected $account_is_connected = false;

	/**
	 * Whether or not the account is setup make custom payments (i.e Advanced Fields & PPCP), doesn't necessarily mean
	 * that the account can accept payments yet.
	 *
	 * @since 5.1.9
	 *
	 * @var bool
	 */
	protected $supports_custom_payments = false;

	/**
	 * Whether or not the account is ready to take custom payments (i.e Advanced Fields & PPCP).
	 *
	 * @since 5.2.0
	 *
	 * @var bool
	 */
	protected $active_custom_payments = false;

	/**
	 * PayPal account account country.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $account_country;

	/**
	 * Fetches the current signup hash.
	 *
	 * @since 5.1.9
	 *
	 * @return string|null
	 */
	public function get_signup_hash() {
		return $this->signup_hash;
	}

	/**
	 * Sets the value for signup hash locally, in this instance of the Merchant.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed   $value      Value used for the signup hash.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_signup_hash( $value, $needs_save = true ) {
		$this->set_value( 'signup_hash', $value, $needs_save );
	}

	/**
	 * Fetches the current Merchant ID.
	 *
	 * @since 5.1.9
	 *
	 * @return string|null
	 */
	public function get_merchant_id() {
		return $this->merchant_id;
	}

	/**
	 * Sets the value for Merchant ID locally, in this instance of the Merchant.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed   $value      Value used for the Merchant ID.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_merchant_id( $value, $needs_save = true ) {
		$this->set_value( 'merchant_id', $value, $needs_save );
	}

	/**
	 * Gets the value stored for the Merchant ID in PayPal.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_merchant_id_in_paypal() {
		return $this->merchant_id_in_paypal;
	}

	/**
	 * Sets the value for Merchant ID in PayPal locally, in this instance of the Merchant.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed   $value      Value used for the Merchant ID in PayPal.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_merchant_id_in_paypal( $value, $needs_save = true ) {
		$this->set_value( 'merchant_id_in_paypal', $value, $needs_save );
	}

	/**
	 * Gets the value stored for the Client ID.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_client_id() {
		return $this->client_id;
	}

	/**
	 * Sets the value for Merchant ID locally, in this instance of the Merchant.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed   $value      Value used for the Merchant ID.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_client_id( $value, $needs_save = true ) {
		$this->client_id = $value;
	}

	/**
	 * Gets the value stored for the Client Secret.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_client_secret() {
		return $this->client_secret;
	}

	/**
	 * Sets the value for Client Secret locally, in this instance of the Merchant.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed   $value      Value used for the Client Secret.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_client_secret( $value, $needs_save = true ) {
		$this->set_value( 'client_secret', $value, $needs_save );
	}

	/**
	 * Gets the value stored for the Access Token.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_access_token() {
		return $this->access_token;
	}

	/**
	 * Sets the value for Access Token locally, in this instance of the Merchant.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed   $value      Value used for the Access Token.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_access_token( $value, $needs_save = true ) {
		$this->set_value( 'access_token', $value, $needs_save );
	}

	/**
	 * Gets the value stored for if the account is ready for usage.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_account_is_ready() {
		return $this->account_is_ready;
	}

	/**
	 * Sets the value for if this account is ready for usage locally, in this instance of the Merchant.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed   $value      Value used for the Account is Ready.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_account_is_ready( $value, $needs_save = true ) {
		$this->set_value( 'account_is_ready', $value, $needs_save );
	}

	/**
	 * Gets the value stored for if the account is ready for usage.
	 *
	 * @since 5.2.0
	 *
	 * @return string
	 */
	public function get_account_is_connected() {
		return $this->account_is_connected;
	}

	/**
	 * Sets the value for if this account is connected for usage locally, in this instance of the Merchant.
	 *
	 * @since 5.2.0
	 *
	 * @param mixed   $value      Value used for the Account is Connect.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_account_is_connected( $value, $needs_save = true ) {
		$this->set_value( 'account_is_connected', $value, $needs_save );
	}

	/**
	 * Gets the value stored for if this account supports custom payments.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function get_supports_custom_payments() {
		return tribe_is_truthy( $this->supports_custom_payments );
	}

	/**
	 * Sets the value determining if this supports custom payments locally, in this instance of the Merchant.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed   $value      Value used for the Support for Custom Payments.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_supports_custom_payments( $value, $needs_save = true ) {
		$this->set_value( 'supports_custom_payments', $value, $needs_save );
	}

	/**
	 * Gets the value stored for if this account supports custom payments.
	 *
	 * @since 5.2.0
	 *
	 * @return bool
	 */
	public function get_active_custom_payments() {
		return tribe_is_truthy( $this->active_custom_payments );
	}

	/**
	 * Sets the value determining if this supports custom payments locally, in this instance of the Merchant.
	 *
	 * @since 5.2.0
	 *
	 * @param mixed   $value      Value used for the Support for Custom Payments.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_active_custom_payments( $value, $needs_save = true ) {
		$this->set_value( 'active_custom_payments', $value, $needs_save );
	}

	/**
	 * Gets the value stored for the Country Code.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_account_country() {
		return $this->account_country;
	}

	/**
	 * Sets the value for Account Country locally, in this instance of the Merchant.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed   $value      Value used for the Account Country.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	public function set_account_country( $value, $needs_save = true ) {
		$this->set_value( 'account_country', $value, $needs_save );
	}

	/**
	 * Determines if this instances needs to be saved to the DB.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function needs_save() {
		return tribe_is_truthy( $this->needs_save );
	}


	/**
	 * Returns the options key for the account in the merchant mode.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_account_key() {
		$gateway_key   = Gateway::get_key();
		$merchant_mode = $this->get_mode();

		return "tec_tickets_commerce_{$gateway_key}_{$merchant_mode}_account";
	}

	/**
	 * Returns the data retrieved from the access token refreshing process.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_access_token_data_key() {
		$gateway_key   = Gateway::get_key();
		$merchant_mode = $this->get_mode();

		return "tec_tickets_commerce_{$gateway_key}_{$merchant_mode}_access_token_data";
	}

	/**
	 * Returns the data retrieved from the signup process.
	 *
	 * Uses normal WP options to be saved, instead of the normal tribe_update_option.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_signup_data_key() {
		$gateway_key   = Gateway::get_key();
		$merchant_mode = $this->get_mode();

		return "tec_tickets_commerce_{$gateway_key}_{$merchant_mode}_signup_data";
	}

	/**
	 * Returns the options key for the account errors in the merchant mode.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_account_errors_key() {
		$gateway_key   = Gateway::get_key();
		$merchant_mode = $this->get_mode();

		return "tec_tickets_commerce_{$gateway_key}_{$merchant_mode}_account_errors";
	}

	/**
	 * Returns the options key for the account account information.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_user_info_key() {
		$gateway_key   = Gateway::get_key();
		$merchant_mode = $this->get_mode();

		return "tec_tickets_commerce_{$gateway_key}_{$merchant_mode}_user_info";
	}

	/**
	 * Handle initial setup for the object singleton.
	 *
	 * @since 5.1.9
	 */
	public function init() {
		$this->set_mode( tec_tickets_commerce_is_sandbox_mode() ? 'sandbox' : 'live' );
		$this->from_array( $this->get_details_data(), false );
	}

	/**
	 * Return array of merchant details.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function to_array() {
		return [
			'signup_hash'              => $this->get_signup_hash(),
			'merchant_id'              => $this->get_merchant_id(),
			'merchant_id_in_paypal'    => $this->get_merchant_id_in_paypal(),
			'client_id'                => $this->get_client_id(),
			'client_secret'            => $this->get_client_secret(),
			'account_is_ready'         => $this->get_account_is_ready(),
			'account_is_connected'     => $this->get_account_is_connected(),
			'supports_custom_payments' => $this->get_supports_custom_payments(),
			'active_custom_payments'   => $this->get_active_custom_payments(),
			'account_country'          => $this->get_account_country(),
			'access_token'             => $this->get_access_token(),
		];
	}

	/**
	 * Make Merchant object from array.
	 *
	 * @since 5.1.9
	 *
	 * @param array   $data       Which values need to .
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 *
	 * @return boolean
	 */
	public function from_array( array $data, $needs_save = true ) {
		if ( ! $this->validate( $data ) ) {
			return false;
		}

		$this->setup_properties( $data, $needs_save );

		return true;
	}

	/**
	 * Saves a given base value into the class props.
	 *
	 * @since 5.1.9
	 *
	 * @param string $key
	 * @param mixed  $value
	 * @param bool   $needs_save
	 *
	 */
	protected function set_value( $key, $value, $needs_save = true ) {
		$this->{$key} = $value;

		// Determine if we will need to save in the DB.
		if ( $needs_save ) {
			$this->needs_save = true;
		}
	}

	/**
	 * Setup properties from array.
	 *
	 * @since 5.1.9
	 *
	 * @param array   $data       Which values need to be saved.
	 * @param boolean $needs_save Determines if the proprieties saved need to save to the DB.
	 */
	protected function setup_properties( array $data, $needs_save = true ) {
		if ( array_key_exists( 'signup_hash', $data ) ) {
			$this->set_signup_hash( $data['signup_hash'], $needs_save );
		}
		if ( array_key_exists( 'merchant_id', $data ) ) {
			$this->set_merchant_id( $data['merchant_id'], $needs_save );
		}
		if ( array_key_exists( 'merchant_id_in_paypal', $data ) ) {
			$this->set_merchant_id_in_paypal( $data['merchant_id_in_paypal'], $needs_save );
		}
		if ( array_key_exists( 'client_id', $data ) ) {
			$this->set_client_id( $data['client_id'], $needs_save );
		}
		if ( array_key_exists( 'client_secret', $data ) ) {
			$this->set_client_secret( $data['client_secret'], $needs_save );
		}
		if ( array_key_exists( 'account_is_ready', $data ) ) {
			$this->set_account_is_ready( $data['account_is_ready'], $needs_save );
		}
		if ( array_key_exists( 'account_is_connected', $data ) ) {
			$this->set_account_is_connected( $data['account_is_connected'], $needs_save );
		}
		if ( array_key_exists( 'active_custom_payments', $data ) ) {
			$this->set_active_custom_payments( $data['active_custom_payments'], $needs_save );
		}
		if ( array_key_exists( 'supports_custom_payments', $data ) ) {
			$this->set_supports_custom_payments( $data['supports_custom_payments'], $needs_save );
		}
		if ( array_key_exists( 'account_country', $data ) ) {
			$this->set_account_country( $data['account_country'], $needs_save );
		}
		if ( array_key_exists( 'access_token', $data ) ) {
			$this->set_access_token( $data['access_token'], $needs_save );
		}
	}

	/**
	 * Validate merchant details.
	 *
	 * @since 5.1.6
	 *
	 * @param array $merchant_details
	 */
	public function validate( $merchant_details ) {
		$required = $this->account_props;

		if ( array_diff( $required, array_keys( $merchant_details ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Save merchant details.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function save() {
		if ( false === $this->needs_save() ) {
			return false;
		}

		$saved = update_option( $this->get_account_key(), $this->to_array() );

		// If we were able to save, we reset the needs save.
		if ( $saved ) {
			$this->needs_save = false;
		}

		return $saved;
	}

	/**
	 * Get the merchant details data.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	protected function get_details_data() {
		return (array) get_option( $this->get_account_key(), [] );
	}

	/**
	 * Delete merchant account details on the Database.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function delete_data() {
		$status = update_option( $this->get_account_key(), null );

		if ( $status ) {
			$data = array_fill_keys( $this->account_props, null );
			// reset internal values.
			$this->setup_properties( $data, false );
		}

		return $status;
	}

	/**
	 * Returns the account errors if there are any.
	 *
	 * @since 5.1.9
	 *
	 * @return string[]|null
	 */
	public function get_account_errors() {
		return get_option( $this->get_account_errors_key(), null );
	}

	/**
	 * Saves the account error message.
	 *
	 * @since 5.1.9
	 *
	 * @param string[] $error_message
	 *
	 * @return bool
	 */
	public function save_account_errors( $error_message ) {
		return update_option( $this->get_account_errors_key(), $error_message );
	}

	/**
	 * Deletes the errors for the account.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function delete_account_errors() {
		return delete_option( $this->get_account_errors_key() );
	}


	/**
	 * Saves signup data from the transient into a permanent storage.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function get_access_token_data() {
		return get_option( $this->get_access_token_data_key(), [] );
	}

	/**
	 * Saves the access token data, and adds some extra information for better usage.
	 *
	 * @since 5.1.9
	 *
	 * @param array $token_data
	 *
	 * @return bool
	 */
	public function save_access_token_data( array $token_data ) {
		if ( empty( $token_data['access_token'] ) ) {
			return false;
		}

		$this->set_access_token( $token_data['access_token'] );
		$this->save();

		if ( ! empty( $token_data['expires_in'] ) ) {
			$expires_in = Dates::interval( 'PT' . $token_data['expires_in'] . 'S' );

			// Store date related data in readable formats.
			$token_data['token_retrieval_time']  = Dates::build_date_object( 'now' )->format( Dates::DBDATETIMEFORMAT );
			$token_data['token_expiration_time'] = Dates::build_date_object( 'now' )->add( $expires_in )->format( Dates::DBDATETIMEFORMAT );
		}

		return update_option( $this->get_access_token_data_key(), $token_data );
	}

	/**
	 * Delete access token data.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function delete_access_token_data() {
		return update_option( $this->get_access_token_data_key(), null );
	}

	/**
	 * Saves signup data from the transient into permanent option.
	 *
	 * @since 5.1.9
	 *
	 * @param array $signup_data
	 *
	 * @return bool
	 */
	public function save_signup_data( array $signup_data ) {
		return update_option( $this->get_signup_data_key(), $signup_data );
	}

	/**
	 * Saves signup data from the transient into a permanent storage.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function get_signup_data() {
		return get_option( $this->get_signup_data_key(), [] );
	}

	/**
	 * Deletes signup data from the DB.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function delete_signup_data() {
		return delete_option( $this->get_signup_data_key() );
	}

	/**
	 * Saves user info to make sure we have full access later on.
	 *
	 * @since 5.1.9
	 *
	 * @param array $user_info User info from the PayPal API.
	 *
	 * @return bool
	 */
	public function save_user_info( array $user_info = [] ) {
		return update_option( $this->get_user_info_key(), $user_info );
	}

	/**
	 * Deletes the user info from the DB.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function delete_user_info() {
		return delete_option( $this->get_user_info_key() );
	}

	/**
	 * Saves signup data from the transient into
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function get_user_info() {
		return get_option( $this->get_user_info_key(), [] );
	}

	/**
	 * Returns whether or not the account has been connected
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function account_is_connected() {
		return tribe_is_truthy( $this->merchant_id_in_paypal );
	}

	/**
	 * Disconnects the merchant completely.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function disconnect() {
		$statuses = [
			$this->delete_data(),
			$this->delete_access_token_data(),
			$this->delete_signup_data(),
			$this->delete_user_info(),
			$this->delete_account_errors(),
		];

		return in_array( false, $statuses, true );
	}

	/**
	 * Determines if the Merchant is connected.
	 *
	 * @since 5.2.0
	 *
	 * @return bool
	 */
	public function is_connected( $recheck = false ) {
		$saved_merchant_id = $this->get_merchant_id_in_paypal();

		if ( ! $saved_merchant_id ) {
			return false;
		}

		if ( ! $recheck && true === $this->get_account_is_connected() ) {
			return true;
		}

		$seller_status = tribe( WhoDat::class )->get_seller_status( $saved_merchant_id );
		$product_names = wp_list_pluck( Arr::get( $seller_status, [ 'products' ] ), 'name' );

		$is_connected = count( array_intersect( $product_names, [ 'EXPRESS_CHECKOUT', 'PPCP_CUSTOM', 'PPCP_STANDARD' ] ) ) >= 1;

		if ( $is_connected ) {
			$this->set_account_is_connected( true );
			$this->save();
		}

		return $is_connected;
	}

	/**
	 * Determines if the Merchant is active.
	 *
	 * @since 5.1.9
	 *
	 * @return bool
	 */
	public function is_active( $recheck = false ) {
		$saved_merchant_id = $this->get_merchant_id_in_paypal();

		if ( ! $saved_merchant_id ) {
			return false;
		}

		if ( ! $this->is_connected( $recheck ) ) {
			return false;
		}

		if ( ! $recheck && true === $this->get_account_is_ready() ) {
			return true;
		}

		$seller_status           = tribe( WhoDat::class )->get_seller_status( $saved_merchant_id );
		$payments_receivable     = tribe_is_truthy( Arr::get( $seller_status, 'payments_receivable' ) );
		$primary_email_confirmed = tribe_is_truthy( Arr::get( $seller_status, 'primary_email_confirmed' ) );

		$is_active = ( $payments_receivable && $primary_email_confirmed );

		if ( $is_active && $this->get_supports_custom_payments() ) {
			// Grab the PPCP_CUSTOM product from the status data
			$custom_product         = current(
				array_filter(
					$seller_status['products'],
					static function ( $product ) {
						return 'PPCP_CUSTOM' === $product['name'];
					}
				)
			);
			$active_custom_payments = 'SUBSCRIBED' === Arr::get( $custom_product, 'vetting_status' );

			// For custom payments we save here.
			$this->set_active_custom_payments( $active_custom_payments );
			$this->save();
		}

		if ( $is_active ) {
			$this->set_account_is_ready( true );
			$this->save();
		}

		return $is_active;
	}

	/**
	 * Fetches the locale for the website, but pass it on a filter to allow changing of the locale here.
	 *
	 * @since 5.2.0
	 *
	 * @return string
	 */
	public function get_locale() {
		$locale = get_locale();

		/**
		 * Allows filtering of the locale for the Merchant.
		 *
		 * @since 5.2.0
		 *
		 * @param string $locale
		 */
		return apply_filters( 'tec_tickets_commerce_gateway_paypal_merchant_locale', $locale );
	}
}
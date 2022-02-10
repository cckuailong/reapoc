<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal\REST;

use TEC\Tickets\Commerce\Gateways\PayPal\Client;
use TEC\Tickets\Commerce\Gateways\PayPal\Merchant;
use TEC\Tickets\Commerce\Gateways\PayPal\Refresh_Token;

use TEC\Tickets\Commerce\Gateways\PayPal\Signup;
use TEC\Tickets\Commerce\Gateways\PayPal\Webhooks;
use TEC\Tickets\Commerce\Gateways\PayPal\WhoDat;
use TEC\Tickets\Commerce\Notice_Handler;
use Tribe__Documentation__Swagger__Provider_Interface;
use Tribe__Settings;
use Tribe__Utils__Array as Arr;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;


/**
 * Class On_Boarding_Endpoint
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal\REST
 */
class On_Boarding_Endpoint implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * The REST API endpoint path.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $path = '/commerce/paypal/on-boarding';

	/**
	 * Register the actual endpoint on WP Rest API.
	 *
	 * @since 5.1.9
	 */
	public function register() {
		$namespace     = tribe( 'tickets.rest-v1.main' )->get_events_route_namespace();
		$documentation = tribe( 'tickets.rest-v1.endpoints.documentation' );

		register_rest_route(
			$namespace,
			$this->get_endpoint_path(),
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'args'                => $this->fetch_token_args(),
				'callback'            => [ $this, 'handle_fetch_token' ],
				'permission_callback' => '__return_true',
			]
		);

		register_rest_route(
			$namespace,
			$this->get_endpoint_path(),
			[
				'methods'             => WP_REST_Server::READABLE,
				'args'                => $this->signup_redirect_args(),
				'callback'            => [ $this, 'handle_signup_redirect' ],
				'permission_callback' => '__return_true',
			]
		);

		$documentation->register_documentation_provider( $this->get_endpoint_path(), $this );
	}

	/**
	 * Gets the Endpoint path for the on boarding process.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_endpoint_path() {
		return $this->path;
	}

	/**
	 * Get the REST API route URL.
	 *
	 * @since 5.1.9
	 *
	 * @return string The REST API route URL.
	 */
	public function get_route_url() {
		$namespace = tribe( 'tickets.rest-v1.main' )->get_events_route_namespace();

		return rest_url( '/' . $namespace . $this->get_endpoint_path(), 'https' );
	}

	/**
	 * Gets the Return URL pointing to this on boarding route.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_return_url( $hash = null ) {
		$arguments = [
			'hash' => $hash,
		];

		return add_query_arg( $arguments, $this->get_route_url() );
	}

	/**
	 * Handles the request that happens in parallel to the User Signup on PayPal but before we redirect the user from
	 * the mini browser. So when passing error messages, they need to be registered to be fetched in the FE.
	 *
	 * @since 5.1.9
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response An array containing the data on success or a WP_Error instance on failure.
	 */
	public function handle_fetch_token( WP_REST_Request $request ) {
		$response = [
			'success' => false,
		];

		$signup   = tribe( Signup::class );
		$client   = tribe( Client::class );
		$merchant = tribe( Merchant::class );

		// Send a request to fetch the access token.
		$paypal_response = $client->get_access_token_from_authorization_code(
			$request->get_param( 'shared_id' ),
			$request->get_param( 'auth_code' ),
			$signup->get_transient_hash()
		);

		if ( ! $paypal_response || array_key_exists( 'error', $paypal_response ) ) {
			$response['error'] = __( 'Unexpected response from PayPal when on boarding', 'event-tickets' );

			return new WP_REST_Response( $response );
		}

		// Save the information on the merchant system.
		$merchant->save_access_token_data( $paypal_response );

		tribe( Refresh_Token::class )->register_cron_job_to_refresh_token( $paypal_response['expires_in'] );

		$response['success'] = true;

		return new WP_REST_Response( $response );
	}

	/**
	 * This request is ran when the user is redirected back from the PayPal miniBrowser, and will not respond with
	 * a JSON request, but with a redirect of the user with a success link or error link into the payments tab.
	 *
	 * @since 5.1.9
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return void This is strictly a redirect response.
	 */
	public function handle_signup_redirect( WP_REST_Request $request ) {
		$signup        = tribe( Signup::class );
		$existing_hash = $signup->get_transient_hash();
		$request_hash  = $request->get_param( 'hash' );
		$return_url    = Tribe__Settings::instance()->get_url( [ 'tab' => 'payments' ] );

		if ( $request_hash !== $existing_hash ) {
			$this->redirect_with( 'invalid-paypal-signup-hash', $return_url );
		}

		$seller_data              = tribe( WhoDat::class )->get_seller_referral_data( $signup->get_referral_data_link() );
		$supports_custom_payments = in_array( 'PPCP', Arr::get( $seller_data, [ 'referral_data', 'products' ], [] ), true );

		$merchant_id           = $request->get_param( 'merchantId' );
		$merchant_id_in_paypal = $request->get_param( 'merchantIdInPayPal' );

		/**
		 * @todo Need to figure out where this gets saved in the merchant API.
		 */
		$permissions_granted = $request->get_param( 'permissionsGranted' );
		$consent_status      = $request->get_param( 'consentStatus' );
		$account_status      = $request->get_param( 'accountStatus' );

		$merchant = tribe( Merchant::class );

		$merchant->save_signup_data( $seller_data );

		$merchant->set_signup_hash( $request_hash );
		$merchant->set_merchant_id( $merchant_id );
		$merchant->set_merchant_id_in_paypal( $merchant_id_in_paypal );

		$access_token = $merchant->get_access_token();
		$credentials  = tribe( WhoDat::class )->get_seller_credentials( $access_token );

		if ( ! isset( $credentials['client_id'], $credentials['client_secret'] ) ) {
			// Save what we have before moving forward.
			$merchant->save();

			$this->redirect_with( 'invalid-paypal-seller-credentials', $return_url );
		}

		$merchant->set_client_id( $credentials['client_id'] );
		$merchant->set_client_secret( $credentials['client_secret'] );

		$merchant->set_supports_custom_payments( $supports_custom_payments );

		$merchant->set_account_is_connected( true );
		$merchant->set_account_is_ready( false );

		$merchant->save();

		$client = tribe( Client::class );

		// Pull Access token data.
		$token_data = $client->get_access_token_from_client_credentials( $credentials['client_id'], $credentials['client_secret'] );
		$merchant->save_access_token_data( $token_data );

		// Pull user info from PayPal.
		$user_info = $client->get_user_info();
		$merchant->save_user_info( $user_info );

		// Configures the Webhooks when setting up the new merchant.
		tribe( Webhooks::class )->create_or_update_existing();

		// Force the recheck of if the merchant is active.
		// This will also check if the custom payments are active.
		$merchant->is_active( true );

		/**
		 * @todo Need to figure out where this gets saved in the merchant API.
		 */
		update_option( 'tickets_commerce_permissions_granted', $permissions_granted );
		update_option( 'tickets_commerce_consent_status', $consent_status );
		update_option( 'tickets_commerce_account_status', $account_status );

		tribe( Notice_Handler::class )->trigger_admin( 'tc-paypal-signup-complete' );

		$this->redirect_with( 'paypal-signup-complete', $return_url );
	}

	/**
	 * Using wp_safe_redirect sends the client back to a given URL after removing the Signup data acquired.
	 *
	 * @since 5.1.9
	 *
	 * @param string $status Which status we will add to the URL
	 * @param string $url    Which URL we are sending the client to.
	 * @param array  $data   Extra that that will be json encoded to the URL.
	 *
	 */
	protected function redirect_with( $status, $url, array $data = [] ) {
		$signup = tribe( Signup::class );

		// We always clean signup data before redirect.
		$signup->delete_transient_data();
		$signup->delete_transient_hash();

		$query_args = [ 'tc-status' => $status ];

		if ( ! empty( $data ) ) {
			$query_args['tc-data'] = wp_json_encode( $data );
		}

		// Add an status slug to the URL.
		$url = add_query_arg( $query_args, $url );

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Arguments used for the signup redirect.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function signup_redirect_args() {
		// Webhooks do not send any arguments, only JSON content.
		return [
			'hash'               => [
				'description'       => 'The nonce validation',
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The wp_nonce argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'merchantId'         => [
				'description'       => 'The merchant ID',
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The merchantId argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'merchantIdInPayPal' => [
				'description'       => 'The merchant ID in PayPal',
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The merchantIdInPayPal argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'permissionsGranted' => [
				'description'       => 'The merchant ID in PayPal',
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The permissionsGranted argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'consentStatus'      => [
				'description'       => 'The merchant ID in PayPal',
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The consentStatus argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'accountStatus'      => [
				'description'       => 'The merchant ID in PayPal',
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The accountStatus argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
		];
	}

	/**
	 * Arguments used for the fetching of the token request.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function fetch_token_args() {
		// Webhooks do not send any arguments, only JSON content.
		return [
			'nonce'     => [
				'description'       => 'The nonce validation for WP',
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The wp_nonce argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'auth_code' => [
				'description'       => 'Authorization Code from PayPal',
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The merchantId auth_code must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'shared_id' => [
				'description'       => 'The shared ID from PayPal',
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The shared_id argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
		];
	}

	/**
	 * Sanitize a request argument based on details registered to the route.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed $value Value of the 'filter' argument.
	 *
	 * @return string|array
	 */
	public function sanitize_callback( $value ) {
		if ( is_array( $value ) ) {
			return array_map( 'sanitize_text_field', $value );
		}

		return sanitize_text_field( $value );
	}

	/**
	 * {@inheritDoc}
	 *
	 * @TODO  We need to make sure Swagger documentation is present.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function get_documentation() {
		return [];
	}
}

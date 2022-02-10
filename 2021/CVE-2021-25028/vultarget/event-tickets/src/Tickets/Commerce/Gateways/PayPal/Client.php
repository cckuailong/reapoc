<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal;

use TEC\Tickets\Commerce\Gateways\PayPal\REST\Webhook_Endpoint;
use TEC\Tickets\Commerce\Gateways\PayPal\Webhooks\Events;
use Tribe__Utils__Array as Arr;

/**
 * Class Client
 *
 * @since   5.1.6
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 *
 */
class Client {
	/**
	 * Debug ID from PayPal.
	 *
	 * @since 5.2.0
	 *
	 * @var string
	 */
	protected $debug_header;

	/**
	 * Get environment base URL.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_environment_url() {
		$merchant = tribe( Merchant::class );

		return $merchant->is_sandbox() ?
			'https://api.sandbox.paypal.com' :
			'https://api.paypal.com';
	}

	/**
	 * Safely checks if we have an access token to be used.
	 *
	 * @since 5.1.9
	 *
	 * @return string
	 */
	public function get_access_token() {
		return tribe( Merchant::class )->get_access_token();
	}

	/**
	 * Get REST API endpoint URL for requests.
	 *
	 * @since 5.1.9
	 *
	 *
	 * @param string $endpoint   The endpoint path.
	 * @param array  $query_args Query args appended to the URL.
	 *
	 * @return string The API URL.
	 *
	 */
	public function get_api_url( $endpoint, array $query_args = [] ) {
		$base_url = $this->get_environment_url();
		$endpoint = ltrim( $endpoint, '/' );

		return add_query_arg( $query_args, "{$base_url}/{$endpoint}" );
	}

	/**
	 * Fetches the JS SDK url.
	 *
	 * We use something like: https://www.paypal.com/sdk/js?client-id=sb&locale=en_US&components=buttons
	 *
	 * @link  https://developer.paypal.com/docs/checkout/reference/customize-sdk/#query-parameters
	 *
	 * @since 5.1.9
	 *
	 * @param array $query_args Which query args will be added.
	 *
	 * @return string
	 */
	public function get_js_sdk_url( array $query_args = [] ) {
		$url        = 'https://www.paypal.com/sdk/js';
		$merchant   = tribe( Merchant::class );
		$query_args = array_merge( [
			'client-id'       => $merchant->get_client_id(),
			'merchant-id'     => $merchant->get_merchant_id_in_paypal(),
			'components'      => 'hosted-fields,buttons',
			'intent'          => 'capture',
			'locale'          => $merchant->get_locale(),
			'disable-funding' => 'credit',
			'currency'        => tribe_get_option( \TEC\Tickets\Commerce\Settings::$option_currency_code, 'USD' ),
		], $query_args );
		$url        = add_query_arg( $query_args, $url );

		/**
		 * Filter the PayPal JS SDK url.
		 *
		 * @since 5.1.9
		 *
		 * @param string $url        Which URL we are going to use to load the SDK JS.
		 * @param array  $query_args Which URL args will be added to the JS SDK url.
		 */
		return apply_filters( 'tec_tickets_commerce_gateway_paypal_js_sdk_url', $url, $query_args );
	}

	/**
	 * Get PayPal homepage url.
	 *
	 * @since 5.1.6
	 *
	 * @return string
	 */
	public function get_home_page_url() {
		$subdomain = tribe( Merchant::class )->is_sandbox() ? 'sandbox.' : '';

		return sprintf(
			'https://%1$spaypal.com/',
			$subdomain
		);
	}

	/**
	 * Stores the debug header from a given PayPal request, which allows for us to store it with the gateway payload.
	 *
	 * @since 5.2.0
	 *
	 * @param string $id Which ID we are storing.
	 *
	 */
	protected function set_debug_header( $id ) {
		$this->debug_header = $id;
	}

	/**
	 * Fetches the last stored debug id from PayPal.
	 *
	 * @since 5.2.0
	 *
	 * @return string|null
	 */
	public function get_debug_header() {
		return $this->debug_header;
	}

	/**
	 * Send a given method request to a given URL in the PayPal API.
	 *
	 * @since 5.1.10
	 * @since 5.2.0 Included $retries param.
	 *
	 * @param string $method
	 * @param string $url
	 * @param array  $query_args
	 * @param array  $request_arguments
	 * @param bool   $raw
	 * @param int    $retries Param used to determine the amount of time this particular request was retried.
	 *
	 * @return array|\WP_Error
	 */
	public function request( $method, $url, array $query_args = [], array $request_arguments = [], $raw = false, $retries = 0 ) {
		$method = strtoupper( $method );

		// If the endpoint passed is a full URL don't try to append anything.
		$url = 0 !== strpos( $url, 'https://' )
			? $this->get_api_url( $url, $query_args )
			: add_query_arg( $query_args, $url );

		$default_arguments = [
			'headers' => [
				'Accept'        => 'application/json',
				'Authorization' => sprintf( 'Bearer %1$s', $this->get_access_token() ),
				'Content-Type'  => 'application/json',
			]
		];

		// By default it's important that we have a body set for any method that is not the GET method.
		if ( 'GET' !== $method ) {
			$default_arguments['body'] = [];
		}

		foreach ( $default_arguments as $key => $default_argument ) {
			$request_arguments[ $key ] = array_merge( $default_argument, Arr::get( $request_arguments, $key, [] ) );
		}

		if ( 'GET' !== $method ) {
			$content_type = Arr::get( $request_arguments, [ 'headers', 'Content-Type' ] );
			if ( empty( $content_type ) ) {
				$content_type = Arr::get( $request_arguments, [ 'headers', 'content-type' ] );
			}

			// For all other methods we try to make the body into the correct type.
			if (
				! empty( $request_arguments['body'] )
				&& 'application/json' === strtolower( $content_type )
			) {
				$request_arguments['body'] = wp_json_encode( $request_arguments[ $key ] );
			}
		}

		if ( 'GET' === $method ) {
			$response = wp_remote_get( $url, $request_arguments );
		} elseif ( 'POST' === $method ) {
			$response = wp_remote_post( $url, $request_arguments );
		} else {
			$request_arguments['method'] = $method;
			$response                    = wp_remote_request( $url, $request_arguments );
		}

		if ( is_wp_error( $response ) ) {
			tribe( 'logger' )->log_error( sprintf(
				'[%s] PayPal "%s" request error: %s',
				$method,
				$url,
				$response->get_error_message()
			), 'tickets-commerce-paypal' );

			return $response;
		}

		// When raw is true means we dont do any logic.
		if ( true === $raw ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );

		// If the debug header was set we pass it or reset it.
		$this->set_debug_header( null );
		if ( ! empty( $response['headers']['Paypal-Debug-Id'] ) ) {
			$this->set_debug_header( $response['headers']['Paypal-Debug-Id'] );
		}

		// When we get specifically a 401 and we are not trying to generate a token we try once more.
		if (
			401 === $response_code
			&& 2 >= $retries
			&& false === strpos( $url, 'v1/oauth2/token' )
		) {
			$merchant   = tribe( Merchant::class );
			$token_data = $this->get_access_token_from_client_credentials( $merchant->get_client_id(), $merchant->get_client_secret() );
			$saved      = $merchant->save_access_token_data( $token_data );

			// If we properly saved, just re-try the request.
			if ( $saved ) {
				$arguments = func_get_args();
				array_pop( $arguments );
				$arguments[] = $retries + 1;

				return call_user_func_array( [ $this, 'request' ], $arguments );
			}
		}

		/**
		 * @todo we need to log and be more verbose about the responses. Specially around failed JSON strings.
		 */
		$response_body = wp_remote_retrieve_body( $response );
		$response_body = @json_decode( $response_body, true );
		if ( empty( $response_body ) ) {
			return $response;
		}

		if ( ! is_array( $response_body ) ) {
			tribe( 'logger' )->log_error( sprintf( '[%s] Unexpected PayPal %s response', $url, $method ), 'tickets-commerce-paypal' );

			return new \WP_Error( 'tec-tickets-commerce-gateway-paypal-client-unexpected', null, [
				'method'            => $method,
				'url'               => $url,
				'query_args'        => $query_args,
				'request_arguments' => $request_arguments,
				'response'          => $response,
			] );
		}

		return $response_body;
	}

	/**
	 * Send a GET request to the PayPal API.
	 *
	 * @since 5.1.9
	 *
	 * @param string $endpoint
	 * @param array  $query_args
	 * @param array  $request_arguments
	 * @param bool   $raw
	 *
	 * @return array|null
	 */
	public function get( $endpoint, array $query_args = [], array $request_arguments = [], $raw = false ) {
		return $this->request( 'GET', $endpoint, $query_args, $request_arguments, $raw );
	}

	/**
	 * Send a POST request to the PayPal API.
	 *
	 * @since 5.1.9
	 *
	 * @param string $endpoint
	 * @param array  $query_args
	 * @param array  $request_arguments
	 * @param bool   $raw
	 *
	 * @return array|null
	 */
	public function post( $endpoint, array $query_args = [], array $request_arguments = [], $raw = false ) {
		return $this->request( 'POST', $endpoint, $query_args, $request_arguments, $raw );
	}

	/**
	 * Send a PATCH request to the PayPal API.
	 *
	 * @since 5.1.10
	 *
	 * @param string $endpoint
	 * @param array  $query_args
	 * @param array  $request_arguments
	 * @param bool   $raw
	 *
	 * @return array|null
	 */
	public function patch( $endpoint, array $query_args = [], array $request_arguments = [], $raw = false ) {
		return $this->request( 'PATCH', $endpoint, $query_args, $request_arguments, $raw );
	}

	/**
	 * Send a DELETE request to the PayPal API.
	 *
	 * @since 5.1.10
	 *
	 * @param string $endpoint
	 * @param array  $query_args
	 * @param array  $request_arguments
	 * @param bool   $raw
	 *
	 * @return array|null
	 */
	public function delete( $endpoint, array $query_args = [], array $request_arguments = [], $raw = false ) {
		return $this->request( 'DELETE', $endpoint, $query_args, $request_arguments, $raw );
	}

	/**
	 * Retrieves an Access Token for the Client ID and Secret.
	 *
	 * @since 5.1.9
	 *
	 * @param string $client_id     The Client ID.
	 * @param string $client_secret The Client Secret.
	 *
	 * @return array|null The token details response or null if there was a problem.
	 */
	public function get_access_token_from_client_credentials( $client_id, $client_secret ) {
		$auth       = base64_encode( "$client_id:$client_secret" );
		$query_args = [];

		$args = [
			'headers' => [
				'Authorization' => sprintf( 'Basic %1$s', $auth ),
				'Content-Type'  => 'application/x-www-form-urlencoded',
			],
			'body'    => [
				'grant_type' => 'client_credentials',
			],
		];

		return $this->post( 'v1/oauth2/token', $query_args, $args );
	}

	/**
	 * Retrieves an Access Token from the authorization code.
	 *
	 * @since 5.1.9
	 *
	 * @param string $shared_id Shared ID for merchant.
	 * @param string $auth_code Authorization code from on boarding.
	 * @param string $nonce     Seller nonce from on boarding.
	 *
	 * @return array|null The token details response or null if there was a problem.
	 */
	public function get_access_token_from_authorization_code( $shared_id, $auth_code, $nonce ) {
		$auth       = base64_encode( $shared_id );
		$query_args = [];

		$args = [
			'headers' => [
				'Authorization' => sprintf( 'Basic %1$s', $auth ),
				'Content-Type'  => 'application/x-www-form-urlencoded',
			],
			'body'    => [
				'grant_type'    => 'authorization_code',
				'code'          => $auth_code,
				'code_verifier' => $nonce,
			],
		];

		return $this->post( 'v1/oauth2/token', $query_args, $args );
	}

	/**
	 * Retrieves a Client Token from the stored Access Token.
	 *
	 * @link  https://developer.paypal.com/docs/business/checkout/advanced-card-payments/
	 *
	 * @since 5.1.9
	 *
	 * @return array|null The client token details response or null if there was a problem.
	 */
	public function get_client_token() {
		$query_args = [];
		$args       = [
			'headers' => [],
			'body'    => [],
		];

		return $this->post( 'v1/identity/generate-token', $query_args, $args );
	}

	/**
	 * Based on a Purchase Unit creates a PayPal order.
	 *
	 * @link  https://developer.paypal.com/docs/api/orders/v2/#orders_create
	 * @link  https://developer.paypal.com/docs/api/orders/v2/#definition-purchase_unit_request
	 *
	 * @since 5.1.9
	 *
	 * @param array<string,mixed>|array<array> $units              {
	 *                                                             Purchase unit used to setup the order in PayPal.
	 *
	 * @type string                            $reference_id       Reference ID to PayPal.
	 * @type string                            $description        Description of this Purchase Unit.
	 * @type string                            $value              Value to be payed.
	 * @type string                            $currency           Which currency.
	 * @type string                            $merchant_id        Merchant ID.
	 * @type string                            $merchant_paypal_id PayPal Merchant ID.
	 * @type string                            $first_name         Payee First Name.
	 * @type string                            $last_name          Payee Last Name.
	 * @type string                            $email              Payee email.
	 * @type string                            $disbursement_mode  (optional) By default 'INSTANT'.
	 * @type string                            $payer_id           (optional) PayPal Payer ID
	 * @type string                            $tax_id             (optional) Tax ID for this purchase Unit.
	 * @type string                            $tax_id_type        (optional) Tax ID for this purchase Unit.
	 *
	 *                     }
	 * @return array|null
	 */
	public function create_order( array $units = [] ) {
		$merchant   = tribe( Merchant::class );
		$query_args = [];
		$body       = [
			'intent'              => 'CAPTURE',
			'purchase_units'      => [],
			'application_context' => [
				'shipping_preference' => 'NO_SHIPPING',
				'user_action'         => 'PAY_NOW',
			],
		];

		// Determine if this set of units was just a single unit before looping.
		if ( ! empty( $units['reference_id'] ) ) {
			$units = [ $units ];
		}

		foreach ( $units as $unit ) {
			/**
			 * @link https://developer.paypal.com/docs/api/orders/v2/#definition-payer
			 */
			$purchase_unit = [
				'reference_id'        => Arr::get( $unit, 'reference_id' ),
				'description'         => Arr::get( $unit, 'description' ),
				'amount'              => [
					'value'         => Arr::get( $unit, 'value' ),
					'currency_code' => Arr::get( $unit, 'currency' ),
				],
				'payee'               => [
					'merchant_id' => Arr::get( $unit, 'merchant_paypal_id', $merchant->get_merchant_id_in_paypal() ),
				],
				'payer'               => [
					'name'          => [
						'given_name' => Arr::get( $unit, 'first_name' ),
						'surname'    => Arr::get( $unit, 'last_name' ),
					],
					'email_address' => Arr::get( $unit, 'email' ),
				],
				'payment_instruction' => [
					'disbursement_mode' => Arr::get( $unit, 'disbursement_mode', 'INSTANT' ),
				],
			];

			$items = Arr::get( $unit, 'items' );
			if ( ! empty( $items ) ) {
				$purchase_unit['items']               = $items;
				$purchase_unit['amount']['breakdown'] = [
					'item_total' => [
						'value'         => Arr::get( $unit, 'value' ),
						'currency_code' => Arr::get( $unit, 'currency' ),
					],
				];
			}

			if ( ! empty( $unit['tax_id'] ) ) {
				$purchase_unit['payer']['tax_info']['tax_id'] = Arr::get( $unit, 'tax_id' );
			}

			if ( ! empty( $unit['tax_id_type'] ) ) {
				$purchase_unit['payer']['tax_info']['tax_id_type'] = Arr::get( $unit, 'tax_id_type' );
			}

			/**
			 * @todo We should have some sort of Purchase Unit validation here.
			 */

			$body['purchase_units'][] = $purchase_unit;
		}

		$args = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
				'Prefer'                        => 'return=representation',
			],
			'body'    => $body,
		];

		$response = $this->post( '/v2/checkout/orders', $query_args, $args );

		return $response;
	}

	/**
	 * Captures an order for a given ID in PayPal.
	 *
	 * @since 5.1.9
	 *
	 * @since 5.1.10 Added support for passing `payerID` param for PayPal API.
	 *
	 * @param string $order_id Order ID to capture.
	 * @param string $payer_id Payer ID for given order from PayPal.
	 *
	 * @return array|null
	 */
	public function capture_order( $order_id, $payer_id = '' ) {
		$query_args = [];
		$body       = [];

		if ( ! empty( $payer_id ) ) {
			$body['payerID'] = $payer_id;
		}

		/**
		 * If we need to handle failures.
		 *
		 * @link https://developer.paypal.com/docs/platforms/checkout/add-capabilities/handle-funding-failures/
		 * 'PayPal-Mock-Response'          => '{"mock_application_codes" : "INSTRUMENT_DECLINED"}',
		 */
		$args = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
				'Prefer'                        => 'return=representation',
			],
			'body'    => $body,
		];

		$capture_id = urlencode( $order_id );
		$url        = '/v2/checkout/orders/{order_id}/capture';
		$url        = str_replace( '{order_id}', $capture_id, $url );
		$response   = $this->post( $url, $query_args, $args );

		return $response;
	}

	/**
	 * Gets the profile information from the customer in PayPal.
	 *
	 * @link  https://developer.paypal.com/docs/api/identity/v1/#userinfo_get
	 *
	 * @since 5.1.9
	 *
	 * @return array|null
	 */
	public function get_user_info() {
		$query_args = [
			'schema' => 'paypalv1.1',
		];
		$body       = [];
		$args       = [];

		$url      = '/v1/identity/oauth2/userinfo';
		$response = $this->get( $url, $query_args, $args );

		return $response;
	}

	public function refund_payment( $capture_id ) {
		$query_args = [];
		$body       = [];
		$args       = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
				'Prefer'                        => 'return=representation',
			],
			'body'    => $body,
		];

		$capture_id = urlencode( $capture_id );
		$url        = '/v2/payments/captures/{capture_id}/refund';
		$url        = str_replace( '{capture_id}', $capture_id, $url );
		$response   = $this->post( $url, $query_args, $args );

		return $response;
	}

	/**
	 * This uses the links property of the payment to retrieve the Parent Payment ID from PayPal.
	 *
	 * @since 5.1.10
	 *
	 * @param string $payment The payment event object.
	 *
	 * @return string|false The parent payment ID or false if not found.
	 */
	public function get_payment_authorization( $payment ) {
		$query_args = [];
		$args       = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
				'Prefer'                        => 'return=representation',
			],
		];

		if ( ! wp_http_validate_url( $payment ) ) {
			$payment = urlencode( $payment );
			$url     = '/v2/payments/authorizations/{payment_id}';
			$url     = str_replace( '{payment_id}', $payment, $url );
		} else {
			$url = $payment;
		}

		$response = $this->get( $url, $query_args, $args );

		return $response;
	}

	/**
	 * Verify the identity of the Webhook request, to avoid any security problems.
	 *
	 * @link  https://developer.paypal.com/docs/api/webhooks/v1/#verify-webhook-signature_post
	 *
	 * @since 5.1.10
	 *
	 * @param string $webhook_id Which webhook id we have currently stored on the database.
	 * @param array  $event      The Event received by the endpoint from PayPal.
	 * @param array  $headers    Headers from the PayPal request that we use to verify the signature.
	 *
	 * @return bool
	 */
	public function verify_webhook_signature( $webhook_id, $event, $headers ) {
		$query_args = [];
		$body       = [
			'transmission_id'   => Arr::get( $headers, 'transmission_id' ),
			'transmission_time' => Arr::get( $headers, 'transmission_time' ),
			'transmission_sig'  => Arr::get( $headers, 'transmission_sig' ),
			'cert_url'          => Arr::get( $headers, 'cert_url' ),
			'auth_algo'         => Arr::get( $headers, 'auth_algo' ),
			'webhook_id'        => $webhook_id,
			'webhook_event'     => $event,
		];
		$args       = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
			],
			'body'    => $body,
		];

		$url      = 'v1/notifications/verify-webhook-signature';
		$response = $this->post( $url, $query_args, $args );

		return 'SUCCESS' === Arr::get( $response, 'verification_status', false );
	}


	/**
	 * Get the list of webhooks.
	 *
	 * @see   https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_list
	 * @since 5.1.10
	 *
	 * @return array[] The list of PayPal webhooks.
	 */
	public function list_webhooks() {
		$query_args = [];
		$body       = [];
		$args       = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
				'Prefer'                        => 'return=representation',
			],
			'body'    => $body,
		];

		$url      = '/v1/notifications/webhooks';
		$response = $this->get( $url, $query_args, $args );

		if ( empty( $response['webhooks'] ) ) {
			return [];
		}

		return $response['webhooks'];
	}


	/**
	 * Get the webhook data from a specific webhook ID.
	 *
	 * @see   https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_get
	 * @since 5.1.10
	 *
	 * @param string $webhook_id The webhook ID.
	 *
	 * @return array|null The PayPal webhook data.
	 */
	public function get_webhook( $webhook_id ) {
		$query_args = [];
		$body       = [];
		$args       = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
			],
			'body'    => $body,
		];

		$webhook_id = urlencode( $webhook_id );
		$url        = '/v1/notifications/webhooks/{webhook_id}';
		$url        = str_replace( '{webhook_id}', $webhook_id, $url );
		$response   = $this->get( $url, $query_args, $args );

		if ( ! isset( $response['id'], $response['name'] ) ) {
			$error = @json_decode( $response['body'], true );

			if ( 'INVALID_RESOURCE_ID' === $error['name'] ) {
				// The webhook was not found.
				tribe( 'logger' )->log_warning( __( 'The PayPal webhook does not exist', 'event-tickets' ), 'tickets-commerce-gateway-paypal' );
			} else {
				// Other unexpected response.
				tribe( 'logger' )->log_warning( __( 'Unexpected PayPal response when getting webhook', 'event-tickets' ), 'tickets-commerce-gateway-paypal' );
			}

			return null;
		}

		return $response;
	}


	/**
	 * Creates a webhook with the given event types registered.
	 *
	 * @see   https://developer.paypal.com/docs/api/webhooks/v1/#webhooks_post
	 * @since 5.1.10
	 *
	 * @return array|\WP_Error
	 */
	public function create_webhook() {
		$events      = tribe( Events::class )->get_registered_events();
		$webhook_url = tribe( Webhook_Endpoint::class )->get_route_url();

		$query_args = [];
		$body       = [
			'url'         => $webhook_url,
			'event_types' => array_map(
				static function ( $event_type ) {
					return [
						'name' => $event_type,
					];
				},
				$events
			),
		];
		$args       = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
			],
			'body'    => $body,
		];

		$url      = '/v1/notifications/webhooks';
		$response = $this->post( $url, $query_args, $args );

		if ( ! $response || empty( $response['id'] ) ) {
			$error = @json_decode( $response['body'], true );
			if ( empty( $error['name'] ) ) {
				$message = __( 'Unexpected PayPal response when creating webhook', 'event-tickets' );
				tribe( 'logger' )->log_error( $message, 'tickets-commerce-gateway-paypal' );

				return new \WP_Error( 'tec-tickets-commerce-gateway-paypal-webhook-unexpected', $message, $response );
			}

			if ( 'WEBHOOK_URL_ALREADY_EXISTS' === $error['name'] ) {
				return new \WP_Error( 'tec-tickets-commerce-gateway-paypal-webhook-url-already-exists', $error['message'], $response );
			}

			if ( 'WEBHOOK_NUMBER_LIMIT_EXCEEDED' === $error['name'] ) {
				$message = __( 'PayPal webhook limit has been reached, you need to go into your developer.paypal.com account and remove webhooks from the associated account', 'event-tickets' );
				// Limit has been reached, we cannot just delete all webhooks without permission.
				tribe( 'logger' )->log_error( $message, 'tickets-commerce-gateway-paypal' );

				return new \WP_Error( 'tec-tickets-commerce-gateway-paypal-webhook-limit-exceeded', $message, $response );
			}
		}

		return $response;
	}

	/**
	 * Updates the webhook url and events
	 *
	 * @since 5.1.10
	 *
	 * @param string $webhook_id
	 *
	 * @return array|\WP_Error
	 */
	public function update_webhook( $webhook_id ) {
		$events      = tribe( Events::class )->get_registered_events();
		$webhook_url = tribe( Webhook_Endpoint::class )->get_route_url();

		$query_args = [];
		$body       = [
			[
				'op'    => 'replace',
				'path'  => '/url',
				'value' => $webhook_url,
			],
			[
				'op'    => 'replace',
				'path'  => '/event_types',
				'value' => array_map(
					static function ( $event_type ) {
						return [
							'name' => $event_type,
						];
					},
					$events
				),
			],
		];
		$args       = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
			],
			'body'    => $body,
		];

		$webhook_id = urlencode( $webhook_id );
		$url        = '/v1/notifications/webhooks/{webhook_id}';
		$url        = str_replace( '{webhook_id}', $webhook_id, $url );
		$response   = $this->patch( $url, $query_args, $args );

		if ( ! $response || empty( $response['id'] ) ) {
			$error = @json_decode( $response['body'], true );
			if ( empty( $error['name'] ) ) {
				$message = __( 'Unexpected PayPal response when updating webhook', 'event-tickets' );
				tribe( 'logger' )->log_error( $message, 'tickets-commerce-gateway-paypal' );

				return new \WP_Error( 'tec-tickets-commerce-gateway-paypal-webhook-update-unexpected', $message );
			}

			if ( 'INVALID_RESOURCE_ID' === $error['name'] ) {
				return new \WP_Error( 'tec-tickets-commerce-gateway-paypal-webhook-update-invalid-id', $error['message'] );
			}
		}

		return $response;
	}

	/**
	 * Deletes the webhook with the given id.
	 *
	 * @since 5.1.10
	 *
	 * @param string $webhook_id
	 *
	 * @return bool|\WP_Error Whether or not the deletion was successful
	 */
	public function delete_webhook( $webhook_id ) {
		$query_args = [];
		$args       = [
			'headers' => [
				'PayPal-Partner-Attribution-Id' => Gateway::ATTRIBUTION_ID,
			],
		];

		$webhook_id = urlencode( $webhook_id );
		$url        = '/v1/notifications/webhooks/{webhook_id}';
		$url        = str_replace( '{webhook_id}', $webhook_id, $url );
		$response   = $this->delete( $url, $query_args, $args, true );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return 204 === wp_remote_retrieve_response_code( $response );
	}
}

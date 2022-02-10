<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal\REST;

use TEC\Tickets\Commerce\Gateways\PayPal\Client;
use TEC\Tickets\Commerce\Gateways\PayPal\Merchant;
use TEC\Tickets\Commerce\Gateways\PayPal\REST;
use TEC\Tickets\Commerce\Gateways\PayPal\Webhooks;
use TEC\Tickets\Commerce\Gateways\PayPal\Webhooks\Headers;
use TEC\Tickets\Commerce\Gateways\PayPal\Webhooks\Listeners\Payment_Capture_Completed;
use TEC\Tickets\Commerce\Gateways\PayPal\Webhooks\Validation;
use Tribe__Documentation__Swagger__Provider_Interface;
use Tribe__REST__Endpoints__CREATE_Endpoint_Interface;
use Tribe__Tickets__REST__V1__Endpoints__Base;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Class Webhook Endpoint.
 *
 * @since   5.1.6
 * @package TEC\Tickets\Commerce\Gateways\PayPal\REST
 */
class Webhook_Endpoint implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * The REST API endpoint path.
	 *
	 * @since 5.1.6
	 *
	 * @var string
	 */
	protected $path = '/commerce/paypal/webhook';

	/**
	 * Register the actual endpoint on WP Rest API.
	 *
	 * @since 5.1.10
	 */
	public function register() {
		$namespace     = tribe( 'tickets.rest-v1.main' )->get_events_route_namespace();
		$documentation = tribe( 'tickets.rest-v1.endpoints.documentation' );

		register_rest_route(
			$namespace,
			$this->get_endpoint_path(),
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'args'                => $this->get_args(),
				'callback'            => [ $this, 'handle_request' ],
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
	 * This grabs the headers from the webhook request to be used in the signature verification
	 *
	 * A strange thing here is that the headers are inconsistent between live and sandbox mode, so this also checks for
	 * both forms of the headers (studly case and all caps).
	 *
	 * @since 5.1.10
	 *
	 * @param array $paypal_headers
	 *
	 * @return array|WP_Error
	 */
	public function parse_headers( array $paypal_headers ) {
		$header_keys = [
			'transmission_id'   => 'Paypal-Transmission-Id',
			'transmission_time' => 'Paypal-Transmission-Time',
			'transmission_sig'  => 'Paypal-Transmission-Sig',
			'cert_url'          => 'Paypal-Cert-Url',
			'auth_algo'         => 'Paypal-Auth-Algo',
		];

		$headers      = [];
		$missing_keys = [];
		foreach ( $header_keys as $property => $key ) {
			if ( ! isset( $paypal_headers[ $key ] ) ) {
				$key = str_replace( '-', '_', $key );
				$key = strtoupper( $key );

				if ( ! isset( $paypal_headers[ $key ] ) ) {
					$key = strtolower( $key );
				}
			}

			if ( isset( $paypal_headers[ $key ] ) ) {
				$headers[ $property ] = $paypal_headers[ $key ];
			} else {
				$missing_keys[] = $property;
			}
		}

		if ( ! empty( $missing_keys ) ) {
			return new WP_Error( 'tec-tickets-commerce-paypal-webhook-missing-headers', null, [ 'missing_keys' => $missing_keys ] );
		}

		return $headers;
	}

	/**
	 * Handle the Webhook requests coming from PayPal.
	 *
	 * @since 5.1.10
	 *
	 * @param WP_REST_Request $request   The request object.
	 *
	 * @return WP_Error|WP_REST_Response An array containing the data on success or a WP_Error instance on failure.
	 */
	public function handle_request( WP_REST_Request $request ) {
		if ( ! tribe( Merchant::class )->is_active() ) {
			return new WP_Error( 'tec-tickets-commerce-paypal-merchant-inactive' );
		}

		$event = $request->get_body_params();

		tribe( 'logger' )->log_debug(
			sprintf(
			// Translators: %s: The event type.
				__( 'Received PayPal webhook event for type: %s', 'event-tickets' ),
				$event['event_type']
			),
			'tickets-commerce-gateway-paypal'
		);

		// Check if the event type matches.
		if ( ! tribe( Webhooks\Events::class )->is_valid( $event['event_type'] ) ) {
			tribe( 'logger' )->log_debug(
				sprintf(
				// Translators: %s: The PayPal payment event.
					__( 'Invalid event type for webhook event: %s', 'event-tickets' ),
					json_encode( $event )
				),
				'tickets-commerce-gateway-paypal'
			);

			return new WP_Error( 'tec-tickets-commerce-paypal-webhook-invalid-type', null, $event );
		}

		$webhook_id = tribe( Webhooks::class )->get_setting( 'id' );
		$headers    = $this->parse_headers( $request->get_headers() );

		if ( ! tribe( Client::class )->verify_webhook_signature( $webhook_id, $event, $headers ) ) {
			tribe( 'logger' )->log_error( __( 'Failed PayPal webhook event verification', 'event-tickets' ), 'tickets-commerce-gateway-paypal' );

			return new WP_Error( 'tec-tickets-commerce-paypal-webhook-signature-error', null, [  'webhook_id' => $webhook_id, 'event' => $event, 'headers' => $headers ] );
		}

		$debug_header = $request->get_header( 'Paypal-Debug-Id' );
		if ( ! empty( $debug_header ) ) {
			$event['debug_id'] = $debug_header;
		}

		$order = tribe( Webhooks\Handler::class )->process_event( $event );

		if ( is_wp_error( $order ) ) {
			return $order;
		}

		$data = [
			'success' => true,
			'order'   => $order,
		];

		return new WP_REST_Response( $data );
	}

	/**
	 * Arguments used for the signup redirect.
	 *
	 * @since 5.1.10
	 *
	 * @return array
	 */
	public function get_args() {
		return [];
	}

	/**
	 * {@inheritDoc}
	 *
	 * @since 5.1.6
	 */
	public function get_documentation() {
		return [
			'post' => [
				'consumes'   => [
					'application/json',
				],
				'parameters' => [],
				'responses'  => [
					'200' => [
						'description' => __( 'Processes the Webhook as long as it includes valid Payment Event data', 'event-tickets' ),
						'content'     => [
							'application/json' => [
								'schema' => [
									'type'       => 'object',
									'properties' => [
										'success' => [
											'description' => __( 'Whether the processing was successful', 'event-tickets' ),
											'type'        => 'boolean',
										],
									],
								],
							],
						],
					],
					'403' => [
						'description' => __( 'The webhook was invalid and was not processed', 'event-tickets' ),
						'content'     => [
							'application/json' => [
								'schema' => [
									'type' => 'object',
								],
							],
						],
					],
				],
			],
		];
	}
}

<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal\REST;

use tad\WPBrowser\Adapters\WP;
use TEC\Tickets\Commerce\Cart;
use TEC\Tickets\Commerce\Gateways\PayPal\Gateway;
use TEC\Tickets\Commerce\Gateways\PayPal\Status;
use TEC\Tickets\Commerce\Order;

use TEC\Tickets\Commerce\Gateways\PayPal\Client;
use TEC\Tickets\Commerce\Gateways\PayPal\Merchant;
use TEC\Tickets\Commerce\Gateways\PayPal\Refresh_Token;

use TEC\Tickets\Commerce\Gateways\PayPal\Signup;
use TEC\Tickets\Commerce\Gateways\PayPal\WhoDat;


use TEC\Tickets\Commerce\Module;
use TEC\Tickets\Commerce\Status\Denied;
use TEC\Tickets\Commerce\Status\Pending;
use TEC\Tickets\Commerce\Status\Completed;
use TEC\Tickets\Commerce\Status\Created;
use TEC\Tickets\Commerce\Status\Status_Handler;
use TEC\Tickets\Commerce\Success;
use Tribe__Documentation__Swagger__Provider_Interface;
use Tribe__Settings;
use Tribe__Utils__Array as Arr;

use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;


/**
 * Class Order Endpoint.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal\REST
 */
class Order_Endpoint implements Tribe__Documentation__Swagger__Provider_Interface {

	/**
	 * The REST API endpoint path.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected $path = '/commerce/paypal/order';

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
				'args'                => $this->create_order_args(),
				'callback'            => [ $this, 'handle_create_order' ],
				'permission_callback' => '__return_true',
			]
		);

		register_rest_route(
			$namespace,
			$this->get_endpoint_path() . '/(?P<order_id>[0-9a-zA-Z]+)',
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'args'                => $this->update_order_args(),
				'callback'            => [ $this, 'handle_update_order' ],
				'permission_callback' => '__return_true',
			]
		);

		register_rest_route(
			$namespace,
			$this->get_endpoint_path() . '/(?P<order_id>[0-9a-zA-Z]+)',
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'args'                => $this->fail_order_args(),
				'callback'            => [ $this, 'handle_fail_order' ],
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
	 * Handles the request that creates an order with Tickets Commerce and the PayPal gateway.
	 *
	 * @since 5.1.9
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_Error|WP_REST_Response An array containing the data on success or a WP_Error instance on failure.
	 */
	public function handle_create_order( WP_REST_Request $request ) {
		$response = [
			'success' => false,
		];

		$messages = $this->get_error_messages();

		$order = tribe( Order::class )->create_from_cart( tribe( Gateway::class ) );

		$unit = [
			'reference_id' => $order->ID,
			'value'        => $order->total_value,
			'currency'     => $order->currency,
			'first_name'   => $order->purchaser['first_name'],
			'last_name'    => $order->purchaser['last_name'],
			'email'        => $order->purchaser['email'],
		];

		foreach ( $order->items as $item ) {
			$ticket          = \Tribe__Tickets__Tickets::load_ticket_object( $item['ticket_id'] );
			$unit['items'][] = [
				'name'        => $ticket->name,
				'unit_amount' => [ 'value' => $item['price'], 'currency_code' => $order->currency ],
				'quantity'    => $item['quantity'],
				'item_total'  => [ 'value' => $item['sub_total'], 'currency_code' => $order->currency ],
				'sku'         => $ticket->sku,
			];
		}

		$paypal_order = tribe( Client::class )->create_order( $unit );

		if ( empty( $paypal_order['id'] ) || empty( $paypal_order['create_time'] ) ) {
			return new WP_Error( 'tec-tc-gateway-paypal-failed-creating-order', $messages['failed-creating-order'], $order );
		}

		$debug_header = tribe( Client::class )->get_debug_header();
		if ( ! empty( $debug_header ) ) {
			$paypal_order['debug_id'] = $debug_header;
		}

		$updated = tribe( Order::class )->modify_status( $order->ID, Pending::SLUG, [
			'gateway_payload'  => $paypal_order,
			'gateway_order_id' => $paypal_order['id'],
		] );

		if ( is_wp_error( $updated ) ) {
			return $updated;
		}

		// Respond with the ID for Paypal Usage.
		$response['success'] = true;
		$response['id']      = $paypal_order['id'];

		return new WP_REST_Response( $response );
	}

	/**
	 * Handles the request that updates an order with Tickets Commerce and the PayPal gateway.
	 *
	 * @since 5.1.9
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_Error|WP_REST_Response An array containing the data on success or a WP_Error instance on failure.
	 */
	public function handle_update_order( WP_REST_Request $request ) {
		$response = [
			'success' => false,
		];

		$messages = $this->get_error_messages();

		$paypal_order_id = $request->get_param( 'order_id' );

		$order = tec_tc_orders()->by_args( [
			'status'           => tribe( Pending::class )->get_wp_slug(),
			'gateway_order_id' => $paypal_order_id,
		] )->first();

		if ( ! $order ) {
			return new WP_Error( 'tec-tc-gateway-paypal-nonexistent-order-id', $messages['nonexistent-order-id'], $order );
		}

		$payer_id = $request->get_param( 'payer_id' );

		$paypal_capture_response = tribe( Client::class )->capture_order( $paypal_order_id, $payer_id );

		$debug_header = tribe( Client::class )->get_debug_header();
		if ( ! empty( $debug_header ) ) {
			$paypal_capture_response['debug_id'] = $debug_header;
		}

		if (
			'UNPROCESSABLE_ENTITY' === Arr::get( $paypal_capture_response, 'name' )
		) {
			// Flag the order as Denied.
			tribe( Order::class )->modify_status( $order->ID, Denied::SLUG, [
				'gateway_payload' => $paypal_capture_response,
			] );

			return new WP_Error( 'tec-tc-gateway-paypal-failed-capture', $messages['failed-capture'], $paypal_capture_response );
		}

		$paypal_capture_status = Arr::get( $paypal_capture_response, [ 'status' ] );
		$status                = tribe( Status::class )->convert_to_commerce_status( $paypal_capture_status );

		if ( ! $status ) {
			return new WP_Error( 'tec-tc-gateway-paypal-invalid-capture-status', $messages['invalid-capture-status'], $paypal_capture_response );
		}

		$updated = tribe( Order::class )->modify_status( $order->ID, $status->get_slug(), [
			'gateway_payload' => $paypal_capture_response,
		] );

		if ( is_wp_error( $updated ) ) {
			return $updated;
		}

		$response['success']  = true;
		$response['status']   = $status->get_slug();
		$response['order_id'] = $order->ID;

		// When we have success we clear the cart.
		tribe( Cart::class )->clear_cart();

		$response['redirect_url'] = add_query_arg( [ 'tc-order-id' => $paypal_order_id ], tribe( Success::class )->get_url() );

		return new WP_REST_Response( $response );
	}

	/**
	 * Handles the request that handles failing an order with Tickets Commerce and the PayPal gateway.
	 *
	 * @since 5.2.0
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_Error|WP_REST_Response An array containing the data on success or a WP_Error instance on failure.
	 */
	public function handle_fail_order( WP_REST_Request $request ) {
		$response = [
			'success' => false,
		];

		$paypal_order_id = $request->get_param( 'order_id' );
		$order           = tec_tc_orders()->by_args( [
			'status'           => 'any',
			'gateway_order_id' => $paypal_order_id,
		] )->first();

		$messages = $this->get_error_messages();

		if ( ! $order ) {
			return new WP_Error( 'tec-tc-gateway-paypal-nonexistent-order-id', null, $order );
		}

		$failed_reason = $request->get_param( 'failed_reason' );
		$failed_status = $request->get_param( 'failed_status' );
		if ( empty( $failed_status ) ) {
			$failed_status = 'not-completed';
		}

		$status = tribe( Status_Handler::class )->get_by_slug( $failed_status );

		if ( ! $status ) {
			return new WP_Error( 'tec-tc-gateway-paypal-invalid-failed-status', null, [
				'failed_status' => $failed_status,
				'failed_reason' => $failed_reason
			] );
		}

		/**
		 * @todo possible determine if we should have error code associated with the failing of this order.
		 */
		$updated = tribe( Order::class )->modify_status( $order->ID, $status->get_slug() );

		if ( is_wp_error( $updated ) ) {
			return $updated;
		}

		$response['success']  = true;
		$response['status']   = $status->get_slug();
		$response['order_id'] = $order->ID;
		$response['title']    = $messages['canceled-creating-order'];

		return new WP_REST_Response( $response );
	}

	/**
	 * Arguments used for the signup redirect.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function create_order_args() {
		return [];
	}

	/**
	 * Arguments used for the updating order for PayPal.
	 *
	 * @since 5.1.9
	 *
	 * @return array
	 */
	public function update_order_args() {
		return [
			'order_id' => [
				'description'       => __( 'Order ID in PayPal', 'event-tickets' ),
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The order ID argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'payer_id' => [
				'description'       => __( 'Payer ID token from PayPal', 'event-tickets' ),
				'required'          => false,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The payer ID argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
		];
	}

	/**
	 * Arguments used for the deleting order for PayPal.
	 *
	 * @since 5.2.0
	 *
	 * @return array
	 */
	public function fail_order_args() {
		return [
			'order_id'      => [
				'description'       => __( 'Order ID in PayPal', 'event-tickets' ),
				'required'          => true,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The order ID argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'failed_status' => [
				'description'       => __( 'To which status the failing should change this order to', 'event-tickets' ),
				'required'          => false,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The failed status argument must be a string.', [ 'status' => 400 ] );
					}

					return $value;
				},
				'sanitize_callback' => [ $this, 'sanitize_callback' ],
			],
			'failed_reason' => [
				'description'       => __( 'Why this particular order has failed.', 'event-tickets' ),
				'required'          => false,
				'type'              => 'string',
				'validate_callback' => static function ( $value ) {
					if ( ! is_string( $value ) ) {
						return new WP_Error( 'rest_invalid_param', 'The failed reason argument must be a string.', [ 'status' => 400 ] );
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

	/**
	 * Returns an array of error messages that are used by the API responses.
	 *
	 * @since 5.2.0
	 *
	 * @return array $messages Array of error messages.
	 */
	public function get_error_messages() {
		$messages = [
			'failed-creating-order'   => __( 'Creating new PayPal order failed. Please try again.', 'event-tickets' ),
			'canceled-creating-order' => __( 'Your PayPal order was cancelled.', 'event-tickets' ),
			'nonexistent-order-id'    => __( 'Provided Order id is not valid.', 'event-tickets' ),
			'failed-capture'          => __( 'There was a problem while processing your payment, please try again.', 'event-tickets' ),
			'invalid-capture-status'  => __( 'There was a problem with the Order status change, please try again.', 'event-tickets' ),
		];

		/**
		 * Filter the error messages for PayPal checkout.
		 *
		 * @since 5.2.0
		 *
		 * @param array $messages Array of error messages.
		 */
		return apply_filters( 'tec_tickets_commerce_order_endpoint_error_messages', $messages );
	}
}

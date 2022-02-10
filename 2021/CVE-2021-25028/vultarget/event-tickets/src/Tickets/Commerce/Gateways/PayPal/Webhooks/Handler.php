<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal\Webhooks;

use TEC\Tickets\Commerce\Gateways\PayPal\Client;
use TEC\Tickets\Commerce\Order;
use WP_Error;

/**
 * Class Handler
 *
 * @since   5.1.10
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal\Webhooks
 */
class Handler {

	/**
	 * Gets the parent payment link from the list of Links on the response.
	 *
	 * @since 5.1.10
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	protected function get_parent_payment_link( $links ) {
		return current( array_filter( $links, static function ( $link ) {
			return 'parent_payment' === $link['rel'];
		} ) );
	}

	/**
	 * Process a given PayPal Webhook event, possibly updating the local order with the status sent by the request.
	 *
	 * @since 5.1.10
	 *
	 * @param array $event The PayPal payment event object.
	 *
	 * @return \WP_Post|WP_Error Whether the event was processed successfully.
	 */
	public function process_event( $event ) {
		// Invalid event.
		if ( empty( $event['event_type'] ) || empty( $event['resource'] ) ) {
			return new WP_Error( 'tec-tickets-commerce-paypal-webhook-invalid-payload', null, [ 'event' => $event ] );
		}

		// Check if the event type matches.
		if ( ! tribe( Events::class )->is_valid( $event['event_type'] ) ) {
			tribe( 'logger' )->log_debug(
				sprintf(
				// Translators: %s: The PayPal payment event.
					__( 'Invalid event type for webhook event: %s', 'event-tickets' ),
					json_encode( $event )
				),
				'tickets-commerce-gateway-paypal'
			);

			return new WP_Error( 'tec-tickets-commerce-paypal-webhook-invalid-type', null, [ 'event' => $event ] );
		}

		$new_status = tribe( Events::class )->convert_to_commerce_status( $event['event_type'] );

		$link = $this->get_parent_payment_link( $event['resource']['links'] );

		$parent_payment = tribe( Client::class )->request( $link['method'], $link['url'] );

		if ( ! $parent_payment ) {
			tribe( 'logger' )->log_debug(
				sprintf(
				// Translators: %s: The PayPal payment event.
					__( 'Missing PayPal payment for webhook event: %s', 'event-tickets' ),
					json_encode( $event )
				),
				'tickets-commerce-gateway-paypal'
			);

			return new WP_Error( 'tec-tickets-commerce-paypal-webhook-invalid-parent-payment', null, [
				'parent_payment' => $parent_payment,
				'event'          => $event
			] );
		}

		$order = tec_tc_orders()->by_args( [
			'status'           => 'any',
			'gateway_order_id' => $parent_payment['id'],
		] )->first();

		// If there's no matching payment then it's not tracked by Tickets Commerce.
		if ( ! $order instanceof \WP_Post ) {
			tribe( 'logger' )->log_debug(
				sprintf(
				// Translators: %s: The PayPal payment ID.
					__( 'Missing order for PayPal payment from webhook: %s', 'event-tickets' ),
					$parent_payment['id']
				),
				'tickets-commerce-gateway-paypal'
			);

			return new WP_Error( 'tec-tickets-commerce-paypal-webhook-order-not-found', null, [
				'parent_payment' => $parent_payment,
				'order'          => $order,
				'event'          => $event
			] );
		}

		// Don't do anything if the status is already set.
		if ( $new_status->get_wp_slug() === $order->post_status ) {
			tribe( 'logger' )->log_debug(
				sprintf(
				// Translators: %s: The PayPal payment ID.
					__( 'PayPal Order "%1$s" already on status "%2$s" from webhook: %3$s', 'event-tickets' ),
					$parent_payment['id'],
					$new_status->get_slug(),
					json_encode( $event )
				),
				'tickets-commerce-gateway-paypal'
			);

			return new WP_Error( 'tec-tickets-commerce-paypal-webhook-order-status-already-updated', null, [
				'parent_payment' => $parent_payment,
				'order'          => $order,
				'new_status'     => $new_status,
				'event'          => $event
			] );
		}

		$updated = tribe( Order::class )->modify_status( $order->ID, $new_status->get_slug(), [
			'gateway_payload' => $event,
		] );

		tribe( 'logger' )->log_debug(
			sprintf(
			// Translators: %1$s: The status name; %2$s: The payment information.
				__( 'Change %1$s in PayPal from webhook: %2$s', 'event-tickets' ),
				$new_status->get_slug(),
				sprintf( '[Order ID: %s; PayPal Payment ID: %s]', $order->ID, $parent_payment['id'] )
			),
			'tickets-commerce-gateway-paypal'
		);

		return $updated;
	}

}
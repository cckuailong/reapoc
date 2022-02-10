<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal\Webhooks;

use TEC\Tickets\Commerce\Status as Commerce_Status;

/**
 * Class Events
 *
 * @since   5.1.10
 *
 * @package TEC\Tickets\Commerce\Gateways\PayPal\Webhooks
 */
class Events {
	/**
	 * Webhook Event name for a capture of completed payment.
	 *
	 * @since 5.1.10
	 *
	 * @var string
	 */
	const PAYMENT_CAPTURE_COMPLETED = 'PAYMENT.CAPTURE.COMPLETED';

	/**
	 * Webhook Event name for a capture of denied payment.
	 *
	 * @since 5.1.10
	 *
	 * @var string
	 */
	const PAYMENT_CAPTURE_DENIED = 'PAYMENT.CAPTURE.DENIED';

	/**
	 * Webhook Event name for a capture of refunded payment.
	 *
	 * @since 5.1.10
	 *
	 * @var string
	 */
	const PAYMENT_CAPTURE_REFUNDED = 'PAYMENT.CAPTURE.REFUNDED';

	/**
	 * Webhook Event name for a capture of reversed payment.
	 *
	 * @since 5.1.10
	 *
	 * @var string
	 */
	const PAYMENT_CAPTURE_REVERSED = 'PAYMENT.CAPTURE.REVERSED';

	/**
	 * Default mapping from PayPal Status to Tickets Commerce.
	 *
	 * @since 5.1.10
	 *
	 * @var array
	 */
	protected $default_map = [
		self::PAYMENT_CAPTURE_COMPLETED => Commerce_Status\Completed::SLUG,
		self::PAYMENT_CAPTURE_DENIED    => Commerce_Status\Denied::SLUG,
		self::PAYMENT_CAPTURE_REFUNDED  => Commerce_Status\Refunded::SLUG,
		self::PAYMENT_CAPTURE_REVERSED  => Commerce_Status\Reversed::SLUG,
	];

	/**
	 * Return webhook label's "Nice name".
	 *
	 * @since 5.2.0
	 *
	 * @param string $event_name A PayPal Event String.
	 *
	 * @return string The Webhook label, false on error.
	 */
	public function get_webhook_label( $event_name ) {
		$labels = [
			static::PAYMENT_CAPTURE_COMPLETED => __( 'Completed payments', 'event-tickets' ),
			static::PAYMENT_CAPTURE_DENIED    => __( 'Denied payments', 'event-tickets' ),
			static::PAYMENT_CAPTURE_REFUNDED  => __( 'Refunds', 'event-tickets' ),
			static::PAYMENT_CAPTURE_REVERSED  => __( 'Reversed', 'event-tickets' ),
		];

		/**
		 * Allows filtering of the Webhook map of events for each one of the types we listen for.
		 *
		 * @since 5.2.0
		 *
		 * @param array  $labels     The default map of which event types that translate to a given label string.
		 * @param string $event_name Which event name we are looking for.
		 */
		$labels = apply_filters( 'tec_tickets_commerce_gateway_paypal_webhook_events_labels_map', $labels, $event_name );

		if ( ! $this->is_valid( $event_name ) ) {
			return false;
		}

		if ( isset( $labels[ $event_name ] ) ) {
			return $labels[ $event_name ];
		}

		return false;
	}

	/**
	 * Gets the valid mapping of the webhook events.
	 *
	 * @since 5.1.10
	 *
	 * @return array
	 */
	public function get_valid() {
		/**
		 * Allows filtering of the Webhook map of events for each one of the types we listen for.
		 *
		 * @since 5.1.10
		 *
		 * @param array $map The default map of which event types that translate to a given Status class.
		 */
		return apply_filters( 'tec_tickets_commerce_gateway_paypal_webhook_events_map', $this->default_map );
	}

	/**
	 * Returns of a list of the Webhook events we are listening to.
	 *
	 * @since 5.1.10
	 *
	 * @return string[]
	 */
	public function get_registered_events() {
		return array_keys( $this->get_valid() );
	}

	/**
	 * Checks if a given PayPal webhook event name is valid.
	 *
	 * @since 5.1.10
	 *
	 * @param string $event_name A PayPal Event String.
	 *
	 * @return bool
	 */
	public function is_valid( $event_name ) {
		$events_map = $this->get_valid();

		return isset( $events_map[ $event_name ] );
	}

	/**
	 * Converts a valid PayPal webhook event name into a commerce status object.
	 *
	 * @since 5.1.10
	 *
	 * @param string $event_name A PayPal Event String.
	 *
	 * @return false|Commerce_Status\Status_Interface|null
	 */
	public function convert_to_commerce_status( $event_name ) {
		if ( ! $this->is_valid( $event_name ) ) {
			return false;
		}
		$events_map = $this->get_valid();

		return tribe( Commerce_Status\Status_Handler::class )->get_by_slug( $events_map[ $event_name ] );
	}

}
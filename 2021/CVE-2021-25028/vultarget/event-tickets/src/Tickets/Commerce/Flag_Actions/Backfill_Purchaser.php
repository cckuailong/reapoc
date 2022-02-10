<?php

namespace TEC\Tickets\Commerce\Flag_Actions;

use TEC\Tickets\Commerce\Attendee;
use TEC\Tickets\Commerce\Gateways\PayPal\Gateway as PayPal_Gateway;
use TEC\Tickets\Commerce\Module;
use TEC\Tickets\Commerce\Order;
use TEC\Tickets\Commerce\Status\Completed;
use TEC\Tickets\Commerce\Status\Status_Interface;

/**
 * Class Increase_Stock, normally triggered when refunding on orders get set to not-completed.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Flag_Actions
 */
class Backfill_Purchaser extends Flag_Action_Abstract {
	/**
	 * {@inheritDoc}
	 */
	protected $flags = [
		'backfill_purchaser',
	];

	/**
	 * {@inheritDoc}
	 */
	protected $post_types = [
		Order::POSTTYPE
	];

	/**
	 * {@inheritDoc}
	 */
	public function handle( Status_Interface $new_status, $old_status, \WP_Post $order ) {
		if ( empty( $order->gateway_payload[ Completed::SLUG ] ) ) {
			return;
		}

		if ( ! empty( $order->purchaser_email ) ) {
			return;
		}

		// @todo move this piece out the flag action since it's PayPal gateway specific.
		if (
			empty( $order->gateway )
			|| PayPal_Gateway::get_key() !== $order->gateway
		) {
			return;
		}

		$payload = end( $order->gateway_payload[ Completed::SLUG ] );

		if ( empty( $payload['payer']['email_address'] ) ) {
			return;
		}

		if ( ! filter_var( $payload['payer']['email_address'], FILTER_VALIDATE_EMAIL ) ) {
			return;
		}

		$email      = trim( $payload['payer']['email_address'] );
		$first_name = null;
		$last_name  = null;
		$full_name  = null;

		if ( ! empty( $payload['payer']['name']['given_name'] ) ) {
			$first_name = trim( $payload['payer']['name']['given_name'] );
		}

		if ( ! empty( $payload['payer']['name']['surname'] ) ) {
			$last_name = trim( $payload['payer']['name']['surname'] );
		}

		$full_name = trim( $first_name . ' ' . $last_name );

		update_post_meta( $order->ID, Order::$purchaser_email_meta_key, $email );
		update_post_meta( $order->ID, Order::$purchaser_first_name_meta_key, $first_name );
		update_post_meta( $order->ID, Order::$purchaser_last_name_meta_key, $last_name );
		update_post_meta( $order->ID, Order::$purchaser_full_name_meta_key, $full_name );

		$attendees = tribe( Module::class )->get_attendees_by_order_id( $order->ID );

		if ( empty( $attendees ) ) {
			return;
		}

		foreach ( $attendees as $attendee ) {
			if ( empty( $attendee['holder_email'] ) ) {
				update_post_meta( $attendee['ID'], Attendee::$email_meta_key, $email );
			}

			if ( empty( $attendee['holder_name'] ) || Order::$placeholder_name === $attendee['holder_name'] ) {
				update_post_meta( $attendee['ID'], Attendee::$full_name_meta_key, $full_name );
			}
		}
	}
}

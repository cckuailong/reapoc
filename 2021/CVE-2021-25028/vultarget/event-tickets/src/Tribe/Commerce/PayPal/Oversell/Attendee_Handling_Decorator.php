<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Oversell__Attendee_Handling_Decorator
 *
 * Decorates an oversell policy object to handle oversold attendees.
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Oversell__Attendee_Handling_Decorator implements Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface {

	/**
	 * @var \Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface
	 */
	protected $policy;

	/**
	 * Tribe__Tickets__Commerce__PayPal__Oversell__Attendee_Handling_Decorator constructor.
	 *
	 * @since 4.7
	 *
	 * @param \Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface $policy
	 */
	public function __construct( Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface $policy ) {
		$this->policy = $policy;
	}

	/**
	 * Whether this policy allows overselling or not.
	 *
	 * @since 4.7
	 *
	 * @return bool
	 */
	public function allows_overselling() {
		return $this->policy->allows_overselling();
	}

	/**
	 * Modifies the quantity of tickets that can actually be over-sold according to
	 * this policy.
	 *
	 * @since 4.7
	 *
	 * @param int $qty       The requested quantity
	 * @param int $inventory The current inventory value
	 *
	 * @return int The updated quantity
	 */
	public function modify_quantity( $qty, $inventory ) {
		return $this->policy->modify_quantity( $qty, $inventory );
	}

	/**
	 * Returns the policy post ID.
	 *
	 * @since 4.7
	 *
	 * @return int
	 */
	public function get_post_id() {
		return $this->policy->get_post_id();
	}

	/**
	 * Returns the policy PayPal Order ID (hash).
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_order_id() {
		return $this->policy->get_order_id();
	}

	/**
	 * Returns the policy ticket post ID.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_ticket_id() {
		return $this->policy->get_ticket_id();
	}

	/**
	 * Returns the policy nice name.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->policy->get_name();
	}

	/**
	 * Handles existing oversold attendees generated from an oversell.
	 *
	 * @since 4.7
	 *
	 * @param array $oversold_attendees
	 */
	public function handle_oversold_attendees( array $oversold_attendees ) {
		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		foreach ( $oversold_attendees as $attendee ) {
			$attendee_id = Tribe__Utils__Array::get( $attendee, 'attendee_id', false );
			$event_id    = Tribe__Utils__Array::get( $attendee, 'event_id', false );

			if ( $attendee_id && $event_id ) {
				$paypal->delete_ticket( $event_id, $attendee_id );
			}

			// any oversold attendee, whether deleted or not, is a sale
			$product_id = Tribe__Utils__Array::get( $attendee, 'product_id', false );

			$global_stock = new Tribe__Tickets__Global_Stock( $event_id );
			$shared_capacity = false;
			if ( $global_stock->is_enabled() ) {
				$shared_capacity = true;
			}

			if ( false !== $product_id ) {
				$paypal->increase_ticket_sales_by( $product_id, 1, $shared_capacity, $global_stock );
			}
		}
	}
}

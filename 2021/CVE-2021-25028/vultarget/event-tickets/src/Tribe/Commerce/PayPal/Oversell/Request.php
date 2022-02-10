<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Oversell__Request
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Oversell__Request {

	/**
	 * @var string
	 */
	public static $oversell_action = 'oversell';

	/**
	 * @var string
	 */
	protected $policy;

	/**
	 * @var string
	 */
	protected $order_id;

	/**
	 * Conditionally handles an oversell request.
	 *
	 * @since 4.7
	 */
	public function handle() {
		if ( ! isset( $_GET['tpp_action'], $_GET['tpp_policy'], $_GET['tpp_order_id'], $_GET['tpp_slug'] ) ) {
			return;
		}

		if ( self::$oversell_action !== $_GET['tpp_action'] ) {
			return;
		}

		$this->policy   = $_GET['tpp_policy'];
		$this->order_id = $_GET['tpp_order_id'];

		if ( false === $order = Tribe__Tickets__Commerce__PayPal__Order::from_order_id( $this->order_id ) ) {
			return;
		}

		$cap = get_post_type_object( Tribe__Tickets__Commerce__PayPal__Main::ORDER_OBJECT )->cap->edit_post;
		if ( ! current_user_can( $cap, $order->get_post_id() ) ) {
			return;
		}

		/** @var Tribe__Tickets__Commerce__PayPal__Main $paypal */
		$paypal = tribe( 'tickets.commerce.paypal' );

		add_filter( 'tribe_tickets_commerce_paypal_oversell_policy', array( $this, 'filter_policy' ), 10, 4 );
		add_filter( 'tribe_tickets_commerce_paypal_oversell_generates_notice', '__return_false' );
		add_filter( 'tribe_tickets_commerce_paypal_oversell_policy_object', array( $this, 'filter_policy_object' ), 10, 5 );
		add_filter( 'tribe_exit', array( $this, 'do_not_exit' ), 10, 2 );

		/** @var Tribe__Tickets__Commerce__PayPal__Gateway $gateway */
		$gateway = tribe( 'tickets.commerce.paypal.gateway' );

		$data         = $order->get_meta( 'transaction_data' );

		// put back the order status to pending
		$order->set_meta( 'payment_status', 'pending' );
		$order->update();

		$gateway->set_raw_transaction_data( $data );
		$gateway->set_transaction_data( $gateway->parse_transaction( $data ) );

		$paypal->generate_tickets( Tribe__Tickets__Commerce__PayPal__Stati::$completed, false );

		tribe_transient_notice_remove( $_GET['tpp_slug'] );

		// whatever the choice the order is now Completed
		$order->set_meta( 'payment_status', 'completed' );
		$order->update();

		remove_filter( 'tribe_exit', array( $this, 'do_not_exit' ), 10 );

		/** @var Tribe__Tickets__Commerce__PayPal__Orders__Report $orders_report */
		$post_ids = $order->get_related_post_ids();
		$post     = get_post( reset( $post_ids ) );
		wp_safe_redirect( Tribe__Tickets__Commerce__PayPal__Orders__Report::get_tickets_report_link( $post ) );
		tribe_exit();
	}

	/**
	 * Filters the policy slug to return the one the user has chosen.
	 *
	 * @since 4.7
	 *
	 * @param $policy
	 * @param $post_id
	 * @param $ticket_id
	 * @param $order_id
	 *
	 * @return string
	 */
	public function filter_policy( $policy, $post_id, $ticket_id, $order_id ) {
		if ( $order_id == $this->order_id ) {
			return $this->policy;
		}

		return $policy;
	}

	/**
	 * Filters the policy object to wrap it in an oversold attendee handling decorator.
	 *
	 * @since 4.7
	 *
	 * @param Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface $policy_object
	 *
	 * @return Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface
	 */
	public function filter_policy_object( $policy_object, $policy, $post_id, $ticket_id, $order_id ) {
		if ( ! $policy_object instanceof Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface ) {
			return $policy_object;
		}

		if ( $order_id == $this->order_id ) {
			return new Tribe__Tickets__Commerce__PayPal__Oversell__Attendee_Handling_Decorator( $policy_object );
		}

		return $policy_object;
	}

	/**
	 * Filters the `tribe_exit` function to avoid redirection in mid-process.
	 *
	 * @since 4.7
	 *
	 * @return string
	 */
	public function do_not_exit() {
		return '__return_true';
	}
}

<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Oversell__Policies
 *
 * Strategy factory for oversell policies.
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Oversell__Policies {

	/**
	 * @var string The sub-option name used to store the policy for the whole site.
	 */
	public static $option_name = 'ticket-paypal-stock-overselling-policy';

	/**
	 * @var string The sub-option used to store the value that will decide if admin users should
	 *             be notified or not about oversell-events.
	 */
	public static $notice_option_name = 'ticket-paypal-stock-overselling-notice-display';

	/**
	 * @var string The meta key that is used to store the policy for the post.
	 */
	public static $meta_key = '_tribe_paypal_oversell_policy';

	/**
	 * Factory method that will return a policy based on the current post ID and options.
	 *
	 * Post policy will override the general policy if available; ticket policy will override
	 * general and post policy if available.
	 *
	 * @since 4.7
	 *
	 * @param int    $post_id
	 * @param int    $ticket_id
	 * @param string $order_id
	 *
	 * @returns Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface
	 */
	public function for_post_ticket_order( $post_id, $ticket_id, $order_id ) {
		$policy = tribe_get_option( self::$option_name, 'no-oversell' );

		if ( $post_policy = get_post_meta( $post_id, self::$meta_key, true ) ) {
			$policy = $post_policy;
		}

		if ( $ticket_policy = get_post_meta( $ticket_id, self::$meta_key, true ) ) {
			$policy = $ticket_policy;
		}

		/**
		 * Filters the oversell policy after the general, post and ticket ones have been taken into account.
		 *
		 * @since 4.7
		 *
		 * @param string $policy    The slug of the policy to use.
		 * @param int    $post_id   The current post ID
		 * @param int    $ticket_id The current ticket post ID
		 * @param string $order_id  The current Order PayPal ID (hash)
		 */
		$policy = apply_filters( 'tribe_tickets_commerce_paypal_oversell_policy', $policy, $post_id, $ticket_id, $order_id );

		$map = array(
			'no-oversell'    => 'Tribe__Tickets__Commerce__PayPal__Oversell__No_Oversell',
			'sell-available' => 'Tribe__Tickets__Commerce__PayPal__Oversell__Sell_Available',
			'sell-all'       => 'Tribe__Tickets__Commerce__PayPal__Oversell__Sell_All',
		);

		/**
		 * Allows filtering the policies map.
		 *
		 * @since 4.7
		 *
		 * @param array  $map       An associative array in the shape [ <policy-slug> => <class> ]
		 * @param int    $post_id   The current post ID
		 * @param int    $ticket_id The current ticket post ID
		 * @param string $order_id  The current Order PayPal ID (hash)
		 */
		$map = apply_filters( 'tribe_tickets_commerce_paypal_oversell_policies_map', $map, $post_id, $ticket_id, $order_id );

		$class = Tribe__Utils__Array::get( $map, $policy, reset( $map ) );

		// allow for ready to use instances to be returned, not just class names
		$instance = $class instanceof Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface
			? $class : new $class( $post_id, $ticket_id, $order_id );

		$generate_admin_notice = tribe_get_option( self::$notice_option_name, true );

		/**
		 * Whether overselling should generate an admin notice or not.
		 *
		 * @since 4.7
		 *
		 * @param bool   $generate_admin_notice
		 * @param int    $post_id   The current post ID
		 * @param int    $ticket_id The current ticket post ID
		 * @param string $order_id  The current Order PayPal ID (hash)
		 */
		$generate_admin_notice = apply_filters( 'tribe_tickets_commerce_paypal_oversell_generates_notice', $generate_admin_notice, $post_id, $ticket_id, $order_id );

		if ( $generate_admin_notice ) {
			$instance = new Tribe__Tickets__Commerce__PayPal__Oversell__Admin_Notice_Decorator( $instance );
		}

		/**
		 * Filters the oversell policy object before returning it.
		 *
		 * @since 4.7
		 *
		 * @param Tribe__Tickets__Commerce__PayPal__Oversell__Policy_Interface $instance  The policy object.
		 * @param string                                                       $policy    The slug of the policy to use.
		 * @param int                                                          $post_id   The current post ID
		 * @param int                                                          $ticket_id The current ticket post ID
		 * @param string                                                       $order_id  The current Order PayPal ID (hash)
		 */
		$instance = apply_filters( 'tribe_tickets_commerce_paypal_oversell_policy_object', $instance, $policy, $post_id, $ticket_id, $order_id );

		return $instance;
	}
}

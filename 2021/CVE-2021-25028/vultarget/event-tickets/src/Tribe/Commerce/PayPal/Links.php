<?php

/**
 * Class Tribe__Tickets__Commerce__PayPal__Links
 *
 * A PayPal link repository information.
 *
 * @since 4.7
 */
class Tribe__Tickets__Commerce__PayPal__Links {

	/**
	 * Returns the link to the IPN notification history page on PayPal.
	 *
	 * @since 4.7
	 *
	 * @param string $what Either `link` to return the URL or `tag` to return an `a` tag.
	 *
	 * @return string
	 */
	public function ipn_notification_history( $what = 'tag' ) {
		$link = add_query_arg(
			array( 'cmd' => '_display-ipns-history' ),
			tribe( 'tickets.commerce.paypal.gateway' )->get_settings_url()
		);
		$tag  = '<a href="'
		        . esc_url( $link )
		        . '" target="_blank" rel="noopener noreferrer">'
		        . esc_html__( 'Profile and Settings > My selling tools > Instant Payment Notification > IPN History Page', 'event-tickets' )
		        . '</a>';
		$map  = array(
			'link' => $link,
			'tag'  => $tag,
		);

		return Tribe__Utils__Array::get( $map, $what, '' );
	}

	/**
	 * Returns the link to the IPN notification settings page on PayPal.
	 *
	 * @since 4.7
	 *
	 * @param string $what Either `link` to return the URL or `tag` to return an `a` tag.
	 *
	 * @return string
	 */
	public function ipn_notification_settings( $what = 'tag' ) {
		$link = add_query_arg(
			array( 'cmd' => '_profile-ipn-notify' ),
			tribe( 'tickets.commerce.paypal.gateway' )->get_settings_url()
		);

		$tag = '<a href="'
		       . esc_url( $link )
		       . '" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Profile and Settings > My selling tools > Instant Payment Notification > Update', 'event-tickets' )
		       . '</a>';

		$map = array(
			'link' => $link,
			'tag'  => $tag,
		);

		return Tribe__Utils__Array::get( $map, $what, '' );
	}

	/**
	 * Returns the link to an Order page on PayPal, based on the Order ID.
	 *
	 * @since 4.7
	 *
	 * @param string $what Either `link` to return the URL or `tag` to return an `a` tag.
	 * @param string $order_id The Order PayPal ID (hash).
	 * @param string $text An optional message that will be used as the `tag` text; defaults to
	 *                  the Order PayPal ID (hash).
	 *
	 * @return string
	 */
	public function order_link( $what, $order_id, $text = null ) {
		$text = null !== $text ? $text : $order_id;

		$link = Tribe__Tickets__Commerce__PayPal__Order::get_order_link( $order_id );
		$tag  = '<a href="'
		        . esc_url( $link )
		        . '" target="_blank" rel="noopener noreferrer">' . esc_html__( $text )
		        . '</a>';

		$map = array(
			'link' => $link,
			'tag'  => $tag,
		);

		return Tribe__Utils__Array::get( $map, $what, '' );
	}

	/**
	 * Returns the link to return to the user current cart.
	 *
	 * @since 4.7.3
	 *
	 * @param array $query_args An optional array of query arguments that should be appended to
	 *                          the `shopping_url`.
	 * @return string
	 */
	public function return_to_cart( array $query_args = array() ) {
		/** @var Tribe__Tickets__Commerce__PayPal__Gateway $gateway */
		$gateway = tribe( 'tickets.commerce.paypal.gateway' );

		if ( empty( $query_args['tpp_invoice'] ) ) {
			$invoice_number = tribe_get_request_var( 'tpp_invoice', false );
			if ( false !== $invoice_number ) {
				$query_args['tpp_invoice'] = $invoice_number;
			}
		}

		$args = array(
			'cmd'          => '_cart',
			'display'      => 1,
			'business'     => urlencode( trim( tribe_get_option( 'ticket-paypal-email' ) ) ),
			'shopping_url' => urlencode( add_query_arg( $query_args, get_post_permalink() ) ),
		);

		return add_query_arg( $args, $gateway->get_cart_url() );
	}
}

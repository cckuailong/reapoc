<?php
/**
 * Functions and template tags dedicated to Orders in Ticket Commerce.
 *
 * @since 5.1.9
 */

use TEC\Tickets\Commerce\Models\Order_Model;
use \TEC\Tickets\Commerce\Order;

/**
 * Fetches and returns a decorated post object representing an Order.
 *
 * @since 5.1.9
 *
 * @param null|int|WP_Post $order                  The order ID or post object or `null` to use the global one.
 * @param string|null      $output                 The required return type. One of `OBJECT`, `ARRAY_A`, or `ARRAY_N`, which
 *                                                 correspond to a WP_Post object, an associative array, or a numeric array,
 *                                                 respectively. Defaults to `OBJECT`.
 * @param string           $filter                 Type of filter to apply.
 * @param bool             $force                  Whether to force a re-fetch ignoring cached results or not.
 *
 * @return array|WP_Post|null    The Order post object or array, `null` if not found.
 */
function tec_tc_get_order( $order = null, $output = OBJECT, $filter = 'raw', $force = false ) {
	/**
	 * Filters the order result before any logic applies.
	 *
	 * Returning a non `null` value here will short-circuit the function and return the value.
	 * Note: this value will not be cached and the caching of this value is a duty left to the filtering function.
	 *
	 * @since 5.1.9
	 *
	 * @param mixed       $return      The order object to return.
	 * @param mixed       $order       The order object to fetch.
	 * @param string|null $output      The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                                 correspond to a `WP_Post` object, an associative array, or a numeric array,
	 *                                 respectively. Defaults to `OBJECT`.
	 * @param string      $filter      Type of filter to apply.
	 */
	$return = apply_filters( 'tec_tickets_commerce_get_order_before', null, $order, $output, $filter );

	if ( null !== $return ) {
		return $return;
	}

	$post = false;

	/** @var Tribe__Cache $cache */
	$cache = tribe( 'cache' );

	$cache_post = get_post( $order );

	if ( empty( $cache_post ) || ! Order::is_valid( $cache_post ) ) {
		return null;
	}

	$key_fields = [
		$cache_post->ID,
		$cache_post->post_modified,
		// Use the `post_password` field as we show/hide some information depending on that.
		$cache_post->post_password,
		// We must include options on cache key, because options influence the hydrated data on the Order object.
		wp_json_encode( Tribe__Settings_Manager::get_options() ),
		wp_json_encode( [
			get_option( 'start_of_week' ),
			get_option( 'timezone_string' ),
			get_option( 'gmt_offset' )
		] ),
		$output,
		$filter,
	];

	$cache_key = 'tec_tc_get_order_' . md5( wp_json_encode( $key_fields ) );

	if ( ! $force ) {
		$post = $cache->get( $cache_key, Tribe__Cache_Listener::TRIGGER_SAVE_POST );
	}

	if ( false === $post ) {
		$post = Order_Model::from_post( $order )->to_post( $output, $filter );

		if ( empty( $post ) ) {
			return null;
		}

		/**
		 * Filters the order post object before caching it and returning it.
		 *
		 * Note: this value will be cached; as such this filter might not run on each request.
		 * If you need to filter the output value on each call of this function then use the `tec_tickets_commerce_get_order_before`
		 * filter.
		 *
		 * @since 5.1.9
		 *
		 * @param WP_Post $post   The order post object, decorated with a set of custom properties.
		 * @param string  $output The output format to use.
		 * @param string  $filter The filter, or context of the fetch.
		 */
		$post = apply_filters( 'tec_tickets_commerce_get_order', $post, $output, $filter );

		// Dont try to reset cache when forcing.
		if ( ! $force ) {
			$cache->set( $cache_key, $post, WEEK_IN_SECONDS, Tribe__Cache_Listener::TRIGGER_SAVE_POST );
		}
	}

	/**
	 * Filters the order result after the order has been built from the function.
	 *
	 * Note: this value will not be cached and the caching of this value is a duty left to the filtering function.
	 *
	 * @since 5.1.9
	 *
	 * @param WP_Post     $post        The order post object to filter and return.
	 * @param int|WP_Post $order       The order object to fetch.
	 * @param string|null $output      The required return type. One of OBJECT, ARRAY_A, or ARRAY_N, which
	 *                                 correspond to a `WP_Post` object, an associative array, or a numeric array,
	 *                                 respectively. Defaults to `OBJECT`.
	 * @param string      $filter      Type of filter to apply.
	 */
	$post = apply_filters( 'tec_tickets_commerce_get_order_after', $post, $order, $output, $filter );

	if ( OBJECT !== $output ) {
		$post = ARRAY_A === $output ? (array) $post : array_values( (array) $post );
	}

	return $post;
}

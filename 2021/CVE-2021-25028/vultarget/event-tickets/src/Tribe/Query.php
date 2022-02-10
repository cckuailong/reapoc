<?php


/**
 * Class Tribe__Tickets__Query
 *
 * Modifies the query to allow ticket related filtering.
 */
class Tribe__Tickets__Query {

	/**
	 * @var string The slug of the query var used to filter posts by their ticketing status.
	 */
	public static $has_tickets = 'tribe-has-tickets';

	/**
	 *  Hooks to add query vars and filter the post query.
	 */
	public function hook() {
		add_filter( 'query_vars', array( $this, 'filter_query_vars' ) );
		add_action( 'pre_get_posts', array( $this, 'restrict_by_ticketed_status' ) );
	}

	/**
	 * @param array $query_vars A list of allowed query variables.
	 *
	 * @return array $query_vars A list of allowed query variables.
	 *               plus ours.
	 */
	public function filter_query_vars( array $query_vars = array() ) {
		$query_vars[] = self::$has_tickets;

		return $query_vars;
	}

	/**
	 * If the `has-tickets` query var is set then limit posts by having
	 * or not having tickets assigned.
	 *
	 * @param WP_Query &$this The WP_Query instance (passed by reference).
	 */
	public function restrict_by_ticketed_status( WP_Query $query ) {
		$var = self::$has_tickets;

		$value = $query->get( $var, false );

		if ( false === $value ) {
			return;
		}

		$has_tickets = (bool) $value;

		/** @var Tribe__Tickets__Cache__Cache_Interface $cache */
		$cache      = tribe( 'tickets.cache' );
		$post_types = (array) $query->get( 'post_type', array( 'post' ) );

		$cache->include_past( true );

		$ids = $has_tickets ? $cache->posts_with_ticket_types( $post_types ) : $cache->posts_without_ticket_types( $post_types );

		$post__in = $query->get( 'post__in' );

		$in = ! empty( $post__in ) ? array_intersect( $post__in, $ids ) : $ids;

		// empty means no post of this type has/has-no tickets
		if ( empty( $in ) ) {
			$in = array( 0 );
		}

		$query->set( 'post__in', $in );
		$query->query_vars['post__in'] = $in;

		$cache->include_past( false );
	}
}

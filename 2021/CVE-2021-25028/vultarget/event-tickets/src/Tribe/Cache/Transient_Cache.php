<?php


/**
 * Class Tribe__Ticket__Cache__Transient_Cache
 *
 * Stores and return costly site-wide information.
 */
class Tribe__Tickets__Cache__Transient_Cache extends Tribe__Tickets__Cache__Abstract_Cache implements Tribe__Tickets__Cache__Cache_Interface {


	/**
	 * Resets all caches.
	 *
	 * @param int $post_id
	 */
	public function reset_all( $post_id = 0 ) {
		$post_types = array( 'post' );
		$screen     = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

		if ( $post_id ) {
			$post_types = array( get_post_type( $post_id ) );
		} elseif ( ! is_null( $screen ) && ! empty( $screen->post_type ) ) {
			$post_types = array( $screen->post_type );
		}

		$hash = md5( serialize( $post_types ) );
		foreach ( $this->keys as $key ) {
			delete_transient( __CLASS__ . $key );
			delete_transient( __CLASS__ . $key . $hash );
		}

		// Flush WordPress cache for cache compatibility
		wp_cache_flush();

	}

	/**
	 * Returns array of post IDs of posts that have no tickets assigned.
	 *
	 * Please note that the list is aware of supported types.
	 *
	 * @param array $post_types An array of post types overriding the supported ones.
	 * @param bool $refetch Whether the method should try to get the data from the cache first or not.
	 *
	 * @return array
	 */
	public function posts_without_ticket_types( array $post_types = null, $refetch = false ) {
		if ( ! empty( $post_types ) ) {
			$cache_key = __CLASS__ . 'posts_without_tickets' . md5( serialize( $post_types ) );
		} else {
			$cache_key = __CLASS__ . 'posts_without_tickets';
		}

		$ids = $refetch ? false : get_transient( $cache_key );

		if ( false === $ids ) {
			$ids = $this->fetch_posts_without_ticket_types( $post_types );

			set_transient( $cache_key, $ids, $this->expiration );
		}

		return $ids;
	}

	/**
	 * Returns array of post IDs of posts that have at least one ticket assigned.
	 *
	 * Please note that the list is aware of supported types.
	 *
	 * @param array $post_types An array of post types overriding the supported ones.
	 * @param bool $refetch Whether the method should try to get the data from the cache first or not.
	 *
	 * @return array
	 */
	public function posts_with_ticket_types( array $post_types = null, $refetch = false ) {
		if ( ! empty( $post_types ) ) {
			$cache_key = __CLASS__ . 'posts_with_tickets' . md5( serialize( $post_types ) );
		} else {
			$cache_key = __CLASS__ . 'posts_with_tickets';
		}

		$ids = $refetch ? false : get_transient( $cache_key );

		if ( false === $ids ) {
			$ids = $this->fetch_posts_with_ticket_types( $post_types );

			set_transient( $cache_key, $ids, $this->expiration );
		}

		return $ids;
	}

	/**
	 * Returns an array of all past events post IDs.
	 *
	 * @param bool $refetch Whether the method should try to get the data from the cache first or not.
	 *
	 * @return array
	 */
	public function past_events( $refetch = false ) {
		$ids = $refetch ? false : get_transient( __CLASS__ . 'past_events' );

		if ( false === $ids ) {
			$ids = $this->fetch_past_events();

			set_transient( __CLASS__ . 'past_events', $ids, $this->expiration );
		}

		return $ids;
	}
}

<?php


/**
 * Class Tribe__Ticket__Cache__Transient_Cache
 *
 * Stores and return costly site-wide information.
 */
interface Tribe__Tickets__Cache__Cache_Interface {

	/**
	 * Resets all caches.
	 */
	public function reset_all();

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
	public function posts_without_ticket_types( array $post_types = null, $refetch = false );

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
	public function posts_with_ticket_types( array $post_types = null, $refetch = false );

	/**
	 * Returns an array of all past events post IDs.
	 *
	 * @param bool $refetch Whether the method should try to get the data from the cache first or not.
	 *
	 * @return array
	 */
	public function past_events( $refetch = false );

	/**
	 * Sets the expiration time for the cache.
	 *
	 * @param int $seconds
	 *
	 * @return void
	 */
	public function set_expiration_time( $seconds );

	/**
	 * Whether "past" posts should be included or not.
	 *
	 * Some post types, like Events, have a notion of "past". By default the cache
	 * will not take "past" posts into account.
	 *
	 * @param bool $include_past
	 */
	public function include_past( $include_past );
}

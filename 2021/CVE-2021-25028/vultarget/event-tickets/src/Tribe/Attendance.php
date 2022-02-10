<?php
/**
 * Utilities for tracking ticket provider-agnostic attendance data.
 */
class Tribe__Tickets__Attendance {
	/**
	 * Meta key used to track the number of attendees that have been deleted
	 * for each event.
	 */
	const DELETED_ATTENDEES_COUNT = '_tribe_deleted_attendees_count';

	/**
	 * Container for our instances (one per event/post).
	 *
	 * @var array
	 */
	protected static $instances = array();

	/**
	 * @var int
	 */
	protected $post_id = 0;


	/**
	 * Returns a Tribe__Tickets__Attendance object for the specified post ID.
	 *
	 * @param int $post_id
	 *
	 * @return Tribe__Tickets__Attendance
	 */
	public static function instance( $post_id ) {
		if ( ! isset( self::$instances[ $post_id ] ) ) {
			self::$instances[ $post_id ] = new self( $post_id );
		}

		return self::$instances[ $post_id ];
	}

	protected function __construct( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Increments the count of deleted attendees for an event/post
	 * (defaults to 1 unit).
	 *
	 * @param int $units
	 */
	public function increment_deleted_attendees_count( $units = 1 ) {
		$deleted = absint( get_post_meta( $this->post_id, self::DELETED_ATTENDEES_COUNT, true ) );
		update_post_meta( $this->post_id, self::DELETED_ATTENDEES_COUNT, absint( $deleted + $units ) );
	}

	/**
	 * Returns the count of deleted attendees.
	 *
	 * Note that this was not tracked prior to 4.1.4 release, so inaccuracies may
	 * result where attendees were deleted before then.
	 *
	 * @return int
	 */
	public function get_deleted_attendees_count() {
		return absint( get_post_meta( $this->post_id, self::DELETED_ATTENDEES_COUNT, true ) );
	}

	/**
	 * Deletes the attendees caches for a post.
	 *
	 * @param int $post_id The post `ID` field.
	 */
	public static function delete_attendees_caches( $post_id ) {
		$post_transient = Tribe__Post_Transient::instance();
		$post_transient->delete( $post_id, Tribe__Tickets__Tickets::ATTENDEES_CACHE );
	}
}

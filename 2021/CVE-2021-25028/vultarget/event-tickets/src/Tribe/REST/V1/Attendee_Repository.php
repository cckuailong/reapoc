<?php

/**
 * Class Tribe__Tickets__REST__V1__Attendee_Repository
 *
 * The base Attendee object repository, a decorator of the base one.
 *
 * @since 4.8
 */
class Tribe__Tickets__REST__V1__Attendee_Repository
	extends Tribe__Repository__Decorator
	implements Tribe__Repository__Formatter_Interface {
	/**
	 * @var Tribe__Tickets__Attendee_Repository
	 */
	protected $decorated;

	/**
	 * Tribe__Tickets__REST__V1__Attendee_Repository constructor.
	 *
	 * @since 4.8
	 */
	public function __construct() {
		$this->decorated = tribe( 'tickets.attendee-repository' );
		$this->decorated->set_formatter( $this );
		$this->decorated->set_query_builder($this);
		$this->decorated->set_default_args( array_merge(
			$this->decorated->get_default_args(),
			array( 'order' => 'ASC', 'orderby' => array( 'id', 'title' ) )
		) );
	}

	/**
	 * An override of the default query building process to add clauses
	 * specific to REST API queries
	 *
	 * @param bool $use_query_builder Whether to use the query builder, if set, or not.
	 *
	 * @return WP_Query
	 */
	public function build_query( $use_query_builder = true ) {
		$can_view_hidden_attendees = false;

		if ( current_user_can( 'edit_users' ) || current_user_can( 'tribe_manage_attendees' ) ) {
			$can_view_hidden_attendees = true;
		}

		/**
		 * Whether the current user can view hidden attendees,
		 * for instance, those that opted out of having their data public.
		 *
		 * @param bool         $can_view_hidden_attendees The default value for whether the user can view hidden attendees.
		 * @param null|WP_User $user                      The WP_User instance, if available.
		 *
		 * @since 4.12.0
		 *
		 * @return bool Whether the current user can view hidden attendees.
		 */
		$can_view_hidden_attendees = (bool) apply_filters(
			'tribe_tickets_user_can_view_hidden_attendees_rest',
			$can_view_hidden_attendees,
			wp_get_current_user()
		);

		if ( ! $can_view_hidden_attendees ) {
			$this->decorated->by( 'optout', 'no' );
			$this->decorated->by( 'post_status', 'publish' );
			$this->decorated->by( 'rsvp_status__or_none', 'yes' );
			$this->decorated->by( 'event__show_attendees' );
		}

		$this->decorated->set_query_builder( null );

		return $this->decorated->build_query();
	}

	/**
	 * Overrides the base `order_by` method to map and convert some REST API
	 * specific criteria.
	 *
	 * @param string $order_by The post field, custom field or alias key to order posts by.
	 * @param string $order The order direction; optional; shortcut for the `order` method; defaults
	 *                      to `DESC`.
	 *
	 * @return $this
	 */
	public function order_by( $order_by, $order = 'DESC' ) {
		$map = array(
			'date'      => 'date',
			'relevance' => 'relevance',
			'id'        => 'id',
			'include'   => 'meta_value_num',
			'title'     => 'title',
			'slug'      => 'name',
		);

		// if ( 'include' === $order_by ) {
			// @todo review when one meta key is unified
		// }

		$converted_order_by = Tribe__Utils__Array::get( $map, $order_by, false );

		if ( empty( $converted_order_by ) ) {
			return $this;
		}

		$this->decorated->order_by( $converted_order_by, $order );

		return $this;
	}

	/**
	 * Overrides the base implementation to make sure only accessible
	 * attendees are returned.
	 *
	 * @since 4.8
	 *
	 * @param mixed $primary_key
	 *
	 * @return array|WP_Error The Attendee data on success, or a WP_Error
	 *                        detailing why the read failed.
	 */
	public function by_primary_key( $primary_key ) {
		$this->decorated->set_query_builder( null );

		$query = $this->decorated->get_query();
		$query->set( 'fields', 'ids' );
		$query->set( 'p', $primary_key );
		$found = $query->get_posts();
		/** @var Tribe__Tickets__REST__V1__Messages $messages */
		$messages = tribe( 'tickets.rest-v1.messages' );

		if ( empty( $found ) ) {
			return new WP_Error( 'attendee-not-found', $messages->get_message( 'attendee-not-found' ), array( 'status' => 404 ) );
		}

		if ( current_user_can( 'edit_users' ) || current_user_can( 'tribe_manage_attendees' ) ) {
			return $this->format_item( $found[0] );
		}

		$this->decorated->by( 'optout', 'no' );
		$this->decorated->by( 'post_status', 'publish' );
		$this->decorated->by( 'rsvp_status__or_none', 'yes' );

		$cap_query = $this->decorated->get_query();
		$cap_query->set( 'fields', 'ids' );
		$cap_query->set( 'p', $primary_key );
		$found_w_cap = $cap_query->get_posts();

		if ( empty( $found_w_cap ) ) {
			return new WP_Error( 'attendee-not-accessible', $messages->get_message( 'attendee-not-accessible' ), array( 'status' => 401 ) );
		}

		$this->decorated->set_query_builder( $this );

		return $this->format_item( $found_w_cap[0] );
	}

	/**
	 * Returns the attendee in the REST API format.
	 *
	 * @since 4.8
	 *
	 * @param int|WP_Post $id
	 *
	 * @return array|null The attendee information in the REST API format or
	 *                    `null` if the attendee is invalid.
	 */
	public function format_item( $id ) {
		/**
		 * For the time being we use **another** repository to format
		 * the tickets objects to the REST API format.
		 * If this implementation gets a thumbs-up this class and the
		 * `Tribe__Tickets__REST__V1__Post_Repository` should be merged.
		 */

		/** @var Tribe__Tickets__REST__V1__Post_Repository $repository */
		$repository = tribe( 'tickets.rest-v1.repository' );

		$formatted = $repository->get_attendee_data( $id );

		return $formatted instanceof WP_Error ? null : $formatted;
	}
}

<?php
/**
 * Post attendees trait that contains all of the ORM filters that can be used for any repository.
 *
 * @since 4.12.1
 *
 * @package Tribe\Tickets\Repositories\Traits
 */

namespace Tribe\Tickets\Repositories\Traits;

/**
 * Class Post_Attendees
 *
 * @since 4.12.1
 */
trait Post_Attendees {

	/**
	 * Filters events to include only those that match the provided attendee(s).
	 *
	 * @since 4.12.1
	 *
	 * @param int|array $attendee_ids The attendee(s) to filter by.
	 */
	public function filter_by_attendee( $attendee_ids ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $this->decorated ) ) {
			$repo = $this->decorated;
		}

		global $wpdb;

		$attendee_ids = (array) $attendee_ids;
		$attendee_ids = array_map( 'absint', $attendee_ids );
		$attendee_ids = array_unique( $attendee_ids );
		$attendee_ids = implode( ', ', $attendee_ids );

		$alias_attendee = 'ticket_attendee';

		// Do all of the other filtering.
		$this->filter_by_has_attendees( true );

		// Add the by ID filter.
		$repo->where_clause( "`{$alias_attendee}`.`ID` IN ( {$attendee_ids} )" );
	}

	/**
	 * Filters events to include only those that match the provided attendee state.
	 *
	 * @since 4.12.1
	 *
	 * @param bool $has_attendees Indicates if the event should have attendees attached to it or not.
	 */
	public function filter_by_has_attendees( $has_attendees = true ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $this->decorated ) ) {
			$repo = $this->decorated;
		}

		global $wpdb;

		$alias_event    = 'ticket_attendee_event';
		$alias_attendee = 'ticket_attendee';

		$event_meta_keys = method_exists( $this, 'attendee_to_event_keys' ) ? $this->attendee_to_event_keys() : [];
		$event_meta_keys = array_map( [ $wpdb, '_real_escape' ], $event_meta_keys );
		$event_meta_keys = "'" . implode( "', '", $event_meta_keys ) . "'";

		$attendee_types = method_exists( $this, 'attendee_types' ) ? $this->attendee_types() : [];
		$attendee_types = array_map( [ $wpdb, '_real_escape' ], $attendee_types );
		$attendee_types = "'" . implode( "', '", $attendee_types ) . "'";

		if ( $has_attendees ) {
			// Join to the meta that relates attendees to events.
			$repo->filter_query->join( "
					LEFT JOIN `{$wpdb->postmeta}` AS `{$alias_event}`
						ON `{$alias_event}`.`meta_value` = `{$wpdb->posts}`.`ID`
				", $alias_event );

			// Join to the meta that relates meta to attendees.
			$repo->filter_query->join( "
					LEFT JOIN `{$wpdb->posts}` AS `{$alias_attendee}`
						ON `{$alias_attendee}`.`ID` = `{$alias_event}`.`post_id`
				", $alias_attendee );

			$repo->where_clause( "
					`{$alias_event}`.`meta_key` IN ( {$event_meta_keys} )
					AND `{$alias_attendee}`.`post_type` IN ( {$attendee_types} )
				" );

			return;
		}

		// Handle case where post has no attendees.
		$repo->where_clause( "
				NOT EXISTS (
					SELECT 1
					FROM
						`{$wpdb->postmeta}` AS `{$alias_event}`
					LEFT JOIN `{$wpdb->posts}` AS `{$alias_attendee}`
						ON `{$alias_attendee}`.`ID` = `{$alias_event}`.`post_id`
					WHERE
						`{$alias_event}`.`meta_value` = `{$wpdb->posts}`.`ID`
						AND `{$alias_event}`.`meta_key` IN ( {$event_meta_keys} )
						AND `{$alias_attendee}`.`post_type` IN ( {$attendee_types} )
					LIMIT 1
				)
			" );
	}

	/**
	 * Filters events to include only those that do not match the provided attendee(s).
	 *
	 * @since 4.12.1
	 *
	 * @param int|array $attendee_ids The attendee(s) to filter out.
	 */
	public function filter_by_attendee_not_in( $attendee_ids ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $this->decorated ) ) {
			$repo = $this->decorated;
		}

		global $wpdb;

		$attendee_ids = (array) $attendee_ids;
		$attendee_ids = array_map( 'absint', $attendee_ids );
		$attendee_ids = array_unique( $attendee_ids );
		$attendee_ids = implode( ', ', $attendee_ids );

		$alias_event    = 'sub_ticket_attendee_event';
		$alias_attendee = 'sub_ticket_attendee';

		$event_meta_keys = method_exists( $this, 'attendee_to_event_keys' ) ? $this->attendee_to_event_keys() : [];
		$event_meta_keys = array_map( [ $wpdb, '_real_escape' ], $event_meta_keys );
		$event_meta_keys = "'" . implode( "', '", $event_meta_keys ) . "'";

		$attendee_types = method_exists( $this, 'attendee_types' ) ? $this->attendee_types() : [];
		$attendee_types = array_map( [ $wpdb, '_real_escape' ], $attendee_types );
		$attendee_types = "'" . implode( "', '", $attendee_types ) . "'";

		$repo->where_clause( "
				NOT EXISTS (
					SELECT 1
					FROM
						`{$wpdb->postmeta}` AS `{$alias_event}`
					LEFT JOIN `{$wpdb->posts}` AS `{$alias_attendee}`
						ON `{$alias_attendee}`.`ID` = `{$alias_event}`.`post_id`
					WHERE
						`{$alias_event}`.`meta_value` = `{$wpdb->posts}`.`ID`
						AND `{$alias_event}`.`meta_key` IN ( {$event_meta_keys} )
						AND `{$alias_attendee}`.`post_type` IN ( {$attendee_types} )
						AND `{$alias_attendee}`.`ID` IN ( {$attendee_ids} )
					LIMIT 1
				)
			" );
	}

	/**
	 * Filters events to include only those that match the provided attendee(s).
	 *
	 * @since 4.12.1
	 *
	 * @param int|array $user_ids The user ID(s) to filter by.
	 */
	public function filter_by_attendee_user( $user_ids ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $this->decorated ) ) {
			$repo = $this->decorated;
		}

		global $wpdb;

		$user_ids = (array) $user_ids;
		$user_ids = array_map( 'absint', $user_ids );
		$user_ids = array_unique( $user_ids );
		$user_ids = implode( ', ', $user_ids );

		$alias_event = 'ticket_attendee_event';
		$alias_user  = 'ticket_attendee_user';

		$event_meta_keys = method_exists( $this, 'attendee_to_event_keys' ) ? $this->attendee_to_event_keys() : [];
		$event_meta_keys = array_map( [ $wpdb, '_real_escape' ], $event_meta_keys );
		$event_meta_keys = "'" . implode( "', '", $event_meta_keys ) . "'";

		$user_meta_key = method_exists( $this, 'attendee_to_user_key' ) ? $this->attendee_to_user_key() : 'null';
		$user_meta_key = $wpdb->_real_escape( $user_meta_key );

		// Join to the meta that relates attendees to events.
		$repo->filter_query->join( "
				LEFT JOIN `{$wpdb->postmeta}` AS `{$alias_event}`
					ON `{$alias_event}`.`meta_value` = `{$wpdb->posts}`.`ID`
			", $alias_event );

		// Join to the meta that relates users to attendees.
		$repo->filter_query->join( "
				LEFT JOIN `{$wpdb->postmeta}` AS `{$alias_user}`
					ON `{$alias_user}`.`post_id` = `{$alias_event}`.`post_id`
			", $alias_user );

		$repo->where_clause( "
				`{$alias_event}`.`meta_key` IN ( {$event_meta_keys} )
				AND `{$alias_user}`.`meta_key` = '{$user_meta_key}'
				AND `{$alias_user}`.`meta_value` IN ( {$user_ids} )
			" );
	}

	/**
	 * Filters events to include only those that do not match the provided attendee(s).
	 *
	 * @since 4.12.1
	 *
	 * @param int|array $user_ids The user ID(s) to filter out.
	 */
	public function filter_by_attendee_user_not_in( $user_ids ) {
		$repo = $this;

		// If the repo is decorated, use that.
		if ( ! empty( $this->decorated ) ) {
			$repo = $this->decorated;
		}

		global $wpdb;

		$user_ids = (array) $user_ids;
		$user_ids = array_map( 'absint', $user_ids );
		$user_ids = array_unique( $user_ids );
		$user_ids = implode( ', ', $user_ids );

		$alias_event = 'sub_ticket_attendee_event';
		$alias_user  = 'sub_ticket_attendee_user';

		$event_meta_keys = method_exists( $this, 'attendee_to_event_keys' ) ? $this->attendee_to_event_keys() : [];
		$event_meta_keys = array_map( [ $wpdb, '_real_escape' ], $event_meta_keys );
		$event_meta_keys = "'" . implode( "', '", $event_meta_keys ) . "'";

		$user_meta_key = method_exists( $this, 'attendee_to_user_key' ) ? $this->attendee_to_user_key() : 'null';
		$user_meta_key = $wpdb->_real_escape( $user_meta_key );

		$repo->where_clause( "
				NOT EXISTS (
					SELECT 1
					FROM
						`{$wpdb->postmeta}` AS `{$alias_event}`
					WHERE
						`{$alias_event}`.`meta_value` = `{$wpdb->posts}`.`ID`
						AND `{$alias_event}`.`meta_key` IN ( {$event_meta_keys} )
						AND	NOT EXISTS (
							SELECT 1
							FROM
								`{$wpdb->postmeta}` AS `{$alias_user}`
							WHERE
								`{$alias_user}`.`post_id` = `{$alias_event}`.`post_id`
								AND `{$alias_user}`.`meta_key` = '{$user_meta_key}'
								AND `{$alias_user}`.`meta_value` IN ( {$user_ids} )
							LIMIT 1
						)
					LIMIT 1
				)
			" );
	}
}

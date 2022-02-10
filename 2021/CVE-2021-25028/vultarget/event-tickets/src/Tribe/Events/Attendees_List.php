<?php
/**
 * Class that detects if given post
 * is displaying the Attendee List.
 */

namespace Tribe\Tickets\Events;

use Tribe__Tickets__Main;
use WP_Post;

/**
 * Class Attendees_List
 *
 * @since 4.12.0
 *
 * @package Tribe\Tickets\Events
 */
class Attendees_List {

	/**
	 * Meta key to hold the if the Post has Attendees List hidden.
	 *
	 * @since 4.12.0
	 *
	 * @var string
	 */
	const HIDE_META_KEY = '_tribe_hide_attendees_list';

	/**
	 * Meta name to control whether the Attendee List meta was changed by a shortcode in the content.
	 *
	 * @since 4.12.0
	 *
	 * @var string
	 */
	protected static $attendee_list_by_shortcode = 'tribe_tickets_attendee_list_triggered_by_shortcode';

	/**
	 * Determine if we need to hide the attendees list.
	 *
	 * @since 4.12.0
	 *
	 * @param int|WP_Post $post   The post object or ID.
	 * @param boolean     $strict Whether to strictly check the meta value.
	 *
	 * @return bool|null Whether the attendees list is hidden, null if not set at all with strict mode off.
	 */
	public static function is_hidden_on( $post, $strict = true ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		if ( ! $post instanceof WP_Post ) {
			return true;
		}

		$is_hidden = get_post_meta( $post->ID, self::HIDE_META_KEY, true );

		// By default non-existent meta will be an empty string.
		if ( '' === $is_hidden ) {
			if ( $strict ) {
				// Default to hide - which is unchecked but stored as true (1) in the Db for backwards compat.
				$is_hidden = true;
			} else {
				// There's no value set, so it's not explicitly set as hidden.
				$is_hidden = null;
			}
		} else {
			/**
			 * Invert logic for backwards compat.
			 */
			$is_hidden = ! $is_hidden;
		}

		/**
		 * Use this to filter and hide the Attendees List for a specific post or all of them.
		 *
		 * @since 4.12.0
		 *
		 * @param bool    $is_hidden Whether the attendees list is hidden.
		 * @param WP_Post $post      The post object.
		 */
		return apply_filters( 'tribe_tickets_hide_attendees_list', $is_hidden, $post );
	}

	/**
	 * Determine whether we should hide the optout option.
	 *
	 * @since 4.12.0
	 * @since 5.0.0 Removed setting `global $post`, accidentally affecting other code.
	 *
	 * @param bool $should_hide Whether the optout form should be hidden or not.
	 * @param int  $post_id     The post ID the ticket belongs to.
	 *
	 * @return bool Whether we should hide the optout option.
	 *
	 * @see \Tribe\Tickets\Events\Events_Service_Provider::hooks
	 */
	public function should_hide_optout( $should_hide, $post_id = 0 ) {
		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		$is_hidden_on = static::is_hidden_on( $post_id, false );

		if ( null !== $is_hidden_on ) {
			return $is_hidden_on;
		}

		$post = get_post( $post_id );

		// The setting isn't set yet, but let's double check until the setting gets migrated.
		return ! $this->is_showing_attendee_list_with_blocks( $post );
	}

	/**
	 * Determines whether this post is displaying the Attendees List.
	 *
	 * @since 4.12.0
	 *
	 * @param int|WP_Post $post The Post being checked.
	 *
	 * @return int|bool|void Void if didn't try to update. The return of update_post_meta otherwise.
	 *
	 * @see \Tribe\Tickets\Events\Events_Service_Provider::hooks
	 */
	public function maybe_update_attendee_list_hide_meta( $post ) {
		if ( is_numeric( $post ) ) {
			$post = get_post( $post );
		}

		// Early bail: Invalid post.
		if ( ! $post instanceof WP_Post ) {
			return null;
		}

		// Early bail: is an autosave or auto-draft.
		if ( wp_is_post_autosave( $post ) || wp_is_post_revision( $post ) ) {
			return null;
		}

		/** @var Tribe__Tickets__Main $main */
		$main = tribe( 'tickets.main' );

		$post_types_allowed_to_have_tickets = $main->post_types();

		// Early bail: This post type can't have tickets.
		if ( ! in_array( $post->post_type, $post_types_allowed_to_have_tickets, true ) ) {
			return null;
		}

		/** @var \Tribe__Editor $editor */
		$editor = tribe( 'editor' );

		if ( ! $editor->is_events_using_blocks() ) {
			return;
		}

		$this->track_shortcode_driven_meta( $post );

		$is_showing_attendee_list = $this->is_showing_attendee_list_with_blocks( $post );

		/**
		 * Returns true if the post is displaying a list of attendees.
		 *
		 * You can use this filter to let the system know that you're displaying
		 * the Attendee List in some other way.
		 *
		 * @since 4.12.0
		 *
		 * @param bool    $is_showing_attendee_list Whether the post is showing the attendee list or not.
		 * @param WP_Post $post                     The WP_Post object being checked.
		 */
		$is_showing_attendee_list = (bool) apply_filters(
			'tribe_tickets_event_is_showing_attendee_list',
			$is_showing_attendee_list,
			$post
		);

		return update_post_meta( $post->ID, self::HIDE_META_KEY, (int) $is_showing_attendee_list );
	}

	/**
	 * Get list of public attendees for display.
	 *
	 * @since 4.12.0
	 *
	 * @param WP_Post|int $post_id Post object or ID.
	 * @param  int        $limit   Limit of attendees to be retrieved from database.
	 *
	 * @return array List of public attendees for display.
	 */
	public function get_attendees_for_post( $post_id, $limit = - 1 ) {
		$post = get_post( $post_id );

		if ( ! $post instanceof WP_Post ) {
			return [];
		}

		$args = [
			'by' => [
				// Exclude people who have opted out or not specified optout.
				'optout' => 'no_or_none',
				// Only include public attendees.
				'post_status' => 'publish',
				// Only include RSVP status yes.
				'rsvp_status__or_none' => 'yes',
				// Only include public order statuses.
				'order_status' => 'public',
			],
		];

		/**
		 * Allow for adjusting the limit of attendees fetched from the database for the front-end "Who's Coming?" list.
		 *
		 * @since 4.10.6
		 *
		 * @param int $limit_attendees Number of attendees to retrieve. Default is no limit -1.
		 */
		$limit_attendees = (int) apply_filters( 'tribe_tickets_attendees_list_limit_attendees', $limit );

		if ( 0 < $limit_attendees ) {
			$args['per_page'] = $limit_attendees;
		}

		$attendees  = \Tribe__Tickets__Tickets::get_event_attendees( $post->ID, $args );
		$emails     = [];

		// Bail if there are no attendees
		if ( empty( $attendees ) || ! is_array( $attendees ) ) {
			return [];
		}

		$attendees_for_display = [];

		foreach ( $attendees as $key => $attendee ) {
			// Skip when we already have another email like this one.
			if ( isset( $emails[ $attendee['purchaser_email'] ] ) ) {
				continue;
			}

			$emails[ $attendee['purchaser_email'] ] = true;

			$attendees_for_display[] = $attendee;
		}

		return $attendees_for_display;
	}

	/**
	 * This keeps track of whether the Attendee List is being displayed becase of a shortcode
	 * in the content, and acts accordingly if said shortcode is removed.
	 *
	 * @since 4.12.0
	 *
	 * @param WP_Post $post The Post being checked.
	 *
	 * @return void
	 */
	private function track_shortcode_driven_meta( WP_Post $post ) {
		$is_visible_by_meta                  = ! static::is_hidden_on( $post );
		$has_attendee_list_shortcode         = has_shortcode( $post->post_content, 'tribe_attendees_list' );
		$has_attendee_list_by_shortcode_meta = get_post_meta( $post->ID, self::$attendee_list_by_shortcode, true ) === 'yes';

		/*
		 * If what triggers the Attendee List to display is a shortcode in the content,
		 * let's save this piece of information so that we revert it when the shortcode is removed.
		 */
		if ( ! $is_visible_by_meta && $has_attendee_list_shortcode ) {
			update_post_meta( $post->ID, self::$attendee_list_by_shortcode, 'yes' );
		}

		/*
		 * The shortcode that triggered the Attendee List to display is no longer in the content.
		 * Thus, we shall update the "Attendee List" meta to "hidden" again.
		 */
		if ( $has_attendee_list_by_shortcode_meta && ! $has_attendee_list_shortcode ) {
			update_post_meta( $post->ID, self::$attendee_list_by_shortcode, 'no' );

			add_filter( 'tribe_tickets_event_is_showing_attendee_list', '__return_false', 9 );
		}
	}

	/**
	 * Determine whether we are showing the attendee list with the block editor.
	 *
	 * @since 4.12.0
	 *
	 * @param WP_Post $post The Post being checked.
	 *
	 * @return bool Whether we are showing the attendee list with the block editor.
	 */
	protected function is_showing_attendee_list_with_blocks( WP_Post $post ) {
		$has_attendee_list_block     = function_exists( 'has_block' ) ? has_block( 'tribe/attendees', $post ) : false;
		$has_attendee_list_shortcode = has_shortcode( $post->post_content, 'tribe_attendees_list' );

		return $has_attendee_list_block || $has_attendee_list_shortcode;
	}

	/**
	 * Determine whether we are showing the attendee list with the block editor.
	 *
	 * @since 4.12.0
	 *
	 * @param WP_Post $post The Post being checked.
	 *
	 * @return bool Whether we are showing the attendee list with the block editor.
	 */
	protected function is_showing_attendee_list_with_classical_editor( WP_Post $post ) {
		$is_visible_by_meta          = ! static::is_hidden_on( $post );
		$has_attendee_list_shortcode = has_shortcode( $post->post_content, 'tribe_attendees_list' );

		return $is_visible_by_meta || $has_attendee_list_shortcode;
	}

	/**
	 * Get total number of attendees that are confirmed.
	 *
	 * @since 5.1.5
	 *
	 * @param int|string $post_id The post or event that is checked.
	 *
	 * @return int Total number of attendees attending the event.
	 */
	public function get_attendance_counts( $post_id ) {
		$attendees_orm = tribe_attendees();

		$attendees_orm->where( 'event', $post_id )
		              ->where( 'rsvp_status__or_none', 'yes' );

		return $attendees_orm->found();
	}
}

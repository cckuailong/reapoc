<?php

namespace TEC\Tickets;

/**
 * Class Event
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets
 */
class Event {

	/**
	 * Value stored for the Events from TEC.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	protected static $post_type = 'tribe_events';

	/**
	 * Gets the TEC events CPT, will fallback into the Static variable on this class, but will try to pull from
	 * TEC main class constant first.
	 *
	 * @since 5.1.9
	 *
	 *
	 * @return string
	 */
	public static function get_post_type() {
		if ( class_exists( '\Tribe__Events__Main' ) ) {
			return \Tribe__Events__Main::POSTTYPE;
		}
		return static::$post_type;
	}

}
<?php
/**
 * Provides a shortcode which generates a list of events that the current user
 * has indicated they will attend. Works for all ticketing providers.
 *
 * Simple example (will assume the current user as the person of interest):
 *
 *     [tribe-user-event-confirmations]
 *
 * Example specifying a user:
 *
 *     [tribe-user-event-confirmations user="512"]
 *
 * Example specifying a limit to the number of events which should be returned:
 *
 *     [tribe-user-event-confirmations limit="16"]
 */
class Tribe__Tickets__Shortcodes__User_Event_Confirmation_List {
	protected $shortcode_name = 'tribe-user-event-confirmations';
	protected $params = [];

	/**
	 * Registers a user event confirmation list shortcode
	 *
	 * @since 4.5.2 moved the $shortcode_name parameter to a protected property
	 *        as it's needs to be used in other methods
	 */
	public function __construct( ) {
		/**
		 * Provides an opportunity to modify the registered shortcode name
		 * for the frontend attendee list.
		 *
		 * @param string $shortcode_name
		 */
		$this->shortcode_name = apply_filters( 'tribe_tickets_shortcodes_attendee_list_name', $this->shortcode_name );

		add_shortcode( $this->shortcode_name, [ $this, 'generate' ] );
	}

	/**
	 * Generate the user event confirmation list.
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public function generate( $params ) {
		$this->set_params( $params );

		ob_start();

		if ( ! is_user_logged_in() ) {
			include Tribe__Tickets__Templates::get_template_hierarchy( 'shortcodes/my-attendance-list-logged-out' );
		} else {
			$this->generate_attendance_list();
		}

		return ob_get_clean();
	}

	/**
	 * Given a set of parameters, ensure that the expected keys are present
	 * and set to reasonable defaults where necessary.
	 *
	 * @param $params
	 */
	protected function set_params( $params ) {
		/**
		 * Allow filtering of the default limit for the [tribe-user-event-confirmations] shortcode.
		 *
		 * @since 4.12.1
		 *
		 * @param int $default_limit The default limit to use.
		 */
		$default_limit = apply_filters( 'tribe_tickets_shortcodes_attendee_list_limit', 100 );

		$this->params = shortcode_atts( [
			'limit' => $default_limit,
			'user'  => get_current_user_id()
		], $params, $this->shortcode_name );

		$this->params['limit'] = (int) $this->params['limit'];
		$this->params['user']  = absint( $this->params['user'] );
	}

	/**
	 * Gets the user's attendance data and passes it to the relevant view.
	 */
	protected function generate_attendance_list() {
		$event_ids = $this->get_upcoming_attendances();

		include Tribe__Tickets__Templates::get_template_hierarchy( 'shortcodes/my-attendance-list' );
	}

	/**
	 * Get list of upcoming event IDs for which the specified user is an attendee.
	 *
	 * If attending an Event (The Events Calendar), this list will only display upcoming events that have not yet ended. If attending another type of post (e.g. Post or Page), this list will display all corresponding posts.
	 *
	 * @return array
	 */
	protected function get_upcoming_attendances() {
		/** @var \Tribe\Tickets\Repositories\Post_Repository $post_orm */
		$post_orm = tribe( 'tickets.post-repository' );

		// Limit to a specific number of events.
		if ( 0 < $this->params['limit'] ) {
			$post_orm->per_page( $this->params['limit'] );
		}

		// Order by event date.
		$post_orm->order_by( 'event_date', 'ASC' );

		// Events that have not yet ended.
		$post_orm->by( 'ends_after', current_time( 'mysql' ) );

		// Events with attendees by the specific user ID.
		$post_orm->by( 'attendee_user', $this->params['user'] );

		return $post_orm->get_ids();
	}
}

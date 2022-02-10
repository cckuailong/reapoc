<?php


/**
 * Class Tribe__Tickets__Admin__Screen_Options__Attendees
 *
 * Handles the Attendees report table screen options.
 */
class Tribe__Tickets__Admin__Screen_Options__Attendees {
	/**
	 * @var string The user option that will store how many attendees should be shown per page.
	 */
	public static $per_page_user_option = 'event_tickets_attendees_per_page';

	/**
	 * @var string The screen id these screen options should render on.
	 */
	protected $screen_id;

	/**
	 * @var WP_Screen Either the globally defined WP_Screen instance or an injected dependency.
	 */
	protected $screen;

	/**
	 * Tribe__Tickets__Admin__Screen_Options__Attendees constructor.
	 *
	 * @param string         $screen_id The slug of the screen this screen options should apply to.
	 * @param WP_Screen|null $screen    An injectable instance of the WP_Screen object.
	 */
	public function __construct( $screen_id, $screen = null ) {
		$this->screen_id = $screen_id;
		$this->screen    = $screen;
	}

	/**
	 * Adds the screen options required on the current screen.
	 *
	 * @return bool Whether the screen options were added or not.
	 */
	public function add_options() {
		$this->screen = $this->screen ? $this->screen : get_current_screen();

		if ( ! is_object( $this->screen ) || $this->screen->id !== $this->screen_id ) {
			return false;
		}

		$this->add_column_headers_options();

		return true;
	}

	protected function add_column_headers_options() {
		add_filter( "manage_{$this->screen->id}_columns", array( $this, 'filter_manage_columns' ) );
	}

	/**
	 * Adds the "Columns" screen option by simply listing the column headers and titles.
	 *
	 * @param array $columns The Attendee table columns and titles, def. empty array.
	 *
	 * @return array
	 */
	public function filter_manage_columns( array $columns ) {
		return tribe( 'tickets.admin.attendees_table' )->get_table_columns();
	}

	/**
	 * Filters the save operations of screen options to save the ones the class manages.
	 *
	 * @since 4.7
	 *
	 * @param bool $status Whether the option should be saved or not.
	 * @param string $option The user option slug.
	 * @param mixed $value The user option value.
	 *
	 * @return bool|mixed Either `false` if the user option is not one managed by the class or the user
	 *                    option value to save.
	 */
	public function filter_set_screen_options( $status, $option, $value ) {
		if ( $option === self::$per_page_user_option ) {
			return $value;
		}

		return $status;
	}
}

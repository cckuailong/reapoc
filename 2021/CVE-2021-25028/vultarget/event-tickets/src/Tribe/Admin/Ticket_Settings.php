<?php

/**
 * Manages the admin settings UI in relation to ticket configuration.
 */
class Tribe__Tickets__Admin__Ticket_Settings {

	/**
	 * Sets up the display of timezone-related settings and listeners to deal with timezone-update
	 * requests (which are initiated from within the settings screen).
	 */
	public function __construct() {
		add_action( 'tribe_settings_do_tabs', [ $this, 'settings_ui' ] );
	}

	/**
	 * Loads the ticket settings from an admin-view file and returns them as an array.
	 *
	 * @since 4.10.9 Use customizable ticket name functions.
	 */
	public function settings_ui() {
		$settings = $this->get_settings_array();

		new Tribe__Settings_Tab( 'event-tickets', tribe_get_ticket_label_plural( 'settings_tab' ), $settings );
	}

	/**
	 * Loads the timezone settings from an admin-view file and returns them as an array.
	 *
	 * @return array
	 */
	protected function get_settings_array() {
		$plugin_path = Tribe__Tickets__Main::instance()->plugin_path;
		include $plugin_path . 'src/admin-views/tribe-options-tickets.php';

		/** @var array $tickets_tab Set in the file included above*/
		return $tickets_tab;
	}
}

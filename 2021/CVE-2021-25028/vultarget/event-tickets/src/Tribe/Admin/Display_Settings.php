<?php

/**
 * Manages the admin settings UI in relation to display configuration.
 *
 * @since 4.12.3
 */
class Tribe__Tickets__Admin__Display_Settings {

	/**
	 * Add display settings on the Events > Settings > Display tab.
	 *
	 * @since 4.12.3
	 */
	public function hook() {
		add_filter( 'tribe_display_settings_tab_fields', [ $this, 'add_display_settings' ] );
	}

	/**
	 * Add display settings for Event Tickets.
	 *
	 * @since 4.12.3
	 *
	 * @param array $settings List of display settings.
	 *
	 * @return array List of display settings.
	 */
	public function add_display_settings( $settings ) {
		$plugin_path = Tribe__Tickets__Main::instance()->plugin_path;
		include $plugin_path . 'src/admin-views/tribe-options-display.php';

		return $settings;
	}
}

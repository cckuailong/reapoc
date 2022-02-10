<?php
/**
 * Class Tribe__Tickets__Admin__Screen_Options
 *
 * Hooks Screen Options handlers for supported table screens.
 *
 * This class does not contain the business logic, it only hooks the classes
 * that will handle the logic.
 */
class Tribe__Tickets__Admin__Screen_Options {

	/**
	 * Hooks the specific classes dealing with Tickets related Screen Options to the required
	 * filters and actions
	 */
	public function hook() {
		$attendees_page = 'admin_page_tickets-attendees';
		$screen_options = new Tribe__Tickets__Admin__Screen_Options__Attendees( $attendees_page );
		add_action( "load-{$attendees_page}", array( $screen_options, 'add_options' ) );
		add_filter( 'set-screen-option', array( $screen_options, 'filter_set_screen_options' ), 10, 3 );
	}
}

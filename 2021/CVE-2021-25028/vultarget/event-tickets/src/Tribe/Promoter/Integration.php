<?php

/**
 * Class Tribe__Tickets__Promoter__Integration
 *
 * Class used to handle Event Tickets integration and customizations needed for Promoter.
 *
 * @since 4.10.2
 */
class Tribe__Tickets__Promoter__Integration {

	/**
	 * Hooks for integration and customizations needed for Promoter.
	 *
	 * @since 4.10.2
	 */
	public function hook() {
		add_action( 'rest_api_init', [ $this, 'maybe_show_rest_api_attendee_data' ], 11 );
	}

	/**
	 * Maybe show REST API Attendee data for Tickets if Promoter is active and authorized.
	 *
	 * @since 4.10.2
	 */
	public function maybe_show_rest_api_attendee_data() {
		/** @var Tribe__Promoter__PUE $pue */
		$pue = tribe( 'promoter.pue' );

		/** @var Tribe__Promoter__Connector $connector */
		$connector = tribe( 'promoter.connector' );

		// Only add our hook if Promoter has a license key setup and this user is authorized.
		if ( ! $pue->has_license_key() || ! $connector->is_user_authorized() ) {
			return;
		}

		// Attendee data is needed by Promoter requests.
		add_filter( 'tribe_tickets_rest_api_always_show_attendee_data', '__return_true', 99 );
	}

}
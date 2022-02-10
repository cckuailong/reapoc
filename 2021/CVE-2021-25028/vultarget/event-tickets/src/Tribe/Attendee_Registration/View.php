<?php

/**
 * Class Tribe__Tickets__Attendee_Registration__View
 */
class Tribe__Tickets__Attendee_Registration__View extends Tribe__Tickets__Editor__Template {
	/**
	 * Display the Attendee Info page when the correct permalink is loaded.
	 *
	 * @since 4.9
	 * @since 4.12.0 Removed $content and $context parameters
	 *
	 * @return string The resulting template content
	 */
	public function display_attendee_registration_page() {
		return $this->display_attendee_registration_shortcode();
	}

	/**
	 * Render the Attendee Info shortcode.
	 *
	 * @since 4.12.0
	 * @since 4.12.3 Get provider slug more consistently.
	 *
	 * @return string The resulting template content
	 */
	public function display_attendee_registration_shortcode() {
		try {
			/* @var Tribe__Tickets_Plus__Attendee_Registration__View $view */
			$view = tribe( 'tickets-plus.attendee-registration.view' );

			return $view->get_page_content();
		} catch ( Exception $exception ) {
			return '';
		}
	}

	/**
	 * Get the provider Cart URL.
	 *
	 * @since 4.9
	 *
	 * @param string $provider Provider identifier.
	 *
	 * @return bool|string
	 */
	public function get_cart_url( $provider ) {
		try {
			/* @var Tribe__Tickets_Plus__Attendee_Registration__View $view */
			$view = tribe( 'tickets-plus.attendee-registration.view' );

			return $view->get_cart_url( $provider );
		} catch ( Exception $exception ) {
			return '';
		}
	}


	/**
	 * Get the cart provider class/object.
	 *
	 * @since 4.11.0
	 * @since 4.12.3 Check if provider is a proper object and is active.
	 *
	 * @param string $provider A string indicating the desired provider.
	 *
	 * @return boolean|object The provider object or boolean false if none found.
	 */
	public function get_cart_provider( $provider ) {
		try {
			/* @var Tribe__Tickets_Plus__Attendee_Registration__View $view */
			$view = tribe( 'tickets-plus.attendee-registration.view' );

			return $view->get_cart_provider( $provider );
		} catch ( Exception $exception ) {
			return '';
		}
	}

	/**
	 * Given a provider, get the class to be applied to the attendee registration form.
	 *
	 * @since 4.10.4
	 * @since 4.12.3 Consolidate getting provider.
	 *
	 * @param string|Tribe__Tickets__Tickets $provider The provider/attendee object name indicating ticket provider.
	 *
	 * @return string The class string or empty string if provider not found or not active.
	 */
	public function get_form_class( $provider ) {
		try {
			/* @var Tribe__Tickets_Plus__Attendee_Registration__View $view */
			$view = tribe( 'tickets-plus.attendee-registration.view' );

			return $view->get_form_class( $provider );
		} catch ( Exception $exception ) {
			return '';
		}
	}
}

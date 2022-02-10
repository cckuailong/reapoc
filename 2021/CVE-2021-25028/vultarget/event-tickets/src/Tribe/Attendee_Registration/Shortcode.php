<?php

/**
 * Provides shortcodes for the attendee registration template.
 *
 * @since 4.10.2
 */
class Tribe__Tickets__Attendee_Registration__Shortcode {
	protected $shortcode_name = 'tribe_attendee_registration';
	protected $params         = [];

	public function hook() {
		// block editor has a fit if we don't bail on the admin...don't really need them in other places?
		if ( is_admin() || tribe( 'context' )->doing_cron() ) {
			return;
		}

		add_shortcode( $this->shortcode_name, [ $this, 'render' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'maybe_enqueue_scripts'] );
	}

	public function maybe_enqueue_scripts() {
		if (
			is_archive()
			|| is_admin()
		) {
			return;
		}

		$shortcode_page = (int) tribe_get_option( 'ticket-attendee-page-id', 0 );

		// Option is not set, don't enqueue scripts.
		if ( ! $shortcode_page ) {
			return;
		}

		$page = get_queried_object();

		// Not on a shortcode page, don't enqueue scripts.
		if ( ! $page || ! $page instanceof WP_Post || $shortcode_page !== $page->ID ) {
			return;
		}

		// Enqueue styles and scripts for this page.
		tribe_asset_enqueue_group( 'tribe-tickets-registration-page' );
	}

	/**
	 * Renders the shortcode AR page.
	 *
	 * @since 4.10.2
	 *
	 * @return string
	 */
	public function render() {
		/** @var \Tribe\Tickets\Plus\Attendee_Registration\View $view */
		$view = tribe( 'tickets-plus.attendee-registration.view' );

		return $view->get_page_content();
	}

}

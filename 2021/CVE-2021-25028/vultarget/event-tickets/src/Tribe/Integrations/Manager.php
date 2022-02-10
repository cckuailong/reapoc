<?php

/**
 * Class Tribe__Tickets__Integrations__Manager
 *
 * Loads and manages the third-party plugins integration implementations.
 *
 * @since 4.11.5
 */
class Tribe__Tickets__Integrations__Manager {

	/**
	 * The current instance of the object.
	 *
	 * @since 4.11.5
	 *
	 * @var Tribe__Tickets__Integrations__Manager
	 */
	protected static $instance;

	/**
	 * The class singleton constructor.
	 *
	 * @since 4.11.5
	 *
	 * @return Tribe__Tickets__Integrations__Manager
	 */
	public static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Conditionally loads the classes needed to integrate with third-party plugins.
	 *
	 * Third-party plugin integration classes and methods will be loaded only if
	 * supported plugins are activated.
	 *
	 * @since 4.11.5
	 */
	public function load_integrations() {
		$this->load_freemius();
	}

	/**
	 * Loads our Freemius integration
	 *
	 * @since 4.11.5
	 */
	private function load_freemius() {
		tribe_singleton( 'tickets.integrations.freemius', new Tribe__Tickets__Integrations__Freemius );
	}
}

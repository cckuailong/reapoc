<?php
/**
 * Handles registering and setup for legacy compatibility from Ticket Commerce towards the old Tribe Commerce.
 *
 * @since 5.1.6
 *
 * @package TEC\Tickets\Commerce
 */

namespace TEC\Tickets\Commerce;

use \tad_DI52_ServiceProvider;

/**
 * Class Legacy Compat.
 *
 * @since 5.1.6
 *
 * @package TEC\Tickets\Commerce
 */
class Legacy_Compat extends tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.1.6
	 */
	public function register() {
//		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required to handle legacy compatibility.
	 *
	 * @since 5.1.10
	 */
	protected function add_actions() {

	}

	/**
	 * Adds the filters required to handle legacy compatibility.
	 *
	 * @since 5.1.10
	 */
	protected function add_filters() {
		add_filter( 'tribe_events_tickets_module_name', [ $this, 'set_legacy_module_name' ] );
	}

	/**
	 * Show the legacy PayPal as not recommended.
	 *
	 * @since 5.1.10
	 *
	 * @param $name string Name of the provider.
	 *
	 * @return string
	 */
	public function set_legacy_module_name( $name ) {
		return $name != 'Tribe Commerce' ? $name : __( 'Tribe Commerce ( Legacy PayPal, not recommended )', 'event-tickets' );
	}

	/**
	 * Tribe Commerce stored currency codes in a different option key. If we can't find values from Tickets Commerce,
	 * try to load values from Tribe Commerce before falling back to the default - and update Tickets Commerce with
	 * what we found.
	 *
	 * @since TBD
	 *
	 * @return string
	 */
	public function maybe_load_currency_code_from_tribe_commerce( $value ) {

		$currency = Currency::get_currency_code();

		if ( ! empty( $currency ) ) {
			return $currency;
		}

		return Currency::get_currency_code_fallback();
	}
}
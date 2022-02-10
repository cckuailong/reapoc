<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( TEC\Tickets\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'tickets.hooks' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( TEC\Tickets\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'tickets..hooks' ), 'some_method' ] );
 *
 * @since 5.1.6
 *
 * @package TEC\Tickets
 */

namespace TEC\Tickets;

use \tad_DI52_ServiceProvider;
use TEC\Tickets\Commerce\Payments_Tab;

/**
 * Class Hooks.
 *
 * @since 5.1.6
 *
 * @package TEC\Tickets
 */
class Hooks extends tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.1.6
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by each Tickets component.
	 *
	 * @since 5.1.6
	 */
	protected function add_actions() {
		add_action( 'tribe_settings_do_tabs', [ tribe( Payments_Tab::class ), 'register_tab' ], 15 );
		add_action( 'tribe_settings_after_save_' . Payments_Tab::$slug, [ $this, 'generate_payments_pages' ] );
	}

	/**
	 * Generate TicketsCommerce Pages.
	 *
	 * @since 5.2.1
	 */
	public function generate_payments_pages() {
		$this->container->make( Payments_Tab::class )->maybe_generate_pages();
	}

	/**
	 * Adds the filters required by each Tickets component.
	 *
	 * @since 5.1.6
	 */
	protected function add_filters() {
	}
}
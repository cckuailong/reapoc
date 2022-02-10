<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * remove_filter( 'some_filter', [ tribe( TEC\Tickets\Commerce\Gateways\Manual\Hooks::class ), 'some_filtering_method' ] );
 * remove_filter( 'some_filter', [ tribe( 'tickets.commerce.gateways.manual.hooks' ), 'some_filtering_method' ] );
 *
 * To remove an action:
 * remove_action( 'some_action', [ tribe( TEC\Tickets\Commerce\Gateways\Manual\Hooks::class ), 'some_method' ] );
 * remove_action( 'some_action', [ tribe( 'tickets.commerce.gateways.manual.hooks' ), 'some_method' ] );
 *
 * @since   5.2.0
 *
 * @package TEC\Tickets\Commerce\Gateways\Manual
 */

namespace TEC\Tickets\Commerce\Gateways\Manual;

use TEC\Tickets\Commerce\Gateways\Manual\Gateway;

use Tribe__Utils__Array as Arr;


/**
 * Class Hooks.
 *
 * @since   5.2.0
 *
 * @package TEC\Tickets\Commerce\Gateways\Manual
 */
class Hooks extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 5.2.0
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by each Tickets Commerce component.
	 *
	 * @since 5.2.0
	 */
	protected function add_actions() {

	}

	/**
	 * Adds the filters required by each Tickets Commerce component.
	 *
	 * @since 5.2.0
	 */
	protected function add_filters() {
		add_filter( 'tec_tickets_commerce_gateways', [ $this, 'filter_add_gateway' ], 10, 2 );
	}

	/**
	 * Add this gateway to the list of available.
	 *
	 * @since 5.2.0
	 *
	 * @param array $gateways List of available gateways.
	 *
	 * @return array
	 */
	public function filter_add_gateway( array $gateways = [] ) {
		return $this->container->make( Gateway::class )->register_gateway( $gateways );
	}
}

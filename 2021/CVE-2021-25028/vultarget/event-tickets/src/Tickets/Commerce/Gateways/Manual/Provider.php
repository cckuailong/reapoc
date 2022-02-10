<?php

namespace TEC\Tickets\Commerce\Gateways\Manual;

/**
 * Service provider for the Tickets Commerce: Manual Gateway.
 *
 * @since   5.2.0
 * @package TEC\Tickets\Commerce\Gateways\Manual
 */
class Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Register the provider singletons.
	 *
	 * @since 5.2.0
	 */
	public function register() {
		$this->container->singleton( Gateway::class );
		$this->container->singleton( Order::class );

		$this->register_hooks();
		$this->register_assets();
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for this Service Provider
	 *
	 * @since 5.2.0
	 */
	protected function register_assets() {
		$assets = new Assets( $this->container );
		$assets->register();

		$this->container->singleton( Assets::class, $assets );
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for this Service Provider.
	 *
	 * @since 5.2.0
	 */
	protected function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container
		$this->container->singleton( Hooks::class, $hooks );
	}
}

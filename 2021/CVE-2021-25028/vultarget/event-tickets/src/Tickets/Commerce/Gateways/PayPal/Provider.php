<?php

namespace TEC\Tickets\Commerce\Gateways\PayPal;

/**
 * Service provider for the Tickets Commerce: PayPal Commerce gateway.
 *
 * @since   5.1.6
 * @package TEC\Tickets\Commerce\Gateways\PayPal
 */
class Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Register the provider singletons.
	 *
	 * @since 5.1.6
	 */
	public function register() {
		$this->container->singleton( Gateway::class );

		$this->register_hooks();
		$this->register_assets();

		$this->container->singleton( Merchant::class, Merchant::class, [ 'init' ] );

		$this->container->singleton( Refresh_Token::class );
		$this->container->singleton( Client::class );
		$this->container->singleton( Signup::class );
		$this->container->singleton( Status::class );

		$this->container->singleton( Webhooks::class );
		$this->container->singleton( Webhooks\Events::class );
		$this->container->singleton( Webhooks\Handler::class );

		$this->register_endpoints();
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for this Service Provider
	 *
	 * @since 5.1.6
	 */
	protected function register_assets() {
		$assets = new Assets( $this->container );
		$assets->register();

		$this->container->singleton( Assets::class, $assets );
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for this Service Provider.
	 *
	 * @since 5.1.6
	 */
	protected function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container
		$this->container->singleton( Hooks::class, $hooks );
	}

	/**
	 * Register REST API endpoints.
	 *
	 * @since 5.1.6
	 */
	public function register_endpoints() {
		$hooks = new REST( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container
		$this->container->singleton( REST::class, $hooks );
	}

}

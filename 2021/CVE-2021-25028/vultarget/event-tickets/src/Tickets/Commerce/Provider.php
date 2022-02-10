<?php
/**
 * The main service provider for the Tickets Commerce.
 *
 * @since   5.1.6
 * @package TEC\Tickets\Commerce
 */

namespace TEC\Tickets\Commerce;

use tad_DI52_ServiceProvider;
use TEC\Tickets\Commerce\Gateways;
use \Tribe__Tickets__Main as Tickets_Plugin;


/**
 * Service provider for the Tickets Commerce.
 *
 * @since   5.1.6
 * @package TEC\Tickets\Commerce
 */
class Provider extends tad_DI52_ServiceProvider {

	/**
	 * Register the provider singletons.
	 *
	 * @since 5.1.6
	 */
	public function register() {

		$this->container->register( Payments_Tab::class );
		$this->register_assets();

		// Specifically prevents anything else from loading.
		if ( ! tec_tickets_commerce_is_enabled() ) {
			return;
		}

		$this->register_hooks();

		$this->load_functions();

		$this->register_legacy_compat();

		// Register the SP on the container.
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'tickets.commerce.provider', $this );

		// Register all singleton classes.
		$this->container->singleton( Gateways\Manager::class, Gateways\Manager::class );

		$this->container->singleton( Reports\Attendance_Totals::class );
		$this->container->singleton( Reports\Attendees::class );
		$this->container->singleton( Reports\Orders::class );
		$this->container->singleton( Admin_Tables\Orders::class );
		$this->container->singleton( Admin_Tables\Attendees::class );

		$this->container->singleton( Editor\Metabox::class );
		$this->container->singleton( Notice_Handler::class );

		$this->container->singleton( Module::class );
		$this->container->singleton( Attendee::class );
		$this->container->singleton( Order::class );
		$this->container->singleton( Ticket::class );
		$this->container->singleton( Cart::class );
		$this->container->singleton( Cart\Unmanaged_Cart::class );

		$this->container->singleton( Checkout::class );
		$this->container->singleton( Settings::class );
		$this->container->singleton( Tickets_View::class );

		$this->container->register( Status\Status_Handler::class );
		$this->container->register( Flag_Actions\Flag_Action_Handler::class );

		// Register Compatibility Classes
		$this->container->singleton( Compatibility\Events::class );

		// Load any external SPs we might need.
		$this->container->register( Gateways\PayPal\Provider::class );
		$this->container->register( Gateways\Manual\Provider::class );

		// Register and add hooks for admin notices.
		$this->container->register( Admin\Notices::class );
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for Tickets Commerce.
	 *
	 * @since 5.1.6
	 */
	protected function register_assets() {
		$assets = new Assets( $this->container );
		$assets->register();

		$this->container->singleton( Assets::class, $assets );
	}

	/**
	 * Include All function files.
	 *
	 * @since 5.1.9
	 */
	protected function load_functions() {
		$path = Tickets_Plugin::instance()->plugin_path;

		require_once $path . 'src/functions/commerce/orm.php';
		require_once $path . 'src/functions/commerce/orders.php';
		require_once $path . 'src/functions/commerce/attendees.php';
		require_once $path . 'src/functions/commerce/tickets.php';
	}

	/**
	 * Registers the provider handling all the 1st level filters and actions for Tickets Commerce.
	 *
	 * @since 5.1.6
	 */
	protected function register_hooks() {
		$hooks = new Hooks( $this->container );
		$hooks->register();

		// Allow Hooks to be removed, by having the them registered to the container
		$this->container->singleton( Hooks::class, $hooks );
		$this->container->singleton( 'tickets.commerce.hooks', $hooks );
	}

	/**
	 * Registers the provider handling compatibility with legacy payments from Tribe Tickets Commerce.
	 *
	 * @since 5.1.6
	 */
	protected function register_legacy_compat() {
		$v1_compat = new Legacy_Compat( $this->container );
		$v1_compat->register();

		$this->container->singleton( Legacy_Compat::class, $v1_compat );
		$this->container->singleton( 'tickets.commerce.legacy-compat', $v1_compat );
	}
}

<?php

namespace Tribe\Tickets\Promoter;

use tad_DI52_ServiceProvider;
use Tribe\Tickets\Promoter\Triggers\Dispatcher;
use Tribe\Tickets\Promoter\Triggers\Factory;
use Tribe\Tickets\Promoter\Triggers\Observers\Commerce;
use Tribe\Tickets\Promoter\Triggers\Observers\RSVP;
use Tribe__Tickets__Promoter__Integration;
use Tribe__Tickets__Promoter__Observer;

/**
 * Class Tribe__Tickets__Service_Providers__Promoter
 *
 * @since 4.12.3
 */
class Service_Provider extends tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.12.3
	 */
	public function register() {
		$this->container->singleton( Tribe__Tickets__Promoter__Integration::class, Tribe__Tickets__Promoter__Integration::class, [ 'hook' ] );
		$this->container->singleton( 'tickets.promoter.integration', Tribe__Tickets__Promoter__Integration::class, [ 'hook' ] );

		$this->container->singleton( Tribe__Tickets__Promoter__Observer::class, Tribe__Tickets__Promoter__Observer::class, [ 'hook' ] );
		$this->container->singleton( 'tickets.promoter.observer', Tribe__Tickets__Promoter__Observer::class, [ 'hook' ] );

		$this->container->singleton( Factory::class, Factory::class, [ 'hook' ] );
		$this->container->singleton( Dispatcher::class, Dispatcher::class, [ 'hook' ] );
		$this->container->singleton( Commerce::class, Commerce::class, [ 'hook' ] );
		$this->container->singleton( RSVP::class, RSVP::class, [ 'hook' ] );

		$this->load();
	}

	/**
	 * Any hooking for any class needs happen here.
	 *
	 * In place of delegating the hooking responsibility to the single classes they are all hooked here.
	 *
	 * @since 4.12.3
	 */
	protected function load() {
		tribe( 'tickets.promoter.integration' );
		tribe( 'tickets.promoter.observer' );
		tribe( Factory::class );
		tribe( Dispatcher::class );
		tribe( RSVP::class );
		tribe( Commerce::class );
	}
}

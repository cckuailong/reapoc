<?php
/**
 * Class Tribe__Tickets__Service_Provider
 *
 * Provides the Events Tickets Plus service.
 *
 * This class should handle implementation binding, builder functions and hooking for any first-level hook and be
 * devoid of business logic.
 *
 * @since 4.6
 */
class Tribe__Tickets__Service_Provider extends tad_DI52_ServiceProvider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.6
	 */
	public function register() {
		$this->container->singleton( 'tickets.assets', new Tribe__Tickets__Assets() );
		$this->container->singleton( 'tickets.handler', 'Tribe__Tickets__Tickets_Handler' );
		$this->container->singleton( 'tickets.attendees', 'Tribe__Tickets__Attendees', [ 'hook' ] );
		$this->container->singleton( 'tickets.version', 'Tribe__Tickets__Version', [ 'hook' ] );
		$this->container->singleton( 'tickets.metabox', 'Tribe__Tickets__Metabox', [ 'hook' ] );

		// Caching
		$this->container->singleton( 'tickets.cache-central', 'Tribe__Tickets__Cache__Central', [ 'hook' ] );
		$this->container->singleton( 'tickets.cache', tribe( 'tickets.cache-central' )->get_cache() );

		// Query Vars
		$this->container->singleton( 'tickets.query', 'Tribe__Tickets__Query', [ 'hook' ] );

		// Tribe Data API Init
		$this->container->singleton( 'tickets.data_api', 'Tribe__Tickets__Data_API' );

		// Ticket view handler.
		$this->container->singleton( 'tickets.tickets-view', Tribe__Tickets__Tickets_View::class );

		// View links, columns and screen options
		$this->container->singleton( 'tickets.admin.views', 'Tribe__Tickets__Admin__Views', [ 'hook' ] );
		$this->container->singleton( 'tickets.admin.columns', 'Tribe__Tickets__Admin__Columns', [ 'hook' ] );
		$this->container->singleton( 'tickets.admin.screen-options', 'Tribe__Tickets__Admin__Screen_Options', [ 'hook' ] );
		$this->container->singleton( 'tickets.admin.settings.display', 'Tribe__Tickets__Admin__Display_Settings', [ 'hook' ] );

		// Status Manager
		$this->container->singleton( 'tickets.status', 'Tribe__Tickets__Status__Manager', [ 'hook' ] );

		// Editor
		$this->container->singleton( 'tickets.editor', 'Tribe__Tickets__Editor', [ 'hook' ] );

		$this->container->singleton( 'tickets.admin.notices', 'Tribe__Tickets__Admin__Notices', [ 'hook' ] );

		// Attendees Table
		$this->container->singleton( 'tickets.admin.attendees_table', 'Tribe__Tickets__Attendees_Table' );

		// Migration queues.
		$this->container->singleton( 'tickets.migration.queue_4_12', \Tribe\Tickets\Migration\Queue_4_12::class, [ 'hooks' ] );

		$this->load();
	}

	/**
	 * Any hooking for any class needs happen here.
	 *
	 * In place of delegating the hooking responsibility to the single classes they are all hooked here.
	 *
	 * @since 4.6
	 */
	protected function load() {
		tribe( 'tickets.query' );
		tribe( 'tickets.handler' );
		tribe( 'tickets.attendees' );
		tribe( 'tickets.version' );
		tribe( 'tickets.metabox' );
		tribe( 'tickets.status' );
		tribe( 'tickets.editor' );

		// Migration queues.
		tribe( 'tickets.migration.queue_4_12' );

		if ( is_admin() ) {
			tribe( 'tickets.admin.views' );
			tribe( 'tickets.admin.columns' );
			tribe( 'tickets.admin.screen-options' );
			tribe( 'tickets.admin.notices' );
			tribe( 'tickets.admin.settings.display' );
		}
	}

	/**
	 * Binds and sets up implementations at boot time.
	 *
	 * @since 4.6
	 */
	public function boot() {
		// no ops
	}
}

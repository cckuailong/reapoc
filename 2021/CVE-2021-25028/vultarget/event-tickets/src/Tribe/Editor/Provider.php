<?php
/**
 * Handle the provider for the Event Tickets Editor functionality.
 */

use Tribe\Tickets\Editor\Warnings;

/**
 * Register Event Tickets provider
 *
 * @since 4.9
 */
class Tribe__Tickets__Editor__Provider extends tad_DI52_ServiceProvider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.9
	 */
	public function register() {
		// The general warnings class.
		$this->container->singleton( 'tickets.editor.warnings', Warnings::class, [ 'hook' ] );

		// Register these all the time - as we now use them in most of the templates, blocks or otherwise.
		$this->container->singleton( 'tickets.editor.template.overwrite', 'Tribe__Tickets__Editor__Template__Overwrite' );
		$this->container->singleton( 'tickets.editor.template', 'Tribe__Tickets__Editor__Template' );
		$this->container->singleton( 'tickets.editor.blocks.tickets', 'Tribe__Tickets__Editor__Blocks__Tickets' );
		$this->container->singleton( 'tickets.editor.blocks.rsvp', 'Tribe__Tickets__Editor__Blocks__Rsvp' );
		$this->container->singleton( 'tickets.editor.configuration', 'Tribe__Tickets__Editor__Configuration', [ 'hook' ] );

		$this->register_for_blocks();

		// Handle general non-block-specific instances.
		tribe( 'tickets.editor.warnings' );
	}

	/**
	 * Handle registration for blocks-functionality separately.
	 *
	 * @since 5.0.4
	 */
	public function register_for_blocks() {
		/** @var \Tribe__Editor $editor */
		$editor = tribe( 'editor' );

		// Only register for blocks if we are using them.
		if ( ! $editor->should_load_blocks() ) {
			return;
		}

		$this->container->singleton(
			'tickets.editor.compatibility.tickets',
			'Tribe__Tickets__Editor__Compatibility__Tickets',
			[ 'hook' ]
		);

		$this->container->singleton( 'tickets.editor.assets', 'Tribe__Tickets__Editor__Assets', [ 'register' ] );

		$this->container->singleton( 'tickets.editor.blocks.tickets-item', 'Tribe__Tickets__Editor__Blocks__Tickets_Item' );
		$this->container->singleton( 'tickets.editor.blocks.attendees', 'Tribe__Tickets__Editor__Blocks__Attendees' );

		$this->container->singleton( 'tickets.editor.meta', 'Tribe__Tickets__Editor__Meta' );
		$this->container->singleton( 'tickets.editor.rest.compatibility', 'Tribe__Tickets__Editor__REST__Compatibility', [ 'hook' ] );
		$this->container->singleton( 'tickets.editor.attendees_table', 'Tribe__Tickets__Attendees_Table' );

		$this->hook();

		/**
		 * Lets load all compatibility related methods
		 *
		 * @todo remove once RSVP and tickets blocks are completed
		 */
		$this->load_compatibility_tickets();

		// Initialize the correct Singleton.
		tribe( 'tickets.editor.assets' );
		tribe( 'tickets.editor.configuration' );
		tribe( 'tickets.editor.template.overwrite' )->hook();
	}

	/**
	 * Any hooking any class needs happen here.
	 *
	 * In place of delegating the hooking responsibility to the single classes they are all hooked here.
	 *
	 * @since 4.9
	 */
	protected function hook() {
		// Setup the Meta registration.
		add_action( 'init', tribe_callback( 'tickets.editor.meta', 'register' ), 15 );
		add_filter(
			'register_meta_args',
			tribe_callback( 'tickets.editor.meta', 'register_meta_args' ),
			10,
			4
		);

		// Handle REST specific meta filtering.
		add_filter(
			'rest_dispatch_request',
			tribe_callback( 'tickets.editor.meta', 'filter_rest_dispatch_request' ),
			10,
			3
		);

		// Setup the Rest compatibility layer for WP.
		tribe( 'tickets.editor.rest.compatibility' );

		// Register blocks.
		add_action(
			'tribe_editor_register_blocks',
			tribe_callback( 'tickets.editor.blocks.rsvp', 'register' )
		);

		add_action(
			'tribe_editor_register_blocks',
			tribe_callback( 'tickets.editor.blocks.tickets', 'register' )
		);

		add_action(
			'tribe_editor_register_blocks',
			tribe_callback( 'tickets.editor.blocks.tickets-item', 'register' )
		);

		add_action(
			'tribe_editor_register_blocks',
			tribe_callback( 'tickets.editor.blocks.attendees', 'register' )
		);

		global $wp_version;
		if( version_compare( $wp_version, '5.8', '<' ) ) {
			// WP version is less then 5.8.
			add_action(
				'block_categories',
				tribe_callback( 'tickets.editor', 'block_categories' )
			);
		} else {
			// WP version is 5.8 or above.
			add_action(
				'block_categories_all',
				tribe_callback( 'tickets.editor', 'block_categories' )
			);
		}
		
	}

	/**
	 * Initializes the correct classes for when Tickets is active.
	 *
	 * @since 4.9
	 *
	 * @return bool
	 */
	private function load_compatibility_tickets() {
		tribe( 'tickets.editor.compatibility.tickets' );
		return true;
	}

	/**
	 * Binds and sets up implementations at boot time.
	 *
	 * @since 4.9
	 */
	public function boot() {}
}

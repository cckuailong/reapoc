<?php

namespace WebpConverter\Loader;

use WebpConverter\HookableInterface;

/**
 * Adds integration with active method of loading images.
 */
class LoaderIntegration implements HookableInterface {

	/**
	 * Object of image loader method.
	 *
	 * @var LoaderInterface
	 */
	private $loader;

	public function __construct( LoaderInterface $loader ) {
		$this->loader = $loader;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'plugins_loaded', [ $this, 'load_loader_actions' ] );
		add_action( LoaderAbstract::ACTION_NAME, [ $this, 'refresh_loader' ], 10, 2 );
	}

	/**
	 * Loads hooks for loader if loader is active.
	 *
	 * @return void
	 * @internal
	 */
	public function load_loader_actions() {
		if ( ! $this->loader->is_active_loader() || apply_filters( 'webpc_server_errors', [], true ) ) {
			return;
		}
		$this->loader->init_hooks();
	}

	/**
	 * Activates or deactivates loader.
	 *
	 * @param bool $is_active Is active loader?
	 * @param bool $is_debug  Is debugging?
	 *
	 * @return void
	 * @internal
	 */
	public function refresh_loader( bool $is_active, bool $is_debug = false ) {
		if ( ( $is_active || $is_debug ) && $this->loader->is_active_loader() ) {
			$this->loader->activate_loader( $is_debug );
		} else {
			$this->loader->deactivate_loader();
		}
	}
}

<?php
/**
 * Handles admin conditional content.
 *
 * @since   4.14.7
 * @package Tribe\Admin\Conditional_Content;
 */

namespace Tribe\Admin\Conditional_Content;

/**
 * Conditional Content Provider.
 *
 * @since 4.14.7
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Registers the required objects and filters.
	 *
	 * @since 4.14.7
	 */
	public function register() {
		$this->container->singleton(  Black_Friday::class, Black_Friday::class, [ 'hook' ] );
		$this->hooks();
	}

	/**
	 * Set up hooks for classes.
	 *
	 * @since 4.14.7
	 */
	protected function hooks() {
		add_action( 'tribe_plugins_loaded', [ $this, 'plugins_loaded' ] );
	}

	/**
	 * Setup for things that require plugins loaded first.
	 *
	 * @since 4.14.7
	 */
	public function plugins_loaded() {
		$this->container->make( Black_Friday::class );
	}
}

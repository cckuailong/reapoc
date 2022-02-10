<?php

namespace WebpConverter\Loader;

use WebpConverter\HookableInterface;

/**
 * Interface for class that supports method of loading images.
 */
interface LoaderInterface extends HookableInterface {

	/**
	 * Returns mime types for loader.
	 *
	 * @return string[] Output formats with mime types.
	 */
	public function get_mime_types(): array;

	/**
	 * Returns status if loader is active.
	 *
	 * @return bool Is loader active?
	 */
	public function is_active_loader(): bool;

	/**
	 * Initializes actions for activating loader.
	 *
	 * @param bool $is_debug Is debugging?
	 *
	 * @return void
	 */
	public function activate_loader( bool $is_debug = false );

	/**
	 * Initializes actions for deactivating loader.
	 *
	 * @return void
	 */
	public function deactivate_loader();
}

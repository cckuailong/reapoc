<?php

namespace WebpConverter\Settings\Page;

/**
 * Interface for class that supports tab in plugin settings page.
 */
interface PageInterface {

	/**
	 * Returns status if view is active.
	 *
	 * @return bool Is view active?
	 */
	public function is_page_active(): bool;

	/**
	 * Displays view for plugin settings page.
	 *
	 * @return void
	 */
	public function show_page_view();
}

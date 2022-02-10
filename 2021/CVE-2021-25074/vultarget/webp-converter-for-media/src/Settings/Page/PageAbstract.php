<?php

namespace WebpConverter\Settings\Page;

/**
 * Abstract class for class that supports tab in plugin settings page.
 */
abstract class PageAbstract implements PageInterface {

	/**
	 * Returns status if view is active.
	 *
	 * @return bool Is view active?
	 */
	public function is_page_active(): bool {
		return false;
	}
}

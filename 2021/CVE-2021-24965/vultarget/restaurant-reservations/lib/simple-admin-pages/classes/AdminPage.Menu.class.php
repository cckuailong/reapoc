<?php

/**
 * Register, display and save an settings page as a submenu item in the
 * WordPress admin menu.
 *
 * @since 1.1
 * @package Simple Admin Pages
 */

class sapAdminPageMenu_2_6_1 extends sapAdminPage_2_6_1 {

	public $setup_function = 'add_menu_page'; // WP function to register the page

	/**
	 * Add the page to the appropriate menu slot.
	 * @since 1.0
	 */
	public function add_admin_menu() {

		call_user_func(
			$this->setup_function,
			$this->title,
			$this->menu_title,
			$this->capability,
			$this->id,
			array( $this, 'display_admin_menu' ),
			$this->icon,
			$this->position
		);
	}
}

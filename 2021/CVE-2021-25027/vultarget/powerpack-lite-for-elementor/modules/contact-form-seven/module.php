<?php
namespace PowerpackElementsLite\Modules\ContactFormSeven;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	/**
	 * Module is active or not.
	 *
	 * @since 1.3.3
     *
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_active() {
		if ( class_exists( 'WPCF7_ContactForm' ) ) {
			return true;
		}
		return false;
	}

    /**
	 * Get Module Name.
	 *
	 * @since 1.3.3
     *
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'pp-contact-form-7';
	}

    /**
	 * Get Widgets.
	 *
	 * @since 1.3.3
     *
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return [
			'Contact_Form_7',
		];
	}
}

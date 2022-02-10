<?php
namespace PowerpackElementsLite\Modules\FormidableForms;

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
        if ( class_exists( 'FrmHooksController' ) ) {
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
		return 'pp-formidable-forms';
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
			'Formidable_Forms',
		];
	}
}

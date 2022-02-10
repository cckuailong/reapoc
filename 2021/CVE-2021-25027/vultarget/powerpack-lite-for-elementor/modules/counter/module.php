<?php
namespace PowerpackElementsLite\Modules\Counter;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-counter';
	}

	public function get_widgets() {
		return [
			'Counter',
		];
	}
}

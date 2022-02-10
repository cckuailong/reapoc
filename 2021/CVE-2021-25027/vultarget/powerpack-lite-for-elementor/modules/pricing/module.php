<?php
namespace PowerpackElementsLite\Modules\Pricing;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-pricing';
	}

	public function get_widgets() {
		return [
			'Price_Menu',
			'Pricing_Table',
		];
	}
}

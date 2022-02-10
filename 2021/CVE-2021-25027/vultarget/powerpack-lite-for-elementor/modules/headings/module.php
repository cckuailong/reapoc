<?php
namespace PowerpackElementsLite\Modules\Headings;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-dual-heading';
	}

	public function get_widgets() {
		return [
			'Dual_Heading',
			'Fancy_Heading',
		];
	}
}

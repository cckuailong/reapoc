<?php
namespace PowerpackElementsLite\Modules\AdvancedAccordion;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-advanced-accordion';
	}

	public function get_widgets() {
		return [
			'Advanced_Accordion',
		];
	}
}

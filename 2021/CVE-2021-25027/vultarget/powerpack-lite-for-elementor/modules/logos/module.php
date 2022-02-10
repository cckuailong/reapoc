<?php
namespace PowerpackElementsLite\Modules\Logos;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-logo-carousel';
	}

	public function get_widgets() {
		return [
			'Logo_Carousel',
			'Logo_Grid',
		];
	}
}

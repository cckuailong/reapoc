<?php
namespace PowerpackElementsLite\Modules\Hotspots;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-image-hotspots';
	}

	public function get_widgets() {
		return [
			'Hotspots',
		];
	}
}

<?php
namespace PowerpackElementsLite\Modules\ContentTicker;

use PowerpackElementsLite\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function get_name() {
		return 'pp-content-ticker';
	}

	public function get_widgets() {
		return [
			'Content_Ticker',
		];
	}
}

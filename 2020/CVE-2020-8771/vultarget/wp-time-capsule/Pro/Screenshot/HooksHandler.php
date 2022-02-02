<?php

class Wptc_Screenshot_Hooks_Hanlder extends Wptc_Base_Hooks_Handler {
	private $screenshot;

	public function __construct() {
		$this->screenshot = WPTC_Base_Factory::get('Wptc_Screenshot');
	}

	public function take_screenshot($type ) {
		$this->screenshot->take_screenshot($type);
	}

	public function flush() {
		$this->screenshot->flush();
	}
}
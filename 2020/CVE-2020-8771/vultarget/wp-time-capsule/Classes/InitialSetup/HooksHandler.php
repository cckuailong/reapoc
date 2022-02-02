<?php

class Wptc_InitialSetup_Hooks_Handler extends Wptc_Base_Hooks_Handler {
	protected $settings;

	public function __construct() {
		$this->settings = WPTC_Base_Factory::get('Wptc_InitialSetup');
	}
}
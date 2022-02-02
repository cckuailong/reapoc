<?php
class Wptc_InitialSetup_Init {

	public function __construct() {
	}

	public function init() {
		if ($this->is_privileged()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Base_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	public function is_privileged() {
		return true;
	}
}
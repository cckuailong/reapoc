<?php

class WPTC_Pro extends WPTC_Privileges {
	private static $object_cache;

	public function __construct() {
		$this->init();
	}

	public function init() {
		if ($this->is_sub_valid()) {
			// do_action('just_initialized_wptc_h', '');
			WPTC_Pro_Factory::get('Wptc_Pro_Hooks')->register_hooks();
		}
	}
}
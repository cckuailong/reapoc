<?php

class Wptc_Base {
	public function __construct() {
	}

	public function init() {
		if ($this->is_privileged()) {
			WPTC_Base_Factory::get('Wptc_Base_Hooks')->register_hooks();
		}
	}

	public function is_privileged() {
		return true;
	}

}
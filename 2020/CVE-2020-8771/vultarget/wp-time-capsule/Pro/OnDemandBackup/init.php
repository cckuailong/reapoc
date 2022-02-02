<?php

class Wptc_On_Demand_Backup extends WPTC_Privileges {

	public function init() {
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Pro_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	private function is_switch_on() {
		return true;
	}

}
<?php

Class Wptc_On_Demand_Backup_Config extends Wptc_Base_Config {
	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->set_used_options();
	}

	protected function set_used_options() {
		$this->used_options = array(

		);
	}

}
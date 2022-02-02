<?php

class Wptc_Backup_Hooks_Handler extends Wptc_Base_Hooks_Handler {
	protected $config;
	protected $backup_controller;
	protected $backup_obj;
	protected $current_backup_id;

	public function __construct() {
		$this->config = WPTC_Factory::get('config');
		$this->backup_obj = WPTC_Base_Factory::get('Wptc_Backup');
	}

	//WPTC's specific hooks start

	//WPTC's specific hooks end

}
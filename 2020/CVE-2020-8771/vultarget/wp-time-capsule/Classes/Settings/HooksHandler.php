<?php

class Wptc_Settings_Hooks_Handler extends Wptc_Base_Hooks_Handler {
	protected $settings;

	public function __construct() {
		$this->settings = WPTC_Base_Factory::get('Wptc_Settings');
	}

	public function admin_footer_text($admin_footer_text) {
		return $this->settings->admin_footer_text($admin_footer_text);
	}

	public function update_footer($update_footer) {
		return $this->settings->update_footer($update_footer);
	}

	public function save_settings_revision_limit($revision_limit) {
		return $this->settings->save_settings_revision_limit($revision_limit);
	}
}
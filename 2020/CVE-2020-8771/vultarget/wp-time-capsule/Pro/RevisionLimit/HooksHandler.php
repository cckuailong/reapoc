<?php

class Wptc_Revision_Limit_Hooks_Hanlder extends Wptc_Base_Hooks_Handler {

	public function __construct() {
		$this->revision_limit = WPTC_Base_Factory::get('Wptc_Revision_Limit');
	}

	public function update_eligible_revision_limit( $privileges_args ) {
		$this->revision_limit->update_eligible_revision_limit( $privileges_args);
	}

	public function set_revision_limit( $days , $cross_check_failed = false) {
		$this->revision_limit->set_revision_limit( $days, $cross_check_failed );
	}

	public function get_current_revision_limit() {
		return $this->revision_limit->get_current_revision_limit();
	}

	public function get_eligible_revision_limit() {
		return $this->revision_limit->get_eligible_revision_limit();
	}

	public function get_days_show_from_revision_limits() {
		return $this->revision_limit->get_days_show_from_revision_limits();
	}
}
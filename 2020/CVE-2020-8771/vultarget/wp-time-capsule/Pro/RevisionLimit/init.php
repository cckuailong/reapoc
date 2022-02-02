<?php

class Wptc_Revision_Limit extends WPTC_Privileges {

	private $config;

	public function __construct(){
		$this->config = WPTC_Factory::get('config');
	}

	public function init() {
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Pro_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	private function is_switch_on(){
		return true;
	}

	public function update_eligible_revision_limit($response){

		wptc_log('', "--------update_eligible_revision_limit--------");

		if(empty($response)){
			$this->config->set_option('eligible_revision_limit', WPTC_DEFAULT_MAX_REVISION_LIMIT);
			return $this->cross_check_revision_limit_days();
		}

		if (is_numeric($response)) {
			$revision_limit = $response;
		} else {
			$revision_limit = $response['Wptc_Revision_Limit']->days;

			if(empty($revision_limit)){
				$revision_limit = WPTC_DEFAULT_MAX_REVISION_LIMIT;
			}
		}

		$this->config->set_option('eligible_revision_limit', $revision_limit);

		$this->cross_check_revision_limit_days();
	}

	private function cross_check_revision_limit_days(){

		wptc_log('', "--------cross_check_revision_limit_days--------");

		$current_revision_limit  = $this->config->get_option('revision_limit');
		$eligible_revision_limit = $this->get_eligible_revision_limit();

		if ($current_revision_limit > $eligible_revision_limit) {
			$this->set_revision_limit($eligible_revision_limit, $cross_check_failed = true);
		}
	}

	public function set_revision_limit($days, $cross_check_failed = false){

		//wptc_log(get_backtrace_string_wptc(), "--------set_revision_limit--backtrace------");

		wptc_log($days, "--------set_revision_limit--------");

		$this->config->set_option('revision_limit', $days);

		$this->set_last_revision_limit($days);

		if (!$cross_check_failed) {
			return ;
		}

		$default_repo = $this->config->get_option('default_repo');

		if (empty($default_repo)) {
			return ;
		}

		$cloud_repo = WPTC_Factory::get($default_repo);

		//If crossed check failed then change the lifecycle in s3 automatically
		$cloud_repo->validate_max_revision_limit($days);
		return ;

	}

	private function set_last_revision_limit($days){
		$current_limit = $this->get_last_revision_limit();
		$new_limit     = strtotime(date('Y-m-d', strtotime('today - ' . $days . ' days')));

		if (empty($current_limit) || $current_limit < $new_limit ) {
			return $this->config->set_option('last_revision_limit', $new_limit);
		}
	}

	private function get_last_revision_limit(){
		return $this->config->get_option('last_revision_limit');
	}

	public function get_current_revision_limit(){
		$revision_limit 		 = $this->config->get_option('revision_limit');
		$eligible_revision_limit = $this->get_eligible_revision_limit();

		if ($revision_limit > $eligible_revision_limit) {
			return $eligible_revision_limit;
		}

		return $revision_limit;
	}

	public function get_eligible_revision_limit(){
		$eligible_revision_limit = $this->config->get_option('eligible_revision_limit');
		return empty($eligible_revision_limit) ? WPTC_DEFAULT_MAX_REVISION_LIMIT : $eligible_revision_limit;
	}

	public function get_days_show_from_revision_limits(){
		$revision_limit = $this->get_current_revision_limit();

		$revision_limit_to_time_stamp = strtotime(date('Y-m-d', strtotime('today - ' . $revision_limit . ' days')));

		if ($this->config->get_option('default_repo') !== 's3') {
			return $revision_limit_to_time_stamp;
		}

		$last_revision_limit_to_time_stamp = $this->get_last_revision_limit();

		if (empty($last_revision_limit_to_time_stamp) || $revision_limit_to_time_stamp > $last_revision_limit_to_time_stamp) {
			return $revision_limit_to_time_stamp;
		}

		return $last_revision_limit_to_time_stamp;
	}
}

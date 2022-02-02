<?php

class Wptc_Rollback extends WPTC_Privileges{
	private	$config,
			$request;

	public function __construct() {
		$this->config = WPTC_Pro_Factory::get('Wptc_Rollback_Config');
	}

	public function init(){
		if ($this->is_privileged_feature(get_class($this)) && $this->is_switch_on()) {
			$supposed_hooks_class = get_class($this) . '_Hooks';
			WPTC_Pro_Factory::get($supposed_hooks_class)->register_hooks();
		}
	}

	private function is_switch_on(){
		return true;
	}

	private function init_request($request){
		$this->request = $request;
	}

	public function get_previous_versions($request){

		$this->init_request($request);

		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare("SELECT * FROM {$wpdb->base_prefix}wptc_backups WHERE `update_details` LIKE '%%%s%%' ORDER BY `id` DESC", $this->request['name'])
		);

		$result = $this->get_rollback_restore_point($results);

		if (empty($result)) {
			wptc_die_with_json_encode( array('status' => 'error', 'msg' => 'no updates found' ) );
		}

		wptc_log($result,'-----------$result----------------');

		$result->update_details['path'] = $this->get_update_path() . '/' . $this->request['slug'];

		wptc_die_with_json_encode(
			array(
				'status' => 'success',
				'data'   => (array) $result
			)
		);
	}

	private function get_rollback_restore_point($results){
		foreach ($results as $key => $result) {
			$result->update_details = $this->tokenize_upgrade_details($result);

			if (empty($result->update_details) || empty($this->request['version']) || empty($result->update_details['version'])) {
				continue;
			}

			if (version_compare($this->request['version'], $result->update_details['version'], '>=')) {

				if(!empty($this->request) && !empty($this->request['name'])){
					if( $this->request['name'] != $result->update_details['name'] ){
						continue;
					}
				}

				return $result;
			}
		}

		return array();
	}

	private function tokenize_upgrade_details($result){
		$update_details = unserialize($result->update_details);

		if ($this->request['type'] === 'plugin') {
			$update_details = $update_details['plugin'];
		} else if ($this->request['type'] === 'theme') {
			$update_details = $update_details['theme'];
		}

		foreach ($update_details as $details) {
			if (strstr($details['name'], $this->request['name']) && $details['status'] === 'success') {
				return $details;
			}
		}

		return array();
	}

	private function get_update_path(){
		if ($this->request['type'] === 'plugin') {
			return wptc_remove_fullpath(WP_PLUGIN_DIR);
		} else if ($this->request['type'] === 'theme') {
			if (!function_exists('get_theme_root')) {
				include_once ABSPATH . 'wp-includes/theme.php';
			}

			return wptc_remove_fullpath(get_theme_root());
		}
	}
}

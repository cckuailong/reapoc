<?php

class WPTC_Privileges {

	public function is_sub_valid() {
		$sub_info = WPTC_Factory::get('config')->get_option('subscription_info');
		$sub_info = json_decode($sub_info, true);

		if(empty($sub_info) || empty($sub_info['subscription_end'])){
			return false;
		}

		$cur_time = time();
		if($cur_time > $sub_info['subscription_end']){
			return false;
		}

		return true;
	}

	public function is_privileged_feature($class_name) {
		$privileges_arr = WPTC_Factory::get('config')->get_option('privileges_wptc');
		$privileges_arr = json_decode($privileges_arr);

		if(empty($privileges_arr)){
			return false;
		}

		foreach($privileges_arr as $type => $features_arr){
			foreach($features_arr as $k => $feature){
				if($class_name == $feature){
					return true;
				}
			}
		}

		return false;
	}

	public function get_privileged_class_arr() {
		$privileges_arr = WPTC_Factory::get('config')->get_option('privileges_wptc');
		$privileges_arr = json_decode($privileges_arr);

		if(empty($privileges_arr)){
			return array();
		}

		$privilege_class_arr = array();
		foreach($privileges_arr as $type => $features_arr){
			foreach($features_arr as $k => $feature){
				array_push($privilege_class_arr, $feature);
			}
		}

		return $privilege_class_arr;
	}

}
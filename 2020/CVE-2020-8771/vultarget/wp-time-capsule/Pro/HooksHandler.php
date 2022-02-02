<?php

class Wptc_Pro_Hooks_Hanlder extends Wptc_Base_Hooks_Handler {

	public function __construct() {
	}

	//WPTC's specific hooks start

	public function just_initialized_wptc_h($arg1 = '', $arg2 = null, $arg3 = null, $arg4 = null) {

		// wptc_log('', "--------just_initialized_wptc_h--pro hook------");

		$class_arr = WPTC_Pro_Factory::get('WPTC_Privileges')->get_privileged_class_arr();

		if(empty($class_arr)){
			return true;
		}

		foreach($class_arr as $k => $v){
			$supposed_class = $v;

			if(class_exists($supposed_class) && $this->is_wptc_class($supposed_class)){
				WPTC_Pro_Factory::get($supposed_class)->init();
			}
		}

		check_wptc_update_after_pro_loaded();
	}

	public function is_wptc_class($class_name)
	{
		if(stripos($class_name, 'Wptc') !== false){
			return true;
		}
		return false;
	}

	//WPTC's specific hooks end
}

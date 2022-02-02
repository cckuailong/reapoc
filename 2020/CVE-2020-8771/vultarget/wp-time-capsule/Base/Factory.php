<?php

class WPTC_Base_Factory {
	private static $object_cache;

	private function __construct() {

	}

	private static function get_class_name($name) {
		return $name;
	}

	public static function get($name, $noCache = false) {
		$class_name = self::get_class_name($name);

		if (!class_exists($class_name)) {
			//throw new Exception('Not able to init Pro Factory Class.');
			// wptc_log($class_name, "--------no class--------");
			return null;
		}

		if (!isset(self::$object_cache[$class_name]) || $noCache == true) {
			self::$object_cache[$class_name] = new $class_name();
		}

		return self::$object_cache[$class_name];
	}

	public static function set($name, $object) {
		if ($name == 'db') {
			self::$object_cache['WPDB'] = $object;
		} else {
			self::$object_cache[self::get_class_name($name)] = $object;
		}
	}

	public static function reset() {
		self::$object_cache = array();
	}
}
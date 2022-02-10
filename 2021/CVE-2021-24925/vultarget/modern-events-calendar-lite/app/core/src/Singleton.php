<?php

namespace MEC;

class Singleton {

	private static $instance;

	public static function getInstance() {

		$class_name = get_called_class();

		if ( !isset( self::$instance[ $class_name ] ) ) {

			self::$instance[ $class_name ] = new $class_name();
		}

		return self::$instance[ $class_name ];
	}
}

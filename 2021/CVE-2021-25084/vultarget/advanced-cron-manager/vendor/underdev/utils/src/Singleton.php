<?php
/**
 * Singleton class
 */

namespace underDEV\Utils;

class Singleton {

    protected static $instances = array();

    protected function __construct() {}

    protected function __clone() {}

    public function __wakeup() {
        throw new Exception( 'Cannot unserialize singleton' );
    }

    public static function get() {

        $class = get_called_class();

        if ( ! isset( self::$instances[ $class ] ) ) {
            self::$instances[ $class ] = new static;
        }

        return self::$instances[ $class ];

    }

}

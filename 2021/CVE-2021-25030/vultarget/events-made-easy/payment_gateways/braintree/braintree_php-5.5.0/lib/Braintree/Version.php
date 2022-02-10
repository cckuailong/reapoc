<?php
namespace Braintree;

/**
 * Braintree Library Version
 * stores version information about the Braintree library
 */
class Version
{
    /**
     * class constants
     */
    const MAJOR = 5;
    const MINOR = 5;
    const TINY = 0;

    /**
     * @ignore
     * @access protected
     */
    protected function  __construct()
    {
    }

    /**
     *
     * @return string the current library version
     */
    public static function get()
    {
        return self::MAJOR . '.' . self::MINOR . '.' . self::TINY;
    }
}

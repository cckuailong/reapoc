<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Math;

class Math
{
    private static $engine;
    private static $engineName;

    public static function setEngine($engine)
    {
        static::$engine = $engine;
    }

    public static function setEngineName($engineName)
    {
        static::$engineName = $engineName;
    }

    public static function getEngine()
    {
        return static::$engine;
    }

    public static function getEngineName()
    {
        return static::$engineName;
    }

    public static function __callStatic($name, $arguments)
    {
        if (is_null(static::$engine)) {
            if (extension_loaded('gmp')) {
                static::$engine = new GmpEngine();
                static::$engineName = "GMP";
            } elseif (extension_loaded('bcmath')) {
                static::$engine = new BcEngine();
                static::$engineName = "BCMATH";
            } else {
                throw new \Exception('The GMP or BCMATH extension for PHP is required.');
            }
        }

        return call_user_func_array(array(static::$engine, $name), $arguments);
    }
}

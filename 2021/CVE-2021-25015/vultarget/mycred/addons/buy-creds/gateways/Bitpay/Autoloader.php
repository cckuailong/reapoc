<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay;

/**
 * @package Bitpay
 */
class Autoloader
{
    /**
     * Register the autoloader, by default this will put the BitPay autoloader
     * first on the stack, to append the autoloader, pass `false` as an argument.
     *
     * Some applications will throw exceptions if the class isn't found and
     * some are not compatible with PSR standards.
     *
     * @param boolean $prepend
     */
    public static function register($prepend = true)
    {
        spl_autoload_register(array(__CLASS__, 'autoload'), true, (bool) $prepend);
    }

    /**
     * Unregister this autoloader
     */
    public static function unregister()
    {
        spl_autoload_unregister(array(__CLASS__, 'autoload'));
    }

    /**
     * Give a class name and it will require the file.
     *
     * @param  string $class
     * @return bool
     */
    public static function autoload($class)
    {
        if (0 === strpos($class, 'Bitpay\\')) {
            $classname = substr($class, 7);

            $file = __DIR__.DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $classname).'.php';

            if (is_file($file) && is_readable($file)) {
                require_once $file;

                return true;
            }

            throw new \Exception(sprintf('Class "%s" Not Found', $class));
        }
    }
}

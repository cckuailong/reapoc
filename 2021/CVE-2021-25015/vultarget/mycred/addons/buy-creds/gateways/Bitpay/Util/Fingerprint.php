<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

/**
 * Utility class for generating an operating system & enivironment fingerprint
 */
class Fingerprint
{
    protected static $sigData;
    protected static $finHash;

    /**
     * Generates a string of environment information and
     * takes the hash of that value to use as the env
     * fingerprint.
     *
     * @param void
     * @return string
     */
    final public static function generate()
    {
        if (null !== self::$finHash) {
            return self::$finHash;
        }

        self::$finHash = '';
        self::$sigData = array();

        $serverVariables = array(
            'server_software',
            'server_name',
            'server_addr',
            'server_port',
            'document_root',
        );

        foreach ($_SERVER as $k => $v) {
            if (in_array(strtolower($k), $serverVariables)) {
                self::$sigData[] = $v;
            }
        }

        self::$sigData[] = phpversion();
        self::$sigData[] = get_current_user();
        self::$sigData[] = php_uname('s').php_uname('n').php_uname('m').PHP_OS.PHP_SAPI.ICONV_IMPL.ICONV_VERSION;
        self::$sigData[] = sha1_file(__FILE__);

        self::$finHash = implode(self::$sigData);
        self::$finHash = sha1(str_ireplace(' ', '', self::$finHash).strlen(self::$finHash).metaphone(self::$finHash));
        self::$finHash = sha1(self::$finHash);

        return self::$finHash;
    }
}

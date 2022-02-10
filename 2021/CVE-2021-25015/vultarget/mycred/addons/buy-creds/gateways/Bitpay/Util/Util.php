<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Util;

use Bitpay\PointInterface;
use Bitpay\Point;
use Bitpay\Math\Math;

/**
 * Utility class used by string and arbitrary integer methods.
 *
 * @package Bitcore
 */
class Util
{
    /**
     * @var string
     */
    const HEX_CHARS = '0123456789abcdef';

    /**
     * Computes a digest hash value for the given data using
     * the given method, and returns a raw or binhex encoded
     * string, see:
     * http://us1.php.net/manual/en/function.openssl-digest.php
     *
     * @param string $data
     *
     * @return string
     */
    public static function sha256($data, $binary = false)
    {
        return openssl_digest($data, 'SHA256', $binary);
    }

    /**
     * Computes a digest hash value for the given data using
     * the given method, and returns a raw or binhex encoded
     * string, see:
     * http://us1.php.net/manual/en/function.openssl-digest.php
     *
     * @param string $data
     *
     * @return string
     */
    public static function sha512($data)
    {
        return openssl_digest($data, 'sha512');
    }

    /**
     * Generate a keyed hash value using the HMAC method.
     * http://us1.php.net/manual/en/function.hash-hmac.php
     *
     * @param string $data
     * @param string $key
     *
     * @return string
     */
    public static function sha512hmac($data, $key)
    {
        return hash_hmac('SHA512', $data, $key);
    }

    /**
     * Returns a RIPDEMD160 hash of a value.
     *
     * @param string $data
     *
     * @return string
     */
    public static function ripe160($data, $binary = false)
    {
        return openssl_digest($data, 'ripemd160', $binary);
    }

    /**
     * Returns a SHA256 hash of a RIPEMD160 hash of a value.
     *
     * @param string $data
     *
     * @return string
     */
    public static function sha256ripe160($data)
    {
        return bin2hex(self::ripe160(self::sha256($data, true), true));
    }

    /**
     * Returns a double SHA256 hash of a value.
     *
     * @param string $data
     *
     * @return string
     */
    public static function twoSha256($data, $binary = false)
    {
        return self::sha256(self::sha256($data, $binary), $binary);
    }

    /**
     * Returns a nonce for use in REST calls.
     *
     * @see http://en.wikipedia.org/wiki/Cryptographic_nonce
     *
     * @return string
     */
    public static function nonce()
    {
        return microtime(true);
    }

    /**
     * Returns a GUID for use in REST calls.
     *
     * @see http://en.wikipedia.org/wiki/Globally_unique_identifier
     *
     * @return string
     */
    public static function guid()
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            bin2hex(openssl_random_pseudo_bytes(4)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            bin2hex(openssl_random_pseudo_bytes(2)),
            bin2hex(openssl_random_pseudo_bytes(6))
        );
    }

    /**
     * Encodes a decimal value into hexadecimal.
     *
     * @param  string $dec
     * @return string
     */
    public static function encodeHex($dec)
    {
        if (!is_string($dec) && !ctype_digit($dec)) {
            throw new \Exception(sprintf('Argument is expected to be a string of decimal numbers. You passed in "%s"', gettype($dec)));
        }

        if (substr($dec, 0, 1) === '-') {
            $dec = substr($dec, 1);
        }

        $hex = '';

        while (Math::cmp($dec, 0) > 0) {
            $q = Math::div($dec, 16);
            $rem = Math::mod($dec, 16);
            $dec = $q;

            $hex = substr(self::HEX_CHARS, intval($rem), 1).$hex;
        }

        return $hex;
    }

    /**
     * Decodes a hexadecimal value into decimal.
     *
     * @param  string $hex
     * @return string
     */
    public static function decodeHex($hex)
    {
        if (!is_string($hex) || !ctype_xdigit($hex) && '0x' != substr($hex, 0, 2)) {
            throw new \Exception('Argument must be a string of hex digits.');
        }

        $hex = strtolower($hex);

        // if it has a prefix of 0x this needs to be trimed
        if (substr($hex, 0, 2) == '0x') {
            $hex = substr($hex, 2);
        }

        $hexLen = strlen($hex);
        for ($dec = '0', $i = 0; $i < $hexLen; $i++) {
            $current = strpos(self::HEX_CHARS, $hex[$i]);
            $dec     = Math::add(Math::mul($dec, 16), $current);
        }

        return $dec;
    }

    public static function doubleAndAdd($hex, PointInterface $point, CurveParameterInterface $parameters = null)
    {
        if (null === $parameters) {
            $parameters = new Secp256k1();
        }
        $tmp = self::decToBin($hex);

        $n   = strlen($tmp) - 1;
        $S   = new Point(PointInterface::INFINITY, PointInterface::INFINITY);


        while ($n >= 0) {
            $S = self::pointDouble($S);

            if ($tmp[$n] == 1) {
                $S = self::pointAdd($S, $point);
            }
            $n--;
        }

        return new Point($S->getX(), $S->getY());
    }

    /**
     * This method returns a binary string representation of
     * the decimal number. Used for the doubleAndAdd() method.
     *
     * @see http://php.net/manual/en/function.decbin.php but for large numbers
     *
     * @param string
     * @return string
     */
    public static function decToBin($dec)
    {
        if (substr(strtolower($dec), 0, 2) == '0x') {
            $dec = self::decodeHex(substr($dec, 2));
        }

        $bin  = '';
        while (Math::cmp($dec, '0') > 0) {
            if (Math::mod($dec, 2) == '1') {
                $bin .= '1';
            } else {
                $bin .= '0';
            }
            $prevDec = $dec;
            $dec = Math::div($dec, 2);
            //sanity check to avoid infinite loop
            if (Math::cmp($prevDec, $dec) < 1) {
                throw new \Exception('Math library has unexpected behavior, please report the following information to support@bitpay.com. Math Engine is: ' . Math::getEngineName() . '. PHP Version is: ' . phpversion() . '.');
            }
        }

        return $bin;
    }

    /**
     * Point multiplication method 2P = R where
     *   s = (3xP2 + a)/(2yP) mod p
     *   xR = s2 - 2xP mod p
     *   yR = -yP + s(xP - xR) mod p
     *
     * @param  PointInterface $point
     * @param CurveParameterInterface
     * @return PointInterface
     */
    public static function pointDouble(PointInterface $point, CurveParameterInterface $parameters = null)
    {
        if ($point->isInfinity()) {
            return $point;
        }

        if (null === $parameters) {
            $parameters = new Secp256k1();
        }

        $p = $parameters->pHex();
        $a = $parameters->aHex();

        $s = 0;
        $R = array(
            'x' => 0,
            'y' => 0,
        );

        // Critical math section
        try {
            $m      = Math::add(Math::mul(3, Math::mul($point->getX(), $point->getX())), $a);
            $o      = Math::mul(2, $point->getY());
            $n      = Math::invertm($o, $p);
            $n2     = Math::mod($o, $p);
            $st     = Math::mul($m, $n);
            $st2    = Math::mul($m, $n2);
            $s      = Math::mod($st, $p);
            $s2     = Math::mod($st2, $p);
            $xmul   = Math::mul(2, $point->getX());
            $smul   = Math::mul($s, $s);
            $xsub   = Math::sub($smul, $xmul);
            $xmod   = Math::mod($xsub, $p);
            $R['x'] = $xmod;
            $ysub   = Math::sub($point->getX(), $R['x']);
            $ymul   = Math::mul($s, $ysub);
            $ysub2  = Math::sub(0, $point->getY());
            $yadd   = Math::add($ysub2, $ymul);

            $R['y'] = Math::mod($yadd, $p);

        } catch (\Exception $e) {
            throw new \Exception('Error in Util::pointDouble(): '.$e->getMessage());
        }

        return new Point($R['x'], $R['y']);
    }

        /**
     * Point addition method P + Q = R where:
     *   s = (yP - yQ)/(xP - xQ) mod p
     *   xR = s2 - xP - xQ mod p
     *   yR = -yP + s(xP - xR) mod p
     *
     * @param PointInterface
     * @param PointInterface
     *
     * @return PointInterface
     */
    public static function pointAdd(PointInterface $P, PointInterface $Q)
    {
        if ($P->isInfinity()) {
            return $Q;
        }

        if ($Q->isInfinity()) {
            return $P;
        }

        if ($P->getX() == $Q->getX() && $P->getY() == $Q->getY()) {
            return self::pointDouble(new Point($P->getX(), $P->getY()));
        }

        $p = '0x'.Secp256k1::P;
        $a = '0x'.Secp256k1::A;
        $s = 0;
        $R = array(
            'x' => 0,
            'y' => 0,
            's' => 0,
        );

        // Critical math section
        try {
            $m      = Math::sub($P->getY(), $Q->getY());
            $n      = Math::sub($P->getX(), $Q->getX());
            $o      = Math::invertm($n, $p);
            $st     = Math::mul($m, $o);
            $s      = Math::mod($st, $p);

            $R['x'] = Math::mod(
                Math::sub(
                    Math::sub(
                        Math::mul($s, $s),
                        $P->getX()
                    ),
                    $Q->getX()
                ),
                $p
            );
            $R['y'] = Math::mod(
                Math::add(
                    Math::sub(
                        0,
                        $P->getY()
                    ),
                    Math::mul(
                        $s,
                        Math::sub(
                            $P->getX(),
                            $R['x']
                        )
                    )
                ),
                $p
            );

            $R['s'] = $s;
        } catch (Exception $e) {
            throw new \Exception('Error in Util::pointAdd(): '.$e->getMessage());
        }

        return new Point($R['x'], $R['y']);
    }

    /**
     * Converts hex value into octet (byte) string
     *
     * @param string
     *
     * @return string
     */
    public static function binConv($hex)
    {
        $rem    = '';
        $dv     = '';
        $byte   = '';
        $digits = array();

        for ($x = 0; $x < 256; $x++) {
            $digits[$x] = chr($x);
        }

        if (substr(strtolower($hex), 0, 2) != '0x') {
            $hex = '0x'.strtolower($hex);
        }

        while (Math::cmp($hex, 0) > 0) {
            $dv   = Math::div($hex, 256);
            $rem  = Math::mod($hex, 256);
            $hex  = $dv;
            $byte = $byte.$digits[$rem];
        }

        return strrev($byte);
    }

    /**
     * Checks dependencies for the library
     *
     * @return array list of each requirement, boolean true if met, string error message if not as value
     */
    public static function checkRequirements()
    {
        $requirements = array();

        // PHP Version
        if (!defined('PHP_VERSION_ID')) {
            $version = explode('.', PHP_VERSION);
            define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
        }
        if (PHP_VERSION_ID < 50400) {
            $requirements['PHP'] = 'Your PHP version, ' . PHP_VERSION . ', is too low. PHP version >= 5.4 is required.';
        } else {
            $requirements['PHP'] = true;
        }

        // Mcrypt Extension
        if (!extension_loaded('mcrypt')) {
            $requirements['Mcrypt'] = 'The Mcrypt PHP extension could not be found.';
        } else {
            $requirements['Mcrypt'] = true;
        }

        // OpenSSL Extension
        if (!extension_loaded('openssl')) {
            $requirements['OpenSSL'] = 'The OpenSSL PHP extension could not be found.';
        } else {
            $requirements['OpenSSL'] = true;
        }

        // JSON Extension
        if (!extension_loaded('json')) {
            $requirements['JSON'] = 'The JSON PHP extension could not be found.';
        } else {
            $requirements['JSON'] = true;
        }

        // cURL Extension
        if (!extension_loaded('curl')) {
            $requirements['cURL'] = 'The cURL PHP extension could not be found.';
        } else {
            $requirements['cURL'] = true;
            $curl_version = curl_version();
            $ssl_supported = ($curl_version['features'] & CURL_VERSION_SSL);
            if (!$ssl_supported) {
                $requirements['cURL.SSL'] = 'The cURL PHP extension does not have SSL support.';
            } else {
                $requirements['cURL.SSL'] = true;
            }
        }

        // Math
        if (!extension_loaded('bcmath') && !extension_loaded('gmp')) {
            $requirements['Math'] = 'Either the BC Math or GMP PHP extension is required.  Neither could be found.';
        } else {
            $requirements['Math'] = true;
        }

        return $requirements;
    }
}

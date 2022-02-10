<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License 
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Crypto;

/**
 * Wrapper around the HASH Message Digest Framework for PHP
 *
 * @see http://php.net/manual/en/book.hash.php
 */
class HashExtension implements CryptoInterface
{
    /**
     * @inheritdoc
     */
    public static function hasSupport()
    {
        return function_exists('hash');
    }

    /**
     * Return a list of registered hashing algorithms.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @return array
     */
    public function getAlgos()
    {
        return hash_algos();
    }

    /**
     * Copy a hashing context.
     * (PHP 5 >= 5.3.0)
     *
     * @param resource
     * @return resource
     */
    final public function copy($context)
    {
        if (!is_resource($context)) {
            return false;
        }

        return hash_copy($context);
    }

    /**
     * Compares two strings using the same time
     * whether they're equal or not. This function
     * should be used to mitigate timing attacks;
     * for instance, when testing crypt() password
     * hashes.
     * (PHP 5 >= 5.6.0)
     *
     * @param string
     * @param string
     */
    public function equals($string1, $string2)
    {
        if (!is_string($string1) || !is_string($string2)) {
            return false;
        }

        return hash_equals($string1, $string2);
    }

    /**
     * Generate a hash value using the contents of
     * a given file. Returns a string containing
     * the calculated message digest as lowercase
     * hexits unless raw_output is set to true in
     * which case the raw binary representation of
     * the message digest is returned.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param string
     * @param string
     * @param bool
     * @return string
     */
    public function file($algorithm, $filename, $raw_output = false)
    {
        return hash_file($algorithm, (string) $filename, $raw_output);
    }

    /**
     * Finalize an incremental hash and return
     * resulting digest.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param resource
     * @param bool
     * @return string
     */
    public function finalize($context, $raw_output = false)
    {
        if (!is_resource($context)) {
            return false;
        }

        return hash_final($context, $raw_output);
    }

    /**
     * Generate a keyed hash value using the HMAC
     * method and the contents of a given file.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param string
     * @param string
     * @param string
     * @param bool
     * @return string
     */
    public function hmacFile($algorithm, $filename, $key, $raw_output = false)
    {
        return hash_hmac_file($algorithm, $filename, $key, $raw_output);
    }

    /**
     * Generate a keyed hash value using the HMAC
     * method and the message passed via $data.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param string
     * @param string
     * @param string
     * @param bool
     * @return string
     */
    public function hmac($algo, $data, $key, $raw_output = false)
    {
        return hash_hmac($algo, $data, $key, $raw_output);
    }

    /**
     * Initialize an incremental hashing context and
     * returns a Hashing Context resource for use with
     * hash_update(), hash_update_stream(), hash_update_file(),
     * and hash_final(). Note: the only option possible
     * for $options at this time is HASH_HMAC. When
     * this is specified, the key *must* be used as well.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param string
     * @param int
     * @param string
     * @return resource
     */
    public function init($algorithm, $options = 0, $key = null)
    {
        return hash_init($algorithm, $options, $key);
    }

    /**
     * Generate a PBKDF2 key derivation of a supplied
     * password. An E_WARNING will be raised if the
     * algorithm is unknown, the iterations parameter
     * is less than or equal to 0, the length is less
     * than 0 or the salt is too long (greater than
     * INT_MAX - 4). The salt should be generated
     * randomly with openssl_ramdom_pseudo_bytes().
     * (PHP 5 >= 5.5.0)
     *
     * @param string
     * @param string
     * @param string
     * @param int
     * @param int
     * @param bool
     * @return string
     */
    public function pbkdf2($algo, $password, $salt, $iterations, $length = 0, $raw_output = false)
    {
        return hash_pbkdf2($algo, $password, $salt, $iterations, $length, $raw_output);
    }

    /**
     * Pump data into an active hashing context
     * from a file. Returns TRUE on success or
     * FALSE on failure.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param resource
     * @param string
     * @param resource
     * @return bool
     */
    public function updateFile($hcontext, $filename, $scontext = null)
    {
        if (!is_resource($hcontext)) {
            return false;
        }

        return hash_update_file($hcontext, $filename, $scontext);
    }

    /**
     * Pump data into an active hashing context
     * from an open stream. Returns the actual
     * number of bytes added to the hashing
     * context from handle.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param resource
     * @param resource
     * @param int
     * @return int
     */
    public function updateStream($context, $handle, $length = -1)
    {
        if (!is_resource($context) || !is_resource($handle)) {
            return false;
        }

        return hash_update_stream($context, $handle, $length);
    }

    /**
     * Pump data into an active hashing context.
     * The PHP function itself only returns true.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param resource
     * @param string
     * @return bool
     */
    public function update($context, $data)
    {
        if (!is_resource($context)) {
            return false;
        }

        return hash_update($context, $data);
    }

    /**
     * Generate a hash value (message digest)
     * based on the request algorithm and the
     * provided data. Outputs hex unless the
     * $raw_output param is set to true.
     * (PHP 5 >= 5.1.2, PECL hash >= 1.1)
     *
     * @param string
     * @param string
     * @param bool
     * @return string
     */
    public function generate($algo, $data, $raw_output = false)
    {
        if (empty($data)) {
            return false;
        }

        return hash($algo, $data, $raw_output);
    }
}

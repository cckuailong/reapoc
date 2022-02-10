<?php
/**
 * @license Copyright 2011-2014 BitPay Inc., MIT License
 * see https://github.com/bitpay/php-bitpay-client/blob/master/LICENSE
 */

namespace Bitpay\Crypto;

/**
 * Wrapper around the Mcrypt PHP Extension
 *
 * @see http://php.net/manual/en/book.mcrypt.php
 */
class McryptExtension implements CryptoInterface
{
    /**
     * @inheritdoc
     */
    public static function hasSupport()
    {
        return function_exists('mcrypt_encrypt');
    }

    /**
     * @inheritdoc
     */
    public function getAlgos()
    {
        return mcrypt_list_algorithms();
    }

    /**
     * Returns the inititialization size used for a particular cipher
     * type.  Returns an integer IV size on success or boolean false
     * on failure.  If no IV is needed for the cipher type and mode,
     * a zero is returned.
     *
     * @param  string   $cipher_type
     * @return int|bool
     */
    public function getIVSize($cipher_type = MCRYPT_TRIPLEDES)
    {
        $block_mode = 'cbc';

        return mcrypt_get_iv_size($cipher_type, $block_mode);
    }

    /**
     * Returns the maximum key size that can be used with a particular
     * cipher type. Any key size equal to or less than the returned
     * value are legal key sizes.  Depending on if the local mycrypt
     * extension is linked against 2.2 or 2.3/2.4 the block mode could
     * be required, hence the if/else statement.
     *
     * @param  string $cipher_type
     * @return int
     */
    public function getKeySize($cipher_type = MCRYPT_TRIPLEDES)
    {
        $block_mode = 'cbc';

        $max_key_size = mcrypt_get_key_size($cipher_type);

        if ($max_key_size !== false) {
            return $max_key_size;
        } else {
            return mcrypt_get_key_size($cipher_type, $block_mode);
        }
    }

    /**
     * Performs an internal self-test on the specified mcrypt algorithm and
     * returns either boolean true/false depending on if the self-test passed
     * or failed.
     *
     * @param  string  $cipher_type
     * @return boolean
     */
    public function algoSelfTest($cipher_type = MCRYPT_TRIPLEDES)
    {
        return mcrypt_module_self_test($cipher_type);
    }

    /**
     *
     * Encrypts $text based on your $key and $iv.  The returned text is
     * base-64 encoded to make it easier to work with in various scenarios.
     * Default cipher is MCRYPT_TRIPLEDES but you can substitute depending
     * on your specific encryption needs.
     *
     * @param  string    $text
     * @param  string    $key
     * @param  string    $iv
     * @param  int       $bit_check
     * @param  string    $cipher_type
     * @return string    $text
     * @throws Exception $e
     *
     */
    public function encrypt($text, $key = '', $iv = '', $bit_check = 8, $cipher_type = MCRYPT_TRIPLEDES)
    {
        try {
            /* Ensure the key & IV is the same for both encrypt & decrypt. */
            if (!empty($text) && is_string($text)) {
                $text_num = str_split($text, $bit_check);
                $text_num = $bit_check - strlen($text_num[count($text_num) - 1]);

                for ($i = 0; $i<$text_num; $i++) {
                    $text = $text.chr($text_num);
                }

                $cipher = mcrypt_module_open($cipher_type, '', 'cbc', '');
                mcrypt_generic_init($cipher, $key, $iv);

                $encrypted = mcrypt_generic($cipher, $text);
                mcrypt_generic_deinit($cipher);

                mcrypt_module_close($cipher);

                return base64_encode($encrypted);
            } else {
                return $text;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * Decrypts $text based on your $key and $iv.  Make sure you use the same key
     * and initialization vector that you used when encrypting the $text. Default
     * cipher is MCRYPT_TRIPLEDES but you can substitute depending on the cipher
     * used for encrypting the text - very important.
     *
     * @param  string    $encrypted_text
     * @param  string    $key
     * @param  string    $iv
     * @param  int       $bit_check
     * @param  string    $cipher_type
     * @return string    $text
     * @throws Exception $e
     *
     */
    public function decrypt($encrypted_text, $key = '', $iv = '', $bit_check = 8, $cipher_type = MCRYPT_TRIPLEDES)
    {
        try {
            /* Ensure the key & IV is the same for both encrypt & decrypt. */
            if (!empty($encrypted_text)) {
                $cipher = mcrypt_module_open($cipher_type, '', 'cbc', '');

                mcrypt_generic_init($cipher, $key, $iv);
                $decrypted = mdecrypt_generic($cipher, base64_decode($encrypted_text));

                mcrypt_generic_deinit($cipher);
                $last_char = substr($decrypted, -1);

                for ($i = 0; $i < $bit_check; $i++) {
                    if (chr($i) == $last_char) {
                        $decrypted = substr($decrypted, 0, strlen($decrypted) - $i);
                        break;
                    }
                }

                mcrypt_module_close($cipher);

                return $decrypted;
            } else {
                return $encrypted_text;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}

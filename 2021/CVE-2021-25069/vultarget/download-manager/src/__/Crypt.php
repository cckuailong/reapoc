<?php

/**
 * Class Crypt
 * From v4.1.9
 * Updated in v4.7.5
 */

namespace WPDM\__;

class Crypt
{

    function __construct()
    {
    }

    public static function encrypt($text)
    {
        if($text === '') return '';

        $encKey = get_option('__wpdm_enc_key');
        if(!$encKey){
            $encKey = self::encKey();
            update_option('__wpdm_enc_key', $encKey);
        }

        $text = is_array($text) || is_object($text) ? json_encode($text) : $text;

        if(function_exists('openssl_cipher_iv_length')) {
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($text, $cipher, $encKey, $options = OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $encKey, $as_binary = true);
            $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

            $ciphertext = str_replace(array('+', '/', '='), array('-', '_', ''), $ciphertext);
            $ciphertext = trim($ciphertext, '=');
        } else {
            $ciphertext = base64_encode($text);
            $ciphertext = str_replace("=", "", $ciphertext);
            $ciphertext = base64_encode(base64_encode($encKey).$ciphertext);
            $ciphertext = trim($ciphertext, "=");
        }
        return $ciphertext;
    }

    public static function decrypt($ciphertext, $ARRAY = false)
    {
        if($ciphertext === '') return $ciphertext;

        $encKey = get_option('__wpdm_enc_key');
        if(!$encKey)
            return $ciphertext;
        if(function_exists('openssl_cipher_iv_length')) {
            $ciphertext = str_replace(array('-', '_'), array('+', '/'), $ciphertext);
            $c = base64_decode($ciphertext);
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len = 32);
            if (empty($hmac)) return '';
            $ciphertext_raw = substr($c, $ivlen + $sha2len);
            try {
                $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $encKey, $options = OPENSSL_RAW_DATA, $iv);
                $calcmac = hash_hmac('sha256', $ciphertext_raw, $encKey, $as_binary = true);
                if (hash_equals($hmac, $calcmac)) {
                    $original_plaintext = trim($original_plaintext);
                    $unjsoned_plaintext = json_decode($original_plaintext, $ARRAY);
                    $original_plaintext = is_object($unjsoned_plaintext) || is_array($unjsoned_plaintext) ? $unjsoned_plaintext : $original_plaintext;
                    return $original_plaintext;
                }
            } catch (\Exception $e) {
                return '';
            }
        } else {
            $encKey = base64_encode($encKey);
            $ciphertext = base64_decode($ciphertext);
            $ciphertext = str_replace($encKey, "", $ciphertext);
            $original_plaintext = base64_decode($ciphertext);
            $unjsoned_plaintext = json_decode($original_plaintext, $ARRAY);
            $original_plaintext = is_object($unjsoned_plaintext) || is_array($unjsoned_plaintext) ? $unjsoned_plaintext : $original_plaintext;
            return $original_plaintext;
        }

        return '';
    }

    public static function encKey($length = 256) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()"`~.,;:|\][{}<>?/|';
        $key = '';
        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[mt_rand(0, strlen($chars) - 1)];
        }

        return $key;
    }

}

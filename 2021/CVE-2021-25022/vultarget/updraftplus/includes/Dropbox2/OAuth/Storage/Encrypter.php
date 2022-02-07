<?php

/**
 * This class provides the functionality to encrypt
 * and decrypt access tokens stored by the application
 * @author Ben Tadiar <ben@handcraftedbyben.co.uk>
 * @link https://github.com/benthedesigner/dropbox
 * @package Dropbox\Oauth
 * @subpackage Storage
 */

/* UpdraftPlus notes
Using this was fairly pointless (it encrypts storage credentials at rest). But, it's implemented now, so needs supporting.
Investigation shows that mcrypt and phpseclib native encryption using different padding schemes.
As a result, that which is encrypted by phpseclib native can be decrypted by mcrypt, but not vice-versa. Each can (as you'd expect) decrypt the results of their own encryption.
As a consequence, it makes sense to always encrypt with phpseclib native, and prefer decrypting with with mcrypt if it is available and otherwise fall back to phpseclib.
We could deliberately re-encrypt all loaded information with phpseclib native, but there seems little need for that yet. There can only be a problem if mcrypt is disabled - which pre-July-2015 meant that Dropbox wouldn't work at all. Now, it will force a re-authorisation.
*/

class Dropbox_Encrypter
{    
    // Encryption settings - default settings yield encryption to AES (256-bit) standard
    // @todo Provide PHPDOC for each class constant
    const KEY_SIZE = 32;
    const IV_SIZE = 16;
    
    /**
     * Encryption key
     * @var null|string
     */
    private $key = null;
    
    /**
     * Check Mcrypt is loaded and set the encryption key
     * @param string $key
     * @return void
     */
    public function __construct($key)
    {
        if (preg_match('/^[A-Za-z0-9]+$/', $key) && $length = strlen($key) === self::KEY_SIZE) {
            # Short-cut so that the mbstring extension is not required
            $this->key = $key;
        } elseif (($length = mb_strlen($key, '8bit')) !== self::KEY_SIZE) {
            throw new Dropbox_Exception('Expecting a ' .  self::KEY_SIZE . ' byte key, got ' . $length);
        } else {
            // Set the encryption key
            $this->key = $key;
        }
    }
    
    /**
     * Encrypt the OAuth token
     * @param \stdClass $token Serialized token object
     * @return string
     */
    public function encrypt($token)
    {

        // Encryption: we always use phpseclib for this
        global $updraftplus;
        $ensure_phpseclib = $updraftplus->ensure_phpseclib('Crypt_AES');
        
        if (is_wp_error($ensure_phpseclib)) {
            $updraftplus->log("Failed to load phpseclib classes (".$ensure_phpseclib->get_error_code()."): ".$ensure_phpseclib->get_error_message());
            $updraftplus->log("Failed to load phpseclib classes (".$ensure_phpseclib->get_error_code()."): ".$ensure_phpseclib->get_error_message(), 'error');
            return false;
        }
        
        $updraftplus->ensure_phpseclib('Crypt_Rijndael');

        if (!function_exists('crypt_random_string')) require_once(UPDRAFTPLUS_DIR.'/vendor/phpseclib/phpseclib/phpseclib/Crypt/Random.php');
        
        $iv = crypt_random_string(self::IV_SIZE);
        
        // Defaults to CBC mode
        $rijndael = new Crypt_Rijndael();
        
        $rijndael->setKey($this->key);
        
        $rijndael->setIV($iv);

        $cipherText = $rijndael->encrypt($token);
        
        return base64_encode($iv . $cipherText);
    }
    
    /**
     * Decrypt the ciphertext
     * @param string $cipherText
     * @return object \stdClass Unserialized token
     */
    public function decrypt($cipherText)
    {
    
        // Decryption: prefer mcrypt, if available (since it can decrypt data encrypted by either mcrypt or phpseclib)

        $cipherText = base64_decode($cipherText);
        $iv = substr($cipherText, 0, self::IV_SIZE);
        $cipherText = substr($cipherText, self::IV_SIZE);
    
        if (function_exists('mcrypt_decrypt')) {
            // @codingStandardsIgnoreLine
            $token = @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, $cipherText, MCRYPT_MODE_CBC, $iv);
        } else {
            global $updraftplus;
            $updraftplus->ensure_phpseclib('Crypt_Rijndael');

            $rijndael = new Crypt_Rijndael();
            $rijndael->setKey($this->key);
            $rijndael->setIV($iv);
            $token = $rijndael->decrypt($cipherText);
        }
        
        return $token;
    }
}

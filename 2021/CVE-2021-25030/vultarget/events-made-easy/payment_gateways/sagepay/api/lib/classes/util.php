<?php

defined('SAGEPAY_SDK_PATH') || exit('No direct script access.');
define("MASK_FOR_HIDDEN_FIELDS", "...");
/**
 * Common utilities shared by all Integration methods
 *
 * @category  Payment
 * @package   Sagepay

 * @copyright (c) 2013, Sage Pay Europe Ltd.
 */
class SagepayUtil
{
     
    /**
     * The associated array containing card types and values
     *
     * @return array Array of card codes.
     */
    static protected $cardNames = array(
        'visa' => 'Visa',
        'visaelectron' => 'Visa Electron',
        'mastercard' => 'Mastercard',
        'amex' => 'American Express',
        'delta' => 'Delta',
        'dc' => 'Diners Club',
        'jcb' => 'JCB',
        'laser' => 'Laser',
        'maestro' => 'Maestro',
    );

    /**
     * The card types that SagePay supports.
     *
     * @return array Array of card codes.
     */
    static public function cardTypes()
    {
        return array_keys(self::$cardNames);
    }

    /**
     * Populate the card names in to a usable array.
     *
     * @param array $availableCards Available card codes.
     *
     * @return array Array of card codes and names.
     */
    static public function availableCards(array $availableCards)
    {
        $cardArr = array();

        // Filter input card types
        foreach ($availableCards as $code)
        {
            $code = strtolower($code);
            if ((array_key_exists($code, self::$cardNames)))
            {
                $cardArr[$code] = self::$cardNames[$code];
            }
        }

        return $cardArr;
    }

    /**
     * PHP's mcrypt does not have built in PKCS5 Padding, so we use this.
     *
     * @param string $input The input string.
     *
     * @return string The string with padding.
     */
    static protected function addPKCS5Padding($input)
    {
        $blockSize = 16;
        $padd = "";

        // Pad input to an even block size boundary.
        $length = $blockSize - (strlen($input) % $blockSize);
        for ($i = 1; $i <= $length; $i++)
        {
            $padd .= chr($length);
        }

        return $input . $padd;
    }

    /**
     * Remove PKCS5 Padding from a string.
     *
     * @param string $input The decrypted string.
     *
     * @return string String without the padding.
     * @throws SagepayApiException
     */
    static protected function removePKCS5Padding($input)
    {
        $blockSize = 16;
        $padChar = ord($input[strlen($input) - 1]);

        /* Check for PadChar is less then Block size */
        if ($padChar > $blockSize)
        {
            throw new SagepayApiException('Invalid encryption string');
        }
        /* Check by padding by character mask */
        if (strspn($input, chr($padChar), strlen($input) - $padChar) != $padChar)
        {
            throw new SagepayApiException('Invalid encryption string');
        }

        $unpadded = substr($input, 0, (-1) * $padChar);
        /* Chech result for printable characters */
        if (preg_match('/[[:^print:]]/', $unpadded))
        {
            throw new SagepayApiException('Invalid encryption string');
        }
        return $unpadded;
    }

    /**
     * Encrypt a string ready to send to SagePay using encryption key.
     *
     * @param  string  $string  The unencrypyted string.
     * @param  string  $key     The encryption key.
     *
     * @return string The encrypted string.
     */
    static public function encryptAes($string, $key)
    {
        // AES encryption, CBC blocking with PKCS5 padding then HEX encoding.
        // Add PKCS5 padding to the text to be encypted.
        $string = self::addPKCS5Padding($string);

        // Perform encryption with PHP's MCRYPT module.
        $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $key);

        // Perform hex encoding and return.
        return "@" . strtoupper(bin2hex($crypt));
    }

    /**
     * Decode a returned string from SagePay.
     *
     * @param string $strIn         The encrypted String.
     * @param string $password      The encyption password used to encrypt the string.
     *
     * @return string The unecrypted string.
     * @throws SagepayApiException
     */
    static public function decryptAes($strIn, $password)
    {
        // HEX decoding then AES decryption, CBC blocking with PKCS5 padding.
        // Use initialization vector (IV) set from $str_encryption_password.
        $strInitVector = $password;

        // Remove the first char which is @ to flag this is AES encrypted and HEX decoding.
        $hex = substr($strIn, 1);

        // Throw exception if string is malformed
        if (!preg_match('/^[0-9a-fA-F]+$/', $hex))
        {
            throw new SagepayApiException('Invalid encryption string');
        }
        $strIn = pack('H*', $hex);

        // Perform decryption with PHP's MCRYPT module.
        $string = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $password, $strIn, MCRYPT_MODE_CBC, $strInitVector);
        return self::removePKCS5Padding($string);
    }

    /**
     * Convert a data array to a query string ready to post.
     *
     * @param  array   $data        The data array.
     * @param  string  $delimeter   Delimiter used in query string
     * @param  boolean $urlencoded  If true encode the final query string
     *
     * @return string The array as a string.
     */
    static public function arrayToQueryString(array $data, $delimiter = '&', $urlencoded = false)
    {
        $queryString = '';
        $delimiterLength = strlen($delimiter);

        // Parse each value pairs and concate to query string
        foreach ($data as $name => $value)
        {   
            // Apply urlencode if it is required
            if ($urlencoded)
            {
                $value = urlencode($value);
            }
            $queryString .= $name . '=' . $value . $delimiter;
        }

        // remove the last delimiter
        return substr($queryString, 0, -1 * $delimiterLength);
    }

    static public function arrayToQueryStringRemovingSensitiveData(array $data,array $nonSensitiveDataKey, $delimiter = '&', $urlencoded = false)
    {
        $queryString = '';
        $delimiterLength = strlen($delimiter);

        // Parse each value pairs and concate to query string
        foreach ($data as $name => $value)
        {
           if (!in_array($name, $nonSensitiveDataKey)){
				$value=MASK_FOR_HIDDEN_FIELDS;
		   }
		   else if ($urlencoded){
				$value = urlencode($value);
		   }
           	// Apply urlencode if it is required
            	
           $queryString .= $name . '=' . $value . $delimiter;
        }

        // remove the last delimiter
        return substr($queryString, 0, -1 * $delimiterLength);
    }
    /**
     * Convert string to data array.
     *
     * @param string  $data       Query string
     * @param string  $delimeter  Delimiter used in query string
     *
     * @return array
     */
    static public function queryStringToArray($data, $delimeter = "&")
    {
        // Explode query by delimiter
        $pairs = explode($delimeter, $data);
        $queryArray = array();

        // Explode pairs by "="
        foreach ($pairs as $pair)
        {
            $keyValue = explode('=', $pair);

            // Use first value as key
            $key = array_shift($keyValue);

            // Implode others as value for $key
            $queryArray[$key] = implode('=', $keyValue);
        }
        return $queryArray;
    }

   static public function queryStringToArrayRemovingSensitiveData($data, $delimeter = "&", $nonSensitiveDataKey)
    {  
        // Explode query by delimiter
        $pairs = explode($delimeter, $data);
        $queryArray = array();

        // Explode pairs by "="
        foreach ($pairs as $pair)
        {
            $keyValue = explode('=', $pair);
            // Use first value as key
            $key = array_shift($keyValue);
            if (in_array($key, $nonSensitiveDataKey)){
			  $keyValue = explode('=', $pair);
			}
			else{
			  $keyValue = array(MASK_FOR_HIDDEN_FIELDS);
			}
		    // Implode others as value for $key
			$queryArray[$key] = implode('=', $keyValue);
    		
        }
        return $queryArray;
    }
    /**
     * Logging the debugging information to "debug.log"
     *
     * @param  string  $message
     * @return boolean
     */
    static public function log($message)
    {
        $settings = SagepaySettings::getInstance();
        if ($settings->getLogError())
        {
            $filename = SAGEPAY_SDK_PATH . '/debug.log';
            $line = '[' . date('Y-m-d H:i:s') . '] :: ' . $message;
            try
            {
                $file = fopen($filename, 'a+');
                fwrite($file, $line . PHP_EOL);
                fclose($file);
            } catch (Exception $ex)
            {
                return false;
            }
        }
        return true;
    }

    /**
     * Extract last 4 digits from card number;
     *
     * @param string $cardNr
     *
     * @return string
     */
    static public function getLast4Digits($cardNr)
    {
        // Apply RegExp to extract last 4 digits
        $matches = array();
        if (preg_match('/\d{4}$/', $cardNr, $matches))
        {
            return $matches[0];
        }
        return '';
    }

}

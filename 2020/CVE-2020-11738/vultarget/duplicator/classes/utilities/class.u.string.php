<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
/**
 * Utility class working with strings
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP
 * @subpackage classes/utilities
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
class DUP_STR
{

    /**
     * Append the value to the string if it doesn't already exist
     *
     * @param string $string The string to append to
     * @param string $value The string to append to the $string
     *
     * @return string Returns the string with the $value appended once
     */
    public static function appendOnce($string, $value)
    {
        return $string.(substr($string, -1) == $value ? '' : $value);
    }

    /**
     * Returns true if the string contains UTF8 characters
     * @see http://php.net/manual/en/function.mb-detect-encoding.php
     *
     * @param string  $string     The class name where the $destArray exists
     *
     * @return null
     */
    public static function hasUTF8($string)
    {
        return preg_match('%(?:
            [\xC2-\xDF][\x80-\xBF]        # non-overlong 2-byte
            |\xE0[\xA0-\xBF][\x80-\xBF]               # excluding overlongs
            |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}      # straight 3-byte
            |\xED[\x80-\x9F][\x80-\xBF]               # excluding surrogates
            |\xF0[\x90-\xBF][\x80-\xBF]{2}    # planes 1-3
            |[\xF1-\xF3][\x80-\xBF]{3}                  # planes 4-15
            |\xF4[\x80-\x8F][\x80-\xBF]{2}    # plane 16
            )+%xs', $string);
    }

    /**
     * Returns true if the $needle is found in the $haystack
     *
     * @param string  $haystack     The full string to search in
     * @param string  $needle       The string to for
     *
     * @return bool
     */
    public static function contains($haystack, $needle)
    {
        $pos = strpos($haystack, $needle);
        return ($pos !== false);
    }

    /**
     * Returns true if the $haystack string starts with the $needle
     *
     * @param string  $haystack     The full string to search in
     * @param string  $needle       The string to for
     *
     * @return bool Returns true if the $haystack string starts with the $needle
     */
    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Returns true if the $haystack string ends with the $needle
     *
     * @param string  $haystack     The full string to search in
     * @param string  $needle       The string to for
     *
     * @return bool Returns true if the $haystack string ends with the $needle
     */
    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }
        return (substr($haystack, -$length) === $needle);
    }


}


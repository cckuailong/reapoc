<?php
/**
 * Utility functions
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package snaplib
 * @subpackage classes/utilities
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (!class_exists('DupLiteSnapLibUtil', false)) {

    class DupLiteSnapLibUtil
    {

        public static function getArrayValue(&$array, $key, $required = true, $default = null)
        {
            if (array_key_exists($key, $array)) {
                return $array[$key];
            } else {
                if ($required) {
                    throw new Exception("Key {$key} not present in array");
                } else {
                    return $default;
                }
            }
        }

        public static function getCallingFunctionName()
        {
            $callers      = debug_backtrace();
            $functionName = $callers[2]['function'];
            $className    = isset($callers[2]['class']) ? $callers[2]['class'] : '';

            return "{$className}::{$functionName}";
        }

        public static function getWorkPercent($startingPercent, $endingPercent, $totalTaskCount, $currentTaskCount)
        {
            if ($totalTaskCount > 0) {
                $percent = $startingPercent + (($endingPercent - $startingPercent) * ($currentTaskCount / (float) $totalTaskCount));
            } else {
                $percent = 0;
            }

            return $percent;
        }

        public static function make_hash()
        {
            // IMPORTANT!  Be VERY careful in changing this format - the FTP delete logic requires 3 segments with the last segment to be the date in YmdHis format.
            try {
                if (function_exists('random_bytes') && self::PHP53()) {
                    return bin2hex(random_bytes(8)).mt_rand(1000, 9999).'_'.date("YmdHis");
                } else {
                    return strtolower(md5(uniqid(rand(), true))).'_'.date("YmdHis");
                }
            }
            catch (Exception $exc) {
                return strtolower(md5(uniqid(rand(), true))).'_'.date("YmdHis");
            }
        }

        public static function PHP53()
        {
            return version_compare(PHP_VERSION, '5.3.2', '>=');
        }

        /**
         * Groups an array into arrays by a given key, or set of keys, shared between all array members.
         *
         * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
         * This variant allows $key to be closures.
         *
         * @param array $array   The array to have grouping performed on.
         * @param mixed $key,... The key to group or split by. Can be a _string_, an _integer_, a _float_, or a _callable_.
         *                       - If the key is a callback, it must return a valid key from the array.
         *                       - If the key is _NULL_, the iterated element is skipped.
         *                       - string|int callback ( mixed $item )
         *
         * @return array|null Returns a multidimensional array or `null` if `$key` is invalid.
         */
        public static function arrayGroupBy(array $array, $key)
        {
            if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
                trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
                return null;
            }
            $func    = (!is_string($key) && is_callable($key) ? $key : null);
            $_key    = $key;
            // Load the new array, splitting by the target key
            $grouped = array();
            foreach ($array as $value) {
                $key = null;
                if (is_callable($func)) {
                    $key = call_user_func($func, $value);
                } elseif (is_object($value) && isset($value->{$_key})) {
                    $key = $value->{$_key};
                } elseif (isset($value[$_key])) {
                    $key = $value[$_key];
                }
                if ($key === null) {
                    continue;
                }
                $grouped[$key][] = $value;
            }
            // Recursively build a nested grouping if more parameters are supplied
            // Each grouped array value is grouped according to the next sequential key
            if (func_num_args() > 2) {
                $args = func_get_args();
                foreach ($grouped as $key => $value) {
                    $params        = array_merge(array($value), array_slice($args, 2, func_num_args()));
                    $grouped[$key] = call_user_func_array(array(__CLASS__, 'arrayGroupBy'), $params);
                }
            }
            return $grouped;
        }

        /**
         * Converts human readable types (10GB) to bytes
         *
         * @param string $from   A human readable byte size such as 100MB
         *
         * @return int	Returns and integer of the byte size
         */
        public static function convertToBytes($from)
        {
            if (is_numeric($from)) {
                return $from;
            }

            $number = substr($from, 0, -2);
            switch (strtoupper(substr($from, -2))) {
                case "KB": return $number * 1024;
                case "MB": return $number * pow(1024, 2);
                case "GB": return $number * pow(1024, 3);
                case "TB": return $number * pow(1024, 4);
                case "PB": return $number * pow(1024, 5);
            }

            $number = substr($from, 0, -1);
            switch (strtoupper(substr($from, -1))) {
                case "K": return $number * 1024;
                case "M": return $number * pow(1024, 2);
                case "G": return $number * pow(1024, 3);
                case "T": return $number * pow(1024, 4);
                case "P": return $number * pow(1024, 5);
            }
            return $from;
        }

        /**
         *  Sanitize input for XSS code
         *
         *  @param string $val		The value to sanitize
         *
         *  @return string Returns the input value cleaned up.
         */
        public static function sanitize($input)
        {
            return filter_var($input, FILTER_SANITIZE_STRING);
        }

        /**
         * remove all non stamo chars from string
         * 
         * @param string $string
         * @return string
         */
        public static function sanitize_non_stamp_chars($string)
        {
            return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $string);
        }

        /**
         * Determines whether a PHP ini value is changeable at runtime.
         *
         * @since 4.6.0
         *
         * @staticvar array $ini_all
         *
         * @link https://secure.php.net/manual/en/function.ini-get-all.php
         *
         * @param string $setting The name of the ini setting to check.
         * @return bool True if the value is changeable at runtime. False otherwise.
         */
        public static function wp_is_ini_value_changeable($setting)
        {
            if (function_exists('wp_is_ini_value_changeable')) {
                return wp_is_ini_value_changeable($setting);
            }

            static $ini_all;

            if (!isset($ini_all)) {
                $ini_all = false;
                // Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
                if (function_exists('ini_get_all')) {
                    $ini_all = ini_get_all();
                }
            }

            // Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
            if (isset($ini_all[$setting]['access']) && ( INI_ALL === ( $ini_all[$setting]['access'] & 7 ) || INI_USER === ( $ini_all[$setting]['access'] & 7 ) )) {
                return true;
            }

            // If we were unable to retrieve the details, fail gracefully to assume it's changeable.
            if (!is_array($ini_all)) {
                return true;
            }

            return false;
        }

        /**
         * The val value returns if it is between min and max otherwise it returns min or max
         * 
         * @param int $val
         * @param int $min
         * @param int $max
         * @return int
         */
        public static function getIntBetween($val, $min, $max)
        {
            return min((int) $max, max((int) $min, (int) $val));
        }
    }
}

<?php
/**
 * Snap strings utils
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DupLiteSnapLib
 * @copyright (c) 2017, Snapcreek LLC
 * @license	https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (!class_exists('DupLiteSnapLibStringU', false)) {

    class DupLiteSnapLibStringU
    {

        public static function boolToString($b)
        {
            return ($b ? 'true' : 'false');
        }

        public static function truncateString($s, $maxWidth)
        {
            if (strlen($s) > $maxWidth) {
                $s = substr($s, 0, $maxWidth - 3).'...';
            }

            return $s;
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
    }
}
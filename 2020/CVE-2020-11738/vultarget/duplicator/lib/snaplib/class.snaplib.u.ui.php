<?php
/**
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

if (!class_exists('DupLiteSnapLibUIU', false)) {

    class DupLiteSnapLibUIU
    {

        public static function echoBoolean($val)
        {
            echo $val ? 'true' : 'false';
        }

        public static function echoChecked($val)
        {
            // filter_var is available in >= php 5.2
            if (function_exists('filter_var') && defined('FILTER_VALIDATE_BOOLEAN')) {
                echo filter_var($val, FILTER_VALIDATE_BOOLEAN) ? 'checked' : '';
            } else {
                echo $val ? 'checked' : '';
            }
        }

        public static function echoDisabled($val)
        {
            echo $val ? 'disabled' : '';
        }

        public static function echoSelected($val)
        {
            echo $val ? 'selected' : '';
        }

        public static function getSelected($val)
        {
            return ($val ? 'selected' : '');
        }
    }
}
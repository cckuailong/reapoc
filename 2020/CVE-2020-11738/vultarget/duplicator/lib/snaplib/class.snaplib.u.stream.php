<?php
/**
 * Snap stream utils
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

if (!class_exists('DupLiteSnapLibStreamU', false)) {

    class DupLiteSnapLibStreamU
    {

        public static function streamGetLine($handle, $length, $ending)
        {
            $line = stream_get_line($handle, $length, $ending);

            if ($line === false) {
                throw new Exception('Error reading line.');
            }

            return $line;
        }
    }
}
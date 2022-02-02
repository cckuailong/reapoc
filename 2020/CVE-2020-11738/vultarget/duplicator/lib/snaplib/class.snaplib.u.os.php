<?php
/**
 * Snap OS utils
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

if (!class_exists('DupLiteSnapLibOSU', false)) {

    class DupLiteSnapLibOSU
    {

        const DEFAULT_WINDOWS_MAXPATH = 260;
        const DEFAULT_LINUX_MAXPATH   = 4096;

        /**
         * return true if current SO is windows
         * 
         * @staticvar bool $isWindows
         * @return bool
         */
        public static function isWindows()
        {
            static $isWindows = null;
            if (is_null($isWindows)) {
                $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
            }
            return $isWindows;
        }

        /**
         *  return current SO path path len
         * @staticvar int $maxPath
         * @return int
         */
        public static function maxPathLen()
        {
            static $maxPath = null;
            if (is_null($maxPath)) {
                if (defined('PHP_MAXPATHLEN')) {
                    $maxPath = PHP_MAXPATHLEN;
                } else {
                    // for PHP < 5.3.0
                    $maxPath = self::isWindows() ? self::DEFAULT_WINDOWS_MAXPATH : self::DEFAULT_LINUX_MAXPATH;
                }
            }
            return $maxPath;
        }
    }
}
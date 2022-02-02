<?php
/**
 * Wordpress utility functions
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

if (!class_exists('DupLiteSnapLibUtilWp', false)) {

    /**
     * Wordpress utility functions
     */
    class DupLiteSnapLibUtilWp
    {

        const PATH_FULL     = 0;
        const PATH_RELATIVE = 1;
        const PATH_AUTO     = 2;

        private static $corePathList = null;
        private static $safeAbsPath  = null;

        /**
         * return safe ABSPATH without last /
         * perform safe function only one time
         *
         * @return string
         */
        public static function getSafeAbsPath()
        {
            if (is_null(self::$safeAbsPath)) {
                if (defined('ABSPATH')) {
                    self::$safeAbsPath = rtrim(DupLiteSnapLibIOU::safePath(ABSPATH), '/');
                } else {
                    self::$safeAbsPath = '';
                }
            }

            return self::$safeAbsPath;
        }

        /**
         * check if path is in wordpress core list
         *
         * @param string $path
         * @param int $fullPath // if PATH_AUTO check if is a full path or relative path
         *                         if PATH_FULL remove ABSPATH len without check
         *                         if PATH_RELATIVE consider path a relative path
         * @param bool $isSafe // if false call rtrim(DupLiteSnapLibIOU::safePath( PATH ), '/')
         *                        if true consider path a safe path without check
         *
         *  PATH_FULL and PATH_RELATIVE is better optimized and perform less operations
         *
         * @return boolean
         */
        public static function isWpCore($path, $fullPath = self::PATH_AUTO, $isSafe = false)
        {
            if ($isSafe == false) {
                $path = rtrim(DupLiteSnapLibIOU::safePath($path), '/');
            }



            switch ($fullPath) {
                case self::PATH_FULL:
                    $absPath = self::getSafeAbsPath();
                    if (strlen($path) < strlen($absPath)) {
                        return false;
                    }
                    $relPath = ltrim(substr($path, strlen($absPath)), '/');
                    break;
                case self::PATH_RELATIVE:
                    $relPath = ltrim($path, '/');
                    break;
                case self::PATH_AUTO:
                default:
                    $absPath = self::getSafeAbsPath();
                    if (strpos($path, $absPath) === 0) {
                        $relPath = ltrim(substr($path, strlen($absPath)), '/');
                    } else {
                        $relPath = ltrim($path, '/');
                    }
            }

            // if rel path is empty is consider root path so is a core folder.
            if (empty($relPath)) {
                return true;
            }

            $pExploded = explode('/', $relPath);
            $corePaths = self::getCorePathsList();

            foreach ($pExploded as $current) {
                if (!isset($corePaths[$current])) {
                    return false;
                }

                $corePaths = $corePaths[$current];
            }
            return true;
        }

        /**
         * get core path list from relative abs path
         * [
         *      'folder' => [
         *          's-folder1' => [
         *              file1 => [],
         *              file2 => [],
         *          ],
         *          's-folder2' => [],
         *          file1 => []
         *      ]
         * ]
         *
         * @return array
         */
        public static function getCorePathsList()
        {
            if (is_null(self::$corePathList)) {
                require_once(dirname(__FILE__).'/wordpress.core.files.php');
            }
            return self::$corePathList;
        }
    }
}
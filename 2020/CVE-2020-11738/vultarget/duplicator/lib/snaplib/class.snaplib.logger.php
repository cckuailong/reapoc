<?php
/**
 * Snap Logger utils
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

if (!class_exists('DupLiteSnapLibLogger', false)) {

    class DupLiteSnapLibLogger
    {

        public static $logFilepath = null;
        static $logHandle          = null;

        public static function init($logFilepath)
        {
            self::$logFilepath = $logFilepath;
        }

        public static function clearLog()
        {
            if (file_exists(self::$logFilepath)) {
                if (self::$logHandle !== null) {
                    fflush(self::$logHandle);
                    fclose(self::$logHandle);
                    self::$logHandle = null;
                }
                @unlink(self::$logFilepath);
            }
        }

        public static function logObject($s, $o, $flush = false)
        {
            self::log($s, $flush);
            self::log(print_r($o, true), $flush);
        }

        public static function log($s, $flush = false, $callingFunctionOverride = null)
        {
            //   echo "{$s}<br/>";
            $lfp = self::$logFilepath;
            //  echo "logging $s to {$lfp}<br/>";
            if (self::$logFilepath === null) {
                throw new Exception('Logging not initialized');
            }

            if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
                $timepart = $_SERVER['REQUEST_TIME_FLOAT'];
            } else {
                $timepart = $_SERVER['REQUEST_TIME'];
            }

            $thread_id = sprintf("%08x", abs(crc32($_SERVER['REMOTE_ADDR'].$timepart.$_SERVER['REMOTE_PORT'])));

            $s = $thread_id.' '.date('h:i:s').":$s";

            if (self::$logHandle === null) {

                self::$logHandle = fopen(self::$logFilepath, 'a');
            }

            fwrite(self::$logHandle, "$s\n");

            if ($flush) {
                fflush(self::$logHandle);

                fclose(self::$logHandle);

                self::$logHandle = fopen(self::$logFilepath, 'a');
            }
        }
        private static $profileLogArray = null;
        private static $prevTS          = -1;

        public static function initProfiling()
        {
            self::$profileLogArray = array();
        }

        public static function writeToPLog($s)
        {
            throw new exception('not implemented');
            $currentTime = microtime(true);

            if (array_key_exists($s, self::$profileLogArray)) {
                $dSame = $currentTime - self::$profileLogArray[$s];
                $dSame = number_format($dSame, 7);
            } else {
                $dSame = 'N/A';
            }

            if (self::$prevTS != -1) {
                $dPrev = $currentTime - self::$prevTS;
                $dPrev = number_format($dPrev, 7);
            } else {
                $dPrev = 'N/A';
            }

            self::$profileLogArray[$s] = $currentTime;
            self::$prevTS              = $currentTime;

            self::log("  {$dPrev}  :  {$dSame}  :  {$currentTime}  :     {$s}");
        }
    }
}
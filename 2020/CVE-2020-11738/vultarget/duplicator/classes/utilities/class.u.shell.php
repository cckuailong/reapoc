<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
// Exit if accessed directly
if (! defined('DUPLICATOR_VERSION')) exit;

class DUP_Shell_U
{
    /**
     * Escape a string to be used as a shell argument with bypass support for Windows
     *
     * 	NOTES:
     * 		Provides a way to support shell args on Windows OS and allows %,! on Windows command line
     * 		Safe if input is know such as a defined constant and not from user input escape shellarg
     * 		on Windows with turn %,! into spaces
     *
     * @return string
     */
    public static function escapeshellargWindowsSupport($string)
    {
        if (strncasecmp(PHP_OS, 'WIN', 3) == 0) {
            if (strstr($string, '%') || strstr($string, '!')) {
                $result = '"'.str_replace('"', '', $string).'"';
                return $result;
            }
        }
        return escapeshellarg($string);
    }

    /**
     *
     * @return boolean
     *
     */
    public static function isPopenEnabled() {

        if (!DUP_Util::isIniFunctionEnalbe('popen') || !DUP_Util::isIniFunctionEnalbe('proc_open')) {
            $ret = false;
        } else {
            $ret = true;
        }

        $ret = apply_filters('duplicator_pro_is_popen_enabled', $ret);
        return $ret;
    }
}
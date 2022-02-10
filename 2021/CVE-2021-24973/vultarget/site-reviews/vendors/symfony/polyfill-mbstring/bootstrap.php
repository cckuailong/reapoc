<?php

/**
 * This is an extract of the symfony/polyfill-mbstring package.
 * 
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package symfony/polyfill-mbstring v1.18.1
 */

use GeminiLabs\Symfony\Polyfill\Mbstring as p;

if (!function_exists('mb_convert_case')) {
    function mb_convert_case($s, $mode, $enc = null) { return p\Mbstring::mb_convert_case($s, $mode, $enc); }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen($s, $enc = null) { return p\Mbstring::mb_strlen($s, $enc); }
}
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($s, $enc = null) { return p\Mbstring::mb_strtolower($s, $enc); }
}
if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper($s, $enc = null) { return p\Mbstring::mb_strtoupper($s, $enc); }
}
if (!function_exists('mb_strwidth')) {
    function mb_strwidth($s, $enc = null) { return p\Mbstring::mb_strwidth($s, $enc); }
}
if (!function_exists('mb_substr')) {
    function mb_substr($s, $start, $length = 2147483647, $enc = null) { return p\Mbstring::mb_substr($s, $start, $length, $enc); }
}
if (extension_loaded('mbstring')) {
    return;
}
if (!defined('MB_CASE_UPPER')) {
    define('MB_CASE_UPPER', 0);
}
if (!defined('MB_CASE_LOWER')) {
    define('MB_CASE_LOWER', 1);
}
if (!defined('MB_CASE_TITLE')) {
    define('MB_CASE_TITLE', 2);
}

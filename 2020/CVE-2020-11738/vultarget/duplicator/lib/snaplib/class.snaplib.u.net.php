<?php
/**
 * Snap Net utils
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

if (!class_exists('DupLiteSnapLibNetU', false)) {

    class DupLiteSnapLibNetU
    {

        public static function postWithoutWait($url, $params)
        {
            foreach ($params as $key => &$val) {
                if (is_array($val)) {
                    $val = implode(',', $val);
                }
                $post_params[] = $key.'='.urlencode($val);
            }

            $post_string = implode('&', $post_params);

            $parts = parse_url($url);

            $fp = fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 60);

            $out = "POST ".$parts['path']." HTTP/1.1\r\n";
            $out .= "Host: ".$parts['host']."\r\n";
            $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $out .= "Content-Length: ".strlen($post_string)."\r\n";
            $out .= "Connection: Close\r\n\r\n";

            if (isset($post_string)) {
                $out .= $post_string;
            }

            fwrite($fp, $out);

            fclose($fp);
        }

        public static function getRequestValue($paramName, $isRequired = true, $default = null)
        {
            if (isset($_REQUEST[$paramName])) {

                return $_REQUEST[$paramName];
            } else {

                if ($isRequired) {
                    throw new Exception("Parameter $paramName not present");
                }

                return $default;
            }
        }
    }
}